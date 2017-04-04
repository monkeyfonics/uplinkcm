<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

$ident = $_GET['ident'];
	
/* invoice */
$query = "
	select		id,
				ident,
				header,
				pid,
				cid,
  				loc,
  				created,
  				dated,
  				ongoing,
  				end_date,
  				next_create,
  				recurring,
  				active
	from		$acco.invoice_def
	where		ident = $ident
	
";

$in = pg_query($conn, $query);

$in_r = pg_fetch_array($in);


/* fecth invoices for selected template */
$query = "
	select		id,
				header,
				addhead,
				pid,
				cid,
  				loc,
  				invoice_id,
  				def_id,
  				created,
  				dated,
  				due_date,
  				ref,
  				pub
	from		$acco.invoice_out
	where		def_id = $in_r[ident]
	order by	dated desc
	
";

$def = pg_query($conn, $query);


/*contact */
$query = "
	select		$acco.invoice_def.cid,
				$acco.contacts.id as coid,
				$acco.contacts.lname as lname,
				$acco.contacts.fname as fname
	FROM 		$acco.contacts LEFT OUTER JOIN $acco.invoice_def ON ($acco.contacts.id = $acco.invoice_def.pid)
	where		$acco.contacts.id = $in_r[pid]
	
";

$cl = pg_query($conn, $query);
$cl_r = pg_fetch_array($cl);

/*company */
$query = "
	select		$acco.invoice_def.cid,
				$acco.company.id as cmid,
				$acco.company.name as name
	FROM 		$acco.company LEFT OUTER JOIN $acco.invoice_def ON ($acco.company.id = $acco.invoice_def.cid)
	where		$acco.company.id = $in_r[cid]
	
";

$co = pg_query($conn, $query);
$co_r = pg_fetch_array($co);

/*invoice item category */

$query = "
	select		id,
				name,
  				vat
	from		public.list_invoice_group
	order by 	id
	
	
";

$ig = pg_query($conn, $query);



$in_r[created] = date('Y-m-d', strtotime($in_r[created]));
$in_r[dated] = date('Y-m-d', strtotime($in_r[dated]));
$in_r[next_create] = date('Y-m-d', strtotime($in_r[next_create]));
$in_r[end_date] = date('Y-m-d', strtotime($in_r[end_date]));

if ($in_r[active] == t) {
				$activate = "{$lng->__('Deactivate')}";
			} else {
				$activate = "{$lng->__('Activate')}";
			}

			
/*checks to see if there is no end date */
if ($in_r[ongoing] == t) {
				$ongoing = "{$lng->__('Yes')}";
				$end_date = " ";
			} else {
				$ongoing = "{$lng->__('No')}";
				$end_date = $in_r[end_date];
			}
/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=def&template=def_view&ident=$in_r[ident]'>
			<div class='header'>{$lng->__('Invoice Templates')} - $in_r[header] - $in_r[ident]</div>
		</a>
		<a href='index.php?section=def&template=def_edit&ident=$in_r[ident]'>
			<div>{$lng->__('Edit Template')}</div>
		</a>
		<a href='transaction.php?t=def_act&ident=$in_r[ident]&acco=$acco'>
			<div>$activate</div>
		</a>
		<a href='transaction.php?t=def_copy&ident=$in_r[ident]&acco=$acco'>
			<div>{$lng->__('Copy Template')}</div>
		</a>
	</div>
";

echo "
	<div class='fullcont'>
		
		
		<table class='grid'>
			<tr>
				<td class='head'>
					{$lng->__('Dated')}:
				</td>
				<td>
					$in_r[dated]
				</td>
				<td class='head'>
					{$lng->__('Header')}:
				</td>
				<td>
					$in_r[header]
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Next invoice')}:
				</td>
				<td>
					$in_r[next_create]
				</td>
				<td class='head'>
					{$lng->__('Person')}:
				</td>
				<td>
					";
						if($cl_r[coid]) { echo "$cl_r[lname], $cl_r[fname]"; }
					echo "
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Ongoing')}:
				</td>
				<td>
					$ongoing
				</td>
				<td class='head'>
					{$lng->__('Company')}:
				</td>
				<td>
					$co_r[name]
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
					{$lng->__('Language')}:
				</td>
				<td>
					$in_r[loc]
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('End date')}:
				</td>
				<td>
					$end_date
				</td>
				<td class='head'>
					
				</td>
				<td>
					
				</td>
			</tr>
		
		
		</table>
		";
/*invoice items*/
		
$query = "
	select		id,
				cat,
				item,
				def_id,
				price,
  				qty,
  				unit,
  				vat
	from		$acco.invoice_def_item
	where		def_id = $ident
	order by	item
	
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
		
		$vat_clear = $it_r[vat] * 100;
		
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
					$vat_clear %
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
	
	echo "
		<div class='fullcont'>
			<h3 class='header'>{$lng->__('Invoices')}</h3>
			<table class='list'>
				<tr>
						<th>
							{$lng->__('Dated')}
						</th>
						<th>
							{$lng->__('Header')}
						</th>
						<th>
							{$lng->__('Invoice number')}
						</th>
					</tr>
			
		";
		while ($def_r = pg_fetch_array($def)) {
			/* format dates*/
			$def_r[dated] = date('Y-m-d', strtotime($def_r[dated]));
				echo "
					<tr>
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$def_r[id]&ident=$def_r[def_id]&invid=$def_r[invoice_id]'>
								$def_r[dated]
							</a>
						</td>
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$def_r[id]&ident=$def_r[def_id]&invid=$def_r[invoice_id]'>
								$def_r[header] - $def_r[addhead]
							</a>
						</td>
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$def_r[id]&ident=$def_r[def_id]&invid=$def_r[invoice_id]'>
								$def_r[invoice_id]
							</a>
						</td>
					</tr>
					
					";
			}
	echo "
			</table>
		</div>
	";
?>