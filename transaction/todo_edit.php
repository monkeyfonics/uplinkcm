<?php

$acco = $_POST['acco'];

$tid = $_POST['tid'];
$cont = $_POST['cont'];
$created = $_POST['created'];
$due = $_POST['due'];
$pid = $_POST['pid'];
$cid = $_POST['cid'];


if ($tid) {
	$query = "
			update $acco.todo
			set		cont='$cont',
					due='$due',
					contact_id=$pid,
					company_id=$cid
					
			where	id=$tid
		";
	$todo = pg_query($conn, $query);
	

	$ret_url = 'index.php?section=todo&template=todo_view&tid='.$tid;
	$message = 'Todo Updated ';
} else {
	
	
	$query = "
			insert into $acco.todo (
			created,
			due,
			cont,
			contact_id,
			company_id,
			completed
			) values (
			'$created',
			'$due',
			'$cont',
			$pid,
			$cid,
			false
			)
		";
	$todo = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=todo&template=todo_list';
	$message = 'Todo Saved ';
}

		
		
		$icon = 'layout/img/icon_succ.png';
		
		echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message $tid</p>
			</div>
			";
		


		header("Refresh: 1; URL=".$ret_url);
		
?>