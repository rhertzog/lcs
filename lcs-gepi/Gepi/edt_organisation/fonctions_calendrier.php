<?php

/**
 * @version $Id: fonctions_calendrier.php 1598 2008-03-09 12:43:52Z jjocal $
 *
 * Fichier de fonctions destin�es au calendrier
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

// Fonction qui retourne le type de la semaine en cours
function typeSemaineActu(){
		$retour = '0';
	$numero_sem_actu = date("W");
	$query = mysql_query("SELECT type_edt_semaine FROM edt_semaines WHERE num_edt_semaine = '".$numero_sem_actu."'");

	if (count($query) != 1) {
		$retour = '0';
	}else{
		$type = mysql_result($query, "type_edt_semaine");
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
		$table = 'absences_creneaux_bis';
	}else{
		$table = 'absences_creneaux';
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
		$table = "absences_creneaux_bis";
	}else {
		$table = "absences_creneaux";
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
			id_groupe LIKE 'AID|%' AND
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