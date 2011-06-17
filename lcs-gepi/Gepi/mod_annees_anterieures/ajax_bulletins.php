<?php

/*
 * $Id: ajax_bulletins.php 6090 2010-12-11 12:55:19Z crob $
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

@set_time_limit(0);

// Initialisations files
require_once("../lib/initialisations.inc.php");

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

//INSERT INTO droits SET id='/mod_annees_anterieures/ajax_bulletins.php',administrateur='V',professeur='V',cpe='V',scolarite='V',eleve='F',responsable='F',secours='F',autre='V',description='Statistiques: classe, effectifs',statut='';
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;

//$logineleve=isset($_POST['logineleve']) ? $_POST['logineleve'] : NULL;
$logineleve=isset($_GET['logineleve']) ? $_GET['logineleve'] : NULL;
// Faire un filtrage sur $logineleve:
// - Un �l�ve ne doit acc�der qu'� ses infos personnelles
// - Un responsable ne doit acc�der qu'aux infos des enfants dont il est (actuellement) responsable
// - Un professeur ne doit acc�der, selon le mode choisi:
//        . qu'aux donn�es des �l�ves qu'il a en groupe
//        . qu'aux donn�es de tous les �l�ves dont il a les classes
//        . � toutes les donn�es �l�ves
// - Un CPE, un compte scolarit�... comme pour les profs.
// Il faut rendre ces choix param�trables dans Droits d'acc�s.


$annee_scolaire=isset($_GET['annee_scolaire']) ? $_GET['annee_scolaire'] : NULL;
$num_periode=isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL;

// Mode: 'bull_simp' ou 'avis_conseil'
$mode=isset($_GET['mode']) ? $_GET['mode'] : NULL;
// Faire un filtrage sur ces valeurs.


// Si le module n'est pas activ�...
if(getSettingValue('active_annees_anterieures')!="y") {
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'acc�s illicite?
	tentative_intrusion(1, "Tentative d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'acc�der au module Ann�es ant�rieures qui n'est pas activ�.");

	//echo "1";

	header("Location: ../logout.php?auto=1");
	die();
}

// Il faut arriver sur cette page avec un $logineleve pass� en param�tre.

// Faire les filtrages selon le statut � ce niveau en tenant compte:
// - du fait que le statut est autoris� � acc�der dans Droits d'acc�s;
// - du login �l�ve fourni.

require('fonctions_annees_anterieures.inc.php');

$acces=check_acces_aa($logineleve);

if($acces=="y") {
	header('Content-Type: text/html; charset=ISO-8859-15');
	
	// On a pass� les barri�res, on passe au traitement

		/*
			$logineleve:      login actuel de l'�l�ve
			$id_classe:       identifiant de la classe actuelle de l'�l�ve
			$annee_scolaire:  nom de l'ann�e � afficher
			$num_periode:     num�ro de la p�riode � afficher
		*/

		//global $gepiPath;
		global $gecko;

		//echo "On est dans ajax_bulletins<br />";

		//debug_var();

		$sql="SELECT * FROM eleves WHERE login='$logineleve';";
		$res_ele=mysql_query($sql);
	
		if(mysql_num_rows($res_ele)==0) {
			// On ne devrait pas arriver l�.
			echo "<p>L'�l�ve dont le login serait '$logineleve' n'est pas dans la table 'eleves'.</p>\n";
		}
		else {
			$lig_ele=mysql_fetch_object($res_ele);
	
			// Infos �l�ve
			//$ine: INE de l'�l�ve (identifiant commun aux tables 'eleves' et 'archivage_disciplines')
			$ine=$lig_ele->no_gep;
			$ele_nom=$lig_ele->nom;
			$ele_prenom=$lig_ele->prenom;
			$naissance=$lig_ele->naissance;
			//$naissance2=formate_date($lig_ele->naissance);
	
			// Classe actuelle:
			$classe=get_nom_classe($id_classe);

			echo "<div style='border-bottom: 1px solid black; border-right: 1px solid black; padding: 3px; margin-left: 1px; ";
			echo "border-left: 1px solid black; ";
			if($gecko) {
				//echo "<div style='border: 1px solid black; background-image: url(\"../images/background/opacite50.png\"); padding: 3px;'>\n";
				echo "background-image: url(\"../images/background/opacite50.png\"); ";
			}
			else {
				//echo "<div style='border: 1px solid black; background-color: white; padding: 3px;'>\n";
				echo "background-color: white; ";
			}
			echo "'>\n";

			// Si l'ann�e scolaire n'a pas �t� pass�e en variable, on r�cup�re la premi�re ann�e scolaire pour laquelle il y a des archives pour cet �l�ve.
			if(!isset($annee_scolaire)) {
				$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee";
				$res_annee=mysql_query($sql);

				if(mysql_num_rows($res_annee)==0) {
					echo "<p>Aucune ann�e archiv�e pour cet �l�ve.</p>\n";
					die();
				}

				$lig_annee=mysql_fetch_object($res_annee);
				$annee_scolaire=$lig_annee->annee;
			}

			// Si le num_periode n'a pas �t� pass� en variable, on prend la premi�re p�riode: 1
			if(!isset($num_periode)) {
				$num_periode=1;
			}

			//$sql="SELECT DISTINCT nom_periode FROM archivage_disciplines WHERE ine='$ine' AND num_periode='$num_periode' AND annee='$annee_scolaire'";
			$sql="SELECT DISTINCT nom_periode, classe FROM archivage_disciplines WHERE ine='$ine' AND num_periode='$num_periode' AND annee='$annee_scolaire';";
			$res_per=mysql_query($sql);
	
			if(mysql_num_rows($res_per)==0) {
				$nom_periode="p�riode $num_periode";
				$classe_ant="???";
			}
			else {
				$lig_per=mysql_fetch_object($res_per);
				$nom_periode=$lig_per->nom_periode;
				$classe_ant=$lig_per->classe;
			}
	
			echo "<h2 style='color:black;'>Ant�c�dents de $ele_prenom $ele_nom: mill�sime $annee_scolaire</h2>\n";
	
			//echo "<p>Bulletin simplifi� de $prenom $nom pour la p�riode $num_periode de l'ann�e scolaire $annee_scolaire</p>";
			echo "<p>Bulletin simplifi� de $ele_prenom $ele_nom: $nom_periode de l'ann�e scolaire $annee_scolaire en <strong>$classe_ant</strong> <em style='font-size: x-small;'>(actuellement en $classe)</em></p>\n";
	
			// Affichage des infos �l�ve
	
			// Affichage des mati�res
			//echo "<table class='table_annee_anterieure' width='100%' summary='Mati�res/notes'>\n";
			echo "<table class='boireaus' width='100%' summary='Mati�res/notes'>\n";
			echo "<tr style='color:black;'>\n";
			echo "<th rowspan='2'>Mati�re</th>\n";
			echo "<th colspan='3'>Classe</th>\n";
			echo "<th rowspan='2'>El�ve</th>\n";
			echo "<th rowspan='2'>Appr�ciations/Conseils</th>\n";
			echo "</tr>\n";
	
			echo "<tr style='color:black;'>\n";
			echo "<th class='td_note_classe'>min</th>\n";
			echo "<th class='td_note_classe'>moy</th>\n";
			echo "<th class='td_note_classe'>max</th>\n";
			echo "</tr>\n";
	
			$alt=1;
			$sql="SELECT * FROM archivage_disciplines WHERE annee='$annee_scolaire' AND num_periode='$num_periode' AND ine='$ine' AND special='' ORDER BY matiere";
			//echo "$sql<br />\n";
			$res_mat=mysql_query($sql);
	
			if(mysql_num_rows($res_mat)==0) {
				// On ne devrait pas arriver l�.
				echo "<tr><td colspan='6'>Aucun r�sultat enregistr�???</td></tr>\n";
			}
			else {
				while($lig_mat=mysql_fetch_object($res_mat)) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td>";
					echo "<b>".htmlentities(stripslashes($lig_mat->matiere))."</b><br />\n";
					echo "<span class='info_prof'>".htmlentities(stripslashes($lig_mat->prof))."</span>\n";
					echo "</td>\n";
					echo "<td class='td_note_classe'>$lig_mat->moymin</td>\n";
					echo "<td class='td_note_classe'>$lig_mat->moyclasse</td>\n";
					echo "<td class='td_note_classe'>$lig_mat->moymax</td>\n";
					echo "<td class='td_note'>$lig_mat->note</td>\n";
					echo "<td>".htmlentities(stripslashes($lig_mat->appreciation))."</td>\n";
					echo "</tr>\n";
				}
			}

			// Affichage des AIds
			$sql="SELECT type.nom type_nom, aid.nom nom_aid, aid.responsables responsables, app.note_moyenne_classe moyenne_aid, app.note_min_classe min_aid, app.note_max_classe max_aid, app.note_eleve note_aid, app.appreciation appreciation, type.note_sur note_sur_aid, type.type_note type_note
				FROM archivage_appreciations_aid app, archivage_aids aid, archivage_types_aid type
				WHERE
				app.annee='$annee_scolaire' and
				app.periode='$num_periode' and
				app.id_eleve='$ine' and
				app.id_aid=aid.id and
				aid.id_type_aid=type.id and
				type.display_bulletin='y'
				ORDER BY type.nom, aid.nom";
			//echo "$sql<br />";
			$res_aid=mysql_query($sql);
			if(mysql_num_rows($res_aid)>0) {
				while($lig_aid=mysql_fetch_object($res_aid)) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td>";
					echo "<b>".htmlentities(stripslashes($lig_aid->type_nom))." : ".htmlentities(stripslashes($lig_aid->nom_aid))."</b><br />\n";
					echo "<span class='info_prof'>".htmlentities(stripslashes($lig_aid->responsables))."</span>\n";
					echo "</td>\n";
					echo "<td class='td_note_classe'>$lig_aid->moyenne_aid</td>\n";
					echo "<td class='td_note_classe'>$lig_aid->min_aid</td>\n";
					echo "<td class='td_note_classe'>$lig_aid->max_aid</td>\n";
					echo "<td class='td_note'>$lig_aid->note_aid";
					echo "</td>\n";
					echo "<td>";
					if (($lig_aid->note_sur_aid != 20) and ($lig_aid->note_aid !='-')) {
						echo "(note sur ".$lig_aid->note_sur_aid.") ";
					}
	
					echo htmlentities(stripslashes($lig_aid->appreciation))."</td>\n";
					echo "</tr>\n";
				}
			}
	
			echo "</table>\n";
	
	
			// Affichage des absences
			$sql="SELECT * FROM archivage_disciplines WHERE annee='$annee_scolaire' AND num_periode='$num_periode' AND ine='$ine' AND special='ABSENCES'";
			//echo "$sql<br />\n";
			$res_abs=mysql_query($sql);
	
			if(mysql_num_rows($res_abs)==0) {
				echo "<p>Aucune information sur les absences/retards.</p>\n";
			}
			elseif(mysql_num_rows($res_abs)>1) {
				echo "<p>Bizarre: Il y a plus d'un enregistrement pour cette �l�ve, cette p�riode et cette ann�e.</p>\n";
			}
			else {
				$lig_abs=mysql_fetch_object($res_abs);
	
				$nb_absences=$lig_abs->nb_absences;
				$non_justifie=$lig_abs->non_justifie;
				$nb_retards=$lig_abs->nb_retards;
	
				echo "<p>";
				if ($nb_absences=='0') {
					echo "<i>Aucune demi-journ�e d'absence</i>.";
				}
				else {
					echo "<i>Nombre de demi-journ�es d'absence ";
					if ($non_justifie=='0') {echo "justifi�es ";}
					echo ": </i><b>$nb_absences</b>";
					if ($non_justifie != '0') {
						echo " (dont <b>$non_justifie</b> non justifi�e"; if ($non_justifie != '1') {echo "s";}
						echo ")";
					}
					echo ".";
				}
				if ($nb_retards!='0') {
					echo "<i> Nombre de retards : </i><b>$nb_retards</b>";
				}
				echo "  (C.P.E. charg�(e)";
				echo " du suivi : ".$lig_abs->prof.")";
				if ($lig_abs->appreciation!= "") {echo "<br />$lig_abs->appreciation";}
				echo "</p>\n";
			}
	
			// Affichage de l'avis du conseil
			$sql="SELECT * FROM archivage_disciplines WHERE annee='$annee_scolaire' AND num_periode='$num_periode' AND ine='$ine' AND special='AVIS_CONSEIL'";
			//echo "$sql<br />\n";
			$res_avis=mysql_query($sql);
	
	
			//echo "<table class='table_annee_anterieure' width='100%' summary='Avis du conseil'>\n";
			echo "<table class='boireaus' width='100%' summary='Avis du conseil'>\n";
			echo "<tr>\n";
			echo "<td align='left'>\n";
			echo "<p><i>Avis du Conseil de classe : </i><br />\n";
	
			$prof_suivi="";
			if(mysql_num_rows($res_avis)==0) {
				echo "Aucune information sur l'avis du conseil de classe.</p>\n";
			}
			elseif(mysql_num_rows($res_avis)>1) {
				echo "Bizarre: Il y a plus d'un enregistrement pour cette �l�ve, cette p�riode et cette ann�e.</p>\n";
				$prof_suivi="?";
			}
			else {
				$lig_avis=mysql_fetch_object($res_avis);
				echo "$lig_avis->appreciation</p>\n";
				$prof_suivi=$lig_avis->prof;
			}
			echo "</td>\n";
			echo "<td align='center'>\n";
			echo "<p>Classe suivie par: <b>$prof_suivi</b></p>\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
	
			echo "</div>\n";
			// Afficher des liens permettant de passer rapidement � la p�riode suivante/pr�c�dente
			// + un tableau des ann�es/p�riodes (ann�es sur une ligne en colspan=nb_per et num_periode en dessous)
		}
	}
//}
?>
