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

/*get the first letter of lastname*/
$firstletter = substr($ul_r[lname],0,1);

/*language clean up */

if ($ul_r[loc] == 'fi') {
	$langu="{$lng->__('Finnish')}";
} elseif ($ul_r[loc] == 'sv') {
	$langu="{$lng->__('Swedish')}";
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


$query = "
	select		$acco.contact_notes.id as id,
				$acco.contact_notes.contact_id as contact_id,
				$acco.contact_notes.created as created,
				$acco.contact_notes.cont as cont,
				$acco.contact_notes.created_by as created_by,
				public.users.login as username,
				public.users.fname as fname,
				public.users.lname as lname
	from		$acco.contact_notes left OUTER JOIN public.users ON ($acco.contact_notes.created_by = public.users.id)
	where		contact_id = $suid
	order by created desc
	Limit 		15
";

$con_not = pg_query($conn, $query);

/*contact invoices*/
$query = "
	select		id,
				header,
				addhead,
				invoice_id,
				dated,
				ref,
				pub
	from		$acco.invoice_out
	where		pid = $suid
	order by dated desc, invoice_id desc
	Limit 		15
	
";

$con_in = pg_query($conn, $query);



/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=contacts&template=contact_list#$firstletter'>
			<div class='header'>{$lng->__('Contact List')}</div>
		</a>
		<a href='index.php?section=contacts&template=contact_view&suid=$ul_r[id]'>
			<div class='header'>$ul_r[lname], $ul_r[fname]</div>
		</a>
		<a href='index.php?section=contacts&template=contact_edit&suid=$ul_r[id]'>
			<div>{$lng->__('Edit Contact')}</div>
		</a>
		<a href='index.php?section=contacts&template=contact_note&suid=$ul_r[id]'>
			<div>{$lng->__('New Note')}</div>
		</a>
		<a href='index.php?section=todo&template=todo_edit&suid=$ul_r[id]'>
			<div>{$lng->__('New Todo')}</div>
		</a>
		<a href='index.php?section=def&template=def_edit&suid=$ul_r[id]'>
			<div>{$lng->__('New Invoice')}</div>
		</a>
		";
		/* check viability for deleting contacts */
		/*
		echo "
		<a href='transaction.php?t=del_contact&suid=$ul_r[id]&acco=$acco' onclick='return confirm(\"{$lng->__('Delete')}?\");'>
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
					{$lng->__('Content')}:
				</th>
				<th>
					{$lng->__('User')}:
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
				<td>
					<a href='index.php?section=contacts&template=contact_note&suid=$ul_r[id]&notid=$con_not_r[id]'>
						$con_not_r[username]
					</a>
				</td>
			</tr>
			";
		}
		echo "
			<tr>
				<td colspan='3'>
					{$lng->__('Show all')}
				</td>
			</tr>
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
							{$lng->__('Invoice number')}:
						</th>
						<th>
							{$lng->__('Amount')}:
						</th>
					</tr>
			";
			while ($con_in_r = pg_fetch_array($con_in)) {
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
					where		invoice_id = $con_in_r[invoice_id]
					
				";
				
				$con_it = pg_query($conn, $query);
				$combprice ='';
				while ($con_it_r = pg_fetch_array($con_it)) {
						$tempprice1 = $con_it_r[price] * $con_it_r[qty];
						$tempprice2 = $tempprice1 * $con_it_r[vat];
						$combprice += ($tempprice1 + $tempprice2);
					}
				$invidformat = chunk_split($con_in_r[invoice_id], 6, ' ');
				$date = date('Y-m-d', strtotime($con_in_r[dated]));
				if ($con_in_r[pub] == f) {
					$pub = "class='pub'";
				} else {
					$pub = " ";
				}
				echo "
					
					<tr>
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$con_in_r[id]&ident=$con_in_r[invoice_id]' $pub>
								$date
							</a>
						</td>
						
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$con_in_r[id]&ident=$con_in_r[invoice_id]' $pub>
								$con_in_r[header] - $con_in_r[addhead]
							</a>
						</td>
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$con_in_r[id]&ident=$con_in_r[invoice_id]' $pub>
								$invidformat
							</a>
						</td>
						<td>
							<a href='index.php?section=invoice&template=invoice_view&inoid=$con_in_r[id]&ident=$con_in_r[invoice_id]' $pub>
								".number_format($combprice,2,","," ")." &euro;
							</a>
						</td>
					</tr>
					";
					
			}
		echo "
				<tr>
					<td colspan='4'>
						<a href='index.php?section=invoice&template=invoice_list&cfilter=$ul_r[id]'>
							{$lng->__('Show all')} {$lng->__('Published')}
						</a>
					</td>
				</tr>
				<tr>
					<td colspan='4'>
						<a href='index.php?section=invoice&template=invoice_list_pend&cfilter=$ul_r[id]' class='pub'>
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