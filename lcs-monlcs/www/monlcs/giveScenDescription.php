<?php
include('includes/secure_no_header.inc.php');
if ($_POST) {
	extract($_POST);
	$sql = "select setter,descr from monlcs_db.ml_scenarios where id='$id';";
	$c = mysql_query($sql) or die("Erreur $sql !");
	if (mysql_num_rows($c) == 0)
		die("Erreur d'identifiant!");
	$descr = mysql_result($c,0,'descr');
	$setter = mysql_result($c,0,'setter');
	if (($descr == NULL) && ($setter == $user))
		die('Changer description');
	if ($descr == NULL) 
		die('Aucune description');
	die($descr);
} else die('erreur');

?>