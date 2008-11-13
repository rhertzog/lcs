<?php
include('includes/secure_no_header.inc.php');
if ($_POST) {
	extract($_POST);
	$sql = "select owner, descr from monlcs_db.ml_ressources where id='$id';";
	$c = mysql_query($sql) or die("Erreur $sql !");
	if (mysql_num_rows($c) == 0)
		die("Erreur d'indetifiant!");
	$descr = mysql_result($c,0,'descr');
	$owner = mysql_result($c,0,'owner');
	if (($descr == NULL) && ($owner == $uid))
		die('Changer description');
	if ($descr == NULL) 
		die('Aucune description');
	die(stringForJavascript($descr));
} else die('erreur');

?>