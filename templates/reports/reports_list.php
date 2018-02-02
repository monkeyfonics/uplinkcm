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
		
			
			<table class='list'>
				
	";
		$daynow = date(d);
		$dayfirst = "01";
		$monthnow = date(m);
		$yearnow = date(Y);
		$yearlast = $yearnow - 1;
		
		
		/* i = amount of months to show */
		for ($i = 1; $i <= 8; $i++) {
			if ($i == 1) {
				$marker = "style='font-style: italic;'";
				$dated =  date("Y-m-d", strtotime("$yearnow-$monthnow-$daynow"));
				$minus =  date("Y-m-d", strtotime("$yearnow-$monthnow-$dayfirst"));
			} else {
				$marker = "style=' '";
				$dated = $minus;
				$minus = strtotime ( '-1 month' , strtotime ( $dated ) ) ;
				
				/* subtract a day to make it the last of same month as minus */
				$dated = strtotime ( '-1 day' , strtotime ( $dated ) ) ;
				
				/*reformat them after subtractions*/
				$minus = date ( 'Y-m-d' , $minus );
				$dated = date ( 'Y-m-d' , $dated );
			}

			
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
					$acco.invoice_out.def_id as def_id,
					$acco.invoice_out.invoice_id as invoice_id,
					$acco.invoice_out.runid as runid,
					$acco.invoice_out.created as created_out,
					$acco.invoice_out.dated as dated_out,
					$acco.invoice_out.ref as ref,
					$acco.invoice_out.pub as pub,
					$acco.invoice_out.cash as cash
		from		$acco.invoice_out LEFT JOIN $acco.invoice_def
		ON			($acco.invoice_out.def_id = $acco.invoice_def.ident)
		where		$acco.invoice_out.dated between '$minus' and '$dated'
		and			$acco.invoice_out.pub = true
		order by	$acco.invoice_out.dated desc, $acco.invoice_out.invoice_id desc
		
				
		
	";

$monthly = pg_query($conn, $query);
			
			
			
			$yearnow = date(Y,strtotime($minus));
			$monthtext = date(F,strtotime($minus));
			$now = date(n-Y);
			
			echo "
				<tr>
					<td class='report_header' $marker colspan='4'>
						<a href='index.php?section=reports&template=reports_month&dated=$dated&minus=$minus'>
							$monthtext $yearnow
						</a>
					</td>
					
				</tr>
				<tr>
					<td class='bold' $marker>
						<a href='index.php?section=reports&template=reports_month&dated=$dated&minus=$minus'>
							Date
						</a>
					</td>
					<td colspan='2' class='bold' $marker>
						<a href='index.php?section=reports&template=reports_month&dated=$dated&minus=$minus'>
							$minus - $dated
						</a>
					</td>
					<td class='bold' $marker>
						<a href='index.php?section=reports&template=reports_month&dated=$dated&minus=$minus'>
							{$lng->__('Contact')}
						</a>
					</td>
					<td class='bold' $marker>
						<a href='index.php?section=reports&template=reports_month&dated=$dated&minus=$minus'>
							{$lng->__('Amount')}
						</a>
					</td>
					<td class='bold' title='{$lng->__('Cash')}' $marker>
						<a href='index.php?section=reports&template=reports_month&dated=$dated&minus=$minus'>
							
							€
				
						</a>
					</td>
					<td class='bold' title='{$lng->__('Downloaded')}' $marker>
						<a href='index.php?section=reports&template=reports_month&dated=$dated&minus=$minus'>
							
							DL
				
						</a>
					</td>
				</tr>
			";
		$nrofinv = 0;
		
		while ($monthly_r = pg_fetch_array($monthly)) {
			if ($monthly_r[cash] == t) {
				$cash = "&#10004;";
			} else {
				$cash = " ";
			}
			if ($monthly_r[pub] == t) {
				$nrofinv++;
			}
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
			
			} else {
						
			}
			
			/*pdf link items */
			$query = "
			select		$acco.invoice_pdf_link.id as id,
						$acco.invoice_pdf_link.filename as filename,
		  				$acco.invoice_pdf_link.invoice_id as invoice_id,
		  				$acco.invoice_pdf_link.send_id as send_id,
		  				$acco.invoice_pdf_link.sent as sent,
		  				$acco.invoice_pdf_link.pin as pin,
		  				$acco.invoice_pdf_link.downloaded as downloaded,
		  				$acco.invoice_pdf_link.recieved as recieved,
		  				$acco.invoice_pdf_link.recipient as recipient
			from		$acco.invoice_pdf_link
			where		$acco.invoice_pdf_link.invoice_id = $monthly_r[invoice_id]
		";
		
		$pdf = pg_query($conn, $query);
		
		$pdf_r = pg_fetch_array($pdf);
			
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
					$tempprice2 = number_format($tempprice1 * $it_r[vat],2,".","");
					if ($it_r[invoice_id]== $last_id) {
						$combprice += ($tempprice1 + $tempprice2);
					} else {
						$combprice = ($tempprice1 + $tempprice2);
					}
					
					$last_id = $it_r[invoice_id];
				}
			/*format price */
			$formatprice = number_format($combprice, 2, '.', '');
			/*format invoice id */
			$invidformat = chunk_split($monthly_r[invoice_id], 6, ' ');
			/*format date*/
			$dated_out = date('Y-m-d',strtotime($monthly_r[dated_out]));
			$dated_out_s = date('d',strtotime($monthly_r[dated_out]));
			
			/*format date for dowanloads*/
			$recieved_date = date('Y-m-d H:m:i',strtotime($pdf_r[recieved]));
			$sent_date = date('Y-m-d H:m:i',strtotime($pdf_r[sent]));
			
			
			/*logic for pdf link*/
			if ($pdf_r[sent]) {
				if ($pdf_r[downloaded] == t) {
					$linkoutput = "DL";
					$title = "{$lng->__('Downloaded at')} $recieved_date";
				} else {
					$linkoutput = "{$lng->__('Sent')}";
					$title = "{$lng->__('Sent at')} $sent_date";
				}
			} else {
				$linkoutput = "N/A";
				$title = "{$lng->__('Not sent yet')}";
			}
			
			echo "
				<tr>
					<td>
						<span class='small_screen'>
							$dated_out_s 
						</span>
						<span class='big_screen'>
							$dated_out
						</span>
						
					</td>
					<td>
						<a href='index.php?section=invoice&template=invoice_view&inoid=$monthly_r[outid]&invid=$monthly_r[invoice_id]'>
						$invidformat
						</a>
					</td>
					<td>
						<a href='index.php?section=invoice&template=invoice_view&inoid=$monthly_r[outid]&invid=$monthly_r[invoice_id]'>
						$monthly_r[header] - $monthly_r[addhead]
						</a>
					</td>
					<td>
					";
						if($monthly_r[pid]) { echo "<a href='index.php?section=contacts&template=contact_view&suid=$co_r[id]'><span style='color:#000;'>$co_r[lname], $co_r[fname]</span></a>"; }
						
						if($monthly_r[cid]) { echo " - <a href='index.php?section=company&template=company_view&suid=$com_r[id]'><span style='color:#656565;'>$com_r[name]</span></a>"; }
					echo "
						
					</td>
					<td>
						".number_format($formatprice,2,","," ")." € 
					</td>
					<td style='text-align:center;'>
						$cash
					</td>
					<td title='$title' style='text-align:center;'>
						$linkoutput
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
						<b>{$lng->__('Total')}:</b>
					</td>
					<td>
						$nrofinv
					</td>
					
					<td>
						
					</td>
					<td>
						
					</td>
					<td>
						<b>$totalformat €</b>
					</td>
					<td>
						
					</td>
					<td>
						
					</td>
				</tr>
				
			";
			
			$total = 0;
		}
echo "
			</table>
			
		
";		
		
}
?>