<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version  Lcs-2.4.10
* Derniere mise a jour " => mrfi =>" 14/03/2015
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/

header ('Content-type: text/html; charset=utf-8');
require  "/var/www/lcs/includes/headerauth.inc.php";
include "/usr/share/lcs/desktop/core/includes/desktop_check.php";
include ("/var/www/lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    $user = $purifier->purify( $_POST['user']) ;
    $file =  $purifier->purify($_POST['file']) ;
    $ou =  $purifier->purify($_POST['ou']);

$koa=explode("_", $file);
// tous les fichiers icone dans desktop/core/data appartenant au user
if( $ou=='all' ) {
	$del_f= $file;
	$mess= "le fichier  ".$del_f." a &eacute;t&eacute; supprim&eacute;";
	$img="info";
	$title="Information !";
	$cmd='for i in /usr/share/lcs/desktop/core/data/*; do rm  $i/'.$del_f.'; done';
	exec($cmd);
}
// cas des liens partages
else if( is_array($ou) ){
	$del_f= $file;
	$mess= "le fichier ".$del_f."a &eacute;t&eacute; supprim&eacute; des groupes ";
	$img="info";
	$title="Information !";
	$tmpArray=array();
	foreach($ou as $d){
		if( is_dir("../data/".$d) ){
			$cmd="rm /usr/share/lcs/desktop/core/data/".$d."/".$del_f;
			exec($cmd);
			$mess.="<br />".$d."&nbsp;";
		}
	}
	$dirData = "../data/";
	foreach(scandir($dirData) as $k=>$dirGp){
		if($dirGp !="." && $dirGp!=".." && is_dir($dirData.$dirGp) ){
			$ressCnt=$dirData.$dirGp."/".$del_f;
			if (isset($ressCnt) && is_file($ressCnt)) {
				$tmpGps =  json_decode(file_get_contents($ressCnt));
				$mess.="<br/>".$ressCnt;
				foreach($tmpGps as $g=>$gName){
					if($g!=="groups"){
						$mess.="<br/>".  $g."=>".$gName;
						$tmpArray[$g]=$gName;
					}
				}
				//$prefs['ress'][$del_f)] =  json_decode(file_get_contents($_ficn));
				$tmpArray['groups'][]=$dirGp;
				//re-ecriture du fichier
				$fp=fopen($ressCnt,"w");
				$json_fp=json_encode($tmpArray);
				$fwrite = fwrite($fp,$json_fp);
				fclose($fp);

			}
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
if( !isset($_POST['force']) && $_POST['force'] != "force" )
echo json_encode($resp);
?>