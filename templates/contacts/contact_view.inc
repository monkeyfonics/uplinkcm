<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.inc';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

$suid = $_GET['suid'];
	
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

/*language clean up */

if ($ul_r[loc] == 'fi') {
	$langu="{$lng->__('Finnish')}";
} elseif ($ul_r[loc] == 'sv') {
	$langu="{$lng->__('DSwedish')}";
} elseif ($ul_r[loc] == 'en') {
	$langu="{$lng->__('English')}";
}	

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


/*contact notes*/
$query = "
	select		id,
				contact_id,
				created,
				cont
	from		$acco.contact_notes
	where		contact_id = $suid
	order by created desc
	
";

$con_not = pg_query($conn, $query);

/*contact invoices*/
$query = "
	select		id,
				header,
				addhead,
				invoice_id,
				dated,
				ref
	from		$acco.invoice_out
	where		pid = $suid
	order by dated desc
	
";

$con_in = pg_query($conn, $query);



/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=contacts&template=contact_view&suid=$ul_r[id]'>
			<div class='header'>$ul_r[lname], $ul_r[fname]</div>
		</a>
		<a href='index.php?section=contacts&template=contact_edit&suid=$ul_r[id]'>
			<div>{$lng->__('Edit Contact')}</div>
		</a>
		<a href='index.php?section=contacts&template=contact_note&suid=$ul_r[id]'>
			<div>{$lng->__('New Note')}</div>
		</a>
		<a href='index.php?section=def&template=def_edit&suid=$ul_r[id]'>
			<div>{$lng->__('New Invoice')}</div>
		</a>
	</div>
";

echo "
	<div class='fullcont'>
		
		
		<table class='grid'>
			<tr>
				<td class='head'>
					{$lng->__('Name')}:
				</td>
				<td>
					$ul_r[lname] $ul_r[fname] 
				</td>
				<td class='head'>
					{$lng->__('Address')}:
				</td>
				<td>
					$ul_r[bill_addr]
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Phone')}:
				</td>
				<td>
					$ul_r[phone1]
				</td>
				<td class='head'>
					{$lng->__('City')}:
				</td>
				<td>
					$ul_r[bill_zip] $ul_r[bill_city]
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Email')}:
				</td>
				<td>
					$ul_r[email]
				</td>
				<td class='head'>
					{$lng->__('Country')}:
				</td>
				<td>
					$ul_r[bill_country]
				</td>
			</tr>
			<tr>
				<td class='head'>
					www:
				</td>
				<td>
					$ul_r[www]
				</td>
				<td class='head'>
					{$lng->__('Language')}:
				</td>
				<td>
					$langu
				</td>
			</tr>
		
		";
		
		
		
		echo "	
		</table>
		";
		
		/* notes */
	echo "
		<div class='smbox'>
		<h4>{$lng->__('Notes')}:</h4>
		<table class='list'>
			<tr>
				<th style='width: 150px;'>
					{$lng->__('Date')}:
				</th>
				<th>
					{$lng->__('Note')}:
				</th>
			</tr>
			";
		while($con_not_r = pg_fetch_array($con_not)) {
			$date = strtotime($con_not_r[created]);
			echo "
			<tr>
				<td>
					<a href='index.php?section=contacts&template=contact_note&suid=$ul_r[id]&notid=$con_not_r[id]'>
						".date('Y-m-d', $date)."
					</a>
				</td>
				<td>
					<a href='index.php?section=contacts&template=contact_note&suid=$ul_r[id]&notid=$con_not_r[id]'>
						$con_not_r[cont]
					</a>
				</td>
			</tr>
			";
		}
		echo "
		</table>
		</div>
		";
		
		/*company*/
		echo "
		<div class='smbox'>
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
		echo "
			</table>
		</div>
		";
		
		/*Invoices*/
		echo "
		<div class='smbox'>
			<h4>{$lng->__('Invoices')}:</h4>
			<table class='list'>
				<tr>
						
						<th>
							{$lng->__('Dated')}:
						</th>
						
						<th>
							{$lng->__('Header')}:
						</th>
						<th>
							{$lng->__('Reference')}:
						</th>
					</tr>
			";
			while ($con_in_r = pg_fetch_array($con_in)) {
				$date = date('Y-m-d', strtotime($con_in_r[dated]));
				echo "
					
					<tr>
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$con_in_r[id]&ident=$con_in_r[invoice_id]'>
								$date
							</a>
						</td>
						
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$con_in_r[id]&ident=$con_in_r[invoice_id]'>
								$con_in_r[header] - $con_in_r[addhead]
							</a>
						</td>
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$con_in_r[id]&ident=$con_in_r[invoice_id]'>
								$con_in_r[ref]
							</a>
						</td>
					</tr>
					";
			}
		echo "
			</table>
		</div>
		";
		
		
	echo "	
	</div>
	";
?>