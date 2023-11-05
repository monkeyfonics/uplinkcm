<?php

require 'invoice_gen.php';


/*update invoice print status */

$query = "
			update $acco.invoice_out
			set		printed=now()
			where	id=$inoid
		";
$ch = pg_query($conn, $query);
	


/*output pdf to browser*/
$invoice->display("{$custlng->__('Invoice')}_".$acc_hold_r['name']."_".$in_r['invoice_id']);

?>