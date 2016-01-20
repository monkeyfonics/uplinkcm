<?php
	
$datenow = date('Y-m-d');



	/* invoice */
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
  				next_create,
  				recurring,
  				active
	from		$acco.invoice_def
	
	
";

$in = pg_query($conn, $query);

echo "
		<div class='console'>
			<div>Console:</div>
	";
while ($in_r = pg_fetch_array($in)) {
	$inid = $in_r[id];
	$ident = $in_r[ident];
	
	$next = date('Y-m-d', strtotime($in_r[next_create]));
	$dated = date('Y-m-d', strtotime($in_r[dated]));
	$end_date = date('Y-m-d', strtotime($in_r[end_date]));
	
	
	/*copying values from invoice def */
	$header = $in_r[header];
	$pid = $in_r[pid];
	$cid = $in_r[cid];
	$loco = $in_r[loc];
	
	$rec = $in_r[recurring];
	if ($rec == 0) {
		$rec = 1;
	}
	
	$dateplus = date('Y-m-d', strtotime("+".$rec." month")); 
	
	/* if ongoing then assume end date is today */
	if ($in_r[ongoing] == 't') {
		$end_check = $dateplus;
	} else {
		$end_check = $end_date;
	}
	
	while ($in_r[active] == 't' && $datenow >= $next && $next <= $end_check) {
		
		$outrand = rand(100, 999);
		
		/*adding random nr to invoice number, running number is added once its published*/
		$invpend = $in_r[ident].$outrand;
		
		
		/*fetch invoice items to copy even if they exist*/
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

		$ii = pg_query($conn, $query);
		/* insert copy function for items*/
		while ($ii_r = pg_fetch_array($ii)) {
			
			/* creating invoice out items */
			$query = "
			insert into $acco.invoice_out_item (
				def_id,
				cat,
				item,
				invoice_id,
				price,
				qty,
				unit,
				vat
				) values (
				$1,
				$2,
				$3,
				$4,
				$5,
				$6,
				$7,
				$8
	
			)
		";
		$ioi = pg_query_params($conn, $query, Array($ii_r[id],$ii_r[cat],$ii_r[item],$invpend,$ii_r[price],$ii_r[qty],$ii_r[unit],$ii_r[vat]));
			
			
		}
		
		
		
		
		$month_n = date('Y-m-d', strtotime($next));
		
		$end_n = date('Y-m-d', strtotime($end_date));
		$month = date('F', strtotime($next));
		
		if ($end_n <= $datenow) {
			$end_n = $end_n;
		} elseif ($end_n = "") {
			$end_n = $datenow;
		} else {
			$end_n = $datenow;
		}
		
		
		echo "<hr/>{$lng->__('Invoice')}: <br/>".$in_r[header]." - ";
		
		
		
		
		/*write invoice outs according to published/recurring/date */
		$startTime = strtotime($month_n);
		$endTime = strtotime($end_n);
		
		do {
			
			/*test translate according to invoice language*/
			$langu = $in_r['loc'];
			$inlang = new Translator($langu);//$outputlanguage: ISO code (example: de,en,fi,sv...) --> these are the names of each file
			$inlang->setPath('lang/pdf');
			/*translate month into invoice language*/
			$monthtrans = "{$inlang->__($month)}";
			
			echo $monthtrans." $invnextid<br/>";
			
			$startTime = strtotime('+'.$rec.' months',$startTime); 
			
			
			
		
			$dueplus = date('Y-m-d', strtotime('+1 week', strtotime($datenow)));
			
			/* reference gen */
			$rand = rand(100, 999);
			$refdat = date('Ymd', strtotime($next));
			$ref = "1".$refdat.$rand;
			
			$compref = $ref.viite($ref);
			
			
			$pub = "f";
			$cash = "f";
			
			
			/* creating invoice outs */
			$query = "
			insert into $acco.invoice_out (
				header,
				pid,
				cid,
				loc,
				addhead,
				def_id,
				invoice_id,
				created,
				dated,
				due_date,
				ref,
				pub,
				cash
				
				) values (
				$1,
				$2,
				$3,
				$4,
				$5,
				$6,
				$7,
				$8,
				$9,
				$10,
				$11,
				$12,
				$13
	
			)
		";
		$io = pg_query_params($conn, $query, Array($header,$pid,$cid,$loco,$monthtrans,$ident,$invpend,$datenow,$next,$dueplus,$compref,$pub,$cash));
		
		
		/* try to check if date exceeds max date for month*/
		
		//nextcreate before add
		$nextbefore = explode("-", $next);
		$next_check = date('Y-m-d', mktime(0, 0, 0, $nextbefore[1], 1, $nextbefore[0]));
		
		/* add recurring value to last created just for check */	
		$next_c = date('Y-m-d', strtotime("+".$rec." months", strtotime($next_check)));
		$next_m = date('t', strtotime("+".$rec." months", strtotime($next_check)));
		$nextafter = explode("-", $next_c);
		
		/* check if day is more the max of month */
		if ($nextbefore[2] > $next_m) {
			$next = date("Y-m-d", mktime(0, 0, 0, $nextafter[1], $next_m, $nextafter[0]));
			
		} else {
			/* add recurring value to last created */	
			$next = date('Y-m-d', strtotime("+".$rec." months", strtotime($next)));
			
		}
		/* add recurring value to month */	
		$month = date('F', strtotime("+".$rec." months", strtotime($month)));
		
		} while ($startTime < $endTime);
		
		
		//echo "start".$month_n."i".$i."end".$end_n;
		
		
		$query = "
			update $acco.invoice_def
			set		next_create='$next'		
			where	id=$inid
			";
			$iu = pg_query($conn, $query);
		/*
		 * must check to see that template isnt ongoing */
		if ($in_r[ongoing] == 'f' && $next >= $end_date) {
			//change active to false if enddate is reached
			$query = "
			update $acco.invoice_def
			set		active='f'	
			where	id=$inid
			";
			$act = pg_query($conn, $query);
		}
		 
	}
}
 echo "</div>";
 
/* checking for invoice outs */
$query = "
	select		id,
				addhead,
				invoice_id,
				created,
				due_date,
				ref
	from		$acco.invoice_out
	
	
";

$io = pg_query($conn, $query);



	
	


?>