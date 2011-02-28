<?php
header ('Content-type: text/html; charset=utf-8');
require  "/var/www/lcs/includes/headerauth.inc.php";
include "/var/www/Annu/includes/ldap.inc.php";
include "/var/www/Annu/includes/ihm.inc.php";
list ($idpers, $login)= isauth();
$resp=array();

$who          = $_POST['user'] ;
$type         = $_POST['ress_type'] ;
$title        = $_POST['ress_title'] ;
$url          = $_POST['url'] ;
$rss          = $_POST['rss'] ;
$name         = $_POST['name'] ;
$description  = $_POST['description'] ;
$owner        = $_POST['owner'] ;
$statut       = $_POST['ress_statut'] ;
$vignette     = $_POST['vignette'] ;
$filexml      = 'RESS_'.$login.'_'.$name.'.xml';
$thumbnail = $vignette!='' ? $vignette : 'images/icons/logo_lcs20.png';
//if($who!=$login) return ;
list($user, $groups)=people_get_variables($login, false);

/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Creation du XML
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/
$syndic = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<response>
	<fichier version="1.0" type=""></fichier>
    <etab></etab>
    <auteur></auteur>
    <matiere></matiere>
    <titre></titre>
    <description></description>
    <ressource>
        <type></type>
        <url></url>
        <titre_ress></titre_ress>
        <rss></rss>
        <vignette></vignette>
        <owner></owner>
        <statut></statut>
        <descr_ress></descr_ress>
        <created_at></created_at>
        <x></x>
        <y></y>
        <z></z>
        <w></w>
        <h></h>
        <min></min>
    </ressource>
</response>
XML;

/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Insertion des valeurs
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/
$entries = simplexml_load_string($syndic);

/*
Modification de la valeur d'une balise
$entries->fichier = 'contenu de b';
$entries->c = 'Contenu spécial avec balises <> ';
*/
$entries->fichier = $filexml;
$entries->etab = $_SERVER['HTTP_HOST'];
$entries->auteur = utf8_encode($user["fullname"]);


$entries->ressource->type = $type;
$entries->ressource->url = $url;
$entries->ressource->titre_ress = utf8_encode($title);
$entries->ressource->rss = $rss;
$entries->ressource->vignette = $thumbnail;
$entries->ressource->owner = utf8_encode($owner);
$entries->ressource->statut = $statut;
$entries->ressource->descr_ress = utf8_encode($description);
$entries->ressource->created_at = mktime();
$entries->ressource->x = $_POST['x'];
$entries->ressource->y = $_POST['y'];
$entries->ressource->z = $_POST['z'];
$entries->ressource->h = $_POST['h'];
$entries->ressource->min = $_POST['min'];
#mb_convert_encoding($entries->ressource->descr_ress,'iso-8859-1','utf-8');

//Ajout ou modification d'un attribut
$entries->fichier['langue'] = 'fr';
$entries->fichier['type'] = 'classique';

//Suppression d'un attribut
#Unset($entries->b['version']);

#echo $entries->asxml();
//echo '/home/'.$login.'/Documents/Ressources';
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Ecrirure et enregistrement du XML
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/
$res=array();
if(!is_dir('/home/'.$login.'/Documents/Ressources')){ mkdir('/home/'.$login.'/Documents/Ressources', 0770);}
if (file_exists('/home/'.$login.'/Documents//Ressources/'.$filexml)) {
	$res['success']= 0;
	$res['mess']= 'ErreurLe fichier '.$filexml.' existe. Choisissez un autre nom';
}
else{
	file_put_contents( '/home/'.$login.'/Documents//Ressources/'.$filexml, $entries->asxml() );
	$res['success']= 1;
	$res['mess']= 'Le fichier '.$filexml.' a été enregistré';
}
	echo json_encode($res);
	return;

?> 