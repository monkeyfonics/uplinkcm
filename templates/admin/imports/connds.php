<?php
/*old uplink cm */
$dbh="db.uplink.fi";
$dbu="postgres";
$ldaph="127.0.0.1";
$ldapdn = "dc=grinda,dc=fi";
$ldaprdn  = 'cn=admin,dc=grinda,dc=fi';
$ldappass = 'mac1ntosh';

$uudb="uudb";
$ds="uplink_cm";

putenv("PGHOST=$dbh");
putenv("PGUSER=$dbu");

$conn = pg_connect("dbname=".$ds);
/* end settings*/


/*new uplink cm */
// Database Settings
$setting['db']['type'] = "pgsql"; // mysql or pgsql
$setting['db']['server'] = "127.0.0.1";
$setting['db']['port'] = 5432; // 3306 for mysql & 5432 for postgresql
$setting['db']['user'] = "postgres";
$setting['db']['password'] = "mac1ntosh";
$setting['db']['name'] = "ucm";


putenv("PGHOST=".$setting['db']['server']);
putenv("PGUSER=".$setting['db']['user']);

$connput = pg_connect("dbname=".$setting['db']['name']);
/* end settings*/

?>