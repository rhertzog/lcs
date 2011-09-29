<?php
/* $Id: initialisation_annee.inc.php 7871 2011-08-21 15:41:32Z crob $ */

// Nettoyage des tables

// ATTENTION: Lors de l'ajout de tables � nettoyer � une �tape donn�e, � veiller, notamment dans le cas de tables temporaires, � v�rifier que dans la suite de la proc�deure d'initialisation, cette table n'intervient pas... ou sans perturbation

// Etape de l'import des �l�ves et classes
$liste_tables_del_etape_eleves = array(
// mod_abs2
"a_agregation_decompte",
"a_notifications",
"a_saisies",
"a_saisies_version",
"a_traitements",
// Absences
"absences",
"absences_gep",
"absences_rb",
"absences_repas",
"absences_eleves",
"vs_alerts_eleves",
"vs_alerts_groupes",
"vs_alerts_types",
// AID
"aid",
"aid_appreciations",
"avis_conseil_classe",
"j_aid_eleves",
"j_aid_utilisateurs",
"j_aid_eleves_resp",
"j_aid_utilisateurs_gest",
"j_aidcateg_super_gestionnaires",
"j_aidcateg_utilisateurs",

// El�ves et responsables
"eleves",
"responsables",
/*
// NE FAUDRAIT-IL PAS VIDER ICI responsables2, resp_pers et reps_adr?
// NON: Cela emp�che de conserver les comptes utilisateurs pour les responsables
"responsables2",
"resp_pers",
"resp_adr",
*/
"j_eleves_classes",
//==========================
// On ne vide plus la table chaque ann�e
// Probl�me avec Sconet qui r�cup�re seulement l'�tablissement de l'ann�e pr�c�dente qui peut �tre l'�tablissement courant
//"j_eleves_etablissements",
//==========================
"j_eleves_professeurs",
"j_eleves_regime",
// Notes et appr�ciations
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
"synthese_app_classe",
//==========================
// Tables notanet
'notanet',
'notanet_avis',
'notanet_app',
'notanet_lvr_ele',
'notanet_socle_commun',
'notanet_verrou',
'notanet_socles',
'notanet_ele_type',
//==========================
"observatoire",
"observatoire_comment",
"observatoire_suivi",

"tempo2",
"tempo",
// D�coupe de trombinoscopes
"trombino_decoupe",
"trombino_decoupe_param",
// Cahier de notes
"cc_dev",
"cc_eval",
"cc_notes_eval",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
/*
"ct_entry",
// Cahier de textes
"ct_documents",
"ct_devoirs_entry",
"ct_private_entry"
*/
// mod_examen_blanc
"ex_classes",
"ex_groupes",
"ex_notes",
// mod_epreuve_blanche
"eb_copies",
"eb_epreuves",
"eb_groupes",
"eb_profs",
// G�n�se des classes
"gc_ele_arriv_red",
"gc_eleves_options",
// mod_discipline
"s_communication",
"s_exclusions",
"s_incidents",
"s_protagonistes",
"s_reports",
"s_retenues",
"s_sanctions",
"s_traitement_incident",
"s_travail",
"s_travail_mesure",
// Table optionnelle pour les fils RSS de CDT
"rss_users"
);

// Etape de l'import des mati�res
$liste_tables_del_etape_matieres = array(
"eleves_groupes_settings",
"groupes",
"j_eleves_groupes",
"j_groupes_matieres",
"j_groupes_professeurs",
"j_groupes_classes",
"j_groupes_visibilite",
"j_signalement",
"eleves_groupes_settings",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
"matieres_app_corrections",
"matieres_app_delais",
"matieres_appreciations_acces",
"synthese_app_classe",
"tempo2",
"tempo",
"cc_dev",
"cc_eval",
"cc_notes_eval",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
"ct_devoirs_entry",
"ct_documents",
"ct_entry",
"ct_devoirs_documents",
"ct_private_entry",
"ct_sequences",
'edt_classes',
'edt_cours'
/*
// Attente de confirmation de Pascal Fautrero pour d�commenter...
,
"edt_calendrier",
//"edt_classes",
"edt_cours",
//"edt_creneaux",
//"edt_creneaux_bis",
"edt_dates_special",
"edt_init",
//"edt_semaines",
//"edt_setting"
*/
);

// Etape de l'import des responsables
$liste_tables_del_etape_resp = array(
// On vide l'ancienne table responsables pour ne pas conserver des infos d'ann�es ant�rieures:
"responsables",

"responsables2",
"resp_pers",
"resp_adr",
"tempo2",
"tempo"
);

// Etape de l'import des professeurs
$liste_tables_del_etape_professeurs = array(
"j_aid_utilisateurs",
"j_aid_utilisateurs_gest",
"j_groupes_professeurs",
"j_eleves_professeurs",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
"matieres_app_corrections",
"matieres_app_delais",
"matieres_appreciations_acces",
"synthese_app_classe",
"observatoire_j_resp_champ",
"tempo2",
"tempo",
"cc_dev",
"cc_eval",
"cc_notes_eval",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
"udt_lignes",
"udt_corresp"
);

?>