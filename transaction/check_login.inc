<?php

require 'includes/PasswordHash.inc';

// Base-2 logarithm of the iteration count used for password stretching
$hash_cost_log2 = 8;
// Do we require the hashes to be portable to older systems (less secure)?
$hash_portable = FALSE;

$user = $_POST['user'];
$pass = $_POST['pass'];

$hash = '*'; // In case the user is not found

$hasher = new PasswordHash($hash_cost_log2, $hash_portable);

$_SESSION['s_id']=0;

$query = "
	select		id,
				login,
				pass,
				lang
	from		users
	where		login = '$user'
";

$ch = pg_query($conn, $query);

$ch_r = pg_fetch_array($ch);

$hash = $ch_r[pass];

if ($hasher->CheckPassword($pass, $hash)) {
		$message = 'Authentication succeeded';
		$ret_url = 'index.php?template=account_sel';
		$_SESSION['s_id'] = $ch_r['id'];
		$icon = 'layout/img/icon_succ.png';
} else {
		$message = 'Authentication failed';
		$ret_url = 'index.php?template=login';
		$icon = 'layout/img/icon_fail.png';
}
unset($hasher);

/*set language */
$_SESSION['lang'] = $ch_r[lang];

header("Refresh: 2; URL=".$ret_url);

echo "
	<div class='messagebox'>
		<img class='messageicon' src='$icon' alt='$message'>
		<p class='messagetext'>$message</p>
	</div>
	";

?>