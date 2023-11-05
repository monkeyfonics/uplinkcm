<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r['identy'];


if (!$_GET['suid']) {
	$suid = 0;
	
} else {
	$suid = $_GET['suid'];
}

/*create full contact list */
$query = "
	select		id,
				fname,
				lname
				
	from		$acco.contacts
	
	
";

$full = pg_query($conn, $query);

while ($full_r = pg_fetch_array($full)) {
	$jsname[] = "$full_r[fname] $full_r[lname]";
	$jsnamef[] = "$full_r[fname]";
	$jsnamel[] = "$full_r[lname]";
	//$jsname[] = "$full_r[id]";
}

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
	where		id = $suid
	
";

$ul = pg_query($conn, $query);

$ul_r = pg_fetch_array($ul);
if (!$suid) {
	$header = 'New Contact';
} else {
	$header = $ul_r['lname'].', '.$ul_r['fname'];

/* existing companies */
$query = "
	select		$acco.link_company_contact.contact_id,
				$acco.link_company_contact.company_id,
				$acco.link_company_contact.prim,
				$acco.company.id as coid,
				$acco.company.name as name,
				$acco.company.ytunnus as ytunnus,
				$acco.company.email as email
	FROM 		$acco.company LEFT OUTER JOIN $acco.link_company_contact ON ($acco.company.id = $acco.link_company_contact.company_id)
	where		$acco.link_company_contact.contact_id = $ul_r[id]
	order by 	$acco.link_company_contact.prim
	
";

$cl = pg_query($conn, $query);
}



/* new companies */
/* only limit results if not new company */
if ($cl) {
	$query = "
		select		
					id,
					name,
					ytunnus,
					email
		FROM 		$acco.company
		where		id not in (
			select		$acco.link_company_contact.company_id
			from		$acco.link_company_contact
			where $acco.link_company_contact.contact_id = $ul_r[id]
		)
		order by	name
	";
} else {
	$query = "
	select		
					id,
					name,
					ytunnus,
					email
		FROM 		$acco.company
		order by	name
	
	
	";
}
$nc = pg_query($conn, $query);

/*use buttons row */
echo "
	<form action='transaction.php?t=contact_edit' method='post' id='consave'>
	<input type='hidden' name='suid' value='$ul_r[id]'/>
	<input type='hidden' name='acco' value='$acco'/>
	
	<div class='buttons'>
		<a href='index.php?section=contacts&template=contact_view&suid=$ul_r[id]'>
			<div class='header'>$header</div>
		</a>
		
			<button formid='consave' class='usebutton'>{$lng->__('Save Contact')}</button>
		
	</div>
";
/* script for checking duplicate contacts*/
echo "
	<script type='text/javascript'>
		var fulllist = ".json_encode($jsname,JSON_HEX_QUOT).";
		var listf = ".json_encode($jsnamef,JSON_HEX_QUOT).";
		var listl = ".json_encode($jsnamel,JSON_HEX_QUOT).";
		var arrayLength = fulllist.length;
		function checkNames()
		{
			document.getElementById('coninfo').innerHTML = '&nbsp;';
			
			var typedf = document.getElementById('fname').value;
			var firstjs = typedf.trim();
			var typedl = document.getElementById('lname').value;
			var lastjs = typedl.trim();
	    	var fetchfull = typedf+typedl;
	    	var search = new RegExp('^'+firstjs, 'i');
	    	var searchl = new RegExp('^'+lastjs, 'i');
	    	var hits = 0;
			var names = '';
			
			for (var i = 0; i < arrayLength; i++) {
				if (listf[i].match(search) && listl[i].match(searchl)) {
					hits += 1;
					names += ' - '+fulllist[i];
					if (hits <= 3){
						
						if (firstjs || lastjs) {
							document.getElementById('coninfo').innerHTML = 'Duplicate names: '+hits+'. ';
							document.getElementById('coninfo').innerHTML += names;
						} else {
							document.getElementById('coninfo').innerHTML = '&nbsp;';
						}
						
					} else {
						
					}
					
				} else {
					
				}
			    
			}
			
		}
	</script>
	
";


echo "
	<div class='fullcont'>
		<div id='coninfo' style='color: red;'>
			&nbsp;
		</div>
		
		<table class='grid'>
			<tr>
				<td class='head'>
					{$lng->__('First Name')}:
				</td>
				<td>
					 <input type='text' id='fname' name='fname' value='$ul_r[fname]' onkeyup='checkNames()' tabindex='1' autofocus/>
				</td>
				<td class='head'>
					{$lng->__('Address')}:
				</td>
				<td>
					<input type='text' name='bill_addr' value='$ul_r[bill_addr]' tabindex='8'/>
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Last Name')}:
				</td>
				<td>
					<input type='text' id='lname' name='lname' value='$ul_r[lname]' onkeyup='checkNames()' tabindex='2'/>
				</td>
				<td class='head'>
					{$lng->__('Zip')}:
				</td>
				<td>
					<input type='text' name='bill_zip' value='$ul_r[bill_zip]' tabindex='9'/>
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Phone')} 1:
				</td>
				<td>
					<input type='text' name='phone1' value='$ul_r[phone1]' tabindex='3'/>
				</td>
				<td class='head'>
					{$lng->__('City')}:
				</td>
				<td>
					<input type='text' name='bill_city' value='$ul_r[bill_city]' tabindex='10'/>
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Phone')} 2:
				</td>
				<td>
					<input type='text' name='phone2' value='$ul_r[phone2]' tabindex='4'/>
				</td>
				<td class='head'>
					{$lng->__('Country')}:
				</td>
				<td>
					<input type='text' name='bill_country' value='$ul_r[bill_country]' tabindex='11'/>
			</tr>
			
			<tr>
				<td class='head'>
					{$lng->__('Email')}:
				</td>
				<td>
					<input type='text' name='email' value='$ul_r[email]' tabindex='5'/>
				</td>
				<td class='head'>
					{$lng->__('Language')}:
				</td>
				<td>
				";
					if ($ul_r['loc'] == 'fi') $sel1=" selected='selected'"; else $sel1="";
					if ($ul_r['loc'] == 'sv') $sel2=" selected='selected'"; else $sel2="";
					if ($ul_r['loc'] == 'en') $sel3=" selected='selected'"; else $sel3="";
					
				echo "
					<select name='loco' tabindex='12'>
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
					www:
				</td>
				<td>
					<input type='text' name='www' value='$ul_r[www]' tabindex='7'/>
				</td>
				<td class='head'>
					
				</td>
				<td>
					
				</td>
			</tr>
		";
		
		echo "	
		</table>
		
			";
	if ($suid != 0) {
		echo "
		
		<h4>{$lng->__('Company')}:</h4>
		<table class='list'>
			<tr>
					
					<th>
						{$lng->__('Name')}:
					</th>
					
					<th>
						{$lng->__('VAT-nr')}:
					</th>
					<th>
						{$lng->__('Email')}:
					</th>
				</tr>
		";
		
		if ($cl) {
			while ($cl_r = pg_fetch_array($cl)) {
				echo "
					
					<tr>
						<td>
							<a href='index.php?section=company&template=company_view&suid=$cl_r[coid]'>
							$cl_r[name]
							</a>
						</td>
						
						<td>
							$cl_r[ytunnus]
						</td>
						<td>
							$cl_r[email]
						</td>
					</tr>
					";
			}
		}
	echo "
			</table>
					<h4>{$lng->__('Add company')}:</h4>
				
					<select name='nco'>
						<option value=''>
							{$lng->__('None')}
						</option>
					";
					while ($nc_r = pg_fetch_array($nc)) {
						echo "
						<option value='$nc_r[id]'>
							$nc_r[name]
						</option>
						";
					}	
					echo "
					</select>
					<input type='submit' name='add_co' value='{$lng->__('Add')}'></input>
				";
	}
		echo "
				
		</form>
	</div>
	";
?>