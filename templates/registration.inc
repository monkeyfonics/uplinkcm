<?php

/*create full users list */
$query = "
	select		id,
				login
				
	from		public.users
	
	
";

$full = pg_query($conn, $query);

while ($full_r = pg_fetch_array($full)) {
	$jsuser[] = "$full_r[login]";
	
}


/* script for checking duplicate contacts*/
echo "
	<script type='text/javascript'>
		var list = ".json_encode($jsuser,JSON_HEX_QUOT).";
		var arrayLength = list.length;
		
		function checkLogin()
		{
			document.getElementById('coninfo').innerHTML = '&nbsp;';
			
			var typed = document.getElementById('login').value;
			var loginjs = typed.trim();
			
			for (var i = 0; i < arrayLength; i++) {
				if (list[i] == loginjs) {
						if (loginjs) {
							document.getElementById('coninfo').innerHTML += 'User already exists '+loginjs;
						} else {
							document.getElementById('coninfo').innerHTML = '&nbsp;';
						}

				} else {
					
				}
			    
			}
			
		}
	</script>
	
";

echo "
	<div class='login reg'>
		<div id='coninfo' style='color: red;'>
			&nbsp;
		</div>
		<h2>{$lng->__('Registration')}</h2>
		<form action='transaction.php?t=check_reg' method='post'>
		<input type='hidden' name='op' value='new'/>
		<table align='center'>
			<tr>
				<td>
					Email*:
				</td>
				<td>
					<input type='text' id='login' name='user' onkeyup='checkLogin()'>
				</td>
			</tr>
			<tr>
				<td>
					Password*:
				</td>
				<td>
					<input type='password' name='pass'>
				</td>
			</tr>
			
			<tr>
				<td>
					First Name:
				</td>
				<td>
					<input type='text' name='fname'>
				</td>
			</tr>
			<tr>
				<td>
					Last Name:
				</td>
				<td>
					<input type='text' name='lname'>
				</td>
			</tr>
			<tr>
				<td>
					Telephone:
				</td>
				<td>
					<input type='text' name='tel'>
				</td>
			</tr>
			<tr>
				<td>
					Language:
				</td>
				<td>
					<select name='lang'>
					  <option value='en' selected='selected'>English</option>
					  <option value='sv'>Svenska</option>
					  <option value='fi'>Suomi</option>
					</select>
					";
					
					echo "
				</td>
			</tr>
			
			<tr>
				<td colspan='2' style='text-align: center;'>
					<input type='submit' value='Create'>
				</td>
			</tr>
		</table>
		
		</form>
	</div>
	";
?>