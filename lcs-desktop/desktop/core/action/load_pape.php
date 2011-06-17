<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* @load_prefs.php
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version 0.2~19
* Derniere mise a jour : 20/12/2010
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/

#header ('Content-type: text/html; charset=utf-8');
require  "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();
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
if ( $idpers == "0" || !is_file('/home/'.$login.'/Documents/Ressources/ilcs/ilcs_'.$login.'.json') ) {
	$url='../json/ilcs_default.json';
}
	$obj = file_get_contents($url);
//	$res = json_decode($obj);
//	$resp=array("r" =>$res);
//	echo json_encode($res);
?>