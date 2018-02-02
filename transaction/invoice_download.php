<?php
	$filename = $_GET['filename'];
	$sendid = $_GET['send_id'];
	$acco = $_GET['acco'];
	$pin = $_GET['pin'];
	
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
		where		$acco.invoice_pdf_link.send_id = $sendid
	";

$in = pg_query($conn, $query);

$in_r = pg_fetch_array($in);
	
	/* check for pin */
	if ($pin == $in_r[pin]) {
		echo "$filename";
		
		$file_url = 'pdf/'.$acco.'/'.$filename.'.pdf';
		
		echo "$file_url";
		
		header('Content-Type: application/pdf');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
		readfile($file_url); // do the double-download-dance (dirty but worky)
		 
		/* update recieved flag*/
		$query = "
				update $acco.invoice_pdf_link
				set		downloaded=true
						
				where	send_id=$sendid
			";
		$if = pg_query($conn, $query);
	
	} else {
		echo "Wrong pin";
	}
?>