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
	where lvl != 1
	order by 	create_date
	
";

$ou = pg_query($conn, $query);

		
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
		
		<h2 class='header'>Accepted users</h2>
		
		
		<table class='list'>
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
					Acc:
				</th>
			</tr>
		";
		
		
		
		while ($ou_r = pg_fetch_array($ou)) {
			$countnr = 0;
			$query = "
				select		acc_id,
							user_id,
							active
				from		account_user_link
				where user_id = $ou_r[id]
				
			";

			$aul = pg_query($conn, $query);
			while ($aul_r = pg_fetch_array($aul)) {
				$countnr += 1;
			}
			$date = strtotime($ou_r[create_date]);
			echo "	
				<tr>
					
					<td>
						<a href='?section=admin&template=user_info&usid=$ou_r[id]'>$ou_r[login]</a>
					</td>
					<td>
						$ou_r[lvl]
					</td>
					<td>
						$ou_r[fname]
					</td>
					<td>
						$ou_r[lname]
					</td>
					<td>
						".date('Y-m-d H:i:s', $date)."
					</td>
					<td>
						$countnr
					</td>
				</tr>
			";
		}
		
		
		echo "	
			
		</table>
		
		
	</div>
	";
?>