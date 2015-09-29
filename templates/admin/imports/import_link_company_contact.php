<?php

include 'connds.php';


$query = "
	select		person_id,
				company_id,
				prim
  				
	from		uplink_cm.public.link_person_company
	order by	person_id
	
";

$import = pg_query($conn, $query);


/* execute stuff */
while ($import_r = pg_fetch_array($import)) {
	echo "$import_r[person_id]\n";
	
	if ($import_r['prim'] == 't') {
		$bool = 1;
	} else {
		$bool = 0;
	}
	
	$query = "
			insert into ucm.acc_uplin_678.link_company_contact (
			contact_id,
			company_id,
			prim
			) values (
			$import_r[person_id],
			'$import_r[company_id]',
			'$bool'
			)
		";
	$importput = pg_query($connput, $query);
	
}



?>