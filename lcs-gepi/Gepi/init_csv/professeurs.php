<?php
@set_time_limit(0);
/*
* $Id: professeurs.php 6074 2010-12-08 15:43:17Z crob $
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
//"absences",
//"aid",
//"aid_appreciations",
//"aid_config",
//"avis_conseil_classe",
//"classes",
//"droits",
//"eleves",
//"responsables",
//"etablissements",
//"j_aid_eleves",
"j_aid_utilisateurs",
"j_groupes_professeurs",
"j_aid_utilisateurs_gest",
"j_aidcateg_super_gestionnaires",
//"j_eleves_classes",
//"j_eleves_etablissements",
"j_eleves_professeurs",
//"j_eleves_regime",
//"j_professeurs_matieres",
//"log",
//"matieres",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
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
$titre_page = "Outil d'initialisation de l'ann�e : Importation des professeurs";
require_once("../lib/header.inc");
//************** FIN EN-TETE ***************

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Quatri�me phase d'initialisation<br />Importation des professeurs</h3></center>\n";


if (!isset($_POST["action"])) {
	//
	// On s�lectionne le fichier � importer
	//

	echo "<p>Vous allez effectuer la quatri�me �tape : elle consiste � importer le fichier <b>g_professeurs.csv</b> contenant les donn�es des professeurs.</p>\n";
	echo "<p>Les champs suivants doivent �tre pr�sents, dans l'ordre, et <b>s�par�s par un point-virgule</b> : </p>\n";
	echo "<ul><li>Nom</li>\n" .
			"<li>Pr�nom</li>\n" .
			"<li>Civilit�</li>\n" .
			"<li>Adresse e-mail</li>\n" .
			"</ul>\n";
	echo "<p>Veuillez pr�ciser le nom complet du fichier <b>g_professeurs.csv</b>.</p>\n";
	echo "<form enctype='multipart/form-data' action='professeurs.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";

	echo "<br />\n";
	echo "<label for='en_tete' style='cursor:pointer;'>Si le fichier � importer comporte une premi�re ligne d'en-t�te (non vide) � ignorer, <br />cocher la case ci-contre</label>&nbsp;<input type='checkbox' name='en_tete' id='en_tete' value='yes' checked />\n";


	echo "<br /><br /><p>Quelle formule appliquer pour la g�n�ration du login ?<br />\n";
	echo "<input type='radio' name='login_mode' value='name' checked /> nom";
	echo "<br />\n<input type='radio' name='login_mode' value='name8' /> nom (tronqu� � 8 caract�res)";
	echo "<br />\n<input type='radio' name='login_mode' value='fname8' /> pnom (tronqu� � 8 caract�res)";
	echo "<br />\n<input type='radio' name='login_mode' value='fname19' /> pnom (tronqu� � 19 caract�res)";
	echo "<br />\n<input type='radio' name='login_mode' value='firstdotname' /> prenom.nom";
	echo "<br />\n<input type='radio' name='login_mode' value='firstdotname19' /> prenom.nom (tronqu� � 19 caract�res)";
	echo "<br />\n<input type='radio' name='login_mode' value='namef8' /> nomp (tronqu� � 8 caract�res)";
	echo "<br />\n<input type='radio' name='login_mode' value='lcs' /> pnom (fa�on LCS)";
	echo "<br />\n</p>\n<p>Quel mode d'authentification est utilis� ?  (laissez 'Gepi' si vous ne savez pas de quoi il s'agit)</p>\n";
	echo "<p>\n<input type='radio' name='sso' value='gepi' checked /> Gepi";
	echo "<br />\n<input type='radio' name='sso' value='sso' /> SSO (aucun mot de passe ne sera g�n�r�)";
	echo "<br />\n<input type='radio' name='sso' value='ldap' /> Ldap (aucun mot de passe ne sera g�n�r�)</p>\n";
	echo "<p><input type='submit' value='Valider' /></p>\n";
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

		// On passe tous les utilisateurs en etat "inactif"

		$res = mysql_query("UPDATE utilisateurs SET etat='inactif' WHERE statut = 'professeur'");


		$go = true;
		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;
		$total_deja_presents = 0;
		while ($go) {

			$reg_nom = $_POST["ligne".$i."_nom"];
			$reg_prenom = $_POST["ligne".$i."_prenom"];
			$reg_civilite = $_POST["ligne".$i."_civilite"];
			$reg_email = $_POST["ligne".$i."_email"];
			$reg_login = $_POST["ligne".$i."_login"];
			$reg_sso = $_POST["ligne".$i."_sso"];

			// On nettoie et on v�rifie :
			//$reg_nom = preg_replace("/[^A-Za-z .\-]/","",trim(strtoupper($reg_nom)));
			$reg_nom = my_ereg_replace("�","AE",my_ereg_replace("�","ae",my_ereg_replace("�","OE",my_ereg_replace("�","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim(strtoupper($reg_nom)))))));
			if (strlen($reg_nom) > 50) $reg_nom = substr($reg_nom, 0, 50);
			//$reg_prenom = preg_replace("/[^A-Za-z .\-�������]/","",trim($reg_prenom));
			$reg_prenom = my_ereg_replace("�","AE",my_ereg_replace("�","ae",my_ereg_replace("�","OE",my_ereg_replace("�","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim($reg_prenom))))));
			if (strlen($reg_prenom) > 50) $reg_prenom = substr($reg_prenom, 0, 50);

			//if ($reg_civilite != "M." AND $reg_civilite != "MME" AND $reg_civilite != "MLLE") $reg_civilite = "M.";
			if ($reg_civilite != "M." AND $reg_civilite != "MME" AND $reg_civilite != "MLLE") { $reg_civilite = "";}

			if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $reg_email)) $reg_email = "-";

			$reg_login = preg_replace("/[^A-Za-z0-9._]/","",trim(strtoupper($reg_login)));
			if (strlen($reg_login) > 50) $reg_login = substr($reg_login, 0, 50);

			// Maintenant que tout est propre, on fait un test pour voir si le compte n'existe pas d�j�

			$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $reg_login . "'"), 0);

			if ($test == 0) {
				// Test n�gatif : aucun professeur avec ce login. On enregistre.

			$reg_password = "";
			switch($reg_sso){
				case "ldap":
				$auth_mode = "ldap";
				$change_mdp = "n";
				break;
				case "sso":
				$auth_mode = "sso";
				$change_mdp = "n";
				break;
				default:
				$auth_mode = "gepi";
				$change_mdp = "y";
				// On g�n�re un password :
				$feed = "0123456789abcdefghijklmnopqrstuvwxyz";
					for ($t=0; $t < 20; $t++){
						$reg_password .= substr($feed, rand(0, strlen($feed)-1), 1);
					}
					$reg_password = md5($reg_password);
				break;
			}


				$insert = mysql_query("INSERT INTO utilisateurs SET " .
						"login = '" . $reg_login . "', " .
						"nom = '" . $reg_nom . "', " .
						"prenom = '" . $reg_prenom . "', " .
						"civilite = '" . $reg_civilite . "', " .
						"password = '" . $reg_password . "', " .
						"email = '" . $reg_email . "', " .
						"statut = 'professeur', " .
						"etat = 'actif', " .
						"change_mdp = '" . $change_mdp . "', " .
						"auth_mode = '" . $auth_mode . "' " );

				if (!$insert) {
					$error++;
					echo mysql_error();
				} else {
					$total++;
				}
			} else {
				// Le login existe d�j�. On passe l'utilisateur � nouveau en �tat 'actif'
				$res = mysql_query("UPDATE utilisateurs SET etat = 'actif' WHERE login = '" . $reg_login . "'");
				$total_deja_presents++;
			}


			$i++;
			if (!isset($_POST['ligne'.$i.'_nom'])) {$go = false;}
		}

		if ($error > 0) {echo "<p><font color=red>Il y a eu " . $error . " erreurs.</font></p>\n";}
		if ($total > 0) {echo "<p>" . $total . " professeurs ont �t� enregistr�s.</p>\n";}
		if($total_deja_presents>0) {echo "<p>" . $total_deja_presents . " professeurs d�j� pr�sents.</p>\n";}

		echo "<p><a href='index.php'>Revenir � la page pr�c�dente</a></p>\n";


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
		if(strtolower($csv_file['name']) == "g_professeurs.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas � ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
				echo "<p><a href='professeurs.php'>Cliquer ici </a> pour recommencer !</p>\n";
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

							// 0 : Nom
							// 1 : Pr�nom
							// 2 : Civilit�
							// 3 : Adresse email

							// On nettoie et on v�rifie :
							//$tabligne[0] = preg_replace("/[^A-Za-z .\-]/","",trim(strtoupper($tabligne[0])));
							$tabligne[0] = my_ereg_replace("�","AE",my_ereg_replace("�","ae",my_ereg_replace("�","OE",my_ereg_replace("�","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim(strtoupper($tabligne[0])))))));
							if (strlen($tabligne[0]) > 50) $tabligne[0] = substr($tabligne[0], 0, 50);

							//$tabligne[1] = preg_replace("/[^A-Za-z .\-�������]/","",trim($tabligne[1]));
							$tabligne[1] = my_ereg_replace("�","AE",my_ereg_replace("�","ae",my_ereg_replace("�","OE",my_ereg_replace("�","oe",preg_replace("/[^A-Za-z .\-������������������������������]/","",trim($tabligne[1]))))));
							if (strlen($tabligne[1]) > 50) $tabligne[1] = substr($tabligne[1], 0, 50);

							//if ($tabligne[2] != "M." AND $tabligne[2] != "MME" AND $tabligne[2] != "MLLE") $tabligne[2] = "M.";
							if ($tabligne[2] != "M." AND $tabligne[2] != "MME" AND $tabligne[2] != "MLLE") { $tabligne[2] = "";}

							$tabligne[3] = preg_replace("/\"/", "", trim($tabligne[3]));
							if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $tabligne[3])) $tabligne[3] = "-";


							// On regarde si le prof existe d�j� dans la base
							$test = mysql_query("SELECT login FROM utilisateurs WHERE (nom = '" . $tabligne[0] . "' AND prenom = '" . $tabligne[1] . "')");
							$prof_exists = false;
							if (mysql_num_rows($test) == 0) {

								// On g�n�re le login

								$reg_nom_login = preg_replace("/\040/","_", $tabligne[0]);
								$reg_prenom_login = strtr($tabligne[1], "�������", "eeueiae");
								$reg_prenom_login = preg_replace("/[^a-zA-Z.\-]/", "", $reg_prenom_login);
								if ($_POST['login_mode'] == "name") {
										$temp1 = $reg_nom_login;
										$temp1 = strtoupper($temp1);
										$temp1 = my_ereg_replace(" ","", $temp1);
										$temp1 = my_ereg_replace("-","_", $temp1);
										$temp1 = my_ereg_replace("'","", $temp1);
										//$temp1 = substr($temp1,0,8);

									} elseif ($_POST['login_mode'] == "name8") {
										$temp1 = $reg_nom_login;
										$temp1 = strtoupper($temp1);
										$temp1 = my_ereg_replace(" ","", $temp1);
										$temp1 = my_ereg_replace("-","_", $temp1);
										$temp1 = my_ereg_replace("'","", $temp1);
										$temp1 = substr($temp1,0,8);
									} elseif ($_POST['login_mode'] == "fname8") {
										$temp1 = $reg_prenom_login{0} . $reg_nom_login;
										$temp1 = strtoupper($temp1);
										$temp1 = my_ereg_replace(" ","", $temp1);
										$temp1 = my_ereg_replace("-","_", $temp1);
										$temp1 = my_ereg_replace("'","", $temp1);
										$temp1 = substr($temp1,0,8);
									} elseif ($_POST['login_mode'] == "fname19") {
										$temp1 = $reg_prenom_login{0} . $reg_nom_login;
										$temp1 = strtoupper($temp1);
										$temp1 = my_ereg_replace(" ","", $temp1);
										$temp1 = my_ereg_replace("-","_", $temp1);
										$temp1 = my_ereg_replace("'","", $temp1);
										$temp1 = substr($temp1,0,19);
									} elseif ($_POST['login_mode'] == "firstdotname") {

										$temp1 = $reg_prenom_login . "." . $reg_nom_login;
										$temp1 = strtoupper($temp1);

									$temp1 = my_ereg_replace(" ","", $temp1);
										$temp1 = my_ereg_replace("-","_", $temp1);
										$temp1 = my_ereg_replace("'","", $temp1);
										//$temp1 = substr($temp1,0,19);
									} elseif ($_POST['login_mode'] == "firstdotname19") {
										$temp1 = $reg_prenom_login . "." . $reg_nom_login;
										$temp1 = strtoupper($temp1);
										$temp1 = my_ereg_replace(" ","", $temp1);
										$temp1 = my_ereg_replace("-","_", $temp1);
										$temp1 = my_ereg_replace("'","", $temp1);
										$temp1 = substr($temp1,0,19);
									} elseif ($_POST['login_mode'] == "namef8") {
										$temp1 =  substr($reg_nom_login,0,7) . $reg_prenom_login{0};
										$temp1 = strtoupper($temp1);
										$temp1 = my_ereg_replace(" ","", $temp1);
										$temp1 = my_ereg_replace("-","_", $temp1);
										$temp1 = my_ereg_replace("'","", $temp1);
										//$temp1 = substr($temp1,0,8);
									} elseif ($_POST['login_mode'] == "lcs") {
										$nom = $reg_nom_login;
									$nom = strtolower($nom);
									if (preg_match("/\s/",$nom)) {
										$noms = preg_split("/\s/",$nom);
										$nom1 = $noms[0];
										if (strlen($noms[0]) < 4) {
											$nom1 .= "_". $noms[1];
											$separator = " ";
											} else {
											$separator = "-";
											}
										} else {
										$nom1 = $nom;
											$sn = ucfirst($nom);
										}
										$firstletter_nom = $nom1{0};
										$firstletter_nom = strtoupper($firstletter_nom);
										$prenom = $reg_prenom_login;
										$prenom1 = $affiche[1]{0};
										$temp1 = $prenom1 . $nom1;
									}

									$login_prof = $temp1;
									// On teste l'unicit� du login que l'on vient de cr�er
									$m = 2;
									$test_unicite = 'no';
									$temp = $login_prof;
									while ($test_unicite != 'yes') {
										$test_unicite = test_unique_login($login_prof);
										if ($test_unicite != 'yes') {
											$login_prof = $temp.$m;
											$m++;
										}
									}
									$login_prof = substr($login_prof, 0, 50);
									$login_prof = preg_replace("/[^A-Za-z0-9._]/","",trim(strtoupper($login_prof)));
							} else {
								// Le prof semble d�j� exister. On r�cup�re son login actuel
								$login_prof = mysql_result($test, 0, "login");
								$prof_exists = true;
							}

							$data_tab[$k] = array();
							$data_tab[$k]["nom"] = $tabligne[0];
							$data_tab[$k]["prenom"] = $tabligne[1];
							$data_tab[$k]["civilite"] = $tabligne[2];
							$data_tab[$k]["email"] = $tabligne[3];
							$data_tab[$k]["reg_login"] = $login_prof;
							$data_tab[$k]["prof_exists"] = $prof_exists;
							//$data_tab[$k]["sso"] = $_POST['login_mode'];
							$data_tab[$k]["sso"] = $_POST['sso'];

						}
					$k++;
					}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout �a.

				echo "<form enctype='multipart/form-data' action='professeurs.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table border='1' class='boireaus' summary='Tableau des professeurs'>\n";
				echo "<tr><th>Login</th><th>Nom</th><th>Pr�nom</th><th>Civilit�</th><th>Email</th><th>Authentification</th></tr>\n";

				$alt=1;
				for ($i=0;$i<$k-1;$i++) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					if ($data_tab[$i]["prof_exists"]) {
						echo "<td style='color: blue;'>\n";
					} else {
						echo "<td>\n";
					}
					echo $data_tab[$i]["reg_login"];
					echo "<input type='hidden' name='ligne".$i."_login' value='" . $data_tab[$i]["reg_login"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["nom"];
					echo "<input type='hidden' name='ligne".$i."_nom' value='" . $data_tab[$i]["nom"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["prenom"];
					echo "<input type='hidden' name='ligne".$i."_prenom' value='" . $data_tab[$i]["prenom"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["civilite"];
					echo "<input type='hidden' name='ligne".$i."_civilite' value='" . $data_tab[$i]["civilite"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["email"];
					echo "<input type='hidden' name='ligne".$i."_email' value='" . $data_tab[$i]["email"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["sso"];
					echo "<input type='hidden' name='ligne".$i."_sso' value='" . $data_tab[$i]["sso"] . "' />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}

				echo "</table>\n";

				echo "<input type='submit' value='Enregistrer' />\n";

				echo "</form>\n";
			}

		} else if (trim($csv_file['name'])=='') {

			echo "<p>Aucun fichier n'a �t� s�lectionn� !<br />\n";
			echo "<a href='professeurs.php'>Cliquer ici </a> pour recommencer !</p>\n";

		} else {
			echo "<p>Le fichier s�lectionn� n'est pas valide !<br />\n";
			echo "<a href='professeurs.php'>Cliquer ici </a> pour recommencer !</p>\n";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
