<?php require_once('../include/common.inc.php');  

	$json = file_get_contents('../templates/openTicket.tpl');
	$json = str_replace('%EMAIL%',$array_user['email'],$json);
	die($json);
?>
