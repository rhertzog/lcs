<?php

	include "includes/secure_no_header.inc.php";

	extract($_POST);
	
	if ($ML_Adm == "Y")
		$sql = "SELECT id_ressource FROM monlcs_db.ml_scenarios where id_scen='$id_scen' ;";
	else
		$sql = "SELECT id_ressource FROM monlcs_db.ml_scenarios where id_scen='$id_scen' and setter='$uid' ;";
	
	$c2=mysql_query($sql) or die(stringForJavascript("ERR $sql"));

	if (mysql_num_rows($c2) == 0 )
		die('Pb de droits');

	if ($ML_Adm == "Y") {
		$sql = "DELETE FROM monlcs_db.ml_scenarios where id_scen='$id_scen';";
		$c=mysql_query($sql) or die(stringForJavascript("ERR $sql"));
	}
	else {
		$sql = "DELETE FROM monlcs_db.ml_scenarios where id_scen='$id_scen' and setter='$uid';";
		$c = mysql_query($sql) or die(stringForJavascript("ERR $sql"));
	}

		$sql = "DELETE FROM monlcs_db.ml_scenarios_token where id_scen='$id_scen' ;";
		$c = mysql_query($sql) or die(stringForJavascript("ERR $sql"));

		if ($ML_Adm == "Y") 
			$sql = "DELETE FROM monlcs_db.ml_acad_propose where type='scen' AND id_ress='$id_scen' ;";
		else		
			$sql = "DELETE FROM monlcs_db.ml_acad_propose where uid='$uid' AND type='scen' AND id_ress='$id_scen' ;";

		$c = mysql_query($sql) or die(stringForJavascript("ERR $sql"));



?>
