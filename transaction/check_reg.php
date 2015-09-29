<?php

require 'includes/PasswordHash.php';

// Base-2 logarithm of the iteration count used for password stretching
$hash_cost_log2 = 8;
// Do we require the hashes to be portable to older systems (less secure)?
$hash_portable = FALSE;

$user = $_POST['user'];
$pass = $_POST['pass'];
$pass2 = $_POST['pass2'];
$op = $_POST['op'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$tel = $_POST['tel'];
$lang = $_POST['lang'];

$hasher = new PasswordHash($hash_cost_log2, $hash_portable);

$hash = $hasher->HashPassword($pass);
	if (strlen($hash) < 20)
		fail('Failed to hash new password');
	unset($hasher);




/* check for duplicates */
$query = "
	select		id,
				login
	from		users
	
";

$au = pg_query($conn, $query);

$au_r = pg_fetch_array($au);

if ($au_r[login] == $user) {
	$message = 'User Already exists';
	$ret_url = 'index.php?template=login';
} else {
	/* changing old users*/
	if ($op == 'change') {
		$usid = $_POST['usid'];
		$query = "
			update users
			set		pass='$hash'
			where	id=$usid
		";
		
		$ret_url = 'index.php?template=main';
		$message = 'Update Successfull';
		$icon = 'layout/img/icon_succ.png';
	
	/* new users*/
	} elseif ($op == 'new') {
		$_SESSION['s_id'] = 0;
		
		$query = "
		insert into users (
		login,
		pass,
		fname,
		lname,
		tel,
		lang
		) values (
		'$user',
		'$hash',
		'$fname',
		'$lname',
		'$tel',
		'$lang'
		)
		";
	
		$ret_url = 'index.php?template=login';
		$message = 'User created';
		$icon = 'layout/img/icon_succ.png';
		
		/* send mail to admin */
		$charset = "Content-Type: text/plain; charset=UTF-8\r\n";
		$to = $system_email;
		$subject = "CM - New User";
		$email = "New User registered: ".$user;
		$from = "ContactManager";
		$headers = $charset."From:".$from;
		mail($to,$subject,$email,$headers);
		
		/* send mail to user */
		$charset = "Content-Type: text/plain; charset=UTF-8\r\n";
		$to = $user;
		$from = "Contact Manager<$system_email>";
		$headers = $charset."From:".$from."\r\nReply-To:".$system_email;
		$subject = "{$lng->__('From Uplink Contact Manager to')} ".$fname;
		$email = "{$lng->__('You registered with your email')} ".$user." \r\n{$lng->__('Please log in at')} $wname \r\n{$lng->__('If you did not register this adress please contact us at')} $system_email";
		
		mail($to,$subject,$email,$headers);
		
	} elseif ($op == 'reset') {
		$loginad = $_POST['resetlogin'];
		$query = "
			update users
			set		pass='$hash'
			where	login='$loginad'
		";
		
		$ret_url = 'index.php?template=main';
		$message = 'Reset Successfull';
		$icon = 'layout/img/icon_succ.png';
	} else {
		$ret_url = 'index.php?template=main';
		$icon = 'layout/img/icon_fail.png';
	}
}



$ch = pg_query($conn, $query);

header("Refresh: 2; URL=".$ret_url);


echo "
	<div class='messagebox'>
		<img class='messageicon' src='$icon' alt='$message'>
		<p class='messagetext'>$message</p>
	</div>
	";


?>