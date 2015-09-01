<?php

include 'connds.php';


$query = "
	select		id,
				dati,
				pid,
				body,
				user_id,
  				mod
  				
	from		uplink_cm.public.notes
	order by	id
	
";

$import = pg_query($conn, $query);


/* execute stuff */
while ($import_r = pg_fetch_array($import)) {
	echo "$import_r[pid]\n";
	
	$query = "
			insert into cm_dev.acc_uplin_2.contact_notes (
			id,
			contact_id,
			created,
			cont,
			created_by
			) values (
			$import_r[id],
			'$import_r[pid]',
			'$import_r[dati]',
			'$import_r[body]',
			'$import_r[user_id]'
			)
		";
	$importput = pg_query($connput, $query);
	
}



?>