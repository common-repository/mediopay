<?php
/*
 * Plugin Name: MedioPay
 * Description: This plugin allows PayWalls and Tip Button for Wordpress
 * Version: 1.9
 * Requires at least: 4.7
 * Requires PHP: 6.2
 * Author: MedioPay
 * Author URI: https://mediopay.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


//
// Starting the plugin: Activate and Deactive, load files and scripts
//

// Activation, Deactivation, Uninstall
register_uninstall_hook( 'mediopay/mediopay.ph', 'uninstall_mediopay' );

require('functions/mp_basic_functions.php');
require('functions/mp_editor_functions.php');
require('functions/mp_settings_functions.php');
require('classes/mp_post_data.php');
require('functions/mp_html_functions.php');
require('functions/mp_api_functions.php');
//require('functions/mp_ajax.php');


function mp_load_post_data() {
	
}
add_action( 'init', 'mp_load_post_data' );


register_deactivation_hook( 'mediopay/mediopay.php', 'mediopaydeactivate' );
register_activation_hook( 'mediopay/mediopay.php', 'mediopayactivate' );
register_activation_hook( 'mediopay/mediopay.php', 'mediopayactivate_data' );
add_action( 'wp_enqueue_scripts', 'mediopay_add_scripts' );
add_action( 'admin_enqueue_scripts', 'mediopay_add_admin_scripts' );

// Register Option for MedioPay

add_action( 'admin_init', 'mediopay_register_settings' );
add_action('admin_menu', 'mediopay_register_options_page');


//
// save settings a user made in the dashboard
//

if (isset($_POST['settings'])) {
if(isset($_POST['MedioPay_address']) OR 
	isset($_POST['MedioPay_currency']) OR 
	isset($_POST['MedioPay_deactivate_metadata']) OR 
	isset($_POST['MedioPay_sharing_quote']) OR 
	isset($_POST['MedioPay_ref_quote']) OR 
	isset($_POST['MedioPay_fixed_amount']) OR 
	isset($_POST['MedioPay_fixed_amount_tips']) OR 
	isset($_POST['MedioPay_fixed_thank_you']) OR 
	isset($_POST['MedioPay_bar_color']) OR 
	isset($_POST['MedioPay_paywall_msg']) OR 
	isset($_POST['MedioPay_editable_tips']) OR 
	isset($_POST['MedioPay_address_2']) OR 
	isset($_POST['MedioPay_second_address_share']) OR 
	isset($_POST['MedioPay_link_color']) OR
	isset($_POST['MedioPay_align_left']) OR
	isset($_POST['MedioPay_threshold']) OR
	isset($_POST['MedioPay_paywhatuwant'])		
  ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'mediopay';
		
		if(isset($_POST['MedioPay_address'])) {
			$newaddress = sanitize_text_field($_POST["MedioPay_address"]);
		$newaddress = array( 'address' => $newaddress );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newaddress,$data_where);
	}
	if(isset($_POST['MedioPay_currency'])) {
		$newcurrency = sanitize_text_field($_POST['MedioPay_currency']);
		/*global $wpdb;
		$table_name = $wpdb->prefix . 'mediopay';*/
		$newcurrency = array( 'currency' => $newcurrency );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newcurrency,$data_where);
	}
	if(isset($_POST['MedioPay_sharing_quote'])) {
		$newsharing = sanitize_text_field($_POST["MedioPay_sharing_quote"]);
		/*global $wpdb;
		$table_name = $wpdb->prefix . 'mediopay';*/
		$newsharing = array( 'sharingQuote' => $newsharing );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newsharing,$data_where);
	}
	if(isset($_POST['MedioPay_ref_quote'])) {
		$newref = sanitize_text_field($_POST["MedioPay_ref_quote"]);
		global $wpdb;
		$table_name = $wpdb->prefix . 'mediopay';
		$newref= array( 'ref' => $newref );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newref,$data_where);
	}
	if(isset($_POST['MedioPay_fixed_amount'])) {
		$newfixed = sanitize_text_field($_POST["MedioPay_fixed_amount"]);
		/*global $wpdb;
		$table_name = $wpdb->prefix . 'mediopay';*/
		$newfixed = array( 'fixedAmount' => $newfixed );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newfixed,$data_where);
	}
	if(isset($_POST['MedioPay_fixed_amount_tips'])) {
		$newfixedtips = sanitize_text_field($_POST["MedioPay_fixed_amount_tips"]);
		global $wpdb;
		$table_name = $wpdb->prefix . 'mediopay';
		$newfixedtips = array( 'fixedTipAmount' => $newfixedtips );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newfixedtips,$data_where);
	}
	if(isset($_POST['MedioPay_fixed_thank_you'])) {
		$newthankyou = sanitize_text_field($_POST["MedioPay_fixed_thank_you"]);
		/*global $wpdb;
		$table_name = $wpdb->prefix . 'mediopay';*/
		$newthankyou = array( 'fixedThankYou' => $newthankyou );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newthankyou,$data_where);
	}
	if(isset($_POST['MedioPay_deactivate_metadata'])) {
		$newmetadata = sanitize_text_field($_POST["MedioPay_deactivate_metadata"]);
		/*global $wpdb;
		$table_name = $wpdb->prefix . 'mediopay';*/
		$newmetadata = array( 'noMetanet' => $newmetadata );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newmetadata,$data_where);
	}
	else {
		/*global $wpdb;
		$table_name = $wpdb->prefix . 'mediopay';*/
		$newmetadata = array( 'noMetanet' => 'no' );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newmetadata,$data_where);
	}
	if(isset($_POST['MedioPay_deactivate_edit'])) {
		$newedit = sanitize_text_field($_POST["MedioPay_deactivate_edit"]);
		//echo $newedit;
		/*global $wpdb;
		$table_name = $wpdb->prefix . 'mediopay';*/
		$newedit = array( 'noEditField' => $newedit );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newedit,$data_where);
	}
	else {
		$newedit = array( 'noEditField' => 'no' );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newedit,$data_where);
	}
	if(isset($_POST['MedioPay_bar_color'])) {
		$newedit = sanitize_hex_color($_POST["MedioPay_bar_color"]);
		//echo $newedit;
		/*global $wpdb;
		$table_name = $wpdb->prefix . 'mediopay';*/
		$newedit = array( 'barColor' => $newedit );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newedit,$data_where);
	}
	
	if(isset($_POST['MedioPay_thisURL'])) {
		$thisURL = esc_url($_POST['MedioPay_thisURL']);
		//echo "<script>thisURL='" . $thisURL . "';</script>";
		//echo $thisURL;
	}
	if(isset($_POST['MedioPay_paywall_msg'])) {
		$myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" );
		if ( isset($myrows[0]->paywallMsg)) {
			$newedit = sanitize_text_field($_POST["MedioPay_paywall_msg"]);
			$newedit = array( 'paywallMsg' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
		else {
			 $wpdb->query("ALTER TABLE " . $table_name . " ADD paywallMsg tinytext NOT NULL");
			 $newedit = sanitize_text_field($_POST["MedioPay_paywall_msg"]);
			$newedit = array( 'paywallMsg' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);
		}
	}	
	if(isset($_POST['MedioPay_tipping_msg'])) {
		$myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" );
		if ( isset($myrows[0]->tippingMsg)) {
			$newedit = sanitize_text_field($_POST["MedioPay_tipping_msg"]);
			$newedit = array( 'tippingMsg' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
		else {
			 $wpdb->query("ALTER TABLE " . $table_name . " ADD tippingMsg tinytext NOT NULL");
			 $newedit = sanitize_text_field($_POST["MedioPay_tipping_msg"]);
			$newedit = array( 'tippingMsg' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);
		}
	}	
	if(isset($_POST['MedioPay_editable_tips'])) {
		$myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" );
		if ( isset($myrows[0]->editableTips)) {
			$newedit = sanitize_text_field($_POST["MedioPay_editable_tips"]);
			$newedit = array( 'editableTips' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
		else {
			 $wpdb->query("ALTER TABLE " . $table_name . " ADD editableTips tinytext NOT NULL");
			 $newedit = sanitize_text_field($_POST["MedioPay_editable_tips"]);
			$newedit = array( 'editableTips' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);
		}
	}	
	else {
		if ( isset($myrows[0]->editableTips)) {
			$newmetadata = array( 'editableTips' => 'no' );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newmetadata,$data_where);
		}
		else {
			$wpdb->query("ALTER TABLE " . $table_name . " ADD editableTips tinytext NOT NULL");
			$newedit = 'no';
			$newedit = array( 'editableTips' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
	}
	if(isset($_POST['MedioPay_address_2'])) {
		$myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" );
		if ( isset($myrows[0]->address2)) {
			$newedit = sanitize_text_field($_POST["MedioPay_address_2"]);
			$newedit = array( 'address2' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
		else {
			 $wpdb->query("ALTER TABLE " . $table_name . " ADD address2 tinytext NOT NULL");
			 $newedit = sanitize_text_field($_POST["MedioPay_address_2"]);
			$newedit = array( 'address2' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
	}	
	else {
		if ( isset($myrows[0]->address2)) {
			$newmetadata = array( 'address2' => 'none' );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newmetadata,$data_where);
		}
		else {
			$wpdb->query("ALTER TABLE " . $table_name . " ADD address2 tinytext NOT NULL");
			$newedit = 'none';
			$newedit = array( 'address2' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
	}	
	
	if(isset($_POST['MedioPay_second_address_share'])) {
		$myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" );
		if ( isset($myrows[0]->secondAddressShare)) {
			$newedit = sanitize_text_field($_POST["MedioPay_second_address_share"]);
			$newedit = array( 'secondAddressShare' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
		else {
			 $wpdb->query("ALTER TABLE " . $table_name . " ADD secondAddressShare tinytext NOT NULL");
			 $newedit = sanitize_text_field($_POST["MedioPay_second_address_share"]);
			$newedit = array( 'secondAddressShare' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);
		}
	}	
	else {
		if ( isset($myrows[0]->secondAddressShare)) {
			$newmetadata = array( 'secondAddressShare' => 'none' );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newmetadata,$data_where);
		}
		else {
			$wpdb->query("ALTER TABLE " . $table_name . " ADD secondAddressShare tinytext NOT NULL");
			$newedit = 'none';
			$newedit = array( 'secondAddressShare' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
	}	
	if(isset($_POST['MedioPay_link_color'])) {
		//echo "Set link color";
		$myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" );
		if ( isset($myrows[0]->linkColor)) {
			$newedit = sanitize_text_field($_POST["MedioPay_link_color"]);
			$newedit = array( 'linkColor' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
		else {
			 $wpdb->query("ALTER TABLE " . $table_name . " ADD linkColor tinytext NOT NULL");
			 $newedit = sanitize_text_field($_POST["MedioPay_link_color"]);
			$newedit = array( 'linkColor' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);
		}
	}
	// paywhatyouwant
	if(isset($_POST['MedioPay_paywhatuwant'])) {
		//echo "Set link color";
		$myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" );
		if ( isset($myrows[0]->paywhatyouwant)) {
			$newedit = sanitize_text_field($_POST["MedioPay_paywhatuwant"]);
			$newedit = array( 'paywhatyouwant' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
		else {
			 $wpdb->query("ALTER TABLE " . $table_name . " ADD paywhatyouwant tinytext NOT NULL");
			 $newedit = sanitize_text_field($_POST["MedioPay_paywhatuwant"]);
			$newedit = array( 'paywhatyouwant' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);
		}
	}
	else {
		$myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" );
		if ( isset($myrows[0]->paywhatyouwant)) {
			$newedit = "no";
			$newedit = array( 'paywhatyouwant' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
		else {
			 $wpdb->query("ALTER TABLE " . $table_name . " ADD paywhatyouwant tinytext NOT NULL");
			 $newedit = "no";
			$newedit = array( 'paywhatyouwant' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);
		}
	}
	// threshold
	if(isset($_POST['MedioPay_threshold'])) {
		//echo "Set link color";
		$myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" );
		if ( isset($myrows[0]->threshold)) {
			$newedit = sanitize_text_field($_POST["MedioPay_threshold"]);
			$newedit = array( 'threshold' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
		else {
			 $wpdb->query("ALTER TABLE " . $table_name . " ADD threshold tinytext NOT NULL");
			 $newedit = sanitize_text_field($_POST["MedioPay_threshold"]);
			$newedit = array( 'threshold' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);
		}
	}
	/*
	if(isset($_POST['MedioPay_align_left'])) {
		$myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" );
		if ( isset($myrows[0]->editableTips)) {
			$newedit = sanitize_text_field($_POST["MedioPay_align_left"]);
			$newedit = array( 'alignLeft' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
		else {
			 $wpdb->query("ALTER TABLE " . $table_name . " ADD alignLeft tinytext NOT NULL");
			 $newedit = sanitize_text_field($_POST["MedioPay_align_left"]);
			$newedit = array( 'alignLeft' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);
		}
	}	
	else {
		if ( isset($myrows[0]->editableTips)) {
			$newmetadata = array( 'alignLeft' => 'no' );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newmetadata,$data_where);
		}
		else {
			$wpdb->query("ALTER TABLE " . $table_name . " ADD alignLeft tinytext NOT NULL");
			$newedit = 'no';
			$newedit = array( 'alignLeft' => $newedit );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);	
		}
	}*/	
	//echo "<script>location.replace(thisURL);</script>";
}
}


// Hook functions in the dashboard

add_action( 'add_meta_boxes', 'mediopay_custom_meta_paidcontent' );
add_action( 'add_meta_boxes', 'mediopay_custom_meta_tips' );
add_action( 'add_meta_boxes', 'mediopay_custom_meta_second_receiver' );
add_action( 'save_post', 'mediopay_meta_save' );


//
// Load a blog page
//


// activate PayWall from the second editor field

function mediopay_create_paywall($post_content) {
	$blogpath = get_bloginfo($show = 'wpurl') . "/";
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	if ($blogpath !== $actual_link) {
	
	$mp_fullcontent = $post_content;
	$mp_post_data = new stdClass();
	mp_whattodo($mp_post_data, $post_content);
	if ($mp_post_data->active == 0 ) {
		if ($mp_post_data->tipping == 1) {
				mp_tipping_data($mp_post_data);
				if ($mp_post_data->editableTips == "yes") {
						$buttonid = "editable_mbutton";	
						$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1 mp_invisible", "mp_tipFrame", "counterTips", $buttonid, "tip");							 	
					 }
				else {
					 	$buttonid = "tbutton";
					 	$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1", "mp_tipFrame", "counterTips", $buttonid, "tip" );	
						echo "<script>make_paywall1_object('tip','tip')";
				}		
			}
		return $mp_fullcontent;
	}	
	else if ($mp_post_data->yenpoints == 1) {
		if ($mp_post_data->paywall1 == 1 AND $mp_post_data->paywall1_type == "editor") {
			global $wpdb;
			$table_name2 = $wpdb->prefix . 'mediopay_paidcontent';
			if (strlen($mp_myrows2[0]->paidcontent1) > 3) {
				$mp_paid_content = $mp_myrows2[0]->paidcontent1;
			}
			else {
				$mp_paid_content = get_post_meta( $mypost_id, 'meta-paidcontent', true );	
			}				
			$mp_paid_content = nl2br($mp_paid_content);
			$mp_paid_content = "<br />" . $mp_paid_content;
			if ($mp_post_data->tipping == 1) {
				mp_tipping_data($mp_post_data);	
				if ($mp_post_data->editableTips == "yes") {
					$buttonid = "editable_mbutton";	
					$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1 mp_invisible", "mp_tipFrame", "counterTips", $buttonid, "tip");							 	
				}
				else {
				 	$buttonid = "tbutton";
				 	$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1", "mp_tipFrame", "counterTips", $buttonid, "tip" );	
					echo "<script>make_paywall1_object('tip','tip')";
				 }	
			}
			$mp_fullcontent .= $mp_paid_content;
			return $mp_fullcontent;
		}
		else {
			if ($mp_post_data->tipping == 1) {
				mp_tipping_data($mp_post_data);	
				if ($mp_post_data->editableTips == "yes") {
						$buttonid = "editable_mbutton";	
						$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1 mp_invisible", "mp_tipFrame", "counterTips", $buttonid, "tip");							 	
				}
				else {
					 	$buttonid = "tbutton";
					 	$mp_fullcontent1 .= mp_build_tippings_field($mp_post_data, "mp_frame1", "mp_tipFrame", "counterTips", $buttonid, "tip");	
						echo "<script>make_paywall1_object('tip','tip')";
				}	
			}
			return $mp_fullcontent;		
		}
	}
	else {
		mp_general_info($mp_post_data);
		if ($mp_post_data->paywall1 == 1) {
			mp_checkcookies($mp_post_data, 1);
			if ($mp_post_data->cookie1 == 1 or $mp_post_data->threshold_achieved == 1) {
				$mp_paid_content = get_post_meta( $mp_post_data->postid, 'meta-paidcontent', true );
				$mp_paid_content = nl2br($mp_paid_content);
				$mp_paid_content = "<br />" . $mp_paid_content;
				if ($mp_post_data->sponsor !== 0) {
					$mp_fullcontent .= mp_sponsorship($mp_post_data, 'mp_sponsor1');
				}
				$mp_fullcontent .= $mp_paid_content;
				if ($mp_post_data->tipping == 1) {
					 mp_tipping_data($mp_post_data);
					 if ($mp_post_data->editableTips == "yes") {
						$buttonid = "editable_mbutton";	
						$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1", "mp_tipFrame", "counterTips", $buttonid, "tip");							 	
					 }
					 else {
					 	$buttonid = "tbutton";
					 	$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1", "mp_tipFrame", "counterTips", $buttonid, "tip");	
					 	echo "<script>make_paywall1_object('tip','tip')";
					 }	
				}	
				return $mp_fullcontent;		
			}
			else {
				mp_paywall1_data($mp_post_data);
				if ($mp_post_data->paywall1_type == "shortcode") {
					mp_checkcookies($mp_post_data, 2);
					if ($mp_post_data->cookie2 == 1 or $mp_post_data->threshold_achieved == 1) {
						$mp_class = "mp_frame1";
						$mp_class2 = "mp_fading";
					}
					else {
						$mp_class = "mp_frame1 mp_invisible";	
						$mp_class2 = "mp_fading mp_invisible";			
					}
					if ($mp_post_data->paywall2) {
						mp_paywall2_data($mp_post_data);
						mp_prepare_fading($mp_post_data, "second", " ");	
					 	$mp_fullcontent .= "<div id='mp_fade2' class='" .$mp_class2 . "' >";
					 	$mp_fullcontent .= $mp_post_data->fading_content2 . "</div>";	
					 	$mp_fullcontent .= mp_build_paywall($mp_post_data, $mp_class, "mp_frame2", "second", "mp_counter2", "mbutton2", "");
					 	if ($mp_post_data->paywhatyouwant == 0) {
						 	echo "<script>make_paywall1_object('paywall2', 'editor')</script>";
						}
						if ($mp_post_data->tipping == 1) {
							 mp_tipping_data($mp_post_data);
							 if ($mp_post_data->editableTips == "yes") {
								$buttonid = "editable_mbutton";	
								$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1 mp_invisible", "mp_tipFrame", "counterTips", $buttonid, "tip");							 	
							 }
							 else {
							 	$buttonid = "tbutton";
							 	$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1 mp_invisible", "mp_tipFrame", "counterTips", $buttonid, "tip");
							 	echo "<script>make_paywall1_object('tip','tip')</script>";
							 }	
						}					
					}
					else {
						if ($mp_post_data->tipping == 1) {
							 mp_tipping_data($mp_post_data);
							 if ($mp_post_data->editableTips == "yes") {
								$buttonid = "editable_mbutton";	
								$mp_fullcontent .= mp_build_tippings_field($mp_post_data, $mp_class, "mp_tipFrame", "counterTips", $buttonid, "tip");							 	
							 }
							 else {
							 	$buttonid = "tbutton";
							 	$mp_fullcontent .= mp_build_tippings_field($mp_post_data, $mp_class, "mp_tipFrame", "counterTips",$buttonid, "tip");
							 	echo "<script>make_paywall1_object('tip','tip')";
							 }	
						}
					}
					return $mp_fullcontent;		
				}
				else if ($mp_post_data->paywall1_type == "editor") {
					mp_prepare_fading($mp_post_data, "first", "");
					$mp_fullcontent .= "<div id='mp_fade1' class='mp_fading' >";	
					$mp_fullcontent .= $mp_post_data->fading_content1 . "</div>";
					$mp_fullcontent .= mp_build_paywall($mp_post_data, "mp_frame1", "mp_frame1", "first", "mp_counter1", "mbutton1","");
					if ($mp_post_data->paywhatyouwant == 0) {
						echo "<script>make_paywall1_object('paywall1', 'editor')</script>";
					}
					if ($mp_post_data->tipping == 1) {
						 mp_tipping_data($mp_post_data);
						 if ($mp_post_data->editableTips == "yes") {
								$buttonid = "editable_mbutton";	
								$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1 mp_invisible", "mp_tipFrame", "counterTips", $buttonid, "tip");							 	
							 }
							 else {
							 	$buttonid = "tbutton";
							 	$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1 mp_invisible", "mp_tipFrame", "counterTips", $buttonid, "tip");
						 		echo "<script>make_paywall1_object('tip', 'tip')";
							 }	
					}
					return $mp_fullcontent;					
				}					
			}
		}
		else if ($mp_post_data->tipping == 1) {
			 mp_tipping_data($mp_post_data);
			 if ($mp_post_data->editableTips == "yes") {
					$buttonid = "editable_mbutton";	
					$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1", "mp_tipFrame", "counterTips", $buttonid, "tip");							 	
			 }
			 else {
				 	$buttonid = "tbutton";
				 	$mp_fullcontent .= mp_build_tippings_field($mp_post_data, "mp_frame1", "mp_tipFrame", "counterTips", $buttonid, "tip");
			 		echo "<script>make_paywall1_object('tip', 'tip')";
			 }	
			 return $mp_fullcontent;	
		}
		else {
			return $mp_fullcontent;		
		}	
	}
	}
	else {
		return $post_content;
	}
}



add_filter('the_content', 'mediopay_create_paywall');


// use PayWall with shortcodes. All the operations are the same as with the second editor field.


add_shortcode( 'paywall', 'MedioPay_paywall_function' );

function MedioPay_paywall_function( $attr, $content) {
	ob_start();	
	$blogpath = get_bloginfo($show = 'wpurl') . "/";
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	if ($blogpath !== $actual_link) {
		if (isset($mp_post_data)) {
		}
		else {
			$mp_post_data = new stdClass();
			mp_whattodo($mp_post_data, $content);
			$mp_post_data->paywall1_type = "shortcode";
			if ($mp_post_data->yenpoints == 1) {
			}
			else {
				mp_general_info($mp_post_data);
				mp_checkcookies($mp_post_data, 2);
				if ($mp_post_data->cookie2 == 1 or $mp_post_data->threshold_achieved == 1) {
					if ($mp_post_data->paywall2 == 1) {
						//echo "<script>aftercooke('paywall');</script>";
					}
					else if ($mp_post_data->tipping == 1) {
						//echo "<script>aftercooke('tipping');</script>";
					}
					if ($mp_post_data->sponsor !== 0) {
						echo mp_sponsorship($mp_post_data, 'mp_sponsor2');
					}
					echo $content;
				}
				else {
					mp_paywall1_data($mp_post_data);
					if (isset($attr["amount"])){
						echo "<script>paymentAmount1=\"" . esc_js($attr["amount"]) . "\";</script>";
						$mp_amount = $attr["amount"];
						$mp_post_data->amount = $attr["amount"];
					}
					else {
						echo "<script>paymentAmount1=\"" . esc_js($mp_post_data->amount) . "\";</script>";
						$mp_amount = $mp_post_data->amount;
					}
					if (isset($attr["message"])){
						$mp_post_data->paywallmsg = $attr["message"];
					}
					mp_prepare_fading($mp_post_data, "first", esc_js($content));
					$mp_paywall = mp_build_paywall($mp_post_data, "mp_frame1", "mp_frame1", "first", "mp_counter1", "mbutton1", $content);
					echo "<div id='mp_fade1' class='mp_fading' >" . $mp_post_data->fading_content1 . 
					"</div>" . $mp_paywall;
					if ($mp_post_data->paywhatyouwant == 0) {
						echo "<script>make_paywall1_object('paywall1', 'shortcode')</script>";
					}
				}
			}		
		}	
	}
	return ob_get_clean();
}

// Create tipping field inside the text

add_shortcode( 'tipme', 'MedioPay_tipping_function' );

function MedioPay_tipping_function( $attr, $content) {
	ob_start();
	$blogpath = get_bloginfo($show = 'wpurl') . "/";
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$mp_post_data = new stdClass();
	$mp_amount = (isset($attr["amount"])) ? $mp_amount = $attr["amount"] : $mp_amount = "no";
	$mp_post_data->tipme_hidebehind = (isset($attr["hidebehind"])) ? $mp_post_data->tipme_hidebehind = 1 : $mp_post_data->tipme_hidebehind = 0;
	echo "<script>tipmeAmount=\"" . $mp_amount . "\";</script>";
	if (isset($attr["message"])) {
		$mp_post_data->tippingMsg = $attr["message"];
		echo "<script>tipMeMsg = \"" . esc_js($attr["message"]) . "\";</script>";
		$mp_tipMeMsg = $attr["message"];
	}
	else {
		$mp_tipMeMsg = "no";	
	}	
	if ($blogpath !== $actual_link) {
			mp_whattodo($mp_post_data, $content);	
			mp_general_info($mp_post_data);	
			mp_tipme_data($mp_post_data, $mp_amount, $mp_tipMeMsg);	
			if ($mp_post_data->editableTips == "yes") {
				echo mp_build_tippings_field($mp_post_data, "mp_frame1", "mp_tipFrame2", "counterTips2", "editable_mbutton2", "tipme" );
			}
			else {
				echo mp_build_tippings_field($mp_post_data, "mp_frame1", "mp_tipFrame2", "counterTips2", "tbutton2", "tipme" );
				echo "<script>make_paywall1_object('tipme')</script>";
			}
	}
	return ob_get_clean();
}




/*
do_action( ' pre_comment_on_post', int $comment_post_ID );

function action_pre_comment_on_post( $array ) {
	echo "<script>console.log('comment');</script>";
}

add_action( 'pre_comment_on_post', 'action_pre_comment_on_post', 10, 1);

function add_non_fake_textarea_field( $default ) {
	$commenter = wp_get_current_commenter();
	$default['comment_notes_after'] .= 
	'<p class="comment-form-just_another_id">
	<label for="just_another_id">Comment:</label>
	<textarea id="just_another_id" name="just_another_id" cols="45" rows="8" aria-required="true"></textarea>
	</p>';
	return $default;
}*/
 





?>
