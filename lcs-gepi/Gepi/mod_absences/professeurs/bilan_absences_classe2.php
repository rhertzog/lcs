<?php

/**
 *
 * @version $Id$
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

$titre_page = "Bilan des absences";
$affiche_connexion = "oui";
$niveau_arbo = 2;
$nobar = 'oui';
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");
// ainsi que les fonctions de l'EdT pour la gestion des créneaux
require_once("../../edt_organisation/fonctions_calendrier.php");
require_once("../../edt_organisation/fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:../../utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../../logout.php?auto=1");
    die();
}

// Sécurité
// SQL : INSERT INTO droits VALUES ( '/mod_absences/professeurs/bilan_absences_classe.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Bilan des absences saisies par classe', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_absences/professeurs/bilan_absences_classe.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Bilan des absences saisies par classe', '');";
/*/
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=2");
    die();
}*/

// ======================== Initialisation des données ==================== //
$id_classe = isset($_GET["id_classe"]) ? $_GET["id_classe"] : (isset($_POST["id_classe"]) ? $_POST["id_classe"] : NULL);
$debut = isset($_POST["debut"]) ? $_POST["debut"] : date("d/m/Y");
$fin = isset($_POST["fin"]) ? $_POST["fin"] : date("d/m/Y");
//$ = isset($_POST[""]) ? $_POST[""] : NULL;

$aff_debug = $aff_nom_classe = $aff_liste_abs = $aff_bilan = $aff_liste_eleves = NULL;


// ======================== Traitement des données ======================== //

	if ($id_classe == NULL) {
		trigger_error("Impossible d'afficher les informations demandées car la classe n'est pas précisée.", E_USER_ERROR);
	}

	// On recherche les renseignements sur cette classe
	$sql_c = "SELECT classe FROM classes WHERE id = '".$id_classe."' LIMIT 1";
	$query_c = mysqli_query($GLOBALS["mysqli"], $sql_c) OR trigger_error('Impossible d\'afficher la classe.', E_USER_WARNING);

	$rep = mysqli_fetch_array($query_c);

	$aff_nom_classe .= $rep["classe"];

	// On calcule les timestamp dont on a besoin
	$choix_date_deb = explode("/", $debut);
	$choix_date_fin = explode("/", $fin);
	$date_deb_ts = mktime(0,0,0, $choix_date_deb[1], $choix_date_deb[0], $choix_date_deb[2]);
	$date_fin_ts = mktime(23,59,60, $choix_date_fin[1], $choix_date_fin[0], $choix_date_fin[2]);
	$ts = $date_deb_ts;

	// Nbre de jours demandés
	$nbre_de_jours = ($date_fin_ts - $date_deb_ts) / 86400;

	// On recherche l'ensemble des absences enregistrées sur ces dates là
	$sql_a = "SELECT id, nom, prenom, retard_absence, debut_ts, fin_ts
				FROM absences_rb a, eleves e
				WHERE debut_ts >= '".$date_deb_ts."'
				AND fin_ts <= '".$date_fin_ts."'
				AND a.eleve_id = e.login
				ORDER BY nom, prenom";
	$query_a = mysqli_query($GLOBALS["mysqli"], $sql_a) OR trigger_error('Impossible de lister les absents.', E_USER_ERROR);

	$aff = get_eleves_classe($id_classe);

		$aff_tab = '<tr><td>Nom</td>';
	for($j = 0 ; $j < $nbre_de_jours ; $j++){

		// On affiche la première ligne  du tableau
		$aff_tab .= '<td>'.date("d/m", $ts).'</td>';
		$date_du_jour[$j] = date("d/m", $ts);
		$ts = $ts + 86400;

	}
		$aff_tab .= '</tr>';

	for($i = 0 ; $i < $aff["nbre"] ; $i++){

		$ts = $date_deb_ts;
		$aff_bilan = NULL;

		for($j = 0 ; $j < $nbre_de_jours ; $j++){

			// On initialise une variable pour calculer le nombre de saisies d'absences pour chaque jour
			$calc[$j] = 0;

			while($rep = mysqli_fetch_array($query_a)){

				// On va calculer le nombre d'entrées saisies pour chaque jour demandé
				if ($rep["debut_ts"] >= $ts AND $rep["fin_ts"] <= ($ts + 86400)) {
					$calc++;
				}
			}
			$aff_bilan .= '<td title="'.$date_du_jour[$j].'">'.$calc[$j].'</td>';

			$ts = $ts + 86400;

		}

		$aff_liste_eleves .= '<tr><td>'.$aff[$i]["nom"].' '.$aff[$i]["prenom"].'</td>'.$aff_bilan.'</tr>'."\n";

	}

	$aff_liste_abs = $aff_liste_eleves;


	// debuggage intensif
	$aff_debug = "\n".$date_deb_ts."\n".$date_fin_ts."\n".$nbre_de_jours."\n";

// ======================== CSS et js particuliers ========================
$utilisation_win = "oui";
//$utilisation_jsdivdrag = "non";
//$javascript_specifique = ".js";
$style_specifique = "mod_absences/styles/bilan_absences";

// ===================== entete Gepi ======================================//
require_once("../../lib/header.inc.php");
// ===================== fin entete =======================================//

echo "<!-- page Bilan_des_absences.".$aff_debug."-->";

?>

<p><a href="./bilan_absences_professeur.php"><img src="../../images/icons/back.png" alt="Revenir en arri&egrave;re" />&nbsp;RETOUR</a></p>

	<form name="time" action="./bilan_absences_classe2.php" method="post">

<h3 class="gepi">Les absences de la classe de <span style="font-weight: bold;"><?php echo $aff_nom_classe; ?>&nbsp;</span>
du&nbsp;
			<input type="text" name="debut" value="<?php echo $debut; ?>" />
			<a href="#calend" onclick="window.open('../../lib/calendrier/pop.calendrier.php?frm=time&amp;ch=debut','calendrier','width=350,height=170,scrollbars=0').focus();">
		<img src="../../lib/calendrier/petit_calendrier.gif" alt="" border="0" /></a>
&nbsp;&nbsp;au&nbsp;
			<input type="text" name="fin" value="<?php echo $fin; ?>" />
			<a href="#calend" onclick="window.open('../../lib/calendrier/pop.calendrier.php?frm=time&amp;ch=fin','calendrier','width=350,height=170,scrollbars=0').focus();">
		<img src="../../lib/calendrier/petit_calendrier.gif" alt="" border="0" /></a>


		<input type="hidden" name="id_classe" value="<?php echo $id_classe; ?>" />
		<input type="submit" name="Valider" value="Valider" />
</h3>
	</form>

<table summary="Liste des absents sur le temps demand&eacute;" id="aff_abs">

	<?php echo $aff_tab; ?>

	<?php echo $aff_liste_abs; ?>

</table>


<?php
// Inclusion du bas de page
require_once("../../lib/footer.inc.php");
?>