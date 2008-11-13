<?
include "includes/secure_no_header.inc.php";

extract($_POST);

$sql = "DELETE from `monlcs_db`.`ml_ressourcesAffect` WHERE id='$id';";
$curseur=mysql_query($sql) or die(stringForJavascript("$sql requete invalide"));

die(stringForJavascript("$sql"));
?>
