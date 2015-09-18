<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

/* check for contact filter */
if ($_GET['cfilter']) {
	$filter = $_GET['cfilter'];
	$sqlfilt = "and $acco.invoice_out.pid = $filter";
} elseif ($_GET['comfilter']) {
	$filter = $_GET['comfilter'];
	$sqlfilt = "and $acco.invoice_out.cid = $filter";
}  else {
	$filter = 0;
	$sqlfilt = " ";
}

/* nr of rows to diplay */
$rows = 15;

if (!$_GET['startrow']) {
	$startrow = 0;
} else {
	$startrow = $_GET['startrow'];
}

/* row check */
$query = "
		select		{$acco}.invoice_out.id as id
					
		from		$acco.invoice_out LEFT JOIN $acco.invoice_def
		ON			($acco.invoice_out.invoice_id = $acco.invoice_def.ident)
		where		$acco.invoice_out.pub = true
		$sqlfilt
		
		order by	$acco.invoice_out.dated desc
		
		
	";
	$rowcheck = pg_query($conn, $query);


// Iterates backwards if no rows found
$fetchrow = $startrow;
do {
	$query = "
		select		{$acco}.invoice_def.id as id,
					{$acco}.invoice_def.ident as ident,
					{$acco}.invoice_def.end_date as end_date,
					{$acco}.invoice_def.recurring as recurring,
					{$acco}.invoice_out.header as header,
					{$acco}.invoice_out.pid as pid,
					{$acco}.invoice_out.cid as cid,
					$acco.invoice_out.loc as loc,
					$acco.invoice_out.id as outid,
					$acco.invoice_out.addhead as addhead,
					$acco.invoice_out.def_id as def_id,
					$acco.invoice_out.invoice_id as invoice_id,
					$acco.invoice_out.created as created_out,
					$acco.invoice_out.dated as dated_out,
					$acco.invoice_out.ref as ref,
					$acco.invoice_out.pub as pub,
					$acco.invoice_out.printed as printed
		from		$acco.invoice_out LEFT JOIN $acco.invoice_def
		ON			($acco.invoice_out.def_id = $acco.invoice_def.ident)
		where		$acco.invoice_out.pub = true
		$sqlfilt
		order by	$acco.invoice_out.dated desc, $acco.invoice_out.id desc
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
		<a href='index.php?section=invoice&template=invoice_list'>
			<div class='header'>{$lng->__('Published')} {$lng->__('Invoices')}</div>
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
				<th class='first'>
					{$lng->__('Dated')}:
				</th>
				<th>
					{$lng->__('Reference')}:
				</th>
				<th>
					{$lng->__('Header')}:
				</th>
				<th>
					{$lng->__('Person')}:
				</th>
				<th>
					{$lng->__('Amount')}:
				</th>
				<th>
					{$lng->__('Printed')}:
				</th>
			</tr>
			";
		while ($in_r = pg_fetch_array($in)) {
			if ($in_r[pub] == f) {
				$bolde = 'font-weight: bold;';
			} else {
				$bolde = '';
			}
				
			
			/* users */
			$query = "
				select		$acco.invoice_out.id,
							$acco.invoice_out.pid as pid,
							$acco.contacts.fname as fname,
							$acco.contacts.lname as lname
				from		$acco.invoice_out LEFT OUTER JOIN $acco.contacts ON ($acco.invoice_out.pid = $acco.contacts.id)
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
			
			$date = strtotime($in_r[dated_out]);
			if ($in_r[printed]) {
				$printed = date('Y-m-d', strtotime($in_r[printed]));
			} else {
				$printed = "{$lng->__('Not printed')}";
			}
			
			
			
			/*invoice items for counting total cost*/
					
			$query = "
				select		id,
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

			$combprice;
			while ($it_r = pg_fetch_array($it)) {
					$tempprice1 = $it_r[price] * $it_r[qty];
					$tempprice2 = $tempprice1 * $it_r[vat];
					$combprice += ($tempprice1 + $tempprice2);
				}
			$pripath = "index.php?section=invoice&template=invoice_view&inoid=$in_r[outid]&ident=$in_r[ident]&invid=$in_r[invoice_id]";
			
			$refformat = chunk_split($in_r[ref], 5, ' ');
			
			echo "	
			
				<tr>
					
					<td class='first'>
						<a href='$pripath'>
							".date('Y-m-d', $date)."
						</a>
					</td>
					<td>
						<a href='$pripath'>
							$refformat
						</a>
					</td>
					<td>
						<a href='$pripath' style='$bolde'>
							$in_r[header] - $in_r[addhead]
						</a>
					</td>
					<td>
					";
						if($in_r[pid]) { echo "$ua_r[lname], $ua_r[fname]"; }
						
						if($in_r[cid]) { echo " - <span style='color:#656565;'>$ca_r[name]</span>"; }
					echo "
					</td>
					<td>
						".number_format($combprice,2,","," ")." &euro;
					</td>
					<td>
						$printed
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
				<a href='index.php?section=invoice&template=invoice_list&startrow=$backrow&cfilter=$filter'>
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
				<a href='index.php?section=invoice&template=invoice_list&startrow=$newrow&cfilter=$filter'>
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
	</div>
	";
?>
