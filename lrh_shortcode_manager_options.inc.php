<?php
/* This is the options form/page for the ShortCode Manager
*/


function lrhsim_manage_options()
{
	global $lrhsim_info;

	// Ensure we can see shortcodes object
	global $shortcode_tags;

	//need the options
	$opt=lrhsim_getoptions();


	//Build list of all codes from active codes and inactive codes
	$tags=array_merge(array_keys($shortcode_tags),array_values($opt['inactive']));
	$tags=array_unique($tags);
	//Sort alphabetically
	sort($tags);

	//Update data on submission
	if (!empty($_POST['submit'])) {
		if (wp_verify_nonce($_POST[clrhsim_fld_NONCE],plugin_basename(__FILE__))) {
			$codes=array();
			foreach($tags as $sctag) {
				//If not listed or not checked, add to deactivated list
				if (!isset($_POST["lrhsimsc_$sctag"]) || (!$_POST["lrhsimsc_$sctag"])) {
					$codes[]=$sctag;
				}
			}
			$opt['inactive']=$codes;
			update_option(clrhsim_OPTIONS,$opt);
			echo "<p>".__('Changed saved.')."</p>\r\n";
		}
	}


	if ($tags) {
		echo '<form method="post" action="';
		echo $_SERVER['PHP_SELF'];
		echo '?page=';
		echo plugin_basename(__FILE__);
		echo '">';

		wp_nonce_field(plugin_basename( __FILE__ ),clrhsim_fld_NONCE);

		echo '<table class="widefat">';
		echo '<thead><tr>';
		echo '<th>'.__('Active',clrhsim_id).'</th>';
		echo '<th>'.__('Name',clrhsim_id).'</th>';
		echo '<th>'.__('Description',clrhsim_id).'</th>';
		echo '<th>'.__('Self<br>Closing',clrhsim_id).'</th>';
		echo '<th>'.__('Required Parameters',clrhsim_id).'</th>';
		echo '<th>'.__('Optional Parameters',clrhsim_id).'</th>';
		echo "</tr></thead>\r\n";
		echo '<tbody>';

		$trs='x';//column class
		$divs="style='padding-left:8px;text-indent:-8px;'";//styling for parameters

		foreach($tags as $shortcode) {
			//Get additional information about this tag
			$info=lrhsim_getshortcodeinfo($shortcode);

			//Is shortcode active?
			$active=(!in_array($shortcode,$opt['inactive']));

			//Each row has an alternating class
			$trs=(''==$trs)?' class="alternate"':'';
			echo "<tr$trs>";

			//Column is checkbox
			echo "<td><input type='checkbox' name='lrhsimsc_$shortcode'";
			if ($active) echo ' checked';
			echo '></td>';

			//Column is name and shortcode (if not the same)
			echo '<td>';
			$name=$info[cSHORTCODE_NAME];
			if ($name!=$shortcode) echo "$name<br>";
			echo "<b>[$shortcode]</b></td>";

			//Column is description
			$desc=esc_attr(nl2br($info[cSHORTCODE_DESC]));
			echo "<td>$desc</td>";

			//Column is self closing indicator
			$self=$info[cSHORTCODE_SELFCLS];
			echo '<td>'.(('u'===$self)?'':(($self)?'Yes':'No')).'</td>';

			//Column is required parameters
			$p=$info[cSHORTCODE_RPARAMS];
			echo '<td>';
			if ($p) {
				foreach($p as $fld=>$hint) {
					echo "<div $divs><b>$fld</b>= $hint</div>";
				}
			}
			echo '</td>';

			//Column is optional parameters
			$p=$info[cSHORTCODE_OPARAMS];
			echo '<td>';
			if ($p) {
				foreach($p as $fld=>$hint) {
					echo "<div $divs><b>$fld</b>= $hint</div>";
				}
			}
			echo '</td>';

			echo "</tr>\r\n";

		}
		echo '</tbody>';
		echo '</table>';
		echo '<br><input type="submit" name="submit" class="button" value="Save Changes">';
		echo '</form>';
	} else {
		echo "<p>".__('Sorry, no shortcodes found',clrhsim_id).'<p>';
	}

	echo '<p>'.__('Version',clrhsim_id).' '.$lrhsim_info['version'].'</p>';
}


echo '<div class="wrap">';
echo '<h1>'.__('Shortcodes',clrhsim_id).'</h1>';

if (current_user_can('manage_options')) {
	lrhsim_manage_options();
} else {
	echo '<p>Something has gone terribly wrong.</p>';
}

echo "</div>";//wrap

?>