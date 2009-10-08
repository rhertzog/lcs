<?php require_once('../include/common.inc.php');  

	//si mustRegister alors on charge le form register
	
	if ($HD->authenticate($user))
		{
			if ($_POST)
				extract($_POST);
			if ($_GET)
				extract($_GET);
			
			if ($mustRegister == 2) {
				$json = file_get_contents('../templates/registerAdmin.tpl');
				$json = str_replace('%LISTEADMINS%',liste_admins(),$json);
			}
			else {
				$json = file_get_contents('../templates/register.tpl');
				$json = str_replace('%LOGIN%',"$login@$domain",$json);
			}
			$json = str_replace('%NOM%',$array_user['nom'],$json);
			$json = str_replace('%PRENOM%',$array_user['prenom'],$json);
			$json = str_replace('%EMAIL%',strtolower($array_user['prenom']).".".strtolower($array_user['nom'])."@ac-caen.fr",$json);
			//$json = str_replace('%NOM%',$array_user['nom'],$json);
		}
	die($json);
?>
