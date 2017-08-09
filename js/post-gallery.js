// ================================
// = GLOBAL VARIABLE DECLARATIONS =
// ================================
var post_gallery_count 		= 1;
var post_gallery_max 		= 1;
var post_image_index		= 0;
var controls 				= '';
var existing_images_count 	= 0;
var reference_url			= '';






/**
 * Generates unique markup for the Order field as well as the 'New' and 'Delete' links
 * @param {Number} count Current index of the Post Image
 */
function post_gallery_generate_controls_markup(count)
{
	controls = '<div class="post_gallery_controls"><div class="post_gallery_textfield"><label for="post_gallery_image_order_' + count + '">Order</label><input type="text" class="post_gallery_order" name="post_gallery_image_order_' + count + '" id="post_gallery_image_order_' + count + '" value="' + count + '" /></div><p><a class="post_image_duplicate" href="#">New Image</a> <a class="post_image_delete" href="#">Delete This Image</a></p></div>';
}






/**
 * Generates applicable markup for the image fields themselves 
 */
function post_images_generate_image_markup()
{
	return '<div class="post_gallery_image_wrapper"><div class="post_gallery_fields"><div class="post_gallery_textfield"><label for="post_gallery_image_title_'+post_image_index+'">Title</label><input type="text" id="post_gallery_image_title_'+post_image_index+'" name="post_gallery_image_title_'+post_image_index+'" value="" /></div><div class="post_gallery_textfield"><label for="post_gallery_image_caption_'+post_image_index+'">Caption</label><input type="text" id="post_gallery_image_caption_'+post_image_index+'" name="post_gallery_image_caption_'+post_image_index+'" value="" /></div><div class="post_gallery_upload post_gallery_upload_file"><a href="#" class="post_gallery_upload_link" id="post_gallery_upload_'+post_image_index+'">Upload</span></div></div><div class="post_gallery_image_thumbnail" id="post_gallery_image_thumbnail_'+post_image_index+'"></div><input type="hidden" style="display:none;" id="post_gallery_image_location_'+post_image_index+'" name="post_gallery_image_location_'+post_image_index+'" value="Unknown" /></div>';
}






/**
 * Duplicates WordPress' generation of markup for add_meta_box() 
 */
function post_images_generate_image_markup_with_meta_box()
{
	return '<div id="post_gallery_section'+post_image_index+'" class="postbox post_gallery_image"><div class="handlediv" title="Click to toggle"><br /></div><h3 class="hndle"><span>Post Image</span></h3><div class="inside">'+post_images_generate_image_markup()+'</div></div>';
}






/**
 * Injects the markup provided by WordPress' add_meta_box, including required fields
 */
function post_gallery_create_image_duplicate()
{
	post_gallery_count++;
	post_gallery_max++;
	post_image_index++;
	jQuery('.post_gallery_image:last').after(post_images_generate_image_markup_with_meta_box());
	post_gallery_hook_upload(reference_url,post_image_index);
	post_gallery_generate_controls_markup(post_image_index);
	jQuery('#post_gallery_section'+post_image_index+' .post_gallery_image_wrapper').after(controls);
	post_gallery_update_images_count();
	post_gallery_hook_controls();
}






/**
 * Removes an existing Image entry if there's more than one. If only one image is left, we just clear the input values
 */
function post_gallery_create_image_delete()
{
	if(post_gallery_count==1)
	{
		jQuery('#post_gallery_delete_hook').attr('id','');
		// reset all inputs
		jQuery('.post_gallery_image:first input').attr('value','');
		jQuery('input.post_gallery_order:first').attr('value','1');
		jQuery('.post_gallery_image_thumbnail:first').empty();
		post_gallery_update_images_count();
		post_gallery_hook_controls();
	}
	else
	{
		jQuery('#post_gallery_delete_hook').parent().parent().parent().parent().remove();
		post_gallery_update_images_count();
		post_gallery_hook_controls();
	}
	
}






/**
 * Removes click events from 'duplicate' and 'delete' anchors
 */
function post_gallery_unhook_controls()
{
	jQuery("a.post_image_duplicate").unbind('click');
	jQuery("a.post_image_delete").unbind('click');
}






/**
 * Binds the click events for 'duplicate' and 'delete' anchors
 */
function post_gallery_hook_controls()
{
	post_gallery_unhook_controls();
	jQuery('a.post_image_duplicate').click(function()
	{
		post_gallery_create_image_duplicate();
		return false;
	});
	jQuery('a.post_image_delete').click(function()
	{
		jQuery(this).attr('id','post_gallery_delete_hook');
		post_gallery_create_image_delete();
		return false;
	});
}






/**
 * Loads the existing Images from the WordPress database via JSON
 * @param {String} target_url The URL to the plugins of the current WordPress installation
 * @param {Number} post_id The WordPress post ID for which you would like to retrieve Images
 */
function post_gallery_load_existing_images(target_url,post_id)
{
	// we need to set the plugin_url
	reference_url = target_url;
	// first we need to get an array of Note IDs
	jQuery.getJSON(target_url+'/post-gallery/post-gallery-get-images-for-post.php', { id: post_id },
		function(json){
			existing_images_count = json.length;
			
			if(existing_images_count>0)
			{
				// we know the total number of notes, let's prep the textareas
				for (var i=1; i <= existing_images_count; i++) {
					post_image_index++;
					jQuery('#post_gallery_section'+i).addClass('post_gallery_image');
					post_gallery_count = i;
					jQuery('#post_gallery_section'+i+' .inside').append(post_images_generate_image_markup());
					// fill in the title
					jQuery('#post_gallery_image_title_'+i).attr('value',json[i-1].image_title);
					// fill in the caption
					jQuery('#post_gallery_image_caption_'+i).attr('value',json[i-1].image_caption);
					// fill in the location
					jQuery('#post_gallery_image_location_'+i).attr('value',json[i-1].image_location);
					// fill in the thumbnail
					jQuery('#post_gallery_image_thumbnail_'+i).empty().append('<img src="' + target_url + '/post-gallery/thirdparty/phpthumb/phpThumb.php?src=' + target_url + '/post-gallery/uploads/' + json[i-1].image_location + '&w=125&h=125&zc=1&q=90" alt="" />');
					post_gallery_generate_controls_markup(i);
					jQuery('#post_gallery_section'+i+' .post_gallery_image_wrapper').after(controls);
					post_gallery_hook_upload(target_url,i);
				};
				
				// we MUST update this count, or nothing will get saved
				post_gallery_update_images_count();
				
				// need to hook all of our newly created controls...
				post_gallery_hook_controls();
				
				// finally we'll take care of the input tracking we need
				post_gallery_prep_input_trackers();
			}
		});
}






/**
 * Counts the number of Images on the page and sets the proper input values in the DOM
 */
function post_gallery_update_images_count()
{
	post_gallery_count = parseInt(jQuery('.post_gallery_image_wrapper').length);
	jQuery('#post_gallery_image_count').attr('value',post_gallery_count);
	if(post_gallery_max<post_gallery_count)
	{
		post_gallery_max = post_gallery_count;
	}
	jQuery('#post_gallery_max').attr('value',post_gallery_max);
}






/**
 * Injects fields we need for tracking stats about existing Post Images 
 */
function post_gallery_prep_input_trackers()
{
	// we need to track how many Images we've got
	jQuery('#post').append('<input type="hidden" name="post_gallery_image_count" id="post_gallery_image_count" value="'+post_gallery_count+'" />');
	jQuery('#post').append('<input type="hidden" name="post_gallery_max" id="post_gallery_max" value="'+post_gallery_count+'" />');
}






/**
 * Initializes functionality. 
 * @param {String} plugin_url The URL at which this plugin resides
 */
function post_gallery_init_images(plugin_url)
{
	if(jQuery('#post').length!=0)
	{
		for (var i=1; i <= post_gallery_count; i++)
		{
			jQuery('#post_gallery_section'+i+'').addClass('post_gallery_image');
			post_gallery_generate_controls_markup(i);
			jQuery('#post_gallery_section'+i+' .post_gallery_image_wrapper').after(controls);
		};
		post_image_index = post_gallery_count;
		post_gallery_update_images_count();	
		post_gallery_max = post_gallery_count;
		post_gallery_prep_input_trackers();
		post_gallery_hook_controls();
	}
}






/**
 * Hooks all the upload links and hijacks the click event to trigger our AJAX upload
 * @param {String} plugins_url The URL where this plugin is stored
 * @param {Number} index Index of upload link occurence 
 */
function post_gallery_hook_upload(plugins_url,index)
{
	// need to set our variable...
	reference_url = plugins_url;
	new Ajax_upload('#post_gallery_upload_'+index, {
		action: plugins_url+'/post-gallery/post-gallery-upload-handler.php',
		name: 'userfile',
		onComplete: function(file, response) 
			{
				var new_image = response.split("|");
				var new_image_url = new_image[0] + "/post-gallery/uploads/" + new_image[1];
				var phpthumb_url = new_image[0] + '/post-gallery/thirdparty/phpthumb/phpThumb.php?src=' + new_image_url + '&w=125&h=125&zc=1&q=90';
				jQuery('#post_gallery_image_location_'+index).attr('value',new_image[1]);
				jQuery('#post_gallery_image_thumbnail_'+index).empty().append('<img src="' + phpthumb_url + '" alt="" />');
			},
		onSubmit : function(file , ext){
			if (!(ext && /^(jpg)$/.test(ext)))
			{
				alert('Error: invalid file extension');
				// cancel upload
				return false;
			}
			else
			{
				jQuery('#post_gallery_image_thumbnail_'+index).empty().append('<img src="' + plugins_url + '/post-gallery/images/loading.gif" alt="" />');
			}
		}
	});
}