<?php
if ($s_lvl < 1) {
	header("Location: $rpath");
    die();
}
require 'includes/accountcheck.php';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r['identy'];

$query = "
	select		id,
				name,
				ytunnus,
				www,
  				bill_addr,
  				bill_zip,
  				bill_city,
  				bill_country,
  				email,
  				phone,
  				created,
  				modified
	from		$acco.company
	order by	name
";

$ul = pg_query($conn, $query);

/* abc shortcut */
echo "
	<div class='abc_container'>
		";
	while ($ul_r = pg_fetch_array($ul)) {
		$l = strtoupper(mb_substr($ul_r['name'],0,1,"UTF-8"));
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
		<a href='index.php?section=company&template=company_list'>
			<div class='header'>{$lng->__('Companies')}</div>
		</a>
		<a href='index.php?section=company&template=company_edit'>
			<div>{$lng->__('New Company')}</div>
		</a>
	</div>
";

echo "
	<div class='fullcont'>
		
		
		<table class='list large'>
			<tr>
				<th class='first'>
					{$lng->__('Name')}:
				</th>
				<th>
					{$lng->__('VAT-nr')}:
				</th>
				<th class='small_hide'>
					{$lng->__('Email')}:
				</th>
				<th>
					{$lng->__('www')}:
				</th>
			</tr>
			";
		pg_result_seek($ul,0);
		while ($ul_r = pg_fetch_array($ul)) {
				
			/*insert anchor */
			$l = strtoupper(mb_substr($ul_r['name'],0,1,"UTF-8"));
			if ($l != $letter && strlen($l)) {
				$letter = $l;
				$a = "<a id='$l'/>";
			} else {
				$a = "";
			}
				
			// format webaddress
			$webadr = str_replace("http://","",$ul_r['www']);
			
			echo "	
				<tr>
					
					<td class='first'>
						$a
						<a href='index.php?section=company&template=company_view&suid=$ul_r[id]'>
							$ul_r[name]
						</a>
					</td>
					<td>
						$ul_r[ytunnus]
					</td>
					<td class='small_hide'>
						$ul_r[email]
					</td>
					<td>
						<a href='http://$webadr' target='_blank'>
							$ul_r[www]
						</a>
					</td>
				</tr>
			";
		}
		
		
		echo "	
		</table>
		
		
	</div>
	";
?>