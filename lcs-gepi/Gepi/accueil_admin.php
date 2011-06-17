<?php
/*
 * $Id: accueil_admin.php 6491 2011-02-11 17:10:43Z jjacquard $
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
 
 
/* ---------Variables envoy�es au gabarit
*
*	$tbs_menu
*				-> classe								classe CSS
*				-> image								icone du lien
*				-> texte								texte du titre du menu
*				-> entree								entr�es du menu
*							-> lien						lien vers la page
*							-> titre   				texte du lien
*							-> expli					explications
*	$niveau_arbo									Niveau dans l'arborescence
*	$titre_page										Titre de la page
*	$tbs_last_connection					Vide, pour ne pas avoir d'erreur dans le bandeau
*	$tbs_retour										Lien retour arri�re
*	$tbs_ariane										Fil d'arianne
*
*
*	Variables h�rit�es de :
*
*	header_template.inc
*	header_barre_prof_template.inc
*	footer_template.inc.php
*
 */


$niveau_arbo = 0;
// Initialisations files
require_once("./lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location:utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ./logout.php?auto=1");
    die();
};

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
// function affiche_ligne($chemin_,$titre_,$expli_,$tab,$statut_) {
function affiche_ligne($chemin_,$statut_) {

	$tmp_tab=explode("#",$chemin_);
	//if (acces($chemin_,$statut_)==1)  {
	if (acces($tmp_tab[0],$statut_)==1)  {
		$temp = substr($chemin_,1);
	/*
		  echo "<tr>";
		  //echo "<td width='30%'><a href=$temp>$titre_</a></span>";
		  echo "<td width='30%'><a href=$temp>$titre_</a>";
		  echo"</td>";
		  echo "<td>$expli_</td>";
		  echo "</tr>";
	*/
		return $temp;
	}else{
		return false;
	}
}


if (!checkAccess()) {
    header("Location: ./logout.php?auto=1");
    die();
}

// Begin standart header
$titre_page = "Accueil - Administration des bases";
$tbs_last_connection="";

// ====== Inclusion des balises head et du bandeau =====
include_once("./lib/header_template.inc");
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/
if (!suivi_ariane($_SERVER['PHP_SELF'],"Administration des bases"))
		echo "erreur lors de la cr�ation du fil d'ariane";
/****************************************************************

****************************************************************/

//require_once("./lib/header.inc");

//$tbs_retour="accueil.php";
//$tbs_ariane[0]=array("titre" => "accueil" , "lien"=>"accueil.php");

//if (isset($msg)) { echo "<font color='red' size='2'>$msg</font>"; }

//<center>



$chemin = array(
"/etablissements/index.php",
"/matieres/index.php",
"/utilisateurs/index.php",
"/eleves/index.php",
"/responsables/index.php",
"/classes/index.php",
//"/groupes/index.php",
"/aid/index.php",
"/mod_trombinoscopes/trombinoscopes_admin.php#gestion_fichiers",
"/mef/admin_mef.php"
);

$titre = array(
"Gestion des �tablissements",
"Gestion des mati�res",
"Gestion des comptes d'acc�s des utilisateurs",
"Gestion des ".$gepiSettings['denomination_eleves'],
"Gestion des ".$gepiSettings['denomination_responsables'],
"Gestion des classes",
//"Gestion des groupes",
"Gestion des AID",
"Gestion du trombinoscope",
"Gestion des mef (niveaux)"
);

$expli = array(
"D�finir, modifier, supprimer des �tablissements de la base de donn�es.",
"D�finir, modifier, supprimer des mati�res de la base de donn�es.",
"G�rer les comptes d'acc�s permettant aux utilisateurs de se connecter � Gepi (personnels de l'�tablissement, ".$gepiSettings['denomination_eleves']." et ".$gepiSettings['denomination_responsables'].").",
"D�finir, modifier, supprimer les ".$gepiSettings['denomination_eleves'].".",
"D�finir, modifier, supprimer les ".$gepiSettings['denomination_responsables'].".",
"D�finir, modifier, supprimer les classes.
<br />G�rer les param�tres des classes : p�riodes, coefficients, affichage du rang, ...
<br />Affecter les mati�res et les ".$gepiSettings['denomination_professeurs']." aux classes.
<br />Affecter les ".$gepiSettings['denomination_eleves']." aux classes.
<br />Affecter le ".$gepiSettings['gepi_prof_suivi'].", les CPE, modifier le r�gime et la mention \"redoublant\".
<br />Modifier les mati�res suivies par les ".$gepiSettings['denomination_eleves'].".
<br />Modifier des param�tres du bulletin.",
//"D�finir, modifier, supprimer les groupes d'enseignement",
"D�finir, modifier, supprimer des AID (Activit�s Inter-Disciplinaires).
<br />Affecter les ".$gepiSettings['denomination_professeurs']." et les ".$gepiSettings['denomination_eleves'].".",
"Rep�rer les personnels/".$gepiSettings['denomination_eleves']." n'ayant pas de photo.
<br />Vider le dossier des photos,...",
"Gestion des mef (niveaux)"
);

$nb_ligne = count($chemin);
//echo "\$nb_ligne=$nb_ligne<br />";
//
// Outils d'administration
//
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    //if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
	$tmp_tab=explode("#",$chemin[$i]);
	//echo "<p>\$chemin[$i]=".$chemin[$i]."<br />";
	//echo "\$tmp_tab[0]=".$tmp_tab[0]."<br />";
	//echo "acces($tmp_tab[0],".$_SESSION['statut'].")=".acces($tmp_tab[0],$_SESSION['statut'])."<br />";
    if (acces($tmp_tab[0],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
    //echo "<table width=750 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    /*
   echo "<table class='menu' summary='Administration des bases'>\n";
    echo "<tr>\n";
    echo "<th colspan='2'><img src='./images/icons/database.png' alt='Bases' class='link'/> - Administration des bases</th>\n";
    echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
    */
    
    $nummenu=0;
		$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'./images/icons/database.png' , 'texte'=>"Administration des bases");
	 
		for ($i=0;$i<$nb_ligne;$i++) {
			$numitem=$i;
			$adresse=affiche_ligne($chemin[$i],$_SESSION['statut']);
			if ($adresse != false) {
				$tbs_menu[$nummenu]['entree'][]=array('lien'=>$adresse , 'titre'=>$titre[$i], 'expli'=>$expli[$i]);
			}
		}
    
}


// </center>

//require_once "./lib/footer.inc.php"; 
$tbs_microtime	="";
$tbs_pmv="";
require_once ("./lib/footer_template.inc.php");
	
//==================================
// D�commenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
//debug_var();


include('./templates/origine/accueil_admin_template.php');

// ------ on vide les tableaux -----
?>
