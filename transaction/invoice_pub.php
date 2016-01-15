<?php

require 'includes/session.php';
require 'includes/accountcheck.php';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

$datenow = date('Y-m-d');

/* fetch info for invoice */

$invid = $_GET['invid'];
$inoid = $_GET['inoid'];

/*make due date one week from today*/
$dueplus = date('Y-m-d', strtotime('+1 week', strtotime($datenow)));


/* create running number for invoices once they are published */

		$query = "
		SELECT MAX(runid) as runid
		from		$acco.invoice_out
		ORDER BY 	runid DESC 
		LIMIT 1
		";
		$invmaxid = pg_query($conn, $query);
		$invmaxid_r = pg_fetch_array($invmaxid);
		
		/* add 1 to get the next free number*/
		$invnextid = $invmaxid_r[runid]+1;

/* adding running number to invoice when its published*/
 		$invoice_id_add = $invid.$invnextid;
		
/*update invoice publish status */

$query = "
			update $acco.invoice_out
			set		pub=true,
					due_date='$dueplus',
					runid=$invnextid
			where	id=$inoid
		";
	$ch = pg_query($conn, $query);


$message = $lng->__('Invoice')." ".$lng->__('Published');
	$icon = 'layout/img/icon_succ.png';
	$ret_url = 'index.php?section=invoice&template=invoice_list';


echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";
		

		


		header("Refresh: 1; URL=".$ret_url);

?>