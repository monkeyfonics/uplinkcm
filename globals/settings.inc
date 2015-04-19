<?php

// Database Settings
$setting['db']['type'] = "pgsql"; // mysql or pgsql
$setting['db']['server'] = "127.0.0.1";
$setting['db']['port'] = 5432; // 3306 for mysql & 5432 for postgresql
$setting['db']['user'] = "postgres";
$setting['db']['password'] = "mac1ntosh";
$setting['db']['name'] = "ucm";


putenv("PGHOST=".$setting['db']['server']);
putenv("PGUSER=".$setting['db']['user']);

$GLOBALS['apath']="/var/www/web/production/uplinkcm";
$rpath="/";
$wname="cm.uplink.fi";
?>