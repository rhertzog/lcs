<?php
include "includes/secure_no_header.inc.php";
if ($_POST) {
	extract($_POST);
		
	if ($titre == 'null')
		die("Titre nul!");

	if (eregi('Note',$id)) {
		$idRe = substr($id,12);
		$sql = "UPDATE monlcs_db.ml_notes SET titre='$titre' WHERE id='$idRe'  and setter='$uid';";
		$c = mysql_query($sql) or die("ERR $sql");

	}

	else
	{
		$idRe = substr($id,8);
		$sql = "UPDATE monlcs_db.ml_ressources SET titre='$titre' WHERE id='$idRe'  and owner='$uid';";
		$c = mysql_query($sql) or die("ERR $sql");
	}


	die($sql);

}
?>
