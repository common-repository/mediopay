 function mp_handlePayment(payment, object) {
	mp_outputs = []	
	for (let i=0; i<payment.paymentOutputs.length; i++) {
		mp_outputs.push(payment.paymentOutputs[i].to)
	}  
	 mp_amount = object.amount; 
    mp_destination_address = payment.paymentOutputs[1].to;
    mp_paymail = payment.senderPaymail;
    mp_mypostid = object.mypostid;
    mp_numberof_payments = object.mp_buys;
    mp_userID = payment.userId;
	 mp_newCounter = object.newCounter;
	 mp_form = object.paywallform;
	 if (typeof object.paywall1 !== "undefined") {
		mp_method = "paywall1";	 
	 }
	 else if (typeof object.paywall2 !== "undefined") {
		mp_method = "paywall2";	 
	 }
	 if (typeof object.firstPartner !== "undefined" && object.firstPartner.length > 0) {
		 mp_firstPartner = object.firstPartner;	 
	 }	 
	 else {
		mp_firstPartner = "no";	 
	 }
	if (typeof object.secondPartner !== "undefined" && object.secondPartner.length > 0)  {
		 mp_secondPartner = object.secondPartner;	 
	 }	 
	 else {
		mp_secondPartner = "no";	 
	 }
	 if (typeof object.thirdPartner !== "undefined" && object.thirdPartner.length > 0) {
		 mp_thirdPartner = object.thirdPartner;	 
	 }	 
	 else {
		mp_thirdPartner = "no";	 
	 }
	if (typeof object.fourthPartner !== "undefined" && object.fourthPartner.length > 0) {
		 mp_fourthPartner = object.fourthPartner;	 
	 }	 
	 else {
		mp_fourthPartner = "no";	 
	 }
	 mp_preview = object.preview;
	 mp_sharing = object.sharing;	 
	 let mp_data = {
				'MedioPay_postid': mp_mypostid,
          	'MedioPay_outputs': mp_outputs,
          	'MedioPay_number': mp_numberof_payments,
          	'MedioPay_userID': mp_userID,
          	'Mediopay_newCounter': mp_newCounter,
          	'MedioPay_firstPartner': mp_firstPartner,
          	'MedioPay_secondPartner': mp_secondPartner,
          	'MedioPay_thirdPartner': mp_thirdPartner,
				'MedioPay_fourthPartner': mp_fourthPartner,
				'MedioPay_shareQuote': mp_sharing,
				'MedioPay_preview': mp_preview,
				'MedioPay_amount': mp_amount,
				'MedioPay_form': mp_form,
				'MedioPay_method': mp_method,
				'MedioPay_paymail': mp_paymail,
				'MedioPay_threshold_active': object.threshold_active,
				'MedioPay_threshold': object.threshold 
				
	 }
	 fetch(mp_blogpath + '/wp-json/mediopay/v1/throwcontent1/', {
       method: 'POST', // or 'PUT'
       headers: {
           'Content-Type': 'application/json',
       },
       body: JSON.stringify(mp_data),
    })
    .then((response) => {
        return response.json();
    })
    .then((myJson) => {
       responseobj = JSON.parse(myJson);
	    mp_unlockcontent(responseobj.method, payment, responseobj.secret, responseobj.paidcontent, responseobj.number);
    })
    .catch((error) => {
      console.error('There has been a problem with your fetch operation:', error);
    })	 
}
function mp_handleFailedPayment1(error) {
        alert("Sorry, the payment did not process correctly.")
}


async function mp_unlockcontent(method, payment, secret, paidcontent, method2) {
  let mp_frame  
  let mp_fade;
  let mp_unlock;
  let mp_inv1_after_ed;
  let mp_inv1_after_sc;
  let mp_inv2_after_sc;
  let mp_invTip_after;
  let number = method2;
  
  if (typeof payment.userId !== "undefined") {
  		mp_paymentid = payment.userId;
  }
  
  if (method == "editor") {  
		  if (number == 1) {
			  mp_unlock = document.getElementById("mp_unlockable1");
			  mp_fade = document.getElementById("mp_fade1");
 			 mp_frame = document.getElementById("mp_frame1");
		  }
		  if (number == 2) {
				mp_unlock = document.getElementById("mp_unlockable2");
				mp_fade = document.getElementById("mp_fade2");	
				mp_frame = document.getElementById("mp_frame2");	  
		  }
		  mp_invTip_after = document.getElementById("mp_tipFrame");
	}
	else if (number == 1 && method == "shortcode") {
		mp_fade = document.getElementById("mp_fade1");
		mp_frame = document.getElementById("mp_frame1");
		mp_unlock = document.getElementById("mp_unlockable1");	
		mp_inv1_after_sc = document.getElementById("mp_fade2");
		mp_inv2_after_sc = document.getElementById("mp_frame2");
		mp_invTip_after = document.getElementById("mp_tipFrame");
	}
 	if (mp_fade !== null) {
		mp_fade.parentNode.removeChild(mp_fade);	
  	}
	if (mp_unlock !== null && mp_unlock !== undefined) {	
		mp_unlock.innerHTML = paidcontent; 
		mp_unlock.classList.toggle("mp_unlocked");
	}
	if (mp_frame !== null) {
	   mp_frame.classList.toggle("mp_paid");
   	mp_frame.innerHTML="<em>Share <a href='" + dataLink + "?ref=" + mp_paymentid + "' class='paywalllink'>this link</a> to get a share of later payments.</em><br />See the <a href='https://www.mediopay.com/value-list/' class='paywalllink'>Ranking of the most valuable posts</a>";
  	}
	
	if (number == 1 && method == "editor") {  
		if (mp_invTip_after !== null) {
			mp_invTip_after.classList.remove("mp_invisible");	
		}	
	}  	
	
	let mp_current_url = window.location.href;
	if (mp_current_url.includes("?ref")) {
		let mp_ref_position = mp_current_url.indexOf("?ref");	
		mp_current_url = mp_current_url.substring(0, mp_ref_position);
	}	
	let mp_date = new Date();
	let mp_exdays = 800;
	mp_date.setTime(mp_date.getTime() + (mp_exdays*24*60*60*1000));
  	var mp_expires = "expires="+ mp_date.toUTCString();
  	
  	if (method == "editor") {
  		document.cookie = mp_current_url + "1X" + payment.userId + "X=" + secret + ";" + mp_expires;
  	}
  	if (method == "shortcode") {
  		document.cookie = mp_current_url + "2X" + payment.userId + "X=" + secret + ";" + mp_expires;	
  	}  	
  	
  	if (number == 1 && method == "shortcode") {
  		if (mp_inv1_after_sc !== null) {
  			mp_inv1_after_sc.classList.remove("mp_invisible");
  			if (mp_inv2_after_sc !== null) {
      		mp_inv2_after_sc.classList.remove("mp_invisible"); 	
      	}
      	if (mp_getcookie.includes(mp_current_url) && mp_getcookie.includes("1X") && mp_getcookie.includes("X=") )  {
				let mp_data = {
					'MedioPay_postid': mp_mypostid,
            	'mp_cookies': mp_getcookie,
            	'mp_position': "editor"	
	 			}
	 			fetch(mp_blogpath + '/wp-json/mediopay/v1/processcookies/', {
       			method: 'POST', // or 'PUT'
       			headers: {
           			'Content-Type': 'application/json',
       			},
       			body: JSON.stringify(mp_data),
    			})
    			.then((response) => {
        			return response.json();
    			})
    			.then((myJson) => {
       			myJson = JSON.parse(myJson);
					if (myJson.paidcontent.length > 5) {
				  		mp_unlockcontent("editor", 0, 0, myJson["paidcontent"], method2);
					} 
					else if (mp_checkBox == "yes" || method2 == "mp_tipme") {
       				if (method2 == "mp_tipme") {
							mp_makeObject("mp_tipme");								  					
					  	}	
					  	else {
							mp_makeObject('nothing');
						}
					}	
				  	
					else {
						mp_cookie_failed_2 = "yes";										
						mp_makeObject("editor", method2);
					}							         						
    			})	
    		}	
    	}
    	else if (mp_invTip_after !== null) {
			mp_invTip_after.classList.remove("mp_invisible");	
		}
    }				
	 adjustlinkcolor();
}


function mp_handleSuccessfulTip(payment, object) {
  mp_outputs = []	
	for (let i=0; i<payment.paymentOutputs.length; i++) {
		mp_outputs.push(payment.paymentOutputs[i].to)
	}   
	if (typeof object.tip !== "undefined") {
		mp_thanks = mp_thankYou;
	}
	else if (typeof object.tipme !== "undefined") {
		mp_thanks = mp_tipme_thankYou;
	}
    mp_destination_address = payment.paymentOutputs[1].to;
    mp_mypostid = object.mypostid;
    mp_numberof_payments = object.mp_buys;
    mp_userID = payment.userId;
	 mp_newCounter = object.newCounter;
	  mp_preview = object.preview;
	// console.log(mp_newCounter);
	 if (typeof object.firstPartner !== "undefined" && object.firstPartner.length > 0) {
		 mp_firstPartner = object.firstPartner;	 
	 }	 
	 else {
		mp_firstPartner = "no";	 
	 }
	if (typeof object.secondPartner !== "undefined" && object.secondPartner.length > 0) {
		 mp_secondPartner = object.secondPartner;	 
	 }	 
	 else {
		mp_secondPartner = "no";	 
	 }
	 if (typeof object.thirdPartner !== "undefined" && object.thirdPartner.length > 0) {
		 mp_thirdPartner = object.thirdPartner;	 
	 }	 
	 else {
		mp_thirdPartner = "no";	 
	 }
	if (typeof object.fourthPartner !== "undefined" && object.fourthPartner.length > 0) {
		 mp_fourthPartner = object.fourthPartner;	 
	 }	 
	 else {
		mp_fourthPartner = "no";	 
	 }
	 if (typeof object.tip2 !== "undefined") {
		mp_typeoftip = "tip2";	 
	 }
	 else {
		mp_typeoftip = "tip1";	 
	 }
	 mp_sharing = object.sharing;
	// console.log(object.sharing);
	let mp_data = {
	 			'MedioPay_postid': mp_mypostid,
            'MedioPay_outputs': mp_outputs,
            'MedioPay_number': mp_numberof_payments,
            'MedioPay_userID': mp_userID,
            'Mediopay_newCounter': mp_newCounter,
            'MedioPay_firstPartner': mp_firstPartner,
            'MedioPay_secondPartner': mp_secondPartner,
            'MedioPay_thirdPartner': mp_thirdPartner,
				'MedioPay_fourthPartner': mp_fourthPartner,
				'MedioPay_shareQuote': mp_sharing,
				'MedioPay_preview': mp_preview,
				'MedioPay_amount': object.amount,
				'MedioPay_type': mp_typeoftip
	 }
	 fetch(mp_blogpath + '/wp-json/mediopay/v1/handletips/', {
       method: 'POST', // or 'PUT'
       headers: {
           'Content-Type': 'application/json',
       },
       body: JSON.stringify(mp_data),
    })
    .then((response) => {
        return response.json();
    })
    .then((myJson) => {
       responseobj = JSON.parse(myJson);
        if (object.tip == 1) {	   
  				document.getElementById("mp_tipFrame").innerHTML = "<h2>" + mp_thanks + "</h2><em>Share <a href='" + dataLink + "?ref=" + payment.userId + "' class='paywalllink'>this link</a> to get a share of later payments.</em><br />See the <a href='https://www.mediopay.com/value-list/' class='paywalllink'>Ranking of the most valuable posts</a>";
				adjustlinkcolor();	  
  	 		}
  		if (object.tipme == 1) {
  			document.getElementById("mp_tipFrame2").innerHTML = "<h2>" + mp_thanks + "</h2><em>Share <a href='" + dataLink + "?ref=" + payment.userId + "' class='paywalllink'>this link</a> to get a share of later payments.</em><br />See the <a href='https://www.mediopay.com/value-list/' class='paywalllink'>Ranking of the most valuable posts</a>";
			adjustlinkcolor();
			if (object.hidebehind == 1) {
				mp_countdown = 0;
				mp_showcontent();
			}	
  		}
    })
    .catch((error) => {
      console.error('There has been a problem with your fetch operation:', error);
    })	 	
}




function mp_getInfo(mp_field) {
mp_verb = "read the rest of the article";
if (mp_field == "mp_tip") {
  mp_verb = "tip the author";
}
document.getElementById(mp_field).innerHTML = "<br /><br />You can " + mp_verb + " with Bitcoin SV (BSV) and MoneyButton. <a href='http://mediopay.com/bsv-how' class='paywalllink'>Learn how and get your starting BSV</a>. We promise you will pass the paywall very quickly.<br /></div>";
adjustlinkcolor();	
document.getElementById(mp_field).setAttribute( "onClick", "javascript: mp_noInfo('" + mp_field + "')");
}
function mp_noInfo(mp_field) {
document.getElementById(mp_field).innerHTML = "<b>(?)</b>";
document.getElementById(mp_field).setAttribute( "onClick", "javascript: mp_getInfo('" + mp_field + "')");
}


function mp_create_cookie(mp_hash) {
	let mp_current_url = window.location.href;
	if (mp_current_url.includes("?ref")) {
		let mp_ref_position = mp_current_url.indexOf("?ref");	
		mp_current_url = mp_current_url.substring(0, mp_ref_position);
	}
	let mp_date = new Date();
	let mp_exdays = 800;
	mp_date.setTime(mp_date.getTime() + (mp_exdays*24*60*60*1000));
  	var mp_expires = "expires="+ mp_date.toUTCString();
	document.cookie = mp_current_url + "=" + mp_hash + ";" + mp_expires;
}

function aftercooke(method) {
	if (method == "paywall") {
	  element1 = document.getElementById('mp_fade2');
	  element2 = document.getElementById('mp_frame2');
	  element1.classList.remove('mp_invisible');
	  element2.classList.remove('mp_invisible');
	}
	else if (method == "tipping") {
		element1 = document.getElementById('mp_tipFrame');		
		element1.classList.remove('mp_invisible');
	}
}
