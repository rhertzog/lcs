<?php

/**
 * EdT Gepi : le menu pour les includes require_once().
 *
 * @version $Id: menu.inc.php 3223 2009-06-18 10:26:37Z jjocal $
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

// S�curit� : �viter que quelqu'un appelle ce fichier seul
$serveur_script = $_SERVER["SCRIPT_NAME"];
$analyse = explode("/", $serveur_script);
	if ($analyse[3] == "menu.inc.php") {
		die();
	}

//===========================INITIALISATION DES VARIABLES=======

// Pour �viter d'avoir un d�calage dans les infobulles
$pas_de_decalage_infobulle = "oui";

// AJOUT: boireaus
$voirgroup=isset($_GET['voirgroup']) ? $_GET['voirgroup'] : (isset($_POST['voirgroup']) ? $_POST['voirgroup'] : NULL);
$visioedt=isset($_GET['visioedt']) ? $_GET['visioedt'] : (isset($_POST['visioedt']) ? $_POST['visioedt'] : NULL);
$salleslibres=isset($_GET['salleslibres']) ? $_GET['salleslibres'] : (isset($_POST['salleslibres']) ? $_POST['salleslibres'] : NULL);

	// D�terminer l'include dans le div id=lecorps
if ($salleslibres == "ok") $page_inc_edt = 'edt_chercher.php';
elseif ($visioedt == 'eleve1') $page_inc_edt = 'voir_edt_eleve.php';
elseif (($visioedt == 'prof1') OR ($visioedt == 'classe1') OR ($visioedt == 'salle1')) $page_inc_edt = 'voir_edt.php';
else $page_inc_edt = '';
//===========================

// Fonction qui g�re le fonctionnement du menu
function menuEdtJs($numero){
	$aff_menu_edt = "";
	// On r�cup�re la valeur du r�glage "param_menu_edt"
	$reglage = mysql_fetch_array(mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'param_menu_edt'"));
	if ($reglage["valeur"] == "mouseover") {
		$aff_menu_edt = " onmouseover=\"javascript:montre('sEdTmenu".$numero."');\"";
	} elseif ($reglage["valeur"] == "click") {
		$aff_menu_edt = " onclick=\"javascript:montre('sEdTmenu".$numero."');\"";
	} else {
		$aff_menu_edt = "";
	}
	return $aff_menu_edt;
}
function displaydd($numero){
		// On r�cup�re la valeur du r�glage "param_menu_edt"
	$reglage = mysql_fetch_array(mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'param_menu_edt'"));
	if ($reglage["valeur"] == "rien") {
		return " style=\"display: block;\"";
	}else {
		return "style=\"display: none;\"";
		//return "";
	}
}
function statutAutreSetting(){
	// On cherche quel est le droit dont dispose cet utilisateur 'autre'
	if ($_SESSION["statut"] == 'autre') {
		$query = mysql_query("SELECT autorisation FROM droits_speciaux WHERE id_statut = '".$_SESSION["statut_special_id"]."' AND nom_fichier = '/tous_les_edt'");
		$rep = mysql_result($query, "autorisation");

		if ($rep["autorisation"] == 'V') {
			return 'oui';
		}else{
			return 'non';
		}
	}else{
		return 'oui';
	}
}
?>
<!-- On affiche le menu edt -->

	<div id="agauche">

<?php
if (getSettingValue("use_only_cdt") == 'y' AND $_SESSION["statut"] == 'professeur'){}else{ ?>
  		<span class="refus"><?php echo ('Semaine n� '.date("W")); ?></span>

		<dl id="menu_edt">
			<dd>
<br />
			</dd>

		<dt<?php echo menuEdtJs("1"); ?>>Visionner</dt>

			<dd id="sEdTmenu1"<?php echo displaydd("1"); ?>>
				<ul>
					<?php if (statutAutreSetting() == 'oui') {
						echo '
					<li><a href="index_edt.php?visioedt=prof1">Professeur</a></li>';
					}?>
					<li><a href="index_edt.php?visioedt=classe1">Classe</a></li>
					<?php if (statutAutreSetting() == 'oui') {
						echo '
					<li><a href="index_edt.php?visioedt=salle1">Salle</a></li>';
					}?>
					<li><a href="index_edt.php?visioedt=eleve1">El&egrave;ve</a></li>
				</ul>
			</dd>
<?php /*
if ($_SESSION['statut'] == "administrateur") {
echo '
		<dt'.menuEdtJs("2").'>Modifier</dt>

			<dd id="sEdTmenu2"'.displaydd("2").'>
				<ul>
					<li><a href="modif_edt_tempo.php">temporairement</a></li>
				</ul>
			</dd>';
}*/

	// La fonction chercher_salle est param�trable
$aff_cherche_salle = GetSettingEdt("aff_cherche_salle");
	if ($aff_cherche_salle == "tous") {
		$aff_ok = "oui";
	}
	else if ($aff_cherche_salle == "admin") {
		$aff_ok = "administrateur";
	}
	else
	$aff_ok = "non";
	// En fonction du r�sultat, on propose l'affichage ou non
	if ($aff_ok == "oui" OR $_SESSION["statut"] == $aff_ok) {
		echo '
		<dt'.menuEdtJs("3").'>Chercher</dt>

			<dd id="sEdTmenu3"'.displaydd("3").'>
				<ul>
					<li><a href="index_edt.php?salleslibres=ok">Salles libres</a></li>
				</ul>
			</dd>
			';
	}

if ($_SESSION['statut'] == "administrateur") {
	echo '
		<dt'.menuEdtJs("4").'>Admin</dt>

			<dd id="sEdTmenu4"'.displaydd("4").'>
				<ul>
					<li style="font-size: 0.9em;"><a href="voir_groupe.php">Les enseignements</a></li>';

if (getSettingValue("mod_edt_gr") == "y") {
	echo '
	<li><a href="../edt_gestion_gr/edt_aff_gr.php">Les groupes</a></li>';
}

	echo '
					<li><a href="ajouter_salle.php">Les Salles</a></li>
					<li><a href="edt_initialiser.php">Initialiser</a></li>
					<li><a href="edt_parametrer.php">Param�trer</a></li>
					<li><a href="edt_param_couleurs.php">Couleurs</a></li>
				</ul>
			</dd>

		<dt'.menuEdtJs("5").'>Calendrier</dt>

			<dd id="sEdTmenu5"'.displaydd("5").'>
				<ul>
					<li><a href="edt_calendrier.php">P�riodes</a></li>
					<li><a href="../mod_absences/admin/admin_config_semaines.php?action=visualiser">Semaines</a></li>
				</ul>
			</dd>
		';
}
?>
		</dl>
<br />
<?php
}
?>
	</div>