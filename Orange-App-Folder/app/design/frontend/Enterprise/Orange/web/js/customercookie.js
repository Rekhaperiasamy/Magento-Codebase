require(['jquery'], function($) {
	"use strict";  
	var grp = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);	
	if(window.location.href.indexOf('zelfstandigen') > -1 || window.location.href.indexOf('independants') > -1)
	{	
		eraseCookie('SEGMENT');
		createCookie('SEGMENT','SOHO','1');		
	}
	else {
		if(readCookie('SEGMENT') == 'SOHO') {
			if (window.location.href.toLowerCase().indexOf("/cart/add/") >= 0) {
				eraseCookie('SEGMENT');
				createCookie('SEGMENT','SOHO','1');	
			}
			else if (window.location.href.toLowerCase().indexOf("/cart/updatepost") >= 0) {
				eraseCookie('SEGMENT');
				createCookie('SEGMENT','SOHO','1');
			}
			else {
				eraseCookie('SEGMENT');
				createCookie('SEGMENT','res','1');
			}
		}
		else {
			eraseCookie('SEGMENT');
			createCookie('SEGMENT','res','1');	
		}
	}
	// if(grp == 'nl' || grp == 'fr')
	// {	
		// eraseCookie('SEGMENT');
		// createCookie('SEGMENT','res','1');		
	// }
	function createCookie(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/;domain=.orange.be";		
	}

	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}

	function eraseCookie(name) {
		createCookie(name,"",-1);
	}
	return;
});
