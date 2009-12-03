<?php

/**
 * Fichier destin� � permettre la modification d'un cours
 *
 * @version $Id: modifier_cours.php 2147 2008-07-23 09:01:04Z tbelliard $
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

$titre_page = "Modifier un cours de l'emploi du temps";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// S�curit�
// INSERT INTO droits VALUES ('/edt_organisation/modifier_cours.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'Modifier un cours', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// S�curit� suppl�mentaire par rapport aux param�tres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die('Vous devez demander � votre administrateur l\'autorisation de voir cette page.');
}
// On v�rifie que le droit soit le bon pour le profil scolarit�
	$autorise = "non";
if ($_SESSION["statut"] == "administrateur") {
	$autorise = "oui";
}
elseif ($_SESSION["statut"] == "scolarite" AND $gepiSettings['scolarite_modif_cours'] == "y") {
	$autorise = "oui";
}
else {
	$autorise = "non";
	exit('Vous n\'�tes pas autoris� � modifier les cours des emplois du temps, contacter l\'administrateur de Gepi');
}


// ===== Initialisation des variables =====
$id_cours = isset($_GET["id_cours"]) ? $_GET["id_cours"] : (isset($_POST["id_cours"]) ? $_POST["id_cours"] : NULL);
$type_edt = isset($_GET["type_edt"]) ? $_GET["type_edt"] : (isset($_POST["type_edt"]) ? $_POST["type_edt"] : NULL);
$identite = isset($_GET["identite"]) ? $_GET["identite"] : (isset($_POST["identite"]) ? $_POST["identite"] : NULL);
$modifier_cours = isset($_POST["modifier_cours"]) ? $_POST["modifier_cours"] : NULL;
$enseignement = isset($_POST["enseignement"]) ? $_POST["enseignement"] : NULL;
$ch_jour_semaine = isset($_POST["ch_jour_semaine"]) ? $_POST["ch_jour_semaine"] : NULL;
$ch_heure = isset($_POST["ch_heure"]) ? $_POST["ch_heure"] : NULL;
$heure_debut = isset($_POST["heure_debut"]) ? $_POST["heure_debut"] : NULL;
$duree = isset($_POST["duree"]) ? $_POST["duree"] : NULL;
$choix_semaine = isset($_POST["choix_semaine"]) ? $_POST["choix_semaine"] : NULL;
$login_salle = isset($_POST["login_salle"]) ? $_POST["login_salle"] : NULL;
$periode_calendrier = isset($_POST["periode_calendrier"]) ? $_POST["periode_calendrier"] : NULL;
//$ = isset($_GET[""]) ? $_GET[""] : (isset($_POST[""]) ? $_POST[""] : NULL);

// Traitement des changements
if (isset($modifier_cours) AND $modifier_cours == "ok") {
	// On modifie sans v�rification ?
	$req_modif = mysql_query("UPDATE edt_cours SET id_groupe = '$enseignement',
	 id_salle = '$login_salle',
	 jour_semaine = '$ch_jour_semaine',
	 id_definie_periode = '$ch_heure',
	 duree = '$duree',
	 heuredeb_dec = '$heure_debut',
	 id_semaine = '$choix_semaine',
	 id_calendrier = '$periode_calendrier'
	WHERE id_cours = '".$id_cours."'")
	or die('Erreur dans la mofication du cours : '.mysql_error().'');
}

if ($req_modif) {
		echo '
		<span class="accept">Ce cours est modifi� !</span>
		<form name="retour" method="post" action="index_edt.php">
		<input type="hidden" name="visioedt" value="'.$type_edt.'1" />
		<input type="hidden" name="login_edt" value="'.$identite.'" />
		<input type="hidden" name="type_edt_2" value="'.$type_edt.'" />
		<input type="image" src="../images/icons/back.png" border="0" name="submit" alt="Revenir" title="Revenir" /> Revenir
		</form>
		';

}


// CSS et js particulier � l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "edt_organisation/style_edt";

// +++++++++++++++ ent�te de Gepi +++++++++
require_once("../lib/header.inc");
// +++++++++++++++ ent�te de Gepi +++++++++

// On ajoute le menu EdT
require_once("./menu.inc.php");


?>

<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php

// Si tout est ok, on affiche le cours re�u en GET ou POST
if ($autorise == "oui") {
	// On r�cup�re les infos sur le cours
	$req_cours = mysql_query("SELECT * FROM edt_cours WHERE id_cours = '".$id_cours."'");
	$rep_cours = mysql_fetch_array($req_cours);

	// On r�cup�re les infos sur le professeur
	$rep_prof = mysql_fetch_array(mysql_query("SELECT nom, prenom FROM utilisateurs WHERE login = '".$identite."'"));

	// On affiche les diff�rents items du cours
echo '
	<fieldset>
		<legend>Modification du cours</legend>
		<form action="modifier_cours.php" name="choix_prof" method="post">

			<h2>'.$rep_prof["prenom"].' '.$rep_prof["nom"].' ('.$id_cours.')</h2>

	<table id="edt_modif">
		<tr class="ligneimpaire">
			<td>
			<select name="enseignement">';

		$tab_enseignements = get_groups_for_prof($identite);
echo '
				<option value="rien">Choix de l\'enseignement</option>
	';


		for($i=0; $i<count($tab_enseignements); $i++) {
	if(isset($rep_cours["id_groupe"])){
		if($rep_cours["id_groupe"] == $tab_enseignements[$i]["id"]){
			$selected=" selected='selected'";
		}
		else{
			$selected="";
		}
	}
	else{
		$selected="";
	}
			echo '
				<option value="'.$tab_enseignements[$i]["id"].'"'.$selected.'>'.$tab_enseignements[$i]["classlist_string"].' : '.$tab_enseignements[$i]["description"].'</option>
				';
		}
echo '
			</select>



			</td>
			<td>
				<select name="ch_jour_semaine">
	';

	// On propose aussi le choix du jour

	$req_jour = mysql_query("SELECT id_horaire_etablissement, jour_horaire_etablissement FROM horaires_etablissement");
	$rep_jour = mysql_fetch_array($req_jour);

	$tab_select_jour = array();

	for($a=0; $a<=count($rep_jour); $a++) {
		$tab_select_jour[$a]["id"] = mysql_result($req_jour, $a, "id_horaire_etablissement");
		$tab_select_jour[$a]["jour_sem"] = mysql_result($req_jour, $a, "jour_horaire_etablissement");

		if(isset($rep_cours["jour_semaine"])){
			if($rep_cours["jour_semaine"] == $tab_select_jour[$a]["jour_sem"]){
				$selected=" selected='selected'";
			}
			else{
				$selected="";
			}
		}
		else{
		$selected="";
		}
		echo '
		<option value="'.$tab_select_jour[$a]["jour_sem"].'"'.$selected.'>'.$tab_select_jour[$a]["jour_sem"].'</option>
		';
	}
echo '
				</select>
			</td>
			<td>
			<select name="ch_heure">
				<option value="rien">Horaire</option>';

	// On propose aussi le choix de l'horaire

	$req_heure = mysql_query("SELECT id_definie_periode, nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	$rep_heure = mysql_num_rows($req_heure);

	for($b=0; $b<$rep_heure; $b++) {

		$tab_select_heure[$b]["id_heure"] = mysql_result($req_heure, $b, "id_definie_periode");
		$tab_select_heure[$b]["creneaux"] = mysql_result($req_heure, $b, "nom_definie_periode");
		$tab_select_heure[$b]["heure_debut"] = mysql_result($req_heure, $b, "heuredebut_definie_periode");
		$tab_select_heure[$b]["heure_fin"] = mysql_result($req_heure, $b, "heurefin_definie_periode");

		if(isset($rep_cours["id_definie_periode"])){
			if($rep_cours["id_definie_periode"] == $tab_select_heure[$b]["id_heure"]){
				$selected=" selected='selected'";
			}
			else{
				$selected="";
			}
		}
		else{
			$selected="";
		}
		echo '
		<option value="'.$tab_select_heure[$b]["id_heure"].'"'.$selected.'>'.$tab_select_heure[$b]["creneaux"].' : '.$tab_select_heure[$b]["heure_debut"].' - '.$tab_select_heure[$b]["heure_fin"].'</option>
		';

	}
echo '
			</select>

			</td>
		</tr>
		<tr class="lignepaire">
			<td>
	';


	// On v�rifie comment ce cours commence
	// A revoir car �a ne marche pas
if (isset($rep_cours["heuredeb_dec"])) {
		if ($rep_cours["heuredeb_dec"] === 0) {
		$selected0 = " selected='selected'";
		$selected5 = '';
	}
	else if ($rep_cours["heuredeb_dec"] == "0.5") {
		$selected0 = '';
		$selected5 = " selected='selected'";
	}
	else {
		$selected0 = "";
		$selected5 = "";
	}
}

echo '
			<select name="heure_debut">
				<option value="0"'.$selected0.'>Le cours commence au d�but d\'un cr�neau</option>
				<option value="0.5"'.$selected5.'>Le cours commence au milieu d\'un cr�neau</option>
			</select>

			</td>
			<td>
	';
	// On d�termine le selected de la duree
	$selected[1] = $selected[2] = $selected[3] = $selected[4] = $selected[5] = $selected[6] = $selected[7] = $selected[8] = '';
	if (isset($rep_cours["duree"])) {
		$selected[$rep_cours["duree"]] = " selected='selected'";
	}
echo '
			<select name="duree">
				<option value="2"'.$selected[2].'>1 heure</option>
				<option value="3"'.$selected[3].'>1.5 heure</option>
				<option value="4"'.$selected[4].'>2 heures</option>
				<option value="5"'.$selected[5].'>2.5 heures</option>
				<option value="6"'.$selected[6].'>3 heures</option>
				<option value="7"'.$selected[7].'>3.5 heures</option>
				<option value="8"'.$selected[8].'>4 heures</option>
				<option value="1"'.$selected[1].'>1/2 heure</option>
			</select>

			</td>
			<td>

			<select name="choix_semaine">
				<option value="0">Toutes les semaines</option>
		';
		// on r�cup�re les types de semaines

	$req_semaines = mysql_query('SELECT SQL_SMALL_RESULT DISTINCT type_edt_semaine FROM edt_semaines LIMIT 5');
	$nbre_semaines = mysql_num_rows($req_semaines);

	for ($s=0; $s<$nbre_semaines; $s++) {
			$rep_semaines[$s]["type_edt_semaine"] = mysql_result($req_semaines, $s, "type_edt_semaine");
			if (isset($rep_cours["id_semaine"])) {
				if ($rep_cours["id_semaine"] == $rep_semaines[$s]["type_edt_semaine"]) {
					$selected = " selected='selected'";
				}
				else $selected = "";
			}
			else $selected = "";
		echo '
				<option value="'.$rep_semaines[$s]["type_edt_semaine"].'"'.$selected.'>Semaine '.$rep_semaines[$s]["type_edt_semaine"].'</option>
		';
	}

echo '
			</select>

			</td>
		</tr>
		<tr class="ligneimpaire">
			<td>

			<select  name="login_salle">
				<option value="rien">Salle</option>
	';
	// Choix de la salle
	$tab_select_salle = renvoie_liste("salle");

	for($c=0;$c<count($tab_select_salle);$c++) {
		if(isset($rep_cours["id_salle"])){
			if($rep_cours["id_salle"] == $tab_select_salle[$c]["id_salle"]){
				$selected=" selected='selected'";
			}
			else{
				$selected="";
			}
		}
		else{
			$selected="";
		}
		echo "
				<option value='".$tab_select_salle[$c]["id_salle"]."'".$selected.">".$tab_select_salle[$c]["nom_salle"]."</option>\n";
	}
echo '
			</select>

			</td>
			<td>

			<select name="periode_calendrier">
				<option value="rien">Ann�e enti�re</option>
	';
	// Choix de la p�riode d�finie dans le calendrier
	$req_calendrier = mysql_query("SELECT * FROM edt_calendrier WHERE etabferme_calendrier = '1' AND etabvacances_calendrier = '0'");
	$nbre_calendrier = mysql_num_rows($req_calendrier);
		for ($a=0; $a<$nbre_calendrier; $a++) {
			$rep_calendrier[$a]["id_calendrier"] = mysql_result($req_calendrier, $a, "id_calendrier");
			$rep_calendrier[$a]["nom_calendrier"] = mysql_result($req_calendrier, $a, "nom_calendrier");

		if(isset($rep_cours["id_calendrier"])){
			if($rep_cours["id_calendrier"] == $rep_calendrier[$a]["id_calendrier"]){
				$selected=" selected='selected'";
			}
			else{
				$selected="";
			}
		}
		else{
			$selected="";
		}

			echo '
				<option value="'.$rep_calendrier[$a]["id_calendrier"].'"'.$selected.'>'.$rep_calendrier[$a]["nom_calendrier"].'</option>
			';
		}

echo '
			</select>

			</td>
			<td>
		<input type="hidden" name="id_cours" value="'.$id_cours.'" />
		<input type="hidden" name="type_edt" value="'.$type_edt.'" />
		<input type="hidden" name="identite" value="'.$identite.'" />
		<input type="hidden" name="modifier_cours" value="ok" />
		<input type="submit" name="Enregistrer" value="Enregistrer" />

			</td>

		</tr>

	</table>
		</form>';



echo '
	</fieldset>
	';

}// if $autorise...
else {
	die();
}
?>

	</div>

<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>