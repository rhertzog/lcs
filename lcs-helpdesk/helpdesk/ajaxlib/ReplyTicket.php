<?php

//Ajout du reply au ticket

require_once('../include/common.inc.php');

	if ($_POST)
                $params = $_POST;
        if ($_GET)
              $params = $_GET;

        if (!$params)
                die('0');

        $data = array();
        foreach($params as $k=>$v) {
                $v = str_replace('\"','"',$v);
		$data[]= "$k=".urlencode($v);
        }

	
        $url = $HD->urlHD."LcsAPI/ReplyTicket";
        //die(print_r($data));
        $response = $HD->getProxy()->process($url, $data, 'POST');

	die(htmlentities($response));
        $headers = $HD->getProxy()->getHeaders();
        if (eregi('200 OK',$headers[0])) {
                die($response);
        }  else {
                $network_error = stripslashes("HTTP ERROR");
		die("{ success: false }");
	}

?>
