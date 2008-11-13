<?php

	include "includes/secure_no_header.inc.php";

	extract($_POST);
	
	if ($ML_Adm == "Y")
		$sql = "SELECT id_ressource FROM monlcs_db.ml_scenarios where id_scen='$id_scen' ;";
	else
		$sql = "SELECT id_ressource FROM monlcs_db.ml_scenarios where id_scen='$id_scen' and setter='$uid' ;";
	
	$c2=mysql_query($sql) or die(stringForJavascript("ERR $sql"));
	echo $sql;

	if (mysql_num_rows($c2) == 0 )
		die('Pb de droits');

	if ($ML_Adm == "Y")
		$sql = "DELETE FROM monlcs_db.ml_scenarios where id_scen='$id_scen';";
	else
		$sql = "DELETE FROM monlcs_db.ml_scenarios where id_scen='$id_scen' and setter='$uid';";

	$c2=mysql_query($sql) or die(stringForJavascript("ERR $sql"));
	echo $sql;

?>
