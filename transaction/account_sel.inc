<?php

$acc = $_POST['accoun'];


$_SESSION['ac_id'] = $acc;

$query = "
	select		accounts.id as aid,
				accounts.name,
				accounts.vatnr as vatnr
	FROM 		accounts 
	where 		id = $acc
	
	
	
";

$co = pg_query($conn, $query);
$co_r = pg_fetch_array($co);

$message = 'Selected account ';
$ret_url = 'index.php?template=main';

header("Refresh: 2; URL=".$ret_url);

echo "
	<div class='messagebox'>
		<img class='messageicon' src='layout/img/icon_succ.png' alt='$message'>
		<p class='messagetext'>$message<br/> $co_r[name]</p>
	</div>
	";

?>