<?php

require 'includes/PasswordHash.inc';

// Base-2 logarithm of the iteration count used for password stretching
$hash_cost_log2 = 8;
// Do we require the hashes to be portable to older systems (less secure)?
$hash_portable = FALSE;





$user = $_POST['email'];

$query = "
	select		id,
				login,
				pass,
				lang
	from		users
	
";

$ch = pg_query($conn, $query);

$wrong = 0;
$right = 0;
$addr = "";
while ($ch_r = pg_fetch_array($ch)) {
	if ($user == $ch_r['login']) {
		$right = 1;
		$addr = "$ch_r[login]";
		$passd = "$ch_r[pass]";
	} else {
		$wrong = 1;
	}
}

if ($right == 0 and $wrong == 1) {
	
	$message = 'No such user: <br/>'.$user.' <br/> Try Again.';
	$ret_url = 'file.php?template=pass_rec';
	$icon = 'layout/img/icon_fail.png';
} elseif ($right == 1) {
	
	$message = 'Found user <br/>'.$addr.'<br/>Sending reset to email.';
	$ret_url = 'index.php';
	$icon = 'layout/img/icon_succ.png';
	
	
	
	
	/* generate new hash */
	$expires = date('Y-m-d:H', strtotime('+24 hours'));
	$salt = "yolo";
	$newhash = hash('sha256', $addr.$passd.$salt.$expires);
	
	/*email content*/
	$to = $addr;
	$subject = "Password Reset";
	$email = "Hello!\r\n There has been a request for the password for this account on Uplink contact manager. \r\nClick here to reset your password: http://$wname/file.php?template=pass_res&login=".$addr."&passh=".$newhash."&expires=".$expires."\r\n Otherwise ignore this email.";
	
	$from = "info@uplink.fi";
	$headers = "From:" . $from;
	mail($to,$subject,$email,$headers);
}


echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message $datenu</p>
			</div>
			";
		
		header("Refresh: 2; URL=".$ret_url);

?>