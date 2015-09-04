<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

$suid = $_GET['suid'];
	
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

/*get the first letter of lastname*/
$firstletter = substr($ul_r[name],0,1);

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


/*company invoices*/
$query = "
	select		id,
				header,
				addhead,
				invoice_id,
				dated,
				ref,
				pub
	from		$acco.invoice_out
	where		cid = $suid
	order by dated desc
	Limit		15
";

$com_in = pg_query($conn, $query);


/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=company&template=company_list#$firstletter'>
			<div class='header'>{$lng->__('Company List')}</div>
		</a>
		<a href='index.php?section=company&template=company_view&suid=$ul_r[id]'>
			<div class='header'>$ul_r[name]</div>
		</a>
		<a href='index.php?section=company&template=company_edit&suid=$ul_r[id]'>
			<div>{$lng->__('Edit Company')}</div>
		</a>
		<a href='index.php?section=todo&template=todo_edit&comid=$ul_r[id]'>
			<div>{$lng->__('New Todo')}</div>
		</a>
		";
		/* check viability for deleting contacts */
		/*
		echo "
		<a href='transaction.php?t=del_company&suid=$ul_r[id]&acco=$acco' onclick='return confirm(\"{$lng->__('Delete')}?\");'>
			<div>{$lng->__('Delete')}</div>
		</a>
		 ";
		 */
		echo "
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
					$ul_r[name]
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
					{$lng->__('VAT-nr')}:
				</td>
				<td>
					$ul_r[ytunnus]
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
					{$lng->__('Phone')}:
				</td>
				<td>
					$ul_r[phone]
				</td>
			</tr>
		
		";
		
		echo "	
		</table>
		<div class='smbox'>
		<h4>{$lng->__('Contacts')}:</h4>
		<table class='list'>
			<tr>
					
					<th>
						{$lng->__('Contact')}:
					</th>
					
					<th>
						{$lng->__('Primary')}:
					</th>
				</tr>
		";
		while ($cl_r = pg_fetch_array($cl)) {
			if ($cl_r[prim] == t) {
				$prim = "{$lng->__('Yes')}";
			} else {
				$prim = "{$lng->__('No')}";
			}
			echo "
				<tr>
					
					<td>
						<a href='index.php?section=contacts&template=contact_view&suid=$cl_r[coid]'>
						$cl_r[lname], $cl_r[fname]
						</a>
					</td>
					
					<td>
						$prim
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
			while ($com_in_r = pg_fetch_array($com_in)) {
				$date = date('Y-m-d', strtotime($com_in_r[dated]));
				if ($com_in_r[pub] == f) {
					$pub = "class='pub'";
				} else {
					$pub = " ";
				}
				echo "
					
					<tr>
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$com_in_r[id]&ident=$com_in_r[invoice_id]' $pub>
								$date
							</a>
						</td>
						
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$com_in_r[id]&ident=$com_in_r[invoice_id]' $pub>
								$com_in_r[header] - $com_in_r[addhead]
							</a>
						</td>
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$com_in_r[id]&ident=$com_in_r[invoice_id]' $pub>
								$com_in_r[ref]
							</a>
						</td>
					</tr>
					";
			}
		echo "
				<tr>
					<td colspan='3'>
						<a href='index.php?section=invoice&template=invoice_list&comfilter=$ul_r[id]'>
							{$lng->__('Show all')} {$lng->__('Published')}
						</a>
					</td>
				</tr>
				<tr>
					<td colspan='3'>
						<a href='index.php?section=invoice&template=invoice_list_pend&comfilter=$ul_r[id]' class='pub'>
							{$lng->__('Show all')} {$lng->__('Pending')}
						</a>
					</td>
				</tr>
			</table>
		</div>
		";
	
	echo "
	</div>
	";
	
	
?>