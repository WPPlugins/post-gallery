<?php

function post_gallery_get_images($post_id=null)
{
	global $post;
	if( empty($post_id) )
	{
		$post_id = $post->ID;
	}
	$images = Post_gallery::post_gallery_get_existing_images($post_id);
	if(!is_array($images))
	{
		$images = array();
	}
	return $images;
}

?>