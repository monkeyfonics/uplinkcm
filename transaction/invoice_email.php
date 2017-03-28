<?php

/*email invoice to contact*/

require 'invoice_gen.php';

$email_id= $_GET['eid'];

/*account email*/
$sent = "$acc_hold_r[email]";
$accountname = "$acc_hold_r[name]";
/*customer email */
if ($cl_r[email]) {
	$tocontact = "$cl_r[email]";
} else {
	$tocontact = "";
}


/*update invoice print status */

$query = "
			update $acco.invoice_out
			set		emailed=now()
			where	id=$inoid
		";
$ch = pg_query($conn, $query);
	
$invoice->invoice($info);

//$invoice->fetch("uplink_".$in_r[ident]);

$attachment = chunk_split(base64_encode($invoice->fetch()));

$due = date('d.m.Y', strtotime($in_r['due_date_out']));
$amount = $total+$vattot;
$amount = number_format($amount,2,".","");

/* send mail to recipient */
$random_hash = md5(date('r', time()));
$charset = "\r\nMIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\r\ncharset=UTF-8";
$from = $sent;
$to = $tocontact;
$headers = "From:" . $from.$charset;
$subject = "{$custlng->__('New invoice from')} ".$accountname;
$subject = mb_encode_mimeheader($subject,'UTF-8','Q');

$accnameformat = preg_replace("/[^A-Za-z0-9]/", '', $acc_hold_r[name]);
$attachname = "{$custlng->__('Invoice')}_".$accnameformat."_".$in_r[invoice_id]."";

$plainpart = "
--PHP-mixed-$random_hash
Content-Type: text/plain; charset=UTF-8\r\n
{$custlng->__('New invoice from')} ".$accountname." \r\n
{$custlng->__('Invoice number')}: $in_r[invoice_id] \r\n
{$custlng->__('Due date')}: $due \r
{$custlng->__('Amount')}: $amount €\r\n
{$custlng->__('See attachment for pdf')}\r\n
{$custlng->__('Virtual barcode')}: $virtual";
$attachpart = "
--PHP-mixed-$random_hash
Content-Type: application/pdf; name=".$attachname.".pdf
Content-Transfer-Encoding: base64 
Content-Disposition: attachment 

$attachment
--PHP-mixed-$random_hash--";

$body = $plainpart.$attachpart;
/* send the mail */
$mail_sent = @mail($to,$subject,$body,$headers);


/* send mail to user */
$to2 = $sent;
$subject2 = "{$lng->__('Confirmation of sent invoice to')} ".$tocontact;
$subject2 = mb_encode_mimeheader($subject2,'UTF-8','Q');

$plainpart2 = "
--PHP-mixed-$random_hash
Content-Type: text/plain; charset=UTF-8\r\n
{$lng->__('Confirmation of sent invoice to')} ".$tocontact." \r\n
{$lng->__('New invoice from')} ".$accountname." \r\n
{$lng->__('Invoice number')}: $in_r[invoice_id] \r\n
{$lng->__('Due date')}: $due \r
{$lng->__('Amount')}: $amount €\r\n
{$lng->__('See attachment for pdf')}\r\n
{$lng->__('Virtual barcode')}: $virtual";

$body2 = $plainpart2.$attachpart;

/*send confirmation to site user*/
$mail_sent2 = @mail($to2,$subject2,$body2,$headers);

$mail_sent ? $message = "{$lng->__('Mail sent')}" : $message = "{$lng->__('Mail failed')}"; 

/*exit*/
$ret_url = 'index.php?section=invoice&template=invoice_view&inoid='.$in_r[outid].'&ident='.$in_r[ident].'&invid='.$in_r[invoice_id];
$icon = 'layout/img/icon_succ.png';

header("Refresh: 2; URL=".$ret_url);

echo "
	<div class='messagebox'>
		<img class='messageicon' src='$icon' alt='$message'>
		<p class='messagetext'>$message</p>
	</div>
	";

?>