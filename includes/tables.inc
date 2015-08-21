<?php

/* ***not in use*** check is done against file modified time*/
$last_update = "2015-08-21 10:36:00";

// Note default string values must be quoted.

// Table definitions
$tables = array(
    array(
        "type" => 2, // 1 - temporary (re-created each time), 2 - May delete columns, 3 - rename columns, 4 - do not delete
        "name" => "contacts", // Name of table
        "pkey" => "id", // Primary key column(s) (comma separated)
        "cols" => array( // List the columns
            "id" => array("type"=>"serial"),
            "fname" => array("type"=>"character varying"),
            "lname" => array("type"=>"character varying"),
            "bill_addr" => array("type"=>"character varying"),
            "bill_zip" => array("type"=>"character varying"),
            "bill_city" => array("type"=>"character varying"),
            "bill_country" => array("type"=>"character varying"),
            "email" => array("type"=>"character varying"),
            "phone1" => array("type"=>"character varying"),
            "phone2" => array("type"=>"character varying"),
            "www" => array("type"=>"character varying"),
            "created" => array("type"=>"timestamp without time zone"),
            "modified" => array("type"=>"timestamp without time zone"),
            "loc" => array("type"=>"character varying"),
            ),
        "index" => array( // List of indexes
			"indexname" => array("table"=>"tablename","definition"=>"count(foo)"),
			)
        ),
		array(
        "type" => 2, // 1 - temporary (re-created each time), 2 - May delete columns, 3 - rename columns, 4 - do not delete
        "name" => "company", // Name of table
        "pkey" => "id", // Primary key column(s) (comma separated)
        "cols" => array( // List the columns
            "id" => array("type"=>"serial"),
            "name" => array("type"=>"character varying"),
            "ytunnus" => array("type"=>"character varying"),
            "bill_addr" => array("type"=>"character varying"),
            "bill_zip" => array("type"=>"character varying"),
            "bill_city" => array("type"=>"character varying"),
            "bill_country" => array("type"=>"character varying"),
            "email" => array("type"=>"character varying"),
            "phone" => array("type"=>"character varying"),
            "www" => array("type"=>"character varying"),
            "created" => array("type"=>"timestamp without time zone"),
            "modified" => array("type"=>"timestamp without time zone"),
            ),
        "index" => array( // List of indexes
			"indexname" => array("table"=>"tablename","definition"=>"count(foo)"),
			)
        ),
        array(
        "type" => 2, // 1 - temporary (re-created each time), 2 - May delete columns, 3 - rename columns, 4 - do not delete
        "name" => "invoice_def", // Name of table
        "pkey" => "id", // Primary key column(s) (comma separated)
        "cols" => array( // List the columns
            "id" => array("type"=>"serial"),
            "ident" => array("type"=>"bigint"),
            "header" => array("type"=>"character varying"),
            "pid" => array("type"=>"integer"),
            "cid" => array("type"=>"integer"),
            "loc" => array("type"=>"character varying"),
            "created" => array("type"=>"timestamp without time zone"),
            "dated" => array("type"=>"date"),
            "ongoing" => array("type"=>"boolean"),
            "end_date" => array("type"=>"date"),
            "next_create" => array("type"=>"date"),
            "recurring" => array("type"=>"integer"),
            "active" => array("type"=>"boolean"),
            ),
        "index" => array( // List of indexes
			"indexname" => array("table"=>"tablename","definition"=>"count(foo)"),
			)
        ),
        array(
        "type" => 2, // 1 - temporary (re-created each time), 2 - May delete columns, 3 - rename columns, 4 - do not delete
        "name" => "invoice_def_item", // Name of table
        "pkey" => "id", // Primary key column(s) (comma separated)
        "cols" => array( // List the columns
            "id" => array("type"=>"serial"),
            "cat" => array("type"=>"integer"),
            "item" => array("type"=>"character varying"),
            "invoice_id" => array("type"=>"bigint"),
            "price" => array("type"=>"real"),
            "qty" => array("type"=>"real"),
            "unit" => array("type"=>"integer"),
            "vat" => array("type"=>"real"),
            ),
        "index" => array( // List of indexes
			"indexname" => array("table"=>"tablename","definition"=>"count(foo)"),
			)
        ),
        array(
        "type" => 2, // 1 - temporary (re-created each time), 2 - May delete columns, 3 - rename columns, 4 - do not delete
        "name" => "invoice_out", // Name of table
        "pkey" => "id", // Primary key column(s) (comma separated)
        "cols" => array( // List the columns
            "id" => array("type"=>"serial"),
            "header" => array("type"=>"character varying"),
            "pid" => array("type"=>"integer"),
            "cid" => array("type"=>"integer"),
            "loc" => array("type"=>"character varying"),
            "addhead" => array("type"=>"character varying"),
            "invoice_id" => array("type"=>"bigint"),
            "created" => array("type"=>"timestamp without time zone"),
            "dated" => array("type"=>"timestamp without time zone"),
            "due_date" => array("type"=>"timestamp without time zone"),
            "ref" => array("type"=>"character varying"),
            "pub" => array("type"=>"boolean"),
            "cash" => array("type"=>"boolean"),
            ),
        "index" => array( // List of indexes
			"indexname" => array("table"=>"tablename","definition"=>"count(foo)"),
			)
        ),
        array(
        "type" => 2, // 1 - temporary (re-created each time), 2 - May delete columns, 3 - rename columns, 4 - do not delete
        "name" => "invoice_out_item", // Name of table
        "pkey" => "id", // Primary key column(s) (comma separated)
        "cols" => array( // List the columns
            "id" => array("type"=>"serial"),
            "def_id" => array("type"=>"integer"),
            "cat" => array("type"=>"integer"),
            "item" => array("type"=>"character varying"),
            "invoice_id" => array("type"=>"bigint"),
            "price" => array("type"=>"real"),
            "qty" => array("type"=>"real"),
            "unit" => array("type"=>"integer"),
            "vat" => array("type"=>"real"),
            ),
        "index" => array( // List of indexes
			"indexname" => array("table"=>"tablename","definition"=>"count(foo)"),
			)
        ),
        array(
        "type" => 2, // 1 - temporary (re-created each time), 2 - May delete columns, 3 - rename columns, 4 - do not delete
        "name" => "link_company_contact", // Name of table
        "pkey" => "contact_id,company_id", // Primary key column(s) (comma separated)
        "cols" => array( // List the columns
            "contact_id" => array("type"=>"integer"),
            "company_id" => array("type"=>"integer"),
            "prim" => array("type"=>"boolean"),
            ),
        "index" => array( // List of indexes
			"indexname" => array("table"=>"tablename","definition"=>"count(foo)"),
			)
        ),
        array(
        "type" => 2, // 1 - temporary (re-created each time), 2 - May delete columns, 3 - rename columns, 4 - do not delete
        "name" => "contact_notes", // Name of table
        "pkey" => "id", // Primary key column(s) (comma separated)
        "cols" => array( // List the columns
            "id" => array("type"=>"serial"),
            "contact_id" => array("type"=>"integer"),
            "created" => array("type"=>"timestamp without time zone"),
            "cont" => array("type"=>"character varying"),
            "created_by" => array("type"=>"integer"),
            ),
        "index" => array( // List of indexes
			"indexname" => array("table"=>"tablename","definition"=>"count(foo)"),
			)
        ),
         array(
        "type" => 2, // 1 - temporary (re-created each time), 2 - May delete columns, 3 - rename columns, 4 - do not delete
        "name" => "todo", // Name of table
        "pkey" => "id", // Primary key column(s) (comma separated)
        "cols" => array( // List the columns
            "id" => array("type"=>"serial"),
            "contact_id" => array("type"=>"integer"),
            "company_id" => array("type"=>"integer"),
            "created" => array("type"=>"timestamp without time zone"),
            "cont" => array("type"=>"character varying"),
            "completed" => array("type"=>"boolean"),
            "created_by" => array("type"=>"integer"),
            ),
        "index" => array( // List of indexes
			"indexname" => array("table"=>"tablename","definition"=>"count(foo)"),
			)
        )
       
        
    );

?>