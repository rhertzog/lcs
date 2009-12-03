<?php

/**
 *
 *
 * @version $Id: edt_init_fonctions.php 2041 2008-07-04 14:40:03Z jjocal $
 *
 * Ensemble des fonctions qui renvoient la concordance pour le fichier txt
 * de l'import des EdT.
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, St�phane Boireau, Julien Jocal
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

// le login du prof
function renvoiLoginProf($numero){
	// on cherche dans la base
	$query = mysql_query("SELECT nom_gepi FROM edt_init WHERE nom_export = '".$numero."' AND ident_export = '1'");
	if ($query) {

		$test = mysql_num_rows($query);
		if ($test >= 1) {
			$retour = mysql_result($query, "nom_gepi");
		}else{
			$retour = 'erreur_prof';
		}

	}else{
		$retour = 'erreur_prof';
	}

	return $retour;
}

// la salle
function renvoiIdSalle($chiffre){
	// On cherche l'Id de la salle
		$retour = 'erreur_salle';
	// On ne prend que les 10 premi�res lettres du num�ro ($chiffre)
	$cherche = substr($chiffre, 0, 10);
	$query = mysql_query("SELECT id_salle FROM salle_cours WHERE numero_salle = '".$cherche."'");
	if ($query) {
		//$reponse = mysql_result($query, "id_salle");
		$reponse = mysql_fetch_array($query);
		if ($reponse["id_salle"] == '') {
			$retour = "inc";
		}else{
			$retour = $reponse["id_salle"];
		}
	}else{
		$retour = 'erreur_salle';
	}

	return $retour;
}

// le jour
function renvoiJour($diminutif){
	// Les jours sont de la forme lu, Ma, Je,...
	switch ($diminutif) {
	case 'Lu':
	    $retour = 'lundi';
	    break;
	case 'Ma':
	    $retour = 'mardi';
	    break;
	case 'Me':
	    $retour = 'mercredi';
	    break;
	case 'Je':
	    $retour = 'jeudi';
	    break;
	case 'Ve':
	    $retour = 'vendredi';
	    break;
	case 'Sa':
	    $retour = 'samedi';
	    break;
	case 'Di':
	    $retour = 'dimanche';
	    break;
	default :
		$retour = 'inc';
	}
	return $retour;
}

// renvoie le nom de la bonne table des cr�neaux
function nomTableCreneau($jour){
	$jour_semaine = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
		$numero_jour = NULL;
	for($t = 0; $t < 7; $t++){
		// On cherche � faire correspondre le numero_jour avec ce que donne la fonction php date("w")
		if ($jour == $jour_semaine[$t]) {
			$numero_jour = $t;
		}else{
			// A priori il n'y a rien � faire
		}
	}
	// Ensuite, en fonction du r�sultat, on teste et on renvoie la bonne table des cr�neaux
	if ($numero_jour == getSettingValue("jour_different")) {
		$retour = 'absences_creneaux_bis';
	}else{
		$retour = 'absences_creneaux';
	}

	return $retour;
}
// Id du cr�neau de d�but
function renvoiIdCreneau($heure_brute, $jour){
	// On transforme $heure_brute en un horaire de la forme hh:mm:ss
	$minutes = substr($heure_brute, 2);
	$heures = substr($heure_brute, 0, -2);
	$heuredebut = $heures.':'.$minutes.':00';
	$table = nomTableCreneau($jour);
	$query = mysql_query("SELECT id_definie_periode FROM ".$table." WHERE
					heuredebut_definie_periode <= '".$heuredebut."' AND
					heurefin_definie_periode > '".$heuredebut."'")
						OR DIE('Erreur renvoiIdCreneau : '.mysql_error());
	if ($query) {
		$nbre = mysql_num_rows($query);
		if ($nbre >= 1) {
			$retour = mysql_result($query, "id_definie_periode");
		}else{
			$retour = '0';
		}

	}else{
		$retour = 'erreur_creneau';
	}

	return $retour;
}

// dur�e d'un cr�neau dans Gepi
function dureeCreneau(){
	// On r�cup�re les infos sur un cr�neau
	$creneau = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux LIMIT 1"));
	$deb = $creneau["heuredebut_definie_periode"];
	$fin = $creneau["heurefin_definie_periode"];
	$nombre_mn_deb = (substr($deb, 0, -5) * 60) + (substr($deb, 3, -3));
	$nombre_mn_fin = (substr($fin, 0, -5) * 60) + (substr($fin, 3, -3));
	$retour = $nombre_mn_fin - $nombre_mn_deb;

	return $retour;
}

// La dur�e pour les imports texte
function renvoiDuree($deb, $fin){
	// On d�termine la dur�e d'un cours
	$duree_cours_base = dureeCreneau();
	$nombre_mn_deb = (substr($deb, 0, -2) * 60) + (substr($deb, 2));
	$nombre_mn_fin = (substr($fin, 0, -2) * 60) + (substr($fin, 2));
	$duree_mn = $nombre_mn_fin - $nombre_mn_deb;
	// le nombre d'heures enti�res
	$nbre = $duree_mn / $duree_cours_base;
	settype($nbre, 'integer');
	// le nombre de minutes qui restent
	$mod = $duree_mn % $duree_cours_base;
	// Et on analyse ce dernier (attention, la dur�e se compte en demi-cr�neaux)
	if ($mod >= (($duree_cours_base * 2) / 3)) {
		// Si c'est sup�rieur au 2/3 de la dur�e du cours, alors c'est une heure enti�re
		$retour = ($nbre * 2) + 2;
	}elseif($mod > (($duree_cours_base) / 3)) {
		// Si c'est sup�rieur au tiers de la dur�e d'un cours, alors c'est un demi-cr�neau de plus
		$retour = ($nbre * 2) + 1;
	}else{
		// sinon, c'est un souci de quelques minutes sans importance
		$retour = $nbre * 2;
	}

	return $retour;
}

// Heure debut decal�e ou pas
function renvoiDebut($id_creneau, $heure_deb, $jour){
	// On d�termine la dur�e d'un cours
	$duree_cours_base = dureeCreneau();
	// nbre de mn de l'heure de l'import
	$nombre_mn_deb = (substr($heure_deb, 0, -2) * 60) + (substr($heure_deb, 2));
	// Nombre de mn de l'horaire de Gepi
	$table = nomTableCreneau($jour);
	if ($id_creneau != '') {
			$heure = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM ".$table." WHERE id_definie_periode = '".$id_creneau."'"));
		$decompose = explode(":", $heure["heuredebut_definie_periode"]);
		$nbre_mn_gepi = ($decompose[0] * 60) + $decompose[1];
		// On fait la diff�rence entre les deux horaires qui ont �t� convertis en nombre de minutes
		$diff = $nombre_mn_deb - $nbre_mn_gepi;
		// et on analyse cette diff�rence
		if ($diff === 0 OR $diff < ($duree_cours_base / 4)) {
			$retour = '0';
		}elseif($diff > ($duree_cours_base / 3) AND $diff < (($duree_cours_base / 3) * 2)){
			$retour = '0.5';
		}else{
			$retour = '0';
		}
	}else{
		// par d�faut, on renvoie un d�but classique
		$retour = '0';
	}

	return $retour;
}

// Renvoi des concordances
function renvoiConcordances($chiffre, $etape){
	// On r�cup�re dans la table edt_init la bonne concordance
	// 2=Classe 3=GROUPE 4=PARTIE 5=Mati�res pour IndexEducation
	// 1=cr�neaux 2=classe 3=mati�re 4=professeurs 7=regroupements 10=fr�quence pour UDT de OMT
	if ($chiffre != '') {
		$sql = "SELECT nom_gepi FROM edt_init WHERE
								(nom_export = '".$chiffre."' OR nom_export = '".remplace_accents($chiffre, 'all_nospace')."')
								AND ident_export = '".$etape."'";
		$query = mysql_query($sql);
	}else{
		$query = NULL;
	}

	if ($query) {
		$test = mysql_num_rows($query);
		if ($test >= 1) {
			$reponse = mysql_fetch_array($query)
				OR trigger_error('Erreur dans le $reponse pour le '.$chiffre.'<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> sur la requ�te '.$sql, E_USER_WARNING);
		}else{
			$reponse["nom_gepi"] = '';
		}

		if ($reponse["nom_gepi"] == '') {
			$retour = "inc";
		}else{
			$retour = $reponse["nom_gepi"];
		}
	}else{
		$retour = "erreur";
	}

	return $retour;
}

// L'id_groupe
function renvoiIdGroupe($prof, $classe_txt, $matiere_txt, $grp_txt, $partie_txt, $type_import){
	// $prof est le login du prof tel qu'il existe dans Gepi, alors que les autres infos ne sont pas encore "concord�s"

	if ($type_import == 'texte') {
		// On se pr�occupe de la partie qui arrive de edt_init_texte.php et edt_init_concordance.php
		// Les autres variables sont explicites dans leur d�signation (c'est leur nom dans l'export texte)
		$classe = renvoiConcordances($classe_txt, 2);
		$matiere = renvoiConcordances($matiere_txt, 5);
		$partie = $partie_txt; //renvoiConcordances($partie_txt, 4);
		$grp = renvoiConcordances($grp_txt, 3);
		//echo $classe.'|'.$matiere.'|'.$prof.'&nbsp;&nbsp;->&nbsp;&nbsp;';
	}elseif($type_import == 'csv2'){
		// On se pr�occupe de la partie csv2 venant de edt_init_csv2.php et edt_init_concordance2.php
		$classe = $classe_txt;
		$matiere = $matiere_txt;
		$partie = '';
		$grp = $grp_txt;
	}else{
		$classe = '';
		$matiere = '';
		$partie = '';
		$grp = '';
	}


	// On commence par le groupe. S'il existe, on le renvoie tout de suite
	if($type_import == 'texte'
		AND $grp != "erreur"
		AND $grp != "aucun"
		AND $grp != ''
		AND $grp != "inc"){

		return $grp;

	}elseif ($grp != "aucun"
		AND $grp != ''
		AND $grp != "inc"
		AND $type_import != 'texte') {

		return $grp;

	}else{
		// On r�cup�re la classe, la mati�re et le professeur
		// et on cherche un enseignement qui pourrait correspondre avec
		$req_groupe = mysql_query("SELECT jgp.id_groupe FROM j_groupes_professeurs jgp, j_groupes_classes jgc, j_groupes_matieres jgm WHERE
						jgp.login = '".$prof."' AND
						jgc.id_classe = '".$classe."' AND
						jgm.id_matiere = '".$matiere."' AND
						jgp.id_groupe = jgc.id_groupe AND
						jgp.id_groupe = jgm.id_groupe");

    	$rep_groupe = mysql_fetch_array($req_groupe);
			//print_r($rep_groupe);
			//echo '<br />';
    	$nbre_rep = mysql_num_rows($req_groupe);
    	// On v�rifie ce qu'il y a dans la r�ponse
    	if ($nbre_rep == 0) {
			$retour = "aucun";
		} elseif ($nbre_rep > 1) {
    		$retour = "plusieurs";
    	}else{
			$retour = $rep_groupe["id_groupe"];
		}

	} // fin du else

	return $retour;
}

/*
 * Fonction qui teste si une salle existe dans Gepi et qui l'enregistre si elle n'existe pas
 * $numero est le num�ro de la salle
*/
function testerSalleCsv2($numero){
	// On teste la table
	$query = mysql_query("SELECT id_salle FROM salle_cours WHERE numero_salle = '".$numero."'")
				OR trigger_error('Erreur dans la requ�te '.$query.' : '.mysql_error());
	$rep = @mysql_result($query, "id_salle");
	if ($rep != '' AND $rep != NULL AND $rep != FALSE) {
		// On renvoie "ok"
		return "ok";
	}else{
		// On enregistre la nouvelle salle
		$query2 = mysql_query("INSERT INTO salle_cours SET numero_salle = '".$numero."', nom_salle = ''");
		if ($query2) {
			return "enregistree";
		}
	}
}

/*
 * Fonction qui teste si une salle existe dans Gepi
 * $numero est le num�ro de la salle
*/
function salleifexists($numero){
	// On teste la table
	$sql = "SELECT id_salle FROM salle_cours WHERE numero_salle = '".$numero."'";
	$query = mysql_query($sql)
				OR trigger_error('Impossible de v�rifier l\'existence de cette salle : la requ�te '.$sql.' a �chou� : '.mysql_error(), E_USER_WARNING);
	// On force tout de m�me le r�sultat
	$rep = @mysql_result($query, "id_salle");
	if ($rep != '' AND $rep != NULL AND $rep != FALSE) {
		// On renvoie "oui"
		return "oui";
	}else{
		return "non";
	}
}


/*
 * Fonction qui fonction renvoie l'id du cr�neau de d�part, la dur�e et le moment du d�but du cours (CSV2)
 * sous la forme d'un tableau id_creneau, duree et debut
*/
function rechercheCreneauCsv2($creneau){
	$duree_base = dureeCreneau();

	// On fait attention � la construction de ce cr�neau
	$test1 = explode(" - ", $creneau);

	// Pour le id du creneau
	$id_creneau = renvoiConcordances($creneau, 1);
	if ($id_creneau != 'inc') {
		$retour["id_creneau"] = $id_creneau;
	}else{
		// Il faut chercher d'une autre fa�on le bon id de cours avec $test1[0]
		$test2 = explode("h", $test1[0]); // $test2[0] = 8 et $test2[1] = 00
		if (strlen($test2[0]) < 2) {
			// On ajoute un '0' devant l'heure
			$heure = '0'.$test2[0];
		}else{
			$heure = $test2[0];
		}
		$heure_reconstruite = $heure.':'.$test2[1].':'.'00';
		$query = mysql_query("SELECT DISTINCT id_definie_periode FROM absences_creneaux
						WHERE heuredebut_definie_periode <= '".$heure_reconstruite."'
						ORDER BY heuredebut_definie_periode ASC LIMIT 1");
		if ($query) {
			// On a trouv�
			$reponse_id = mysql_fetch_array($query);
			if ($reponse_id["id_definie_periode"] != '') {
				$retour["id_creneau"] = $id_creneau = $reponse_id["id_definie_periode"];
			}else{
				// Si on n'a pas de r�ponse valide, on ne peut pas d�finir le cours
				return 'erreur';
			}
		}
	}

	// la dur�e et le d�but
	if (isset($test1[1])) {
		// �a veut dire que le cr�neau �tudi� est de la forme 8h00 - 9h35 : $test1[0] = 8h00 et $test1(1] = 9h00
		// on recherche si le d�but est bon ou pas pour savoir si le cours commence au d�but du cr�neau ou pas
		$heure_debut = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$id_creneau."'"));
		$test3 = explode(":", $heure_debut["heuredebut_definie_periode"]);
		if (substr($test3[0], 0, -1) == "0") {
			$heu = substr($test3[0], -1);
		}else{
			$heu = $test3[0];
		}

		// On d�finit le moment de d�but du cours
		if (($heu.'h'.$test3[1]) == $test1[0]) {
			// Le cours commence au d�but du cr�neau
			$retour["debut"] = '0';
		}else{
			// Le cours commence au milieu du cr�neau
			$retour["debut"] = 'O.5';
		}

		// On d�finit la dur�e
		$he0 = explode("h", $test1[0]); // l'heure de d�but de la demande
		$he1 = explode("h", $test1[1]); // l'heure de fin de la demande
		if (!isset($he0[1])) { $he0[1] = '00';	}
		if (!isset($he1[1])) { $he1[1] = '00';	}
		$duree_demandee = (60 * ($he1[0] - $he0[0])) + ($he1[1] - $he0[1]);
		if ($duree_demandee == $duree_base) {
			// ALors la dur�e est de 1 cr�neau donc 2 pour Gepi
			$retour["duree"] = 2;
		}elseif($duree_demandee < $duree_base){
			// Alors le cours la moiti� d'un cr�neau
			$retour["duree"] = 1;
		}else{
			// Le cours dure plus de 1 cr�neau
			// On d�termine la dur�e exacte
			$test_duree = $duree_demandee / $duree_base;
			// On r�cup�re le nombre de cr�neaux entiers
			$nbre_t = explode(".", $test_duree); // $nbre_t[0] est donc le nombre cr�neaux entiers
			if (isset($nbre_t[1])) {
				$test2 = substr($nbre_t[1], 0, 1); // on ne garde que le premier chiffre apr�s la virgule
			}else{
				$test2 = 0;
			}

			if ($test2 < 3) {
				// c'est fini
				$retour["duree"] = $nbre_t[0] * 2;
			}elseif($test2 > 7){
				// On ajoute 1 cr�neau entier en plus
				$retour["duree"] = ($nbre_t[0] * 2) + 2;
			}else{
				// On ajoute un demi cr�neau en plus
				$retour["duree"] = ($nbre_t[0] * 2) + 1;
			}

		}

	}else{
		// �a veut dire que le cours commence au d�but du cr�neau et dure 1 cr�neau (donc 2 pour Gepi)
		$retour["duree"] = '2';
		$retour["debut"] = '0';
	}
	return $retour;
}

/*
 * Fonction qui enregistre les cours des imports UDT de OMT
*/
function enregistreCoursCsv2($jour, $creneau, $classe, $matiere, $prof, $salle, $groupe, $regroupement, $effectif, $modalite, $frequence, $aire){
	$retour["msg_erreur"] = '';
	// Les �tapes vont de 0 � 11 en suivant l'ordre des variables ci-dessus
	// Si un cours est enregistr�, on renvoie 'oui', sinon on renvoie 'non'

	// le jour => il est bon, il faut juste l'�crire en minuscule
	$jour_e = strtolower($jour);
	// Cette fonction renvoie l'id du cr�neau de d�part, la dur�e et le moment du d�but du cours
	$test_creneau = rechercheCreneauCsv2($creneau);
	$creneau_e = $test_creneau["id_creneau"];
	$duree_e = $test_creneau["duree"];
	$heuredeb_dec = $test_creneau["debut"];
	// On r�cup�re les concordances
	$classe_e = renvoiConcordances($classe, 2);
	$matiere_e = renvoiConcordances($matiere, 3);
	$prof_e = renvoiConcordances($prof, 4);
	$salle_e = renvoiIdSalle($salle); // on peut se le permettre puisque le travail sur les salles a d�j� �t� effectu�
	$type_semaine = renvoiConcordances($frequence, 10);
	if ($type_semaine == '' OR $type_semaine == 'erreur') {
		$type_semaine = '0';
	}

	// Il reste � d�terminer le groupe
	if ($regroupement != '') {

		$test = explode("|", $regroupement);

		if ($test[0] == 'EDT') {

			$groupe_e = $regroupement;

		}else{

			$groupe_e = renvoiConcordances($regroupement, 7);

			if ($groupe_e == 'erreur') {
				$regrp = '';
				$groupe_e = renvoiIdGroupe($prof_e, $classe_e, $matiere_e, $regrp, $groupe, 'csv2');
			}

		}

	}else{
		// On recherche le groupe
		$regrp = '';
		$groupe_e = renvoiIdGroupe($prof_e, $classe_e, $matiere_e, $regrp, $groupe, 'csv2');
	}

	// On v�rifie si tous les champs importants sont pr�cis�s ou non
	if ($jour_e == '' OR $creneau_e == 'erreur'
		OR $groupe_e == 'aucun' OR $groupe_e == 'plusieurs' OR $groupe_e == 'erreur'
		OR $matiere_e == 'inc' OR $classe_e == 'inc'
		OR $prof_e == 'inc' OR $prof_e == 'erreur' OR $prof_e == 'aucun') {

		// Il manque des informations
		$retour["reponse"] = 'non';
		$retour["msg_erreur"] .= $jour_e.'|'.$creneau_e.'|'.$groupe_e.'|'.$matiere_e.'|'.$classe_e.'|'.$prof_e;

	}else{
		// On v�rifie que cette ligne n'existe pas d�j�
		// On ne tient pas compte du type de semaine car on estime que si un enseignement a lieu sur deux types de semaines, c'est qu'il a lieu toutes les semaines
		$ifexists = mysql_query("SELECT id_cours FROM edt_cours WHERE
							id_groupe = '".$groupe_e."' AND
							id_salle = '".$salle_e."' AND
							jour_semaine = '".$jour_e."' AND
							id_definie_periode = '".$creneau_e."' AND
							duree = '".$duree_e."' AND
							heuredeb_dec = '".$heuredeb_dec."' AND
							id_calendrier = '0' AND
							modif_edt = '0' AND
							login_prof = '".$prof_e."'")
							OR DIE('erreur dans la requ�te '.$ifexists.' : '.mysql_error());

		$erreur_report = mysql_fetch_array($ifexists);
		$retour["msg_erreur"] .= 'Ce cours existe d�j� ('.$erreur_report["id_cours"].').';

		if (mysql_num_rows($ifexists) < 1) {
			// On enregistre la ligne
			$sql = "INSERT INTO `edt_cours` (`id_cours`,
										`id_groupe`,
										`id_salle`,
										`jour_semaine`,
										`id_definie_periode`,
										`duree`,
										`heuredeb_dec`,
										`id_semaine`,
										`id_calendrier`,
										`modif_edt`,
										`login_prof`)
								VALUES ('',
										'".$groupe_e."',
										'".$salle_e."',
										'".$jour_e."',
										'".$creneau_e."',
										'".$duree_e."',
										'".$heuredeb_dec."',
										'".$type_semaine."',
										'0',
										'0',
										'".$prof_e."')";

			$retour["msg_erreur"] .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;'.$sql;

			$envoi = mysql_query($sql) OR DIE('Erreur dans la requ�te '.$sql);
			if ($envoi) {
				// et on renvoie 'ok'
				$retour["reponse"] = 'ok';
			}else{
				$retour["reponse"] = 'non';
			}
		}else{
			$retour["reponse"] = 'non';
		}
	}
	return $retour;
}

// fonction qui permet de v�rifier si on doit / peut cr�er un edt_gr pour les emplois de temps
function gestion_edt_gr($tab){

	$retour = '';
// On va regarder si on peut cr�er un edt_gr avec pour nom $tab[7] et pour nom long $tab[3]
if ($tab[4] != '') {
	// le professeur est pr�cis�, donc il s'agit d'un cours
	if ($tab[8] == 'CG') {

		$type_sub = 'classe';
		$subdivision = renvoiConcordances($tab[2], 2);
		$nom = $tab[7];

	}elseif($tab[8] == 'TP' OR $tab[8] == 'TD'){

		$type_sub = 'demi';
		$subdivision = renvoiConcordances($tab[2], 2);
		// On v�rifie que les regroupements soient bien pr�cis� sinon, c'est le groupe qui est choisi
		if ($tab[7] != '') {
			$nom = $tab[7];
		}else{
			$nom = $tab[6];
		}

	}else{
		$type_sub = 'autre';
		$subdivision = 'plusieurs';
		$nom = $tab[7];
	}

	$nom_long = $tab[3];

	// On v�rifie si ce edt_gr n'existe pas d�j�... s'il existe, on pr�cise que le type de subdivision passe � 'autre'
	// et on passe subdivision � 'plusieurs'
	$query_verif = mysql_query("SELECT id FROM edt_gr_nom
										WHERE nom = '".$nom."'
										AND nom_long = '".$nom_long."'
										AND (subdivision_type = '".$type_sub."' OR subdivision_type = 'autre')");
	$nbre = mysql_num_rows($query_verif);

	if ($nbre >= 1) {

		// alors il existe d�j�, on le met � jour et on s'en va
		//$rep_id = mysql_result($query_verif, "id");
		$rep_id = mysql_fetch_array($query_verif);
		$maj = mysql_query("UPDATE edt_gr_nom SET subdivision_type = 'autre', subdivision = 'plusieurs' WHERE id = '".$rep_id["id"]."'");

		$retour = $rep_id["id"];

	}else{

		// on cr�e cet edt_gr
		$query_create = mysql_query("INSERT INTO edt_gr_nom (id, nom, nom_long, subdivision_type, subdivision)
												VALUES ('', '".$nom."', '".$nom_long."', '".$type_sub."', '".$subdivision."')");
		// On r�cup�re son id
		$query_id = mysql_query("SELECT id FROM edt_gr_nom
												WHERE nom = '".$nom."'
												AND nom_long = '".$nom_long."'
												AND subdivision_type = '".$type_sub."'
												AND subdivision = '".$subdivision."");
		//$recup_id = mysql_result($query_id, "id");
		$recup_id = mysql_fetch_array($query_id);
		$create_prof = mysql_query("INSERT INTO edt_gr_prof (id, id_gr_nom, id_utilisateurs)
																				VALUES('', '".$recup_id["id"]."', '".renvoiConcordances($tab[4], 4)."')");
		$retour = $recup_id["id"];

	}

}else{

	// on n'a pas cr�� de edt_gr donc on renvoie 'non
	$retour = 'non';
}

	return $retour;

}
?>