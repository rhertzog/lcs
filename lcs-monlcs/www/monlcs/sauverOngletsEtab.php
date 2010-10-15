<?php
include("includes/secure_no_header.inc.php");
if ($_POST) {
	extract($_POST);
	//test conformité liste menus
	$liste_menu=str_replace("##","#",$liste_menu);
	$elems = explode('#',$liste_menu);
	$elems_clean=array();
	//nettoyage
	for ($x=0;$x<count($elems);$x++) {
		if ($elems[$x] != null && !in_array($elems[$x],$elems_clean)) {
			if (eregi('perso',$elems[$x]))
				die('Désolé le nom perso est réservé! Veuillez en choisir un autre S.V.P');

			$elems_clean[] = $elems[$x];
			}
	}
	$elems = $elems_clean;
	sort($elems);
	//test existence nom onglet
	$sql1 = "select * from ml_zones where nom='$onglet_name'";
	$cx1 = mysql_query($sql1) or die("ERREUR $sql1");
	if (mysql_num_rows($cx1) != 0)
		die ("$onglet_name est déjà pris !");
			
	//retrouver le rang de l'onglet
	if ($place_etab == -1) {
	//retrouver le nombre d'onglets statiques
	$sql111 = "select * from ml_zones where user='all' and status='fixed'";
	$cx111 = mysql_query($sql111) or die("ERREUR $sql111");
	$depart = mysql_num_rows($cx111)+1;
	} else {
		$depart = $place_etab+1;	
	}
		
	//Decaler tous les autres onglets (etab + perso)
	$sql_112 = "UPDATE ml_zones SET `rang` = `rang` + 1 where rang >= '$depart' and status <> 'fixed' ";
	$cx_112 = mysql_query($sql_112) or die("ERREUR $sql_112");

	//ajout des menus pour user
	$sql2 = "insert into ml_zones (`id`,`nom`,`user`,`rang`,`status`) VALUES('','$onglet_name','all','$depart','etab');";
	$cx2 = mysql_query($sql2) or die("ERREUR $sql2");
	//recup de l'id onglet
	$sql3 = "select id from ml_zones where nom='$onglet_name' and user='all' and status='etab';";
	$cx3 = mysql_query($sql3) or die("ERREUR $sql3");
	$idOnglet = mysql_result($cx3,0,'id');
	
	for ($x=0;$x<count($elems);$x++) {
		$menu = $elems[$x];
		$caption = strtolower(str_replace(" ","_",$menu));
		$sql4 ="INSERT INTO `ml_tabs` ( `id` , `id_tab` , `user` , `caption` , `nom` )"
			."VALUES ("
			."NULL , '$idOnglet', 'all', '$menu', '$caption');";
		 
		$cx4 = mysql_query($sql4) or die("ERREUR $sql4");
	}
	
	
	print("$uid a créé avec succès $onglet_name");

} else die('erreur');
	

		
		
    	 
	



?>
