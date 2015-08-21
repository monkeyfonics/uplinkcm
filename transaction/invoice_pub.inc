<?php

require 'includes/session.inc';
require 'includes/accountcheck.inc';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

$datenow = date('Y-m-d');

/* fetch info for invoice */

$ident = $_GET['ident'];
$inoid = $_GET['inoid'];

/*make due date one week from today*/
$dueplus = date('Y-m-d', strtotime('+1 week', strtotime($datenow)));

/*update invoice print status */

$query = "
			update $acco.invoice_out
			set		pub=true,
					due_date='$dueplus'
			where	id=$inoid
		";
	$ch = pg_query($conn, $query);


$message = $lng->__('Invoice')." ".$lng->__('Published').$dueplus;
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