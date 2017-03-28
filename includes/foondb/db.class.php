<?php

/*
 *  db class
 *  Written by Niklas Schönberg
 *
 *  A class that extends PDO with some useful stuff. Works with
 *  PostgreSQL.
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

class db extends pdo {
    protected $schema = "";
    protected $fail = false;
    public $results = array();

    public function __construct($dsn, $user=null, $pass = null) {
        try {
            parent::__construct($dsn, $user, $pass);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            //$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('DB_Query'));
        } catch (PDOException $e) {
            die('Database Error');
        }
    }

    function schema($schema="") {
        if (!empty($schema))
            $this->schema = $schema;
        return $this->schema;
    }

    function isFail() {
        return ($this->fail?true:false);
    }
    function setFail($fail) {
        $this->fail = ($fail?true:false);
    }

    function query ($statement) {
        if ($result = parent::query(str_replace("#schema#", $this->schema(), $statement))) {
            return $result;
        } else {
            $this->setFail(true);
            error_log("Query failed:\n".$statement);
            trigger_error("Query failed:\n".$statement, E_USER_ERROR);
        }
    }

    function prepare ($statement, $driver_options = array()) {
        if ($result = parent::prepare(str_replace("#schema#", $this->schema(), $statement),$driver_options)) {
            return $result;
        } else {
            $this->setFail(true);
            error_log("Prepare failed:\n".$statement);
            trigger_error("Prepare failed:\n".$statement, E_USER_ERROR);
        }
    }
    static function toArray($string) {
        if (preg_match("/\{([^}]+)\}/",$string,$matches)) {
            $array = explode(",",$matches[1]);
            foreach ($array as &$item) {
                $item = trim($item,' "');
            }
            return $array;
        }
        return $string;
    }

}

class DB_Query extends PDOStatement {
    public function fetch ($fetch_style = PDO::FETCH_ASSOC, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0) {
        if ($row = parent::fetch($fetch_style, $cursor_orientation, $cursor_offset)) {
            foreach (array_keys($row) as $key) {
                if (substr($key, 0,4) == "arr_") {
                   $row[substr($key,4)] = db::toArray($row[$key]);
                }
            }
        }
        return $row;
    }
}


?>
