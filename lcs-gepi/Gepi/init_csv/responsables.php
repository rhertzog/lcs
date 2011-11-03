<?php
@set_time_limit(0);
/*
* $Id: responsables.php 8558 2011-10-28 09:46:34Z crob $
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

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_resp;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e : Importation des �l�ves - Etape 1";
require_once("../lib/header.inc");
//************** FIN EN-TETE ***************

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

//debug_var();

?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Premi�re phase d'initialisation<br />Importation des responsables d'�l�ves</h3></center>";


if (!isset($_POST["action"])) {
	//
	// On s�lectionne le fichier � importer
	//

	echo "<p>Vous allez effectuer la deuxi�me �tape : elle consiste � importer le fichier <b>g_responsables.csv</b> contenant les donn�es �l�ves.</p>\n";
	echo "<p>Les champs suivants doivent �tre pr�sents, dans l'ordre, et <b>s�par�s par un point-virgule</b> :</p>\n";
	echo "<ul><li>Identifiant �l�ve interne � l'�tablissement (n�, et non login) <b>(*)</b></li>\n" .
			"<li>Nom du responsable <b>(*)</b></li>\n" .
			"<li>Pr�nom</li>\n" .
			"<li>Civilit�</li>\n" .
			"<li>Ligne 1 adresse</li>\n" .
			"<li>Ligne 2 adresse</li>\n" .
			"<li>Code postal</li>\n" .
			"<li>Commune</li>\n" .
			"</ul>\n";
	echo "<p>Veuillez pr�ciser le nom complet du fichier <b>g_responsables.csv</b>.</p>\n";
	echo "<form enctype='multipart/form-data' action='responsables.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";
    echo "<p><label for='en_tete' style='cursor:pointer;'>Si le fichier � importer comporte une premi�re ligne d'en-t�te (non vide) � ignorer, <br />cocher la case ci-contre</label>&nbsp;<input type='checkbox' name='en_tete' id='en_tete' value='yes' checked /></p>\n";
	echo "<p><input type='submit' value='Valider' />\n";
	echo "</form>\n";

	echo "<p><i>NOTE:</i> Les champs marqu�s d'un <b>(*)</b> doivent �tre non vides.</p>\n";
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
			if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
			}
			$j++;
		}

		// Suppression des comptes de responsables:
		$sql="DELETE FROM utilisateurs WHERE statut='responsable';";
		$del=mysql_query($sql);

		$sql="SELECT * FROM temp_responsables;";
		$res_temp=mysql_query($sql);
		if(mysql_num_rows($res_temp)==0) {
			echo "<p style='color:red'>ERREUR&nbsp;: Aucun responsable n'a �t� trouv�&nbsp;???</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		//$go = true;
		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;
		//while ($go) {
		while ($lig=mysql_fetch_object($res_temp)) {
			/*
			$reg_id_eleve = $_POST["ligne".$i."_id_eleve"];
			$reg_nom = $_POST["ligne".$i."_nom"];
			$reg_prenom = $_POST["ligne".$i."_prenom"];
			$reg_civilite = $_POST["ligne".$i."_civilite"];
			$reg_adresse1 = $_POST["ligne".$i."_adresse1"];
			$reg_adresse2 = $_POST["ligne".$i."_adresse2"];
			$reg_code_postal = $_POST["ligne".$i."_code_postal"];
			$reg_commune = $_POST["ligne".$i."_commune"];
			*/
			$reg_id_eleve = $lig->elenoet;
			$reg_nom = $lig->nom;
			$reg_prenom = $lig->prenom;
			$reg_civilite = $lig->civilite;
			$reg_adresse1 = $lig->adresse1;
			$reg_adresse2 = $lig->adresse2;
			$reg_code_postal = $lig->code_postal;
			$reg_commune = $lig->commune;

			// On nettoie et on v�rifie :
			$reg_id_eleve = preg_replace("/[^0-9]/","",trim($reg_id_eleve));

			//$reg_nom = preg_replace("/[^A-Za-z .\-]/","",trim(strtoupper($reg_nom)));
			$reg_nom = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim(strtoupper($reg_nom)))))));
			if (strlen($reg_nom) > 50) $reg_nom = substr($reg_nom, 0, 50);
			//$reg_prenom = preg_replace("/[^A-Za-z .\-�������]/","",trim($reg_prenom));
			$reg_prenom = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim($reg_prenom))))));

			// ���������������������զ����ݾ�������������������������������

			if (strlen($reg_prenom) > 50) $reg_prenom = substr($reg_prenom, 0, 50);

			//if ($reg_civilite != "M." AND $reg_civilite != "MME" AND $reg_civilite != "MLLE") $reg_civilite = "M.";
			if ($reg_civilite != "M." AND $reg_civilite != "MME" AND $reg_civilite != "MLLE") { $reg_civilite = "";}

			//$reg_adresse1 = preg_replace("/[^A-Za-z0-9 .\-�������]/","",trim($reg_adresse1));
			$reg_adresse1 = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z0-9 .\-������������������������������]/","",trim(strtr($reg_adresse1,"'"," ")))))));

			if (strlen($reg_adresse1) > 50) $reg_adresse1 = substr($reg_adresse1, 0, 50);

			//$reg_adresse2 = preg_replace("/[^A-Za-z0-9 .\-�������]/","",trim($reg_adresse2));
			$reg_adresse2 = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z0-9 .\-������������������������������]/","",trim(strtr($reg_adresse2,"'"," ")))))));
			if (strlen($reg_adresse2) > 50) $reg_adresse2 = substr($reg_adresse2, 0, 50);

			$reg_code_postal = preg_replace("/[^0-9]/","",trim($reg_code_postal));
			if (strlen($reg_code_postal) > 6) $reg_code_postal = substr($reg_code_postal, 0, 6);

			//$reg_commune = preg_replace("/[^A-Za-z0-9 .\-�������]/","",trim($reg_commune));
			$reg_commune = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z0-9 .\-������������������������������]/","",trim(strtr($reg_commune,"'"," ")))))));
			if (strlen($reg_commune) > 50) $reg_commune = substr($reg_commune, 0, 50);

			// On v�rifie que l'�l�ve existe
			$test = mysql_result(mysql_query("SELECT count(login) FROM eleves WHERE elenoet = '" . $reg_id_eleve . "'"), 0);

			if($reg_id_eleve==""){
				echo "<p>Erreur : L'identifiant �l�ve est vide pour $reg_prenom $reg_nom</p>\n";
			}
			else{
				if($reg_nom==""){
					echo "<p>Erreur : Le nom du responsable est vide pour l'�l�ve $reg_id_eleve</p>\n";
				}
				else{
					if ($test == 0 OR !$test) {
						// Test n�gatif : aucun �l�ve avec cet ID... On envoie un message d'erreur.
						echo "<p>Erreur : l'�l�ve avec l'identifiant interne " . $reg_id_eleve . " n'existe pas dans Gepi.</p>\n";
					} else {
						// Test positif : on peut donc enregistrer les donn�es de responsable.

						// On regarde si une entr�e existe d�j� pour l'�l�ve en question
						$test = mysql_query("SELECT ereno, nom1, nom2 FROM responsables WHERE ereno = '" . $reg_id_eleve . "'");
						$insert = null;

						if (mysql_num_rows($test) == 0) {
							// Aucune entr�e n'existe. On enregistre le responsable comme premier responsable

							$sql="INSERT INTO responsables SET " .
								"ereno = '" . $reg_id_eleve . "', " .
								"nom1 = '" . $reg_nom . "', " .
								"prenom1 = '" . $reg_prenom . "', " .
								"adr1 = '" . $reg_adresse1 . "', " .
								"adr1_comp = '" . $reg_adresse2 . "', " .
								"commune1 = '" . $reg_commune . "', " .
								"cp1 = '" . $reg_code_postal . "'";
							$insert = mysql_query($sql);

						} else {
							// Une entr�e existe
							// On regarde si le responsable 1 a d�j� �t� saisi
							if (mysql_result($test, 0, "nom1") == "") {
								$sql="UPDATE responsables SET " .
									"nom1 = '" . $reg_nom . "', " .
									"prenom1 = '" . $reg_prenom . "', " .
									"adr1 = '" . $reg_adresse1 . "', " .
									"adr1_comp = '" . $reg_adresse2 . "', " .
									"commune1 = '" . $reg_commune . "', " .
									"cp1 = '" . $reg_code_postal . "' " .
									"WHERE " .
									"ereno = '" . $reg_id_eleve . "'";
								$insert = mysql_query($sql);

							} else if (mysql_result($test, 0, "nom2") == "") {
								$sql="UPDATE responsables SET " .
									"nom2 = '" . $reg_nom . "', " .
									"prenom2 = '" . $reg_prenom . "', " .
									"adr2 = '" . $reg_adresse1 . "', " .
									"adr2_comp = '" . $reg_adresse2 . "', " .
									"commune2 = '" . $reg_commune . "', " .
									"cp2 = '" . $reg_code_postal . "' " .
									"WHERE " .
									"ereno = '" . $reg_id_eleve . "'";
								$insert = mysql_query($sql);

							} else {
								// Erreur ! Les deux responsables ont d�j� �t� saisis...
								echo "<p>Erreur pour " . $reg_prenom . " " . $reg_nom . " ! Les deux responsables ont d�j� �t� saisis.</p>\n";
							}


						}

						if ($insert == false) {
							$error++;
							$erreur_mysql=mysql_error();
							if($erreur_mysql!=""){echo "<p><font color='red'>".$erreur_mysql."</font></p>\n";}
							//echo "<p>$sql</p>\n";
						} else {
							$total++;
						}
					}
				}
			}

			$i++;
			//if (!isset($_POST['ligne'.$i.'_nom'])) $go = false;
		}

		if ($error > 0) echo "<p><font color='red'>Il y a eu " . $error . " erreurs.</font></p>\n";
		if ($total > 0) echo "<p>" . $total . " responsables ont �t� enregistr�s.</p>\n";

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
		if(strtolower($csv_file['name']) == "g_responsables.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas � ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
				echo "<p><a href='responsables.php'>Cliquer ici </a> pour recommencer !</p>\n";
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
				while (!feof($fp)) {
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!="") {

						$tabligne=explode(";",$ligne);

						// 0 : Identifiant interne �l�ve
						// 1 : Nom
						// 2 : Pr�nom
						// 3 : Civilit�
						// 4 : Ligne 1 adresse
						// 5 : Ligne 2 adresse
						// 6 : Code postal
						// 7 : Commune

							// On nettoie et on v�rifie :
						$tabligne[0] = preg_replace("/[^0-9]/","",trim($tabligne[0]));

						//$tabligne[1] = preg_replace("/[^A-Za-z .\-]/","",trim(strtoupper($tabligne[1])));
						$tabligne[1] = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim(strtoupper($tabligne[1])))))));
						if (strlen($tabligne[1]) > 50) $tabligne[1] = substr($tabligne[1], 0, 50);

						//$tabligne[2] = preg_replace("/[^A-Za-z .\-�������]/","",trim($tabligne[2]));
						$tabligne[2] = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim($tabligne[2]))))));
						if (strlen($tabligne[2]) > 50) $tabligne[2] = substr($tabligne[2], 0, 50);

						//if ($tabligne[3] != "M." AND $tabligne[3] != "MME" AND $tabligne[3] != "MLLE") $tabligne[3] = "M.";
						if ($tabligne[3] != "M." AND $tabligne[3] != "MME" AND $tabligne[3] != "MLLE") { $tabligne[3] = "";}

						//$tabligne[4] = preg_replace("/[^A-Za-z0-9 .\-�������]/","",trim($tabligne[4]));
						$tabligne[4] = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z0-9 .\-������������������������������]/","",trim(strtr($tabligne[4],"'"," ")))))));
						if (strlen($tabligne[4]) > 50) $tabligne[4] = substr($tabligne[4], 0, 50);

						//$tabligne[5] = preg_replace("/[^A-Za-z0-9 .\-�������]/","",trim($tabligne[5]));
						$tabligne[5] = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z0-9 .\-������������������������������]/","",trim(strtr($tabligne[5],"'"," ")))))));
						if (strlen($tabligne[5]) > 50) $tabligne[5] = substr($tabligne[5], 0, 50);

						$tabligne[6] = preg_replace("/[^0-9]/","",trim($tabligne[6]));
						if (strlen($tabligne[6]) > 6) $tabligne[6] = substr($tabligne[6], 0, 6);

						//$tabligne[7] = preg_replace("/[^A-Za-z0-9 .\-�������]/","",trim($tabligne[7]));
						$tabligne[7] = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z0-9 .\-������������������������������]/","",trim(strtr($tabligne[7],"'"," ")))))));
						if (strlen($tabligne[7]) > 50) $tabligne[7] = substr($tabligne[7], 0, 50);


						$data_tab[$k] = array();


						$data_tab[$k]["id_eleve"] = $tabligne[0];
						$data_tab[$k]["nom"] = $tabligne[1];
						$data_tab[$k]["prenom"] = $tabligne[2];
						$data_tab[$k]["civilite"] = $tabligne[3];
						$data_tab[$k]["adresse1"] = $tabligne[4];
						$data_tab[$k]["adresse2"] = $tabligne[5];
						$data_tab[$k]["code_postal"] = $tabligne[6];
						$data_tab[$k]["commune"] = $tabligne[7];

						$k++;
					}
					//$k++;
				}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout �a.

				$sql="CREATE TABLE IF NOT EXISTS temp_responsables (
				id int(11) NOT NULL auto_increment,
				elenoet varchar(50) NOT NULL default '', 
				nom varchar(50) NOT NULL default '', 
				prenom varchar(50) NOT NULL default '', 
				civilite varchar(50) NOT NULL default '', 
				adresse1 varchar(100) NOT NULL default '', 
				adresse2 varchar(100) NOT NULL default '', 
				code_postal varchar(6) NOT NULL default '', 
				commune varchar(50) NOT NULL default '',
				PRIMARY KEY  (id)
				);";
				$create_table = mysql_query($sql);

				$sql="TRUNCATE TABLE temp_responsables;";
				$vide_table = mysql_query($sql);

				$nb_error=0;

				echo "<form enctype='multipart/form-data' action='responsables.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table class='boireaus' summary='Tableau des responsables'>\n";
				echo "<tr><th>ID �l�ve</th><th>Nom</th><th>Pr�nom</th><th>Civilit�</th><th>Ligne 1 adresse</th><th>Ligne 2 adresse</th><th>Code postal</th><th>Commune</th></tr>\n";

				$alt=1;
				for ($i=0;$i<$k-1;$i++) {
					$alt=$alt*(-1);
                    echo "<tr class='lig$alt'>\n";
					echo "<td";
					if($data_tab[$i]["id_eleve"]==""){
						echo " style='color:red;'";
					}
					echo ">\n";

					$sql="INSERT INTO temp_responsables SET elenoet='".addslashes($data_tab[$i]["id_eleve"])."',
					nom='".addslashes($data_tab[$i]["nom"])."',
					prenom='".addslashes($data_tab[$i]["prenom"])."',
					civilite='".addslashes($data_tab[$i]["civilite"])."',
					adresse1='".addslashes($data_tab[$i]["adresse1"])."',
					adresse2='".addslashes($data_tab[$i]["adresse2"])."',
					commune='".addslashes($data_tab[$i]["commune"])."',
					code_postal='".addslashes($data_tab[$i]["code_postal"])."';";
					$insert=mysql_query($sql);
					if(!$insert) {
						echo "<span style='color:red'>";
						echo $data_tab[$i]["id_eleve"];
 						echo "</span>";
						$nb_error++;
					}
					else {
						echo $data_tab[$i]["id_eleve"];
					}
					//echo "<input type='hidden' name='ligne".$i."_id_eleve' value='" . $data_tab[$i]["id_eleve"] . "' />\n";
					echo "</td>\n";
					echo "<td";
					if($data_tab[$i]["id_eleve"]==""){
						echo " style='color:red;'";
					}
					echo ">\n";
					echo $data_tab[$i]["nom"];
					//echo "<input type='hidden' name='ligne".$i."_nom' value='" . $data_tab[$i]["nom"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["prenom"];
					//echo "<input type='hidden' name='ligne".$i."_prenom' value='" . $data_tab[$i]["prenom"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["civilite"];
					//echo "<input type='hidden' name='ligne".$i."_civilite' value='" . $data_tab[$i]["civilite"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["adresse1"];
					//echo "<input type='hidden' name='ligne".$i."_adresse1' value='" . $data_tab[$i]["adresse1"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["adresse2"];
					//echo "<input type='hidden' name='ligne".$i."_adresse2' value='" . $data_tab[$i]["adresse2"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["code_postal"];
					//echo "<input type='hidden' name='ligne".$i."_code_postal' value='" . $data_tab[$i]["code_postal"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["commune"];
					//echo "<input type='hidden' name='ligne".$i."_commune' value='" . $data_tab[$i]["commune"] . "' />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}

				echo "</table>\n";

				if($nb_error>0) {
					echo "<span style='color:red'>$nb_error erreur(s) d�tect�e(s) lors de la pr�paration.</style><br />\n";
				}

				echo "<input type='submit' value='Enregistrer' />\n";

				echo "</form>\n";
			}

		} else if (trim($csv_file['name'])=='') {

			echo "<p>Aucun fichier n'a �t� s�lectionn� !<br />\n";
			echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</p>\n";

		} else {
			echo "<p>Le fichier s�lectionn� n'est pas valide !<br />\n";
			echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</p>\n";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>