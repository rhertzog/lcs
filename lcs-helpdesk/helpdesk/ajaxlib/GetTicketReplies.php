<?php
/*SCRIPT QUI CHERCHE LES INFOS LIEES A UN TICKET */
  	require_once('../include/common.inc.php');
	if ($_POST)
		extract($_POST);
	if ($_GET)
		extract($_GET);

        $url = $HD->urlHD."LcsAPI/GetTicketReplies?id=$id";
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
