<?php

 require_once('../include/common.inc.php');
        if ($_POST)
              $params = $_POST;
        //if ($_GET)
        //      $params = $_GET;

        if (!$params)
                die('0');

	//var_dump($HD);
	//die();

        $data = array();
        foreach($params as $k=>$v) {
                $v = str_replace('\"','"',$v);
		$v = str_replace('\"','"',$v);
                $v = str_replace("\'","'",$v);
 
               $data[]= "$k=".urlencode($v);
        }

        $url =  $HD->urlHD."Tickets/ModifReply";
	 //die(implode('<BR />',$data));
        //$proxy = new Proxy();

        $response = $HD->proxy->process($url, $data, 'POST');
        //die(htmlentities($response));
	$headers = $HD->proxy->getHeaders();
        if (eregi('200 OK',$headers[0])) {
                //@$XML  = new SimpleXMLElement($response);
                //$status =  (string) $XML->status;
                //die($status);
		die();
        }
        else
                echo "HTTP ERROR ".$headers[0];


?>
