<?php

	$accountname = $_POST['aname'];
	$vatnr = $_POST['vat'];
	$user = $_POST['user'];
	$contact = $_POST['contact'];

$query = "
	select		id,
				fname,
				lname,
				login,
				tel
	from		users
	where id = $user
	
";

$us = pg_query($conn, $query);

$us_r = pg_fetch_array($us);

$userinfo = $us_r[login]." \r\n".$us_r[lname].", ".$us_r[fname]." \r\nTel.:".$us_r[tel];

	/*email content*/
	$addr = $system_email;
	$to = $addr;
	$subject = "Account Requested for CM";
	$email = "Hello!\r\nThere has been a request for a new account on Uplink contact manager. \r\nAccount name:".$accountname." \r\nVat-nr:".$vatnr." \r\nBy user: ".$userinfo." \r\nContact by:".$contact;
	
	$from = "cm@uplink.fi";
	$charset = "Content-Type: text/plain; charset=UTF-8\r\n";
	$headers = $charset."From:" . $from;
	
	$ret_url = 'index.php?template=account_sel';
	$message = 'Account requested for '.$accountname;
	$icon = 'layout/img/icon_succ.png';
	
	
	mail($to,$subject,$email,$headers);
	
	echo "
	<div class='messagebox'>
		<img class='messageicon' src='$icon' alt='$message'>
		<p class='messagetext'>$message</p>
	</div>
	";

	header("Refresh: 3; URL=".$ret_url);

?>