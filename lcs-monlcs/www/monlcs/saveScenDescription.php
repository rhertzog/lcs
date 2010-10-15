<?php
include('includes/secure_no_header.inc.php');
if ($_POST) {
	extract($_POST);
	$sql = "select * from monlcs_db.ml_scenarios where id='$id';";
	$c = mysql_query($sql) or die("Erreur $sql !");
	if (mysql_num_rows($c) == 0)
		die("Erreur de droits!");
	$R = mysql_fetch_object($c);
	$sql = "update monlcs_db.ml_scenarios set descr='$descr' where titre='$R->titre' and matiere='$R->matiere' and setter='$uid';";
	$c = mysql_query($sql) or die("Erreur Update $sql !");
	die('Ras!');

	
} else die('erreur');

?>