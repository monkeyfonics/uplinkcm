<?php

require 'includes/session.inc';
require 'includes/accountcheck.inc';

$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];


/* fetch info for invoice */

$ident = $_GET['ident'];
$inoid = $_GET['inoid'];


/*update invoice print status */

$query = "
			update $acco.invoice_out
			set		pub=true	
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