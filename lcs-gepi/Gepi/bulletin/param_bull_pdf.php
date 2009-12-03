<?php
/*
* $Id: param_bull_pdf.php 3843 2009-11-30 16:22:27Z crob $
*
* Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


// r�glage pour le utf8
$decode = isset($_POST["decode"]) ? $_POST["decode"] : 'n';
$ok = isset($_POST["ok"]) ? $_POST["ok"] : NULL;
if ($ok == "Enregistrer") {
	// On peut alors tester les variables envoy�es et mettre � jour les r�glages pour l'utf8
		// On v�rifie si le setting existe
	$operation = saveSetting('decode_pdf_utf8', $decode) OR DIE('Erreur dans le saveSetting().');
}

$reg_ok = 'yes';
$msg = '';
if (isset($_POST['option_modele_bulletin'])) {
	// Sauvegarde des param�trages par d�faut des choix de mod�les pour les classes
	if (!saveSetting("option_modele_bulletin", $_POST['option_modele_bulletin'])) {
		$msg .= "Erreur lors de l'enregistrement de option_modele_bulletin !";
		$reg_ok = 'no';
	}
}

//=========================
// AJOUT: boireaus 20081224
if(isset($_POST['valide_modif_model'])) {
	$affiche_nom_etab=isset($_POST['affiche_nom_etab']) ? $_POST['affiche_nom_etab'] : 0;
	$affiche_adresse_etab=isset($_POST['affiche_adresse_etab']) ? $_POST['affiche_adresse_etab'] : 0;
}
//=========================
//===================================================
// Christian renvoye vers le fichier PDF bulletin
	if (empty($_GET['classe']) and empty($_POST['classe'])) {$classe="";}
		else { if (isset($_GET['classe'])) {$classe=$_GET['classe'];} if (isset($_POST['classe'])) {$classe=$_POST['classe'];} }
	if (empty($_GET['eleve']) and empty($_POST['eleve'])) {$eleve="";}
		else { if (isset($_GET['eleve'])) {$eleve=$_GET['eleve'];} if (isset($_POST['eleve'])) {$eleve=$_POST['eleve'];} }
	if (empty($_GET['periode']) and empty($_POST['periode'])) {$periode="";}
		else { if (isset($_GET['periode'])) {$periode=$_GET['periode'];} if (isset($_POST['periode'])) {$periode=$_POST['periode'];} }
	if (empty($_GET['creer_pdf']) and empty($_POST['creer_pdf'])) {$creer_pdf="";}
		else { if (isset($_GET['creer_pdf'])) {$creer_pdf=$_GET['creer_pdf'];} if (isset($_POST['creer_pdf'])) {$creer_pdf=$_POST['creer_pdf'];} }

	if (empty($_GET['type_bulletin']) and empty($_POST['type_bulletin'])) {$type_bulletin="";}
		else { if (isset($_GET['type_bulletin'])) {$type_bulletin=$_GET['type_bulletin'];} if (isset($_POST['type_bulletin'])) {$type_bulletin=$_POST['type_bulletin'];} }

	if (empty($_GET['periode_ferme']) and empty($_POST['periode_ferme'])) { $periode_ferme = ''; }
	else { if (isset($_GET['periode_ferme'])) { $periode_ferme = $_GET['periode_ferme']; } if (isset($_POST['periode_ferme'])) { $periode_ferme = $_POST['periode_ferme']; } }
	if (empty($_GET['selection_eleve']) and empty($_POST['selection_eleve'])) { $selection_eleve = ''; }
	else { if (isset($_GET['selection_eleve'])) { $selection_eleve = $_GET['selection_eleve']; } if (isset($_POST['selection_eleve'])) { $selection_eleve = $_POST['selection_eleve']; } }

	$message_erreur = '';
	if ( !empty($classe[0]) and empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de s�lectionner la ou les p�riode(s) !'; }
	if ( empty($classe[0]) and !empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de s�lectionner la ou les classe(s) !'; }
	if ( empty($classe[0]) and empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de s�lectionner la ou les classe(s) et la ou les p�riode(s) !'; }

	$_SESSION['classe'] = $classe;
	$_SESSION['eleve'] = $eleve;
	$_SESSION['periode'] = $periode;
	$_SESSION['periode_ferme'] = $periode_ferme;
	$_SESSION['type_bulletin'] = $type_bulletin;


	//==========================================
	// CETTE PAGE N'EXISTE PLUS
	//if(!empty($creer_pdf) and !empty($periode[0]) and !empty($classe[0]) and !empty($type_bulletin) and empty($selection_eleve) ) {  header("Location: buletin_pdf.php"); }
	//==========================================

// FIN Christian renvoye vers le fichier PDF bulletin
//===================================================


//===================================================
// Modif Christian pour les variable PDF
	$selection = isset($_POST["selection"]) ? $_POST["selection"] :NULL;
	$selection_eleve = isset($_POST["selection_eleve"]) ? $_POST["selection_eleve"] :NULL;
	$bt_select_periode = isset($_POST["bt_select_periode"]) ? $_POST["bt_select_periode"] :NULL;
	$valide_modif_model = isset($_POST["valide_modif_model"]) ? $_POST["valide_modif_model"] :NULL;

	if (empty($_FILES['fichier'])) { $fichier = ""; } else { $fichier = $_FILES['fichier']; }
	if (empty($_GET['format']) and empty($_POST['format'])) {$format="";}
		else { if (isset($_GET['format'])) {$format=$_GET['format'];} if (isset($_POST['format'])) {$format=$_POST['format'];} }
	if (empty($_GET['modele']) and empty($_POST['modele'])) {$modele="";}
		else { if (isset($_GET['modele'])) {$modele=$_GET['modele'];} if (isset($_POST['modele'])) {$modele=$_POST['modele'];} }
	if (empty($_GET['action_model']) and empty($_POST['action_model'])) {$action_model="";}
		else { if (isset($_GET['action_model'])) {$action_model=$_GET['action_model'];} if (isset($_POST['action_model'])) {$action_model=$_POST['action_model'];} }
	if (empty($_GET['modele_action']) and empty($_POST['modele_action'])) {$modele_action='';}
		else { if (isset($_GET['modele_action'])) {$modele_action=$_GET['modele_action'];} if (isset($_POST['modele_action'])) {$modele_action=$_POST['modele_action'];} }
	if (empty($_GET['action']) and empty($_POST['action'])) {$action="";}
		else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }


	if (empty($_GET['id_model_bulletin']) and empty($_POST['id_model_bulletin'])) {$id_model_bulletin="";}
	    else { if (isset($_GET['id_model_bulletin'])) {$id_model_bulletin=$_GET['id_model_bulletin'];} if (isset($_POST['id_model_bulletin'])) {$id_model_bulletin=$_POST['id_model_bulletin'];} }
	//if (empty($_GET['id_modele_bulletin']) and empty($_POST['id_modele_bulletin'])) {$id_modele_bulletin="";}
	//	else { if (isset($_GET['id_modele_bulletin'])) {$id_modele_bulletin=$_GET['id_modele_bulletin'];} if (isset($_POST['id_modele_bulletin'])) {$id_modele_bulletin=$_POST['id_modele_bulletin'];} }

	if (empty($_GET['active_bloc_datation']) and empty($_POST['active_bloc_datation'])) { $active_bloc_datation = ''; }
	else { if (isset($_GET['active_bloc_datation'])) { $active_bloc_datation = $_GET['active_bloc_datation']; } if (isset($_POST['active_bloc_datation'])) { $active_bloc_datation = $_POST['active_bloc_datation']; } }
	if (empty($_GET['active_bloc_eleve']) and empty($_POST['active_bloc_eleve'])) { $active_bloc_eleve = ''; }
	else { if (isset($_GET['active_bloc_eleve'])) { $active_bloc_eleve = $_GET['active_bloc_eleve']; } if (isset($_POST['active_bloc_eleve'])) { $active_bloc_eleve = $_POST['active_bloc_eleve']; } }
	if (empty($_GET['active_bloc_adresse_parent']) and empty($_POST['active_bloc_adresse_parent'])) { $active_bloc_adresse_parent = ''; }
	else { if (isset($_GET['active_bloc_adresse_parent'])) { $active_bloc_adresse_parent = $_GET['active_bloc_adresse_parent']; } if (isset($_POST['active_bloc_adresse_parent'])) { $active_bloc_adresse_parent = $_POST['active_bloc_adresse_parent']; } }
	if (empty($_GET['active_bloc_absence']) and empty($_POST['active_bloc_absence'])) { $active_bloc_absence = ''; }
	else { if (isset($_GET['active_bloc_absence'])) { $active_bloc_absence = $_GET['active_bloc_absence']; } if (isset($_POST['active_bloc_absence'])) { $active_bloc_absence = $_POST['active_bloc_absence']; } }
	if (empty($_GET['active_bloc_note_appreciation']) and empty($_POST['active_bloc_note_appreciation'])) { $active_bloc_note_appreciation = ''; }
	else { if (isset($_GET['active_bloc_note_appreciation'])) { $active_bloc_note_appreciation = $_GET['active_bloc_note_appreciation']; } if (isset($_POST['active_bloc_note_appreciation'])) { $active_bloc_note_appreciation = $_POST['active_bloc_note_appreciation']; } }
	if (empty($_GET['active_bloc_avis_conseil']) and empty($_POST['active_bloc_avis_conseil'])) { $active_bloc_avis_conseil = ''; }
	else { if (isset($_GET['active_bloc_avis_conseil'])) { $active_bloc_avis_conseil = $_GET['active_bloc_avis_conseil']; } if (isset($_POST['active_bloc_avis_conseil'])) { $active_bloc_avis_conseil = $_POST['active_bloc_avis_conseil']; } }
	if (empty($_GET['active_bloc_chef']) and empty($_POST['active_bloc_chef'])) { $active_bloc_chef = ''; }
	else { if (isset($_GET['active_bloc_chef'])) { $active_bloc_chef = $_GET['active_bloc_chef']; } if (isset($_POST['active_bloc_chef'])) { $active_bloc_chef = $_POST['active_bloc_chef']; } }
	if (empty($_GET['active_photo']) and empty($_POST['active_photo'])) { $active_photo = ''; }
	else { if (isset($_GET['active_photo'])) { $active_photo = $_GET['active_photo']; } if (isset($_POST['active_photo'])) { $active_photo = $_POST['active_photo']; } }
	if (empty($_GET['active_coef_moyenne']) and empty($_POST['active_coef_moyenne'])) { $active_coef_moyenne = ''; }
	else { if (isset($_GET['active_coef_moyenne'])) { $active_coef_moyenne = $_GET['active_coef_moyenne']; } if (isset($_POST['active_coef_moyenne'])) { $active_coef_moyenne = $_POST['active_coef_moyenne']; } }
	if (empty($_GET['active_nombre_note']) and empty($_POST['active_nombre_note'])) { $active_nombre_note = ''; }
	else { if (isset($_GET['active_nombre_note'])) { $active_nombre_note = $_GET['active_nombre_note']; } if (isset($_POST['active_nombre_note'])) { $active_nombre_note = $_POST['active_nombre_note']; } }
	if (empty($_GET['active_nombre_note_case']) and empty($_POST['active_nombre_note_case'])) { $active_nombre_note_case = ''; }
	else { if (isset($_GET['active_nombre_note_case'])) { $active_nombre_note_case = $_GET['active_nombre_note_case']; } if (isset($_POST['active_nombre_note_case'])) { $active_nombre_note_case = $_POST['active_nombre_note_case']; } }
	if (empty($_GET['active_moyenne']) and empty($_POST['active_moyenne'])) { $active_moyenne = ''; }
	else { if (isset($_GET['active_moyenne'])) { $active_moyenne = $_GET['active_moyenne']; } if (isset($_POST['active_moyenne'])) { $active_moyenne = $_POST['active_moyenne']; } }
	if (empty($_GET['active_moyenne_eleve']) and empty($_POST['active_moyenne_eleve'])) { $active_moyenne_eleve = ''; }
	else { if (isset($_GET['active_moyenne_eleve'])) { $active_moyenne_eleve = $_GET['active_moyenne_eleve']; } if (isset($_POST['active_moyenne_eleve'])) { $active_moyenne_eleve = $_POST['active_moyenne_eleve']; } }
	if (empty($_GET['active_moyenne_classe']) and empty($_POST['active_moyenne_classe'])) { $active_moyenne_classe = ''; }
	else { if (isset($_GET['active_moyenne_classe'])) { $active_moyenne_classe = $_GET['active_moyenne_classe']; } if (isset($_POST['active_moyenne_classe'])) { $active_moyenne_classe = $_POST['active_moyenne_classe']; } }
	if (empty($_GET['active_moyenne_min']) and empty($_POST['active_moyenne_min'])) { $active_moyenne_min = ''; }
	else { if (isset($_GET['active_moyenne_min'])) { $active_moyenne_min = $_GET['active_moyenne_min']; } if (isset($_POST['active_moyenne_min'])) { $active_moyenne_min = $_POST['active_moyenne_min']; } }
	if (empty($_GET['active_moyenne_max']) and empty($_POST['active_moyenne_max'])) { $active_moyenne_max = ''; }
	else { if (isset($_GET['active_moyenne_max'])) { $active_moyenne_max = $_GET['active_moyenne_max']; } if (isset($_POST['active_moyenne_max'])) { $active_moyenne_max = $_POST['active_moyenne_max']; } }
	if (empty($_GET['active_regroupement_cote']) and empty($_POST['active_regroupement_cote'])) { $active_regroupement_cote = ''; }
	else { if (isset($_GET['active_regroupement_cote'])) { $active_regroupement_cote = $_GET['active_regroupement_cote']; } if (isset($_POST['active_regroupement_cote'])) { $active_regroupement_cote = $_POST['active_regroupement_cote']; } }
	if (empty($_GET['active_entete_regroupement']) and empty($_POST['active_entete_regroupement'])) { $active_entete_regroupement = ''; }
	else { if (isset($_GET['active_entete_regroupement'])) { $active_entete_regroupement = $_GET['active_entete_regroupement']; } if (isset($_POST['active_entete_regroupement'])) { $active_entete_regroupement = $_POST['active_entete_regroupement']; } }
	if (empty($_GET['active_moyenne_regroupement']) and empty($_POST['active_moyenne_regroupement'])) { $active_moyenne_regroupement = ''; }
	else { if (isset($_GET['active_moyenne_regroupement'])) { $active_moyenne_regroupement = $_GET['active_moyenne_regroupement']; } if (isset($_POST['active_moyenne_regroupement'])) { $active_moyenne_regroupement = $_POST['active_moyenne_regroupement']; } }
	if (empty($_GET['active_rang']) and empty($_POST['active_rang'])) { $active_rang = ''; }
	else { if (isset($_GET['active_rang'])) { $active_rang = $_GET['active_rang']; } if (isset($_POST['active_rang'])) { $active_rang = $_POST['active_rang']; } }
	if (empty($_GET['active_graphique_niveau']) and empty($_POST['active_graphique_niveau'])) { $active_graphique_niveau = ''; }
	else { if (isset($_GET['active_graphique_niveau'])) { $active_graphique_niveau = $_GET['active_graphique_niveau']; } if (isset($_POST['active_graphique_niveau'])) { $active_graphique_niveau = $_POST['active_graphique_niveau']; } }
	if (empty($_GET['active_appreciation']) and empty($_POST['active_appreciation'])) { $active_appreciation = ''; }
	else { if (isset($_GET['active_appreciation'])) { $active_appreciation = $_GET['active_appreciation']; } if (isset($_POST['active_appreciation'])) { $active_appreciation = $_POST['active_appreciation']; } }
	if (empty($_GET['affiche_doublement']) and empty($_POST['affiche_doublement'])) { $affiche_doublement = ''; }
	else { if (isset($_GET['affiche_doublement'])) { $affiche_doublement = $_GET['affiche_doublement']; } if (isset($_POST['affiche_doublement'])) { $affiche_doublement = $_POST['affiche_doublement']; } }
	if (empty($_GET['affiche_date_naissance']) and empty($_POST['affiche_date_naissance'])) { $affiche_date_naissance = ''; }
	else { if (isset($_GET['affiche_date_naissance'])) { $affiche_date_naissance = $_GET['affiche_date_naissance']; } if (isset($_POST['affiche_date_naissance'])) { $affiche_date_naissance = $_POST['affiche_date_naissance']; } }
	if (empty($_GET['affiche_dp']) and empty($_POST['affiche_dp'])) { $affiche_dp = ''; }
	else { if (isset($_GET['affiche_dp'])) { $affiche_dp = $_GET['affiche_dp']; } if (isset($_POST['affiche_dp'])) { $affiche_dp = $_POST['affiche_dp']; } }
	if (empty($_GET['affiche_nom_court']) and empty($_POST['affiche_nom_court'])) { $affiche_nom_court = ''; }
	else { if (isset($_GET['affiche_nom_court'])) { $affiche_nom_court = $_GET['affiche_nom_court']; } if (isset($_POST['affiche_nom_court'])) { $affiche_nom_court = $_POST['affiche_nom_court']; } }
	if (empty($_GET['affiche_effectif_classe']) and empty($_POST['affiche_effectif_classe'])) { $affiche_effectif_classe = ''; }
	else { if (isset($_GET['affiche_effectif_classe'])) { $affiche_effectif_classe = $_GET['affiche_effectif_classe']; } if (isset($_POST['affiche_effectif_classe'])) { $affiche_effectif_classe = $_POST['affiche_effectif_classe']; } }
	if (empty($_GET['affiche_numero_impression']) and empty($_POST['affiche_numero_impression'])) { $affiche_numero_impression = ''; }
	else { if (isset($_GET['affiche_numero_impression'])) { $affiche_numero_impression = $_GET['affiche_numero_impression']; } if (isset($_POST['affiche_numero_impression'])) { $affiche_numero_impression = $_POST['affiche_numero_impression']; } }
	if (empty($_GET['active_reperage_eleve']) and empty($_POST['active_reperage_eleve'])) { $active_reperage_eleve = ''; }
	else { if (isset($_GET['active_reperage_eleve'])) { $active_reperage_eleve = $_GET['active_reperage_eleve']; } if (isset($_POST['active_reperage_eleve'])) { $active_reperage_eleve = $_POST['active_reperage_eleve']; } }
	if (empty($_GET['couleur_reperage_eleve1']) and empty($_POST['couleur_reperage_eleve1'])) { $couleur_reperage_eleve1 = ''; }
	else { if (isset($_GET['couleur_reperage_eleve1'])) { $couleur_reperage_eleve1 = $_GET['couleur_reperage_eleve1']; } if (isset($_POST['couleur_reperage_eleve1'])) { $couleur_reperage_eleve1 = $_POST['couleur_reperage_eleve1']; } }
	if (empty($_GET['couleur_reperage_eleve2']) and empty($_POST['couleur_reperage_eleve2'])) { $couleur_reperage_eleve2 = ''; }
	else { if (isset($_GET['couleur_reperage_eleve2'])) { $couleur_reperage_eleve2 = $_GET['couleur_reperage_eleve2']; } if (isset($_POST['couleur_reperage_eleve2'])) { $couleur_reperage_eleve2 = $_POST['couleur_reperage_eleve2']; } }
	if (empty($_GET['couleur_reperage_eleve3']) and empty($_POST['couleur_reperage_eleve3'])) { $couleur_reperage_eleve3 = ''; }
	else { if (isset($_GET['couleur_reperage_eleve3'])) { $couleur_reperage_eleve3 = $_GET['couleur_reperage_eleve3']; } if (isset($_POST['couleur_reperage_eleve3'])) { $couleur_reperage_eleve3 = $_POST['couleur_reperage_eleve3']; } }
	if (empty($_GET['couleur_categorie_entete']) and empty($_POST['couleur_categorie_entete'])) { $couleur_categorie_entete = ''; }
	else { if (isset($_GET['couleur_categorie_entete'])) { $couleur_categorie_entete = $_GET['couleur_categorie_entete']; } if (isset($_POST['couleur_categorie_entete'])) { $couleur_categorie_entete = $_POST['couleur_categorie_entete']; } }
	if (empty($_GET['couleur_categorie_entete1']) and empty($_POST['couleur_categorie_entete1'])) { $couleur_categorie_entete1 = ''; }
	else { if (isset($_GET['couleur_categorie_entete1'])) { $couleur_categorie_entete1 = $_GET['couleur_categorie_entete1']; } if (isset($_POST['couleur_categorie_entete1'])) { $couleur_categorie_entete1 = $_POST['couleur_categorie_entete1']; } }
	if (empty($_GET['couleur_categorie_entete2']) and empty($_POST['couleur_categorie_entete2'])) { $couleur_categorie_entete2 = ''; }
	else { if (isset($_GET['couleur_categorie_entete2'])) { $couleur_categorie_entete2 = $_GET['couleur_categorie_entete2']; } if (isset($_POST['couleur_categorie_entete2'])) { $couleur_categorie_entete2 = $_POST['couleur_categorie_entete2']; } }
	if (empty($_GET['couleur_categorie_entete3']) and empty($_POST['couleur_categorie_entete3'])) { $couleur_categorie_entete3 = ''; }
	else { if (isset($_GET['couleur_categorie_entete3'])) { $couleur_categorie_entete3 = $_GET['couleur_categorie_entete3']; } if (isset($_POST['couleur_categorie_entete3'])) { $couleur_categorie_entete3 = $_POST['couleur_categorie_entete3']; } }
	if (empty($_GET['couleur_categorie_cote']) and empty($_POST['couleur_categorie_cote'])) { $couleur_categorie_cote = ''; }
	else { if (isset($_GET['couleur_categorie_cote'])) { $couleur_categorie_cote = $_GET['couleur_categorie_cote']; } if (isset($_POST['couleur_categorie_cote'])) { $couleur_categorie_cote = $_POST['couleur_categorie_cote']; } }
	if (empty($_GET['couleur_categorie_cote1']) and empty($_POST['couleur_categorie_cote1'])) { $couleur_categorie_cote1 = ''; }
	else { if (isset($_GET['couleur_categorie_cote1'])) { $couleur_categorie_cote1 = $_GET['couleur_categorie_cote1']; } if (isset($_POST['couleur_categorie_cote1'])) { $couleur_categorie_cote1 = $_POST['couleur_categorie_cote1']; } }
	if (empty($_GET['couleur_categorie_cote2']) and empty($_POST['couleur_categorie_cote2'])) { $couleur_categorie_cote2 = ''; }
	else { if (isset($_GET['couleur_categorie_cote2'])) { $couleur_categorie_cote2 = $_GET['couleur_categorie_cote2']; } if (isset($_POST['couleur_categorie_cote2'])) { $couleur_categorie_cote2 = $_POST['couleur_categorie_cote2']; } }
	if (empty($_GET['couleur_categorie_cote3']) and empty($_POST['couleur_categorie_cote3'])) { $couleur_categorie_cote3 = ''; }
	else { if (isset($_GET['couleur_categorie_cote3'])) { $couleur_categorie_cote3 = $_GET['couleur_categorie_cote3']; } if (isset($_POST['couleur_categorie_cote3'])) { $couleur_categorie_cote3 = $_POST['couleur_categorie_cote3']; } }
	if (empty($_GET['couleur_moy_general']) and empty($_POST['couleur_moy_general'])) { $couleur_moy_general = ''; }
	else { if (isset($_GET['couleur_moy_general'])) { $couleur_moy_general = $_GET['couleur_moy_general']; } if (isset($_POST['couleur_moy_general'])) { $couleur_moy_general = $_POST['couleur_moy_general']; } }
	if (empty($_GET['couleur_moy_general1']) and empty($_POST['couleur_moy_general1'])) { $couleur_moy_general1 = ''; }
	else { if (isset($_GET['couleur_moy_general1'])) { $couleur_moy_general1 = $_GET['couleur_moy_general1']; } if (isset($_POST['couleur_moy_general1'])) { $couleur_moy_general1 = $_POST['couleur_moy_general1']; } }
	if (empty($_GET['couleur_moy_general2']) and empty($_POST['couleur_moy_general2'])) { $couleur_moy_general2 = ''; }
	else { if (isset($_GET['couleur_moy_general2'])) { $couleur_moy_general2 = $_GET['couleur_moy_general2']; } if (isset($_POST['couleur_moy_general2'])) { $couleur_moy_general2 = $_POST['couleur_moy_general2']; } }
	if (empty($_GET['couleur_moy_general3']) and empty($_POST['couleur_moy_general3'])) { $couleur_moy_general3 = ''; }
	else { if (isset($_GET['couleur_moy_general3'])) { $couleur_moy_general3 = $_GET['couleur_moy_general3']; } if (isset($_POST['couleur_moy_general3'])) { $couleur_moy_general3 = $_POST['couleur_moy_general3']; } }
	if (empty($_GET['titre_entete_matiere']) and empty($_POST['titre_entete_matiere'])) { $titre_entete_matiere = ''; }
	else { if (isset($_GET['titre_entete_matiere'])) { $titre_entete_matiere = $_GET['titre_entete_matiere']; } if (isset($_POST['titre_entete_matiere'])) { $titre_entete_matiere = $_POST['titre_entete_matiere']; } }
	if (empty($_GET['titre_entete_coef']) and empty($_POST['titre_entete_coef'])) { $titre_entete_coef = ''; }
	else { if (isset($_GET['titre_entete_coef'])) { $titre_entete_coef = $_GET['titre_entete_coef']; } if (isset($_POST['titre_entete_coef'])) { $titre_entete_coef = $_POST['titre_entete_coef']; } }
	if (empty($_GET['titre_entete_nbnote']) and empty($_POST['titre_entete_nbnote'])) { $titre_entete_nbnote = ''; }
	else { if (isset($_GET['titre_entete_nbnote'])) { $titre_entete_nbnote = $_GET['titre_entete_nbnote']; } if (isset($_POST['titre_entete_nbnote'])) { $titre_entete_nbnote = $_POST['titre_entete_nbnote']; } }
	if (empty($_GET['titre_entete_rang']) and empty($_POST['titre_entete_rang'])) { $titre_entete_rang = ''; }
	else { if (isset($_GET['titre_entete_rang'])) { $titre_entete_rang = $_GET['titre_entete_rang']; } if (isset($_POST['titre_entete_rang'])) { $titre_entete_rang = $_POST['titre_entete_rang']; } }
	if (empty($_GET['titre_entete_appreciation']) and empty($_POST['titre_entete_appreciation'])) { $titre_entete_appreciation = ''; }
	else { if (isset($_GET['titre_entete_appreciation'])) { $titre_entete_appreciation = $_GET['titre_entete_appreciation']; } if (isset($_POST['titre_entete_appreciation'])) { $titre_entete_appreciation = $_POST['titre_entete_appreciation']; } }
	if (empty($_GET['caractere_utilse']) and empty($_POST['caractere_utilse'])) { $caractere_utilse = ''; }
	else { if (isset($_GET['caractere_utilse'])) { $caractere_utilse = $_GET['caractere_utilse']; } if (isset($_POST['caractere_utilse'])) { $caractere_utilse = $_POST['caractere_utilse']; } }
	if (empty($_GET['X_parent']) and empty($_POST['X_parent'])) { $X_parent = ''; }
	else { if (isset($_GET['X_parent'])) { $X_parent = $_GET['X_parent']; } if (isset($_POST['X_parent'])) { $X_parent = $_POST['X_parent']; } }
	if (empty($_GET['Y_parent']) and empty($_POST['Y_parent'])) { $Y_parent = ''; }
	else { if (isset($_GET['Y_parent'])) { $Y_parent = $_GET['Y_parent']; } if (isset($_POST['Y_parent'])) { $Y_parent = $_POST['Y_parent']; } }
	if (empty($_GET['X_eleve']) and empty($_POST['X_eleve'])) { $X_eleve = ''; }
	else { if (isset($_GET['X_eleve'])) { $X_eleve = $_GET['X_eleve']; } if (isset($_POST['X_eleve'])) { $X_eleve = $_POST['X_eleve']; } }
	if (empty($_GET['Y_eleve']) and empty($_POST['Y_eleve'])) { $Y_eleve = ''; }
	else { if (isset($_GET['Y_eleve'])) { $Y_eleve = $_GET['Y_eleve']; } if (isset($_POST['Y_eleve'])) { $Y_eleve = $_POST['Y_eleve']; } }
	if (empty($_GET['cadre_eleve']) and empty($_POST['cadre_eleve'])) { $cadre_eleve = ''; }
	else { if (isset($_GET['cadre_eleve'])) { $cadre_eleve = $_GET['cadre_eleve']; } if (isset($_POST['cadre_eleve'])) { $cadre_eleve = $_POST['cadre_eleve']; } }
	if (empty($_GET['X_datation_bul']) and empty($_POST['X_datation_bul'])) { $X_datation_bul = ''; }
	else { if (isset($_GET['X_datation_bul'])) { $X_datation_bul = $_GET['X_datation_bul']; } if (isset($_POST['X_datation_bul'])) { $X_datation_bul = $_POST['X_datation_bul']; } }
	if (empty($_GET['Y_datation_bul']) and empty($_POST['Y_datation_bul'])) { $Y_datation_bul = ''; }
	else { if (isset($_GET['Y_datation_bul'])) { $Y_datation_bul = $_GET['Y_datation_bul']; } if (isset($_POST['Y_datation_bul'])) { $Y_datation_bul = $_POST['Y_datation_bul']; } }
	if (empty($_GET['cadre_datation_bul']) and empty($_POST['cadre_datation_bul'])) { $cadre_datation_bul = ''; }
	else { if (isset($_GET['cadre_datation_bul'])) { $cadre_datation_bul = $_GET['cadre_datation_bul']; } if (isset($_POST['cadre_datation_bul'])) { $cadre_datation_bul = $_POST['cadre_datation_bul']; } }
	if (empty($_GET['hauteur_info_categorie']) and empty($_POST['hauteur_info_categorie'])) { $hauteur_info_categorie = ''; }
	else { if (isset($_GET['hauteur_info_categorie'])) { $hauteur_info_categorie = $_GET['hauteur_info_categorie']; } if (isset($_POST['hauteur_info_categorie'])) { $hauteur_info_categorie = $_POST['hauteur_info_categorie']; } }
	if (empty($_GET['X_note_app']) and empty($_POST['X_note_app'])) { $X_note_app = ''; }
	else { if (isset($_GET['X_note_app'])) { $X_note_app = $_GET['X_note_app']; } if (isset($_POST['X_note_app'])) { $X_note_app = $_POST['X_note_app']; } }
	if (empty($_GET['Y_note_app']) and empty($_POST['Y_note_app'])) { $Y_note_app = ''; }
	else { if (isset($_GET['Y_note_app'])) { $Y_note_app = $_GET['Y_note_app']; } if (isset($_POST['Y_note_app'])) { $Y_note_app = $_POST['Y_note_app']; } }
	if (empty($_GET['longeur_note_app']) and empty($_POST['longeur_note_app'])) { $longeur_note_app = ''; }
	else { if (isset($_GET['longeur_note_app'])) { $longeur_note_app = $_GET['longeur_note_app']; } if (isset($_POST['longeur_note_app'])) { $longeur_note_app = $_POST['longeur_note_app']; } }
	if (empty($_GET['hauteur_note_app']) and empty($_POST['hauteur_note_app'])) { $hauteur_note_app = ''; }
	else { if (isset($_GET['hauteur_note_app'])) { $hauteur_note_app = $_GET['hauteur_note_app']; } if (isset($_POST['hauteur_note_app'])) { $hauteur_note_app = $_POST['hauteur_note_app']; } }
	if (empty($_GET['largeur_coef_moyenne']) and empty($_POST['largeur_coef_moyenne'])) { $largeur_coef_moyenne = ''; }
	else { if (isset($_GET['largeur_coef_moyenne'])) { $largeur_coef_moyenne = $_GET['largeur_coef_moyenne']; } if (isset($_POST['largeur_coef_moyenne'])) { $largeur_coef_moyenne = $_POST['largeur_coef_moyenne']; } }
	if (empty($_GET['largeur_nombre_note']) and empty($_POST['largeur_nombre_note'])) { $largeur_nombre_note = ''; }
	else { if (isset($_GET['largeur_nombre_note'])) { $largeur_nombre_note = $_GET['largeur_nombre_note']; } if (isset($_POST['largeur_nombre_note'])) { $largeur_nombre_note = $_POST['largeur_nombre_note']; } }
	if (empty($_GET['largeur_d_une_moyenne']) and empty($_POST['largeur_d_une_moyenne'])) { $largeur_d_une_moyenne = ''; }
	else { if (isset($_GET['largeur_d_une_moyenne'])) { $largeur_d_une_moyenne = $_GET['largeur_d_une_moyenne']; } if (isset($_POST['largeur_d_une_moyenne'])) { $largeur_d_une_moyenne = $_POST['largeur_d_une_moyenne']; } }
	if (empty($_GET['largeur_niveau']) and empty($_POST['largeur_niveau'])) { $largeur_niveau = ''; }
	else { if (isset($_GET['largeur_niveau'])) { $largeur_niveau = $_GET['largeur_niveau']; } if (isset($_POST['largeur_niveau'])) { $largeur_niveau = $_POST['largeur_niveau']; } }
	if (empty($_GET['largeur_rang']) and empty($_POST['largeur_rang'])) { $largeur_rang = ''; }
	else { if (isset($_GET['largeur_rang'])) { $largeur_rang = $_GET['largeur_rang']; } if (isset($_POST['largeur_rang'])) { $largeur_rang = $_POST['largeur_rang']; } }
	if (empty($_GET['X_absence']) and empty($_POST['X_absence'])) { $X_absence = ''; }
	else { if (isset($_GET['X_absence'])) { $X_absence = $_GET['X_absence']; } if (isset($_POST['X_absence'])) { $X_absence = $_POST['X_absence']; } }
	if (empty($_GET['hauteur_entete_moyenne_general']) and empty($_POST['hauteur_entete_moyenne_general'])) { $hauteur_entete_moyenne_general = ''; }
	else { if (isset($_GET['hauteur_entete_moyenne_general'])) { $hauteur_entete_moyenne_general = $_GET['hauteur_entete_moyenne_general']; } if (isset($_POST['hauteur_entete_moyenne_general'])) { $hauteur_entete_moyenne_general = $_POST['hauteur_entete_moyenne_general']; } }
	if (empty($_GET['X_avis_cons']) and empty($_POST['X_avis_cons'])) { $X_avis_cons = ''; }
	else { if (isset($_GET['X_avis_cons'])) { $X_avis_cons = $_GET['X_avis_cons']; } if (isset($_POST['X_avis_cons'])) { $X_avis_cons = $_POST['X_avis_cons']; } }
	if (empty($_GET['cadre_avis_cons']) and empty($_POST['cadre_avis_cons'])) { $cadre_avis_cons = ''; }
	else { if (isset($_GET['cadre_avis_cons'])) { $cadre_avis_cons = $_GET['cadre_avis_cons']; } if (isset($_POST['cadre_avis_cons'])) { $cadre_avis_cons = $_POST['cadre_avis_cons']; } }
	if (empty($_GET['X_sign_chef']) and empty($_POST['X_sign_chef'])) { $X_sign_chef = ''; }
	else { if (isset($_GET['X_sign_chef'])) { $X_sign_chef = $_GET['X_sign_chef']; } if (isset($_POST['X_sign_chef'])) { $X_sign_chef = $_POST['X_sign_chef']; } }
	if (empty($_GET['cadre_sign_chef']) and empty($_POST['cadre_sign_chef'])) { $cadre_sign_chef = ''; }
	else { if (isset($_GET['cadre_sign_chef'])) { $cadre_sign_chef = $_GET['cadre_sign_chef']; } if (isset($_POST['cadre_sign_chef'])) { $cadre_sign_chef = $_POST['cadre_sign_chef']; } }
	if (empty($_GET['affiche_filigrame']) and empty($_POST['affiche_filigrame'])) { $affiche_filigrame = ''; }
	else { if (isset($_GET['affiche_filigrame'])) { $affiche_filigrame = $_GET['affiche_filigrame']; } if (isset($_POST['affiche_filigrame'])) { $affiche_filigrame = $_POST['affiche_filigrame']; } }
	if (empty($_GET['texte_filigrame']) and empty($_POST['texte_filigrame'])) { $texte_filigrame = ''; }
	else { if (isset($_GET['texte_filigrame'])) { $texte_filigrame = $_GET['texte_filigrame']; } if (isset($_POST['texte_filigrame'])) { $texte_filigrame = $_POST['texte_filigrame']; } }
	if (empty($_GET['affiche_logo_etab']) and empty($_POST['affiche_logo_etab'])) { $affiche_logo_etab = ''; }
	else { if (isset($_GET['affiche_logo_etab'])) { $affiche_logo_etab = $_GET['affiche_logo_etab']; } if (isset($_POST['affiche_logo_etab'])) { $affiche_logo_etab = $_POST['affiche_logo_etab']; } }
	if (empty($_GET['nom_etab_gras']) and empty($_POST['nom_etab_gras'])) { $nom_etab_gras = ''; }
	else { if (isset($_GET['nom_etab_gras'])) { $nom_etab_gras = $_GET['nom_etab_gras']; } if (isset($_POST['nom_etab_gras'])) { $nom_etab_gras = $_POST['nom_etab_gras']; } }
	if (empty($_GET['entente_mel']) and empty($_POST['entente_mel'])) { $entente_mel = ''; }
	else { if (isset($_GET['entente_mel'])) { $entente_mel = $_GET['entente_mel']; } if (isset($_POST['entente_mel'])) { $entente_mel = $_POST['entente_mel']; } }
	if (empty($_GET['entente_tel']) and empty($_POST['entente_tel'])) { $entente_tel = ''; }
	else { if (isset($_GET['entente_tel'])) { $entente_tel = $_GET['entente_tel']; } if (isset($_POST['entente_tel'])) { $entente_tel = $_POST['entente_tel']; } }
	if (empty($_GET['entente_fax']) and empty($_POST['entente_fax'])) { $entente_fax = ''; }
	else { if (isset($_GET['entente_fax'])) { $entente_fax = $_GET['entente_fax']; } if (isset($_POST['entente_fax'])) { $entente_fax = $_POST['entente_fax']; } }
	if (empty($_GET['L_max_logo']) and empty($_POST['L_max_logo'])) { $L_max_logo = ''; }
	else { if (isset($_GET['L_max_logo'])) { $L_max_logo = $_GET['L_max_logo']; } if (isset($_POST['L_max_logo'])) { $L_max_logo = $_POST['L_max_logo']; } }
	if (empty($_GET['H_max_logo']) and empty($_POST['H_max_logo'])) { $H_max_logo = ''; }
	else { if (isset($_GET['H_max_logo'])) { $H_max_logo = $_GET['H_max_logo']; } if (isset($_POST['H_max_logo'])) { $H_max_logo = $_POST['H_max_logo']; } }
	if (empty($_GET['toute_moyenne_meme_col']) and empty($_POST['toute_moyenne_meme_col'])) { $toute_moyenne_meme_col = ''; }
	else { if (isset($_GET['toute_moyenne_meme_col'])) { $toute_moyenne_meme_col = $_GET['toute_moyenne_meme_col']; } if (isset($_POST['toute_moyenne_meme_col'])) { $toute_moyenne_meme_col = $_POST['toute_moyenne_meme_col']; } }
	if (empty($_GET['active_coef_sousmoyene']) and empty($_POST['active_coef_sousmoyene'])) { $active_coef_sousmoyene = ''; }
	else { if (isset($_GET['active_coef_sousmoyene'])) { $active_coef_sousmoyene = $_GET['active_coef_sousmoyene']; } if (isset($_POST['active_coef_sousmoyene'])) { $active_coef_sousmoyene = $_POST['active_coef_sousmoyene']; } }
	if (empty($_GET['arrondie_choix']) and empty($_POST['arrondie_choix'])) { $arrondie_choix = ''; }
	else { if (isset($_GET['arrondie_choix'])) { $arrondie_choix = $_GET['arrondie_choix']; } if (isset($_POST['arrondie_choix'])) { $arrondie_choix = $_POST['arrondie_choix']; } }
	if (empty($_GET['nb_chiffre_virgule']) and empty($_POST['nb_chiffre_virgule'])) { $nb_chiffre_virgule = ''; }
	else { if (isset($_GET['nb_chiffre_virgule'])) { $nb_chiffre_virgule = $_GET['nb_chiffre_virgule']; } if (isset($_POST['nb_chiffre_virgule'])) { $nb_chiffre_virgule = $_POST['nb_chiffre_virgule']; } }
	if (empty($_GET['chiffre_avec_zero']) and empty($_POST['chiffre_avec_zero'])) { $chiffre_avec_zero = ''; }
	else { if (isset($_GET['chiffre_avec_zero'])) { $chiffre_avec_zero = $_GET['chiffre_avec_zero']; } if (isset($_POST['chiffre_avec_zero'])) { $chiffre_avec_zero = $_POST['chiffre_avec_zero']; } }
	if (empty($_GET['autorise_sous_matiere']) and empty($_POST['autorise_sous_matiere'])) { $autorise_sous_matiere = ''; }
	else { if (isset($_GET['autorise_sous_matiere'])) { $autorise_sous_matiere = $_GET['autorise_sous_matiere']; } if (isset($_POST['autorise_sous_matiere'])) { $autorise_sous_matiere = $_POST['autorise_sous_matiere']; } }
	if (empty($_GET['affichage_haut_responsable']) and empty($_POST['affichage_haut_responsable'])) { $affichage_haut_responsable = ''; }
	else { if (isset($_GET['affichage_haut_responsable'])) { $affichage_haut_responsable = $_GET['affichage_haut_responsable']; } if (isset($_POST['affichage_haut_responsable'])) { $affichage_haut_responsable = $_POST['affichage_haut_responsable']; } }
	if (empty($_GET['entete_model_bulletin']) and empty($_POST['entete_model_bulletin'])) {$entete_model_bulletin="";}
		else { if (isset($_GET['entete_model_bulletin'])) {$entete_model_bulletin=$_GET['entete_model_bulletin'];} if (isset($_POST['entete_model_bulletin'])) {$entete_model_bulletin=$_POST['entete_model_bulletin'];} }
	if (empty($_GET['ordre_entete_model_bulletin']) and empty($_POST['ordre_entete_model_bulletin'])) {$ordre_entete_model_bulletin="";}
		else { if (isset($_GET['ordre_entete_model_bulletin'])) {$ordre_entete_model_bulletin=$_GET['ordre_entete_model_bulletin'];} if (isset($_POST['ordre_entete_model_bulletin'])) {$ordre_entete_model_bulletin=$_POST['ordre_entete_model_bulletin'];} }
	if (empty($_GET['affiche_etab_origine']) and empty($_POST['affiche_etab_origine'])) {$affiche_etab_origine="";}
		else { if (isset($_GET['affiche_etab_origine'])) {$affiche_etab_origine=$_GET['affiche_etab_origine'];} if (isset($_POST['affiche_etab_origine'])) {$affiche_etab_origine=$_POST['affiche_etab_origine'];} }
	if (empty($_GET['imprime_pour']) and empty($_POST['imprime_pour'])) {$imprime_pour="";}
		else { if (isset($_GET['imprime_pour'])) {$imprime_pour=$_GET['imprime_pour'];} if (isset($_POST['imprime_pour'])) {$imprime_pour=$_POST['imprime_pour'];} }
	if (empty($_GET['copie_model']) and empty($_POST['copie_model'])) { $copie_model = ''; }
	else { if (isset($_GET['copie_model'])) { $copie_model = $_GET['copie_model']; } if (isset($_POST['copie_model'])) { $copie_model = $_POST['copie_model']; } }
	if (empty($_GET['largeur_matiere']) and empty($_POST['largeur_matiere'])) { $largeur_matiere = ''; }
	else { if (isset($_GET['largeur_matiere'])) { $largeur_matiere = $_GET['largeur_matiere']; } if (isset($_POST['largeur_matiere'])) { $largeur_matiere = $_POST['largeur_matiere']; } }
	if (empty($_GET['taille_texte_date_edition']) and empty($_POST['taille_texte_date_edition'])) { $taille_texte_date_edition = ''; }
	else { if (isset($_GET['taille_texte_date_edition'])) { $taille_texte_date_edition = $_GET['taille_texte_date_edition']; } if (isset($_POST['taille_texte_date_edition'])) { $taille_texte_date_edition = $_POST['taille_texte_date_edition']; } }
	if (empty($_GET['taille_texte_matiere']) and empty($_POST['taille_texte_matiere'])) { $taille_texte_matiere = ''; }
	else { if (isset($_GET['taille_texte_matiere'])) { $taille_texte_matiere = $_GET['taille_texte_matiere']; } if (isset($_POST['taille_texte_matiere'])) { $taille_texte_matiere = $_POST['taille_texte_matiere']; } }
	if (empty($_GET['active_moyenne_general']) and empty($_POST['active_moyenne_general'])) { $active_moyenne_general = ''; }
	else { if (isset($_GET['active_moyenne_general'])) { $active_moyenne_general = $_GET['active_moyenne_general']; } if (isset($_POST['active_moyenne_general'])) { $active_moyenne_general = $_POST['active_moyenne_general']; } }
	if (empty($_GET['titre_bloc_avis_conseil']) and empty($_POST['titre_bloc_avis_conseil'])) { $titre_bloc_avis_conseil = ''; }
	else { if (isset($_GET['titre_bloc_avis_conseil'])) { $titre_bloc_avis_conseil = $_GET['titre_bloc_avis_conseil']; } if (isset($_POST['titre_bloc_avis_conseil'])) { $titre_bloc_avis_conseil = $_POST['titre_bloc_avis_conseil']; } }
	if (empty($_GET['taille_titre_bloc_avis_conseil']) and empty($_POST['taille_titre_bloc_avis_conseil'])) { $taille_titre_bloc_avis_conseil = ''; }
	else { if (isset($_GET['taille_titre_bloc_avis_conseil'])) { $taille_titre_bloc_avis_conseil = $_GET['taille_titre_bloc_avis_conseil']; } if (isset($_POST['taille_titre_bloc_avis_conseil'])) { $taille_titre_bloc_avis_conseil = $_POST['taille_titre_bloc_avis_conseil']; } }
	if (empty($_GET['taille_profprincipal_bloc_avis_conseil']) and empty($_POST['taille_profprincipal_bloc_avis_conseil'])) { $taille_profprincipal_bloc_avis_conseil = ''; }
	else { if (isset($_GET['taille_profprincipal_bloc_avis_conseil'])) { $taille_profprincipal_bloc_avis_conseil = $_GET['taille_profprincipal_bloc_avis_conseil']; } if (isset($_POST['taille_profprincipal_bloc_avis_conseil'])) { $taille_profprincipal_bloc_avis_conseil = $_POST['taille_profprincipal_bloc_avis_conseil']; } }
	if (empty($_GET['affiche_fonction_chef']) and empty($_POST['affiche_fonction_chef'])) { $affiche_fonction_chef = ''; }
	else { if (isset($_GET['affiche_fonction_chef'])) { $affiche_fonction_chef = $_GET['affiche_fonction_chef']; } if (isset($_POST['affiche_fonction_chef'])) { $affiche_fonction_chef = $_POST['affiche_fonction_chef']; } }
	if (empty($_GET['taille_texte_fonction_chef']) and empty($_POST['taille_texte_fonction_chef'])) { $taille_texte_fonction_chef = ''; }
	else { if (isset($_GET['taille_texte_fonction_chef'])) { $taille_texte_fonction_chef = $_GET['taille_texte_fonction_chef']; } if (isset($_POST['taille_texte_fonction_chef'])) { $taille_texte_fonction_chef = $_POST['taille_texte_fonction_chef']; } }
	if (empty($_GET['taille_texte_identitee_chef']) and empty($_POST['taille_texte_identitee_chef'])) { $taille_texte_identitee_chef = ''; }
	else { if (isset($_GET['taille_texte_identitee_chef'])) { $taille_texte_identitee_chef = $_GET['taille_texte_identitee_chef']; } if (isset($_POST['taille_texte_identitee_chef'])) { $taille_texte_identitee_chef = $_POST['taille_texte_identitee_chef']; } }
	if (empty($_GET['tel_image']) and empty($_POST['tel_image'])) { $tel_image = ''; }
	else { if (isset($_GET['tel_image'])) { $tel_image = $_GET['tel_image']; } if (isset($_POST['tel_image'])) { $tel_image = $_POST['tel_image']; } }
	if (empty($_GET['tel_texte']) and empty($_POST['tel_texte'])) { $tel_texte = ''; }
	else { if (isset($_GET['tel_texte'])) { $tel_texte = $_GET['tel_texte']; } if (isset($_POST['tel_texte'])) { $tel_texte = $_POST['tel_texte']; } }
	if (empty($_GET['fax_image']) and empty($_POST['fax_image'])) { $fax_image = ''; }
	else { if (isset($_GET['fax_image'])) { $fax_image = $_GET['fax_image']; } if (isset($_POST['fax_image'])) { $fax_image = $_POST['fax_image']; } }
	if (empty($_GET['fax_texte']) and empty($_POST['fax_texte'])) { $fax_texte = ''; }
	else { if (isset($_GET['fax_texte'])) { $fax_texte = $_GET['fax_texte']; } if (isset($_POST['fax_texte'])) { $fax_texte = $_POST['fax_texte']; } }
	if (empty($_GET['courrier_image']) and empty($_POST['courrier_image'])) { $courrier_image = ''; }
	else { if (isset($_GET['courrier_image'])) { $courrier_image = $_GET['courrier_image']; } if (isset($_POST['courrier_image'])) { $courrier_image = $_POST['courrier_image']; } }
	if (empty($_GET['courrier_texte']) and empty($_POST['courrier_texte'])) { $courrier_texte = ''; }
	else { if (isset($_GET['courrier_texte'])) { $courrier_texte = $_GET['courrier_texte']; } if (isset($_POST['courrier_texte'])) { $courrier_texte = $_POST['courrier_texte']; } }
	if (empty($_GET['largeur_bloc_eleve']) and empty($_POST['largeur_bloc_eleve'])) { $largeur_bloc_eleve = ''; }
	else { if (isset($_GET['largeur_bloc_eleve'])) { $largeur_bloc_eleve = $_GET['largeur_bloc_eleve']; } if (isset($_POST['largeur_bloc_eleve'])) { $largeur_bloc_eleve = $_POST['largeur_bloc_eleve']; } }
	if (empty($_GET['hauteur_bloc_eleve']) and empty($_POST['hauteur_bloc_eleve'])) { $hauteur_bloc_eleve = ''; }
	else { if (isset($_GET['hauteur_bloc_eleve'])) { $hauteur_bloc_eleve = $_GET['hauteur_bloc_eleve']; } if (isset($_POST['hauteur_bloc_eleve'])) { $hauteur_bloc_eleve = $_POST['hauteur_bloc_eleve']; } }
	if (empty($_GET['largeur_bloc_adresse']) and empty($_POST['largeur_bloc_adresse'])) { $largeur_bloc_adresse = ''; }
	else { if (isset($_GET['largeur_bloc_adresse'])) { $largeur_bloc_adresse = $_GET['largeur_bloc_adresse']; } if (isset($_POST['largeur_bloc_adresse'])) { $largeur_bloc_adresse = $_POST['largeur_bloc_adresse']; } }
	if (empty($_GET['hauteur_bloc_adresse']) and empty($_POST['hauteur_bloc_adresse'])) { $hauteur_bloc_adresse = ''; }
	else { if (isset($_GET['hauteur_bloc_adresse'])) { $hauteur_bloc_adresse = $_GET['hauteur_bloc_adresse']; } if (isset($_POST['hauteur_bloc_adresse'])) { $hauteur_bloc_adresse = $_POST['hauteur_bloc_adresse']; } }
	if (empty($_GET['largeur_bloc_datation']) and empty($_POST['largeur_bloc_datation'])) { $largeur_bloc_datation = ''; }
	else { if (isset($_GET['largeur_bloc_datation'])) { $largeur_bloc_datation = $_GET['largeur_bloc_datation']; } if (isset($_POST['largeur_bloc_datation'])) { $largeur_bloc_datation = $_POST['largeur_bloc_datation']; } }
	if (empty($_GET['hauteur_bloc_datation']) and empty($_POST['hauteur_bloc_datation'])) { $hauteur_bloc_datation = ''; }
	else { if (isset($_GET['hauteur_bloc_datation'])) { $hauteur_bloc_datation = $_GET['hauteur_bloc_datation']; } if (isset($_POST['hauteur_bloc_datation'])) { $hauteur_bloc_datation = $_POST['hauteur_bloc_datation']; } }
	if (empty($_GET['taille_texte_classe']) and empty($_POST['taille_texte_classe'])) { $taille_texte_classe = ''; }
	else { if (isset($_GET['taille_texte_classe'])) { $taille_texte_classe = $_GET['taille_texte_classe']; } if (isset($_POST['taille_texte_classe'])) { $taille_texte_classe = $_POST['taille_texte_classe']; } }
	if (empty($_GET['type_texte_classe']) and empty($_POST['type_texte_classe'])) { $type_texte_classe = ''; }
	else { if (isset($_GET['type_texte_classe'])) { $type_texte_classe = $_GET['type_texte_classe']; } if (isset($_POST['type_texte_classe'])) { $type_texte_classe = $_POST['type_texte_classe']; } }
	if (empty($_GET['taille_texte_annee']) and empty($_POST['taille_texte_annee'])) { $taille_texte_annee = ''; }
	else { if (isset($_GET['taille_texte_annee'])) { $taille_texte_annee = $_GET['taille_texte_annee']; } if (isset($_POST['taille_texte_annee'])) { $taille_texte_annee = $_POST['taille_texte_annee']; } }
	if (empty($_GET['type_texte_annee']) and empty($_POST['type_texte_annee'])) { $type_texte_annee = ''; }
	else { if (isset($_GET['type_texte_annee'])) { $type_texte_annee = $_GET['type_texte_annee']; } if (isset($_POST['type_texte_annee'])) { $type_texte_annee = $_POST['type_texte_annee']; } }
	if (empty($_GET['taille_texte_periode']) and empty($_POST['taille_texte_periode'])) { $taille_texte_periode = ''; }
	else { if (isset($_GET['taille_texte_periode'])) { $taille_texte_periode = $_GET['taille_texte_periode']; } if (isset($_POST['taille_texte_periode'])) { $taille_texte_periode = $_POST['taille_texte_periode']; } }
	if (empty($_GET['type_texte_periode']) and empty($_POST['type_texte_periode'])) { $type_texte_periode = ''; }
	else { if (isset($_GET['type_texte_periode'])) { $type_texte_periode = $_GET['type_texte_periode']; } if (isset($_POST['type_texte_periode'])) { $type_texte_periode = $_POST['type_texte_periode']; } }
	if (empty($_GET['taille_texte_categorie_cote']) and empty($_POST['taille_texte_categorie_cote'])) { $taille_texte_categorie_cote = ''; }
	else { if (isset($_GET['taille_texte_categorie_cote'])) { $taille_texte_categorie_cote = $_GET['taille_texte_categorie_cote']; } if (isset($_POST['taille_texte_categorie_cote'])) { $taille_texte_categorie_cote = $_POST['taille_texte_categorie_cote']; } }
	if (empty($_GET['taille_texte_categorie']) and empty($_POST['taille_texte_categorie'])) { $taille_texte_categorie = ''; }
	else { if (isset($_GET['taille_texte_categorie'])) { $taille_texte_categorie = $_GET['taille_texte_categorie']; } if (isset($_POST['taille_texte_categorie'])) { $taille_texte_categorie = $_POST['taille_texte_categorie']; } }
	if (empty($_GET['type_texte_date_datation']) and empty($_POST['type_texte_date_datation'])) { $type_texte_date_datation = ''; }
	else { if (isset($_GET['type_texte_date_datation'])) { $type_texte_date_datation = $_GET['type_texte_date_datation']; } if (isset($_POST['type_texte_date_datation'])) { $type_texte_date_datation = $_POST['type_texte_date_datation']; } }
	if (empty($_GET['cadre_adresse']) and empty($_POST['cadre_adresse'])) { $cadre_adresse = ''; }
	else { if (isset($_GET['cadre_adresse'])) { $cadre_adresse = $_GET['cadre_adresse']; } if (isset($_POST['cadre_adresse'])) { $cadre_adresse = $_POST['cadre_adresse']; } }
	if (empty($_GET['centrage_logo']) and empty($_POST['centrage_logo'])) { $centrage_logo = ''; }
	else { if (isset($_GET['centrage_logo'])) { $centrage_logo = $_GET['centrage_logo']; } if (isset($_POST['centrage_logo'])) { $centrage_logo = $_POST['centrage_logo']; } }
	if (empty($_GET['Y_centre_logo']) and empty($_POST['Y_centre_logo'])) { $Y_centre_logo = ''; }
	else { if (isset($_GET['Y_centre_logo'])) { $Y_centre_logo = $_GET['Y_centre_logo']; } if (isset($_POST['Y_centre_logo'])) { $Y_centre_logo = $_POST['Y_centre_logo']; } }
	if (empty($_GET['ajout_cadre_blanc_photo']) and empty($_POST['ajout_cadre_blanc_photo'])) { $ajout_cadre_blanc_photo = ''; }
	else { if (isset($_GET['ajout_cadre_blanc_photo'])) { $ajout_cadre_blanc_photo = $_GET['ajout_cadre_blanc_photo']; } if (isset($_POST['ajout_cadre_blanc_photo'])) { $ajout_cadre_blanc_photo = $_POST['ajout_cadre_blanc_photo']; } }
	if (empty($_GET['affiche_moyenne_mini_general']) and empty($_POST['affiche_moyenne_mini_general'])) { $affiche_moyenne_mini_general = ''; }
	else { if (isset($_GET['affiche_moyenne_mini_general'])) { $affiche_moyenne_mini_general = $_GET['affiche_moyenne_mini_general']; } if (isset($_POST['affiche_moyenne_mini_general'])) { $affiche_moyenne_mini_general = $_POST['affiche_moyenne_mini_general']; } }
	if (empty($_GET['affiche_moyenne_maxi_general']) and empty($_POST['affiche_moyenne_maxi_general'])) { $affiche_moyenne_maxi_general = ''; }
	else { if (isset($_GET['affiche_moyenne_maxi_general'])) { $affiche_moyenne_maxi_general = $_GET['affiche_moyenne_maxi_general']; } if (isset($_POST['affiche_moyenne_maxi_general'])) { $affiche_moyenne_maxi_general = $_POST['affiche_moyenne_maxi_general']; } }
	if (empty($_GET['affiche_date_edition']) and empty($_POST['affiche_date_edition'])) { $affiche_date_edition = ''; }
	else { if (isset($_GET['affiche_date_edition'])) { $affiche_date_edition = $_GET['affiche_date_edition']; } if (isset($_POST['affiche_date_edition'])) { $affiche_date_edition = $_POST['affiche_date_edition']; } }
	if (empty($_GET['affiche_ine']) and empty($_POST['affiche_ine'])) { $affiche_ine = ''; }
	else { if (isset($_GET['affiche_ine'])) { $affiche_ine = $_GET['affiche_ine']; } if (isset($_POST['affiche_ine'])) { $affiche_ine = $_POST['affiche_ine']; } }

// fin Christian
//===================================================

//==============================
// Initialisation d'un tableau des champs de model_bulletin
include('bulletin_pdf.inc.php');
//==============================

//===================================================
// d�but de la validation ajouter/modifier/supprimer des mod�les
if(!empty($valide_modif_model))
{
	if($action_model==='ajouter') {
		$id_model_bulletin=get_max_id_model_bulletin();
		$id_model_bulletin++;
		for($i=0;$i<count($champ_bull_pdf);$i++) {
			$nom=$champ_bull_pdf[$i];
			if(isset($$nom)) {
				$valeur=$$nom;
				if($valeur=='') {
					if($type_champ_pdf["$nom"]!="texte") {
						$valeur=0;
					}
				}

				//$sql="INSERT INTO modele_bulletin SET id_model_bulletin='$id_model_bulletin', nom='$nom', valeur='".$$nom."';";
				$sql="INSERT INTO modele_bulletin SET id_model_bulletin='$id_model_bulletin', nom='$nom', valeur='".$valeur."';";
				//echo "$sql<br />\n";
				$insert=mysql_query($sql);
			}
			else {
				// Normalement, cela ne devrait pas arriver si on r�cup�re correctement les valeurs soumises du formulaire.
				// Il faudrait ins�rer une valeur par d�faut.
				// Prendre celle du mod�le Standard?
			}
		}
	}

	if($action_model==='modifier') {
		for($i=0;$i<count($champ_bull_pdf);$i++) {
			$nom=$champ_bull_pdf[$i];
			if(isset($$nom)) {
				$valeur=$$nom;
				if($valeur=='') {
					if($type_champ_pdf["$nom"]!="texte") {
						$valeur=0;
					}
				}

				$sql="SELECT 1=1 FROM modele_bulletin WHERE id_model_bulletin='$id_model_bulletin' AND nom='$nom';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0) {
					//$sql="INSERT INTO modele_bulletin SET id_model_bulletin='$id_model_bulletin', nom='$nom', valeur='".$$nom."';";
					$sql="INSERT INTO modele_bulletin SET id_model_bulletin='$id_model_bulletin', nom='$nom', valeur='".$valeur."';";
					//echo "$sql<br />\n";
					$insert=mysql_query($sql);
				}
				else {
					//$sql="UPDATE modele_bulletin SET valeur='".$$nom."' WHERE id_model_bulletin='$id_model_bulletin' AND nom='$nom';";
					$sql="UPDATE modele_bulletin SET valeur='".$valeur."' WHERE id_model_bulletin='$id_model_bulletin' AND nom='$nom';";
					//echo "$sql<br />\n";
					$update=mysql_query($sql);
				}
			}
			/*
			else {
				echo "Pas de valeur pour $nom<br />";
			}
			*/
		}
	}

	if($id_model_bulletin!='1') {
		if($action_model==='supprimer') {
			$requete_model="DELETE FROM ".$prefix_base."modele_bulletin WHERE id_model_bulletin ='".$id_model_bulletin."';";

			//AJOUT ERIC Si on supprime un mod�le, s'il est utilis� pour une classe on r�initialise pour la classe la valeur � NULL du champs modele_bulletin_pdf
			$requete_classe="UPDATE classes SET modele_bulletin_pdf=NULL WHERE (modele_bulletin_pdf='$id_model_bulletin')";
			//echo $requete_classe;
			mysql_query($requete_classe) or die('Erreur SQL !'.$requete_classe.'<br>'.mysql_error());

			mysql_query($requete_model) or die('Erreur SQL !'.$requete_model.'<br>'.mysql_error());
		}
	}
	//mysql_query($requete_model) or die('Erreur SQL !'.$requete_model.'<br>'.mysql_error());
}
// fin ajouter/modifier/supprimer des mod�les
//===================================================


//===================================================
// DEBUT import de mod�le de bulletin pdf par fichier csv
if ( isset($action) and $action === 'importmodelcsv' ) {

	if($_FILES['fichier']['type'] != "")
	{
			$fichiercsv = isset($_FILES["fichier"]) ? $_FILES["fichier"] : NULL;
		if (!isset($fichiercsv['tmp_name']) or ($fichiercsv['tmp_name'] === '')) {
			$msg = "Erreur de t�l�chargement niveau 1.";
		} else if (!file_exists($fichiercsv['tmp_name'])) {
				$msg = "Erreur de t�l�chargement niveau 2.";
		} else if ((!preg_match('/csv$/',$fichiercsv['name'])) and $fichiercsv['type'] === "application/x-csv"){
				$msg = "Erreur : seuls les fichiers ayant l'extension .jpg sont autoris�s.";
		} else {

			if(!isset($msg)) {$msg="";}

			$fp = fopen($fichiercsv['tmp_name'],"r");

			$ligne = fgets($fp,4096);      // je lis la ligne
			if($ligne!="") {
				// On remplit un tableau des champs
				//$tab_champs_csv[]=explode(";",$ligne);
				$tab_champs_csv=explode(";",$ligne);

				$indice=-1;
				// Recherche de l'indice du champ id_model_bulletin
				for($i=0;$i<count($tab_champs_csv);$i++) {
					//echo "\$tab_champs_csv[$i]=".$tab_champs_csv[$i];
					if($tab_champs_csv[$i]=='id_model_bulletin') {
						$indice=$i;
						//echo " TROUV�: \$indice=$indice";
						break;
					}
					//echo "<br />";
				}

				$indice_nom_modele=-1;
				// Recherche de l'indice du champ nom du mod�le
				for($i=0;$i<count($tab_champs_csv);$i++) {
					//echo "\$tab_champs_csv[$i]=".$tab_champs_csv[$i];
					if($tab_champs_csv[$i]=='nom_model_bulletin') {
						$indice_nom_modele=$i;
						//echo " TROUV�: \$indice_nom_modele=$indice_nom_modele";
						break;
					}
					//echo "<br />";
				}

				if($indice!=-1) {
					// On importe ligne par ligne dans un tableau
					while (!feof($fp)) //Jusqu'a la fin du fichier
					{

						$ligne = fgets($fp,4096);      // je lis la ligne
						unset($tab_valeurs_csv);
						//$tab_valeurs_csv[]=explode(";",$ligne);
						$tab_valeurs_csv=explode(";",$ligne);

						// Si $tab_valeurs_csv[$indice] est vide, il faut tester le nom du mod�le pour retrouver l'id_model_bulletin ou en affecter un nouveau
						// Normalement, si on repart d'un export fait avec la version modifi�e de export_modele_pdf.php, les id_model_bulletin sont bien export�s aussi.

						if($tab_valeurs_csv[$indice]=="") {
							$sql="SELECT DISTINCT id_model_bulletin FROM modele_bulletin WHERE nom='nom_model_bulletin' AND valeur='".$tab_valeurs_csv[$indice_nom_modele]."';";
							//echo "$sql<br />";
							$res_nom_model=mysql_query($sql);

							if(mysql_num_rows($res_nom_model)>0) {
								$tmp_lig_nom_model=mysql_fetch_object($res_nom_model);
								$tab_valeurs_csv[$indice]=$tmp_lig_nom_model->id_model_bulletin;
							}
							else {
								$sql="SELECT MAX(id_model_bulletin) AS max_id_model_bulletin FROM modele_bulletin;";
								//echo "$sql<br />";
								$res_max=mysql_query($sql);

								if(mysql_num_rows($res_max)>0) {
									$tmp_lig_max=mysql_fetch_object($res_max);

									$tab_valeurs_csv[$indice]=$tmp_lig_max->max_id_model_bulletin+1;
								}
								else {
									// Ca ne devrait pas arriver � moins d'avoir supprim� tous les mod�les.
									$tab_valeurs_csv[$indice]=1;
								}
							}
						}

						if($tab_valeurs_csv[$indice_nom_modele]!="") {
							$sql="DELETE FROM modele_bulletin WHERE id_model_bulletin='".$tab_valeurs_csv[$indice]."';";
							//echo "$sql<br />";
							$nettoyage=mysql_query($sql);

							for($i=0;$i<count($tab_champs_csv);$i++) {
								if($i!=$indice) {
									$sql="INSERT modele_bulletin SET id_model_bulletin='".$tab_valeurs_csv[$indice]."', nom='".$tab_champs_csv[$i]."', valeur='".$tab_valeurs_csv[$i]."';";
									//echo "$sql<br />";
									$insert=mysql_query($sql);
								}
							}
						}
					}

					/*
					// Parcourir $champ_bull_pdf pour ne pas oublier de champ
					for($i=0;$i<count($tab_champs_csv);$i++) {

					}
					*/
				}
				else {
					$msg="Erreur: Le champ 'id_model_bulletin' n'a pas �t� trouv� dans le fichier CSV.";
				}
			}
			fclose($fp);
		}
	}
}
// FIN import de mod�le de bulletin pdf par fichier csv
//===================================================


if (($reg_ok == 'yes') and (isset($_POST['ok']))) {
	$msg = "Enregistrement r�ussi !";
}

//**************** EN-TETE *********************
$titre_page = "Param�tres de configuration des bulletins scolaires PDF";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// Pour afficher les variables transmises en GET/POST/SERVER/SESSION
//debug_var();

?>
<script type="text/javascript">
<!--
function CocheCheckbox() {

	nbParams = CocheCheckbox.arguments.length;

	for (var i=0;i<nbParams-1;i++) {

		theElement = CocheCheckbox.arguments[i];
		formulaire = CocheCheckbox.arguments[nbParams-1];

		if (document.forms[formulaire].elements[theElement])
			document.forms[formulaire].elements[theElement].checked = true;
	}
}

function DecocheCheckbox() {

	nbParams = DecocheCheckbox.arguments.length;

	for (var i=0;i<nbParams-1;i++) {

		theElement = DecocheCheckbox.arguments[i];
		formulaire = DecocheCheckbox.arguments[nbParams-1];

		if (document.forms[formulaire].elements[theElement])
			document.forms[formulaire].elements[theElement].checked = false;
	}
}
//-->
</script>

<?php


	if ((($_SESSION['statut']=='professeur') AND ((getSettingValue("GepiProfImprBul")!='yes') OR ((getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")!='yes')))) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")!='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")!='yes')))
	{
		die("Droits insuffisants pour effectuer cette op�ration");
	}

	echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour � l'accueil</a>\n";
	//echo " | <a href=\"./index.php?format=pdf\"> Impression des bulletins PDF</a>\n";
	echo " | <a href=\"./bull_index.php\"> Impression des bulletins</a>\n";
	echo " | <a href=\"./param_bull.php\"> Param�tres d'impression des bulletins HTML</a>\n";
	//echo "</p>\n";
	//echo "<br /><br />\n";

	//=========================================================
	// $action_model peut valoir: ajouter/modifier/supprimer
	if((empty($action_model) or !empty($valide_modif_model))) //affiche la liste des mod�les
	{

		echo "</p>\n";
		echo "<br /><br />\n";

		echo "<center>
		<form name ='form3' method='post' action='export_modele_pdf.php'>
		<table style='text-align: left; width: 400px; border: 1px solid #74748F;' border='0' cellpadding='1' cellspacing='1' summary='Tableau des mod�les existants'>
		<tbody>
		<tr>
			<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 100%;' colspan='4' rowspan='1'><a href='".$_SERVER['PHP_SELF']."?modele=aff&amp;action_model=ajouter'>Ajouter un nouveau mod�le</a></td>
		</tr>
		<tr>
			<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 12px; background: #333333; font: normal 10pt Arial; color: #E0EDF1;'></td>
			<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 50%; background: #333333; font: normal 10pt Arial; color: #E0EDF1;'>Mod�le</td>
			<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: #333333; font: normal 10pt Arial; color: #E0EDF1;'>Modifier</td>
			<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: #333333; font: normal 10pt Arial; color: #E0EDF1;'>Supprimer</td>
		</tr>\n";

		$i = '1';
		$nb_modele = '0'; $varcoche = '';

		//$requete_model = mysql_query('SELECT id_model_bulletin, nom_model_bulletin FROM '.$prefix_base.'model_bulletin');
		$requete_model = mysql_query("SELECT id_model_bulletin, valeur FROM ".$prefix_base."modele_bulletin WHERE nom='nom_model_bulletin' ORDER BY id_model_bulletin;");
        if(mysql_num_rows($requete_model)==0) {
            $message_alerte="<p style='text-align:center; color:red;'>Il semble qu'aucun mod�le ne soit d�fini.<br />Ce n'est pas normal.<br />";
            if($_SESSION['login']=='administrateur') {
                $message_alerte.="Vous devriez effectuer/forcer une <a href='../utilitaires/maj.php'>mise � jour de la base</a> pour corriger.<br />Prenez tout de m�me soin de v�rifier que personne d'autre que vous n'est connect�.\n";
            }
            else {
                $message_alerte.="Contactez l'administrateur pour qu'il effecute une mise � jour de la base.\n";
            }
            $message_alerte.="</p>\n";
        }
        else {
            while($data_model = mysql_fetch_array($requete_model)) {
                if ($i === '1') { $i = '2'; $couleur_cellule = '#CCCCCC'; } else { $couleur_cellule = '#DEDEDE'; $i = '1'; }

                echo "<tr>\n";

                echo "<td style='vertical-align: top; white-space: nowrap; text-align: left; width: 12px; background: $couleur_cellule;'>\n";
                //echo "<input name='selection[$nb_modele]' id='sel$nb_modele' value='1' type='checkbox' />\n";
                echo "<input name='selection[$nb_modele]' id='sel$nb_modele' value='".$data_model['id_model_bulletin']."' type='checkbox' />\n";
                echo "<input name='id_model_bulletin[$nb_modele]' value='".$data_model['id_model_bulletin']."' type='hidden' />\n";

                $varcoche = $varcoche."'sel".$nb_modele."',";

                echo "</td>\n";

                echo "<td style='vertical-align: top; white-space: nowrap; text-align: left; width: 50%; background: $couleur_cellule'>\n";
                //echo ucfirst($data_model['nom_model_bulletin']);
                echo ucfirst($data_model['valeur']);
                echo "</td>\n";

                echo "<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: $couleur_cellule'>\n";
                echo "[<a href='".$_SERVER['PHP_SELF']."?modele=aff&amp;action_model=modifier&amp;modele_action=".$data_model['id_model_bulletin']."'>\n";
                echo "Modifier\n";
                echo "</a>]\n";
                echo "</td>\n";

                echo "<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: $couleur_cellule;'>\n";
                if($data_model['id_model_bulletin']!='1') {
                    echo "[<a href='".$_SERVER['PHP_SELF']."?modele=aff&amp;action_model=supprimer&amp;modele_action=".$data_model['id_model_bulletin']."'>Supprimer</a>]";
                }
                else {
                    echo "&nbsp;";
                }
                echo "</td>\n";
                echo "</tr>\n";

                $nb_modele = $nb_modele + 1;
            }
        }
		$varcoche = $varcoche."'form3'";

		echo "<tr>\n";
		echo "<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 100%;' colspan='4' rowspan='1'><a href='".$_SERVER['PHP_SELF']."?modele=aff&amp;action_model=ajouter'>Ajouter un nouveau mod�le</a><br /></td>\n";
		echo "</tr>\n";
		echo "</tbody>\n";
		echo "</table>\n";

		echo "<a href=\"javascript:CocheCheckbox($varcoche)\">Cocher</a> | <a href=\"javascript:DecocheCheckbox($varcoche)\">D�cocher</a>\n";
		echo "<input type='submit' value='Exporter' style='border: 0px; color: #0000AA; text-decoration: none;' />\n";
		echo "<span style='background : #FFFFF1; padding-left: 2px;'><a href='".$_SERVER['PHP_SELF']."?action=import' class='submit'>Importer</a></span>\n";
		echo "</form>\n";

		if ( $action === 'import' ) {
			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='importfichier'>\n";
			echo "<input name='fichier' type='file' />\n";
			echo "<input type='hidden' name='MAX_FILE_SIZE' value='150000' />\n";
			echo "<input type='hidden' name='action' value='importmodelcsv' />\n";
			echo "<input type='submit' value='Importer' />\n";
			echo "</form>\n";
		}

		echo "</center>\n";

        if(isset($message_alerte)) {
            echo $message_alerte;
        }

		echo "<br />\n";
		echo "<br />\n";
		echo "<hr />\n";


		//ERIC
		$nb_ligne = 1;
		$bgcolor = "#DEDEDE";
		echo "<form name=\"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" style=\"width: 100%\">\n";
		echo "<h3>Options gestion des mod�les d'impression PDF</h3>\n";
		echo "<table cellpadding=\"8\" cellspacing=\"0\" width=\"100%\" border=\"0\" summary=\"Tableau des options d'impression par classe\">\n";

		echo "<tr ";  if ($nb_ligne % 2) echo "bgcolor=".$bgcolor; echo " >\n"; $nb_ligne++;
		echo "<td style=\"font-variant: small-caps;\" width=\"80%\" >\n";
		echo "Interdire la s�lection du mod�le de bulletin lors de l'impression. Le mod�le doit �tre d�fini dans les param�tres de chaque classe. <i>(En cas d'absence de mod�le, le mod�le standard est utilis�.)</i><br />\n";
		echo "</td>\n";
		echo "<td style=\"text-align: center;\">\n";
			echo "<input type=\"radio\" name=\"option_modele_bulletin\" value=\"1\" ";
			if (getSettingValue("option_modele_bulletin") == '1') {echo " checked=\"checked\"";}
			echo " />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr ";  if ($nb_ligne % 2) echo "bgcolor=".$bgcolor; echo " >\n"; $nb_ligne++;
		echo "<td style=\"font-variant: small-caps;\" width=\"80%\" >\n";
		echo "Le mod�le utilis� par d�faut est celui d�fini dans les param�tres de la classe. Un autre mod�le pourra �tre choisi lors de l'impression des bulletins. Il s'appliquera � toutes les classes s�lectionn�es.<br />\n";
		echo "</td>\n";
		echo "<td style=\"text-align: center;\">\n";
			echo "<input type=\"radio\" name=\"option_modele_bulletin\" value=\"2\" ";
			if (getSettingValue("option_modele_bulletin") == '2') {echo " checked=\"checked\"";}
			echo " />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr ";  if ($nb_ligne % 2) echo "bgcolor=".$bgcolor; echo " >\n"; $nb_ligne++;
		echo "<td style=\"font-variant: small-caps;\" width=\"80%\" >\n";
		echo "Le mod�le devra �tre choisi au moment de l'impression ind�pendamment du mod�le param�tr� dans les param�tres de la classe. Il s'appliquera � toutes les classes s�lectionn�es.<br />\n";
		echo "</td>\n";
		echo "<td style=\"text-align: center;\">\n";
			echo "<input type=\"radio\" name=\"option_modele_bulletin\" value=\"3\" ";
			if (getSettingValue("option_modele_bulletin") == '3') {echo " checked=\"checked\"";}
			echo " />\n";
		echo "</td>\n";
		echo "</tr>\n";
		// Possibilit� d'ajouter la fonction utf8-decode() dans certains cas sur les bulletins pdf.
		// Ce r�glage est ensuite directement r�cup�r� dans fpdf.php et ex_fpdf.php
		if (getSettingValue("decode_pdf_utf8") == "y") {
			$selected = ' checked="checked"';
		}
		else{
			$selected = '';
		}
		echo '
		<tr><td style="font-variant: small-caps; color: brown;">
		<label for="decodeUtf8">Sur certains serveurs web, il y a un probl�me d\'encodage dans la g�n�ration des pdf, ce coche devrait r�soudre le probl�me :</label>
		</td><td style="text-align: center;">
		<input type="checkbox" id="decodeUtf8" name="decode" value="y"'.$selected.' />
		</td></tr>
		';
		echo "</table>\n<hr />\n";

		/*
		// Comment� parce que j'ai un soucis sur le bulletin PDF pour tout positionner.
		echo "<p>";
		echo "<input name='bull_pdf_INE_eleve' style='border: 1px solid #74748F;' type='checkbox' value='y'";
		if(getSettingValue('bull_pdf_INE_eleve')=='y') {
			echo " checked='checked'";
		}
		echo " />&nbsp;Afficher le num�ro INE de l'�l�ve sur le bulletin PDF.";
		echo "</p>\n";

		echo "<hr />\n";
		*/

		echo "<center><input type=\"submit\" name=\"ok\" value=\"Enregistrer\" style=\"font-variant: small-caps;\"/></center>";
		echo "</form>";

	}
	//=========================================================



	unset($nom_model_bulletin_ecrased);

	if($modele==='aff' and ($action_model==='ajouter' or $action_model==='modifier' or $action_model==='supprimer') and empty($valide_modif_model)) //affiche la liste des mod�les
	{
		// $modele_action contient l'id_model_bulletin du mod�le � modifier/supprimer
		if(empty($modele_action)) {
			// On est dans le cas d'un ajout
			$model_bulletin='';
		}
		else {
			$model_bulletin=$modele_action;
		}

		if($action_model==='ajouter' or $action_model==='modifier') {

			// Recherche de l'id_model_bulletin pour lequel on va rechercher les valeurs dans la table MySQL 'modele_bulletin'

			if($action_model==='ajouter') {
				$id_model_courant='';
				if($copie_model==='') {
					// On prend le mod�le standard comme mod�le pour le nouveau mod�le ajout�
					$id_model_bulletin=1;
				}
				else {
					// On prend le mod�le id_model_bulletin=$type_bulletin comme mod�le pour le nouveau mod�le ajout�
					// $type_bulletin est transmis par le formulaire "Copier les param�tres du mod�le"
					$id_model_bulletin=$type_bulletin;

					// On r�cup�re le nom du mod�le qui avait peut-�tre d�j� �t� saisi
					$nom_model_bulletin=isset($_POST['nom_model_bulletin']) ? $_POST['nom_model_bulletin'] : "Nouveau";
					// En effet $nom_model_bulletin va �tre par la suite �cras� par la r�cup�ration des valeurs enregistr�es pour $id_model_bulletin

					// On met de c�t� le nom du nouveau mod�le:
					$nom_model_bulletin_ecrased=$nom_model_bulletin;
				}
			}

			if($action_model==='modifier') {
				// On conserve le $model_bulletin=$model_action � modifier
				$id_model_courant=$model_bulletin;

				if($copie_model==='') {
					$id_model_bulletin=$model_bulletin;
				}
				else {
					$id_model_bulletin=$type_bulletin;

					$nom_model_bulletin_ecrased=$_POST['nom_model_bulletin'];
				}
			}

			// On r�cup�re les valeurs du mod�le $id_model_bulletin (que ce soit le mod�le actuellement modifi� ou celui qui sert de mod�le pour une recopie)
			$sql="SELECT * FROM modele_bulletin WHERE id_model_bulletin='".$id_model_bulletin."';";
			//echo "$sql<br />\n";
			$res_modele=mysql_query($sql);
			while ($lig=mysql_fetch_object($res_modele)) {
				$nom=$lig->nom;
				$valeur=$lig->valeur;

				$$nom=$valeur;
				// La valeur de $nom_model_bulletin est �cras�e ici par $valeur quand $nom='nom_model_bulletin'
			}


			if($action_model==='ajouter') {
				if($copie_model==='') {
					$nom_model_bulletin="Nouveau";
				}
				else {
					// On restaure le nom de mod�le �cras� lors de la recopie
					$nom_model_bulletin=$nom_model_bulletin_ecrased;
				}
			}


			if ( $action_model==='modifier' and $copie_model != '' ) {
				/*
				// id du mod�le
				$id_model_bulletin = $modele_action;
				//echo "\$id_model_bulletin=$id_model_bulletin<br />";
				// nom du mod�le
				$nom_model_bulletin = $nom_model_bulletin;
				//echo "\$nom_model_bulletin=$nom_model_bulletin<br />";
				*/

				$nom_model_bulletin=$nom_model_bulletin_ecrased;
			}


			//echo " | <a href=\"./".$_SERVER['PHP_SELF']."\"> Param�tres d'impression des bulletins PDF</a>";
			echo " | <a href=\"".$_SERVER['PHP_SELF']."\"> Param�tres d'impression des bulletins PDF</a>";

			echo "</p>\n";
			echo "<br /><br />\n";

			//============================================
			echo "<form method='post' action='".$_SERVER['PHP_SELF']."?modele=aff' name='action_modele_copie_form'>\n";
			echo "<p>Mod�le: <select tabindex='5' name='type_bulletin'>\n";

			// s�lection des mod�les des bulletins.
			//$requete_model = mysql_query('SELECT id_model_bulletin, nom_model_bulletin FROM '.$prefix_base.'model_bulletin ORDER BY '.$prefix_base.'model_bulletin.nom_model_bulletin ASC');
			$sql="SELECT id_model_bulletin, valeur FROM modele_bulletin WHERE nom='nom_model_bulletin' ORDER BY nom ASC";
			$requete_model = mysql_query($sql);
			while($donner_model = mysql_fetch_array($requete_model))
			{
				echo "<option value='".$donner_model['id_model_bulletin']."'";
				if(!empty($type_bulletin) and $type_bulletin===$donner_model['id_model_bulletin']) {
					echo " selected='selected'";
				}
				echo ">";
				//echo ucfirst($donner_model['nom_model_bulletin']);
				echo ucfirst($donner_model['valeur']);
				echo "</option>\n";
			}

			echo "</select>&nbsp;\n";

			if ( $action_model === 'modifier' ) {
				echo "<input type='hidden' name='modele_action' value='$modele_action' />\n";
				echo "<input type='hidden' name='nom_model_bulletin' value='$nom_model_bulletin' />\n";
			}

			echo "<input type='hidden' name='action_model' value='$action_model' />\n";
			echo "<input type='hidden' name='modele' value='$modele' />\n";
			echo "<input type='hidden' name='format' value='$format' />\n";
			echo "<input type='submit' id='copie_model' name='copie_model' value='Copier les param�tres de ce mod�le' onClick=\"return confirm('Attention cette action va �craser votre s�lection actuelle')\" />\n";
			echo "</form>\n";
			//============================================

			echo "<form method='post' action='".$_SERVER['PHP_SELF']."?modele=aff' name='action_modele_form'>\n";

			if(!isset($nom_model_bulletin)) {
				$nom_model_bulletin = 'Nouveau';
			}

			//echo "\$nom_model_bulletin=$nom_model_bulletin<br />";
			/*
			if(isset($nom_model_bulletin_ecrased)) {

				echo "<h2>Mise en page du mod�le de bulletin ($nom_model_bulletin_ecrased)</h2>";

				if($id_model_bulletin!='1') {
					//echo "$nom_model_bulletin -";
					echo "Nom du mod�le :&nbsp;";
					echo "<input name='nom_model_bulletin' size='22' style='border: 1px solid #74748F;' type='text' ";
					if(!empty($nom_model_bulletin_ecrased)) {
						echo "value=\"$nom_model_bulletin_ecrased\"";
					}
					echo " />";
				}
				else {
					echo "Nom du mod�le: ".ucfirst($nom_model_bulletin_ecrased);
				}
			}
			else {
			*/
				echo "<h2>Mise en page du mod�le de bulletin ($nom_model_bulletin)</h2>";

				//if($id_model_bulletin!='1') {
				//if(($id_model_bulletin!='1')||($nom_model_bulletin=='Nouveau')) {
				if(($id_model_courant!='1')||($nom_model_bulletin=='Nouveau')) {
					//echo "$nom_model_bulletin -";
					echo "Nom du mod�le :&nbsp;";
					echo "<input name='nom_model_bulletin' size='22' style='border: 1px solid #74748F;' type='text' ";
					if(!empty($nom_model_bulletin)) {
						echo "value=\"$nom_model_bulletin\"";
					}
					echo " />";
				}
				else {
					// On devrait avoir ici: Mod�le Standard avec id_model_bulletin=1
					echo "Nom du mod�le: ".ucfirst($nom_model_bulletin);
					//echo "<input type='hidden' name='id_model_bulletin' value='$id_model_bulletin' />\n";
					echo "<input name='nom_model_bulletin' type='hidden' value=\"$nom_model_bulletin\" />\n";
				}

				echo "<input type='hidden' name='id_model_bulletin' value='$id_model_courant' />\n";
			//}

			echo "<br />\n";

			/*
			if($id_model_bulletin==='1') {
				echo "<input type='hidden' name='id_model_bulletin' value='$id_model_bulletin' />\n";
				echo "<input name='nom_model_bulletin' type='hidden' value=\"$nom_model_bulletin\" />\n";
			}
			*/

			?>
			Nom de la police de caract�res&nbsp;<input name="caractere_utilse" size="10" style="border: 1px solid #74748F;" type="text" <?php if(!empty($caractere_utilse)) { ?>value="<?php echo $caractere_utilse; ?>" <?php } ?> />&nbsp;<span style="font-weight: bold; color: rgb(255, 0, 0);">*</span><br /><span style="font-style: italic; color: rgb(255, 0, 0);">* (Attention � ne modifier que si la police existe sur le serveur web voir avec l'administrateur de GEPI)</span><br />
			<table style="text-align: left; width: 100%; border: 1px solid #74748F;" border="0" cellpadding="2" cellspacing="2"  summary="Tableau des param�tres du mod�le">
			<tbody>
			<tr>
				<td style="vertical-align: center; white-space: nowrap; text-align: center; width: 100%; background: #B3B7BF;" colspan="2" rowspan="1">
				<input type="submit" id="valide_modif_model" name="valide_modif_model" value="Valider le mod�le" />
				</td>
			</tr>
			<tr>
			<td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">
				<div style="font-weight: bold; background: #CFCFCF;">Cadre information �tablissement</div>

			<?php
				// AJOUT: boireaus 20081224
				// Afficher le nom de l'�tablissement
				echo "<input name='affiche_nom_etab' id='affiche_nom_etab' style='border: 1px solid #74748F;' type='checkbox' value='1' ";
				//if(!empty($affiche_nom_etab) and $affiche_nom_etab==='1') {
				if((!isset($affiche_nom_etab))||($affiche_nom_etab!='0')) {
					echo "checked='checked'";
				}
				echo "/>&nbsp;<label for='affiche_nom_etab' style='cursor: pointer;'>Afficher le nom de l'�tablissement</label><br />\n";

				// Afficher l'adresse de l'�tablissement
				echo "<input name='affiche_adresse_etab' id='affiche_adresse_etab' style='border: 1px solid #74748F;' type='checkbox' value='1' ";
				//if(!empty($affiche_adresse_etab) and $affiche_adresse_etab==='1') {
				if((!isset($affiche_adresse_etab))||($affiche_adresse_etab!='0')) {
					echo "checked='checked'";
				}
				echo "/>&nbsp;<label for='affiche_adresse_etab' style='cursor: pointer;'>Afficher l'adresse de l'�tablissement</label><br />\n";

			?>

			<input name="nom_etab_gras" id="nom_etab_gras" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($nom_etab_gras) and $nom_etab_gras==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="nom_etab_gras" style="cursor: pointer;">Nom de l'�tablissement en gras</label><br />

			<input name="affiche_filigrame" id="filigrame" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_filigrame) and $affiche_filigrame==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="filigrame" style="cursor: pointer;">Filigrane</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;<label for="text_fili" style="cursor: pointer;">texte du filigrane</label>&nbsp;<input name="texte_filigrame" id="text_fili" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($texte_filigrame)) { ?>value="<?php echo $texte_filigrame; ?>" <?php } ?> /><br />

			<input name="entente_tel" id="telephone" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($entente_tel) and $entente_tel==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="telephone" style="cursor: pointer;">T�l�phone</label><br />
			&nbsp;&nbsp;&nbsp;Texte&nbsp;<input name="tel_texte" size="4" style="border: 1px solid #74748F;" type="text" <?php if(!empty($tel_texte)) { ?>value="<?php echo $tel_texte; ?>" <?php } ?> /> ou
			<input name="tel_image" id="tel_image_1" value="tel1" type="radio" <?php if(!empty($tel_image) and $tel_image==='tel1') { ?>checked="checked"<?php } ?> /><label for="tel_image_1" style="cursor: pointer;"><img src="../images/imabulle/tel1.jpg" style="width: 6.5px; height: 15.5px; border: 0px" alt="" title="" /></label>
			<input name="tel_image" id="tel_image_2" value="tel2" type="radio" <?php if(!empty($tel_image) and $tel_image==='tel2') { ?>checked="checked"<?php } ?> /><label for="tel_image_2" style="cursor: pointer;"><img src="../images/imabulle/tel2.jpg" style="width: 18.5px; height: 15px; border: 0px" alt="" title="" /></label>
			<input name="tel_image" id="tel_image_3" value="tel3" type="radio" <?php if(!empty($tel_image) and $tel_image==='tel3') { ?>checked="checked"<?php } ?> /><label for="tel_image_3" style="cursor: pointer;"><img src="../images/imabulle/tel3.jpg" style="width: 18px; height: 15px; border: 0px" alt="" title="" /></label>
			<input name="tel_image" id="tel_image_4" value="tel4" type="radio" <?php if(!empty($tel_image) and $tel_image==='tel4') { ?>checked="checked"<?php } ?> /><label for="tel_image_4" style="cursor: pointer;"><img src="../images/imabulle/tel4.jpg" style="width: 16px; height: 16px; border: 0px" alt="" title="" /></label>
			<input name="tel_image" id="tel_image_5" value="" type="radio" <?php if(empty($tel_image) and $tel_image==='') { ?>checked="checked"<?php } ?> /><label for="tel_image_5" style="cursor: pointer;">aucune</label>
			<br />
			<input name="entente_fax" id="fax" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($entente_fax) and $entente_fax==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="fax" style="cursor: pointer;">Fax</label><br />
			&nbsp;&nbsp;&nbsp;Texte&nbsp;<input name="fax_texte" size="4" style="border: 1px solid #74748F;" type="text" <?php if(!empty($fax_texte)) { ?>value="<?php echo $fax_texte; ?>" <?php } ?> /> ou
			<input name="fax_image" id="fax_image_1" value="fax" type="radio" <?php if(!empty($fax_image) and $fax_image==='fax') { ?>checked="checked"<?php } ?> /><label for="fax_image_1" style="cursor: pointer;"><img src="../images/imabulle/fax.jpg" style="width: 20px; height: 20px; border: 0px" alt="" title="" /></label>
			<input name="fax_image" id="fax_image_2" value="" type="radio" <?php if(empty($fax_image) and $fax_image==='') { ?>checked="checked"<?php } ?> /><label for="fax_image_2" style="cursor: pointer;">aucune</label>
			<br />
			<input name="entente_mel" id="courrier" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($entente_mel) and $entente_mel==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="courrier" style="cursor: pointer;">Courriel</label><br />
			&nbsp;&nbsp;&nbsp;Texte&nbsp;<input name="courrier_texte" size="4" style="border: 1px solid #74748F;" type="text" <?php if(!empty($courrier_texte)) { ?>value="<?php echo $courrier_texte; ?>" <?php } ?> /> ou
			<input name="courrier_image" id="courrier_image_1" value="courrier" type="radio" <?php if(!empty($courrier_image) and $courrier_image==='courrier') { ?>checked="checked"<?php } ?> /><label for="courrier_image_1" style="cursor: pointer;"><img src="../images/imabulle/courrier.jpg" style="width: 20px; height: 20px; border: 0px" alt="" title="" /></label>
			<input name="courrier_image" id="courrier_image_2" value="courrier2" type="radio" <?php if(!empty($courrier_image) and $courrier_image==='courrier2') { ?>checked="checked"<?php } ?> /><label for="courrier_image_2" style="cursor: pointer;"><img src="../images/imabulle/courrier2.jpg" style="width: 20px; height: 20px; border: 0px" alt="" title="" /></label>
			<input name="courrier_image" id="courrier_image_3" value="sourismel" type="radio" <?php if(!empty($courrier_image) and $courrier_image==='sourismel') { ?>checked="checked"<?php } ?> /><label for="courrier_image_3" style="cursor: pointer;"><img src="../images/imabulle/sourismel.jpg" style="width: 28px; height: 20px; border: 0px" alt="" title="" /></label>
			<input name="courrier_image" id="courrier_image_4" value="" type="radio" <?php if(empty($courrier_image) and $courrier_image==='') { ?>checked="checked"<?php } ?> /><label for="courrier_image_4" style="cursor: pointer;">aucune</label>
			<br /><br />
			Logo de l'�tablissement<br />
			<input name="affiche_logo_etab" id="aff_logo" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_logo_etab) and $affiche_logo_etab==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="aff_logo" style="cursor: pointer;">Affiche le logo</label><br />
			<label for="larg_logo" style="cursor: pointer;">Largeur</label>&nbsp;<input name="L_max_logo" id="larg_logo" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($L_max_logo)) { ?>value="<?php echo $L_max_logo; ?>" <?php } ?> />mm&nbsp;/&nbsp;<label for="haut_logo" style="cursor: pointer;">Hauteur</label>&nbsp;<input name="H_max_logo" id="haut_logo" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($H_max_logo)) { ?>value="<?php echo $H_max_logo; ?>" <?php } ?> />mm&nbsp;<br />
			<input name="centrage_logo" id="centrage_logo" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($centrage_logo) and $centrage_logo==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="centrage_logo" style="cursor: pointer;">Centrer le logo</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for="Y_centre_logo" style="cursor: pointer;">Positionnement du centrage (Y)</label>&nbsp;<input name="Y_centre_logo" id="Y_centre_logo" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_centre_logo)) { ?>value="<?php echo $Y_centre_logo; ?>" <?php } ?> />mm<br /><br />

				<div style="font-weight: bold; background: #CFCFCF;">Cadre information identit� �l�ve</div>
			<input name="active_bloc_eleve" value="1" type="radio" <?php if(!empty($active_bloc_eleve) and $active_bloc_eleve==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activer &nbsp;<input name="active_bloc_eleve" value="0" type="radio" <?php if(empty($active_bloc_eleve) or (!empty($active_bloc_eleve) and $active_bloc_eleve!='1')) { ?>checked="checked"<?php } ?> />&nbsp;D�sactiver<br />
			Positionnement X&nbsp;<input name="X_eleve" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_eleve)) { ?>value="<?php echo $X_eleve; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_eleve" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_eleve)) { ?>value="<?php echo $Y_eleve; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="largeur_bloc_eleve" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_bloc_eleve)) { ?>value="<?php echo $largeur_bloc_eleve; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_bloc_eleve" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_bloc_eleve)) { ?>value="<?php echo $hauteur_bloc_eleve; ?>" <?php } ?> />mm&nbsp;<br />
			<input name="cadre_eleve" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_eleve) and $cadre_eleve==='1') { ?>checked="checked"<?php } ?> />&nbsp;Ajouter un encadrement<br />
			<input name="active_photo" id="active_photo" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_photo) and $active_photo==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="active_photo" style="cursor: pointer;">la photo</label> (<input name="ajout_cadre_blanc_photo" id="ajout_cadre_blanc_photo" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($ajout_cadre_blanc_photo) and $ajout_cadre_blanc_photo==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="ajout_cadre_blanc_photo" style="cursor: pointer;">Ajouter un cadre blanc</label> )<br />
			<input name="affiche_doublement" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_doublement) and $affiche_doublement==='1') { ?>checked="checked"<?php } ?> />&nbsp;si doublement<br />
			<input name="affiche_date_naissance" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_date_naissance) and $affiche_date_naissance==='1') { ?>checked="checked"<?php } ?> />&nbsp;la date de naissance<br />
			<input name="affiche_dp" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_dp) and $affiche_dp==='1') { ?>checked="checked"<?php } ?> />&nbsp;le r�gime<br />

			<?php
				/*
				// Il ne faut pas mettre l� une variable destin�e � arriver dans 'setting' pour toutes les classes et tous les mod�les PDF.
				echo "<input name='bull_pdf_INE_eleve' style='border: 1px solid #74748F;' type='checkbox' value='1'";
				if($bull_pdf_INE_eleve=='y') {
					echo " checked='checked'";
				}
				echo " />&nbsp;le r�gime<br />\n"
				*/
			?>

			<input name="affiche_nom_court" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_nom_court) and $affiche_nom_court==='1') { ?>checked="checked"<?php } ?> />&nbsp;nom court de la classe<br />
			<input name="affiche_ine" id="affiche_ine" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_ine) and $affiche_ine==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="affiche_ine" style="cursor: pointer;">num�ro INE de l'�l�ve</label><br />
			<input name="affiche_effectif_classe" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_effectif_classe) and $affiche_effectif_classe==='1') { ?>checked="checked"<?php } ?> />&nbsp;effectif de la classe<br />
			<input name="affiche_numero_impression" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_numero_impression) and $affiche_numero_impression==='1') { ?>checked="checked"<?php } ?> />&nbsp;num�ro d'impression<br />
			<input name="affiche_etab_origine" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_etab_origine) and $affiche_etab_origine==='1') { ?>checked="checked"<?php } ?> />&nbsp;�tablissement d'origine<br /><br />

			</td>
				<td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">
				<div style="font-weight: bold; background: #CFCFCF;">Cadre datation du bulletin</div>
			<input name="active_bloc_datation" value="1" type="radio" <?php if(!empty($active_bloc_datation) and $active_bloc_datation==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activer &nbsp;<input name="active_bloc_datation" value="0" type="radio" <?php if(empty($active_bloc_datation) or (!empty($active_bloc_datation) and $active_bloc_datation!='1')) { ?>checked="checked"<?php } ?> />&nbsp;D�sactiver<br />
			Positionnement X&nbsp;<input name="X_datation_bul" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_datation_bul)) { ?>value="<?php echo $X_datation_bul; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_datation_bul" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_datation_bul)) { ?>value="<?php echo $Y_datation_bul; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="largeur_bloc_datation" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_bloc_datation)) { ?>value="<?php echo $largeur_bloc_datation; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_bloc_datation" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_bloc_datation)) { ?>value="<?php echo $hauteur_bloc_datation; ?>" <?php } ?> />mm&nbsp;<br />
			<input name="cadre_datation_bul" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_datation_bul) and $cadre_datation_bul==='1') { ?>checked="checked"<?php } ?> />&nbsp;Ajouter un encadrement<br /><br />

			Taille du texte "classe"&nbsp;<input name="taille_texte_classe" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_classe)) { ?>value="<?php echo $taille_texte_classe; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;format
				<select name="type_texte_classe">
						<option value="" <?php if ( isset($type_texte_classe) and $type_texte_classe === '' ) { ?>selected="selected"<?php } ?>>d�faut</option>
						<option value="N" <?php if ( isset($type_texte_classe) and $type_texte_classe === 'N' ) { ?>selected="selected"<?php } ?>>normal</option>
						<option value="B" <?php if ( isset($type_texte_classe) and $type_texte_classe === 'B' ) { ?>selected="selected"<?php } ?> style="font-weight: bold;">gras</option>
						<option value="I" <?php if ( isset($type_texte_classe) and $type_texte_classe === 'I' ) { ?>selected="selected"<?php } ?> style="font-style: italic;">italique</option>
						<option value="U" <?php if ( isset($type_texte_classe) and $type_texte_classe === 'U' ) { ?>selected="selected"<?php } ?> style="text-decoration: underline;">soulign�e</option>
					</select><br />

				Taille du texte "ann�e scolaire"&nbsp;<input name="taille_texte_annee" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_annee)) { ?>value="<?php echo $taille_texte_annee; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;format
				<select name="type_texte_annee">
						<option value="" <?php if ( isset($type_texte_annee) and $type_texte_annee === '' ) { ?>selected="selected"<?php } ?>>d�faut</option>
						<option value="N" <?php if ( isset($type_texte_annee) and $type_texte_annee === 'N' ) { ?>selected="selected"<?php } ?>>normal</option>
						<option value="B" <?php if ( isset($type_texte_annee) and $type_texte_annee === 'B' ) { ?>selected="selected"<?php } ?> style="font-weight: bold;">gras</option>
						<option value="I" <?php if ( isset($type_texte_annee) and $type_texte_annee === 'I' ) { ?>selected="selected"<?php } ?> style="font-style: italic;">italique</option>
						<option value="U" <?php if ( isset($type_texte_annee) and $type_texte_annee === 'U' ) { ?>selected="selected"<?php } ?> style="text-decoration: underline;">soulign�e</option>
					</select><br />

			Taille du texte "p�riode"&nbsp;<input name="taille_texte_periode" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_periode)) { ?>value="<?php echo $taille_texte_periode; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;format
				<select name="type_texte_periode">
						<option value="" <?php if ( isset($type_texte_periode) and $type_texte_periode === '' ) { ?>selected="selected"<?php } ?>>d�faut</option>
						<option value="N" <?php if ( isset($type_texte_periode) and $type_texte_periode === 'N' ) { ?>selected="selected"<?php } ?>>normal</option>
						<option value="B" <?php if ( isset($type_texte_periode) and $type_texte_periode === 'B' ) { ?>selected="selected"<?php } ?> style="font-weight: bold;">gras</option>
						<option value="I" <?php if ( isset($type_texte_periode) and $type_texte_periode === 'I' ) { ?>selected="selected"<?php } ?> style="font-style: italic;">italique</option>
						<option value="U" <?php if ( isset($type_texte_periode) and $type_texte_periode === 'U' ) { ?>selected="selected"<?php } ?> style="text-decoration: underline;">soulign�e</option>
					</select><br />

			Taille du texte "date d'edition"&nbsp;<input name="taille_texte_date_edition" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_date_edition)) { ?>value="<?php echo $taille_texte_date_edition; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;format
				<select name="type_texte_date_datation">
						<option value="" <?php if ( isset($type_texte_date_datation) and $type_texte_date_datation === '' ) { ?>selected="selected"<?php } ?>>d�faut</option>
						<option value="N" <?php if ( isset($type_texte_date_datation) and $type_texte_date_datation === 'N' ) { ?>selected="selected"<?php } ?>>normal</option>
						<option value="B" <?php if ( isset($type_texte_date_datation) and $type_texte_date_datation === 'B' ) { ?>selected="selected"<?php } ?> style="font-weight: bold;">gras</option>
						<option value="I" <?php if ( isset($type_texte_date_datation) and $type_texte_date_datation === 'I' ) { ?>selected="selected"<?php } ?> style="font-style: italic;">italique</option>
						<option value="U" <?php if ( isset($type_texte_date_datation) and $type_texte_date_datation === 'U' ) { ?>selected="selected"<?php } ?> style="text-decoration: underline;">soulign�e</option>
					</select><br />
			&nbsp;&nbsp;&nbsp;<input name="affiche_date_edition" id="affiche_date_edition" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_date_edition) and $affiche_date_edition==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="affiche_date_edition" style="cursor: pointer;">Afficher la date d'�dition</label><br /><br /><br />

				<div style="font-weight: bold; background: #CFCFCF;">Cadre adresse des parents</div>
			<input name="active_bloc_adresse_parent" value="1" type="radio" <?php if(!empty($active_bloc_adresse_parent) and $active_bloc_adresse_parent==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activer &nbsp;<input name="active_bloc_adresse_parent" value="0" type="radio" <?php if(empty($active_bloc_adresse_parent) or (!empty($active_bloc_adresse_parent) and $active_bloc_adresse_parent!='1')) { ?>checked="checked"<?php } ?> />&nbsp;D�sactiver<br />
			Positionnement X&nbsp;<input name="X_parent" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_parent)) { ?>value="<?php echo $X_parent; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_parent" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_parent)) { ?>value="<?php echo $Y_parent; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="largeur_bloc_adresse" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_bloc_adresse)) { ?>value="<?php echo $largeur_bloc_adresse; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_bloc_adresse" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_bloc_adresse)) { ?>value="<?php echo $hauteur_bloc_adresse; ?>" <?php } ?> />mm&nbsp;<br />
			<input name="cadre_adresse" id="cadre_adresse" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_adresse) and $cadre_adresse==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="cadre_adresse" style="cursor: pointer;">Ajouter un encadrement</label><br /><br />
			Imprimer les bulletins pour :<br />
			<input name="imprime_pour" value="1" type="radio" <?php if( (!empty($imprime_pour) and $imprime_pour==='1') or empty($imprime_pour) ) { ?>checked="checked"<?php } ?> />&nbsp;seulement pour le 1er responsable<br />
			<input name="imprime_pour" value="2" type="radio" <?php if(!empty($imprime_pour) and $imprime_pour==='2') { ?>checked="checked"<?php } ?> />&nbsp;le 1er et 2�me responsable s'ils n'ont pas la m�me adresse<br />
			<input name="imprime_pour" value="3" type="radio" <?php if(!empty($imprime_pour) and $imprime_pour==='3') { ?>checked="checked"<?php } ?> />&nbsp;forcer pour le 1er et 2�me responsable<br /><br />
			</td>
		</tr>
		<tr>
			<td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;" colspan="2" rowspan="1">
			<div style="font-weight: bold; background: #CFCFCF;">Cadre note et appr�ciation</div>
			<input name="active_bloc_note_appreciation" value="1" type="radio" <?php if(!empty($active_bloc_note_appreciation) and $active_bloc_note_appreciation==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activer &nbsp;<input name="active_bloc_note_appreciation" value="0" type="radio" <?php if(empty($active_bloc_note_appreciation) or (!empty($active_bloc_note_appreciation) and $active_bloc_note_appreciation!='1')) { ?>checked="checked"<?php } ?> />&nbsp;D�sactiver<br />
			Positionnement X&nbsp;<input name="X_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_note_app)) { ?>value="<?php echo $X_note_app; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_note_app)) { ?>value="<?php echo $Y_note_app; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="longeur_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($longeur_note_app)) { ?>value="<?php echo $longeur_note_app; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_note_app)) { ?>value="<?php echo $hauteur_note_app; ?>" <?php } ?> />mm&nbsp;<br />
			Ent�te<br />
			&nbsp;&nbsp;&nbsp;Titre de la colonne mati�re : <input name="titre_entete_matiere" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_matiere)) { ?>value="<?php echo $titre_entete_matiere; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Largeur du bloc mati�re&nbsp;<input name="largeur_matiere" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_matiere)) { ?>value="<?php echo $largeur_matiere; ?>" <?php } ?> />mm<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte "mati�re"&nbsp;<input name="taille_texte_matiere" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_matiere)) { ?>value="<?php echo $taille_texte_matiere; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;Titre de la colonne coefficient : <input name="titre_entete_coef" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_coef)) { ?>value="<?php echo $titre_entete_coef; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Titre de la colonne nombre de note : <input name="titre_entete_nbnote" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_nbnote)) { ?>value="<?php echo $titre_entete_nbnote; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Titre de la colonne rang : <input name="titre_entete_rang" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_rang)) { ?>value="<?php echo $titre_entete_rang; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Titre de la colonne appr�ciation : <input name="titre_entete_appreciation" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_appreciation)) { ?>value="<?php echo $titre_entete_appreciation; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Type de l'ent�te des moyennes&nbsp;
				<select name="entete_model_bulletin">
						<option value="1" <?php if ( isset($entete_model_bulletin) and $entete_model_bulletin === '1' ) { ?>selected="selected"<?php } ?>>1-moyenne</option>
						<option value="2" <?php if ( isset($entete_model_bulletin) and $entete_model_bulletin === '2' ) { ?>selected="selected"<?php } ?>>2-pour la classe</option>
					</select><br />
			&nbsp;&nbsp;&nbsp;Choix de l'ordre&nbsp;
				<select name="ordre_entete_model_bulletin">
						<option value="1" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '1' ) { ?>selected="selected"<?php } ?>>1 - eleve | min | classe | max | rang | niveau | appreciation |</option>
					<option value="2" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '2' ) { ?>selected="selected"<?php } ?>>2 - min | classe | max | eleve | niveau | rang | appreciation |</option>
						<option value="3" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '3' ) { ?>selected="selected"<?php } ?>>3 - eleve | niveau | rang | appreciation | min | classe | max |</option>
						<option value="4" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '4' ) { ?>selected="selected"<?php } ?>>4 - eleve | classe | min | max | rang | niveau | appreciation |</option>
						<option value="5" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '5' ) { ?>selected="selected"<?php } ?>>5 - eleve | min | classe | max | niveau | rang | appreciation |</option>
						<option value="6" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '6' ) { ?>selected="selected"<?php } ?>>6 - min | classe | max | eleve | rang | niveau | appreciation |</option>
					</select><br />
			<div style="background: #EFEFEF; font-style:italic;">Autres</div>
			<input name="active_coef_moyenne" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_coef_moyenne) and $active_coef_moyenne==='1') { ?>checked="checked"<?php } ?> />&nbsp;Coefficient de chaque mati�re<br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colonne des coefficients&nbsp;<input name="largeur_coef_moyenne" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_coef_moyenne)) { ?>value="<?php echo $largeur_coef_moyenne; ?>" <?php } ?> />mm<br />
			&nbsp;&nbsp;&nbsp;<input name="active_coef_sousmoyene" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_coef_sousmoyene) and $active_coef_sousmoyene==='1') { ?>checked="checked"<?php } ?> />&nbsp;l'afficher sous la moyenne de l'�l�ve<br />
			<input name="active_nombre_note_case" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_nombre_note_case) and $active_nombre_note_case==='1') { ?>checked="checked"<?php } ?> />&nbsp;Nombre de notes par mati�re dans une case<br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colonne du nombre de notes&nbsp;<input name="largeur_nombre_note" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_nombre_note)) { ?>value="<?php echo $largeur_nombre_note; ?>" <?php } ?> />mm<br />
			&nbsp;&nbsp;&nbsp;<input name="active_nombre_note" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_nombre_note) and $active_nombre_note==='1') { ?>checked="checked"<?php } ?> />&nbsp;l'afficher sous la moyenne de l'�l�ve<br />
			<div style="background: #EFEFEF; font-style:italic;">Moyenne</div>
			<input name="active_moyenne" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne) and $active_moyenne==='1') { ?>checked="checked"<?php } ?> />&nbsp;Les moyennes<br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colonne d'une moyenne&nbsp;<input name="largeur_d_une_moyenne" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_d_une_moyenne)) { ?>value="<?php echo $largeur_d_une_moyenne; ?>" <?php } ?> />mm<br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_eleve" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_eleve) and $active_moyenne_eleve==='1') { ?>checked="checked"<?php } ?> />&nbsp;Moyenne de l'�l�ve&nbsp;&nbsp;&nbsp;(<input name="active_reperage_eleve" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_reperage_eleve) and $active_reperage_eleve==='1') { ?>checked="checked"<?php } ?> />&nbsp;Mettre un fond de couleur - R:<input name="couleur_reperage_eleve1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_reperage_eleve1)) { ?>value="<?php echo $couleur_reperage_eleve1; ?>" <?php } ?> /> G:<input name="couleur_reperage_eleve2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_reperage_eleve2)) { ?>value="<?php echo $couleur_reperage_eleve2; ?>" <?php } ?> /> B:<input name="couleur_reperage_eleve3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_reperage_eleve3)) { ?>value="<?php echo $couleur_reperage_eleve3; ?>" <?php } ?> />)<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="toute_moyenne_meme_col" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($toute_moyenne_meme_col) and $toute_moyenne_meme_col==='1') { ?>checked="checked"<?php } ?> />&nbsp;Afficher Moyennes classe/min/max sous la moyenne de l'�l�ve � condition qu'elles soient coch�es<br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_classe" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_classe) and $active_moyenne_classe==='1') { ?>checked="checked"<?php } ?> />&nbsp;Moyenne de la classe<br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_min" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_min) and $active_moyenne_min==='1') { ?>checked="checked"<?php } ?> />&nbsp;Moyenne la plus basse<br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_max" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_max) and $active_moyenne_max==='1') { ?>checked="checked"<?php } ?> />&nbsp;Moyenne la plus haute<br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_general" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_general) and $active_moyenne_general === '1') { ?>checked="checked"<?php } ?> />&nbsp;Ligne des moyenne g�n�ral<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="affiche_moyenne_mini_general" id="affiche_moyenne_mini_general" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_moyenne_mini_general) and $affiche_moyenne_mini_general === '1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="affiche_moyenne_mini_general" style="cursor: pointer;">moyenne g�n�ral la plus basse</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="affiche_moyenne_maxi_general" id="affiche_moyenne_maxi_general" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_moyenne_maxi_general) and $affiche_moyenne_maxi_general === '1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="affiche_moyenne_maxi_general" style="cursor: pointer;">moyenne g�n�ral la plus haute</label><br />
			&nbsp;Arrondir les moyennes � : <input name="arrondie_choix" value="0.01" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.01') { ?>checked="checked"<?php } ?> />0,01 <input name="arrondie_choix" value="0.1" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.1') { ?>checked="checked"<?php } ?> />0,1 <input name="arrondie_choix" value="0.25" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.25') { ?>checked="checked"<?php } ?> />0,25 <input name="arrondie_choix" value="0.5" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.5') { ?>checked="checked"<?php } ?> />0,5 <input name="arrondie_choix" value="1" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='1') { ?>checked="checked"<?php } ?> />1<br />
			&nbsp;Nombre de z�ros apr�s la virgule : <input name="nb_chiffre_virgule" value="2" type="radio" <?php if(!empty($nb_chiffre_virgule) and $nb_chiffre_virgule==='2') { ?>checked="checked"<?php } ?> />2  <input name="nb_chiffre_virgule" value="1" type="radio" <?php if(!empty($nb_chiffre_virgule) and $nb_chiffre_virgule==='1') { ?>checked="checked"<?php } ?> />1 - <input name="chiffre_avec_zero" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($chiffre_avec_zero) and $chiffre_avec_zero==='1') { ?>checked="checked"<?php } ?> /> ne pas afficher le "0" apr�s la virgule<br />
			<div style="background: #EFEFEF; font-style:italic;">Autres</div>
			<input name="active_rang" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_rang) and $active_rang==='1') { ?>checked="checked"<?php } ?> />&nbsp;Rang de l'�l�ve<br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colonne rang&nbsp;<input name="largeur_rang" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_rang)) { ?>value="<?php echo $largeur_rang; ?>" <?php } ?> />mm<br />
			<input name="active_graphique_niveau" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_graphique_niveau) and $active_graphique_niveau==='1') { ?>checked="checked"<?php } ?> />&nbsp;Graphique de niveau<br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colonne niveau&nbsp;<input name="largeur_niveau" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_niveau)) { ?>value="<?php echo $largeur_niveau; ?>" <?php } ?> />mm<br />
			<input name="active_appreciation" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_appreciation) and $active_appreciation==='1') { ?>checked="checked"<?php } ?> />&nbsp;Appr�ciation par mati�re<br />
			&nbsp;&nbsp;&nbsp;<input name="autorise_sous_matiere" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($autorise_sous_matiere) and $autorise_sous_matiere==='1') { ?>checked="checked"<?php } ?> />&nbsp;Autoriser l'affichage des sous mati�res<br />
			Hauteur de la moyenne g�n�rale&nbsp;<input name="hauteur_entete_moyenne_general" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_entete_moyenne_general)) { ?>value="<?php echo $hauteur_entete_moyenne_general; ?>" <?php } ?> />mm<br />
			<div style="background: #EFEFEF; font-style:italic;">Cat�gories de mati�res :</div>
			&nbsp;&nbsp;&nbsp;<input name="active_regroupement_cote" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_regroupement_cote) and $active_regroupement_cote==='1') { ?>checked="checked"<?php } ?> />&nbsp;Nom des cat�gories de mati�res sur le cot�&nbsp;&nbsp;&nbsp;(<input name="couleur_categorie_cote" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($couleur_categorie_cote) and $couleur_categorie_cote==='1') { ?>checked="checked"<?php } ?> />&nbsp;Mettre un fond de couleur - R:<input name="couleur_categorie_cote1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_cote1)) { ?>value="<?php echo $couleur_categorie_cote1; ?>" <?php } ?> /> G:<input name="couleur_categorie_cote2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_cote2)) { ?>value="<?php echo $couleur_categorie_cote2; ?>" <?php } ?> /> B:<input name="couleur_categorie_cote3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_cote3)) { ?>value="<?php echo $couleur_categorie_cote3; ?>" <?php } ?> />)<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte&nbsp;<input name="taille_texte_categorie_cote" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_categorie_cote)) { ?>value="<?php echo $taille_texte_categorie_cote; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;<input name="active_entete_regroupement" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_entete_regroupement) and $active_entete_regroupement==='1') { ?>checked="checked"<?php } ?> />&nbsp;Nom des cat�gories de mati�res en ent�te&nbsp;&nbsp;&nbsp;(<input name="couleur_categorie_entete" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($couleur_categorie_entete) and $couleur_categorie_entete==='1') { ?>checked="checked"<?php } ?> />&nbsp;Mettre un fond de couleur - R:<input name="couleur_categorie_entete1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_entete1)) { ?>value="<?php echo $couleur_categorie_entete1; ?>" <?php } ?> /> G:<input name="couleur_categorie_entete2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_entete2)) { ?>value="<?php echo $couleur_categorie_entete2; ?>" <?php } ?> /> B:<input name="couleur_categorie_entete3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_entete3)) { ?>value="<?php echo $couleur_categorie_entete3; ?>" <?php } ?> />)<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte&nbsp;<input name="taille_texte_categorie" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_categorie)) { ?>value="<?php echo $taille_texte_categorie; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Hauteur ent�te des cat�gories&nbsp;<input name="hauteur_info_categorie" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_info_categorie)) { ?>value="<?php echo $hauteur_info_categorie; ?>" <?php } ?> />mm<br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_regroupement" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_regroupement) and $active_moyenne_regroupement==='1') { ?>checked="checked"<?php } ?> />&nbsp;Moyenne des cat�gories de mati�res<br />
			<div style="background: #EFEFEF; font-style:italic;">Moyenne g�n�rale</div>
			&nbsp;&nbsp;&nbsp;<input name="couleur_moy_general" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($couleur_moy_general) and $couleur_moy_general==='1') { ?>checked="checked"<?php } ?> />&nbsp;Mettre un fond de couleur - R:<input name="couleur_moy_general1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_moy_general1)) { ?>value="<?php echo $couleur_moy_general1; ?>" <?php } ?> /> G:<input name="couleur_moy_general2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_moy_general2)) { ?>value="<?php echo $couleur_moy_general2; ?>" <?php } ?> /> B:<input name="couleur_moy_general3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_moy_general3)) { ?>value="<?php echo $couleur_moy_general3; ?>" <?php } ?> /><br /><br />
			<div style="font-weight: bold; background: #CFCFCF;">Cadre Absences/CPE</div>
			<input name="active_bloc_absence" value="1" type="radio" <?php if(!empty($active_bloc_eleve) and $active_bloc_eleve==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activer &nbsp;<input name="active_bloc_absence" value="0" type="radio" <?php if(empty($active_bloc_absence) or (!empty($active_bloc_absence) and $active_bloc_absence!='1')) { ?>checked="checked"<?php } ?> />&nbsp;D�sactiver<br />
			Positionnement X&nbsp;<input name="X_absence" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_absence)) { ?>value="<?php echo $X_absence; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_absence" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($Y_absence)) { ?>value="<?php echo $Y_absence; ?>" <?php } ?> />mm&nbsp;<br /><br />
			</td>
		</tr>
		<tr>
			<td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">
				<div style="font-weight: bold; background: #CFCFCF;">Cadre Avis conseil de classe</div>
			<input name="active_bloc_avis_conseil" value="1" type="radio" <?php if(!empty($active_bloc_avis_conseil) and $active_bloc_avis_conseil==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activer &nbsp;<input name="active_bloc_avis_conseil" value="0" type="radio" <?php if(empty($active_bloc_avis_conseil) or (!empty($active_bloc_avis_conseil) and $active_bloc_avis_conseil!='1')) { ?>checked="checked"<?php } ?> />&nbsp;D�sactiver<br />
			Positionnement X&nbsp;<input name="X_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_avis_cons)) { ?>value="<?php echo $X_avis_cons; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_avis_cons)) { ?>value="<?php echo $Y_avis_cons; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="longeur_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($longeur_avis_cons)) { ?>value="<?php echo $longeur_avis_cons; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_avis_cons)) { ?>value="<?php echo $hauteur_avis_cons; ?>" <?php } ?> />mm&nbsp;<br />
			Titre du bloc avis conseil de classe : <input name="titre_bloc_avis_conseil" size="19" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_bloc_avis_conseil)) { ?>value="<?php echo $titre_bloc_avis_conseil; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte&nbsp;<input name="taille_titre_bloc_avis_conseil" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_titre_bloc_avis_conseil)) { ?>value="<?php echo $taille_titre_bloc_avis_conseil; ?>" <?php } ?> />pixel<br />
			Taille du texte du professeur principal"&nbsp;<input name="taille_profprincipal_bloc_avis_conseil" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_profprincipal_bloc_avis_conseil)) { ?>value="<?php echo $taille_profprincipal_bloc_avis_conseil; ?>" <?php } ?> />pixel<br />
			<input name="cadre_avis_cons" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_avis_cons) and $cadre_avis_cons==='1') { ?>checked="checked"<?php } ?> />&nbsp;Ajouter un encadrement<br /><br />
			</td>
				<td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">
				<div style="font-weight: bold; background: #CFCFCF;">Cadre signature du chef</div>
			<input name="active_bloc_chef" value="1" type="radio" <?php if(!empty($active_bloc_chef) and $active_bloc_chef==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activer &nbsp;<input name="active_bloc_chef" value="0" type="radio" <?php if(empty($active_bloc_chef) or (!empty($active_bloc_chef) and $active_bloc_chef!='1')) { ?>checked="checked"<?php } ?> />&nbsp;D�sactiver<br />
			Positionnement X&nbsp;<input name="X_sign_chef" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_sign_chef)) { ?>value="<?php echo $X_sign_chef; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_sign_chef" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($Y_sign_chef)) { ?>value="<?php echo $Y_sign_chef; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="longeur_sign_chef" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($longeur_sign_chef)) { ?>value="<?php echo $longeur_sign_chef; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_sign_chef" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($hauteur_sign_chef)) { ?>value="<?php echo $hauteur_sign_chef; ?>" <?php } ?> />mm&nbsp;<br />
			<input name="affichage_haut_responsable" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affichage_haut_responsable) and $affichage_haut_responsable==='1') { ?>checked="checked"<?php } ?> />&nbsp;Afficher l'identit� du responsable de direction<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte&nbsp;<input name="taille_texte_identitee_chef" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_identitee_chef)) { ?>value="<?php echo $taille_texte_identitee_chef; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="affiche_fonction_chef" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_fonction_chef) and $affiche_fonction_chef==='1') { ?>checked="checked"<?php } ?> />&nbsp;Afficher sa fonction<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte&nbsp;<input name="taille_texte_fonction_chef" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_fonction_chef)) { ?>value="<?php echo $taille_texte_fonction_chef; ?>" <?php } ?> />pixel<br />
			<input name="cadre_sign_chef" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_sign_chef) and $cadre_sign_chef==='1') { ?>checked="checked"<?php } ?> />&nbsp;Ajouter un encadrement<br /><br />
			</td>
		</tr>
		<tr>
			<td style="vertical-align: center; white-space: nowrap; text-align: center; width: 100%; background: #B3B7BF;" colspan="2" rowspan="1">
			<?php
			/*
			// D�j� ins�r� plus haut
			if($action_model==='modifier') {
				echo "<input type='hidden' name='id_model_bulletin' value='$id_model_bulletin' />\n";
			}
			*/
			?>
			<input type="hidden" name="action_model" value="<?php echo $action_model; ?>" />
			<input type="submit" id="valide_modif_model2" name="valide_modif_model" value="Valider le mod�le" />
			</td>
		</tr>
		</tbody>
		</table>
		</form>
		<?php
	}

	if($action_model==='supprimer' and empty($valide_modif_model)) {

		echo "<form method='post' action='".$_SERVER['PHP_SELF']."?modele=aff' name='action_modele_form'>\n";
		echo "<h2>Supprimer un mod�le de bulletin</h2>\n";

		$sql="SELECT valeur FROM modele_bulletin WHERE id_model_bulletin='$model_bulletin' AND nom='nom_model_bulletin';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Aucun mod�le n'a �t� trouv� pour l'identifiant $model_bulletin</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		else {
			$lig_tmp=mysql_fetch_object($res);
			echo "<p>Vous allez supprimer le mod�le <strong>$lig_tmp->valeur</strong></p>\n";
	?>
		<table style="text-align: left; width: 100%; border: 1px solid #74748F;" border="0" cellpadding="2" cellspacing="2" summary="Suppression d'un mod�le">
		<tbody>
		<tr>
			<td style="vertical-align: center; white-space: nowrap; text-align: center; width: 100%; background: #B3B7BF;" colspan="2" rowspan="1">
			<br /><span style="font-weight: bold; color: rgb(255, 0, 0);">Souhaitez-vous supprimer ce mod�le ?</span><br /><br />
			<input type="hidden" name="id_model_bulletin" value="<?php echo $model_bulletin; ?>" />
			<input type="hidden" name="action_model" value="<?php echo $action_model; ?>" />
			<input type="submit" id="valide_modif_model" name="valide_modif_model" value="Oui supprimer ce mod�le" />
			</td>
		</tr>
		</tbody>
		</table>
		</form>
		<?php
		}
	}
}


require("../lib/footer.inc.php");
?>
