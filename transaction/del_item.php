<?php
	$acd = $_GET['acid'];
	$item = $_GET['item'];
	$user_id = $_GET['usid'];
	if ($item = "user") {
		$query = "
			delete
			from		users
			where		id = $user_id
			";
		pg_query($conn,$query);
		
		$del = pg_query($conn, $query);
		
		$message = "User: ".$user_id." deleted";
		$ret_url = 'index.php?section=admin&template=user_new_list';
	}
	
$icon = 'layout/img/icon_succ.png';

echo "
	<div class='messagebox'>
		<img class='messageicon' src='$icon' alt='$message'>
		<p class='messagetext'>$message</p>
	</div>
	";

		header("Refresh: 3; URL=".$ret_url);


?>
