<?php



include("includes/session.php");

$s_lvl = $tu_r[lvl];

if ($ac_id) {
	require_once('includes/accountcheck.php');
	$ac_r = pg_fetch_array($ac);
	$acco = $ac_r[identy];
}

/*check for unmade invoices*/
if ($_SESSION['ac_id']) {
	include('includes/invoicecheck.php');
}

//echo "$outputlanguage - $s_id - $ac_id";

$section = $_GET['section'];
$template = $_GET['template'];

if ($section == "") {
	$dashact = "menuactive";
	$conact = " ";
	$comact = " ";
	$invact = " ";
	$repact = " ";
	$todoact = " ";
} elseif ($section == "contacts") {
	$dashact = " ";
	$conact = "menuactive";
	$comact = " ";
	$invact = " ";
	$repact = " ";
	$todoact = " ";
} elseif ($section == "company") {
	$dashact = " ";
	$conact = " ";
	$comact = "menuactive";
	$invact = " ";
	$repact = " ";
	$todoact = " ";
} elseif ($section == "invoice" or $section == "def") {
	$dashact = " ";
	$conact = " ";
	$comact = " ";
	$invact = "menuactive";
	$repact = " ";
	$todoact = " ";
} elseif ($section == "reports") {
	$dashact = " ";
	$conact = " ";
	$comact = " ";
	$invact = " ";
	$repact = "menuactive";
	$todoact = " ";
} elseif ($section == "todo") {
	$dashact = " ";
	$conact = " ";
	$comact = " ";
	$invact = " ";
	$repact = " ";
	$todoact = "menuactive";
}


echo "
	<div class='topcont'>
		
		";

		
		if ($s_id == 0) {
			echo "
				
			";
		} else {
			echo "
			
					<div class='logout sbar'>
					
						
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
		<div class='logoandtext'>
			<div class='logo'>
				<a href='$rpath'>Uplink Contact Manager</a>
			</div>
			
			<div class='account bstyle'>
				 <a href='?template=account_sel'>{$lng->__('Account')}: $ac_r[name]</a>
			</div>
		</div>
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
						<div class='menuitem $dashact'>
							{$lng->__('Dash')}
						</div>
					</a>
					<a href='index.php?section=contacts&template=contact_list'>
						<div class='menuitem $conact'>
							{$lng->__('Contacts')}
						</div>
					</a>
					<a href='index.php?section=company&template=company_list'>
						<div class='menuitem $comact'>
							{$lng->__('Companies')}
						</div>
					</a>
					<a href='index.php?section=invoice&template=invoice_list'>
						<div class='menuitem $invact'>
							{$lng->__('Invoices')}
						</div>
					</a>
					<a href='index.php?section=reports&template=reports_list'>
						<div class='menuitem $repact'>
							{$lng->__('Reports')}
						</div>
					</a>
					<a href='index.php?section=todo&template=todo_list'>
						<div class='menuitem $todoact'>
							{$lng->__('Todo')}
						</div>
					</a>
					
				";
				}
			}
echo "
		</div>
";
if ($section == "def" or $section == "invoice") {
	if ($template == "def_list") {
		$defact = "menuactive";
		$pendact = " ";
		$invact = " ";
	} elseif ($template == "invoice_list_pend") {
		$defact = " ";
		$pendact = "menuactive";
		$invact = " ";
	} elseif ($template == "invoice_list") {
		$defact = " ";
		$pendact = " ";
		$invact = "menuactive";	
	} else {
		$defact = " ";
		$pendact = " ";
		$invact = " ";	
	}
	
		
	echo "
		<div class='menu_lower'>
			<a href='index.php?section=def&template=def_list'>
				<div class='loweritem $defact'>
					{$lng->__('Templates')}
				</div>
			</a>
			<a href='index.php?section=invoice&template=invoice_list_pend'>
				<div class='loweritem $pendact pub'>
					{$lng->__('Pending')}
				</div>
			</a>
			<a href='index.php?section=invoice&template=invoice_list'>
				<div class='loweritem $invact'>
					{$lng->__('Published')}
				</div>
			</a>
			
		</div>
		";
}

	
?>