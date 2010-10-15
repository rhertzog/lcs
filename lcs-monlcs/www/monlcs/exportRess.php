<?php
	header('Content-Type: text/xml; charset=utf-8');
	include('/var/www/monlcs/includes/config.inc.php');
	$output = "";
	$output =  "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
	$output .="<response>";


if ($_GET) {
		extract($_GET);
		if (!isset($jeton)) {
			$output .= "<erreur>Absence de jetons</erreur>";
		} else {
			$sql ="SELECT * from monlcs_db.ml_acad_propose where jeton='$jeton'";
			$c = mysql_query($sql) or die("ERREUR SQL: $sql");
			if (mysql_num_rows($c) == 0 ) {
				$output.="<erreur>Jeton invalide</erreur>";
			} else {
				//peter le jeton pour le rendre inaccessible
				$sql2 ="DELETE from monlcs_db.ml_acad_propose where jeton='$jeton'";
				$c2 = mysql_query($sql2) or die("ERREUR SQL: $sql2");

				$R = mysql_fetch_object($c);
                                $code = $R->jeton;
				$user = $R->uid;
                                $idR  = $R->id_ress;
                                $idT  = $R->menu;
				
				$sq2 = "select * from monlcs_db.ml_tabs where id='$idT'";
				$cc2 = mysql_query($sq2);
				if (mysql_num_rows($cc2) == 0 ) {
					$output.="<erreur>Menu non localis&eacute;</erreur>";
				} else {
					$infos = mysql_fetch_object($cc2);
					$output.="<menu>$infos->caption</menu>";
					}

				
				$sq = "select * from monlcs_db.ml_ressources where id='$idR'";
				$cc = mysql_query($sq);
				if (mysql_num_rows($cc) ==0 ) {
					$output.="<erreur>Ressource non localis&eacute;e</erreur>";
				} else {
					$infos = mysql_fetch_object($cc);
					$output.="<ressource>\n";
						$output.="<url>$infos->url</url>\n";
	
					$output.="</ressource>\n";

				}
				
			}
        	}
	    } else {
			$output .= "<erreur>Absence de param&eagrave;tres</erreur>";

	    }

	$output .= "\n";
	$output .="</response>";

	print($output);

?>
