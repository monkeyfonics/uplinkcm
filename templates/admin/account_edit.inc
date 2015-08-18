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
				bill_email,
				bank_account,
				bic,
				bill_tel
	from		accounts
	where id = $acd
	
";

$ac = pg_query($conn, $query);

$ac_r = pg_fetch_array($ac);

if ($ac_r[active]==t) {
	$checked=" checked";
} else {
	$checked="";
}

/*use buttons row */
echo "
	<form action='transaction.php?t=account_edit' method='post' id='accsave'>
	<div class='buttons'>
		
		<a href='index.php?section=admin&template=account_list'>
			<div class='header'>Accounts</div>
		</a>
		
		<button formid='accsave' class='usebutton'>{$lng->__('Save account')}</button>
		
	</div>
";

echo "
	<div class='centercont'>
		
		<input type='hidden' name='acti' value='false'/>
		";
		if ($acd) {
			echo "
			<h2 class='header'>Edit $ac_r[name]</h2>
			<input type='hidden' name='no' value='old'/>
			<input type='hidden' name='acid' value='$acd'/>
			
			";
		} else {
			echo "
			<h2>Create Account</h2>
			<input type='hidden' name='no' value='new'/>
			";
		}
		echo "
		<table class='large'>
			<tr>
				<td>
					Name:
				</td>
				<td colspan='3'>
					<input type='text' name='name' value='$ac_r[name]' style='width:100%;'/>
				</td>
			</tr>
			<tr>
				<td>
					Id:
				</td>
				<td>
					$ac_r[id]
				</td>
				<td>
					Active:
				</td>
				<td>
					<input type='checkbox' name='acti' value='true' $checked/>
				</td>
			</tr>
			<tr>
				<td>
					Address:
				</td>
				<td>
					<input type='text' name='bill_addr' value='$ac_r[bill_addr]'/>
				</td>
				<td>
					Vatnr:
				</td>
				<td>
					<input type='text' name='vatnr' value='$ac_r[vatnr]'/>
				</td>
			</tr>
			<tr>
				
				<td>
					Zip:
				</td>
				<td>
					<input type='text' name='bill_zip' value='$ac_r[bill_zip]'/>
				</td>
				<td>
					Email:
				</td>
				<td>
					<input type='text' name='bill_email' value='$ac_r[bill_email]'/>
				</td>
			</tr>
			<tr>
				<td>
					City:
				</td>
				<td>
					<input type='text' name='bill_city' value='$ac_r[bill_city]'/>
				</td>
				<td>
					Bank Account:
				</td>
				<td>
					<input type='text' name='bank_account' value='$ac_r[bank_account]'/>
				</td>
				
			</tr>
			<tr>
				<td>
					Country:
				</td>
				<td>
					<input type='text' name='bill_country' value='$ac_r[bill_country]'/>
				</td>
				<td>
					BIC:
				</td>
				<td>
					<input type='text' name='bic' value='$ac_r[bic]'/>
				</td>
			</tr>
			<tr>
				<td>
					Telephone:
				</td>
				<td>
					<input type='text' name='bill_tel' value='$ac_r[bill_tel]'/>
				</td>
				<td>
					
				</td>
				<td>
					
				</td>
			</tr>
	";

echo "
			
			
		</table>
		</form>
	</div>
";

?>