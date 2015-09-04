<?php

/*zebra image script (new)*/
require 'includes/zebra/Zebra_Image.php';

function show_error($error_code, $source_path, $target_path)
    {

        // if there was an error, let's see what the error is about
        switch ($error_code) {

            case 1:
                echo 'Source file "' . $source_path . '" could not be found!';
                break;
            case 2:
                echo 'Source file "' . $source_path . '" is not readable!';
                break;
            case 3:
                echo 'Could not write target file "' . $source_path . '"!';
                break;
            case 4:
                echo $source_path . '" is an unsupported source file format!';
                break;
            case 5:
                echo $target_path . '" is an unsupported target file format!';
                break;
            case 6:
                echo 'GD library version does not support target file format!';
                break;
            case 7:
                echo 'GD library is not installed!';
                break;
            case 8:
                echo '"chmod" command is disabled via configuration!';
                break;

        }

	}

$acid = $_POST[acid];
$acco = $_POST[acco];

$filepath = 'acc_img/'.$acco.'/';
$temppath = 'tempfiles/'.$acco.'/';

/*check if folder exists else create it */
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
	
	

    // create a new instance of the class
    $image = new Zebra_Image();

    // indicate a source image
    $image->source_path = $newFile;

    
	/*if original ext is wanted*/
    $ext = substr($image->source_path, strrpos($image->source_path, '.') + 1);

    // indicate a target image for thumb
    $image->target_path = $plainpath . 'logo.png';

    // resize thumb
    // and if there is an error, show the error message
    if (!$image->resize(150, 150, ZEBRA_IMAGE_BOXED, -1)) show_error($image->error, $image->source_path, $image->target_path);
	
	// indicate a target image for bigger
    $image->target_path = $plainpath . 'logo_big.png';

    // resize thumb
    // and if there is an error, show the error message
    if (!$image->resize(400, 400, ZEBRA_IMAGE_BOXED, -1)) show_error($image->error, $image->source_path, $image->target_path);
	
	
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
//header("Refresh: 2; URL=".$ret_url);


?>