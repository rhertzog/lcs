<?php
include "includes/secure_no_header.inc.php";

require ("/var/www/lcs/includes/config.inc.php");
require_once ("/var/www/lcs/includes/functions.inc.php");
$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");


$id = $_POST['id'];

// Liens dynamiques vers les plugins installés 
$query="SELECT * from applis where name='$id';";
$result=mysql_query($query);
if ($result) {
        while ($r=mysql_fetch_object($result)) {
		$posx = 100;
		$posy = 100;
		$width = 400;
		$height = 300;
		$url = '../lcs/statandgo.php?use='.$id;	
		$titre = $r->descr;
		$content .= "ajaxWind".$r->id."=dhtmlwindow.open('ajaxWind".$r->id."','iframe','$url','$titre',";
		$content .= "'width=$width"."px".",height=$height"."px".",left=$posx"."px".",top=$posy"."px".",";
		$content .= "resize=1,scrolling=1,center=0'";
		$content .= ");";

	 }
							

print stringForJavascript($content);
}



?>
