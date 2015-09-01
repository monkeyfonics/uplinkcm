<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

if ($_GET['tid']) {
	$tid = $_GET['tid'];
	
} else {
	$tid = 0;
}
if ($_GET['suid']) {
	$suid = $_GET['suid'];
	
} else {
	$suid = 0;
}
if ($_GET['comid']) {
	$comid = $_GET['comid'];
	
} else {
	$comid = 0;
}

/*todo*/
$query = "
	select		id,
				contact_id,
				company_id,
				created,
  				cont,
  				completed
	from		$acco.todo
	where		id = $tid
	
";

$todo = pg_query($conn, $query);

$todo_r = pg_fetch_array($todo);


/*contacts */
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
  				www,
  				loc
	from		$acco.contacts
	
";

$ul = pg_query($conn, $query);



/*companies*/
$query = "
	select		id,
				name,
				ytunnus,
				www,
  				bill_addr,
  				bill_zip,
  				bill_city,
  				bill_country,
  				email,
  				phone
	from		$acco.company

";

$cl = pg_query($conn, $query);

if ($tid = 0) {
	$head = "New Todo";
} else {
	$head = substr($todo_r[cont], 0, 10);
}

echo "
	<form action='transaction.php?t=todo_edit' method='post' id='todosave'>
	<input type='hidden' name='tid' value='$tid'/>
	<input type='hidden' name='acco' value='$acco'/>
	
";

/*use buttons row */
echo "


	<div class='buttons'>
		<a href='index.php?section=todo&template=todo_view&tid=$tid'>
			<div class='header'>{$lng->__('Todo')} - $head</div>
		</a>
		
		<button formid='todosave' class='usebutton'>{$lng->__('Save Todo')}</button>
		
	</div>
";
if ($todo_r[created]) {
	$date = strtotime($todo_r[created]);
} else {
	$date = date("Y-m-d");
}


echo "
	<div class='fullcont'>
		
		
		<table class='grid'>
			<tr>
				<td class='head'>
					Contact:
				</td>
				<td>
					<select name='pid'>
						<option value='0'>
							None
						</option>
						";
						while ($ul_r = pg_fetch_array($ul)) {
							if ($ul_r[id] == $todo_r[contact_id] or $ul_r[id] == $suid) $sel=" selected='selected'"; else $sel="";
							echo "
								<option value='$ul_r[id]' $sel>
									$ul_r[lname], $ul_r[fname]
								</option>
							";
						}
					echo "
					</select>
				</td>
				<td class='head'>
					Created:
				</td>
				<td>
					$date
				</td>
				
			</tr>
			<tr>
				<td class='head'>
					Company:
				</td>
				<td>
					<select name='cid'>
						<option value='0'>
							None
						</option>
						";
						while ($cl_r = pg_fetch_array($cl)) {
							if ($cl_r[id] == $todo_r[company_id] or $cl_r[id] == $comid) $sel=" selected='selected'"; else $sel="";
							echo "
								<option value='$cl_r[id]' $sel>
									$cl_r[name]
								</option>
							";
						}
					echo "
					</select>
				</td>
				<td class='head'>
					Completed:
				</td>
				<td>
					$todo_r[completed]
				</td>
			</tr>
			<tr>
				<td class='head' colspan='4'>
					Content:
				</td>
				
			</tr>
			<tr>
				<td colspan='4'>
					<textarea name='cont' style='width:100%;' rows='4'>$todo_r[cont]</textarea>
				</td>
				
			</tr>
			
		
		
		</table>
		</form>
		";
echo "
		
	</div>
	";
?>