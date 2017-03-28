<?php


$acco = $_GET['acco'];

$ident = $_GET['ident'];
	
/* invoice */
$query = "
	select		id,
				ident,
				header,
				pid,
				cid,
  				loc,
  				created,
  				dated,
  				ongoing,
  				end_date,
  				next_create,
  				recurring,
  				active
	from		$acco.invoice_def
	where		ident = $ident
	
";

$in = pg_query($conn, $query);

$in_r = pg_fetch_array($in);

/*contact */
$query = "
	select		$acco.invoice_def.cid,
				$acco.contacts.id as coid,
				$acco.contacts.lname as lname,
				$acco.contacts.fname as fname
	FROM 		$acco.contacts LEFT OUTER JOIN $acco.invoice_def ON ($acco.contacts.id = $acco.invoice_def.pid)
	where		$acco.contacts.id = $in_r[pid]
	
";

$cl = pg_query($conn, $query);
$cl_r = pg_fetch_array($cl);

/*company */
$query = "
	select		$acco.invoice_def.cid,
				$acco.company.id as cmid,
				$acco.company.name as name
	FROM 		$acco.company LEFT OUTER JOIN $acco.invoice_def ON ($acco.company.id = $acco.invoice_def.cid)
	where		$acco.company.id = $in_r[cid]
	
";

$co = pg_query($conn, $query);
$co_r = pg_fetch_array($co);

/*fetch*/
$query = "
	select		id,
				cat,
				item,
				invoice_id,
				price,
  				qty,
  				unit,
  				vat
  				
	from		$acco.invoice_def_item
	where		invoice_id = $ident
	
";

$it = pg_query($conn, $query);


/* copy the old data into new record with a new ident number */
	$newheader = $in_r[header]." - copy";


	$idf = date('Ym', strtotime($in_r[dated]));
	$ids = rand(100, 999);
	$ident = $idf.$ids;
	
	$query = "
			insert into $acco.invoice_def (
			ident,
			header,
			pid,
			cid,
			created,
			dated,
			ongoing,
			end_date,
			next_create,
			recurring,
			loc,
			active
			
			) values (
			$ident,
			'$newheader',
			$in_r[pid],
			$in_r[cid],
			now(),
			'$in_r[dated]',
			'$in_r[ongoing]',
			'$in_r[end_date]',
			'$in_r[next_create]',
			$in_r[recurring],
			'$in_r[loc]',
			'false'
			
			
			)
		";
	$ch = pg_query($conn, $query);
	
	/* copy invoice items also */
	


while ($it_r = pg_fetch_array($it)) {
	
	
	
	$query = "
			insert into $acco.invoice_def_item (
			item,
			invoice_id,
			price,
			qty,
			unit,
			vat,
			cat
			) values (
			'$it_r[item]',
			$ident,
			$it_r[price],
			$it_r[qty],
			$it_r[unit],
			$it_r[vat],
			$it_r[cat]
			)
		";
	$cr = pg_query($conn, $query);
}
	
	$message = $lng->__('Template Copied')." $in_r[header]";
	$icon = 'layout/img/icon_succ.png';
	$ret_url = 'index.php?section=def&template=def_edit&ident='.$ident;
	
	echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";
		
		header("Refresh: 3; URL=".$ret_url);
		
	
	
?>