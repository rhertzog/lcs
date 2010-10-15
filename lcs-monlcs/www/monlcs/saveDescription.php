<?php
include('includes/secure_no_header.inc.php');
if ($_POST) {
	extract($_POST);
	$sql = "select * from monlcs_db.ml_ressources where id='$id' and owner='$uid';";
	$c = mysql_query($sql) or die("Erreur $sql !");
	if (mysql_num_rows($c) == 0)
		die("Erreur de droits!");
	$sql = "update monlcs_db.ml_ressources set descr='$descr' where id='$id' and owner='$uid';";
	$c = mysql_query($sql) or die("Erreur Update $sql !");
	die('Ras!');

	
} else die('erreur');

?>