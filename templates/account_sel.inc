<?php

$query = "
	select		accounts.id as aid,
				accounts.name,
				accounts.vatnr as vatnr
	FROM accounts LEFT OUTER JOIN account_user_link ON (accounts.id = account_user_link.acc_id)
	where user_id = $s_id
	and accounts.active = TRUE
	and account_user_link.active = TRUE
	order by accounts.name
	
	
";

$co = pg_query($conn, $query);



echo "
	<div class='centercont'>
		<h2 class='header'>{$lng->__('Select an account')}</h2>
	
		<form action='transaction.php?t=account_sel' method='post'>
			
	";
		
			while ($co_r = pg_fetch_array($co)) {
			echo "	
				<div class='div50'>
				<button name='accoun' class='listbutton bigstyle' value='$co_r[aid]'>
					<span>$co_r[name]</span><br/>
					$co_r[vatnr]
				</button>
				</div>
			";
			}
echo "		
			<div class='div50'>
				<button name='accoun' class='listbutton bigstyle' value='1'>
					<span>{$lng->__('Demo account')}</span><br/>
					{$lng->__('A generic demo account')}
				</button>
			</div>
		</form>		
	</div>
			
	";
	
	/* contact for account*/
	echo "
	<div class='centercont' style='margin-top: 30px; min-height:50px;'>
		<h2 class='header'>{$lng->__('New account')}</h2>
		
		<a href='index.php?template=request_acc'>
			<div style='text-align: center; font-size: 120%;' class='cbutton bigstyle'>
				<span>
					{$lng->__('Request an account')}
				</span>
			</div>
		</a>
		
		<a href='index.php?template=request_acc_help'>
			<div style='text-align: center; padding: 10px;' class=''>
				{$lng->__('What is an account')}?
			</div>
		</a>
	</div>
			
	";
		
	
?>