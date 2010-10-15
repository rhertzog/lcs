<?php

	include "includes/secure_no_header.inc.php";
	
	if (is_eleve($uid))
		die('ko');
	if ($_POST) {
		extract($_POST);
		//id du bouton aide help (S,R,P) num 
		$cas =$id[4];
		$real_id = substr($id,5);
		
		if ($cas == 'S')
			$sql = "select * from monlcs_db.ml_scenarios where setter='$uid' and id='$real_id';";
		else
			$sql = "select * from monlcs_db.ml_ressources where owner='$uid' and id='$real_id';";

		$c = mysql_query($sql) or die("ERREUR $sql");

		if (mysql_num_rows($c) == 0)	
			die('ko');
		else {
			$R = mysql_fetch_object($c);
			die(stringForJavascript($R->descr));		
		}

	} else die('ko');

?>
