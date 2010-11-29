<?php
#header ('Content-type: text/html; charset=utf-8');
require  "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();

// creation du XML
$syndic = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<bureau>
	<userburo>
	</userburo>
</bureau>
XML;

$dom = new domDocument();
$dom->loadXML($syndic);
if (!$dom) {
  echo 'Erreur de traitement XML';
  exit;
  }
$frstN=array("wallpaper","pos_wallpaper", "iconsize", "iconsfield", "bgcolor", "quicklaunch");

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
$json="{bureau: { 'userburo': {"
	."wallpaper: '".htmlentities($_POST["wallpaper"])."',"
	."pos_wallpaper: '".$_POST["pos_wallpaper"]."',"
	."iconsiz: '".$_POST["iconsize"]."',"
	."iconsfiel: '".$_POST["iconsfield"]."',"
	."bgcolor: '".$_POST["bgcolor"]."',"
	."quicklaunch: '".$_POST["quicklaunch"]."',";
foreach($_POST["icons"] as $k=>$val){
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
		echo $json_fp;
#		echo json_decode($json_fp);

?> 