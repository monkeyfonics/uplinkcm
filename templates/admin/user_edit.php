<?php

if ($_GET['own']) {
	$usid = $s_id;
} else {
	if ($s_lvl < 5) {
		header("Location: $rpath");
	    die();
	}
	$usid = $_GET['usid'];
}


$query = "
	select		id,
				login,
				fname,
				lname,
				lvl,
				tel,
				lang,
				last_login
	from		users
	where id = '$usid'
	
";

$ac = pg_query($conn, $query);

$ac_r = pg_fetch_array($ac);

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
		<form action='transaction.php?t=user_edit' method='post'>
		
		";
		if ($usid) {
			echo "
			<h2>Edit $ac_r[login]</h2>
			<input type='hidden' name='no' value='old'/>
			<input type='hidden' name='usid' value='$usid'/>
			
			";
		} else {
			echo "
			<h2>Create Account</h2>
			<input type='hidden' name='no' value='new'/>
			";
		}
		echo "
		<table class='list'>
			<tr>
				<td>
					Id:
				</td>
				<td>
					$ac_r[id]
				</td>
				
			</tr>
			<tr>
				<td class='first'>
					First Name:
				</td>
				<td>
					<input type='text' name='fname' value='$ac_r[fname]'/>
				</td>
			</tr>
			<tr>
				<td>
					Last Name:
				</td>
				<td>
					<input type='text' name='lname' value='$ac_r[lname]'/>
				</td>
			</tr>
			<tr>
				<td>
					Tel:
				</td>
				<td>
					<input type='text' name='tel' value='$ac_r[tel]'/>
				</td>
			</tr>
			
	";

echo "
			
			<tr>
				<td colspan='2' style='text-align: center;'>
					<input type='submit' value='Save'>
				</td>
			</tr>
		</table>
		</form>
	</div>
";

?>