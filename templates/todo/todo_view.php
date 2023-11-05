<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r['identy'];

$tid = $_GET['tid'];

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


/*contacts */
$query = "
	select		$acco.todo.contact_id,
				$acco.contacts.id as coid,
				$acco.contacts.lname as lname,
				$acco.contacts.fname as fname
	FROM 		$acco.contacts LEFT OUTER JOIN $acco.todo ON ($acco.contacts.id = $acco.todo.contact_id)
	where		$acco.contacts.id = $todo_r[contact_id]
	
";

$ul = pg_query($conn, $query);
$ul_r = pg_fetch_array($ul);

if ($tid) {
	
	$todo_r['id'] = $tid;
} else {
	$wher = '';
}

//*company */
$query = "
	select		$acco.todo.company_id,
				$acco.company.id as cmid,
				$acco.company.name as name
	FROM 		$acco.company LEFT OUTER JOIN $acco.todo ON ($acco.company.id = $acco.todo.company_id)
	where		$acco.company.id = $todo_r[company_id]
	
";

$co = pg_query($conn, $query);
$co_r = pg_fetch_array($co);

if ($todo_r['completed'] == true) {
	$completed = "{$lng->__('Not Completed')}";
} else {
	$completed = "{$lng->__('Mark Completed')}";
}

$head = substr($todo_r['cont'], 0, 10);


/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=todo&template=todo_view&tid=$tid'>
			<div class='header'>{$lng->__('Todo')} - $head</div>
		</a>
		<a href='index.php?section=todo&template=todo_edit&tid=$tid'>
			<div>{$lng->__('Edit Todo')}</div>
		</a>
		<a href='transaction.php?t=todo_comp&tid=$todo_r[id]&acco=$acco'>
			<div>$completed</div>
		</a>
	</div>
";

$date = date('Y-m-d', strtotime($todo_r['created']));
$due = date('Y-m-d', strtotime($todo_r['due']));

if (!$todo_r['contact_id']) {
	$person = "{$lng->__('None')}";
} else {
	$person = "$ul_r[lname], $ul_r[fname]";
}

if (!$todo_r['company_id']) {
	$company = "{$lng->__('None')}";
} else {
	$company = "$co_r[name]";
}

if ($todo_r['completed'] == true) {
	$comp = "{$lng->__('Yes')}";
	$class = "green";
} else {
	$comp = "{$lng->__('No')}";
	$class = "red";
}
echo "
	<div class='fullcont'>
		
		
		<table class='grid'>
			<tr>
				<td class='head'>
					{$lng->__('Contact')}:
				</td>
				<td>
					$person
				</td>
				<td class='head'>
					{$lng->__('Created')}:
				</td>
				<td>
					$date
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Company')}:
				</td>
				<td>
					$company
				</td>
				<td class='head'>
					{$lng->__('Due date')}:
				</td>
				<td>
					$due
				</td>
			</tr>
			<tr>
				<td class='head'>
					{$lng->__('Completed')}:
				</td>
				<td class='$class'>
					$comp
				</td>
				<td class='head'>
					
				</td>
				<td>
					
				</td>
			</tr>
			
		</table>
		<div class='todocontent'>
			<h3>{$lng->__('Content')}:</h3>
			<p>
				$todo_r[cont]
			</p>
		</div>
		
		";
echo "
		
	</div>
	";
?>