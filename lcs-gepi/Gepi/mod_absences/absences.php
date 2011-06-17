<?php

/**
 *
 * @version $Id: absences.php 6281 2011-01-04 17:26:47Z crob $
 *
 * Fichier destin� � g�rer les acc�s responsables et �l�ves du module absences
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// ================================ Initiatisation de base ======================
$niveau_arbo = 1;
// Initialisations files
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//On v�rifie si le module est activ�
if (substr(getSettingValue("active_module_absence"),0,1)!='y') {
	header("Location: ../accueil.php");
    die("Le module n'est pas activ�.");
}
elseif (substr(getSettingValue("active_absences_parents"),0,1)!='y'){
	// On v�rifie aussi que l'acc�s parents est bien autoris�
	header("Location: ../accueil.php");
	die("Le module n'est pas activ�.");
}

// =============================== fin initialisation de base ===================
// =============================== Ensemble des op�rations php ==================

// on met le header ici pour r�cup�rer des infos sur les enfants
$style_specifique = 'mod_absences/styles/parents_absences';
$javascript_specifique = '';
//**************** EN-TETE *****************
$titre_page = "Les absences";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On r�cup�re du header les infos sur les enfants : $tab_tmp_ele
// S�curit� suppl�mentaire car il faut n�cessairement �tre un responsable pour avoir ces infos
$aff_absences = array();
$nbre = count($tab_tmp_ele);

for($i = 0; $i < $nbre; ){
	$aff_absences[$i] = '';
	$n = $i + 1; // pour le nom et le pr�nom de l'�l�ve

	// On r�cup�re toutes les absences qui correspondent � ce login
	$query = mysql_query("SELECT * FROM absences_eleves WHERE eleve_absence_eleve = '".$tab_tmp_ele[$i]."' ORDER BY a_date_absence_eleve")
					OR DIE('Erreur dans la r�cup�ration des absences de votre enfant : '.mysql_error());
	$nbre_absence = mysql_num_rows($query);

	// et on les mets en forme
	for($a = 0; $a < $nbre_absence; $a++){
		// on r�cup�re ce dont on a besoin
		$abs[$a]["d_date_absence_eleve"] = mysql_result($query, $a, "d_date_absence_eleve");
		$abs[$a]["a_date_absence_eleve"] = mysql_result($query, $a, "a_date_absence_eleve");
		$abs[$a]["heuredeb_absence"] = mysql_result($query, $a, "d_heure_absence_eleve");
		$abs[$a]["heurefin_absence"] = mysql_result($query, $a, "a_heure_absence_eleve");
		$abs[$a]["justification"] = mysql_result($query, $a, "justify_absence_eleve");
		$abs[$a]["type"] = mysql_result($query, $a, "type_absence_eleve");
		$abs[$a]["id"] = mysql_result($query, $a, "id_absence_eleve");
		// on v�rifie le type
		if ($abs[$a]["type"] == "A") {
			$type = "<td style=\"abs\">Abs.</td>";
		}elseif($abs[$a]["type"] == "R"){
			$type = "<td style=\"ret\">Ret.</td>";
		}else {
			$type = "<td>-</td>";

		}
		// on v�rifie la justification
		if ($abs[$a]["justification"] == "N") {
			$justifie = "<td>Non justifi�e</td>";
		}elseif($abs[$a]["justification"] == "T"){
			$justifie = "<td>Par tel.</td>";
		}elseif($abs[$a]["justification"] == "O"){
			$justifie = "<td>Oui</td>";
		}else{
			$justifie = "<td> - </td>";
		}
		// on construit la ligne
		$aff_absences[$a] = '
			<tr>
				<td>'.$tab_tmp_ele[$n].'</td>'
				.$type.'
				<td>Du '.$abs[$a]["d_date_absence_eleve"].' � '.$abs[$a]["heuredeb_absence"].'</td>
				<td>Au '.$abs[$a]["a_date_absence_eleve"].' � '.$abs[$a]["heurefin_absence"].'</td>'
				.$justifie.'
				<td>non</td>
			</tr>
		';

	}
	// On v�rifie si les bulletins ont �t� renseign�s pour les diff�rentes p�riodes
	$query_b = mysql_query("SELECT * FROM absences WHERE login = '".$tab_tmp_ele[$i]."' ORDER BY periode");
	$verif = mysql_num_rows($query_b);
		$aff_absences_bulletin = '';
	if ($verif >= 1) {
		$aff_absences_bulletin .= '<br /><br />
		<table id="absBull">
			<caption title="Ces absences sont enregistr�es sur le bulletin apr�s traitement et v�rification.">
			Les absences retenues sur le bulletin</caption>
			<thead>
				<tr>
					<th>El�ve concern�</th>
					<th>P�riode</th>
					<th>Nbre d\'absences</th>
					<th>dont non justifi�es</th>
					<th>Nbre de retards</th>
					<th>Appr�ciation</th>
				</tr>
			</thead>
			<tbody>
		';
		for($ab = 0; $ab < $verif; $ab++){
			$absbull[$ab]["periode"] = mysql_result($query_b, $ab, "periode");
			$absbull[$ab]["nb_absences"] = mysql_result($query_b, $ab, "nb_absences");
			$absbull[$ab]["non_justifie"] = mysql_result($query_b, $ab, "non_justifie");
			$absbull[$ab]["nb_retards"] = mysql_result($query_b, $ab, "nb_retards");
			$absbull[$ab]["appreciation"] = mysql_result($query_b, $ab, "appreciation");
			if ($absbull[$ab]["appreciation"] == "") {
				$appreciation = "Aucune";
			}else {
				$appreciation = $absbull[$ab]["appreciation"];
			}
			// On construit le tableau
			$aff_absences_bulletin .= '
				<tr>
				<td>'.$tab_tmp_ele[$n].'</td>
				<td>'.$absbull[$ab]["periode"].'</td>
				<td>'.$absbull[$ab]["nb_absences"].'</td>
				<td>'.$absbull[$ab]["non_justifie"].'</td>
				<td>'.$absbull[$ab]["nb_retards"].'</td>
				<td>'.$appreciation.'</td>
				</tr>
			';
		}
		$aff_absences_bulletin .= '</tbody></table>'."\n";
	} // if ($verif >= 1)...


	$i = $i + 2;
// MODIF } // fin for($i = 0; $i < count($tab_tmp_ele); ...

// =============================== Fin des op�rations php =======================

?>
<!-- Debut de la page absences parents -->
<h2>Les Absences de <?php echo $tab_tmp_ele[$n]; ?></h2>

<table id="abs">
	<caption title="Ces absences sont l'ensemble des saisies enregistr&eacute;es avant v&eacute;rification">Les absences enregistr&eacute;es dans l'&eacute;tablissement</caption>
	<thead>
		<tr>
			<th>El&egrave;ve concern&eacute;</th>
			<th>Abs. / Ret.</th>
			<th>Date et heure de d&eacute;but de l'absence</th>
			<th>Date et heure de fin de l'absence</th>
			<th>Justification</th>
			<th>Proposer un justificatif d'absence</th>
		</tr>
	</thead>
	<tbody>

<?php // on affiche toutes les lignes
for($c = 0; $c < $nbre_absence; $c++){
	echo $aff_absences[$c]."\n";
}
?>
	</tbody>

</table>

<?php // Si les bulletins sont renseign�s, on affiche les infos relatives aux absences
  if (isset($aff_absences_bulletin) AND $aff_absences_bulletin != "") {
    echo $aff_absences_bulletin;
  }
} // fin for($i = 0; $i < count($tab_tmp_ele)
echo "<!-- fin de la page absences parents -->";
// on inclut le footer
require("../lib/footer.inc.php");
?>