<?php

$new = $_POST['no'];
$usid = $_POST['usid'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$tel = $_POST['tel'];




if ($new == "old") {
	$query = "
			update users
			set		fname='$fname',
					lname='$lname',
					tel='$tel'
					
			where	id=$usid
		";
	$ch = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=admin&template=user_info&usid='.$usid;
	$message = 'User Updated';
	$icon = 'layout/img/icon_succ.png';
	
} elseif ($new == "new") {
	
	$query = "
			insert into users (
			fname,
			lname,
			tel
			
			) values (
			'$fname',
			'$lname',
			'$tel'
			)
		";
	$ch = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=admin&template=user_list';
	$message = 'User Saved';
	$icon = 'layout/img/icon_succ.png';
} else {
	$message = 'Failed';
	$icon = 'layout/img/icon_fail.png';
	$ret_url = 'index.php?section=admin&template=main';
}

		
		
		

		
echo "
	<div class='messagebox'>
		<img class='messageicon' src='$icon' alt='$message'>
		<p class='messagetext'>$message</p>
	</div>
	";

		header("Refresh: 1; URL=".$ret_url);
		
?>