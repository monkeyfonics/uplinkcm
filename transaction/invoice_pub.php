<?php

require 'includes/session.php';
require 'includes/accountcheck.php';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

$datenow = date('Y-m-d');

/* fetch info for invoice */

$invpend = $_GET['invid'];
$inoid = $_GET['inoid'];

/*make due date one week from today*/
$dueplus = date('Y-m-d', strtotime('+1 week', strtotime($datenow)));

/*create new invoice id from date and runnng number */
$id_date = date('Ym');


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
		if ($invnextid >= 10000) {
			$invnextid = '0001';
		}
		$invnextid = str_pad($invnextid, 4, "0", STR_PAD_LEFT);
/* adding running number to invoice when its published*/
 		$invoice_id = $id_date.$invnextid;
		
/*update invoice publish status */

$query = "
			update $acco.invoice_out
			set		pub=true,
					dated='$datenow',
					due_date='$dueplus',
					invoice_id=$invoice_id,
					runid=$invnextid
			where	id=$inoid
		";
$ch = pg_query($conn, $query);
	
/*update invoice items to same invoiceid*/
$query = "
			update $acco.invoice_out_item
			set		invoice_id=$invoice_id
					
			where	invoice_id=$invpend
		";
$ui = pg_query($conn, $query);


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