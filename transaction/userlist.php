<?php

$levl = $_POST['lvl'];
$usd = $_POST['usid'];

$query = "
			update users
			set		lvl=$levl
			where 	id = $usd
		";
$ch = pg_query($conn, $query);

$ret_url = 'index.php?template=main';
$message = 'Update Successfull';
$icon = 'layout/img/icon_succ.png';

echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message $usid</p>
			</div>
			";

?>