<?php

$acco = $_POST['acco'];
$ident = $_POST['ident'];
$inoid = $_POST['inoid'];
$invid = $_POST['invid'];
$ref = $_POST['ref'];
$header = $_POST['header'];
$addheader = $_POST['addheader'];
$dated = $_POST['dated'];
$pid = $_POST['pid'];
$cid = $_POST['cid'];
$due_date = $_POST['due_date'];
$loc = $_POST['loc'];


if ($inoid) {
	$query = "
			update $acco.invoice_out
			set		
					header='$header',
					addhead='$addheader',
					pid=$pid,
					cid=$cid,
					dated='$dated',
					due_date='$due_date',
					loc='$loc'
	
			where	id=$inoid
		";
	$ch = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=invoice&template=invoice_view&inoid='.$inoid;
	
	/* updating invoice items*/
	if (isset($_POST['ids'])) {
		$ids = $_POST['ids'];
		foreach ($ids as $i) {
			/* existing invoice item */
			
			/* if no price*/
			if (!$_POST['price'][$i]) {
				$price[$i] = 0;
			} else {
				$price[$i] = $_POST['price'][$i];
			}
			/* if no qty*/
			if (!$_POST['qty'][$i]) {
				$qty[$i] = 1;
			} else {
				$qty[$i] = $_POST['qty'][$i];
			}
			
			$cat[$i] = $_POST['cat'][$i];
			$item[$i] = $_POST['item'][$i];
			$vat[$i] = $_POST['vat'][$i];
			$unit[$i] = $_POST['unit'][$i];	
			
			 
			 
			
			$price[$i] = str_replace($deci,".",$price[$i]);
			$price[$i] = str_replace(" ","",$price[$i]);
			
			$qty[$i] = str_replace(",",".",$qty[$i]);
			
			
			$query = "
				update		$acco.invoice_out_item
				set			item='$item[$i]',
							invoice_id=$invid,
							qty=$qty[$i],
							unit=$unit[$i],
							price=$price[$i],
							vat=$vat[$i],
							cat=$cat[$i]
							
				where		id=$i		
				";
			pg_query($conn,$query);
		}
		
	}
}

		
		$message = "{$lng->__('Invoice Updated')}";
		$icon = 'layout/img/icon_succ.png';
		
		echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";
		


		header("Refresh: 1; URL=".$ret_url);
		
?>