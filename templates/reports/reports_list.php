<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
} else {
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];


/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=reports&template=reports_list'>
			<div class='header'>{$lng->__('Reports')}</div>
		</a>
		
		
	</div>
";


echo "
	<div class='fullcont'>
		
	";

/* widget invoice report*/
echo "
		<div class='widget'>
			
			<table class='list'>
				
	";
		$daynow = "01";
		$monthnow = date(m);
		$yearnow = date(Y);
		$yearlast = $yearnow - 1;
		$count = $monthnow;
		/* i = amount of months to show */
		for ($i = 1; $i <= 6; $i++) {
			$dated =  date("Y-m-d", strtotime("$yearnow-$count-$daynow"));
						
			$minus = strtotime ( '-1 month' , strtotime ( $dated ) ) ;
			$minus = date ( 'Y-m-d' , $minus );
			
			/* subtract a day to make it the last of same month */
			$dated = strtotime ( '-1 day' , strtotime ( $dated ) ) ;
			$dated = date ( 'Y-m-d' , $dated );
			
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
			/*subtract 1 for month */	
			$count = $count - 1;
			if ($count <= 0) {
				$count = $count + 12;
				$yearnow = $yearlast;
			}

			
			$month = date(F,strtotime("2000-".$count."-01"));
			$now = date(n-Y);
			echo "
				<tr>
					<td class='bold'>
						<a href='index.php?section=reports&template=reports_month&dated=$dated&minus=$minus'>
							$month $yearnow
						</a>
					</td>
					<td class='bold'>
						<a href='index.php?section=reports&template=reports_month&dated=$dated&minus=$minus'>
							$minus - $dated
						</a>
					</td>
					<td class='bold'>
						<a href='index.php?section=reports&template=reports_month&dated=$dated&minus=$minus'>
							Amount
						</a>
					</td>
				</tr>
			";
		while ($monthly_r = pg_fetch_array($monthly)) {
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
				where		invoice_id = $monthly_r[ident]
				
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
			
			echo "
				<tr>
					<td>
						
						
						
					</td>
					<td>
						<a href='index.php?section=invoice&template=invoice_view&inoid=$monthly_r[outid]&ident=$monthly_r[ident]'>
						$monthly_r[header] - $monthly_r[addhead]
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
		
}
?>