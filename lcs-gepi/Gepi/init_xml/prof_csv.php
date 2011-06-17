<?php
@set_time_limit(0);
/*
* $Id: prof_csv.php 5936 2010-11-21 17:32:17Z crob $
*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
extract($_POST, EXTR_OVERWRITE);

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

check_token();

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
"j_aid_utilisateurs_gest",
"j_aidcateg_super_gestionnaires",
"j_aidcateg_utilisateurs",
"j_groupes_professeurs",
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
//"observatoire",
//"observatoire_comment",
//"observatoire_config",
//"observatoire_niveaux",
"observatoire_j_resp_champ",
//"observatoire_suivi",
//"periodes",
//"periodes_observatoire",
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
$titre_page = "Outil d'initialisation de l'ann�e : Importation des mati�res";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On v�rifie si l'extension d_base est active
//verif_active_dbase();

?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php
echo "<center><h3 class='gepi'>Quatri�me phase d'initialisation<br />Importation des professeurs</h3></center>";

if (!isset($step1)) {
	$j=0;
	$flag=0;
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE '$liste_tables_del[$j]'"));
		if($test==1){
			if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$flag=1;
			}
		}
		$j++;
	}

	$test = mysql_result(mysql_query("SELECT count(*) FROM utilisateurs WHERE statut='professeur'"),0);
	if ($test != 0) {$flag=1;}

	if ($flag != 0){
		echo "<p><b>ATTENTION ...</b><br />";
		echo "Des donn�es concernant les professeurs sont actuellement pr�sentes dans la base GEPI<br /></p>";
		echo "<p>Si vous poursuivez la proc�dure les donn�es telles que notes, appr�ciations, ... seront effac�es.</p>";
		echo "<ul><li>Seules la table contenant les utilisateurs (professeurs, admin, ...) et la table mettant en relation les mati�res et les professeurs seront conserv�es.</li>";
		echo "<li>Les professeurs de l'ann�e pass�e pr�sents dans la base GEPI et non pr�sents dans la base CSV de cette ann�e ne sont pas effac�s de la base GEPI mais simplement d�clar�s \"inactifs\".</li>";
		echo "</ul>";
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>";
		echo add_token_field();
		echo "<input type=hidden name='step1' value='y' />";
		echo "<input type='submit' name='confirm' value='Poursuivre la proc�dure' />";
		echo "</form>";
		echo "</div>";
		echo "</body>";
		echo "</html>";
		die();
	}
}

if (!isset($is_posted)) {
	$j=0;
	while ($j < count($liste_tables_del)) {
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE '$liste_tables_del[$j]'"));
		if($test==1){
			if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
			}
		}
		$j++;
	}
	$del = @mysql_query("DELETE FROM tempo2");

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method=post>";
	echo add_token_field();
	echo "<p>Importation du fichier <b>F_wind.csv</b> contenant les donn�es relatives aux professeurs.";
	echo "<p>Veuillez pr�ciser le nom complet du fichier <b>F_wind.csv</b>.";
	echo "<input type=hidden name='is_posted' value='yes' />";
	echo "<input type=hidden name='step1' value='y' />";
	echo "<p><input type='file' size='80' name='dbf_file' />";
	echo "<br /><br /><p>Quelle formule appliquer pour la g�n�ration du login ?</p>";
	echo "<input type='radio' name='login_gen_type' value='name' checked /> nom";
	echo "<br /><input type='radio' name='login_gen_type' value='name8' /> nom (tronqu� � 8 caract�res)";
	echo "<br /><input type='radio' name='login_gen_type' value='fname8' /> pnom (tronqu� � 8 caract�res)";
	echo "<br /><input type='radio' name='login_gen_type' value='fname19' /> pnom (tronqu� � 19 caract�res)";
	echo "<br /><input type='radio' name='login_gen_type' value='firstdotname' /> prenom.nom";
	echo "<br /><input type='radio' name='login_gen_type' value='firstdotname19' /> prenom.nom (tronqu� � 19 caract�res)";
	echo "<br /><input type='radio' name='login_gen_type' value='namef8' /> nomp (tronqu� � 8 caract�res)";
	echo "<br /><input type='radio' name='login_gen_type' value='lcs' /> pnom (fa�on LCS)";
	echo "<br /><br /><p>Ces comptes seront-ils utilis�s en Single Sign-On avec CAS ou LemonLDAP ? (laissez 'non' si vous ne savez pas de quoi il s'agit)</p>";
	echo "<br /><input type='radio' name='sso' value='no' checked /> Non";
	echo "<br /><input type='radio' name='sso' value='yes' /> Oui (aucun mot de passe ne sera g�n�r�)";
	echo "<p><input type='submit' value='Valider' />";
	echo "</form>";

} else {

	$dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
	// On commence par rendre inactifs tous les professeurs
	$req = mysql_query("UPDATE utilisateurs set etat='inactif' where statut = 'professeur'");

	// on efface la ligne "display_users" dans la table "setting" de fa�on � afficher tous les utilisateurs dans la page  /utilisateurs/index.php
	$req = mysql_query("DELETE from setting where NAME = 'display_users'");

	//if(strtoupper($dbf_file['name']) == "F_WIND.DBF") {
	if(strtoupper($dbf_file['name']) == "F_WIND.CSV") {
		//$fp = @dbase_open($dbf_file['tmp_name'], 0);
		$fp=fopen($dbf_file['tmp_name'],"r");
		if(!$fp) {
		echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
		echo "<a href='".$_SERVER['PHP_SELF']."?a=a".add_token_in_url()."'>Cliquer ici </a> pour recommencer !</center></p>";
		} else {
			// on constitue le tableau des champs � extraire
			$tabchamps = array("AINOMU","AIPREN","AICIVI","NUMIND","FONCCO","INDNNI" );

			//$nblignes = dbase_numrecords($fp); //number of rows
			//$nbchamps = dbase_numfields($fp); //number of fields

			$nblignes=0;
			while (!feof($fp)) {
				$ligne = fgets($fp, 4096);
				if($nblignes==0){
					/*
					$temp=explode(";",$ligne);
					$nbchamps=sizeof($temp);
					echo "\$nbchamps=$nbchamps<br />\n";
					for($i=0;$i<$nbchamps;$i++){
						echo "\$temp[$i]=$temp[$i]<br />\n";
					}
					*/

					// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renomm�s avec l'ajout de ',...' en fin de nom de champ.
					// On ne retient pas ces ajouts pour $en_tete
					$temp=explode(";",$ligne);
					for($i=0;$i<sizeof($temp);$i++){
						$temp2=explode(",",$temp[$i]);
						$en_tete[$i]=$temp2[0];
					}

					//$en_tete=explode(";",$ligne);
					$nbchamps=sizeof($en_tete);
				}
				$nblignes++;
			}
			fclose ($fp);

	/*
			if (@dbase_get_record_with_names($fp,1)) {
				$temp = @dbase_get_record_with_names($fp,1);
			} else {
				echo "<p>Le fichier s�lectionn� n'est pas valide !<br />";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
				die();
			}

			$nb = 0;
			foreach($temp as $key => $val){
				$en_tete[$nb] = "$key";
				$nb++;
			}
	*/

			// On range dans tabindice les indices des champs retenus
			/*
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					//if ($en_tete[$i] == $tabchamps[$k]) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[] = $i;
					}
				}
			}
			*/
			$cpt_tmp=0;
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[$cpt_tmp]=$i;
						$cpt_tmp++;
					}
				}
			}

			echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent � des professeurs nouveaux dans la base GEPI. les identifiants en vert correspondent � des professeurs d�tect�s dans les fichiers CSV mais d�j� pr�sents dans la base GEPI.<br /><br />Il est possible que certains professeurs ci-dessous, bien que figurant dans le fichier CSV, ne soient plus en exercice dans votre �tablissement cette ann�e. C'est pourquoi il vous sera propos� en fin de proc�dure d'initialsation, un nettoyage de la base afin de supprimer ces donn�es inutiles.</p>";
			echo "<table border=1 cellpadding=2 cellspacing=2>";
			echo "<tr><td><p class=\"small\">Identifiant du professeur</p></td><td><p class=\"small\">Nom</p></td><td><p class=\"small\">Pr�nom</p></td><td>Mot de passe *</td></tr>";
			srand();
			$nb_reg_no = 0;
			//=========================
			$fp=fopen($dbf_file['tmp_name'],"r");
			// On lit une ligne pour passer la ligne d'ent�te:
			$ligne = fgets($fp, 4096);
			//=========================
			for($k = 1; ($k < $nblignes+1); $k++){
				//$ligne = dbase_get_record($fp,$k);
				if(!feof($fp)){
					//=========================
					// MODIF: boireaus 20071024
					//$ligne = fgets($fp, 4096);
					$ligne = my_ereg_replace('"','',fgets($fp, 4096));
					//=========================
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps); $i++) {
							//$affiche[$i] = dbase_filter(trim($ligne[$tabindice[$i]]));
							$affiche[$i] = dbase_filter(trim($tabligne[$tabindice[$i]]));
						}
						//Civilit�
						$civilite = '';
						if ($affiche[2] = "ML") $civilite = "Mlle";
						if ($affiche[2] = "MM") $civilite = "Mme";
						if ($affiche[2] = "M.") $civilite = "M.";


						$prenoms = explode(" ",$affiche[1]);
						$premier_prenom = $prenoms[0];
						$prenom_compose = '';
						if (isset($prenoms[1])) $prenom_compose = $prenoms[0]."-".$prenoms[1];

						// On effectue d'abord un test sur le NUMIND
						$sql="select login from utilisateurs where (
						numind='".$affiche[3]."' and
						numind!='' and
						statut='professeur')";
						//echo "<tr><td>$sql</td></tr>";
						$test_exist = mysql_query($sql);
						$result_test = mysql_num_rows($test_exist);
						if ($result_test == 0) {
							// On tente ensuite une reconnaissance sur nom/pr�nom, si le test NUMIND a �chou�
							$sql="select login from utilisateurs where (
							nom='".traitement_magic_quotes($affiche[0])."' and
							prenom = '".traitement_magic_quotes($premier_prenom)."' and
							statut='professeur')";
							// Pour debug:
							//echo "$sql<br />";
							$test_exist = mysql_query($sql);
							$result_test = mysql_num_rows($test_exist);
							if ($result_test == 0) {
								if ($prenom_compose != '') {
									$test_exist2 = mysql_query("select login from utilisateurs
									where (
									nom='".traitement_magic_quotes($affiche[0])."' and
									prenom = '".traitement_magic_quotes($prenom_compose)."' and
									statut='professeur'
									)");
									$result_test2 = mysql_num_rows($test_exist2);
									if ($result_test2 == 0) {
										$exist = 'no';
									} else {
										$exist = 'yes';
										$login_prof_gepi = mysql_result($test_exist2,0,'login');
									}
								} else {
									$exist = 'no';
								}
							} else {
								$exist = 'yes';
								$login_prof_gepi = mysql_result($test_exist,0,'login');
							}
						}
						else {
							$exist = 'yes';
							$login_prof_gepi = mysql_result($test_exist,0,'login');
						}

						if ($exist == 'no') {

							// Aucun professeur ne porte le m�me nom dans la base GEPI. On va donc rentrer ce professeur dans la base

							$affiche[1] = traitement_magic_quotes(corriger_caracteres($affiche[1]));

							if ($_POST['login_gen_type'] == "name") {
								$temp1 = $affiche[0];
								$temp1 = strtoupper($temp1);
								$temp1 = my_ereg_replace(" ","", $temp1);
								$temp1 = my_ereg_replace("-","_", $temp1);
								$temp1 = my_ereg_replace("'","", $temp1);
								//$temp1 = substr($temp1,0,8);

							} elseif ($_POST['login_gen_type'] == "name8") {
								$temp1 = $affiche[0];
								$temp1 = strtoupper($temp1);
								$temp1 = my_ereg_replace(" ","", $temp1);
								$temp1 = my_ereg_replace("-","_", $temp1);
								$temp1 = my_ereg_replace("'","", $temp1);
								$temp1 = substr($temp1,0,8);
							} elseif ($_POST['login_gen_type'] == "fname8") {
								$temp1 = $affiche[1]{0} . $affiche[0];
								$temp1 = strtoupper($temp1);
								$temp1 = my_ereg_replace(" ","", $temp1);
								$temp1 = my_ereg_replace("-","_", $temp1);
								$temp1 = my_ereg_replace("'","", $temp1);
								$temp1 = substr($temp1,0,8);
							} elseif ($_POST['login_gen_type'] == "fname19") {
								$temp1 = $affiche[1]{0} . $affiche[0];
								$temp1 = strtoupper($temp1);
								$temp1 = my_ereg_replace(" ","", $temp1);
								$temp1 = my_ereg_replace("-","_", $temp1);
								$temp1 = my_ereg_replace("'","", $temp1);
								$temp1 = substr($temp1,0,19);
							} elseif ($_POST['login_gen_type'] == "firstdotname") {
								if ($prenom_compose != '') {
									$firstname = $prenom_compose;
								} else {
									$firstname = $premier_prenom;
								}

								$temp1 = $firstname . "." . $affiche[0];
								$temp1 = strtoupper($temp1);

								$temp1 = my_ereg_replace(" ","", $temp1);
								$temp1 = my_ereg_replace("-","_", $temp1);
								$temp1 = my_ereg_replace("'","", $temp1);
								//$temp1 = substr($temp1,0,19);
							} elseif ($_POST['login_gen_type'] == "firstdotname19") {
								if ($prenom_compose != '') {
									$firstname = $prenom_compose;
								} else {
									$firstname = $premier_prenom;
								}

								$temp1 = $firstname . "." . $affiche[0];
								$temp1 = strtoupper($temp1);
								$temp1 = my_ereg_replace(" ","", $temp1);
								$temp1 = my_ereg_replace("-","_", $temp1);
								$temp1 = my_ereg_replace("'","", $temp1);
								$temp1 = substr($temp1,0,19);
							} elseif ($_POST['login_gen_type'] == "namef8") {
								$temp1 =  substr($affiche[0],0,7) . $affiche[1]{0};
								$temp1 = strtoupper($temp1);
								$temp1 = my_ereg_replace(" ","", $temp1);
								$temp1 = my_ereg_replace("-","_", $temp1);
								$temp1 = my_ereg_replace("'","", $temp1);
								//$temp1 = substr($temp1,0,8);
							} elseif ($_POST['login_gen_type'] == "lcs") {
								$nom = $affiche[0];
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
								$prenom = $affiche[1];
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
							$affiche[0] = traitement_magic_quotes(corriger_caracteres($affiche[0]));
							// Mot de passe
							//echo "<tr><td colspan='4'>strlen($affiche[5])=".strlen($affiche[5])."<br />\$affiche[4]=$affiche[4]<br />\$_POST['sso']=".$_POST['sso']."</td></tr>";
							if (strlen($affiche[5])>2 and $affiche[4]=="ENS" and $_POST['sso'] == "no") {
								//
								$pwd = md5(trim($affiche[5])); //NUMEN
								//$mess_mdp = "NUMEN";
								$mess_mdp = "Mot de passe dans le fichier fourni";
								//echo "<tr><td colspan='4'>NUMEN: $affiche[5] $pwd</td></tr>";
							} elseif ($_POST['sso']== "no") {
								$pwd = md5(rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9));
								$mess_mdp = $pwd;
								//echo "<tr><td colspan='4'>Choix 2: $pwd</td></tr>";
					//                       $mess_mdp = "Inconnu (compte bloqu�)";
							} elseif ($_POST['sso'] == "yes") {
								$pwd = '';
								$mess_mdp = "aucun (sso)";
								//echo "<tr><td colspan='4'>sso</td></tr>";
							}

							// utilise le pr�nom compos� s'il existe, plut�t que le premier pr�nom

							//$res = mysql_query("INSERT INTO utilisateurs VALUES ('".$login_prof."', '".$affiche[0]."', '".$premier_prenom."', '".$civilite."', '".$pwd."', '', 'professeur', 'actif', 'y', '')");
							//$sql="INSERT INTO utilisateurs SET login='$login_prof', nom='$affiche[0]', prenom='$premier_prenom', civilite='$civilite', password='$pwd', statut='professeur', etat='actif', change_mdp='y'";
							$sql="INSERT INTO utilisateurs SET login='$login_prof', nom='$affiche[0]', prenom='$premier_prenom', civilite='$civilite', password='$pwd', statut='professeur', etat='actif', change_mdp='y', numind='$affiche[3]'";
							$res = mysql_query($sql);
							// Pour debug:
							//echo "<tr><td colspan='4'>$sql</td></tr>";

							if(!$res){$nb_reg_no++;}
							$res = mysql_query("INSERT INTO tempo2 VALUES ('".$login_prof."', '".$affiche[3]."')");
							echo "<tr><td><p><font color='red'>".$login_prof."</font></p></td><td><p>".$affiche[0]."</p></td><td><p>".$premier_prenom."</p></td><td>".$mess_mdp."</td></tr>\n";
						} else {
							//$res = mysql_query("UPDATE utilisateurs set etat='actif' where login = '".$login_prof_gepi."'");
							// On corrige aussi les nom/pr�nom/civilit� et numind parce que la reconnaissance a aussi pu se faire sur le nom/pr�nom
							$res = mysql_query("UPDATE utilisateurs set etat='actif', nom='$affiche[0]', prenom='$premier_prenom', civilite='$civilite', numind='$affiche[3]' where login='".$login_prof_gepi."'");

							if(!$res) $nb_reg_no++;
							$res = mysql_query("INSERT INTO tempo2 VALUES ('".$login_prof_gepi."', '".$affiche[3]."')");
							echo "<tr><td><p><font color='green'>".$login_prof_gepi."</font></p></td><td><p>".$affiche[0]."</p></td><td><p>".$affiche[1]."</p></td><td>Inchang�</td></tr>\n";
						}
					}
				}
			}
				//dbase_close($fp);
			fclose($fp);
			echo "</table>";
			if ($nb_reg_no != 0) {
				echo "<p>Lors de l'enregistrement des donn�es il y a eu $nb_reg_no erreurs. Essayez de trouvez la cause de l'erreur et recommencez la proc�dure avant de passer � l'�tape suivante.";
			} else {
				echo "<p>L'importation des professeurs dans la base GEPI a �t� effectu�e avec succ�s !</p>";

				/*
				echo "<p><b>* Pr�cision sur les mots de passe (en non-SSO) :</b><br />
				(il est conseill� d'imprimer cette page)</p>
				<ul>
				<li>Lorsqu'un nouveau professeur est ins�r� dans la base GEPI, son mot de passe lors de la premi�re
				connexion � GEPI est son NUMEN.</li>
				<li>Si le NUMEM n'est pas disponible dans le fichier F_wind.csv, GEPI g�n�re al�atoirement
				un mot de passe.</li></ul>";
				*/
				echo "<p><b>* Pr�cision sur les mots de passe (en non-SSO) :</b><br />
				(il est conseill� d'imprimer cette page)</p>
				<ul>
				<li>Lorsqu'un nouveau professeur est ins�r� dans la base GEPI, son mot de passe lors de la premi�re
				connexion � GEPI est celui inscrit dans le F_wind.csv.</li>
				<li>Si le mot de passe n'est pas disponible dans le fichier F_wind.csv, GEPI g�n�re al�atoirement
				un mot de passe.</li></ul>";
				echo "<p><b>Dans tous les cas le nouvel utilisateur est amen� � changer son mot de passe lors de sa premi�re connexion.</b></p>";
				echo "<br /><p>Vous pouvez proc�der � la cinqui�me phase d'affectation des mati�res � chaque professeur, d'affectation des professeurs dans chaque classe et de d�finition des options suivies par les �l�ves.</p>";
			}
			//echo "<center><p><b><a href='prof_disc_classe.php'>Proc�der � la cinqui�me phase d'initialisation</a></b></p></center><br /><br />";
			echo "<center><p><b><a href='prof_disc_classe_csv.php?a=a".add_token_in_url()."'>Proc�der � la cinqui�me phase d'initialisation</a></b></p></center><br /><br />";
		}
	} else if (trim($dbf_file['name'])=='') {
		echo "<p>Aucun fichier n'a �t� s�lectionn� !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."?a=a".add_token_in_url()."'>Cliquer ici </a> pour recommencer !</center></p>";

	} else {
		echo "<p>Le fichier s�lectionn� n'est pas valide !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."?a=a".add_token_in_url()."'>Cliquer ici </a> pour recommencer !</center></p>";
	}
}
require("../lib/footer.inc.php");
?>