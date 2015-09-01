<?php
if ($s_lvl < 5) {
	header("Location: $rpath");
    die();
}

$acd = $_GET['acid'];

$query = "
	select		id,
				name,
				vatnr,
				bill_addr,
				bill_zip,
				bill_city,
				bill_country,
				created,
				active,
				identy,
				bill_email
	from		accounts
	where id = $acd
	
";

$ac = pg_query($conn, $query);

$ac_r = pg_fetch_array($ac);

/* already added users */
$query = "
	select		account_user_link.acc_id,
				account_user_link.user_id as user_id,
				account_user_link.active as active,
				users.login as login,
				users.fname as fname,
				users.lname as lname
	from		account_user_link LEFT OUTER JOIN users ON (account_user_link.user_id = users.id)
	where acc_id = $acd
	
";

$ua = pg_query($conn, $query);

/* user dropdown */

$query = "
	select		id,
				login,
				fname,
				lname
				
	from		users
	where		id not in (
		select		account_user_link.user_id
		from		account_user_link
		where acc_id = $acd
	)
";

$us = pg_query($conn, $query);

/*use buttons row */
echo "
	<div class='buttons'>
		
		<a href='index.php?section=admin&template=account_info&acid=$acd'>
			<div class='header'>Account info</div>
		</a>
		
	</div>
";
	echo "
		<div class='centercont'>
			
			
			<h3 class='header'>$ac_r[name]</h3>
			<table class='list large'>
				<tr>
					<th>
						Login:
					</th>
					<th>
						Name:
					</th>
					<th>
						Active:
					</th>
					<th>
						
					</th>
					<th>
						
					</th>
				</tr>
				
	";
	
	
	
	while ($ua_r = pg_fetch_array($ua)) {
		
		if ($ua_r[active]==t) {
			$checked = "Yes";
			$val = "f";
			$buttontext = "Deactivate";
		} else {
			$checked = "No";
			$val = "t";
			$buttontext = "Activate";
		}
				echo "
				<form action='transaction.php?t=account_user' method='post' id='$ua_r[user_id]'>
				<input type='hidden' name='account' value='$ac_r[id]'/>
				<input type='hidden' name='type1' value='upd'/>
				<input type='hidden' name='act' value='$val'/>
				
				
					<tr>
						<td>
							$ua_r[login]
						</td>
						<td>
							$ua_r[lname], $ua_r[fname]
						</td>
						<td>
							$checked
							
						</td>
						<td>
							<button name='deac' type='submit' form='$ua_r[user_id]' value='$ua_r[user_id]'>$buttontext</button>
						</td>
						<td>
							<button name='del' type='submit' form='$ua_r[user_id]' value='$ua_r[user_id]'>Del</button>
						</td>
					</tr>
				</form>
				";
						
	}
	
	echo "
	
			
		</table>
		<br/>
		<form action='transaction.php?t=account_user' method='post'>
			<input type='hidden' name='account' value='$ac_r[id]'/>
			<input type='hidden' name='type2' value='add'/>
		<table>
			<tr>
				
				<td>
					<select name='user'>
						<option>
							Select User
						</option>
	";
	
	/*add new */
	while ($us_r = pg_fetch_array($us)) {			
				echo "
					<option value='$us_r[id]'>
							$us_r[login]
					</option>
				";
						
	}
	
	echo "
					</select>
				
						</td>
						<td>
							<button name='submit' type='submit'>Add</button>
						</td>
					<tr/>
			</table>
			</form>
		</div>
	";
?>