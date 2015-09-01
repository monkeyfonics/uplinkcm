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
				fname,
				lname
	from		users
	where lvl = 1
	order by 	create_date
	
";

$nu = pg_query($conn, $query);

		
/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=admin&template=main'>
			<div class='header'>Admin</div>
		</a>
		<a href='index.php?section=admin&template=user_edit&usid=0'>
			<div>{$lng->__('New User')}</div>
		</a>
		
	</div>
";
		
		echo "
		
		
		<div class='centercont'>
		
		
		
		
		
		";
		/* new users*/
		echo "
		<h2 class='header'>New Users</h2>
		
		
		<table class='list' align='center'>
			<tr>
				
				<th>
					Username:
				</th>
				<th>
					Level:
				</th>
				<th>
					First:
				</th>
				<th>
					Last:
				</th>
				<th>
					Date:
				</th>
				<th>
					Del
				</th>
			</tr>
		";
		
		
		
		while ($nu_r = pg_fetch_array($nu)) {
			$date = strtotime($nu_r[create_date]);
			echo "	
				<tr>
					
					<td>
						<a href='?section=admin&template=user_info&usid=$nu_r[id]'>$nu_r[login]</a>
					</td>
					<td>
						$nu_r[lvl]
					</td>
					<td>
						$nu_r[fname]
					</td>
					<td>
						$nu_r[lname]
					</td>
					<td>
						".date('Y-m-d H:i:s', $date)."
					</td>
					<td>
						<a href='transaction.php?t=del_item&acid=$acd&item=user&usid=$nu_r[id]' onclick='return confirm(\"Really delete?\");'>
    						<span>Del</span>
    					</a>
					</td>
				</tr>
			";
		}
		
		
		echo "	
			
		</table>
		
	</div>
	";
?>