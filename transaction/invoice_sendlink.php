<?php
	/* invoice pdf link */
	$query = "
		select		$acco.invoice_pdf_link.id as id,
					$acco.invoice_pdf_link.filename as filename,
	  				$acco.invoice_pdf_link.invoice_id as invoice_id,
	  				$acco.invoice_pdf_link.send_id as send_id,
	  				$acco.invoice_pdf_link.sent as sent,
	  				$acco.invoice_pdf_link.recieved as recieved,
	  				$acco.invoice_pdf_link.recipient as recipient,
	  				$acco.invoice_pdf_link.active as active,
		from		$acco.invoice_pdf_link
		
	";
	
	$if = pg_query($conn, $query);
	
	$if_r = pg_fetch_array($if);



?>