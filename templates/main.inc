<?php
if ($s_lvl < 1) {
	
} else {
require 'includes/accountcheck.inc';


$ac_r = pg_fetch_array($ac);

$acco = $ac_r[identy];

/*contacts*/

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
  				www,
  				created
	from		$acco.contacts
	order by	created desc
	limit 		10
	
";

$ul = pg_query($conn, $query);

/* invoice */

$query = "
		select		{$acco}.invoice_def.id as id,
					{$acco}.invoice_def.ident as ident,
					{$acco}.invoice_def.end_date as end_date,
					{$acco}.invoice_def.recurring as recurring,
					{$acco}.invoice_out.header as header,
					{$acco}.invoice_out.pid as pid,
					{$acco}.invoice_out.cid as cid,
					$acco.invoice_out.loc as loc,
					$acco.invoice_out.id as outid,
					$acco.invoice_out.addhead as addhead,
					$acco.invoice_out.invoice_id as invoice_id,
					$acco.invoice_out.created as created_out,
					$acco.invoice_out.dated as dated_out,
					$acco.invoice_out.ref as ref,
					$acco.invoice_out.pub as pub
		from		$acco.invoice_out LEFT JOIN $acco.invoice_def
		ON			($acco.invoice_out.invoice_id = $acco.invoice_def.ident)
		order by	$acco.invoice_out.dated desc
		limit 		10
		
	";

$in = pg_query($conn, $query);


/*notes*/

$query = "
	select		$acco.contact_notes.id,
				$acco.contact_notes.contact_id,
				$acco.contact_notes.cont as cont,
  				$acco.contact_notes.created as created,
  				$acco.contacts.fname as fname,
  				$acco.contacts.lname as lname
	from		$acco.contact_notes LEFT JOIN $acco.contacts
	ON			($acco.contact_notes.contact_id = $acco.contacts.id)
	order by	created desc
	limit 		6
	
";

$note = pg_query($conn, $query);

/*todo*/

$query = "
	select		$acco.todo.id,
				$acco.todo.contact_id,
				$acco.todo.company_id,
				$acco.todo.cont as cont,
  				$acco.todo.created as created,
  				$acco.todo.completed as completed,
  				$acco.contacts.fname as fname,
  				$acco.contacts.lname as lname
	from		$acco.todo LEFT JOIN $acco.contacts
	ON			($acco.todo.contact_id = $acco.contacts.id)
	order by	completed, created desc
	limit 		10
	
";

$todo = pg_query($conn, $query);

echo "
	<div class='centercont'>
		
	";


/* widget new todo*/
echo "
		<div class='widget'>
			<div class='header'><a href='index.php?section=todo&template=todo_list'>{$lng->__('New Todos')}</a></div>
			<table class='list'>
				
	";
		while ($todo_r = pg_fetch_array($todo)) {
			$date = strtotime($todo_r[created]);
			if ($todo_r[completed] == f) {
				$bolde = 'bold';
			} else {
				$bolde = '';
			}
			echo "
				<tr>
					<td class='$bolde'>
						<a href='index.php?section=todo&template=todo_view&tid=$todo_r[id]'>
						";
							echo substr($todo_r[cont], 0, 30);
							echo " 
						</a>
					</td>
					<td>
						".date('Y-m-d', $date)."
					</td>
				</tr>
			";
		}
echo "
			</table>
			
		</div>
";		

/* widget new invoice*/
echo "
		<div class='widget'>
			<div class='header'><a href='index.php?section=invoice&template=invoice_list'>{$lng->__('New Invoices')}</a></div>
			<table class='list'>
				
	";
		while ($in_r = pg_fetch_array($in)) {
			$date = strtotime($in_r[dated_out]);
			if ($in_r[pub] == f) {
				$bolde = 'pub';
			} else {
				$bolde = 'bold';
			}
			echo "
				<tr>
					<td class='$bolde'>
						<a href='index.php?section=invoice&template=invoice_view&inoid=$in_r[outid]&ident=$in_r[ident]'  class='$bolde'>
						$in_r[header] - $in_r[addhead]
						</a>
					</td>
					<td>
						".date('Y-m-d', $date)."
					</td>
				</tr>
			";
		}
echo "
			</table>
			
		</div>
";		
		
		/* widget new notes*/
echo "
		<div class='widget'>
			<div class='header'><a href='index.php?section=contacts&template=contact_list'>{$lng->__('New Notes')}</a></div>
			<table class='list'>
				
	";
		while ($note_r = pg_fetch_array($note)) {
			$date = strtotime($note_r[created]);
			echo "
				<tr>
					<td>
						<a href='index.php?section=contacts&template=contact_view&suid=$note_r[contact_id]'>
						";
						echo substr($note_r[cont], 0, 30);
						echo "
							<br/>- $note_r[lname], $note_r[fname]
						</a>
					</td>
					<td>
						".date('Y-m-d', $date)."
					</td>
				</tr>
			";
		}
echo "
			</table>
		</div>
";	
	
	/* widget new contact*/
echo "
		<div class='widget'>
			<div class='header'><a href='index.php?section=contacts&template=contact_list'>{$lng->__('New Contacts')}</a></div>
			<table class='list'>
				
	";
		while ($ul_r = pg_fetch_array($ul)) {
			$date = strtotime($ul_r[created]);
			echo "
				<tr>
					<td>
						<a href='index.php?section=contacts&template=contact_view&suid=$ul_r[id]'>
							$ul_r[lname] , $ul_r[fname]
						</a>
					</td>
					<td>
						".date('Y-m-d', $date)."
					</td>
				</tr>
			";
		}
echo "
			</table>
		</div>
";		
		
}
?>