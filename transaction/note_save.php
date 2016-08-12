<?php

$acco = $_POST['acco'];
$s_id = $_SESSION['s_id'];

$consuid = $_POST['consuid'];
$comsuid = $_POST['comsuid'];

if ($_POST['consuid']) {
	$consuid = $_POST['consuid'];
	$comsuid = 0;
	$ret_url = 'index.php?section=contacts&template=contact_view&suid='.$consuid;
} else {
	$consuid = 0;
	$comsuid = $_POST['comsuid'];
	$ret_url = 'index.php?section=company&template=company_view&suid='.$comsuid;
}

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
			company_id,
			created_by
			) values (
			now(),
			'$cont',
			$consuid,
			$comsuid,
			$s_id
			)
		";
	$not = pg_query($conn, $query);
	
	
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