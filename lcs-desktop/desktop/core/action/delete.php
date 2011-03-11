<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* @delete.php
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version 2.4.8
* Derniere mise a jour: 06/03/2011
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/

header ('Content-type: text/html; charset=utf-8');
require  "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();

$user = $_POST['user'] ;
$file = $_POST['file'] ;	
$ou = $_POST['ou'];

$koa=explode("_", $file);
// tous les fichiers icone dans desktop/core/data appartenant au user
if( $ou=='all' ) {
	$del_f= $file.".json";
	$mess= "le fichier  ".$del_f." a &eacute;t&eacute; supprim&eacute;";
	$img="info";
	$title="Information !";
	$cmd='for i in /usr/share/lcs/desktop/core/data/*; do rm  $i/'.$del_f.'; done';
	#$mess.= " / ".$koa[0]." | ".$koa[1]." | ".$koa[2]." | ".$koa[3]." | ";
	exec($cmd);
	//foreach($res as $val){
	//$mess.= "<br>".$val." :: ";
	//}
}
else if( is_array($ou) ){
	$del_f= $file.".json";
	$mess= "le fichier ".$del_f."a &eacute;t&eacute; supprim&eacute; des groupes ";
	$img="info";
	$title="Information !";
	foreach($ou as $d){
		if( is_dir("../data/".$d) ){
			$cmd="rm /usr/share/lcs/desktop/core/data/$d/$del_f";
			exec($cmd);
			$mess.=$d." ";
		}
	}
		
#	$mess= " / ".$ou[0]." | ".$ou[1]." | ".$ou[2]." | ".$ou[3]." | ".$ou;
}
else if( $ou=='buro' ){
	$userrep = "/home/$user/Profile/";
	if (!is_dir($userrep)) {
		$mess= utf8_decode("Erreur! le dossier \"/home/$user/Profile/\" n'existe pas");
		$img="alert";
		$title="Erreur !";
	}else{
		$userfile = $userrep.$file.$user.'.json' ;
		if(!is_file($userfile)){
			$mess= utf8_decode('Le fichier '.$file .$user.'.json n&rsquo;existe pas');
			$img="alert";
			$title="Erreur !";
		}else{
			$command='rm '. $userfile;
			exec($command);
			$mess= utf8_decode('Le fichier '.$file .$user.'.json a &eacute;t&eacute; supprim&eacute;.');
			$img="info";
			$title="Information";
		}
	}
}
else if($ou=='default' && $user=='admin') {
	$userrep = "../json/";
	$userfile = "../json/PREFS_default.json" ;
		if(!is_file($userfile)){
			$mess= utf8_decode('Aucune configuration n\'est enregistr&eacute;e');
			$img="alert";
			$title="Erreur !";
		}else{
			$command='rm '. $userfile;
			exec($command);
			$mess= utf8_decode('Le par d&eacute;faut a &eacute;t&eacute; supprim&eacute;e.');
			$img="info";
			$title="Information";
		}
}
$resp=array(
"mess" => $mess,
"img" => $img,
"title" => $title
);
echo json_encode($resp);
?>