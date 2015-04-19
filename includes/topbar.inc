<?php



include("includes/session.inc");

$s_lvl = $tu_r[lvl];

if ($ac_id) {
	require_once('includes/accountcheck.inc');
	$ac_r = pg_fetch_array($ac);
	$acco = $ac_r[identy];
}

/*check for unmade invoices*/
if ($_SESSION['ac_id']) {
	include('includes/invoicecheck.inc');
}

//echo "$outputlanguage - $s_id - $ac_id";

echo "
	<div class='topcont'>
		<div class='logo'>
			<a href='$rpath'>Uplink Contact Manager</a>
		</div>
		
		<div class='account bstyle'>
			 <a href='?template=account_sel'>{$lng->__('Account')}: $ac_r[name]</a>
		</div>
		";

		
		if ($s_id == 0) {
			echo "
				
			";
		} else {
			echo "
				<div class='logout'>
				
					
					<a href='index.php?section=admin&template=userman'><div class='button bstyle'><span class='settingtext'>{$lng->__('User')}: $tu_r[login]</span><span class='settingicon'><img src='layout/img/cog_grey.png' alt='Settings'/></span></div></a>
					
					<a href='transaction.php?t=logout'><div class='button bstyle'>{$lng->__('Logout')}</div></a>
					";
				
				
					if ($tu_r[lvl] >= 5) {
						echo "<a href='index.php?section=admin&template=main'><div class='button bstyle'>Admin</div></a>";
					}
					echo "
				</div>
			";
		}
		
	echo "
	</div>
	";
	
	/*menu */
	
	echo "
		<div class='menu'>
		";
			
			if ($_SESSION['ac_id'] != 0) {
				if ($tu_r[lvl] >= 1) {
				echo "		
					<a href='index.php?template=main'>
						<div class='menuitem'>
							{$lng->__('Dash')}
						</div>
					</a>
					<a href='index.php?section=contacts&template=contact_list'>
						<div class='menuitem'>
							{$lng->__('Contacts')}
						</div>
					</a>
					<a href='index.php?section=company&template=company_list'>
						<div class='menuitem'>
							{$lng->__('Companies')}
						</div>
					</a>
					<a href='index.php?section=def&template=def_list'>
						<div class='menuitem'>
							{$lng->__('Templates')}
						</div>
					</a>
					<a href='index.php?section=invoice&template=invoice_list'>
						<div class='menuitem'>
							{$lng->__('Invoices')}
						</div>
					</a>
					<a href='index.php?section=reports&template=reports_list'>
						<div class='menuitem'>
							{$lng->__('Reports')}
						</div>
					</a>
					<a href='index.php?section=todo&template=todo_list'>
						<div class='menuitem'>
							{$lng->__('Todo')}
						</div>
					</a>
					
				";
				}
			}
echo "
		</div>
";
	
	
?>