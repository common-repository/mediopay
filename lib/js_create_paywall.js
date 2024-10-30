// if the user added a paymail address, query polynym to get the BSV address

/*function mp_getAddress(object) {
	mp_payload = object;
	if (mp_payload[0]["to"].includes("@")) {
				fetch("https://api.polynym.io/getAddress/" + mp_theAddress).then(function(r) {
  					return r.json()
  				}).then(function(r) {
					for (let i=0; i<mp_payload.length; i++) {
  						mp_payload[i]["to"] = r.address;
  					}
  					if (k == 1 && p == 0) {
  						for (let i=0;i<mp_payload.length;i++) {
							mp_makeButton(mp_payload[i]);
							p = 1;
    					}
    				}
	  				else {
						k = 1;
	  				}
  				})
  			}
  			else {
				if (k == 1 && p == 0) {
	  				for (let i=0;i<mp_payload.length;i++) {
						mp_makeButton(mp_payload[i]);
						p = 1;
    				}
	  			}
	  			else {
						k = 1;
	  			}
  			}
} */

// ask Planaria to get information about the first transactions to get the address for income sharing and the number of transactions

function mp_prepareObject(mp_payload) {
	setTimeout(function() {	
	if (mp_payload.nometanet == "yes" && mp_payload.newCounter == "no") {
		mp_payload.sharing = 0.0;
		mp_payload.number = 0;
		mp_payload.buys = 0;
		mp_payload.tips = 0;
		mp_makeButton(mp_payload);
	}	
	else if (mp_payload.newCounter == "yes")  {
		mp_makeButton(mp_payload);
	}
	else {
		AskPlanaria(mp_payload);			
	}
	}, 400);
}

/*
function mp_prepareObject0(object) {
	mp_payload = object;
	setTimeout(function() {	
		let tipbuttr = document.getElementById('tbutton');
		for (let i=0; i<mp_payload.length; i++) {
			console.log(i);
			if (mp_payload[i].nometanet == "yes" && mp_payload[i].newCounter == "no") {
				console.log("no metanet + no newCounter " + i);
				mp_payload[i].sharing = 0.0;
				mp_payload[i].number = 0;
				mp_payload[i].buys = 0;
				mp_payload[i].tips = 0;
				mp_makeButton(mp_payload[i]);
				p = 1;
			}	
			else if (mp_payload[i].newCounter == "yes" || mp_payload[i].newCounter2 == "yes")  {
				if (typeof mp_payload[i].mp_buys1 !== "undefined") {
					mp_payload[i].number = mp_payload[i].mp_buys1;
					mp_makeButton(mp_payload[i]);
				}
				else if (typeof mp_payload[i].mp_buys2 !== "undefined") {
					mp_payload[i].number = mp_payload[i].mp_buys2;
					mp_makeButton(mp_payload[i]);
				}
				else if (typeof mp_payload[i].mp_tips !== "undefined") {
					mp_payload[i].number = mp_payload[i].mp_tips;
					mp_makeButton(mp_payload[i]);
				}		
				else {
					mp_makeButton(mp_payload[i]);				
				}		
			}
			else {
				console.log("metanet, but no new counter");
				mp_payload_1 = []
				mp_payload_1.push(mp_payload[i]);
				AskPlanaria(mp_payload_1);			
			}
		}
	}, 1400);
}
	*/
	
function AskPlanaria(mp_payload) {
	 var query = {
     		 "v": 3,
      	 "q": {
        		"find": {
         	$or: [{"out.s1": "1NYJFDJbcSS2xGhGcxYnQWoh4DAjydjfYU"},{"out.s2": "1NYJFDJbcSS2xGhGcxYnQWoh4DAjydjfYU"}],
         	$or: [{"out.s3": mp_payload["title"]},{"out.s4": mp_payload["title"]}]
         },
        "limit": 120,
        "sort": { "blk.i": 1 }
       }
   }
	var b64 = btoa(JSON.stringify(query));
	var url = "https://genesis.bitdb.network/q/1FnauZ9aUH2Bex6JzdcV4eNX7oLSSEbxtN/" + b64;
	var header = {
  		headers: { key: "1CN88CMwB8wAVeoX2zm9CCZE4ZrrHDjZL5" }
	};
	fetch(url, header).then(function(r) {
  		return r.json()
	}).then(function(r) {
    	mp_results = r.c.concat(r.u);
    	mp_payload.buys = mp_results.length;
    	if (typeof mp_results[0] !== "undefined") {
    		if (typeof mp_results[0].out[0].s7 !== "undefined") {
					mp_payload["sharing"] = mp_results[0].out[0].s7;					
    		}
    	}
		if (mp_payload.sharing > 0) {
			for (n=0; n<mp_payload.length; n++) {
				if (typeof mp_results[0].out[2] !== "undefined") {
					mp_payload["firstPartner"] = mp_results[0].out[2].e.a;
				}
				else {
					mp_payload[n]["firstPartner"] = mp_results[0].in[0].e.a;
				}
			}
		}
 		if (mp_payload.sharing > 0.1 && mp_results.length > 1 ) {
		 	for (n=0; n<mp_payload.length; n++) {
		 		if (typeof mp_results[1].out[3] !== "undefined") {
					mp_payload["secondPartner"] = mp_results[1].out[3].e.a;
				}
				else {
					mp_payload["secondPartner"] = mp_results[1].in[0].e.a;
				}
			}
 		}
 		if (mp_payload.sharing > 0.2 && mp_results.length > 2 ) {
			 for (n=0; n<mp_payload.length; n++) {
			 	if (typeof mp_results[2].out[4] !== "undefined") {
					mp_payload["thirdPartner"] = mp_results[2].out[4].e.a;
				}
				else {
					mp_payload["thirdPartner"] = mp_results[2].in[0].e.a;
				}
 			}
 		}
 		if (mp_payload.sharing > 0.3 && mp_results.length > 3 ) {
			 for (n=0; n<mp_payload.length; n++) {
			 	if (typeof mp_results[3].out[5] !== "undefined") {
					mp_payload["fourthPartner"] = mp_results[3].out[5].e.a;
				}
				else {
					mp_payload["firstPartner"] = mp_results[3].in[0].e.a;
				}
			}
    	}
    	mp_makeButton(mp_payload);
	});
}


function mp_makeButton(mp_payload) {
	console.log(mp_payload);
	if (mp_payload.paywalltype == "paywall1") {
		mp_skript = "mp_handlePayment(payment, mp_payload)";
		paymentLabel = "Buy";
		mp_element = "mbutton1";
		paywallreturn1 = mp_payload.returndata;
	}
	else if (mp_payload.paywalltype == "paywall2") {
		mp_skript = "mp_shandleSuccessfulPayment(payment, mp_payload)";
		paymentLabel = "Buy";
		mp_element = "mbutton2";
		paywallreturn2 =  mp_payload.returndata;
		
	}	
	else if (mp_payload.paywalltype == "tip") {
		mp_skript = "mp_handleSuccessfulTip(payment, mp_payload)";
		paymentLabel = "Tip";
		if (typeof mp_payload.editable !== "undefined") {
			mp_element = "editable_mbutton";	
		}	
		else {
			mp_element = "tbutton";
		}
		tipreturn = mp_payload.returndata;
	}	
	else if (mp_payload.paywalltype == "tipme") {	
		mp_skript = "mp_handleSuccessfulTip(payment, mp_payload)";
		paymentLabel = "Tip";
		if (typeof mp_payload.editable !== "undefined") {
			mp_element = "editable_mbutton2";	
		}	
		else {
			mp_element = "tbutton2";
		}
		tipreturn2 = mp_payload.returndata;
	}	
	
	// This is just for backward compatibility with old posts which use Planaria instead of their own database
	/*
	if (mp_payload.newCounter !== "yes") {	
		let sharingWord = "";
		numbercountTips = "<span class='icon'>&#10084;</span> " + mp_payload.buys;
		if ((mp_payload.sharing * 10) > mp_payload.buys) {
			switch (mp_payload.number) {
				case(0):
					shareQuote = mp_payload.sharing*100;
					break;
				case(1):
					shareQuote = mp_payload.sharing * 50;
					break;
				case(2):
					shareQuote = mp_payload.sharing * 30;
					break;
				case(3):
					shareQuote = mp_payload.sharing * 20;
					break;
			}
			sharingWord1 = "&nbsp;&nbsp;&nbsp;&nbsp;<span id='mp_box2' onclick='mp_expandInfo2(\"mp_box2\")'><span class='icon'>&cent;</span> " + shareQuote + "% revenue share";
			sharingWord2 = "&nbsp;&nbsp;&nbsp;&nbsp;<span id='mp_box5' onclick='mp_expandInfo2(\"mp_box5\")'><span class='icon'>&cent;</span> " + shareQuote + "% revenue share";
			sharingWord3 = "&nbsp;&nbsp;&nbsp;&nbsp;<span id='mp_box6' onclick='mp_expandInfo2(\"mp_box6\")'><span class='icon'>&cent;</span> " + shareQuote + "% revenue share";
		}
		else {
			sharingWord1 = "";
			sharingWord2 = "";
			sharingWord3 = "";
		}
		if (typeof mp_payload.paywall1 !== "undefined") {
			if (document.getElementById("mp_counter1") !== null) {
				if (typeof mp_payload.mp_buys !== "undefined") {
					if (mp_payload.nometanet !== "yes" && mp_payload.mp_buys !== 0) {
						document.getElementById("mp_counter1").innerHTML = "<span class='icon'>&#x1F48E;</span>" + mp_payload.mp_buys + " buyers</span>" + sharingWord1 + "<br /><a href='https://mediopay.com/bsv-how" + mp_linksig + "' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></span>";
						adjustlinkcolor();
		  			}	   
		   		else {
		   			document.getElementById("mp_counter1").innerHTML = "<span class='icon'>&#x1F48E;</span>" + mp_payload.mp_buys + " buyers</span>" + sharingWord1 + "<br /><a href='https://mediopay.com/bsv-how" + mp_linksig + "' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></span>";
						adjustlinkcolor();
		  			 }
				}
				else {
					document.getElementById("mp_counter1").innerHTML = "  <span class='icon'>&#x1F48E;</span> 0 buyers</span> " + sharingWord1 + "<br /><a href='https://mediopay.com/bsv-how" + mp_linksig + "' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></span>";
					adjustlinkcolor();		
				}
			}
		
		}
		if (typeof mp_payload.tip !== "undefined") {
			if (typeof mp_payload.mp_buys !== "undefined") {
				if (object.nometanet !== "yes" && mp_payload.buys !== 0) {
					document.getElementById("counterTips").innerHTML = " <span class='icon'>&#10084;</span>" + mp_payload.mp_buys + " tips</span>" + sharingWord3 + "<br /><a href='https://mediopay.com/bsv-how" + mp_linksig + "' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></div>";
					adjustlinkcolor();			
				}
				else {
					document.getElementById("counterTips").innerHTML = " <span class='icon'>&#10084;</span>" + mp_payload.mp_buys + " tips</span>" + sharingWord3 + "<br /><a href='https://mediopay.com/bsv-how" + mp_linksig + "' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></div>";
					adjustlinkcolor();			
				}
			}
			else {
				document.getElementById("counterTips").innerHTML = " <span class='icon'>&#10084;</span> 0 tips</span>" + sharingWord3 + "<br /><a href='https://mediopay.com/bsv-how' target='_blank" + mp_linksig + "' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></div>";
				adjustlinkcolor();		
			}	
		}
		if (typeof mp_payload.tipme !== "undefined") {
			if (typeof mp_payload.mp_buys !== "undefined") {
				if (object.nometanet !== "yes" && mp_payload.mp_buys !== 0) {
					document.getElementById("counterTips2").innerHTML = " <span class='icon'>&#10084;</span>" + mp_payload.mp_buys + " tips</span>" + sharingWord3 + "<br /><a href='https://mediopay.com/bsv-how" + mp_linksig + "' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></div>";
					adjustlinkcolor();			
				}
				else {
					document.getElementById("counterTips2").innerHTML = " <span class='icon'>&#10084;</span>" + mp_payload.mp_buys + " tips</span>" + sharingWord3 + "<br /><a href='https://mediopay.com/bsv-how" + mp_linksig + "' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></div>";
					adjustlinkcolor();			
				}
			}
			else {
				document.getElementById("counterTips2").innerHTML = " <span class='icon'>&#10084;</span> 0 tips</span>" + sharingWord3 + "<br /><a href='https://mediopay.com/bsv-how" + mp_linksig + "' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></div>";
				adjustlinkcolor();		
			}	
		}
		if (typeof mp_payload.paywall2 !== "undefined") {
			if (document.getElementById("mp_counter2") !== null) {		
				if (typeof mp_payload.buys !== "undefined") {
					if (mp_payload.buys == 0) {
						document.getElementById("mp_counter2").innerHTML = "<span class='icon'>&#x1F48E;</span>" + mp_payload.buys + " buyers</span>" + sharingWord2 + "<br /><a href='https://mediopay.com/bsv-how" + mp_linksig + "' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></span>";
						adjustlinkcolor();			
					}
					else {
						document.getElementById("mp_counter2").innerHTML = "<span class='icon'>&#x1F48E;</span>" + mp_payload.buys + " buyers</span>" + sharingWord2 + "<br /><a href='https://mediopay.com/bsv-how" + mp_linksig + "' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></span>";
						adjustlinkcolor();			
					}
				}
				else {
					document.getElementById("mp_counter2").innerHTML = " <span class='icon'>&#x1F48E;</span> 0 buyers</span>" + sharingWord2 + "<br /><a href='https://mediopay.com/bsv-how" + mp_linksig + "' target='_blank' class='paywalllink'>Don't know how to pay? Learn and get free BSV!</a></span>";
					adjustlinkcolor();		
				}
			}		
		}
	}
	*/
	// now we define the amounts for the payment	
	
	if (typeof mp_payload["refID"] !== "undefined") {
		mp_payload.refAmount = mp_payload.amount * mp_payload.ref;
	}
	else {
		mp_payload.refAmount = 0;
	}
	if (typeof mp_payload.fourthPartner !== "undefined" && mp_payload.fourthPartner !== 0 && mp_payload.sharing > 0.3) {
		mp_payload.sharingamount1 = mp_payload.amount * (mp_payload.sharing * 0.35);
		mp_payload.sharingamount2 = mp_payload.amount * (mp_payload.sharing * 0.30);
		mp_payload.sharingamount3 = mp_payload.amount * (mp_payload.sharing * 0.20);
		mp_payload.sharingamount4 = mp_payload.amount * (mp_payload.sharing * 0.15);
		mp_payload.amount = mp_payload.amount * (1 - mp_payload.sharing) - mp_payload.refAmount;
	}
	else if (typeof mp_payload.thirdPartner !== "undefined" && mp_payload.thirdPartner !== 0 && mp_payload.sharing > 0.2) {
		mp_payload.sharingamount1 = mp_payload.amount * (mp_payload.sharing * 0.50);
		mp_payload.sharingamount2 = mp_payload.amount * (mp_payload.sharing * 0.30);
		mp_payload.sharingamount3 = mp_payload.amount * (mp_payload.sharing * 0.20);
		mp_payload.amount = mp_payload.amount * (1 - mp_payload.sharing) - mp_payload.refAmount;
	}
	else if (typeof mp_payload.secondPartner !== "undefined" && mp_payload.secondPartner !== 0 && mp_payload.sharing > 0.1) {
		mp_payload.sharingamount1 = mp_payload.amount * (mp_payload.sharing * 0.60);
		mp_payload.sharingamount2 = mp_payload.amount * (mp_payload.sharing * 0.40);
		mp_payload.amount = mp_payload.amount * (1 - mp_payload.sharing) - mp_payload.refAmount;
	}
	else if (typeof mp_payload.firstPartner !== "undefined" && mp_payload.firstPartner !== 0 && mp_payload.sharing > 0)  {
		mp_payload.sharingamount1 = mp_payload.amount * mp_payload.sharing;
		mp_payload.amount = mp_payload.amount * (1 - mp_payload.sharing) - mp_payload.refAmount;
	}
	if (typeof mp_payload.address2 !== "undefined") {
		if (mp_payload.address2.length > 0 && mp_payload.address2 !== "none" && typeof mp_payload.address2share !== "undefined" && mp_payload.address2share !== "0.0") {			
			mb_amount2 = mp_payload.amount * mp_payload.address2share;
			mb_amount1 = mp_payload.amount - mb_amount2;
		}
		else {
			mb_amount1 = mp_payload.amount 		
		}	
	}	
	else {
			mb_amount1 = mp_payload.amount 		
	}	

	/*
	if (mp_payload.currency !== "BSV") {
		mp_payload.amount = mp_payload["amount"].toFixed(2);
		if (typeof mp_payload.amount2 !== "undefined") {
			mp_payload.amount2 = mp_payload.amount2.toFixed(2);		
		}
		if (typeof mp_payload.sharingamount1 !== "undefined") {
			mp_payload.sharingamount1 = mp_payload.sharingamount1.toFixed(2);		
		}
		if (typeof mp_payload.sharingamount2 !== "undefined") {
			mp_payload.sharingamount2 = mp_payload.sharingamount2.toFixed(2);	
		}
		if (typeof mp_payload.sharingamount3 !== "undefined") {
			mp_payload.sharingamount3 = mp_payload.sharingamount3.toFixed(2);	
		}
		if (typeof mp_payload.sharingamount4 !== "undefined") {
			mp_payload.sharingamount4 = mp_payload.sharingamount4.toFixed(2);		
		}
		if (typeof mp_payload.refAmount !== "undefined") {
			mp_payload.refAmount = mp_payload.refAmount.toFixed(2);		
		}
	}	
	*/
	outPuts = [
		{
			to: mp_payload.to,
         currency: mp_payload.currency,
         amount: mb_amount1
		}
	]
	if (typeof mb_amount2 !== "undefined")  {
		outPutsA2 = {
			to: mp_payload.address2,
         currency: mp_payload.currency,
         amount: mb_amount2
		}
		outPuts.push(outPutsA2);
	}
	if (typeof mp_payload.firstPartner !== "undefined" && mp_payload.sharing >= 0.1  && mp_payload.firstPartner !== 0)  {
		outPuts1st = {
			to: mp_payload.firstPartner,
         currency: mp_payload.currency,
         amount: mp_payload.sharingamount1
		}
		outPuts.push(outPuts1st);
	}
	if (typeof mp_payload.secondPartner !== "undefined" && mp_payload.sharing >= 0.2  && mp_payload.secondPartner !== 0)  {
		outPuts2nd = {
			to: mp_payload.secondPartner,
         currency: mp_payload.currency,
         amount: mp_payload.sharingamount2
		}
		outPuts.push(outPuts2nd);
	}
	if (typeof mp_payload.thirdPartner !== "undefined" && mp_payload.sharing >= 0.3 && mp_payload.thirdPartner !== 0)  {
		outPuts3rd = {
			to: mp_payload.thirdPartner,
         currency: mp_payload.currency,
         amount: mp_payload.sharingamount3
		}
		outPuts.push(outPuts3rd);
	}
	if (typeof mp_payload.fourthPartner !== "undefined" && mp_payload.sharing >= 0.4 && mp_payload.fourthPartner !== 0)  {
		outPuts4th = {
			to: mp_payload.fourthPartner,
         currency: mp_payload.currency,
         amount: mp_payload.sharingamount4
		}
		outPuts.push(outPuts4th);
	}
	if (typeof mp_payload["refID"] !== "undefined") {
		outPutsRef = {
			to: mp_payload.refID,
         currency: mp_payload.currency,
         amount: mp_payload.refAmount
		}
		outPuts.push(outPutsRef);
	}
	if (typeof mp_payload.nometanet !== "undefined") {
		outPutsMeta = {
         script: mp_payload.returndata,
         amount: '0',
         currency: 'BSV'
		}
		outPuts.push(outPutsMeta);
	}
 	mbobject = {
		outputs: outPuts,
		label: paymentLabel,
   	onPayment: function (payment) {
			if (typeof paywallreturn1 !== "undefined") {
				if (payment.paymentOutputs[0].script == paywallreturn1) {
					mp_handlePayment(payment, mp_payload);
				}
			}
         if (typeof tipreturn !== "undefined") {
          	if (payment.paymentOutputs[0].script == tipreturn) {
              mp_handleSuccessfulTip(payment, mp_payload);
     			}
     		}
     		if (typeof tipreturn2 !== "undefined") {
          	if (payment.paymentOutputs[0].script == tipreturn2) {
              mp_handleSuccessfulTip(payment, mp_payload);
     			}
     		}
     		if (typeof paywallreturn2 !== "undefined") {
				if (payment.paymentOutputs[0].script == paywallreturn2) {
					mp_handlePayment(payment, mp_payload);
				}
			}
     	},
      onError: function (arg) { console.log('onError', arg) }
   }
   console.log(mbobject);
   if (typeof mp_payload.editable !== "undefined") {
   	if (typeof mp_payload.tip !== "undefined" ) {
   		if (mp_payload.tip == "yes" ) {
   			document.getElementById("editable_mbutton_wrap_1").innerHTML = "";
				mp_element = "editable_mbutton_1"; 
			}  
		}   
   	if (typeof mp_payload.tip2 !== "undefined" ) {
   		if (mp_payload.tip2 == "yes" ) {
   			document.getElementById("editable_mbutton_wrap").innerHTML = "";
				mp_element = "editable_mbutton"; 
			}  
		}   
   }
	const div = document.getElementById(mp_element);
	if (typeof mp_payload.cookie !== "undefined") {
	}
	else if (typeof mp_payload.cookie2 !== "undefined") {
	}
	else {
		console.log(mp_element + " div " + div);
		if (div !== null) {
  			moneyButton.render(div,	mbobject);
  		}
  		else {	
  		}
  	}
 }


function mp_expandInfo(mp_elem, mp_amount, mp_currency) {
	if (mp_elem == "box") {
			mp_verb = "bought";
			if (typeof mp_buys == "undefined") {
				mp_buys = 0
			}
			document.getElementById("mp_box").innerHTML = "<span class='icon'>&#x1F48E;</span>" + mp_buys + " people " + mp_verb + " this article and spend " + (mp_amount * mp_buys).toFixed(2) + " " + mp_currency;
			document.getElementById("mp_box").style.width = "400px";
			document.getElementById("mp_box").style.cursor = "default";
			document.getElementById("mp_box").setAttribute( "onClick", "javascript: mp_deflateInfo('mp_box', " + mp_amount + ")");
			document.getElementById("mp_box").onmouseleave = function() { mp_deflateInfo('box', mp_amount);	};
	}
	if (mp_elem == "mp_box3") {
		console.log("box3");
			if (typeof mp_tips == "undefined") {
				mp_tips = 0
			}
			mp_verb = "tipped";
			mp_theIcon = "&#10084;";
			if (mp_tips == 0) {
				mp_theIcon = "&#x1F641;";
			}
			mp_tipped_amount = mp_amount * mp_tips;
			if (typeof mp_tippedAmount !== "undefined") {
				mp_tipped_amount = mp_tippedAmount;			
			}
		  	document.getElementById("mp_box3").innerHTML = "<span class='icon'>" + mp_theIcon + "</span>" + mp_tips + " people " + mp_verb + " this article and spend " + (mp_tiped_amount).toFixed(2) + " " + mp_currency;
				document.getElementById("mp_box3").style.width = "400px";
			document.getElementById("mp_box3").style.cursor = "default";
			document.getElementById("mp_box3").setAttribute( "onClick", "javascript: mp_deflateInfo('box3', " + mp_amount + ")");
		 document.getElementById("mp_box3").onmouseleave = function() { deflateInfo('mp_box3', mp_amount);	};
	}
	if (mp_elem == "box4") {
		if (typeof mp_buys2 == "undefined") {
				mp_buys2 = 0
			}
			mp_verb = "bought";
			document.getElementById("mp_box4").innerHTML = "<span class='icon'>&#x1F48E;</span>" + mp_buys2 + " people " + mp_verb + " this article and spend " + (mp_amount * mp_buys2).toFixed(2) + " " + mp_currency;
				document.getElementById("mp_box4").style.width = "400px";
			document.getElementById("mp_box4").style.cursor = "default";
			document.getElementById("mp_box4").setAttribute( "onClick", "javascript: mp_deflateInfo('mp_box4', " + mp_amount + ")");
			document.getElementById("mp_box4").onmouseleave = function() { console.log("now");mp_deflateInfo('mp_box4', mp_amount);	};
	}
}

function mp_deflateInfo(mp_elem, mp_amount) {
	if (mp_elem == "box") {
		mp_noum = " buyers";
		mp_theIcon = "&#x1F48E;";
		if (mp_buys == 0) {
			mp_theIcon = "&#x1F48E;";
		}
		document.getElementById("mp_box").innerHTML = "<span class='icon'>" + mp_theIcon + "</span>" + mp_buys ;
		document.getElementById("mp_box").style.width = "120px";
		document.getElementById("mp_box").style.cursor = "help";
		document.getElementById("mp_box").setAttribute( "onClick", "javascript: mp_expandInfo('mp_box', " + mp_amount + ")");
		document.getElementById("mp_box").onmouseon = function() { mp_expandInfo('mp_box', mp_amount);	};

	}
	if (mp_elem == "mp_box3") {
		mp_noum = " tips";
		mp_theIcon = "&#10084;";
			mp_theIcon = "&#x1F641;";
			if (mp_tips == 0) {
		}
		document.getElementById("mp_box3").innerHTML = "<span class='icon'>" + mp_theIcon + "</span>" + mp_tips;
		document.getElementById("mp_box3").style.width = "120px";
		document.getElementById("mp_box3").style.cursor = "help";
		document.getElementById("mp_box3").setAttribute( "onClick", "javascript: mp_expandInfo('mp_box3', " + mp_amount + ")");
		document.getElementById("mp_box3").onmouseon = function() { mp_expandInfo('mp_box3', mp_amount);	};
	}
	if (mp_elem == "mp_box4") {
		mp_noum = " buyers";
		mp_theIcon = "&#x1F48E;";
		if (mp_buys2 == 0) {
			mp_theIcon = "&#x1F48E;";
		}
		document.getElementById("mp_box4").innerHTML =  "<span class='icon'>" + mp_theIcon + "</span>" + mp_buys2;
		document.getElementById("mp_box4").style.width = "120px";
		document.getElementById("mp_box4").style.cursor = "help";
		document.getElementById("mp_box4").setAttribute( "onClick", "javascript: mp_expandInfo('mp_box4', " + mp_amount + ")");
		document.getElementById("mp_box4").onmouseon = function() { mp_expandInfo('mp_box4', mp_amount);	};
	}
}

function mp_expandInfo2(mp_elem) {
	if (mp_elem == "mp_box2") {
		if (typeof mp_buys == "undefined") {
			mp_buys = 0;
		}
		mp_noum = mp_buys + 1 + ". ";
		mp_verb = "buyer of ";
		mp_verb2 = "purchases";
	}
	if (mp_elem == "mp_box5") {
		if (typeof mp_buys2 == "undefined") {
			mp_buys2 = 0;
		}
		mp_noum = mp_buys2 + 1 + ". ";
		mp_verb = "buyer of ";
		mp_verb2 = "purchases";
	}
	else {
		if (typeof mp_tips == "undefined") {
			mp_tips = 0;
		}
		mp_noum = mp_tips + 1 + ". ";
		mp_verb = "to tip ";
		mp_verb2 = "tips";
	}
	document.getElementById(mp_elem).innerHTML = "<span class='icon'>&cent;</span> You are the " + mp_noum + mp_verb + "this article and will receive an income share for all " + mp_verb2 + " after you for up to " + shareQuote + "%";
	document.getElementById(mp_elem).style.width = "400px";
	document.getElementById(mp_elem).style.cursor = "default";
	document.getElementById(mp_elem).setAttribute( "onClick", "javascript: mp_deflateInfo2('" + mp_elem + "')");
	document.getElementById(mp_elem).onmouseleave = function() { mp_deflateInfo2(mp_elem);	};


}

function mp_deflateInfo2(mp_elem) {
	document.getElementById(mp_elem).innerHTML = "<span class='icon'>&cent;</span> " + shareQuote + "%";;
	document.getElementById(mp_elem).style.width = "250px";
	document.getElementById(mp_elem).style.cursor = "help";
	document.getElementById(mp_elem).setAttribute( "onClick", "javascript: mp_expandInfo2('" + mp_elem + "')");
	document.getElementById(mp_elem).onmouseon = function() { expandInfo2(mp_elem);	};
}

function adjustlinkcolor() {
	console.log("adjust link color");
	if (typeof mp_linkColor !== "undefined") {
		console.log(mp_linkColor);
		if (mp_linkColor.length > 3) {
				paywalllinks = document.getElementsByClassName("paywalllink");
				console.log(paywalllinks.length);
				var i;
				for (i = 0; i < paywalllinks.length; i++) {
 	 				paywalllinks[i].style.color = mp_linkColor;
					paywalllinks[i].classList.add('paywalllinks2');
					//paywalllinks2 = document.getElementsByClassName("paywalllink2");
					//paywalllinks2[i].style.color = mp_linkColor;
				} 			
			
				//document.getElementsByClassName("paywalllink").style.color = mp_linkColor;	
				//document.getElementByClassName("paywalllink2").style.color = mp_linkColor;	
		}	
	}

}
