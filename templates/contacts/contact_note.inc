<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}


$suid = $_GET['suid'];

if ($_GET['notid']) {
$notid = $_GET['notid'];
} else {
	$notid = 0;
}

$query = "
	select		id,
				fname,
				lname,
				bill_addr,
  				bill_zip,
  				bill_city,
  				bill_country,
  				email,
  				phone1,
  				phone2,
  				www
	from		$acco.contacts
	where		id = $suid
	
";

$ul = pg_query($conn, $query);

$ul_r = pg_fetch_array($ul);


	/*contact notes*/
$query = "
	select		id,
				contact_id,
				created,
				cont
	from		$acco.contact_notes
	where		id = $notid
	
";

$con_not = pg_query($conn, $query);

$con_not_r = pg_fetch_array($con_not);
echo "
<form action='transaction.php?t=note_save' method='post' id='notesave'>
	<input type='hidden' name='suid' value='$ul_r[id]'/>
	<input type='hidden' name='acco' value='$acco'/>
	<input type='hidden' name='notid' value='$con_not_r[id]'/>
	
	<div class='buttons'>
		<a href='index.php?section=contacts&template=contact_view&suid=$ul_r[id]'>
			<div class='header'>$ul_r[lname], $ul_r[fname]</div>
		</a>
		
			<button formid='notesave' class='usebutton'>{$lng->__('Save Note')}</button>
		
	</div>
";
echo "
	<div class='fullcont'>
		<table class='grid'>
			<tr>
				<td class='head'>
					{$lng->__('Note')}:
				</td>
			</tr>
			<tr>
				
				<td>
					<textarea name='cont' rows='7' style='width:100%;'>$con_not_r[cont]</textarea>
				</td>
			</tr>
	</div>
	";
?>