<?php

include "includes/secure_no_header.inc.php";

if ($_POST) {
	extract($_POST);
$sql = "UPDATE monlcs_db.ml_ressources SET statut='$statut' WHERE id='$id';";
$c = mysql_query($sql) or die("ERR $sql");

}
?>