<?php

@set_time_limit(0);
/*
* $Id: init_pp.php 5343 2010-09-19 17:45:42Z crob $
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

$liste_tables_del = array(
"j_eleves_professeurs"
);

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e : Importation des professeurs principaux";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>

<?php

// On v�rifie si l'extension d_base est active
//verif_active_dbase();

//debug_var();

echo "<center><h3 class='gepi'>Sixi�me phase<br />Importation des professeurs principaux</h3></center>\n";

if (!isset($step1)) {
	$j=0;
	$flag=0;
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
			$flag=1;
		}
		$j++;
	}
	if ($flag != 0){
		echo "<p><b>ATTENTION ...</b><br />\n";
		echo "Des professeurs principaux sont actuellement d�finis dans la base GEPI (<i>table 'j_eleves_professeurs'</i>)<br /></p>\n";
		//echo "<p>Si vous poursuivez la proc�dure ces donn�es seront supprim�es et remplac�es par celles de votre fichier F_DIV.CSV</p>\n";
		echo "<p>Si vous poursuivez la proc�dure ces donn�es seront supprim�es et remplac�es par celles de votre import XML.</p>\n";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo "<input type='hidden' name='step1' value='y' />\n";
		echo "<input type='submit' name='confirm' value='Poursuivre la proc�dure' />\n";
		echo "</form>\n";
		echo "<br />\n";
		require("../lib/footer.inc.php");
		die();
	}
}

$tempdir=get_user_temp_directory();
if(!$tempdir){
	echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas d�fini!?</p>\n";
	// Il ne faut pas aller plus loin...
	// SITUATION A GERER
}

if (!isset($is_posted)) {
	$j=0;
	while ($j < count($liste_tables_del)) {
		if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
			//echo "DELETE FROM $liste_tables_del[$j]<br />";
			$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
		}
		$j++;
	}

	if(!file_exists("../temp/$tempdir/f_div.csv")){
		echo "<p>Le fichier <b>f_div.csv</b> n'est pas pr�sent dans votre dossier temporaire.<br />Auriez-vous saut� l'�tape de l'importation des professeurs???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<p><b>ATTENTION ...</b><br />Vous ne devez proc�der � cette op�ration uniquement si la constitution des classes a �t� effectu�e et si les professeurs ont �t� import�s !</p>\n";
	//echo "<p>Importation du fichier <b>F_div.csv</b> (<i>g�n�r� lors de l'importation des professeurs</i>) contenant les associations classe/professeur principal.</p>\n";
	echo "<p>Importation des associations classe/professeur principal.</p>\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<input type=hidden name='step1' value='y' />\n";
	//echo "<p><input type='file' size='80' name='dbf_file' />";
	echo "<p><input type=submit value='Valider' /></p>\n";
	echo "</form>\n";

} else {
	//$dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
	//if(strtoupper($dbf_file['name']) == "F_TMT.DBF") {
	//if(strtoupper($dbf_file['name']) == "F_DIV.CSV") {
		//$fp = dbase_open($dbf_file['tmp_name'], 0);
		//$fp = fopen($dbf_file['tmp_name'],"r");
		$fp = fopen("../temp/$tempdir/f_div.csv","r");
		if(!$fp) {
			echo "<p>Impossible d'ouvrir le fichier CSV normalement g�n�r� lors de l'import des professeurs.</p>\n";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
		} else {
			// on constitue le tableau des champs � extraire
			//$tabchamps = array("MATIMN","MATILC");
			$tabchamps = array("DIVCOD","NUMIND");

			//$nblignes = dbase_numrecords($fp); //number of rows
			//$nbchamps = dbase_numfields($fp); //number of fields

			$nblignes=0;
			while (!feof($fp)) {
				$ligne = fgets($fp, 4096);
				if($nblignes==0){
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
			/*
			echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent � des nouvelles mati�res dans la base GEPI. les identifiants en vert correspondent � des identifiants de mati�res d�tect�s dans le fichier GEP mais d�j� pr�sents dans la base GEPI.<br /><br />Il est possible que certaines mati�res ci-dessous, bien que figurant dans le fichier GEP, ne soient pas utilis�es dans votre �tablissement cette ann�e. C'est pourquoi il vous sera propos� en fin de proc�dure d'initialsation, un nettoyage de la base afin de supprimer ces donn�es inutiles.</p>";
			*/
			echo "<table border='1' class='boireaus' cellpadding='2' cellspacing='2' summary='Tableau des professeurs principaux'>\n";
			echo "<tr><th><p class=\"small\">Classe</p></th><th><p class=\"small\">Professeur principal</p></th></tr>\n";


			//=========================
			//$fp=fopen($dbf_file['tmp_name'],"r");
			$fp = fopen("../temp/$tempdir/f_div.csv","r");
			// On lit une ligne pour passer la ligne d'ent�te:
			$ligne = fgets($fp, 4096);
			//=========================
			$nb_reg_no = 0;
			$nb_reg_ok = 0;
			for($k = 1; ($k < $nblignes+1); $k++){
				//$ligne = dbase_get_record($fp,$k);
				if(!feof($fp)){
					//====================
					// Suppression des guillemets �ventuels
					//$ligne = fgets($fp, 4096);
					$ligne = my_ereg_replace('"','',fgets($fp, 4096));
					//====================
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						$temoin_erreur="non";
						for($i = 0; $i < count($tabchamps); $i++) {
							//$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[$tabindice[$i]]))));
							$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
							//echo "<tr><td colspan='2'>|\$affiche[$i]|=|$affiche[$i]|</td></tr>";
							if($affiche[$i]==""){
								$temoin_erreur="oui";
								$nb_reg_no++;
							}
						}

						if($temoin_erreur!="oui"){
							$sql="SELECT id FROM classes WHERE classe='$affiche[0]'";
							$res_classe=mysql_query($sql);
							if(mysql_num_rows($res_classe)==1){
								$lig_classe=mysql_fetch_object($res_classe);
								$id_classe=$lig_classe->id;

								/*
								$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode"
								$res_periodes=mysql_query($sql);
								unset($tab_num_periode);
								$tab_num_periode=array();
								while($lig_periode=mysql_fetch_object($res_periodes)){
									$tab_num_periode[]=$lig_periode->num_periode;
								}
								*/
								$sql="SELECT col1 FROM tempo2 WHERE col2='$affiche[1]'";
								//echo "<tr><td>$sql</td></tr>\n";
								$res_prof=mysql_query($sql);
								$lig_prof=mysql_fetch_object($res_prof);

								//$sql="SELECT login,periode FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY login,periode"
								//$sql="SELECT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY login";
								$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY login";
								$res_eleve=mysql_query($sql);
								$temoin_erreur_classe=0;
								while($lig_eleve=mysql_fetch_object($res_eleve)){
									$sql="INSERT INTO j_eleves_professeurs VALUES('$lig_eleve->login','$lig_prof->col1','$id_classe')";
									$res_prof_eleve=mysql_query($sql);
									if(!$res_prof_eleve){
										$temoin_erreur_classe++;
										//echo "<tr><td>$sql</td></tr>\n";
									}
								}

								if($temoin_erreur_classe==0){
									echo "<tr style='background-color:#aae6aa;'><td>$affiche[0]</td><td>$lig_prof->col1</td></tr>\n";
									$nb_reg_ok++;
								}
								else{
									echo "<tr style='background-color:red;'><td>$affiche[0]</td><td>$lig_prof->col1</td></tr>\n";
									$nb_reg_no++;
								}
								//echo "<tr><td>$affiche[0]</td><td>$lig_prof->col1</td></tr>\n";
							}
						}
						else{
							echo "<tr style='background-color:red;'><td>$affiche[0]</td><td>$affiche[1]</td></tr>\n";
						}
					}
				}
			}
			echo "</table>\n";
			//dbase_close($fp);
			fclose($fp);

			if ($nb_reg_no != 0) {
				echo "<p>Lors de l'enregistrement des donn�es, il y a eu $nb_reg_no erreurs.<br />Essayez d'en trouver la cause et recommencez la proc�dure avant de passer � l'�tape suivante.</p>\n";
			} else {
				if($nb_reg_ok>0){
					echo "<p>L'importation des professeurs principaux dans la base GEPI a �t� effectu�e avec succ�s !</p>\n";
				}
				else{
					echo "<p>Aucun professeur principal n'a �t� inscrit dans la base GEPI !</p>\n";
				}

				if(getSettingValue("mode_sauvegarde")=="mysqldump") {$mode_sauvegarde="system_dump";}
				else {$mode_sauvegarde="dump";}

				echo "<p>Avant de proc�der � un nettoyage des tables pour supprimer les donn�es inutiles, vous devriez effectuer une <a href='../gestion/accueil_sauve.php?action=$mode_sauvegarde&amp;quitter_la_page=y' target='_blank'>sauvegarde</a><br />\n";
				echo "Apr�s cette sauvegarde, effectuez le nettoyage en repassant par 'Gestion g�n�rale/Initialisation des donn�es � partir de fichiers DBF et XML/Proc�der � la septi�me phase'.<br />\n";
				echo "Si les donn�es sont effectivement inutiles, c'est termin�.<br />\n";
				echo "Sinon, vous pourrez restaurer votre sauvegarde et vous aurez pu noter les associations profs/mati�res/classes manquantes... � effectuer par la suite manuellement dans 'Gestion des bases'.</p>\n";

				echo "<p>Vous pouvez proc�der � l'�tape suivante de nettoyage des tables GEPI.</p>\n";
				echo "<center><p><a href='clean_tables.php'>Suppression des donn�es inutiles</a></p></center>\n";
			}
		}
	/*
	}
	else if (trim($dbf_file['name'])=='') {
		echo "<p>Aucun fichier n'a �t� s�lectionn� !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";

	} else {
		echo "<p>Le fichier s�lectionn� n'est pas valide !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
	}
	*/
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>