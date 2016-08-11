<?php
if ($s_lvl < 1) {
	
} else {
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

/*contacts*/

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
  				created
	from		$acco.contacts
	order by	created desc
	limit 		10
	
";

$ul = pg_query($conn, $query);

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
					$acco.invoice_out.pub as pub,
					$acco.invoice_out.printed as printed,
					$acco.invoice_out.emailed as emailed
		from		$acco.invoice_out LEFT JOIN $acco.invoice_def
		ON			($acco.invoice_out.invoice_id = $acco.invoice_def.ident)
		order by	$acco.invoice_out.dated desc, $acco.invoice_out.invoice_id desc
		
		
	";

$in = pg_query($conn, $query);


/*notes*/

$query = "
	select		$acco.contact_notes.id,
				$acco.contact_notes.contact_id,
				$acco.contact_notes.cont as cont,
  				$acco.contact_notes.created as created,
  				$acco.contacts.fname as fname,
  				$acco.contacts.lname as lname
	from		$acco.contact_notes LEFT JOIN $acco.contacts
	ON			($acco.contact_notes.contact_id = $acco.contacts.id)
	order by	created desc
	limit 		5
	
";

$note = pg_query($conn, $query);

/*todo*/

$query = "
	select		$acco.todo.id,
				$acco.todo.contact_id,
				$acco.todo.company_id,
				$acco.todo.cont as cont,
  				$acco.todo.created as created,
  				$acco.todo.completed as completed,
  				$acco.contacts.fname as fname,
  				$acco.contacts.lname as lname
	from		$acco.todo LEFT JOIN $acco.contacts
	ON			($acco.todo.contact_id = $acco.contacts.id)
	order by	completed, created desc
	limit 		5
	
";

$todo = pg_query($conn, $query);

/* common variables*/
$today = date('Y-m-d');
$dayfirst = "01";
$monthnow = date(m);
$yearnow = date(Y);
$minus =  date("Y-m-d", strtotime("$yearnow-$monthnow-$dayfirst"));
//remove one day to make it last day of last month
$lastofmonth = strtotime ( '-1 day' , strtotime ( $minus ) ) ;
$lastofmonth = date ( 'Y-m-d' , $lastofmonth );
$lastofmonth01 = strtotime ( '-1 month' , strtotime ( $minus ) ) ;
$lastofmonth01 = date ( 'Y-m-d' , $lastofmonth01 );
//remove one month to make 3 month comparison
$twomonthlast = strtotime ( '-1 months, -1 day' , strtotime ( $minus ) ) ;
$twomonthlast = date ( 'Y-m-d' , $twomonthlast );
$twomonthlast01 = strtotime ( '-2 months' , strtotime ( $minus ) ) ;
$twomonthlast01 = date ( 'Y-m-d' , $twomonthlast01 );

$monthnowtext = date(F);
$lastmonthtext = strtotime ( '-1 month' , strtotime ( $monthnowtext ) ) ;
$lastmonthtext = date ( 'F' , $lastmonthtext );
$month3text = strtotime ( '-2 month' , strtotime ( $monthnowtext ) ) ;
$month3text = date ( 'F' , $month3text );
echo "
	<div class='frontcont'>
		
	";


		

/* widget new invoice*/
echo "
		<div class='widget'>
			<div class='header'><a href='index.php?section=invoice&template=invoice_list'>{$lng->__('New Invoices')}</a></div>
			<h4>$minus - $today</h4>
			<table class='list'>
				
	";
		$pubcount = 0;
		$unpubcount = 0;
		$printcount = 0;
		$emailcount = 0;
		$tosend = 0;
		
		$unpubcountlast = 0;
		$pubcountlast =0;
		
		$unpubcount3 = 0;
		$pubcount3 =0;
		while ($in_r = pg_fetch_array($in)) {
			$date = date('Y-m-d', strtotime($in_r[dated_out]));
			
			if ($date > $minus) {
				if ($in_r[pub] == f) {
					$pub = 'pub';
					$unpubcount++;
					
				} else {
					$pub = ' ';
					$pubcount++;
					/*check how many are unsent of the published*/
					if ($in_r[printed] == '' and $in_r[emailed] == '') {
						$tosend++;
					} else {
						
					}
				}
				/* check for printed flag*/
				if ($in_r[printed] == '') {
					
				} else {
					$printcount++;
				}
				/* check for emailed flag*/
				if ($in_r[emailed] == '') {
					
				} else {
					$emailcount++;
				}
				
			} //end of this month
			
			// last month
			if ($date > $lastofmonth01 and $date < $lastofmonth) {
				if ($in_r[pub] == f) {
					$unpubcountlast++;
				} else {
					$pubcountlast++;
					/*check how many are unsent of the published*/
					if ($in_r[printed] == '' and $in_r[emailed] == '') {
						
					} else {
						
					}
				}
				
			} //end of last month
			// 3rd month
			if ($date > $twomonthlast01 and $date < $twomonthlast) {
				if ($in_r[pub] == f) {
					$unpubcount3++;
				} else {
					$pubcount3++;
					
				}
				
			} //end of 3rd month
			
		}
echo "
				<tr>
					<td class='bold'>
						<a href='index.php?section=invoice&amp;template=invoice_list_pend'>
						{$lng->__('Pending')}
						</a>
					</td>
					<td class='center'>
						
						$unpubcount
					</td>
				</tr>
				<tr>
					<td class='bold'>
						<a href='index.php?section=invoice&amp;template=invoice_list'>
							{$lng->__('Published')}
						</a>
					</td>
					<td class='center'>
						$pubcount
					</td>
				</tr>
				<tr>
					<td class='bold'>
						<a href='index.php?section=invoice&amp;template=invoice_list'>
							{$lng->__('Printed')}
						</a>
					</td>
					<td class='center'>
						$printcount
					</td>
				</tr>
				<tr>
					<td class='bold'>
						<a href='index.php?section=invoice&amp;template=invoice_list'>
							{$lng->__('Emailed')}
						</a>
					</td>
					<td class='center'>
						$emailcount
					</td>
				</tr><tr>
					<td class='bold'>
						<a href='index.php?section=invoice&amp;template=invoice_list'>
							{$lng->__('Unsent')}
						</a>
					</td>
					<td class='center'>
						$tosend
					</td>
				</tr>
			</table>
";			
			/*barchart to compare this month and last month */
				/* determine order of highest to lowest values*/
				if ($pubcount >= $pubcountlast and $pubcount >= $pubcount3) {
					$high = $pubcount;
					$medium = $pubcountlast;
					$low = $pubcount3;
					if ($high != 0) {
					$highpros = 100;
					$mediumpros = ($medium / $high)*100;
					$lowpros = ($low / $high)*100;
					}
					$month1pros = $highpros;
					$month2pros = $mediumpros;
					$month3pros = $lowpros;
				} elseif ($pubcountlast >= $pubcount and $pubcountlast >= $pubcount3) {
					$high = $pubcountlast;
					$medium = $pubcount;
					$low = $pubcount3;
					if ($high != 0) {
					$highpros = 100;
					$mediumpros = ($medium / $high)*100;
					$lowpros = ($low / $high)*100;
					}
					$month1pros = $mediumpros;
					$month2pros = $highpros;
					$month3pros = $lowpros;
				} elseif ($pubcount3 >= $pubcount and $pubcount3 >= $pubcountlast) {
					$high = $pubcount3;
					$medium = $pubcount;
					$low = $pubcountlast;
					if ($high != 0) {
					$highpros = 100;
					$mediumpros = ($medium / $high)*100;
					$lowpros = ($low / $high)*100;
					}
					$month1pros = $lowpros;
					$month2pros = $mediumpros;
					$month3pros = $highpros;
				}
			
echo "		
			<h4 title='{$lng->__('Invoices compared to last month')}'>{$lng->__('Chart')}</h4>
			
			<div class='chartcont'>
				<div class='linecont'>
					<div class='chartheader'>{$lng->__($monthnowtext)}</div>
					<div class='chartbarcont'><div style='width: $month1pros%' class='chartbar' title='{$lng->__('Number of invoices')}'>$pubcount</div></div>
				</div>
				<div class='linecont'>
					<div class='chartheader'>{$lng->__($lastmonthtext)}</div>
					<div class='chartbarcont'><div style='width: $month2pros%' class='chartbar' title='{$lng->__('Number of invoices')}'>$pubcountlast</div></div>
				</div>
				<div class='linecont'>
					<div class='chartheader'>{$lng->__($month3text)}</div>
					<div class='chartbarcont'><div style='width: $month3pros%' class='chartbar' title='{$lng->__('Number of invoices')}'>$pubcount3</div></div>
				</div>
			</div>	
		</div>
";		
		

	
	/* widget new contact*/
echo "
		<div class='widget'>
			<div class='header'><a href='index.php?section=contacts&template=contact_list'>{$lng->__('New Contacts')}</a></div>
			<table class='list'>
				
	";
		while ($ul_r = pg_fetch_array($ul)) {
			$date = strtotime($ul_r[created]);
			echo "
				<tr>
					<td>
						<a href='index.php?section=contacts&template=contact_view&suid=$ul_r[id]'>
							$ul_r[lname] , $ul_r[fname]
						
							<div class='itemheader'>".date('Y-m-d', $date)."</div>
						</a>
					</td>
				</tr>
			";
		}
echo "
			</table>
		</div>
";		

/* widget new todo*/
echo "
		<div class='widget'>
			<div class='header'><a href='index.php?section=todo&template=todo_list'>{$lng->__('New Todos')}</a></div>
			<table class='list'>
				
	";
		while ($todo_r = pg_fetch_array($todo)) {
			$date = strtotime($todo_r[created]);
			if ($todo_r[cont] == '') {
				$todo_r[cont] = "{$lng->__('Empty')}";
			}
			if ($todo_r[completed] == f) {
				$bolde = 'bold';
			} else {
				$bolde = '';
			}
			echo "
				<tr>
					<td class='$bolde'>
						<a href='index.php?section=todo&template=todo_view&tid=$todo_r[id]'>
						";
							echo substr($todo_r[cont], 0, 30);
							echo " 
						
							<div class='itemheader'>".date('Y-m-d', $date)."<br/> {$lng->__('by')} $todo_r[lname], $todo_r[fname]</div>
						</a>
					</td>
				</tr>
			";
		}
echo "
			</table>
			
		</div>
";

		/* widget new notes*/
echo "
		<div class='widget'>
			<div class='header'><a href='index.php?section=contacts&template=contact_list'>{$lng->__('New Notes')}</a></div>
			<table class='list'>
				
	";
		while ($note_r = pg_fetch_array($note)) {
			$date = strtotime($note_r[created]);
			echo "
				<tr>
					
					<td>
						<a href='index.php?section=contacts&template=contact_view&suid=$note_r[contact_id]'>
						";
						echo substr($note_r[cont], 0, 30);
						echo "
							<div class='itemheader'>".date('Y-m-d', $date)."<br/> {$lng->__('by')} $note_r[lname], $note_r[fname]</div>
						</a>
					</td>
				</tr>
				
			";
		}
echo "
			</table>
		</div>
";	

}
?>