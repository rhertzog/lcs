<?
	include "includes/secure_no_header.inc.php";
	if($_POST) {
		extract($_POST);

		#################################################################################################################################
		# Modifi� par MrT le 13 novembre 2008
		# Fonction: d�pot acad�mique pour sc�narios MonLCS
		# Version 2.1
		#################################################################################################################################

		//       cr�er un jeton
	        //       le xml seron g�n�r� par exportScen.php?jeton=XXXXXXXXXXXXXXXXXXXXXXXXXX
       		//       appeller le script distant sur CRDP ?jeton= ?etab=
        	//       prefixe de jeton RP (ressource propos�e) ou SC (sc�nario)
	                	
		$now = date("Y-m-d H:i:s");
       		$jeton = 'SC_'.md5("$baseurl/$uid/scenario/$id_scen/");
       	
		//sauvegarde en sql
	    	       		
		$sql = "DELETE FROM `monlcs_db`.`ml_acad_propose` WHERE `jeton` = '$jeton' ;"; 
		$c = mysql_query($sql) or die("ERREUR: mysql $sql"); 

	        $sql = "INSERT INTO `monlcs_db`.`ml_acad_propose` ("
				."`id` ,"
				."`jeton` ,"
				."`etab` ,"
				."`uid` ,"
				."`id_ress` ,"
				."`type` ,"
				."`menu` ,"
				."`date`"
				.")"
				." VALUES ("
				."NULL , '$jeton', '$baseurl', '$uid', '$id_scen','scen', 'scenario','$now'"
				.");";
	        $c = mysql_query($sql) or die("ERREUR: mysql $sql"); 

       	        $url_distante = $conf_url_distant;
	        $params = "?etab=$baseurl&jeton=$jeton";
       	        die("$url_distante$params");
	} else {
 		die("alert('ERREUR: Pb de param�tres!')");
 	}
?>
