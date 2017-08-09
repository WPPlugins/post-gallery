<?php

/**
 * Post_gallery
 *
 * @package Post Gallery
 * @author Jonathan Christopher
 */


class Post_gallery
{


	/**
	 * Runs only when the plugin is activated, sets up the database table for Post Gallery
	 *
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_install()
	{
		// insert tables if needed
		global $wpdb, $table_prefix, $post;
		$table_name = $wpdb->prefix . "postgallery";
		if($wpdb->get_var("show tables like '$table_name'") != $table_name)
		{
			$sql = "CREATE TABLE " . $table_name . " (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				postid bigint(20) NOT NULL,
				image_title varchar(255),
				image_caption varchar(255),
				image_location varchar(255),
				image_order int(4) NOT NULL,
				status varchar(10),
				UNIQUE KEY id (id)
				);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		
		// create directory if needed
		$uploads_dir = dirname(__FILE__) . '/uploads';
		if(!is_dir($uploads_dir)||!is_writable($uploads_dir))
		{
			mkdir($uploads_dir, 0777);
		}
	}
	
	
	
	
	function post_gallery_init_admin_menu()
	{
		add_options_page('Post Gallery', 'Post Gallery', 8, __FILE__, array('Post_gallery','post_gallery_plugin_options'));
	}
	
	
	
	
	function post_gallery_plugin_options()
	{
		echo '<div class="wrap">';
			echo '<div id="icon-options-general" class="icon32"><br /></div>';
			echo '<h2>Post Gallery Options</h2>';
			echo '<form action="options.php" method="post">';
				wp_nonce_field('update-options');
				echo '<div style="padding:20px 0 0 0;">';
				echo '<input type="checkbox" name="post_notes_on_pages" value="true"';
				if (get_option('post_notes_on_pages')=='true') { echo 'checked="checked"'; }
				echo ' />';
				echo '<span style="padding-left:10px;">Include on Pages</span>';
				echo '</div>';
				echo '<input type="hidden" name="action" value="update" />';
				echo '<input type="hidden" name="page_options" value="post_notes_on_pages" />';
				echo '<p class="submit">';
				echo '<input type="submit" class="button-primary" value="Save" />';
				echo '</p>';
			echo '</form>';
		echo '</div>';
	}






	/**
	 * Injects the markup to include the required Post Gallery JavaScript
	 *
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_javascript()
	{

$abspath = str_replace('/wp-admin','',substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
$plugins_url = $abspath . '/wp-content/plugins';
$ajaxupload = <<<END
jQuery(document).ready(function()
{
	post_gallery_hook_upload("$plugins_url",1);
});
END;

		
		echo '<!-- WordPress Post Gallery Plugin START -->'."\n";
		echo '<script type="text/javascript" src="' . $abspath . '/wp-content/plugins/post-gallery/js/jquery.ajax.upload.js"></script>'."\n";
		echo '<script type="text/javascript" src="' . $abspath . '/wp-content/plugins/post-gallery/js/post-gallery.js"></script>'."\n";
		echo '<script type="text/javascript">'."\n";
		echo $ajaxupload;
		echo '</script>'."\n";
		echo '<!-- WordPress Post Gallery Plugin END -->'."\n";
	}






	/**
	 * Injects the markup to include the required Post Gallery CSS
	 *
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_css()
	{
		$abspath = str_replace('/wp-admin','',substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
		$plugins_url = $abspath . '/wp-content/plugins';
		echo '<link rel="stylesheet" href="' . $plugins_url . '/post-gallery/css/post-gallery.css" type="text/css" media="screen" />';
	}






	/**
	 * Calls WordPress' function for injecting markup into the post screen
	 * Only adds the injection request to a queue within WordPress, does not actually insert at this time
	 *
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_add_image_entry()
	{
		add_meta_box( 'post_gallery_section1', __( 'Post Image', 'post_gallery_imagedomain' ), 
				array('Post_gallery','post_gallery_inner_custom_box'), 'post', 'advanced' );
		if (get_option('post_notes_on_pages')=='true')
		{
			add_meta_box( 'post_gallery_section1', __( 'Post Image', 'post_gallery_imagedomain' ), 
				array('Post_gallery','post_gallery_inner_custom_box'), 'page', 'advanced' );
		}
	}






	/**
	 * Callback that injects the markup within the meta box
	 *
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_inner_custom_box()
	{
$markup = <<<END
<div class="post_gallery_image_wrapper">
	<div class="post_gallery_fields">
		<div class="post_gallery_textfield">
			<label for="post_gallery_image_title_1">Title</label>
			<input type="text" id="post_gallery_image_title_1" name="post_gallery_image_title_1" value="" />
		</div>
		<div class="post_gallery_textfield">
			<label for="post_gallery_image_caption_1">Caption</label>
			<input type="text" id="post_gallery_image_caption_1" name="post_gallery_image_caption_1" value="" />
		</div>
		<div class="post_gallery_upload post_gallery_upload_file">
			<a href="#" class="post_gallery_upload_link" id="post_gallery_upload_1">Upload</span>
		</div>
	</div>
	<div class="post_gallery_image_thumbnail" id="post_gallery_image_thumbnail_1"></div>
	<input type="hidden" style="display:none;" id="post_gallery_image_location_1" name="post_gallery_image_location_1" value="Unknown" />
</div>
END;
		// we can force 1 because this function is called only when no Images exist
		echo $markup;
	}






	/**
	 * Repeated call to WordPress' add_meta_box() in order to prep an area for an existing Post Gallery image
	 *
	 * @param int $tmp_index The index of the current image
	 * @param int $tmp_post_note_id The id of the current Post Gallery image
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_insert_image($tmp_index,$tmp_post_image_id)
	{
		global $tmp_post_image_id, $post_image_index, $count;
		$post_image_id = $tmp_post_image_id;
		$count = $tmp_index;
		
		add_meta_box( 'post_gallery_section'.$tmp_index, __( 'Post Image', 'post_gallery_imagedomain' ), 
				array('Post_gallery','post_gallery_inner_custom_box_populated'), 'post', 'advanced' );
		if (get_option('post_notes_on_pages')=='true')
		{
			add_meta_box( 'post_gallery_section'.$tmp_index, __( 'Post Image', 'post_gallery_imagedomain' ), 
				array('Post_gallery','post_gallery_inner_custom_box_populated'), 'page', 'advanced' );
		}
	}






	/**
	 * Empty callback for post_gallery_insert_note() - we don't want anything prepped because we're going to inject it later
	 *
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_inner_custom_box_populated()
	{
		return;
	}






	/**
	 * Injects JavaScript to set up retrieval of existing Post Gallery image copy
	 *
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_load_existing_images_javascript()
	{
		echo "\n\n" . '<script type="text/javascript">' . "\n";
		if(isset($_GET['post']))
		{
			// check to make sure we have images
			if(intval(Post_gallery::post_gallery_get_images_count($_GET['post']))>0)
			{
				$abspath = str_replace('/wp-admin','',substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
				$plugins_url = $abspath . '/wp-content/plugins';
				
				echo '	post_gallery_load_existing_images("' . $plugins_url . '",' . $_GET['post'] . ')' . "\n";
			}
			else
			{
				// just going to initialize a starter meta box
				echo '	post_gallery_init_images()' . "\n";
			}
		}
		else
		{
			// just going to initialize a starter meta box
			echo '	post_gallery_init_images()' . "\n";
		}
		echo '</script>' . "\n\n";
	}






	/**
	 * Deletes all 'final' Images for a single post
	 *
	 * @param int $post_id The id of the post for which you want to remove all existing Post Images
	 * @return void
	 * @author Jonathan Christopher
	 */
	function flush_existing_post_images($post_id)
	{
		global $wpdb, $table_prefix, $post;
		$table_name = $wpdb->prefix . "postgallery";
		
		// make sure we're dealing with a post not a page
		if ( 'page' != $_POST['post_type'] )
		{
			// check to make sure the sure can indeed edit the post
			if ( !current_user_can( 'edit_post', $post_id ))
				return $post_id;
		}
		
		$delete = "DELETE FROM " . $table_name . " WHERE status='final' and postid = " . $post_id;
		$results = $wpdb->query($delete);
		return $post_id;
	}






	/**
	 * Parses POST data and saves any Post Images to the database
	 *
	 * @param int $post_id The id of the current post
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_save_images($post_id)
	{
		global $wpdb, $table_prefix, $post;
		$table_name = $wpdb->prefix . "postgallery";
				
		// make sure we're dealing with a post not a page
		if ('page' != $_POST['post_type'])
		{
			// check to make sure the sure can indeed edit the post
			if (!current_user_can( 'edit_post', $post_id ))
			{
				return $post_id;
			}	
			
			if(!isset($_POST['post_gallery_image_count']))
			{
				return $post_id;
			}
		}

		// OK, we're authenticated: we need to find and save the data
		
		// We need to track whether or not this is a post revision or final
		if(wp_is_post_revision($post_id))
		{
			$image_status = "revision";
		}
		else
		{
			$image_status = "final";
			// Let's remove any old final versions... we don't need them and we don't want them
			Post_gallery::flush_existing_post_images($post_id);
		}

		for ($i=1; $i <= $_POST['post_gallery_max']; $i++)
		{
			if(isset($_POST['post_gallery_image_location_'.$i]))
			{
				// set the location
				$image_location = $_POST['post_gallery_image_location_'.$i];
			
				if(trim($image_location)!="")
				{
					// set the title
					$image_title = $_POST['post_gallery_image_title_'.$i];
				
					// set the caption
					$image_caption = $_POST['post_gallery_image_caption_'.$i];
				
					// let's set the order
					$image_order = $_POST['post_gallery_max'];
					if(isset($_POST['post_gallery_image_order_'.$i]))
					{
						$image_order = trim($_POST['post_gallery_image_order_'.$i]);
					}
				
					$insert = "INSERT INTO " . $table_name .
							  " (postid, image_title, image_caption, image_location, image_order, status) " .
							  "VALUES (" . $wpdb->escape($post_id) . ",'" . $wpdb->escape($image_title) . "','" . $wpdb->escape($image_caption) . "','" . $wpdb->escape($image_location) . "','" . $wpdb->escape($image_order) . "','" . $wpdb->escape($image_status) . "')";

					$results = $wpdb->query($insert);
				}
			}
		}
		
		return $post_id;
	}






	/**
	 * Retrieves the existing Post Images for a single post
	 *
	 * @param int $post_id The ID of the post for which you would like to retrieve Post Images
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_load_existing_images($post_id)
	{
		global $wpdb, $table_prefix, $post, $post_image_index;
		$table_name = $wpdb->prefix . "postgallery";
		
		// Prep query
		$sql = "SELECT * FROM " . $table_name . " WHERE postid = " . $post_id;
		$results = $wpdb->get_results($sql,ARRAY_A);
		$results = Post_gallery::post_gallery_stripslashes_deep($results);
		
		// Set notes count
		$post_image_count = sizeof($results);
		
		if($post_image_count>0)
		{
			// Inject pre-populated notes
			for ($i=1; $i <= $post_image_count; $i++)
			{
				$post_image_index=$i;
				Post_gallery::post_gallery_insert_image($i,$results[$i-1]['id']);
			}
		}
	}






	/**
	 * Recursive function to strip slashes from an associative array
	 *
	 * @param array $value The array from which you want to strip slashes
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_stripslashes_deep($value)
	{
		$value = is_array($value) ?
			array_map('stripslashes_deep', $value) :
		stripslashes($value);
		return $value;
	}






	/**
	 * Retrieve the existing Images for a single post
	 *
	 * @param int $post_id The ID of the post for which you would like to retrieve Post Notes
	 * @return Array $results An associative array containing all images for post
	 * @author Jonathan Christopher
	 */
	function post_gallery_get_existing_images($post_id)
	{
		global $wpdb, $table_prefix, $post, $post_image_index;
		$table_name = $wpdb->prefix . "postgallery";
		
		// Prep query
		$sql = "SELECT image_title, image_caption, image_location FROM " . $table_name . " WHERE status = 'final' AND postid = " . $post_id . " AND image_location != '' AND UPPER(image_location) != 'UNKNOWN' ORDER BY image_order";
		$results = $wpdb->get_results($sql,ARRAY_A);
		
		// we need to strip all slashes
		$results = Post_gallery::post_gallery_stripslashes_deep($results);
		return $results;
	}






	/**
	 * Retrieve the number of Images for a particular post
	 *
	 * @param int $post_id The post for which you would like the Post Image count
	 * @return int $result The number of Post Notes for a single post
	 * @author Jonathan Christopher
	 */
	function post_gallery_get_images_count($post_id)
	{
		global $wpdb, $table_prefix, $post;
		$table_name = $wpdb->prefix . "postgallery";
		
		$sql = "SELECT count(postid) as cnt FROM " . $table_name . " WHERE image_location != '' AND UPPER(image_location) != 'UNKNOWN' AND postid = " . $post_id;
		$result = $wpdb->get_results($sql,ARRAY_A);
		return intval($result[0]['cnt']);
	}






	/**
	 * Initializes functionality for Post Gallery
	 *
	 * @return void
	 * @author Jonathan Christopher
	 */
	function post_gallery_init_gallery()
	{
		global $post_image_index;
		$post_image_index = 1;
		
		if(isset($_GET['post']))
		{
			$post_id = $_GET['post'];
			if(!is_array($post_id))
			{
				$post_count = intval(Post_gallery::post_gallery_get_images_count($post_id));
				if($post_count>0)
				{
					// We're editing, and we have existing notes, so let's load all existing
					Post_gallery::post_gallery_load_existing_images($post_id);
				}
				else
				{
					// No existing notes, let's just set up a blank...
					Post_gallery::post_gallery_add_image_entry();
				}
			}
		}
		else
		{
			// This is a new note, only need to prep empty note
			Post_gallery::post_gallery_add_image_entry();
		}
	}
}

?>