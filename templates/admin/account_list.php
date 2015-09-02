<?php
if ($s_lvl < 5) {
	header("Location: $rpath");
    die();
}
/*include for last update */
require_once('includes/tables.php');
$tablefile = "includes/tables.php";

$query = "
	select		id,
				name,
				vatnr,
				bill_addr,
				created,
				updated,
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
					Updated:
				</th>
				<th>
					Active:
				</th>
				<th>
					DB:
				</th>
			</tr>
		";
		
		
		$filetime = date ("Y-m-d H:i:s", filemtime($tablefile));
		while ($ac_r = pg_fetch_array($ac)) {
			$created = strtotime($ac_r[created]);
			$updated = strtotime($ac_r[updated]);
			
			
			
			if ($filetime > $ac_r[updated]) {
				$nosync = "no sync";
				$syncclass = "red";
			} else {
				$nosync = "synced";
				$syncclass = "green";
			}
			echo "	
				<tr>
					
					<td>
						<a href='?section=admin&template=account_info&acid=$ac_r[id]'>$ac_r[name]</a>
					</td>
					<td>
						$ac_r[vatnr]
					</td>
					
					<td>
						".date('Y-m-d', $created)."
					</td>
					<td>
						".date('Y-m-d', $updated)."
					</td>
					<td>
						$ac_r[active]
					</td>
					<td class='$syncclass'>
						$nosync
					</td>
				</tr>
			";
		}
		
		
		echo "	
			
		</table>
		
		
	</div>
	";
?>