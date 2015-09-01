<?php
	$usid = $_GET['usid'];
	
	
	$query = "
	select		id,
				login,
				fname,
				lname
	from		users
	where id = '$usid'
	
";

$ac = pg_query($conn, $query);

$ac_r = pg_fetch_array($ac);

$charset = "Content-Type: text/plain; charset=UTF-8\r\n";
$to = "tommy@uplink.fi";
$subject = "Test mail";
$message = "Hello! This is a simple email message.\r\n";
$from = "info@uplink.fi";
$headers = $charset."From:" . $from;
mail($to,$subject,$message,$headers);
echo "Mail Sent.";
?>