<?php

$trans_slideshow = array(	'create' => 'Slideshow Manager',
							'intro'	=> 'Use this page to create a slideshow of images. Each slideshow which you create is stored in an XML file. PodHawk treats this file as a media file, just like a normal audio or video file, and displays the slideshow on your webpage in place of an audio or video player.',
							'description' => "This plugin creates a new backend page \"slideshow\" (in the \"Postings\" menu) which enables all users with edit_own permissions to create slideshows. The slideshow is stored in an XML file which can be used as the media file for a posting.",
							'slideshow_name' => 'Name for this slideshow',
							'slideshow_name_help' => "eg 'myslideshow'. Give each of your slideshows a different name.",
							'thumb_or_text' => 'Thumbnails or a text link?',
							'text_link' => 'Text link, no thumbnails',
							'thumb1' => 'Thumbnail for first image only',
							'thumball' => 'Thumbnails for all images',
							'thumb_text_help' => 'Users can view the slideshow by clicking on a text link, or by clicking on a thumbnail image. You can have thumbnails for all the images in your slideshow, or just for the first image.',
							'txt' => 'Textlink',
							'txt_help' => 'What should the text link say?',
							'thumb_size' => 'Thumbnail size',
							'thumb_width_height' => 'Is this the width of each thumbnail image, or the height, or both (square images)?',
							'width' => 'Width',
							'height' => 'Height',
							'square' => 'Square',
							'width_height_help' => 'Width - the height of the thumbnail adjusts automatically to the width you have chosen. Height - the width adjusts automatically to the height you have chosen. Square - a square image with the height and width you have chosen.',
							'image_row_help' => "For each image that you want in your slideshow, enter the name of an image in your images folder; or the web address (starting http://... or https://) of an image on the web. Podhawk will download any external images to your 'images' folder. If you want a caption to display below the image, enter it also. Add, amend or remove rows as necessary to create the slideshow you want. Then click \"Make slideshow!\" PodHawk will create an XML file containing your slideshow and place it in your \"Upload\" folder.",
							'name_web' => 'Name or web address',
							'remove_image' => 'Remove this image',
							'add_row' => 'Add a new row',
							'preview_slideshow' => 'Preview this slideshow!',
							'upfolder' => 'Upload folder',
							'upfolder_contents' => 'These slideshow files are in your upload folder',
							'file_edit' => 'Edit this file',
							'file_delete' => 'Delete this file',
							'upload_empty' => 'There are no slideshow files in your upload folder.',
							'slide_posts' => 'Slideshows in posts',
							'slideposts_links' => 'These posts are linked to slideshows',
							'post_title' => 'Post title',
							'slidename' => 'Slideshow name',
							'file_name' => 'File name',
							'post_edit' => 'Edit post',
							'slideshow_edit' => 'Edit slideshow',
							'no_slideposts' => 'I cannot find any postings with slideshow files.',
							'create_slideshow' => 'Create Slideshow'							
						
);

$trans_menu = array ('slideshow' => 'Slideshow');

?>
