<?php

$acco = $_POST['acco'];
$s_id = $_SESSION['s_id'];

$suid = $_POST['suid'];
$cont = $_POST['cont'];
$notid = $_POST['notid'];


if ($notid) {
	$query = "
			update $acco.contact_notes
			set		cont='$cont'
					
			where	id=$notid
		";
	$not = pg_query($conn, $query);
	

	$ret_url = 'index.php?section=contacts&template=contact_view&suid='.$suid;
	$message = "{$lng->__('Note')} {$lng->__('updated')}";
} else {

	
	$query = "
			insert into $acco.contact_notes (
			created,
			cont,
			contact_id,
			created_by
			) values (
			now(),
			'$cont',
			$suid,
			$s_id
			)
		";
	$not = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=contacts&template=contact_view&suid='.$suid;
	$message = "{$lng->__('Note')} {$lng->__('saved')}";
}

		
		
		$icon = 'layout/img/icon_succ.png';
		
		echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";
		


		header("Refresh: 1; URL=".$ret_url);
		
?>