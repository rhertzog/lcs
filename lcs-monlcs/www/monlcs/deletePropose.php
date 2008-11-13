<?
	include "includes/secure_no_header.inc.php";
	if ($_POST) {
		extract($_POST);
		$sql = "select * from ml_tabs where nom='".$tab."';";
		//$content .= $sql;
		$curseur=mysql_query($sql) or die(stringForJavascript("ERR $sql"));
		if ( mysql_num_rows($curseur) != 0 ) {
			$id_menu = mysql_result($curseur,0,'id');
			$sql = "DELETE from `monlcs_db`.`ml_ressourcesProposees` WHERE id_ressource='$id' and id_menu='$id_menu' ;";
			$curseur=mysql_query($sql) or die(stringForJavascript("$sql requete invalide"));
			die(stringForJavascript("$sql"));
		}
	}
?>
