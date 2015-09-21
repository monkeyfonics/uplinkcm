<?php

include 'connds.php';


$query = "
	select		id,
				name,
				ytunnus,
				www,
				post_addr,
  				post_zip,
  				post_city,
  				country,
  				created,
  				modified
	from		uplink_cm.public.company
	order by	id
	
";

$import = pg_query($conn, $query);


/* execute stuff */
while ($import_r = pg_fetch_array($import)) {
	echo "$import_r[name]\n";
	
	$query = "
			insert into ucm.acc_uplin_678.company (
			id,
			name,
			ytunnus,
			bill_addr,
			bill_zip,
			bill_city,
			bill_country,
			email,
			phone,
			www,
			created,
			modified
			) values (
			$import_r[id],
			'$import_r[name]',
			'$import_r[ytunnus]',
			'$import_r[post_addr]',
			'$import_r[post_zip]',
			'$import_r[post_city]',
			'Finland',
			'',
			'',
			'$import_r[www]',
			now(),
			now()
			)
		";
	$importput = pg_query($connput, $query);
	
}



?>