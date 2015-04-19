<?php

include("globals/init.inc");

include("headers/header.inc");

include("includes/topbar.inc");

if (!$_GET[section]) {

	$_GET[section] = "";
}

if (!$_GET[template]) {
		
	
	if ($_SESSION['s_id'] == 0) {
		$_GET[template] = "login";
	} else {
		$_GET[template] = "main";
	}
	
}




echo "<div class='main_box'>";

include("templates/$_GET[section]/$_GET[template].inc");

echo "</div>";

include("headers/footer.inc");

?>