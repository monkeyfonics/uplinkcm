<?php
	$acco = $_GET['acco'];
	$conid = $_GET['suid'];

		$query = "
			delete
			from		$acco.contacts
			where		$acco.contacts.id = $conid;
			";
		pg_query($conn,$query);
		
		/*remove association also to companies */
		$query = "
			delete
			from		$acco.link_company_contact
			where		$acco.link_company_contact.contact_id = $conid;
			";
		pg_query($conn,$query);
		
		
		
		$message = "Contact ".$ident." deleted";
		$ret_url = 'index.php?section=contacts&template=contact_list';
	
	
$icon = 'layout/img/icon_succ.png';

echo "
	<div class='messagebox'>
		<img class='messageicon' src='$icon' alt='$message'>
		<p class='messagetext'>$message</p>
	</div>
	";

		header("Refresh: 3; URL=".$ret_url);


?>