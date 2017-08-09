=== Post Gallery ===
Contributors: jchristopher
Tags: post, posts, images, gallery
Requires at least: 2.7
Tested up to: 2.8
Stable tag: 1.0.6

Provides ability to add any number of images to a post

== Description ==

**PLEASE NOTE:** Post Gallery has been officially deprecated and [Attachments](http://wordpress.org/extend/plugins/attachments/) now replaces it. Post Gallery will continue to work if you're a current user, but it is recommended that Attachments be used instead.

-----

This WordPress plugin gives you the ability to append any number of images (along with titles and captions) to your Posts (and Pages). Existing images can be resorted and removed at any time.

View more details as well as a screencast at [the official Plugin page](http://mondaybynoon.com/wordpress-post-gallery/ "WordPress Post Gallery Official plugin page")

Compatible with WordPress 2.7+

Updated July 6, 2009: Fixed bug where Upload button failed to invoke the browse dialog. Changed all file paths to be absolute.

Updated March 18, 2009: The uploads directory is now automatically created during activation. Now includes the option to add a Gallery to Pages via new Settings page.

Updated February 17, 2009: Now correctly returns an accurate record of images per post. There was a bug present which possibly returned an empty record.

== Installation ==

1. Download the plugin and unzip it into your /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' admin menu in WordPress

To retrieve an associative array containing all Gallery images for a post, use the following within The Loop:

`<?php $post_images = post_gallery_get_images(); ?>`

You will be provided an associative array with which to work in your template:

`<?php $total_images = sizeof($post_images); ?>
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
<?php endif ?>`

== Screenshots ==

1. Post Image