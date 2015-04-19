<?php
$usid = $_POST['usid'];
$lvl = $_POST['lvl'];



$query = "
			update users
			set		lvl=$lvl
			where	id=$usid
		";
$ch = pg_query($conn, $query);

$message = 'Changed level to ';
$ret_url = 'index.php?section=admin&template=user_info&usid=12';
$icon = 'layout/img/icon_succ.png';



echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message $lvl</p>
			</div>
			";
			
header("Refresh: 1; URL=".$ret_url);
?>