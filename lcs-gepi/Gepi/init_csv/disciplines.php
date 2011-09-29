<?php
@set_time_limit(0);
/*
* $Id: disciplines.php 8209 2011-09-13 16:40:20Z crob $
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

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e : Importation des mati�res";
require_once("../lib/header.inc");
//************** FIN EN-TETE ***************
?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/>Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Troisi�me phase d'initialisation<br />Importation des mati�res</h3></center>\n";


if (!isset($_POST["action"])) {
	//
	// On s�lectionne le fichier � importer
	//

	echo "<p>Vous allez effectuer la troisi�me �tape : elle consiste � importer le fichier <b>g_disciplines.csv</b> contenant les donn�es relatives aux disciplines.</p>\n";
	echo "<p><i>Remarque :</i> cette op�ration n'efface aucune donn�e dans la base. Elle ne fait qu'une mise � jour, le cas �ch�ant, de la liste des mati�res.</p>\n";
	echo "<p>Les champs suivants doivent �tre pr�sents, dans l'ordre, et <b>s�par�s par un point-virgule</b> : </p>\n";
	echo "<ul><li>Nom court de la mati�re (il doit �tre unique)</li>\n" .
			"<li>Nom long de la mati�re</li>\n" .
			"</ul>\n";
	echo "<p>Veuillez pr�ciser le nom complet du fichier <b>g_disciplines.csv</b>.</p>\n";
	echo "<form enctype='multipart/form-data' action='disciplines.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";
	//echo "<p><input type=\"checkbox\" name=\"ligne_entete\" value='y' /> Cocher si le fichier comporte une ligne d'ent�te.</p>\n";
    echo "<p><label for='ligne_entete' style='cursor:pointer;'>Si le fichier � importer comporte une premi�re ligne d'en-t�te (non vide) � ignorer, <br />cocher la case ci-contre</label>&nbsp;<input type='checkbox' name='ligne_entete' id='ligne_entete' value='yes' checked /></p>\n";
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

		$sql="SELECT * FROM tempo2;";
		$res_temp=mysql_query($sql);
		if(mysql_num_rows($res_temp)==0) {
			echo "<p style='color:red'>ERREUR&nbsp;: Aucune association �l�ve/option n'a �t� trouv�e&nbsp;???</p>\n";
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
		$nb_matieres_existantes=0;
		//while ($go) {
		while ($lig=mysql_fetch_object($res_temp)) {

			//$reg_nom_court = $_POST["ligne".$i."_nom_court"];
			//$reg_nom_long = $_POST["ligne".$i."_nom_long"];

			$reg_nom_court = $lig->col1;
			$reg_nom_long = $lig->col2;

			// On nettoie et on v�rifie :
			$reg_nom_court = preg_replace("/[^A-Za-z0-9.\-]/","",trim(strtoupper($reg_nom_court)));
			if (strlen($reg_nom_court) > 50) $reg_nom_court = substr($reg_nom_court, 0, 50);
			//$reg_nom_long = preg_replace("/[^A-Za-z0-9 .\-��������]/","",trim($reg_nom_long));
			$reg_nom_long = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z0-9 .\-������������������������������]/","",trim($reg_nom_long))))));
			if (strlen($reg_nom_long) > 200) $reg_nom_long = substr($reg_nom_long, 0, 200);

			// Maintenant que tout est propre, on fait un test sur la table pour voir si la mati�re existe d�j� ou pas

			$test = mysql_result(mysql_query("SELECT count(matiere) FROM matieres WHERE matiere = '" . $reg_nom_court . "'"), 0);

			if ($test == 0) {
				// Test n�gatif : aucune mati�re avec ce nom court... on enregistre !

				$insert = mysql_query("INSERT INTO matieres SET " .
						"matiere = '" . $reg_nom_court . "', " .
						"nom_complet = '" . $reg_nom_long . "',priority='0',matiere_aid='n',matiere_atelier='n'");
						//"nom_complet = '" . htmlentities($reg_nom_long) . "'");

				if (!$insert) {
					$error++;
					echo mysql_error();
				} else {
					$total++;
				}

			}
			else {
				$nb_matieres_existantes++;
			}


			$i++;
			if (!isset($_POST['ligne'.$i.'_nom_court'])) $go = false;
		}

		//if ($error > 0) echo "<p><font color='red'>Il y a eu " . $error . " erreurs.</font></p>\n";
		if ($error > 0){
			if ($error == 1){
				echo "<p><font color='red'>Il y a eu " . $error . " erreur.</font></p>\n";
			}
			else{
				echo "<p><font color='red'>Il y a eu " . $error . " erreurs.</font></p>\n";
			}
		}
		//if ($total > 0) echo "<p>" . $total . " mati�res ont �t� enregistr�es.</p>\n";
		if ($total > 0){
			if ($total == 1){
				echo "<p>" . $total . " mati�re a �t� enregistr�e.</p>\n";
			}
			else{
				echo "<p>" . $total . " mati�res ont �t� enregistr�es.</p>\n";
			}
		}

		if($nb_matieres_existantes>0) {
			if ($nb_matieres_existantes == 1){
				echo "<p>" . $nb_matieres_existantes . " mati�re existait d�j�.</p>\n";
			}
			else{
				echo "<p>" . $nb_matieres_existantes . " mati�res existaient d�j�.</p>\n";
			}
		}

		echo "<p><a href='index.php'>Revenir � la page pr�c�dente</a></p>\n";


	} else if ($_POST['action'] == "upload_file") {
		check_token(false);
		//
		// Le fichier vient d'�tre envoy� et doit �tre trait�
		// On va donc afficher le contenu du fichier tel qu'il va �tre enregistr� dans Gepi
		// en proposant des champs de saisie pour modifier les donn�es si on le souhaite
		//

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
		$ligne_entete=isset($_POST['ligne_entete']) ? $_POST['ligne_entete'] : 'no';

		// On v�rifie le nom du fichier... Ce n'est pas fondamentalement indispensable, mais
		// autant forcer l'utilisateur � �tre rigoureux
		if(strtolower($csv_file['name']) == "g_disciplines.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas � ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
				echo "<p><a href='disciplines.php'>Cliquer ici </a> pour recommencer !</p>\n";
			} else {

				// Fichier ouvert ! On attaque le traitement

				// On va stocker toutes les infos dans un tableau
				// Une ligne du CSV pour une entr�e du tableau
				$data_tab = array();

				//=========================
				if($ligne_entete=="yes"){
					// On lit une ligne pour passer la ligne d'ent�te:
					$ligne = fgets($fp, 4096);
				}
				//=========================

					$k = 0;
					while (!feof($fp)) {
						$ligne = fgets($fp, 4096);
						if(trim($ligne)!="") {

							$tabligne=explode(";",$ligne);

							// 0 : Nom court de la mati�re
							// 1 : Nom long de la mati�re


							// On nettoie et on v�rifie :
							$tabligne[0] = preg_replace("/[^A-Za-z0-9.\-]/","",trim(strtoupper($tabligne[0])));
							if (strlen($tabligne[0]) > 50) $tabligne[0] = substr($tabligne[0], 0, 50);
							//$tabligne[1] = preg_replace("/[^A-Za-z0-9 .\-��������]/","",trim($tabligne[1]));
							$tabligne[1] = preg_replace("/�/","AE",preg_replace("/�/","ae",preg_replace("/�/","OE",preg_replace("/�/","oe",preg_replace("/[^A-Za-z0-9 .\-������������������������������]/","",trim($tabligne[1]))))));
							if (strlen($tabligne[1]) > 200) $tabligne[1] = substr($tabligne[1], 0, 200);

							$data_tab[$k] = array();



							$data_tab[$k]["nom_court"] = $tabligne[0];
							$data_tab[$k]["nom_long"] = $tabligne[1];

						}
					$k++;
					}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout �a.

				$sql="TRUNCATE TABLE tempo2;";
				$vide_table = mysql_query($sql);

				$nb_error=0;

				echo "<form enctype='multipart/form-data' action='disciplines.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table border='1' class='boireaus' summary='Tableau des mati�res'>\n";
				echo "<tr><th>Nom court (unique)</th><th>Nom long</th></tr>\n";


				$alt=1;
				for ($i=0;$i<$k-1;$i++) {
					$alt=$alt*(-1);
                    echo "<tr class='lig$alt'>\n";
					echo "<td>\n";
					$sql="INSERT INTO tempo2 SET col1='".$data_tab[$i]["nom_court"]."',
					col2='".addslashes($data_tab[$i]["nom_long"])."';";
					$insert=mysql_query($sql);
					if(!$insert) {
						echo "<span style='color:red'>";
						echo $data_tab[$i]["nom_court"];
 						echo "</span>";
						$nb_error++;
					}
					else {
						echo $data_tab[$i]["nom_court"];
					}
					//echo "<input type='hidden' name='ligne".$i."_nom_court' value='" . $data_tab[$i]["nom_court"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["nom_long"];
					//echo "<input type='hidden' name='ligne".$i."_nom_long' value='" . $data_tab[$i]["nom_long"] . "' />\n";
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
			echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</p>\n";

		} else {
			echo "<p>Le fichier s�lectionn� n'est pas valide !<br />\n";
			echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</p>\n";
		}
	}
}
require("../lib/footer.inc.php");
?>