<?php
include("includes/secure_no_header.inc.php");
if ($_POST) {
	extract($_POST);
	//test conformit� liste menus
	$liste_menu=str_replace("##","#",$liste_menu);
	$elems = explode('#',$liste_menu);
	$elems_clean=array();
	//nettoyage
	for ($x=0;$x<count($elems);$x++) {
		if ($elems[$x] != null && !in_array($elems[$x],$elems_clean)) {
			if (eregi('perso',$elems[$x]))
				die('D�sol� le nom perso est r�serv�! Veuillez en choisir un autre S.V.P');

			$elems_clean[] = $elems[$x];
			}
	}
	$elems = $elems_clean;
	sort($elems);
	
	//MODE ONGLET PERSO

	//test existence nom onglet
	$sql1 = "select * from ml_zones where nom='$onglet_name' and user='$uid'";
	$cx1 = mysql_query($sql1) or die("ERREUR $sql1");
	if (mysql_num_rows($cx1) != 0)
		die ("$onglet_name est d�j� pris !");
	//retrouver le nombre d'onglets statiques
	$sql111 = "select * from ml_zones where user='all'";
	$cx111 = mysql_query($sql111) or die("ERREUR $sql111");
	$depart = mysql_num_rows($cx111)+1;
	
	//retrouver rang du menu pour user
	$sql11 = "select * from ml_zones where user='$uid' order by rang DESC;";
	$cx11 = mysql_query($sql11) or die("ERREUR $sql11");
	$rang = $depart+mysql_num_rows($cx11);
		
	
	//ajout des menus pour user
	$sql2 = "insert into ml_zones (`id`,`nom`,`user`,`rang`) VALUES('','$onglet_name','$uid','$rang');";
	$cx2 = mysql_query($sql2) or die("ERREUR $sql2");
	//recup de l'id onglet
	$sql3 = "select id from ml_zones where nom='$onglet_name' and user='$uid';";
	$cx3 = mysql_query($sql3) or die("ERREUR $sql3");
	$idOnglet = mysql_result($cx3,0,'id');
	
	for ($x=0;$x<count($elems);$x++) {
		$menu = $elems[$x];
		$caption = noaccent(strtolower(str_replace(" ","_",$menu)));
		$sql4 ="INSERT INTO `ml_tabs` ( `id` , `id_tab` , `user` , `caption` , `nom` )"
			."VALUES ("
			."NULL , '$idOnglet', '$uid', '$menu', '$caption');";
		 
		$cx4 = mysql_query($sql4) or die("ERREUR $sql4");
	}
	
	
	print("$uid a cr�� avec succ�s $onglet_name");

} else die('erreur');
	

		
		
    	 
	



?>
