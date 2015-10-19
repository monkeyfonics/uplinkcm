<?php

require 'includes/session.php';
require 'includes/accountcheck.php';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

/* include pdf building function */
include ("includes/reports.php");

/*language*/
$l = $_SESSION['lang'];

$lngpdf = new Translator($l);//$outputlanguage: ISO code (example: de,en,fi,sv...) --> these are the names of each file
 
$lngpdf->setPath('lang/pdf/');


/*fetch timespan*/
$dated = $_GET['dated'];
$minus = $_GET['minus'];
$acco = $_GET['acco'];

$accountname = $ac_r[name];

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
					$acco.invoice_out.def_id as def_id,
					$acco.invoice_out.invoice_id as invoice_id,
					$acco.invoice_out.created as created_out,
					$acco.invoice_out.dated as dated_out,
					$acco.invoice_out.ref as ref,
					$acco.invoice_out.pub as pub
		from		$acco.invoice_out LEFT JOIN $acco.invoice_def
		ON			($acco.invoice_out.def_id = $acco.invoice_def.ident)
		where		$acco.invoice_out.dated between '$minus' and '$dated'
		and			$acco.invoice_out.pub = true
		order by	$acco.invoice_out.dated desc
		
				
		
	";

$monthly = pg_query($conn, $query);	








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
	
	
/*check for company*/
if ($monthly_r[cid]) {
	/*get name for company*/
					
$query = "
	select		id,
				name,
				ytunnus
	from		$acco.company
	where		id = $monthly_r[cid]
				
";
			
$com = pg_query($conn, $query);
$com_r = pg_fetch_array($com);

/*combine contact company name */
$contactcom = $co_r[lname].", ".$co_r[fname]." - ".$com_r[name];

} else {
/*combine contact name */
$contactcom = $co_r[lname].", ".$co_r[fname];
}
	
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
				where		invoice_id = $monthly_r[invoice_id]
				
			";
			
			$it = pg_query($conn, $query);

			$combprice;
			while ($it_r = pg_fetch_array($it)) {
					$tempprice1 = $it_r[price] * $it_r[qty];
					$tempprice2 = $tempprice1 * $it_r[vat];
					if ($it_r[invoice_id]== $last_id) {
						$combprice += ($tempprice1 + $tempprice2);
					} else {
						$combprice = ($tempprice1 + $tempprice2);
					}
					
					$last_id = $it_r[invoice_id];
				}
	/*format price */
	
	//$formatprice =  str_replace('.', ',', $combprice);
	$formatprice = number_format($combprice, 2, ',', '');
	/*format ref number*/
	$refformat = chunk_split($monthly_r[ref], 5, ' ');
	/*format date */
	$date = strtotime($monthly_r[dated_out]);
	$month = date('F', $date);
	$date = date('Y-m-d', $date);
	
	$totalprice = $totalprice + $combprice;
	
	/*fields of data for report*/
	$fields[] = array(
	"date"=>$date,
	"ref"=>$refformat, 
	"contact"=>$contactcom,
	"amount"=>$formatprice, 
	"name"=>$monthly_r[header]." - ".$monthly_r[addhead]
	);
	
}
/*Write last line*/
$formattotalprice =  number_format($totalprice, 2, ',', '');
$endfield = array("{$lngpdf->__('Total')}", '', '', $formattotalprice, '');



$report = new report();

/* pass info to pdf creation */
$info = Array(
	"header" => "{$lngpdf->__('Report for')} {$lngpdf->__($month)}",
	"account" => $accountname,
	"locale" => $accountname,
	"from" => $minus,
	"to" => $dated,
	"hitem" => Array(
		Array(
			"date" => "{$lngpdf->__('Date')}",
			"ref" => "{$lngpdf->__('Reference')}",
			"contact" => "{$lngpdf->__('Contact')}",
			"amount" => "{$lngpdf->__('Amount')}",
			"name" => "{$lngpdf->__('Invoice')}",
			),
		),
	"items" => $fields,
	"totalname" => "{$lngpdf->__('Total')}",
	"total" => $formattotalprice,
	);

$report->content($info);

$report->display("_report");

?>