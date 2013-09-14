<?php
/* Builds the metabox for display */

/**
 * Displays the Shortcodes list in a metabox

 * @uses $shortcode_tags
 * @uses wp_nonce_field()
 * @uses lrhsim_getshortcodeinfo()
**/
function lrhsim_meta_box_shortcodes($post)
{
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ),clrhsim_fld_NONCE);
	wp_nonce_field('closedpostboxes','closedpostboxesnonce',false);

	global $shortcode_tags;  //WordPress active shortcode list

	echo '<div class="lrhsim_items_container">';

	//Use $shortcode_tags as the master list
	//so only 'active' shortcodes will show here
	//Look for additional information by calling lrhsim_getshortcodeinfo()

	//Build a javascript data object for later use
	// Object is indexed by id which is the assigned id of the shortcode
	// Each record has a before and after field
	// The insert is made and the cursor left between them
	$jj=array();

	//format of a parameter
	$pfmt='<div style="padding-left:12px;text-indent:-8px;"><b>%1$s</b> = %2$s</div>';

	//loop through all the active shortcodes
	foreach($shortcode_tags as $sctag=>$f) {
		//get the additional information
		$info=lrhsim_getshortcodeinfo($sctag);
		$name=$info[cSHORTCODE_NAME];
		$desc=esc_attr($info[cSHORTCODE_DESC]);
		$desc=str_replace(array("\r\n","\r","\n"),'<br>',$desc);
		$self=$info[cSHORTCODE_SELFCLS];

		//add to javascript data, will be list of data fields
		$a=array();

		//before part
		$s="[$sctag";
		// required params
		$p=$info[cSHORTCODE_RPARAMS];
		if (count($p)) {
			foreach($p as $fld=>$hint) {
				$s.=" $fld=";
				$desc.=sprintf($pfmt,$fld,$hint);
			}
		}
		if ($self) $s.=" "; else $s.="]";
		$a[]="b:'$s'";

		//after part
		if ($self) $s="]"; else $s="[/$sctag]";
		$a[]="a:'$s'";

		//one data line for the javascript object
		$scid=str_replace('-','_',$sctag);
		$jj[]="$scid:{".implode(',',$a)."}\r\n";

		// optional params
		$p=$info[cSHORTCODE_OPARAMS];
		if (count($p)) {
			foreach($p as $fld=>$hint) {
				$desc.=sprintf($pfmt,$fld,$hint);
			}
		}

		echo '<div class="lrhsim_item_container">';
		echo "<span id='$scid' class='lrhsim_item_button'>insert</span>";
		echo "<span class='lrhsim_item_name'>$name</span>";
		echo "<span class='lrhsim_item_desc_hide'>$desc</span>";
		echo '</div>';
	}
	echo "</div><!-- lrhsim_items_container -->\n";

	//Output the needed javascript
	echo "<script type='text/javascript'>\n";
	//data object
	echo "var lrhsim_data1={\n".implode(',',$jj)."};\n";

	//When document is ready, we install some event handlers...
	//Uses the lrh_shortcode_js.js code
	echo "jQuery(document).ready("
		."function(){lrhsim_js_init()}"
		.");\n";
	echo "</script>\n";
}



?>