<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


// Initialisation des feuilles de style apr�s modification pour am�liorer l'accessibilit�
$accessibilite="y";

// Begin standart header
$titre_page = "Accueil GEPI - Professeurs";
$affiche_connexion = 'yes';
$niveau_arbo = 0;

// Initialisations files
require_once("./lib/initialisations.inc.php");

// On teste s'il y a une mise � jour de la base de donn�es � effectuer
if (test_maj()) {
	header("Location: ./utilitaires/maj.php");
}

// Resume session
$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
	header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
	die();
} else if ($resultat_session == '0') {
	header("Location: ./logout.php?auto=1");
	die();
}

// S�curit�
if (!checkAccess()) {
	header("Location: ./logout.php?auto=2");
	die();
}

unset ($_SESSION['order_by']);
$test_https = 'y'; // pour ne pas avoir � refaire le test si on a besoin de l'URL compl�te (rss)
if (!isset($_SERVER['HTTPS'])
	OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != "on")
	OR (isset($_SERVER['X-Forwaded-Proto']) AND $_SERVER['X-Forwaded-Proto'] != "https"))
{
	$test_https = 'n';
}


if($_SESSION['statut']=='professeur'){
	$accueil_simpl=isset($_GET['accueil_simpl']) ? $_GET['accueil_simpl'] : NULL;
	if(!isset($accueil_simpl)){
		$pref_accueil_simpl=getPref($_SESSION['login'],'accueil_simpl',"n");
		$accueil_simpl=$pref_accueil_simpl;
	}

	if($accueil_simpl=="y"){
		header("Location: ./accueil_simpl_prof.php");
	}
}
else{
	$accueil_simpl=NULL;
}

// End standard header
require_once("./lib/header.inc");
/*
function acces($id,$statut) {
	$tab_id = explode("?",$id);
	$query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
	$droit = @mysql_result($query_droits, 0, $statut);
	if ($droit == "V") {
		return "1";
	} else {
		return "0";
	}
}
*/

function affiche_ligne($chemin_, $titre_, $expli_, $statut_) {
	if (acces($chemin_,$statut_)==1)  {
		$temp = substr($chemin_,1);
		echo "<tr>\n";
		echo "<td class='acc_prof'><a href=\"$temp\" title=\"$expli_\" >";
		echo "&nbsp; <img src=\"./images/info_p1.png\" alt=\"En savoir plus\" title=\"$expli_\" /> - $titre_</a></td>\n";
		echo "</tr>\n";
	}
}

//fonction compte_fin_module permet de mettre des balises tr ou td tous les deux modules affich�
$compteur_module = 0;
function compte_fin_module () {
	global $compteur_module;
	$compteur_module = $compteur_module + 1;
	if ($compteur_module % 2 == 1) {
		echo '</td><td	valign=top>';
	} else {
		echo '</td></tr>';
		echo '<tr><td valign=top>';
	}
}

echo "<p class='bold'>\n";
echo "<a href='accueil_simpl_prof.php'>Interface graphique</a>";
echo "</p>\n";

echo '<table align="center"><tr><td	valign=top>';

/**********************************************************************
	Outils de gestion au quotidien
 * **********************************************************************/
$test_prof_matiere = sql_count(sql_query("SELECT login FROM j_groupes_professeurs WHERE login = '".$_SESSION['login']."'"));
//on test si il faut afficher le bloc
if ((($test_prof_matiere != "0") and (getSettingValue("active_cahiers_texte")=='y')) || (($test_prof_matiere != "0") and (getSettingValue("active_carnets_notes")=='y')) || (getSettingValue("active_module_absence_professeur")=='y'  and getSettingValue("active_module_absence")=='y' )) {
	echo "<div class='bloc_page_acceuil_professeur'>";
	echo '<table class="table_acceuil">';
	echo '<tbody>';
	echo '<th class="accueil">&nbsp;&nbsp;<img src="./images/icons/saisie.png" alt="Saisie" class="link" />&nbsp;- Au quotidien&nbsp;:</th>';
	if (($test_prof_matiere != "0") and (getSettingValue("active_cahiers_texte")=='y')) {
		affiche_ligne( "/cahier_texte/index.php", "Cahier de textes",  "Cet outil vous permet de constituer un cahier de texte pour chacune de vos classes.", $_SESSION['statut']);
	}
	if (($test_prof_matiere != "0") and (getSettingValue("active_carnets_notes")=='y')) {
		affiche_ligne( "/cahier_notes/index.php", "Carnet de notes",  "Cet outil vous permet de constituer un carnet de notes pour chaque p&eacute;riode et de saisir les notes de toutes vos &eacute;valuations.", $_SESSION['statut']);
	}
	if (getSettingValue("active_module_absence_professeur")=='y'  and getSettingValue("active_module_absence")=='y' ) {
		affiche_ligne( "/mod_absences/professeurs/prof_ajout_abs.php", "Absences",  "Cet outil vous permet de g&eacute;rer les absences durant vos cours.", $_SESSION['statut']);
	}
	
	if (getSettingValue("active_module_absence_professeur")=='y'  and getSettingValue("active_module_absence")=='2' ) {
		affiche_ligne( "/mod_ab2/index.php", "Absences",  "Cet outil vous permet de g&eacute;rer les absences durant vos cours.", $_SESSION['statut']);
	}
	echo '</tbody>';
	echo '</table>';
	echo "</div>";
	compte_fin_module();
}

/**********************************************************************
	En fin de periode
 * **********************************************************************/
if ($test_prof_matiere != "0") {
	echo "<div class='bloc_page_acceuil_professeur'>";
	echo '<table class="table_acceuil">';
	echo '<tbody>';
	echo '<th class="accueil">&nbsp;&nbsp;<img src="./images/icons/saisie.png" alt="" class="link" /> - En fin de p&eacute;riode&nbsp;:</th>';
	affiche_ligne( "/saisie/index.php", "Remplir les bulletins pour mes mati&egrave;res",  "Cet outil permet de remplir les bulletins pour votre mati&egrave;re.", $_SESSION['statut']);
	affiche_ligne( "/prepa_conseil/index1.php", "Recapituler mes moyennes et appr&eacute;ciations",  "Cet outil permet de r&eacute;capituler mes moyennes et appr&eacute;ciations pour pr&eacute;parer le conseil de classe.", $_SESSION['statut']);
	echo '</tbody>';
	echo '</table>';
	echo "</div>";
	compte_fin_module();
}

/**********************************************************************
	Visualiser / imprimer
**********************************************************************/
$test_prof_suivi = sql_count(sql_query("SELECT professeur FROM j_eleves_professeurs  WHERE professeur = '".$_SESSION['login']."'"));
$condition_releve_note = (
	(getSettingValue("active_carnets_notes")=='y')
	AND
	(
		(getSettingValue("GepiAccesReleveProf") == "yes") OR
		(getSettingValue("GepiAccesReleveProfTousEleves") == "yes") OR
		(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes") OR
		((getSettingValue("GepiAccesReleveProfP") == "yes") AND ($test_prof_suivi != "0"))
	)
);
$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes");
$active_module_trombino_pers=getSettingValue("active_module_trombino_pers");

//aid
$call_data = sql_query("select indice_aid, nom from aid_config WHERE outils_complementaires = 'y' order by nom_complet");
$nb_aid = mysql_num_rows($call_data);
$call_data2 = sql_query("select id from archivage_types_aid WHERE outils_complementaires = 'y'");
$nb_aid_annees_anterieures = mysql_num_rows($call_data2);
$nb_total=$nb_aid+$nb_aid_annees_anterieures;

if ($condition_releve_note ||  (getSettingValue("GepiAccesBulletinSimplePP")=="yes") || ($active_module_trombinoscopes=='y') || ($active_module_trombino_pers=='y') || ($nb_total != 0)) {
	echo "<div class='bloc_page_acceuil_professeur'>";
	echo '<table class="table_acceuil">';
	echo '<tbody>';
	echo '<th class="accueil">&nbsp;&nbsp;<img src="./images/icons/contact.png" alt="Trombi" class="link" /><img src="./images/icons/print.png" alt="Imprimer" class="link" />- Visualiser/Imprimer :</th>';

/** relev�s de notes**/
	if ($condition_releve_note) {
		affiche_ligne( "/cahier_notes/visu_releve_notes_bis.php", "Les relev&eacute;s de notes",   "Cet outil vous permet de visualiser &agrave; l'&eacute;cran et d'imprimer les relev&eacute;s de notes, ".$gepiSettings['denomination_eleve']." par ".$gepiSettings['denomination_eleve'].", classe par classe.", $_SESSION['statut']);
	}

	// Bulletins simplifi�s
	if (getSettingValue("GepiAccesBulletinSimplePP")=="yes") {
		$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."';";
		$test_pp=mysql_num_rows(mysql_query($sql));
		if($test_pp>0) {
			affiche_ligne( "/prepa_conseil/index3.php", "Les bulletins simplifi&eacute;s",   "Cet outil vous permet de visualiser &agrave; l'&eacute;cran et d'imprimer les relev&eacute;s de notes, ".$gepiSettings['denomination_eleve']." par ".$gepiSettings['denomination_eleve'].", classe par classe.", $_SESSION['statut']);
		}
	}

	//Trombinoscope
	if (($active_module_trombinoscopes=='y') || ($active_module_trombino_pers=='y')) {
		affiche_ligne( "/mod_trombinoscopes/trombinoscopes.php", "Les trombinoscopes",   "Cet outil vous permet de visualiser les trombinoscopes des classes.", $_SESSION['statut']);

		// On appelle les aid "trombinoscope"
		$chemin = array();
		$titre = array();
		$expli = array();
		$call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid= '".getSettingValue("num_aid_trombinoscopes")."' ORDER BY nom");
		$nb_aid = mysql_num_rows($call_data);
		$i=0;
		while ($i < $nb_aid) {
			$indice_aid = @mysql_result($call_data, $i, "indice_aid");
			$call_prof = mysql_query("SELECT * FROM j_aid_utilisateurs_gest WHERE (id_utilisateur = '" . $_SESSION['login'] . "' and indice_aid = '$indice_aid')");
			$nb_result = mysql_num_rows($call_prof);
			if (($nb_result != 0) or ($_SESSION['statut'] == 'secours')) {
				$nom_aid = @mysql_result($call_data, $i, "nom");
				$chemin[] = "/aid/index2.php?indice_aid=".$indice_aid;
				$titre[] = $nom_aid;
				$expli[] = "Cet outil vous permet de visualiser quels ".$gepiSettings['denomination_eleves']." ont le droit d'envoyer/modifier leur photo.";
			}
			$i++;
		}

		$nb_ligne = count($chemin);
		for ($i=0;$i<$nb_ligne;$i++) {
			if (acces($chemin[$i],$_SESSION['statut'])==1)  {affiche_ligne($chemin[$i],$titre[$i],$expli[$i], $_SESSION['statut']);}
		}
	}

	//equipe pedagogique et fiche eleve
	affiche_ligne( "/groupes/visu_profs_class.php", "Les &eacute;quipes p&eacute;dagogiques",   "Ceci vous permet de conna&icirc;tre tous les ".$gepiSettings['denomination_professeurs']." des classes dans lesquelles vous intervenez, ainsi que les compositions des groupes concern&eacute;s.", $_SESSION['statut']);
	affiche_ligne( "/eleves/visu_eleve.php", "Les fiches ".$gepiSettings['denomination_eleve'],  "Ce menu vous permet de consulter dans une m&ecirc;me page les informations concernant un ".$gepiSettings['denomination_eleve'], $_SESSION['statut']);

	//aid
	if ($nb_total != 0) {
		$chemin = array();
		$titre = array();
		$expli = array();
		$i = 0;
		while ($i<$nb_aid) {
			$indice_aid = mysql_result($call_data,$i,"indice_aid");
			$_indice_aid[] = mysql_result($call_data,$i,"indice_aid");
			$nom_aid = mysql_result($call_data,$i,"nom");
			$chemin[]="/aid/index_fiches.php?indice_aid=".$indice_aid;
			$titre[] = $nom_aid;
			$expli[] = "Tableau r&eacute;capitulatif, liste des ".$gepiSettings['denomination_eleves'].", ...";
			$i++;
		}
		$nb_ligne = count($chemin);
		$affiche = 'no';
		for ($i=0;$i<$nb_ligne;$i++) {
			if ((acces($chemin[$i],$_SESSION['statut'])==1))  {$affiche = 'yes';}
		}
		if (($nb_aid_annees_anterieures > 0) and (acces("/aid/annees_anterieures_accueil.php",$_SESSION['statut'])==1)) $affiche = 'yes';

		if ($affiche=='yes') {
			for ($i=0;$i<$nb_ligne;$i++) {
				if (AfficheAid($_SESSION['statut'],$_SESSION['login'],$_indice_aid[$i]))
				affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
			}
			if (($nb_aid_annees_anterieures > 0) and (acces("/aid/annees_anterieures_accueil.php",$_SESSION['statut'])==1)) {
				$chemin_="/aid/annees_anterieures_accueil.php";
				$titre_ = "Fiches projets des ann&eacute;es ant&eacute;rieures";
				$expli_ = "Acc&egrave;s administrateur aux fiches projets des ann&eacute;es ant&eacute;rieures";
				affiche_ligne($chemin_,$titre_,$expli_,$tab,$_SESSION['statut']);
			}
		}
	}

/* Emploi du temps ***/
	affiche_ligne( "/edt_organisation/index_edt.php", "L'emploi du temps",  "Cet outil permet la consultation des emplois du temps.", $_SESSION['statut']);

	echo '</tbody>';
	echo '</table>';
	echo "</div>";
	compte_fin_module();
}
 /*********************************************************************
	Fin Visualiser / imprimer
**********************************************************************/

/**********************************************
	Preparer le conseil
**********************************************/
if (($test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf")=='yes')) {
	echo "<div class='bloc_page_acceuil_professeur'>";
	echo '<table class="table_acceuil">';
	echo '<tbody>';
	echo '<th class="accueil">&nbsp;&nbsp;<img src="./images/icons/saisie.png" alt="Saisie" class="link" /> - Pr&eacute;parer le conseil&nbsp;:</th>';
	affiche_ligne( "/prepa_conseil/index2.php", "Moyennes d'une classe",  "Cet outil permet de r&eacute;capituler les moyennes d'une classe pour pr&eacute;parer le conseil.", $_SESSION['statut']);
	affiche_ligne( "/saisie/saisie_avis.php", "Remplir les pieds de bulletins",  "Cet outil permet de remplir les pieds de bulletins en tant que professeur principal.", $_SESSION['statut']);
	affiche_ligne( "/visualisation/index.php", "Comparatifs (graphiques)",  "Cet outil permet d'avoir des comparatifs graphiques.", $_SESSION['statut']);
	affiche_ligne( "/cahier_notes/index2.php", "Moyennes des carnets de notes",  "Cet outil permet un r&eacute;sum&eacute; des moyennes du carnet de notes.", $_SESSION['statut']);
	echo '</tbody>';
	echo '</table>';
	echo "</div>";
	compte_fin_module();
}
/**********************************************
	Fin Preparer le conseil
**********************************************/

/**********************************************
	Outils
**********************************************/
echo "<div class='bloc_page_acceuil_professeur'>";
echo '<table class="table_acceuil">';
echo '<tbody>';
echo '<th class="accueil">&nbsp;&nbsp;<img src="./images/icons/saisie.png" alt="saisie" class="link" />- Outils&nbsp;:</th>';
affiche_ligne( "/groupes/mes_listes.php", "Listes d'&eacute;l&egrave;ves",  "Vos listes d'&eacute;l&egrave;ves au format CSV avec les champs CLASSE;LOGIN;NOM;PRENOM;SEXE;DATE_NAISS.", $_SESSION['statut']);
affiche_ligne( "/impression/impression_serie.php", "Listes d'&eacute;l&egrave;ves (<img src='./images/icons/pdf.png' alt='au format pdf' />)",  "Vos listes d'&eacute;l&egrave;ves au format PDF", $_SESSION['statut']);
if (($test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf")=='yes')) {
	affiche_ligne( "/saisie/impression_avis.php", "Avis du conseil de classe",  "Cet outil donne une synth&egrave;se des avis du conseil de classe.", $_SESSION['statut']);
}
echo '</tbody>';
echo '</table>';
echo "</div>";
compte_fin_module();
/**********************************************
	Fin Outils
**********************************************/

/**********************************************
	Gestion de projets/evenements
**********************************************/
if (getSettingValue("active_inscription")=='y' && getSettingValue("active_inscription_utilisateurs")=='y') {
	echo "<div class='bloc_page_acceuil_professeur'>";
	echo '<table class="table_acceuil">';
	echo '<tbody>';
	echo '<th class="accueil">&nbsp;&nbsp;<img src="./images/icons/saisie.png" alt="" class="link" /> - Gestion de projets&nbsp;:</th>';
	affiche_ligne("/mod_inscription/inscription_index.php",  "Acc&egrave;s au module d'inscription/visualisation", "S'inscrire ou se d&eacute;sinscrire - Consulter les inscriptions", $_SESSION['statut']);
	echo '</tbody>';
	echo '</table>';
	echo "</div>";
	compte_fin_module();
}
/**********************************************
	Fin Gestion de projets/evenements
**********************************************/
echo '</td></tr></table>';

require_once ("./lib/footer.inc.php");
?>
