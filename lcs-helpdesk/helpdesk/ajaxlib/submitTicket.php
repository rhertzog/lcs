<?php 
	require_once('../include/common.inc.php'); 
	if ($_POST)
		$params = $_POST; 
	//if ($_GET)
	//	$params = $_GET; 

	if (!$params)
		die('0');	

	$data = array();
	foreach($params as $k=>$v) {
		$v = str_replace('\"','"',$v);
		$v = str_replace("\'","'",$v);
		$data[]= "$k=".urlencode($v);	
	}

	$url =  $HD->urlAPI."/createTicket"; 	
	//die(implode('<BR />',$data));
	//$proxy = new Proxy();
	
	$response = $HD->proxy->process($url, $data, 'POST');
	$headers = $HD->proxy->getHeaders();
	if (eregi('200 OK',$headers[0])) {
		@$XML  = new SimpleXMLElement($response);
                $status =  (string) $XML->status;
		die($status);
	}
	else
		echo "HTTP ERROR ".$headers[0];	
?>
