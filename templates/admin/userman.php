<?php
include("includes/session.inc");

$query = "
	select		id,
				login,
				pass,
				fname,
				lname,
				lang,
				lvl
	from		users
	where		id = '$s_id'
";

$ch = pg_query($conn, $query);

$ch_r = pg_fetch_array($ch);

echo "
	<div class='centercont'>
		<div class='div33'>
			<a href='?section=admin&template=user_edit&own=1'>
						<div class='bigbutton bigstyle'>
							<span>Edit User</span><br/>
							Change user details.
						</div>
			</a>
		</div>
		<div class='div33'>
			<a href='?section=admin&template=user_pass'>
						<div class='bigbutton bigstyle'>
							<span>Change Password</span><br/>
							Change the password.
						</div>
			</a>
		</div>
		<div class='div33'>
			<a href='?section=admin&template=user_lang'>
						<div class='bigbutton bigstyle'>
							<span>Change Language</span><br/>
							Change the language.
						</div>
			</a>
		</div>
		<table class='grid' align='center' style='clear:both;'>
			<tr>
				<td>
					Username:
				</td>
				<td>
					$ch_r[login]
				</td>
			</tr>
			<tr>
				<td>
					Id:
				</td>
				<td>
					$ch_r[id]
				</td>
			</tr>
			<tr>
				<td>
					First:
				</td>
				<td>
					$ch_r[fname]
				</td>
			</tr>
			<tr>
				<td>
					Last:
				</td>
				<td>
					$ch_r[lname]
				</td>
			</tr>
			<tr>
				<td>
					Language:
				</td>
				<td>
					$ch_r[lang]
				</td>
			</tr>
			
			
		</table>
		
		
	</div>
	";
?>