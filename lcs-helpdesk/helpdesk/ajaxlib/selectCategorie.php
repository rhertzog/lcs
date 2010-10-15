<?php 
        require_once('../include/common.inc.php');
        if ($_POST)
                $params = $_POST;

        $url =  $HD->urlAPI."/giveCategories";

        $response = $HD->proxy->process($url);
	$headers = $HD->proxy->getHeaders();
        if (eregi('200 OK',$headers[0])) {

		$json = file_get_contents('../templates/selectCategorie.tpl');
		$json = str_replace('%TREE%',$response,$json);
		die($json);

        }
        else
                echo "HTTP ERROR ".$headers[0];



?>
