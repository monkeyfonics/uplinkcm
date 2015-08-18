<?php
if ($s_lvl < 5) {
	header("Location: $rpath");
    die();
}

$usid = $_GET['usid'];

$query = "
	select		id,
				login,
				lvl,
				create_date,
				fname,
				lname,
				tel,
				lang,
				last_login
	from		users
	where id = $usid
	
";

$ac = pg_query($conn, $query);

$ac_r = pg_fetch_array($ac);


$query = "
	select		account_user_link.acc_id,
				account_user_link.user_id,
				account_user_link.active,
				accounts.id as id,
				accounts.name as name,
				accounts.vatnr as vatnr
	from		account_user_link LEFT OUTER JOIN accounts ON (account_user_link.acc_id = accounts.id)
	where account_user_link.user_id = $usid
	
";

$us = pg_query($conn, $query);

$joined = strtotime($ac_r[create_date]);
$last = strtotime($ac_r[last_login]);

/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=admin&template=main'>
			<div class='header'>Admin</div>
		</a>
		<a href='index.php?section=admin&template=user_list'>
			<div class='header'>User List</div>
		</a>
		
	</div>
";

echo "
	<div class='centercont'>
		
		<div class='div33'>
			<a href='index.php?section=admin&template=user_edit&usid=$ac_r[id]'>
				<div class='bigbutton bigstyle'>
					<span>Edit User</span><br/>
						Edits User.
				</div>
			</a>
		</div>
		<div class='div33'>
			<a href='?section=admin&template=user_pass&usid=$ac_r[id]'>
				<div class='bigbutton bigstyle'>
					<span>Change Password</span><br/>
					Change the password.
				</div>
			</a>
		</div>
		<div class='div33'>
			<a href='index.php?section=admin&template=send_mail&usid=$ac_r[id]'>
				<div class='bigbutton bigstyle'>
					<span>Send Mail</span><br/>
						Send mail.
				</div>
			</a>
		</div>
		<div class='div33'>
			<a href='?section=admin&template=user_lvl&usid=$ac_r[id]'>
						<div class='bigbutton bigstyle'>
							<span>Change Level</span><br/>
							Change the level of the user.
						</div>
			</a>
		</div>
		
		<div style='clear:both;'>
		<h2 class='header'>$ac_r[fname] $ac_r[lname]</h2>
		<table class='grid'>
			
			<tr>
				<td>
					Id:
				</td>
				<td>
					$ac_r[id]
				</td>
				<td>
					Login:
				</td>
				<td>
					$ac_r[login]
				</td>
			</tr>
			<tr>
				<td>
					First:
				</td>
				<td>
					$ac_r[fname]
				</td>
				<td>
					Tel:
				</td>
				<td>
					$ac_r[tel]
				</td>
				
			</tr>
			<tr>
				<td>
					Last:
				</td>
				<td>
					$ac_r[lname]
				</td>
				<td>
					Lang:
				</td>
				<td>
					$ac_r[lang]
				</td>
			</tr>
			<tr>
				<td>
					Joined:
				</td>
				<td>
					".date('Y-m-d', $joined)."
				</td>
				<td>
					Lvl:
				</td>
				<td>
					$ac_r[lvl]
				</td>
			</tr>
			<tr>
				<td>
					Last login:
				</td>
				<td>
					".date('Y-m-d', $last)."
				</td>
				<td>
				
				</td>
				<td>
				
				</td>
			</tr>
			
		</table>
	";
if ($ac_r[lvl] > 1) {
	

echo "
		<hr/>
		<h3 class='header'>Accounts:</h3>
		
		<table class='list'>
		
	";	
		while ($us_r = pg_fetch_array($us)) {			
			echo "
				<tr>
					<td>
						<a href='index.php?section=admin&template=account_info&acid=$us_r[id]'>$us_r[vatnr]</a>
					</td>
					<td>
						<a href='index.php?section=admin&template=account_info&acid=$us_r[id]'>$us_r[name]</a>
					</td>
					<td>
						<a href='index.php?section=admin&template=account_user&acid=$us_r[id]'>Edit</a>
					</td>
				</tr>
			";
					
		}
	echo "
		</table>
		
";
}	
		
	echo "
		</div>	
	</div>
";

?>