<?php
	header('Content-Type: text/xml; charset=utf-8');
	include('/var/www/lcs/includes/headerauth.inc.php');
	include('/var/www/monlcs/includes/config.inc.php');
	include('/var/www/monlcs/includes/fonctions.inc.php');
	list ($idpers,$uid)= isauth();
 	

	$output = "";
	

	if ($_GET) {
		extract($_GET);
		if (!isset($jeton)) {
			$output .= "<erreur>Absence de jeton</erreur>";
		} else {
			mysql_connect($host,$userDB,$passDB) or die('Connexion mysql impossible!');
			mysql_select_db($DB) or die('Base inconnue!');
		
			
			$output .="<response>";
			$sql ="SELECT * from monlcs_db.ml_acad_propose where jeton='$jeton'";
			$c = mysql_query($sql) or die("ERREUR SQL: $sql");
			if (mysql_num_rows($c) == 0 ) {
				$output.="<erreur>Jeton invalide</erreur>";
			} else {
				//peter le jeton pour le rendre inaccessible apres acquisition?
				//$sql2 ="DELETE from monlcs_db.ml_acad_propose where jeton='$jeton'";
				//$c2 = mysql_query($sql2) or die("ERREUR SQL: $sql2");

				$R = mysql_fetch_object($c);
                                $code = $R->jeton;
				$user = $R->uid;
                                $id_scen  = $R->id_ress;
                                
				
								
				$sq = "select * from monlcs_db.ml_scenarios where id_scen='$id_scen'";
				$cc = mysql_query($sq);
				if (mysql_num_rows($cc) ==0 ) {
					$output.="<erreur>Ressource non localis&eacute;e</erreur>";
				} else {
					$tabRess = array();
					for($x=0;$x<mysql_num_rows($cc);$x++) {
						$infos = mysql_fetch_object($cc);
						if ($x == 0) {
							$output.="<etab>$baseurl</etab>\n";
							$output.="<matiere>$infos->matiere</matiere>\n";
							$clean_t1 = patchToXml($infos->titre);
							$output.="<titre>$clean_t1</titre>\n";
							$clean_d1 = patchToXml($infos->descr);
							$output.="<description>$clean_d1</description>\n";
						}
						
					        if ( !in_array($infos->type.$infos->id_ressource,$tabRess)) {
							$output .="<ressource>\n";
							
							$output.="<type>$infos->type</type>\n";
							if ($infos->type =='ressource' ) {
								$sql2 ="SELECT * from monlcs_db.ml_ressources where id='$infos->id_ressource'";
								$c2 = mysql_query($sql2) or die("ERREUR SQL: $sql2");
								$tabRess[] = $infos->type.$infos->id_ressource;
								$infos2 = mysql_fetch_object($c2);
								$clean_url = patchUrl($infos2->url,$baseurl);
								$output.="<url>".patchToXml($clean_url)."</url>\n";
								$clean_titre = patchToXml($infos2->titre);
								$output.="<titre_ress>$clean_titre</titre_ress>\n";
								$check_RSS = $infos2->RSS_template;
								$output.="<rss>$check_RSS</rss>\n";
								$output.="<vignette>$infos2->url_vignette</vignette>\n";
								$output.="<owner>".$infos2->owner."@".$baseurl."</owner>\n";
								$output.="<statut>$infos2->statut</statut>\n";
								$clean_d2 = patchToXML($infos2->descr);
								$output.="<descr_ress>$clean_d2</descr_ress>\n";
								$output.="<created_at>$infos2->ajoutee_le</created_at>\n";
							}
							
							if ($infos->type =='note' ) {
								$sql2 ="SELECT * from monlcs_db.ml_notes where id='$infos->id_ressource'";
								$c2 = mysql_query($sql2) or die("ERREUR SQL: $sql2");
								$tabRess[] = $infos->type.$infos->id_ressource;
								$infos2 = mysql_fetch_object($c2);
								$clean_msg = patchToXml($infos2->msg);
								$output.="<note_msg>$clean_msg</note_msg>\n";
								$output.="<note_setter>".$infos2->setter."@".$baseurl."</note_setter>\n";
								$clean_titre = patchToXml($infos2->titre);
								$output.="<note_title>$clean_titre</note_title>\n";

							}

							$output.="<x>$infos->x</x>\n";
							$output.="<y>$infos->y</y>\n";
							$output.="<z>$infos->z</z>\n";
							$output.="<w>$infos->w</w>\n";
							$output.="<h>$infos->h</h>\n";

							$output.="<min>$infos->min</min>\n";
						
							$output.="</ressource>\n";
						 }
					   
					}
				}
				
			}
        	}
	    } else {
			$output .= "<erreur>Absence de param&eagrave;tres</erreur>";

	}

	$output .= "\n";
	$output .="</response>";
	//$output = mb_convert_encoding($output,"HTML-ENTITIES","auto");
	$output = utf8_encode($output);
	print($output);

?>
