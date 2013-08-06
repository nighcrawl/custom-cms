<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

include('../../inc/functions.php');

// Define a destination
$targetFolder = '/uploads'; // Relative to the root


if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];

	
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
		if(move_uploaded_file($tempFile,$targetFile)) {
			 $_FILES['Filedata']['name'];
			if(imagethumb( $targetFile, $targetFile, $max_size = 960, $expand = true, $square = FALSE, $crop = true,960,466)){
				echo "cool";
			} else {
				echo "pascool";
			}
		} else {
			echo "-1";
		}
	} else {
		echo 'Invalid file type.';
	}
} else { echo '0'; }
?>