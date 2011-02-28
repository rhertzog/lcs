<?php
header ('Content-type: text/html; charset=utf-8');
require  "/var/www/lcs/includes/headerauth.inc.php";
include "/var/www/Annu/includes/ldap.inc.php";
include "/var/www/Annu/includes/ihm.inc.php";
list ($idpers, $login)= isauth();
$resp=array();

$who      = $_POST['user'] ;
$title 	  = $_POST['name'];
$action   = $_POST['act'];
$name     = strtolower( filter( $title ) ) ;
$dir_ress = "/home/".$login."/Documents/Ressources";
$dir_pape = $dir_ress."/".$name;

/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Ecrirure et enregistrement du XML
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/
if(!is_dir( $dir_ress )) mkdir( $dir_ress, 0770 );
if(!is_dir($dir_pape)) mkdir($dir_pape, 0770);

if($action=="rm" ) {
	$pape = $dir_pape;
	$dir = opendir($pape);
	while($file = readdir($dir)) {
		if ($file!="." && $file != "..")
		unlink($pape . "/" . $file);
	}
	closedir($dir);
	rmdir($pape);
	echo json_encode( array('default'=> 'le parcours '.$name.' a été supprimé') );
}
else if ( $action=='save') {
	$_j = array();
	$_j['title']=$title;
	$_j['name']=$name;
	if( is_array($_POST['data'])  ) {
		foreach($_POST['data'] as $k=>$val){
			$_j['ress'][$k] = $val;
		}
	}

	$fp=fopen($dir_pape."/PAPE_".$login."_".$name.".json","w+");
	$json_fp=json_encode($_j);
	fwrite($fp,$json_fp);
	fclose($fp);
	echo json_encode( array('default'=> 'le parcours '.$name.' a été enregistré') );
}
#__/__/__/__/__/__/__/__/__/__/
# Function filter(chaine)
# remplace tous les caracteres accentues par leur euivalent et les espace par des underscore
# supprime tous les carateres non alpha-numeriques
#__/__/__/__/__/__/__/__/__/__/
function filter($in) {
	$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[ ]@i','@[^a-zA-Z0-9_]@');
	$replace = array ('e','a','i','u','o','c','_','');
	return preg_replace($search, $replace, $in);
}
?> 