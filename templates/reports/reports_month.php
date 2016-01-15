<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
} else {
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];


$dated = $_GET[dated];
$minus = $_GET[minus];


$minus = date ( 'Y-m-d' , strtotime($minus) );

$dated = date ( 'Y-m-d' , strtotime($dated) );


$month = date(F,strtotime($dated));

/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=reports&template=reports_list'>
			<div class='header'>{$lng->__('Reports')}</div>
		</a>
		<a href='index.php?section=reports&template=reports_month&dated=$dated&minus=$minus'>
			<div class='header'>{$lng->__('Month')} - $month</div>
		</a>
		<a href='out.php?section=reports&t=report_gen_csv&dated=$dated&minus=$minus&acco=$acco' target='blank'>
			<div>{$lng->__('Print')} CSV</div>
		</a>
		<a href='out.php?section=reports&t=report_gen_pdf&dated=$dated&minus=$minus&acco=$acco' target='blank'>
			<div>{$lng->__('Print')} PDF</div>
		</a>
	</div>
";

echo "
	<div class='fullcont'>
		
	";

/* widget invoice report*/
echo "
		<div>
			<div class='header'>{$lng->__('Monthly Report')}</div>
			<table class='list'>
				
	";
		
			
/* invoice */
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
					$acco.invoice_out.invoice_id as invoice_id,
					$acco.invoice_out.created as created_out,
					$acco.invoice_out.dated as dated_out,
					$acco.invoice_out.ref as ref,
					$acco.invoice_out.pub as pub
		from		$acco.invoice_out LEFT JOIN $acco.invoice_def
		ON			($acco.invoice_out.invoice_id = $acco.invoice_def.ident)
		where		$acco.invoice_out.dated between '$minus' and '$dated'
		and			$acco.invoice_out.pub = true
		order by	$acco.invoice_out.dated desc
		
				
		
	";

$monthly = pg_query($conn, $query);	
			

			
			
			echo "
				<tr>
					<td>
						<a href='index.php?section=invoice&template=invoice_view&inoid=$in_r[outid]&ident=$in_r[ident]'>
						$month
						</a>
					</td>
					<td>
						{$lng->__('Invoice number')}
					</td>
					<td>
						{$lng->__('Invoice')}
					</td>
					
					<td>
						{$lng->__('Contact')}
					</td>
					<td>
						{$lng->__('Amount')}
					</td>
				</tr>
			";
		while ($monthly_r = pg_fetch_array($monthly)) {
			
			 /*get name for contact*/
					
			$query = "
				select		id,
							lname,
							fname
				from		$acco.contacts
				where		id = $monthly_r[pid]
							
			";
						
			$co = pg_query($conn, $query);
			$co_r = pg_fetch_array($co);
			/*check for company*/
			if ($monthly_r[cid]) {
				/*get name for company*/
								
			$query = "
				select		id,
							name,
							ytunnus
				from		$acco.company
				where		id = $monthly_r[cid]
							
			";
						
			$com = pg_query($conn, $query);
			$com_r = pg_fetch_array($com);
			
			/*combine contact company name */
			$contactcom = $co_r[lname].", ".$co_r[fname]." - ".$com_r[name];
			
			} else {
			/*combine contact name */
			$contactcom = $co_r[lname].", ".$co_r[fname];
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
				where		invoice_id = $monthly_r[invoice_id]
				
			";
			
			$it = pg_query($conn, $query);

			$combprice;
			while ($it_r = pg_fetch_array($it)) {
					$tempprice1 = $it_r[price] * $it_r[qty];
					$tempprice2 = $tempprice1 * $it_r[vat];
					if ($it_r[invoice_id]== $last_id) {
						$combprice += ($tempprice1 + $tempprice2);
					} else {
						$combprice = ($tempprice1 + $tempprice2);
					}
					
					$last_id = $it_r[invoice_id];
				}
	/*format price */
	
	
	$formatprice = number_format($combprice, 2, ',', '');
	
				
			
			$dated = date ( 'Y-m-d' , strtotime($monthly_r[dated_out]));
			echo "
				<tr>
					<td>
						$dated
						
					</td>
					<td>
						<a href='index.php?section=invoice&template=invoice_view&inoid=$monthly_r[outid]&ident=$monthly_r[ident]'>
							$monthly_r[invoice_id]
						</a>
					</td>
					<td>
						<a href='index.php?section=invoice&template=invoice_view&inoid=$monthly_r[outid]&ident=$monthly_r[ident]'>
						$monthly_r[header] - $monthly_r[addhead]
						</a>
					</td>
					<td>
						<a href='index.php?section=contacts&template=contact_view&suid=$co_r[id]'>
						$contactcom
						</a>
					</td>
					<td>
						$formatprice €
					</td>
				</tr>
			";
			
			$total += $combprice;
		}
			
			/*format price */
			
			$totalformat = number_format($total, 2, ',', '');
			
			echo "
				<tr>
					<td>
						{$lng->__('Total')}:
						
					</td>
					<td>
						
					</td>
					<td>
						
					</td>
					<td>
						
					</td>
					<td>
						<b>$totalformat €</b>
					</td>
				</tr>
			";
			$total = 0;
		}
echo "
			</table>
			
		</div>
";		
		

?>