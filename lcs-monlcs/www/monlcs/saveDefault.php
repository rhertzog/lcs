<?php
	include "includes/secure_no_header.inc.php";
	if ($_POST) {
		extract($_POST);
	if ($tab == 'scenario_choix') {
		die(stringForJavascript('Pas géré ici'));
	}

	$ref=substr($ref,8);
	if (eregi('Cmd',$ref)) {//if
		die(stringForJavascript('Pas de update pour les cmd ;)'));
	}

	if ($ref == '')
		die("Probleme de ressource! $ref");
	
	$sqlY = "select * from ml_tabs where nom='".$tab."';";
	$curseurY=mysql_query($sqlY) or die(stringForJavascript("ERR $sqlY"));
	$id_menu = mysql_result($curseurY,0,id);

	if (eregi('Note',$ref)) {//if
		$ref=substr($ref,4);
		if ($ref == '')
			die('Pb de note');
		//chercher si je suis auteur
		$sqSQ = "select * from monlcs_db.ml_notes where id='$ref' ";
		$cSQ = mysql_query($sqSQ) or die("ERR $sqSQ");
		$TITRE = mysql_result($cSQ,0,'titre');
		$SETTER =  mysql_result($cSQ,0,'setter');
		$MSG =  mysql_result($cSQ,0,'msg');
		
		if ( $uid ==  $SETTER) {
			if ($vis == 'block')
				$sql = "UPDATE monlcs_db.ml_notes SET cible='skel_user' where id='$ref';";
			else
				$sql = "UPDATE monlcs_db.ml_notes SET cible='' where id='$ref';";
			$c = mysql_query($sql) or die("ERREUR $sql;");
		}//if suis auteur
		die(stringForJavascript("Fin de gestion de la note $ref avec $sql !"));
	}


	//C une resource
	$sql = "SELECT * FROM `monlcs_db`.`ml_geometry` WHERE `id_ressource` ='".$ref."' and id_menu='$id_menu' and user='skel_user';";
	$c = mysql_query($sql) or die("ERR $sql"); 
	if (mysql_num_rows($c) != 0 ) {//if2
	if ($vis == 'block') {
		$sq = "UPDATE `monlcs_db`.`ml_geometry` SET `x` = '".$x."', `y` = '".$y."', `z` = '".$z."',`w` = '".$w."',`h` = '".$h."' , `min` = '".$min."' WHERE `id_ressource` ='".$ref."' and id_menu='$id_menu' and user='skel_user' LIMIT 1 ;";
	} else {
		$sq = "DELETE FROM `monlcs_db`.`ml_geometry` WHERE `id_ressource` ='".$ref."' and id_menu='$id_menu' and user='skel_user' LIMIT 1 ;";
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
	NULL , '$id_menu', '$ref', 'skel_user', '$x', '$y', '$z','$w', '$h', '$min'
	);";
	print(stringForJavascript($sql));
	$curseur=mysql_query($sql) or die('<ul><li>requete invalide</li></ul>');
    } 
	}//else
	}//if get



?>
