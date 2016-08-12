<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}


$suid = $_GET['suid'];

if ($_GET['notid']) {
	$notid = $_GET['notid'];
	/*contact notes*/
	$query = "
		select		$acco.contact_notes.id as id,
					$acco.contact_notes.contact_id as contact_id,
					$acco.contact_notes.company_id as company_id,
					$acco.contact_notes.created as created,
					$acco.contact_notes.cont as cont,
					$acco.contact_notes.created_by as created_by,
					public.users.login as username,
					public.users.fname as fname,
					public.users.lname as lname
		from		$acco.contact_notes left OUTER JOIN public.users ON ($acco.contact_notes.created_by = public.users.id)
		where		$acco.contact_notes.id = $notid
		
	";
	
	$con_not = pg_query($conn, $query);
	
	$con_not_r = pg_fetch_array($con_not);
} else {
	$notid = 0;
	$query = "
		select		public.users.login as username,
					public.users.fname as fname,
					public.users.lname as lname
		from		public.users 
		where		public.users.id = $s_id
		
	";
	
	$con_not = pg_query($conn, $query);
	
	$con_not_r = pg_fetch_array($con_not);
}

$query = "
	select		id,
				name,
				ytunnus,
				bill_addr,
  				bill_zip,
  				bill_city,
  				bill_country,
  				email,
  				phone,
  				www
	from		$acco.company
	where		id = $suid
	
";

$ul = pg_query($conn, $query);

$ul_r = pg_fetch_array($ul);



echo "
<form action='transaction.php?t=note_save' method='post' id='notesave'>
	<input type='hidden' name='consuid' value=''/>
	<input type='hidden' name='comsuid' value='$ul_r[id]'/>
	<input type='hidden' name='acco' value='$acco'/>
	<input type='hidden' name='notid' value='$con_not_r[id]'/>
	
	<div class='buttons'>
		<a href='index.php?section=contacts&template=contact_view&suid=$ul_r[id]'>
			<div class='header'>$ul_r[name]</div>
		</a>
		
			<button formid='notesave' class='usebutton'>{$lng->__('Save Note')}</button>
		
	</div>
";
echo "
	<div class='fullcont'>
		<table class='grid'>
			<tr>
				<td class='head'>
					{$lng->__('Note')} {$lng->__('by')} $con_not_r[name]:
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