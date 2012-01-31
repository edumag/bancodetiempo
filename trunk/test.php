<?php

include_once("includes/inc.global.php");
include_once("Text/Password.php"); 

/* JPEGCam Test Script */
/* Receives JPEG webcam submission and saves to local file */
$cUser->MustBeLevel(1); 
$pathname= 'media/fotos/';
$mix=Text_Password::create(6) . chr(rand(50,57));
$filename = date('YmdHis') . $mix. '.jpg';
$fileurl = $pathname . $filename;
$result = file_put_contents( $fileurl, file_get_contents('php://input') );
if (!$result) {
	print "ERROR: Failed to write data to $filename, check permissions\n";
	exit();
}

$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $fileurl;
print "$url\n";

?>