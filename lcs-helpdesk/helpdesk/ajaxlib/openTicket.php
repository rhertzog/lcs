<?php require_once('../include/common.inc.php');  

	$json = file_get_contents('../templates/openTicket.tpl');
	//$json = str_replace('%EMAIL%',$array_user['email'],$json);
	$login_user = strtolower($array_user['prenom'].'.'.$array_user['nom'].'@ac-caen.fr');
	$json = str_replace('%EMAIL%',$login_user,$json);
	die($json);
?>
