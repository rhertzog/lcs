<?php
/*
* @version: $Id: add_modif_cc_eval.php 6721 2011-03-28 19:47:06Z crob $
*
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
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session=$session_gepi->security_check();
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
if (getSettingValue("active_carnets_notes")!='y') {
	die("Le module n'est pas activ�.");
}

require('cc_lib.php');

$id_racine=isset($_POST["id_racine"]) ? $_POST["id_racine"] : (isset($_GET["id_racine"]) ? $_GET["id_racine"] : NULL);

if(!isset($id_racine)) {
	$mess="Racine non pr�cis�e pour $nom_cc.<br />";
	header("Location: index.php?msg=$mess");
	die();
}

// On teste si le carnet de notes appartient bien � la personne connect�e
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de p�n�trer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}

$appel_cahier_notes=mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe=mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group=get_group($id_groupe);
$periode_num=mysql_result($appel_cahier_notes, 0, 'periode');
include "../lib/periodes.inc.php";

$id_dev=isset($_POST["id_dev"]) ? $_POST["id_dev"] : (isset($_GET["id_dev"]) ? $_GET["id_dev"] : NULL);
if(!isset($id_dev)) {
	$mess="$nom_cc non pr�cis�.<br />";
	header("Location: index_cc.php?id_racine=$id_racine&msg=$mess");
	die();
}

$sql="SELECT * FROM cc_dev WHERE id='$id_dev' AND id_groupe='$id_groupe';";
$query=mysql_query($sql);
if($query) {
	$id_cn_dev=mysql_result($query, 0, 'id_cn_dev');
	$nom_court_dev=mysql_result($query, 0, 'nom_court');
	$nom_complet_dev=mysql_result($query, 0, 'nom_complet');
	$description_dev=mysql_result($query, 0, 'description');
}
else {
	header("Location: index.php?msg=".rawurlencode("Le num�ro de devoir n est pas associ� � ce groupe."));
	die();
}

$id_eval=isset($_POST["id_eval"]) ? $_POST["id_eval"] : (isset($_GET["id_eval"]) ? $_GET["id_eval"] : NULL);
if(isset($id_eval))  {
	$sql="SELECT * FROM cc_eval WHERE id='$id_eval';";
	$query=mysql_query($sql);
	if($query) {
		// V�rifier que l'�valuation est bien associ�e au CC.
		$sql="SELECT * FROM cc_eval WHERE id='$id_eval' AND id_dev='$id_dev';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$mess="L'�valuation n�$id_eval n'est pas associ�e au $nom_cc n�$id_dev.<br />";
			header("Location: index_cc.php?id_racine=$id_racine&msg=$mess");
			die();
		}

		$id_cn_dev=mysql_result($query, 0, 'id_cn_dev');
		$nom_court=mysql_result($query, 0, 'nom_court');
		$nom_complet=mysql_result($query, 0, 'nom_complet');
		$description=mysql_result($query, 0, 'description');
		$display_date=mysql_result($query, 0, 'date');
		$note_sur=mysql_result($query, 0, 'note_sur');
	}
	else {
		header("Location: index.php?msg=".rawurlencode("L �valuation n�$id_eval n'existe pas."));
		die();
	}
}
else {
	//header("Location: ../logout.php?auto=1");
	//die();

	$nom_court="Ev";
	$nom_complet="Evaluation n�";
	$description="";
	$display_date=strftime('%d/%m/%Y');
	$note_sur=5;
}

$matiere_nom=$current_group["matiere"]["nom_complet"];
$matiere_nom_court=$current_group["matiere"]["matiere"];
$nom_classe=$current_group["classlist_string"];

// enregistrement des donn�es
if (isset($_POST['ok'])) {
	check_token();

	$nom_court=traitement_magic_quotes($_POST['nom_court']);
	$nom_complet=traitement_magic_quotes($_POST['nom_complet']);
	$description=traitement_magic_quotes($_POST['description']);

	$note_sur=preg_replace('/[^0-9]/','',$_POST['note_sur']);

	if($nom_court=='') {
		$msg="Le nom_court de l'�valuation ne peut pas �tre vide.";
		header("Location: index_cc.php?id_racine=$id_racine&msg=$msg");
		die();
	}
	elseif($note_sur=='') {
		$msg="La valeur de note_sur n'est pas valide.";
		header("Location: index_cc.php?id_racine=$id_racine&msg=$msg");
		die();
	}
	else {
		if(!isset($id_eval)) {
			$sql="INSERT INTO cc_eval SET id_dev='$id_dev';";
			$insert=mysql_query($sql);
			if(!$insert) {
				$msg="Erreur lors de la cr�ation de l'�valuation associ�e au $nom_cc n�$id_dev.";
				header("Location: index_cc.php?id_racine=$id_racine&msg=$msg");
				die();
			}
			else {
				$id_eval=mysql_insert_id();
			}
		}

		if ($_POST['display_date']) {
			if (my_ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $_POST['display_date'])) {
				$annee=substr($_POST['display_date'],6,4);
				$mois=substr($_POST['display_date'],3,2);
				$jour=substr($_POST['display_date'],0,2);
			} else {
				$annee=strftime("%Y");
				$mois=strftime("%m");
				$jour=strftime("%d");
			}
		} else {
			$annee=strftime("%Y");
			$mois=strftime("%m");
			$jour=strftime("%d");
		}
		$date=$annee."-".$mois."-".$jour." 00:00:00";

		$sql="UPDATE cc_eval SET nom_court='$nom_court', nom_complet='$nom_complet', description='$description', note_sur='$note_sur', date='".$date."' WHERE id='$id_eval';";
		$update=mysql_query($sql);
		if(!$insert) {
			$msg="Erreur lors de la cr�ation ou mise � jour de l'�valuation associ�e au $nom_cc n�$id_dev. $sql";
		}
		else {
			$msg="Cr�ation ou mise � jour de l'�valuation associ�e au $nom_cc n�$id_dev effectu�e.";
		}
		header("Location: index_cc.php?id_racine=$id_racine&msg=$msg");
		die();
	}

}

//Configuration du calendrier
include("../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("formulaire", "display_date");

//**************** EN-TETE *****************
$titre_page="Carnet de notes - Ajout/modification d'un $nom_cc";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
echo add_token_field();

echo "<div class='norme'>\n";
echo "<p class='bold'>\n";
echo "<a href='index_cc.php?id_racine=$id_racine'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";
echo "</div>\n";

echo "<h2 class='gepi'>Configuration d'une �valuation de $nom_court_dev (<i>$nom_complet_dev</i>)&nbsp;:</h2>\n";

$aff_nom_court="y";
$aff_nom_complet="y";
$aff_description="y";
$aff_date="y";
$aff_note_sur="y";

echo "<div align='center'>\n";
echo "<table class='boireaus' border='1' summary='Parametres de l �valuation'>\n";

if($aff_nom_court=='y'){
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom court&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='text' name='nom_court' size='40' value=\"".$nom_court."\" autocomplete='off' onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else{
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom court&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name='nom_court' size='40' value=\"".$nom_court."\" onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}


if($aff_nom_complet=='y'){
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom complet&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='text' name='nom_complet' size='40' value=\"".$nom_complet."\" autocomplete='off' onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else{
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom complet&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name='nom_complet' size='40' value=\"".$nom_complet."\" onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}

if($aff_description=='y'){
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Description&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<textarea name='description' rows='2' cols='40' >".$description."</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else{
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Description&nbsp;:</td>\n";
	echo "<td>\n";
	//echo "<textarea name='description' rows='2' cols='40' >".$description."</textarea>\n";
	echo "<input type='hidden' name='description' value='$description' />\n";
	echo "</td>\n";
	echo "</tr>\n";
}

if($aff_note_sur=='y'){
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Note sur&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='text' name='note_sur' size='4' autocomplete='off' onfocus=\"javascript:this.select()\" value=\"".$note_sur."\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else{
	echo "<tr style='display:none;'>\n";
	echo "<td>Note sur:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name='note_sur' size='4' value=\"".$coef."\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}

if($aff_date=='y'){
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Date:</td>\n";
	echo "<td>\n";
	echo "<input type='text' name='display_date' id='display_date' size='10' value=\"".$display_date."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
	echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"";
	echo "><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else{
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Date:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name='display_date' size='10' autocomplete='off' onfocus=\"javascript:this.select()\" value=\"".$display_date."\" onKeyDown=\"clavier_date(this.id,event);\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}

echo "</table>\n";
echo "</div>\n";
echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='hidden' name='id_racine' value='$id_racine' />\n";
echo "<input type='hidden' name='id_dev' value='$id_dev' />\n";

if(isset($id_eval)) {
	echo "<input type='hidden' name='id_eval' value='$id_eval' />\n";
}

if($aff_nom_court=='y'){
	echo "<script type='text/javascript'>
	document.formulaire.nom_court.focus();
</script>\n";
}

echo "<p style='text-align:center;'><input type=\"submit\" name='ok' value=\"Enregistrer\" style=\"font-variant: small-caps;\" /></p>\n";

echo "</form>\n";
echo "<br />\n";
require("../lib/footer.inc.php");
?>
