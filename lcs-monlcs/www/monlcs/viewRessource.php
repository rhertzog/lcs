<?php
include "includes/secure_no_header.inc.php";

$id = $_POST['id'];
$maxY = $_POST['maxY'];
$minX = $_POST['minX'];
$minY = $_POST['minY'];
$maxX = $_POST['maxX'];

$maxScreen = $_POST['maxScreen'];

$content .= "document.getElementById('content').style.display='none';";

$sql = "select * from ml_ressources where id='$id';";
$curseur=mysql_query($sql) or die(stringForJavascript('requete invalide'));
$max = mysql_num_rows($curseur);

//id 	titre 	url 	RSS_template 	owner

for ($x=0;$x<mysql_num_rows($curseur);$x++) {
$id = mysql_result($curseur,$x,'id');

$width = 400;
$height = 300;

if ( ($maxX + $width) >  $maxScreen) {
	$posx = 5;
	$posy = $maxY+60;
	$line = true;
} else {
	$posx = $maxX+5;
	$posy = $minY;
       $line=false;
}
$rss = mysql_result($curseur,$x,'RSS_template');

$url = mysql_result($curseur,$x,'url');
if (eregi('.swf',$url))
				$url='giveCleanFlash.php?url='.$url;
$url_vignette = mysql_result($curseur,$x,'url_vignette');

if ($rss !='null') {
	if ($rss == 'RSS_img')
		$TEMP ="rss2html/template.html";
	else {
		$TEMP ="rss2html/template_no_img.html";
	}
	//$url = "rss2html/rss2html.php?XMLFILE=$url&TEMPLATE=$TEMP";
	} 
$titre = mysql_result($curseur,$x,'titre');

if ($url_vignette != null) {
	if (eregi('thumbalizr',$url_vignette))
		$urlAffiche = 'giveCleanVignette.php?url='.$url;
	else
		$urlAffiche = $url_vignette;
	}
else
	$urlAffiche = $url;

$content .= "ajaxWind$id=dhtmlwindow.open('ajaxWind$id','iframe','$urlAffiche','$titre',";
$content .= "'width=$width"."px".",height=$height"."px".",left=$posx"."px".",top=$posy"."px".",";
$content .= "resize=1,scrolling=1,center=0'";
$content .= ");";



}
print stringForJavascript($content);




?>
