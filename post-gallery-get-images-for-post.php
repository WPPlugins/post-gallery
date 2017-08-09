<?php
	require( dirname(__FILE__) . '/../../../wp-config.php' );
	require 'thirdparty/jsonwrapper/jsonwrapper.php';
	require_once 'post-gallery.php';
	
	if(isset($_GET['id']))
	{
		$post_id = $_GET['id'];
		$images = Post_gallery::post_gallery_get_existing_images($post_id);
		if($images=="")
		{
			echo "0";
		}
		else
		{
			echo json_encode($images);
		}
	}
	else
	{
		echo 'An unexpected error has occurred.';
	}
?>