<?php 

$acd = $_POST['account'];
$user_id = $_POST['user'];
$type1 = $_POST['type1'];
$type2 = $_POST['type2'];
$deac = $_POST['deac'];
$act = $_POST['act'];
$del = $_POST['del'];

if ($type2 == 'add') {
	/*add user to an account */
	$query = "
			insert into account_user_link (
			acc_id,
			user_id,
			active
			) values (
			$acd,
			$user_id,
			TRUE
			
			)
		";
$ch = pg_query($conn, $query);


$message = 'Added user';
} elseif ($deac) {
	
/*deactivate user */
	$query = "
			update account_user_link
			set		active='$act'
			where	acc_id=$acd 
			and 	user_id=$deac
			
			
		";
$ch = pg_query($conn, $query);

$message = 'Updated Account';
}


if ($del) {
	
	$query = "
			DELETE FROM account_user_link
			WHERE 	acc_id=$acd
			and 	user_id=$del
			
			
		";
		$dlte = pg_query($conn, $query);
		
$message = 'Deleted user';
}



$ret_url = 'index.php?section=admin&template=account_user&acid='.$acd;

$icon = 'layout/img/icon_succ.png';

echo "
	<div class='messagebox'>
		<img class='messageicon' src='$icon' alt='$message'>
		<p class='messagetext'>$message</p>
	</div>
	";


header("Refresh: 1; URL=".$ret_url);
?>