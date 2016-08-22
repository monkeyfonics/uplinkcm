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
	order by dated desc, invoice_id desc
	Limit		15
";

$com_in = pg_query($conn, $query);

/*notes*/
$query = "
	select		$acco.contact_notes.id as id,
				$acco.contact_notes.contact_id as contact_id,
				$acco.contact_notes.company_id as company_id,
				$acco.contact_notes.created as created,
				$acco.contact_notes.cont as cont,
				$acco.contact_notes.created_by as created_by,
				public.users.login as username,
				public.users.fname as fname,
				public.users.lname as lname
	from		$acco.contact_notes left OUTER JOIN public.users ON ($acco.contact_notes.created_by = public.users.id)
	where		company_id = $suid
	order by created desc
	Limit 		15
";

$con_not = pg_query($conn, $query);

/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=company&template=company_list#$firstletter'>
			<div class='header'>{$lng->__('Companies')}</div>
		</a>
		<a href='index.php?section=company&template=company_view&suid=$ul_r[id]'>
			<div class='header'>$ul_r[name]</div>
		</a>
		<a href='index.php?section=company&template=company_edit&suid=$ul_r[id]'>
			<div>{$lng->__('Edit Company')}</div>
		</a>
		<a href='index.php?section=company&template=company_note&suid=$ul_r[id]'>
			<div>{$lng->__('New Note')}</div>
		</a>
		<a href='index.php?section=todo&template=todo_edit&comid=$ul_r[id]'>
			<div>{$lng->__('New Todo')}</div>
		</a>
		<a href='index.php?section=def&template=def_edit&coid=$ul_r[id]'>
			<div>{$lng->__('New Invoice')}</div>
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

// format webaddress
$webadr = str_replace("http://","",$ul_r[www]);

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
					<a href='http://maps.google.com/maps?q=$ul_r[bill_addr]%20$ul_r[bill_zip]%20$ul_r[bill_city]%20$ul_r[bill_country]' target='_blank'>
							$ul_r[bill_addr]
					</a>
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
					<a href='http://maps.google.com/maps?q=$ul_r[bill_addr]%20$ul_r[bill_zip]%20$ul_r[bill_city]%20$ul_r[bill_country]' target='_blank'>
						$ul_r[bill_zip] $ul_r[bill_city]
					</a>
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Email')}:
				</td>
				<td>
					<a href='mailto:$ul_r[email]' target='_blank'>
						$ul_r[email]
					</a>
				</td>
				<td class='head'>
					{$lng->__('Country')}:
				</td>
				<td>
					<a href='http://maps.google.com/maps?q=$ul_r[bill_addr]%20$ul_r[bill_zip]%20$ul_r[bill_city]%20$ul_r[bill_country]' target='_blank'>
						$ul_r[bill_country]
					</a>
				</td>
			</tr>
			<tr>
				<td class='head'>
					www:
				</td>
				<td>
					<a href='http://$webadr' target='_blank'>
						$ul_r[www]
					</a>
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
			while ($com_in_r = pg_fetch_array($com_in)) {/*invoice items*/
		
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
					where		invoice_id = $com_in_r[invoice_id]
					
				";
				
				$com_it = pg_query($conn, $query);
				$combprice ='';
				while ($com_it_r = pg_fetch_array($com_it)) {
						$tempprice1 = $com_it_r[price] * $com_it_r[qty];
						$tempprice2 = $tempprice1 * $com_it_r[vat];
						$combprice += ($tempprice1 + $tempprice2);
					}
					
				$invidformat = chunk_split($com_in_r[invoice_id], 6, ' ');
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
						<a href='index.php?section=invoice&template=invoice_list&comfilter=$ul_r[id]'>
							{$lng->__('Show all')} {$lng->__('Published')}
						</a>
					</td>
				</tr>
				<tr>
					<td colspan='4'>
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