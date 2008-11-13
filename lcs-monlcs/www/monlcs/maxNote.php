<?
include "includes/secure_no_header.inc.php";

extract($_GET);

$sql = "SELECT *  from `monlcs_db`.`ml_notes` WHERE id='$id';";
$curseur=mysql_query($sql) or die(stringForJavascript("$sql requete invalide"));
if ($curseur) {
	$R = mysql_fetch_object($curseur);
	echo $R-> msg;
}
	

?>
