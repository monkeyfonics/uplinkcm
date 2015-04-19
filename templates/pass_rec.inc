<?php
echo "
	<div class='login'>
		<h2>{$lng->__('Account to recover')}</h2>
		<form action='transaction.php?t=pass_rec' method='post'>
	
		<table class='login' align='center'>
			<tr>
				<td>
					{$lng->__('Email')}:
				</td>
				<td>
					<input type='text' name='email'>
				</td>
			</tr>
			
			<tr>
				<td colspan='2' style='text-align: center;'>
					<input class='cbutton bstyle' type='submit' value='{$lng->__('Reset')}'>
				</td>
			</tr>
		</table>
		
		</form>
	
	</div>
	
	";
?>