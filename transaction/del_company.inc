<?php
	$acco = $_GET['acco'];
	$comid = $_GET['suid'];

		$query = "
			delete
			from		$acco.company
			where		$acco.company.id = $comid;
			";
		pg_query($conn,$query);
		
		/*remove association also to companies */
		$query = "
			delete
			from		$acco.link_company_contact
			where		$acco.link_company_contact.company_id = $comid;
			";
		pg_query($conn,$query);
		
		
		
		$message = "Company ".$ident." deleted";
		$ret_url = 'index.php?section=company&template=company_list';
	
	
$icon = 'layout/img/icon_succ.png';

echo "
	<div class='messagebox'>
		<img class='messageicon' src='$icon' alt='$message'>
		<p class='messagetext'>$message</p>
	</div>
	";

		header("Refresh: 3; URL=".$ret_url);


?>