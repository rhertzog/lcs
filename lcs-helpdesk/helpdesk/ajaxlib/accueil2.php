<?php require_once('../include/common.inc.php');  
	//si mustRegister alors on charge le form register
	
	if ($HD->authenticate())
		$json = file_get_contents('../templates/accueil.tpl');
	$json = str_replace('%MSG%',$HD->getMessage(),$json);
	die($json);
?>
