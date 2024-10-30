<?php

function mp_prepare_fading($mp_post_data, $mp_number, $thecontent) {
	if ($mp_number == "first") {	
		if ($mp_post_data->paywall1_type == "shortcode") {
			$mp_realContent = $thecontent;
			
		}
		else if ($mp_post_data->paywall1_type == "editor") {
			$mp_realContent = $mp_post_data->paidcontent1;
			if (strlen($mp_realContent) > 3) {
			}
			else {
				$mp_realContent = get_post_meta( $mp_post_data->postid, 'meta-paidcontent', true );		
			}

		}	
		
		$mp_lengthContent = strlen($mp_realContent);
		if ($mp_lengthContent > 400) {
			$mp_fading_content = substr( $mp_realContent, 0, 300);
			$mp_fading_content = wp_strip_all_tags( $mp_fading_content);
			$mp_post_data->fading_content1 = wp_strip_all_tags( $mp_fading_content);
		}
		else {
			$mp_fading_content = substr( $mp_realContent, 0, 100);
			$mp_fading_content = wp_strip_all_tags( $mp_fading_content);
			$mp_post_data->fading_content1 =  wp_strip_all_tags( $mp_fading_content);
		}			
	}
	else if ($mp_number == "second") {
		if (isset($mp_post_data->paidcontent2)) {
			$mp_realContent = $mp_post_data->paidcontent2;
		}	
		else {
			$mp_realContent = get_post_meta( $mypost_id, 'meta-paidcontent', true );		
		}
		$mp_lengthContent = strlen($mp_realContent);
		if ($mp_lengthContent > 300) {
			$mp_fading_content = substr($mp_realContent, 0, 300);
			$mp_fading_content = wp_strip_all_tags( $mp_fading_content);
			$mp_post_data->fading_content2 = wp_strip_all_tags( $mp_fading_content);
		}
		else {
			$mp_post_data->fading_content2 =  wp_strip_all_tags( $mp_post_data->paidcontent);
		}			
	}				
}

function mp_build_paywall($mp_post_data, $mp_class, $mp_id, $mp_number, $mp_counter, $buttonid, $mp_content) {
	$blogpath = get_bloginfo($show = 'wpurl') . "/";
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$posqm = strpos($actual_link, "?");		
	if ($posqm !== " " && $posqm > 0) {
		$actual_link2 = substr($actual_link, 0, $posqm);
	}
	else {
		$actual_link2 = $actual_link;		
	}
	$path = plugin_dir_url( 'mediopay.php');
	$path = $path . "mediopay/lib/";
	$tp_message = $mp_post_data->paywallmsg;
	if (strlen($tp_message) > 140)	{
		$tp_message = "<span class='paywallheader_smallest'>". $tp_message . "</span>";
	}
	else if (strlen($tp_message) > 80)	{
		$tp_message = "<span class='paywallheader_small'>". $tp_message . "</span>";
	}
	else {
		$tp_message = "<span class='paywallheader'>". $tp_message . "</span>";
	}
	if ($mp_number == "first") {
		$pwtype = "paywall1";
		if ($mp_post_data->paywall1_type == "editor") {
			$pwform = "editor";
			if (isset($mp_post_data->paidcontent1)) {
				$mp_content = $mp_post_data->paidcontent1;
			}
			else {
				$mp_content = get_post_meta( $mp_post_data->postid, 'meta-paidcontent', true );			
			}		
		}
		else {
			$pwform = "shortcode";
		}
		$spanid = "mp_pay1";
		$unlockable = "mp_unlockable1";
		if (strlen($mp_post_data->buys1) > 0) {
			$buys = $mp_post_data->buys1;
		}
		else {
			$buys = "no";		
		}
	}
	else if ($mp_number == "second") {
		$pwtype = "paywall2";
		$pwform = "editor";
		if (isset($mp_post_data->paidcontent2)) {
			$mp_content = $mp_post_data->paidcontent2;
		}
		else {
			$mp_content = get_post_meta( $mp_post_data->postid, 'meta-paidcontent', true );	
		}
		$spanid = "mp_pay2";
		$unlockable = "mp_unlockable2";
		if (strlen($mp_post_data->buys2) > 0) {
			$buys = $mp_post_data->buys2;
		}
		else {
			$buys = "no";		
		}
	}	
	if ($mp_post_data->paywhatyouwant == 1) {
	  $important_part = "<div id='editable_mbutton_wrap' class='mp_choose_amount' style='margin-top:10px'>How much do you want to pay?<br />					
				<input type='number' id='mp_editable_pw' style='width:100px' step='.01' value='" . number_format($mp_post_data->amount, 2, '.', ' ') . "'></input> " . $mp_post_data->currency . 
				"&nbsp; &nbsp; &nbsp; &nbsp; <input type='button' onclick='make_paywall1_object(\"" . $pwtype . "\"," . "\"" . $pwform . "\")' value='Pay'>
			</div><br />";	
	}
	else {
		$important_part = "<br />";
	}
	
	$mp_paywall = "<div class='" . $mp_class . "' id='" . $mp_id . "' style='background-color:" . $mp_post_data->barColor  . "'>";
	$stringlength = strlen($mp_content);
	
	if ($buys == "no") {
		$incounter = "";
		$shareQuote = 0;	
	}
	else {
		if (($mp_post_data->share * 10) > $buys) {
			switch ($buys) {
				case(0):
					$shareQuote = $mp_post_data->share*100;
					break;
				case(1):
					$shareQuote = $mp_post_data->share * 50;
					break;
				case(2):
					$shareQuote = $mp_post_data->share * 30;
					break;
				case(3):
					$shareQuote = $mp_post_data->share * 20;
					break;
			}
		}
		else {
			$shareQuote = 0;	
		}
	}
	$incounter = "<span class='icon'>&#x1F48E;</span>" . $buys . " buyers ";
	
	if ($mp_number == "first")	{
		if (isset($mp_post_data->incomePW1)) {
			$mp_income = $mp_post_data->incomePW1;	
			if ($mp_post_data->incomePW1 <= "0.00" and $buys > 0) {
				$mp_income = $buys * $mp_post_data->amount;
			}
			else {
				$mp_income = $mp_post_data->incomePW1;					
			}	
		}
		else {
			$mp_income = $buys * $mp_post_data->amount;		
		}
		$mp_threshold = "mp_threshold11";
		$mp_threshold2 = "mp_threshold12";
	}
	else if ($mp_number == "second") {
		if (isset($mp_post_data->incomePW1)) {
			if ($mp_post_data->incomePW2 == "0" and $buys > 0) {
				$mp_income = $buys * $mp_post_data->amount;	
			}
			else {
				$mp_income = $mp_post_data->incomePW2;			
			}
		}
		else {
			$mp_income = $buys * $mp_post_data->amount;		
		}
		$mp_threshold = "mp_threshold21";
		$mp_threshold2 = "mp_threshold22";	
	}
	$mp_income = floatval($mp_income);
	$mp_income = number_format($mp_income, 2, '.', ' ');
	$incounter .= "(" . $mp_income . " " . esc_html($mp_post_data->currency) . ")";
	if ($shareQuote !== 0) {
		$incounter .= "&nbsp;&nbsp;&nbsp;&nbsp<span class='icon'>&cent;</span> " . $shareQuote . " % revenue share";
	}	
	if ($mp_post_data->threshold !== 0) {
		$mp_post_data->incomeall = floatval($mp_post_data->incomeall);
		$mp_threshold_width = $mp_post_data->incomeall / $mp_post_data->threshold * 100 * 1.5;
		$incounter .=	
			"<br /><!--<p style='margin-left:10%; margin-right:10%'>--><br />" .
				number_format($mp_post_data->incomeall, 2, '.', ' ') . " of " . number_format($mp_post_data->threshold, 2, '.', ' ') . " " . $mp_post_data->currency . " reached to shut down the paywall for all.";
				if ($mp_post_data->paywhatyouwant == 1) {
					$incounter .=	
					" Pay " . number_format(($mp_post_data->threshold - $mp_post_data->incomeall), 2, '.', ' ') . " " . $mp_post_data->currency . " to become the sponsor of this post.
					";	
				}
				$incounter .=	"				
				<!--</p>--><br />
				<div style='width:161px; height:22px; padding:3px; border:2px solid black; margin:auto' id='" . $mp_threshold . "'>
				<div style='height:100%; width:" . $mp_threshold_width . "px' id='" . $mp_threshold2 . "'></div>
			</div>		
			";
	}
	if ($mp_post_data->faucet == 1) {
		$incounter .=
			"<br />
			<a href='https://mediopay.com/bsv-how?signature=" . 
			$mp_post_data->linksignature . "&nonce=" . $mp_post_data->nonce . "&blogpath=" . $blogpath . "&postlink=" . $actual_link2 .
			"' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></span><script>adjustlinkcolor()</script>";
	}
	else {
		$incounter .=
			"<br />
			<a href='https://mediopay.com/bsv-how' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></span><script>adjustlinkcolor()</script>";
	}
	
	if ($mp_post_data->paywhatyouwant == 0) {
		$mp_paywall .= $tp_message . "<br />" . $stringlength . "</b> characters for " . esc_html($mp_post_data->amount) . " " . esc_html($mp_post_data->currency);
	}
	else {
		$mp_paywall .= $tp_message . "<br />Pay what you want for " . $stringlength . "</b> characters behind the wall.";
	}
	$mp_paywall .= 	
			"<script>MedioPay_textColor('" . $mp_id . "');</script>
  			<br /><br />" .
  			$important_part . 
  			"<div class='money-button' id='" . $buttonid . "'></div>
  			<div id='" . 
  			$mp_counter . 
  			"' style='margin-top:7px'>" . 
  			$incounter . "
  			<br /></div> 
  			<a href='https://www.yenpoint.jp/Mediopay_Demo/bob.php?receiver=Alice@YenPoint.jp&amount=" . $mp_post_data->amount . "&link=" . esc_url($actual_link) . "&blogurl = " . $blogpath . "' target='_blank'>Use Yen Points</a>        	 
  			<br /><br /></div>
	   	<div id='" . $unlockable . "'>
	   	</div>";
	 return $mp_paywall;
}



function mp_build_tippings_field($mp_post_data, $mp_class, $mp_tip_type, $mp_counter_type, $mbuttonid, $mptiptype) {
	if ($mp_tip_type == "mp_tipFrame2") {
		$tp_message = $mp_post_data->tipmeMsg;
		$prefilled_amount = number_format($mp_post_data->tipme_amount, 2, '.', ' ');
	}
	else {
		$tp_message = $mp_post_data->tippingMsg;
		$prefilled_amount = $prefilled_amount = number_format($mp_post_data->tip_amount, 2, '.', ' ');
	}
	if ($mbuttonid == "editable_mbutton" OR $mbuttonid == "editable_mbutton2") {
	  $important_part = "<div id='editable_mbutton_wrap' class='mp_choose_amount' style='margin-top:10px'>How much do you want to tip?<br />					
				<input type='number' id='mp_editable' style='width:100px' step='.01' value='" . $prefilled_amount . "'></input> " . $mp_post_data->currency . 
				"&nbsp; &nbsp; &nbsp; &nbsp; <input type='button' onclick='make_paywall1_object(\"" . $mptiptype . "\")' value='Tip'>
			</div><br />";	
	}
	else {
		$important_part = "<br />";
	}
	if (isset($mp_post_data->tipme_hidebehind)) {
		if ($mp_post_data->tipme_hidebehind == 1) {
			$mp_hidebehind = "<span id='dontwant'><br /><input type='button' onclick='mp_showcontent()' value='I will pay later.'></span>";
			echo "<script>mp_hidebehind()</script>";
			echo "<script>mp_tipmehidebehind = 1</script>";
		}
		else {
			$mp_hidebehind = "";	
			echo "<script>mp_tipmehidebehind = 0</script>";
		}
	}	
	else {
		$mp_hidebehind = "";	
		echo "<script>mp_tipmehidebehind = 0</script>";
	}
	$incounter = 
		"<span class='icon'>&#10084;</span>" . 		
		$mp_post_data->tips .
		" Tips (" .
		esc_html($mp_post_data->mp_tipped_amount) .
		" " .  esc_html($mp_post_data->currency) .		
		")";			
	if (($mp_post_data->tips / 10) < $mp_post_data->share) {
		switch ($mp_post_data->tips) {
				case(0):
					$shareQuote = $mp_post_data->share*100;
					break;
				case(1):
					$shareQuote = $mp_post_data->share * 50;
					break;
				case(2):
					$shareQuote = $mp_post_data->share * 30;
					break;
				case(3):
					$shareQuote = $mp_post_data->share * 20;
					break;
			}			
		$incounter .= 
			"&nbsp;&nbsp;&nbsp;&nbsp<span class='icon'>&cent;</span> " .
			$shareQuote .
			"% revenue share";	
	}
	if ($mp_post_data->faucet == 1) {
		$posqm = strpos($mp_post_data->actuallink, "?");		
		if ($posqm !== " " && $posqm > 0) {
			$actual_link2 = substr($mp_post_data->actuallink, 0, $posqm);
		}		
		else {
			$actual_link2 = $mp_post_data->actuallink;		
		}
		$incounter .=
			"<br /><a href='https://mediopay.com/bsv-how?signature=" . 
			$mp_post_data->linksignature . "&nonce=" . $mp_post_data->nonce . "&blogpath=" . $mp_post_data->blogpath . "&postlink=" . $actual_link2 .
			"' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></span><script>adjustlinkcolor()</script>";
	}
	else {
		$incounter =
			"<br /><a href='https://mediopay.com/bsv-how' target='_blank' class='paywalllink'>Don't know how to pay?</a></span><script>adjustlinkcolor()</script>";
	}
	
	if (strlen($tp_message) > 140)	{
		$tp_message = "<span class='paywallheader_smallest'>". $tp_message . "</span>";
	}
	else if (strlen($tp_message) > 80)	{
		$tp_message = "<span class='paywallheader_small'>". $tp_message . "</span>";
	}
	else {
		$tp_message = "<span class='paywallheader'>". $tp_message . "</span>";
	}
	
	$tipping_field =
		"<div style='clear:both;'></div>
	   <br />
		<div class='" . $mp_class . "' id='" . $mp_tip_type . "' style='background-color:" . $mp_post_data->barColor  . "'>
		<script>MedioPay_textColor('" . $mp_tip_type . "');</script>" . 
		$tp_message .
		$important_part .
		"<div class='money-button' id='" . $mbuttonid . "'></div>
		<div id='" . $mp_counter_type . "'>" . $incounter . "</div>" .
		$mp_hidebehind .
		"</div><br />";
	return $tipping_field;
		
}


function mp_sponsorship($mp_post_data, $mp_sponsor) {
	$sponsor_field =
 	"<div style='clear:both;'></div>
	   <br />
		<div class='mp_frame1' id='" . $mp_sponsor . "' style='background-color:" . $mp_post_data->barColor  . "'>
		<script>MedioPay_textColor('" . $mp_sponsor . "');</script>
		<h3 style='font-family: arial'>This text was sponsored by " . $mp_post_data->sponsor . "</h3>
		<p>Send him a little bit of BitCoin to say thank you.</p>
		</div><br />";
	return $sponsor_field;
}




?>