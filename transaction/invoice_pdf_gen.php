<?php

/*settings for creating pdf file */
$filepath = 'pdf/'.$acco.'/';

/*check if folder exists else create it */
if (file_exists($filepath)) {
	
} else {
	mkdir($filepath, 0777);
	chmod($filepath, 0777);
}

/* invoice */
$query = "
	select		$acco.invoice_pdf_link.id as id,
				$acco.invoice_pdf_link.filename as filename,
  				$acco.invoice_pdf_link.invoice_id as invoice_id,
  				$acco.invoice_pdf_link.send_id as send_id,
  				$acco.invoice_pdf_link.sent as sent,
  				$acco.invoice_pdf_link.recieved as recieved,
  				$acco.invoice_pdf_link.recipient as recipient
	from		$acco.invoice_pdf_link
	where		$acco.invoice_pdf_link.invoice_id = $in_r[invoice_id]
	
";

$pdf = pg_query($conn, $query);

$pdf_r = pg_fetch_array($pdf);

/*remove whitespace*/
$accnameformat = preg_replace("/[^A-Za-z0-9]/", '', $acc_hold_r[name]);

$pdfname = "{$custlng->__('Invoice')}_".$accnameformat."_".$in_r[invoice_id];
$pdfoutput = $filepath.$pdfname;

 /*check if entry exists in link */
 if ($pdf_r[invoice_id]) {
 	// do not create link if it exists
 } else {
 	/*create link to download file for customer if it doesnt exist*/

	$send_idn = rand(10000000, 99999999);
	$pin = rand(1000, 9999);
	
	$query = "
				insert into $acco.invoice_pdf_link (
				invoice_id,
				send_id,
				filename,
				recipient,
				pin,
				active
				) values (
				$in_r[invoice_id],
				$send_idn,
				'$pdfname',
				'$cl_r[email]',
				$pin,
				true
				)
			";
	$pdf = pg_query($conn, $query);
		
 }
 
/*save pdf to file*/
$invoice->savef($pdfoutput);




?>
