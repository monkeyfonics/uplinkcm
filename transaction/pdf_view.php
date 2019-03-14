<?php

$s_lvl = $_SESSION['s_lvl'];

$invid = $_GET['invoice_id'];
$sendid = $_GET['send_id'];
$inoid = $_GET['inoid'];
$acco = $_GET['acco'];
$pin = $_GET['pin'];

/*choose which id to use*/
if ($_GET['send_id']) {
	$activeid = "where		$acco.invoice_pdf_link.send_id = $sendid";
} elseif ($_GET['invoice_id']) {
	$activeid = "where		$acco.invoice_pdf_link.invoice_id = $invid";
}

/*fetch the account*/

$query = "
	select		public.accounts.id,
				public.accounts.name as name,
				public.accounts.identy,
				public.accounts.active
	from		public.accounts
	where		public.accounts.identy = '$acco'
	
";

$acp = pg_query($conn, $query);
$acp_r = pg_fetch_array($acp);

/* invoice pdf link */
$query = "
	select		$acco.invoice_pdf_link.id as id,
				$acco.invoice_pdf_link.filename as filename,
  				$acco.invoice_pdf_link.invoice_id as invoice_id,
  				$acco.invoice_pdf_link.send_id as send_id,
  				$acco.invoice_pdf_link.sent as sent,
  				$acco.invoice_pdf_link.pin as pin,
  				$acco.invoice_pdf_link.recieved as recieved,
  				$acco.invoice_pdf_link.recipient as recipient
	from		$acco.invoice_pdf_link
	$activeid
";

$in = pg_query($conn, $query);

$in_r = pg_fetch_array($in);

if ($in_r[sent]) {
	$sent_date = date('Y-m-d H:m:s', strtotime($in_r[sent]));
} else {
	$sent_date = "{$lng->__('Not sent yet')}";
}



if ($pin == $in_r[pin]) {
	echo "
	<h2>{$lng->__('New invoice from')} $acp_r[name]</h2>
	ID nr: $in_r[send_id]<br>
	{$lng->__('Sent')}: $sent_date<br>

	<h3>
		<a href='out.php?section=invoice&t=invoice_download&filename=$in_r[filename]&acco=$acco&send_id=$in_r[send_id]&pin=$pin' target='blank'>$in_r[filename].pdf</a>
	</h3>
";
} else {
	echo "
		<h2>{$lng->__('Wrong pin')}</h2>
	";
}



if ($_GET['send_id']) {
	/* update recieved flag*/
	$query = "
				update $acco.invoice_pdf_link
				set		recieved=now()
						
				where	send_id=$sendid
			";
	$if = pg_query($conn, $query);
}

?>