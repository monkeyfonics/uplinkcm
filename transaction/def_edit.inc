<?php

$acco = $_POST['acco'];
$blank = $_POST['blank'];

$inid = $_POST['inid'];
$ident = $_POST['ident'];

$header = $_POST['header'];
$created = $_POST['created'];
$dated = $_POST['dated'];
$pid = $_POST['pid'];
$cid = $_POST['cid'];
$end_date = $_POST['end_date'];
$ongoing = $_POST['ongoing'];
$next_create = $_POST['next_create'];
$recurring = $_POST['recurring'];
$loc = $_POST['loc'];

//Dated
$datedsplit = explode("-", $dated);
$dated_max = date("t", mktime(0, 0, 0, $datedsplit[1], 1, $datedsplit[0]));

//nextcreate
$nextsplit = explode("-", $next_create);
$next_max = date("t", mktime(0, 0, 0, $nextsplit[1], 1, $nextsplit[0]));


/* check if day is more the max of month */
if ($nextsplit[2] > $next_max) {
	$next_create = date("Y-m-d", mktime(0, 0, 0, $nextsplit[1], $next_max, $nextsplit[0]));
	
}
if ($datedsplit[2] > $dated_max) {
	
	$dated = date("Y-m-d", mktime(0, 0, 0, $datedsplit[1], $dated_max, $datedsplit[0]));
	
}



/*check if template is new */
if ($blank == 1) {
	$next_create = $dated;
}
/* or if next creat is smaller then dated*/
if ($next_create < $dated){
	$next_create = $dated;
	
}


if ($ongoing == 't') {
	$on = 't';
	$end_date = $dated;
} else {
	$on = 'f';
}

if ($ident) {
	$query = "
			update $acco.invoice_def
			set		header='$header',
					dated='$dated',
					next_create='$next_create',
					pid=$pid,
					cid=$cid,
					ongoing='$on',
					end_date='$end_date',
					recurring=$recurring,
					loc='$loc'
					
					
			where	id=$inid
		";
	$ch = pg_query($conn, $query);
	
	$message = 'Template Updated';
	$icon = 'layout/img/icon_succ.png';
	$ret_url = 'index.php?section=def&template=def_view&&ident='.$ident;
	
} else {
	$idf = date('Ym', strtotime($dated));
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
			'$header',
			$pid,
			$cid,
			now(),
			'$dated',
			'$ongoing',
			'$end_date',
			'$next_create',
			$recurring,
			'$loc',
			'false'
			
			
			)
		";
	$ch = pg_query($conn, $query);
	
	$message = $lng->__('Template Saved');
	$icon = 'layout/img/icon_succ.png';
	$ret_url = 'index.php?section=def&template=def_view&ident='.$ident;
	
}
$deci = array(',');
/* new invoice item */


if (isset($_POST['add_item'])) {
	
	/* need to write invoice id somehow*/
	
	/* if no price*/
	if (!$_POST['pricen']) {
		$pricen = 0;
	} else {
		$pricen = $_POST['pricen'];
	}
	/* if no qty*/
	if (!$_POST['qtyn']) {
		$qtyn = 1;
	} else {
		$qtyn = $_POST['qtyn'];
	}
	
	
	$catn = $_POST['catn'];
	$itemn = $_POST['itemn'];
	$unitn = $_POST['unitn'];
	$vatn = $_POST['vatn'];

	$pricen = str_replace($deci,".",$pricen);
	$pricen = str_replace(" ","",$pricen);
	
	$qtyn = str_replace(",",".",$qtyn);
	
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
			'$itemn',
			$ident,
			$pricen,
			$qtyn,
			$unitn,
			$vatn,
			$catn
			)
		";
	$cr = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=def&template=def_edit&ident='.$ident;
}
/*deleting items */
if (isset($item_del)) {
	$query = "
		delete
		from		invoice_item 
		where		id = $del
		";
	pg_query($conn,$query);
}

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
			update		$acco.invoice_def_item
			set			item='$item[$i]',
						invoice_id=$ident,
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

		
		
		echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";
		

		


		header("Refresh: 1; URL=".$ret_url);
		
?>