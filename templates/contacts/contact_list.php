<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

$query = "
	select		id,
				fname,
				lname,
				bill_addr,
  				bill_zip,
  				bill_city,
  				bill_country,
  				email,
  				phone1,
  				phone2,
  				www
	from		$acco.contacts
	order by	lname, fname
	
";

$ul = pg_query($conn, $query);


/* abc shortcut */
echo "
	<div class='abc_container'>
		";
	while ($ul_r = pg_fetch_array($ul)) {
		$l = strtoupper(mb_substr($ul_r[lname],0,1,"UTF-8"));
		if ($l != $letter && strlen($l)) {
			$letter = $l;
			echo "<a class='abc_letters' href='#$l'><div class='abc_item'>$l</div></a>";
		}
	}				
	echo "	
	</div>
	";


/*use buttons row */
echo "
	<div class='buttons'>
		<a href='index.php?section=contacts&template=contact_list'>
			<div class='header'>{$lng->__('Contacts')}</div>
		</a>
		<a href='index.php?section=contacts&template=contact_edit'>
			<div>{$lng->__('New Contact')}</div>
		</a>
	</div>
";





echo "


	<div class='fullcont'>
		
		
		<table class='list large'>
			<tr>
				<th class='first'>
					{$lng->__('Namn')}:
				</th>
				<th class='small_hide'>
					{$lng->__('Email')}:
				</th>
				<th>
					{$lng->__('Phone')}:
				</th>
				<th>
					{$lng->__('Address')}:
				</th>
			</tr>
			";
		pg_result_seek($ul,0);
		while ($ul_r = pg_fetch_array($ul)) {
			
			/*insert anchor */
			$l = strtoupper(mb_substr($ul_r[lname],0,1,"UTF-8"));
			if ($l != $letter && strlen($l)) {
				$letter = $l;
				$a = "<a id='$l'>";
			} else {
				$a = "";
			}
			
			
			echo "	
				<tr>
					
					<td class='first'>
					
					$a
						<a href='index.php?section=contacts&template=contact_view&suid=$ul_r[id]'>
							$ul_r[lname], $ul_r[fname]
						</a>
					</td>
					<td class='small_hide'>
						<a href='index.php?section=contacts&template=contact_view&suid=$ul_r[id]'>
						$ul_r[email]
						</a>
					</td>
					<td>
						<a href='index.php?section=contacts&template=contact_view&suid=$ul_r[id]'>
						$ul_r[phone1]
						</a>
					</td>
					<td>
						
						$ul_r[bill_addr] $ul_r[bill_zip] $ul_r[bill_city]
					</td>
				</tr>
			";
		}
		
		
		echo "	
		</table>
		
		
	</div>
	";
?>