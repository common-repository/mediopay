<?php


add_action( 'rest_api_init', function () {
  register_rest_route( 'mediopay/v1', '/throwcontent1/', array(
    'methods' => 'POST',
    'callback' => 'mp_throw_content',
  ) );
} );

// Deliver content after a paywall was payed.


function mp_throw_content( WP_REST_Request $mp_request ) {
	global $wpdb;
	// get the data	
	$mp_mypost_id = $mp_request['MedioPay_postid'];
	$mp_method = $mp_request['MedioPay_form'];
	$mp_outputs = $mp_request['MedioPay_outputs'];
	$mp_is_preview = $mp_request['MedioPay_preview'];
	$mp_amount = $mp_request['MedioPay_amount'];
	$mp_paymail = $mp_request['MedioPay_paymail'];
	$mp_threshold = $mp_request['MedioPay_threshold'];
	$mp_threshold_active = $mp_request['MedioPay_threshold_active'];
		
	if ($mp_is_preview === "yes") {
		$mp_number = $_POST['MedioPay_number'];
	}
	else {
		$mp_number = $mp_request['MedioPay_number'] + 1;
	}	
	$mp_userid = $mp_request['MedioPay_userID'];
	$mp_newCounter = $mp_request['Mediopay_newCounter'];
	$mp_share = $mp_request['MedioPay_shareQuote'];
	$mp_postid = $mp_request['MedioPay_postid'];
	$newdata =  get_post_meta( $mp_mypost_id, 'meta-newdata', true );
	
	if ($mp_request['MedioPay_method'] == "paywall1") {
		$mp_pw = 1;	
		if ($mp_number === 1) {
			update_post_meta( $mp_mypost_id, 'meta-first-buys1', $mp_userid);			
		}
		if ($mp_number === 2) {
			update_post_meta( $mp_mypost_id, 'meta-second-buys1', $mp_userid);			
		}
		if ($mp_number === 3) {
			update_post_meta( $mp_mypost_id, 'meta-third-buys1', $mp_userid);			
		}
		if ($mp_number === 4) {
			update_post_meta( $mp_mypost_id, 'meta-fourth-buys1', $mp_userid);			
		}
	}	
	else if ($mp_request['MedioPay_method'] == "paywall2") {
		if ($mp_number === 1) {
			update_post_meta( $mp_mypost_id, 'meta-first-buys2', $mp_userid);			
		}
		if ($mp_number === 2) {
			update_post_meta( $mp_mypost_id, 'meta-second-buys2', $mp_userid);			
		}
		if ($mp_number === 3) {
			update_post_meta( $mp_mypost_id, 'meta-third-buys2', $mp_userid);			
		}
		if ($mp_number === 4) {
			update_post_meta( $mp_mypost_id, 'meta-fourth-buys2', $mp_userid);			
		}
		$mp_pw = 2;	
	}
	
	// get paid content
	if ($mp_method === "editor") {
		$mp_newdb = get_post_meta( $mp_mypost_id, 'meta-newdb', true );
		if ($mp_newdb === "yes") {
			$table_name2 = $wpdb->prefix . "mediopay_paidcontent";		
			$this_rows =  $wpdb->get_results( "SELECT * FROM " . $table_name2 . " WHERE postid = " . $mp_postid ); 
			if (isset($this_rows[0]->postid)) {
				if ($mp_pw == 1) {
					if (strlen($this_rows[0]->paidcontent1) > 5) {
						$mp_paid_content = $this_rows[0]->paidcontent1;	
					}
					else {
						$mp_paid_content = get_post_meta( $mp_mypost_id, 'meta-paidcontent', true );				
					}
				}
				else {
					if (strlen($this_rows[0]->paidcontent2) > 5) {
						$mp_paid_content = $this_rows[0]->paidcontent2;	
					}
					else {
						$mp_paid_content = get_post_meta( $mp_mypost_id, 'meta-paidcontent', true );				
					}			
				}	
			}
		}	
		else {	
			$mp_paid_content = get_post_meta( $mp_mypost_id, 'meta-paidcontent', true );
		}
		$mp_paid_content = nl2br($mp_paid_content);
		$mp_paid_content = "<br />" . $mp_paid_content;
		$mp_secretname = "meta-secretword-1";
		
	}
	else if ($mp_method === "shortcode") {
		$mp_newdb = get_post_meta( $mp_mypost_id, 'meta-newdb', true );
		if ($mp_newdb === "yes") {
			$table_name2 = $wpdb->prefix . "mediopay_paidcontent";	
			$this_rows =  $wpdb->get_results( "SELECT * FROM " . $table_name2 . " WHERE postid = " . $mp_postid ); 
			if (isset($this_rows[0]->postid)) {
				$mp_paid_content = "<br />" . $this_rows[0]->paidcontent1;	
			}
		}	
		else {	
			global $wpdb;
			$table_name = $wpdb->prefix . 'posts';
			$myrows = $wpdb->get_results( "SELECT post_content FROM " . $table_name . " WHERE ID = " . $mp_mypost_id );
			$mp_paid_content = $myrows[0]->post_content;
			$mp_pos = strpos($mp_paid_content, "[paywall");
			$mp_pos_helper = substr($mp_paid_content,$mp_pos, 300);
			$mp_pos_helper_pos = strpos($mp_pos_helper, "]");
			$mp_pos = $mp_pos + $mp_pos_helper_pos + 1;
			$mp_pos2 = strpos($mp_paid_content, "[/paywall]");
			$mp_paid_content = "<br />" . substr($mp_paid_content, $mp_pos, ($mp_pos2 - $mp_pos));
		}
		$mp_paid_content =  nl2br($mp_paid_content);
		$mp_secretname = "meta-secretword-2";
		
	}
	/*	// test
	$mp_output->secret = 1111;
	$mp_output->paidcontent = 2222;
	$mp_output->method = 3333;
	$mp_output_json = json_encode($mp_output);
	return $mp_output_json;		*/
	
	// get address from wp-db
	global $wpdb;
	$table_name = $wpdb->prefix . 'mediopay';
	$myrows = $wpdb->get_results( "SELECT address FROM " . $table_name . " WHERE id = 1" );
	$mp_address = $myrows[0]->address;
	
	// if address is part of payment outputs
	
	if (in_array($mp_address, $mp_outputs)) {
		// if new data structure
			
		if ($newdata == "yes") {
			if ($mp_pw == 1) {
				$oldamount = get_post_meta( $mp_mypost_id, 'IncomePW1', true );
				$oldamountall = get_post_meta( $mp_mypost_id, 'IncomeAll', true );
				$newamount = $oldamount + $mp_amount;
				$newamountall = $oldamountall + $mp_amount;
				update_post_meta ( $mp_mypost_id, 'IncomePW1', $newamount );
				update_post_meta ( $mp_mypost_id, 'IncomeAll', $newamountall );
				update_post_meta( $mp_mypost_id, 'meta_buys1', $mp_number );
			}	
			else if ($mp_pw == 2) {
				$oldamount = get_post_meta( $mp_mypost_id, 'IncomePW2', true );
				$oldamountall = get_post_meta( $mp_mypost_id, 'IncomeAll', true );
				$newamount = $oldamount + $mp_amount;
				$newamountall = $oldamountall + $mp_amount;
				update_post_meta ( $mp_mypost_id, 'IncomePW2', $newamount );
				update_post_meta ( $mp_mypost_id, 'IncomeAll', $newamountall );
				update_post_meta( $mp_mypost_id, 'meta_buys2', $mp_number );
			}
			if ($mp_threshold_active == 1) {
				if ($newamountall >= $mp_threshold) {		
					update_post_meta ( $mp_mypost_id, 'mp_sponsor', $mp_paymail );
				}
			}		
		}
		// if not, create new data structure
		else {
			$newamount = $mp_number * $mp_amount;
			update_post_meta ( $mp_mypost_id, 'meta_newdata', "yes" );
			if ($mp_method === "editor") {
				if ($mp_pw == 1) {
					update_post_meta( $mp_mypost_id, 'meta_buys1', $mp_number );
					update_post_meta ( $mp_mypost_id, 'IncomePW1', $newamount );
					update_post_meta ( $mp_mypost_id, 'IncomeAll', $newamount );
				}
				else {
					update_post_meta( $mp_mypost_id, 'meta_buys2', $mp_number );
					update_post_meta ( $mp_mypost_id, 'IncomePW2', $newamount );
					update_post_meta ( $mp_mypost_id, 'IncomeAll', $newamount );	
				}		
			}
			else if ($mp_method === "shortcode") {
				update_post_meta( $mp_mypost_id, 'meta_buys1', $mp_number );
				update_post_meta ( $mp_mypost_id, 'IncomePW1', $newamount );	
				update_post_meta ( $mp_mypost_id, 'IncomeAll', $newamount );		
			}	
			if ($mp_newCounter !== "yes") {
				update_post_meta($mp_mypost_id, 'meta-newcounter', 'yes');
				update_post_meta( $mp_mypost_id, 'meta_share', $mp_share );	
			}	
			if ($mp_threshold_active == 1) {
				$newamountall = get_post_meta( $mp_mypost_id, 'IncomeAll', true );
				if ($newamountall >= $mp_threshold) {		
					update_post_meta ( $mp_mypost_id, 'mp_sponsor', $mp_paymail );
				}
			}			
		}			
		// get secret or create one
		if (get_post_meta( $mp_mypost_id, $mp_secretname, true ) !== null) {
			$mp_meta_secret = get_post_meta( $mp_mypost_id, $mp_secretname, true );
			if (strlen($mp_meta_secret) > 0) {
			}	
			else {
				$mp_meta_secret = rand(100000, 999999);
				update_post_meta ( $mp_mypost_id, $mp_secretname, $mp_meta_secret );
			}
		}	
		else {
			$mp_meta_secret = rand(100000, 999999);
			update_post_meta ( $mp_mypost_id, $mp_secretname, $mp_meta_secret );
		}
		if (strlen($mp_meta_secret) > 0) {
				$mp_output->secret = $mp_meta_secret;
				$mp_output->paidcontent = $mp_paid_content;
				$mp_output->method = $mp_method;
				$mp_output->number = $mp_pw;
				$mp_output_json = json_encode($mp_output);
				return $mp_output_json;	
				//echo "secret" . $mp_meta_secret1 . "<br />" . $mp_paid_content_1;			
		}			
		else {
				return "nosecret1111" . $mp_meta_secret . $mp_paid_content;
			}
	}
	else {
		return "12345654321 Address doesn't match. Are you trying to cheat?"  . $mp_address . var_dump($mp_outputs);	
	}
	wp_die();
}


// Process tips - no Rest API needed


add_action( 'rest_api_init', function () {
  register_rest_route( 'mediopay/v1', '/handletips/', array(
    'methods' => 'POST',
    'callback' => 'mp_handleTips',
  ) );
} );

function mp_handleTips( WP_REST_Request $mp_tipsRequest ) {
	//return json_encode("hi");	
	$mp_mypost_id = $mp_tipsRequest['MedioPay_postid'];
	$mp_outputs = $mp_tipsRequest['MedioPay_outputs'];
	$mp_is_preview = $mp_tipsRequest['MedioPay_preview'];
	$mp_amount = $mp_tipsRequest['MedioPay_amount'];
	if ($mp_is_preview === "yes") {
		$mp_number = $mp_tipsRequest['MedioPay_number'];
	}
	else {
		$mp_number = $mp_tipsRequest['MedioPay_number'] + 1;
	}
	$mp_userid = $mp_tipsRequest['MedioPay_userID'];
	$mp_newCounter = $mp_tipsRequest['Mediopay_newCounter'];
	$mp_share = $mp_tipsRequest['MedioPay_shareQuote'];
	$mp_amount = $mp_tipsRequest['MedioPay_amount'];
	$mp_typeoftip = $mp_tipsRequest['MedioPay_type'];
	if ($mp_typeoftip == "tip1") {
		$oldamount = get_post_meta( $mp_mypost_id, 'IncomeTp1', true );
	}
	else if ($mp_typeoftip == "tip2") {
		$oldamount = get_post_meta( $mp_mypost_id, 'IncomeTp2', true );
	}
	if (strlen($oldamount) > 0) {
		$newamount = $oldamount + $mp_amount;
		$oldamountall = get_post_meta( $mp_mypost_id, 'IncomeAll', true );
		$newamountall = $oldamountall + $newamount;
	}	
	else {
		$newamount = $mp_amount;
		$newamountall = $mp_amount;
	}
	if ($mp_typeoftip == "tip1") {	
		update_post_meta ( $mp_mypost_id, 'IncomeTp1', $newamount );
	}	
	else if ($mp_typeoftip == "tip2") {
		update_post_meta ( $mp_mypost_id, 'IncomeTp2', $newamount );
	}
	update_post_meta ( $mp_mypost_id, 'IncomeAll', $newamountall );
	
	if ($mp_newCounter == "yes") {
		update_post_meta( $mp_mypost_id, 'meta_tips', $mp_number );
		update_post_meta( $mp_mypost_id, 'meta_share', $mp_share );
		if ($mp_number === 1) {
			update_post_meta( $mp_mypost_id, 'meta-first-tips', $mp_userid);			
		}
		if ($mp_number === 2) {
			update_post_meta( $mp_mypost_id, 'meta-second-tips', $mp_userid);			
		}
		if ($mp_number === 3) {
			update_post_meta( $mp_mypost_id, 'meta-third-tips', $mp_userid);			
		}
		if ($mp_number === 4) {
			update_post_meta( $mp_mypost_id, 'meta-fourth-tips', $mp_userid);			
		}	
		$current_amount =  get_post_meta ($mp_mypost_id, 'meta-tipped-amount');
		$current_amount = $current_amount[0];
		if (isset($current_amount)) {
			if (strlen($current_amount) > 0 && $current_amount > 0) {
				$newamount = $current_amount + $mp_amount;
				update_post_meta( $mp_mypost_id, 'meta-tipped-amount', $newamount);							
			}		
			else {
				update_post_meta( $mp_mypost_id, 'meta-tipped-amount', $mp_amount);			
			}
		}
		else {
			update_post_meta( $mp_mypost_id, 'meta-tipped-amount', $mp_amount);
		}							
	}
	else {
		update_post_meta( $mp_mypost_id, 'meta-newcounter', 'yes');
		update_post_meta( $mp_mypost_id, 'meta_tips', $mp_number );
		update_post_meta( $mp_mypost_id, 'meta_share', $mp_share );
		if ($mp_tipsRequest['MedioPay_firstPartner'] !== "no")	{
			update_post_meta( $mp_mypost_id, 'meta-first-tips', $mp_tipsRequest['MedioPay_firstPartner']);				
		}
		if ($mp_tipsRequest['MedioPay_secondPartner'] !== "no")	{
			update_post_meta( $mp_mypost_id, 'meta-second-tips', $mp_tipsRequest['MedioPay_secondPartner']);				
		}
		if ($mp_tipsRequest['MedioPay_thirdPartner'] !== "no")	{
			update_post_meta( $mp_mypost_id, 'meta-third-tips', $mp_tipsRequest['MedioPay_thirdPartner']);				
		}
		if ($mp_tipsRequest['MedioPay_fourthPartner'] !== "no")	{
			update_post_meta( $mp_mypost_id, 'meta-fourth-tips', $mp_tipsRequest['MedioPay_fourthPartner']);				
		}
	}
	return json_encode("handled tips");
wp_die();
}


add_action( 'rest_api_init', function () {
  register_rest_route( 'mediopay/v1', '/processcookies/', array(
    'methods' => 'POST',
    'callback' => 'mp_processCookies',
  ) );
} );

// Deliver content after a paywall was payed.


function mp_processCookies( WP_REST_Request $mp_cookieRequest ) {
	$mp_mypost_id = $mp_cookieRequest['MedioPay_postid'];
	$mp_cookies = $mp_cookieRequest['mp_cookies'];
	$mp_position_paywall = $mp_cookieRequest['mp_position'];
	
	$mp_output->position = $mp_position_paywall;		
	$mp_meta_secret1 = get_post_meta( $mp_mypost_id, 'meta-secretword-1', true );
	$mp_meta_secret2 = get_post_meta( $mp_mypost_id, 'meta-secretword-2', true );
	if ($mp_position_paywall === "editor") {	
		if ( strpos($mp_cookies, $mp_meta_secret1) !== false ) {
			global $wpdb;
	   	$table_name = $wpdb->prefix . 'mediopay';
			$mp_paid_content_1 = get_post_meta( $mp_mypost_id, 'meta-paidcontent', true );
			$bodytag = str_replace("%body%", "schwarz", "<body text='%body%'>");	
			$mp_paid_content_1 = nl2br($mp_paid_content_1);
			$mp_paid_content_1 = str_replace("</h2><br />", "</h2><p>", $mp_paid_content_1);		
			$mp_output->paidcontent = "<br />" . $mp_paid_content_1;
			$mp_output_json = json_encode($mp_output);
			return $mp_output_json;	
		}
		else {
			return "f... ";
		}
	}
	if ($mp_position_paywall === "mp_shortcode") {	
		if ( strpos($mp_cookies, $mp_meta_secret2) !== false ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'posts';
			$myrows = $wpdb->get_results( "SELECT post_content FROM " . $table_name . " WHERE ID = " . $mp_mypost_id );
			$mp_paid_content_2 = $myrows[0]->post_content;
			$mp_pos = strpos($mp_paid_content_2, "[paywall");
			$mp_poshelper = $mp_pos + 100;
			$mp_substring = substr($mp_paid_content_2, $mp_pos, $mp_poshelper);
			$mp_pos = $mp_pos + strpos($mp_substring, "]");
			$mp_pos2 = strpos($mp_paid_content_2, "[/paywall]");
			$mp_paid_content_2 = substr($mp_paid_content_2, ($mp_pos + 1), ($mp_pos2 - $mp_pos - 1));
			$mp_paid_content_2 = nl2br($mp_paid_content_2);
			$mp_output->paidcontent = $mp_paid_content_2;
			$mp_output_json = json_encode($mp_output);
			return $mp_output_json;	
		}
		else {	
			return "f. " . $mp_meta_secret2;
		}
	}
	wp_die();
}	

add_action( 'rest_api_init', function () {
  register_rest_route( 'mediopay/v1', '/register4faucet/', array(
    'methods' => 'POST',
    'callback' => 'register4faucet',
  ) );
} );


function register4faucet( WP_REST_Request $mp_faucetRequest ) {
	$hello = $mp_faucetRequest->hello;
	global $wpdb;
	$table_name = $wpdb->prefix . 'mediopay';
	$myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" );	
	if ( isset($myrows[0]->secret)) {
		$secret4faucet = $myrows[0]->secret;
		if (strlen($secret4faucet) > 5) {
		}
		else {
			$secret4faucet = bin2hex(random_bytes(12));		
			$newedit = array( 'secret' => $secret4faucet );
			$data_where = array( 'id' => 1);
			$wpdb->update($table_name,$newedit,$data_where);
		}			
	}
	else {
		$wpdb->query("ALTER TABLE " . $table_name . " ADD secret tinytext NOT NULL");
		$secret4faucet = bin2hex(random_bytes(12));		
		$newedit = array( 'secret' => $secret4faucet );
		$data_where = array( 'id' => 1);
		$wpdb->update($table_name,$newedit,$data_where);
	}	
	
	$blogpath = get_bloginfo($show = 'wpurl');
	$output->secret = $secret4faucet;
	$output->blogpath = $blogpath;
	$outputs = json_encode($output);
	return $outputs;
}








?>