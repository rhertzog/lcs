<?php
/*
 * Last modification  : 10/02/2007
 *
 * Copyright 2001, 2006 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

// Global configuration file
// Quand on est en SSL, IE n'arrive pas � ouvrir le PDF.
//Le probl�me peut �tre r�solu en ajoutant la ligne suivante :
Header('Pragma: public');

//=============================
// REMONT�:
// Initialisations files
require_once("../lib/initialisations.inc.php");
//=============================

require('../fpdf/fpdf.php');
require('../fpdf/ex_fpdf.php');

define('FPDF_FONTPATH','../fpdf/font/');
define('LargeurPage','210');
define('HauteurPage','297');

/*
// Initialisations files
require_once("../lib/initialisations.inc.php");
*/

require_once("./class_pdf.php");
require_once ("./liste.inc.php");

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un probl�me qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');

// Resume session
$resultat_session = $session_gepi->security_check();

$ok=isset($_POST['ok']) ? $_POST["ok"] : 0;

if ($ok==0) {
	if ($resultat_session == 'c') {
		header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
		die();
	} else if ($resultat_session == '0') {
		header("Location: ../logout.php?auto=1");
		die();
	};

	//INSERT INTO droits VALUES ('/impression/parametres_impression_pdf.php', 'V', 'V', 'V', 'V', 'V', 'V', 'Impression des listes PDF; r�glage des param�tres', '');
	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}

//**************** EN-TETE **************************************
//$titre_page = "Impression de listes au format PDF <br />Choix des param�tres".$periode;
$titre_page = "Impression de listes au format PDF <br />Choix des param�tres";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

echo "<p class='bold'>";
echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo " | <a href='./impression.php'>Impression rapide � l'unit�</a>";
echo " | <a href='./impression_serie.php'>Impression en s�rie</a>";
echo "</p>\n";

echo "<h3>Choix des param�tres : </h3>\n";

echo "<div>\n
   <fieldset>\n";
       echo "<legend>Modifiez l'apparence du document PDF :</legend>\n";
	   echo "<form method=\"post\" action=\"parametres_impression_pdf.php\" name=\"choix_parametres\">\n";
	   echo "<input value=\"Valider les param�tres\" name=\"Valider\" type=\"submit\" /><br />\n";
       echo "<br />\n";
	   echo "<b>D�finition des marges du document :</b><br />\n";
	   echo "&nbsp;&nbsp;Marge � gauche : <input type=\"text\" name=\"marge_gauche\" size=\"2\" maxlength=\"2\" value=\"10\" /> <br />\n";
	   echo "&nbsp;&nbsp;Marge � droite : <input type=\"text\" name=\"marge_droite\" size=\"2\" maxlength=\"2\" value=\"10\" /> <br />\n";
	   echo "&nbsp;&nbsp;Marge du haut : <input type=\"text\" name=\"marge_haut\" size=\"2\" maxlength=\"2\" value=\"10\" /> <br />\n";
	   echo "&nbsp;&nbsp;Marge du bas : <input type=\"text\" name=\"marge_bas\" size=\"2\" maxlength=\"2\" value=\"10\" /> <br />\n";
	   echo "&nbsp;&nbsp;Option marge reliure ? <input type=\"radio\" name=\"marge_reliure\" value=\"1\" checked /> Oui <input type=\"radio\" name=\"marge_reliure\" value=\"0\" /> Non<br />\n";
	   echo "&nbsp;&nbsp;Option emplacement des perforations classeur  ? <input type=\"radio\" name=\"avec_emplacement_trous\" value=\"1\" checked /> Oui <input type=\"radio\" name=\"avec_emplacement_trous\" value=\"0\" /> Non<br />\n";
	   echo "<br />\n";
	   echo "<b>Informations � afficher sur le document :</b><br />\n";
	   echo "&nbsp;&nbsp;Afficher le professeur responsable de la classe ? <input type=\"radio\" name=\"affiche_pp\" value=\"1\" checked /> Oui <input type=\"radio\" name=\"affiche_pp\" value=\"0\" /> Non<br />\n";
	   echo "&nbsp;&nbsp;Afficher une ligne de texte avant le tableau  ? <input type=\"radio\" name=\"avec_ligne_texte\" value=\"1\" checked /> Oui <input type=\"radio\" name=\"avec_ligne_texte\" value=\"0\" /> Non ";
       echo "&nbsp;Texte : &nbsp;<input type=\"text\" name=\"ligne_texte\" size=\"50\" value=\"&nbsp;\" /> <br />\n";
	   echo "&nbsp;&nbsp;Afficher l'effectif de la classe ? <input type=\"radio\" name=\"afficher_effectif\" value=\"1\" checked /> Oui <input type=\"radio\" name=\"afficher_effectif\" value=\"0\" /> Non<br />\n";
	   echo "<br />\n";
	   echo "<b>Styles du tableau : </b><br />\n";
	   echo "&nbsp;&nbsp;Tout sur une seule page ? <input type=\"radio\" name=\"une_seule_page\" value=\"1\" checked /> Oui <input type=\"radio\" name=\"une_seule_page\" value=\"0\" /> Non<br />\n";
	   echo "&nbsp;&nbsp;Hauteur d'une ligne : <input type=\"text\" name=\"h_ligne\" size=\"2\" maxlength=\"2\" value=\"8\" /> <br />\n";
	   echo "&nbsp;&nbsp;Largeur d'une colonne : <input type=\"text\" name=\"l_colonne\" size=\"2\" maxlength=\"2\" value=\"8\" /> <br />\n";
	   echo "&nbsp;&nbsp;Largeur colonne Nom / Pr�nom : <input type=\"text\" name=\"l_nomprenom\" size=\"2\" maxlength=\"2\" value=\"40\" /> <br />\n";
	   echo "&nbsp;&nbsp;Nombre ligne(s) avant : <input type=\"text\" name=\"nb_ligne_avant\" size=\"2\" maxlength=\"2\" value=\"2\" /> \n";
	   echo "&nbsp;&nbsp;Hauteur de la premi�re ligne avant : <input type=\"text\" name=\"h_ligne1_avant\" size=\"2\" maxlength=\"2\" value=\"25\" /> <br />\n";
	   echo "&nbsp;&nbsp;Nombre ligne(s) apr�s : <input type=\"text\" name=\"nb_ligne_apres\" size=\"2\" maxlength=\"2\" value=\"1\" /> <br />\n";
	   echo "&nbsp;&nbsp;Quadrillage total des cellules ? <input type=\"radio\" name=\"encadrement_total_cellules\" value=\"1\" checked /> Oui [ <input type=\"radio\" name=\"encadrement_total_cellules\" value=\"0\" /> Non \n";
	   echo "&nbsp;&nbsp;Nombre de cellules quadrill�es apr�s le nom : <input type=\"text\" name=\"nb_cellules_quadrillees\" size=\"2\" maxlength=\"2\" value=\"5\" /> ] <br />\n";
       echo "<br />\n";
	   echo "<b>Informations en bas du document : </b><br />\n";
	   echo "&nbsp;&nbsp;R�server une zone vide sous le tableau ? <input type=\"radio\" name=\"zone_vide\" value=\"1\" checked /> Oui <input type=\"radio\" name=\"zone_vide\" value=\"0\" /> Non";
	   echo "&nbsp;&nbsp;Hauteur de la zone : <input type=\"text\" name=\"hauteur_zone_finale\" size=\"2\" maxlength=\"2\" value=\"20\" /> (0 tout ce qui reste)<br />\n";
	   echo "<input value=\"1\" name=\"ok\" type=\"hidden\" />\n";
	   echo "<br />\n";
	   echo "<input value=\"Valider les param�tres\" name=\"Valider\" type=\"submit\" />\n";
       echo "<br />\n";
     echo "</form>\n";
   echo "</fieldset>\n
 </div>";
	require("../lib/footer.inc.php");

/*
marge_gauche
marge_droite
marge_haut
marge_bas
marge_reliure
avec_emplacement_trous
affiche_pp
avec_ligne_texte
ligne_texte
afficher_effectif
une_seule_page
h_ligne
l_colonne
l_nomprenom
nb_ligne_avant
h_ligne1_avant
nb_ligne_apres
encadrement_total_cellules
nb_cellules_quadrillees
zone_vide
hauteur_zone_finale
*/

} else { // if OK

  // On enregistre dans la session et on redirige vers impression_serie.php
  $_SESSION['marge_gauche']=isset($_POST['marge_gauche']) ? $_POST["marge_gauche"] : 10;
  $_SESSION['marge_droite']=isset($_POST['marge_droite']) ? $_POST["marge_droite"] : 10;
  $_SESSION['marge_haut']=isset($_POST['marge_haut']) ? $_POST["marge_haut"] : 10;
  $_SESSION['marge_bas']=isset($_POST['marge_bas']) ? $_POST["marge_bas"] : 10;
  $_SESSION['marge_reliure']=isset($_POST['marge_reliure']) ? $_POST["marge_reliure"] : 1;
  $_SESSION['avec_emplacement_trous']=isset($_POST['avec_emplacement_trous']) ? $_POST["avec_emplacement_trous"] : 1;
  $_SESSION['affiche_pp']=isset($_POST['affiche_pp']) ? $_POST["affiche_pp"] : 1;
  $_SESSION['avec_ligne_texte']=isset($_POST['avec_ligne_texte']) ? $_POST["avec_ligne_texte"] : 1;
  $_SESSION['ligne_texte']=isset($_POST['ligne_texte']) ? $_POST["ligne_texte"] : " ";
  $_SESSION['afficher_effectif']=isset($_POST['afficher_effectif']) ? $_POST["afficher_effectif"] : 1;
  $_SESSION['une_seule_page']=isset($_POST['une_seule_page']) ? $_POST["une_seule_page"] : 1;
  $_SESSION['h_ligne']=isset($_POST['h_ligne']) ? $_POST["h_ligne"] : 8;
  $_SESSION['l_colonne']=isset($_POST['l_colonne']) ? $_POST["l_colonne"] : 8;
  $_SESSION['l_nomprenom']=isset($_POST['l_nomprenom']) ? $_POST["l_nomprenom"] : 40;
  $_SESSION['nb_ligne_avant']=isset($_POST['nb_ligne_avant']) ? $_POST["nb_ligne_avant"] : 2;
  $_SESSION['h_ligne1_avant']=isset($_POST['h_ligne1_avant']) ? $_POST["h_ligne1_avant"] : 25;
  $_SESSION['nb_ligne_apres']=isset($_POST['nb_ligne_apres']) ? $_POST["nb_ligne_apres"] : 1;
  $_SESSION['encadrement_total_cellules']=isset($_POST['encadrement_total_cellules']) ? $_POST["encadrement_total_cellules"] : 1;
  $_SESSION['nb_cellules_quadrillees']=isset($_POST['nb_cellules_quadrillees']) ? $_POST["nb_cellules_quadrillees"] : 5;
  $_SESSION['zone_vide']=isset($_POST['zone_vide']) ? $_POST["zone_vide"] : 1;
  $_SESSION['hauteur_zone_finale']=isset($_POST['hauteur_zone_finale']) ? $_POST["hauteur_zone_finale"] : 20;

  //echo $_SESSION['avec_emplacement_trous'];

  header("Location: ./impression_serie.php");
		die();
}
?>
