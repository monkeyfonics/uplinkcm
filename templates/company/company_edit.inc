<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.inc';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];


if (!$_GET['suid']) {
	$suid = 0;
	
} else {
	$suid = $_GET['suid'];
}

/*create full company list */
$query = "
	select		id,
				name,
				ytunnus
				
	from		$acco.company
	
	
";

$full = pg_query($conn, $query);

while ($full_r = pg_fetch_array($full)) {
	$jsname[] = "$full_r[name]";
	$jsytun[] = "$full_r[ytunnus]";
}
	
$query = "
	select		id,
				name,
				ytunnus,
				bill_addr,
  				bill_zip,
  				bill_city,
  				bill_country,
  				email,
  				phone,
  				www
	from		$acco.company
	where		id = $suid
	
";

$ul = pg_query($conn, $query);

$ul_r = pg_fetch_array($ul);
if (!$suid) {
	$header = "{$lng->__('New Company')}";
} else {
	$header = $ul_r[name];


/* fetch contacts for company */
$query = "
	select		$acco.link_company_contact.contact_id,
				$acco.link_company_contact.company_id,
				$acco.link_company_contact.prim as prim,
				$acco.contacts.id as coid,
				$acco.contacts.lname as lname,
				$acco.contacts.fname as fname
	FROM 		$acco.contacts LEFT OUTER JOIN $acco.link_company_contact ON ($acco.contacts.id = $acco.link_company_contact.contact_id)
	where		$acco.link_company_contact.company_id = $ul_r[id]
	order by 	$acco.link_company_contact.prim
	
";

$cl = pg_query($conn, $query);
}

/* only limit results if not new company */
if ($cl) {
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
  				www
	from		$acco.contacts
	where		id not in (
		select		$acco.link_company_contact.contact_id
		from		$acco.link_company_contact
		where company_id = $ul_r[id]
	)
	
	";
} else {
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
  				www
	from		$acco.contacts
	
	
	";
}


$nc = pg_query($conn, $query);


/*use buttons row */
echo "
	<form action='transaction.php?t=company_edit' method='post' id='comsave'>
	<input type='hidden' name='suid' value='$ul_r[id]'/>
	<input type='hidden' name='acco' value='$acco'/>
	
	<div class='buttons'>
		<a href='index.php?section=company&template=company_view&suid=$ul_r[id]'>
			<div class='header'>$header</div>
		</a>
		
			<button formid='comsave' class='usebutton'>{$lng->__('Save Company')}</button>
		
	</div>
";
/* script for checking duplicate contacts*/
echo "
	<script type='text/javascript'>
		var fulllist = ".json_encode($jsname,JSON_HEX_QUOT).";
		var listn = ".json_encode($jsname,JSON_HEX_QUOT).";
		var listy = ".json_encode($jsytun,JSON_HEX_QUOT).";
		var arrayLength = listn.length;
		function checkNames()
		{
			document.getElementById('coninfo').innerHTML = '&nbsp;';
			
			var typedn = document.getElementById('name').value;
			var firstjs = typedn.trim();
			var typedy = document.getElementById('ytun').value;
			var lastjs = typedy.trim();
	    	
	    	var search = new RegExp('^'+firstjs, 'i');
	    	var searchl = new RegExp('^'+lastjs, 'i');
	    	var hits = 0;
			
			for (var i = 0; i < arrayLength; i++) {
				
				if (listn[i].match(search) && listy[i].match(searchl)) {
					hits += 1;
					if (hits <= 3){
						if (firstjs || lastjs) {
							document.getElementById('coninfo').innerHTML = 'Duplicate names: ';
							document.getElementById('coninfo').innerHTML += hits+'. '+listn[i]+':'+listy[i]+' - ';
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
					{$lng->__('Name')}:
				</td>
				<td>
					 <input type='text' id='name' name='name' value='$ul_r[name]' onkeyup='checkNames()'></input>
				</td>
				<td class='head'>
					{$lng->__('Address')}:
				</td>
				<td>
					<input type='text' name='bill_addr' value='$ul_r[bill_addr]'></input>
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('VAT-nr')}:
				</td>
				<td>
					<input type='text' id='ytun' name='ytunnus' value='$ul_r[ytunnus]' onkeyup='checkNames()'></input>
				</td>
				<td class='head'>
					{$lng->__('Zip')}:
				</td>
				<td>
					<input type='text' name='bill_zip' value='$ul_r[bill_zip]'> </input> 
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Phone')}:
				</td>
				<td>
					<input type='text' name='phone' value='$ul_r[phone]'></input>
				</td>
				<td class='head'>
					{$lng->__('City')}:
				</td>
				<td>
					<input type='text' name='bill_city' value='$ul_r[bill_city]'></input>
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Email')}:
				</td>
				<td>
					<input type='text' name='email' value='$ul_r[email]'></input>
				</td>
				<td class='head'>
					{$lng->__('Country')}:
				</td>
				<td>
					<input type='text' name='bill_country' value='$ul_r[bill_country]'></input>
				</td>
			</tr>
			<tr>
				<td class='head'>
					www:
				</td>
				<td>
					<input type='text' name='www' value='$ul_r[www]'></input>
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
		<h4>{$lng->__('Contacts')}:</h4>
		<table class='list'>
			<tr>
					
					<td>
						{$lng->__('Contact')}:
					</td>
					
					<td>
						{$lng->__('Primary')}:
					</td>
				</tr>
		";
		if ($cl) {
			while ($cl_r = pg_fetch_array($cl)) {
			if ($cl_r[prim] == t) {
				$selt = " selected='selected'";
				$self = "";
			} else {
				$selt = "";
				$self = " selected='selected'";
			}
			echo "
				<input type='hidden' name='cids[]' value='$cl_r[coid]'/>
				<tr>
					
					<td>
						<a href='index.php?section=contacts&template=contact_view&suid=$cl_r[coid]'>
						$cl_r[lname], $cl_r[fname]
						</a>
					</td>
					
					<td>
						<select name='primary[$cl_r[coid]]'>
							<option value='t' $selt>
								{$lng->__('Yes')}
							</option>
							<option value='f' $self>
								{$lng->__('No')}
							</option>
						</select>
						
					</td>
				</tr>
				";
			}
		}
		
	echo "
		</table>
		<h4>{$lng->__('Add Contact')}:</h4>
				
					<select name='nco'>
						<option value=''>
							{$lng->__('None')}
						</option>
					";
					while ($nc_r = pg_fetch_array($nc)) {
						echo "
						<option value='$nc_r[id]'>
							$nc_r[lname], $nc_r[fname]
						</option>
						";
					}	
					echo "
					</select>
					<input type='submit' name='add_co' value='Add'></input>
		";
		}
	echo "	
		</form>
	</div>
	";
?>