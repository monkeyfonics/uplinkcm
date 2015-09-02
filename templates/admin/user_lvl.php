<?php
include("includes/session.php");


if (!$_GET['usid']) {
	$usid = $s_id;
} else {
	$usid = $_GET['usid'];
}

$query = "
	select		id,
				fname,
				lname,
				lvl
	from		users
	where id = '$usid'
	
";

$ac = pg_query($conn, $query);

$ac_r = pg_fetch_array($ac);

if ($ac_r[lvl] == 1) {
	$style1 = "style='border:1px solid #000;'";
} elseif ($ac_r[lvl] == 2) {
	$style2 = "style='border:1px solid #000;'";
} elseif ($ac_r[lvl] == 5) {
	$style3 = "style='border:1px solid #000;'";
} else {
	$style1 = "";
	$style2 = "";
	$style3 = "";
}

	echo "
	
		<div class='centercont'>
			$ac_r[fname] $ac_r[lname]: $ac_r[lvl]
			
		
			<form action='transaction.php?t=user_lvl' method='post'>
				<input type='hidden' name='usid' value='$ac_r[id]'/>
				<div class='div33'>
					<button name='lvl' class='bigbutton bigstyle' $style1 value='1'>
						<span>Unchecked user</span>
							
					</button>
				</div>
				<div class='div33'>
					<button name='lvl' class='bigbutton bigstyle' $style2 value='2'>
						<span>Accepted user</span>
							
					</button>
				</div>
				<div class='div33'>
					<button name='lvl' class='bigbutton bigstyle' $style3 value='5'>
						<span>Administrator</span>
							
					</button>
				</div>
			</form>
		</div>
	";
	
	

?>