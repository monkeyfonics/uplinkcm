<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

$ident = $_GET['ident'];
$invid = $_GET['invid'];
$inoid = $_GET['inoid'];
	
/* invoice */
$query = "
	select		$acco.invoice_def.id as id,
				$acco.invoice_def.ident as ident,
				$acco.invoice_def.end_date as end_date,
  				$acco.invoice_def.recurring as recurring,
  				$acco.invoice_def.header as defhead,
				$acco.invoice_out.header as header,
				$acco.invoice_out.pid as pid,
				$acco.invoice_out.cid as cid,
  				$acco.invoice_out.loc as loc,
  				$acco.invoice_out.id as outid,
  				$acco.invoice_out.addhead as addhead,
  				$acco.invoice_out.def_id as def_id,
  				$acco.invoice_out.invoice_id as invoice_id,
  				$acco.invoice_out.runid as runid,
  				$acco.invoice_out.created as created_out,
  				$acco.invoice_out.dated as dated_out,
  				$acco.invoice_out.due_date as due_date_out,
  				$acco.invoice_out.ref as ref,
  				$acco.invoice_out.pub as pub,
  				$acco.invoice_out.printed as printed,
  				$acco.invoice_out.emailed as emailed
	from		$acco.invoice_def left OUTER JOIN $acco.invoice_out ON ($acco.invoice_def.ident = $acco.invoice_out.def_id)
	where		$acco.invoice_out.id = $inoid;
	
	
";

$in = pg_query($conn, $query);

$in_r = pg_fetch_array($in);

/*contact */
$query = "
	select		$acco.invoice_out.pid,
				$acco.contacts.id as coid,
				$acco.contacts.lname as lname,
				$acco.contacts.fname as fname,
				$acco.contacts.email as email
	FROM 		$acco.contacts LEFT OUTER JOIN $acco.invoice_out ON ($acco.contacts.id = $acco.invoice_out.pid)
	where		$acco.contacts.id = $in_r[pid]
	
";

$cl = pg_query($conn, $query);
$cl_r = pg_fetch_array($cl);

/*company */
$query = "
	select		$acco.invoice_out.cid,
				$acco.company.id as cmid,
				$acco.company.name as name
	FROM 		$acco.company LEFT OUTER JOIN $acco.invoice_out ON ($acco.company.id = $acco.invoice_out.cid)
	where		$acco.company.id = $in_r[cid]
	
";

$co = pg_query($conn, $query);
$co_r = pg_fetch_array($co);


$datedcount = new DateTime($in_r[dated_out]);
$duecount = new DateTime($in_r[due_date_out]);

$diff = $duecount->diff($datedcount)->format("%a");

$in_r[created_out] = date('Y-m-d', strtotime($in_r[created_out]));
$in_r[dated_out] = date('Y-m-d', strtotime($in_r[dated_out]));
$in_r[due_date_out] = date('Y-m-d', strtotime($in_r[due_date_out]));



$email_id = $cl_r[coid];

if ($in_r[cid]) {
	$contact_link = "
		<a href='index.php?section=company&template=company_view&suid=$co_r[cmid]'>
			<div class='header'>$co_r[name]</div>
		</a>
	";
	
} else {
	$contact_link = "
		<a href='index.php?section=contacts&template=contact_view&suid=$cl_r[coid]'>
			<div class='header'>$cl_r[lname], $cl_r[fname]</div>
		</a>
	";
}

if ($in_r[printed]) {
	$prin = "&bull;";
} else {
	$prin = " ";
}
if ($in_r[emailed]) {
	$emai = "&bull;";
} else {
	$emai = " ";
}


/*use buttons row */
echo "
	<div class='buttons'>
		$contact_link
		
		
		
		<a href='index.php?section=invoice&template=invoice_view&inoid=$inoid&ident=$in_r[ident]'>
			<div class='header'>{$lng->__('Invoices')} - $in_r[header]</div>
		</a>
		";
		/* if invoice is published*/
		if ($in_r[pub] == t) {
			echo "
				<a href='out.php?section=invoice&t=invoice_print&ident=$in_r[ident]&inoid=$inoid' target='blank'>
					<div>{$lng->__('Print')} $prin</div>
				</a>
				";
				if ($cl_r[email]) {
					echo "
					<a href='transaction.php?section=invoice&t=invoice_email&ident=$in_r[ident]&inoid=$inoid&eid=$email_id' onclick='return confirm(\"{$lng->__('Send to')} $cl_r[email]?\");'>
						<div>{$lng->__('Email')} $emai</div>
					</a>
					";
				}
				
		} else {
			echo "
				<a href='transaction.php?section=invoice&t=invoice_pub&invid=$in_r[invoice_id]&inoid=$inoid'>
					<div>{$lng->__('Publish')}</div>
				</a>
			";
			/* only edit if not published */
			echo "
				<a href='index.php?section=invoice&template=invoice_edit&inoid=$inoid&invid=$in_r[invoice_id]' onclick='return confirm(\"{$lng->__('Edit')}?\");'>
					<div>{$lng->__('Edit')}</div>
				</a>
			";
			/* only delete if not published */
			echo "
				<a href='transaction.php?t=del_invoice&invid=$in_r[invoice_id]&inoid=$inoid&acco=$acco' onclick='return confirm(\"{$lng->__('Delete')}?\");'>
					<div>{$lng->__('Delete')}</div>
				</a>
			";
		}
		
echo "
		
	</div>
";

$refformat = chunk_split($in_r[ref], 5, ' ');
$invidformat = chunk_split($in_r[invoice_id], 6, ' ');
echo "
	<div class='fullcont'>
		
		
		<table class='grid'>
			<tr>
				<td class='head'>
					{$lng->__('Invoice number')}:
				</td>
				<td>
					<a href='index.php?section=invoice&template=invoice_view&inoid=$in_r[outid]&invid=$in_r[invoice_id]'>
						$invidformat
					</a>
				</td>
				<td class='head'>
					{$lng->__('Template header')}:
				</td>
				<td>
					<a href='index.php?section=def&template=def_view&ident=$in_r[def_id]'>$in_r[header]</a>
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Reference')}:
				</td>
				<td>
					$in_r[ref]
				</td>
				<td class='head'>
					{$lng->__('Person')}:
				</td>
				<td>
					<a href='index.php?section=contacts&template=contact_view&suid=$cl_r[coid]'>
						";
						if($cl_r[pid]) { echo "$cl_r[lname], $cl_r[fname]"; }
						echo "
					</a>
				</td>
			</tr>
			<tr>
			";
				if ($in_r[pub] == f) {
					echo "
					<td class='head'>
						{$lng->__('Created')}:
					</td>
					<td>
						$in_r[created_out]
					</td>
					";
				} else {
					echo "
					<td class='head'>
						{$lng->__('Dated')}:
					</td>
					<td>
						$in_r[dated_out]
					</td>
					";
				}
				
				echo "			
				<td class='head'>
					{$lng->__('Company')}:
				</td>
				<td>
					<a href='index.php?section=company&template=company_view&suid=$co_r[cmid]'>
						$co_r[name]
					</a>
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Due date')}:
				</td>
				<td>
					$in_r[due_date_out]
				</td>
				<td class='head'>
					{$lng->__('Language')}:
				</td>
				<td>
					$in_r[loc]
				</td>
			</tr>
			<tr>
				
				<td class='head'>
					{$lng->__('Recurring')}:
				</td>
				<td>
					";
						if ($in_r[recurring] == 0) {
							echo "{$lng->__('No')}";
						} else {
							echo "{$lng->__('Every')} $in_r[recurring] {$lng->__('Month(s)')}";
						}
					echo "
				</td>
				<td class='head'>
					
				</td>
				<td>
					
				</td>
			</tr>
			<tr>
				<td colspan='4'>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Specification')}:
				</td>
				<td colspan='3'>
					$in_r[addhead]
				</td>
			</tr>
		
		</table>
		";
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


		
		echo "	
		
		<table class='list'>
			<tr>
				<th>
					{$lng->__('Cat.')}:
				</th>
				<th>
					{$lng->__('Product')}:
				</th>
				<th>
					{$lng->__('Qty.')}:
				</th>
				<th>
					{$lng->__('Price(0%)')}:
				</th>
				<th>
					{$lng->__('Total (0%)')}:
				</th>
				<th>
					{$lng->__('Vat')}:
				</th>
				<th>
					{$lng->__('Price (Vat)')}:
				</th>
			</tr>
		";
		while ($it_r = pg_fetch_array($it)) {
			$price = $it_r[price];
			$fullprice = $it_r[price] * $it_r[qty];
			$vatprice = $fullprice * $it_r[vat];
			$fullvatprice = $fullprice + $vatprice;
			$invoiceprice += $fullvatprice;
			
			if ($it_r[unit] == 1) {
				$unit = 'hour(s)';
			} elseif ($it_r[unit] == 2) {
				$unit = 'month(s)';
			} elseif ($it_r[unit] == 3) {
				$unit = 'qty';
			}
			
			/* category fetch */
		

		$query = "
			select		id,
						name,
		  				vat
			from		public.list_invoice_group
			where		id = $it_r[cat]
			
			
		";
		
		$ig = pg_query($conn, $query);
		
		$ig_r = pg_fetch_array($ig);
			
		echo "
			<tr>
				<td>
					$ig_r[name]
				</td>
				<td>
					$it_r[item]
				</td>
				<td>
					$it_r[qty] $unit
				</td>
				<td>
					".number_format($price,2,","," ")." &euro;
				</td>
				<td>
					".number_format($fullprice,2,","," ")." &euro;
				</td>
				<td>
					$it_r[vat] %
				</td>
				<td>
					".number_format($fullvatprice,2,","," ")." &euro;
				</td>
			</tr>
			";
		}
		echo "
			<tr>
				<td class='bold'>
					{$lng->__('Total')}:
				</td>
				<td>
					
				</td>
				<td>
					
				</td>
				<td>
					
				</td>
				<td>
					
				</td>
				<td>
					
				</td>
				<td class='bold'>
					".number_format($invoiceprice,2,","," ")." &euro;
				</td>
			</tr>
		</table>
		
	</div>
	";
?>