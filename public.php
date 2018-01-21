<?php

include("globals/init.php");
include("headers/header.php");

include("includes/topbar_plain.php");

echo "<div class='main_box'>";
include("transaction/$_GET[t].php");

echo "</div>";

include("headers/footer.php");
?>