<?php

$acco = $_POST['acco'];

$inid = $_POST['inid'];
$ref = $_POST['ref'];
$header = $_POST['header'];
$created = $_POST['created'];
$pid = $_POST['pid'];
$cid = $_POST['cid'];
$due_date = $_POST['due_date'];
$recurring = $_POST['recurring'];
$loc = $_POST['loc'];


if ($inid) {
	$query = "
			update $acco.invoice_def
			set		ref=1,
					header='$header',
					created='$created',
					pid=$pid,
					cid=$cid,
					due_date='$due_date',
					recurring=$recurring,
					loc='$loc'
					
					
			where	id=$inid
		";
	$ch = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=invoice&template=invoice_view&inid='.$inid;
	
} else {

	
	$query = "
			insert into $acco.invoice (
			ref,
			header,
			created,
			pid,
			cid,
			due_date,
			recurring,
			loc,
			created
			
			) values (
			$ref,
			'$header',
			$pid,
			$cid,
			'$due_date',
			$recurring,
			'$loc',
			now()
			
			)
		";
	$ch = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=invoice&template=invoice_list';
	
}

		
		$message = 'Invoice Saved ';
		$icon = 'layout/img/icon_succ.png';
		
		echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";
		


		/*header("Refresh: 1; URL=".$ret_url);*/
		
?>