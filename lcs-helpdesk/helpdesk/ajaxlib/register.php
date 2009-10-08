<?php 
	require_once('../include/common.inc.php'); 
	if ($_POST)
		$params = $_POST; 
	//if ($_GET)
	//	$params = $_GET; 

	$data = array();
	foreach($params as $k=>$v) {
		//if ($k == 'login')
		//	$v .= '@'. $domain;
 
		$data[]= "$k=".urlencode($v);	
	}

	$url =  $HD->urlAPI."/register"; 	
	
	$response = $HD->proxy->process($url, $data, 'POST');
	//die(htmlentities($response));
	$headers = $HD->proxy->getHeaders();
	if (eregi('200 OK',$headers[0])) {
		@$XML  = new SimpleXMLElement($response);
                $auth_status =  (string) $XML->status;
                $message =  (string) $XML->message;
                if ($auth_status == 'SUCCESS')
			die($auth_status);
		else
			die("$auth_status $message");
	}
	else
		echo "HTTP ERROR ".$headers[0];	
?>
