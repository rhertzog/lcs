<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version  Lcs-2.4.10
* Derniere mise a jour " => mrfi =>" 14/03/2015
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/
#header ('Content-type: text/html; charset=utf-8');
require  "/var/www/lcs/includes/headerauth.inc.php";
include "/usr/share/lcs/desktop/core/includes/desktop_check.php";

// creation du XML
$xmlPrefs = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<bureau>
	<userburo>
	</userburo>
</bureau>
XML;

$dom = new domDocument();
$dom->loadXML($xmlPrefs);
if (!$dom) {
  echo 'Erreur de traitement XML';
  exit;
  }
$frstN=array("wallpaper","pos_wallpaper", "iconsize", "iconsfield", "bgcolor", "quicklaunch", "s_idart", "winsize", "data");

$liste = $dom->getElementsByTagName('userburo');
$neud = $liste->item(0);

foreach($frstN as $thisN){
	$$thisN  = $dom->createElement( $thisN );
	$$thisN ->appendChild(
	    $dom->createTextNode( $_POST[$thisN ] )
	);
$neud->appendChild( $$thisN  );
}

foreach($_POST['icons'] as $k=>$val){
$noeud = $liste->item(0);
$node = $dom->createElement("icon");
	foreach($val as $k1=>$val1){
		$icon_x = $dom->createElement($k1,$val1);
		$node->appendchild($icon_x);
	}
	$noeud->appendChild($node);
}


$s = simplexml_import_dom($dom);

// ecriture du fichier
$filexml='PREFS_'.$login.'.xml';
file_put_contents( '/home/'.$login.'/Profile/'.$filexml, $s->asxml() );

//echo $s->asxml();

#__/__/__/__/__/__/__/__/__/__/
# Test de création json
#__/__/__/__/__/__/__/__/__/__/
$i=0;
$_j= array();
$_j["bureau"]["userburo"]["wallpaper"] = htmlentities($_POST["wallpaper"]);
$_j["bureau"]["userburo"]["pos_wallpaper"] = $_POST["pos_wallpaper"];
$_j["bureau"]["userburo"]["iconsiz"] = $_POST["iconsize"];
$_j["bureau"]["userburo"]["iconsfield"] = $_POST["iconsfield"];
$_j["bureau"]["userburo"]["bgcolor"] = $_POST["bgcolor"];
$_j["bureau"]["userburo"]["quicklaunch"] = $_POST["quicklaunch"];
$_j["bureau"]["userburo"]["s_idart"] = $_POST["s_idart"];
$_j["bureau"]["userburo"]["winsize"] = $_POST["winsize"];
$_j["bureau"]["userburo"]["data"] = $_POST["data"];

$json="{bureau: { 'userburo': {"
	."wallpaper: '".htmlentities($_POST["wallpaper"])."',"
	."pos_wallpaper: '".$_POST["pos_wallpaper"]."',"
	."iconsiz: '".$_POST["iconsize"]."',"
	."iconsfiel: '".$_POST["iconsfield"]."',"
	."bgcolor: '".$_POST["bgcolor"]."',"
	."quicklaunch: '".$_POST["quicklaunch"]."',"
	."s_idart: '".$_POST["s_idart"]."',"
	."data: '".$_POST["data"]."',";
foreach($_POST["icons"] as $k=>$val){
	$_j["bureau"]["userburo"]["icon"][]= $val;
	$json.=$i==0 ? "":", ";
	$json.="icon_".$k.": {";
	$j=0;
	foreach($val as $k1=>$val1){
		$json.=$j==0 ? "":", ";
		$json.= $k1.": '".$val1."'";
		$j.=+1;
	}
	$json.="}";
	$i++;
}
$json.="}}}";

		$fp=fopen("/home/".$login."/Profile/PREFS_".$login.".json","w");
		$json_fp=json_encode($json);
#		fwrite($fp,$json);
		fwrite($fp,$json_fp);
		fclose($fp);
#		echo $json_fp;
#		echo json_decode($json_fp);

		$_fp=fopen("/home/".$login."/Profile/PREFS_TEST_".$login.".json","w");
		$_json_fp=json_encode($_j);
#		fwrite($fp,$json);
		fwrite($_fp,$_json_fp);
		fclose($_fp);
		echo $_json_fp;
#		echo json_decode($json_fp);

?>