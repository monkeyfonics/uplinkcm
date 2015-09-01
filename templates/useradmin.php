<?php
if ($s_lvl < 5) {
	header("Location: $rpath");
    die();
}

$query = "
	select		id,
				login,
				lvl,
				create_date,
				email
	from		users
	where lvl = 1
	order by 	create_date
	
";

$nu = pg_query($conn, $query);

$query = "
	select		id,
				login,
				lvl,
				create_date,
				email
	from		users
	where lvl != 1
	order by 	create_date
	
";

$ou = pg_query($conn, $query);

echo "
	<div>
		<h2 class='header'>New Users</h2>
		<form action='transaction.php?t=userlist' method='post'>
		<input type='hidden' name='nu' value='change'/>
		<table class='list' align='center'>
			<tr>
				<th>
					User:
				</th>
				<th>
					Level:
				</th>
				<th>
					Email:
				</th>
				<th>
					Date:
				</th>
				
			</tr>
			";
		while ($nu_r = pg_fetch_array($nu)) {
			$date = strtotime($nu_r[create_date]);
			
			echo "	
				<input type='hidden' name='usid' value='$nu_r[id]'/>
				<tr>
					
					<td>
						<a href='?template=createdb&usid=$nu_r[id]'>$nu_r[login]</a>
					</td>
					<td>
						<input type='text' name='lvl' value='$nu_r[lvl]'/>
					</td>
					<td>
						<input type='text' name='lvl' value='$nu_r[email]'/>
					</td>
					<td>
						".date('Y-m-d H:i:s', $date)."
					</td>
				</tr>
				
			";
		}
		echo "	
			<tr>
					
				<td colspan='4' style='text-align:center;'>
					<input id='psubmit' type='submit' value='Save'/>
				</td>
			</tr>
		</table>
		
		</form>
		";
		
		echo "
		<h2 class='header'>Users with level</h2>
		<form action='transaction.php?t=userlist' method='post'>
		<input type='hidden' name='ou' value='change'/>
		<table class='list' align='center'>
			<tr>
				<th>
					User:
				</th>
				<th>
					Level:
				</th>
				<th>
					Email:
				</th>
				<th>
					Date:
				</th>
			</tr>
		";
		
		
		
		while ($ou_r = pg_fetch_array($ou)) {
			$date = strtotime($ou_r[create_date]);
			echo "	
				<tr>
					
					<td>
						<a href='?template=createdb&usid=$ou_r[id]'>$ou_r[login]</a>
					</td>
					<td>
						<input type='text' name='lvl' value='$ou_r[lvl]'/>
					</td>
					<td>
						<input type='text' name='lvl' value='$ou_r[email]'/>
					</td>
					<td>
						".date('Y-m-d H:i:s', $date)."
					</td>
				</tr>
			";
		}
		
		
		echo "	
			<tr>
					
				<td colspan='4' style='text-align:center;'>
					<input id='psubmit' type='submit' value='Save'/>
				</td>
			</tr>
		</table>
		
		</form>
	</div>
	";
?>