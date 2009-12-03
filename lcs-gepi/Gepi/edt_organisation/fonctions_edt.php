<?php

/**
 * Fonctions pour l'EdT
 *
 * @version $Id: fonctions_edt.php 3245 2009-06-23 20:26:42Z jjocal $
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


// Fonction qui renvoie le nombre de lignes du tableau EdT

function nbre_lignes_tab_edt(){
	$compter_lignes = mysql_query("SELECT nom_definie_periode FROM absences_creneaux");
	$nbre_lignes = (mysql_num_rows($compter_lignes)) + 1;
	return $nbre_lignes;
}


// Fonction qui renvoie le nombre de colonnes du tableau EdT

function nbre_colonnes_tab_edt(){
	//global $compter_colonnes;
	$compter_colonnes = mysql_query("SELECT jour_horaire_etablissement FROM horaires_etablissement");
	$nbre_colonnes = (mysql_num_rows($compter_colonnes)) + 1;
	return $nbre_colonnes;
}


// Fonction qui renvoie la liste des cr�neaux (M1, M2, M3, ...) dans l'ordre de la journ�e

function retourne_creneaux(){

	$req_nom_creneaux_r = mysql_query("SELECT nom_definie_periode FROM absences_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	if ($req_nom_creneaux_r) {
		$rep_creneaux = array();
		while($data_creneaux = mysql_fetch_array($req_nom_creneaux_r)) {
			$rep_creneaux[] = $data_creneaux["nom_definie_periode"];
		}
	}else{
		$rep_creneaux = '';
	}
	return $rep_creneaux;
}

// Fonction qui retourne la liste des horaires 08h00 - 09h00 au lieu des M1 M2 et Cie

function retourne_horaire(){

	$req_nom_horaire = mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");

	if ($req_nom_horaire) {
		$num_nom_horaire = mysql_num_rows($req_nom_horaire);
		$horaire = array();
		for($i=0; $i<$num_nom_horaire; $i++) {
			$horaire1[$i]["heure_debut"] = mysql_result($req_nom_horaire, $i, "heuredebut_definie_periode");
			$exp_hor = explode(":", $horaire1[$i]["heure_debut"]);
			$horaire[$i]["heure_debut"] = $exp_hor[0].":".$exp_hor[1]; // On enl�ve les secondes
			$horaire1[$i]["heure_fin"] = mysql_result($req_nom_horaire, $i, "heurefin_definie_periode");
			$exp_hor = explode(":", $horaire1[$i]["heure_fin"]);
			$horaire[$i]["heure_fin"] = $exp_hor[0].":".$exp_hor[1];
		}
	}else{
		$horaire = '';
	}

	return $horaire;
}


// Fonction qui renvoie la liste des id_creneaux dans l'ordre de la journ�e

function retourne_id_creneaux(){

	$req_id_creneaux = mysql_query("SELECT id_definie_periode FROM absences_creneaux
								WHERE type_creneaux != 'pause'
								ORDER BY heuredebut_definie_periode");
	// On compte alors le nombre de r�ponses et on renvoie en fonction de la r�ponse
	$nbre_rep = count($req_id_creneaux);
	if ($nbre_rep == 0) {
		return "aucun";
	}elseif ($req_id_creneaux) {
		$rep_id_creneaux = array();
		while($data_id_creneaux = mysql_fetch_array($req_id_creneaux)) {
			$rep_id_creneaux[] = $data_id_creneaux["id_definie_periode"];
		}
	}else{
		Die('Erreur sur retourne_id_creneaux 1');
	}
	return $rep_id_creneaux;
}


// Fonction qui renvoie un tableau des r�glages de la table edt-setting

function retourne_setting_edt($reglage_edt){

	$req_edt_set = mysql_query("SELECT valeur FROM edt_setting WHERE reglage ='".$reglage_edt."'");
	$rep_edt_set = mysql_fetch_array($req_edt_set);

	$setting_edt = $rep_edt_set["valeur"];

	return $setting_edt;
}


// Fonction qui renvoie les id_groupe � un horaire donn� (jour_semaine id_definie_periode)

function retourne_ens($jour_semaine, $id_creneaux){

	$req_nom_creneaux = mysql_query("SELECT nom_definie_periode FROM absences_creneaux WHERE id_definie_periode ='".$id_creneaux."'");
	$rep_nom_creneaux = mysql_fetch_array($req_nom_creneaux);
	// On r�cup�re tous les enseignements de l'horaire
	$req_ens = mysql_query("SELECT id_groupe FROM edt_cours WHERE id_definie_periode='".$id_creneaux."' && jour_semaine ='".$jour_semaine."'");

	$result_ens = array();
	while($rep_ens = mysql_fetch_array($req_ens)) {
		$result_ens[] = $rep_ens;
	}
	return $result_ens;
}


// Fonction qui renvoie les enseignements d'un professeur (id_groupe)

function enseignements_prof($login_prof, $rep){

	$req = mysql_query("SELECT id_groupe FROM j_groupes_professeurs WHERE login ='".$login_prof."'");
	$enseignements_prof_num = mysql_num_rows($req);

	if ($rep === 1) {
		// on renvoie alors le nombre d'enseignements
		return $enseignements_prof_num;
	} else {
		$result = array();
		while($enseignements_prof = mysql_fetch_array($req)) {
			// on renvoie alors la liste des enseignements
			$result[] = $enseignements_prof;
		}
		return $result;
	}
}
function semaine_actu(){

		/**
		* On cherche � d�terminer � quel type de semaine se rattache la semaine actuelle
		* Il y a deux possibilit�s : soit l'�tablissement utilise les semaines classiques ISO soit il a d�fini
		* des num�ros sp�ciaux.
 		*/
	global $sem; // permet de modifier les requ�tes si n�cessaire pour avoir les cours sur une semaine donn�e
		//
		$rep = array();

		$semaine = date("W") + ($sem);

		$query_s = mysql_query("SELECT type_edt_semaine FROM edt_semaines WHERE id_edt_semaine = '".$semaine."' LIMIT 1");
		$rep["type"] = mysql_result($query_s, "type_edt_semaine");

		return $rep;
	}
// Fonction g�n�rale qui renvoie un tableau des enseignements � un horaire donn� (heure et jour)

function cree_tab_general($login_general, $id_creneaux, $jour_semaine, $type_edt, $heuredeb_dec){

	global $utilise_type_semaine;
	$sem = semaine_actu();

	if (isset($utilise_type_semaine) AND $utilise_type_semaine = 'y') {
		$requete_sem = "(id_semaine = '0' OR id_semaine = '".$sem["type"]."') AND ";
	}else{
		$requete_sem = NULL;
	}



		$tab_ens = array();
	if ($type_edt == "prof") {
		$req_ens_horaire = mysql_query("SELECT id_groupe FROM edt_cours WHERE
								jour_semaine = '".$jour_semaine."' AND
								id_definie_periode = '".$id_creneaux."' AND
								heuredeb_dec = '".$heuredeb_dec."'AND ".$requete_sem."
								login_prof = '".$login_general."'
								ORDER BY id_semaine")
									or die('Erreur : cree_tab_general(prof) !');

	} elseif ($type_edt == "classe") {
		$req_ens_horaire = mysql_query("SELECT * FROM edt_cours, j_groupes_classes WHERE
								edt_cours.jour_semaine = '".$jour_semaine."' AND
								edt_cours.id_definie_periode = '".$id_creneaux."' AND
								edt_cours.id_groupe = j_groupes_classes.id_groupe AND
								id_classe = '".$login_general."' AND
								edt_cours.heuredeb_dec = '".$heuredeb_dec."'
								ORDER BY edt_cours.id_groupe")
									or die('Erreur : cree_tab_general(classe) : '.mysql_error());
	} elseif ($type_edt == "eleve"){
		$req_ens_horaire = mysql_query("SELECT * FROM edt_cours, j_eleves_groupes WHERE
								edt_cours.jour_semaine = '".$jour_semaine."' AND
								edt_cours.id_definie_periode = '".$id_creneaux."' AND
								edt_cours.id_groupe = j_eleves_groupes.id_groupe AND
								login = '".$login_general."' AND ".$requete_sem."
								edt_cours.heuredeb_dec = '".$heuredeb_dec."'
								ORDER BY edt_cours.id_semaine")
									or die('Erreur : cree_tab_general(eleve) !'.mysql_error());
		// On y ajoute les �ventuelles AID
		$rep_aid_eleve = NULL; // pour �tre certain qu'elle est vide
		$aid = renvoieAid("eleve", $login_general);
		$nbre_aid = count($aid);
		for($a = 0; $a < $nbre_aid; $a++){
			// on va tester la table pour chaque aid de l'�l�ve pour voir si �a donne quelque chose
			$cherche_aid[$a] = mysql_query("SELECT * FROM edt_cours WHERE
								jour_semaine = '".$jour_semaine."' AND
								id_definie_periode = '".$id_creneaux."' AND ".$requete_sem."
								id_groupe = 'AID|".$aid[$a]["id_aid"]."' AND
								heuredeb_dec = '".$heuredeb_dec."'
								ORDER BY id_semaine")
									or die('Erreur : cree_tab_general(eleve AID) : '.msql_error());
			if ($cherche_aid[$a]) {
				$rep_aid = mysql_fetch_array($cherche_aid[$a]);
				if ($rep_aid) {
					// Si c'est concluant, on le atocke dans les cours de l'�l�ve (voir plus bas)
					$rep_aid_eleve[] = $rep_aid["id_groupe"];
				}
			}
		}
	} elseif ($type_edt == "salle") {
		$req_ens_horaire = mysql_query("SELECT * FROM edt_cours WHERE
								edt_cours.jour_semaine='".$jour_semaine."' AND
								edt_cours.id_definie_periode='".$id_creneaux."' AND
								edt_cours.id_salle='".$login_general."' AND
								edt_cours.heuredeb_dec = '".$heuredeb_dec."'
								ORDER BY edt_cours.id_semaine")
									or die('Erreur : cree_tab_general(salle) !');
	} else {
		$req_ens_horaire = "";
	}

	while($data_rep_ens = mysql_fetch_array($req_ens_horaire)) {
		$tab_ens[] = $data_rep_ens["id_groupe"];
	}
	// Si c'est un �l�ve, on v�rifie s'il ne faut pas ajouter les AID
	if ($type_edt == "eleve") {
		// la boucle concernera au maximum le nombre d'AID auxquelles l'�l�ve appratient
		for($a = 0; $a < $nbre_aid; $a++){
			if (isset($rep_aid_eleve[$a])) {
				$tab_ens[] = $rep_aid_eleve[$a];
			}
		}
	}
	return $tab_ens;
}


// Fonction qui renvoie la duree d'un enseignement � un cr�neau et un jour donn� pour renseigner le rollspan

function renvoie_duree($id_creneaux, $jour_semaine, $enseignement){
	$req_duree = mysql_query("SELECT duree FROM edt_cours WHERE jour_semaine = '".$jour_semaine."' AND id_definie_periode = '".$id_creneaux."' AND id_groupe = '".$enseignement."'");
	$rep_duree = mysql_fetch_array($req_duree);
	$reponse_duree = $rep_duree["duree"];

	if ($reponse_duree == 1) {
		$duree = "1";
	}
	elseif ($reponse_duree == 2) {
		$duree = "2";
	}
	elseif ($reponse_duree == 3) {
		$duree = "3";
	}
	elseif ($reponse_duree == 4) {
		$duree = "4";
	}
	elseif ($reponse_duree == 5) {
		$duree = "5";
	}
	elseif ($reponse_duree == 6) {
		$duree = "6";
	}
	elseif ($reponse_duree == 7) {
		$duree = "7";
	}
	elseif ($reponse_duree == 8) {
		$duree = "8";
	}
	elseif ($reponse_duree === 0 OR $reponse_duree == 0) {
		$duree = "n";
	}
	else $duree = "2";

	return $duree;
}

// Fonction qui renvoie l'heure de d�but d'un cours
function renvoie_heuredeb($id_creneaux, $jour_semaine, $enseignement){
	$req_heuredeb = mysql_query("SELECT heuredeb_dec FROM edt_cours WHERE jour_semaine = '".$jour_semaine."' AND id_definie_periode = '".$id_creneaux."' AND id_groupe = '".$enseignement."'");
	$rep_heuredeb = mysql_fetch_array($req_heuredeb);
	$reponse_heuredeb = $rep_heuredeb["heuredeb_dec"];
		// Heure debut = 0 (debut cr�neau) ou 0.5 (milieu cr�neau)
	return $reponse_heuredeb;
}

// Fonction qui associe la classe, le professeur, et la mati�re li�s � un enseignement � une heure et un jour donn�
// Le tout format� pour s'afficher dans une case du tableau EdT

function contenu_enseignement($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $heuredeb_dec){

	// CHercher la dur�e de l'enseignement qui pr�c�de celui qu'on veut afficher
			// Nouvel essai de technique
			$aff_rien = "non";
			$cours_precedent = "non";
	$cherche_creneaux = array();
	$cherche_creneaux = retourne_id_creneaux();
	$ch_index = array_search($id_creneaux, $cherche_creneaux);
		if (isset($cherche_creneaux[$ch_index-1])) {
			$ens_precedent = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-1], $jour_semaine, $type_edt, $heuredeb_dec);
			if (isset($ens_precedent[0]) OR isset($ens_precedent[1])) {
				$aff_precedent = renvoie_duree($cherche_creneaux[$ch_index-1], $jour_semaine, $ens_precedent[0]);
				// cas o� le cours pr�c�dent a deux cours, on v�rifie le deuxi�me aussi
				if (isset($ens_precedent[1])) {
					$aff_precedent1 = renvoie_duree($cherche_creneaux[$ch_index-1], $jour_semaine, $ens_precedent[1]);
				} else {$aff_precedent1 = 0;}

				$nbre_ens_precedent = count($ens_precedent);
				if ($aff_precedent > 2 OR $aff_precedent1 > 2) {
					$aff_rien = "oui";
				}
				// Cas o� un cours se termine au milieu d'un cr�neau
				if ($aff_precedent == 2 AND $heuredeb_dec == "0.5") {
					$cours_precedent = "1heure";
				}
			}
			else $aff_precedent = NULL;
		}
		else $aff_precedent = NULL;

		if (isset($cherche_creneaux[$ch_index-2])) {
			$ens_precedent = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-2], $jour_semaine, $type_edt, $heuredeb_dec);
			if (isset($ens_precedent[0])) {
				$aff_precedent = renvoie_duree($cherche_creneaux[$ch_index-2], $jour_semaine, $ens_precedent[0]);
				$nbre_ens_precedent = count($ens_precedent);
				if ($aff_precedent > 4) {
					$aff_rien = "oui";
				}
				if ($aff_precedent == 4 AND $heuredeb_dec == "0.5") {
					$cours_precedent = "2heures";
				}
			}
			else $aff_precedent = NULL;
		}
		else $aff_precedent = NULL;

		if (isset($cherche_creneaux[$ch_index-3])) {
			$ens_precedent = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-3], $jour_semaine, $type_edt, $heuredeb_dec);
			if (isset($ens_precedent[0])) {
				$aff_precedent = renvoie_duree($cherche_creneaux[$ch_index-3], $jour_semaine, $ens_precedent[0]);
				$nbre_ens_precedent = count($ens_precedent);
				if ($aff_precedent > 6) {
					$aff_rien = "oui";
				}
				if ($aff_precedent == 6 AND $heuredeb_dec == "0.5") {
					$cours_precedent = "3heures";
				}
			}
			else $aff_precedent = NULL;
		}
		else $aff_precedent = NULL;

		if (isset($cherche_creneaux[$ch_index-4])) {
			$ens_precedent = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-4], $jour_semaine, $type_edt, $heuredeb_dec);
			if (isset($ens_precedent[0])) {
				$aff_precedent = renvoie_duree($cherche_creneaux[$ch_index-4], $jour_semaine, $ens_precedent[0]);
				$nbre_ens_precedent = count($ens_precedent);
				if ($aff_precedent > 8) {
					$aff_rien = "oui";
				}
				if ($aff_precedent == 8 AND $heuredeb_dec == "0.5") {
					$cours_precedent = "4heures";
				}
			}
			else $aff_precedent = NULL;
		}
		else $aff_precedent = NULL;

	// On fait la m�me op�ration en inversant le $heuredeb_dec
				$aff_rien_dec = "non";
		if ($heuredeb_dec == "0") {
			$heuredeb_cherch = "0.5";
		}
		else if ($heuredeb_dec == "0.5") {
			$heuredeb_cherch = "0";
		}
		else $heuredeb_cherch = NULL;

		if (isset($cherche_creneaux[$ch_index-1])) {
			$ens_precedent_dec = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-1], $jour_semaine, $type_edt, $heuredeb_cherch);
			if (isset($ens_precedent_dec[0])) {
				$aff_precedent_dec = renvoie_duree($cherche_creneaux[$ch_index-1], $jour_semaine, $ens_precedent_dec[0]);
				if ($heuredeb_cherch == "0.5" AND $aff_precedent_dec > 1) {
					$aff_rien_dec = "oui";
				}
				else if ($heuredeb_cherch == "0" AND $aff_precedent_dec > 3) {
					$aff_rien_dec = "oui";
				}
				elseif ($heuredeb_cherch == "0" AND $aff_precedent_dec == 3) {
					$cours_precedent = "1heuredemi";
				}
			}
			else $aff_precedent_dec = NULL;
		}
		else $aff_precedent_dec = NULL;

		if (isset($cherche_creneaux[$ch_index-2])) {
			$ens_precedent_dec = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-2], $jour_semaine, $type_edt, $heuredeb_cherch);
			if (isset($ens_precedent_dec[0])) {
				$aff_precedent_dec = renvoie_duree($cherche_creneaux[$ch_index-2], $jour_semaine, $ens_precedent_dec[0]);
				if ($heuredeb_cherch == "0.5" AND $aff_precedent_dec > 3) {
					$aff_rien_dec = "oui";
				}
				else if ($heuredeb_cherch == "0" AND $aff_precedent_dec > 5) {
					$aff_rien_dec = "oui";
				}
				elseif ($heuredeb_cherch == "0" AND $aff_precedent_dec == 5) {
					$cours_precedent = "2heuresdemi";
				}
			}
			else $aff_precedent_dec = NULL;
		}
		else $aff_precedent_dec = NULL;

		if (isset($cherche_creneaux[$ch_index-3])) {
			$ens_precedent_dec = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-3], $jour_semaine, $type_edt, $heuredeb_cherch);
			if (isset($ens_precedent_dec[0])) {
				$aff_precedent_dec = renvoie_duree($cherche_creneaux[$ch_index-3], $jour_semaine, $ens_precedent_dec[0]);
				if ($heuredeb_cherch == "0.5" AND $aff_precedent_dec > 5) {
					$aff_rien_dec = "oui";
				}
				else if ($heuredeb_cherch == "0" AND $aff_precedent_dec > 7) {
					$aff_rien_dec = "oui";
				}
				elseif ($heuredeb_cherch == "0" AND $aff_precedent_dec == 7) {
					$cours_precedent = "3heuresdemi";
				}
			}
			else $aff_precedent_dec = NULL;
		}
		else $aff_precedent_dec = NULL;

		if (isset($cherche_creneaux[$ch_index-4])) {
			$ens_precedent_dec = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-4], $jour_semaine, $type_edt, $heuredeb_cherch);
			if (isset($ens_precedent_dec[0])) {
				$aff_precedent_dec = renvoie_duree($cherche_creneaux[$ch_index-4], $jour_semaine, $ens_precedent_dec[0]);
				if ($heuredeb_cherch == "0.5" AND $aff_precedent_dec > 7) {
					$aff_rien_dec = "oui";
				}
				else if ($heuredeb_cherch == "0" AND $aff_precedent_dec > 9) {
					$aff_rien_dec = "oui";
				}
			}
			else $aff_precedent_dec = NULL;
		}
		else $aff_precedent_dec = NULL;

	// alors on v�rifie le cours en heuredeb_dec = 0.5
	// Normalement ces lignes ne servent plus � rien
		$ens_tab_cheval = NULL;
		$duree_tab_cheval = NULL;
	if ($heuredeb_dec == "0") {
		$ens_tab_cheval = cree_tab_general($req_type_login, $id_creneaux, $jour_semaine, $type_edt, "0.5");
			$nbre_tab_cheval = count($ens_tab_cheval);
		if (isset($ens_tab_cheval[0]) AND $nbre_tab_cheval != 0) {
			$duree_tab_cheval = renvoie_duree($id_creneaux, $jour_semaine, $ens_tab_cheval[0]);
		}else{
			$duree_tab_cheval = NULL;
		}
		if (isset($cherche_creneaux[$ch_index-1]) AND isset($ens_tab_cheval[0])) {
			$duree_tab_pre_dec = renvoie_duree($cherche_creneaux[$ch_index-1], $jour_semaine, $ens_tab_cheval[0]);
			if ($duree_tab_pre_dec > 2) {
				$aff_rien = "oui";
			}
		}
	}
	else $ens_tab_cheval = NULL;

	// On v�rifie les cours en heuredeb_dec = 0
	// Normalement ces lignes ne servent plus � rien aussi
			$ens_tab_0 = NULL;
			$duree_tab_0 = NULL;
	if ($heuredeb_dec == "0.5") {
		$ens_tab_0 = cree_tab_general($req_type_login, $id_creneaux, $jour_semaine, $type_edt, "0");
			$nbre_tab_0 = count($ens_tab_0);
		if (isset($ens_tab_0[0]) AND $nbre_tab_0 != 0) {
			$duree_tab_0 = renvoie_duree($id_creneaux, $jour_semaine, $ens_tab_0[0]);
		}
		else $duree_tab_0 = NULL;
	// On v�rifie quand m�me le cours pr�c�dent de 3 cellules et sa duree
		if (isset($cherche_creneaux[$ch_index-1])) {
			$ens_tab_pre_dec = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-1], $jour_semaine, $type_edt, "0");
			$nbre_tab_pre_dec = count($ens_tab_pre_dec);
			if (isset($ens_tab_pre_dec[0]) AND $nbre_tab_pre_dec != 0) {
				$duree_tab_pre_dec = renvoie_duree($cherche_creneaux[$ch_index-1], $jour_semaine, $ens_tab_pre_dec[0]);
				if ($duree_tab_pre_dec > 2) {
					$aff_rien = "oui";
				}
			}
		}
		else $duree_tab_pre_dec = NULL;
	}
	else $ens_tab_0 = NULL;

	// Chercher l'enseignement � afficher
	$ens_tab = cree_tab_general($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $heuredeb_dec);
		if ($type_edt == "eleve") {
				$nbre_ens_1 = count($ens_tab);
			if ($nbre_ens_1 === 0) {
				$nbre_ens = 0;
			} elseif($nbre_ens_1 === 1) {
				// S'il y en a qu'un, c'est que c'est une AID
				$nbre_ens = count($ens_tab);
			}else{
				// 3 �tant le nombre de p�riodes (il faudra changer cela)

				$nbre_ens = ($nbre_ens_1/3);
				$b = 0;
				for($a = 1; $a < ($nbre_ens + 1); $a++) {
					$b = $b+3;
					$ens_tab[$a] = isset($ens_tab[$b]) ? $ens_tab[$b] : NULL;
				}

			}
		} else {
			$nbre_ens = count($ens_tab);
		}


//debuggage intensif (� enlever en prod)
if (isset($ens_tab_cheval[0])) {
	 $aff1 = $ens_tab_cheval[0]."etc ";
} else $aff1 = "Netc ";
if (isset($duree_tab_cheval)) {
	 $aff2 = $duree_tab_cheval."dtc ";
}else $aff2 = "Ndtc ";
if (isset($aff_precedent)) {
	$aff3 = $aff_precedent."ap ";
}else $aff3 = "Nap ";
if (isset($ens_precedent[0])) {
	$aff4 = $ens_precedent[0]."ep ";
}else $aff4 = "Nep ";
if (isset($nbre_ens_precedent)) {
	$aff4b = $nbre_ens_precedent."nep";
}else $aff4b = "Nnep";
if (isset($duree_tab_pre_dec)) {
	$aff5 = $duree_tab_pre_dec."dtpc ";
}else $aff5 = "Ndtpc ";
if (isset($nbre_ens)) {
	$aff6 = $nbre_ens."ne ";
}else $aff6 = "Nne ";
$aff_debug = $aff1.$aff2.$aff3.$aff4.$aff5.$aff6;
// A ENLEVER APRES DEBBUG
//$debg = $cherche_creneaux[$ch_index-3]."|".$ens_precedent[0]."|".$aff_precedent."|".$heuredeb_dec."|".$aff_rien."|".$cours_precedent;
$debg = NULL;
	// On ajoute la possibilit� de cr�er un cours juste en cliquant sur le "-"
		// On pr�cise si le cours d�bute au milieu ou au d�but du cr�neau
		if ($heuredeb_dec == "0.5") {
			$deb = "milieu";
		}else {
			$deb = "debut";
		}
		// On envoie le lien si et seulement si c'est un administrateur ou un scolarite ou si l'admin a donn� le droit aux professeurs
    if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR getSettingValue("edt_remplir_prof") == 'y') AND $type_edt == "prof") {
			$creer_cours = '<a href=\'javascript:centrerpopup("modifier_cours_popup.php?cours=aucun&amp;identite='.$req_type_login.'&amp;horaire='.$jour_semaine.'|'.$id_creneaux.'|'.$deb.'",700,285,"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no")\'>
			<img src="../images/icons/ico_plus.png" title="Cr&eacute;er un cours" alt="Cr&eacute;er un cours" /></a>';
		}else{
			$creer_cours = "-";
		}
		if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR getSettingValue("edt_remplir_prof") == 'y') AND $type_edt == "prof") {
			$ajouter_cours = '<a href=\'javascript:centrerpopup("modifier_cours_popup.php?cours=aucun&amp;identite='.$req_type_login.'&amp;horaire='.$jour_semaine.'|'.$id_creneaux.'|'.$deb.'",700,285,"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no")\'>
			<img src="../images/icons/ico_plus.png" title="Ajouter un cours" alt="Ajouter un cours" /></a>';
		}else{
			$ajouter_cours = "";
		}

	// Ajout d'un dispositif pour les sanctions
	global $fonction_onclick;
	$fonction_onclick = isset($fonction_onclick) ? $fonction_onclick : NULL;
	if ($fonction_onclick == "y") {
		// On a besoin du nom du cr�neau
		$nom_creneau = mysql_fetch_array(mysql_query("SELECT nom_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$id_creneaux."' LIMIT 1"));
		$aff_click_sanctions = '<br /><img src="../images/edit16.png" title="Ajouter une sanction" alt="Ajouter une sanction" onclick="clic_edt(\''.$nom_creneau["nom_definie_periode"].'\', \''.$jour_semaine.'\');" />';
	}else{

		$aff_click_sanctions = NULL;

	}

	// On envoie l'affichage en fonction du nombre de r�ponses � la requ�te du nombre de cours

		if ($nbre_ens === 0) {
			if ($cours_precedent == "1heure" OR $cours_precedent == "2heures" OR $cours_precedent == "3heures" OR $cours_precedent == "4heures") {
				$case_tab = "<td style=\"height: 35px;\">".$creer_cours."<!--raf1 ".$aff_debug."--></td>";
			}
			elseif ($cours_precedent == "1heuredemi" OR $cours_precedent == "2heuresdemi" OR $cours_precedent == "3heuresdemi") {
				$case_tab = "<td style=\"height: 35px;\">".$creer_cours."<!--raf1 ".$aff_debug."--></td>";
			}
			elseif ($heuredeb_dec == "0.5" AND isset($duree_tab_pre_dec) AND $duree_tab_pre_dec == 3) {
				$case_tab = "<td style=\"height: 35px;\">".$creer_cours."<!--raf1 ".$aff_debug."--></td>";
			}
			elseif ($aff_rien == "non" AND $aff_rien_dec == "non" AND ($duree_tab_cheval == 1 OR $duree_tab_0 == 1)) {
				$case_tab = "<td style=\"height: 35px;\">".$creer_cours."<!--raf2a ".$aff_debug."--></td>";
			}
			elseif ($heuredeb_dec == "0.5") {
				$case_tab = "<!--rien1 ".$aff_debug."-->";
			}
				// Cas d'un demi creneau en heuredeb_dec = 0 qui est vide
			elseif ($heuredeb_dec == "0" AND isset($ens_tab_cheval[0]) AND $duree_tab_cheval != "n" AND isset($aff_precedent) AND $aff_precedent != 3) {
				$case_tab = "<!--raf2 ".$aff_debug."-->";
			}
			elseif (isset($aff_rien) AND $aff_rien == "oui") {
				$case_tab = "<!--rien2b ".$aff_debug."-->";
			}
			elseif (isset($aff_rien_dec) AND $aff_rien_dec == "oui") {
				$case_tab = "<!--rien2c ".$aff_debug."-->";
			}
			elseif ($heuredeb_dec == "0" AND isset($ens_tab_cheval[0]) AND $duree_tab_cheval != "n" AND (!$aff_precedent OR (isset($aff_precedent) AND $aff_precedent != 3))) {
				$case_tab = "<td style=\"height: 35px;\">".$creer_cours."<!--AFF2 ".$aff_debug."+".$debg."--></td>";
				//$case_tab = "<!-- rien2d ".$aff_debug." -->";
			}
			//elseif ((isset($aff_precedent)) AND ($aff_precedent == 3 OR $aff_precedent == 4 OR $aff_precedent == 5 OR $aff_precedent == 6)) {
			//	$case_tab = "<!--rien2 ".$aff1.$aff2.$aff3.$aff4.$aff4b.$aff5.$aff6."-->";
			//}
			else $case_tab = "<td rowspan=\"2\">".$creer_cours."<!--raf3 ".$aff1.$aff2.$aff3.$aff4.$aff4b.$aff5.$aff6."-->".$aff_click_sanctions."</td>\n";
		}
	// Cas o� il y a qu'un seul enseignement
		elseif ($nbre_ens === 1) {
			//enseignement = $ens_tab[0]
			if (renvoie_duree($id_creneaux, $jour_semaine, $ens_tab[0]) == "n") {
				$case_tab = "<!--rien3 ".$aff1.$aff2.$aff3.$aff4.$aff4b.$aff5.$aff6."-->";
			}
			else $case_tab = "<td rowspan=\"".renvoie_duree($id_creneaux, $jour_semaine, $ens_tab[0])."\" style=\"background-color: ".couleurCellule($ens_tab[0]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[0]).$ajouter_cours."</td>\n";
		}
	// Cas avec deux enseignements
		elseif ($nbre_ens == 2) {
			$duree0 = renvoie_duree($id_creneaux, $jour_semaine, $ens_tab[0]);
			$duree1 = renvoie_duree($id_creneaux, $jour_semaine, $ens_tab[1]);
			if ($duree0 == $duree1) {
				// si les deux dur�es sont �quivalentes alors le rowspan est �gal � cette dur�e
				$rowspan = $duree0;
				$complement = "";
				$rowleft = $rowright = "2";
				$style_l = $style_r = $style_tab = " height: 70px;";
				// Sinon, on choisit la dur�e la plus longue
			}else if ($duree0 > $duree1) {
					// � gauche le plus long
				$rowspan = $duree0;
				$complement = "
						<tr>
							<!--<td>1</td><td></td>-->
						</tr>
						<tr>
							<td rowspan=\"2\" style=\"height: 70px;\">-</td><!--<td>2</td>-->
						</tr>\n";
				$rowleft = "4";
				$rowright = "2";
				$style_l = "height: 100%;";
				$style_r = "height: 70px;";
				$style_tab = "height: 140px;";
			}else {
					// � droite le plus long
				$rowspan = $duree1;
				$complement = "
						<tr>
							<!--<td></td><td>4</td>-->
						</tr>
						<tr>
							<td rowspan=\"2\" style=\"height: 70px;\">-</td><!--<td>3</td>-->
						</tr>\n";
				$rowleft = "2";
				$rowright = "4";
				$style_r = "height: 100%;";
				$style_l = "height: 70px;";
				$style_tab = "height: 140px;";
			}
			$case_tab = "
				<td rowspan=\"".$rowspan."\" style=\"height: 35px;\">
					<table class=\"tab_edt_1\" style=\"".$style_tab."\">
						<tr>
							<td rowspan=\"".$rowleft."\" style=\"font-size: 10px; ".$style_l." background-color: ".couleurCellule($ens_tab[0]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[0])."</td>
							<td rowspan=\"".$rowright."\" style=\"font-size: 10px; ".$style_r." background-color: ".couleurCellule($ens_tab[1]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[1])."</td>
						</tr>
						".$complement."
						<tr>
							<!--<td>5</td><td></td>-->
						</tr>
					</table>
				</td>";
		}

	// Cas avec 3 enseignements
		elseif ($nbre_ens == 3) {
			$case_1_tab = "<td rowspan=\"2\"><table class=\"tab_edt_1\" BORDER=\"0\" CELLSPACING=\"0\"><tbody>\n";
			$case_2_tab = ("<tr>\n<td style=\"font-size: 8px; background-color: ".couleurCellule($ens_tab[0]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[0])."</td>\n<td style=\"font-size: 8px; background-color: ".couleurCellule($ens_tab[1]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[1])."</td>\n<td style=\"font-size: 8px; background-color: ".couleurCellule($ens_tab[2]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[2])."</td>\n</tr>\n");
			$case_3_tab = "</tbody>\n</table>\n</td>";

			$case_tab = $case_1_tab.$case_2_tab.$case_3_tab;
		}
	// Cas avec plus de trois enseignements
		elseif ($nbre_ens > 3) {
				// On met la liste des enseignements dans $contenu de l'infobulle
				$contenu = "";
				for($z=0; $z<$nbre_ens; $z++) {
					$contenu .= "<p>".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[$z])."</p>";
				}
				// dans le nom du div, on pr�voit beaucoup d'infos pour �viter que deux div aient le m�me nom
			$id_div = "ens_".$id_creneaux."_".$jour_semaine.$ens_tab[$z];
			$case_tab = "<td rowspan=\"".renvoie_duree($id_creneaux, $jour_semaine, $ens_tab[0])."\"><a href='#' onclick=\"afficher_div('".$id_div."','Y',10,10);return false;\">VOIR</a>".creer_div_infobulle($id_div, "Liste des enseignements", "#330033", $contenu, "#FFFFFF", 20,0,"y","y","n","n")."</td>\n";
		}
		else {
		// AJOUT: boireaus
		$case_tab = NULL;
		}

	return $case_tab;
}


// Fonction qui construit le contenu d'un cr�neaux.

function contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $enseignement){

	global $fonction_onclick;
	$fonction_onclick = isset($fonction_onclick) ? $fonction_onclick : NULL;

	// On r�cup�re l'id
	$req_recup_id = mysql_fetch_array(mysql_query("SELECT id_cours FROM edt_cours WHERE
										id_groupe = '".$enseignement."' AND
										jour_semaine = '".$jour_semaine."' AND
										id_definie_periode = '".$id_creneaux."'"));

	// Seul l'admin peut effacer ce cours ou le scolarite si l'admin l'y a autoris�
	$effacer_cours = "";
  if (($_SESSION["statut"] == "scolarite" AND GetSettingEdt('scolarite_modif_cours') == "y") OR
      $_SESSION["statut"] == "administrateur" OR
      ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y') ) {

		$effacer_cours = '
					<a href="./effacer_cours.php?supprimer_cours='.$req_recup_id["id_cours"].'&amp;type_edt='.$type_edt.'&amp;identite='.$req_type_login.'" onclick="return confirm(\'Confirmez-vous cette suppression ?\')">
					<img src="../images/icons/delete.png" title="Effacer" alt="Effacer" /></a>
				';
	}else {
		$effacer_cours = "";
	}
	// Seuls l'admin et la scolarit� peuvent modifier un cours (sauf si admin n'a pas autoris� scolarite)
	if (($_SESSION["statut"] == "scolarite" AND GetSettingEdt('scolarite_modif_cours') == "y") OR $_SESSION["statut"] == "administrateur") {
		$modifier_cours = '
				<a href=\'javascript:centrerpopup("modifier_cours_popup.php?id_cours='.$req_recup_id["id_cours"].'&amp;type_edt='.$type_edt.'&amp;identite='.$req_type_login.'",700,280,"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no")\'>
				<img src="../images/edit16.png" title="Modifier" alt="Modifier" /></a>
						';
	}else {
		$modifier_cours = "";
	}

	if ($fonction_onclick == "y") {
		// On a besoin du nom du cr�neau
		$nom_creneau = mysql_fetch_array(mysql_query("SELECT nom_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$id_creneaux."' LIMIT 1"));
		$aff_click_sanctions = '<br /><img src="../images/edit16.png" title="Ajouter une sanction" alt="Ajouter une sanction" onclick="clic_edt(\''.$nom_creneau["nom_definie_periode"].'\', \''.$jour_semaine.'\');" />';
	}else{

		$aff_click_sanctions = NULL;

	}

	// On v�rifie si $enseignement est ou pas pas un AID (en v�rifiant qu'il est bien renseign�)
	$analyse = explode("|", $enseignement);
	if ($analyse[0] == "AID") {
		//echo "c'est un AID";
		$req_nom_aid = mysql_query("SELECT nom, indice_aid FROM aid WHERE id = '".$analyse[1]."'");
		$rep_nom_aid = mysql_fetch_array($req_nom_aid);

		// On r�cup�re le nom de l'aid
		$req_nom_complet = mysql_query("SELECT nom FROM aid_config WHERE indice_aid = '".$rep_nom_aid["indice_aid"]."'");
		$rep_nom_complet = mysql_fetch_array($req_nom_complet);
		$aff_matiere = $rep_nom_complet["nom"];

		$contenu="";

		// On compte les �l�ves de l'aid $aff_nbre_eleve
		$req_nbre_eleves = mysql_query("SELECT login FROM j_aid_eleves WHERE id_aid = '".$analyse[1]."' ORDER BY login");
		$aff_nbre_eleve = mysql_num_rows($req_nbre_eleves);
		for($a=0; $a < $aff_nbre_eleve; $a++) {
			$rep_eleves[$a]["login"] = mysql_result($req_nbre_eleves, $a, "login");
			$noms = mysql_fetch_array(mysql_query("SELECT nom, prenom FROM eleves WHERE login = '".$rep_eleves[$a]["login"]."'"));
			$contenu .= $noms["nom"]." ".$noms["prenom"]."<br />";
		}
		$titre_listeleve = "Liste des �l�ves (".$aff_nbre_eleve.")";
		$id_div_p = $jour_semaine.$rep_nom_aid["nom"].$id_creneaux.$enseignement;
		$id_div = strtr($id_div_p, " -|/'&;", "wwwwwww");
		$classe_js = "<a href=\"#\" onclick=\"afficher_div('".$id_div."','Y',10,10);return false;\">".$rep_nom_aid["nom"]."</a>
			".creer_div_infobulle($id_div, $titre_listeleve, "#330033", $contenu, "#FFFFFF", 20,0,"y","y","n","n");
		// On dresse la liste des noms de prof (on n'affiche que le premier)
		$noms_prof = mysql_fetch_array(mysql_query("SELECT nom, civilite FROM j_aid_utilisateurs jau, utilisateurs u WHERE
									id_aid = '".$analyse[1]."' AND
									jau.id_utilisateur = u.login
									ORDER BY nom LIMIT 1")); // on n'en garde qu'un
		$rep_nom_prof['civilite'] = $noms_prof["civilite"].' '.$noms_prof["nom"].'<br />';
		$rep_nom_prof['nom'] = "<span style='font-size: 0.8em;'>Cours en groupe</span>";

	}elseif($analyse[0] == '' OR $analyse[0] == 'inc'){

		// le groupe n'est pas renseign�, donc, on affiche en fonction
		$aff_matiere = 'inc.';
		$classe_js = NULL;
		$aff_nbre_eleve = '0';
		$aff_sem = NULL;
		$rep_salle = NULL;

	}elseif($analyse[0] == 'EDT'){

		// le groupe est un edt_gr donc on cherche les infos qui vont bien
		if ($analyse[1] != '') {
			/*/ on r�cup�re les infos de cet edt_gr
			$edt_gr = mysql_query("SELECT egn.nom, egn.nom_long, egc.id_classe, ege.id_eleve
												FROM edt_gr_nom egn, edt_gr_classes egc, edt_gr_eleves ege
												WHERE egn.id = '".$analyse[1]."'
												AND egn.id = egc.id_gr_nom
												AND egn.id = ege.id_gr_nom
												ORDER BY ege.id_eleve LIMIT 1")
											OR trigger_error('Erreur', E_USER_WARNING);
			*/
			$edt_gr = mysql_query("SELECT nom, nom_long FROM edt_gr_nom WHERE id = '".$analyse[1]."'")
											OR trigger_error('Erreur', E_USER_WARNING);
			$aff_gr = mysql_fetch_array($edt_gr);

			$aff_matiere = $aff_gr["nom"];
			$classe_js = $aff_gr["nom_long"];

			$query_p = mysql_query("SELECT nom, civilite FROM edt_gr_profs egp, utilisateurs u
																WHERE u.login = egp.id_utilisateurs
																AND egp.id_gr_nom = '".$analyse[1]."'") OR trigger_error('Erreur ', E_USER_ERROR);
			$rep_nom_prof = mysql_fetch_array($query_p);


			$aff_nbre_eleve = '0';
			$aff_sem = NULL;
			$rep_salle = NULL;

		}else{

			$aff_matiere = 'inc.';
			$classe_js = NULL;
			$aff_nbre_eleve = '0';
			$aff_sem = NULL;
			$rep_salle = NULL;
		}

	}else {

		// on r�cup�re le nom court de la classe en question
		$req_id_classe = mysql_query("SELECT id_classe FROM j_groupes_classes WHERE id_groupe ='".$enseignement."'");
		$rep_id_classe = mysql_fetch_array($req_id_classe);
		$req_classe = mysql_query("SELECT classe FROM classes WHERE id ='".$rep_id_classe['id_classe']."'");
		$rep_classe = mysql_fetch_array($req_classe);

		// On r�cup�re la p�riode active en passant d'abord par le calendrier
		$query_cal = mysql_query("SELECT numero_periode FROM edt_calendrier WHERE
														debut_calendrier_ts <= '".date("U")."'
														AND fin_calendrier_ts >= '".date("U")."'
														AND numero_periode != '0'
														AND classe_concerne_calendrier LIKE '%".$rep_id_classe['id_classe']."%'")
									OR trigger_error('Impossible de lire le calendrier.', E_USER_NOTICE);
		$p_c = mysql_fetch_array($query_cal);

		$query_periode = mysql_query("SELECT num_periode FROM periodes WHERE verouiller = 'N' OR verouiller = 'P'")
									OR trigger_error('Impossible de r�cup�rer la bonne p�riode.', E_USER_NOTICE);
		$p = mysql_fetch_array($query_periode);

		$per = isset($p_c["numero_periode"]) ? $p_c["numero_periode"] : (isset($p["num_periode"]) ? $p["num_periode"] : "1");

		// On compte le nombre d'�l�ves
		$req_compter_eleves = mysql_query("SELECT COUNT(*) FROM j_eleves_groupes WHERE periode = '".$per."' AND id_groupe ='".$enseignement."'");
		$rep_compter_eleves = mysql_fetch_array($req_compter_eleves);
		$aff_nbre_eleve = $rep_compter_eleves[0];

		// On r�cup�re la liste des �l�ves de l'enseignement
		if (($type_edt == "prof") OR ($type_edt == "salle")) {
			$current_group = get_group($enseignement);

			$contenu="";

			// $per �tant le num�ro de la p�riode
			if (isset($current_group["eleves"][$per]["users"])) {
				foreach ($current_group["eleves"][$per]["users"] as $eleve_login) {
					$contenu .= $eleve_login['nom']." ".$eleve_login['prenom']."<br />";
				}
			}

			$titre_listeleve = "Liste des �l�ves (".$aff_nbre_eleve.")";

			//$classe_js = aff_popup($rep_classe['classe'], "edt", $titre_listeleve, $contenu);
			$id_div_p = $jour_semaine.$rep_classe['classe'].$id_creneaux.rand();
			$id_div = strtr($id_div_p, " -|/'&;", "wwwwwww");
			$classe_js = "<a href=\"#\" onclick=\"afficher_div('".$id_div."','Y',10,10);return false;\">".$rep_classe['classe']."</a>
				".creer_div_infobulle($id_div, $titre_listeleve, "#330033", $contenu, "#FFFFFF", 20,0,"y","y","n","n");
		}
		// On r�cup�re le nom et la civilite du prof en question
		$req_login_prof = mysql_query("SELECT login FROM j_groupes_professeurs WHERE id_groupe ='".$enseignement."'");
		$rep_login_prof = mysql_fetch_array($req_login_prof);
		$req_nom_prof = mysql_query("SELECT nom, civilite FROM utilisateurs WHERE login ='".$rep_login_prof['login']."'");
		$rep_nom_prof = mysql_fetch_array($req_nom_prof);

		// On r�cup�re le nom de l'enseignement en question (en fonction du param�tre long ou court)
		$req_groupe = mysql_query("SELECT description FROM groupes WHERE id ='".$enseignement."'");
		$rep_matiere = mysql_fetch_array($req_groupe);
		if (GetSettingEdt("edt_aff_matiere") == "long") {
			// SI c'est l'admin, il faut r�duire la taille de la police de caract�res
			//if ($_SESSION["statut"] == "administrateur") {
			//$aff_matiere = "<span class=\"edt_admin\">".$rep_matiere['description']."</span>";
			//}
			//else
			$aff_matiere = $rep_matiere['description'];
		}else {
			$req_2_matiere = mysql_query("SELECT id_matiere FROM j_groupes_matieres WHERE id_groupe ='".$enseignement."'");
			$rep_2_matiere = mysql_fetch_array($req_2_matiere);
			$aff_matiere = $rep_2_matiere['id_matiere'];
		}

	} // fin du else apr�s les aid

	// On r�cup�re le type de semaine si besoin
	$req_sem = mysql_query("SELECT id_semaine FROM edt_cours WHERE id_cours ='".$req_recup_id["id_cours"]."'");
	$rep_sem = mysql_fetch_array($req_sem);
	if ($rep_sem["id_semaine"] == "0") {
		$aff_sem = '';
	}else {
		$aff_sem = '<font color="#663333"> - Sem.'.$rep_sem["id_semaine"].'</font>';
	}

	// On r�cup�re le nom complet de la salle en question
	if (GetSettingEdt("edt_aff_salle") == "nom") {
		$salle_aff = "nom_salle";
	}else {
		$salle_aff = "numero_salle";
	}
	$req_id_salle = mysql_query("SELECT id_salle FROM edt_cours WHERE id_groupe ='".$enseignement."' AND id_definie_periode ='".$id_creneaux."' AND jour_semaine ='".$jour_semaine."'");
	$rep_id_salle = mysql_fetch_array($req_id_salle);
	$req_salle = mysql_query("SELECT ".$salle_aff." FROM salle_cours WHERE id_salle ='".$rep_id_salle['id_salle']."'");
	$tab_rep_salle = mysql_fetch_array($req_salle);
	$rep_salle = $tab_rep_salle[0];



	if ($type_edt == "prof"){
		return ("".$aff_matiere."<br />\n".$classe_js." ".$effacer_cours." ".$modifier_cours." \n".$aff_sem."<br />\n<i>".$rep_salle."</i> - ".$aff_nbre_eleve." �l.\n");
	}elseif (($type_edt == "classe") OR ($type_edt == "eleve")){
		return ("".$aff_matiere."<br />".$rep_nom_prof['civilite']." ".$rep_nom_prof['nom']."<br /><i>".$rep_salle."</i> ".$aff_sem.$aff_click_sanctions."");
	}elseif ($type_edt == "salle"){
		return ("".$aff_matiere."<br />\n".$rep_nom_prof['civilite']." ".$rep_nom_prof['nom']." ".$aff_sem."<br />\n".$classe_js." - ".$aff_nbre_eleve." �l.\n");
	}else{
		return '';
	}
}


// Fonction qui construit la premi�re ligne du tableau

function premiere_ligne_tab_edt(){

	echo("<table class=\"tab_edt\">\n");
	echo("<tbody>\n");
	echo("<tr>\n");
	echo("<th>Horaires</th>\n");

	$compter_colonnes = mysql_query("SELECT jour_horaire_etablissement FROM horaires_etablissement WHERE ouvert_horaire_etablissement = 1");
	while ($jour_semaine = mysql_fetch_array($compter_colonnes)) {

	printf("<th>".$jour_semaine["jour_horaire_etablissement"]."</th>\n");
	}
    echo("</tr>\n");
}


// Fonction qui construit chaque ligne du tableau

function construction_tab_edt($heure, $heuredeb_dec){

	if ($_SESSION['statut'] == "eleve") {
		$req_type_login = $_SESSION['login'];
	}else{
		$req_type_login = isset($_GET["login_edt"]) ? $_GET["login_edt"] : (isset($_POST["login_edt"]) ? $_POST["login_edt"] : (isset($_GET["ele_login"]) ? $_GET["ele_login"] : NULL));
	}

	if ($_SESSION['statut'] == "eleve") {
		$type_edt = $_SESSION['statut'];
	}else{
		$type_edt = isset($_GET["type_edt_2"]) ? $_GET["type_edt_2"] : (isset($_POST["type_edt_2"]) ? $_POST["type_edt_2"] : 'eleve');
	}
	// On d�termine le nombre de jours � afficher
	$compter_nbre_colonnes = mysql_query("SELECT jour_horaire_etablissement FROM horaires_etablissement WHERE ouvert_horaire_etablissement = 1");
	$compter_colonnes = mysql_num_rows($compter_nbre_colonnes);

	$req_colonnes = mysql_query("SELECT jour_horaire_etablissement FROM horaires_etablissement WHERE ouvert_horaire_etablissement = 1");
		$jour_sem_tab = array();
		while($data_sem_tab = mysql_fetch_array($req_colonnes)) {
			$jour_sem_tab[] = $data_sem_tab["jour_horaire_etablissement"];
		}
	if ($compter_colonnes <= 4) {
		return "".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[0], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[1], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[2], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[3], $type_edt, $heuredeb_dec))."\n</tr>\n";
	}
	elseif ($compter_colonnes <= 5) {
		return "".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[0], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[1], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[2], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[3], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[4], $type_edt, $heuredeb_dec))."\n</tr>\n";
	}
	elseif ($compter_colonnes <= 6) {
		return "".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[0], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[1], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[2], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[3], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[4], $type_edt, $heuredeb_dec))."\n"
				.(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[5], $type_edt, $heuredeb_dec))."\n</tr>\n";
	}
}


// Fonction qui renvoie la liste des professeurs, des classe ou des salles

function renvoie_liste($type) {

	$rep_liste = "";
	if ($type == "prof") {
		$req_liste = mysql_query("SELECT nom, prenom, login FROM utilisateurs WHERE etat ='actif' AND statut='professeur' ORDER BY nom");

		$nb_liste = mysql_num_rows($req_liste);

	$tab_liste = array();

		for($i=0;$i<$nb_liste;$i++) {
			$rep_liste[$i]["nom"] = mysql_result($req_liste, $i, "nom");
			$rep_liste[$i]["prenom"] = mysql_result($req_liste, $i, "prenom");
			$rep_liste[$i]["login"] = mysql_result($req_liste, $i, "login");
			}
	return $rep_liste;
	}
	if ($type == "classe") {
		$req_liste = mysql_query("SELECT id, classe FROM classes ORDER BY classe");

		$nb_liste = mysql_num_rows($req_liste);

	$tab_liste = array();

		for($i=0;$i<$nb_liste;$i++) {
			$rep_liste[$i]["id"] = mysql_result($req_liste, $i, "id");
			$rep_liste[$i]["classe"] = mysql_result($req_liste, $i, "classe");
			}
	return $rep_liste;
	}
	if ($type == "salle") {
		$req_liste = mysql_query("SELECT id_salle, numero_salle, nom_salle FROM salle_cours ORDER BY numero_salle");

		$nb_liste = mysql_num_rows($req_liste);

	$tab_liste = array();

		for($i=0;$i<$nb_liste;$i++) {
			$rep_liste[$i]["id_salle"] = mysql_result($req_liste, $i, "id_salle");
			$rep_liste[$i]["numero_salle"] = mysql_result($req_liste, $i, "numero_salle");
			$rep_liste[$i]["nom_salle"] = mysql_result($req_liste, $i, "nom_salle");
			}
	return $rep_liste;
	}
	if ($type == "eleve") {
		$req_liste = mysql_query("SELECT nom, prenom, login FROM eleves GROUP BY nom");

		$nb_liste = mysql_num_rows($req_liste);

	$tab_liste = array();

		for($i=0;$i<$nb_liste;$i++) {
			$rep_liste[$i]["nom"] = mysql_result($req_liste, $i, "nom");
			$rep_liste[$i]["prenom"] = mysql_result($req_liste, $i, "prenom");
			$rep_liste[$i]["login"] = mysql_result($req_liste, $i, "login");
			}
	return $rep_liste;
	}
}

// Fonction qui retourne le nom long et court de la classe d'un �l�ve

function aff_nom_classe($log_eleve) {
	$req_id_classe = mysql_query("SELECT id_classe FROM j_eleves_classes WHERE login = '".$log_eleve."'");
	$rep_id_classe = mysql_fetch_array($req_id_classe);

	$req_nom_classe = mysql_query("SELECT classe, nom_complet FROM classes WHERE id ='".$rep_id_classe["id_classe"]."'");

	$rep_nom_classe1 = mysql_fetch_array($req_nom_classe);
	$rep_nom_classe = $rep_nom_classe1["classe"];

	return $rep_nom_classe;
}

// Fonction qui renvoie la liste des �l�ves dont le nom commence par la lettre $alpha

function renvoie_liste_a($type, $alpha){
	if ($type == "eleve") {
		$req_eleves_a = mysql_query("SELECT login, nom, prenom FROM eleves WHERE nom LIKE '$alpha%' ORDER BY nom");

		$nb_liste = mysql_num_rows($req_eleves_a);

	$rep_liste = array();

		for($i=0;$i<$nb_liste;$i++) {
			$rep_liste[$i]["nom"] = mysql_result($req_eleves_a, $i, "nom");
			$rep_liste[$i]["prenom"] = mysql_result($req_eleves_a, $i, "prenom");
			$rep_liste[$i]["login"] = mysql_result($req_eleves_a, $i, "login");
			}
	return $rep_liste;
	}
}

// Fonction qui renvoie la liste des �l�ves d'une classe

function renvoie_liste_classe($id_classe_post){
	$req_liste_login = mysql_query("SELECT login FROM j_eleves_classes WHERE id_classe = '".$id_classe_post."' AND periode = '1'") OR die ('Erreur : renvoie_liste_classe() : '.mysql_error().'.');
	$nb_eleves = mysql_num_rows($req_liste_login);

	$rep_liste_eleves = array();

		for($i=0; $i<$nb_eleves; $i++) {

			$rep_liste_eleves[$i]["login"] = mysql_result($req_liste_login, $i, "login");
		}
	return $rep_liste_eleves;
}

// Fonction qui renvoie le nom qui correspond � l'identifiant envoy� et au type (salle, prof, classe et �l�ve)

function renvoie_nom_long($id, $type){
	{
	if ($type == "prof") {
		$req_nom_long = mysql_query("SELECT nom, prenom, civilite FROM utilisateurs WHERE login = '".$id."'");
		$nom = @mysql_result($req_nom_long, 0, 'nom');
    	$prenom = @mysql_result($req_nom_long, 0, 'prenom');
    	$civilite = @mysql_result($req_nom_long, 0, 'civilite');

    	$nom_long = $civilite." ".$nom." ".$prenom;
	}

	elseif ($type == "eleve") {
		$req_nom_long = mysql_query("SELECT nom, prenom FROM eleves WHERE login = '".$id."'");
		$nom = @mysql_result($req_nom_long, 0, 'nom');
    	$prenom = @mysql_result($req_nom_long, 0, 'prenom');

    	$nom_long = $prenom." ".$nom;
	}
	elseif ($type == "salle") {
		$req_nom_long = mysql_query("SELECT nom_salle FROM salle_cours WHERE id_salle = '".$id."'");
		$nom = @mysql_result($req_nom_long, 0, 'nom_salle');

    	$nom_long = 'la '.$nom;
	}
	elseif ($type == "classe") {
		$req_nom_long = mysql_query("SELECT nom_complet FROM classes WHERE id = '".$id."'");
		$nom = @mysql_result($req_nom_long, 0, 'nom_complet');

		$nom_long = 'la classe de '.$nom;
	}
	}
	return $nom_long;
}


// Fonction qui affiche toutes les salles sans enseignements � un horaire donn�

function aff_salles_vides($id_creneaux, $id_jour_semaine){

	// tous les id de toutes les salles
	$req_liste_salle = mysql_query("SELECT id_salle FROM salle_cours");
		$tab_toutes = array();
		while($rep_toutes = mysql_fetch_array($req_liste_salle))
		{
		$tab_toutes[]=$rep_toutes["id_salle"];
		}
	// Tous les id des salles qui ont cours � id_creneaux et id_jour_semaine
	$req_liste_salle_c = mysql_query("SELECT id_salle FROM edt_cours WHERE id_definie_periode = '".$id_creneaux."' AND jour_semaine = '".$id_jour_semaine."'");
		$tab_utilisees = array();
		while($rep_utilisees = mysql_fetch_array($req_liste_salle_c))
		{
		$tab_utilisees[]=$rep_utilisees["id_salle"];
		}

	$result = array_diff($tab_toutes, $tab_utilisees);

	return $result;
}


// checked pour le param�trage de l'EdT
function aff_checked($aff, $valeur){
	$req_aff = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = '".$aff."'");
	$rep_aff = mysql_fetch_array($req_aff);

	if ($rep_aff['valeur'] === $valeur) {
		$retour_aff = ("checked='checked' ");
	}
	else {
		$retour_aff = ("");
	}
	return $retour_aff;
}

// retourne les settings de l'EdT
function GetSettingEdt($param_edt){
	$req_param_edt = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = '".$param_edt."'");
	$rep_param_edt = mysql_fetch_array($req_param_edt);

	$retourne = $rep_param_edt["valeur"];

	return $retourne;
}

// Retourne le nom de la salle
function nom_salle($id_salle_r){
	$req_nom_salle = mysql_query("SELECT nom_salle FROM salle_cours WHERE id_salle = '".$id_salle_r."'");
	$reponse = mysql_fetch_array($req_nom_salle);
		$nom_salle_r = $reponse["nom_salle"];

	return $nom_salle_r;
}
// Retourne le nom de la salle
function numero_salle($id_salle_r){
	$req_nom_salle = mysql_query("SELECT numero_salle FROM salle_cours WHERE id_salle = '".$id_salle_r."'");
	$reponse = mysql_fetch_array($req_nom_salle);
		$nom_salle_r = $reponse["numero_salle"];

	return $nom_salle_r;
}

// Fonction qui renvoie la couleur de fond de cellule pour une mati�re
function couleurCellule($enseignement){
	// On v�rifie si on a affaire � une aid ou pas
	$verif_aid = explode("|", $enseignement);
	if ($verif_aid[0] == "AID") {
		return "none";
	} else {
		// Ce n'est pas une aid, on cherche donc la mati�re rattach�e � cet enseignement
		$sql = mysql_query("SELECT id_matiere FROM j_groupes_matieres WHERE id_groupe = '".$enseignement."'");
		$req_matiere = mysql_fetch_array($sql);
		$matiere = "M_".$req_matiere["id_matiere"];

		// on cherche s'il existe un r�glage pour cette mati�re
		$sql = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = '".$matiere."'");
		$nbre_reponse = mysql_num_rows($sql);
		// On construit la r�ponse en fonction de l'existence ou non de ce r�glage
		if ($nbre_reponse == 0) {
			return "none";
		} else {
			// On v�rifie que le r�glage soit sur coul
			if (GetSettingEdt("edt_aff_couleur") == "coul") {
				$couleur = mysql_fetch_array($sql);
				return $couleur["valeur"];
			} else {
				return "none";
			}

		}
	}
}


// Fonction qui renvoie les AID en fonction du statut du demandeur (prof, classe, �l�ve)
function renvoieAid($statut, $nom){
	$sql = "";
	if ($statut == "prof") {
		$sql = "SELECT id_aid, indice_aid FROM j_aid_utilisateurs WHERE id_utilisateur = '".$nom."' ORDER BY indice_aid";
	}elseif ($statut == "classe"){
		$sql = "";
	}elseif ($statut == "eleve"){
		$sql = "SELECT id_aid, indice_aid FROM j_aid_eleves WHERE login = '".$nom."' ORDER BY indice_aid";
	} else{
		return NULL;
	}
	// On envoie la requ�te
	if ($sql) {
		$requete = mysql_query($sql) OR DIE('Erreur dans la requ�te : '.mysql_error());
		$nbre = mysql_num_rows($requete);
		// Et on retourne le tableau
			$resultat = array();
		for($i = 0; $i < $nbre; $i++) {
			$resultat[$i]["id_aid"] = mysql_result($requete, $i, "id_aid");
			$resultat[$i]["indice_aid"] = mysql_result($requete, $i, "indice_aid");
		}
		return $resultat;

	} else{
		return NULL;
	}

}

// Fonction qui renvoie l'edt d'une classe sur un cr�neau pr�cis
function afficherCoursClasse($nom_classe, $choix_creneau){

	// On r�cup�re l'id de la classe et on cherche
	$query_c = mysql_query("SELECT id FROM classes WHERE nom = '".$nom_classe."' LIMIT 1");
	$id_classe = mysql_result($query_c, "id");
	$cours = cree_tab_general($id_classe, $choix_creneau, $jour, 'classe', 'O');

	return $cours;
}
?>