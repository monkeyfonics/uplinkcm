<?php
/* user id */

if ($_SESSION['s_id']) {
	$s_id = $_SESSION['s_id'];
	
} else {
	$s_id = 0;
	$_GET[section] = "";
	$_GET[template] = "login";
}

/* account id */

if ($_SESSION['ac_id']) {
	$ac_id = $_SESSION['ac_id'];
	
} else {
	$ac_id = 0;
}

/* query user info */
$query = "
	select		id,
				login,
				pass,
				lvl,
				fname,
				lname,
				tel,
				lang
	from		public.users
	where		id = $s_id
";

$tu = pg_query($conn, $query);

$tu_r = pg_fetch_array($tu);


?>