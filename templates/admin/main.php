<?php
if ($s_lvl < 5) {
	header("Location: $rpath");
    die();
}



/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=admin&template=main'>
			<div class='header'>Admin</div>
		</a>
		
		
	</div>
";


echo "
	<div class='centercont'>
	";
		
			
			echo "	
				<div class='div33'>
					<a href='?section=admin&template=account_list'>
						<div class='bigbutton bigstyle'>
							<span>Accounts</span><br/>
							Lists accounts.
						</div>
					</a>
				</div>
				<div class='div33'>
					<a href='?section=admin&template=user_list'>
						<div class='bigbutton bigstyle'>
							<span>Users</span><br/>
							Lists accepted users.
						</div>
					</a>
				</div>
				<div class='div33'>
					<a href='?section=admin&template=user_new_list'>
						<div class='bigbutton bigstyle'>
							<span>Unchecked Users</span><br/>
							Lists users that have not been checked.
						</div>
					</a>
				</div>
				
			";
		
echo "	
				
	</div>
			
	";
		
	
?>