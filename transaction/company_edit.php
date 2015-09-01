<?php

$acco = $_POST['acco'];

$suid = $_POST['suid'];
$name = $_POST['name'];
$ytunnus = $_POST['ytunnus'];
$phone = $_POST['phone'];
$bill_addr = $_POST['bill_addr'];
$bill_zip = $_POST['bill_zip'];
$bill_city = $_POST['bill_city'];
$bill_country = $_POST['bill_country'];
$email = $_POST['email'];
$www = $_POST['www'];


$newcontact = $_POST['nco'];

if ($suid) {
	$query = "
			update $acco.company
			set		name='$name',
					ytunnus='$ytunnus',
					phone='$phone',
					email='$email',
					bill_addr='$bill_addr',
					bill_zip='$bill_zip',
					bill_city='$bill_city',
					bill_country='$bill_country',
					www='$www',
					modified=now()
					
			where	id=$suid
		";
	$ch = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=company&template=company_view&suid='.$suid;
	
} else {

	
	$query = "
			insert into $acco.company (
			name,
			ytunnus,
			phone,
			email,
			bill_addr,
			bill_zip,
			bill_city,
			bill_country,
			www,
			created,
			modified
			) values (
			'$name',
			'$ytunnus',
			'$phone',
			'$email',
			'$bill_addr',
			'$bill_zip',
			'$bill_city',
			'$bill_country',
			'$www',
			now(),
			now()
			)
		";
	$ch = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=company&template=company_list';
	
}
/* updating primary contact */
if (isset($_POST['cids'])) {
	$cids = $_POST['cids'];
	foreach ($cids as $i) {
		$primary[$i] = $_POST['primary'][$i];
		$query = "
			update $acco.link_company_contact
			set		prim='$primary[$i]'
			where	contact_id=$i
			and company_id=$suid
		";
	$co = pg_query($conn, $query);
	}
	$ret_url = 'index.php?section=company&template=company_edit&suid='.$suid;
}
/* adding contacts to companies */
if (isset($_POST['add_co'])) {
		$query = "
			insert into $acco.link_company_contact (
			contact_id,
			company_id
			) values (
			$newcontact,
			$suid
			)
		";
	$co = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=company&template=company_edit&suid='.$suid;
}
		
		$message = 'Company Saved';
		$icon = 'layout/img/icon_succ.png';
		
		echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";
		


		header("Refresh: 1; URL=".$ret_url);
		
?>