<?php
	include("includes/secure_no_header.inc.php");

	if ($_POST) {
		extract($_POST);
             if (is_perso_tab($tab) || ( is_etab_tab($tab) && ($ML_Adm == "Y") ) ) 
			$sql = "SELECT * from monlcs_db.ml_zones where nom='$ancien_titre'";
	      else
			die("Renommage impossible");


		$cx = mysql_query($sql) or die("Err SQL $sql");
		if (mysql_num_rows($cx) == 0 )
			die("Cet onglet ne peut �tre renomm�");
                else {
	              $id = mysql_result($cx,0,'id');
			$sql1 = "UPDATE monlcs_db.ml_zones SET nom='$nouveau_titre' where id='$id'; ";
			$cx1 = mysql_query($sql1) or die("Err SQL $sql1");
			die("Onglet renomm� avec succ�s.");
		     }
	}
	else
		 die("Erreur de param�tres");


?>
