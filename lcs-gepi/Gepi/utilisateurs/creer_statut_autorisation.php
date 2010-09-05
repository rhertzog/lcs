<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2008
 * Fichier d'inclusion � la gestion des statuts 'autre'
 *
 */
//$niveau_arbo = 1;
	// Initialisations files
	//require_once("../lib/initialisations.inc.php");

if (!$_SESSION["statut"] OR ($_SESSION["statut"] != 'autre' AND $_SESSION["statut"] != 'administrateur')) {

		//tentative_intrusion(1, "Tentative d'acc�s � un fichier sans avoir les droits n�cessaires");
		trigger_error('Vous avez proc�d� � une lecture de fichier interdit', E_USER_ERROR);

}

// droits g�n�raux et communs � tous les utilisateurs
$autorise[0] = array('/accueil.php',
				'/utilisateurs/mon_compte.php',
				'/gestion/contacter_admin.php',
				'/gestion/info_gepi.php');
// droits sp�cifiques sur les pages relatives aux droits possibles
$autorise[1] = array('/cahier_notes/visu_releve_notes.php');
$autorise[2] = array('/prepa_conseil/index3.php', '/prepa_conseil/edit_limite.php');
$autorise[3] = array('/mod_absences/gestion/voir_absences_viescolaire.php',
						'/mod_absences/gestion/bilan_absences_quotidien.php',
						'/mod_absences/gestion/bilan_absences_classe.php',
						'/mod_absences/gestion/bilan_absences_quotidien_pdf.php',
						'/mod_absences/lib/tableau.php',
						'/mod_absences/lib/export_csv.php');
$autorise[4] = array('/mod_absences/gestion/select.php',
						'/mod_absences/gestion/ajout_abs.php',
						'/mod_absences/lib/liste_absences.php');
$autorise[5] = array('/cahier_texte/see_all.php');
$autorise[6] = array('/cahier_texte_admin/visa_ct.php');
$autorise[7] = array('/edt_organisation/index_edt.php');
$autorise[8] = array('/tous_les_edt');
$autorise[9] = array('/messagerie/index.php');
$autorise[10]= array('/eleves/visu_eleve.php',
						'/eleves/liste_eleves.php');
$autorise[11]= array('/voir_resp');
$autorise[12]= array('/voir_ens');
$autorise[13]= array('/voir_notes');
$autorise[14]= array('/voir_bulle');
$autorise[15]= array('/voir_abs');
//$autorise[16]= array('/voir_anna');
//$autorise[16]= array('/mod_annees_anterieures/popup_annee_anterieure.php');
$autorise[16]= array('/mod_annees_anterieures/consultation_annee_anterieure.php', '/mod_annees_anterieures/popup_annee_anterieure.php');
$autorise[17]= array('/mod_trombinoscopes/trombinoscopes.php', '/mod_trombinoscopes/trombi_impr.php');
$autorise[18]= array('/mod_discipline/index.php',
					 '/mod_discipline/saisie_incident.php',
					 '/mod_discipline/incidents_sans_protagonistes.php',
					 '/mod_discipline/sauve_role.php',
					 '/mod_discipline/update_colonne_retenue.php',
					 '/mod_discipline/traiter_incident.php',
					 '/mod_ooo/retenue.php',
					 '/mod_ooo/rapport_incident.php');
$autorise[19]= array('/mod_abs2/saisir_eleve.php',
					 '/mod_abs2/index.php',
					 '/mod_abs2/visu_saisie.php',
					 '/mod_abs2/enregistrement_modif_saisie.php',
					 '/mod_abs2/liste_saisies.php',
					 '/mod_abs2/enregistrement_saisie_eleve.php');


$iter = count($autorise);
$nbre_menu = $iter - 1;
// Intitul�s pour le menu et le champ name du formulaire
$menu_accueil[0] = array($nbre_menu);
$menu_accueil[1] = array('relev�s de notes', 'Visionner les relev�s de notes de tous les �l�ves', 'ne');
$menu_accueil[2] = array('Bulletins simplifi�s', 'Visionner tous les bulletins simplifi�s de tous les �l�ves', 'bs');
$menu_accueil[3] = array('Voir les absences', 'Visionner toutes les absences de l\'�tablissement', 'va');
$menu_accueil[4] = array('Saisir les absences', 'Saisir les absences de tous les �l�ves de l\'�tablissement', 'sa');
$menu_accueil[5] = array('Cahiers de textes', 'Visionner tous les cahiers de textes de l\'�tablissement', 'cdt');
$menu_accueil[6] = array('Signer les cahiers de textes', 'Signer (viser) les cahiers de textes', 'cdt_visa');
$menu_accueil[7] = array('Emploi du temps', 'Visionner les emplois du temps de tous les �l�ves', 'ee');
$menu_accueil[8] = array('Emploi du temps', 'Visionner tous les emplois du temps de l\'�tablissement', 'te');
$menu_accueil[9] = array('Panneau d\'affichage', 'G�rer les messages � afficher sur la page d\'accueil des utilisateurs.', 'pa');
$menu_accueil[10] = array('Fiches �l�ves', 'Acc�s g�n�ral sur les fiches des �l�ves', 've');
$menu_accueil[11] = array('Fiches : ', 'Fiches : voir les responsables', 'vre');
$menu_accueil[12] = array('Fiches : ', 'Fiches : voir les enseignements', 'vee');
$menu_accueil[13] = array('Fiches : ', 'Fiches : voir les relev�s de notes', 'vne');
$menu_accueil[14] = array('Fiches : ', 'Fiches : voir les bulletins simplifi�s', 'vbe');
$menu_accueil[15] = array('Fiches : ', 'Fiches : voir les absences', 'vae');
$menu_accueil[16] = array('Fiches : ', 'Fiches : voir ann�e ant�rieure', 'anna');
$menu_accueil[17] = array('Trombinoscope', 'Trombinoscope des �l�ves', 'tr');
$menu_accueil[18] = array('Discipline', 'Acc�der au module Discipline : D�clarer et g�rer ses incidents', 'dsi');
$menu_accueil[19] = array('Absence', 'Acc�der au module absence.', 'abs');


?>