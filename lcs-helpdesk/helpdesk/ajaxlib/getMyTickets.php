<?php 
	require_once('../include/common.inc.php'); 
	if ($_POST)
		$params = $_POST; 

	$url = $HD->urlHD."LcsAPI/getMyTickets"; 	
	//die($url);
	$response = $HD->getProxy()->process($url);
	//die(htmlentities($response));
	$headers = $HD->getProxy()->getHeaders();
	if (eregi('200 OK',$headers[0])) {
		die($response);
	}
	else
		die("HTTP ERROR ".$headers[0]);	
?>
