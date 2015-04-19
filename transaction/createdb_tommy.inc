<?php


$acd = $_GET['acid'];

$query = "
	select		id,
				name,
				created,
				identy
	from		accounts
	where id = $acd
	
";

$aa = pg_query($conn, $query);

$aa_r = pg_fetch_array($aa);




/* creating schema */
$accoun = $aa_r[identy];

$query1 = "
 	 		SELECT schema_name FROM information_schema.schemata WHERE schema_name = '$accoun';
			
		";
$ca = pg_query($conn, $query1);
$ca_r = pg_fetch_array($ca);

if ($accoun == $ca_r[schema_name]) {
	/* do nothing */
	$message2 = 'No need for schema';
} else {
	
	$query2 = "
 	 		SELECT schema_name FROM information_schema.schemata WHERE schema_name = '$accoun';
			CREATE SCHEMA $accoun;
		";
	$ch = pg_query($conn, $query2);
	
	$message2 = 'Created schema';
}
 
 

/* db creating */

$query3 = "
			CREATE TABLE IF NOT EXISTS $accoun.contacts(
				id serial NOT NULL,
				fname character varying(30),
				lname character varying(40),
				bill_addr character varying(50),
				bill_zip character varying(15),
				bill_city character varying(25),
				bill_country character varying(25),
				email character varying(50),
				phone character varying(15),
				www character varying(50),
				created timestamp without time zone,
				modified timestamp without time zone,
				CONSTRAINT pk_key PRIMARY KEY (id)
			);
			CREATE TABLE IF NOT EXISTS $accoun.company(
				id serial NOT NULL,
				name character varying(150),
				ytunnus character varying(15),
				www character varying(150),
				bill_addr character varying(50),
				bill_zip character varying(15),
				bill_city character varying(25),
				bill_country character varying(25),
				email character varying(50),
				phone character varying(15),
				created timestamp without time zone,
				modified timestamp without time zone,
				CONSTRAINT company_id_pkey PRIMARY KEY (id)
			);
			
			
		";
$cr = pg_query($conn, $query3);

$ret_url = 'index.php?section=admin&template=main';
$message = 'Update Successfull';






echo "<p style='text-align:center;'>$message for $aa_r[name] $ca_r[schema_name]<br/> $message2</p>";

?>