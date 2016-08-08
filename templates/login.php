<?php
echo "
	<div class='login'>
		<h2>{$lng->__('Login')}</h2>
		<form action='transaction.php?t=check_login' method='post'>
	
		<table class='login' align='center'>
			<tr>
				<td>
					{$lng->__('Email')}:
				</td>
				<td>
					<input type='email' name='user' autocapitalize='none' autofocus>
				</td>
			</tr>
			<tr>
				<td>
					{$lng->__('Password')}:
				</td>
				<td>
					<input type='password' name='pass'>
				</td>
			</tr>
			<tr>
				<td colspan='2' style='text-align: center;'>
					<input class='cbutton bstyle' type='submit' value='Login'>
				</td>
			</tr>
		</table>
		
		</form>
		
		<a href='file.php?template=pass_rec'>
			<div class='pass_rec'>
				{$lng->__('Forgot Password')}
			</div>
		</a>
	</div>
	
	<div class='login'>
		<h3>{$lng->__('Register a new user')}.</h3>
		<a href='file.php?template=registration'><div class='cbutton bstyle'>{$lng->__('Register')}</div></a>
	</div>
	";
?>