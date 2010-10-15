<?
include "includes/secure_no_header.inc.php";
if ($_POST) {

extract($_POST);

$sql = "DELETE from `monlcs_db`.`ml_droits` WHERE id='$id';";
//echo $sql;
$curseur=mysql_query($sql) or die("<ul><li>$sql requete invalide</li></ul>");
}

?>
