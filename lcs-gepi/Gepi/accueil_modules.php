<?php
/*
 * $Id: accueil_modules.php 4884 2010-07-25 07:09:32Z regis $
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

// Initialisation des feuilles de style apr�s modification pour am�liorer l'accessibilit�
$accessibilite="y";

// Begin standart header
$titre_page = "Accueil - Administration des modules";
$racine_gepi = 'yes';
$affiche_connexion = 'yes';
$niveau_arbo = 0;
$gepiPathJava=".";

// Initialisations files
require_once("./lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
   header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
   die();
} else if ($resultat_session == '0') {
   header("Location: ./logout.php?auto=1");
   die();
}

if (!checkAccess()) {
	header("Location: ./logout.php?auto=1");
	die();
}

include "class_php/class_menu_general.php";

$tab[0] = "administrateur";
$tab[1] = "professeur";
$tab[2] = "cpe";
$tab[3] = "scolarite";
$tab[4] = "eleve";
$tab[5] = "secours";

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

/*
function affiche_ligne($chemin_,$titre_,$expli_,$tab,$statut_,$key_setting) {
    if (acces($chemin_,$statut_)==1)  {
        $temp = substr($chemin_,1);
        echo "<tr>\n";
        //echo "<td width=30%><a href=$temp>$titre_</a></span>";
        echo "<td>\n";
		if($key_setting!='') {
			$sql="SELECT 1=1 FROM setting WHERE name LIKE '$key_setting' AND (value='y' OR value='yes');";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				echo "<img src='images/enabled.png' width='20' height='20' title='Module actif' alt='Module actif' />\n";
			}
			else {
				echo "<img src='images/disabled.png' width='20' height='20' title='Module inactif' alt='Module inactif' />\n";
			}
		}
		else {
			echo "<img src='images/icons/ico_question.png' width='19' height='19' title='Etat inconnu' alt='Etat inconnu' />\n";
		}
        echo "</td>\n";
        echo "<td width='30%'><a href=$temp>$titre_</a>";
        echo "</td>\n";
        echo "<td>$expli_</td>\n";
        echo "</tr>\n";
    }
}
*/
/*
$titre_page = "Accueil - Administration des modules";
$racine_gepi = 'yes';
*/
/*
require_once("./lib/header.inc");


<p class=bold><a href="./accueil.php"><img src='./images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>


if (isset($msg)) { echo "<font color='red' size=2>$msg</font>"; }
echo "<center>";
*/
/*
$key_setting=array('active_cahiers_texte',
'active_carnets_notes');
$chemin = array(
"/cahier_texte_admin/index.php",
"/cahier_notes_admin/index.php");

$chemin[] = "/mod_absences/admin/index.php";
$key_setting[]='active_module_absence%';
$chemin[] = "/mod_abs2/admin/index.php";
$key_setting[]='active_module_absence%';
$chemin[] = "/edt_organisation/edt.php";
$key_setting[]='autorise_edt%';

if ($force_msj) {
	$chemin[] = "/mod_miseajour/admin/index.php";
	$key_setting[]='active_module_msj';
}

$chemin[] = "/mod_trombinoscopes/trombinoscopes_admin.php";
$key_setting[]='active_module_trombinoscopes';
$chemin[] = "/mod_notanet/notanet_admin.php";
$key_setting[]='active_notanet';
$chemin[] = "/mod_inscription/inscription_admin.php";
$key_setting[]='active_inscription';
$chemin[] = "/cahier_texte_admin/rss_cdt_admin.php";
$key_setting[]='rss_cdt_eleve';

$titre = array(
"Cahier de textes",
"Carnets de notes");
$titre[] = "Absences";
$titre[] = "Absences 2";
$titre[] = "Emploi du temps";
if ($force_msj) {$titre[] = "Mise � jour automatis�e";}
$titre[] = "Trombinoscope";
$titre[] = "Notanet/Fiches Brevet";
$titre[] = "Inscription";
$titre[] = "<img src=\"images/icons/rss.png\" alt='rss' />&nbsp;-&nbsp;Flux rss";

$expli = array(
"Pour g�rer les cahiers de texte, (configuration g�n�rale, ...)",
"Pour g�rer les carnets de notes (configuration g�n�rale, ...)");
$expli[] = "Pour g�rer le module absences";
$expli[] = "Pour g�rer le module absences 2 (en cours de developpement)";
$expli[] = "Pour g�rer l'ouverture de l'emploi du temps de Gepi.";
if ($force_msj) {$expli[] = "Pour g�rer le module de mise � jour de GEPI";}
$expli[] = "Pour g�rer le module trombinoscope";
$expli[] = "Pour g�rer le module Notanet/Fiches Brevet";
$expli[] = "Pour g�rer simplement les inscriptions des ".$gepiSettings['denomination_professeurs']." par exemple � des stages ou bien des interventions dans les coll�ges";
$expli[] = "Gestion des flux rss des cahiers de textes produits par Gepi";

// AUtorisation des statuts personnalis�s
// Ann�es ant�rieures
$chemin[] = "/utilisateurs/creer_statut_admin.php";
$titre[] = "Cr�er des statuts personnalis�s";
$expli[] = "D�finir des statuts suppl�mentaires en personnalisant les droits d'acc�s.";
$key_setting[]='statuts_prives';

// Ann�es ant�rieures
$chemin[] = "/mod_annees_anterieures/admin.php";
$titre[] = "Ann�es ant�rieures";
$expli[] = "Pour g�rer le module Ann�es ant�rieures";
$key_setting[]='active_annees_anterieures';

// Module ateliers
$chemin[] = "/mod_ateliers/ateliers_config.php";
$titre[] = "Ateliers";
$expli[] = "Gestion et mise en place d'ateliers de type conf�rences (gestion des ateliers, des intervenants, des inscriptions...).";
$key_setting[]='active_ateliers';

// Module discipline
$chemin[] = "/mod_discipline/discipline_admin.php";
$titre[] = "Discipline";
$expli[] = "Pour g�rer le module Discipline.";
$key_setting[]='active_mod_discipline';

//Module mod�le Open_Office
$chemin[] = "/mod_ooo/ooo_admin.php";
$titre[] = "Mod�le OpenOffice";
$expli[] = "Pour g�rer les mod�les Open Office de Gepi.";
$key_setting[]='active_mod_ooo';

//Module ECTS
$chemin[] = "/mod_ects/ects_admin.php";
$titre[] = "Saisie ECTS";
$expli[] = "Pour g�rer les cr�dits ECTS attribu�s pour chaque enseignement.";
$key_setting[]='active_mod_ects';

//Module Plugins
$chemin[] = "/mod_plugins/index.php";
$titre[] = "G�rer les plugins";
$expli[] = "Interface d'administration des plugins personnels de l'�tablissement.";
$key_setting[]='';

//Module G�n�se des classes
$chemin[] = "/mod_genese_classes/admin.php";
$titre[] = "G�n�se des classes";
$expli[] = "Pour g�rer le module G�n�se des classes.";
$key_setting[]='active_mod_genese_classes';

//Module Epreuve blanche
$chemin[] = "/mod_epreuve_blanche/admin.php";
$titre[] = "Epreuves blanches";
$expli[] = "Pour g�rer des �preuves blanches (anonymat des copies,...).";
$key_setting[]='active_mod_epreuve_blanche';

//Module Examen blanc
$chemin[] = "/mod_examen_blanc/admin.php";
$titre[] = "Examens blancs";
$expli[] = "Pour g�rer des examens blancs.";
$key_setting[]='active_mod_examen_blanc';

//Module "Admissions Post-Bac"
$chemin[] = "/mod_apb/admin.php";
$titre[] = "Admissions Post-Bac";
$expli[] = "Pour g�rer l'export XML vers la plateforme 'Admissions post-bac'.";
$key_setting[]='active_mod_apb';

//Module "Admissions Post-Bac"
$chemin[] = "/mod_gest_aid/admin.php";
$titre[] = "Gestionnaires d'AID";
$expli[] = "Pour ouvrir la possibilit� de d�finir des gestionnaires pour chaque AID.";
$key_setting[]='active_mod_gest_aid';

$nb_ligne = count($chemin);
//
// Outils d'administration
//
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
    //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    echo "<table class='menu' summary='Administration des modules'>\n";
    echo "<tr>\n";
    echo "<th colspan='3'><img src='./images/icons/control-center.png' alt='Admin modules' class='link'/> - Administration des modules</th>\n";
    echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut'],$key_setting[$i]);
    }
    echo "</table>\n";
}

</center>

	require("./lib/footer.inc.php");
	*/
?>
	
<?php
// ====== Inclusion des balises head et du bandeau =====
 //$msg = "Essai message";

	include_once("./lib/header_template.inc");
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/
if (!suivi_ariane($_SERVER['PHP_SELF'],"Administration des modules"))
		echo "erreur lors de la cr�ation du fil d'ariane";
/****************************************************************

****************************************************************/

/* ===== Titre du menu ===== */
	$menuTitre=array();
	$menuTitre[]=new menuGeneral;
	end($menuTitre);
	$a = key($menuTitre);
	$menuTitre[$a]->classe='accueil';
	$menuTitre[$a]->icone['chemin']='./images/icons/control-center.png';
	$menuTitre[$a]->icone['titre']='';
	$menuTitre[$a]->icone['alt']="Admin modules";
	$menuTitre[$a]->texte='Administration des modules';

/* ===== Item du menu ===== */
	$menuPage=array();
	
// cahier de texte
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/cahier_texte_admin/index.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_cahiers_texte') ;	
		$nouveauItem->titre="Cahier de textes" ;
		$nouveauItem->expli="Pour g�rer les cahiers de texte, (configuration g�n�rale, ...)" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);	
	
// cahier de notes
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/cahier_notes_admin/index.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_carnets_notes') ;	
		$nouveauItem->titre="Carnets de notes" ;
		$nouveauItem->expli="Pour g�rer les carnets de notes (configuration g�n�rale, ...)" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
	
// Absences
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_absences/admin/index.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_module_absence%') ;	
		$nouveauItem->titre="Absences" ;
		$nouveauItem->expli="Pour g�rer le module absences" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
	
// Absences 2
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_abs2/admin/index.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_module_absence%') ;	
		$nouveauItem->titre="Absences 2" ;
		$nouveauItem->expli="Pour g�rer le module absences 2 (en cours de developpement)" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
	
// Emploi du temps
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/edt_organisation/edt.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('autorise_edt%') ;	
		$nouveauItem->titre="Emploi du temps" ;
		$nouveauItem->expli="Pour g�rer l'ouverture de l'emploi du temps de Gepi." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
	
// Mise � jour automatis�e
	if ($force_msj) {
		$nouveauItem = new itemGeneral();
		$nouveauItem->chemin='/mod_miseajour/admin/index.php';	
		if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
		{
			$nouveauItem->choix_icone('active_module_msj') ;	
			$nouveauItem->titre="Mise � jour automatis�e" ;
			$nouveauItem->expli="Pour g�rer le module de mise � jour de GEPI" ;
			$menuPage[]=$nouveauItem;
		}
		unset($nouveauItem);
	}
	
// Trombinoscope
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_trombinoscopes/trombinoscopes_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_module_trombinoscopes') ;	
		$nouveauItem->titre="Trombinoscope" ;
		$nouveauItem->expli="Pour g�rer le module trombinoscope." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Notanet/Fiches Brevet
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_notanet/notanet_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_notanet') ;	
		$nouveauItem->titre="Notanet/Fiches Brevet" ;
		$nouveauItem->expli="Pour g�rer le module Notanet/Fiches Brevet" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Inscription
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_inscription/inscription_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_inscription') ;	
		$nouveauItem->titre="Inscription" ;
		$nouveauItem->expli="Pour g�rer simplement les inscriptions des ".$gepiSettings['denomination_professeurs']." par exemple � des stages ou bien des interventions dans les coll�ges" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Flux rss
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/cahier_texte_admin/rss_cdt_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('rss_cdt_eleve') ;	
		$nouveauItem->titre="<img src=\"images/icons/rss.png\" alt='rss' />&nbsp;-&nbsp;Flux rss" ;
		$nouveauItem->expli="Gestion des flux rss des cahiers de textes produits par Gepi" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);


// Autorisation des statuts personnalis�s
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/utilisateurs/creer_statut_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('statuts_prives') ;	
		$nouveauItem->titre="Cr�er des statuts personnalis�s" ;
		$nouveauItem->expli="D�finir des statuts suppl�mentaires en personnalisant les droits d'acc�s." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
	
// Ann�es ant�rieures
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_annees_anterieures/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_annees_anterieures') ;	
		$nouveauItem->titre="Ann�es ant�rieures" ;
		$nouveauItem->expli="Pour g�rer le module Ann�es ant�rieures." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Module ateliers
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_ateliers/ateliers_config.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_ateliers') ;	
		$nouveauItem->titre="Ateliers" ;
		$nouveauItem->expli="Gestion et mise en place d'ateliers de type conf�rences (gestion des ateliers, des intervenants, des inscriptions...)." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Module discipline
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/discipline_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_discipline') ;	
		$nouveauItem->titre="Discipline" ;
		$nouveauItem->expli="Pour g�rer le module Discipline." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module mod�le Open_Office
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_ooo/ooo_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_ooo') ;	
		$nouveauItem->titre="Mod�le OpenOffice" ;
		$nouveauItem->expli="Pour g�rer les mod�les Open Office de Gepi." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module ECTS
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_ects/ects_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_ects') ;	
		$nouveauItem->titre="Saisie ECTS" ;
		$nouveauItem->expli="Pour g�rer les cr�dits ECTS attribu�s pour chaque enseignement." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module Plugins
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_plugins/index.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('') ;	
		$nouveauItem->titre="G�rer les plugins" ;
		$nouveauItem->expli="Interface d'administration des plugins personnels de l'�tablissement." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module G�n�se des classes
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_genese_classes/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_genese_classes') ;	
		$nouveauItem->titre="G�n�se des classes" ;
		$nouveauItem->expli="Pour g�rer le module G�n�se des classes." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module Epreuve blanche
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_epreuve_blanche/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_epreuve_blanche') ;	
		$nouveauItem->titre="Epreuves blanches" ;
		$nouveauItem->expli="Pour g�rer des �preuves blanches (anonymat des copies,...)." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module Examen blanc
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_examen_blanc/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_examen_blanc') ;	
		$nouveauItem->titre="Examens blancs" ;
		$nouveauItem->expli="Pour g�rer des examens blancs." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module "Admissions Post-Bac"
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_apb/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_apb') ;	
		$nouveauItem->titre="Admissions Post-Bac" ;
		$nouveauItem->expli="Pour g�rer l'export XML vers la plateforme 'Admissions post-bac'." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module Gestionnaires d'AID
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_gest_aid/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_gest_aid') ;	
		$nouveauItem->titre="Gestionnaires d'AID" ;
		$nouveauItem->expli="Pour ouvrir la possibilit� de d�finir des gestionnaires pour chaque AID." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);


$tbs_microtime	="";
$tbs_pmv="";
require_once ("./lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseign�
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// D�commenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var2();


$nom_gabarit = './templates/'.$_SESSION['rep_gabarits'].'/accueil_modules_template.php';

$tbs_last_connection=""; // On n'affiche pas les derni�res connexions
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuPage);


/*
for ($i=0;$i<count($menuPage);$i++)
{
	echo "<img src='".$menuPage[$i]->icone['chemin']."' width='19' height='19' title='".$menuPage[$i]->icone['titre']."' alt='".$menuPage[$i]->icone['alt']."' />";
	echo " - ".$i." - <a href='".substr($menuPage[$i]->chemin,1)."'>".$menuPage[$i]->titre."</a> - ".$menuPage[$i]->expli."<br />";
}
*/

?>
