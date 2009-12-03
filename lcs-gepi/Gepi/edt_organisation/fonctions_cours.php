<?php

/**
 * Ensemble des fonctions qui permettent de cr�er un nouveau cours en v�rifiant les pr�c�dents
 *
 * @version $Id: fonctions_cours.php 1565 2008-03-02 17:05:35Z jjocal $
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

// Toutes les fonctions d'initialisation de l'EdT sont utiles
require_once("edt_init_fonctions.php");

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
	$heure_creneau_appele = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$creneau."'"));
	$requete = mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE
						heuredebut_definie_periode < '".$heure_creneau_appele["heuredebut_definie_periode"]."' AND
						type_creneaux != 'pause'
						ORDER BY heuredebut_definie_periode");
	$nbre = mysql_num_rows($requete);

	return $nbre;
} // nombreCreneauxPrecedent()

// Fonction qui renvoie le nombre de cr�neaux qui suivent celui qui est appel�
function nombreCreneauxApres($creneau){
	// On r�cup�re l'heure du creneau appel�
	$heure_creneau_appele = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$creneau."'"));
	$requete = mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE heuredebut_definie_periode > '".$heure_creneau_appele["heuredebut_definie_periode"]."' AND type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
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

// Fonction qui v�rifie si l'id_groupe de l'Edt n'est pas une AID
function retourneAid($id_groupe){
	// On explode pour voir
	$explode = explode("|", $id_groupe);
	if ($explode[0] == "AID") {
		return $explode[1];
	}else{
		return "non";
	}
}

/*
 * Fonction qui renvoie le d�but et la fin d'un cours en prenant en compte l'id�e que chaque cr�neau
 * dure 2 "temps". Par exemple, pour un cours qui commence au d�but du 4�me cr�neau de la journ�e et
 * qui dure 2 heures, la fonction renvoie $retour["deb"] = 5 et $retour["fin"] = 8;
 * $jour = le jour de la semaine en toute lettre et en Fran�ais
 * $creneau = id du cr�neau (table absences_creneaux)
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

/*
 * Fonction qui v�rifie si un prof a d�j� cours pendant le cours qu'on veut cr�er
 * en tenant compte des types de semaine, des cours avant et apr�s le cours qu'on veut cr�er.
 * $nom = login du prof (de Gepi)
 * $jour = le jour en toute lettres et en Fran�ais
 * $creneau = id de la table absences_creneaux
 * $duree = nombre de demi-cours (un cours d'un cr�neau et demi aura donc une dur�e de 3)
 * $heuredeb_dec vaut '0' si le cours commence au d�but d'un cr�neau et '0.5' si le cours commence au milieu du cr�neau
 * $type_semaine reprend la table edt_semaines ('0' si le cours a lieu toutes les semaines).
 * la fonction renvoie 'oui' si le prof n'a pas d�j� cours et 'non' s'il a d�j� cours.
*/
function verifProf($nom, $jour, $creneau, $duree, $heuredeb_dec, $type_semaine){
	$prof_libre = 'oui';
	// On r�cup�re quelques infos utiles sur le cours qu'on veut cr�er
	$verif_temps = dureeTemps($jour, $creneau, $heuredeb_dec, $duree);
	$query_hdddc = mysql_query("SELECT heuredebut_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$creneau."'");
	$heurededebutducours = mysql_result($query_hdddc, "heuredebut_definie_periode");
echo '<br /> Essai pour un cours '.$nom.' '.$jour.' '.$creneau.' '.$duree.' '.$heuredeb_dec.' '.$type_semaine.' '.$verif_temps["deb"].' '.$verif_temps["fin"];
	// On essaie de r�cup�rer directement tous les cours de ce prof dans la journ�e
	$query = mysql_query("SELECT e.jour_semaine, e.id_definie_periode, e.heuredeb_dec, e.duree, c.heuredebut_definie_periode
							FROM edt_cours e, absences_creneaux c
							WHERE jour_semaine = '".$jour."'
							AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
							AND login_prof = '".$nom."'
							AND e.id_definie_periode = c.id_definie_periode
							ORDER BY heuredebut_definie_periode
							") OR DIE('Erreur dans la requ�te : '.mysql_error());
	$compter_reponses = mysql_num_rows($query);
	if ($compter_reponses == 0) {
		// c'est fini, comme c'est le premier cours de ce prof sur cette journ�e, le cours est ok
		return $prof_libre;
	}else{
		// On d�termine pour chaque cours quel est le d�but et la fin
		for($a = 0; $a < $compter_reponses; $a++){
			$jour_v[$a] = mysql_result($query, $a, "jour_semaine");
			$creneau_v[$a] = mysql_result($query, $a, "id_definie_periode");
			$heuredebut_v[$a] = mysql_result($query, $a, "heuredebut_definie_periode");
			$heuredeb_dec_v[$a] = mysql_result($query, $a, "heuredeb_dec");
			$duree_v[$a] = mysql_result($query, $a, "duree");
			$temps_v = dureeTemps($jour_v[$a], $creneau_v[$a], $heuredeb_dec_v[$a], $duree_v[$a]);
echo '<br />'.$jour_v[$a].' | '.$creneau_v[$a].' | '.$heuredeb_dec_v[$a].' | '.$duree_v[$a].' | '.$heuredebut_v[$a].' | '.$heuredeb_dec;
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> '.$temps_v["deb"].' - '.$temps_v["fin"].'<br />';
			// On v�rifie d'abord les cours qui commencent avant le cours qu'on veut cr�er
			if ($heuredebut_v[$a] < $heurededebutducours) {
				// alors on v�rifie si la fin des cours pr�c�dents n'empi�tent pas sur le cours � cr�er
				if ($verif_temps["deb"] <= $temps_v["fin"]) {
					// Si la fin d'un cours existant d�passe le d�but du cours qu'on veut cr�er, on renvoie 'non'
					return 'non';
				}else{
					$prof_libre = 'oui';
				}
			}elseif($heuredebut_v[$a] == $heurededebutducours){
				// Le cas o� les cours existants commencent sur le m�me cr�neau que le cours � cr�er
				if ($heuredeb_dec_v[$a] == $heuredeb_dec) {
					// les deux cours se chevauchent
					return 'non';
				}elseif($heuredeb_dec_v[$a] < $heuredeb_dec){
					// le cours existant commence juste avant celui qu'on veut cr�er
					// Si sa dur�e d�passe ou �gale 2, alors il y a chevauchement
					if ($duree_v[$a] >= 2) {
						return 'non';
					}else{
						$prof_libre = 'oui';
					}
				}else{
					// le cours existant commence juste apr�s celui qu'on veut cr�er
					// Si la dur�e du cours � cr�er d�passe ou �gale 2, alors il y a chevauchement
					if ($duree >= 2) {
						return 'non';
					}else{
						$prof_libre = 'oui';
					}
				}

			}else{
				// Nous sommes dans le cas o� des cours existants commencent apr�s le cours � cr�er
				if ($verif_temps["fin"] >= $temps_v["deb"]) {
					// Alors on ne peut pas cr�er un nouveau cours la dessus
					return 'non';
				}else{
					$prof_libre = 'oui';
				}
			}
		}
	}
	return $prof_libre;
}

// Fonction qui v�rifie si la salle est libre pour ce nouveau cours
function verifSalle($salle, $jour, $creneau, $duree, $heuredeb_dec, $type_semaine){

		$sallelibre = "oui";
	// On commence par v�rifier le creneau demand� TEST1
	$requete = mysql_query("SELECT id_cours FROM edt_cours WHERE
				id_salle = '".$salle."'
				AND jour_semaine = '".$jour."'
				AND id_definie_periode = '".$creneau."'
				AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
				AND heuredeb_dec = '".$heuredeb_dec."'")
					OR DIE('Erreur dans la v�rification TEST1 : '.mysql_error());
	$verif = mysql_num_rows($requete);
	// S'il y a une r�ponse, c'est que la salle est d�j� prise
	if ($verif >= 1) {
		$sallelibre = "non TEST1";
	}
	//echo "TEST1 : ".$verif."|".$sallelibre."<br />";

	// On v�rifie alors les cr�neaux pr�c�dents TEST2
	$nbre_tests = nombreCreneauxPrecedent($creneau) + 1;
	$test_creneau = $creneau;
	$heuredeb_dec_i = inverseHeuredeb_dec($heuredeb_dec);

		// d'abord sur le m�me $heuredeb_dec
	for($a = 1; $a < $nbre_tests; $a++) {
		$test_creneau = creneauPrecedent($test_creneau);
		$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
					id_salle = '".$salle."'
					AND jour_semaine = '".$jour."'
					AND id_definie_periode = '".$test_creneau."'
					AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
					AND heuredeb_dec = '".$heuredeb_dec."'")
						OR DIE('Erreur dans la v�rification TEST2a : '.mysql_error());
		$verif = mysql_fetch_array($requete);
		// Si la duree du cours pr�c�dent exc�de le cours qu'on veut cr�er, c'est pas possible (sauf si la semaine en question l'exige)
		if ($verif["duree"] > (2 * $a)) {
			$sallelibre = "non TEST2a ".$verif["id_cours"];
		}
		//echo "TEST2a(".$a.") : ".$verif."|".$sallelibre."<br />";

		// Puis on v�rifie en inverseHeuredeb_dec($heuredeb_dec)
		$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
					id_salle = '".$salle."'
					AND jour_semaine = '".$jour."'
					AND id_definie_periode = '".$test_creneau."'
					AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
					AND heuredeb_dec = '".$heuredeb_dec_i."'")
						OR DIE('Erreur dans la v�rification TEST2b : '.mysql_error());
		$verif = mysql_fetch_array($requete);
		// Si la duree du cours pr�c�dent exc�de le cours qu'on veut cr�er, c'est pas possible (sauf si la semaine en question l'exige)
		if ($verif["duree"] > ((2 * $a) + 1)) {
			$sallelibre = "non TEST2b";
		}
		//echo "TEST2b(".$a.") : ".$verif."|".$sallelibre."<br />";

	} // fin du for($a

	// En fonction du cours appel�, on v�rifie les cr�neaux suivant si la dur�e exc�de 1 cr�neau
	// TEST3
	$nbre_tests = nombreCreneauxApres($creneau) + 1;
	$creneau_s = $creneau;
	$creneau_a = creneauSuivant($creneau);
	// On v�rifie d'abord le demi-creneau suivant
	if ($duree > 1) {
		if ($heuredeb_dec == 0) {
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
					id_salle = '".$salle."'
					AND jour_semaine = '".$jour."'
					AND id_definie_periode = '".$creneau."'
					AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
					AND heuredeb_dec = '0.5'")
						OR DIE('Erreur dans la v�rification TEST3a : '.mysql_error());
		}elseif ($heuredeb_dec == "0.5"){
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
					id_salle = '".$salle."'
					AND jour_semaine = '".$jour."'
					AND id_definie_periode = '".$creneau_a."'
					AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
					AND heuredeb_dec = '0'")
						OR DIE('Erreur dans la v�rification TEST3b : '.mysql_error());
		}
		$verif = mysql_num_rows($requete);
		if ($verif >= 1) {
			$sallelibre = "non TEST3ab";
		}
	}
	// Puis on v�rifie tous les cours suivants pour �tre certain que la dur�e du cours � cr�er n'empi�te pas sur un autre
	// mais d'abord, si la dur�e est de 1/2 cr�neau, on renvoie un "oui"
	if ($duree == '1') {
		return "oui";
	}
	// sinon on v�rifie en fonction de la dur�e demand�e
	for($b = 1; $b < $nbre_tests; $b++) {
			$creneau_s = creneauSuivant($creneau_s);
		if ($duree > ($b * 2)) {
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
				id_salle = '".$salle."'
				AND jour_semaine = '".$jour."'
				AND id_definie_periode = '".$creneau_s."'
				AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
				AND heuredeb_dec = '".$heuredeb_dec."'")
					OR DIE('Erreur dans la v�rification TEST3c : '.mysql_error());
			$verif = mysql_num_rows($requete);
		}elseif($duree > (($b * 2) + 1)) {
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
				id_salle = '".$salle."'
				AND jour_semaine = '".$jour."'
				AND id_definie_periode = '".$creneau_s."'
				AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
				AND heuredeb_dec = '".$heuredeb_dec_i."'")
					OR DIE('Erreur dans la v�rification TEST3d : '.mysql_error());
			$verif = mysql_num_rows($requete);
		}
		if ($verif >= 1) {
			$sallelibre = "non TEST3cd";
		}
	} // fin du for($b...

	return $sallelibre;
} // verifSalle()

// Fonction qui v�rifie si un groupe est libre pour le cours appel�
function verifGroupe($groupe, $jour, $creneau, $duree, $heuredeb_dec, $type_semaine){

		$groupelibre = "oui";
		$heuredeb_dec_i = inverseHeuredeb_dec($heuredeb_dec);
	// On v�rifie le cr�neau demand� TEST
	$requete = mysql_query("SELECT id_cours FROM edt_cours WHERE
		id_groupe = '".$groupe."' AND
		jour_semaine = '".$jour."' AND
		id_definie_periode = '".$creneau."' AND
		(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
		heuredeb_dec = '".$heuredeb_dec."'")
			OR DIE('Erreur dans la verification TESTa : '.mysql_error());
	$verif = mysql_num_rows($requete);
	if ($verif >= 1) {
		$groupelibre = "non";
	}
	// Dans le cas o� le cours commence au milieu du cr�neau, il faut v�rifier le demi-creneau pr�c�dent
	if ($heuredeb_dec == "O.5") {
		$requete = mysql_query("SELECT id_cours FROM edt_cours WHERE
			id_groupe = '".$groupe."' AND
			jour_semaine = '".$jour."' AND
			id_definie_periode = '".$creneau."' AND
			(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
			heuredeb_dec = '0'")
				OR DIE('Erreur dans la verification TESTb : '.mysql_error());
		$verif = mysql_fetch_array($requete);
		if ($verif["duree"] > 1) {
			$groupelibre = "non";
		}
	}
	// On v�rifie les cr�neaux avant TEST1
	$nbre_tests = nombreCreneauxPrecedent($creneau) + 1;
	$creneau_test = $creneau;
	for($a = 1; $a < $nbre_tests; $a++){
		$creneau_test = creneauPrecedent($creneau_test);
		// Premier test sur le cr�neau pr�c�dent avec le m�me d�but (m�me $heuredeb_dec)
		$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
			id_groupe = '".$groupe."' AND
			jour_semaine = '".$jour."' AND
			id_definie_periode = '".$creneau_test."' AND
			(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
			heuredeb_dec = '".$heuredeb_dec."'")
				OR DIE('Erreur dans la verification TEST1a : '.mysql_error());
		$verif = mysql_fetch_array($requete);
		if ($verif["duree"] > (2 * $a)) {
			$groupelibre = "non";
		}
		// Deuxi�me test sur le cr�neau pr�c�dent avec le d�but invers� (inverseHeuredeb_dec($heuredeb_dec))
		$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
			id_groupe = '".$groupe."' AND
			jour_semaine = '".$jour."' AND
			id_definie_periode = '".$creneau_test."' AND
			(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
			heuredeb_dec = '".$heuredeb_dec_i."'")
				OR DIE('Erreur dans la verification TEST1b : '.mysql_error());
		$verif = mysql_fetch_array($requete);
		if ($verif["duree"] > ((2 * $a) + 1)) {
			$groupelibre = "non";
		}
		// Quand un cours commence sur le d�but du cr�neau, on v�rifie le demi-cours pr�c�dent
		// dont la dur�e ne doit pas exc�der 1 (seulement dans le premier tour de la boucle)
		if($heuredeb_dec == "0" AND $a == 1){
			if ($verif["duree"] > 1) {
				$groupelibre = "non";
			}
		}
	} // fin du for($a...

	// Si la dur�e du cours demand� d�passe 1, on v�rifie alors les cr�neaux suivants TEST2
	$nbre_tests = nombreCreneauxApres($creneau) + 1;
	$creneau_test = $creneau;

	// SI le cours commence au milieu du creneau, on v�rifie le demi-creneau suivant
	if ($heuredeb_dec == "0.5" AND $duree > 1) {
		$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
			id_groupe = '".$groupe."' AND
			jour_semaine = '".$jour."' AND
			id_definie_periode = '".creneauSuivant($creneau)."' AND
			(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
			heuredeb_dec = '0'")
				OR DIE('Erreur dans la verification TEST2 : '.mysql_error());
		$verif = mysql_num_rows($requete);
		if ($verif >= 1) {
			$groupelibre = "non";
		}
	}

	for($b = 1; $b < $nbre_tests; $b++) {
		$creneau_test = creneauSuivant($creneau_test);

		if ($duree >= 2 * $b) {
			// Premier test sur le cr�neau suivant avec le m�me d�but (m�me $heuredeb_dec)
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
				id_groupe = '".$groupe."' AND
				jour_semaine = '".$jour."' AND
				id_definie_periode = '".$creneau_test."' AND
				(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
				heuredeb_dec = '".$heuredeb_dec."'")
					OR DIE('Erreur dans la verification TEST2a : '.mysql_error());
			$verif = mysql_num_rows($requete);
			if ($verif >= 1) {
				$groupelibre = "non";
			}
			// Deuxi�me test sur le cr�neau suivant avec le d�but invers� (inverseHeuredeb_dec($heuredeb_dec))
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
				id_groupe = '".$groupe."' AND
				jour_semaine = '".$jour."' AND
				id_definie_periode = '".$creneau_test."' AND
				(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
				heuredeb_dec = '".$heuredeb_dec_i."'")
					OR DIE('Erreur dans la verification TEST2b : '.mysql_error());
			$verif_b = mysql_num_rows($requete);
			if ($verif_b >= 1) {
				$groupelibre = "non";
			}
		}
	//echo "TEST2b($b)".$creneau_test."|".$verif."|".$verif_b."|".$duree."|".$heuredeb_dec."|".$heuredeb_dec_i."<br />";
	} // fin du fror($b...

	return $groupelibre;
} // verifGroupe()
?>