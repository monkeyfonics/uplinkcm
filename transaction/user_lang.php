<?php
$s_id = $_SESSION['s_id'];
$lang = $_POST['lang'];

$_SESSION['lang'] = $lang;

$query = "
			update users
			set		lang='$lang'
			where	id=$s_id
		";
$ch = pg_query($conn, $query);

if ($lang == 'sv') {
	$langout = "{$lng->__('Swedish')}";
} elseif ($lang == 'fi') {
	$langout = "{$lng->__('Finnish')}";
} else {
	$langout = "{$lng->__('English')}";
}

$message = "{$lng->__('Selected language')} $langout";
$ret_url = 'index.php?section=admin&template=user_lang';
$icon = 'layout/img/icon_succ.png';
header("Refresh: 1; URL=".$ret_url);

echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";

?>