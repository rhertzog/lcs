<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version  Lcs-2.4.10
* Derniere mise a jour " => mrfi =>" 14/03/2015
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/
require  "/var/www/lcs/includes/headerauth.inc.php";
include "/usr/share/lcs/desktop/core/includes/desktop_check.php";
/*
*Prevoir verif de l'identite du user
*/

/*
* Recup des liens rss
*/
$res=array();

	//$dir = '/home/'.$user.'/Documents/Ressources';
	$dir = '../../Ressources';
	//
	if( !is_dir($dir) ) {
		$res['error'] = 'Aucune ressource n\'est disponible (Pas de dossier Ressource)';
		echo  json_encode($res);
		exit;
	}
	else{
		$files = scandir( $dir );
		$m = "/^RESS/";
		foreach( $files as $r ){
			if( $r !="." && $r!=".." && !preg_match( $m, $r ) ){
				//$url="/home/admin/Documents/Ressources/educnat/PAPE_admin_educnat.json";
				$url=$dir."/".$r."/PAPE_".$login."_".$r.".json";
				$obj= file_get_contents($url);
				$res[$r] = array(
					"url" => $url,
					"pape" => json_decode($obj)
				);
			}
		}
		echo json_encode($res);
		exit;
	}

$url='/home/'.$login.'/Documents/Ressources/iLcs/ilcs_'.$login.'.json';
if ( $login == "" || !is_file('/home/'.$login.'/Documents/Ressources/ilcs/ilcs_'.$login.'.json') ) {
	$url='../json/ilcs_default.json';
}
	$obj = file_get_contents($url);
//	$res = json_decode($obj);
//	$resp=array("r" =>$res);
//	echo json_encode($res);
?>