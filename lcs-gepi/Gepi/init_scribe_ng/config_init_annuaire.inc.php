<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Liste des tables � vider avant de proc�der � l'importation
 * (ne pas modifier � moins de savoir pr�cis�ment ce que vous faites)
  */

$liste_tables_del = array(
"absences",
"absences_gep",
"aid",
"aid_appreciations",
    //"aid_config",
"avis_conseil_classe",
    // Par la suite, � voir si on efface les classes ou non...
"classes",
    //"droits",
"eleves",
"responsables",
"responsables2",
"resp_pers",
"resp_adr",
    //"etablissements",
"j_aid_eleves",
"j_aid_eleves_resp",
"j_aid_utilisateurs",
"j_aid_utilisateurs_gest",
"j_eleves_classes",
    //==========================
    // On ne vide plus la table chaque ann�e
    // Probl�me avec Sconet qui r�cup�re seulement l'�tablissement de l'ann�e pr�c�dente qui peut �tre l'�tablissement courant
    //"j_eleves_etablissements",
    //==========================
"j_eleves_professeurs",
"j_eleves_regime",
    //"j_professeurs_matieres",
    //"log",
    //"matieres",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
// On efface aussi les periodes
"periodes",
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
    //"groupes",
"j_eleves_groupes",
"j_groupes_classes",
    //"j_groupes_matieres",
    //"j_groupes_professeurs",
"eleves_groupes_settings"
    //"setting"
);

/*
 * Nom de la p�riode ajout�e par d�faut aux classes lors de l'import
 */
$nom_periode_defaut = "Periode";


?>
