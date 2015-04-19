<?php

$acco = $_POST['acco'];

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
	
} else {

	
	$query = "
			insert into $acco.contact_notes (
			created,
			cont,
			contact_id
			) values (
			now(),
			'$cont',
			$suid
			)
		";
	$not = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=contacts&template=contact_view&suid='.$suid;
	
}

		
		$message = 'Note Saved ';
		$icon = 'layout/img/icon_succ.png';
		
		echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";
		


		header("Refresh: 1; URL=".$ret_url);
		
?>