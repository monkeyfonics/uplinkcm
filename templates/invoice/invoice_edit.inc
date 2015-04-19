<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.inc';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];


if (!$_GET['inid']) {
	$inid = 0;
	
} else {
	$inid = $_GET['inid'];
}

if (!$_GET['suid']) {
	$suid = 0;
	
} else {
	$suid = $_GET['suid'];
}

/* invoice */
$query = "
	select		$acco.invoice_def.id as id,
				$acco.invoice_def.end_date as end_date,
  				$acco.invoice_def.recurring as recurring,
				$acco.invoice_out.header as header,
				$acco.invoice_out.pid as pid,
				$acco.invoice_out.cid as cid,
  				$acco.invoice_out.loc as loc,
  				$acco.invoice_out.id as outid,
  				$acco.invoice_out.addhead as addhead,
  				$acco.invoice_out.invoice_id as invoice_id,
  				$acco.invoice_out.created as created_out,
  				$acco.invoice_out.ref as ref
	from		$acco.invoice_def left OUTER JOIN $acco.invoice_out ON ($acco.invoice_def.id = $acco.invoice_out.invoice_id)
	where		$acco.invoice_out.id = $inid;
	
	
";

$in = pg_query($conn, $query);

$in_r = pg_fetch_array($in);

/*contacts */
$query = "
	select		id,
				fname,
				lname,
				bill_addr,
  				bill_zip,
  				bill_city,
  				bill_country,
  				email,
  				phone,
  				www,
  				loc
	from		$acco.contacts
	
";

$ul = pg_query($conn, $query);


if ($suid) {
	
	$in_r[pid] = $suid;
} else {
	$wher = '';
}

/*companies*/
$query = "
	select		id,
				name,
				ytunnus,
				www,
  				bill_addr,
  				bill_zip,
  				bill_city,
  				bill_country,
  				email,
  				phone
	from		$acco.company
	$wher
";

$cl = pg_query($conn, $query);

if (!$inid) {
	$header = 'New';
	$in_r[created] = date('Y-m-d');
	$oneWeek = date('Y-m-d', strtotime('+1 week')) ;
	$in_r[due_date] = $oneWeek;
	
} else {
	$header = $in_r[header];
}

/*use buttons row */
echo "
	<form action='transaction.php?t=invoice_edit' method='post' id='insave'>
	<input type='hidden' name='inid' value='$in_r[id]'/>
	<input type='hidden' name='acco' value='$acco'/>
	<input type='hidden' name='ref' value='$in_r[ref]'/>
	
	<div class='buttons'>
		<a href='index.php?section=invoice&template=invoice_view&inid=$in_r[id]'>
			<div class='header'>$header</div>
		</a>
		
			<button formid='insave' class='usebutton'>{$lng->__('Save Invoice')}</button>
		
	</div>
";

echo "
	<div class='fullcont'>
		
		
		<table class='grid'>
			<tr>
				<td class='head'>
					Reference:
				</td>
				<td>
					 $in_r[ref]
				</td>
				<td class='head'>
					Header:
				</td>
				<td>
					<input type='text' name='header' value='$in_r[header]'></input>
				</td>
			</tr>
			<tr>
				<td class='head'>
					Created:
				</td>
				<td>
					<input type='text' name='created' value='$in_r[created]'></input>
				</td>
				<td class='head'>
					Person:
				</td>
				<td>
					<select name='pid'>
						<option value='0'>
							None
						</option>
						";
						while ($ul_r = pg_fetch_array($ul)) {
							if ($ul_r[id] == $in_r[pid]) $sel=" selected"; else $sel="";
							echo "
								<option value='$ul_r[id]' $sel>
									$ul_r[lname], $ul_r[fname]
								</option>
							";
						}
					echo "
					</select>
				</td>
			</tr>
			<tr>
				<td class='head'>
					End Date:
				</td>
				<td>
					<input type='text' name='due_date' value='$in_r[due_date]'></input>
						
				</td>
				<td class='head'>
					Company:
				</td>
				<td>
					<select name='cid'>
						<option value='0'>
							None
						</option>
						";
						while ($cl_r = pg_fetch_array($cl)) {
							if ($cl_r[id] == $in_r[cid]) $sel=" selected"; else $sel="";
							echo "
								<option value='$cl_r[id]' $sel>
									$cl_r[name]
								</option>
							";
						}
					echo "
					</select>
				</td>
			</tr>
			<tr>
				<td class='head'>
					Recurring:
				</td>
				<td>
				";
					if ($in_r[recurring] == 0) $sel0=" selected"; else $sel0="";
					if ($in_r[recurring] == 1) $sel1=" selected"; else $sel1="";
					if ($in_r[recurring] == 2) $sel2=" selected"; else $sel2="";
					if ($in_r[recurring] == 3) $sel3=" selected"; else $sel3="";
					if ($in_r[recurring] == 6) $sel6=" selected"; else $sel6="";
					if ($in_r[recurring] == 12) $sel12=" selected"; else $sel12="";
				echo "
					<select name='recurring'>
						<option value='0'$sel0>
							None
						</option>
						
						<option value='1'$sel1>
							Every Month
						</option>
						<option value='2'$sel2>
							Every 2 Months
						</option>
						<option value='3'$sel3>
							Every 3 Months
						</option>
						<option value='6'$sel6>
							Every 6 Months
						</option>
						<option value='12'$sel12>
							Every 12 Months
						</option>
					</select>
				</td>
				<td class='head'>
					Language:
				</td>
				<td>
					<select name='loc'>
						<option value='fi'>
							Suomi
						</option>
						<option value='sv'>
							Svenska
						</option>
						<option value='en'>
							English
						</option>
					</select>
				</td>
			</tr>
			
		
		";
		
		echo "	
		</table>
		";
		/*invoice items*/
		
$query = "
	select		id,
				item,
				invoice_id,
				price,
  				qty,
  				unit,
  				vat
	from		$acco.invoice_out_item
	where		invoice_id = $inid
	
";

$it = pg_query($conn, $query);


		
		echo "	
		
		<table class='list'>
			<tr>
				<th>
					Item:
				</th>
				<th>
					Qty:
				</th>
				<th>
					price:
				</th>
				<th>
					Vat:
				</th>
			</tr>
		";
		while ($it_r = pg_fetch_array($it)) {
			$price = $it_r[price] / 100;
			if ($it_r[unit] = 1) {
				$unit = 'hour';
			} elseif ($it_r[unit] = 2) {
				$unit = 'month';
			} elseif ($it_r[unit] = 3) {
				$unit = 'qty';
			}
		echo "
			<tr>
				<td>
					<input class='full' type='text' name='item' value='$it_r[item]'></input>
				</td>
				<td>
					<input type='text' name='item' value='$it_r[qty]'></input> $unit
				</td>
				<td>
					<input type='text' name='price' value='".number_format($price,2,","," ")."'></input>
				</td>
				
				<td>
					<input type='text' name='item' value='$it_r[vat]'></input>
				</td>
			</tr>
			";
		}
		echo "
		</table>
		</form>
	</div>
	";
?>