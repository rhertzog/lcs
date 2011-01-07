<?php
@set_time_limit(0);
/*
* $Id: eleves.php 6074 2010-12-08 15:43:17Z crob $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


// Initialisations files
require_once("../lib/initialisations.inc.php");


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$liste_tables_del = array(
"absences",
"absences_gep",
"aid",
"aid_appreciations",
//"aid_config",
"avis_conseil_classe",
//"classes",
//"droits",
"eleves",
"responsables",
"responsables2",
"resp_pers",
"resp_adr",
//"etablissements",
"j_aid_eleves",
"j_aid_eleves_resp",
"j_aid_utilisateurs",
"j_aid_utilisateurs_gest",
"j_eleves_classes",
//==========================
// On ne vide plus la table chaque ann�e
// Probl�me avec Sconet qui r�cup�re seulement l'�tablissement de l'ann�e pr�c�dente qui peut �tre l'�tablissement courant
//"j_eleves_etablissements",
//==========================
"j_eleves_professeurs",
"j_eleves_regime",
"j_eleves_groupes",
//"j_professeurs_matieres",
//"log",
//"matieres",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
//==========================
// Tables notanet
'notanet',
'notanet_avis',
'notanet_app',
'notanet_verrou',
'notanet_socles',
'notanet_ele_type',
//==========================
//"periodes",
"tempo2",
//"temp_gep_import",
"tempo",
//"utilisateurs",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
//"setting"
);




//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e : Importation des �l�ves - Etape 1";
require_once("../lib/header.inc");
//************** FIN EN-TETE ***************

//==================================
// RNE de l'�tablissement pour comparer avec le RNE de l'�tablissement de l'ann�e pr�c�dente
$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
//==================================

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

//debug_var();

?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Premi�re phase d'initialisation<br />Importation des �l�ves</h3></center>\n";


if (!isset($_POST["action"])) {
	//
	// On s�lectionne le fichier � importer
	//

	echo "<p>Vous allez effectuer la premi�re �tape : elle consiste � importer le fichier <b>g_eleves.csv</b> contenant les donn�es �l�ves.</p>\n";
	echo "<p>Les champs suivants doivent �tre pr�sents, dans l'ordre, et <b>s�par�s par un point-virgule</b> : </p>\n";
	echo "<ul><li>Nom</li>\n" .
			"<li>Pr�nom</li>\n" .
			"<li>Date de naissance au format JJ/MM/AAAA</li>\n" .
			"<li>n� identifiant interne � l'�tablissement (indispensable : c'est ce num�ro qui est utilis� pour faire la liaison lors des autres importations)</li>\n" .
			"<li>n� identifiant national</li>\n" .
			"<li>Code �tablissement pr�c�dent</li>\n" .
			"<li>Doublement (OUI ou NON)</li>\n" .
			"<li>R�gime (INTERN ou EXTERN ou IN.EX. ou DP DAN)</li>\n" .
			"<li>Sexe (F ou M)</li>\n" .
			"</ul>\n";
	echo "<p>Veuillez pr�ciser le nom complet du fichier <b>g_eleves.csv</b>.</p>\n";
	echo "<form enctype='multipart/form-data' action='eleves.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";
	echo "<p><label for='en_tete' style='cursor:pointer;'>Si le fichier � importer comporte une premi�re ligne d'en-t�te (non vide) � ignorer, <br />cocher la case ci-contre</label>&nbsp;<input type='checkbox' name='en_tete' id='en_tete' value='yes' checked /></p>\n";
	echo "<p><input type='submit' value='Valider' />\n";
	echo "</form>\n";

} else {
	//
	// Quelque chose a �t� post�
	//
	if ($_POST['action'] == "save_data") {
		check_token(false);
		//
		// On enregistre les donn�es dans la base.
		// Le fichier a d�j� �t� affich�, et l'utilisateur est s�r de vouloir enregistrer
		//

		// Premi�re �tape : on vide les tables

		$j=0;
		while ($j < count($liste_tables_del)) {
			//if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
			$sql="SELECT count(*) FROM $liste_tables_del[$j];";
			$res_test_tab=mysql_query($sql);
			if($res_test_tab) {
				if (mysql_result($res_test_tab,0)!=0) {
					$sql="DELETE FROM $liste_tables_del[$j];";
					$del = @mysql_query($sql);
				}
			}
			$j++;
		}

		// Suppression des comptes d'�l�ves:
		$sql="DELETE FROM utilisateurs WHERE statut='eleves';";
		$del=mysql_query($sql);

		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;
		while (true) {

			$reg_nom = $_POST["ligne".$i."_nom"];
			$reg_prenom = $_POST["ligne".$i."_prenom"];
			$reg_naissance = $_POST["ligne".$i."_naissance"];
			$reg_id_int = $_POST["ligne".$i."_id_int"];
			$reg_id_nat = $_POST["ligne".$i."_id_nat"];
			$reg_etab_prec = $_POST["ligne".$i."_etab_prec"];
			$reg_double = $_POST["ligne".$i."_doublement"];
			$reg_regime = $_POST["ligne".$i."_regime"];
			$reg_sexe = $_POST["ligne".$i."_sexe"];

			//==========================
			// DEBUG
			//echo "<p>\$reg_nom=$reg_nom<br />\n";
			//echo "\$reg_prenom=$reg_prenom<br />\n";
			//echo "\$reg_id_int=$reg_id_int<br />\n";
			//==========================

			// On nettoie et on v�rifie :
			//$reg_nom = preg_replace("/[^A-Za-z .\-]/","",trim(strtoupper($reg_nom)));
			//$reg_nom = preg_replace("/[^A-Za-z .\-������������������������������]/","",trim(strtoupper($reg_nom)));
			$reg_nom = my_ereg_replace("�","AE",my_ereg_replace("�","ae",my_ereg_replace("�","OE",my_ereg_replace("�","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim(strtoupper($reg_nom)))))));

			if (strlen($reg_nom) > 50) $reg_nom = substr($reg_nom, 0, 50);
			//$reg_prenom = preg_replace("/[^A-Za-z .\-�������]/","",trim($reg_prenom));
			//$reg_prenom = preg_replace("/[^A-Za-z .\-������������������������������]/","",trim($reg_prenom));
			$reg_prenom = my_ereg_replace("�","AE",my_ereg_replace("�","ae",my_ereg_replace("�","OE",my_ereg_replace("�","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim($reg_prenom))))));

			if (strlen($reg_prenom) > 50) $reg_prenom = substr($reg_prenom, 0, 50);
			$naissance = explode("/", $reg_naissance);
			if (!preg_match("/[0-9]/", $naissance[0]) OR strlen($naissance[0]) > 2 OR strlen($naissance[0]) == 0) $naissance[0] = "00";
			if (strlen($naissance[0]) == 1) $naissance[0] = "0" . $naissance[0];

			if (!preg_match("/[0-9]/", $naissance[1]) OR strlen($naissance[1] OR strlen($naissance[1]) == 0) > 2) $naissance[1] = "00";
			if (strlen($naissance[1]) == 1) $naissance[1] = "0" . $naissance[1];

			if (!preg_match("/[0-9]/", $naissance[2]) OR strlen($naissance[2]) > 4 OR strlen($naissance[2]) == 3 OR strlen($naissance[2]) == 1) $naissance[2] = "00";
			if (strlen($naissance[2]) == 1) $naissance[2] = "0" . $naissance[2];

			//$reg_naissance = mktime(0, 0, 0, $naissance[1], $naissance[0], $naissance[2]);
			$reg_naissance = $naissance[2] . "-" . $naissance[1] . "-" . $naissance[0];
			$reg_id_int = preg_replace("/[^0-9]/","",trim($reg_id_int));

			$reg_id_nat = preg_replace("/[^A-Z0-9]/","",trim($reg_id_nat));

			$reg_etab_prec = preg_replace("/[^A-Z0-9]/","",trim($reg_etab_prec));

			$reg_double = trim(strtoupper($reg_double));
			if ($reg_double != "OUI" AND $reg_double != "NON") $reg_double = "NON";


			$reg_regime = trim(strtoupper($reg_regime));
			if ($reg_regime != "INTERN" AND $reg_regime != "EXTERN" AND $reg_regime != "IN.EX." AND $reg_regime != "DP DAN") $reg_regime = "DP DAN";

			if ($reg_sexe != "F" AND $reg_sexe != "M") $reg_sexe = "F";

			// Maintenant que tout est propre, on fait un test sur la table eleves pour s'assurer que l'�l�ve n'existe pas d�j�.
			// Ca permettra d'�viter d'enregistrer des �l�ves en double

			$test = mysql_result(mysql_query("SELECT count(login) FROM eleves WHERE elenoet = '" . $reg_id_int . "'"), 0);

			//==========================
			// DEBUG
			//echo "\$reg_id_int=$reg_id_int<br />\n";
			//echo "\$test=$test<br />\n";
			//==========================

			if ($test == 0) {
				// Test n�gatif : aucun �l�ve avec cet ID... on enregistre !

				// On g�n�re un login
				$reg_login = preg_replace("/\040/","_", $reg_nom);
				//====================================
				// AJOUT: boireaus
				$reg_login = strtr($reg_login,"������������������������������","aaaeeeeiioouuucAAAEEEEIIOOUUUC");
				//====================================
				$reg_login = preg_replace("/[^a-zA-Z]/", "", $reg_login);
				if (strlen($reg_login) > 9) $reg_login = substr($reg_login, 0, 9);
				//====================================
				// MODIF: boireaus
				//$reg_login .= "_" . substr($reg_prenom, 0, 1);
				$reg_login .= "_" . strtr(substr($reg_prenom, 0, 1),"������������������������������","aaaeeeeiioouuucAAAEEEEIIOOUUUC");
				//====================================
				$reg_login = strtoupper($reg_login);

				$p = 1;
				while (true) {
					$test_login = mysql_result(mysql_query("SELECT count(login) FROM eleves WHERE login = '" . $reg_login . "'"), 0);
					if ($test_login != 0) {
						$reg_login .= strtr(substr($reg_prenom, $p, 1), "������������������������������", "aaaeeeeiioouuucAAAEEEEIIOOUUUC");
						$p++;
					} else {
						break 1;
					}
					$reg_login = strtoupper($reg_login);
				}

				// Normalement on a maintenant un login dont on est s�r qu'il est unique...

				//==========================
				// DEBUG
				//echo "On va enregistrer l'�l�ve avec le login \$reg_login=$reg_login</p>\n";
				//==========================

				// On insert les donn�es

				$insert = mysql_query("INSERT INTO eleves SET " .
						"no_gep = '" . $reg_id_nat . "', " .
						"login = '" . $reg_login . "', " .
						"nom = '" . $reg_nom . "', " .
						"prenom = '" . $reg_prenom . "', " .
						"sexe = '" . $reg_sexe . "', " .
						"naissance = '" . $reg_naissance . "', " .
						"elenoet = '" . $reg_id_int . "', " .
						"ereno = '" . $reg_id_int . "'");

				if (!$insert) {
					$error++;
					echo mysql_error();
				} else {
					$total++;

					// On enregistre l'�tablissement d'origine, le r�gime, et si l'�l�ve est redoublant
					//============================================
					if (($reg_etab_prec != '')&&($reg_id_int != '')) {
						/*
							$insert2 = mysql_query("INSERT INTO j_eleves_etablissements SET id_eleve = '" . $reg_login . "', id_etablissement = '" . $reg_etab_prec . "'");

							if (!$insert2) {
								$error++;
								echo mysql_error();
							}
						*/

						if($gepiSchoolRne!="") {
							if($gepiSchoolRne!=$reg_etab_prec) {
								$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_id_int';";
								$test_etab=mysql_query($sql);
								if(mysql_num_rows($test_etab)==0){
									$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_id_int', id_etablissement='$reg_etab_prec';";
									$insert_etab=mysql_query($sql);
									if (!$insert_etab) {
										//echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'�l�ve $reg_nom $reg_prenom � l'�tablissement $reg_etab_prec.</p>\n";
										$error++;
										echo mysql_error();
									}
								}
								else {
									$sql="UPDATE j_eleves_etablissements SET id_etablissement='$reg_etab_prec' WHERE id_eleve='$reg_id_int';";
									$update_etab=mysql_query($sql);
									if (!$update_etab) {
										//echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'�l�ve $reg_nom $reg_prenom � l'�tablissement $reg_etab_prec.</p>\n";
										$error++;
										echo mysql_error();
									}
								}
							}
						}
						else {
							// Si le RNE de l'�tablissement courant (celui du GEPI) n'est pas renseign�, on ins�re les nouveaux enregistrements, mais on ne met pas � jour au risque d'�craser un enregistrement correct avec l'info que l'�l�ve de 1�re �tait en 2nde dans le m�me �tablissement.
							// Il suffira de faire un
							//       DELETE FROM j_eleves_etablissements WHERE id_etablissement='$gepiSchoolRne';
							// une fois le RNE renseign�.
							$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_id_int';";
							$test_etab=mysql_query($sql);
							if(mysql_num_rows($test_etab)==0){
								$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_id_int', id_etablissement='$reg_etab_prec';";
								$insert_etab=mysql_query($sql);
								if (!$insert_etab) {
									//echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'�l�ve $reg_nom $reg_prenom � l'�tablissement $reg_etab_prec.</p>\n";
									$error++;
									echo mysql_error();
								}
							}
						}

					}
					//============================================

					if ($reg_double == "OUI") {
						$reg_double = "R";
					} else {
						$reg_double = "-";
					}

					if ($reg_regime == "INTERN") {
						$reg_regime = "int.";
					} else if ($reg_regime == "EXTERN") {
						$reg_regime = "ext.";
					} else if ($reg_regime == "DP DAN") {
						$reg_regime = "d/p";
					} else if ($reg_regime == "IN.EX.") {
						$reg_regime = "i-e";
					}

					$insert3 = mysql_query("INSERT INTO j_eleves_regime SET login = '" . $reg_login . "', doublant = '" . $reg_double . "', regime = '" . $reg_regime . "'");
					if (!$insert3) {
						$error++;
						echo mysql_error();
					}
				}

			}
			$i++;
			if (!isset($_POST['ligne'.$i.'_nom'])) break 1;
		}

		if ($error > 0) echo "<p><font color=red>Il y a eu " . $error . " erreurs.</font></p>\n";
		if ($total > 0) echo "<p>" . $total . " �l�ves ont �t� enregistr�s.</p>\n";

		echo "<p><a href='index.php'>Revenir � la page pr�c�dente</a></p>\n";

		// On sauvegarde le t�moin du fait qu'il va falloir convertir pour remplir les nouvelles tables responsables:
		saveSetting("conv_new_resp_table", 0);

	} else if ($_POST['action'] == "upload_file") {
		check_token(false);
		//
		// Le fichier vient d'�tre envoy� et doit �tre trait�
		// On va donc afficher le contenu du fichier tel qu'il va �tre enregistr� dans Gepi
		// en proposant des champs de saisie pour modifier les donn�es si on le souhaite
		//

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

		// On v�rifie le nom du fichier... Ce n'est pas fondamentalement indispensable, mais
		// autant forcer l'utilisateur � �tre rigoureux
		if(strtolower($csv_file['name']) == "g_eleves.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas � ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
				echo "<p><a href='eleves.php'>Cliquer ici </a> pour recommencer !</p>\n";
			} else {

				// Fichier ouvert ! On attaque le traitement

				// On va stocker toutes les infos dans un tableau
				// Une ligne du CSV pour une entr�e du tableau
				$data_tab = array();

				//=========================
				// On lit une ligne pour passer la ligne d'ent�te:
				if($en_tete=="yes") {
					$ligne = fgets($fp, 4096);
				}
				//=========================

					$k = 0;
					$nat_num = array();
					while (!feof($fp)) {
						$ligne = fgets($fp, 4096);
						if(trim($ligne)!="") {

							$tabligne=explode(";",$ligne);

							// 0 : Nom
							// 1 : Pr�nom
							// 2 : Date de naissance
							// 3 : identifiant interne
							// 4 : identifiant national
							// 5 : �tablissement pr�c�dent
							// 6 : Doublement (OUI || NON)
							// 7 : R�gime : INTERN || EXTERN || IN.EX. || DP DAN
							// 8 : Sexe : F || M

							// On nettoie et on v�rifie :
							//=====================================
							// MODIF: boireaus
							//$tabligne[0] = preg_replace("/[^A-Za-z .\-]/","",trim(strtoupper($tabligne[0])));
							//$tabligne[0] = preg_replace("/[^A-Za-z .\-������������������������������]/","",trim(strtoupper($tabligne[0])));
							$tabligne[0] = my_ereg_replace("�","AE",my_ereg_replace("�","ae",my_ereg_replace("�","OE",my_ereg_replace("�","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim(strtoupper($tabligne[0])))))));
							//=====================================
							if (strlen($tabligne[0]) > 50) {$tabligne[0] = substr($tabligne[0], 0, 50);}

							//=====================================
							// MODIF: boireaus
							//$tabligne[1] = preg_replace("/[^A-Za-z .\-�������]/","",trim($tabligne[1]));
							//$tabligne[1] = preg_replace("/[^A-Za-z .\-������������������������������]/","",trim($tabligne[1]));
							$tabligne[1] = my_ereg_replace("�","AE",my_ereg_replace("�","ae",my_ereg_replace("�","OE",my_ereg_replace("�","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim($tabligne[1]))))));
							//=====================================
							if (strlen($tabligne[1]) > 50) $tabligne[1] = substr($tabligne[1], 0, 50);

							$naissance = explode("/", $tabligne[2]);
							if (!preg_match("/[0-9]/", $naissance[0]) OR strlen($naissance[0]) > 2 OR strlen($naissance[0]) == 0) $naissance[0] = "00";
							if (strlen($naissance[0]) == 1) $naissance[0] = "0" . $naissance[0];

							// Au cas o� la date de naissance serait vraiment mal fichue:
							if(!isset($naissance[1])) {
								$naissance[1]="00";
							}

							if (!preg_match("/[0-9]/", $naissance[1]) OR strlen($naissance[1] OR strlen($naissance[1]) == 0) > 2) $naissance[1] = "00";
							if (strlen($naissance[1]) == 1) $naissance[1] = "0" . $naissance[1];

							// Au cas o� la date de naissance serait vraiment mal fichue:
							if(!isset($naissance[2])) {
								$naissance[2]="0000";
							}

							if (!preg_match("/[0-9]/", $naissance[2]) OR strlen($naissance[2]) > 4 OR strlen($naissance[2]) == 3 OR strlen($naissance[2]) < 2) $naissance[2] = "0000";

							$tabligne[2] = $naissance[0] . "/" . $naissance[1] . "/" . $naissance[2];

							$tabligne[3] = preg_replace("/[^0-9]/","",trim($tabligne[3]));

							$tabligne[4] = preg_replace("/[^A-Z0-9]/","",trim($tabligne[4]));
							$tabligne[4] = preg_replace("/\"/", "", $tabligne[4]);

							$tabligne[5] = preg_replace("/[^A-Z0-9]/","",trim($tabligne[5]));
							$tabligne[5] = preg_replace("/\"/", "", $tabligne[5]);

							$tabligne[6] = trim(strtoupper($tabligne[6]));
							$tabligne[6] = preg_replace("/\"/", "", $tabligne[6]);
							if ($tabligne[6] != "OUI" AND $tabligne[6] != "NON") $tabligne[6] = "NON";


							$tabligne[7] = trim(strtoupper($tabligne[7]));
							$tabligne[7] = preg_replace("/\"/", "", $tabligne[7]);
							if ($tabligne[7] != "INTERN" AND $tabligne[7] != "EXTERN" AND $tabligne[7] != "IN.EX." AND $tabligne[7] != "DP DAN") $tabligne[7] = "DP DAN";

							$tabligne[8] = trim(strtoupper($tabligne[8]));
							$tabligne[8] = preg_replace("/\"/", "", $tabligne[8]);
							if ($tabligne[8] != "F" AND $tabligne[8] != "M") $tabligne[8] = "F";

							if ($tabligne[4] != "" AND !in_array($tabligne[4], $nat_num)) {
								$nat_num[] = $tabligne[4];
								$data_tab[$k] = array();
								$data_tab[$k]["nom"] = $tabligne[0];
								$data_tab[$k]["prenom"] = $tabligne[1];
								$data_tab[$k]["naissance"] = $tabligne[2];
								$data_tab[$k]["id_int"] = $tabligne[3];
								$data_tab[$k]["id_nat"] = $tabligne[4];
								$data_tab[$k]["etab_prec"] = $tabligne[5];
								$data_tab[$k]["doublement"] = $tabligne[6];
								$data_tab[$k]["regime"] = $tabligne[7];
								$data_tab[$k]["sexe"] = $tabligne[8];
								// On incr�mente pour le prochain enregistrement
								$k++;
							}
						}
					}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout �a.

				echo "<form enctype='multipart/form-data' action='eleves.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table class='boireaus' border='1' summary='Tableau des �l�ves'>\n";
				echo "<tr><th>Nom</th><th>Pr�nom</th><th>Sexe</th><th>Date de naissance</th><th>n� �tab.</th><th>n� nat.</th><th>Code �tab.</th><th>Double.</th><th>R�gime</th></tr>\n";

				$alt=1;
				for ($i=0;$i<$k;$i++) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td>\n";
					echo $data_tab[$i]["nom"];
					echo "<input type='hidden' name='ligne".$i."_nom' value='" . $data_tab[$i]["nom"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["prenom"];
					echo "<input type='hidden' name='ligne".$i."_prenom' value='" . $data_tab[$i]["prenom"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["sexe"];
					echo "<input type='hidden' name='ligne".$i."_sexe' value='" . $data_tab[$i]["sexe"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["naissance"];
					echo "<input type='hidden' name='ligne".$i."_naissance' value='" . $data_tab[$i]["naissance"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["id_int"];
					echo "<input type='hidden' name='ligne".$i."_id_int' value='" . $data_tab[$i]["id_int"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["id_nat"];
					echo "<input type='hidden' name='ligne".$i."_id_nat' value='" . $data_tab[$i]["id_nat"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["etab_prec"];
					echo "<input type='hidden' name='ligne".$i."_etab_prec' value='" . $data_tab[$i]["etab_prec"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["doublement"];
					echo "<input type='hidden' name='ligne".$i."_doublement' value='" . $data_tab[$i]["doublement"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["regime"];
					echo "<input type='hidden' name='ligne".$i."_regime' value='" . $data_tab[$i]["regime"] . "' />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}

				echo "</table>\n";
				echo "$k �l�ves ont �t� d�tect�s dans le fichier.<br />\n";
				echo "<input type='submit' value='Enregistrer' />\n";

				echo "</form>\n";
			}

		} else if (trim($csv_file['name'])=='') {

			echo "<p>Aucun fichier n'a �t� s�lectionn� !<br />\n";
			echo "<a href='eleves.php'>Cliquer ici </a> pour recommencer !</p>\n";

		} else {
			echo "<p>Le fichier s�lectionn� n'est pas valide !<br />";
			echo "<a href='eleves.php'>Cliquer ici </a> pour recommencer !</p>";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>