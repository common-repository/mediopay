function mediopayHideNextElements() {
  setTimeout(function() {
    let element1 = document.getElementById("mp_fade");
    let element2 = document.getElementById("mp_frame1");
    let element3 = document.getElementById("mp_tipFrame");
    if (element1 !== null) {
        element1.classList.add("mp_invisible");
    }
    if (element2 !== null) {
        element2.classList.add("mp_invisible");
    }
    if (element3 !== null) {
        element3.classList.add("mp_invisible");
    }
  },200);
}


function mp_hidebehind() {
	setTimeout(function() { 
	mp_hideit = 0;
	mp_content = document.getElementsByClassName("entry-content");
	if (typeof mp_content[0] !== "undefined") {
	}
	else {
	    mp_content = document.getElementsByClassName("post_text_inner");
	}
	//mp_content[0].style.visibility = "hidden";
	mp_childnodes = mp_content[0].children;
	for (i=0; i<mp_childnodes.length; i++) {
		if (mp_hideit == 1) {
			mp_childnodes[i].style.visibility = "hidden";
		}		
		if (mp_childnodes[i].id == "mp_tipFrame2") {
			mp_hideit = 1;
		}	
	}
	}, 200);
}

function mp_showcontent() {
	if (typeof mp_countdown !== "undefined") {
		mp_content = document.getElementsByClassName("entry-content");
		if (typeof mp_content[0] !== "undefined") {
	}
	else {
	    mp_content = document.getElementsByClassName("post_text_inner");
	}
		mp_childnodes = mp_content[0].children;
		for (i=0; i<mp_childnodes.length; i++) {
			if (mp_childnodes[i].style.visibility == "hidden") {
				mp_childnodes[i].style.visibility = "visible";
			}
		}		
	}
	else {
	harr = 0;		
	document.getElementById("dontwant").innerHTML = "<br /><b>wait 4 Seconds</b>";
	setInterval(function() {
	harr++;
	if (harr < 4) {
		document.getElementById("dontwant").innerHTML = "<br /><b>wait " + (4 - harr) + " Seconds</b>";	
	}
	else {
	mp_content = document.getElementsByClassName("entry-content");
		if (typeof mp_content[0] !== "undefined") {
	}
	else {
	    mp_content = document.getElementsByClassName("post_text_inner");
	}
	mp_childnodes = mp_content[0].children;
	for (i=0; i<mp_childnodes.length; i++) {
		if (mp_childnodes[i].style.visibility == "hidden") {
			mp_childnodes[i].style.visibility = "visible";
		}
	}	
	}
	}, 1000);
	}
}

function MedioPay_textColor(elementID) {
  setTimeout(function() {
    // Variables for red, green, blue values
      var r, g, b, hsp;
      color = mp_barColor;
      // Check the format of the color, HEX or RGB?
      if (color.match(/^rgb/)) {
          // If HEX --> store the red, green, blue values in separate variables
          color = mp_barColor.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);
          r = color[1];
          g = color[2];
          b = color[3];
      }
      else {
          // If RGB --> Convert it to HEX: http://gist.github.com/983661
          color = +("0x" + color.slice(1).replace(
          color.length < 5 && /./g, '$&$&'));

          r = color >> 16;
          g = color >> 8 & 255;
          b = color & 255;
      }

      // HSP (Highly Sensitive Poo) equation from http://alienryderflex.com/hsp.html
      hsp = Math.sqrt(
      0.299 * (r * r) +
      0.587 * (g * g) +
      0.114 * (b * b)
      );

      // Using the HSP value, determine whether the color is light or dark
      if (hsp>127.5) {
          document.getElementById(elementID).style.color = "black";
          if (document.getElementById("mp_tipFrame") !== null) {
            document.getElementById("mp_tipFrame").style.color = "black";
          }
          document.getElementById("mp_threshold11").style.borderColor = "black";
          document.getElementById("mp_threshold12").style.backgroundColor = "black";
			 document.getElementById("mp_threshold21").style.borderColor = "black";
          document.getElementById("mp_threshold22").style.backgroundColor = "black";

      }
      else {
      	console.log("change color");
          document.getElementById(elementID).style.color = "white";
          document.getElementById("mp_threshold11").style.borderColor = "white";
          document.getElementById("mp_threshold12").style.backgroundColor = "white";
			 document.getElementById("mp_threshold21").style.borderColor = "white";
          document.getElementById("mp_threshold22").style.backgroundColor = "white";
          if (document.getElementById("mp_tipFrame") !== null) {
            document.getElementById("mp_tipFrame").style.color = "white";
          }
          else {
          console.log("no tipframe")
          }
          if (document.getElementById("mp_pay1") !== null) {
            document.getElementById("mp_pay1").innerHTML = "<img src='" + MedioPayPath + "questionmark-white.png' width='17' /></span><br />"
          }
          if (document.getElementById("mp_pay2") !== null) {
            document.getElementById("mp_pay2").innerHTML = "<img src='" + MedioPayPath + "questionmark-white.png' width='17' /></span><br />"
          }
          if (document.getElementById("mp_tip") !== null) {
            document.getElementById("mp_tip").innerHTML = "<img src='" + MedioPayPath + "questionmark-white.png' width='17' /></span><br />"
          }
      }
    }, 200);
  }



function MedioPay_changeColor() {
    var e = document.getElementById("select_color");
    var color = e.options[e.selectedIndex].value;
    document.getElementById("select_color").style.backgroundColor = "#" + color;
}


function make_paywall1_object(pwtype, pwform) {
	
	let mp_current_url = window.location.href;
	paywall1 = {};
	if (mp_current_url.includes("?ref")) {
		let mp_ref_position = mp_current_url.indexOf("?ref");	
		mp_current_url = mp_current_url.substring(0, mp_ref_position);
	}
	dataDomain = window.location.hostname;
	dataURL = window.location.pathname;
	if (pwtype == "tip") {
	    paywall1.tip = 1;
	    if (typeof tipAmount !== "undefined") {
			 paywall1.amount = tipAmount;
		 }
		if (mp_newCounter == "yes") {
			paywall1.newCounter = "yes"; 
			paywall1.mp_buys = mp_tips;
			if (typeof mp_first_tips !== "undefined") {
				paywall1.firstPartner = mp_first_tips;			
			}
			if (typeof mp_second_tips !== "undefined") {
				paywall1.secondPartner = mp_second_tips;			
			}
			if (typeof mp_third_tips !== "undefined") {
				paywall1.thirdPartner = mp_third_tips;			
			}
			if (typeof mp_fourth_tips !== "undefined") {
				paywall1.fourthPartner = mp_fourth_tips;			
			}    
    	}
    	else {
			 paywall1.newCounter = "no";   
    	}
		typenumber = "100201";
   }
   else if (pwtype == "tipme") {
		paywall1.tipme = 1;
		if (typeof tipAmount !== "undefined") {
			 paywall1.amount = tipAmount;
		 }
    	if (mp_newCounter == "yes") {
			paywall1.newCounter = "yes"; 
			paywall1.mp_buys = mp_tips;
			if (typeof mp_first_tips !== "undefined") {
				paywall1.firstPartner = mp_first_tips;			
			}
			if (typeof mp_second_tips !== "undefined") {
				paywall1.secondPartner = mp_second_tips;			
			}
			if (typeof mp_third_tips !== "undefined") {
				paywall1.thirdPartner = mp_third_tips;			
			}
			if (typeof mp_fourth_tips !== "undefined") {
				paywall1.fourthPartner = mp_fourth_tips;			
			}    
    	}
    	else {
			 paywall1.newCounter = "no";   
    	}
    	if (mp_tipmehidebehind == 1) {
			paywall1.hidebehind = 1;    	
    	}
    	else {
			paywall1.hidebehind = 0;    	
    	}
    	typenumber = "100201";
	}
	else if (pwtype == "paywall1") {
		paywall1.paywall1 = 1;
		paywall1.amount = paymentAmount1;		
		if (mp_newCounter == "yes") {
			paywall1.newCounter = "yes"; 
			paywall1.mp_buys = mp_buys1;
			if (typeof mp_first_buys !== "undefined") {
				paywall1.firstPartner = mp_first_buys;			
			}
			if (typeof mp_second_buys !== "undefined") {
				paywall1.secondPartner = mp_second_buys;			
			}
			if (typeof mp_third_buys !== "undefined") {
				paywall1.thirdPartner = mp_third_buys;			
			}
			if (typeof mp_fourth_buys !== "undefined") {
				paywall1.fourthPartner = mp_fourth_buys;			
			}    
    	}
    	else {
			 paywall1.newCounter = "no";   
    	}
		typenumber = "100101";
	}
	else if (pwtype == "paywall2") {
		paywall1.paywall2 = 1;
		paywall1.amount = paymentAmount2;
		if (mp_newCounter == "yes") {
			paywall1.newCounter = "yes"; 
			paywall1.mp_buys = mp_buys2;
			if (typeof mp_first_buys2 !== "undefined") {
				paywall1.firstPartner = mp_first_buys2;			
			}
			if (typeof mp_second_buys2 !== "undefined") {
				paywall1.secondPartner = mp_second_buys2;			
			}
			if (typeof mp_third_buys2 !== "undefined") {
				paywall1.thirdPartner = mp_third_buys2;			
			}
			if (typeof mp_fourth_buys2 !== "undefined") {
				paywall1.fourthPartner = mp_fourth_buys2;			
			}    
    	}
    	else {
			 paywall1.newCounter = "no";   
    	}
		typenumber = "100102";
	}
	if (nometanet == "yes" || mp_preview == "yes") {
		var returndata = bsv.Script.buildSafeDataOut(['1NYJFDJbcSS2xGhGcxYnQWoh4DAjydjfYU', "" + typenumber]).toASM();
	}
	else {
		var returndata = bsv.Script.buildSafeDataOut(['1NYJFDJbcSS2xGhGcxYnQWoh4DAjydjfYU', "" + typenumber, "" + dataTitle, "" + dataContent, "" + dataDomain, "" + dataURL, "" + sharingQuota, "" + refQuota]).toASM();	 
	}
	
	paywall1.typenumber = typenumber;
   paywall1.title = dataTitle;
   paywall1.baseurl = dataDomain;
   paywall1.path = dataURL;
   paywall1.sharing = sharingQuota;
   paywall1.ref = refQuota;
   paywall1.nometanet = nometanet;
   paywall1.returndata = returndata;
   paywall1.to = mp_theAddress;
   paywall1.currency = mp_theCurrency;
   paywall1.mypostid = mp_mypostid;
   paywall1.paywalltype = pwtype;
   paywall1.paywallform = pwform;
   
    if (typeof mp_refID !== "undefined") {
          paywall1.refID = mp_refID;
     }
    if (mp_preview == "yes") {
		paywall1.preview = 1;    
    }
   else {
		 paywall1.preview = 0;    
   }
	if (typeof mp_address2 !== "undefined" && mp_address2.length > 0) {
			if (mp_address2 !== "none") {
				paywall1.address2 = mp_address2;
				paywall1.address2share = mp_secondAddressShare;
			}		
	}
	else {
	}	
	if (typeof mp_threshold !== "undefined") {
		paywall1.threshold = mp_threshold;
	}	
	else {
		paywall1.threshold = 0;
	}
	if ((typeof mp_editable_tips !== "undefined" && pwtype == "tipme") || (typeof mp_editable_tips !== "undefined" && pwtype == "tip")  ) {
		paywall1.editable = "yes";
		paywall1.amount = document.getElementById("mp_editable").value;
	}
	if (( pwtype == "paywall1" && mp_pwyw == 1) || (pwtype == "paywall2" && mp_pwyw == 1)) {
		paywall1.editable = "yes";
		paywall1.amount = document.getElementById("mp_editable_pw").value;
	}
	else {
	}
	if (mp_threshold_active == 1) {
		paywall1.threshold_active = 1;	
	}
	else {
		paywall1.threshold_active = 0;	
	}
	mp_prepareObject(paywall1);
}




