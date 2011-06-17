<?php
/* $Id: saisie_param.php 5984 2010-11-24 14:54:46Z crob $ */
/*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


// Initialisations files
$niveau_arbo = 1;
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

if (isset($_POST['enregistrer_param'])) {
	check_token();
	/*
	if(!isset($msg)){
		$msg="";
	}
	*/
	$msg="";

	if (isset($_POST['fb_academie'])) {
		if (!saveSetting("fb_academie", $_POST['fb_academie'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_academie !";
		}
	}

	if (isset($_POST['fb_departement'])) {
		if (!saveSetting("fb_departement", $_POST['fb_departement'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_departement !";
		}
	}

	if (isset($_POST['fb_session'])) {
		if (!saveSetting("fb_session", $_POST['fb_session'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_session !";
		}
	}


	if (isset($_POST['fb_mode_moyenne'])) {
		if (!saveSetting("fb_mode_moyenne", $_POST['fb_mode_moyenne'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_mode_moyenne !";
		}
	}

	if($msg==""){$msg="Enregistrement effectu�.";}
}





//echo '<link rel="stylesheet" type="text/css" media="print" href="impression.css">';

//**************** EN-TETE *****************
$titre_page = "Param�tres Fiches Brevet";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='../accueil.php'>Accueil</a>";
echo " | <a href='index.php'>Accueil Notanet</a>";
echo "</p>\n";
echo "</div>\n";

echo "<h2>Param�tres des Fiches Brevet</h2>\n";

echo "<form action='".$_SERVER['PHP_SELF']."' name='form_param' method='post'>\n";
echo add_token_field();
echo "<table border='0' summary='Param�tres'>\n";

$alt=1;
$fb_academie=getSettingValue("fb_academie");
$alt=$alt*(-1);
echo "<tr";
if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
echo ">\n";
echo "<td>Acad�mie de: </td>\n";
echo "<td><input type='text' name='fb_academie' value='$fb_academie' /></td>\n";
echo "</tr>\n";

$fb_departement=getSettingValue("fb_departement");
$alt=$alt*(-1);
echo "<tr";
if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
echo ">\n";
echo "<td>D�partement de: </td>\n";
echo "<td><input type='text' name='fb_departement' value='$fb_departement' /></td>\n";
echo "</tr>\n";

$fb_session=getSettingValue("fb_session");
//echo "<tr><td colspan='2'>\$fb_session=$fb_session</td></tr>";
if($fb_session==""){
	$tmp_date=getdate();
	$tmp_mois=$tmp_date['mon'];
	if($tmp_mois>9){
		$fb_session=$tmp_date['year']+1;
	}
	else{
		$fb_session=$tmp_date['year'];
	}
}
//echo "<tr><td colspan='2'>\$fb_session=$fb_session</td></tr>";
$alt=$alt*(-1);
echo "<tr";
if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
echo ">\n";
echo "<td>Session: </td>\n";
echo "<td><input type='text' name='fb_session' value='$fb_session' /></td>\n";
echo "</tr>\n";

// ****************************************************************************
// MODE DE CALCUL POUR LES MOYENNES DES REGROUPEMENTS DE MATIERES:
// - LV1: on fait la moyenne de toutes les LV1 (AGL1, ALL1)
// ou
// - LV1: on pr�sente pour chaque �l�ve, la moyenne qui correspond � sa LV1: ALL1 s'il fait ALL1,...
// ****************************************************************************
$fb_mode_moyenne=getSettingValue("fb_mode_moyenne");
$alt=$alt*(-1);
echo "<tr";
if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
echo ">\n";
echo "<td valign='top'>Mode de calcul des moyennes pour les options Notanet associ�es � plusieurs mati�res (<i>ex.: LV1 associ�e � AGL1 et ALL1</i>): </td>\n";
echo "<td>";
	echo "<table border='0' summary='Mode de calcul des moyennes'>\n";
	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='fb_mode_moyenne' id='fb_mode_moyenne_1' value='1' ";
	if($fb_mode_moyenne!="2"){
		echo "checked />";
	}
	else{
		echo "/>";
	}
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='fb_mode_moyenne_1'>Calculer la moyenne de toutes mati�res d'une m�me option Notanet confondues<br />\n";
	echo "(<i>on compte ensemble les AGL1 et ALL1; c'est la moyenne de toute la LV1 qui est effectu�e</i>)</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='fb_mode_moyenne' id='fb_mode_moyenne_2' value='2' ";
	if($fb_mode_moyenne=="2"){
		echo "checked />";
	}
	else{
		echo "/>";
	}
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='fb_mode_moyenne_2'>Calculer les moyennes par mati�res<br />\n";
	echo "(<i>on ne m�lange pas AGL1 et ALL1 dans le calcul de la moyenne de classe pour un �l�ve</i>)</label>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

echo "</td>\n";
echo "</tr>\n";

echo "</table>\n";
echo "<p align='center'><input type='submit' name='enregistrer_param' value='Enregistrer' /></p>\n";
echo "</form>\n";

require("../lib/footer.inc.php");
?>
