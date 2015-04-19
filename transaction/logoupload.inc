<?php
/*wide image script */
include 'includes/wideimage/WideImage.php';

$acid = $_POST[acid];
$acco = $_POST[acco];

$filepath = 'acc_img/'.$acco.'/';
$temppath = 'tempfiles/'.$acco.'/';

/*check if folder exists else creat it */
if (file_exists($filepath)) {
	
} else {
	mkdir($filepath, 0777);
	chmod($filepath, 0777);
}
if (file_exists($temppath)) {
	
} else {
	mkdir($temppath, 0777);
	chmod($temppath, 0777);
}

function bytesToSize1024($bytes, $precision = 2) {
    $unit = array('B','KB','MB');
    return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
}
$tmp_name = $_FILES["image_file"]["tmp_name"];
$sFileName = $_FILES['image_file']['name'];
$sFileType = $_FILES['image_file']['type'];
$sFileSize = bytesToSize1024($_FILES['image_file']['size'], 1);

if ($sFileType == "image/gif") {
	$ext = ".gif";
} elseif ($sFileType == "image/jpeg") {
	$ext = ".jpg";
} elseif ($sFileType == "image/png")  {
	$ext = ".png";
}



if (!empty($_FILES)) {
    $tempFile =  $temppath.$sFileName;
    
    move_uploaded_file($tmp_name, $tempFile);
	
	$plainpath = $filepath;
	$filename = "logo";
	$newFile =  $temppath.$filename.$ext;
    rename($tempFile, $newFile);
	
	/*wideimage stuff*/
    $image = WideImage::load($newFile);
	$back = $image->allocateColor(255, 255, 255);
    $resized = $image->resizeDown(800,800);
    $resized->saveToFile($plainpath.$filename."_big.jpg");
	/* check size */
	$sizeh = $resized->getHeight();
	$sizew = $resized->getWidth();
	
	/*make thumb*/
	$thumb = $image->resizeDown(null, 150,'outside');
	
	/* check size */
	$sizeth = $thumb->getHeight();
	$sizetw = $thumb->getWidth();
	
	/* make used logo with cropped size as square */
	
	
    $thumb->saveToFile($plainpath.$filename.".jpg");
	
	/*output */
	$message = "
	Your file: ".$sFileName." has been successfully received.
	";
	$ret_url = "index.php?section=admin&template=account_info&acid=$acid";
	$icon = 'layout/img/icon_succ.png';
	
	echo "
			<div class='messagebox'>
				<img class='messageicon' src='$icon' alt='$message'>
				<p class='messagetext'>$message</p>
			</div>
			";
		

		


		
	
	
}
header("Refresh: 2; URL=".$ret_url);


?>