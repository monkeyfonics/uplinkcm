<?php
if ($s_lvl < 5) {
	header("Location: $rpath");
    die();
}

$acd = $_GET['acid'];

$query = "
	select		id,
				name,
				vatnr,
				bill_addr,
				bill_zip,
				bill_city,
				bill_country,
				created,
				active,
				identy,
				bill_email,
				bank_account
	from		accounts
	where id = $acd
	
";

$ac = pg_query($conn, $query);

$ac_r = pg_fetch_array($ac);

if ($ac_r[active]==t) {
	$checked=" checked";
} else {
	$checked="";
}

/*use buttons row */
echo "
	<div class='buttons'>
		
		<a href='index.php?section=admin&template=account_list'>
			<div class='header'>Accounts</div>
		</a>
		<a href='index.php?section=admin&template=account_info&acid=$ac_r[id]'>
			<div class='header'>Account Info $ac_r[name]</div>
		</a>
	</div>
";
$logopath = 'acc_img/'.$ac_r[identy].'/logo.png';
echo "
	<div class='centercont'>
		<h2 class='header'>Upload Logo</h2>
		<div class='imgshow'>
		";
		if (file_exists($logopath)) {
			echo "
			<img src='$logopath' style='height: 150px;'/>
			";
		} else {
			echo "
			
			No image yet
			";
		}
		echo "
		</div>
		<form action='transaction.php?t=logoupload' enctype='multipart/form-data' method='post'>
		<input type='hidden' name='acid' value='$acd'/>
		<input type='hidden' name='acco' value='$ac_r[identy]'/>
		
		<input type='file' name='image_file' id='image_file'/>
		<input type='submit' value='Upload' />
		";
			

echo "
			
			
		</form>
	</div>
";

?>