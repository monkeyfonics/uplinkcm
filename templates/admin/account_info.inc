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

$query = "
	select		account_user_link.acc_id,
				account_user_link.user_id,
				account_user_link.active,
				users.login as login,
				users.lname as lname,
				users.fname as fname
	from		account_user_link LEFT OUTER JOIN users ON (account_user_link.user_id = users.id)
	where acc_id = $acd
	
";

$us = pg_query($conn, $query);

/*use buttons row */
echo "
	<div class='buttons'>
		
		<a href='index.php?section=admin&template=account_list'>
			<div class='header'>Accounts</div>
		</a>
		
	</div>
";

echo "
	<div class='centercont'>
		
		<div class='div33'>
			<a href='index.php?section=admin&template=account_edit&acid=$ac_r[id]'>
				<div class='bigbutton bigstyle'>
					<span>Edit Account</span><br/>
						Edits Account.
				</div>
			</a>
		</div>
		<div class='div33'>
			<a href='index.php?section=admin&template=account_user&acid=$ac_r[id]'>
				<div class='bigbutton bigstyle'>
					<span>Edit Users</span><br/>
						Edits user rights to account.
				</div>
			</a>
		</div>
		<div class='div33'>
			<a href='transaction.php?t=createdb&acid=$acd'>
				<div class='bigbutton bigstyle'>
					<span>Create database</span><br/>
					Creates database for account.
					
				</div>
			</a>
		</div>
		<div class='div33'>
			<a href='index.php?section=admin&template=upload_logo&acid=$ac_r[id]'>
				<div class='bigbutton bigstyle'>
					<span>Upload Logo</span><br/>
					Upload logo for account.
				</div>
			</a>
		</div>
		
		<div style='clear:both;'>
		<h2 class='header'>$ac_r[name]</h2>
		";
		$logopath = "acc_img/".$ac_r[identy]."/logo.png";
		if (file_exists($logopath)) {
			echo "
			<img src='$logopath' style='max-height: 150px;'/>
			";
		} else {
			echo "
			No image
			";
		}
		echo "
		<table class='grid'>
			
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
					$ac_r[active]
				</td>
			</tr>
			<tr>
				<td>
					Vatnr:
				</td>
				<td>
					$ac_r[vatnr]
				</td>
				<td>
					Ident:
				</td>
				<td>
					$ac_r[identy]
				</td>
			</tr>
			<tr>
				<td>
					Address:
				</td>
				<td>
					$ac_r[bill_addr]
				</td>
				<td>
					Zip:
				</td>
				<td>
					$ac_r[bill_zip]
				</td>
			</tr>
			<tr>
				<td>
					City:
				</td>
				<td>
					$ac_r[bill_city]
				</td>
				<td>
					Country:
				</td>
				<td>
					$ac_r[bill_country]
				</td>
			</tr>
			<tr>
				<td>
					Email:
				</td>
				<td>
					$ac_r[bill_email]
				</td>
				<td>
					Bank Account:
				</td>
				<td>
					$ac_r[bank_account]
				</td>
			</tr>
			<tr>
				<td>
					Tel:
				</td>
				<td>
					$ac_r[bill_tel]
				</td>
				<td>
					Bic:
				</td>
				<td>
					$ac_r[bic]
				</td>
			</tr>
		</table>
	";

echo "
		
		
		<hr/>
		<h3 class='header'>Users:</h3>
		
		<table class='list'>
	";	
		while ($us_r = pg_fetch_array($us)) {
			if ($us_r[active] == t) {
				$active = "Active";
			} else {
				$active = "Not active";
			}			
			echo "
				<tr>
					<td>
						$us_r[login]
					</td>
					<td>
						$us_r[lname], $us_r[fname]
					</td>
					<td>
						$active
					</td>
				</tr>
			";
					
		}
	echo "
		</table>
		</div>	
	</div>
";

?>