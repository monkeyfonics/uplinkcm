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
  				$acco.invoice_pdf_link.sent as sent,
  				$acco.invoice_pdf_link.recieved as recieved,
  				$acco.invoice_pdf_link.recipient as recipient
	from		$acco.invoice_pdf_link
	
";

$pdf = pg_query($conn, $query);

$pdf_r = pg_fetch_array($pdf);



$pdfname = "{$custlng->__('Invoice')}_".$acc_hold_r[name]."_".$in_r[invoice_id];
$pdfoutput = $filepath.$pdfname;
$query = "
			insert into $acco.invoice_pdf_link (
			invoice_id,
			filename,
			recipient,
			pin,
			sent
			) values (
			$in_r[invoice_id],
			'$pdfname',
			'$cl_r[email]',
			'1234',
			now()
			)
		";
$pdf = pg_query($conn, $query);

/*save pdf to file*/
$invoice->savef($pdfoutput);

?>
