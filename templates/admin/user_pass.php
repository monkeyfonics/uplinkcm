<?php
include("includes/session.php");

if (!$_GET['usid']) {
	$usid = $s_id;
} else {
	$usid = $_GET['usid'];
}

$query = "
	select		id,
				login,
				pass
	from		users
	where		id = '$usid'
";

$ch = pg_query($conn, $query);

$ch_r = pg_fetch_array($ch);

echo "
	<div class='login'>
		<h2>{$lng->__('Choose new password')}</h2>
		<form action='transaction.php?t=check_reg' method='post'>
		<input type='hidden' name='op' value='change'/>
		<input type='hidden' name='usid' value='$usid'/>
		<table align='center'>
			<tr>
				<td>
					{$lng->__('User')}:
				</td>
				<td>
					$ch_r[login]
				</td>
			</tr>
			<tr>
				<td>
					{$lng->__('New password')}:
				</td>
				<td>
					<input id='pass1' type='password' name='pass'/>
				</td>
			</tr>
			<tr>
				<td>
					{$lng->__('Retype password')}:
				</td>
				<td>
					<input id='pass2' type='password' name='pass2' onkeyup='checkpass()'/>
				</td>
			</tr>
			
			<tr>
				<td colspan='2' style='text-align: center;'>
					<input id='psubmit' type='submit' value='Save' disabled/>
				</td>
			</tr>
		</table>
		
		</form>
	</div>
	";
?>