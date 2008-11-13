<?
include "includes/secure_no_header.inc.php";

extract($_POST);

if ($ML_Adm == 'Y') {
$sql = "DELETE from `monlcs_db`.`ml_ressourcesProposees` WHERE id_ressource='$id';";
$curseur=mysql_query($sql) or die(stringForJavascript("$sql requete invalide"));
$sql = "DELETE from `monlcs_db`.`ml_ressourcesAffect` WHERE id_ressource='$id';";
$curseur=mysql_query($sql) or die(stringForJavascript("$sql requete invalide"));
$sql = "DELETE from `monlcs_db`.`ml_geometry` WHERE id_ressource='$id';";
$curseur=mysql_query($sql) or die(stringForJavascript("$sql requete invalide"));
$sql = "DELETE from `monlcs_db`.`ml_scenarios` WHERE id_ressource='$id';";
$curseur=mysql_query($sql) or die(stringForJavascript("$sql requete invalide"));
}

$sql = "SELECT * from `monlcs_db`.`ml_ressources` WHERE id='$id' ;";
$curseur=mysql_query($sql) or die(stringForJavascript("$sql requete invalide"));
$R = mysql_fetch_object($curseur);

if ( ($ML_Adm == 'Y') || ($uid == $R->owner ) ) {
$sql = "DELETE from `monlcs_db`.`ml_ressources` WHERE id='$id' ;";
$curseur=mysql_query($sql) or die(stringForJavascript("$sql requete invalide"));
}

die(stringForJavascript("CLEAN"));
?>
