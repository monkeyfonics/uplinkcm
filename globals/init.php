<?php
session_start();


include("settings.php");
include("local_functions.php");
/*encoding for site*/
mb_internal_encoding("UTF-8");

$conn = pg_connect("dbname=".$setting['db']['name']." user=".$setting['db']['user']." password=".$setting['db']['password']);

if (empty($_SESSION['s_id'])) {
   $_SESSION['s_id'] = 0;
}

if (empty($_SESSION['ac_id'])) {
   $_SESSION['ac_id'] = 0;
} 

if (empty($_SESSION['lang'])) {
   $_SESSION['lang'] = 'en';
}



/* translate */

require_once("includes/trans.php");


$outputlanguage = $_SESSION['lang'];

$lng = new Translator($outputlanguage);//$outputlanguage: ISO code (example: de,en,fi,sv...) --> these are the names of each file
 
$lng->setPath('lang/');

?>