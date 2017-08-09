<?php
require( dirname(__FILE__) . '/../../../wp-config.php' );
$operationSuccess = false;

if (isset($_FILES['userfile']) && (!empty($_FILES['userfile']['tmp_name'])))
{
		$special_chars = array (' ','`','"','\'','\\','/'," ","#","$","%","^","&","*","!","~","‘","\"","’","'","=","?","/","[","]","(",")","|","<",">",";","\\",",");
		$filename = str_replace($special_chars,'',$_FILES['userfile']['name']);
		$filename = time() . "_" . $filename;
		@move_uploaded_file( $_FILES['userfile']['tmp_name'], dirname(__FILE__) . '/uploads/' . $filename );
		@chmod(dirname(__FILE__) . '/uploads/' . $filename, 0755);
		$operationSuccess = true;
}

if ($operationSuccess == true)
{
	if ($fp_check_file = @fopen(dirname(__FILE__) . '/uploads/' . $filename, 'rb')) 
	{
		fclose($fp_check_file);
		$abspath = str_replace('/post-gallery','',substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
		echo $abspath . "|" . $filename;
	}
}
?>