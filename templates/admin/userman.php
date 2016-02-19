<?php
include("includes/session.php");

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
							<span>{$lng->__('Change')} {$lng->__('user')}</span><br/>
							{$lng->__('Change user details')}.
						</div>
			</a>
		</div>
		<div class='div33'>
			<a href='?section=admin&template=user_pass'>
						<div class='bigbutton bigstyle'>
							<span>{$lng->__('Change')} {$lng->__('password')}</span><br/>
							{$lng->__('Change the password')}.
						</div>
			</a>
		</div>
		<div class='div33'>
			<a href='?section=admin&template=user_lang'>
						<div class='bigbutton bigstyle'>
							<span>{$lng->__('Change')} {$lng->__('language')}</span><br/>
							{$lng->__('Change the language for this user')}.
						</div>
			</a>
		</div>
		<table class='grid' align='center' style='clear:both;'>
			<tr>
				<td>
					{$lng->__('Username')}:
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
					{$lng->__('First name')}:
				</td>
				<td>
					$ch_r[fname]
				</td>
			</tr>
			<tr>
				<td>
					{$lng->__('Last name')}:
				</td>
				<td>
					$ch_r[lname]
				</td>
			</tr>
			<tr>
				<td>
					{$lng->__('Language')}:
				</td>
				<td>
					$ch_r[lang]
				</td>
			</tr>
			
			
		</table>
		
		
	</div>
	";
?>