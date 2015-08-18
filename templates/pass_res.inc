<?php
require 'includes/PasswordHash.inc';


$login = $_GET['login'];
$oldhash = $_GET['passh'];
$expires = $_GET['expires'];

$datenu = date('Y-m-d:H');

/* check if date is too old*/
if ($expires > $datenu) {
	$query = "
	select		id,
				login,
				pass,
				lang
	from		users
	where		login = '$login'
";

$ch = pg_query($conn, $query);

$ch_r = pg_fetch_array($ch);

$hasher = new PasswordHash($hash_cost_log2, $hash_portable);

$hash = $ch_r[pass];


/* generate new hash */
	
	$salt = "yolo";
	$newhash = hash('sha256', $login.$hash.$salt.$expires);


	if ($newhash == $oldhash) {
			$message = 'Authentication succeeded';
			$ret_url = 'index.php?template=account_sel';
			$icon = 'layout/img/icon_succ.png';
			
			
			echo "
			<div class='login'>
				<h2>{$lng->__('New password')}</h2>
				<form action='transaction.php?t=check_reg' method='post'>
				<input type='hidden' name='op' value='reset'/>
				<input type='hidden' name='resetlogin' value='$ch_r[login]'/>
				<table class='login' align='center'>
					<tr>
						<td>
							{$lng->__('Email')}:
						</td>
						<td>
							$login
						</td>
					</tr>
					<tr>
						<td>
							{$lng->__('New password')}:
						</td>
						<td>
							<input type='password' name='pass'>
						</td>
					</tr>
					
					<tr>
						<td colspan='2' style='text-align: center;'>
							<input class='cbutton bstyle' type='submit' value='{$lng->__('Reset')}'>
						</td>
					</tr>
				</table>
				
				</form>
			
			</div>
			
			";
			
			
	} else {
			$message = 'Authentication failed';
			$ret_url = 'index.php?template=login';
			$icon = 'layout/img/icon_fail.png';
			echo "{$lng->__('Please try again')}";
	}
} else {
	echo "{$lng->__('Time expired on link')}, {$lng->__('Please try again')}.";
}






?>