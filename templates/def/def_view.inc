<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.inc';


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
					Dated:
				</td>
				<td>
					$in_r[dated]
				</td>
				<td class='head'>
					Header:
				</td>
				<td>
					$in_r[header]
				</td>
			</tr>
			<tr>
				<td class='head'>
					Next Invoice:
				</td>
				<td>
					$in_r[next_create]
				</td>
				<td class='head'>
					Person:
				</td>
				<td>
					$cl_r[lname], $cl_r[fname]
				</td>
			</tr>
			<tr>
				<td class='head'>
					Ongoing:
				</td>
				<td>
					$ongoing
				</td>
				<td class='head'>
					Company:
				</td>
				<td>
					$co_r[name]
				</td>
			</tr>
			<tr>
				<td class='head'>
					Recurring:
				</td>
				<td>
					Every $in_r[recurring] month(s)
				</td>
				
				<td class='head'>
					Language:
				</td>
				<td>
					$in_r[loc]
				</td>
			</tr>
			<tr>
				<td class='head'>
					End date:
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
				invoice_id,
				price,
  				qty,
  				unit,
  				vat
	from		$acco.invoice_def_item
	where		invoice_id = $ident
	order by	item
	
";

$it = pg_query($conn, $query);


		
		echo "	
		
		<table class='list'>
			<tr>
				<th>
					Cat.:
				</th>
				<th>
					Item:
				</th>
				<th>
					Qty:
				</th>
				<th>
					Price(0%):
				</th>
				<th>
					Full price(0%):
				</th>
				<th>
					Vat:
				</th>
				<th>
					Price (Vat):
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
					$it_r[vat]
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
					Total:
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