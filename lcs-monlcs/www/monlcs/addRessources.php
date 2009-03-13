<?php

include "includes/secure_no_header.inc.php";

$pattern = array('&amp;');
$repl = array('&');

if ($_POST) {
extract($_POST);
//$url_vignette = choix migniature
$url = htmlspecialchars(urldecode($url));


//patch pour video en flv
$test = array_reverse(explode('.',$url));
if ($test && $test[0] == 'flv' && !eregi('monlcs/modules/flv/flvPlayer.swf',$url)) {
        $url = $baseurl."monlcs/modules/flv/flvplayer.swf?file=$url";
        //die($url);
}

//patch pour fichier geogebra
$test = array_reverse(explode('.',$url));
if ($test && $test[0] == 'ggb' && !eregi('monlcs/modules/geogebra/viewer.php',$url)) {
        $url = $baseurl."monlcs/modules/geogebra/viewer.php?ggb=$url";
        //die($url);
}


//patch pour widgets NetVibes non W3C compliant
$url = str_replace( $pattern, $repl, $url);



if ($sitetv == 0) {
	$url_vignette = htmlspecialchars(urldecode($url_vignette));
	$url_vignette = str_replace( $pattern, $repl, $url_vignette);
} else {
	$url_vignette = "http://www.lesite.tv/g_images/298_175/$sitetv.jpg";
}


$titre = htmlspecialchars(urldecode($titre));
$descrAdd = htmlspecialchars(urldecode($descrAdd));
if ($url == "")
	die('url vide!');
if ($titre == "")
	die('le titre manque!');

}
else die('aucun post');

if ($statut == 'true')
	$stat = 'public';
else
	$stat = 'private'; 

if ($statutP == 'true')
	$owner = 'all_profs';
else
	$owner = $uid; 

if ($rss == 'true')
	$gestRSS = 'RSS_no_img';
else
	$gestRSS = 'null'; 




$sql = "SELECT * FROM `monlcs_db`.`ml_ressources` WHERE url='$url' ;";
$c=mysql_query($sql) or die(stringForJavascript($sql));

$date = date('Y-m-d');

if (mysql_num_rows($c) == 0) {

if ($url_vignette == 'null')
	$url_vignette = null;

$sql ="INSERT INTO `monlcs_db`.`ml_ressources` ("
."`id` ,"
."`titre` ,"
."`url` ,"
."`RSS_template` ,"
."`owner` ,"
."`statut` ,"
."`ajoutee_le` ,"
."`url_vignette` ,"
."`descr`"
.")"
." VALUES ("
."'' , '$titre', '$url', '$gestRSS', '$owner', '$stat', '$date', '$url_vignette' ,'$descrAdd'"
.");";

$c2=mysql_query($sql) or die("ERR $sql");
die('La ressource a été insérée avec succes.');
//die($sql);
}
else {
	$R = mysql_fetch_object($c);
	$rep_statut = $R->statut;
	$rep_titre = $R->titre;
	if ($rep_statut == 'private')
		$grain ='priv&eacute;e';
	if ($rep_statut == 'public')
		$grain ='publique';	
	$rep_owner = $R->owner;
	$pattern = $baseurl.'/~';
	if (!eregi($pattern,$R->url)) {
		$sql = "UPDATE monlcs_db.ml_ressources  SET owner='all_profs' where id='$R->id'";
		$c = mysql_query($sql) or die("Erreur $sql");
		//die($sql);
	} else die('Conflit sur une ressource située dans un home utilisateur!');

	die("La ressource nommée $rep_titre doit déjà se trouver dans le pot de ressources!");
}



?>
