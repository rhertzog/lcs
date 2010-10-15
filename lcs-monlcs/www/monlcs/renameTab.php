<?php
	include("includes/secure_no_header.inc.php");

	if ($_POST) {
		extract($_POST);

	if (eregi('perso',$nouveau_titre))
		die('Désolé le nom perso est réservé! Veuillez en choisir un autre S.V.P');
	
	$clean = noaccent(strtolower(implode('_',explode(' ',$nouveau_titre))));

	$sql = "SELECT * FROM `ml_tabs` where user='$uid' and nom='$clean';";
	$c = mysql_query($sql) or die("Erreur $sql");
	
	if (mysql_num_rows($c) != 0)
		die('Erreur, menu déjà présent ailleurs! Il faut changer de nom!');


       if (is_perso_tab($tab) || ( is_etab_tab($tab) && ($ML_Adm == "Y") ) ) 
			$sql = "SELECT * from monlcs_db.ml_tabs where nom='$tab'";
	      else
			die("Renommage impossible");
	
	$cx = mysql_query($sql) or die("Err SQL $sql");
	if (mysql_num_rows($cx) == 0 )
		die("Ce sous-menu ne peut être renommé");
       else {
	             $id = mysql_result($cx,0,'id');
			
			$sql2 = "UPDATE monlcs_db.ml_notes SET menu='$clean' where menu='$tab' and setter='$uid'; ";
			$cx2 = mysql_query($sql2) or die("Err SQL $sql2");
	
			
			$sql1 = "UPDATE monlcs_db.ml_tabs SET caption='$nouveau_titre', nom='$clean' where id='$id'; ";
			$cx1 = mysql_query($sql1) or die("Err SQL $sql1");

						

			die("Sous-menu renommé avec succès.");
	     }
	}
	else
		 die("Erreur de paramètres");


?>
