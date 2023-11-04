<?php

include("globals/init.php");

include("headers/header.php");
echo "Under construction";
include("includes/topbar.php");

if (!$_GET['section']) {

	$_GET['section'] = "";
}

if (!$_GET['template']) {
		
	
	if ($_SESSION['s_id'] == 0) {
		$_GET['template'] = "login";
	} else {
		$_GET['template'] = "main";
	}
	
}




echo "<div class='main_box'>";

include("templates/$_GET[section]/$_GET[template].php");

echo "</div>";

include("headers/footer.php");

?>