<?php
/* add php mailer script*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

/*email invoice to contact*/

require 'invoice_gen.php';

/* recheck invoice link */
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
	where		$acco.invoice_pdf_link.invoice_id = $in_r[invoice_id]
	
";

$pdf = pg_query($conn, $query);

$pdf_r = pg_fetch_array($pdf);


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

/*get the send id*/
$send_id = $pdf_r[send_id];
/*get the send id*/
$pin = $pdf_r[pin];


/*update invoice emailed status */

$query = "
			update $acco.invoice_out
			set		emailed=now()
			where	id=$inoid
		";
$ch = pg_query($conn, $query);

/*update pdflink sent status */

$query = "
			update $acco.invoice_pdf_link
			set		sent=now()
					
			where	send_id=$send_id
		";
$pd = pg_query($conn, $query);



$invoice->invoice($info);

$invoice->fetch("uplink_".$in_r[ident]);

//$attachment = chunk_split(base64_encode($invoice->fetch()));

if (file_exists("pdf/".$acco."/".$pdf_r[filename].".pdf")) {
   $attachment = "pdf/".$acco."/".$pdf_r[filename].".pdf";
} else {
    $attachment = "";
}


$due = date('d.m.Y', strtotime($in_r['due_date_out']));
$amount = $total+$vattot;
$amount = number_format($amount,2,".","");

$pdflink = "http://$wname/public.php?section=invoice&t=pdf_view&send_id=$send_id&acco=$acco&pin=$pin";


/* send mail to recipient */
$mailu = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    $mailu->SMTPDebug = 0;                                 // Enable verbose debug output 0 off, 1 = errors and messages,  2 = messages only
    $mailu->isSMTP();                                      // Set mailer to use SMTP
    $mailu->CharSet = 'UTF-8';
    $mailu->Host = $smtphost;  								// Specify main and backup SMTP servers
    $mailu->SMTPAuth = true;                               // Enable SMTP authentication
    $mailu->Username = $smtpusername;                 		// SMTP username
    $mailu->Password = $smtppassword;                           // SMTP password
    $mailu->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mailu->Port = 587;                                    // TCP port to connect to

    //Recipients
    $mailu->setFrom('cm@uplink.fi', 'Contact Manager');
    $mailu->addAddress($tocontact, $name);     // Add a recipient
    $mailu->addReplyTo('info@uplink-data.fi', 'Information');

    //Attachments
    $mailu->addAttachment($attachment);         // Add attachments

    //Content
    $mailu->isHTML(true);                                  // Set email format to HTML
    $mailu->Subject = "{$custlng->__('New invoice from')} ".$accountname;
    $mailu->Body    = "
    {$custlng->__('New invoice from')} ".$accountname." <br>
	{$custlng->__('Invoice number')}: $in_r[invoice_id] <br>
	{$custlng->__('Due date')}: $due <br>
	{$custlng->__('Amount')}: $amount €<br>
	{$custlng->__('See attachment for pdf')}<br>
	{$custlng->__('Virtual barcode')}: $virtual<br>
	<br>
	<a href='$pdflink'>{$custlng->__('Download PDF')}</a>
	";
	//if client cant accept html
    $mailu->AltBody = "
    {$custlng->__('New invoice from')} ".$accountname." \r\n
	{$custlng->__('Invoice number')}: $in_r[invoice_id] \r\n
	{$custlng->__('Due date')}: $due \r
	{$custlng->__('Amount')}: $amount €\r\n
	{$custlng->__('See attachment for pdf')}\r\n
	{$custlng->__('Virtual barcode')}: $virtual\r\n
	$pdflink
	";

    $mailu->send();
    $message = "{$lng->__('Mail sent')}";
} catch (Exception $e) {
    //echo "{$lng->__('Mail failed')}", $mailu->ErrorInfo;
    $message = "{$lng->__('Mail failed')}";
}


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