<?php

$acco = $_GET['acco'];

$inid = $_GET['inid'];
$ident = $_GET['ident'];


/* invoice template*/
$query = "
	select		id,
				ident,
				header,
				pid,
				cid,
  				loc,
  				created,
  				end_date,
  				next_create,
  				recurring,
  				active
	from		$acco.invoice_def
	where		ident = $ident
	
";

$in = pg_query($conn, $query);

$in_r = pg_fetch_array($in);

if ($in_r[active] == 't') {
	$act = 0;
} else {
	$act = 1;
}


$query = "
			update $acco.invoice_def
			set		active='$act'
					
			where	ident=$ident
		";
	$ch = pg_query($conn, $query);
	
	
	
	
	$ret_url = 'index.php?section=def&template=def_list';
	$message = "{$lng->__('Template Activated')}";
	$icon = 'layout/img/icon_succ.png';
		
		echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";
	

	header("Refresh: 1; URL=".$ret_url);
?>
