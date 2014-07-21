<?php
//include("../../inc/config.php");
	// The Demos don't save files

	if (isset($_FILES["resume_file"]) && is_uploaded_file($_FILES["resume_file"]["tmp_name"]) && $_FILES["resume_file"]["error"] == 0) {
		//echo rand(1000000, 9999999);	// Create a pretend file id, this might have come from a database.
	}
	

	
	// Settings
	$pasta_destino = str_replace('_', '/', $_GET['pasta_destino']);
	$save_path = $_SERVER['DOCUMENT_ROOT'] . "/cms/".$pasta_destino."/";	// The path were we will save the file (getcwd() may not be reliable and should be tested in your environment)
	
	
	$upload_name = "Filedata";
	//$max_file_size_in_bytes = 2147483647;				// 2GB in bytes
	//$extension_whitelist = array("jpg", "gif", "png");	// Allowed file extensions
	$valid_chars_regex = '.A-Z0-9_!@#$%^&()+={}\[\]\',~`-';				// Characters allowed in the file name (in a Regular Expression format)
	
// Other variables	
	$MAX_FILENAME_LENGTH = 260;
	//$file_name = "teste.jpg";
	$file_name = strtolower(preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES[$upload_name]['name'])));
	$file_extension = "";
	$uploadErrors = array(
        0=>"There is no error, the file uploaded with success",
        1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
        2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
        3=>"The uploaded file was only partially uploaded",
        4=>"No file was uploaded",
        6=>"Missing a temporary folder"
	);
	
	
	// Move o arquivo
	if (!@move_uploaded_file($_FILES[$upload_name]["tmp_name"], $save_path.$file_name)) {
		HandleError("File could not be saved.");
		exit(0);
	} else {
		// retorno do envio. Nome do arquivo
		echo $file_name;
	}
	
/* Handles the error output. This error message will be sent to the uploadSuccess event handler.  The event handler
will have to check for any error messages and react as needed. */
function HandleError($message) {
	echo $message;
}
?>