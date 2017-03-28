<?php

/*
 *  dbtable class
 *  Written by Niklas Schönberg
 *
 *  Requirements: db.class.php (An extended PDO class)
 *
 *  A class that creates and modifies tables according to definitions.
 *  Works only on PostgreSQL at the moment. MySQL support might be added
 *  at some point, but I don't use it so I can't really maintain it.
 *
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


class dbtable {
    protected $db;
    protected $name;
    protected $type;
    protected $pkey;
    protected $cols;

    protected $exists;

    function __construct($tab,$db=false) {
        if ($db) {
            $this->db = $db;
        } else {
            $this->db = $GLOBALS['db'];
        }
        // Resolve chicken and egg problem by passing optional "raw" array to method.
        $this->parsePKey($tab['pkey'],$tab['cols']);

        $this->name = $tab['name'];
        $this->type = $tab['type'];
        $this->cols = $this->parseCols($tab['cols']);

        $this->exists = $this->tableExists();
    }
    protected function parsePKey($pkey,$cols=false) {
        if ($cols === false)
            $cols = $this->cols;
        $this->pkey=array();
        foreach (explode(",",$pkey) as $pk) {
            if (array_key_exists($pk,$cols)) {
                $this->pkey[] = $pk;
            }
        }
    }
    protected function parseCols($cols) {
        foreach($cols as $name => &$col) {
            $col['name'] = $name;
            if (empty($col['default'])) $col['default'] = "";
            if (empty($col['notnull'])) $col['notnull'] = false;
            if (in_array($name,$this->pkey)) {
                $col['notnull'] = true;
            }
        }
        return $cols;
    }
    protected function tableExists() {
        $sql = "
            SELECT
                count(table_name) as exists
            FROM
                information_schema.tables
            WHERE
                table_name = :table
            AND
                table_schema = '#schema#'
            ";
        $result = $this->db->prepare($sql);
        $result->execute(array(":table" => $this->name));
        $row = $result->fetch();
        if (empty($row['exists']))
            return false;
        return true;
    }
    protected function tableColumns() {
        $sql = "
            SELECT
                *
            FROM
                information_schema.columns
            WHERE
                table_name = :table
            AND
                table_schema = '#schema#'
            ";
        $result = $this->db->prepare($sql);
        $result->execute(array(":table" => $this->name));
        $cols = array();
        while ($row = $result->fetch()) {
            $this->exists = true;
            $cols[$row['column_name']] = array(
                "name" => $row['column_name'],
                "type" => $row['data_type'],
                "default" => $row['column_default'],
                "notnull" => ($row['is_nullable'] == "NO"),
            );
        }
        return $cols;
    }
    protected function compareCols($col1,$col2) {
        // Check name
        if ($col1['name'] != $col2['name'])
            return false;
        // Check type
        if ($col1['type'] != $col2['type'])
            return false;
        if ($col1['default'] != $col2['default'])
            return false;
        if ($col1['notnull'] != $col2['notnull'])
            return false;

        // Everything matched
        return true;
    }

    public function execute() {
        if ($this->exists && $this->type == 1) {
            $this->dropTable();
            $this->exists = false;
        }
        if ($this->exists) {
            $this->updateTable();
        } else {
            $this->createTable();
        }
    }

    public function createTable() {
        $pkey = implode(",",$this->pkey);

        $columns = "";
        foreach ($this->cols as $name => $attr) {
            $columns .= $this->col($name, $attr).",\n";
        }

        $sql = "
            CREATE TABLE #schema#.{$this->name}
            (
              {$columns}
              CONSTRAINT {$this->name}_pkey PRIMARY KEY ({$pkey})
            )
            WITH (
              OIDS=FALSE
            )";
        $query = $this->db->query($sql);
    }
    public function updateTable() {
        $oldCols = $this->parseCols($this->tableColumns());
        $addCols = array();
        $delCols = $oldCols;
        $modCols = array();

        $cmds = array();
        $cmds[] = "DROP CONSTRAINT IF EXISTS {$this->name}_pkey";

        foreach ($this->cols as $name => $attr) {
            if (isset($oldCols[$name])) {
                unset($delCols[$name]);
                if (!$this->compareCols($attr,$oldCols[$name])) {
                    $modCols[$name] = $attr;
                }
            } else {
                $addCols[$name] = $attr;
            }
        }

        $add = "";
        $del = "";
        $mod = "";

        foreach ($addCols as $name => $attr) {
            $add .= "ADD COLUMN {$this->col($name, $attr)},\n";
        }
        foreach ($delCols as $name => $attr) {
            if (substr($name, 0,7) != "delete_") {
                if ($this->type == 2)
                    $del .= "DROP COLUMN IF EXISTS {$name},\n";
                elseif ($this->type == 3)
                    $cmds[] .= "RENAME COLUMN {$name} to delete_{$name}\n";
            }
        }
        foreach ($modCols as $name => $attr) {
            $default = preg_replace("/::[a-z0-9 ]+/i","",$oldCols[$name]['default']);
            if ($attr['type'] == "serial") {
                // Special case for serial type
                if ($oldCols[$name]['type'] != "integer" || substr($default,0,7) != "nextval") {
                    // See if is already serial
                    // TODO Better check, this might still be ordinary integer.
                    $del .= "DROP COLUMN IF EXISTS {$name},\n";
                    $add .= "ADD COLUMN {$this->col($name, $attr)},\n";
                }
                // Skip the rest of the iteration so not to add "drop not null" and "drop default"
                continue;
            } else if ($attr['type'] != $oldCols[$name]['type']) {
                $mod .= "ALTER COLUMN {$name} SET DATA TYPE {$attr['type']} USING {$name}::{$attr['type']},\n";
            }
            if ($attr['notnull'] != $oldCols[$name]['notnull']) {
                $mod .= "ALTER COLUMN {$name} ".(empty($attr['notnull'])?"DROP":"SET")." NOT NULL,\n";
            }
            if ($attr['default'] != $default) {
                $mod .= "ALTER COLUMN {$name} ".(empty($attr['default'])?"DROP":"SET")." DEFAULT {$attr['default']},\n";
            }
        }

        $asql = trim($del.$add.$mod," \n");
        $pkey = implode(",",$this->pkey);
        if (!empty($asql) || count($cmds) > 1) {
            // Don't run if there's nothing to run.
            $asql .= "ADD CONSTRAINT {$this->name}_pkey PRIMARY KEY ({$pkey})";
            $cmds[] = $asql;

            foreach ($cmds as $cmd) {
                $sql = "
                    ALTER TABLE
                        #schema#.{$this->name}
                    {$cmd}
                    ";
                $query = $this->db->query($sql);
            }
        }
    }
    public function dropTable() {
        $sql = "
            DROP TABLE #schema#.{$this->name}
            ";
        $query = $this->db->query($sql);
    }
    protected function col ($name,$attr) {
        $str = "{$name} {$attr['type']}";
        if (!empty($attr['notnull'])) {
            $str .= " NOT NULL";
        }
        if (!empty($attr['default'])) {
            $str .= " DEFAULT {$attr['default']}";
        }

        return $str;
    }
}

?>
