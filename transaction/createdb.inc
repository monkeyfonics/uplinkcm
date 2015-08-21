
<?php
include "dbtable.class.php";
include "dbview.class.php";

include "db.class.inc";

$dsn = "{$setting['db']['type']}:dbname={$setting['db']['name']};host={$setting['db']['server']}";
$GLOBALS['db'] = new db($dsn,$setting['db']['user'],$setting['db']['password']);


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
$GLOBALS['db']->schema($accoun);

require "includes/tables.inc";

foreach ($tables as $tab) {
    $table = new dbtable($tab);
    $table->execute();
}

/*update time stamp*/
$query = "
			update accounts
			set		updated=now()
					
			where	id=$acd
		";
$au = pg_query($conn, $query);


$ret_url = 'index.php?section=admin&template=account_list';
$message = 'Update Successfull';
$icon = 'layout/img/icon_succ.png';



		
		echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message for $aa_r[name] $ca_r[schema_name]<br/> $message2</p>
			</div>
			";
			
header("Refresh: 2; URL=".$ret_url);

?>