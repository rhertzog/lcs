<?
include "includes/secure_no_header.inc.php";



//alimenter ml_ressourcesAffect 
$sql = "select * from monlcs_db.ml_tabs where nom='".$tab."';";
$curseur=mysql_query($sql) or die(stringForJavascript("ERR $sql"));
	if ($curseur) {
		$idTab = mysql_result($curseur,0,'id');
		$sq ="select * from monlcs_db.ml_ressourcesAffect where id_ressource='$idR' and id_menu='$idTab' and cible='$cible' and setter='$uid';";
		$c = mysql_query($sq) or die("ERR $sq");
		if (mysql_num_rows($c) !=0 ) {
			

		} else {
		
			$sql =" INSERT INTO `monlcs_db`.`ml_ressourcesAffect` (
			`id` ,
			`setter` ,
			`id_menu` ,
			`id_ressource` ,
			`cible` ,
			`x` ,
			`y` ,
			`z` ,
			`w` ,
			`h` ,
			`min` 
			)
			VALUES (
			NULL ,'$uid', '$idTab', '$idR', '$cible','$x','$y','$z','$w','$h','$min'
			);";

		$cIns = mysql_query($sql) or die("ERR $sql");
		echo $sql;

		}
	}//if curseur

?>


