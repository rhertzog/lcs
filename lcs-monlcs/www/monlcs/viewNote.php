<?php
include "includes/secure_no_header.inc.php";
$id = $_POST['id'];

$content .= "document.getElementById('view_note').style.display='none';";

$sql = "select * from ml_notes where id='$id';";
$curseur=mysql_query($sql) or die(stringForJavascript('requete invalide'));

if(mysql_num_rows($curseur) != 0) {
 
$titre = mysql_result($curseur,0,'titre');
$m = mysql_result($curseur,0,'msg');
$posx = mysql_result($curseur,0,'x');
$posy = mysql_result($curseur,0,'y');
$width = mysql_result($curseur,0,'w');
$height = mysql_result($curseur,0,'h');

$content .= "ajaxWindNote$id=dhtmlwindow.open('ajaxWindNote$id','inline','$m','$titre',";
$content .= "'width=$width"."px".",height=$height"."px".",left=$posx"."px".",top=$posy"."px".",";
$content .= "resize=1,scrolling=1,center=0'";
$content .= ");";



}
print stringForJavascript($content);




?>
