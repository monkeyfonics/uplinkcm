<?php

class dbview {
    protected $db;
    protected $name;
    protected $definition;

    protected $exists;

	function __construct($name,$definition,$db=false) {
        if ($db) {
            $this->db = $db;
        } else {
            $this->db = $GLOBALS['db'];
        }
        $this->name = $name;
        $this->definition = $definition;
    }
    function create() {
        $sql = "
            CREATE OR REPLACE VIEW
                #schema#.{$this->name}
            AS
                {$this->definition}
            ";
        $this->db->query($sql);
    }
}

?>
