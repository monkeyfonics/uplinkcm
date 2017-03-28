<?php
$user = $_SESSION['s_id'];

$query = "
	select		id,
				fname,
				lname,
				login,
				tel
	from		users
	where id = $user
	
";

$us = pg_query($conn, $query);

$us_r = pg_fetch_array($us);

echo "
	<div class='centercont'>
		<h2 class='header'>{$lng->__('Request an account')}</h2>
	
		<form action='transaction.php?t=request_acc' method='post'>
			<input type='hidden' name='user' value='$user'/>
			
			
			<label for='aname' class='formheader'>{$lng->__('Name of account')}:</label>
			<input id='aname' name='aname' type='text'></input><br/>
			Name of Company/Organisation<br/>
			<label for='vat' class='formheader'>{$lng->__('VAT-nr')}</label>
			<input id='vat' name='vat' type='text'></input><br/>
			VAT-nr of company<br/>
			<label for='contact' class='formheader'>{$lng->__('I would like to be contacted by:')}</label>
			<input type='radio' name='contact' value='email' checked='checked'>Email
			<input type='radio' name='contact' value='phone'>Phone
			<br>
			Select which way to be contacted, email: $us_r[login] or phone: $us_r[tel]<br/>
			
			<input type='submit' value='Send'></input>
		</form>		
	</div>
			
	";
	
	
		
	
?>