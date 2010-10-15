<?php
	include "includes/secure_no_header.inc.php";
	if ($_POST) {
		extract($_POST);
		
		if ($titre == 'null')
			die("Titre nul!");
		
		$sql = "UPDATE monlcs_db.ml_scenarios SET titre='$titre' WHERE id_scen='$id_scen' ";
		$c = mysql_query($sql) or die("ERR $sql");
	
		die($sql);
	}
?>
