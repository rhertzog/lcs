<?php require_once('../include/common.inc.php');  

	$json = file_get_contents('../templates/openTicket.tpl');
	$contact = strtolower($array_user['prenom']).".".strtolower($array_user['nom'])."@ac-caen.fr";
	$json = str_replace('%EMAIL%',$contact,$json);
	//$json = str_replace('%EMAIL%',$array_user['email'],$json);
	die($json);
?>
