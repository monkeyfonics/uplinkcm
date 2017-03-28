<?php

$acd = $_POST['acid'];
$activ = $_POST[acti];
$name = $_POST['name'];
$vatnr = $_POST['vatnr'];
$bill_addr = $_POST['bill_addr'];
$bill_zip = $_POST['bill_zip'];
$bill_city = $_POST['bill_city'];
$bill_country = $_POST['bill_country'];
$bill_email = trim($_POST['bill_email']);
$bill_tel = $_POST['bill_tel'];
$bank_account = trim($_POST['bank_account']);
$bic = trim($_POST['bic']);

/*fetch accounts list to check for nr */

$query = "
	select		id,
				identy
	from		accounts
	
	
";

$accrow = pg_query($conn, $query);

$acc_row = pg_fetch_array($accrow);

if ($acd) {
	$query = "
			update accounts
			set		name='$name',
					active=$activ,
					vatnr='$vatnr',
					bill_addr='$bill_addr',
					bill_zip='$bill_zip',
					bill_city='$bill_city',
					bill_country='$bill_country',
					bill_email='$bill_email',
					bank_account='$bank_account',
					bic='$bic',
					bill_tel='$bill_tel'
					
			where	id=$acd
		";
	$ch = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=admin&template=account_info&acid='.$acd;
	
} else {
	/* make id for new accounts*/
	$idnr = rand(100, 999);
	
	/* account identifier */
	$identy = strtolower('acc_'.substr($name, 0, 5).'_'.$idnr);
	$identy = preg_replace('/\s+/', '', $identy);

	$query = "
			insert into accounts (
			name,
			active,
			vatnr,
			bill_addr,
			bill_zip,
			bill_city,
			bill_country,
			bill_email,
			identy,
			bank_account,
			bic,
			bill_tel
			) values (
			'$name',
			$activ,
			'$vatnr',
			'$bill_addr',
			'$bill_zip',
			'$bill_city',
			'$bill_country',
			'$bill_email',
			'$identy',
			'$bank_account',
			'$bic',
			'$bill_tel'
			)
		";
	$ch = pg_query($conn, $query);
	
	$ret_url = 'index.php?section=admin&template=account_list';
	
}

		
		$message = "{$lng->__('Account Saved')}";
		$icon = 'layout/img/icon_succ.png';

		
echo "
	<div class='messagebox'>
		<img class='messageicon' src='$icon' alt='$message'>
		<p class='messagetext'>$message</p>
	</div>
	";

		header("Refresh: 1; URL=".$ret_url);
		
?>