<?php

$acco = $_POST['acco'];

$suid = $_POST['suid'];
$lname = $_POST['lname'];
$fname = $_POST['fname'];
$phone1 = $_POST['phone1'];
$bill_addr = $_POST['bill_addr'];
$bill_zip = $_POST['bill_zip'];
$bill_city = $_POST['bill_city'];
$bill_country = $_POST['bill_country'];
$email = $_POST['email'];
$www = $_POST['www'];
$loco = $_POST['loco'];

$newcompany = $_POST['nco'];

if ($suid) {
	$query = "
			update $acco.contacts
			set		lname='$lname',
					fname='$fname',
					phone1='$phone1',
					email='$email',
					bill_addr='$bill_addr',
					bill_zip='$bill_zip',
					bill_city='$bill_city',
					bill_country='$bill_country',
					www='$www',
					modified=now(),
					loc='$loco'
					
			where	id=$suid
		";
	$ch = pg_query($conn, $query);
	
	$message = 'Contact Updated ';
	
	
	$ret_url = 'index.php?section=contacts&template=contact_view&suid='.$suid;
	
} else {

	
	$query = "
			insert into $acco.contacts (
			lname,
			fname,
			phone1,
			phone2,
			email,
			bill_addr,
			bill_zip,
			bill_city,
			bill_country,
			www,
			created,
			modified,
			loc
			) values (
			'$lname',
			'$fname',
			'$phone1',
			'$phone2',
			'$email',
			'$bill_addr',
			'$bill_zip',
			'$bill_city',
			'$bill_country',
			'$www',
			now(),
			now(),
			'$loco'
			)
		";
	$ch = pg_query($conn, $query);
	
	$message = 'Contact Added ';
	
	$ret_url = 'index.php?section=contacts&template=contact_list';
	
}
/* adding companies to contact */
if (isset($_POST['add_co'])) {
		$query = "
			insert into $acco.link_company_contact (
			contact_id,
			company_id
			) values (
			$suid,
			$newcompany
			)
		";
	$co = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=contacts&template=contact_view&suid='.$suid;
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