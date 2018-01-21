<?php

$invid = $_GET['invoice_id'];
$inoid = $_GET['inoid'];
$acco = $_GET['acco'];


/* invoice */
$query = "
	select		$acco.invoice_pdf_link.id as id,
				$acco.invoice_pdf_link.filename as filename,
  				$acco.invoice_pdf_link.invoice_id as invoice_id,
  				$acco.invoice_pdf_link.sent as sent,
  				$acco.invoice_pdf_link.recieved as recieved,
  				$acco.invoice_pdf_link.recipient as recipient
	from		$acco.invoice_pdf_link
	where		$acco.invoice_pdf_link.invoice_id = $invid;
";

$in = pg_query($conn, $query);

$in_r = pg_fetch_array($in);

echo "
	<h2>$in_r[invoice_id]</h2>
<a href='out.php?section=invoice&t=invoice_download&filename=$in_r[filename]&acco=$acco' target='blank'>$in_r[filename]</a>
";
echo "tewt";
?>