<html>
	<head>
	<title></title>
	</head>
	<body style="width:420px; font-family:sans;">
		<textarea id="chatwindow" rows="10" cols="56" style="border:1px solid #aaaaaa; padding:4px;" readonly></textarea><br>
		<input id="chatmsg" type="text" size="20" style="border:1px solid #aaaaaa;" onkeyup="keyup(event.keyCode);"> <input type="button" value="ok" onclick="submit_msg()" style="cursor:pointer;border:1px solid gray;">
	</body>
</html>

<script type="text/javascript">
/* Writing Ajax Requests */
var http_request=false;var http_request2=false;var intUpdate;function ajax_request(url){http_request=false;if(window.XMLHttpRequest){http_request=new XMLHttpRequest();if(http_request.overrideMimeType){http_request.overrideMimeType('text/xml');}}else if(window.ActiveXObject){try{http_request=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){try{http_request=new ActiveXObject("Microsoft.XMLHTTP");}catch(e){}}}
if(!http_request){alertLAC('Giving up :( Cannot create an XMLHTTP instance');return false;}
http_request.onreadystatechange=alertLACContents;http_request.open('GET',url,true);http_request.send(null);}
function alertLACContents(){if(http_request.readyState==4){if(http_request.status==200){rec_response(http_request.responseText);}else{}}}

/* Reading Ajax Requests */
function ajax_request2(url){http_request2=false;if(window.XMLHttpRequest){http_request2=new XMLHttpRequest();if(http_request2.overrideMimeType){http_request2.overrideMimeType('text/xml');}}else if(window.ActiveXObject){try{http_request2=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){try{http_request2=new ActiveXObject("Microsoft.XMLHTTP");}catch(e){}}}
if(!http_request2){alertLAC('Giving up :( Cannot create an XMLHTTP instance');return false;}
http_request2.onreadystatechange=alertLACContents2;http_request2.open('GET',url,true);http_request2.send(null);}
function alertLACContents2(){if(http_request2.readyState==4){if(http_request2.status==200){rec_chatcontent(http_request2.responseText);}else{}}}

/* Chat Stuff */
waittime=2000;
intUpdate=window.setTimeout("read_cont()", waittime);
chatwindow.value = "connecting to chat...";

<?php
	$id1 = $_COOKIE["achess_id"];
	if (trim($id1) != "") {
		$fn = $id1 . "_chat.txt";
	} else {
		$fn = "chat.txt";
	}
	echo "chatfn='" . $fn . "';";
?>

	function display_msg(msg1) {
		/* Fill Textarea with the Content */
		chatwindow.value = msg1;
	}

	function write_msg(msg1) {
		ajax_request("w.php?m=" + escape(msg1));
	}
		
	function submit_msg() {
		/* Send My Message */
		write_msg(chatmsg.value);
		chatmsg.value="";
	}
			
	function rec_response(str1) {
		/* Response From w.php */
	}

	function rec_chatcontent(cont1) {
		if (cont1 != "") { 
			out1 = "";
			/* Display Last Message First */
			while (cont1.indexOf("\n") > -1) {
				out1 = cont1.substr(0, cont1.indexOf("\n")) + "\n" + out1;
				cont1 = cont1.substr(cont1.indexOf("\n") + 1);
			}
			out1 = unescape(out1);
			if (chatwindow.value != out1) { display_msg(out1); }
			intUpdate=window.setTimeout("read_cont()", waittime);
		}		
	}

	function read_cont() { 
		/* Prevent Buffering by using ?x=timeinms */
			zeit = new Date();
		   ms = (zeit.getHours() * 24 * 60 * 1000) + (zeit.getMinutes() * 60 * 1000) + (zeit.getSeconds() * 1000) + zeit.getMilliseconds();
		ajax_request2(chatfn + "?x=" + ms); 
	}
	function keyup(arg1) { 	if (arg1 == 13) { 	submit_msg(); } }
</script>
