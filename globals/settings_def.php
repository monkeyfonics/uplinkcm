<?php

// Database Settings
$setting['db']['type'] = "pgsql"; // mysql or pgsql
$setting['db']['server'] = "127.0.0.1";
$setting['db']['port'] = 5432; // 3306 for mysql & 5432 for postgresql
$setting['db']['user'] = "postgres";
$setting['db']['password'] = "password";
$setting['db']['name'] = "ucm";


putenv("PGHOST=".$setting['db']['server']);
putenv("PGUSER=".$setting['db']['user']);

$GLOBALS['apath']="/var/www/uplinkcm";
$rpath="/";
$wname="cm.uplink.fi";
$system_email="info@uplink-data.fi";

// SMTP settings

$smtphost = "smtp.domain.com";
$smtpusername = "user@domain.com";
$smtppassword = "pass";
?>