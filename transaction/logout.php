<?php
$_SESSION['s_id'] = 0;
$_SESSION['ac_id'] = 0;

$ret_url = 'index.php?template=login';
$icon = 'layout/img/icon_succ.png';
$message = "{$lng->__('Logout Successfull')}";

echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";



header("Refresh: 2; URL=".$ret_url);

?>