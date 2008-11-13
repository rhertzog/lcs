<?php
	include("includes/secure_no_header.inc.php");

	if ($_POST) {

		extract($_POST);

		$sql2 = "select * from ml_tabs where id='$id' ;";
		$cx2 = mysql_query($sql2) or die("ERREUR $sql2");
		$Rx2 = mysql_fetch_object($cx2);	
		$id_onglet = $Rx2->id_tab;

		$sql0 = "select * from ml_zones where id='$id_onglet'";
		$cx0 = mysql_query($sql0) or die("ERREUR $sql0");
		$R = mysql_fetch_object($cx0);

		if ($R->status == 'perso')
			$u = $uid;
		if ($R->status == 'etab')
			$u = 'all';

		if ( ($R->status == 'etab') && ( $ML_Adm != "Y") )
			die('Problème de droits!');
		

		$sql2 = "select * from ml_tabs where id='$id' and user='$u' ;";
		$cx2 = mysql_query($sql2) or die("ERREUR $sql2");
		$Rx2 = mysql_fetch_object($cx2);		
			
		$id_tab = $id;
		$nom = $Rx2->nom;
		//Suppression des notes perso dans onglets perso
		$sql221 = "delete from ml_notes where menu='$nom';";
		$cx221 = mysql_query($sql221) or die("ERREUR $sql221");
		//
		$sql211 = "delete from ml_geometry where id_menu='$id_tab';";
		$cx211 = mysql_query($sql211) or die("ERREUR $sql211");
		
		$sql21 = "delete from ml_tabs where id='$id';";
		$cx21 = mysql_query($sql21) or die("ERREUR $sql21");
	
		die('RAS');
	}
else die('Pb de droits');

?>
