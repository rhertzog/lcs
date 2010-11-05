<?php

/**
 * Ensemble des fonctions qui permettent de cr�er un nouveau cours en v�rifiant les pr�c�dents
 *
 * @version $Id: fonctions_cours.php 5437 2010-09-26 19:08:38Z adminpaulbert $
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

// Toutes les fonctions d'initialisation de l'EdT sont utiles
require_once("edt_init_fonctions.php");
require_once("choix_langue.php");

// =============================================================================
//
//          Fonction qui v�rifie si le prof est disponible pour le cours appel�
//
//              $login_prof = login_prof de la table 'edt_cours' 
//              $jour = jour_horaire_etablissement de la table 'horaires_etablissement'               
//              $creneau = id_definie_periode de la table 'edt_creneaux'
//
// =============================================================================

function ProfDisponible($login_prof, $jour, $creneau, $duree, $heuredeb_dec, $type_semaine, $id_cours, &$InformationMessage, $period){

$prof_libre = true;
$creneaux_occupes = "";
$tab_enseignement = array();
$tab_id_creneaux = retourne_id_creneaux();
if ($login_prof != "") {

    // ===================== d�terminer (en demi-creneaux) le d�but et la fin du cours 
    $j = 0;
    $endprocess = false;
    $rang_creneau = 0;
    while (isset($tab_id_creneaux[$j]) AND (!$endprocess)) {
        if ($tab_id_creneaux[$j] == $creneau) {
            $rang_creneau = $j;
            $endprocess = true;
	    }
        $j++;
	}
    $start_lesson = $rang_creneau*2;
    if ($heuredeb_dec == "0.5") {
	    $start_lesson++;
	}
    $end_lesson = $start_lesson + $duree;

    // ===================== 
    $j = 0;
    while (isset($tab_id_creneaux[$j])) {
        $j++;
	}   
    $end_day = $j*2;

    if ($end_lesson > $end_day) {
        $prof_libre = false;
        $creneaux_occupes = INCOMPATIBLE_LESSON_LENGTH;
	}
    else {
        $j = 0;
        $elapse_time = 0;
        while (isset($tab_id_creneaux[$j])) {
            $current_heure = "0";
	        if ($elapse_time%2 == 1) {
		        $current_heure = "0.5";
	        }
    
            $nb_rows = RecupCoursProf($j, $jour, $login_prof, $current_heure, $type_semaine, $id_cours ,$tab_enseignement, $period);
    
	        if ($nb_rows >=1) {
                $k = 0;
                while (($tab_enseignement['id_groupe'][$k] != "") OR ($tab_enseignement['id_aid'][$k] != "")) {
                    $elapse_time_end = $elapse_time + $tab_enseignement['duree'][$k];
                    if ((($elapse_time<=$start_lesson) AND ($start_lesson<$elapse_time_end)) OR (($elapse_time>=$start_lesson) AND ($elapse_time<$end_lesson))){
                        $req_nom_creneau = mysql_query("SELECT nom_definie_periode FROM edt_creneaux WHERE id_definie_periode = '".$tab_id_creneaux[$j]."' ");
		                $rep_nom_creneau = mysql_fetch_array($req_nom_creneau);
                        if ($tab_enseignement['id_semaine'][$k] != "0") {
                            $creneaux_occupes .= "en ".$rep_nom_creneau['nom_definie_periode']." (Sem ".$tab_enseignement['id_semaine'][$k].") "." ";
                        }
                        else {
                            $creneaux_occupes .= "en ".$rep_nom_creneau['nom_definie_periode']." ";
                        }
                        $prof_libre = false;
                    }
    
                    $k++;
                }
            }
	        $elapse_time++;
            $j=(int)($elapse_time/2);
        }
        if ($creneaux_occupes != "") {
            $creneaux_occupes = LESSON_OVERLAPPING.$creneaux_occupes;
        }
    }
}
$InformationMessage = $creneaux_occupes;

return $prof_libre;

} 

// =============================================================================
//
//          Fonction qui v�rifie si une salle est libre pour le cours appel�
//
//              $salle = id_salle de la table 'salle_cours' 
//              $jour = jour_horaire_etablissement de la table 'horaires_etablissement'               
//              $creneau = id_definie_periode de la table 'edt_creneaux'
//
// =============================================================================

function SalleDisponible($salle, $jour, $creneau, $duree, $heuredeb_dec, $type_semaine, $id_cours, &$InformationMessage, $period){

$salle_libre = true;
$enseignants = "";
$tab_enseignement = array();
$tab_id_creneaux = retourne_id_creneaux();

if (($salle != "") AND ($salle != "rie")) {

    $j = 0;
    $endprocess = false;
    $rang_creneau = 0;
    while (isset($tab_id_creneaux[$j]) AND (!$endprocess)) {
        if ($tab_id_creneaux[$j] == $creneau) {
            $rang_creneau = $j;
            $endprocess = true;
	    }
        $j++;
	}
    $start_lesson = $rang_creneau*2;
    if ($heuredeb_dec == "0.5") {
	    $start_lesson++;
	}
    $end_lesson = $start_lesson + $duree;


    $j = 0;
    $elapse_time = 0;
    while (isset($tab_id_creneaux[$j])) {
        $current_heure = "0";
	    if ($elapse_time%2 == 1) {
		    $current_heure = "0.5";
	    }

        $nb_rows = RecupCoursSallesCommunes($j, $jour, $salle, $current_heure, $type_semaine, $id_cours ,$tab_enseignement, $period);

	    if ($nb_rows >=1) {
            $k = 0;
            while (($tab_enseignement['id_groupe'][$k] != "") OR ($tab_enseignement['id_aid'][$k] != "")) {
                $elapse_time_end = $elapse_time + $tab_enseignement['duree'][$k];
                if ((($elapse_time<=$start_lesson) AND ($start_lesson<$elapse_time_end)) OR (($elapse_time>=$start_lesson) AND ($elapse_time<$end_lesson))){
                    $req_nom_prof = mysql_query("SELECT nom FROM utilisateurs WHERE login = '".$tab_enseignement['login'][$k]."' ");
		            $rep_nom_prof = mysql_fetch_array($req_nom_prof);
                    $req_nom_creneau = mysql_query("SELECT nom_definie_periode FROM edt_creneaux WHERE id_definie_periode = '".$tab_id_creneaux[$j]."' ");
		            $rep_nom_creneau = mysql_fetch_array($req_nom_creneau);
                    if ($tab_enseignement['id_semaine'][$k] != "0") {
                        $enseignants .= $rep_nom_prof['nom']." (Sem ".$tab_enseignement['id_semaine'][$k].") "."en ".$rep_nom_creneau['nom_definie_periode']." ";
                    }
                    else {
                        $enseignants .= $rep_nom_prof['nom']." "."en ".$rep_nom_creneau['nom_definie_periode']." ";
                    }
                    $salle_libre = false;
                }

                $k++;
            }
        }
	    $elapse_time++;
        $j=(int)($elapse_time/2);
    }
    if ($enseignants != "") {
        $enseignants = CLASSROOM_NOT_FREE.$enseignants;
    }
}
$InformationMessage = $enseignants;

return $salle_libre;

} // verifSalle()

// =============================================================================
//
//          Fonction qui v�rifie si un groupe est libre pour le cours appel�
//
// =============================================================================

function GroupeDisponible($groupe, $id_aid, $jour, $creneau, $duree, $heuredeb_dec, $type_semaine, $id_cours, &$InformationMessage, $period){

$groupe_libre = true;
$enseignants = "";
$tab_enseignement = array();
$tab_id_creneaux = retourne_id_creneaux();
if (($groupe != "") OR ($id_aid != "")) {

    if ($id_aid != "") {
        $groupe_type = "AID";
    }
    else {
        $groupe_type = "ENS";
    }

    $j = 0;
    $endprocess = false;
    $rang_creneau = 0;
    while (isset($tab_id_creneaux[$j]) AND (!$endprocess)) {
        if ($tab_id_creneaux[$j] == $creneau) {
            $rang_creneau = $j;
            $endprocess = true;
	    }
        $j++;
	}
    $start_lesson = $rang_creneau*2;
    if ($heuredeb_dec == "0.5") {
	    $start_lesson++;
	}
    $end_lesson = $start_lesson + $duree;


    $j = 0;
    $elapse_time = 0;

    while (isset($tab_id_creneaux[$j])) {
        $current_heure = "0";
	    if ($elapse_time%2 == 1) {
		    $current_heure = "0.5";
	    }

        $nb_rows = RecupCoursElevesCommuns($j, $jour, $groupe, $id_aid, $groupe_type, $current_heure, $type_semaine, $id_cours ,$tab_enseignement, $period);

	    if ($nb_rows >=1) {
            $k = 0;
           
            while (($tab_enseignement['id_groupe'][$k] != "") OR ($tab_enseignement['id_aid'][$k] != "")) {
                $elapse_time_end = $elapse_time + $tab_enseignement['duree'][$k];
                if ((($elapse_time<=$start_lesson) AND ($start_lesson<$elapse_time_end)) OR (($elapse_time>=$start_lesson) AND ($elapse_time<$end_lesson))){
                    if ($tab_enseignement['aid'][$k] == 0) {
                        if ($groupe_type == "ENS") {
                            $req_nombre_eleves = mysql_query("SELECT DISTINCT login FROM j_eleves_groupes WHERE login IN 
							    (SELECT login FROM j_eleves_groupes WHERE id_groupe = '".$tab_enseignement['id_groupe'][$k]."') AND
							    id_groupe = '".$groupe."'  ");
                        }
                        else {
                            $req_nombre_eleves = mysql_query("SELECT DISTINCT login FROM j_aid_eleves WHERE login IN 
							    (SELECT login FROM j_eleves_groupes WHERE id_groupe = '".$tab_enseignement['id_aid'][$k]."') AND
							    id_aid = '".$groupe."'  ");

                        }
                    }
                    else {
                        if ($groupe_type == "ENS") {
                            $req_nombre_eleves = mysql_query("SELECT DISTINCT login FROM j_eleves_groupes WHERE login IN 
							    (SELECT login FROM j_aid_eleves WHERE id_aid = '".$tab_enseignement['id_groupe'][$k]."') AND
							    id_groupe = '".$groupe."'  ");
                        }
                        else {
                            $req_nombre_eleves = mysql_query("SELECT DISTINCT login FROM j_aid_eleves WHERE login IN 
							    (SELECT login FROM j_aid_eleves WHERE id_aid = '".$tab_enseignement['id_aid'][$k]."') AND
							    id_aid = '".$groupe."'  ");

                        }
                    }
                    $req_nom_prof = mysql_query("SELECT nom FROM utilisateurs WHERE login = '".$tab_enseignement['login'][$k]."' ");
		            $rep_nom_prof = mysql_fetch_array($req_nom_prof);
                    
                    $enseignants .= $rep_nom_prof['nom']." (".mysql_num_rows($req_nombre_eleves)." �l�ves) ";
                    // ---- Si nb �l�ves < 5, ce sont sans doute des �l�ves affect�s provisoirement dans un autre cours (CLA) : on accepte la cr�ation
                    if (mysql_num_rows($req_nombre_eleves) >= 5) {
                        $groupe_libre = false;
                    }
                }
                $k++;
            }
        }
	    $elapse_time++;
        $j=(int)($elapse_time/2);
    }
    if ($enseignants != "") {
        if ($groupe_libre == false) {
            $enseignants = STUDENTS_NOT_FREE.$enseignants;
        }
        else {
            $enseignants = SOME_STUDENTS_NOT_FREE.$enseignants;
        }
    }
}
else {
    $groupe_libre = false;
    $enseignants = GROUP_IS_EMPTY;
}

$InformationMessage = $enseignants;

return $groupe_libre;

} // verifGroupe()

// =============================================================================
//
//
// =============================================================================

function RecupCoursElevesCommuns($creneau_courant, $jour, $groupe, $id_aid, $groupe_type, $current_heure, $type_semaine, $id_cours, &$tab_enseignement, $period)
{
    $tab_id_creneaux = retourne_id_creneaux();
    $k = 0;


    if (($period != NULL) AND ($period != '0')) {
        $calendrier = "(id_calendrier = '".$period."' OR id_calendrier = '0')";
    }
    else {
        $calendrier = "1=1";
    }
    // --------- Rechercher les groupes d'enseignement qui ont des �l�ves en commun avec le groupe vis�

	$sql_request = "DELETE FROM j_eleves_groupes_delestage";
	$req = mysql_query($sql_request);

	$sql_request = "SELECT login, id_groupe, periode FROM j_eleves_groupes WHERE id_groupe = '".$groupe."'";
	$req = mysql_query($sql_request);
	
	while ($rep = mysql_fetch_array($req)) {
		$sql_request = "INSERT INTO j_eleves_groupes_delestage SET
			login = '".$rep['login']."', 
			id_groupe = '".$rep['id_groupe']."',
			periode = '".$rep['periode']."'";
		$req_insertion = mysql_query($sql_request);
	}
	
	
    if ($type_semaine == "0") {
        if ($groupe_type == "ENS") {
            $req_creneau = mysql_query("SELECT duree , login_prof , id_groupe FROM edt_cours WHERE 
                                    id_groupe IN (SELECT id_groupe FROM j_eleves_groupes_delestage) AND
                                    jour_semaine = '".$jour."' AND
                                    id_definie_periode = '".$tab_id_creneaux[$creneau_courant]."' AND
                                    heuredeb_dec = '".$current_heure."' AND
                                    id_cours <> '".$id_cours."' AND
                                    ".$calendrier."
                                    ") or die(mysql_error());
        }
        else {      //AID
            $req_creneau = mysql_query("SELECT duree , login_prof , id_groupe FROM edt_cours WHERE 
                                    id_groupe IN (SELECT id_groupe FROM j_eleves_groupes WHERE 
                                                                login IN (SELECT login FROM j_aid_eleves WHERE
                                                                        id_aid = '".$groupe."')) AND
                                    jour_semaine = '".$jour."' AND
                                    id_definie_periode = '".$tab_id_creneaux[$creneau_courant]."' AND
                                    heuredeb_dec = '".$current_heure."' AND
                                    id_cours <> '".$id_cours."' AND
                                    $calendrier
                                    ") or die(mysql_error());
        }
    }
    else {      
        if ($groupe_type == "ENS") {
            $req_creneau = mysql_query("SELECT duree , login_prof , id_groupe FROM edt_cours WHERE 
                                    id_groupe IN (SELECT id_groupe FROM j_eleves_groupes_delestage) AND
                                    jour_semaine = '".$jour."' AND
                                    id_definie_periode = '".$tab_id_creneaux[$creneau_courant]."' AND
                                    heuredeb_dec = '".$current_heure."' AND
                                    (id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
                                    id_cours <> '".$id_cours."' AND
                                    $calendrier
                                    ") or die(mysql_error());
        }
        else {
            $req_creneau = mysql_query("SELECT duree , login_prof , id_groupe FROM edt_cours WHERE 
                                    id_groupe IN (SELECT id_groupe FROM j_eleves_groupes WHERE 
                                                                login IN (SELECT login FROM j_aid_eleves WHERE
                                                                        id_aid = '".$groupe."')) AND
                                    jour_semaine = '".$jour."' AND
                                    id_definie_periode = '".$tab_id_creneaux[$creneau_courant]."' AND
                                    heuredeb_dec = '".$current_heure."' AND
                                    (id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
                                    id_cours <> '".$id_cours."' AND
                                    $calendrier
                                    ") or die(mysql_error());
        }
    }

    while ($rep_creneau = mysql_fetch_array($req_creneau)) {
        $tab_enseignement['duree'][$k] = $rep_creneau['duree'];
        $tab_enseignement['login'][$k] = $rep_creneau['login_prof'];
        $tab_enseignement['id_groupe'][$k] = $rep_creneau['id_groupe'];
        $tab_enseignement['id_aid'][$k] = "";
        $tab_enseignement['aid'][$k] = 0;
        $k++;
    }

    // --------- Rechercher les AIDs qui ont des �l�ves en commun avec le groupe vis�

    $req_creneau_aid = mysql_query("SELECT DISTINCT id_aid FROM j_aid_eleves WHERE 
                                                            login IN (SELECT login FROM j_eleves_groupes WHERE
                                                                    id_groupe = '".$groupe."') 
                                ") or die(mysql_error());
    while ($rep_creneau_aid = mysql_fetch_array($req_creneau_aid)) {
        if ($type_semaine == "0") {
            $req_creneau_aid_2 = mysql_query("SELECT duree , login_prof , id_groupe , id_aid FROM edt_cours WHERE 
                                    id_aid = '".$rep_creneau_aid['id_aid']."' AND
                                    jour_semaine = '".$jour."' AND
                                    id_definie_periode = '".$tab_id_creneaux[$creneau_courant]."' AND
                                    heuredeb_dec = '".$current_heure."' AND
                                    id_cours <> '".$id_cours."' AND
                                    $calendrier
                                     ");
        }
        else {
            $req_creneau_aid_2 = mysql_query("SELECT duree , login_prof , id_groupe , id_aid FROM edt_cours WHERE 
                                    id_aid = '".$rep_creneau_aid['id_aid']."' AND
                                    jour_semaine = '".$jour."' AND
                                    id_definie_periode = '".$tab_id_creneaux[$creneau_courant]."' AND
                                    heuredeb_dec = '".$current_heure."' AND
                                    (id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
                                    id_cours <> '".$id_cours."' AND
                                    $calendrier
                                     ");
        }
        if (mysql_num_rows($req_creneau_aid_2) != 0) {

            $rep_creneau_aid_2 = mysql_fetch_array($req_creneau_aid_2);
            $tab_enseignement['duree'][$k] = $rep_creneau_aid_2['duree'];
            $tab_enseignement['login'][$k] = $rep_creneau_aid_2['login_prof'];
            $tab_enseignement['id_aid'][$k] = $rep_creneau_aid_2['id_aid'];
            $tab_enseignement['id_groupe'][$k] = "";
            $tab_enseignement['aid'][$k] = 1;
            $k++;
        }
    }
    $tab_enseignement['id_groupe'][$k] = "";
    $tab_enseignement['id_aid'][$k] = "";	
    return $k;

}
// =============================================================================
//
//
// =============================================================================

function RecupCoursSallesCommunes($creneau_courant, $jour, $salle, $current_heure, $type_semaine, $id_cours, &$tab_enseignement, $period)
{
    $tab_id_creneaux = retourne_id_creneaux();

    $k = 0;

    if (($period != NULL) AND ($period != '0')) {
        $calendrier = "(id_calendrier = '".$period."' OR id_calendrier = '0')";
    }
    else {
        $calendrier = "1=1";
    }
    // --------- Rechercher les groupes d'enseignement qui ont la salle en commun avec le groupe vis�
    if ($type_semaine == "0") {
            $req_creneau = mysql_query("SELECT duree , login_prof , id_groupe, id_aid, id_semaine FROM edt_cours WHERE 
                                    id_salle = '".$salle."'  AND
                                    jour_semaine = '".$jour."' AND
                                    id_definie_periode = '".$tab_id_creneaux[$creneau_courant]."' AND
                                    heuredeb_dec = '".$current_heure."' AND
                                    id_cours <> '".$id_cours."' AND
                                    ".$calendrier."
                                    ") or die(mysql_error());            

    }
    else {

            $req_creneau = mysql_query("SELECT duree , login_prof , id_groupe, id_aid, id_semaine FROM edt_cours WHERE 
                                    id_salle = '".$salle."'  AND
                                    jour_semaine = '".$jour."' AND
                                    id_definie_periode = '".$tab_id_creneaux[$creneau_courant]."' AND
                                    heuredeb_dec = '".$current_heure."' AND
                                    (id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
                                    id_cours <> '".$id_cours."' AND
                                    ".$calendrier."
                                    ") or die(mysql_error());          

    }
    while ($rep_creneau = mysql_fetch_array($req_creneau)) {
        $tab_enseignement['duree'][$k] = $rep_creneau['duree'];
        $tab_enseignement['login'][$k] = $rep_creneau['login_prof'];
        $tab_enseignement['id_semaine'][$k] = $rep_creneau['id_semaine'];
        if ($rep_creneau['id_aid'] != "") {
            $tab_enseignement['id_groupe'][$k] = "";
            $tab_enseignement['id_aid'][$k] = $rep_creneau['id_aid'];
            $tab_enseignement['aid'][$k] = 1;
        }
        else {
            $tab_enseignement['id_groupe'][$k] = $rep_creneau['id_groupe'];
            $tab_enseignement['id_aid'][$k] = "";
            $tab_enseignement['aid'][$k] = 0;
        }

        $k++;
    }

    $tab_enseignement['id_groupe'][$k] = "";
    $tab_enseignement['id_aid'][$k] = "";	
    return $k;

}

// =============================================================================
//
//
// =============================================================================

function RecupCoursProf($creneau_courant, $jour, $login_prof, $current_heure, $type_semaine, $id_cours, &$tab_enseignement, $period)
{
    $tab_id_creneaux = retourne_id_creneaux();

    $k = 0;
    // --------- Rechercher les groupes d'enseignement qui ont le prof sp�cifi�
    if (($period != NULL) AND ($period != 0)) {
        $calendrier = "(id_calendrier = '".$period."' OR id_calendrier = '0')";
    }
    else {
        $calendrier = "1=1";
    }

    if ($type_semaine == "0") {
        $req_creneau = mysql_query("SELECT duree , login_prof , id_groupe, id_aid, id_semaine FROM edt_cours WHERE 
                                login_prof = '".$login_prof."'  AND
                                jour_semaine = '".$jour."' AND
                                id_definie_periode = '".$tab_id_creneaux[$creneau_courant]."' AND
                                heuredeb_dec = '".$current_heure."' AND
                                id_cours <> '".$id_cours."' AND
                                ".$calendrier."
                                ") or die(mysql_error());
    }
    else {
        $req_creneau = mysql_query("SELECT duree , login_prof , id_groupe, id_aid, id_semaine FROM edt_cours WHERE 
                                login_prof = '".$login_prof."'  AND
                                jour_semaine = '".$jour."' AND
                                id_definie_periode = '".$tab_id_creneaux[$creneau_courant]."' AND
                                heuredeb_dec = '".$current_heure."' AND
                                (id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
                                id_cours <> '".$id_cours."' AND
                                ".$calendrier."
                                ") or die(mysql_error());
    }
    while ($rep_creneau = mysql_fetch_array($req_creneau)) {
        $tab_enseignement['duree'][$k] = $rep_creneau['duree'];
        $tab_enseignement['login'][$k] = $rep_creneau['login_prof'];
        $tab_enseignement['id_semaine'][$k] = $rep_creneau['id_semaine'];
        if ($rep_creneau['id_aid'] != NULL) {
            $tab_enseignement['id_groupe'][$k] = "";
            $tab_enseignement['id_aid'][$k] = $rep_creneau['id_aid'];
            $tab_enseignement['aid'][$k] = 1;
        }
        else {
            $tab_enseignement['id_groupe'][$k] = $rep_creneau['id_groupe'];
            $tab_enseignement['id_aid'][$k] = "";
            $tab_enseignement['aid'][$k] = 0;
        }

        $k++;
    }

    $tab_enseignement['id_groupe'][$k] = "";
    $tab_enseignement['id_aid'][$k] = "";	
    return $k;

}


// Fonction qui renvoie l'id du cr�neau suivant de celui qui est appel�
function creneauSuivant($creneau){
	$cherche_creneaux = array();
	$cherche_creneaux = retourne_id_creneaux();
	$ch_index = array_search($creneau, $cherche_creneaux);
	if (isset($cherche_creneaux[$ch_index+1])) {
		$reponse = $cherche_creneaux[$ch_index+1];
	}else{
		$reponse = "aucun";
	}
	return $reponse;
} // creneauSuivant()

// Fonction qui renvoie l'id du cr�neau pr�c�dent de celui qui est appel�
function creneauPrecedent($creneau){
	$cherche_creneaux = array();
	$cherche_creneaux = retourne_id_creneaux();
	$ch_index = array_search($creneau, $cherche_creneaux);
	if (isset($cherche_creneaux[$ch_index-1])) {
		$reponse = $cherche_creneaux[$ch_index-1];
	}else{
		$reponse = "aucun";
	}
	return $reponse;
} // creneauPrecedent()

// Fonction qui renvoie le nombre de cr�neaux pr�c�dents celui qui est appel�
function nombreCreneauxPrecedent($creneau){
	// On r�cup�re l'heure du creneau appel�
	$heure_creneau_appele = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM edt_creneaux WHERE id_definie_periode = '".$creneau."'"));
	$requete = mysql_query("SELECT id_definie_periode FROM edt_creneaux WHERE
						heuredebut_definie_periode < '".$heure_creneau_appele["heuredebut_definie_periode"]."' AND
						type_creneaux != 'pause'
						ORDER BY heuredebut_definie_periode");
	$nbre = mysql_num_rows($requete);

	return $nbre;
} // nombreCreneauxPrecedent()

// Fonction qui renvoie le nombre de cr�neaux qui suivent celui qui est appel�
function nombreCreneauxApres($creneau){
	// On r�cup�re l'heure du creneau appel�
	$heure_creneau_appele = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM edt_creneaux WHERE id_definie_periode = '".$creneau."'"));
	$requete = mysql_query("SELECT id_definie_periode FROM edt_creneaux WHERE heuredebut_definie_periode > '".$heure_creneau_appele["heuredebut_definie_periode"]."' AND type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	$nbre = mysql_num_rows($requete);

	return $nbre;
} // nombreCreneauxApres()

// Fonction qui renvoie l'inverse de heuredeb_dec
function inverseHeuredeb_dec($heuredeb_dec){
	if ($heuredeb_dec == "0.5") {
		$retour = "0";
	}elseif($heuredeb_dec == "0"){
		$retour = "0.5";
	}else{
		$retour = NULL;
	}

	return $retour;
} // inverseHeuredeb_dec()

/*
 * Fonction qui renvoie le d�but et la fin d'un cours en prenant en compte l'id�e que chaque cr�neau
 * dure 2 "temps". Par exemple, pour un cours qui commence au d�but du 4�me cr�neau de la journ�e et
 * qui dure 2 heures, la fonction renvoie $retour["deb"] = 5 et $retour["fin"] = 8;
 * $jour = le jour de la semaine en toute lettre et en Fran�ais
 * $creneau = id du cr�neau (table edt_creneaux)
 * $heuredeb_dec vaut '0' si le cours commence au d�but d'un cr�neau et '0.5' si le cours commence au milieu du cr�neau
 * $duree = nombre de demi-cours (un cours d'un cr�neau et demi aura donc une dur�e de 3)
*/
function dureeTemps($jour, $creneau, $heuredeb_dec, $duree){
	// On d�termine le "lieu" du d�but du cours
	$deb = 0;
	$fin = 0;
	$c_p = nombreCreneauxPrecedent($creneau);
	// et on calcule de d�but
	if ($c_p == 0) {
		$deb = 0;
	}elseif ($heuredeb_dec == 0) {
		$deb = ($c_p * 2) + 1;
	}else{
		$deb = ($c_p * 2) + 2;
	}
	// puis la fin
	$fin = $deb + $duree - 1;
	$retour = array();
	$retour["deb"] = $deb;
	$retour["fin"] = $fin;

	return $retour;
}

?>