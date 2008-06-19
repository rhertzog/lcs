#!/usr/bin/php

<?php
	/* $Id$ */
	/*
		Page d'import des comptes depuis les fichiers CSV/XML de Sconet
		Auteur: Stéphane Boireau (Animateur de Secteur pour les TICE sur Bernay/Pont-Audemer (27))
		Dernière modification: 08/05/2007
                Portage LCS : jean-Luc Chrétien jean-luc.chretien@tice;accaen.fr
                Dernière modification : 28/05/2007
	*/

        include "se3orlcs_import_comptes.php";

	// Choix de destination des my_echo():
	$dest_mode="file";
	// On va écrire dans le fichier $echo_file et non dans la page courante... ce qui serait problématique depuis que cette page PHP n'est plus visitée depuis un navigateur.

	// Date et heure...
	$aujourdhui2 = getdate();
	$annee_aujourdhui2 = $aujourdhui2['year'];
	$mois_aujourdhui2 = sprintf("%02d",$aujourdhui2['mon']);
	$jour_aujourdhui2 = sprintf("%02d",$aujourdhui2['mday']);
	$heure_aujourdhui2 = sprintf("%02d",$aujourdhui2['hours']);
	$minute_aujourdhui2 = sprintf("%02d",$aujourdhui2['minutes']);
	$seconde_aujourdhui2 = sprintf("%02d",$aujourdhui2['seconds']);

	$debut_import="$jour_aujourdhui2/$mois_aujourdhui2/$annee_aujourdhui2 à $heure_aujourdhui2:$minute_aujourdhui2:$seconde_aujourdhui2";

/*
	$fich=fopen("/tmp/rapport_test.txt","a+");
	fwrite($fich,"Le $jour_aujourdhui2/$mois_aujourdhui2/$annee_aujourdhui2 à $heure_aujourdhui2:$minute_aujourdhui2:$seconde_aujourdhui2\n");
	fwrite($fich,"\$type_fichier_eleves=$type_fichier_eleves\n");
	fwrite($fich,"\$eleves_file=$eleves_file\n");
	fwrite($fich,"\$sts_xml_file=$sts_xml_file\n");
	fwrite($fich,"\$prefix=$prefix\n");
	fwrite($fich,"\$annuelle=$annuelle\n");
	fwrite($fich,"\$simulation=$simulation\n");
	fwrite($fich,"\$timestamp=$timestamp\n");
	fwrite($fich,"\$randval=$randval\n");
	fwrite($fich,"\$temoin_creation_fichiers=$temoin_creation_fichiers\n");
	fwrite($fich,"===============================\n");
	fclose($fich);
*/
	//exit();


	// Récupération du type des groupes Equipe_* et Matiere_*
	$sql="SELECT value FROM params WHERE name='type_Equipe_Matiere'";
	$res1=mysql_query($sql);
	if(mysql_num_rows($res1)==0){
		$type_Equipe_Matiere="groupOfNames";
	}
	else{
		$lig_type=mysql_fetch_object($res1);
		$type_Equipe_Matiere=$lig_type->value;
		if(($type_Equipe_Matiere!="groupOfNames")&&($type_Equipe_Matiere!="posixGroup")){
			$type_Equipe_Matiere="groupOfNames";
		}
	}



	function traite_espaces($chaine){
		//$chaine="  Bla   ble bli  blo      blu  ";
		$tab=explode(" ",$chaine);

		$retour=$tab[0];
		for($i=1;$i<count($tab);$i++){
			if($tab[$i]!=""){
				$retour.=" ".$tab[$i];
			}
		}

		$retour=trim($retour);

		return $retour;
	}


	$nouveaux_comptes=0;
	$comptes_avec_employeeNumber_mis_a_jour=0;
	$nb_echecs=0;

	$tab_nouveaux_comptes=array();
	$tab_comptes_avec_employeeNumber_mis_a_jour=array();



	//my_echo("\$creer_equipes_vides=$creer_equipes_vides<br />");

	my_echo("<a name='menu'></a>");
	my_echo("<h3>Menu</h3>");
	my_echo("<blockquote>\n");
	my_echo("<p>Aller à la section</p>\n");
	my_echo("<table border='0'>\n");
	my_echo("<tr>\n");
	my_echo("<td>- </td>\n");
	my_echo("<td>création des comptes professeurs: </td><td><span id='id_creer_profs' style='display:none;'><a href='#creer_profs'>Clic</a></span></td>\n");
	my_echo("</tr>\n");
	my_echo("<tr>\n");
	my_echo("<td>- </td>\n");
	my_echo("<td>création des comptes élèves: </td><td><span id='id_creer_eleves' style='display:none;'><a href='#creer_eleves'>Clic</a></span></td>\n");
	my_echo("</tr>\n");
	if($simulation!="y"){
		my_echo("<tr>\n");
		my_echo("<td>- </td>\n");
		my_echo("<td>création des classes et des équipes: </td><td><span id='id_creer_classes' style='display:none;'><a href='#creer_classes'>Clic</a></span></td>\n");
		my_echo("</tr>\n");
		my_echo("<tr>\n");
		my_echo("<td>- </td>\n");
		// ===========================================================
		// AJOUTS: 20070914 boireaus
		if($creer_matieres=='y'){
			my_echo("<td>création des matières: </td><td><span id='id_creer_matieres' style='display:none;'><a href='#creer_matieres'>Clic</a></span></td>\n");
		}
		else{
			my_echo("<td>création des matières: </td><td><span id='id_creer_matieres' style='color:red;'>non demandée</span></td>\n");
		}
		// ===========================================================
		my_echo("</tr>\n");
		my_echo("<tr>\n");
		my_echo("<td>- </td>\n");

		// ===========================================================
		// AJOUTS: 20070914 boireaus
		if($creer_cours=='y'){
			my_echo("<td>création des cours: </td><td><span id='id_creer_cours' style='display:none;'><a href='#creer_cours'>Clic</a></span></td>\n");
		}
		else{
			my_echo("<td>création des cours: </td><td><span id='id_creer_cours' style='color:red;'>non demandée</span></td>\n");
		}
		// ===========================================================
		my_echo("</tr>\n");
	}
	my_echo("<tr>\n");
	my_echo("<td>- </td>\n");
	my_echo("<td>compte rendu final de ");
	if($simulation=="y"){my_echo("simulation");}else{my_echo("création");}
	my_echo(": </td><td><span id='id_fin' style='display:none;'><a href='#fin'>Clic</a></span></td>\n");
	my_echo("</tr>\n");
	my_echo("</table>\n");
	my_echo("</blockquote>\n");

	//exit;

	if($temoin_creation_fichiers=="oui"){
		my_echo("<h3>Fichiers CSV</h3>");
		my_echo("<blockquote>\n");
		my_echo("<p>Récupérer le fichier:</p>\n");
		my_echo("<table border='0'>\n");
		my_echo("<tr>\n");
		my_echo("<td>- </td>\n");
		//my_echo("<td>F_ele.txt: </td><td><span id='id_f_ele_txt' style='display:none;'><a href='$dossiercsv/f_ele.txt' target='_blank'>Clic</a></span></td>\n");
		my_echo("<td>F_ele.txt: </td><td><span id='id_f_ele_txt' style='display:none;'><a href='/$chemin_http_csv/f_ele.txt' target='_blank'>Clic</a></span></td>\n");
		my_echo("</tr>\n");
		my_echo("<tr>\n");
		my_echo("<td>- </td>\n");
		//my_echo("<td>F_div.txt: </td><td><span id='id_f_div_txt' style='display:none;'><a href='$dossiercsv/f_div.txt' target='_blank'>Clic</a></span></td>\n");
		my_echo("<td>F_div.txt: </td><td><span id='id_f_div_txt' style='display:none;'><a href='/$chemin_http_csv/f_div.txt' target='_blank'>Clic</a></span></td>\n");
		my_echo("</tr>\n");
		my_echo("<tr>\n");
		my_echo("<td>- </td>\n");
		//my_echo("<td>F_men.txt: </td><td><span id='id_f_men_txt' style='display:none;'><a href='$dossiercsv/f_men.txt' target='_blank'>Clic</a></span></td>\n");
		my_echo("<td>F_men.txt: </td><td><span id='id_f_men_txt' style='display:none;'><a href='/$chemin_http_csv/f_men.txt' target='_blank'>Clic</a></span></td>\n");
		my_echo("</tr>\n");
		my_echo("<tr>\n");
		my_echo("<td>- </td>\n");
		//my_echo("<td>F_wind.txt: </td><td><span id='id_f_wind_txt' style='display:none;'><a href='$dossiercsv/f_wind.txt' target='_blank'>Clic</a></span></td>\n");
		my_echo("<td>F_wind.txt: </td><td><span id='id_f_wind_txt' style='display:none;'><a href='/$chemin_http_csv/f_wind.txt' target='_blank'>Clic</a></span></td>\n");
		my_echo("</tr>\n");
		my_echo("</table>\n");
		//my_echo("<p>Supprimer les fichiers générés: <span id='id_suppr_txt' style='display:none;'><a href='".$_SERVER['PHP_SELF']."?nettoyage=oui&amp;dossier=".$timestamp."_".$randval."' target='_blank'>Clic</a></span></p>\n");
		my_echo("<p>Supprimer les fichiers générés: <span id='id_suppr_txt' style='display:none;'><a href='$www_import?nettoyage=oui&amp;dossier=".$timestamp."_".$randval."' target='_blank'>Clic</a></span></p>\n");
		my_echo("</blockquote>\n");
	}


	// Vérification de l'existence de la branche Trash:
	test_creation_trash();



	// Suppression des anciens groupes si l'importation est annuelle:
	//if(isset($_POST['annuelle'])){
	if($annuelle=="y"){
		if($simulation!="y"){
			// A FAIRE...
			//if(del_entry ($entree, $branche)){}else{}
			my_echo("<h3>Importation annuelle");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h3>\n");
			my_echo("<blockquote>\n");

			if(file_exists($sts_xml_file)){
				unset($attribut);
				$attribut=array("memberuid");
				$tab=get_tab_attribut("groups","cn=Profs",$attribut);
				if(count($tab)>0){
					my_echo("<p>On vide le groupe Profs.<br />\n");

					my_echo("Suppression de l'appartenance au groupe de: \n");
					for($i=0;$i<count($tab);$i++){
						if($i==0){
							$sep="";
						}
						else{
							$sep=", ";
						}
						my_echo($sep);

						unset($attr);
						$attr=array();
						$attr["memberuid"]=$tab[$i];
						if(modify_attribut("cn=Profs","groups",$attr, "del")){
							my_echo($tab[$i]);
						}
						else{
							my_echo("<font color='red'>".$tab[$i]."</font>");
						}
					}
					my_echo("</p>\n");
				}
				else{
					my_echo("<p>Le groupe Profs est déjà vide.</p>\n");
				}
				if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
			}

			if(file_exists($eleves_file)){
				unset($attribut);
				$attribut=array("memberuid");
				$tab=get_tab_attribut("groups","cn=Eleves",$attribut);
				if(count($tab)>0){
					my_echo("<p>On vide le groupe Eleves.<br />\n");

					my_echo("Suppression de l'appartenance au groupe de: \n");
					for($i=0;$i<count($tab);$i++){
						if($i==0){
							$sep="";
						}
						else{
							$sep=", ";
						}
						my_echo($sep);

						unset($attr);
						$attr=array();
						$attr["memberuid"]=$tab[$i];
						if(modify_attribut("cn=Eleves","groups",$attr, "del")){
							my_echo($tab[$i]);
						}
						else{
							my_echo("<font color='red'>".$tab[$i]."</font>");
						}
					}
					my_echo("</p>\n");
				}
				else{
					my_echo("<p>Le groupe Eleves est déjà vide.</p>\n");
				}
				if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
			}

			my_echo("<p>Suppression des groupes Classes, Equipes, Cours et Matieres.</p>\n");

			// Recherche des classes,...
			unset($attribut);
			$attribut=array("cn");
			$tab=get_tab_attribut("groups","(|(cn=Classe_*)(cn=Equipe_*)(cn=Cours_*)(cn=Matiere_*))",$attribut);
			if(count($tab)>0){
				my_echo("<table border='0'>\n");
				for($i=0;$i<count($tab);$i++){
					my_echo("<tr>");
					my_echo("<td>");
					my_echo("Suppression de $tab[$i]: ");
					my_echo("</td>");
					my_echo("<td>");
					if(del_entry("cn=$tab[$i]", "groups")){
						my_echo("<font color='green'>SUCCES</font>");
					}
					else{
						my_echo("<font color='red'>ECHEC</font>");
					}
					//my_echo("<br />\n");
					my_echo("</td>");
					my_echo("</tr>");
				}
				//my_echo("</p>\n");
				my_echo("</table>\n");
			}
			if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
			my_echo("</blockquote>\n");
			//exit();
		}
		else{
			my_echo("<h3>Importation annuelle");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h3>\n");
			my_echo("<blockquote>\n");
			my_echo("<p><b>Simulation</b> de la suppression des groupes Classes, Equipes, Cours et Matieres.</p>\n");
			my_echo("<p>Les groupes suivants seraient supprimés: ");
			// Recherche des classes,...
			unset($attribut);
			$attribut=array("cn");
			$tab=get_tab_attribut("groups","(|(cn=Classe_*)(cn=Equipe_*)(cn=Cours_*)(cn=Matiere_*))",$attribut);
			if(count($tab)>0){
				my_echo("$tab[0]");
				for($i=1;$i<count($tab);$i++){
					my_echo(", $tab[$i]");
				}
			}
			else{
					my_echo("AUCUN GROUPE TROUVÉ");
			}
			my_echo("</p>");
			if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		}
	}

	//exit;

	// Partie ELEVES:
	//$type_fichier_eleves=isset($_POST['type_fichier_eleves']) ? $_POST['type_fichier_eleves'] : "csv";
	if($type_fichier_eleves=="csv"){
		//$eleves_csv_file = isset($_FILES["eleves_csv_file"]) ? $_FILES["eleves_csv_file"] : NULL;

		//$eleves_csv_file = isset($_FILES["eleves_file"]) ? $_FILES["eleves_file"] : NULL;
		//$fp=fopen($eleves_csv_file['tmp_name'],"r");

		$fp=fopen($eleves_file,"r");
		if($fp){
			//my_echo("<h2>Section élèves</h2>\n");
			//my_echo("<h3>Section élèves</h3>\n");
			my_echo("<h3>Section élèves");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h3>\n");
			my_echo("<blockquote>\n");
			//my_echo("<h3>Lecture du fichier...</h3>\n");
			my_echo("<h4>Lecture du fichier élèves...</h4>\n");
			my_echo("<blockquote>\n");
			unset($ligne);
			$ligne=array();
			while(!feof($fp)){
				//$ligne[]=fgets($fp,4096);
				// Suppression des guillemets s'il jamais il y en a dans le CSV
				$ligne[]=ereg_replace('"','',fgets($fp,4096));
			}
			fclose($fp);

			my_echo("<p>Terminé.</p>\n");
			if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
			my_echo("</blockquote>\n");




			// Contrôle du contenu du fichier:
			if(stristr($ligne[0],"<?xml ")){
				my_echo("<p style='color:red;'>ERREUR: Le fichier élèves fourni a l'air d'être de type XML et non CSV.</p>\n");
				my_echo("<script type='text/javascript'>
	compte_a_rebours='n';
</script>\n");
				my_echo("</body>\n</html>\n");
				my_echo("<div style='position:absolute; top: 50px; left: 300px; width: 400px; border: 1px solid black; background-color: red;'><div align='center'>ERREUR: Le fichier élèves fourni a l'air d'être de type XML et non CSV.</div></div>");

				// Renseignement du témoin de mise à jour terminée.
				$sql="SELECT value FROM params WHERE name='imprt_cmpts_en_cours'";
				$res1=mysql_query($sql);
				if(mysql_num_rows($res1)==0){
					$sql="INSERT INTO params SET name='imprt_cmpts_en_cours',value='n'";
					$res0=mysql_query($sql);
				}
				else{
					$sql="UPDATE params SET value='n' WHERE name='imprt_cmpts_en_cours'";
					$res0=mysql_query($sql);
				}

				exit();
			}



			//my_echo("<h3>Affichage...</h3>\n");
			//my_echo("<h4>Affichage...</h4>\n");
			my_echo("<h4>Affichage...");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h4>\n");
			my_echo("<blockquote>\n");
			my_echo("<p>Les lignes qui suivent sont le contenu du fichier fourni.<br />Ces lignes ne sont là qu'à des fins de débuggage.<p>\n");
			my_echo("<table border='0'>\n");
			$cpt=0;
			while($cpt<count($ligne)){
				my_echo("<tr valign='top'>\n");
				my_echo("<td style='color: blue;'>$cpt</td><td>".htmlentities($ligne[$cpt])."</td>\n");
				my_echo("</tr>\n");
				$cpt++;
			}
			my_echo("</table>\n");
			my_echo("<p>Terminé.</p>\n");
			if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
			my_echo("</blockquote>\n");
			my_echo("</blockquote>\n");

			my_echo("<a name='analyse'></a>\n");
			//my_echo("<h2>Analyse</h2>\n");
			//my_echo("<h3>Analyse</h3>\n");
			my_echo("<h3>Analyse");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h3>\n");
			my_echo("<blockquote>\n");
			//my_echo("<h3>Repérage des champs</h3>\n");
			//my_echo("<h4>Repérage des champs</h4>\n");
			my_echo("<h4>Repérage des champs");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h4>\n");
			my_echo("<blockquote>\n");

			$champ=array("Nom",
			"Prénom 1",
			"Date de naissance",
			"N° Interne",
			"Sexe",
			"Division");
			// Analyse:
			// Repérage des champs souhaités:
			//$tabtmp=explode(";",$ligne[0]);
			$tabtmp=explode(";",trim($ligne[0]));
			for($j=0;$j<count($champ);$j++){
				$index[$j]="-1";
				for($i=0;$i<count($tabtmp);$i++){
					if($tabtmp[$i]==$champ[$j]){
						my_echo("Champ '<font color='blue'>$champ[$j]</font>' repéré en colonne/position <font color='blue'>$i</font><br />\n");
						$index[$j]=$i;
					}
				}
				if($index[$j]=="-1"){
					my_echo("<p><font color='red'>ERREUR: Le champ '<font color='blue'>$champ[$j]</font>' n'a pas été trouvé.</font></p>\n");
					my_echo("</blockquote>");
					//my_echo("<p><a href='".$_SERVER['PHP_SELF']."'>Retour</a>.</p>\n");
					my_echo("<p><a href='$www_import'>Retour</a>.</p>\n");
					my_echo("<script type='text/javascript'>
	compte_a_rebours='n';
</script>\n");
					my_echo("</blockquote></div></body></html>");
					exit();
				}
			}
			my_echo("<p>Terminé.</p>\n");
			if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
			my_echo("</blockquote>\n");

			//my_echo("<h3>Remplissage des tableaux pour SambaEdu3</h3>\n");
			my_echo("<h3>Remplissage des tableaux");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h3>\n");
			my_echo("<blockquote>\n");
			$cpt=1;
			$tabnumero=array();
			$eleve=array();
			$temoin_format_num_interne="";
			while($cpt<count($ligne)){
				if($ligne[$cpt]!=""){
					//$tabtmp=explode(";",$ligne[$cpt]);
					$tabtmp=explode(";",trim($ligne[$cpt]));

					// Si la division/classe n'est pas vide
					if(isset($tabtmp[$index[5]])){
						if($tabtmp[$index[5]]!=""){
							if(strlen($tabtmp[$index[3]])==11){
								$numero=substr($tabtmp[$index[3]],0,strlen($tabtmp[$index[3]])-6);
							}
							else{
								$temoin_format_num_interne="non_standard";
								if(strlen($tabtmp[$index[3]])==4){
									$numero="0".$tabtmp[$index[3]];
								}
								else{
									$numero=$tabtmp[$index[3]];
								}
							}

							$temoin=0;
							for($i=0;$i<count($tabnumero);$i++){
								if($tabnumero[$i]==$numero){
									$temoin=1;
								}
							}
							if($temoin==0){
								$tabnumero[]=$numero;
								$eleve[$numero]=array();
								$eleve[$numero]["numero"]=$numero;


								//$eleve[$numero]["nom"]=ereg_replace("[^[:space:][:alpha:]]", "", $tabtmp[$index[0]]);
								$eleve[$numero]["nom"]=ereg_replace("[^a-zA-ZÀÄÂÉÈÊËÎÏÔÖÙÛÜ½¼Ççàäâéèêëîïôöùûü_ -]", "", $tabtmp[$index[0]]);
								$eleve[$numero]["prenom"]=ereg_replace("[^a-zA-ZÀÄÂÉÈÊËÎÏÔÖÙÛÜ½¼Ççàäâéèêëîïôöùûü_ -]", "", $tabtmp[$index[1]]);

								// =============================================
								// On ne retient que le premier prénom: 20071101
								$tab_tmp_prenom=explode(" ",$eleve[$numero]["prenom"]);
								$eleve[$numero]["prenom"]=$tab_tmp_prenom[0];
								// =============================================

								//$nom=strtolower(strtr(ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe","$nom"))))," 'ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz"));
								//$prenom=strtolower(strtr(ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe","$prenom"))))," 'ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz"));

								unset($tmpdate);
								$tmpdate=explode("/",$tabtmp[$index[2]]);
								$eleve[$numero]["date"]=$tmpdate[2].$tmpdate[1].$tmpdate[0];
								$eleve[$numero]["sexe"]=$tabtmp[$index[4]];
								//$eleve[$numero]["division"]=ereg_replace("[^[:space:][A-Z][a-z][0-9]]", "",$tabtmp[$index[5]]);
								$eleve[$numero]["division"]=ereg_replace("[^a-zA-Z0-9_ -]", "",remplace_accents($tabtmp[$index[5]]));
							}
						}
					}
				}
				$cpt++;
			}
			my_echo("<p>Terminé.</p>\n");
			if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
			my_echo("</blockquote>\n");
			// A CE STADE, LE TABLEAU $eleves N'EST REMPLI QUE POUR DES DIVISIONS NON VIDES (seuls les élèves affecté dans des classes sont retenus).


			my_echo("<a name='csv_eleves'></a>\n");
			//my_echo("<h3>Affichage d'un CSV des élèves pour SambaEdu3</h3>\n");
			//my_echo("<h4>Affichage d'un CSV des élèves pour SambaEdu3</h4>\n");
			my_echo("<h4>Affichage d'un CSV des élèves");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h4>\n");
			my_echo("<blockquote>\n");
			if($temoin_format_num_interne!=""){
				my_echo("<p style='color:red;'>ATTENTION: Le format des numéros internes des élèves n'a pas l'air standard.<br />Un préfixe 0 a dû être ajouté pour corriger.<br />Veillez à contrôler que vos numéros internes ont bien été analysés malgré tout.</p>\n");
			}
			//my_echo("");
			//if($temoin_creation_fichiers!="non"){$fich=fopen("$dossiercsv/se3/f_ele.txt","w+");}
			if($temoin_creation_fichiers!="non"){$fich=fopen("$dossiercsv/f_ele.txt","w+");}else{$fich=FALSE;}
			$tab_classe=array();
			$cpt_classe=-1;
			for($k=0;$k<count($tabnumero);$k++){
				$temoin_erreur_eleve="n";

				$numero=$tabnumero[$k];
				$chaine="";
				$chaine.=$eleve[$numero]["numero"];
				$chaine.="|";
				$chaine.=remplace_accents($eleve[$numero]["nom"]);
				$chaine.="|";
				$chaine.=remplace_accents($eleve[$numero]["prenom"]);
				$chaine.="|";
				$chaine.=$eleve[$numero]["date"];
				$chaine.="|";
				$chaine.=$eleve[$numero]["sexe"];
				$chaine.="|";
				$chaine.=$eleve[$numero]["division"];
				if($fich){
					//fwrite($fich,$chaine."\n");
					fwrite($fich,html_entity_decode($chaine)."\n");
				}
				my_echo($chaine."<br />\n");
			}
			if($fich){
				fclose($fich);
			}

			//my_echo("disk_total_space($dossiercsv)=".disk_total_space($dossiercsv)."<br />");
			if($temoin_creation_fichiers!="non"){
				my_echo("<script type='text/javascript'>
	document.getElementById('id_f_ele_txt').style.display='';
</script>");
			}

			my_echo("</blockquote>\n");
			if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
			my_echo("</blockquote>\n");
		}
		else{
			//my_echo("<p>ERREUR lors de l'ouverture du fichier ".$eleves_csv_file['name']." (<i>".$eleves_csv_file['tmp_name']."</i>).</p>\n");
			my_echo("<p>ERREUR lors de l'ouverture du fichier '$eleves_file'.</p>\n");
		}
	}
	else{
		// C'est un fichier Eleves...XML
		//$eleves_xml_file = isset($_FILES["eleves_file"]) ? $_FILES["eleves_file"] : NULL;
		//$fp=fopen($eleves_xml_file['tmp_name'],"r");

		$fp=fopen($eleves_file,"r");
		if($fp){

			function extr_valeur($lig){
				unset($tabtmp);
				$tabtmp=explode(">",ereg_replace("<",">",$lig));
				return trim($tabtmp[2]);
			}

			//my_echo("<h2>Fichier élèves</h2>\n");
			//my_echo("<h3>Section élèves</h3>\n");
			my_echo("<h3>Section élèves");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h3>\n");
			my_echo("<blockquote>\n");
			//my_echo("<h3>Lecture du fichier Elèves...</h3>\n");
			//my_echo("<h4>Lecture du fichier Elèves...</h4>\n");
			my_echo("<h4>Lecture du fichier Elèves...");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h4>\n");
			my_echo("<blockquote>\n");
			while(!feof($fp)){
				$ligne[]=fgets($fp,4096);
			}
			fclose($fp);
			my_echo("<p>Terminé.</p>\n");
			if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
			my_echo("</blockquote>\n");



			// Contrôle du contenu du fichier:
			if(!stristr($ligne[0],"<?xml ")){
				my_echo("<p style='color:red;'>ERREUR: Le fichier élèves fourni n'a pas l'air d'être de type XML.<br />La première ligne devrait débuter par '&lt;?xml '.</p>\n");
				my_echo("<script type='text/javascript'>
	compte_a_rebours='n';
</script>\n");
				my_echo("</body>\n</html>\n");
				my_echo("<div style='position:absolute; top: 50px; left: 300px; width: 400px; border: 1px solid black; background-color: red;'><div align='center'>ERREUR: Le fichier élèves fourni n'a pas l'air d'être de type XML.<br />La première ligne devrait débuter par '&lt;?xml '.</div></div>");

				// Renseignement du témoin de mise à jour terminée.
				$sql="SELECT value FROM params WHERE name='imprt_cmpts_en_cours'";
				$res1=mysql_query($sql);
				if(mysql_num_rows($res1)==0){
					$sql="INSERT INTO params SET name='imprt_cmpts_en_cours',value='n'";
					$res0=mysql_query($sql);
				}
				else{
					$sql="UPDATE params SET value='n' WHERE name='imprt_cmpts_en_cours'";
					$res0=mysql_query($sql);
				}

				exit();
			}


			if(!stristr($ligne[1],"<BEE_ELEVES ")){
				my_echo("<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML Elèves.<br />La deuxième ligne devrait contenir '&lt;BEE_ELEVES '.</p>\n");
				my_echo("<script type='text/javascript'>
	compte_a_rebours='n';
</script>\n");
				my_echo("</body>\n</html>\n");
				my_echo("<div style='position:absolute; top: 50px; left: 300px; width: 400px; border: 1px solid black; background-color: red;'><div align='center'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML Elèves.<br />La deuxième ligne devrait contenir '&lt;BEE_ELEVES '.</div></div>");

				// Renseignement du témoin de mise à jour terminée.
				$sql="SELECT value FROM params WHERE name='imprt_cmpts_en_cours'";
				$res1=mysql_query($sql);
				if(mysql_num_rows($res1)==0){
					$sql="INSERT INTO params SET name='imprt_cmpts_en_cours',value='n'";
					$res0=mysql_query($sql);
				}
				else{
					$sql="UPDATE params SET value='n' WHERE name='imprt_cmpts_en_cours'";
					$res0=mysql_query($sql);
				}

				exit();
			}


			//my_echo("<h3>Affichage du XML</h3>\n");
			//my_echo("<blockquote>\n");
			//my_echo("<table border='0'>\n");
			//$cpt=0;
			//while($cpt<count($ligne)){
			//	my_echo("<tr>\n");
			//	my_echo("<td style='color: blue;'>$cpt</td><td>".htmlentities($ligne[$cpt])."</td>\n");
			//	my_echo("</tr>\n");
			//	$cpt++;
			//}
			//my_echo("</table>\n");
			//my_echo("<p>Terminé.</p>\n");
			//my_echo("</blockquote>\n");
			//my_echo("</blockquote>\n");



			//my_echo("<h2>Elèves</h2>\n");
			//my_echo("<h3>Elèves</h3>\n");
			//my_echo("<blockquote>\n");
			//my_echo("<h3>Analyse du fichier pour extraire les informations élèves...</h3>\n");
			//my_echo("<h4>Analyse du fichier pour extraire les informations élèves...</h4>\n");
			my_echo("<h4>Analyse du fichier pour extraire les informations élèves...");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h4>\n");
			my_echo("<blockquote>\n");

			$cpt=0;
			$eleves=array();
			$temoin_eleves=0;
			$temoin_ele=0;
			$temoin_options=0;
			//$temoin_scol=-1;
			$temoin_scol=0;
			//Compteur élève:
			$i=-1;

			$tab_champs_eleve=array("ID_NATIONAL",
			"ELENOET",
			"NOM",
			"PRENOM",
			"DATE_NAISS",
			"DOUBLEMENT",
			"DATE_SORTIE",
			"CODE_REGIME",
			"DATE_ENTREE",
			"CODE_MOTIF_SORTIE",
			"CODE_SEXE",
			);


		//	// Inutile
		//	$tab_champs_scol_an_dernier=array("CODE_STRUCTURE",
		//	"CODE_RNE",
		//	"SIGLE",
		//	"DENOM_PRINC",
		//	"DENOM_COMPL",
		//	"LIGNE1_ADRESSE",
		//	"LIGNE2_ADRESSE",
		//	"LIGNE3_ADRESSE",
		//	"LIGNE4_ADRESSE",
		//	"BOITE_POSTALE",
		//	"MEL",
		//	"TELEPHONE",
		//	"LL_COMMUNE_INSEE"
		//	);


			// PARTIE <ELEVES>
			while($cpt<count($ligne)){
				//my_echo(htmlentities($ligne[$cpt])."<br />\n");

				if(strstr($ligne[$cpt],"<ELEVES>")){
					my_echo("Début de la section ELEVES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
					$temoin_eleves++;
				}
				if(strstr($ligne[$cpt],"</ELEVES>")){
					my_echo("Fin de la section ELEVES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
					$temoin_eleves++;
					break;
				}
				if($temoin_eleves==1){
					if(strstr($ligne[$cpt],"<ELEVE ")){
						$i++;
						$eleves[$i]=array();

						//my_echo("<p><b>".htmlentities($ligne[$cpt])."</b><br />\n");
						unset($tabtmp);
						$tabtmp=explode('"',strstr($ligne[$cpt]," ELEVE_ID="));
						$eleves[$i]["eleve_id"]=trim($tabtmp[1]);
						//my_echo("\$eleves[$i][\"eleve_id\"]=".$eleves[$i]["eleve_id"]."<br />\n");

						unset($tabtmp);
						$tabtmp=explode('"',strstr($ligne[$cpt]," ELENOET="));
						$eleves[$i]["elenoet"]=trim($tabtmp[1]);
						//my_echo("\$eleves[$i][\"elenoet\"]=".$eleves[$i]["elenoet"]."<br />\n");
						$temoin_ele=1;
					}
					if(strstr($ligne[$cpt],"</ELEVE>")){
						$temoin_ele=0;
					}
					if($temoin_ele==1){
						if(strstr($ligne[$cpt],"<SCOLARITE_AN_DERNIER>")){
							$temoin_scol=1;
						}
						if(strstr($ligne[$cpt],"</SCOLARITE_AN_DERNIER>")){
							$temoin_scol=0;
						}

						if($temoin_scol==0){
							for($loop=0;$loop<count($tab_champs_eleve);$loop++){
								if(strstr($ligne[$cpt],"<".$tab_champs_eleve[$loop].">")){
									$tmpmin=strtolower($tab_champs_eleve[$loop]);
									$eleves[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);
									my_echo("\$eleves[$i][\"$tmpmin\"]=".$eleves[$i]["$tmpmin"]."<br />\n");
									break;
								}
							}
						}
					//	// Inutile
					//	else{
					//		$eleves[$i]["scolarite_an_dernier"]=array();
					//		for($loop=0;$loop<count($tab_champs_scol_an_dernier);$loop++){
					//			if(strstr($ligne[$cpt],"<".$tab_champs_scol_an_dernier[$loop].">")){
					//				$tmpmin=strtolower($tab_champs_scol_an_dernier[$loop]);
					//				$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=extr_valeur($ligne[$cpt]);
					//				//my_echo("\$eleves[$i]["scolarite_an_dernier"][\"$tmpmin\"]=".$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]."<br />\n");
					//				break;
					//			}
					//		}
					//	}

					//	if(strstr($ligne[$cpt],"<ID_NATIONAL>")){
					//		$eleves[$i]["id_national"]=extr_valeur($ligne[$cpt]);
					//	}
					//	if(strstr($ligne[$cpt],"<ELENOET>")){
					//		$eleves[$i]["elenoet"]=extr_valeur($ligne[$cpt]);
					//	}
					}
				}
				$cpt++;
			}


			// PARTIE <OPTIONS>
			while($cpt<count($ligne)){
				if(strstr($ligne[$cpt],"<OPTIONS>")){
					my_echo("Début de la section OPTIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n");
					$temoin_options++;
				}
				if(strstr($ligne[$cpt],"</OPTIONS>")){
					my_echo("Fin de la section OPTIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n");
					$temoin_options++;
					break;
				}
				if($temoin_options==1){
					if(strstr($ligne[$cpt],"<OPTION ")){

						//my_echo("<p><b>".htmlentities($ligne[$cpt])."</b><br />\n");
						unset($tabtmp);
						$tabtmp=explode('"',strstr($ligne[$cpt]," ELEVE_ID="));
						$tmp_eleve_id=trim($tabtmp[1]);

						// Recherche du $i de $eleves[$i] correspondant:
						$temoin_ident="non";
						for($i=0;$i<count($eleves);$i++){
							if($eleves[$i]["eleve_id"]==$tmp_eleve_id){
								$temoin_ident="oui";
								break;
							}
						}
						if($temoin_ident!="oui"){
							unset($tabtmp);
							$tabtmp=explode('"',strstr($ligne[$cpt]," ELENOET="));
							$tmp_elenoet=trim($tabtmp[1]);

							for($i=0;$i<count($eleves);$i++){
								if($eleves[$i]["elenoet"]==$tmp_elenoet){
									$temoin_ident="oui";
									break;
								}
							}
						}
						if($temoin_ident=="oui"){
							$eleves[$i]["options"]=array();
							$j=0;
							$temoin_opt=1;
						}
					}
					if(strstr($ligne[$cpt],"</OPTION>")){
						$temoin_opt=0;
					}
					if($temoin_opt==1){
					//if(($temoin_opt==1)&&($temoin_ident=="oui")){
						if(strstr($ligne[$cpt],"<OPTIONS_ELEVE>")){
							$eleves[$i]["options"][$j]=array();
							$temoin_opt_ele=1;
						}
						if(strstr($ligne[$cpt],"</OPTIONS_ELEVE>")){
							$j++;
							$temoin_opt_ele=0;
						}

						$tab_champs_opt=array("NUM_OPTION","CODE_MODALITE_ELECT","CODE_MATIERE");
						if($temoin_opt_ele==1){
							for($loop=0;$loop<count($tab_champs_opt);$loop++){
								if(strstr($ligne[$cpt],"<".$tab_champs_opt[$loop].">")){
									$tmpmin=strtolower($tab_champs_opt[$loop]);
									$eleves[$i]["options"][$j]["$tmpmin"]=extr_valeur($ligne[$cpt]);
									//my_echo("\$eleves[$i][\"$tmpmin\"]=".$eleves[$i]["$tmpmin"]."<br />\n");
									break;
								}
							}
						}
					}
				}
				$cpt++;
			}


			// PARTIE <STRUCTURES>
			$temoin_structures=0;
			$temoin_struct_ele=-1;
			$temoin_struct=-1;
			while($cpt<count($ligne)){
				if(strstr($ligne[$cpt],"<STRUCTURES>")){
					my_echo("Début de la section STRUCTURES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
					$temoin_structures++;
				}
				if(strstr($ligne[$cpt],"</STRUCTURES>")){
					my_echo("Fin de la section STRUCTURES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
					$temoin_structures++;
					break;
				}
				if($temoin_structures==1){
					if(strstr($ligne[$cpt],"<STRUCTURES_ELEVE ")){

						//my_echo("<p><b>".htmlentities($ligne[$cpt])."</b><br />\n");
						unset($tabtmp);
						$tabtmp=explode('"',strstr($ligne[$cpt]," ELEVE_ID="));
						$tmp_eleve_id=trim($tabtmp[1]);

						// Recherche du $i de $eleves[$i] correspondant:
						$temoin_ident="non";
						for($i=0;$i<count($eleves);$i++){
							if($eleves[$i]["eleve_id"]==$tmp_eleve_id){
								$temoin_ident="oui";
								break;
							}
						}
						if($temoin_ident!="oui"){
							unset($tabtmp);
							$tabtmp=explode('"',strstr($ligne[$cpt]," ELENOET="));
							$tmp_elenoet=trim($tabtmp[1]);

							for($i=0;$i<count($eleves);$i++){
								if($eleves[$i]["elenoet"]==$tmp_elenoet){
									$temoin_ident="oui";
									break;
								}
							}
						}
						if($temoin_ident=="oui"){
							$eleves[$i]["structures"]=array();
							$j=0;
							$temoin_struct_ele=1;
						}
					}
					if(strstr($ligne[$cpt],"</STRUCTURES_ELEVE>")){
						$temoin_struct_ele=0;
					}
					if($temoin_struct_ele==1){
						if(strstr($ligne[$cpt],"<STRUCTURE>")){
							$eleves[$i]["structures"][$j]=array();
							$temoin_struct=1;
						}
						if(strstr($ligne[$cpt],"</STRUCTURE>")){
							$j++;
							$temoin_struct=0;
						}

						// TYPE_STRUCTURE vaut D pour la classe et G pour un groupe
						$tab_champs_struct=array("CODE_STRUCTURE","TYPE_STRUCTURE");
						if($temoin_struct==1){
							for($loop=0;$loop<count($tab_champs_struct);$loop++){
								if(strstr($ligne[$cpt],"<".$tab_champs_struct[$loop].">")){
									$tmpmin=strtolower($tab_champs_struct[$loop]);
									$eleves[$i]["structures"][$j]["$tmpmin"]=extr_valeur($ligne[$cpt]);
									//my_echo("\$eleves[$i]["structures"][$j][\"$tmpmin\"]=".$eleves[$i]["structures"][$j]["$tmpmin"]."<br />\n");
									break;
								}
							}
						}
					}
				}
				$cpt++;
			}


			// Générer un tableau des membres des groupes:
			// $structure[$i]["nom"]		->	5LATIN-, 3 A2DEC3,...
			// $structure[$i]["eleve"][]	->	ELENOET


			my_echo("<p>Terminé.</p>\n");
			if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
			my_echo("</blockquote>\n");

			//my_echo("<h3>Affichage (d'une partie) des données ELEVES extraites:</h3>\n");
			//my_echo("<h4>Affichage (d'une partie) des données ELEVES extraites:</h4>\n");
			my_echo("<h4>Affichage (d'une partie) des données ELEVES extraites:");
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h4>\n");
			my_echo("<blockquote>\n");
			my_echo(count($eleves)." élèves dans le fichier.");
			my_echo("<table border='1'>\n");
			my_echo("<tr>\n");
			//my_echo("<th style='color: blue;'>&nbsp;</th>\n");
			my_echo("<th style='color: blue;'>&nbsp;</th>\n");
			my_echo("<th>Elenoet</th>\n");
			my_echo("<th>Nom</th>\n");
			my_echo("<th>Prénom</th>\n");
			my_echo("<th>Sexe</th>\n");
			my_echo("<th>Date de naissance</th>\n");
			my_echo("<th>Division</th>\n");
			my_echo("</tr>\n");
			$i=0;
			while($i<count($eleves)){
				my_echo("<tr>\n");
				//my_echo("<td style='color: blue;'>$cpt</td>\n");
				//my_echo("<td style='color: blue;'>&nbsp;</td>\n");
				my_echo("<td style='color: blue;'>$i</td>\n");
				my_echo("<td>".$eleves[$i]["elenoet"]."</td>\n");
				my_echo("<td>".$eleves[$i]["nom"]."</td>\n");

				// =============================================
				// On ne retient que le premier prénom: 20071101
				$tab_tmp_prenom=explode(" ",$eleves[$i]["prenom"]);
				$eleves[$i]["prenom"]=$tab_tmp_prenom[0];
				// =============================================

				my_echo("<td>".$eleves[$i]["prenom"]."</td>\n");
				my_echo("<td>".$eleves[$i]["code_sexe"]."</td>\n");
				my_echo("<td>".$eleves[$i]["date_naiss"]."</td>\n");
				/*
				if(isset($eleves[$i]["structures"])){
					my_echo("<td>".$eleves[$i]["structures"][0]["code_structure"]."</td>\n");
				}
				else{
					my_echo("<td>&nbsp;</td>\n");
				}
				*/
				$temoin_div_trouvee="";
				if(isset($eleves[$i]["structures"])){
					if(count($eleves[$i]["structures"])>0){
						for($j=0;$j<count($eleves[$i]["structures"]);$j++){
							if($eleves[$i]["structures"][$j]["type_structure"]=="D"){
								$temoin_div_trouvee="oui";
								break;
							}
						}
						if($temoin_div_trouvee==""){
							echo "&nbsp;";
						}
						else{
							my_echo("<td>".$eleves[$i]["structures"][$j]["code_structure"]."</td>");
							$eleves[$i]["classe"]=$eleves[$i]["structures"][$j]["code_structure"];
						}
					}
					else{
						my_echo("<td>&nbsp;</td>\n");
					}
				}
				else{
					my_echo("<td>&nbsp;</td>\n");
				}


				my_echo("</tr>\n");
				//flush();
				$i++;
			}
			my_echo("</table>\n");
			//my_echo("___ ... ___");
			my_echo("</blockquote>\n");
			if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
			my_echo("</blockquote>\n");



			// Avec le fichier XML, on a rempli un tableau $eleves (au pluriel)
			// Remplissage du tableau $eleve (au singulier) calqué sur celui du fichier CSV.
			if($temoin_creation_fichiers!="non"){$fich=fopen("$dossiercsv/f_ele.txt","w+");}else{$fich=FALSE;}
			$eleve=array();
			$tabnumero=array();
			$tab_division=array();
			$i=0;
			while($i<count($eleves)){
				//if(isset($eleves[$i]["structures"][0]["code_structure"])){
				//if(isset($eleves[$i]["structures"])){
				if(isset($eleves[$i]["classe"])){
					//$numero=$eleves[$i]["elenoet"];
					$numero=sprintf("%05d",$eleves[$i]["elenoet"]);
					$tabnumero[]="$numero";
					$eleve[$numero]=array();
					$eleve[$numero]["numero"]="$numero";
					$eleve[$numero]["nom"]=$eleves[$i]["nom"];
					//my_echo("\$eleve[$numero][\"nom\"]=".$eleves[$i]["nom"]."<br />\n");
					//my_echo("<p>\$eleve[$numero][\"nom\"]=".$eleve[$numero]["nom"]." ");
					$eleve[$numero]["prenom"]=$eleves[$i]["prenom"];
					//my_echo("\$eleve[$numero][\"prenom\"]=".$eleve[$numero]["prenom"]." ");
					$tmpdate=explode("/",$eleves[$i]["date_naiss"]);
					$eleve[$numero]["date"]=$tmpdate[2].$tmpdate[1].$tmpdate[0];
					if($eleves[$i]["code_sexe"]==1){$eleve[$numero]["sexe"]="M";}else{$eleve[$numero]["sexe"]="F";}

					//$eleve[$numero]["division"]=$eleves[$i]["structures"][0]["code_structure"];
					$eleve[$numero]["division"]=$eleves[$i]["classe"];

					//my_echo(" en ".$eleve[$numero]["division"]."<br />");
					//my_echo("\$eleve[$numero][\"division\"]=".$eleve[$numero]["division"]."<br />");

					$chaine="";
					$chaine.=$eleve[$numero]["numero"];
					$chaine.="|";
					$chaine.=remplace_accents($eleve[$numero]["nom"]);
					$chaine.="|";
					$chaine.=remplace_accents($eleve[$numero]["prenom"]);
					$chaine.="|";
					$chaine.=$eleve[$numero]["date"];
					$chaine.="|";
					$chaine.=$eleve[$numero]["sexe"];
					$chaine.="|";
					$chaine.=$eleve[$numero]["division"];
					if($fich){
						//fwrite($fich,$chaine."\n");
						fwrite($fich,html_entity_decode($chaine)."\n");
					}

					//my_echo("Parcours des divisions existantes: ");
					$temoin_new_div="oui";
					for($k=0;$k<count($tab_division);$k++){
						//my_echo($tab_division[$k]["nom"]." (<i>$k</i>) ");
						if($eleve[$numero]["division"]==$tab_division[$k]["nom"]){
							$temoin_new_div="non";
							//my_echo(" (<font color='green'><i>BINGO</i></font>) ");
							break;
						}
					}
					if($temoin_new_div=="oui"){
						//$k++;
						$tab_division[$k]=array();
						//$tab_division[$k]["nom"]=ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($eleve[$numero]["division"])));
						$tab_division[$k]["nom"]=$eleve[$numero]["division"];
						$tab_division[$k]["option"]=array();
						//my_echo("<br />Nouvelle classe: \$tab_division[$k][\"nom\"]=".$tab_division[$k]["nom"]."<br />");
					}

					// Et pour les options, on conserve $eleves? NON
					//$eleves[$i]["options"][$j]
					if(isset($eleves[$i]["options"])){
						$eleve[$numero]["options"]=array();
						for($j=0;$j<count($eleves[$i]["options"]);$j++){
							$eleve[$numero]["options"][$j]=array();
							$eleve[$numero]["options"][$j]["code_matiere"]=$eleves[$i]["options"][$j]["code_matiere"];
							// Les autres champs ne sont pas très utiles...

							//my_echo("Option suivie: \$eleve[$numero][\"options\"][$j][\"code_matiere\"]=".$eleve[$numero]["options"][$j]["code_matiere"]."<br />");

							// TESTER SI L'OPTION EST DEJA DANS LA LISTE DES OPTIONS DE LA CLASSE.
							//my_echo("Options existantes: ");
							$temoin_nouvelle_option="oui";
							for($n=0;$n<count($tab_division[$k]["option"]);$n++){
								//my_echo($tab_division[$k]["option"][$n]["code_matiere"]." (<i>$k - $n</i>)");
								if($tab_division[$k]["option"][$n]["code_matiere"]==$eleve[$numero]["options"][$j]["code_matiere"]){
									$temoin_nouvelle_option="non";
									//my_echo(" (<font color='green'><i>BINGO</i></font>) ");
									break;
								}
							}
							//my_echo("<br />");
							if($temoin_nouvelle_option=="oui"){
								//$n++;
								$tab_division[$k]["option"][$n]=array();
								$tab_division[$k]["option"][$n]["code_matiere"]=$eleve[$numero]["options"][$j]["code_matiere"];
								$tab_division[$k]["option"][$n]["eleve"]=array();
								//my_echo("Nouvelle option: \$tab_division[$k][\"option\"][$n][\"code_matiere\"]=".$tab_division[$k]["option"][$n]["code_matiere"]."<br />");
							}
							//my_echo("<br />");
							$tab_division[$k]["option"][$n]["eleve"][]=$eleve[$numero]["numero"];

						//	my_echo("<p>Membres actuels de l'option ".$tab_division[$k]["option"][$n]["code_matiere"]." de ".$tab_division[$k]["nom"].": ");
						//	for($m=0;$m<count($tab_division[$k]["option"][$n]["eleve"]);$m++){
						//		my_echo($tab_division[$k]["option"][$n]["eleve"][$m]." ");
						//	}
						//	my_echo(" ($m)</p>");
						}
					}
				}
				$i++;
			}
			if($fich){
				fclose($fich);
			}
			if($temoin_creation_fichiers!="non"){
				my_echo("<script type='text/javascript'>
	document.getElementById('id_f_ele_txt').style.display='';
</script>");
			}
			//my_echo("disk_total_space($dossiercsv)=".disk_total_space($dossiercsv)."<br />");

		//	// Affichage pour debug:
		//	for($k=0;$k<count($tab_division);$k++){
		//		my_echo("<p>\$tab_division[$k][\"nom\"]=<b>".$tab_division[$k]["nom"]."</b></p>");
		//		for($n=0;$n<count($tab_division[$k]["option"]);$n++){
		//			my_echo("<p>\$tab_division[$k][\"option\"][$n][\"code_matiere\"]=".$tab_division[$k]["option"][$n]["code_matiere"]."<br />");
		//			//my_echo("<ul>");
		//			my_echo("Elèves: ");
		//			my_echo($tab_division[$k]["option"][$n]["eleve"][0]);
		//			for($i=1;$i<count($tab_division[$k]["option"][$n]["eleve"]);$i++){
		//				//my_echo("<li></li>");
		//				my_echo(", ".$tab_division[$k]["option"][$n]["eleve"][$i]);
		//			}
		//			//my_echo("</ul>");
		//			my_echo("</p>");
		//		}
		//		my_echo("<hr />");
		//	}




		}
		else{
			//$eleves_xml_file
			//my_echo("<p>ERREUR lors de l'ouverture du fichier ".$eleves_xml_file['name']." (<i>".$eleves_xml_file['tmp_name']."</i>).</p>\n");
			my_echo("<p>ERREUR lors de l'ouverture du fichier '$eleves_file'</p>\n");
		}
	}

	//my_echo("<p>Fin provisoire...</p>");
	//exit;



	// Lecture du XML de STS...
	$temoin_au_moins_un_prof_princ="";

	//$sts_xml_file = isset($_FILES["sts_xml_file"]) ? $_FILES["sts_xml_file"] : NULL;
	//$fp=fopen($sts_xml_file['tmp_name'],"r");
	$fp=fopen($sts_xml_file,"r");
	if($fp){
		//my_echo("<h2>Section professeurs, matières, groupes,...</h2>\n");
		//my_echo("<h3>Section professeurs, matières, groupes,...</h3>\n");
		my_echo("<h3>Section professeurs, matières, groupes,...");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h3>\n");
		my_echo("<blockquote>\n");
		//my_echo("<h3>Lecture du fichier...</h3>\n");
		//my_echo("<h4>Lecture du fichier...</h4>\n");
		my_echo("<h4>Lecture du fichier...");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h4>\n");
		my_echo("<blockquote>\n");
		unset($ligne);
		$ligne=array();
		while(!feof($fp)){
			$ligne[]=fgets($fp,4096);
		}
		fclose($fp);
		my_echo("<p>Terminé.</p>\n");
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		//my_echo("<p>Aller à la <a href='#se3'>section CSV profs,...</a></p>\n");
		my_echo("</blockquote>\n");



		// Contrôle du contenu du fichier:
		if(!stristr($ligne[0],"<?xml ")){
			my_echo("<p style='color:red;'>ERREUR: Le fichier STS/Emploi-du-temps fourni n'a pas l'air d'être de type XML.<br />La première ligne devrait débuter par '&lt;?xml '.</p>\n");
			my_echo("<script type='text/javascript'>
	compte_a_rebours='n';
</script>\n");
			my_echo("</body>\n</html>\n");
			my_echo("<div style='position:absolute; top: 50px; left: 300px; width: 400px; border: 1px solid black; background-color: red;'><div align='center'>ERREUR: Le fichier STS/Emploi-du-temps fourni n'a pas l'air d'être de type XML.<br />La première ligne devrait débuter par '&lt;?xml '.</div></div>");

			// Renseignement du témoin de mise à jour terminée.
			$sql="SELECT value FROM params WHERE name='imprt_cmpts_en_cours'";
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)==0){
				$sql="INSERT INTO params SET name='imprt_cmpts_en_cours',value='n'";
				$res0=mysql_query($sql);
			}
			else{
				$sql="UPDATE params SET value='n' WHERE name='imprt_cmpts_en_cours'";
				$res0=mysql_query($sql);
			}

			exit();
		}

		if(!stristr($ligne[1],"<STS_EDT>")){
			my_echo("<p style='color:red;'>ERREUR: Le fichier XML professeurs fourni n'a pas l'air d'être un fichier STS/Emploi-du-temps.<br />La deuxième ligne devrait contenir '&lt;STS_EDT&gt;'.</p>\n");
			my_echo("<script type='text/javascript'>
	compte_a_rebours='n';
</script>\n");
			my_echo("</body>\n</html>\n");
			my_echo("<div style='position:absolute; top: 50px; left: 300px; width: 400px; border: 1px solid black; background-color: red;'><div align='center'>ERREUR: Le fichier XML professeurs fourni n'a pas l'air d'être un fichier STS/Emploi-du-temps.<br />La deuxième ligne devrait contenir '&lt;STS_EDT&gt;'.</div></div>");

			// Renseignement du témoin de mise à jour terminée.
			$sql="SELECT value FROM params WHERE name='imprt_cmpts_en_cours'";
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)==0){
				$sql="INSERT INTO params SET name='imprt_cmpts_en_cours',value='n'";
				$res0=mysql_query($sql);
			}
			else{
				$sql="UPDATE params SET value='n' WHERE name='imprt_cmpts_en_cours'";
				$res0=mysql_query($sql);
			}

			exit();
		}



		//my_echo("<h3>Affichage du XML de STS</h3>\n");
		//my_echo("<h4>Affichage du XML de STS</h4>\n");
		my_echo("<h4>Affichage du XML de STS");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h4>\n");
		my_echo("<blockquote>\n");
		my_echo("<table border='0'>\n");
		$cpt=0;
		while($cpt<count($ligne)){
			my_echo("<tr>\n");
			my_echo("<td style='color: blue;'>$cpt</td><td>".htmlentities($ligne[$cpt])."</td>\n");
			my_echo("</tr>\n");
			$cpt++;
		}
		my_echo("</table>\n");
		my_echo("<p>Terminé.</p>\n");
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		my_echo("</blockquote>\n");
		//my_echo("</blockquote>\n");



		//my_echo("<h2>Etablissement</h2>\n");
		//my_echo("<h3>Etablissement</h3>\n");
		//my_echo("<h4>Etablissement</h4>\n");
		my_echo("<h4>Etablissement");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h4>\n");
		my_echo("<blockquote>\n");
		//my_echo("<h3>Analyse du fichier pour extraire les paramètres de l'établissement...</h3>\n");
		//my_echo("<h4>Analyse du fichier pour extraire les paramètres de l'établissement...</h4>\n");
		//my_echo("<h5>Analyse du fichier pour extraire les paramètres de l'établissement...</h5>\n");
		my_echo("<h5>Analyse du fichier pour extraire les paramètres de l'établissement...");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h5>\n");
		my_echo("<blockquote>\n");
		$cpt=0;
		$etablissement=array();
		$temoin_param=0;
		$temoin_academie=0;
		$temoin_annee=0;
		while($cpt<count($ligne)){
			//my_echo(htmlentities($ligne[$cpt])."<br />\n");
			if(strstr($ligne[$cpt],"<PARAMETRES>")){
				my_echo("Début de la section PARAMETRES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_param++;
			}
			if(strstr($ligne[$cpt],"</PARAMETRES>")){
				my_echo("Fin de la section PARAMETRES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_param++;
			}
			if($temoin_param==1){
				if(strstr($ligne[$cpt],"<UAJ ")){
					unset($tabtmp);
					$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
					$etablissement["code"]=trim($tabtmp[1]);
					$temoin_uaj=1;
					//my_echo("\$temoin_uaj=$temoin_uaj à la ligne $cpt et \$tabtmp[1]=$tabtmp[1]<br />\n");
				}
				if(strstr($ligne[$cpt],"</UAJ>")){
					$temoin_uaj=0;
				}
				if(isset($temoin_uaj)){
					if($temoin_uaj==1){
						if(strstr($ligne[$cpt],"<ACADEMIE>")){
							$temoin_academie=1;
							$etablissement["academie"]=array();
						}
						if(strstr($ligne[$cpt],"</ACADEMIE>")){
							$temoin_academie=0;
						}
						if($temoin_academie==1){
							if(strstr($ligne[$cpt],"<CODE>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["academie"]["code"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<LIBELLE>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["academie"]["libelle"]=trim($tabtmp[2]);
							}
						}
						else{
							if(strstr($ligne[$cpt],"<SIGLE>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["sigle"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<DENOM_PRINC>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["denom_princ"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<DENOM_COMPL>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["denom_compl"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<CODE_NATURE>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["code_nature"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<CODE_CATEGORIE>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["code_categorie"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<ADRESSE>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["adresse"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<COMMUNE>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["commune"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<CODE_POSTAL>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["code_postal"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<BOITE_POSTALE>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["boite_postale"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<CEDEX>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["cedex"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<TELEPHONE>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["telephone"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<STATUT>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["statut"]=trim($tabtmp[2]);
							}
							if(strstr($ligne[$cpt],"<ETABLISSEMENT_SENSIBLE>")){
								unset($tabtmp);
								$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
								$etablissement["etablissement_sensible"]=trim($tabtmp[2]);
							}
						}
					}
				}

				if(strstr($ligne[$cpt],"<ANNEE_SCOLAIRE ")){
					unset($tabtmp);
					$tabtmp=explode('"',strstr($ligne[$cpt]," ANNEE"));
					$etablissement["annee"]=array();
					$etablissement["annee"]["annee"]=trim($tabtmp[1]);
					$temoin_annee=1;
				}
				if(strstr($ligne[$cpt],"</ANNEE_SCOLAIRE>")){
					$temoin_annee=0;
				}
				if($temoin_annee==1){
					if(strstr($ligne[$cpt],"<DATE_DEBUT>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						$etablissement["annee"]["date_debut"]=trim($tabtmp[2]);
					}
					if(strstr($ligne[$cpt],"<DATE_FIN>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						$etablissement["annee"]["date_fin"]=trim($tabtmp[2]);
					}
				}
			}
			$cpt++;
		}
		my_echo("<p>Terminé.</p>\n");
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		my_echo("</blockquote>\n");

		//my_echo("<h3>Affichage des données PARAMETRES établissement extraites:</h3>\n");
		//my_echo("<h5>Affichage des données PARAMETRES établissement extraites:</h5>\n");
		my_echo("<h5>Affichage des données PARAMETRES établissement extraites:");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h5>\n");
		my_echo("<blockquote>\n");
		my_echo("<table border='1'>\n");
		my_echo("<tr>\n");
		//my_echo("<th style='color: blue;'>&nbsp;</th>\n");
		my_echo("<th>Code</th>\n");
		my_echo("<th>Code académie</th>\n");
		my_echo("<th>Libelle académie</th>\n");
		my_echo("<th>Sigle</th>\n");
		my_echo("<th>Denom_princ</th>\n");
		my_echo("<th>Denom_compl</th>\n");
		my_echo("<th>Code_nature</th>\n");
		my_echo("<th>Code_categorie</th>\n");
		my_echo("<th>Adresse</th>\n");
		my_echo("<th>Code_postal</th>\n");
		my_echo("<th>Boite_postale</th>\n");
		my_echo("<th>Cedex</th>\n");
		my_echo("<th>Telephone</th>\n");
		my_echo("<th>Statut</th>\n");
		my_echo("<th>Etablissement_sensible</th>\n");
		my_echo("<th>Annee</th>\n");
		my_echo("<th>Date_debut</th>\n");
		my_echo("<th>Date_fin</th>\n");
		my_echo("</tr>\n");
		//$cpt=0;
		//while($cpt<count($etablissement)){
			my_echo("<tr>\n");
			//my_echo("<td style='color: blue;'>$cpt</td>\n");
			//my_echo("<td style='color: blue;'>&nbsp;</td>\n");
			my_echo("<td>".$etablissement["code"]."</td>\n");
			my_echo("<td>".$etablissement["academie"]["code"]."</td>\n");
			my_echo("<td>".$etablissement["academie"]["libelle"]."</td>\n");
			my_echo("<td>".$etablissement["sigle"]."</td>\n");
			my_echo("<td>".$etablissement["denom_princ"]."</td>\n");
			my_echo("<td>".$etablissement["denom_compl"]."</td>\n");
			my_echo("<td>".$etablissement["code_nature"]."</td>\n");
			my_echo("<td>".$etablissement["code_categorie"]."</td>\n");
			my_echo("<td>".$etablissement["adresse"]."</td>\n");
			my_echo("<td>".$etablissement["code_postal"]."</td>\n");
			my_echo("<td>".$etablissement["boite_postale"]."</td>\n");
			my_echo("<td>".$etablissement["cedex"]."</td>\n");
			my_echo("<td>".$etablissement["telephone"]."</td>\n");
			my_echo("<td>".$etablissement["statut"]."</td>\n");
			my_echo("<td>".$etablissement["etablissement_sensible"]."</td>\n");
			my_echo("<td>".$etablissement["annee"]["annee"]."</td>\n");
			my_echo("<td>".$etablissement["annee"]["date_debut"]."</td>\n");
			my_echo("<td>".$etablissement["annee"]["date_fin"]."</td>\n");
			my_echo("</tr>\n");
			$cpt++;
		//}
		my_echo("</table>\n");
		my_echo("</blockquote>\n");
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		my_echo("</blockquote>\n");











		//my_echo("<h2>Matières</h2>\n");
		//my_echo("<h3>Matières</h3>\n");
		//my_echo("<h4>Matières</h4>\n");
		my_echo("<h4>Matières");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h4>\n");
		my_echo("<blockquote>\n");
		//my_echo("<h3>Analyse du fichier pour extraire les matières...</h3>\n");
		//my_echo("<h4>Analyse du fichier pour extraire les matières...</h4>\n");
		//my_echo("<h5>Analyse du fichier pour extraire les matières...</h5>\n");
		my_echo("<h5>Analyse du fichier pour extraire les matières...");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h5>\n");
		my_echo("<blockquote>\n");
		$cpt=0;
		$temoin_matieres=0;
		$matiere=array();
		$i=0;
		$temoin_mat=0;
		while($cpt<count($ligne)){
			//my_echo(htmlentities($ligne[$cpt])."<br />\n");
			if(strstr($ligne[$cpt],"<MATIERES>")){
				my_echo("Début de la section MATIERES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_matieres++;
			}
			if(strstr($ligne[$cpt],"</MATIERES>")){
				my_echo("Fin de la section MATIERES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_matieres++;
			}
			if($temoin_matieres==1){
				// On analyse maintenant matière par matière:
				if(strstr($ligne[$cpt],"<MATIERE ")){
					$matiere[$i]=array();
					unset($tabtmp);
					//$tabtmp=explode("=",ereg_replace(">","",ereg_replace("<","",$ligne[$cpt])));
					$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
					$matiere[$i]["code"]=trim($tabtmp[1]);
					$temoin_mat=1;
				}
				if(strstr($ligne[$cpt],"</MATIERE>")){
					$temoin_mat=0;
					$i++;
				}
				if($temoin_mat==1){
					if(strstr($ligne[$cpt],"<CODE_GESTION>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						//$matiere[$i]["code_gestion"]=$tabtmp[2];
						$matiere[$i]["code_gestion"]=trim(ereg_replace("[^a-zA-Z0-9&_. -]","",html_entity_decode($tabtmp[2])));
					}
					if(strstr($ligne[$cpt],"<LIBELLE_COURT>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						//$matiere[$i]["libelle_court"]=$tabtmp[2];
						$matiere[$i]["libelle_court"]=trim(ereg_replace("[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü&_. -]","",html_entity_decode($tabtmp[2])));
					}
					if(strstr($ligne[$cpt],"<LIBELLE_LONG>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						$matiere[$i]["libelle_long"]=trim($tabtmp[2]);
					}
					if(strstr($ligne[$cpt],"<LIBELLE_EDITION>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						$matiere[$i]["libelle_edition"]=trim($tabtmp[2]);
					}
				}
			}

			$cpt++;
		}
		my_echo("<p>Terminé.</p>\n");
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		my_echo("</blockquote>\n");

		//my_echo("<h3>Affichage des données MATIERES extraites:</h3>\n");
		//my_echo("<h5>Affichage des données MATIERES extraites:</h5>\n");
		my_echo("<h5>Affichage des données MATIERES extraites:");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h5>\n");
		my_echo("<blockquote>\n");
		my_echo("<table border='1'>\n");
		my_echo("<tr>\n");
		my_echo("<th style='color: blue;'>&nbsp;</th>\n");
		my_echo("<th>Code</th>\n");
		my_echo("<th>Code_gestion</th>\n");
		my_echo("<th>Libelle_court</th>\n");
		my_echo("<th>Libelle_long</th>\n");
		my_echo("<th>Libelle_edition</th>\n");
		my_echo("</tr>\n");
		$cpt=0;
		while($cpt<count($matiere)){
			my_echo("<tr>\n");
			my_echo("<td style='color: blue;'>$cpt</td>\n");
			my_echo("<td>".$matiere[$cpt]["code"]."</td>\n");
			my_echo("<td>".htmlentities($matiere[$cpt]["code_gestion"])."</td>\n");
			my_echo("<td>".htmlentities($matiere[$cpt]["libelle_court"])."</td>\n");
			my_echo("<td>".htmlentities($matiere[$cpt]["libelle_long"])."</td>\n");
			my_echo("<td>".htmlentities($matiere[$cpt]["libelle_edition"])."</td>\n");
			my_echo("</tr>\n");
			$cpt++;
		}
		my_echo("</table>\n");
		my_echo("</blockquote>\n");
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		my_echo("</blockquote>\n");




		function get_nom_matiere($code){
			global $matiere;

			$retour=$code;
			for($i=0;$i<count($matiere);$i++){
				if($matiere[$i]["code"]=="$code"){
					$retour=$matiere[$i]["code_gestion"];
					break;
				}
			}
			return $retour;
		}

		function get_nom_prof($code){
			global $prof;

			$retour=$code;
			for($i=0;$i<count($prof);$i++){
				if($prof[$i]["id"]=="$code"){
					$retour=$prof[$i]["nom_usage"];
					break;
				}
			}
			return $retour;
		}


		/*
		// Section commentée
		// Les civilités ne sont pas utiles

		my_echo("<h2>Civilités</h2>\n");
		my_echo("<blockquote>\n");
		my_echo("<h3>Analyse du fichier pour extraire les civilités...</h3>\n");
		my_echo("<blockquote>\n");
		$cpt=0;
		$temoin_civilites=0;
		$civilites=array();
		$i=0;
		while($cpt<count($ligne)){
			//my_echo(htmlentities($ligne[$cpt])."<br />\n");
			if(strstr($ligne[$cpt],"<CIVILITES>")){
				my_echo("Début de la section CIVILITES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_civilites++;
			}
			if(strstr($ligne[$cpt],"</CIVILITES>")){
				my_echo("Fin de la section CIVILITES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_civilites++;
			}
			if($temoin_civilites==1){
				if(strstr($ligne[$cpt],"<CIVILITE ")){
					$civilites[$i]=array();
					unset($tabtmp);
					$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
					$civilites[$i]["code"]=trim($tabtmp[1]);
					$temoin_civ=1;
				}
				if(strstr($ligne[$cpt],"</CIVILITE>")){
					$temoin_civ=0;
					$i++;
				}
				if(isset($temoin_civ)){
					if($temoin_civ==1){
						if(strstr($ligne[$cpt],"<LIBELLE_COURT>")){
							unset($tabtmp);
							$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
							$civilites[$i]["libelle_court"]=trim($tabtmp[2]);
						}
						if(strstr($ligne[$cpt],"<LIBELLE_LONG>")){
							unset($tabtmp);
							$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
							$civilites[$i]["libelle_long"]=trim($tabtmp[2]);
						}
					}
				}
			}
			$cpt++;
		}
		my_echo("<p>Terminé.</p>\n");
		my_echo("</blockquote>\n");

		my_echo("<h3>Affichage des données CIVILITES extraites:</h3>\n");
		my_echo("<blockquote>\n");
		my_echo("<table border='1'>\n");
		my_echo("<tr>\n");
		my_echo("<th style='color: blue;'>&nbsp;</th>\n");
		my_echo("<th>Code</th>\n");
		my_echo("<th>Libelle_court</th>\n");
		my_echo("<th>Libelle_long</th>\n");
		my_echo("</tr>\n");
		$cpt=0;
		while($cpt<count($civilites)){
			my_echo("<tr>\n");
			my_echo("<td style='color: blue;'>$cpt</td>\n");
			my_echo("<td>".$civilites[$cpt]["code"]."</td>\n");
			my_echo("<td>".$civilites[$cpt]["libelle_court"]."</td>\n");
			my_echo("<td>".$civilites[$cpt]["libelle_long"]."</td>\n");
			my_echo("</tr>\n");
			$cpt++;
		}
		my_echo("</table>\n");
		my_echo("</blockquote>\n");
		my_echo("</blockquote>\n");
		*/










		//my_echo("<h2>Personnels</h2>\n");
		//my_echo("<h3>Personnels</h3>\n");
		//my_echo("<h4>Personnels</h4>\n");
		my_echo("<h4>Personnels");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h4>\n");
		my_echo("<blockquote>\n");
		//my_echo("<h3>Analyse du fichier pour extraire les professeurs,...</h3>\n");
		//my_echo("<h4>Analyse du fichier pour extraire les professeurs,...</h4>\n");
		//my_echo("<h5>Analyse du fichier pour extraire les professeurs,...</h5>\n");
		my_echo("<h5>Analyse du fichier pour extraire les professeurs,...");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h5>\n");
		my_echo("<blockquote>\n");
		$cpt=0;
		$temoin_professeurs=0;
		$prof=array();
		$i=0;
		$temoin_prof=0;
		while($cpt<count($ligne)){
			//my_echo(htmlentities($ligne[$cpt])."<br />\n");
			if(strstr($ligne[$cpt],"<INDIVIDUS>")){
				my_echo("Début de la section INDIVIDUS à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_professeurs++;
			}
			if(strstr($ligne[$cpt],"</INDIVIDUS>")){
				my_echo("Fin de la section INDIVIDUS à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_professeurs++;
			}
			if($temoin_professeurs==1){
				// On analyse maintenant matière par matière:
				/*
				if(strstr($ligne[$cpt],"<INDIVIDU ID=")){
					$prof[$i]=array();
					unset($tabtmp);
					$tabtmp=explode('"',$ligne[$cpt]);
					$prof[$i]["id"]=$tabtmp[1];
					$prof[$i]["type"]=$tabtmp[3];
					$temoin_prof=1;
				}
				*/
				if(strstr($ligne[$cpt],"<INDIVIDU ")){
					$prof[$i]=array();
					unset($tabtmp);
					$tabtmp=explode('"',strstr($ligne[$cpt]," ID="));
					$prof[$i]["id"]=trim($tabtmp[1]);
					$tabtmp=explode('"',strstr($ligne[$cpt]," TYPE="));
					$prof[$i]["type"]=trim($tabtmp[1]);
					$temoin_prof=1;
				}
				if(strstr($ligne[$cpt],"</INDIVIDU>")){
					$temoin_prof=0;
					$i++;
				}
				if($temoin_prof==1){
					if(strstr($ligne[$cpt],"<SEXE>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						//$prof[$i]["sexe"]=$tabtmp[2];
						$prof[$i]["sexe"]=trim(ereg_replace("[^1-2]","",$tabtmp[2]));
					}
					if(strstr($ligne[$cpt],"<CIVILITE>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						//$prof[$i]["civilite"]=$tabtmp[2];
						$prof[$i]["civilite"]=trim(ereg_replace("[^1-3]","",$tabtmp[2]));
					}
					if(strstr($ligne[$cpt],"<NOM_USAGE>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						//$prof[$i]["nom_usage"]=$tabtmp[2];
						$prof[$i]["nom_usage"]=trim(ereg_replace("[^a-zA-Z -]","",$tabtmp[2]));
					}
					if(strstr($ligne[$cpt],"<NOM_PATRONYMIQUE>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						//$prof[$i]["nom_patronymique"]=$tabtmp[2];
						$prof[$i]["nom_patronymique"]=trim(ereg_replace("[^a-zA-Z -]","",$tabtmp[2]));
					}
					if(strstr($ligne[$cpt],"<PRENOM>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						//$prof[$i]["prenom"]=$tabtmp[2];
						$prof[$i]["prenom"]=trim(ereg_replace("[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. -]","",$tabtmp[2]));
					}
					if(strstr($ligne[$cpt],"<DATE_NAISSANCE>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						//$prof[$i]["date_naissance"]=$tabtmp[2];
						$prof[$i]["date_naissance"]=trim(ereg_replace("[^0-9-]","",$tabtmp[2]));
					}
					if(strstr($ligne[$cpt],"<GRADE>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						$prof[$i]["grade"]=trim($tabtmp[2]);
					}
					if(strstr($ligne[$cpt],"<FONCTION>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						$prof[$i]["fonction"]=trim($tabtmp[2]);
					}



					if(strstr($ligne[$cpt],"<PROFS_PRINC>")){
						$temoin_profs_princ=1;
						//$prof[$i]["prof_princs"]=array();
						$j=0;
					}
					if(strstr($ligne[$cpt],"</PROFS_PRINC>")){
						$temoin_profs_princ=0;
					}

					if(isset($temoin_profs_princ)){
						if($temoin_profs_princ==1){

							if(strstr($ligne[$cpt],"<PROF_PRINC>")){
								$temoin_prof_princ=1;
								$prof[$i]["prof_princ"]=array();
							}
							if(strstr($ligne[$cpt],"</PROF_PRINC>")){
								$temoin_prof_princ=0;
								$j++;
							}

							if(isset($temoin_prof_princ)){
								if($temoin_prof_princ==1){
									if(strstr($ligne[$cpt],"<CODE_STRUCTURE>")){
										unset($tabtmp);
										$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
										$prof[$i]["prof_princ"][$j]["code_structure"]=trim($tabtmp[2]);
										$temoin_au_moins_un_prof_princ="oui";
									}

									if(strstr($ligne[$cpt],"<DATE_DEBUT>")){
										unset($tabtmp);
										$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
										$prof[$i]["prof_princ"][$j]["date_debut"]=trim($tabtmp[2]);
									}
									if(strstr($ligne[$cpt],"<DATE_FIN>")){
										unset($tabtmp);
										$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
										$prof[$i]["prof_princ"][$j]["date_fin"]=trim($tabtmp[2]);
									}
								}
							}
						}
					}



					if(strstr($ligne[$cpt],"<DISCIPLINES>")){
						$temoin_disciplines=1;
						$prof[$i]["disciplines"]=array();
						$j=0;
					}
					if(strstr($ligne[$cpt],"</DISCIPLINES>")){
						$temoin_disciplines=0;
					}



					if(isset($temoin_disciplines)){
						if($temoin_disciplines==1){
							/*
							if(strstr($ligne[$cpt],"<DISCIPLINE CODE=")){
								$temoin_disc=1;
								unset($tabtmp);
								$tabtmp=explode('"',$ligne[$cpt]);
								$prof[$i]["disciplines"][$j]["code"]=$tabtmp[1];
							}
							*/
							if(strstr($ligne[$cpt],"<DISCIPLINE ")){
								$temoin_disc=1;
								unset($tabtmp);
								$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
								$prof[$i]["disciplines"][$j]["code"]=trim($tabtmp[1]);

								// DEBUG:
								//my_echo("<p><span style='color:red;'>(Ligne $cpt)</span> ".$prof[$i]["nom_usage"].": \n");

								//my_echo($prof[$i]["disciplines"][$j]["code"]."\n");
							}
							if(strstr($ligne[$cpt],"</DISCIPLINE>")){
								$temoin_disc=0;
								$j++;
							}

							//if(isset($temoin_prof_princ)){
								//if($temoin_prof_princ==1){
							if(isset($temoin_disc)){
								if($temoin_disc==1){
									if(strstr($ligne[$cpt],"<LIBELLE_COURT>")){
										unset($tabtmp);
										$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
										$prof[$i]["disciplines"][$j]["libelle_court"]=trim($tabtmp[2]);

										// DEBUG:
										//my_echo(" ".$prof[$i]["disciplines"][$j]["libelle_court"]."<br />\n");
									}
									if(strstr($ligne[$cpt],"<NB_HEURES>")){
										unset($tabtmp);
										$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
										$prof[$i]["disciplines"][$j]["nb_heures"]=trim($tabtmp[2]);
									}
								}
							}
						}
					}

				}
			}



			// On va récupérer les divisions et associations profs/matières...
			if(!isset($temoin_structure)){$temoin_structure=0;}
			if(strstr($ligne[$cpt],"<STRUCTURE>")){
				my_echo("Début de la section STRUCTURE à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_structure++;
			}
			if(strstr($ligne[$cpt],"</STRUCTURE>")){
				my_echo("Fin de la section STRUCTURE à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_structure++;
			}
			if(isset($temoin_structure)){
				if($temoin_structure==1){
					if(!isset($temoin_divisions)){$temoin_divisions=0;}
					if(strstr($ligne[$cpt],"<DIVISIONS>")){
						my_echo("Début de la section DIVISIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n");
						$temoin_divisions++;
						$divisions=array();
						$i=0;
					}
					if(strstr($ligne[$cpt],"</DIVISIONS>")){
						my_echo("Fin de la section DIVISIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n");
						$temoin_divisions++;
					}
					if(isset($temoin_divisions)){
						if($temoin_divisions==1){
							/*
							if(strstr($ligne[$cpt],"<DIVISION CODE=")){
								$temoin_div=1;
								unset($tabtmp);
								$tabtmp=explode('"',$ligne[$cpt]);
								$divisions[$i]["code"]=$tabtmp[1];
							}
							*/
							if(strstr($ligne[$cpt],"<DIVISION ")){
								$temoin_div=1;
								unset($tabtmp);
								$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
								$divisions[$i]["code"]=trim($tabtmp[1]);
							}
							if(strstr($ligne[$cpt],"</DIVISION>")){
								$temoin_div=0;
								$i++;
							}

							if(isset($temoin_div)){
								if($temoin_div==1){
									if(strstr($ligne[$cpt],"<SERVICES>")){
										$temoin_services=1;
										$j=0;
									}
									if(strstr($ligne[$cpt],"</SERVICES>")){
										$temoin_services=0;
									}

									if(isset($temoin_services)){
										if($temoin_services==1){
											/*
											if(strstr($ligne[$cpt],"<SERVICE CODE_MATIERE=")){
												$temoin_disc=1;
												unset($tabtmp);
												$tabtmp=explode('"',$ligne[$cpt]);
												$divisions[$i]["services"][$j]["code_matiere"]=$tabtmp[1];
											}
											*/
											if(strstr($ligne[$cpt],"<SERVICE ")){
												$temoin_disc=1;
												unset($tabtmp);
												$tabtmp=explode('"',strstr($ligne[$cpt]," CODE_MATIERE="));
												$divisions[$i]["services"][$j]["code_matiere"]=trim($tabtmp[1]);

												// DEBUG:
												//my_echo("<p><span style='color:red;'>(Ligne $cpt)</span> ".get_nom_matiere($divisions[$i]["services"][$j]["code_matiere"]).": \n");
											}
											if(strstr($ligne[$cpt],"</SERVICE>")){
												$temoin_disc=0;
												$j++;
											}

											if($temoin_disc==1){
												if(strstr($ligne[$cpt],"<ENSEIGNANTS>")){
													$temoin_enseignants=1;
													$divisions[$i]["services"][$j]["enseignants"]=array();
													$k=0;
												}
												if(strstr($ligne[$cpt],"</ENSEIGNANTS>")){
													$temoin_enseignants=0;
												}
												if(isset($temoin_enseignants)){
													if($temoin_enseignants==1){
														/*
														if(strstr($ligne[$cpt],"<ENSEIGNANT ID=")){
															//$temoin_ens=1;
															unset($tabtmp);
															$tabtmp=explode('"',$ligne[$cpt]);
															$divisions[$i]["services"][$j]["enseignants"][$k]["id"]=$tabtmp[1];
														}
														*/
														if(strstr($ligne[$cpt],"<ENSEIGNANT ")){
															//$temoin_ens=1;
															unset($tabtmp);
															$tabtmp=explode('"',strstr($ligne[$cpt]," ID="));
															$divisions[$i]["services"][$j]["enseignants"][$k]["id"]=trim($tabtmp[1]);

															// DEBUG:
															//my_echo(" ".get_nom_prof($divisions[$i]["services"][$j]["enseignants"][$k]["id"])."\n");
														}
														if(strstr($ligne[$cpt],"</ENSEIGNANT>")){
															//$temoin_ens=0;
															$k++;
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}





					if(!isset($temoin_groupes)){$temoin_groupes=0;}
					if(strstr($ligne[$cpt],"<GROUPES>")){
						my_echo("Début de la section GROUPES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
						$temoin_groupes++;
						$groupes=array();
						$i=0;
					}
					if(strstr($ligne[$cpt],"</GROUPES>")){
						my_echo("Fin de la section GROUPES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
						$temoin_groupes++;
					}
					if(isset($temoin_groupes)){
						if($temoin_groupes==1){
							/*
							if(strstr($ligne[$cpt],"<GROUPE CODE=")){
								$temoin_grp=1;
								unset($tabtmp);
								$tabtmp=explode('"',$ligne[$cpt]);
								$groupes[$i]=array();
								$groupes[$i]["code"]=$tabtmp[1];
								$j=0;
								$m=0;
							}
							*/
							if(strstr($ligne[$cpt],"<GROUPE ")){
								$temoin_grp=1;
								$temoin_services=0;
								unset($tabtmp);
								$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
								$groupes[$i]=array();
								$groupes[$i]["code"]=trim($tabtmp[1]);
								$j=0;
								$m=0;
								$n=0;

								// DEBUG
								//my_echo("<p><span style='color:red;'>(Ligne $cpt)</span> ".$groupes[$i]["code"].": \n");
							}
							if(strstr($ligne[$cpt],"</GROUPE>")){
								$temoin_grp=0;
								$i++;
							}

							if(isset($temoin_grp)){
								if($temoin_grp==1){
									if((strstr($ligne[$cpt],"<LIBELLE_LONG>"))||(strstr($ligne[$cpt],"<LIBELLE_LONG/>"))){
										if(strstr($ligne[$cpt],"<LIBELLE_LONG>")){
											unset($tabtmp);
											$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
											$groupes[$i]["libelle_long"]=trim($tabtmp[2]);
										}
										else{
											$groupes[$i]["libelle_long"]=$groupes[$i]["code"];
										}

										// DEBUG
										//my_echo("libelle_long='".$groupes[$i]["libelle_long"]."' \n");
									}

									if(strstr($ligne[$cpt],"<DIVISIONS_APPARTENANCE>")){
										$temoin_div_appart=1;
									}
									if(strstr($ligne[$cpt],"</DIVISIONS_APPARTENANCE>")){
										$temoin_div_appart=0;
									}

									if(isset($temoin_div_appart)){
										if($temoin_div_appart==1){
											/*
											if(strstr($ligne[$cpt],"<DIVISION_APPARTENANCE CODE=")){
												unset($tabtmp);
												$tabtmp=explode('"',$ligne[$cpt]);
												$groupes[$i]["divisions"][$j]["code"]=$tabtmp[1];
												$j++;
											}
											*/
											if(strstr($ligne[$cpt],"<DIVISION_APPARTENANCE ")){
												unset($tabtmp);
												$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
												$groupes[$i]["divisions"][$j]["code"]=trim($tabtmp[1]);
												$j++;
											}
										}
									}


									if(strstr($ligne[$cpt],"<SERVICES>")){
										$temoin_services=1;
										if(!isset($groupes[$i]["service"])){$groupes[$i]["service"]=array();}
									}
									if(strstr($ligne[$cpt],"</SERVICES>")){
										$temoin_services=0;
									}

									//<SERVICE CODE_MATIERE="020100" CODE_MOD_COURS="CG">
									/*
									if(strstr($ligne[$cpt],"<SERVICE CODE_MATIERE=")){
										unset($tabtmp);
										$tabtmp=explode('"',$ligne[$cpt]);
										$groupes[$i]["code_matiere"]=$tabtmp[1];
									}
									*/
									if($temoin_services==1){
										if(strstr($ligne[$cpt],"<SERVICE ")){
											$groupes[$i]["service"][$n]=array();
											unset($tabtmp);
											$tabtmp=explode('"',strstr($ligne[$cpt]," CODE_MATIERE="));
											$groupes[$i]["service"][$n]["code_matiere"]=trim($tabtmp[1]);
										}

										if(strstr($ligne[$cpt],"</SERVICE>")){
											$n++;
											// En changeant de service, on réinitialise le compteur d'enseignants pour le service
											$m=0;
										}


										//<ENSEIGNANT TYPE="epp" ID="31762">
										// Améliorer la récup de l'attribut ID...
										// ...découper en un tableau avec ' '
										// et rechercher quel champ du tableau commence par ID=

										//<ENSEIGNANT ID="11508" TYPE="epp">

										//if(strstr($ligne[$cpt],"<ENSEIGNANT TYPE=")){
										/*
										if(strstr($ligne[$cpt],"<ENSEIGNANT ID=")){
											unset($tabtmp);
											$tabtmp=explode('"',$ligne[$cpt]);
											//$groupes[$i]["enseignant"][$m]["id"]=$tabtmp[3];
											$groupes[$i]["enseignant"][$m]["id"]=$tabtmp[1];
											$m++;
										}
										*/
										if(strstr($ligne[$cpt],"<ENSEIGNANT ")){
											unset($tabtmp);
											$tabtmp=explode('"',strstr($ligne[$cpt]," ID="));
											//$groupes[$i]["enseignant"][$m]["id"]=$tabtmp[3];
											//$groupes[$i]["enseignant"][$m]["id"]=trim($tabtmp[1]);
											$groupes[$i]["service"][$n]["enseignant"][$m]["id"]=trim($tabtmp[1]);

											// DEBUG
											//my_echo(" ".get_nom_prof($groupes[$i]["enseignant"][$m]["id"])."\n");
											//my_echo(" ".get_nom_prof($groupes[$i]["service"][$n]["enseignant"][$m]["id"])."\n");

											$m++;
										}
									}
								}
							}
						}
					}
				}
			}
			$cpt++;
		}
		my_echo("<p>Terminé.</p>\n");
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		my_echo("</blockquote>\n");


		/*
		my_echo("<h2>Programmes</h2>\n");
		my_echo("<blockquote>\n");
		my_echo("<h3>Analyse du fichier pour extraire les programmes...</h3>\n");
		my_echo("<blockquote>\n");
		my_echo("<p>Il s'agit ici de remplir un tableau des liens entre les MEFS et les MATIERES.</p>\n");
		$cpt=0;
		$temoin_programmes=0;
		$programme=array();
		$i=0;
		$temoin_mat=0;
		while($cpt<count($ligne)){
			//my_echo(htmlentities($ligne[$cpt])."<br />\n");
			if(strstr($ligne[$cpt],"<PROGRAMMES>")){
				my_echo("Début de la section PROGRAMMES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_programmes++;
			}
			if(strstr($ligne[$cpt],"</PROGRAMMES>")){
				my_echo("Fin de la section PROGRAMMES à la ligne <span style='color: blue;'>$cpt</span><br />\n");
				$temoin_programmes++;
			}
			if($temoin_programmes==1){
				// On analyse maintenant matière par matière:
				if(strstr($ligne[$cpt],"<PROGRAMME>")){
					$programme[$i]=array();
					$temoin_prog=1;
				}
				if(strstr($ligne[$cpt],"</PROGRAMME>")){
					$temoin_prog=0;
					$i++;
				}
				if($temoin_prog==1){
					if(strstr($ligne[$cpt],"<CODE_MEF>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						$programme[$i]["code_mef"]=$tabtmp[2];
					}
					if(strstr($ligne[$cpt],"<CODE_MATIERE>")){
						unset($tabtmp);
						$tabtmp=explode(">",ereg_replace("<",">",$ligne[$cpt]));
						$programme[$i]["code_matiere"]=$tabtmp[2];
					}
				}
			}

			$cpt++;
		}
		my_echo("<p>Terminé.</p>\n");
		my_echo("</blockquote>\n");
		*/





		//my_echo("<h3>Affichage des données PROFS,... extraites:</h3>\n");
		//my_echo("<h5>Affichage des données PROFS,... extraites:</h5>\n");
		my_echo("<h5>Affichage des données PROFS,... extraites:");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h5>\n");
		my_echo("<blockquote>\n");
		my_echo("<table border='1'>\n");
		my_echo("<tr>\n");
		my_echo("<th style='color: blue;'>&nbsp;</th>\n");
		my_echo("<th>Id</th>\n");
		my_echo("<th>Type</th>\n");
		my_echo("<th>Sexe</th>\n");
		my_echo("<th>Civilite</th>\n");
		my_echo("<th>Nom_usage</th>\n");
		my_echo("<th>Nom_patronymique</th>\n");
		my_echo("<th>Prenom</th>\n");
		my_echo("<th>Date_naissance</th>\n");
		my_echo("<th>Grade</th>\n");
		my_echo("<th>Fonction</th>\n");
		my_echo("<th>Disciplines</th>\n");
		my_echo("</tr>\n");
		$cpt=0;
		while($cpt<count($prof)){
			my_echo("<tr>\n");
			my_echo("<td style='color: blue;'>$cpt</td>\n");
			my_echo("<td>".$prof[$cpt]["id"]."</td>\n");
			my_echo("<td>".$prof[$cpt]["type"]."</td>\n");
			my_echo("<td>".$prof[$cpt]["sexe"]."</td>\n");
			my_echo("<td>".$prof[$cpt]["civilite"]."</td>\n");
			my_echo("<td>".$prof[$cpt]["nom_usage"]."</td>\n");
			my_echo("<td>".$prof[$cpt]["nom_patronymique"]."</td>\n");

			// =============================================
			// On ne retient que le premier prénom: 20071101
			$tab_tmp_prenom=explode(" ",$prof[$cpt]["prenom"]);
			$prof[$cpt]["prenom"]=$tab_tmp_prenom[0];
			// =============================================

			my_echo("<td>".$prof[$cpt]["prenom"]."</td>\n");
			my_echo("<td>".$prof[$cpt]["date_naissance"]."</td>\n");
			my_echo("<td>".$prof[$cpt]["grade"]."</td>\n");
			my_echo("<td>".$prof[$cpt]["fonction"]."</td>\n");

			my_echo("<td align='center'>\n");

			if($prof[$cpt]["fonction"]=="ENS"){
				my_echo("<table border='1'>\n");
				my_echo("<tr>\n");
				my_echo("<th>Code</th>\n");
				my_echo("<th>Libelle_court</th>\n");
				my_echo("<th>Nb_heures</th>\n");
				my_echo("</tr>\n");
				for($j=0;$j<count($prof[$cpt]["disciplines"]);$j++){
					my_echo("<tr>\n");
					my_echo("<td>".$prof[$cpt]["disciplines"][$j]["code"]."</td>\n");
					my_echo("<td>".$prof[$cpt]["disciplines"][$j]["libelle_court"]."</td>\n");
					my_echo("<td>".$prof[$cpt]["disciplines"][$j]["nb_heures"]."</td>\n");
					my_echo("</tr>\n");
				}
				my_echo("</table>\n");
			}

			my_echo("</td>\n");
			my_echo("</tr>\n");
			$cpt++;
		}
		my_echo("</table>\n");
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		my_echo("</blockquote>\n");




		$temoin_au_moins_une_matiere="";
		$temoin_au_moins_un_prof="";
		// Affichage des infos Enseignements et divisions:
		//my_echo("<a name='divisions'></a><h3>Affichage des divisions</h3>\n");
		//my_echo("<a name='divisions'></a><h5>Affichage des divisions</h5>\n");
		my_echo("<a name='divisions'></a><h5>Affichage des divisions");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h5>\n");
		my_echo("<blockquote>\n");
		for($i=0;$i<count($divisions);$i++){
			//my_echo("<p>\$divisions[$i][\"code\"]=".$divisions[$i]["code"]."<br />\n");
			//my_echo("<h4>Classe de ".$divisions[$i]["code"]."</h4>\n");
			//my_echo("<h6>Classe de ".$divisions[$i]["code"]."</h6>\n");
			my_echo("<h6>Classe de ".$divisions[$i]["code"]);
			if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
			my_echo("</h6>\n");
			my_echo("<ul>\n");
			for($j=0;$j<count($divisions[$i]["services"]);$j++){
				//my_echo("\$divisions[$i][\"services\"][$j][\"code_matiere\"]=".$divisions[$i]["services"][$j]["code_matiere"]."<br />\n");
				my_echo("<li>\n");
				for($m=0;$m<count($matiere);$m++){
					if($matiere[$m]["code"]==$divisions[$i]["services"][$j]["code_matiere"]){
						//my_echo("\$matiere[$m][\"code_gestion\"]=".$matiere[$m]["code_gestion"]."<br />\n");
						my_echo("Matière: ".$matiere[$m]["code_gestion"]."<br />\n");
						$temoin_au_moins_une_matiere="oui";
					}
				}
				my_echo("<ul>\n");
				for($k=0;$k<count($divisions[$i]["services"][$j]["enseignants"]);$k++){
				//$divisions[$i]["services"][$j]["enseignants"][$k]["id"]
					for($m=0;$m<count($prof);$m++){
						if($prof[$m]["id"]==$divisions[$i]["services"][$j]["enseignants"][$k]["id"]){
							//my_echo($prof[$m]["nom_usage"]." ".$prof[$m]["prenom"]."|");
							my_echo("<li>\n");
							my_echo("Enseignant: ".$prof[$m]["nom_usage"]." ".$prof[$m]["prenom"]);
							my_echo("</li>\n");
							$temoin_au_moins_un_prof="oui";
						}
					}
				}
				my_echo("</ul>\n");
				//my_echo("<br />\n");
				my_echo("</li>\n");
			}
			my_echo("</ul>\n");
			//my_echo("</p>\n");
		}
		my_echo("</blockquote>\n");
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		my_echo("</blockquote>\n");
		my_echo("</blockquote>\n");
		my_echo("<h3>Génération des CSV");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h3>\n");
		my_echo("<blockquote>\n");
		my_echo("<a name='se3'></a><h4>Génération du CSV (F_WIND.txt) des profs");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h4>\n");
		my_echo("<blockquote>\n");
		$cpt=0;
		//if($temoin_creation_fichiers!="non"){$fich=fopen("$dossiercsv/se3/f_wind.txt","w+");}
		if($temoin_creation_fichiers!="non"){$fich=fopen("$dossiercsv/f_wind.txt","w+");}else{$fich=FALSE;}
		while($cpt<count($prof)){
			if($prof[$cpt]["fonction"]=="ENS"){
				$date=str_replace("-","",$prof[$cpt]["date_naissance"]);
				$chaine="P".$prof[$cpt]["id"]."|".$prof[$cpt]["nom_usage"]."|".$prof[$cpt]["prenom"]."|".$date."|".$prof[$cpt]["sexe"];
				if($fich){
					//fwrite($fich,$chaine."\n");
					fwrite($fich,html_entity_decode($chaine)."\n");
				}
				my_echo($chaine."<br />\n");
			}
			$cpt++;
		}
		if($temoin_creation_fichiers!="non"){
                    fclose($fich);
                }

		//my_echo("disk_total_space($dossiercsv)=".disk_total_space($dossiercsv)."<br />");
		if($temoin_creation_fichiers!="non"){
			my_echo("<script type='text/javascript'>
document.getElementById('id_f_wind_txt').style.display='';
</script>");
		}

		my_echo("<p>Vous pouvez copier/coller ces lignes dans un fichier texte pour effectuer l'import des comptes profs.</p>\n");
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		my_echo("</blockquote>\n");

		//my_echo("<a name='f_div'></a><h2>Génération d'un CSV du F_DIV pour SambaEdu3</h2>\n");
		//my_echo("<a name='f_div'></a><h3>Génération d'un CSV du F_DIV pour SambaEdu3</h3>\n");
		//my_echo("<a name='f_div'></a><h4>Génération d'un CSV du F_DIV pour SambaEdu3</h4>\n");
		my_echo("<a name='f_div'></a><h4>Génération d'un CSV du F_DIV");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h4>\n");
		my_echo("<blockquote>\n");
		//if($temoin_creation_fichiers!="non"){$fich=fopen("$dossiercsv/se3/f_div.txt","w+");}
		if($temoin_creation_fichiers!="non"){$fich=fopen("$dossiercsv/f_div.txt","w+");}else{$fich=FALSE;}
		for($i=0;$i<count($divisions);$i++){
			$numind_pp="";
			for($m=0;$m<count($prof);$m++){
				if(isset($prof[$m]["prof_princ"])){
					for($n=0;$n<count($prof[$m]["prof_princ"]);$n++){
						if($prof[$m]["prof_princ"][$n]["code_structure"]==$divisions[$i]["code"]){
							$numind_pp="P".$prof[$m]["id"];
						}
					}
				}
			}
			$chaine=$divisions[$i]["code"]."|".$divisions[$i]["code"]."|".$numind_pp;
			if($fich){
				//fwrite($fich,$chaine."\n");
				fwrite($fich,html_entity_decode($chaine)."\n");
			}
			my_echo($chaine."<br />\n");
		}
		if($temoin_creation_fichiers!="non"){
                    fclose($fich);
                }

		//my_echo("disk_total_space($dossiercsv)=".disk_total_space($dossiercsv)."<br />");

		if($temoin_creation_fichiers!="non"){
			my_echo("<script type='text/javascript'>
	document.getElementById('id_f_div_txt').style.display='';
</script>");
		}

		if($temoin_au_moins_un_prof_princ!="oui"){
			my_echo("<p>Il semble que votre fichier ne comporte pas l'information suivante:<br />Qui sont les profs principaux?<br />Cela n'empêche cependant pas l'import du CSV.</p>\n");
		}
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		my_echo("</blockquote>\n");




		//my_echo("<a name='f_men'></a><h2>Génération d'un CSV du F_MEN pour SambaEdu3</h2>\n");
		//my_echo("<a name='f_men'></a><h3>Génération d'un CSV du F_MEN pour SambaEdu3</h3>\n");
		//my_echo("<a name='f_men'></a><h4>Génération d'un CSV du F_MEN pour SambaEdu3</h4>\n");
		my_echo("<a name='f_men'></a><h4>Génération d'un CSV du F_MEN");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h4>\n");
		my_echo("<blockquote>\n");
		if(($temoin_au_moins_une_matiere=="")||($temoin_au_moins_un_prof=="")){
			my_echo("<p>Votre fichier ne comporte pas suffisamment d'informations pour générer ce CSV.<br />Il faut que les emplois du temps soient remontés vers STS pour que le fichier XML permette de générer ce CSV.</p>\n");
		}
		else{
			unset($tab_chaine);
			$tab_chaine=array();

			//if($temoin_creation_fichiers!="non"){$fich=fopen("$dossiercsv/se3/f_men.txt","w+");}
			if($temoin_creation_fichiers!="non"){$fich=fopen("$dossiercsv/f_men.txt","w+");}else{$fich=FALSE;}
			for($i=0;$i<count($divisions);$i++){
				//$divisions[$i]["services"][$j]["code_matiere"]
				$classe=$divisions[$i]["code"];
				for($j=0;$j<count($divisions[$i]["services"]);$j++){
					$mat="";
					for($m=0;$m<count($matiere);$m++){
						if($matiere[$m]["code"]==$divisions[$i]["services"][$j]["code_matiere"]){
							$mat=$matiere[$m]["code_gestion"];
						}
					}
					if($mat!=""){
						for($k=0;$k<count($divisions[$i]["services"][$j]["enseignants"]);$k++){
							$chaine=$mat."|".$classe."|P".$divisions[$i]["services"][$j]["enseignants"][$k]["id"];
							if($fich){
								//fwrite($fich,$chaine."\n");
								fwrite($fich,html_entity_decode($chaine)."\n");
							}
							my_echo($chaine."<br />\n");
							$tab_chaine[]=$chaine;
						}
					}
				}
			}


			//if($_POST['se3_groupes']=='yes'){
			// PROBLEME: On crée des groupes avec tous les membres de la classe...
				//my_echo("<hr width='200' />\n");
				for($i=0;$i<count($groupes);$i++){
					$grocod=$groupes[$i]["code"];
					//my_echo("<p>Groupe $i: \$grocod=$grocod<br />\n");
					for($m=0;$m<count($matiere);$m++){
						//my_echo("\$matiere[$m][\"code\"]=".$matiere[$m]["code"]." et \$groupes[$i][\"code_matiere\"]=".$groupes[$i]["code_matiere"]."<br />\n");
						if(isset($groupes[$i]["code_matiere"])){
							if($matiere[$m]["code"]==$groupes[$i]["code_matiere"]){
								//$matimn=$programme[$k]["code_matiere"];
								$matimn=$matiere[$m]["code_gestion"];
								//my_echo("<b>Trouvé: matière n°$m: \$matimn=$matimn</b><br />\n");
							}
						}
					}
					//$groupes[$i]["enseignant"][$m]["id"]
					//$groupes[$i]["divisions"][$j]["code"]
					if($matimn!=""){
						for($j=0;$j<count($groupes[$i]["divisions"]);$j++){
							$elstco=$groupes[$i]["divisions"][$j]["code"];
							//my_echo("\$elstco=$elstco<br />\n");
							if(!isset($groupes[$i]["enseignant"])){
								$chaine=$matimn."|".$elstco."|";
								$tab_chaine[]=$chaine;
							}
							else{
								if(count($groupes[$i]["enseignant"])==0){
									//$chaine="$matimn;;$elstco");
									$chaine=$matimn."|".$elstco."|";
									/*
									if($fich){
										fwrite($fich,html_entity_decode($chaine)."\n");
									}
									my_echo($chaine."<br />\n");
									*/
									$tab_chaine[]=$chaine;

								}
								else{
									for($m=0;$m<count($groupes[$i]["enseignant"]);$m++){
										$numind=$groupes[$i]["enseignant"][$m]["id"];
										//my_echo("$matimn;P$numind;$elstco<br />\n");
										//$chaine="$matimn;P$numind;$elstco";
										$chaine=$matimn."|".$elstco."|P".$numind;
										/*
										if($fich){
											fwrite($fich,html_entity_decode($chaine)."\n");
										}
										my_echo($chaine."<br />\n");
										*/
										$tab_chaine[]=$chaine;
									}
								}
							}
							//my_echo($grocod.";".$groupes[$i]["divisions"][$j]["code"]."<br />\n");
						}
					}
				}
			//}

			$tab2_chaine=array_unique($tab_chaine);
			//for($i=0;$i<count($tab2_chaine);$i++){
			for($i=0;$i<count($tab_chaine);$i++){
				if(isset($tab2_chaine[$i])){
					if($tab2_chaine[$i]!=""){
						if($fich){
							fwrite($fich,html_entity_decode($tab2_chaine[$i])."\n");
						}
						my_echo($tab2_chaine[$i]."<br />\n");
					}
				}
			}
			if($fich){
				fclose($fich);
			}
			if($temoin_creation_fichiers!="non"){
				//my_echo("disk_total_space($dossiercsv)=".disk_total_space($dossiercsv)."<br />");
				my_echo("<script type='text/javascript'>
	document.getElementById('id_f_men_txt').style.display='';
</script>");
			}

		}
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
		my_echo("</blockquote>\n");

	}
	else{
		//my_echo("<p>ERREUR lors de l'ouverture du fichier ".$sts_xml_file['name']." (<i>".$sts_xml_file['tmp_name']."</i>).</p>\n");
		my_echo("<p>ERREUR lors de l'ouverture du fichier '$sts_xml_file'.</p>\n");
	}

	if($temoin_creation_fichiers!="non"){
		my_echo("<script type='text/javascript'>
	document.getElementById('id_suppr_txt').style.display='';
</script>");
	}



	// =========================================================

	// Création d'une sauvegarde:
	// Problème avec l'emplacement dans lequel www-se3 peut écrire...
	//if($fich=fopen("/var/se3/save/sauvegarde_ldap.sh","w+")){

/*
	if($fich=fopen("/var/remote_adm/sauvegarde_ldap.sh","w+")){
		fwrite($fich,'#!/bin/bash
date=$(date +%Y%m%d-%H%M%S)
#dossier_svg="/var/se3/save/sauvegarde_ldap_avant_import"
dossier_svg="/var/remote_adm/sauvegarde_ldap_avant_import"
mkdir -p $dossier_svg

BASEDN=$(cat /etc/ldap/ldap.conf | grep "^BASE" | tr "\t" " " | sed -e "s/ \{2,\}/ /g" | cut -d" " -f2)
ROOTDN=$(cat /etc/ldap/slapd.conf | grep "^rootdn" | tr "\t" " " | cut -d\'"\' -f2)
PASSDN=$(cat /etc/ldap.secret)

#source /etc/ssmtp/ssmtp.conf

echo "Erreur lors de la sauvegarde de précaution effectuée avant import.
Le $date" > /tmp/erreur_svg_prealable_ldap_${date}.txt
# Le fichier d erreur est généré quoi qu il arrive, mais il n est expédié qu en cas de problème de sauvegarde
/usr/bin/ldapsearch -xLLL -D $ROOTDN -w $PASSDN > $dossier_svg/ldap_${date}.ldif || mail root -s "Erreur sauvegarde LDAP" < /tmp/erreur_svg_prealable_ldap_${date}.txt
rm -f /tmp/erreur_svg_prealable_ldap_${date}.txt
');
		fclose($fich);
		exec("/bin/bash /var/se3/save/sauvegarde_ldap.sh",$retour);
	}
*/

		exec("/usr/bin/sudo $pathscripts/sauvegarde_ldap_avant_import.sh",$retour);

	// =========================================================


	if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}

	my_echo("</blockquote>\n");



	my_echo("<p>Retour au <a href='#menu'>menu</a>.</p>\n");



	my_echo("<a name='profs_se3'></a>\n");
	my_echo("<a name='creer_profs'></a>\n");
	my_echo("<h3>Création des comptes professeurs");
	if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
	my_echo("</h3>\n");
	my_echo("<script type='text/javascript'>
	document.getElementById('id_creer_profs').style.display='';
</script>");
	my_echo("<blockquote>\n");
	$cpt=0;
	while($cpt<count($prof)){
		if($prof[$cpt]["fonction"]=="ENS"){
			// Pour chaque prof:
			//$chaine="P".$prof[$cpt]["id"]."|".$prof[$cpt]["nom_usage"]."|".$prof[$cpt]["prenom"]."|".$date."|".$prof[$cpt]["sexe"]
			// Témoin d'échec de création du compte prof
			$temoin_erreur_prof="";
			$date=str_replace("-","",$prof[$cpt]["date_naissance"]);
			$employeeNumber="P".$prof[$cpt]["id"];
			if($tab=verif_employeeNumber($employeeNumber)){
				my_echo("<p>Uid existant pour employeeNumber=$employeeNumber: $tab[0]<br />\n");
				$uid=$tab[0];

				// ================================
				// Vérification/correction du GECOS
				if($corriger_gecos_si_diff=='y') {
					$nom=remplace_accents(traite_espaces($prof[$cpt]["nom_usage"]));
					$prenom=remplace_accents(traite_espaces($prof[$cpt]["prenom"]));
					if($prof[$cpt]["sexe"]==1){$sexe="M";}else{$sexe="F";}
					$naissance=$date;
					verif_et_corrige_gecos($uid,$nom,$prenom,$naissance,$sexe);
				}
				// ================================

				// ================================
				// Vérification/correction du givenName
				if($corriger_givenname_si_diff=='y') {
					$prenom=strtolower(remplace_accents(traite_espaces($prof[$cpt]["prenom"])));
					//my_echo("Test de la correction du givenName: verif_et_corrige_givenname($uid,$prenom)<br />\n");
					verif_et_corrige_givenname($uid,$prenom);
				}
				// ================================

				// ================================
				// Vérification/correction du pseudo
				//if($annuelle=="y") {
					if($controler_pseudo=='y') {
						$nom=remplace_accents(traite_espaces($prof[$cpt]["nom_usage"]));
						$prenom=strtolower(remplace_accents(traite_espaces($prof[$cpt]["prenom"])));
						verif_et_corrige_pseudo($uid,$nom,$prenom);
					}
				//}
				// ================================

			}
			else{
				my_echo("<p>Pas encore d'uid pour employeeNumber=$employeeNumber<br />\n");

				//$prenom=remplace_accents($prof[$cpt]["prenom"]);
				//$nom=remplace_accents($prof[$cpt]["nom_usage"]);
				$prenom=remplace_accents(traite_espaces($prof[$cpt]["prenom"]));
				$nom=remplace_accents(traite_espaces($prof[$cpt]["nom_usage"]));
				if($uid=verif_nom_prenom_sans_employeeNumber($nom,$prenom)){
					my_echo("$nom $prenom est dans l'annuaire sans employeeNumber: $uid<br />\n");
					my_echo("Mise à jour avec l'employeeNumber $employeeNumber: \n");
					//$comptes_avec_employeeNumber_mis_a_jour++;

					if($simulation!="y"){
						$attributs=array();
						$attributs["employeeNumber"]=$employeeNumber;
						if(modify_attribut ("uid=$uid", "people", $attributs, "add")){
							my_echo("<font color='green'>SUCCES</font>");
							$comptes_avec_employeeNumber_mis_a_jour++;
							$tab_comptes_avec_employeeNumber_mis_a_jour[]=$uid;
						}
						else{
							my_echo("<font color='red'>ECHEC</font>");
							$nb_echecs++;
						}
						my_echo(".<br />\n");
					}
					else{
						my_echo("<font color='blue'>SIMULATION</font>");
						$comptes_avec_employeeNumber_mis_a_jour++;
						$tab_comptes_avec_employeeNumber_mis_a_jour[]=$uid;
					}
				}
				else{
					my_echo("Il n'y a pas de $nom $prenom dans l'annuaire sans employeeNumber<br />\n");
					my_echo("C'est donc un <b>nouveau compte</b>.<br />\n");
					//$nouveaux_comptes++;

					// Création d'un uid:
					if(!$uid=creer_uid($nom,$prenom)){
						$temoin_erreur_prof="o";
						my_echo("<font color='red'>ECHEC: Problème lors de la création de l'uid...</font><br />\n");
						if("$error"!=""){
							my_echo("<font color='red'>$error</font><br />\n");
						}
						$nb_echecs++;
					}
					else{
						/*
						// Récupération du premier uidNumber libre: C'EST FAIT DANS add_user()
						$uidNumber=get_first_free_uidNumber();
						// AJOUTER DES TESTS SUR LE FAIT QU'IL RESTE OU NON DES uidNumber dispo...
						*/
						//$sexe=$prof[$cpt]["sexe"];
						if($prof[$cpt]["sexe"]==1){$sexe="M";}else{$sexe="F";}
						$naissance=$date;
						$password=$naissance;
						my_echo("Ajout du professeur $prenom $nom (<i>$uid</i>): ");
						if($simulation!="y"){
							if(add_user($uid,$nom,$prenom,$sexe,$naissance,$password,$employeeNumber)){
								my_echo("<font color='green'>SUCCES</font>");
								$nouveaux_comptes++;
								$tab_nouveaux_comptes[]=$uid;
							}
							else{
								my_echo("<font color='red'>ECHEC</font>");
								$nb_echecs++;
								$temoin_erreur_prof="o";
							}
						}
						else{
							my_echo("<font color='blue'>SIMULATION</font>");
							$nouveaux_comptes++;
							$tab_nouveaux_comptes[]=$uid;
						}
						my_echo("<br />\n");
					}
				}
			}
			if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}

			if($temoin_erreur_prof!="o"){
				// Ajout au groupe Profs:
				$attribut=array("memberuid");
				$memberUid=get_tab_attribut("groups", "(&(cn=Profs)(memberuid=$uid))", $attribut);
				if(count($memberUid)>0){
					my_echo("$uid est déjà membre du groupe Profs.<br />\n");
				}
				else{
					my_echo("Ajout de $uid au groupe Profs: ");
					if($simulation!="y"){
						$attributs=array();
						$attributs["memberuid"]=$uid;
						if(modify_attribut ("cn=Profs", "groups", $attributs, "add")){
							my_echo("<font color='green'>SUCCES</font>");
						}
						else{
							my_echo("<font color='red'>ECHEC</font>");
							$nb_echecs++;
						}
					}
					else{
						my_echo("<font color='blue'>SIMULATION</font>");
					}
					my_echo(".<br />\n");
				}
			}
			//$chaine="P".$prof[$cpt]["id"]."|".$prof[$cpt]["nom_usage"]."|".$prof[$cpt]["prenom"]."|".$date."|".$prof[$cpt]["sexe"];
		}
		$cpt++;
	}
	if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
	my_echo("</blockquote>\n");





	my_echo("<p>Retour au <a href='#menu'>menu</a>.</p>\n");

	my_echo("<a name='creer_eleves'></a>\n");
	my_echo("<a name='eleves_se3'></a>\n");
	//my_echo("<h2>Création des comptes élèves</h2>\n");
	//my_echo("<h3>Création des comptes élèves</h3>\n");
	my_echo("<h3>Création des comptes élèves");
	if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
	my_echo("</h3>\n");
	my_echo("<script type='text/javascript'>
	document.getElementById('id_creer_eleves').style.display='';
</script>");
	my_echo("<blockquote>\n");
	$tab_classe=array();
	$cpt_classe=-1;
	for($k=0;$k<count($tabnumero);$k++){
		$temoin_erreur_eleve="n";

		$numero=$tabnumero[$k];
		/*
		$chaine="";
		$chaine.=$eleve[$numero]["numero"];
		$chaine.="|";
		$chaine.=remplace_accents($eleve[$numero]["nom"]);
		$chaine.="|";
		$chaine.=remplace_accents($eleve[$numero]["prenom"]);
		$chaine.="|";
		$chaine.=$eleve[$numero]["date"];
		$chaine.="|";
		$chaine.=$eleve[$numero]["sexe"];
		$chaine.="|";
		$chaine.=$eleve[$numero]["division"];
		*/
		/*
		if($fich){
			//fwrite($fich,$chaine."\n");
			fwrite($fich,html_entity_decode($chaine)."\n");
		}
		*/


		// La classe existe-t-elle?
		$div=$eleve[$numero]["division"];
		$div=ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($div)));
		$attribut=array("cn");
		//$cn_classe=get_tab_attribut("groups", "cn=Classe_$div", $attribut);
		$cn_classe=get_tab_attribut("groups", "cn=Classe_".$prefix."$div", $attribut);
		if(count($cn_classe)==0){
			// La classe n'existe pas dans l'annuaire.

			// LE TEST CI-DESSOUS NE CONVIENT PLUS AVEC UN TABLEAU A PLUSIEURS DIMENSIONS... A CORRIGER
			//if(!in_array($div,$tab_classe)){

			$temoin_classe="";
			for($i=0;$i<count($tab_classe);$i++){
				if($tab_classe[$i]["nom"]==$div){
					$temoin_classe="y";
				}
			}

			if($temoin_classe!="y"){
				// On ajoute la classe à créér.
				$cpt_classe++;
				my_echo("<p>Nouvelle classe: $div</p>\n");
				$tab_classe[$cpt_classe]=array();
				$tab_classe[$cpt_classe]["nom"]=$div;
				$tab_classe[$cpt_classe]["creer_classe"]="y";
				$tab_classe[$cpt_classe]["eleves"]=array();
			}
		}
		else{
			// La classe existe déjà dans l'annuaire.

			$temoin_classe="";
			for($i=0;$i<count($tab_classe);$i++){
				if($tab_classe[$i]["nom"]==$div){
					$temoin_classe="y";
				}
			}

			if($temoin_classe!="y"){
				// On ajoute la classe à créér.
				$cpt_classe++;
				my_echo("<p>Classe existante: $div</p>\n");
				$tab_classe[$cpt_classe]=array();
				$tab_classe[$cpt_classe]["nom"]=$div;
				$tab_classe[$cpt_classe]["creer_classe"]="n";
				$tab_classe[$cpt_classe]["eleves"]=array();
			}
		}


		// Pour chaque élève:
		$employeeNumber=$eleve[$numero]["numero"];
		if($tab=verif_employeeNumber($employeeNumber)){
			my_echo("<p>Uid existant pour employeeNumber=$employeeNumber: $tab[0]<br />\n");
			$uid=$tab[0];

			// ================================
			// Vérification/correction du GECOS
			if($corriger_gecos_si_diff=='y') {
				$nom=remplace_accents(traite_espaces($eleve[$numero]["nom"]));
				$prenom=remplace_accents(traite_espaces($eleve[$numero]["prenom"]));
				$sexe=$eleve[$numero]["sexe"];
				$naissance=$eleve[$numero]["date"];
				verif_et_corrige_gecos($uid,$nom,$prenom,$naissance,$sexe);
			}
			// ================================

			// ================================
			// Vérification/correction du givenName
			if($corriger_givenname_si_diff=='y') {
				$prenom=strtolower(remplace_accents(traite_espaces($eleve[$numero]["prenom"])));
				//my_echo("Test de la correction du givenName: verif_et_corrige_givenname($uid,$prenom)<br />\n");
				verif_et_corrige_givenname($uid,$prenom);
			}
			// ================================

			// ================================
			// Vérification/correction du pseudo
			//if($annuelle=="y") {
				if($controler_pseudo=='y') {
					$nom=remplace_accents(traite_espaces($eleve[$numero]["nom"]));
					$prenom=strtolower(remplace_accents(traite_espaces($eleve[$numero]["prenom"])));
					verif_et_corrige_pseudo($uid,$nom,$prenom);
				}
			//}
			// ================================
		}
		else{
			my_echo("<p>Pas encore d'uid pour employeeNumber=$employeeNumber<br />\n");

			//$prenom=remplace_accents($eleve[$numero]["prenom"]);
			//$nom=remplace_accents($eleve[$numero]["nom"]);
			$prenom=remplace_accents(traite_espaces($eleve[$numero]["prenom"]));
			$nom=remplace_accents(traite_espaces($eleve[$numero]["nom"]));
			if($uid=verif_nom_prenom_sans_employeeNumber($nom,$prenom)){
				my_echo("$nom $prenom est dans l'annuaire sans employeeNumber: $uid<br />\n");
				my_echo("Mise à jour avec l'employeeNumber $employeeNumber: \n");
				//$comptes_avec_employeeNumber_mis_a_jour++;

				if($simulation!="y"){
					$attributs=array();
					$attributs["employeeNumber"]=$employeeNumber;
					if(modify_attribut ("uid=$uid", "people", $attributs, "add")){
						my_echo("<font color='green'>SUCCES</font>");
						$comptes_avec_employeeNumber_mis_a_jour++;
						$tab_comptes_avec_employeeNumber_mis_a_jour[]=$uid;
					}
					else{
						my_echo("<font color='red'>ECHEC</font>");
						$nb_echecs++;
					}
				}
				else{
					my_echo("<font color='blue'>SIMULATION</font>");
					$comptes_avec_employeeNumber_mis_a_jour++;
					$tab_comptes_avec_employeeNumber_mis_a_jour[]=$uid;
				}
				my_echo(".<br />\n");
			}
			else{
				my_echo("Il n'y a pas de $nom $prenom dans l'annuaire sans employeeNumber<br />\n");
				my_echo("C'est donc un <b>nouveau compte</b>.<br />\n");
				//$nouveaux_comptes++;

				// Création d'un uid:
				if(!$uid=creer_uid($nom,$prenom)){
					$temoin_erreur_eleve="o";
					my_echo("<font color='red'>ECHEC: Problème lors de la création de l'uid...</font><br />\n");
					if("$error"!=""){
						my_echo("<font color='red'>$error</font><br />\n");
					}
					$nb_echecs++;
				}
				else{
					/*
					// Récupération du premier uidNumber libre: C'EST FAIT DANS add_user()
					$uidNumber=get_first_free_uidNumber();
					// AJOUTER DES TESTS SUR LE FAIT QU'IL RESTE OU NON DES uidNumber dispo...
					*/
					$sexe=$eleve[$numero]["sexe"];
					$naissance=$eleve[$numero]["date"];
					$password=$naissance;
					my_echo("Ajout de l'élève $prenom $nom (<i>$uid</i>): ");
					if($simulation!="y"){
                                                # DBG system ("echo 'add_suser : $uid,$nom,$prenom,$sexe,$naissance,$password,$employeeNumber' >> /tmp/comptes.log");
						if(add_user($uid,$nom,$prenom,$sexe,$naissance,$password,$employeeNumber)){
							my_echo("<font color='green'>SUCCES</font>");
							$nouveaux_comptes++;
							$tab_nouveaux_comptes[]=$uid;
						}
						else{
							my_echo("<font color='red'>ECHEC</font>");
							$temoin_erreur_eleve="o";
							$nb_echecs++;
						}
					}
					else{
						my_echo("<font color='blue'>SIMULATION</font>");
						$nouveaux_comptes++;
						$tab_nouveaux_comptes[]=$uid;
					}
					my_echo("<br />\n");
				}
			}
		}
		if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}

		if($temoin_erreur_eleve!="o"){
			// Ajout au groupe Eleves:
			$attribut=array("memberuid");
			$memberUid=get_tab_attribut("groups", "(&(cn=Eleves)(memberuid=$uid))", $attribut);
			if(count($memberUid)>0){
				my_echo("$uid est déjà membre du groupe Eleves.<br />\n");
			}
			else{
				my_echo("Ajout de $uid au groupe Eleves: ");
				$attributs=array();
				$attributs["memberuid"]=$uid;
				if($simulation!="y"){
					if(modify_attribut ("cn=Eleves", "groups", $attributs, "add")){
						my_echo("<font color='green'>SUCCES</font>");
					}
					else{
						my_echo("<font color='red'>ECHEC</font>");
						$nb_echecs++;
					}
				}
				else{
					my_echo("<font color='blue'>SIMULATION</font>");
				}
				my_echo(".<br />\n");
			}

			// Témoin pour repérer les appartenances à plusieurs classes
			$temoin_plusieurs_classes="n";

			// Ajout de l'élève au tableau de la classe:
			$attribut=array("memberuid");
			$memberUid=get_tab_attribut("groups", "(&(cn=Classe_".$prefix."$div)(memberuid=$uid))", $attribut);
			if(count($memberUid)>0){
				my_echo("$uid est déjà membre de la classe $div.<br />\n");

				// Ajout d'un test:
				// L'élève est-il membre d'autres classes.
				$attribut=array("memberuid");
				$test_memberUid=get_tab_attribut("groups", "(&(cn=Classe_*)(memberuid=$uid))", $attribut);
				if(count($test_memberUid)>1){
					$temoin_plusieurs_classes="y";
				}
			}
			else{
				my_echo("Ajout de $uid au tableau de la classe $div.<br />\n");
				//$tab_classe[$cpt_classe]["eleves"][]=$uid;
				// PROBLEME: Avec l'import XML, les élèves ne sont jamais triés par classes... et ce n'est le cas dans l'import CSV que si on a fait le tri dans ce sens
				// Recherche de l'indice dans tab_classe
				$ind_classe=-1;
				for($i=0;$i<count($tab_classe);$i++){
					if($tab_classe[$i]["nom"]==$div){
						$ind_classe=$i;
					}
				}
				if($ind_classe!=-1){
					$tab_classe[$ind_classe]["eleves"][]=$uid;
				}


				// Ajout d'un test:
				// L'élève est-il membre d'autres classes.
				$attribut=array("memberuid");
				$test_memberUid=get_tab_attribut("groups", "(&(cn=Classe_*)(memberuid=$uid))", $attribut);
				if(count($test_memberUid)>0){
					$temoin_plusieurs_classes="y";
				}
			}

			// Ajout d'un test:
			// L'élève est-il membre d'autres classes.
			if($temoin_plusieurs_classes=="y") {
				$attribut=array("cn");
				$cn_classes_de_l_eleve=get_tab_attribut("groups", "(&(cn=Classe_*)(memberuid=$uid))", $attribut);
				if(count($cn_classes_de_l_eleve)>0){
					for($loop=0;$loop<count($cn_classes_de_l_eleve);$loop++){
						// Exclure Classe_.$prefix.$div
						if($cn_classes_de_l_eleve[$loop]!="Classe_".$prefix.$div){
							my_echo("Suppression de l'appartenance de $uid à la classe ".$cn_classes_de_l_eleve[$loop]." : ");

							unset($attr);
							$attr=array();
							$attr["memberuid"]=$uid;
							if($simulation!="y"){
								if(modify_attribut ("cn=".$cn_classes_de_l_eleve[$loop], "groups", $attr, "del")) {
									my_echo("<font color='green'>SUCCES</font>");
								}
								else{
									my_echo("<font color='red'>ECHEC</font>");
									$nb_echecs++;
								}
							}
							else{
								my_echo("<font color='blue'>SIMULATION</font>");
							}
							my_echo(".<br />\n");
						}
					}
				}
			}
		}


		//my_echo("<font color='green'>".$chaine."</font><br />\n");

		my_echo("</p>\n");
	}
	if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
	my_echo("</blockquote>\n");


	if($simulation=="y"){
		my_echo("<p>Retour au <a href='#menu'>menu</a>.</p>\n");

		my_echo("<a name='fin'></a>\n");
		//my_echo("<h3>Rapport final de simulation</h3>");
		my_echo("<h3>Rapport final de simulation");
		if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
		my_echo("</h3>\n");
		my_echo("<blockquote>\n");
		my_echo("<script type='text/javascript'>
	document.getElementById('id_fin').style.display='';
</script>");



		my_echo("<p>Fin de la simulation!</p>\n");


		$chaine="";
		if($nouveaux_comptes==0){
			//my_echo("<p>Aucun nouveau compte ne serait créé.</p>\n");
			$chaine.="<p>Aucun nouveau compte ne serait créé.</p>\n";
		}
		elseif($nouveaux_comptes==1){
			//my_echo("<p>$nouveaux_comptes nouveau compte serait créé: $tab_nouveaux_comptes[0]</p>\n");
			$chaine.="<p>$nouveaux_comptes nouveau compte serait créé: $tab_nouveaux_comptes[0]</p>\n";
		}
		else{
			/*
			my_echo("<p>$nouveaux_comptes nouveaux comptes seraient créés: ");
			my_echo($tab_nouveaux_comptes[0]);
			for($i=1;$i<count($tab_nouveaux_comptes);$i++){my_echo(", $tab_nouveaux_comptes[$i]");}
			my_echo("</p>\n");
			my_echo("<p><i>Attention:</i> Si un nom de compte est en doublon dans les nouveaux comptes, c'est un bug de la simulation.<br />Le problème ne se produira pas en mode création.</p>\n");
			*/
			$chaine.=$tab_nouveaux_comptes[0];
			for($i=1;$i<count($tab_nouveaux_comptes);$i++){$chaine.=", $tab_nouveaux_comptes[$i]";}
			$chaine.="</p>\n";
			$chaine.="<p><i>Attention:</i> Si un nom de compte est en doublon dans les nouveaux comptes, c'est un bug de la simulation.<br />Le problème ne se produira pas en mode création.</p>\n";
		}


		if($comptes_avec_employeeNumber_mis_a_jour==0){
			//my_echo("<p>Aucun compte existant sans employeeNumber n'aurait été récupéré/corrigé.</p>\n");
			$chaine.="<p>Aucun compte existant sans employeeNumber n'aurait été récupéré/corrigé.</p>\n";
		}
		elseif($comptes_avec_employeeNumber_mis_a_jour==1){
			//my_echo("<p>$comptes_avec_employeeNumber_mis_a_jour compte existant sans employeeNumber aurait été récupéré/corrigé (<i>son employeeNumber serait maintenant renseigné</i>): $tab_comptes_avec_employeeNumber_mis_a_jour[0]</p>\n");
			$chaine.="<p>$comptes_avec_employeeNumber_mis_a_jour compte existant sans employeeNumber aurait été récupéré/corrigé (<i>son employeeNumber serait maintenant renseigné</i>): $tab_comptes_avec_employeeNumber_mis_a_jour[0]</p>\n";
		}
		else{
			/*
			my_echo("<p>$comptes_avec_employeeNumber_mis_a_jour comptes existants sans employeeNumber auraient été récupérés/corrigés (<i>leur employeeNumber serait maintenant renseigné</i>): ");
			my_echo("$tab_comptes_avec_employeeNumber_mis_a_jour[0]");
			for($i=1;$i<count($tab_comptes_avec_employeeNumber_mis_a_jour);$i++){my_echo(", $tab_comptes_avec_employeeNumber_mis_a_jour[$i]");}
			my_echo("</p>\n");
			*/
			$chaine.="<p>$comptes_avec_employeeNumber_mis_a_jour comptes existants sans employeeNumber auraient été récupérés/corrigés (<i>leur employeeNumber serait maintenant renseigné</i>): ";
			$chaine.="$tab_comptes_avec_employeeNumber_mis_a_jour[0]";
			for($i=1;$i<count($tab_comptes_avec_employeeNumber_mis_a_jour);$i++){$chaine.=", $tab_comptes_avec_employeeNumber_mis_a_jour[$i]";}
			$chaine.="</p>\n";
		}


		$chaine.="<p>On ne simule pas la création des groupes... pour le moment.</p>\n";


		my_echo($chaine);


		// Envoi par mail de $chaine et $echo_http_file
                if ( $serveurtype=="SE3") {
		  // Récupérer les adresses,... dans le /etc/ssmtp/ssmtp.conf
		  unset($tabssmtp);
		  $tabssmtp=lireSSMTP();
		  // Contrôler les champs affectés...
		  if(isset($tabssmtp["root"])){
			$adressedestination=$tabssmtp["root"];
			$sujet="[$domain] Rapport de ";
			if($simulation=="y"){$sujet.="simulation de ";}
			$sujet.="création de comptes";
			$message="Import du $debut_import\n";
			$message.="$chaine\n";
			$message.="\n";
			$message.="Vous pouvez consulter le rapport détaillé à l'adresse $echo_http_file\n";
			$entete="From: ".$tabssmtp["root"];
			mail("$adressedestination", "$sujet", "$message", "$entete") or my_echo("<p style='color:red;'><b>ERREUR</b> lors de l'envoi du rapport par mail.</p>\n");
		  } else {
			my_echo("<p style='color:red;'><b>MAIL:</b> La configuration mail ne permet pas d'expédier le rapport.<br />Consultez/renseignez le menu Informations système/Actions sur le serveur/Configurer l'expédition des mails.</p>\n");
		  }
                } else {
                    // Cas du LCS
			$adressedestination="admin@$domain";
			$sujet="[$domain] Rapport de ";
			if($simulation=="y"){$sujet.="simulation de ";}
			$sujet.="création de comptes";
			$message="Import du $debut_import\n";
			$message.="$chaine\n";
			$message.="\n";
			$message.="Vous pouvez consulter le rapport détaillé à l'adresse $echo_http_file\n";
			$entete="From: root@$domain";
			mail("$adressedestination", "$sujet", "$message", "$entete") or my_echo("<p style='color:red;'><b>ERREUR</b> lors de l'envoi du rapport par mail.</p>\n");
                }



		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}

		my_echo("<p><a href='".$www_import."'>Retour</a>.</p>\n");
		my_echo("<script type='text/javascript'>
	compte_a_rebours='n';
</script>\n");
		my_echo("</blockquote>\n");

		my_echo("</body>\n</html>\n");

		// Renseignement du témoin de mise à jour terminée.
		$sql="SELECT value FROM params WHERE name='imprt_cmpts_en_cours'";
		$res1=mysql_query($sql);
		if(mysql_num_rows($res1)==0){
			$sql="INSERT INTO params SET name='imprt_cmpts_en_cours',value='n'";
			$res0=mysql_query($sql);
		}
		else{
			$sql="UPDATE params SET value='n' WHERE name='imprt_cmpts_en_cours'";
			$res0=mysql_query($sql);
		}

		exit();
	}



	// Création des groupes
	my_echo("<p>Retour au <a href='#menu'>menu</a>.</p>\n");

	my_echo("<a name='creer_classes'></a>\n");
	my_echo("<a name='classes_se3'></a>\n");
	//my_echo("<h2>Création des groupes Classes et Equipes</h2>\n");
	//my_echo("<h3>Création des groupes Classes et Equipes</h3>\n");
	my_echo("<h3>Création des groupes Classes et Equipes");
	if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
	my_echo("</h3>\n");
	my_echo("<script type='text/javascript'>
	document.getElementById('id_creer_classes').style.display='';
</script>");
	my_echo("<blockquote>\n");
	// Les groupes classes pour commencer:
	for($i=0;$i<count($tab_classe);$i++){
		$div=$tab_classe[$i]["nom"];
		$temoin_classe="";
		my_echo("<p>");
		if($tab_classe[$i]["creer_classe"]=="y"){
			$attributs=array();
			$attributs["cn"]="Classe_".$prefix."$div";
			//$attributs["objectClass"]="top";
			// MODIF: boireaus 20070728
			$attributs["objectClass"][0]="top";
			$attributs["objectClass"][1]="posixGroup";

			//$attributs["objectClass"]="posixGroup";
			$gidNumber=get_first_free_gidNumber();
			if($gidNumber!=false){
				$attributs["gidNumber"]="$gidNumber";
				// Ou récupérer un nom long du fichier de STS...
				$attributs["description"]="$div";

				//my_echo("<p>Création du groupe classe Classe_".$prefix."$div: ");
				my_echo("Création du groupe classe Classe_".$prefix."$div: ");
				if(add_entry ("cn=Classe_".$prefix."$div", "groups", $attributs)){
					/*
					unset($attributs);
					$attributs=array();
					$attributs["objectClass"]="posixGroup";
					if(modify_attribut("cn=Classe_".$prefix."$div","groups", $attributs, "add")){
					*/
						my_echo("<font color='green'>SUCCES</font>");
					/*
					}
					else{
						my_echo("<font color='red'>ECHEC</font>");
						$temoin_classe="PROBLEME";
						$nb_echecs++;
					}
					*/
				}
				else{
					my_echo("<font color='red'>ECHEC</font>");
					$temoin_classe="PROBLEME";
					$nb_echecs++;
				}
				my_echo("<br />\n");
			}
			else{
				my_echo("<font color='red'>ECHEC</font> Il n'y a plus de gidNumber disponible.<br />\n");
				$temoin_classe="PROBLEME";
				$nb_echecs++;
			}
			if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
		}

		if("$temoin_classe"==""){
			my_echo("Ajout de membres au groupe Classe_".$prefix."$div: ");
			/*
			$attribut=array("memberUid");
			$tabtmp=get_tab_attribut("groups", "cn=Classe_".$prefix."$div", $attribut);
			*/
			for($j=0;$j<count($tab_classe[$i]["eleves"]);$j++){
				$uid=$tab_classe[$i]["eleves"][$j];
				$attribut=array("cn");
				$tabtmp=get_tab_attribut("groups", "(&(cn=Classe_".$prefix."$div)(memberuid=$uid))", $attribut);
				if(count($tabtmp)==0){
					unset($attribut);
					$attribut=array();
					$attribut["memberUid"]=$uid;
					if(modify_attribut("cn=Classe_".$prefix."$div","groups",$attribut,"add")){
						my_echo("<b>$uid</b> ");
					}
					else{
						my_echo("<font color='red'>$uid</font> ");
						$nb_echecs++;
					}
				}
				else{
					my_echo("$uid ");
				}
			}
			my_echo(" (<i>".count($tab_classe[$i]["eleves"])."</i>)\n");
			if($chrono=='y'){my_echo("<br />Fin: ".date_et_heure()."<br />\n");}
		}
		my_echo("</p>\n");

		// Création de l'Equipe?
		//for($i=0;$i<count($tab_classe);$i++){
		//$div=$tab_classe[$i]["nom"];
		$ind=-1;
		$temoin_equipe="";

		// L'équipe existe-t-elle?
		my_echo("<p>");
		$attribut=array("cn");
		$tabtmp=get_tab_attribut("groups", "cn=Equipe_".$prefix."$div", $attribut);
		if(count($tabtmp)==0){
			$attributs=array();
			$attributs["cn"]="Equipe_".$prefix."$div";

			// MODIF: boireaus 20070728
			//$attributs["objectClass"]="top";
			$attributs["objectClass"][0]="top";

			//$attributs["objectClass"]="posixGroup";
			//$attributs["objectClass"]="groupOfNames";
			// On ne peut pas avoir un tableau associatif avec plusieurs fois objectClass

			if($type_Equipe_Matiere=="groupOfNames"){
				// Ou récupérer un nom long du fichier de STS...
				$attributs["description"]="$div";

				// MODIF: boireaus 20070728
				$attributs["objectClass"][1]="groupOfNames";

				my_echo("Création de l'équipe Equipe_".$prefix."$div: ");
				if(add_entry ("cn=Equipe_".$prefix."$div", "groups", $attributs)){
					/*
					unset($attributs);
					$attributs=array();
					$attributs["objectClass"]="groupOfNames";
					//$attributs["objectClass"]="posixGroup";
					if(modify_attribut("cn=Equipe_".$prefix."$div","groups", $attributs, "add")){
					*/
						my_echo("<font color='green'>SUCCES</font>");
					/*
					}
					else{
						my_echo("<font color='red'>ECHEC</font>");
						$temoin_equipe="PROBLEME";
						$nb_echecs++;
					}
					*/
					//my_echo("<font color='green'>SUCCES</font>");
				}
				else{
					my_echo("<font color='red'>ECHEC</font>");
					$temoin_equipe="PROBLEME";
					$nb_echecs++;
				}
			}
			else{
				// Les Equipes sont posix
				$gidNumber=get_first_free_gidNumber();
				if($gidNumber!=false){
					$attributs["gidNumber"]="$gidNumber";

					// Ou récupérer un nom long du fichier de STS...
					$attributs["description"]="$div";

					// MODIF: boireaus 20070728
					$attributs["objectClass"][1]="posixGroup";

					my_echo("Création de l'équipe Equipe_".$prefix."$div: ");
					if(add_entry ("cn=Equipe_".$prefix."$div", "groups", $attributs)){
						/*
						unset($attributs);
						$attributs=array();
						//$attributs["objectClass"]="groupOfNames";
						$attributs["objectClass"]="posixGroup";
						if(modify_attribut("cn=Equipe_".$prefix."$div","groups", $attributs, "add")){
						*/
							my_echo("<font color='green'>SUCCES</font>");
						/*
						}
						else{
							my_echo("<font color='red'>ECHEC</font>");
							$temoin_equipe="PROBLEME";
							$nb_echecs++;
						}
						*/
						//my_echo("<font color='green'>SUCCES</font>");
					}
					else{
						my_echo("<font color='red'>ECHEC</font>");
						$temoin_equipe="PROBLEME";
						$nb_echecs++;
					}
				}
				else{
					my_echo("<font color='red'>ECHEC</font> Il n'y a plus de gidNumber disponible.<br />\n");
					$temoin_equipe="PROBLEME";
					$nb_echecs++;
				}
			}
			my_echo("<br />\n");
			if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
		}

		if($creer_equipes_vides=="y"){
			$temoin_equipe="Remplissage des Equipes non demandé.";
		}

		//my_echo("<p>\$temoin_equipe=$temoin_equipe</p>");

		if($temoin_equipe==""){
			// Recherche de l'indice de la classe dans $divisions
			//my_echo("<font color='yellow'>$div</font> ");
			for($m=0;$m<count($divisions);$m++){
				$tmp_classe=ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($divisions[$m]["code"])));
				//my_echo("<font color='lime'>$tmp_classe</font> ");
				if($tmp_classe==$div){
					$ind=$m;
				}
			}
			//my_echo("ind=$ind<br />");


			if($type_Equipe_Matiere=="groupOfNames"){
				// Les profs principaux ne sont plus gérés comme attribut owner qu'en mode groupOfNames

				// Prof principal
				unset($tab_pp);
				$tab_pp=array();
				for($m=0;$m<count($prof);$m++){
					if(isset($prof[$m]["prof_princ"])){
						for($n=0;$n<count($prof[$m]["prof_princ"]);$n++){
							$tmp_div=$prof[$m]["prof_princ"][$n]["code_structure"];
							$tmp_div=ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($tmp_div)));
							if($tmp_div==$div){
								$employeeNumber="P".$prof[$m]["id"];
								$attribut=array("uid");
								$tabtmp=get_tab_attribut("people", "employeenumber=$employeeNumber", $attribut);
								if(count($tabtmp)!=0){
									$uid=$tabtmp[0];
									if(!in_array($uid,$tab_pp)){
										$tab_pp[]=$uid;
									}
								}
							}
						}
					}
				}
				sort($tab_pp);

				if(count($tab_pp)>0){
					if(count($tab_pp)==1){
						my_echo("Ajout du professeur principal à l'équipe Equipe_".$prefix."$div: ");
					}
					else{
						my_echo("Ajout des professeurs principaux à l'équipe Equipe_".$prefix."$div: ");
					}
					for($m=0;$m<count($tab_pp);$m++){
						$uid=$tab_pp[$m];
						// Est-il déjà PP de la classe?
						$attribut=array("owner");
						//$tabtmp=get_tab_attribut("people", "member=uid=$uid,".$dn["people"], $attribut);
						$tabtmp=get_tab_attribut("groups", "(&(cn=Equipe_".$prefix."$div)(owner=uid=$uid,".$dn["people"]."))", $attribut);
						//$tabtmp=get_tab_attribut("groups", "(&(cn=Equipe_".$prefix."$div)(owner=$uid))", $attribut);
						if(count($tabtmp)==0){
							$attributs=array();
							$attributs["owner"]="uid=$uid,".$dn["people"];
							//$attributs["owner"]="$uid";
							if(modify_attribut("cn=Equipe_".$prefix."$div", "groups", $attributs, "add")){
								my_echo("<b>$uid</b> ");
							}
							else{
								my_echo("<font color='red'>$uid</font> ");
								$nb_echecs++;
							}
						}
						else{
							my_echo("$uid ");
						}
					}
					my_echo("<br />\n");
					if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
				}
			}

			// Membres de l'équipe
			unset($tab_equipe);
			$tab_equipe=array();
			my_echo("Ajout de membres à l'équipe Equipe_".$prefix."$div: ");
			for($j=0;$j<count($divisions[$ind]["services"]);$j++){
				for($k=0;$k<count($divisions[$ind]["services"][$j]["enseignants"]);$k++){
					// Récupérer le login correspondant au NUMIND
					$employeeNumber="P".$divisions[$ind]["services"][$j]["enseignants"][$k]["id"];
					if(!in_array($employeeNumber,$tab_equipe)){
						$tab_equipe[]=$employeeNumber;
						//my_echo("\$employeeNumber=$employeeNumber<br />");

						/*
						$attribut=array("uid");
						$tabtmp=get_tab_attribut("people", "employeenumber=$employeeNumber", $attribut);
						if(count($tabtmp)!=0){
							$uid=$tabtmp[0];
							//my_echo("\$uid=$uid<br />");
							// Le prof est-il déjà membre de l'équipe?
							$attribut=array("member");
							//$tabtmp=get_tab_attribut("people", "member=uid=$uid,".$dn["people"], $attribut);
							$tabtmp=get_tab_attribut("groups", "(&(cn=Equipe_".$prefix."$div)(member=uid=$uid,".$dn["people"]."))", $attribut);
							if(count($tabtmp)==0){
								$attributs=array();
								$attributs["member"]="uid=$uid,".$dn["people"];
								if(modify_attribut("cn=Equipe_".$prefix."$div", "groups", $attributs, "add")){
									my_echo("<b>$uid</b> ");
								}
								else{
									my_echo("<font color='red'>$uid</font> ");
								}
							}
							else{
								my_echo("$uid ");
							}
						}
						*/
					}
				}
			}



			// Rechercher les groupes associés à la classe pour affecter les collègues dans l'équipe
			//$groupes[$i]["divisions"][$j]["code"]	-> 3 A1
			//$groupes[$i]["code_matiere"]			-> 070800
			//$groupes[$i]["enseignant"][$m]["id"]	-> 38101
			for($n=0;$n<count($groupes);$n++){
				for($j=0;$j<count($groupes[$n]["divisions"]);$j++){
					$grp_div=$groupes[$n]["divisions"][$j]["code"];
					$grp_div=ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($grp_div)));
					if($grp_div==$div){
						/*
						if(isset($groupes[$n]["enseignant"])){
							for($m=0;$m<count($groupes[$n]["enseignant"]);$m++){
								$employeeNumber="P".$groupes[$n]["enseignant"][$m]["id"];
								if(!in_array($employeeNumber,$tab_equipe)){
									$tab_equipe[]=$employeeNumber;
								}
							}
						}
						*/

						if(isset($groupes[$n]["service"][0]["enseignant"])){
							for($p=0;$p<count($groupes[$n]["service"]);$p++){
								for($m=0;$m<count($groupes[$n]["service"][$p]["enseignant"]);$m++){
									$employeeNumber="P".$groupes[$n]["service"][$p]["enseignant"][$m]["id"];
									if(!in_array($employeeNumber,$tab_equipe)){
										$tab_equipe[]=$employeeNumber;
									}
								}
							}
						}
					}
				}
			}

			/*
			// On dédoublonne le tableau $tab_equipe
			// On fait un tri sur l'employeeNumber... ce n'est pas très utile.
			sort($tab_equipe);
			$tmp_tab_equipe=$tab_equipe;
			unset($tab_equipe);
			$tab_equipe=array();
			for($n=0;$n<count($tmp_tab_equipe);$n++){
				if(!in_array($tmp_tab_equipe[$n],$tab_equipe)){$tab_equipe[]=$tmp_tab_equipe[$n];}
			}
			*/

			for($n=0;$n<count($tab_equipe);$n++){
				$employeeNumber=$tab_equipe[$n];

				$attribut=array("uid");
				$tabtmp=get_tab_attribut("people", "employeenumber=$employeeNumber", $attribut);
				if(count($tabtmp)!=0){
					$uid=$tabtmp[0];
					//my_echo("\$uid=$uid<br />");
					// Le prof est-il déjà membre de l'équipe?
					//$attribut=array("member");
					//$attribut=array("memberuid");
					//$tabtmp=get_tab_attribut("people", "member=uid=$uid,".$dn["people"], $attribut);

					if($type_Equipe_Matiere=="groupOfNames"){
						// Les groupes Equipes sont groupOfNames
						$attribut=array("member");
						$tabtmp=get_tab_attribut("groups", "(&(cn=Equipe_".$prefix."$div)(member=uid=$uid,".$dn["people"]."))", $attribut);
						//$tabtmp=get_tab_attribut("groups", "(&(cn=Equipe_".$prefix."$div)(memberuid=$uid))", $attribut);
						if(count($tabtmp)==0){
							$attributs=array();
							$attributs["member"]="uid=$uid,".$dn["people"];
							//$attributs["memberuid"]="$uid";
							if(modify_attribut("cn=Equipe_".$prefix."$div", "groups", $attributs, "add")){
								my_echo("<b>$uid</b> ");
							}
							else{
								my_echo("<font color='red'>$uid</font> ");
								$nb_echecs++;
							}
						}
						else{
							my_echo("$uid ");
						}
					}
					else{
						// Les groupes Equipes sont posix
						//$tabtmp=get_tab_attribut("groups", "(&(cn=Equipe_".$prefix."$div)(member=uid=$uid,".$dn["people"]."))", $attribut);
						$attribut=array("memberuid");
						$tabtmp=get_tab_attribut("groups", "(&(cn=Equipe_".$prefix."$div)(memberuid=$uid))", $attribut);
						if(count($tabtmp)==0){
							$attributs=array();
							//$attributs["member"]="uid=$uid,".$dn["people"];
							$attributs["memberuid"]="$uid";
							if(modify_attribut("cn=Equipe_".$prefix."$div", "groups", $attributs, "add")){
								my_echo("<b>$uid</b> ");
							}
							else{
								my_echo("<font color='red'>$uid</font> ");
								$nb_echecs++;
							}
						}
						else{
							my_echo("$uid ");
						}
					}
				}
			}
			my_echo("<br />\n");
			if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
		}
		my_echo("</p>\n");
		//}
	}

	if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
	my_echo("</blockquote>\n");



	my_echo("<p>Retour au <a href='#menu'>menu</a>.</p>\n");

	my_echo("<a name='creer_matieres'></a>\n");
	//my_echo("<h2>Création des groupes Matières</h2>\n");
	//my_echo("<h3>Création des groupes Matières</h3>\n");
	my_echo("<h3>Création des groupes Matières");
	if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}

	// ===========================================================
	// AJOUTS: 20070914 boireaus
	if($creer_matieres=='y'){

		my_echo("</h3>\n");
		my_echo("<script type='text/javascript'>
		document.getElementById('id_creer_matieres').style.display='';
</script>");
		my_echo("<blockquote>\n");
		for($i=0;$i<count($matiere);$i++){
			my_echo("<p>\n");
			$temoin_matiere="";
			//$matiere[$i]["code_gestion"]
			$id_mat=$matiere[$i]["code"];
			//$code_gestion=$matiere[$i]["code_gestion"];
			// En principe les caractères spéciaux ont-été filtrés:
			//$matiere[$i]["code_gestion"]=trim(ereg_replace("[^a-zA-Z0-9&_. -]","",html_entity_decode($tabtmp[2])));
			$mat=$matiere[$i]["code_gestion"];
			$description=remplace_accents($matiere[$i]["libelle_long"]);
			// Faudrait-il enlever d'autres caractères?

			// Le groupe Matiere existe-t-il?
			$attribut=array("cn");
			$tabtmp=get_tab_attribut("groups", "cn=Matiere_".$prefix."$mat", $attribut);
			if(count($tabtmp)==0){
				$attributs=array();
				$attributs["cn"]="Matiere_".$prefix."$mat";

				// MODIF: boireaus 20070728
				//$attributs["objectClass"]="top";
				$attributs["objectClass"][0]="top";

				//$attributs["objectClass"]="posixGroup";
				//$attributs["objectClass"]="groupOfNames";

				// Ou récupérer un nom long du fichier de STS...
				$attributs["description"]="$description";

				if($type_Equipe_Matiere=="groupOfNames"){
					// Les groupes Matieres sont groupOfNames

					// MODIF: boireaus 20070728
					$attributs["objectClass"][1]="groupOfNames";

					//my_echo("<p>Création de la matière Matiere_".$prefix."$mat: ");
					my_echo("Création de la matière Matiere_".$prefix."$mat: ");
					if(add_entry ("cn=Matiere_".$prefix."$mat", "groups", $attributs)){
						/*
						unset($attributs);
						$attributs=array();
						$attributs["objectClass"]="groupOfNames";
						//$attributs["objectClass"]="posixGroup";
						if(modify_attribut("cn=Matiere_".$prefix."$mat","groups", $attributs, "add")){
						*/
							my_echo("<font color='green'>SUCCES</font>");
						/*
						}
						else{
							my_echo("<font color='red'>ECHEC</font>");
							$temoin_matiere="PROBLEME";
							$nb_echecs++;
						}
						*/
						//my_echo("<font color='green'>SUCCES</font>");
					}
					else{
						my_echo("<font color='red'>ECHEC</font>");
						$temoin_matiere="PROBLEME";
						$nb_echecs++;
					}
				}
				else{
					// Les groupes Matieres sont posix
					$gidNumber=get_first_free_gidNumber();
					if($gidNumber!=false){
						$attributs["gidNumber"]="$gidNumber";

						// MODIF: boireaus 20070728
						$attributs["objectClass"][1]="posixGroup";

						//my_echo("<p>Création de la matière Matiere_".$prefix."$mat: ");
						my_echo("Création de la matière Matiere_".$prefix."$mat: ");
						if(add_entry ("cn=Matiere_".$prefix."$mat", "groups", $attributs)){
							/*
							unset($attributs);
							$attributs=array();
							//$attributs["objectClass"]="groupOfNames";
							$attributs["objectClass"]="posixGroup";
							if(modify_attribut("cn=Matiere_".$prefix."$mat","groups", $attributs, "add")){
							*/
								my_echo("<font color='green'>SUCCES</font>");
							/*
							}
							else{
								my_echo("<font color='red'>ECHEC</font>");
								$temoin_matiere="PROBLEME";
								$nb_echecs++;
							}
							*/
							//my_echo("<font color='green'>SUCCES</font>");
						}
						else{
							my_echo("<font color='red'>ECHEC</font>");
							$temoin_matiere="PROBLEME";
							$nb_echecs++;
						}
					}
					else{
						my_echo("<font color='red'>ECHEC</font> Il n'y a plus de gidNumber disponible.<br />\n");
						$temoin_matiere="PROBLEME";
						$nb_echecs++;
					}
				}
				my_echo("<br />\n");
				if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
			}

			unset($tab_matiere);
			$tab_matiere=array();
			if($temoin_matiere==""){
				my_echo("Ajout de membres à la matière Matiere_".$prefix."$mat: ");
				for($n=0;$n<count($divisions);$n++){
					for($j=0;$j<count($divisions[$n]["services"]);$j++){
						if($divisions[$n]["services"][$j]["code_matiere"]==$id_mat){
							for($k=0;$k<count($divisions[$n]["services"][$j]["enseignants"]);$k++){
								// Récupérer le login correspondant au NUMIND
								$employeeNumber="P".$divisions[$n]["services"][$j]["enseignants"][$k]["id"];
								//my_echo("\$employeeNumber=$employeeNumber<br />");
								if(!in_array($employeeNumber,$tab_matiere)){
									$tab_matiere[]=$employeeNumber;
									/*
									$attribut=array("uid");
									$tabtmp=get_tab_attribut("people", "employeenumber=$employeeNumber", $attribut);
									if(count($tabtmp)!=0){
										$uid=$tabtmp[0];
										//my_echo("\$uid=$uid<br />");
										// Le prof est-il déjà membre de l'équipe?
										$attribut=array("member");
										//$tabtmp=get_tab_attribut("people", "member=uid=$uid,".$dn["people"], $attribut);
										$tabtmp=get_tab_attribut("groups", "(&(cn=Matiere_".$prefix."$mat)(member=uid=$uid,".$dn["people"]."))", $attribut);
										if(count($tabtmp)==0){
											$attributs=array();
											$attributs["member"]="uid=$uid,".$dn["people"];
											if(modify_attribut("cn=Matiere_".$prefix."$mat", "groups", $attributs, "add")){
												my_echo("<b>$uid</b> ");
											}
											else{
												my_echo("<font color='red'>$uid</font> ");
											}
										}
										else{
											my_echo("$uid ");
										}
									}
									*/
								}
							}
						}
					}
				}



				// Rechercher les groupes associés à la matière pour affecter les collègues dans l'équipe
				//$groupes[$i]["divisions"][$j]["code"]	-> 3 A1
				//$groupes[$i]["code_matiere"]			-> 070800
				//$groupes[$i]["enseignant"][$m]["id"]	-> 38101
				for($n=0;$n<count($groupes);$n++){
					/*
					if(isset($groupes[$n]["code_matiere"])){
						$grp_id_mat=$groupes[$n]["code_matiere"];
						if($grp_id_mat==$id_mat){
							for($j=0;$j<count($groupes[$n]["divisions"]);$j++){
								if(isset($groupes[$n]["enseignant"])){
									for($m=0;$m<count($groupes[$n]["enseignant"]);$m++){
										$employeeNumber="P".$groupes[$n]["enseignant"][$m]["id"];
										if(!in_array($employeeNumber,$tab_matiere)){
											$tab_matiere[]=$employeeNumber;
										}
									}
								}
							}
						}
					}
					*/

					if(isset($groupes[$n]["service"][0]["code_matiere"])){
						for($p=0;$p<count($groupes[$n]["service"]);$p++){
							$grp_id_mat=$groupes[$n]["service"][$p]["code_matiere"];
							if($grp_id_mat==$id_mat){
								for($j=0;$j<count($groupes[$n]["divisions"]);$j++){
									if(isset($groupes[$n]["service"][$p]["enseignant"])){
										for($m=0;$m<count($groupes[$n]["service"][$p]["enseignant"]);$m++){
											$employeeNumber="P".$groupes[$n]["service"][$p]["enseignant"][$m]["id"];
											if(!in_array($employeeNumber,$tab_matiere)){
												$tab_matiere[]=$employeeNumber;
											}
										}
									}
								}
							}
						}
					}
				}



				for($n=0;$n<count($tab_matiere);$n++){
					$employeeNumber=$tab_matiere[$n];
					$attribut=array("uid");
					$tabtmp=get_tab_attribut("people", "employeenumber=$employeeNumber", $attribut);
					if(count($tabtmp)!=0){
						$uid=$tabtmp[0];
						//my_echo("\$uid=$uid<br />");
						// Le prof est-il déjà membre de la matière?
						if($type_Equipe_Matiere=="groupOfNames"){
							// Les groupes Matieres sont groupOfNames
							$attribut=array("member");
							//$attribut=array("memberuid");
							//$tabtmp=get_tab_attribut("people", "member=uid=$uid,".$dn["people"], $attribut);
							$tabtmp=get_tab_attribut("groups", "(&(cn=Matiere_".$prefix."$mat)(member=uid=$uid,".$dn["people"]."))", $attribut);
							//$tabtmp=get_tab_attribut("groups", "(&(cn=Matiere_".$prefix."$mat)(memberuid=$uid))", $attribut);
							if(count($tabtmp)==0){
								$attributs=array();
								$attributs["member"]="uid=$uid,".$dn["people"];
								//$attributs["memberuid"]="$uid";
								if(modify_attribut("cn=Matiere_".$prefix."$mat", "groups", $attributs, "add")){
									my_echo("<b>$uid</b> ");
								}
								else{
									my_echo("<font color='red'>$uid</font> ");
									$nb_echecs++;
								}
							}
							else{
								my_echo("$uid ");
							}
						}
						else{
							// Les groupes Matieres sont posix
							//$attribut=array("member");
							$attribut=array("memberuid");
							//$tabtmp=get_tab_attribut("people", "member=uid=$uid,".$dn["people"], $attribut);
							//$tabtmp=get_tab_attribut("groups", "(&(cn=Matiere_".$prefix."$mat)(member=uid=$uid,".$dn["people"]."))", $attribut);
							$tabtmp=get_tab_attribut("groups", "(&(cn=Matiere_".$prefix."$mat)(memberuid=$uid))", $attribut);
							if(count($tabtmp)==0){
								$attributs=array();
								//$attributs["member"]="uid=$uid,".$dn["people"];
								$attributs["memberuid"]="$uid";
								if(modify_attribut("cn=Matiere_".$prefix."$mat", "groups", $attributs, "add")){
									my_echo("<b>$uid</b> ");
								}
								else{
									my_echo("<font color='red'>$uid</font> ");
									$nb_echecs++;
								}
							}
							else{
								my_echo("$uid ");
							}
						}
					}
				}
				my_echo("<br />\n");
				if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
			}
			my_echo("</p>\n");
		}
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
	}
	else{
		my_echo("</h3>\n");
		my_echo("<blockquote>\n");
		my_echo("<p>Création des Matières non demandée.</p>\n");
	}

	my_echo("</blockquote>\n");



	my_echo("<p>Retour au <a href='#menu'>menu</a>.</p>\n");

	my_echo("<a name='creer_cours'></a>\n");
	//my_echo("<h2>Création des groupes Cours</h2>\n");
	//my_echo("<h3>Création des groupes Cours</h3>\n");
	my_echo("<h3>Création des groupes Cours");
	if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}

	// ===========================================================
	// AJOUTS: 20070914 boireaus
	if($creer_cours=='y'){

		my_echo("</h3>\n");
		my_echo("<script type='text/javascript'>
		document.getElementById('id_creer_cours').style.display='';
</script>");
		my_echo("<blockquote>\n");
		// Là, il faudrait faire un traitement différent selon que l'import élève se fait par CSV ou XML


		//$divisions[$i]["code"]									3 A2
		//$divisions[$i]["services"][$j]["code_matiere"]			020700
		//$divisions[$i]["services"][$j]["enseignants"][$k]["id"]	38764

		for($i=0;$i<count($divisions);$i++){
			$div=$divisions[$i]["code"];
			$div=ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($div)));

			// Dans le cas de l'import XML, on récupère la liste des options suivies par les élèves
			$ind_div="";
			if($type_fichier_eleves=="xml"){
				// Identifier $k tel que $tab_division[$k]["nom"]==$div
				for($k=0;$k<count($tab_division);$k++){
					if(ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($tab_division[$k]["nom"])))==$div){
						$ind_div=$k;
						break;
					}
				}
			}

			$temoin_cours="";
			// On parcours toutes les matières...
			for($j=0;$j<count($divisions[$i]["services"]);$j++){
				$id_mat=$divisions[$i]["services"][$j]["code_matiere"];


				// Recherche du nom court de la matière:
				for($n=0;$n<count($matiere);$n++){
					if($matiere[$n]["code"]==$id_mat){
						$mat=$matiere[$n]["code_gestion"];
					}
				}

				// La matière est-elle optionnelle dans la classe?
				$temoin_matiere_optionnelle="non";
				$ind_mat="";
				if(($type_fichier_eleves=="xml")&&($ind_div!="")){
					for($k=0;$k<count($tab_division[$ind_div]["option"]);$k++){
						// $tab_division[$k]["option"][$n]["code_matiere"]
						if($tab_division[$ind_div]["option"][$k]["code_matiere"]==$id_mat){
							$temoin_matiere_optionnelle="oui";
							$ind_mat=$k;
							break;
						}
					}
				}

				// Récupérer tous les profs de la matière dans la classe
				// ... les trier
				unset($tab_prof_uid);
				$tab_prof_uid=array();
				// On pourrait aussi parcourir l'annuaire... avec le filtre cn=Equipe_".$prefix."$div... peut-être serait-ce plus rapide...
				for($k=0;$k<count($divisions[$i]["services"][$j]["enseignants"]);$k++){
					// Récupération de l'uid correspondant à l'employeeNumber
					$employeeNumber="P".$divisions[$i]["services"][$j]["enseignants"][$k]["id"];
					$attribut=array("uid");
					$tabtmp=get_tab_attribut("people", "employeenumber=$employeeNumber", $attribut);
					if(count($tabtmp)!=0){
						$uid=$tabtmp[0];
						if(!in_array($uid,$tab_prof_uid)){
							$tab_prof_uid[]=$uid;
						}
					}
				}
				sort($tab_prof_uid);


				// Récupérer tous les membres de la classe si la matière n'a pas été détectée comme optionnelle dans la classe
				// ... les trier
				unset($tab_eleve_uid);
				$tab_eleve_uid=array();
				if($temoin_matiere_optionnelle!="oui"){
					//$attribut=array("memberUid");
					$attribut=array("memberuid");
					//my_echo("Recherche: get_tab_attribut(\"groups\", \"cn=Classe_".$prefix."$div\", $attribut)<br />");
					$tabtmp=get_tab_attribut("groups", "cn=Classe_".$prefix."$div", $attribut);
					if(count($tabtmp)!=0){
						//my_echo("count(\$tabtmp)=".count($tabtmp)."<br />");
						for($k=0;$k<count($tabtmp);$k++){
							//my_echo("\$tabtmp[$k]=".$tabtmp[$k]."<br />");
							// Normalement, chaque élève n'est inscrit qu'une fois dans la classe, mais bon...
							if(!in_array($tabtmp[$k],$tab_eleve_uid)){
								//my_echo("Ajout à \$tab_eleve_uid<br />");
								$tab_eleve_uid[]=$tabtmp[$k];
							}
						}
					}
				}
				else{
					// Faire une boucle sur $eleve[$numero]["options"][$j]["code_matiere"] après avoir identifié le numéro... en faisant une recherche sur  les memberUid de "cn=Classe_".$prefix."$div"
					// Ou: remplir un étage de plus de $tab_division[$k]["option"]
					//$tab_division[$ind_div]["option"][$ind_mat]["eleve"][]
					//my_echo("<p>Matière optionnelle pour $mat en $div:<br />");
					for($k=0;$k<count($tab_division[$ind_div]["option"][$ind_mat]["eleve"]);$k++){
						$attribut=array("uid");
						//my_echo("Recherche: get_tab_attribut(\"groups\", \"cn=Classe_".$prefix."$div\", $attribut)<br />");
						$tabtmp=get_tab_attribut("people", "employeenumber=".$tab_division[$ind_div]["option"][$ind_mat]["eleve"][$k], $attribut);
						if(count($tabtmp)!=0){
							if(!in_array($tabtmp[0],$tab_eleve_uid)){
								//my_echo("Ajout à \$tab_eleve_uid<br />");
								$tab_eleve_uid[]=$tabtmp[0];
							}
						}
					}
				}

				// Création du groupe
				// Le groupe Cours existe-t-il?
				my_echo("<p>\n");
				$attribut=array("cn");
				//my_echo("Recherche de: get_tab_attribut(\"groups\", \"cn=Cours_".$prefix."\".$mat.\"_\".$div, $attribut)<br />");
				$tabtmp=get_tab_attribut("groups", "cn=Cours_".$prefix.$mat."_".$div, $attribut);
				if(count($tabtmp)==0){
					$attributs=array();
					$attributs["cn"]="Cours_".$prefix.$mat."_".$div;

					// MODIF: boireaus 20070728
					//$attributs["objectClass"]="top";
					$attributs["objectClass"][0]="top";
					$attributs["objectClass"][1]="posixGroup";

					//$attributs["objectClass"]="posixGroup";
					//$attributs["objectClass"]="groupOfNames";
					// Il faudrait ajouter un test sur le fait qu'il reste un gidNumber dispo...
					//$gidNumber=get_first_free_gidNumber();
					$gidNumber=get_first_free_gidNumber(10000);
					if($gidNumber!=false){
						$attributs["gidNumber"]="$gidNumber";
						// Ou récupérer un nom long du fichier de STS...
						$attributs["description"]="$mat / $div";

						//my_echo("<p>Création du groupe Cours_".$prefix.$mat."_".$div.": ");
						my_echo("Création du groupe Cours_".$prefix.$mat."_".$div.": ");
						if(add_entry ("cn=Cours_".$prefix.$mat."_".$div, "groups", $attributs)){
							/*
							unset($attributs);
							$attributs=array();
							$attributs["objectClass"]="posixGroup";
							if(modify_attribut("cn=Cours_".$prefix.$mat."_".$div,"groups", $attributs, "add")){
							*/
								my_echo("<font color='green'>SUCCES</font>");
							/*
							}
							else{
								my_echo("<font color='red'>ECHEC</font>");
								$temoin_cours="PROBLEME";
								$nb_echecs++;
							}
							*/
							//my_echo("<font color='green'>SUCCES</font>");
						}
						else{
							my_echo("<font color='red'>ECHEC</font>");
							$temoin_cours="PROBLEME";
							$nb_echecs++;
						}
						my_echo("<br />\n");
						if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
					}
					else{
						my_echo("<font color='red'>ECHEC</font> Il n'y a plus de gidNumber disponible.<br />\n");
						$temoin_cours="PROBLEME";
						$nb_echecs++;
					}
				}


				if($temoin_cours==""){
					// Ajout des membres
					my_echo("Ajout de membres au groupe Cours_".$prefix.$mat."_".$div.": ");
					// Ajout des profs
					for($n=0;$n<count($tab_prof_uid);$n++){
						$uid=$tab_prof_uid[$n];
						$attribut=array("cn");
						//my_echo("Recherche de get_tab_attribut(\"groups\", \"(&(cn=Cours_".$prefix.$mat.\"_\".$div.\")(memberuid=$uid))\", $attribut)<br />");
						$tabtmp=get_tab_attribut("groups", "(&(cn=Cours_".$prefix.$mat."_".$div.")(memberuid=$uid))", $attribut);
						if(count($tabtmp)==0){
							unset($attribut);
							$attribut=array();
							$attribut["memberUid"]=$uid;
							if(modify_attribut("cn=Cours_".$prefix.$mat."_".$div,"groups",$attribut,"add")){
								my_echo("<b>$uid</b> ");
							}
							else{
								my_echo("<font color='red'>$uid</font> ");
								$nb_echecs++;
							}
						}
						else{
							my_echo("$uid ");
						}
					}

					// Ajout des élèves
					for($n=0;$n<count($tab_eleve_uid);$n++){
						$uid=$tab_eleve_uid[$n];
						$attribut=array("cn");
						$tabtmp=get_tab_attribut("groups", "(&(cn=Cours_".$prefix.$mat."_".$div.")(memberuid=$uid))", $attribut);
						if(count($tabtmp)==0){
							unset($attribut);
							$attribut=array();
							$attribut["memberUid"]=$uid;
							if(modify_attribut("cn=Cours_".$prefix.$mat."_".$div,"groups",$attribut,"add")){
								my_echo("<b>$uid</b> ");
							}
							else{
								my_echo("<font color='red'>$uid</font> ");
								$nb_echecs++;
							}
						}
						else{
							my_echo("$uid ");
						}
					}
					my_echo(" (<i>".count($tab_prof_uid)."+".count($tab_eleve_uid)."</i>)\n");
					my_echo("<br />\n");
					if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
				}
				my_echo("</p>\n");
			}
		}

		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}






		// Dans le cas de l'import XML élèves, on a $eleve[$numero]["options"][$j]["code_matiere"]

		// Rechercher les groupes
		//$groupes[$i]["code"]	-> 3 A1TEC1 ou 3AGL1-1
		//$groupes[$i]["divisions"][$j]["code"]	-> 3 A1
		//$groupes[$i]["code_matiere"]			-> 070800
		//$groupes[$i]["enseignant"][$m]["id"]	-> 38101
		for($i=0;$i<count($groupes);$i++){
			if(isset($groupes[$i]["service"])){
				for($p=0;$p<count($groupes[$i]["service"]);$p++){
					/*
					$grp=$groupes[$i]["code"];
					if(count($groupes[$i]["service"])>1){
						if(isset($groupes[$i]["service"][$p]["code_matiere"])){
							$grp=$grp."_".$groupes[$i]["service"][$p]["code_matiere"];
						}
						else{
							$grp=$grp."_".$p;
						}
					}
					$grp=ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($grp)));
					*/
					$temoin_grp="";
					$grp_mat="";

					//my_echo("<p>\$grp=\$groupes[$i][\"code\"]=".$grp."<br />");

					if(isset($groupes[$i]["service"][$p]["code_matiere"])){
						$grp_id_mat=$groupes[$i]["service"][$p]["code_matiere"];
						//my_echo("\$grp_id_mat=\$groupes[$i][\"code_matiere\"]=".$grp_id_mat."<br />");
						// Recherche du nom court de matière
						for($n=0;$n<count($matiere);$n++){
							if($matiere[$n]["code"]==$grp_id_mat){
								$grp_mat=$matiere[$n]["code_gestion"];
							}
						}
					}

					$grp=$groupes[$i]["code"];
					if(count($groupes[$i]["service"])>1){
						if($grp_mat!=""){
							$grp=$grp."_".$grp_mat;
						}
						else{
							$grp=$grp."_".$p;
						}
					}
					$grp=ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($grp)));

					//my_echo("\$grp_mat=".$grp_mat."<br />");


					// Récupération des profs associés à ce groupe
					unset($tab_prof_uid);
					$tab_prof_uid=array();
					if(isset($groupes[$i]["service"][$p]["enseignant"])){
						for($m=0;$m<count($groupes[$i]["service"][$p]["enseignant"]);$m++){
							$employeeNumber="P".$groupes[$i]["service"][$p]["enseignant"][$m]["id"];
							$attribut=array("uid");
							$tabtmp=get_tab_attribut("people", "employeenumber=$employeeNumber", $attribut);
							if(count($tabtmp)!=0){
								$uid=$tabtmp[0];
								if(!in_array($uid,$tab_prof_uid)){
									$tab_prof_uid[]=$uid;
								}
							}
						}
					}

					// Récupération des élèves associés aux classes de ce groupe
					unset($tab_eleve_uid);
					$tab_eleve_uid=array();
					$chaine_div="";
					for($j=0;$j<count($groupes[$i]["divisions"]);$j++){
						$div=$groupes[$i]["divisions"][$j]["code"];
						$div=ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($div)));

						//my_echo("\$div=".$div."<br />");

						//$tab_division[$ind_div]["option"][$k]["code_matiere"]

						// Dans le cas de l'import XML, on récupère la liste des options suivies par les élèves
						$ind_div="";
						if($type_fichier_eleves=="xml"){
							// Identifier $k tel que $tab_division[$k]["nom"]==$div
							for($k=0;$k<count($tab_division);$k++){
								//my_echo("\$tab_division[$k][\"nom\"]=".$tab_division[$k]["nom"]."<br />");
								if(ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($tab_division[$k]["nom"])))==$div){
									$ind_div=$k;
									break;
								}
							}
						}
						//my_echo("\$ind_div=".$ind_div."<br />");



						// La matière est-elle optionnelle dans la classe?
						$temoin_matiere_optionnelle="non";
						$ind_mat="";
						if(($type_fichier_eleves=="xml")&&($ind_div!="")){
							for($k=0;$k<count($tab_division[$ind_div]["option"]);$k++){
							//for($k=0;$k<=count($tab_division[$ind_div]["option"]);$k++){
								//my_echo("\$tab_division[$ind_div][\"option\"][$k][\"code_matiere\"]=".$tab_division[$ind_div]["option"][$k]["code_matiere"]."<br />");
								// $tab_division[$k]["option"][$n]["code_matiere"]
								if($tab_division[$ind_div]["option"][$k]["code_matiere"]==$grp_id_mat){
									$temoin_matiere_optionnelle="oui";
									$ind_mat=$k;
									break;
								}
							}
						}
						//my_echo("\$ind_mat=".$ind_mat."<br />");



						if($chaine_div==""){
							$chaine_div=$div;
						}
						else{
							$chaine_div.=" / ".$div;
						}



						if($temoin_matiere_optionnelle!="oui"){
							//$attribut=array("memberUid");
							$attribut=array("memberuid");
							$tabtmp=get_tab_attribut("groups", "cn=Classe_".$prefix."$div", $attribut);
							if(count($tabtmp)!=0){
								for($k=0;$k<count($tabtmp);$k++){
									// Normalement, chaque élève n'est inscrit qu'une fois dans la classe, mais bon...
									if(!in_array($tabtmp[$k],$tab_eleve_uid)){
										$tab_eleve_uid[]=$tabtmp[$k];
									}
								}
							}
						}
						else{
							//my_echo("<p>Matière optionnelle pour $grp:<br />");
							for($k=0;$k<count($tab_division[$ind_div]["option"][$ind_mat]["eleve"]);$k++){
								$attribut=array("uid");
								//my_echo("Recherche: get_tab_attribut(\"groups\", \"cn=Classe_".$prefix."$div\", $attribut)<br />");
								$tabtmp=get_tab_attribut("people", "employeenumber=".$tab_division[$ind_div]["option"][$ind_mat]["eleve"][$k], $attribut);
								if(count($tabtmp)!=0){
									if(!in_array($tabtmp[0],$tab_eleve_uid)){
										//my_echo("Ajout à \$tab_eleve_uid<br />");
										$tab_eleve_uid[]=$tabtmp[0];
									}
								}
							}
						}
					}


					// Création du groupe
					// Le groupe Cours existe-t-il?
					my_echo("<p>\n");
					$attribut=array("cn");
					$tabtmp=get_tab_attribut("groups", "cn=Cours_".$prefix."$grp", $attribut);
					if(count($tabtmp)==0){
						$attributs=array();
						$attributs["cn"]="Cours_".$prefix."$grp";

						// MODIF: boireaus 20070728
						//$attributs["objectClass"]="top";
						$attributs["objectClass"][0]="top";
						$attributs["objectClass"][1]="posixGroup";

						//$attributs["objectClass"]="posixGroup";
						//$attributs["objectClass"]="groupOfNames";
						// Il faudrait ajouter un test sur le fait qu'il reste un gidNumber dispo...
						//$gidNumber=get_first_free_gidNumber();
						$gidNumber=get_first_free_gidNumber(10000);
						if($gidNumber!=false){
							$attributs["gidNumber"]="$gidNumber";
							// Ou récupérer un nom long du fichier de STS...
							$attributs["description"]="$grp_mat / $chaine_div";

							//my_echo("<p>Création du groupe Cours_".$prefix."$grp: ");
							my_echo("Création du groupe Cours_".$prefix."$grp: ");
							//my_echo(" grp_mat=$grp_mat ");
							if(add_entry ("cn=Cours_".$prefix."$grp", "groups", $attributs)){
								/*
								unset($attributs);
								$attributs=array();
								$attributs["objectClass"]="posixGroup";
								if(modify_attribut("cn=Cours_".$prefix."$grp","groups", $attributs, "add")){
								*/
									my_echo("<font color='green'>SUCCES</font>");
								/*
								}
								else{
									my_echo("<font color='red'>ECHEC</font>");
									$temoin_cours="PROBLEME";
									$nb_echecs++;
								}
								*/
								//my_echo("<font color='green'>SUCCES</font>");
							}
							else{
								my_echo("<font color='red'>ECHEC</font>");
								$temoin_cours="PROBLEME";
								$nb_echecs++;
							}
							my_echo("<br />\n");
							if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
						}
						else{
							my_echo("<font color='red'>ECHEC</font> Il n'y a plus de gidNumber disponible.<br />\n");
							$temoin_cours="PROBLEME";
							$nb_echecs++;
						}
					}


					if($temoin_cours==""){
						// Ajout de membres au groupe
						my_echo("Ajout de membres au groupe Cours_".$prefix."$grp: ");
						// Ajout des profs
						for($n=0;$n<count($tab_prof_uid);$n++){
							$uid=$tab_prof_uid[$n];
							$attribut=array("cn");
							$tabtmp=get_tab_attribut("groups", "(&(cn=Cours_".$prefix."$grp)(memberuid=$uid))", $attribut);
							if(count($tabtmp)==0){
								unset($attribut);
								$attribut=array();
								$attribut["memberUid"]=$uid;
								if(modify_attribut("cn=Cours_".$prefix."$grp","groups",$attribut,"add")){
									my_echo("<b>$uid</b> ");
								}
								else{
									my_echo("<font color='red'>$uid</font> ");
									$nb_echecs++;
								}
							}
							else{
								my_echo("$uid ");
							}
						}

						// Ajout des élèves
						for($n=0;$n<count($tab_eleve_uid);$n++){
							$uid=$tab_eleve_uid[$n];
							$attribut=array("cn");
							$tabtmp=get_tab_attribut("groups", "(&(cn=Cours_".$prefix."$grp)(memberuid=$uid))", $attribut);
							if(count($tabtmp)==0){
								unset($attribut);
								$attribut=array();
								$attribut["memberUid"]=$uid;
								if(modify_attribut("cn=Cours_".$prefix."$grp","groups",$attribut,"add")){
									my_echo("<b>$uid</b> ");
								}
								else{
									my_echo("<font color='red'>$uid</font> ");
									$nb_echecs++;
								}
							}
							else{
								my_echo("$uid ");
							}
						}
						my_echo(" (<i>".count($tab_prof_uid)."+".count($tab_eleve_uid)."</i>)\n");
						my_echo("<br />\n");
						if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
					}
					my_echo("</p>\n");
				}
			}
			else{
				// Pas de section "<SERVICE " dans ce groupe
				$grp=$groupes[$i]["code"];
				$grp=ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($grp)));
				$temoin_grp="";
				$grp_mat="";

				//my_echo("<p>\$grp=\$groupes[$i][\"code\"]=".$grp."<br />");

				/*
				// Pas de section "<SERVICE " dans ce groupe donc pas de CODE_MATIERE
				if(isset($groupes[$i]["code_matiere"])){
					$grp_id_mat=$groupes[$i]["code_matiere"];
					//my_echo("\$grp_id_mat=\$groupes[$i][\"code_matiere\"]=".$grp_id_mat."<br />");
					// Recherche du nom court de matière
					for($n=0;$n<count($matiere);$n++){
						if($matiere[$n]["code"]==$grp_id_mat){
							$grp_mat=$matiere[$n]["code_gestion"];
						}
					}
				}
				*/
				//my_echo("\$grp_mat=".$grp_mat."<br />");


				// Récupération des profs associés à ce groupe
				unset($tab_prof_uid);
				$tab_prof_uid=array();
				/*
				// Pas de section "<SERVICE " dans ce groupe donc pas de sous-section ENSEIGNANT
				if(isset($groupes[$i]["enseignant"])){
					for($m=0;$m<count($groupes[$i]["enseignant"]);$m++){
						$employeeNumber="P".$groupes[$i]["enseignant"][$m]["id"];
						$attribut=array("uid");
						$tabtmp=get_tab_attribut("people", "employeenumber=$employeeNumber", $attribut);
						if(count($tabtmp)!=0){
							$uid=$tabtmp[0];
							if(!in_array($uid,$tab_prof_uid)){
								$tab_prof_uid[]=$uid;
							}
						}
					}
				}
				*/

				// Récupération des élèves associés aux classes de ce groupe
				unset($tab_eleve_uid);
				$tab_eleve_uid=array();
				$chaine_div="";
				for($j=0;$j<count($groupes[$i]["divisions"]);$j++){
					$div=$groupes[$i]["divisions"][$j]["code"];
					$div=ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($div)));

					//my_echo("\$div=".$div."<br />");

					//$tab_division[$ind_div]["option"][$k]["code_matiere"]

					// Dans le cas de l'import XML, on récupère la liste des options suivies par les élèves
					$ind_div="";
					if($type_fichier_eleves=="xml"){
						// Identifier $k tel que $tab_division[$k]["nom"]==$div
						for($k=0;$k<count($tab_division);$k++){
							//my_echo("\$tab_division[$k][\"nom\"]=".$tab_division[$k]["nom"]."<br />");
							if(ereg_replace("'","_",ereg_replace(" ","_",remplace_accents($tab_division[$k]["nom"])))==$div){
								$ind_div=$k;
								break;
							}
						}
					}
					//my_echo("\$ind_div=".$ind_div."<br />");



					// La matière est-elle optionnelle dans la classe?
					$temoin_matiere_optionnelle="non";
					$ind_mat="";
					if(($type_fichier_eleves=="xml")&&($ind_div!="")){
						for($k=0;$k<count($tab_division[$ind_div]["option"]);$k++){
						//for($k=0;$k<=count($tab_division[$ind_div]["option"]);$k++){
							//my_echo("\$tab_division[$ind_div][\"option\"][$k][\"code_matiere\"]=".$tab_division[$ind_div]["option"][$k]["code_matiere"]."<br />");
							// $tab_division[$k]["option"][$n]["code_matiere"]
							if($tab_division[$ind_div]["option"][$k]["code_matiere"]==$grp_id_mat){
								$temoin_matiere_optionnelle="oui";
								$ind_mat=$k;
								break;
							}
						}
					}
					//my_echo("\$ind_mat=".$ind_mat."<br />");



					if($chaine_div==""){
						$chaine_div=$div;
					}
					else{
						$chaine_div.=" / ".$div;
					}



					if($temoin_matiere_optionnelle!="oui"){
						//$attribut=array("memberUid");
						$attribut=array("memberuid");
						$tabtmp=get_tab_attribut("groups", "cn=Classe_".$prefix."$div", $attribut);
						if(count($tabtmp)!=0){
							for($k=0;$k<count($tabtmp);$k++){
								// Normalement, chaque élève n'est inscrit qu'une fois dans la classe, mais bon...
								if(!in_array($tabtmp[$k],$tab_eleve_uid)){
									$tab_eleve_uid[]=$tabtmp[$k];
								}
							}
						}
					}
					else{
						//my_echo("<p>Matière optionnelle pour $grp:<br />");
						for($k=0;$k<count($tab_division[$ind_div]["option"][$ind_mat]["eleve"]);$k++){
							$attribut=array("uid");
							//my_echo("Recherche: get_tab_attribut(\"groups\", \"cn=Classe_".$prefix."$div\", $attribut)<br />");
							$tabtmp=get_tab_attribut("people", "employeenumber=".$tab_division[$ind_div]["option"][$ind_mat]["eleve"][$k], $attribut);
							if(count($tabtmp)!=0){
								if(!in_array($tabtmp[0],$tab_eleve_uid)){
									//my_echo("Ajout à \$tab_eleve_uid<br />");
									$tab_eleve_uid[]=$tabtmp[0];
								}
							}
						}
					}
				}


				// Création du groupe
				// Le groupe Cours existe-t-il?
				my_echo("<p>\n");
				$attribut=array("cn");
				$tabtmp=get_tab_attribut("groups", "cn=Cours_".$prefix."$grp", $attribut);
				if(count($tabtmp)==0){
					$attributs=array();
					$attributs["cn"]="Cours_".$prefix."$grp";

					// MODIF: boireaus 20070728
					//$attributs["objectClass"]="top";
					$attributs["objectClass"][0]="top";
					$attributs["objectClass"][1]="posixGroup";

					//$attributs["objectClass"]="posixGroup";
					//$attributs["objectClass"]="groupOfNames";
					// Il faudrait ajouter un test sur le fait qu'il reste un gidNumber dispo...
					//$gidNumber=get_first_free_gidNumber();
					$gidNumber=get_first_free_gidNumber(10000);
					if($gidNumber!=false){
						$attributs["gidNumber"]="$gidNumber";
						// Ou récupérer un nom long du fichier de STS...
						$attributs["description"]="$grp_mat / $chaine_div";

						//my_echo("<p>Création du groupe Cours_".$prefix."$grp: ");
						my_echo("Création du groupe Cours_".$prefix."$grp: ");
						//my_echo(" grp_mat=$grp_mat ");
						if(add_entry ("cn=Cours_".$prefix."$grp", "groups", $attributs)){
							/*
							unset($attributs);
							$attributs=array();
							$attributs["objectClass"]="posixGroup";
							if(modify_attribut("cn=Cours_".$prefix."$grp","groups", $attributs, "add")){
							*/
								my_echo("<font color='green'>SUCCES</font>");
							/*
							}
							else{
								my_echo("<font color='red'>ECHEC</font>");
								$temoin_cours="PROBLEME";
								$nb_echecs++;
							}
							*/
							//my_echo("<font color='green'>SUCCES</font>");
						}
						else{
							my_echo("<font color='red'>ECHEC</font>");
							$temoin_cours="PROBLEME";
							$nb_echecs++;
						}
						my_echo("<br />\n");
						if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
					}
					else{
						my_echo("<font color='red'>ECHEC</font> Il n'y a plus de gidNumber disponible.<br />\n");
						$temoin_cours="PROBLEME";
						$nb_echecs++;
					}
				}


				if($temoin_cours==""){
					// Ajout de membres au groupe
					my_echo("Ajout de membres au groupe Cours_".$prefix."$grp: ");
					// Ajout des profs
					for($n=0;$n<count($tab_prof_uid);$n++){
						$uid=$tab_prof_uid[$n];
						$attribut=array("cn");
						$tabtmp=get_tab_attribut("groups", "(&(cn=Cours_".$prefix."$grp)(memberuid=$uid))", $attribut);
						if(count($tabtmp)==0){
							unset($attribut);
							$attribut=array();
							$attribut["memberUid"]=$uid;
							if(modify_attribut("cn=Cours_".$prefix."$grp","groups",$attribut,"add")){
								my_echo("<b>$uid</b> ");
							}
							else{
								my_echo("<font color='red'>$uid</font> ");
								$nb_echecs++;
							}
						}
						else{
							my_echo("$uid ");
						}
					}

					// Ajout des élèves
					for($n=0;$n<count($tab_eleve_uid);$n++){
						$uid=$tab_eleve_uid[$n];
						$attribut=array("cn");
						$tabtmp=get_tab_attribut("groups", "(&(cn=Cours_".$prefix."$grp)(memberuid=$uid))", $attribut);
						if(count($tabtmp)==0){
							unset($attribut);
							$attribut=array();
							$attribut["memberUid"]=$uid;
							if(modify_attribut("cn=Cours_".$prefix."$grp","groups",$attribut,"add")){
								my_echo("<b>$uid</b> ");
							}
							else{
								my_echo("<font color='red'>$uid</font> ");
								$nb_echecs++;
							}
						}
						else{
							my_echo("$uid ");
						}
					}
					my_echo(" (<i>".count($tab_prof_uid)."+".count($tab_eleve_uid)."</i>)\n");
					my_echo("<br />\n");
					if($chrono=='y'){my_echo("Fin: ".date_et_heure()."<br />\n");}
				}
				my_echo("</p>\n");
			}
		}
		if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}
	}
	else{
		my_echo("</h3>\n");
		my_echo("<blockquote>\n");
		my_echo("<p>Création des Cours non demandée.</p>\n");
	}

	my_echo("</blockquote>\n");



	my_echo("<p>Retour au <a href='#menu'>menu</a>.</p>\n");

	my_echo("<a name='fin'></a>\n");
	//my_echo("<h3>Rapport final de création</h3>");
	my_echo("<h3>Rapport final de création");
	if($chrono=='y'){my_echo(" (<i>".date_et_heure()."</i>)");}
	my_echo("</h3>\n");
	my_echo("<blockquote>\n");
	my_echo("<p>Terminé!</p>\n");
	my_echo("<script type='text/javascript'>
	document.getElementById('id_fin').style.display='';
</script>");

	$chaine="";
	if($nouveaux_comptes==0){
		$chaine.="<p>Aucun nouveau compte n'a été créé.</p>\n";
		//my_echo("<p>Aucun nouveau compte n'a été créé.</p>\n");
	}
	elseif($nouveaux_comptes==1){
		//my_echo("<p>$nouveaux_comptes nouveau compte a été créé: $tab_nouveaux_comptes[0]</p>\n");
		$chaine.="<p>$nouveaux_comptes nouveau compte a été créé: $tab_nouveaux_comptes[0]</p>\n";
	}
	else{
		/*
		my_echo("<p>$nouveaux_comptes nouveaux comptes ont été créés: \n");
		my_echo($tab_nouveaux_comptes[0]);
		for($i=1;$i<count($tab_nouveaux_comptes);$i++){
			my_echo(", $tab_nouveaux_comptes[$i]");
		}
		my_echo("</p>\n");
		*/
		$chaine.="<p>$nouveaux_comptes nouveaux comptes ont été créés: \n";
		$chaine.=$tab_nouveaux_comptes[0];
		for($i=1;$i<count($tab_nouveaux_comptes);$i++){
			$chaine.=", $tab_nouveaux_comptes[$i]";
		}
		$chaine.="</p>\n";
	}

	if($comptes_avec_employeeNumber_mis_a_jour==0){
		//my_echo("<p>Aucun compte existant sans employeeNumber n'a été récupéré/corrigé.</p>\n");
		$chaine.="<p>Aucun compte existant sans employeeNumber n'a été récupéré/corrigé.</p>\n";
	}
	elseif($comptes_avec_employeeNumber_mis_a_jour==1){
		//my_echo("<p>$comptes_avec_employeeNumber_mis_a_jour compte existant sans employeeNumber a été récupéré/corrigé (<i>son employeeNumber est maintenant renseigné</i>): $tab_comptes_avec_employeeNumber_mis_a_jour[0]</p>\n");
		$chaine.="<p>$comptes_avec_employeeNumber_mis_a_jour compte existant sans employeeNumber a été récupéré/corrigé (<i>son employeeNumber est maintenant renseigné</i>): $tab_comptes_avec_employeeNumber_mis_a_jour[0]</p>\n";
	}
	else{
		/*
		my_echo("<p>$comptes_avec_employeeNumber_mis_a_jour comptes existants sans employeeNumber ont été récupérés/corrigés (<i>leur employeeNumber est maintenant renseigné</i>): \n");
		my_echo("$tab_comptes_avec_employeeNumber_mis_a_jour[0]");
		for($i=1;$i<count($tab_comptes_avec_employeeNumber_mis_a_jour);$i++){my_echo(", $tab_comptes_avec_employeeNumber_mis_a_jour[$i]");}
		my_echo("</p>\n");
		*/
		$chaine.="<p>$comptes_avec_employeeNumber_mis_a_jour comptes existants sans employeeNumber ont été récupérés/corrigés (<i>leur employeeNumber est maintenant renseigné</i>): \n";
		$chaine.="$tab_comptes_avec_employeeNumber_mis_a_jour[0]";
		for($i=1;$i<count($tab_comptes_avec_employeeNumber_mis_a_jour);$i++){$chaine.=", $tab_comptes_avec_employeeNumber_mis_a_jour[$i]";}
		$chaine.="</p>\n";
	}

	if($nb_echecs==0){
		//my_echo("<p>Aucune opération tentée n'a échoué.</p>\n");
		$chaine.="<p>Aucune opération tentée n'a échoué.</p>\n";
	}
	elseif($nb_echecs==1){
		//my_echo("<p style='color:red;'>$nb_echecs opération tentée a échoué.</p>\n");
		$chaine.="<p style='color:red;'>$nb_echecs opération tentée a échoué.</p>\n";
	}
	else{
		//my_echo("<p style='color:red;'>$nb_echecs opérations tentées ont échoué.</p>\n");
		$chaine.="<p style='color:red;'>$nb_echecs opérations tentées ont échoué.</p>\n";
	}
	my_echo($chaine);




/*
	// Envoi par mail de $chaine et $echo_http_file

	// Récupérer les adresses,... dans le /etc/ssmtp/ssmtp.conf
	unset($tabssmtp);
	my_echo("<p>Avant lireSSMTP();</p>");
	$tabssmtp=lireSSMTP();
	my_echo("<p>Après lireSSMTP();</p>");
	my_echo("<p>\$tabssmtp[\"root\"]=".$tabssmtp["root"]."</p>");
	// Contrôler les champs affectés...
	if(isset($tabssmtp["root"])){
		$adressedestination=$tabssmtp["root"];
		$sujet="[$domain] Rapport de ";
		if($simulation=="y"){$sujet.="simulation de ";}
		$sujet.="création de comptes";
		$message="Import du $debut_import\n";
		$message.="$chaine\n";
		$message.="\n";
		$message.="Vous pouvez consulter le rapport détaillé à l'adresse $echo_http_file\n";
		$entete="From: ".$tabssmtp["root"];
		my_echo("<p>Avant mail.</p>");
		mail("$adressedestination", "$sujet", "$message", "$entete") or my_echo("<p style='color:red;'><b>ERREUR</b> lors de l'envoi du rapport par mail.</p>\n");
		my_echo("<p>Après mail.</p>");
	}
	else{
		my_echo("<p>\$tabssmtp[\"root\"] doit être vide.</p>");
		my_echo("<p style='color:red;'><b>MAIL:</b> La configuration mail ne permet pas d'expédier le rapport.<br />Consultez/renseignez le menu Informations système/Actions sur le serveur/Configurer l'expédition des mails.</p>\n");
	}
*/

	//my_echo("<p>Avant màj params.</p>");

	// Renseignement du témoin de mise à jour terminée.
	$sql="SELECT value FROM params WHERE name='imprt_cmpts_en_cours'";
	$res1=mysql_query($sql);
	if(mysql_num_rows($res1)==0){
		$sql="INSERT INTO params SET name='imprt_cmpts_en_cours',value='n'";
		$res0=mysql_query($sql);
	}
	else{
		$sql="UPDATE params SET value='n' WHERE name='imprt_cmpts_en_cours'";
		$res0=mysql_query($sql);
	}

	//my_echo("<p>Après màj params.</p>");

	if($chrono=='y'){my_echo("<p>Fin de l'opération: ".date_et_heure()."</p>\n");}

	my_echo("<p><a href='".$www_import."'>Retour</a>.</p>\n");

	my_echo("</blockquote>\n");
	my_echo("<script type='text/javascript'>
	compte_a_rebours='n';
</script>\n");
	//my_echo("</body>\n</html>\n");

//		}

		// Dans la version PHP4-CLI, envoyer le rapport par mail.
		// Envoyer le contenu de la page aussi?

		// Peut-être forcer une sauvegarde de l'annuaire avant de procéder à une opération qui n'est pas une simulation.
		// Où placer le fichier de sauvegarde?
		// Problème de l'encombrement à terme.
//	}

// SUPPRIMER LES FICHIERS CSV/XML en fin d'import.

	if(file_exists($eleves_file)){
		unlink($eleves_file);
	}

	if(file_exists($sts_xml_file)){
		unlink($sts_xml_file);
	}

	if(file_exists("$dossier_tmp_import_comptes/import_comptes.sh")){
		unlink("$dossier_tmp_import_comptes/import_comptes.sh");
	}


	if(file_exists("/tmp/debug_se3lcs.txt")){
		// Il faut pouvoir écrire dans le fichier depuis /var/www/se3/annu/import_sconet.php sans sudo... donc www-se3 doit être proprio ou avoir les droits...
		exec("chown $user_web /tmp/debug_se3lcs.txt");
	}


	// Envoi par mail de $chaine et $echo_http_file
        if ( $servertype=="SE3" ) {
            // Récupérer les adresses,... dans le /etc/ssmtp/ssmtp.conf
	    unset($tabssmtp);
	    $tabssmtp=lireSSMTP();
	    // Contrôler les champs affectés...
	    if (isset($tabssmtp["root"])){
		$adressedestination=$tabssmtp["root"];
		$sujet="[$domain] Rapport de ";
		if($simulation=="y"){$sujet.="simulation de ";}
		$sujet.="création de comptes";
		$message="Import du $debut_import\n";
		$message.="$chaine\n";
		$message.="\n";
		$message.="Vous pouvez consulter le rapport détaillé à l'adresse $echo_http_file\n";
		$entete="From: ".$tabssmtp["root"];
		mail("$adressedestination", "$sujet", "$message", "$entete") or my_echo("<p style='color:red;'><b>ERREUR</b> lors de l'envoi du rapport par mail.</p>\n");
	    } else  my_echo("<p style='color:red;'><b>MAIL:</b> La configuration mail ne permet pas d'expédier le rapport.<br />Consultez/renseignez le menu Informations système/Actions sur le serveur/Configurer l'expédition des mails.</p>\n");
        } elseif ( $servertype=="LCS") {
		$adressedestination="admin@$domain";
		$sujet="[$domain] Rapport de ";
		if($simulation=="y"){$sujet.="simulation de ";}
		$sujet.="création de comptes";
		$message="Import du $debut_import\n";
		$message.="$chaine\n";
		$message.="\n";
		$message.="Vous pouvez consulter le rapport détaillé à l'adresse $echo_http_file\n";
		$entete="From: root@$domain";
		mail("$adressedestination", "$sujet", "$message", "$entete") or my_echo("<p style='color:red;'><b>ERREUR</b> lors de l'envoi du rapport par mail.</p>\n");
        }
	my_echo("</body>\n</html>\n");

?>
