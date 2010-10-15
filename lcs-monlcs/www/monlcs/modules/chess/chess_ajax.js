/*****************************************************************************\
|	Part of Ajax Chess										  										|
|																										|
|  written 2006 by tornamodo at linuxuser.at												|
|																										|
\*****************************************************************************/

    var http_request = false;
    var http_request2 = false;
    var http_request3 = false;

	 var intCheck;

function timestamp_ms() {
	zeit = new Date();
   ms = (zeit.getHours() * 24 * 60 * 1000) + (zeit.getMinutes() * 60 * 1000) + (zeit.getSeconds() * 1000) + zeit.getMilliseconds();
	return ms;
}

function lost_connection() {
	/* Will Open The Connection Lost Window */
		location.href="index.php";
}


function ajax_check_move() {
		ms = timestamp_ms();
		Game_LastCheck = ms;

		url = "sessions/" + Game_SessID + ".txt?now=" + ms;
//		log(url);
    	str1 = "";

	http_request = false;

        if (window.XMLHttpRequest) { // Mozilla, Safari,...
            http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType) {
                http_request.overrideMimeType('text/xml');
                // See note below about this line
            }
        } else if (window.ActiveXObject) { // IE
            try {
                http_request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!http_request) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }

	
        http_request.onreadystatechange = alertContents;
        http_request.open('GET', url, true);
        http_request.send(null);

}




    
    
function ajax_set_info( info_str, f ) {
	url = "wfile.php";
	
//	log("set: " + info_str);
	http_request2 = false;

        if (window.XMLHttpRequest) { // Mozilla, Safari,...
            http_request2 = new XMLHttpRequest();
            if (http_request2.overrideMimeType) {
                http_request2.overrideMimeType('text/xml');
                // See note below about this line
            }
        } else if (window.ActiveXObject) { // IE
            try {
                http_request2 = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    http_request2 = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!http_request2) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }

        
        httpstr = "f=" + f + "&s=" + info_str;
        http_request2.onreadystatechange = rec_written;
        http_request2.open('POST', url, true);
		  http_request2.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        http_request2.send(httpstr);
}

function rec_written() {
	if (http_request2.readyState == 4) {
		if (http_request2.status == 200) {
			    // perfect!
//			    log("info set ok");
		} else {
		    // there was a problem with the request,
		    // for example the response may be a 404 (Not Found)
		    // or 500 (Internal Server Error) response codes
//			    log("info set NOT ok");
			ajax_set_info( "m" + Game_LastMove , "0" );
		}
	}
}


    
	
/* Open A URL */
function call_url(url, str1) {
        http_request3 = false;

        if (window.XMLHttpRequest) { // Mozilla, Safari,...
            http_request3 = new XMLHttpRequest();
            if (http_request3.overrideMimeType) {
                http_request3.overrideMimeType('text/xml');
                // See note below about this line
            }
        } else if (window.ActiveXObject) { // IE
            try {
                http_request3 = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    http_request3 = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!http_request3) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }

        http_request3.open('POST', url, true);
		  http_request3.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        http_request3.send(str1);

}
