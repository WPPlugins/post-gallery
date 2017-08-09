<?php
/*
Plugin Name: Post Gallery
Version: 1.0.6
Plugin URI: http://mondaybynoon.com/wordpress-post-gallery/
Author: Jonathan Christopher
Author URI: http://jchristopher.me
Description: Provides ability to add any number of additional images to each post

Compatible with WordPress 2.8


================ 
INSTALLATION   
================ 

1. Download the file and unzip it into your /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' admin menu in WordPress


========= 
USAGE   
========= 

To retrieve an associative array containing all Gallery images for a post, use the following within The Loop:	

	<?php $post_images = post_gallery_get_images(); ?>

You will be provided an associative array with which to work in your template:

	<?php $total_images = sizeof($post_images); ?>
	<?php if($total_images>0) : ?>
		<div class="post_gallery">
			<ul>
				<?php for ($i = 0; $i < $total_images; $i++) : ?>
					<li>
						<div class="image_title"><?php echo $post_images[$i]['image_title']; ?></div>
						<div class="image">
							<a href="<?php bloginfo('url'); ?>/wp-content/plugins/post-gallery/thirdparty/phpthumb/phpThumb.php?src=<?php bloginfo('url'); ?>/wp-content/plugins/post-gallery/uploads/<?php echo $post_images[$i]['image_location']; ?>&amp;w=500&amp;h=500&amp;zc=1" id="image_<?php echo $i+1; ?>">
								<img src="<?php bloginfo('url'); ?>/wp-content/plugins/post-gallery/thirdparty/phpthumb/phpThumb.php?src=<?php bloginfo('url'); ?>/wp-content/plugins/post-gallery/uploads/<?php echo $post_images[$i]['image_location']; ?>&amp;w=85&amp;h=85&amp;zc=1" alt="Thumbnail" />
							</a>
						</div>
						<div class="image_caption"><?php echo $post_images[$i]['image_caption']; ?></div>
					</li>
				<?php endfor ?>
			</ul>
		</div>
	<?php endif ?>

*/

/*  Copyright 2009 Jonathan Christopher (email: jonathan@mondaybynoon.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    For a copy of the GNU General Public License, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


global $post_gallery_plugin_url;
$post_gallery_plugin_url = WP_PLUGIN_URL;

require_once 'post-gallery.php';
require 'post-gallery-getter.php';

register_activation_hook( __FILE__, array('Post_gallery','post_gallery_install'));

add_action('admin_head', array('Post_gallery','post_gallery_javascript'));
add_action('admin_head', array('Post_gallery','post_gallery_css'));
add_action('admin_footer', array('Post_gallery','post_gallery_load_existing_images_javascript'));

add_action('admin_menu', array('Post_gallery','post_gallery_init_gallery'));
add_action('admin_menu', array('Post_gallery','post_gallery_init_admin_menu'));

add_action('save_post', array('Post_gallery','post_gallery_save_images'));

?>