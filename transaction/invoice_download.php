<?php
	$filename = $_GET['filename'];
	$acco = $_GET['acco'];
	
	echo "$filename";
	
	$file_url = 'pdf/'.$acco.'/'.$filename.'.pdf';
	
	echo "$file_url";
	
	header('Content-Type: application/pdf');
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
	readfile($file_url); // do the double-download-dance (dirty but worky)
	 
	
?>