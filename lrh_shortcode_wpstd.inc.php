<?php
/* Information for some of the built-in WordPress standard shortcodes
 *
 * This information was gleaned from the codex
*/


// Using this action hook, we will only register the sim filters
// when there is a possibility of them being used
add_action('sim_','lrhsim_action_register_shortcode_information');
function lrhsim_action_register_shortcode_information()
{
	// The priority of 5 allows a simple override by a default filter
	add_filter('sim_audio','filter_sim_audio',5);
	add_filter('sim_video','filter_sim_video',5);
	add_filter('sim_caption','filter_sim_caption',5);
	add_filter('sim_wp_caption','filter_sim_caption',5);
	add_filter('sim_gallery','filter_sim_gallery',5);
}


function filter_sim_audio($information)
{
	$informatino[cSHORTCODE_NAME]='Audio Media';
	$information[cSHORTCODE_DESC]='Allows you to embed audio files.';
	$information[cSHORTCODE_SELFCLS]=true;
	$information[cSHORTCODE_OPARAMS]=
		array('src'=>'name of media file'
			,'mp3'=>'name of mp3 fallback file'
			,'m4a'=>'name of m4a fallback file'
			,'ogg'=>'name of ogg fallback file'
			,'wav'=>'name of wav fallback file'
			,'wma'=>'name of wma fallback file'
			,'loop'=>'loop the media'
			,'autoplay'=>'automatically play the media'
			,'preload'=>'default "none"'
		);
	return $information;
}

function filter_sim_video($information)
{
	$informatino[cSHORTCODE_NAME]='Video Media';
	$information[cSHORTCODE_DESC]='Allows you to embed video files.';
	$information[cSHORTCODE_SELFCLS]=true;
	$information[cSHORTCODE_OPARAMS]=
		array('src'=>'name of media file'
			,'mp4'=>'name of mp4 fallback file'
			,'m4v'=>'name of m4v fallback file'
			,'webm'=>'name of webm fallback file'
			,'ogv'=>'name of ogv fallback file'
			,'wmv'=>'name of wmv fallback file'
			,'flv'=>'name of fla fallback file'
			,'poster'=>'name of image to display as placeholder'
			,'loop'=>'loop the media'
			,'autoplay'=>'automatically play the media'
			,'preload'=>'default "metadata"'
			,'height'=>'height of media'
			,'width'=>'width of media'
		);
	return $information;
}


function filter_sim_caption($information)
{
	$informatino[cSHORTCODE_NAME]='Wrap captions around content';
	$information[cSHORTCODE_DESC]='Allows you wrap captions around content, primarily'
		.' used with individual images';
	$information[cSHORTCODE_SELFCLS]=false;
	$information[cSHORTCODE_RPARAMS]=
		array('caption'=>'text of the caption'
			,'width'=>'width of the caption'
			);
	$information[cSHORTCODE_OPARAMS]=
		array('id'=>'a unigue HTML ID you assign to use with CSS'
			,'align'=>'alignment of the caption: aligncenter, alignright, alignleft, default is alignnone'
		);
	return $information;
}


function filter_sim_gallery($information)
{
	$informatino[cSHORTCODE_NAME]='Image Gallery';
	$information[cSHORTCODE_DESC]='Allows you to add one or more image galleries';
	$information[cSHORTCODE_SELFCLS]=true;
	$information[cSHORTCODE_OPARAMS]=
		array('orderby'=>'sort order of the thumbnails: title, post_date, rand, ID, default is menu_order '
			,'order'=>'direction of sort: ASC (ascending) or DESC (descending)'
			,'columns'=>'number of columns, default is 3'
			,'id'=>'id of post to retrieve images from'
			,'size'=>'size of the thumbnail display: medium, large, full, default is thumbnail'
			,'itemtag'=>'XHTML tag to enclose each item, default "dl"'
			,'icontag'=>'XHTML tag to enclode each icon, default "dt"'
			,'captiontag'=>'XHTML tag to enclose each caption, default "dd"'
			,'link'=>'set to "file" to link to image file, default is image permalink'
			,'include'=>'list of attachment IDs to show'
			,'exclude'=>'list of attachment IDs to not show'
		);
	return $information;
}


// Not done
//[embed]

