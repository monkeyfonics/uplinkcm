<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];


/* nr of rows to diplay */
$rows = 15;

if (!$_GET['startrow']) {
	$startrow = 0;
} else {
	$startrow = $_GET['startrow'];
}

$query = "
	select		id as tid,
				contact_id,
				company_id,
				created,
				due,
  				cont,
  				completed
	from		$acco.todo
	order by	completed, created desc
	
";

$todo = pg_query($conn, $query);

/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=todo&template=todo_list'>
			<div class='header'>{$lng->__('Todo List')}</div>
		</a>
		<a href='index.php?section=todo&template=todo_edit&tid=0'>
			<div>{$lng->__('New Todo Item')}</div>
		</a>
	</div>
";


echo "
	

	<div class='fullcont'>
		
		
		<table class='list large'>
			<tr>
				<th class='first'>
					Dated:
				</th>
				<th>
					Due Date:
				</th>
				<th>
					Contact - Company:
				</th>
				<th>
					Content:
				</th>
				<th>
					Completed:
				</th>
			</tr>
			";
		while ($todo_r = pg_fetch_array($todo)) {
			if ($todo_r[completed] == f) {
				$bolde = 'font-weight: bold;';
				$tf = "No";
				$comp= "red";
			} else {
				$bolde = '';
				$tf = "Yes";
				$comp= "green";
			}
				
			
			/* contacts */
			$query = "
				select		$acco.todo.id,
							$acco.todo.contact_id as cid,
							$acco.todo.company_id as comid,
							$acco.contacts.fname as fname,
							$acco.contacts.lname as lname
				from		$acco.todo LEFT OUTER JOIN $acco.contacts ON ($acco.todo.contact_id = $acco.contacts.id)
				where		$acco.contacts.id = $todo_r[contact_id]
			";
			$con = pg_query($conn, $query);
			$con_r = pg_fetch_array($con);
			/* companies */
			$query = "
				select		$acco.todo.id,
							$acco.todo.contact_id as cid,
							$acco.todo.company_id as comid,
							$acco.company.name as name
				from		$acco.todo LEFT OUTER JOIN $acco.company ON ($acco.todo.company_id = $acco.company.id)
				where		$acco.company.id = $todo_r[company_id]
			";
			$com = pg_query($conn, $query);
			$com_r = pg_fetch_array($com);
			
			
			$date = date('Y-m-d',strtotime($todo_r[created]));
			$due = date('Y-m-d',strtotime($todo_r[due]));
			
			$pripath = "index.php?section=todo&template=todo_view&tid=$todo_r[tid]";
			
			if ($todo_r[contact_id] and $todo_r[company_id] == 0) {
				$target = "$con_r[lname], $con_r[fname]";
			} elseif ($todo_r[contact_id] == 0 and $todo_r[company_id]) {
				$target = "$com_r[name]";
			} elseif ($todo_r[contact_id] and $todo_r[company_id]) {
				$target = "$con_r[lname], $con_r[fname] - $com_r[name]";
			} else {
				$target = "None";
			}
			
			
			
			echo "	
			
				<tr>
					
					<td class='first' style='$bolde'>
						<a href='$pripath'>
							$date
						</a>
					</td>
					<td style='$bolde'>
						<a href='$pripath'>
							$due
						</a>
					</td>
					<td style='$bolde'>
						<a href='$pripath'>
							$target
						</a>
					</td>
					<td style='overflow: hidden; $bolde' >
						<a href='$pripath'>";
							echo substr($todo_r[cont], 0, 30);
							echo " 
						</a>
					</td>
					<td class='$comp'>
						$tf
					</td>
				</tr>
			";
		
			
		}
		
		
		
		echo "	
		</table>
		
		";
?>
