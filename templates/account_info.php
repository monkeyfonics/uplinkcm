<?php

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
	FROM accounts 
	where id = $ac_id
	
	
	
";

$ac = pg_query($conn, $query);

$ac_r = pg_fetch_array($ac);

echo "
	<div class='centercont'>
<div style='clear:both;'>
		
		<div class='logo_holder'>
		";
		$logopath = "acc_img/".$ac_r[identy]."/logo.png";
		if (file_exists($logopath)) {
			echo "
			<img src='$logopath' style='max-height: 150px;'/>
			";
		} else {
			echo "
			{$lng->__('No image')}
			
			";
		}
		echo "
		</div>
		<h2 class='header'>$ac_r[name]</h2>
		<table class='grid'>
			
			
			<tr>
				
				<td>
					{$lng->__('Identyfier')}:
				</td>
				<td>
					$ac_r[identy]
				</td>
				<td>
					{$lng->__('VAT-nr')}:
				</td>
				<td>
					$ac_r[vatnr]
				</td>
			</tr>
			<tr>
				<td>
					{$lng->__('Address')}:
				</td>
				<td>
					$ac_r[bill_addr]
				</td>
				<td>
					{$lng->__('Telephone')}:
				</td>
				<td>
					$ac_r[bill_tel]
				</td>
			</tr>
			<tr>
				<td>
					{$lng->__('Zip')}:
				</td>
				<td>
					$ac_r[bill_zip]
				</td>
				<td>
					{$lng->__('Email')}:
				</td>
				<td>
					$ac_r[bill_email]
				</td>
				
			</tr>
			<tr>
				<td>
					{$lng->__('City')}:
				</td>
				<td>
					$ac_r[bill_city]
				</td>
				
				<td>
					{$lng->__('Bank Account')}:
				</td>
				<td>
					$ac_r[bank_account]
				</td>
			</tr>
			<tr>
				<td>
					{$lng->__('Country')}:
				</td>
				<td>
					$ac_r[bill_country]
				</td>
				
				<td>
					{$lng->__('Bic')}:
				</td>
				<td>
					$ac_r[bic]
				</td>
			</tr>
		</table>
	</div>
";

?>