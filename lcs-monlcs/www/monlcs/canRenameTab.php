<?php
	include("includes/secure_no_header.inc.php");

	if ($_POST) {
		extract($_POST);
       	if(  ($ML_Adm == "Y")  &&  is_etab_tab($tab) )
			die("Y");     
	
		$sql = "SELECT * from monlcs_db.ml_tabs where nom='$tab' and user='$uid'; ";
		$cx = mysql_query($sql) or die("Err SQL $sql");
		if (mysql_num_rows($cx) == 0 )
			die("N");
                else {
			die("Y");
		     }
	}
	else
		 die("Erreur de paramètres");


?>
