<?php

$dated = $_GET['dated'];
$minus = $_GET['minus'];
$acco = $_GET['acco'];

/*fetch accounts list to check for nr */

/* invoice */
$query = "
		select		{$acco}.invoice_def.id as id,
					{$acco}.invoice_def.ident as ident,
					{$acco}.invoice_def.end_date as end_date,
					{$acco}.invoice_def.recurring as recurring,
					{$acco}.invoice_out.header as header,
					{$acco}.invoice_out.pid as pid,
					{$acco}.invoice_out.cid as cid,
					$acco.invoice_out.loc as loc,
					$acco.invoice_out.id as outid,
					$acco.invoice_out.addhead as addhead,
					$acco.invoice_out.invoice_id as invoice_id,
					$acco.invoice_out.created as created_out,
					$acco.invoice_out.dated as dated_out,
					$acco.invoice_out.ref as ref,
					$acco.invoice_out.pub as pub
		from		$acco.invoice_out LEFT JOIN $acco.invoice_def
		ON			($acco.invoice_out.invoice_id = $acco.invoice_def.ident)
		where		$acco.invoice_out.dated between '$minus' and '$dated'
		order by	$acco.invoice_out.dated desc
		
				
		
	";

$monthly = pg_query($conn, $query);	



/* doesnt write to disk*/
$file = fopen('php://output', 'w');

/* write headers */

$headers = array('Dated', 'Reference', 'Name', 'Amount', 'Header');
fputcsv($file, $headers);
$fields;
while ($monthly_r = pg_fetch_assoc($monthly)) {
	
/*get name for contact*/
					
$query = "
	select		id,
				lname,
				fname
	from		$acco.contacts
	where		id = $monthly_r[pid]
				
";
			
$co = pg_query($conn, $query);
$co_r = pg_fetch_array($co);
		
	
	/*invoice items for counting total cost*/
					
			$query = "
				select		id,
							item,
							invoice_id,
							price,
			  				qty,
			  				unit,
			  				vat
				from		$acco.invoice_out_item
				where		invoice_id = $monthly_r[ident]
				
			";
			
			$it = pg_query($conn, $query);

			$combprice;
			while ($it_r = pg_fetch_array($it)) {
					$tempprice1 = $it_r[price] * $it_r[qty];
					$tempprice2 = $tempprice1 * $it_r[vat];
					$combprice = ($tempprice1 + $tempprice2);
				}
	/*format price */
	$formatprice =  str_replace('.', ',', $combprice);
	/*format ref number*/
	$refformat = chunk_split($monthly_r[ref], 5, ' ');
	/*format date */
	$date = strtotime($monthly_r[dated_out]);
	$date = date('Y-m-d', $date);
	
	$totalprice = $totalprice + $combprice;
	
	
	
	$fields = array($date, $refformat, $co_r[lname].", ".$co_r[fname], $formatprice, $monthly_r[header]." - ".$monthly_r[addhead]);
	/*input into csv */
	fputcsv($file, $fields);
}
/*Write last line*/
$formattotalprice =  str_replace('.', ',', $totalprice);
$endfield = array('Total', '', '', $formattotalprice, '');

fputcsv($file, $endfield);

fclose($file);


$filename = "$apath/tempfiles/".$acco."/monthly_".$dated.".csv";
/*serve file */
	header('Content-Encoding: UTF-8');
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment;filename="monthly_'.$dated.'.csv"');
	header('Cache-Control: max-age=0');

    header('Content-Description: File Transfer');
    //header('Content-Type: application/octet-stream');
    //header('Content-Disposition: attachment; filename='.basename($file));
    //header('Expires: 0');
    //header('Cache-Control: must-revalidate');
    //header('Pragma: public');
    //header('Content-Length: ' . filesize($file));
   
   echo $file;
   //readfile($file);
    


		
?>