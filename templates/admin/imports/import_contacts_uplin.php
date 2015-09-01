<?php

include 'connds.php';


$query = "
	select		id,
				fname,
				lname,
				post_addr,
  				post_zip,
  				post_city,
  				country,
  				email,
  				mobile,
  				phone,
  				www
	from		uplink_cm.public.person
	order by	id
	
";

$import = pg_query($conn, $query);


/* execute stuff */
while ($import_r = pg_fetch_array($import)) {
	echo "$import_r[lname], $import_r[fname]\n";
	
	$query = "
			insert into cm_dev.acc_uplin_2.contacts (
			id,
			fname,
			lname,
			bill_addr,
			bill_zip,
			bill_city,
			bill_country,
			email,
			phone1,
			phone2,
			www,
			created,
			modified
			) values (
			$import_r[id],
			'$import_r[fname]',
			'$import_r[lname]',
			'$import_r[post_addr]',
			'$import_r[post_zip]',
			'$import_r[post_city]',
			'Finland',
			'$import_r[email]',
			'$import_r[mobile]',
			'$import_r[phone]',
			'$import_r[www]',
			now(),
			now()
			)
		";
	$importput = pg_query($connput, $query);
	
}



?>