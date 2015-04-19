<?php
$query = "
	select		id,
				name,
				identy,
				active
	from		accounts
	where 		id = $ac_id
	
";

$ac = pg_query($conn, $query);

?>