<?php

$acco = $_GET['acco'];

$tid = $_GET['tid'];



/* todo*/
$query = "
	select		id,
				completed
	from		$acco.todo
	where		id = $tid
	
";

$todo = pg_query($conn, $query);

$todo_r = pg_fetch_array($todo);

if ($todo_r[completed] == 't') {
	$act = 0;
} else {
	$act = 1;
}


$query = "
			update $acco.todo
			set		completed='$act'
					
			where	id=$tid
		";
$ch = pg_query($conn, $query);
	
	
	
	
	$ret_url = 'index.php?section=todo&template=todo_view&tid='.$tid;
	$message = 'Todo Updated';
	$icon = 'layout/img/icon_succ.png';
		
		echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";
	

	header("Refresh: 1; URL=".$ret_url);
?>