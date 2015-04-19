<?php
if ($s_lvl < 5) {
	header("Location: $rpath");
    die();
}



$query = "
	select		id,
				name,
				vatnr,
				bill_addr,
				created,
				active
	from		accounts
	
	order by 	created
	
";

$ac = pg_query($conn, $query);

/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=admin&template=main'>
			<div class='header'>Admin</div>
		</a>
		<a href='index.php?section=admin&template=account_edit&acid=0'>
			<div>{$lng->__('New Account')}</div>
		</a>
		
	</div>
";
		
		echo "
		<div class='centercont'>
		
		<h2 class='header'>Accounts</h2>
		
		
		
		<table class='list large' align='center'>
			<tr>
				<th>
					Name:
				</th>
				<th>
					Vat Nr.:
				</th>
				
				<th>
					Created:
				</th>
				<th>
					Active:
				</th>
			</tr>
		";
		
		
		
		while ($ac_r = pg_fetch_array($ac)) {
			$date = strtotime($ac_r[created]);
			echo "	
				<tr>
					
					<td>
						<a href='?section=admin&template=account_info&acid=$ac_r[id]'>$ac_r[name]</a>
					</td>
					<td>
						$ac_r[vatnr]
					</td>
					
					<td>
						".date('Y-m-d H:i:s', $date)."
					</td>
					<td>
						$ac_r[active]
					</td>
				</tr>
			";
		}
		
		
		echo "	
			
		</table>
		
		
	</div>
	";
?>