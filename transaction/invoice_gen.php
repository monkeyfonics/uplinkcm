<?php

require 'includes/session.php';
require 'includes/accountcheck.php';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

/* include pdf building function */
include ("includes/sepa_v2.php");

$invoice = new sepa();

/* fetch info for invoice */

$ident = $_GET['ident'];
$inoid = $_GET['inoid'];
	
/*account holder */

$query = "
	select		public.accounts.id,
				public.accounts.name,
				public.accounts.vatnr,
				public.accounts.bank_account,
				public.accounts.bic,
				public.accounts.bill_addr as bill_addr,
				public.accounts.bill_zip as bill_zip,
				public.accounts.bill_city as bill_city,
				public.accounts.bill_country as bill_country,
				public.accounts.bill_email as email,
				public.accounts.bill_tel as tel
	FROM 		public.accounts
	where		public.accounts.id = $ac_r[id]
	
";

$acc_hold = pg_query($conn, $query);
$acc_hold_r = pg_fetch_array($acc_hold);
	
	
/* invoice */
$query = "
	select		$acco.invoice_def.id as id,
				$acco.invoice_def.ident as ident,
				$acco.invoice_def.end_date as end_date,
  				$acco.invoice_def.recurring as recurring,
				$acco.invoice_out.header as header,
				$acco.invoice_out.pid as pid,
				$acco.invoice_out.cid as cid,
  				$acco.invoice_out.loc as loc,
  				$acco.invoice_out.id as outid,
  				$acco.invoice_out.addhead as addhead,
  				$acco.invoice_out.invoice_id as invoice_id,
  				$acco.invoice_out.runid as runid,
  				$acco.invoice_out.def_id as def_id,
  				$acco.invoice_out.created as created_out,
  				$acco.invoice_out.dated as dated_out,
  				$acco.invoice_out.due_date as due_date_out,
  				$acco.invoice_out.ref as ref
	from		$acco.invoice_def left OUTER JOIN $acco.invoice_out ON ($acco.invoice_def.ident = $acco.invoice_out.def_id)
	where		$acco.invoice_out.id = $inoid;
	
	
";

$in = pg_query($conn, $query);

$in_r = pg_fetch_array($in);

/*contact */
$query = "
	select		$acco.invoice_out.cid,
				$acco.contacts.id as coid,
				$acco.contacts.lname as lname,
				$acco.contacts.fname as fname,
				$acco.contacts.bill_addr as bill_addr,
				$acco.contacts.bill_zip as bill_zip,
				$acco.contacts.bill_city as bill_city,
				$acco.contacts.bill_country as bill_country,
				$acco.contacts.email as email,
				$acco.contacts.loc as loc,
				$acco.contacts.phone1 as phone1
	FROM 		$acco.contacts LEFT OUTER JOIN $acco.invoice_out ON ($acco.contacts.id = $acco.invoice_out.pid)
	where		$acco.contacts.id = $in_r[pid]
	
";

$cl = pg_query($conn, $query);
$cl_r = pg_fetch_array($cl);


if ($in_r[cid] == 0) {
	/*payer without company*/
	$name = $cl_r['fname']." ".$cl_r['lname'];
	$contact = "";
	$addr = $cl_r['bill_addr'];
	$zip = $cl_r['bill_zip'];
	$city = $cl_r['bill_city'];
	$country = $cl_r['bill_country'];
		
	
} else {
/*company */
$query = "
	select		$acco.invoice_out.cid,
				$acco.company.id as cmid,
				$acco.company.name as name,
				$acco.company.ytunnus as ytunnus,
				$acco.company.bill_addr as bill_addr,
				$acco.company.bill_zip as bill_zip,
				$acco.company.bill_city as bill_city,
				$acco.company.bill_country as bill_country
	FROM 		$acco.company LEFT OUTER JOIN $acco.invoice_out ON ($acco.company.id = $acco.invoice_out.cid)
	where		$acco.company.id = $in_r[cid]
	
";

$co = pg_query($conn, $query);
$co_r = pg_fetch_array($co);
				
	/*payer with company */
	$name = $co_r['name'];
	$contact = $cl_r['fname']." ".$cl_r['lname'];
	$addr = $co_r['bill_addr'];
	$zip = $co_r['bill_zip'];
	$city = $co_r['bill_city'];
	$country = $co_r['bill_country'];
		
	
}

/*creating customer language */
$customerlang = $cl_r[loc];

$custlng = new Translator($customerlang);//$outputlanguage: ISO code (example: de,en,fi,sv...) --> these are the names of each file
 
$custlng->setPath('lang/');

/*invoice items*/
		
$query = "
	select		id,
				def_id,
				cat,
				item,
				invoice_id,
				price,
  				qty,
  				unit,
  				vat
	from		$acco.invoice_out_item
	where		invoice_id = $in_r[invoice_id]
	
";

$it = pg_query($conn, $query);



/* items def */
while ($it_r = pg_fetch_array($it)) {
		
	if ($it_r[unit] == 1) {
		$unit = 'hour';
	} elseif ($it_r[unit] == 2) {
		$unit = 'month';
	} elseif ($it_r[unit] == 3) {
		$unit = 'qty';
	}
	
	/*amount per item, non vat*/	
	$itemprice = number_format($it_r['price'],2,".","");
	/*amount for all items, non vat*/
	$totalrow = $itemprice * $it_r['qty'];
	/*amount for all items, just vat*/
	$totalrowvat = $totalrow * $it_r['vat'];
	/*amount for all rows, non vat*/
	$total += $itemprice * $it_r['qty'];
	$vatpart = $itemprice * $it_r['vat'];
	$totalpart = $itemprice + $vatpart;
	$qtytotal = ($itemprice + $vatpart) * $it_r['qty'];
	/*amount for all vat*/
	$vattot += $totalrowvat;
	/*amount for all total*/
	$totalvat = $total + $vat;
	
	$tx = explode(",",$it_r['item'],2);
	if (count($tx) < 2)
		$tx = explode(" ",$it_r['item'],2);
	if (count($tx) < 2 && strlen(trim($it_r['item'])) > 15)
		$tx = Array('',$it_r['item']);
	$items[] = Array(
		"date"=>date("d.m.Y",$in_r['created_out']),
		"prod"=>trim($tx[0]),
		"text"=>trim($tx[1]),
		"qty"=>$it_r['qty'],
		"unit"=>$unit,
		"price"=>$itemprice,
		"discount"=>'',
		"vat"=>$vatpart,
		"vatpros"=>$it_r['vat'],
		"total"=>$qtytotal,
		);
		
}
//virtual barcode
/*spec: version1 + iban16 + euro6 + cent2 + 000 + ref20 + due6yymmdd + check2 */
$t = $total+$vattot;
$t = round($t, 2);
$ver = 4;
$iban =  trim($acc_hold_r['bank_account'],"fiFI ");
$amount = explode(".", $t);
$eur = str_pad($amount[0], 6, "0", STR_PAD_LEFT);
$cnt = str_pad($amount[1], 2, "0", STR_PAD_LEFT);
$mdl = '000';
$refvir = str_pad($in_r['ref'], 20, "0", STR_PAD_LEFT);
$duevir = date('ymd', strtotime($in_r['due_date_out']));
$virtual = $ver.$iban.$eur.$cnt.$mdl.$refvir.$duevir;

//$logo ="";
$logo="$apath/acc_img/".$acco."/logo.png";

$tel = $acc_hold_r['tel'];
$email = $acc_hold_r['email'];

$created = date('d.m.Y', strtotime($in_r['created_out']));
$dated = date('d.m.Y', strtotime($in_r['dated_out']));
$due = date('d.m.Y', strtotime($in_r['due_date_out']));

/*for counting days untill due*/
$datedcount = new DateTime($in_r[dated_out]);
$duecount = new DateTime($in_r[due_date_out]);

$diff = $duecount->diff($datedcount)->format("%a");
/*format invoice id*/
$invidformat = chunk_split($in_r[invoice_id], 6, ' ');

/* pass info to pdf creation */
$info = Array(
	"nr" => $invidformat,
	"dat" => $dated,
	"due" => $due,
	"terms" => $diff,
	"custref" => $in_r['ref'],
	"ref" => $in_r['ref'],
	"rate" => "8",
	"home" => "Inkoo/Ingå",
	"logo" => $logo,
	"locale" => $in_r['loc'],
	"specification" => $in_r['addhead'],
	"recipient" => Array(
		"name" => $acc_hold_r['name'],
		"street" => $acc_hold_r['bill_addr'],
		"zip" => $acc_hold_r['bill_zip'],
		"city" => $acc_hold_r['bill_city'],
		"country" => $acc_hold_r['bill_country'],
		"phone" => $tel,
		"email" => $email,
		"vatnr" => $acc_hold_r['vatnr'],
		),
	"payer" => Array(
		"name" => $name,
		"contact" => $contact,
		"street" => $addr,
		"zip" => $zip,
		"city" => $city,
		"country" => $country,
		),
	"accounts" => Array(
		Array(
			"iban" => $acc_hold_r['bank_account'],
			"bic" => $acc_hold_r['bic'],
			),
		),
	"items" => $items,
	"text" => Array(
		"head" => "",
		"tail" => "",
		),
	"total" => $total,
	"vat" => $vattot,
	"virtual" => $virtual,
	);






?>