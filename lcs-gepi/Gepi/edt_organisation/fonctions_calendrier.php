<?php

/**
 * @version $Id: fonctions_calendrier.php 6267 2011-01-03 21:08:48Z adminpaulbert $
 *
 * Fichier de fonctions destin�es au calendrier
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Pascal Fautrero
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

// ===============================================================================
//
//      Renvoie le num�ro de la derni�re semaine de l'ann�e civile (52 ou 53)
//
// ===============================================================================

function NumLastWeek() {
/* On regarde si on est entre Aout ou d�cembre auquel cas on est en ann�e scolaire AA - AA+1
ou si on est avant auquel cas on est en ann�e scolaire AA-1 - AA
*/
 if (date("m") >= 8) {
     $derniere_semaine=date("W",mktime(0, 0, 0, 12, 28, date("Y")));
 }else{
     $derniere_semaine=date("W",mktime(0, 0, 0, 12, 28, (date("Y")-1)));
 }
 return $derniere_semaine;
} 


// ===================================================
//
//      Affiche le nom de la p�riode courante (si d�finie dans les edt)
//
// ===================================================
function AffichePeriode($date_ts) {
	$req_periode = mysql_query("SELECT * FROM edt_calendrier");
	$endprocess = false;
	while (($rep_periode = mysql_fetch_array($req_periode)) AND (!$endprocess)) {
		if (($rep_periode['debut_calendrier_ts'] <= $date_ts) AND ($rep_periode['fin_calendrier_ts'] >= $date_ts)) { 
			echo $rep_periode['nom_calendrier'];
			$endprocess = true;
		}
	}	
    
}
// ========================================================================
//
//      Affiche les dates du lundi et du samedi de la semaine courante
//
// ========================================================================
function AfficheDatesDebutFinSemaine() {

        $ts = time();
        while (date("D", $ts) != "Mon") {
        $ts-=86400;
        }
        setlocale (LC_TIME, 'fr_FR','fra');
        echo strftime("%d %b ", $ts);
        $ts+=86400*5;
        echo " - ";
        echo strftime("%d %b %Y", $ts);
}


// ========================================================================
//
//
// =========================================================================
function RecupereTimestampJour ($jour) {

    //setlocale (LC_TIME, 'fr_FR','fra');
    if ((1<=$_SESSION['week_selected']) AND ($_SESSION['week_selected'] <= 28)) {
    //if ((1<=date("n")) AND (date("n") <=8)) {
	    $annee = date("Y");
    }
    else {
	    $annee = date("Y");
    }
    $ts = mktime(0,0,0,1,4,$annee); // d�finition ISO de la semaine 01 : semaine du 4 janvier.
    while (date("D", $ts) != "Mon") {
	    $ts-=86400;
    }
    $semaine = 1;
    
    while ($semaine != $_SESSION['week_selected']) {
	    $ts+=86400*7;
	    $semaine++;
    }
	$timestamp = $ts+86400*($jour+0);
    return $timestamp;
}


// ========================================================================
//
//      R�cup�re les dates des lundis et vendredis de toutes les semaines de l'ann�e scolaire courante
//      Usage : 
//      $tab = RecupereLundisVendredis();
//      echo $tab[0]["lundis"];         // renvoie la date du lundi de la semaine 01     
//      echo $tab[5]["vendredis"];      // renvoie la date du vendredi de la semaine 06 
//
// =========================================================================
function RecupereLundisVendredis () {

    $tab_select_semaine = array();
    setlocale (LC_TIME, 'fr_FR','fra');
    
    if ((1<=date("n")) AND (date("n") <=8)) {
	    $annee = date("Y");
    }
    else {
	    $annee = date("Y")+1;
    }
    $ts = mktime(0,0,0,1,4,$annee); // d�finition ISO de la semaine 01 : semaine du 4 janvier.
    while (date("D", $ts) != "Mon") {
	    $ts-=86400;
    }
    $semaine = 1;
    $ts_ref = $ts;
    $tab_select_semaine[$semaine-1]["lundis"] = strftime("%d %b %Y", $ts);
    $tab_select_semaine[$semaine-1]["vendredis"] = strftime("%d %b %Y", $ts+86400*4);
    
    while ($semaine <=30) {
	    $ts+=86400*7;
	    $semaine++;
	    $tab_select_semaine[$semaine-1]["lundis"] = strftime("%d %b %Y", $ts);
	    $tab_select_semaine[$semaine-1]["vendredis"] = strftime("%d %b %Y", $ts+86400*4);
    }
    $semaine = NumLastWeek();
    $ts = $ts_ref;
    $ts-=86400*7;
	$tab_select_semaine[$semaine-1]["lundis"] = strftime("%d %b %Y", $ts);
	$tab_select_semaine[$semaine-1]["vendredis"] = strftime("%d %b %Y", $ts+86400*4);
    while ($semaine >=33) {
	    $ts-=86400*7;
	    $semaine--;
	    $tab_select_semaine[$semaine-1]["lundis"] = strftime("%d %b %Y", $ts);
	    $tab_select_semaine[$semaine-1]["vendredis"] = strftime("%d %b %Y", $ts+86400*4);
    }
    return $tab_select_semaine;
}

// ========================================================================
//
//      R�cup�re les dates des lundis et vendredis de toutes les semaines de l'ann�e scolaire courante
//      Usage : 
//      $tab = RecupereJoursSemaine();
//      echo $tab[0]["lundis"];         // renvoie la date du lundi de la semaine 01     
//      echo $tab[5]["vendredis"];      // renvoie la date du vendredi de la semaine 06 
//
// =========================================================================
function RecupereJoursSemaine () {

    $tab_select_semaine = array();
    setlocale (LC_TIME, 'fr_FR','fra');
    
    if ((1<=date("n")) AND (date("n") <=8)) {
	    $annee = date("Y");
    }
    else {
	    $annee = date("Y")+1;
    }
    $ts = mktime(0,0,0,1,4,$annee); // d�finition ISO de la semaine 01 : semaine du 4 janvier.
    while (date("D", $ts) != "Mon") {
	    $ts-=86400;
    }
    $semaine = 1;
    $ts_ref = $ts;
    $tab_select_semaine[$semaine-1]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$semaine-1]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$semaine-1]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$semaine-1]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$semaine-1]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$semaine-1]["samedi"] = strftime("%d", $ts+86400*5);
    
    while ($semaine <=30) {
	    $ts+=86400*7;
	    $semaine++;
    $tab_select_semaine[$semaine-1]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$semaine-1]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$semaine-1]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$semaine-1]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$semaine-1]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$semaine-1]["samedi"] = strftime("%d", $ts+86400*5);
    }
    $semaine = NumLastWeek();
    $ts = $ts_ref;
    $ts-=86400*7;
    $tab_select_semaine[$semaine-1]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$semaine-1]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$semaine-1]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$semaine-1]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$semaine-1]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$semaine-1]["samedi"] = strftime("%d", $ts+86400*5);
    while ($semaine >=33) {
	    $ts-=86400*7;
	    $semaine--;
    $tab_select_semaine[$semaine-1]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$semaine-1]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$semaine-1]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$semaine-1]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$semaine-1]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$semaine-1]["samedi"] = strftime("%d", $ts+86400*5);
    }
    return $tab_select_semaine;
}

// ===================================================
//
//      Renvoie "true" si des p�riodes sont d�finies
//
// ===================================================
function PeriodesExistent() {
	$req_periode = mysql_query("SELECT * FROM edt_calendrier");
    if (mysql_num_rows($req_periode) > 0) {
        $retour = true;
    }
    else {
        $retour = false;
    }
    return $retour;
}
// ===================================================
//
//      Renvoie "true" si la p�riode sp�cifi�e existe
//
// ===================================================
function PeriodExistsInDB($period) {
	$req_periode = mysql_query("SELECT id_calendrier FROM edt_calendrier WHERE id_calendrier='".$period."' ");
    if (mysql_num_rows($req_periode) > 0) {
        $retour = true;
    }
    else {
        $retour = false;
    }
    return $retour;
}
// ===================================================
//
//
//
// ===================================================
function ReturnFirstIdPeriod() {
	$req_periode = mysql_query("SELECT id_calendrier FROM edt_calendrier");
    $retour = 0;
	if ($rep_periode = mysql_fetch_array($req_periode)) {
    	$retour = $rep_periode['id_calendrier'];
	}
    return $retour;    
}

// ===================================================
//
//      Renvoie l'id de la p�riode courante
//
// ===================================================
function ReturnIdPeriod($date_ts) {
	$req_periode = mysql_query("SELECT * FROM edt_calendrier");
	$endprocess = false;
    $retour = 0;
	while (($rep_periode = mysql_fetch_array($req_periode)) AND (!$endprocess)) {
		if (($rep_periode['debut_calendrier_ts'] < $date_ts) AND ($rep_periode['fin_calendrier_ts'] > $date_ts)) { 
			$retour = $rep_periode['id_calendrier'];
			$endprocess = true;
		}
	}
    return $retour;    
}

// ===================================================
//
//      Renvoie l'id de la p�riode suivant celle pass�e en argument
//
// ===================================================
function ReturnNextIdPeriod($current_id_period) {
	$req_periode = mysql_query("SELECT * FROM edt_calendrier ORDER BY debut_calendrier_ts ASC");
	$endprocess = false;
    $retour = ReturnIdPeriod(date("U"));
	while (($rep_periode = mysql_fetch_array($req_periode)) AND (!$endprocess)) {
		if ($rep_periode['id_calendrier'] == $current_id_period) { 
			$endprocess = true;
            if ($rep_periode = mysql_fetch_array($req_periode)) {
                $retour = $rep_periode['id_calendrier'];
            }
            else {
                mysql_data_seek($req_periode,0);
                $rep_periode = mysql_fetch_array($req_periode);
                $retour = $rep_periode['id_calendrier'];
            }
		}
	}
    return $retour;    
}

// ===================================================
//
//
//
// ===================================================
function ReturnPreviousIdPeriod($current_id_period) {
	$req_periode = mysql_query("SELECT * FROM edt_calendrier ORDER BY debut_calendrier_ts DESC");
	$endprocess = false;
    $retour = ReturnIdPeriod(date("U"));
	while (($rep_periode = mysql_fetch_array($req_periode)) AND (!$endprocess)) {
		if ($rep_periode['id_calendrier'] == $current_id_period) { 
			$endprocess = true;
            if ($rep_periode = mysql_fetch_array($req_periode)) {
                $retour = $rep_periode['id_calendrier'];
            }
            else {
                mysql_data_seek($req_periode,0);
                $rep_periode = mysql_fetch_array($req_periode);
                $retour = $rep_periode['id_calendrier'];
            }
		}
	}
    return $retour;    
}

// Fonction qui retourne le type de la semaine en cours
function typeSemaineActu(){
		$retour = '0';
	$numero_sem_actu = date("W");
	$query = mysql_query("SELECT type_edt_semaine FROM edt_semaines WHERE num_edt_semaine = '".$numero_sem_actu."'");

	if (count($query) != 1) {
		$retour = '0';
	}else{
		$type = mysql_result($query, 0);
		$retour = $type;
	}
	return $retour;
}

// Fonction qui retourne le jour actu en fran�ais et en toutes lettres
function retourneJour($jour){
	if ($jour === "") {
		$jour = date("w");
	}
	// On traduit le nom du jour
	$semaine = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
			$jour_semaine = '';
	for($a = 0; $a < 7; $a++) {
		if ($jour == $a) {
			$jour_semaine = $semaine[$a];
		}
	}
	return $jour_semaine;
}

// Fonction qui retourne l'id du cr�neau actuel
function retourneCreneau(){
		$retour = 'non';
	$heure = date("H:i:s");
	// On v�rifie si on est dans un jour diff�rent ou pas
	if (date("w") == getSettingValue("creneau_different")) {
		$table = 'edt_creneaux_bis';
	}else{
		$table = 'edt_creneaux';
	}
	$query = mysql_query("SELECT id_definie_periode FROM ".$table." WHERE
			heuredebut_definie_periode <= '".$heure."' AND
			heurefin_definie_periode > '".$heure."'")
				OR DIE('Le creneau n\'est pas trouv� : '.mysql_error());
	if ($query) {
		$reponse = mysql_fetch_array($query);
		$retour = $reponse["id_definie_periode"];
	}else {
		$retour = "non";
	}
	return $retour;
}

//Fonction qui retourne si on est dans la premi�re ou la seconde partie d'un cr�neau
function heureDeb(){
		$retour = '0';
	// On compare des minutes car c'est plus simple
	$heureMn = (date("H") * 60) + date("i");
	$creneauId = retourneCreneau();
	// On v�rifie si il existe un jour diff�rent et si c'est aujourd'hui
	if (date("w") == getSettingValue("creneau_different")) {
		$table = "edt_creneaux_bis";
	}else {
		$table = "edt_creneaux";
	}
	// On r�cup�re l'heure de d�but et celle de fin du cr�neau
	$query = mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM ".$table." WHERE id_definie_periode = '".$creneauId."'");
	if ($query) {
		$reponse = mysql_fetch_array($query);
		// On enl�ve les secondes
		$explodeDeb = explode(":", $reponse["heuredebut_definie_periode"]);
		$explodeFin = explode(":", $reponse["heurefin_definie_periode"]);
		$dureeCreneau = (($explodeFin[0] - $explodeDeb[0]) * 60) + ($explodeFin[1] - $explodeDeb[1]);
		$miCreneau = $dureeCreneau / 2;
		$heureMilieu = ($explodeDeb[0] * 60) + $explodeDeb[1] + $miCreneau;
		// et on compare
		if ($heureMn > $heureMilieu) {
			$retour = 'O.5';
		}elseif($heureMn < $heureMilieu){
			$retour = '0';
		}else{
			$retour = '0';
		}
	}
	return $retour;
}

// Fonction qui retourne l'id du cours d'un prof � un cr�neau, jour et type_semaine donn�s
function retourneCours($prof){
		$retour = 'non';
	$query = mysql_query("SELECT id_cours FROM edt_cours, j_groupes_professeurs WHERE
			edt_cours.jour_semaine='".retourneJour('')."' AND
			edt_cours.id_definie_periode='".retourneCreneau()."' AND
			edt_cours.id_groupe=j_groupes_professeurs.id_groupe AND
			login='".$prof."' AND
			edt_cours.heuredeb_dec = '0' AND
			(edt_cours.id_semaine = '".typeSemaineActu()."' OR edt_cours.id_semaine = '0')
			ORDER BY edt_cours.id_semaine")
				or die('Erreur : retourneCours(prof) !'.mysql_error());
	$nbreCours = mysql_num_rows($query);
	if ($nbreCours >= 1) {
		$reponse = mysql_fetch_array($query);
		$retour = $reponse["id_cours"];
	}else{
		// On teste les AID
		$query_aid = mysql_query("SELECT id_cours FROM edt_cours WHERE
			jour_semaine = '".retourneJour('')."' AND
			id_definie_periode = '".retourneCreneau()."' AND
			id_aid != NULL AND
            id_aid != '' AND
			login_prof = '".$prof."' AND
			heuredeb_dec = '0' AND
			(id_semaine = '".typeSemaineActu()."' OR id_semaine = '0')
			ORDER BY id_semaine")
				or die('Erreur : retourneCours(prof) !'.mysql_error());
			$nbreCours = mysql_num_rows($query_aid);
		if ($nbreCours >= 1) {
			$reponse = mysql_fetch_array($query_aid);
			$retour = $reponse["id_cours"];
		}
	}
	return $retour;
}

?>