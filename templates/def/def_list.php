<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r['identy'];

$today = date('Y-m-d');

/* nr of rows to diplay */
$rows = 15;

if (!$_GET['startrow']) {
	$startrow = 0;
} else {
	$startrow = $_GET['startrow'];
}

/* row check */
$query = "
		select		{$acco}.invoice_def.id as id
					
		from		$acco.invoice_def
		
		order by	$acco.invoice_def.created desc
		
		
	";
	$rowcheck = pg_query($conn, $query);


// Iterates backwards if no rows found
$fetchrow = $startrow;
do {
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
  				recurring,
  				active,
  				next_create
	from		$acco.invoice_def
	order by	next_create desc, created desc
	limit 		{$rows}
	offset		{$fetchrow}
";

$in = pg_query($conn, $query);

$lastrow = pg_num_rows($in);
	$fetchrow -= $rows;
} while ($lastrow == 0 && $fetchrow+$rows > 0);

$totalrows = pg_num_rows($rowcheck);


/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=def&template=def_list'>
			<div class='header'>{$lng->__('Invoice Templates')}</div>
		</a>
		<a href='index.php?section=def&template=def_edit'>
			<div>{$lng->__('New Invoice Template')}</div>
		</a>
	</div>
";


echo "


	<div class='fullcont'>
		
		
		<table class='list large'>
			<tr>
				<th>
					{$lng->__('Active')}:
				</th>
				<th class='first' title='{$lng->__('Last created invoice')}'>
					{$lng->__('Dated')}:
				</th>
				
				<th>
					{$lng->__('Header')}:
				</th>
				<th>
					{$lng->__('Contact')}:
				</th>
				
				<th>
					{$lng->__('Amount')} €:
				</th>
				
			</tr>
			";
		while ($in_r = pg_fetch_array($in)) {
			
			/* users */
			$query = "
				select		$acco.invoice_def.id,
							$acco.invoice_def.pid as pid,
							$acco.contacts.fname as fname,
							$acco.contacts.lname as lname
				from		$acco.invoice_def LEFT OUTER JOIN $acco.contacts ON ($acco.invoice_def.pid = $acco.contacts.id)
				where		$acco.contacts.id = $in_r[pid]
			";
			$ua = pg_query($conn, $query);
			$ua_r = pg_fetch_array($ua);
			
			/* check for company */
			$query = "
				select		$acco.invoice_out.id,
							$acco.invoice_out.cid as cid,
							$acco.company.name as name
				from		$acco.invoice_out LEFT OUTER JOIN $acco.company ON ($acco.invoice_out.cid = $acco.company.id)
				where		$acco.company.id = $in_r[cid]
			";
			$ca = pg_query($conn, $query);
			$ca_r = pg_fetch_array($ca);
			
			$created = date('Y-m-d', strtotime($in_r['created']));
			$date = date('Y-m-d', strtotime($in_r['dated']));
			$next = date('Y-m-d', strtotime($in_r['next_create']));
			$end = date('Y-m-d', strtotime($in_r['end_date']));
			
			/* check if the template has expired */
			if ($in_r['active'] == true) {
				$active = "{$lng->__('Yes')}";
				$expired = "color: green;";
				$icon = "&#10003;";
			} else {
				$active = "{$lng->__('No')}";
				$expired = "color: red;";
				$icon = "x";
			}
			
			if ($in_r['recurring'] > 0) {
				if ($in_r['ongoing'] == true or $next <= $end and $end >= $today) {
					$marker = "&raquo; $in_r[recurring]";
				} else {
					$marker = $icon;
				}
			
			} else {
				$marker = $icon;
			}
			
			
/*invoice items*/
		
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
	where		def_id = $in_r[ident]
	
";

$it = pg_query($conn, $query);
			$combprice;
			while ($it_r = pg_fetch_array($it)) {
					$tempprice1 = $it_r['price'] * $it_r['qty'];
					$tempprice2 = $tempprice1 * $it_r['vat'];
					$combprice += ($tempprice1 + $tempprice2);
				}

/*check for newest created invoice for specific template*/
		
$query = "
	select		dated,
				created
	from		$acco.invoice_out
	where		def_id = $in_r[ident]
	order by	dated desc
	limit 		1
";

$inl = pg_query($conn, $query);
			
$inl_r = pg_fetch_array($inl);
			if ($inl_r['dated']) {
				$lastinvoice = date('Y-m-d', strtotime($inl_r['dated']));
			} else {
				$lastinvoice = "{$lng->__('None')}";
			}
			
			
				
			$pripath = "index.php?section=def&template=def_view&ident=$in_r[ident]";
			
			/*active tooltip*/
			if ($in_r['active'] == true) {
				$activetip = "{$lng->__('Active')}";
			} else {
				$activetip = "{$lng->__('Inactive')}";
			}
			if ($in_r['ongoing'] == true) {
				$ongoingtip = "{$lng->__('Ongoing')}";
			} else {
				$ongoingtip = "{$lng->__('Not ongoing')}";
			}
			echo "	
				<tr>
					<td style='$expired text-align: center;' title='$activetip, $ongoingtip'>
						$marker
					</td>
					<td class='first' title='{$lng->__('Last created invoice')}'>
						<a href='$pripath'>
							$lastinvoice
						</a>
					</td>
					
					<td>
						<a href='$pripath'>
							$in_r[header]
						</a>
					</td>
					<td>
					";
						if($in_r['pid']) { echo "<a href='index.php?section=contacts&template=contact_view&suid=$ua_r[pid]'><span style='color:#000;'>$ua_r[lname], $ua_r[fname]</span></a>"; }
						
						if($in_r['cid']) { echo " - <a href='index.php?section=company&template=company_view&suid=$ca_r[cid]'><span style='color:#656565;'>$ca_r[name]</span></a>"; }
					echo "
						
					</td>
					
					<td>
						".number_format($combprice,2,","," ")." &euro;
					</td>
					
					
				</tr>
			";
			
			$combprice = 0;
		}
		
		
		echo "	
		</table>
		";
		
		$newrow = $startrow+$rows;
		$backrow = $startrow-$rows;
		
		/*just for show*/
		$showstart = $startrow+1;
		$shownew = $newrow;
		
		echo "	
		
		<div class='buttons'>
		";
			if ($startrow > 0) {
				echo "
				<a href='index.php?section=def&template=def_list&startrow=$backrow'>
					<div>{$lng->__('Back')}</div>
				</a>
				";
			} else {
				echo "
				
					<div class='inactive'>{$lng->__('Back')}</div>
				
				";
			}
			echo "
			<div>
				$showstart - $shownew / $totalrows
			</div>
			";
			if ($totalrows  >= $newrow) {
				echo "
				<a href='index.php?section=def&template=def_list&startrow=$newrow'>
					<div>{$lng->__('Next')}</div>
				</a>
				";
			} else {
				echo "
				
					<div class='inactive'>{$lng->__('Next')}</div>
				
				";
			}
		
		echo "
	</div>
	";
?>