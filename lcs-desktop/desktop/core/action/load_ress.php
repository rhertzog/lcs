<?php
require  "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();
$user = $_POST['user'] ;
$file = $_POST['file'] ;
#$dir_ = $_POST['dir'] ;
/*
<response>
	<fichier version="1.0" type="classique" langue="fr"/>
    <etab>lcs.demo.lyc50.ac-caen.fr</etab>
    <auteur>Admin Se3</auteur>
    <matiere/>
    <titre/>
    <description/>
    <ressource>
        <type>ressource</type>
        <url>http://ent.etab.ac-caen.fr</url>
        <titre_ress>ressource_49</titre_ress>
        <rss>null</rss>
        <vignette/>
        <owner>admin</owner>
        <statut>private</statut>
        <descr_ress>aaaaaaa</descr_ress>
        <created_at>1291496481</created_at>
        <x/>
        <y/>
        <z/>
        <w/>
        <h/>
        <min/>
    </ressource>
</response>
*/

$res=array();
$res["file"] = $file;

	#$dir    = '/home/'.$user.'/Documents/Ressources';
	$dir    = '../../Ressources';
	if( !is_dir($dir) ) {
		$res['error'] = 'Aucune ressource n\'est disponible (Pas de dossier Ressource)'; 
		echo  json_encode($res); 
		exit;
	}
	else{	
		$files = scandir($dir);
		if( !$files[2] ){
			$res['error'] = 'Aucune ressource n\'est disponible'; 
			echo  json_encode($res); 
			exit;
		}
		// cas ou on liste tout le rep Ressources
		if($file=="all"){
			$response = simplexml_load_file('../../Ressources/'.$files[2]);
			$res["etab"]=htmlentities($response->etab);
			$res["auteur"]=utf8_decode($response->auteur);
			$res["matiere"]=mb_convert_encoding($response->matiere,'iso-8859-1','utf-8');
			$res["titre"]= mb_convert_encoding($response->titre,'iso-8859-1','utf-8');
			$res["description"]=mb_convert_encoding($response->description,'iso-8859-1','utf-8');
			foreach($files as $r){
				$_f='../../Ressources/'.$r;
				if($r !="." && $r!=".." && is_file($_f) ){
				$response = simplexml_load_file('../../Ressources/'.$r);
				$res["ressource"][] = 	readXmlRess($response);
				}
			}
		} 
		else {
			$response = simplexml_load_file('../../Ressources/'.$file);
			//mb_convert_encoding($ressource->descr_ress,'iso-8859-1','utf-8'); 
			$res["etab"]=htmlentities($response->etab);
			$res["auteur"]=utf8_decode($response->auteur);
			$res["matiere"]=mb_convert_encoding($response->matiere,'iso-8859-1','utf-8');
			$res["titre"]= mb_convert_encoding($response->titre,'iso-8859-1','utf-8');
			$res["description"]=utf8_decode($response->description);
			
			$res["ressource"]=readXmlRess($response) ;
		
		}
		echo json_encode($res);
	}

function readXmlRess($response ) 
{
	$jsonFormat = array();
	foreach ($response->ressource as $ressource) {
		$jsonFormat["f_file"] = $file;
		$jsonFormat["f_type"] = mb_convert_encoding($ressource->type,'iso-8859-1','utf-8');
		$jsonFormat["f_url"] = htmlentities($ressource->url);
		$jsonFormat["f_rss"] = htmlentities($ressource->rss);
		$jsonFormat["f_titre"] =  mb_convert_encoding($ressource->titre_ress,'iso-8859-1','utf-8');
		$jsonFormat["f_rss"] = htmlentities($ressource->rss);
		$jsonFormat["f_vignette"] = htmlentities($ressource->vignette);
		$jsonFormat["f_owner"] =  mb_convert_encoding($ressource->owner,'iso-8859-1','utf-8');
		$jsonFormat["f_statut"] =  mb_convert_encoding($ressource->statut,'iso-8859-1','utf-8');
		$jsonFormat["f_x"] = intval($ressource->x);
		$jsonFormat["f_y"] = intval($ressource->y);
		$jsonFormat["f_z"] = intval($ressource->z);
		$jsonFormat["f_h"] = intval($ressource->h);
		$jsonFormat["f_min"] = intval($ressource->min);
		$jsonFormat["f_description"] = utf8_decode($ressource->descr_ress);
			 
	}
	//$res.='../../Ressources/'.$file;
	return $jsonFormat;
}

function clean($badstring){
    $pattern = Array("?", "?", "?", "?", "?", "?", "?", "?", "?", "?");
    $rep_pat = Array("e", "e", "e", "c", "a", "a", "i", "i", "u", "o");
    $cleaned= str_replace($pattern, $rep_pat, $badstring);
    $file_bad = array("@-@", "@_@", "@[^A-Za-z0-9_\ ]@", "@\ +@");
    $file_good = array(" ", " ", "", " ");
    $cleaned= preg_replace($file_bad, $file_good, $cleaned);
    $cleaned= str_replace(" ", "_", trim($cleaned));
    return $cleaned;
}
?>