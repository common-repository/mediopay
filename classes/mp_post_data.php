<?php

function mp_whattodo($mp_post_data, $post_content) {
	if (isset($_GET["hash"])) {
		$mp_hash = $_GET["hash"];
		$mp_nonce = $_GET["nonce"];
		//echo "<script>console.log('" .  $mp_nonce . "');</script>";
		if ( isset($mp_myrows[0]->secret)) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'mediopay';	
			$mp_myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" ); 
			$secret = $mp_myrows[0]->secret;
			$posqm = strpos($actual_link, "?");
			$actual_link = substr($actual_link, 0, $posqm);
			$message = $secret . $mp_nonce . $actual_link;
			$checkhash = hash("sha256", $message);
			if ($checkhash == $mp_hash) {
				$mp_post_data->yenpoints = 1;
				echo "<script>mp_yenpoints = 'yes';</script>";			
			}
			else {
				echo "<script>mp_yenpoints = 'no';</script>";		
			}
		}		
	}			
	else {	
	$mp_post_data->yenpoints = 0;
	$mp_post_data->active = 0;	
	$mp_post_data->paywall1 = 0;
	$mp_post_data->paywall2 = 0;
	$mp_post_data->tipping = 0;
	$mp_post_data->tipme = 0;
	$mp_post_data->newactive = 0;
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$mypost_id = url_to_postid($actual_link);
	$mp_post_data->postid = $mypost_id;
	$mp_isactive = get_post_meta( $mypost_id, 'mp_active', true );
	global $wpdb;
	$table_name2 = $wpdb->prefix . 'mediopay_paidcontent';
	if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name2 ) ) === $table_name2 ) {
		$this_rows = $wpdb->get_results( "SELECT * FROM " . $table_name2 . " WHERE postid = " . $mypost_id ); 
	}
	if (isset($this_rows[0]->paidcontent1)) {
		if (strlen($this_rows[0]->paidcontent1) > 10) {
			$mp_paidcontent1 = $this_rows[0]->paidcontent1;
		}	
		else {
			$mp_paidcontent1 = get_post_meta( $mypost_id, 'meta-paidcontent', true );
		}	
	}	
	else {
		$mp_paidcontent1 = get_post_meta( $mypost_id, 'meta-paidcontent', true );
	}
	$mp_tippingButton = get_post_meta($mypost_id, 'mp_tipping', true);

	if ($mp_isactive == 1) {
		$mp_post_data->active = 1;	
		$mp_post_data->newactive = 1;
		if (isset($this_rows[0]->postid)) {
			if (strlen($this_rows[0]->paidcontent1) > 3) {
				$mp_post_data->paywall1 = 1;		
				if ( has_shortcode( $post_content, 'paywall' ) ) {
					$mp_post_data->paywall1_type = "shortcode";	
					echo "<script>paywall1_type='shortcode';</script>";
					if (strlen($this_rows[0]->paidcontent2) > 3) {
						$mp_post_data->paywall2 = 1;			
					}	
					else {
						$mp_post_data->paywall2 = 0;			
					}
				}	
				else if (strlen($mp_paidcontent1) > 10) {
					$mp_post_data->paywall1_type = "editor";
					echo "<script>paywall1_type='editor';</script>";
					$mp_post_data->paywall2 = 0;	
				}
				else {
					$mp_post_data->paywall1 = 0;
					$mp_post_data->paywall1_type = 0;
					$mp_post_data->paywall2 = 0;	
				}
			}
			else if ( has_shortcode( $post_content, 'paywall' ) ) {
				$mp_post_data->paywall1 = 1;	
				$mp_post_data->paywall1_type = "shortcode";
				echo "<script>paywall1_type='shortcode';</script>";
			}		
			else if (strlen($mp_paidcontent1) > 3) {
				$mp_post_data->paywall1 = 1;		
				$mp_post_data->paywall1_type = "editor";
				echo "<script>paywall1_type='editor';</script>";		
			}
			else {
				$mp_post_data->paywall1 = 0;			
			}		
		}
		if ($mp_tippingButton == 1) {
			$mp_post_data->tipping = 1;		
		}		
		else {
			$mp_post_data->tipping = 0;		
		}
		$mp_tipme = get_post_meta($mypost_id, 'mp_tipme');
		if ($mp_tipme == 1) {
			$mp_post_data->tipme = 1;		
		}		
		else {
			$mp_post_data->tipme = 0;		
		}	
	}
	else {
		if ( has_shortcode( $post_content, 'paywall' ) ) {
			$mp_post_data->active = 1;
			$mp_post_data->paywall1 = 1;
			$mp_post_data->paywall1_type = "shortcode";	
			echo "<script>paywall1_type='shortcode';</script>";
			if (strlen($mp_paidcontent1) > 10) {
				$mp_post_data->active = 1;
				$mp_post_data->paywall2 = 1;
			}		
		}
		else if (strlen($mp_paidcontent1) > 10) {
			$mp_post_data->active = 1;
			$mp_post_data->paywall1 = 1;
			$mp_post_data->paywall1_type = "editor";
			echo "<script>paywall1_type='editor';</script>";
		}
		
		if ( has_shortcode( $post_content, 'tipme' )) {
			$mp_post_data->active = 1;
			$mp_post_data->tipme = 1;
		}
		if ($mp_tippingButton == 1) {
			$mp_post_data->active = 1;
			$mp_post_data->tipping = 1;
		}
	}
	}
}



function mp_general_info($mp_post_data) {
	// prepare data
	// prepare data sources	
	$blogpath = get_bloginfo($show = 'wpurl') . "/";
	echo "<script>mp_blogpath = '" . $blogpath . "';</script>";
	$mp_post_data->blogpath = $blogpath;
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$mp_post_data->actuallink = $actual_link;
	$mypost_id = url_to_postid($actual_link);
	echo "<script>mp_mypostid='" . esc_js($mypost_id) . "';</script>";
	$mp_post_data->postid = $mypost_id;
	$mp_post_data->url = $actual_link;
	global $wpdb;
	$table_name = $wpdb->prefix . 'mediopay';	
	$mp_myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" ); 
		
	// referal
	$mp_post_data->ref = $mp_myrows[0]->ref;
	echo "<script>refQuota='" . esc_js($mp_post_data->ref) . "';</script>";
	if (isset($_GET["ref"]) AND $mp_post_data->ref !== "0.0") {
		$mp_post_data->affiliate = 1;
		$mp_post_data->affiliate_partner = $_GET["ref"];	
		$mp_post_data->ref = $mp_myrows[0]->ref;	
		//echo "<script>console.log(mp_refID = '" . $_GET["ref"] . "');</script>";	
	}
	// Thresholds
	if (isset($mp_myrows[0]->threshold)) {
		if ($mp_myrows[0]->threshold !== "") {
				$mp_post_data->threshold = $mp_myrows[0]->threshold;
				echo "<script>mp_threshold = '" . $mp_post_data->threshold . "';</script>";	
				echo "<script>mp_threshold_active = 1;</script>";	
		}
		else {
			$mp_post_data->threshold = 0;	
			echo "<script>mp_threshold_active = 0;</script>";			
		}
	}
	else {
		$mp_post_data->threshold = 0;	
		echo "<script>mp_threshold_active = 0;</script>";	
	}		
	$mp_amountall = get_post_meta( $mypost_id, 'IncomeAll', true );
	if (isset($mp_amountall)) {
		if ($mp_amountall !== "") {
			$mp_post_data->incomeall = $mp_amountall;	
		}
		else {
			$mp_post_data->incomeall = 0;
		}
	}	
	else {
		$mp_post_data->incomeall = 0;	
	}
	if ($mp_post_data->threshold !== 0 and $mp_post_data->incomeall >= $mp_post_data->threshold) {
		$mp_post_data->threshold_achieved = 1;	
		$mp_sponsor = get_post_meta( $mypost_id, 'mp_sponsor', true );
		if (strlen($mp_sponsor) > 3) {
			$mp_post_data->sponsor = $mp_sponsor;	
			//$mp_post_data->sponsor = strlen($mp_sponsor);
		}
		else {
			$mp_post_data->sponsor = 0;		
		}
	}
	else {
		$mp_post_data->threshold_achieved = 0;
		$mp_post_data->sponsor = 0;
	}
	
	// paywhatyouwant
	if (isset($mp_myrows[0]->paywhatyouwant)) {
		if ($mp_myrows[0]->paywhatyouwant == "yes") {
			$mp_post_data->paywhatyouwant = 1;	
			echo "<script>mp_pwyw = 1;</script>";	
		}
		else {
			$mp_post_data->paywhatyouwant = 0;	
			echo "<script>mp_pwyw = 0;</script>";	
		}		
	}
	else {
		$mp_post_data->paywhatyouwant = 0;
		echo "<script>mp_pwyw = 0;</script>";		
	}

		
	
	// Secret
	if (isset($mp_myrows[0]->secret)) {
		$faucetyes = get_post_meta( $mypost_id, 'faucet', true );
		if ($faucetyes === "yes") {
			$mp_post_data->nonce = time();
			$mp_post_data->faucet = 1;
			echo "<script>mp_nonce = '" . $mp_post_data->nonce . "';</script>";
			$posqm = strpos($actual_link, "?");
			if ($posqm !== " " && $posqm > 0) {
				$actual_link2 = substr($actual_link, 0, $posqm);
			}
			else {
				$actual_link2 = $actual_link;		
			}
			$secretmessage = $mp_myrows[0]->secret . $mp_post_data->nonce . $blogpath . $actual_link2;
			$mp_post_data->linksignature = hash('sha256', $secretmessage);
			echo "<script>mp_linksig = '?signature=" . $mp_post_data->linksignature . "&nonce=" . $mp_post_data->nonce . "&blogpath=" . $blogpath . "&postlink=" . $actual_link2 . "';</script>";	
		}
		else {
			$mp_post_data->faucet = 0;
			echo "<script>mp_linksig = \"\";</script>";		
		}	
	}	
	else {
		$mp_post_data->faucet = 0;
		echo "<script>mp_linksig = \"\";</script>";
	}
	
	// Secret for Cookies
	$mp_post_data->secret1 = get_post_meta( $mypost_id, 'meta-secretword-1', true ); 
	$mp_post_data->secret2 = get_post_meta( $mypost_id, 'meta-secretword-2', true );	
		
	
	// Sharing quote
	$mp_post_data->share = get_post_meta( $mypost_id, 'meta_share', true );
	if (strlen($mp_post_data->share) == 0) {
		$mp_post_data->share = $mp_myrows[0]->sharingQuote; 
	}		
	echo "<script>sharingQuota='" . esc_js($mp_post_data->share) . "';</script>";
	// Newcounter
	$mp_post_data->newcounter = get_post_meta( $mypost_id, 'meta-newcounter', true );
	if (strlen($mp_post_data->newcounter) > 0) {
		echo "<script>mp_newCounter ='" . $mp_post_data->newcounter . "';</script>";	
	}
	else {
		echo "<script>mp_newCounter ='no';</script>";		
	}	
	//echo $mp_post_data->newcounter;
	$mp_post_data->newdata = get_post_meta( $mypost_id, 'meta-newdata', true );
	echo "<script>mp_newData ='" . $mp_post_data->newdata . "';</script>";
	// address & currency
	$mp_post_data->address = $mp_myrows[0]->address;
	echo "<script>mp_theAddress='" . esc_js($mp_post_data->address) . "';</script>";
	$mp_post_data->currency = $mp_myrows[0]->currency;
	echo "<script>mp_theCurrency='" . esc_js($mp_post_data->currency) . "';</script>";
	
	// Metanet active
	$mp_post_data->noMetanet = $mp_myrows[0]->noMetanet;
	echo "<script>nometanet='" . esc_js($mp_post_data->noMetanet) . "';</script>";
	
	// Design		
	$mp_post_data->barColor = $mp_myrows[0]->barColor;
	echo "<script>mp_barColor='" . esc_js($mp_post_data->barColor) . "';</script>";
	if (isset($mp_myrows[0]->linkColor)) {	
		$mp_post_data->linkColor = $mp_myrows[0]->linkColor;
		if (strlen($mp_post_data->linkColor) > 3) {
			echo "<script>mp_linkColor ='" . $mp_post_data->linkColor . "';</script>";			
		}	
	}
	
	// Data for opreturn message
	echo "<script>dataLink=\"" . get_permalink() . "\";</script>";
	echo "<script>dataTitle=\"" . get_the_title() . "\";dataTitle = encodeURI(dataTitle); </script>";
	if(is_preview()){ 
		echo "<script>var mp_preview='yes';</script>";
	}
	else { 
		echo "<script>var mp_preview='no';</script>";
	};
	$dataContent = get_the_content();
	$dataContent = substr($dataContent, 0, 168);
	$dataContent = wp_strip_all_tags( $dataContent );
	echo "<script>dataContent=\"" . esc_js($dataContent) . "\";</script>";
	
	// second receiver
	
	$mp_post_data->address2 = get_post_meta( $mypost_id, 'address2', true);
	if (isset ($mp_post_data->address2)) {
		if (strlen($mp_post_data->address2) > 0) {
			echo "<script>mp_address2 ='" . $mp_post_data->address2 . "';</script>";	
			$mp_post_data->secondAddressShare = get_post_meta( $mypost_id, 'address2_share', true) / 100;			
				echo "<script>mp_secondAddressShare ='" . $mp_post_data->secondAddressShare . "';</script>";	
		}
		
		else {
			if (isset($mp_myrows[0]->address2)) {
				if (strlen($mp_myrows[0]->address2) > 0) {
					$mp_post_data->address2 = $mp_myrows[0]->address2;			
				}			
			}	
			echo "<script>mp_address2 ='" . $mp_post_data->address2 . "';</script>";
			$mp_post_data->secondAddressShare = get_post_meta( $mypost_id, 'address2_share', true);

			if (isset ($mp_post_data->secondAddressShare)) {
				if ($mp_post_data->secondAddressShare > 0) {
					$mp_post_data->secondAddressShare = $mp_post_data->secondAddressShare / 100;
				}
				else {
					$mp_post_data->secondAddressShare = $mp_myrows[0]->secondAddressShare;
				}
			}
			else {
				$mp_post_data->secondAddressShare = $mp_myrows[0]->secondAddressShare;			
			}	
			echo "<script>mp_secondAddressShare ='" . $mp_post_data->secondAddressShare . "';</script>";		
			
		}						
	}
	
	else {
	}
}


function mp_checkcookies($mp_post_data, $number) {
	$mp_post_data->cookies = 0;
	$sizeof = sizeof($_COOKIE);
	if ($number == 1) {
		$oursecret = $mp_post_data->secret1;
		$mp_post_data->cookie1 = 0;		
	}
	else {
		$oursecret = $mp_post_data->secret2;	
		$mp_post_data->cookie2 = 0;
	}
	if (strlen($oursecret) > 0) {
	for ($i=0; $i<$sizeof; $i++) {
		$cookiepos = strpos(implode($_COOKIE), $oursecret);
		if ($cookiepos !== false) {
			if ($number == 1) {
				$mp_post_data->cookie1 = 1;
			}		
			else {
				$mp_post_data->cookie2 = 1;	
			}			
		}	
	}
	}
}

function mp_paywall1_data($mp_post_data) {
	// prepare data sources	
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$mypost_id = url_to_postid($actual_link);
	global $wpdb;
	$table_name = $wpdb->prefix . 'mediopay';
	$table_name2 = $wpdb->prefix . 'mediopay_paidcontent';
	$mp_myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" ); 
	if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name2 ) ) === $table_name2 ) {
		$mp_myrows2 = $wpdb->get_results( "SELECT * FROM " . $table_name2 . " WHERE postid = " . $mypost_id ); 	
	}
	
	// Paywall-Message
	$mp_post_data->paywallmsg = get_post_meta( $mypost_id, 'PaywallMsg', true);
	if (strlen($mp_post_data->paywallmsg) == 0) {
		$mp_post_data->paywallmsg = (strlen($mp_myrows[0]->paywallMsg) > 0 AND $mp_myrows[0]->paywallMsg !== "none") ? $mp_myrows[0]->paywallMsg : $mp_post_data->paywallmsg = "Tip the author and continue reading.";	
	}		
	
	// Content behind
	if ($mp_post_data->paywall1_type == "shortcode") { 
	}
	else if (isset($mp_myrows2[0]->paidcontent1)) {
		$mp_post_data->paidcontent1 = $mp_myrows2[0]->paidcontent1;
		echo "<script>realContentLength=\"" . strlen($mp_post_data->paidcontent1) . "\";</script>";	
	}
	else {
		$mp_post_data->paidcontent1 = get_post_meta( $mypost_id, 'meta-paidcontent', true );
		echo "<script>realContentLength=\"" . strlen($mp_post_data->paidcontent1) . "\";</script>";		
	}	
	
	// amount
	$mp_post_data->amount = get_post_meta( $mypost_id, 'meta-amount', true ); 
	if (strlen($mp_post_data->amount) == 0 || $mp_post_data->amount == 0) {
		$mp_post_data->amount = $mp_myrows[0]->fixedAmount; 
	}		
	echo "<script>paymentAmount1=\"" . esc_js($mp_post_data->amount) . "\";</script>";
	
	
	
	// former buyers
		$new_data = get_post_meta( $mypost_id, 'meta-newdata', true );	
		if ($new_data == "yes") {
			$mp_post_data->buys1 = get_post_meta( $mypost_id, 'meta_buys1', true ); 
		}
		else if ($mp_post_data->paywall1_type == "editor") {
			$mp_post_data->buys1 = get_post_meta( $mypost_id, 'meta_buys1', true ); 
		}
		else if ($mp_post_data->paywall1_type == "shortcode") {
			$mp_post_data->buys1 = get_post_meta( $mypost_id, 'meta_buys2', true ); 
		}
		if (strlen($mp_post_data->buys1) > 0) {
			echo "<script>mp_buys1=" . $mp_post_data->buys1 . ";</script>";
		}
		else {
			echo "<script>mp_buys1=0;</script>";
		}
		
	// amount already paid
	$mp_income = get_post_meta( $mypost_id, 'IncomePW1', true );
	if (isset($mp_income) and $mp_income !== "" and $mp_income !== "0") {
		$mp_post_data->incomePW1 = $mp_income;	
	}
	else {
		$mp_post_data->incomePW1 = $mp_post_data->buys1 * $mp_post_data->amount;
	}
	$mp_amountall = get_post_meta( $mypost_id, 'IncomeAll', true );
	if ($mp_amountall == "") {
		$mp_post_data->incomeall = $mp_post_data->incomePW1;
	}				
	else if ($mp_amountall < $mp_post_data->incomePW1) {
		$mp_post_data->incomeall += $mp_post_data->incomePW1;	
	}
	else {
	}
		
	// Sharing
		
		if ($mp_post_data->share >= 0.0 ) {
		}	
		else {
			if ($new_data == "yes" || $mp_post_data->paywall1_type == "editor") {
			if ($mp_post_data->share >= 0.1 ) {
				$mp_post_data->first_buys1 = get_post_meta( $mypost_id, 'meta-first-buys1', true );
				echo "<script>mp_first_buys1='" . $mp_post_data->first_buys1 . "';</script>";
			}
			if ($mp_post_data->share >= 0.2 ) {	
				$mp_post_data->second_buys1 = get_post_meta( $mypost_id, 'meta-second-buys1', true );	
				echo "<script>mp_second_buys1='" . $mp_post_data->second_buys1 . "';</script>";
			}
			if ($mp_post_data->share >= 0.3 ) {	
				$mp_post_data->third_buys1 = get_post_meta( $mypost_id, 'meta-third-buys1', true );	
				echo "<script>mp_third_buys1='" . $mp_post_data->third_buys1 . "';</script>";
			}
			if ($mp_post_data->share >= 0.4 ) {
				$mp_post_data->fourth_buys1 = get_post_meta( $mypost_id, 'meta-fourth-buys1', true );
				echo "<script>mp_fourth_buys1='" . $mp_post_data->fourth_buys1 . "';</script>";
			}	
			}
			else if ($mp_post_data->paywall1_type == "shortcode") {
				if ($mp_post_data->share >= 0.1 ) {
				$mp_post_data->first_buys1 = get_post_meta( $mypost_id, 'meta-first-buys2', true );
				echo "<script>mp_first_buys1='" . $mp_post_data->first_buys1 . "';</script>";
			}
			if ($mp_post_data->share >= 0.2 ) {	
				$mp_post_data->second_buys1 = get_post_meta( $mypost_id, 'meta-second-buys2', true );	
				echo "<script>mp_second_buys1='" . $mp_post_data->second_buys1 . "';</script>";
			}
			if ($mp_post_data->share >= 0.3 ) {	
				$mp_post_data->third_buys1 = get_post_meta( $mypost_id, 'meta-third-buys2', true );	
				echo "<script>mp_third_buys1='" . $mp_post_data->third_buys1 . "';</script>";
			}
			if ($mp_post_data->share >= 0.4 ) {
				$mp_post_data->fourth_buys1 = get_post_meta( $mypost_id, 'meta-fourth-buys2', true );
				echo "<script>mp_fourth_buys1='" . $mp_post_data->fourth_buys1 . "';</script>";
			}	
			}	
		}				
}

function mp_paywall2_data($mp_post_data) {
	// prepare data sources	
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$mypost_id = url_to_postid($actual_link);
	global $wpdb;
	$table_name = $wpdb->prefix . 'mediopay';
	$table_name2 = $wpdb->prefix . 'mediopay_paidcontent';
		
	$mp_myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" ); 
	if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name2 ) ) === $table_name2 ) {
		$mp_myrows2 = $wpdb->get_results( "SELECT * FROM " . $table_name2 . " WHERE postid = " . $mypost_id ); 	
	}
	// Secret for Cookies
	$mp_post_data->secret1 = get_post_meta( $mypost_id, 'meta-secretword-1', true ); 
	$mp_post_data->secret2 = get_post_meta( $mypost_id, 'meta-secretword-2', true );	
	
	// Paywall-Message
	$mp_post_data->paywallmsg = get_post_meta( $mypost_id, 'PaywallMsg', true);
	if (strlen($mp_post_data->paywallmsg) == 0) {
		$mp_post_data->paywallmsg = (strlen($mp_myrows[0]->paywallMsg) > 0 AND $mp_myrows[0]->paywallMsg !== "none") ? $mp_myrows[0]->paywallMsg : $mp_post_data->paywallmsg = "Tip the author and continue reading.";	
	}		
	
	// Content behind
	if (isset($mp_myrows2[0]->paidcontent2)) {
			$mp_post_data->paidcontent2 = $mp_myrows2[0]->paidcontent2;
	}
	else {
		$mp_post_data->paidcontent2 = get_post_meta( $mypost_id, 'meta-paidcontent', true );	
	}
	// amount
	
	$mp_post_data->amount = get_post_meta( $mypost_id, 'meta-amount', true ); 
	if (strlen($mp_post_data->amount) == 0 || $mp_post_data->amount == 0) {
		$mp_post_data->amount = $mp_myrows[0]->fixedAmount; 
	}		
	echo "<script>paymentAmount2=\"" . esc_js($mp_post_data->amount) . "\";</script>";
	// second receiver
	
	$mp_post_data->address2 = get_post_meta( $mypost_id, 'address2', true);
	if (isset ($mp_post_data->address2)) {
		if (strlen($mp_post_data->address2) > 0) {
		}
		
		else {
			if (isset($mp_myrows[0]->address2)) {
				if (strlen($mp_myrows[0]->address2) > 0) {
					$mp_post_data->address2 = $mp_myrows[0]->address2;			
				}			
			}	
			echo "<script>mp_address2 ='" . $mp_post_data->address2 . "';</script>";
			$mp_post_data->secondAddressShare = get_post_meta( $mypost_id, 'address2_share', true);
			if (isset ($mp_post_data->secondAddressShare)) {
				if ($mp_post_data->secondAddressShare > 0) {
					$mp_post_data->secondAddressShare = $mp_post_data->secondAddressShare / 100;
				}
				else {
					$mp_post_data->secondAddressShare = 0;
				}
			}
			else {
				$mp_post_data->secondAddressShare = 0;			
			}			
			
		}								
	}
	
	// former buyers
	$new_data = get_post_meta( $mypost_id, 'meta-newdata', true );	
		if ($new_data == "yes") {
			$mp_post_data->buys2 = get_post_meta( $mypost_id, 'meta_buys2', true ); 
		}
		else {
			$mp_post_data->buys2 = get_post_meta( $mypost_id, 'meta_buys1', true ); 		
		}
		if ($mp_post_data->buys2 !== "") {
			echo "<script>mp_buys2=" . $mp_post_data->buys2 . ";</script>";
		}
		else {
			echo "<script>mp_buys2=0;</script>";
		}
		
		
	// Sharing
		
		if ($mp_post_data->share >= 0.0 ) {
		}	
		else {
			if ($new_data == "yes") {
				if ($mp_post_data->share >= 0.1 ) {
				$mp_post_data->first_buys2 = get_post_meta( $mypost_id, 'meta-first-buys2', true );
				echo "<script>mp_first_buys2='" . $mp_post_data->first_buys2 . "';</script>";
				}
				if ($mp_post_data->share >= 0.2 ) {	
				$mp_post_data->second_buys2 = get_post_meta( $mypost_id, 'meta-second-buys2', true );	
				echo "<script>mp_second_buys2='" . $mp_post_data->second_buys2 . "';</script>";
				}
				if ($mp_post_data->share >= 0.3 ) {	
				$mp_post_data->third_buys2 = get_post_meta( $mypost_id, 'meta-third-buys2', true );	
				echo "<script>mp_third_buys2='" . $mp_post_data->third_buys2 . "';</script>";
				}
				if ($mp_post_data->share >= 0.4 ) {
				$mp_post_data->fourth_buys2 = get_post_meta( $mypost_id, 'meta-fourth-buys2', true );
				echo "<script>mp_fourth_buys2='" . $mp_post_data->fourth_buys2 . "';</script>";
				}		
			}	
			else {
			if ($mp_post_data->share >= 0.1 ) {
				$mp_post_data->first_buys2 = get_post_meta( $mypost_id, 'meta-first-buys1', true );
				echo "<script>mp_first_buys2='" . $mp_post_data->first_buys2 . "';</script>";
			}
			if ($mp_post_data->share >= 0.2 ) {	
				$mp_post_data->second_buys2 = get_post_meta( $mypost_id, 'meta-second-buys1', true );	
				echo "<script>mp_second_buys2='" . $mp_post_data->second_buys2 . "';</script>";
			}
			if ($mp_post_data->share >= 0.3 ) {	
				$mp_post_data->third_buys2 = get_post_meta( $mypost_id, 'meta-third-buys1', true );	
				echo "<script>mp_third_buys2='" . $mp_post_data->third_buys2 . "';</script>";
			}
			if ($mp_post_data->share >= 0.4 ) {
				$mp_post_data->fourth_buys2 = get_post_meta( $mypost_id, 'meta-fourth-buys1', true );
				echo "<script>mp_fourth_buys2='" . $mp_post_data->fourth_buys2 . "';</script>";
			}		
			}			
	}
	
	// Income
	
	$mp_income2 = get_post_meta( $mypost_id, 'IncomePW2', true );
	if (isset($mp_income2) and $mp_income2 !== "") {
		$mp_post_data->incomePW2 = $mp_income2;	
	}
	else {
		$mp_post_data->incomePW2 = $mp_post_data->buys2 * $mp_post_data->amount;
	}
	if (isset($mp_amountall)) {
		if ($mp_amountall == "" or $mp_amountall < ($mp_post_data->incomePW2 + $mp_post_data->incomePW1)) {
			$mp_post_data->incomeall += $mp_post_data->incomePW2;
		}	
	}
}

function mp_tipping_data($mp_post_data) {
	// load data
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$mypost_id = url_to_postid($actual_link);
	global $wpdb;
	$table_name = $wpdb->prefix . 'mediopay';
	$mp_myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" ); 	
	
	// Editable and Amount
	if (isset($mp_myrows[0]->editableTips)) {
		$mp_post_data->editableTips = $mp_myrows[0]->editableTips;
		echo "<script>mp_editable_tips='" . $mp_post_data->editableTips . "';</script>";
	}
	else {
		$mp_post_data->editableTips = 0;
	}
		$mp_post_data->tip_amount = get_post_meta( $mypost_id, 'meta-tipAmount', true );
		if (strlen($mp_post_data->tip_amount) == 0) {
			$mp_post_data->tip_amount = $mp_myrows[0]->fixedTipAmount; 
		}	
		echo "<script>tipAmount=\"" . esc_js($mp_post_data->tip_amount)	 . "\";</script>";
	
	// Message after tip
	$mp_post_data->thankyou = get_post_meta( $mypost_id, 'meta-textarea', true ); 
	if (strlen($mp_post_data->thankyou) == 0) {
		if (strlen($mp_myrows[0]->fixedThankYou) > 0) {
			$mp_post_data->thankyou = $mp_myrows[0]->fixedThankYou;
		}
	}
	echo "<script>mp_thankYou=\"" . esc_js($mp_post_data->thankyou) . "\";</script>";
	
	// Message on tipping field
	$mp_post_data->tippingMsg = $mp_myrows[0]->tippingMsg;
	if (strlen($mp_post_data->tippingMsg) == 0) {
		$mp_post_data->tippingMsg = "Be generous and tipshare this post.";
	}
	
	// Tipped Amount
	$mp_post_data->mp_tipped_amount = get_post_meta ($mypost_id, 'meta-tipped-amount');
	if (isset($mp_post_data->mp_tipped_amount)) {
		if (gettype($mp_post_data->mp_tipped_amount)	== "array") {
			 if (isset($mp_post_data->mp_tipped_amount[0])) {
			 	 $mp_post_data->mp_tipped_amount = $mp_post_data->mp_tipped_amount[0];
			 	 $mp_post_data->mp_tipped_amount = number_format($mp_post_data->mp_tipped_amount, 2, '.', ' ');
				 echo "<script>mp_tippedAmount ='" . $mp_post_data->mp_tipped_amount . "';</script>";	
			 }
			 else {
				 $mp_post_data->mp_tipped_amount	= 0.00;
				 echo "<script>mp_tippedAmount ='" . $mp_post_data->mp_tipped_amount . "';</script>";	 
			 }
		}
		else {
			 if ($mp_post_data->mp_tipped_amount > 0) {
			 	$mp_post_data->mp_tipped_amount = number_format($mp_post_data->mp_tipped_amount, 2, '.', ' ');
				echo "<script>mp_tippedAmount ='" . $mp_post_data->mp_tipped_amount[0] . "';</script>";	
			}
		}	
	}
	if (isset($mp_amountall)) {
		if ($mp_amountall == "") {
			$mp_post_data->incomeall += $mp_post_data->mp_tipped_amount;
		}
	}	
	
	// number of tips
	$mp_post_data->tips = get_post_meta( $mypost_id, 'meta_tips', true );
	if ($mp_post_data->tips == ""){
		$mp_post_data->tips = 0;
	}	
	echo "<script>mp_tips=" . $mp_post_data->tips . ";</script>";
	
	// former tippers
	if ($mp_post_data->share >= 0.0 ) {	
	}	
	else {
		if ($mp_post_data->share >= 0.1 AND $mp_post_data->tips > 0) {
			$mp_post_data->first_tips = get_post_meta( $mypost_id, 'meta-first-tips', true );
			echo "<script>mp_first_tips='" . $mp_post_data->first_tips . "';</script>";
		}				
		if ($mp_post_data->share >= 0.2 AND $mp_post_data->tips > 1) {	
			$mp_post_data->second_tips = get_post_meta( $mypost_id, 'meta-second-tips', true ); 
			echo "<script>mp_second_tips='" . $mp_post_data->second_tips . "';</script>";
		}
		if ($mp_post_data->share >= 0.3 AND $mp_post_data->tips > 2) {	
			$mp_post_data->third_tips = get_post_meta( $mypost_id, 'meta-third-tips', true );
			echo "<script>mp_third_tips='" . $mp_post_data->third_tips . "';</script>";
		}
		if ($mp_post_data->share >= 0.4 AND $mp_post_data->tips > 3) {
			$mp_post_data->fourth_tips = get_post_meta( $mypost_id, 'meta-fourth-tips', true );	
			echo "<script>mp_fourth_tips='" . $mp_post_data->fourth_tips . "';</script>";
		}		
	}	
	
	// Income
	
	$mp_incomeTip = get_post_meta( $mypost_id, 'IncomeTp1', true );
	if (isset($mp_incomeTip2)) {
		$mp_post_data->incomeTp1 = $mp_incomeTip;	
	}
	else {
		$mp_post_data->incomeTp1 = "none";
	}				
}

function mp_tipme_data($mp_post_data, $mp_amount, $mp_tipMeMsg) {
	// load data
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$mypost_id = url_to_postid($actual_link);
	global $wpdb;
	$table_name = $wpdb->prefix . 'mediopay';
	$mp_myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" ); 	
	
	// Editable and Amount
	if (isset($mp_myrows[0]->editableTips)) {
		$mp_post_data->editableTips = $mp_myrows[0]->editableTips;
		echo "<script>mp_editable_tips='" . $mp_post_data->editableTips . "';</script>";
	}
	else {
		$mp_post_data->editableTips = 0;
	}
	//if ($mp_amount !== "no") {
		$mp_post_data->tipme_amount = get_post_meta( $mypost_id, 'meta-tipAmount', true );
		if (strlen($mp_post_data->tipme_amount) == 0) {
			$mp_post_data->tipme_amount = $mp_myrows[0]->fixedTipAmount; 
		}	
		echo "<script>tipmeAmount=\"" . esc_js($mp_post_data->tipme_amount)	 . "\";</script>";
	//}
	
	
	// message after tip
			
	$mp_post_data->tipme_thankyou = $mp_myrows[0]->fixedThankYou;
	echo "<script>mp_tipme_thankYou=\"" . esc_js($mp_post_data->tipme_thankyou) . "\";</script>";
	
	// message on tipme field
	if ($mp_tipMeMsg == "no") {
		$mp_post_data->tipmeMsg = $mp_myrows[0]->tippingMsg;
		if (strlen($mp_post_data->tipmeMsg) == 0) {
			$mp_post_data->tipmeMsg = "Be generous and tipshare this post.";
		}
		echo "<script>mp_tipme_Msg=\"" . esc_js($mp_post_data->tipmeMsg) . "\";</script>";
	}
	else {
		$mp_post_data->tipmeMsg = $mp_tipMeMsg;		
	}
	
	// Tipped Amount
	$mp_post_data->mp_tipped_amount = get_post_meta ($mypost_id, 'meta-tipped-amount');
	
	if (isset($mp_post_data->mp_tipped_amount)) {
		if (gettype($mp_post_data->mp_tipped_amount)	== "array") {
			 if (isset($mp_post_data->mp_tipped_amount[0])) {
			 	 $mp_post_data->mp_tipped_amount = $mp_post_data->mp_tipped_amount[0];
			 	 $mp_post_data->mp_tipped_amount = number_format($mp_post_data->mp_tipped_amount, 2, '.', ' ');
				 echo "<script>mp_tippedAmount ='" . $mp_post_data->mp_tipped_amount . "';</script>";	
			 }
			 else {
				 $mp_post_data->mp_tipped_amount	= 0.00;
				 echo "<script>mp_tippedAmount ='" . $mp_post_data->mp_tipped_amount . "';</script>";	 
			 }
		}
		else {
			 if ($mp_post_data->mp_tipped_amount > 0) {
			 	$mp_post_data->mp_tipped_amount = number_format($mp_post_data->mp_tipped_amount, 2, '.', ' ');
					echo "<script>mp_tippedAmount ='" . $mp_post_data->mp_tipped_amount[0] . "';</script>";	
			}
		}	
	}
	// number of tips
	$mp_post_data->tips = get_post_meta( $mypost_id, 'meta_tips', true );
	if ($mp_post_data->tips == ""){
		$mp_post_data->tips = 0;
	}	
	echo "<script>mp_tips=" . $mp_post_data->tips . ";</script>";
	// former tippers
	if ($mp_post_data->share >= 0.0 ) {	
	}	
	else {
		if ($mp_post_data->share >= 0.1 AND $mp_post_data->tips > 0) {
			$mp_post_data->first_tips = get_post_meta( $mypost_id, 'meta-first-tips', true );
			echo "<script>mp_first_tips='" . $mp_post_data->first_tips . "';</script>";
		}				
		if ($mp_post_data->share >= 0.2 AND $mp_post_data->tips > 1) {	
			$mp_post_data->second_tips = get_post_meta( $mypost_id, 'meta-second-tips', true ); 
			echo "<script>mp_second_tips='" . $mp_post_data->second_tips . "';</script>";
		}
		if ($mp_post_data->share >= 0.3 AND $mp_post_data->tips > 2) {	
			$mp_post_data->third_tips = get_post_meta( $mypost_id, 'meta-third-tips', true );
			echo "<script>mp_third_tips='" . $mp_post_data->third_tips . "';</script>";
		}
		if ($mp_post_data->share >= 0.4 AND $mp_post_data->tips > 3) {
			$mp_post_data->fourth_tips = get_post_meta( $mypost_id, 'meta-fourth-tips', true );	
			echo "<script>mp_fourth_tips='" . $mp_post_data->fourth_tips . "';</script>";
		}		
	}	
	// Income
	
	$mp_incomeTip2 = get_post_meta( $mypost_id, 'IncomeTp2', true );
	if (isset($mp_incomeTip2)) {
		$mp_post_data->incomeTp2 = $mp_incomeTip2;	
	}
	else {
		$mp_post_data->incomeTp2 = "none";
	}		
	if (isset($mp_amountall)) {	
		if ($mp_amountall == "") {
			$mp_post_data->incomeall += $mp_post_data->incomeTp2;
		}				
	}
}





function mp_create_object($mp_post_data) {
	$path = plugin_dir_url( 'mediopay.php') . "mediopay/lib/";
	echo "<script>MedioPayPath = '" . $path . "';</script>";
	$blogpath = get_bloginfo($show = 'wpurl');
	echo "<script>mp_blogpath = '" . $blogpath . "';</script>";
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$mypost_id = url_to_postid($actual_link);
	global $wpdb;
	$table_name = $wpdb->prefix . 'mediopay';
	$mp_myrows = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = 1" ); 
	
	/*if (isset($mp_myrows[0]->alignLeft)) {		
		$mp_post_data->alignLeft = $mp_myrows[0]->alignLeft;
		echo "<script>mp_alignLeft = '" . $mp_post_data->alignLeft . "';</script>";
	}
	else {
		$mp_post_data->alignLeft = "no";
		echo "<script>mp_alignLeft = '" . $mp_post_data->alignLeft . "';</script>";
	} */
	
	if (isset($_GET["ref"])) {
	}	
	
	if (isset($_GET["hash"])) {
		$mp_hash = $_GET["hash"];
		$mp_nonce = $_GET["nonce"];
		//echo "<script>console.log('" .  $mp_nonce . "');</script>";
		if ( isset($mp_myrows[0]->secret)) {
			$secret = $mp_myrows[0]->secret;
			$posqm = strpos($actual_link, "?");
			$actual_link = substr($actual_link, 0, $posqm);
			$message = $secret . $mp_nonce . $actual_link;
			$checkhash = hash("sha256", $message);
			if ($checkhash == $mp_hash) {
				$mp_post_data->yenpoints = "yes";
				echo "<script>mp_yenpoints = 'yes';</script>";			
			}
			else {
				echo "<script>mp_yenpoints = 'no';</script>";		
			}
		}		
	}		
	if (isset($mp_myrows[0]->threshold)) {
		$mp_post_data->threshold = $mp_myrows[0]->threshold;
		echo "<script>mp_threshold = '" . $mp_post_data->threshold . "';</script>";	
	}	
	
	if (isset($mp_myrows[0]->secret)) {
		$faucetyes = get_post_meta( $mypost_id, 'faucet', true );
		if ($faucetyes === "yes") {
			$mp_post_data->nonce = time();
			echo "<script>mp_nonce = '" . $mp_post_data->nonce . "';</script>";
			$posqm = strpos($actual_link, "?");
			if ($posqm !== " " && $posqm > 0) {
				$actual_link2 = substr($actual_link, 0, $posqm);
			}
			else {
				$actual_link2 = $actual_link;		
			}
			$secretmessage = $mp_myrows[0]->secret . $mp_post_data->nonce . $blogpath . $actual_link2;
			$mp_post_data->linksignature = hash('sha256', $secretmessage);
			echo "<script>mp_linksig = '?signature=" . $mp_post_data->linksignature . "&nonce=" . $mp_post_data->nonce . "&blogpath=" . $blogpath . "&postlink=" . $actual_link2 . "';</script>";	
		}
		else {
			echo "<script>mp_linksig = \"\";</script>";		
		}	
	}	
	else {
		echo "<script>mp_linksig = \"\";</script>";
	}

	$mp_post_data->secret1 = get_post_meta( $mypost_id, 'meta-secretword-1', true ); 
	$mp_post_data->secret2 = get_post_meta( $mypost_id, 'meta-secretword-2', true );
	$mp_post_data->paywallmsg = get_post_meta( $mypost_id, 'PaywallMsg', true);
	if (strlen($mp_post_data->paywallmsg) == 0) {
		$mp_post_data->paywallmsg = (strlen($mp_myrows[0]->paywallMsg) > 0 AND $mp_myrows[0]->paywallMsg !== "none") ? $mp_myrows[0]->paywallMsg : $mp_post_data->paywallmsg = "Tip the author and continue reading.";	
	}		

	//var_dump($mp_myrows[0]);
	if (isset($mp_myrows[0]->editableTips)) {
		$mp_post_data->editableTips = $mp_myrows[0]->editableTips;
		echo "<script>mp_editable_tips='" . $mp_post_data->editableTips . "';</script>";
	}
	/*else {
		$mp_post_data->editableTips = 'noo';
	}*/
	
	$mp_post_data->paidcontent = get_post_meta( $mypost_id, 'meta-paidcontent', true );
	
	//echo "<script>console.log('post data " . $mp_post_data->paidcontent . "');</script>";
	$mp_post_data->checkbox = get_post_meta( $mypost_id, 'mp_meta_checkbox', true );
	$mp_post_data->thankyou = get_post_meta( $mypost_id, 'meta-textarea', true ); 
	
	if (strlen($mp_post_data->thankyou) == 0) {
		if (strlen($mp_myrows[0]->fixedThankYou) > 0) {
			$mp_post_data->thankyou = $mp_myrows[0]->fixedThankYou;
		}
	}
	$mp_post_data->tippingMsg = $mp_myrows[0]->tippingMsg;
	if (strlen($mp_post_data->tippingMsg) == 0) {
		$mp_post_data->tippingMsg = "Be generous and tipshare this post.";
	}

	$mp_post_data->address2 = get_post_meta( $mypost_id, 'address2', true);
	if (isset ($mp_post_data->address2)) {
		if (strlen($mp_post_data->address2) > 0) {
		}
		else {
			if (isset($mp_myrows[0]->address2)) {
				if (strlen($mp_myrows[0]->address2) > 0) {
					$mp_post_data->address2 = $mp_myrows[0]->address2;			
				}			
			}		
		}
	}
	else {
		if (isset($mp_myrows[0]->address2)) {
				if (strlen($mp_myrows[0]->address2) > 0) {
					$mp_post_data->address2 = $mp_myrows[0]->address2;			
				}			
		}		
	}
	echo "<script>mp_address2 ='" . $mp_post_data->address2 . "';</script>";	
	$mp_post_data->secondAddressShare = get_post_meta( $mypost_id, 'address2_share', true);
	if (isset ($mp_post_data->secondAddressShare)) {
		if ($mp_post_data->secondAddressShare > 0) {
			$mp_post_data->secondAddressShare = $mp_post_data->secondAddressShare / 100;
		}
		else {
			if (isset($mp_myrows[0]->secondAddressShare)) {
				if (strlen($mp_myrows[0]->secondAddressShare) > 0) {
					$mp_post_data->secondAddressShare = $mp_myrows[0]->secondAddressShare;			
				}			
			}		
		}
	}
	else {
		if (isset($mp_myrows[0]->secondAddressShare)) {
				if (strlen($mp_myrows[0]->secondAddressShare) > 0) {
					$mp_post_data->secondAddressShare = $mp_myrows[0]->secondAddressShare;			
				}			
		}		
	}
	echo "<script>mp_secondAddressShare ='" . $mp_post_data->secondAddressShare . "';</script>";
	$mp_post_data->amount = get_post_meta( $mypost_id, 'meta-amount', true ); 
	if (strlen($mp_post_data->amount) == 0 || $mp_post_data->amount == 0) {
			$mp_post_data->amount = $mp_myrows[0]->fixedAmount; 
	}	
	$mp_post_data->fixedAmount = $mp_myrows[0]->fixedAmount; 
	echo "<script>mp_fixedAmount ='" . $mp_post_data->fixedAmount . "';</script>";
	
	$mp_post_data->tip_amount = get_post_meta( $mypost_id, 'meta-tipAmount', true );
	if (strlen($mp_post_data->tip_amount) == 0) {
			$mp_post_data->tip_amount = $mp_myrows[0]->fixedTipAmount; 
	}
	
		
	$mp_post_data->share  = get_post_meta( $mypost_id, 'meta_share', true );
	if (strlen($mp_post_data->share) == 0) {
			$mp_post_data->share = $mp_myrows[0]->sharingQuote;; 
	}	
	
	$mp_post_data->mp_tipped_amount = get_post_meta ($mypost_id, 'mp-tipped-amount');
	
	if (isset($mp_post_data->mp_tipped_amount)) {
		if (gettype($mp_post_data->mp_tipped_amount)	== "array") {
			 if (isset($mp_post_data->mp_tipped_amount[0])) {
					echo "<script>mp_tippedAmount ='" . $mp_post_data->mp_tipped_amount . "';</script>";	
			}
		}
		else {
			 if ($mp_post_data->mp_tipped_amount > 0) {
					echo "<script>mp_tippedAmount ='" . $mp_post_data->mp_tipped_amount . "';</script>";	
			}
		}	
	}

	$mp_post_data->newcounter = get_post_meta( $mypost_id, 'meta-newcounter', true );
	
	
	
	$mp_post_data->buys1 = get_post_meta( $mypost_id, 'meta_buys1', true ); 
	$mp_post_data->buys2 = get_post_meta( $mypost_id, 'meta_buys2', true ); 
	$mp_post_data->tips = get_post_meta( $mypost_id, 'meta_tips', true );
	$mp_post_data->first_buys1 = get_post_meta( $mypost_id, 'meta-first-buys1', true );
	$mp_post_data->second_buys1 = get_post_meta( $mypost_id, 'meta-second-buys1', true );	
	$mp_post_data->third_buys1 = get_post_meta( $mypost_id, 'meta-third-buys1', true );
	$mp_post_data->fourth_buys1 = get_post_meta( $mypost_id, 'meta-fourth-buys1', true );
	$mp_post_data->first_buys2 = get_post_meta( $mypost_id, 'meta-first-buys2', true );
	$mp_post_data->second_buys2 = get_post_meta( $mypost_id, 'meta-second-buys2', true );
	$mp_post_data->third_buys2 = get_post_meta( $mypost_id, 'meta-third-buys2', true );
	$mp_post_data->fourth_buys2 = get_post_meta( $mypost_id, 'meta-fourth-buys2', true );
	$mp_post_data->first_tips = get_post_meta( $mypost_id, 'meta-first-tips', true );
	$mp_post_data->second_tips = get_post_meta( $mypost_id, 'meta-second-tips', true ); 
	$mp_post_data->third_tips = get_post_meta( $mypost_id, 'meta-third-tips', true );
	$mp_post_data->fourth_tips = get_post_meta( $mypost_id, 'meta-fourth-tips', true );
	$mp_post_data->address = $mp_myrows[0]->address;
	$mp_post_data->currency = $mp_myrows[0]->currency;
	$mp_post_data->ref = $mp_myrows[0]->ref;
	$mp_post_data->noMetanet = $mp_myrows[0]->noMetanet;
	$mp_post_data->barColor = $mp_myrows[0]->barColor;
	if (isset($mp_myrows[0]->linkColor)) {	
		$mp_post_data->linkColor = $mp_myrows[0]->linkColor;
		if (strlen($mp_post_data->linkColor) > 3) {
			echo "<script>mp_linkColor ='" . $mp_post_data->linkColor . "';</script>";			
		}	
	}


	// To JavaScript ... 

	if (strlen($mp_post_data->newcounter) > 0) {
		echo "<script>mp_newCounter ='" . $mp_post_data->newcounter . "';</script>";	
			if (strlen($mp_post_data->buys1) > 0) {
				echo "<script>mp_buys1=" . $mp_post_data->buys1 . ";</script>";
			}	
			else {
				echo "<script>mp_buys1=0;</script>";		
			}
			if (strlen($mp_post_data->buys2) > 0) {
				echo "<script>mp_buys2=" . $mp_post_data->buys2 . ";</script>";
			}	
			else {
				echo "<script>mp_buys2=0;</script>";		
			}			
			if (strlen($mp_post_data->tips) > 0) {
				echo "<script>mp_tips=" . $mp_post_data->tips . ";</script>";
			}	
			else {
				echo "<script>mp_tips=0;</script>";		
			}
			if (strlen($mp_post_data->first_buys1) > 0) {
				echo "<script>mp_first_buys1='" . $mp_post_data->first_buys1 . "';</script>";
			}	
			else {
				echo "<script>mp_first_buys1=0;</script>";		
			}
			if (strlen($mp_post_data->second_buys1) > 0) {
				echo "<script>mp_second_buys1='" . $mp_post_data->second_buys1 . "';</script>";
			}	
			else {
				echo "<script>mp_second_buys1=0;</script>";		
			}
			if (strlen($mp_post_data->third_buys1) > 0) {
				echo "<script>mp_third_buys1='" . $mp_post_data->third_buys1 . "';</script>";
			}	
			else {
				echo "<script>mp_third_buys1=0;</script>";		
			}
			if (strlen($mp_post_data->fourth_buys1) > 0) {
				echo "<script>mp_fourth_buys1='" . $mp_post_data->fourth_buys1 . "';</script>";
			}	
			else {
				echo "<script>mp_fourth_buys1=0;</script>";		
			}
			if (strlen($mp_post_data->first_buys2) > 0) {
				echo "<script>mp_first_buys2='" . $mp_post_data->first_buys2 . "';</script>";
			}	
			else {
				echo "<script>mp_first_buys2=0;</script>";		
			}
			if (strlen($mp_post_data->second_buys2) > 0) {
				echo "<script>mp_second_buys2='" . $mp_post_data->second_buys2 . "';</script>";
			}	
			else {
				echo "<script>mp_second_buys2=0;</script>";		
			}
			if (strlen($mp_post_data->third_buys2) > 0) {
				echo "<script>mp_third_buys2='" . $mp_post_data->third_buys2 . "';</script>";
			}	
			else {
				echo "<script>mp_third_buys2=0;</script>";		
			}
			if (strlen($mp_post_data->fourth_buys2) > 0) {
				echo "<script>mp_fourth_buys2='" . $mp_post_data->fourth_buys2 . "';</script>";
			}	
			else {
				echo "<script>mp_fourth_buys2=0;</script>";		
			}
			if (strlen($mp_post_data->first_tips) > 0) {
				echo "<script>mp_first_tips='" . $mp_post_data->first_tips . "';</script>";
			}	
			else {
				echo "<script>mp_first_tips=0;</script>";		
			}
			if (strlen($mp_post_data->second_tips) > 0) {
				echo "<script>mp_second_tips='" . $mp_post_data->second_tips . "';</script>";
			}	
			else {
				echo "<script>mp_second_tips=0;</script>";		
			}
			if (strlen($mp_post_data->third_tips) > 0) {
				echo "<script>mp_third_tips='" . $mp_post_data->third_tips . "';</script>";
			}	
			else {
				echo "<script>mp_third_tips=0;</script>";		
			}
			if (strlen($mp_post_data->fourth_tips) > 0) {
				echo "<script>mp_fourth_tips='" . $mp_post_data->fourth_tips . "';</script>";
			}	
			else {
				echo "<script>mp_fourth_tips=0;</script>";		
			}				
		}
		else {
			echo "<script>mp_newCounter ='no';</script>";	
		}		
		echo "<script>mp_thankYou=\"" . esc_js($mp_post_data->thankyou) . "\";</script>";
		echo "<script>mp_theAddress='" . esc_js($mp_post_data->address) . "';</script>";
		echo "<script>mp_theCurrency='" . esc_js($mp_post_data->currency) . "';</script>";
		echo "<script>sharingQuota='" . esc_js($mp_post_data->share) . "';</script>";
		echo "<script>refQuota='" . esc_js($mp_post_data->ref) . "';</script>";
		echo "<script>nometanet='" . esc_js($mp_post_data->noMetanet) . "';</script>";
		echo "<script>mp_barColor='" . esc_js($mp_post_data->barColor) . "';</script>";
		echo "<script>mp_mypostid='" . esc_js($mypost_id) . "';</script>";
		echo "<script>mp_checkBox='" . esc_js($mp_post_data->checkbox) . "';</script>";		
		echo "<script>dataLink='" . get_permalink() . "';</script>";
		echo "<script>dataTitle='" . encodeURI(get_the_title()) . "';</script>";
		// dataTitle = encodeURI(dataTitle); 
				
		if(is_preview()){ 
			echo "<script>var mp_preview='yes';</script>";
		}
		else { 
			echo "<script>var mp_preview='no';</script>";
		};
		$dataContent = get_the_content();
		$dataContent = substr($dataContent, 0, 168);
		$dataContent = wp_strip_all_tags( $dataContent );
		echo "<script>dataContent=\"" . esc_js($dataContent) . "\";</script>";
		echo "<script>paymentAmount1=\"" . esc_js($mp_post_data->amount) . "\";</script>";
		echo "<script>paymentAmount2=\"" . esc_js($mp_post_data->amount) . "\";</script>";
		echo "<script>tipAmount=\"" . esc_js($mp_post_data->tip_amount)	 . "\";</script>";
		if (strlen($mp_post_data->paidcontent) > 0) {
			echo "<script>realContentLength=\"" . strlen($mp_post_data->paidcontent) . "\";</script>";	
		}
		
}




?>