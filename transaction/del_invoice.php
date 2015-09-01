<?php
	$acco = $_GET['acco'];
	$ident = $_GET['ident'];
	$inoid = $_GET['inoid'];
	
	
		$query = "
			delete
			from		$acco.invoice_out
			where		$acco.invoice_out.id = $inoid;
			";
		pg_query($conn,$query);
		
		
		
		$message = "Invoice ".$ident." deleted";
		$ret_url = 'index.php?section=invoice&template=invoice_list_pend';
	
	
$icon = 'layout/img/icon_succ.png';

echo "
	<div class='messagebox'>
		<img class='messageicon' src='$icon' alt='$message'>
		<p class='messagetext'>$message</p>
	</div>
	";

		header("Refresh: 3; URL=".$ret_url);


?>
