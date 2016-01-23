<?php

require 'includes/session.php';
require 'includes/accountcheck.php';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

$datenow = date('Y-m-d');

/* fetch info for invoice */

$invid = $_GET['invid'];
$inoid = $_GET['inoid'];


/* invoice template*/
$query = "
	select		id,
				cash
	from		$acco.invoice_out
	where		id = $inoid
	
";

$in = pg_query($conn, $query);

$in_r = pg_fetch_array($in);

if ($in_r[cash] == 't') {
	$cash_status =  0;
} else {
	$cash_status =  1;
}

$cash_status = 

/*update invoice cash status */

$query = "
			update $acco.invoice_out
			set		cash='$cash_status'
			where	id=$inoid
		";
$ch = pg_query($conn, $query);


$message = $lng->__('Invoice')." ".$lng->__('updated to')." ".$lng->__('Cash');
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