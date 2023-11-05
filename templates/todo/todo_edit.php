<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r['identy'];

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
				due,
  				cont,
  				completed
	from		$acco.todo
	where		id = $tid
	
";

$todo = pg_query($conn, $query);

$todo_r = pg_fetch_array($todo);

if (!$todo_r['id']) {
	$created = date('Y-m-d');
	$due = date('Y-m-d');
	$head = "New Todo";
} else {
	$created = date('Y-m-d', strtotime($todo_r['created']));
	$due = date('Y-m-d', strtotime($todo_r['due']));
	$head = substr($todo_r['cont'], 0, 10);
}


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
	order by lname asc, fname asc
	
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
	order by	name
";

$cl = pg_query($conn, $query);

if ($suid != 0) {
/* only companies for that contact*/
$query = "
		select		{$acco}.company.id as id,
					{$acco}.company.name as name,
					{$acco}.company.ytunnus as ytunnus		
		from		$acco.company LEFT JOIN $acco.link_company_contact
		ON			($acco.company.id = $acco.link_company_contact.company_id)
		where		{$acco}.link_company_contact.contact_id = $suid
		order by	name
";
$speccomp = pg_query($conn, $query);
} else {
	
}
if ($comid != 0) {
/* only contacts for that company*/
$query = "
		select		{$acco}.contacts.id as id,
					{$acco}.contacts.fname as fname,
					{$acco}.contacts.lname as lname,
					{$acco}.contacts.loc as loc,
					{$acco}.link_company_contact.prim as prim
		from		$acco.contacts LEFT JOIN $acco.link_company_contact
		ON			($acco.contacts.id = $acco.link_company_contact.contact_id)
		where		{$acco}.link_company_contact.company_id = $comid
		order by	lname, fname	
";
$speccont = pg_query($conn, $query);
} else {
	
}

echo "
	<form action='transaction.php?t=todo_edit' method='post' id='todosave'>
	<input type='hidden' name='tid' value='$todo_r[id]'/>
	<input type='hidden' name='acco' value='$acco'/>
	<input type='hidden' name='created' value='$created'/>
	
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



echo "
	<div class='fullcont'>
		
		
		<table class='grid'>
			<tr>
				<td class='head'>
					{$lng->__('Contact')}:
				</td>
				<td>
					<select name='pid'>
						<option value='0'>
							{$lng->__('None')}
						</option>
					";
						while ($speccont_r = pg_fetch_array($speccont)) {
							if ($speccont_r['prim'] == true) {
								$sel = " selected='selected'";
								$conlang = $speccont_r['loc'];
							} else {
								$sel = " ";
							}
							//if ($speccomp_r[id] == $in_r[cid]) $sel=" selected='selected'"; else $sel="";
							echo "
								<option value='$speccont_r[id]' $sel>
									$speccont_r[lname], $speccont_r[fname]
								</option>
							";
						}
					echo "
						<option value='0' disabled>
							----
						</option>
						";
						
						while ($ul_r = pg_fetch_array($ul)) {
							if ($ul_r['id'] == $todo_r['contact_id'] or $ul_r['id'] == $suid) $sel=" selected='selected'"; else $sel="";
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
					{$lng->__('Created')}:
				</td>
				<td>
					<input type='text' name='created' value='$created'></input>
				</td>
				
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Company')}:
				</td>
				<td>
					<select name='cid'>
						<option value='0'>
							{$lng->__('None')}
						</option>
						";
						
						while ($speccomp_r = pg_fetch_array($speccomp)) {
							//if ($speccomp_r[id] == $in_r[cid]) $sel=" selected='selected'"; else $sel="";
							echo "
								<option value='$speccomp_r[id]'>
									$speccomp_r[name]
								</option>
							";
						}
					echo "
						<option value='0' disabled>
							----
						</option>
						";
						while ($cl_r = pg_fetch_array($cl)) {
							if ($cl_r['id'] == $todo_r['company_id'] or $cl_r['id'] == $comid) $sel=" selected='selected'"; else $sel="";
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
					{$lng->__('Due date')}:
				</td>
				<td>
					<input type='text' name='due' value='$due'></input>
				</td>
				
			</tr>
			
			<tr>
				<td class='head' colspan='4'>
					{$lng->__('Content')}:
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