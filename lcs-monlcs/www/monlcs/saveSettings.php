<?php
include "includes/secure_no_header.inc.php";


if ($tab == 'scenario_choix') {
	die(stringForJavascript('Pas géré ici'));
}

$ref=substr($ref,8);
if (eregi('Cmd',$ref)) {//if
	die(stringForJavascript('Pas de update pour les cmd ;)'));
	}

if (eregi('Note',$ref)) {//if
	$ref=substr($ref,4);
	//chercher si je suis auteur
	$sqSQ = "select * from monlcs_db.ml_notes where id='$ref' ";
	$cSQ = mysql_query($sqSQ) or die("ERR $sqSQ");
	$TITRE = mysql_result($cSQ,0,'titre');
	$SETTER =  mysql_result($cSQ,0,'setter');
	$MSG =  mysql_result($cSQ,0,'msg');
	
	if ( $uid ==  $SETTER) {
	if ($vis == 'block') {
	$sql3 =" UPDATE `monlcs_db`.`ml_notes` SET x='$x', y='$y',  z='$z', w='$w', h='$h', min='$min'  WHERE id='$ref' ";
	$c3 = mysql_query($sql3) or die("ERR $sql3");
	}
	else {
	
	$sq7 = "SELECT * FROM monlcs_db.ml_scenarios where type='note' and id_ressource='$ref' ;";	
	$c7 = mysql_query($sq7) or die("ERR $sq7");
	
	if  ( mysql_num_rows($c7) == 0 ) {
	$sql3 =" DELETE FROM `monlcs_db`.`ml_notes`  WHERE id='$ref' and menu='$tab'";
	$c3 = mysql_query($sql3) or die("ERR $sql3");
	}
	}
	}//if suis auteur
	
	die(stringForJavascript("Gestion de la  note $ref terminé par $sql3"));
	}


	//?ressource imposee ?
	$sqlY = "select * from ml_tabs where nom='".$tab."';";
	$curseurY=mysql_query($sqlY) or die(stringForJavascript("ERR $sqlY"));
	$id_menu = mysql_result($curseurY,0,id);

	$sqlX = "SELECT * FROM `monlcs_db`.`ml_ressourcesAffect` WHERE `id_ressource` ='".$ref."' and id_menu='$id_menu'";
	$cX = mysql_query($sqlX) or die("ERR $sqlX");
	if (mysql_num_rows($cX) != 0) {
	$setter = mysql_result($cX,0,'setter');
	$idAffect = mysql_result($cX,0,'id');
	 
		//si pas setter bye bye
		$sqlZ = "SELECT * FROM `monlcs_db`.`ml_ressources` WHERE `id` ='".$ref."';";
		$cZ = mysql_query($sqlZ) or die("ERR $sqlZ");
		
		
		if ( $uid != $setter ) {
		die( stringForJavascript("Impossible de modifier une ress imposee !"));
		}
		else {
			if ($vis == 'block')
				$sq = "UPDATE `monlcs_db`.`ml_ressourcesAffect` SET `x` = '".$x."', `y` = '".$y."', `z` = '".$z."',`w` = '".$w."',`h` = '".$h."', `min` = '".$min."' WHERE `id` ='".$idAffect."' ;";
			if ($vis == 'none')
				$sq = "DELETE FROM `monlcs_db`.`ml_ressourcesAffect` WHERE `id` ='".$idAffect."' ;";
			$c = mysql_query($sq) or die("ERR $sq");
			die($sq);
		}
		
	}

	$sql = "SELECT * FROM `monlcs_db`.`ml_geometry` WHERE `id_ressource` ='".$ref."' and id_menu='$id_menu' and user='$uid';";
	$c = mysql_query($sql) or die("ERR $sql"); 
	if (mysql_num_rows($c) != 0 ) {//if2
	if ($vis == 'block') {
		$sq = "UPDATE `monlcs_db`.`ml_geometry` SET `x` = '".$x."', `y` = '".$y."', `z` = '".$z."',`w` = '".$w."',`h` = '".$h."' , `min` = '".$min."' WHERE `id_ressource` ='".$ref."' and id_menu='$id_menu' and user='$uid' LIMIT 1 ;";
	} else {
		$sq = "DELETE FROM `monlcs_db`.`ml_geometry` WHERE `id_ressource` ='".$ref."' and id_menu='$id_menu' and user='$uid' LIMIT 1 ;";
		}
	print(stringForJavascript($sq));
	$curseur=mysql_query($sq) or die('<ul><li>requete invalide</li></ul>');
	} else {
	if ($vis == 'block') {
	$sql =" INSERT INTO `monlcs_db`.`ml_geometry` (
	`id` ,
	`id_menu` ,
	`id_ressource` ,
	`user` ,
	`x` ,
	`y` ,
	`z` ,
	`w` ,
	`h` ,
	`min`
	
	)
	VALUES (
	NULL , '$id_menu', '$ref', '$uid', '$x', '$y','$z', '$w', '$h', '$min'
	);";
	print(stringForJavascript($sql));
	$curseur=mysql_query($sql) or die('<ul><li>requete invalide</li></ul>');
    } 
	}//else
	



?>
