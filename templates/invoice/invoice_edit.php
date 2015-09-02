<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];


if (!$_GET['inoid']) {
	$inoid = 0;
	
} else {
	$inoid = $_GET['inoid'];
}
if ($_GET['invid']) {
	
	$invid = $_GET['invid'];
}

if (!$_GET['suid']) {
	$suid = 0;
	
} else {
	$suid = $_GET['suid'];
}

/* invoice */
$query = "
	select		$acco.invoice_def.id as id,
				$acco.invoice_def.ident as ident,
				$acco.invoice_def.end_date as end_date,
  				$acco.invoice_def.recurring as recurring,
  				$acco.invoice_out.id as id_out,
				$acco.invoice_out.header as header,
				$acco.invoice_out.pid as pid,
				$acco.invoice_out.cid as cid,
  				$acco.invoice_out.loc as loc,
  				$acco.invoice_out.id as outid,
  				$acco.invoice_out.addhead as addhead,
  				$acco.invoice_out.def_id as def_id,
  				$acco.invoice_out.invoice_id as invoice_id,
  				$acco.invoice_out.created as created,
  				$acco.invoice_out.dated as dated,
  				$acco.invoice_out.due_date as due_date,
  				$acco.invoice_out.ref as ref,
  				$acco.invoice_out.pub as pub
	from		$acco.invoice_def left OUTER JOIN $acco.invoice_out ON ($acco.invoice_def.ident = $acco.invoice_out.def_id)
	where		$acco.invoice_out.id = $inoid;
	
	
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



$in_r[dated] = date('Y-m-d', strtotime($in_r[dated]));
$in_r[due_date] = date('Y-m-d', strtotime($in_r[due_date]));

/*use buttons row */
echo "
	<form action='transaction.php?t=invoice_edit' method='post' id='insave'>
	<input type='hidden' name='inoid' value='$in_r[id_out]'/>
	<input type='hidden' name='ident' value='$in_r[ident]'/>
	<input type='hidden' name='acco' value='$acco'/>
	<input type='hidden' name='ref' value='$in_r[ref]'/>
	
	<div class='buttons'>
		<a href='index.php?section=invoice&template=invoice_view&inoid=$in_r[id]'>
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
					Invoice Nr.:
				</td>
				<td>
					 $in_r[ident]
				</td>
				<td class='head'>
					Header:
				</td>
				<td>
					<input class='full' type='text' name='header' value='$in_r[header]'></input>
				</td>
			</tr>
			<tr>
				<td class='head'>
					Reference:
				</td>
				<td>
					 $in_r[ref]
				</td>
				<td class='head'>
					Add Header:
				</td>
				<td>
					<input class='full' type='text' name='addheader' value='$in_r[addhead]'></input>
				</td>
			</tr>
			<tr>
				<td class='head'>
					Dated:
				</td>
				<td>
					<input type='text' name='dated' value='$in_r[dated]'></input>
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
					Due Date:
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
					
				</td>
				<td>
					
				</td>
				<td class='head'>
					Language:
				</td>
				<td>
				";
					/*check users language somehow if new template*/
						if ($in_r[loc] == 'fi') $sel1=" selected='selected'"; else $sel1="";
						if ($in_r[loc] == 'sv') $sel2=" selected='selected'"; else $sel2="";
						if ($in_r[loc] == 'en') $sel3=" selected='selected'"; else $sel3="";
					
				echo "
					<select name='loc'>
						<option value='fi' $sel1>
							Suomi
						</option>
						<option value='sv' $sel2>
							Svenska
						</option>
						<option value='en' $sel3>
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

/*invoice item category */

$query = "
	select		id,
				name,
  				vat
	from		public.list_invoice_group
	order by 	id
	
	
";

$ig = pg_query($conn, $query);

		
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
					Quantity:
				</th>
				<th>
					Price:
				</th>
				<th>
					Vat:
				</th>
				<th>
					Price %:
				</th>
				<th>
					Add/Del
				</th>
			</tr>
		";
		$unit1 = "{$lng->__('Hour')}";
		$unit2 = "{$lng->__('Month')}";
		$unit3 = "{$lng->__('Qty')}";
		
		/* category fetch */
			while ($ig_r = pg_fetch_array($ig)) {
						
				$cat_ar[] = $ig_r;
				
					}
		
		while ($it_r = pg_fetch_array($it)) {
			$price = $it_r[price];
			$vatprice = $it_r[price] * $it_r[vat];
			$fullvatprice = $price + $vatprice;
			/*Units */
			
			if ($it_r[unit] == 1) {
				
				$usel1 = " selected='selected'";
				$usel2 = ' ';
				$usel3 = ' ';
			} elseif ($it_r[unit] == 2) {
				
				$usel1 = ' ';
				$usel2 = " selected='selected'";
				$usel3 = ' ';
			} elseif ($it_r[unit] == 3) {
				
				$usel1 = ' ';
				$usel2 = ' ';
				$usel3 = " selected='selected'";
			}
		echo "
			<input type='hidden' name='ids[]' value='$it_r[id]'/>
			<tr>
				<td>
					<select class='short' name='cat[$it_r[id]]' id='$it_r[id]' onchange='changeOldVat(this);'>
						";
						foreach ($cat_ar as $catr) {
    						if ($it_r[cat] == $catr[id]) {
								$sel = " selected='selected'";
							}else{
								$sel = " ";
							}
						
						echo "
						<option value='$catr[id]' id='$catr[vat]' $sel>
							$catr[name]
						</option>
						";
						
						}
					
					echo "
					</select>
				</td>
				<td>
					<input class='full' type='text' name='item[$it_r[id]]' value='$it_r[item]'></input>
				</td>
				<td>
					<input class='short' type='text' name='qty[$it_r[id]]' value='$it_r[qty]'></input>
					
					<select class='short' name='unit[$it_r[id]]'>
						<option value='1' $usel1>
							$unit1
						</option>
						<option value='2' $usel2>
							$unit2
						</option>
						<option value='3' $usel3>
							$unit3
						</option>
					</select>
				</td>
				<td>
					<input class='short' type='text' id='price_$it_r[id]' name='price[$it_r[id]]' value='".number_format($price,2,"."," ")."' onchange='addVat($it_r[id]);'></input>
				</td>
				
				<td>
					<input class='short' type='text' id='vat_$it_r[id]' name='vat[$it_r[id]]' value='$it_r[vat]'></input>
				</td>
				<td>
					<input class='short' type='text' id='pricevat_$it_r[id]' name='pricevat[$it_r[id]]' value='".number_format($fullvatprice,2,"."," ")."' onchange='stripVat($it_r[id]);'></input>
				</td>
				<td>
					<input type='submit' name='del_item[$it_r[id]]' value='{$lng->__('Delete')}'></input>
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