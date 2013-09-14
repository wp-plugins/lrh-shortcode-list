<?php
/**
 * Shortcode Information Manager
 *
 * This helps the display boxes to grab additional information
 * if provided by shortcode developers.
 * The additional information includes description, is it self-closing,
 * the required parameters
 * This also allows an admin to deactivate a shortcode
 * Activation is managed by this unit also
 *
 * Providing Shortcode Information
 *  To provide additional information to the helper, create a filter
 *  that updates/returns a array of data
 * Example:
 * add_filter('sim_snapfunc','filter_sim_snapfunc');
 * function filter_sim_snapfunc($information)
 * {
 *   $information[cSHORTCODE_NAME]='Web Site Snap Shot';
 *   $information[cSHORTCODE_DESC]='Allows you to embed a snapshot of a web site';
 *   $information[cSHORTCODE_SELFCLS]=true;
 *   $information[cSHORTCODE_RPARAMS]=
 *      array('url'=>'url of site');
 *   $information[cSHORTCODE_OPARAMS]=
 *      array('alt'=>'description'
 *           ,'w'=>'width'
 *           ,'h'=>'height'
 *           );
 *   return $information;
 * }
 * array $info has some required fields:
 *   tag - the shortcode tag, informational only
 *   name - friendly name
 * and optional fields
 *   desc - longer friendly info
 *   selfcls - true if of form: [code], false if of form: [code][/code]
 *   rparams - array of required parameter names, and short-descriptions
 *     - each row in array is a key of the field parameter with
 *       the data being a short description about field value
 *   oparams - array of optional parameter names and short-descriptions
 *     - same format as rparams
 *
 * Shortcodes are assumed ACTIVE unless a INACTIVE option
 * for it is found
**/

// Name space for the plugin
define('clrhsim_id','lrh_sim1');

// ID for metabox
define('clrhsim_mbid_shortcodes',clrhsim_id.'_mb_shortcodes');

// Field ID for nonce
define('clrhsim_fld_NONCE',clrhsim_id.'_field_nonce');

// Fields in a shortcode information array
// Required, or what's the point?
define('cSHORTCODE_TAG','scTag');
define('cSHORTCODE_NAME','scName');
// Optional
define('cSHORTCODE_DESC','scDesc');
define('cSHORTCODE_SELFCLS','scSelfCls'); //default no
define('cSHORTCODE_RPARAMS','scReqP'); //array of required parameters
define('cSHORTCODE_OPARAMS','scOptP'); //array of optional parameters

// SIM option to store options and list of deactivated shortcodes
define('clrhsim_OPTIONS',clrhsim_id.'_options');
/* This is an array:
  'options'=>array()  options specific to this manager
 ,'inactive'=>array('shortcodename','etc...')  list of inactive shortcodes
*/

// Global stores various info needed by plugin
global $lrhsim_info;
$lrhsim_info=array();


/**
 * Get the ShortCode Information Manager options
 *
 * @uses get_option()
**/
function lrhsim_getoptions()
{
	$opt=get_option(clrhsim_OPTIONS);
	if (FALSE===$opt) {
		//set up default options
		$opt=array('options'=>array(),'inactive'=>array());
	}
	return $opt;
}

/**
 * Create an sim data array with default settings for a shortcode

 * @param string $aTag Shortcode tag to be set up
**/
function lrhsim_shortcodeinfo_default($aTag)
{
	return array
		(cSHORTCODE_TAG=>$aTag
		,cSHORTCODE_NAME=>$aTag
		,cSHORTCODE_SELFCLS=>'u' //unknown
		,cSHORTCODE_DESC=>__('No information available',clrhsim_id)
		,cSHORTCODE_RPARAMS=>array()
		,cSHORTCODE_OPARAMS=>array()
		);
}

/**
 * Get info for a shortcode
 *
 * Calls the appropriate filter to get the information
 * for the requested shortcode tag
 * The filter is 'sim_shortcodetag'
 *
 * @param string $aTag Shortcode tag to get information about
 * @uses lrhsim_shortcodeinfo_default()
 * @uses apply_filter()
**/
function lrhsim_getshortcodeinfo($aTag)
{
	// Set defaults
	$info=lrhsim_shortcodeinfo_default($aTag);
	// Call the filter
	$info=apply_filters("sim_$aTag",$info);
	// force the tag, in case a funny guy tried to change it
	$info[cSHORTCODE_TAG]=$aTag;
	// Ready to go
	return $info;
}

/**
 * Check for shortcodes to make inactive
 *
 * Because we cannot guarantee the order of shortcodes handlers
 * being loaded, and that information is loaded for each shortcode
 * This method is called to make inactive any shortcodes that have
 * been marked as inactive by the admin settings
 * Expected to be called during 'plugins_loaded' action
 *
 * @uses lrhsim_getoptions()
 * @uses remove_shortcode()
**/
function lrhsim_action_shortcodes_checkinactive()
{
	$opt=lrhsim_getoptions();
	if ($opt['inactive']) {
		foreach($opt['inactive'] as $k) {
			remove_shortcode($k);
		}
	}
}

/* Add to admin options page
 *
 * @uses add_options_page()
**/
function lrhsim_action_option_menu_shortcodes()
{
	$d=dirname(plugin_basename(__FILE__));
	add_options_page
		(__('Shortcode Manager',clrhsim_id)
		,__('Shortcodes',clrhsim_id)
		,'manage_options'
		,$d.'/lrh_shortcode_manager_options.inc.php'
		);
}

function lrhsim_filter_plugin_action_links($links)
{
//echo '<pre>YES='.plugin_basename(__FILE__).'</pre>';

	$d=dirname(plugin_basename(__FILE__));
	$link='<a href="'
		.get_admin_url(null,'options-general.php?page='.$d.'/lrh_shortcode_manager_options.inc.php')
		.'">Settings</a>';
	array_unshift($links,$link);
	return $links;
}


/**
 * Add box to post edit screens
 *
 * @uses $lrhsim_info
 * @uses add_meta_box()
 * @uses apply_filters()
**/
function lrhsim_action_add_meta_boxes()
{
	global $lrhsim_info;
	require dirname(__FILE__).'/lrh_shortcode_metabox.inc.php';

	$posttypes=array('post','page');
	//Allow customizing
	$posttypes=apply_filters('lrhsim_showforposttypes',$posttypes);
	//Add a box for each type
	foreach($posttypes as $pt) {
		add_meta_box
			(clrhsim_mbid_shortcodes
			,__('Shortcodes',clrhsim_id).' '.$lrhsim_info['version']
			,'lrhsim_meta_box_shortcodes'
			,$pt
			,'advanced'
			,'default'
			);
	}
}


/**
 * On un-installtion, clear out options
 *
 * @uses: delete_option()
**/
function lrhsim_hook_uninstall()
{
	delete_option(clrhsim_OPTIONS);
}


/**
 * Sets up Shortcode Information Manager for admin use
 *
 * @uses $lrhsim_info
 * @uses add_action()
 * @uses add_filter()
 * @uses wp_register_style()
 * @uses wp_enqueue_style()
 * @uses plugins_url()
**/
function lrhsim_action_adminsetup()
{
	global $lrhsim_info;
	$ver=$lrhsim_info['version'];

	// Show settings link in plugins list
	add_filter('plugin_action_links_'.$lrhsim_info['basename'],'lrhsim_filter_plugin_action_links');

	// Add box to post edit screens
	add_action('add_meta_boxes','lrhsim_action_add_meta_boxes');

	//Hook in styles for metabox
	wp_register_style('lrhsim-metabox-style',plugins_url('/lrh_shortcode_styles.css',__FILE__),array(),$ver);
	wp_enqueue_style('lrhsim-metabox-style');

	//Hook in javasascript for metabox
	wp_register_script('lrhsim-metabox-js',plugins_url('/lrh_shortcode_js.js',__FILE__),array('jquery'),$ver);
	wp_enqueue_script('lrhsim-metabox-js');

	//if it's there, include standard information
	$fn=dirname(__FILE__).'/lrh_shortcode_wpstd.inc.php';
	if (file_exists($fn)) include($fn);

	//Calling this action let's clients register their shortcode information
	//only when if may actually be used, i.e. this plugin is loaded in admin mode
	do_action('sim_');
}

/**
 * Sets up the Shortcode Information Manager
 *
 * @uses $lrhsim_info
 * @uses add_action()
 * @uses register_uninstall_hook()
**/
function lrhsim_setup($aFile)
{
	//Perform all the "setup" for this plugin

	//Store version for later users
	global $lrhsim_info;
	$lrhsim_info['version']='1.2.0';
	$lrhsim_info['basename']=plugin_basename($aFile);

	// After plugins are loaded, check to deactivate shortcodes
	// The high priority number is an attempt to run last
	add_action('plugins_loaded','lrhsim_action_shortcodes_checkinactive',99);

	// Call set up if needed for admin
	add_action('admin_init','lrhsim_action_adminsetup');

	// If plugin gets uninstalled, do some cleanup
	register_uninstall_hook(__FILE__,'lrhsim_hook_uninstall');

	// Here becuase cannot be called from admin_init action
	// Add to admin menu options, if privileged
	add_action('admin_menu','lrhsim_action_option_menu_shortcodes');
}

?>