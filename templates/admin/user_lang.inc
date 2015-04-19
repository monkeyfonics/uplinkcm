<?php
include("includes/session.inc");


if (!$_GET['usid']) {
	$usid = $s_id;
} else {
	$usid = $_GET['usid'];
}

$query = "
	select		id,
				fname,
				lname,
				lang
	from		users
	where id = '$s_id'
	
";

$ac = pg_query($conn, $query);

$ac_r = pg_fetch_array($ac);

if ($ac_r[lang] == "en") {
	$style1 = "style='border:1px solid #000;'";
} elseif ($ac_r[lang] == "sv") {
	$style2 = "style='border:1px solid #000;'";
} elseif ($ac_r[lang] == "fi") {
	$style3 = "style='border:1px solid #000;'";
} else {
	$style1 = "";
	$style2 = "";
	$style3 = "";
}

	echo "
	
		<div class='centercont'>
			$ac_r[fname] $ac_r[lname]: $ac_r[lang]
			
		
			<form action='transaction.php?t=user_lang' method='post'>
				<div class='div33'>
					<button name='lang' class='bigbutton bigstyle' $style1 value='en'>
						<span>English</span>
							
					</button>
				</div>
				<div class='div33'>
					<button name='lang' class='bigbutton bigstyle' $style2 value='sv'>
						<span>Svenska</span>
							
					</button>
				</div>
				<div class='div33'>
					<button name='lang' class='bigbutton bigstyle' $style3 value='fi'>
						<span>Suomi</span>
							
					</button>
				</div>
			</form>
		</div>
	";
	
	

?>