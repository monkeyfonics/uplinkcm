<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];


if (!$_GET['ident']) {
	$ident = 0;
	
} else {
	$ident = $_GET['ident'];
}



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
  				recurring
	from		$acco.invoice_def
	where		ident = $ident
	
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
  				phone1,
  				phone2,
  				www,
  				loc
	from		$acco.contacts
	order by	lname, fname
";

$ul = pg_query($conn, $query);

/* get the contact if defined*/
if (!$_GET['suid']) {
	$suid = 0;
	
} else {
	$suid = $_GET['suid'];
	$in_r[pid] = $suid;
	
	/* specific contact */
$query = "
	select		id,
				fname,
				lname,
				bill_addr,
  				bill_zip,
  				bill_city,
  				bill_country,
  				email,
  				phone1,
  				phone2,
  				www,
  				loc
	from		$acco.contacts
	where		id = $in_r[pid]
";

$spco = pg_query($conn, $query);
$spco_r = pg_fetch_array($spco);
}
/* get the company if defined*/
if (!$_GET['coid']) {
	$coid = 0;
	
} else {
	$coid = $_GET['coid'];
	$in_r[cid] = $coid;
}
if ($in_r[pid]) {
/* only companies for that contact*/
$query = "
		select		{$acco}.company.id as id,
					{$acco}.company.name as name,
					{$acco}.company.ytunnus as ytunnus		
		from		$acco.company LEFT JOIN $acco.link_company_contact
		ON			($acco.company.id = $acco.link_company_contact.company_id)
		where		{$acco}.link_company_contact.contact_id = $in_r[pid]
		order by	name
";
$speccomp = pg_query($conn, $query);
} else {
	
}
if ($in_r[cid]) {
/* only contacts for that company*/
$query = "
		select		{$acco}.contacts.id as id,
					{$acco}.contacts.fname as fname,
					{$acco}.contacts.lname as lname,
					{$acco}.link_company_contact.prim as prim
		from		$acco.contacts LEFT JOIN $acco.link_company_contact
		ON			($acco.contacts.id = $acco.link_company_contact.contact_id)
		where		{$acco}.link_company_contact.company_id = $in_r[cid]
		order by	lname, fname	
";
$speccont = pg_query($conn, $query);
} else {
	
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
	order by	name
	
";

$cl = pg_query($conn, $query);

if (!$ident) {
	$blank = 1;
	$header = "{$lng->__('New Template')}";
	$in_r[dated] = date('Y-m-d');
	
	$in_r[next_create] = date('Y-m-d', strtotime($in_r[dated]));
	$oneWeek = date('Y-m-d', strtotime('+1 week'));
	$in_r[end_date] = date('Y-m-d', strtotime($in_r[dated]));

} else {
	$blank = 0;
	$header = $in_r[header];
}
$in_r[created] = date('Y-m-d', strtotime($in_r[created]));
$in_r[dated] = date('Y-m-d', strtotime($in_r[dated]));
$in_r[next_create] = date('Y-m-d', strtotime($in_r[next_create]));
$in_r[end_date] = date('Y-m-d', strtotime($in_r[end_date]));

/*checks to see if it is ongoing */
if ($in_r[ongoing] == t) {
				$ongoing1 = " ";
				$ongoing2 = " selected='selected' ";
				
			} else {
				$ongoing1 = " selected='selected' ";
				$ongoing2 = " ";
				
			}
/* check if ongoing is active or recurring is 0 */
if ($in_r[ongoing] == t or $in_r[recurring] == 0) {
				
				$enddate_display = "hidden";
				$enddate_display_n = "text";
				
			} else {
				
				$enddate_display = "text";
				$enddate_display_n = "hidden";
				
			}

/*use buttons row */
echo "
	<form action='transaction.php?t=def_edit' method='post' id='insave'>
	<input type='hidden' name='inid' value='$in_r[id]'/>
	<input type='hidden' name='ident' value='$in_r[ident]'/>
	<input type='hidden' name='acco' value='$acco'/>
	<input type='hidden' name='ref' value='$in_r[ref]'/>
	<input type='hidden' name='blank' value='$blank'/>
	
	<div class='buttons'>
		<a href='index.php?section=invoice&template=invoice_view&inid=$in_r[id]'>
			<div class='header'>$header</div>
		</a>
		
			<button formid='insave' class='usebutton'>{$lng->__('Save Template')}</button>
		
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
					<input type='text' name='dated' value='$in_r[dated]'></input>
				</td>
				<td class='head'>
					{$lng->__('Header')}:
				</td>
				<td>
					<input class='full' type='text' name='header' value='$in_r[header]'></input>
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Next invoice')}:
				</td>
				<td>
					
					<input type='hidden' name='next_create' value='$in_r[next_create]'></input>
					<input type='text' name='next_create_show' disabled value='$in_r[next_create]'></input>
				</td>
				<td class='head'>
					{$lng->__('Person')}:
				</td>
				<td>
					<select name='pid'>
						<option value='0'>
							{$lng->__('None')}
						</option>
						";
						
						while ($speccont_r = pg_fetch_array($speccont)) {
							if ($speccont_r[prim] == t) {
								$sel = " selected='selected'";
							} else {
								$sel = " ";
							}
							//if ($speccomp_r[id] == $in_r[cid]) $sel=" selected='selected'"; else $sel="";
							echo "
								<option value='$speccont_r[id]' $sel>
									$speccont_r[lname], $speccont_r[fname]
								</option>
							";
						}
					echo "
						<option value='0' disabled>
							----
						</option>
						";
						while ($ul_r = pg_fetch_array($ul)) {
							if ($ul_r[id] == $in_r[pid]) $sel=" selected='selected'"; else $sel="";
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
					{$lng->__('Ongoing')}:
				</td>
				<td>
					
					
					<select id='ongoing' name='ongoing' onchange='disableEnd();'>
						<option value='f' $ongoing1>
							{$lng->__('No')}
						</option>
						<option value='t' $ongoing2>
							{$lng->__('Yes')}
						</option>
						
					</select>
				</td>
				<td class='head'>
					{$lng->__('Company')}:
				</td>
				<td>
					<select name='cid'>
						<option value='0'>
							{$lng->__('None')}
						</option>
						
						";
						while ($speccomp_r = pg_fetch_array($speccomp)) {
							//if ($speccomp_r[id] == $in_r[cid]) $sel=" selected='selected'"; else $sel="";
							echo "
								<option value='$speccomp_r[id]'>
									$speccomp_r[name]
								</option>
							";
						}
					echo "
						<option value='0' disabled>
							----
						</option>
						";
						while ($cl_r = pg_fetch_array($cl)) {
							if ($cl_r[id] == $in_r[cid]) $sel=" selected='selected'"; else $sel="";
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
					{$lng->__('Recurring')}:
				</td>
				<td>
				";
					if ($in_r[recurring] == 0) $sel0=" selected='selected'"; else $sel0="";
					if ($in_r[recurring] == 1) $sel1=" selected='selected'"; else $sel1="";
					if ($in_r[recurring] == 2) $sel2=" selected='selected'"; else $sel2="";
					if ($in_r[recurring] == 3) $sel3=" selected='selected'"; else $sel3="";
					if ($in_r[recurring] == 6) $sel6=" selected='selected'"; else $sel6="";
					if ($in_r[recurring] == 12) $sel12=" selected='selected'"; else $sel12="";
				echo "
					<select id='rec' name='recurring' onchange='disableEnd();'>
						<option value='0'$sel0>
							{$lng->__('None')}
						</option>
						
						<option value='1'$sel1>
							{$lng->__('Every')} {$lng->__('Month')}
						</option>
						<option value='2'$sel2>
							{$lng->__('Every')} 2 {$lng->__('Months')}
						</option>
						<option value='3'$sel3>
							{$lng->__('Every')} 3 {$lng->__('Months')}
						</option>
						<option value='6'$sel6>
							{$lng->__('Every')} 6 {$lng->__('Months')}
						</option>
						<option value='12'$sel12>
							{$lng->__('Every')} 12 {$lng->__('Months')}
						</option>
					</select>
				</td>
				
				<td class='head'>
					{$lng->__('Language')}:
				</td>
				<td>
				";
					/*check users language somehow if new template*/
					if (!$_GET['suid']) {
						if ($in_r[loc] == 'fi') $sel1=" selected='selected'"; else $sel1="";
						if ($in_r[loc] == 'sv') $sel2=" selected='selected'"; else $sel2="";
						if ($in_r[loc] == 'en') $sel3=" selected='selected'"; else $sel3="";
					} else {
						$userloc = $ul_r[loc];
						if ($spco_r[loc] == 'fi') $sel1=" selected='selected'"; else $sel1="";
						if ($spco_r[loc] == 'sv') $sel2=" selected='selected'"; else $sel2="";
						if ($spco_r[loc] == 'en') $sel3=" selected='selected'"; else $sel3="";
					}
						
					
				echo "
					<select name='loc'>
						<option value='fi' $sel1>
							{$lng->__('Finnish')}
						</option>
						<option value='sv' $sel2>
							{$lng->__('Swedish')}
						</option>
						<option value='en' $sel3>
							{$lng->__('English')}
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('End date')}:
				</td>
				<td>
					<input type='$enddate_display' id='end' name='end_date' value='$in_r[end_date]'></input>
					<input type='$enddate_display_n' id='endshown' name='end_date_show' value='None' disabled ></input>
					
				</td>
				<td colspan='2'>
				</td>
			</tr>
			
		
		";
		
		echo "	
		</table>
		";
		
		
		
/*
* * invoice items
 */
		
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
					{$lng->__('Vat')}:
				</th>
				<th>
					{$lng->__('Price (Vat)')}:
				</th>
				<th>
					{$lng->__('Modify')}
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
		
		/* invoice items */
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
		/* empty row*/
		echo "
			
			<tr>
				<td>
					
					<select class='short' name='catn' id='catn' onchange='changeVat();'>
						
				";
					foreach ($cat_ar as $catr2) {
    						if ($it_r[cat] == $catr2[id]) {
								$sel = " selected='selected'";
								$vate = $catr2[vat];
							}else{
								$sel = " ";
							}
						
						echo "
						
						<option id='$catr2[vat]' value='$catr2[id]' $sel>
							$catr2[name]
						</option>
						";
						
						}
					echo"
				</td>
				<td>
					<input class='full' type='text' name='itemn' value=''></input>
				</td>
				<td>
					<input class='short' type='text' name='qtyn' value=''></input>
					
					<select class='short' name='unitn'>
						<option value='1'>
							$unit1
						</option>
						<option value='2'>
							$unit2
						</option>
						<option value='3'>
							$unit3
						</option>
					</select>
				</td>
				<td>
					<input class='short' type='text' id='pricen' name='pricen' value='' onchange='addVatn();'></input>
				</td>
				
				<td>
					<input class='short' type='text' name='vatn' id='vatn' value='0.24'></input>
				</td>
				<td>
					<input class='short' type='text' id='pricevatn' name='pricevatn' value='' onchange='stripVatn();'></input>
				</td>
				<td>
					<input type='submit' name='add_item' value='{$lng->__('Add item')}'></input>
				</td>
			</tr>
			";
		
		echo "
		</table>
		</form>
	</div>
	";
?>