<?php

include("includes/secure_no_header.inc.php");
if ($_POST) {
	extract($_POST);
	//$id_onglet
	//$caption
	
	$clean = noaccent(strtolower(str_replace(" ","_",$caption)));

	$sql0 = "select * from ml_zones where id='$id_onglet'";
		$cx0 = mysql_query($sql0) or die("ERREUR $sql0");
		$R = mysql_fetch_object($cx0);

		if ($R->status == 'perso')
			$u = $uid;
		if ($R->status == 'etab')
			$u = 'all';

		if ( ($R->status == 'etab') && ( $ML_Adm != "Y") )
			die('Problème de droits!');


	$sql = "SELECT * FROM `ml_tabs` where user='$u' and nom='$clean';";
	$c = mysql_query($sql) or die("Erreur $sql");
	
	if (mysql_num_rows($c) != 0)
		die('Erreur, menu déjà présent ailleurs!\n Il faut changer de nom!');
	

	
	$sql4 ="INSERT INTO `ml_tabs` ( `id` , `id_tab` , `user` , `caption` , `nom` )"
	."VALUES ("
	."NULL , '$id_onglet', '$u', '$caption', '$clean');";
	$cx4 = mysql_query($sql4) or die("ERREUR $sql4");
	die('Menu ajouté avec succès!');

} else die('erreur');

?>
