<?php
	/* $Id: fb_appreciation.inc.php 3181 2009-06-03 09:31:02Z crob $ */

	//=====================
	// on va chercher les appr�ciations si besoin
	$tab_eleves_OOo[$nb_eleve][$j][4]="";			// on initialise le champ pour ne pas avoir d'erreur
				
	if($avec_app=="y") {
		// On v�rifie que la mati�re est dispens�e
		if($tabmatieres[$j][-4]=="non dispensee dans l etablissement") {
			$tab_eleves_OOo[$nb_eleve][$j][4]="non dispens�e dans l �tablissement";		
		}else{		
			$sql="SELECT appreciation FROM notanet_app na,
											notanet_corresp nc
									 WHERE na.login='$lig1->login' AND
											nc.notanet_mat='".$tabmatieres[$j][0]."' AND
											nc.matiere=na.matiere;";
			$res_app=mysql_query($sql);
			if(mysql_num_rows($res_app)>0){
				$lig_app=mysql_fetch_object($res_app);
				$tab_eleves_OOo[$nb_eleve][$j][4]=$lig_app->appreciation;
			}
		}
	}
	//=====================
?>