<?php
/*
 * $Id: parametres_impression_pdf.php 8061 2011-08-30 22:01:10Z jjacquard $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

if (!defined('FPDF_VERSION')) {
	require_once('../fpdf/fpdf.php');
}
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

$msg="";

if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

//INSERT INTO droits VALUES ('/impression/parametres_impression_pdf.php', 'V', 'V', 'V', 'V', 'V', 'V', 'Impression des listes PDF; r�glage des param�tres', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$choix_action_modele=isset($_POST['choix_action_modele']) ? $_POST['choix_action_modele'] : NULL;
$id_modele=isset($_POST['id_modele']) ? $_POST['id_modele'] : NULL;
//$nom_modele=isset($_POST['nom_modele']) ? $_POST['nom_modele'] : "Modele_".strftime('%Y%m%d_%H%M%S');
$nom_modele=isset($_POST['nom_modele']) ? $_POST['nom_modele'] : NULL;

$nouveau_modele=isset($_POST['nouveau_modele']) ? $_POST['nouveau_modele'] : NULL;
$modif_modele=isset($_POST['modif_modele']) ? $_POST['modif_modele'] : NULL;

$sql="CREATE TABLE IF NOT EXISTS modeles_grilles_pdf (
id_modele INT(11) NOT NULL auto_increment,
login varchar(50) NOT NULL default '',
nom_modele varchar(255) NOT NULL,
par_defaut ENUM('y','n') DEFAULT 'n',
PRIMARY KEY (id_modele)
);";
//echo "$sql<br />";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS modeles_grilles_pdf_valeurs (
id_modele INT(11) NOT NULL,
nom varchar(255) NOT NULL default '',
valeur varchar(255) NOT NULL,
INDEX id_modele_champ (id_modele, nom)
);";
//echo "$sql<br />";
$create_table=mysql_query($sql);

// Contr�le des valeurs valid�es
if(isset($id_modele)) {
	if(($id_modele=='')||(!preg_match("/^[0-9]*$/",$id_modele))) {
		$msg="Le mod�le '$id_modele' n'a pas une valeur correcte.<br />";
		unset($id_modele);
	}
}

if(isset($id_modele)) {
	$sql="SELECT 1=1 FROM modeles_grilles_pdf WHERE id_modele='$id_modele' AND login='".$_SESSION['login']."';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$msg.="ERREUR&nbsp;: Le mod�le '$id_modele' n'existe pas ou ne vous est pas associ�.<br />\n";
		unset($id_modele);
	}
}

if(isset($nom_modele)) {
	$nom_modele_initial=$nom_modele;
	$nom_modele=preg_replace("/[^A-Za-z0-9_.\-]/","",remplace_accents($nom_modele,'all'));
	if($nom_modele=='') {
		$msg="Le nom de mod�le '$nom_modele_initial' n'est pas correct.<br />";
		unset($nom_modele);
	}
	unset($nom_modele_initial);
}

// Liste des champs de param�trage des grilles
$tab_champs=array('marge_gauche', 'marge_droite', 'marge_haut', 'marge_bas', 'marge_reliure', 'avec_emplacement_trous', 'affiche_pp', 'avec_ligne_texte', 'ligne_texte', 'afficher_effectif', 'une_seule_page', 'h_ligne', 'l_colonne', 'l_nomprenom', 'nb_ligne_avant', 'h_ligne1_avant', 'nb_ligne_apres', 'encadrement_total_cellules', 'nb_cellules_quadrillees', 'zone_vide', 'hauteur_zone_finale');
// Valeurs par d�faut des champs
$tab_val=array('10', '10', '10', '10', '1', '1', '1', '1', ' ', '1', '1', '8', '8', '40', '2', '25', '1', '1', '5', '1', '20');

$temoin_erreur="n";

if((isset($_POST['enregistrer_parametres']))&&(isset($nom_modele))) {
	check_token();

	//echo "1<br />";
	// Enregistrer...
	if(isset($id_modele)) {
		$sql="SELECT 1=1 FROM modeles_grilles_pdf WHERE id_modele='$id_modele' AND login='".$_SESSION['login']."';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			$msg.="Le mod�le '$id_modele' n'existe pas ou ne vous est pas associ�.<br />";
			unset($id_modele);
		}
	}

	if(!isset($id_modele)) {
		$sql="SELECT 1=1 FROM modeles_grilles_pdf WHERE nom_modele='$nom_modele' AND login='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$msg.="ERREUR: Un mod�le du m�me nom existe d�j�.<br />";
		}
	}

	if($temoin_erreur=="n") {
		if(!isset($id_modele)) {
			$sql="SELECT 1=1 FROM modeles_grilles_pdf WHERE par_defaut='y' AND login='".$_SESSION['login']."';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				$par_defaut='n';
			}
			else {
				$par_defaut='y';
			}

			$sql="INSERT INTO modeles_grilles_pdf SET login='".$_SESSION['login']."', nom_modele='$nom_modele', par_defaut='$par_defaut';";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			if(!$res) {
				$msg.="ERREUR lors de la cr�ation du mod�le '$nom_modele'.<br />";
			}
			else {
				$id_modele=mysql_insert_id();
			}
		}
		else {
			$sql="UPDATE modeles_grilles_pdf SET nom_modele='$nom_modele' WHERE id_modele='$id_modele' AND login='".$_SESSION['login']."';";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			if(!$res) {
				$msg.="ERREUR lors de la mise � jour du nom de mod�le.<br />";
			}
		}
	
		if(isset($id_modele)) {
			$cpt_reg=0;
			$cpt_erreur=0;
			for($loop=0;$loop<count($tab_champs);$loop++) {
				if(isset($_POST[$tab_champs[$loop]])) {
					$sql="INSERT INTO modeles_grilles_pdf_valeurs SET id_modele='$id_modele', nom='".$tab_champs[$loop]."', valeur='".$_POST[$tab_champs[$loop]]."';";
					//echo "$sql<br />";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de l'enregistrement de ".$tab_champs[$loop]." -&gt; ".$_POST[$tab_champs[$loop]]." pour le mod�le '$nom_modele'.<br />";
						$cpt_erreur++;
					}
					else {
						$_SESSION[$tab_champs[$loop]]=isset($_POST[$tab_champs[$loop]]) ? $_POST[$tab_champs[$loop]] : $tab_val[$loop];
						$cpt_reg++;
					}
				}
			}
			if(($cpt_reg>0)&&($cpt_erreur==0)) {
				$msg.="Enregistrement effectu� pour le mod�le n�$id_modele.<br />";
			}
		}
	}

	/*
	for($loop=0;$loop<count($tab_champs);$loop++) {
		$nom=$tab_champs[$loop];
		$$nom=isset($_SESSION[$nom]) ? $_SESSION[$nom] : $tab_val[$loop];
	}
	*/
}
elseif(isset($choix_action_modele)) {
	check_token();

	//echo "2<br />";
	if(isset($id_modele)) {
		//echo "3<br />";
		if($choix_action_modele=='modele_par_defaut') {
			$sql="UPDATE modeles_grilles_pdf SET par_defaut='n' WHERE login='".$_SESSION['login']."';";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			if(!$res) {
				$msg.="ERREUR lors du nettoyage du mod�le par d�faut.<br />\n";
			}
			else {
				$sql="UPDATE modeles_grilles_pdf SET par_defaut='y' WHERE id_modele='$id_modele' AND login='".$_SESSION['login']."';";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if(!$res) {
					$msg.="ERREUR lors de la d�finition du mod�le par d�faut � $id_modele.<br />\n";
				}
				else {
					$msg.="Mod�le n�$id_modele d�fini par d�faut.<br />\n";
				}
			}

			$choix_action_modele="done";
			$modif_modele="y";
		}
		elseif($choix_action_modele=='suppr_modele') {
			$sql="DELETE FROM modeles_grilles_pdf_valeurs WHERE id_modele='$id_modele';";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			if(!$res) {
				$msg.="ERREUR lors de la suppression des valeurs associ�es au mod�le n�$id_modele.<br />\n";
			}
			else {
				$sql="DELETE FROM modeles_grilles_pdf WHERE id_modele='$id_modele' AND login='".$_SESSION['login']."';";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if(!$res) {
					$msg.="ERREUR lors de la suppression du mod�le n�$id_modele.<br />\n";
				}
				else {
					$msg.="Mod�le n�$id_modele supprim�.<br />\n";	
					unset($choix_action_modele);
				}
			}
		}
	}
}

//**************** EN-TETE **************************************
//$titre_page = "Impression de listes au format PDF <br />Choix des param�tres".$periode;
$titre_page = "Impression de listes au format PDF <br />Choix des param�tres";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

//debug_var();

echo "<p class='bold'>";
echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo " | <a href='./impression.php'>Impression rapide � l'unit�</a>";
echo " | <a href='./impression_serie.php'>Impression en s�rie</a>";
echo " | <a href='".$_SERVER['PHP_SELF']."'>R�gler les param�tres du PDF</a>";
echo "</p>\n";

if(!isset($enregistrer_parametres)) {
	if(!isset($choix_action_modele)) {
		echo "<fieldset>\n";
		echo "<legend>Mod�les de documents PDF&nbsp;:</legend>\n";
	
		$sql="SELECT * FROM modeles_grilles_pdf WHERE login='".$_SESSION['login']."' ORDER BY nom_modele;";
		//echo "$sql<br />";
		$res_modeles=mysql_query($sql);
		$nb_modeles=mysql_num_rows($res_modeles);
		$cpt=0;
		if($nb_modeles>0) {
			while($lig=mysql_fetch_object($res_modeles)) {
				$tab_modele[$cpt]['id_modele']=$lig->id_modele;
				$tab_modele[$cpt]['nom_modele']=$lig->nom_modele;
				$cpt++;
			}
		}
	
		echo "<form method=\"post\" action=\"parametres_impression_pdf.php\" name=\"form_nouveau_modele\">\n";
		echo add_token_field();
		echo "<p>Cr�er un nouveau mod�le sous le nom&nbsp;: ";
		echo "<input type='text' name='nom_modele' value='Modele_".strftime('%Y%m%d_%H%M%S')."' />\n";
		echo "<input type='hidden' name='choix_action_modele' value='nouveau_modele' />\n";
		echo " <input type='submit' name='nouveau_modele' value='Cr�er' />\n";
		echo "</p>\n";
		echo "</form>\n";
	
		if($nb_modeles>0) {
			echo "<form method=\"post\" action=\"parametres_impression_pdf.php\" name=\"form_modele_par_defaut\">\n";
			echo add_token_field();
			echo "<p>Mod�le&nbsp;: \n";
			echo "<select name='id_modele'>\n";
			for($loop=0;$loop<count($tab_modele);$loop++) {
				echo "<option value='".$tab_modele[$loop]['id_modele']."'>".$tab_modele[$loop]['nom_modele']."</option>\n";
			}
			echo "</select>\n";
			echo " <input type='hidden' name='choix_action_modele' value='modele_par_defaut' />\n";
			echo " <input type='submit' name='modele_par_defaut' value='D�finir comme mod�le par d�faut' />\n";
			echo "</p>\n";
			echo "</form>\n";
		
			echo "<form method=\"post\" action=\"parametres_impression_pdf.php\" name=\"form_modifier_modele\">\n";
			echo add_token_field();
			echo "<p>Modifier le mod�le&nbsp;: \n";
			echo "<select name='id_modele'>\n";
			for($loop=0;$loop<count($tab_modele);$loop++) {
				echo "<option value='".$tab_modele[$loop]['id_modele']."'>".$tab_modele[$loop]['nom_modele']."</option>\n";
			}
			echo "</select>\n";
			echo " <input type='hidden' name='choix_action_modele' value='modif_modele' />\n";
			echo " <input type='submit' name='modif_modele' value='Modifier' />\n";
			echo "</p>\n";
			echo "</form>\n";
		
			echo "<form method=\"post\" action=\"parametres_impression_pdf.php\" name=\"form_supprimer_modele\">\n";
			echo add_token_field();
			echo "<p>Supprimer le mod�le&nbsp;: \n";
			echo "<select name='id_modele'>\n";
			for($loop=0;$loop<count($tab_modele);$loop++) {
				echo "<option value='".$tab_modele[$loop]['id_modele']."'>".$tab_modele[$loop]['nom_modele']."</option>\n";
			}
			echo "</select>\n";
			echo " <input type='hidden' name='choix_action_modele' value='suppr_modele' />\n";
			echo " <input type='submit' name='suppr_modele' value='Supprimer' />\n";
			echo "</p>\n";
			echo "</form>\n";
		}
		echo "</fieldset>\n";
	
		require("../lib/footer.inc.php");
		die();
	}
}

if(isset($nouveau_modele)) {
	// R�cup�ration des valeurs courantes... ou utilisation des valeurs par d�faut
	for($loop=0;$loop<count($tab_champs);$loop++) {
		$nom=$tab_champs[$loop];
		$$nom=isset($_SESSION[$nom]) ? $_SESSION[$nom] : $tab_val[$loop];
	}
}
elseif(isset($modif_modele)) {
	//$sql="SELECT * FROM modeles_grilles_pdf_valeurs WHERE id_modele='$id_modele' AND login='".$_SESSION['login']."';";	echo "$sql<br />";
	$sql="SELECT * FROM modeles_grilles_pdf_valeurs WHERE id_modele='$id_modele';";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		// Initialisation au valeurs par d�faut
		for($loop=0;$loop<count($tab_champs);$loop++) {
			$nom=$tab_champs[$loop];
			$$nom=isset($_SESSION[$nom]) ? $_SESSION[$nom] : $tab_val[$loop];
		}

		// Remplacement par les valeurs enregistr�es
		while($lig=mysql_fetch_object($res)) {
			$nom=$lig->nom;
			$$nom=$lig->valeur;
		}

		$sql="SELECT * FROM modeles_grilles_pdf WHERE id_modele='$id_modele';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			$nom_modele=$lig->nom_modele;
		}
		else {
			echo "<p style='color:red;'>ERREUR&nbsp;: Le mod�le '$id_modele' n'existe pas dans la table 'modeles_grilles_pdf'.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
	else {
		echo "<p style='color:red;'>ERREUR&nbsp;: Le mod�le '$id_modele' n'existe pas ou ne vous est pas associ�.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
}
else {
	echo "<p style='color:red;'>Choix invalide</p>\n";

	require("../lib/footer.inc.php");
	die();
}

echo "<h3>Choix des param�tres&nbsp;: </h3>\n";

echo "<div>\n
   <fieldset>\n";
		echo "<legend>Modifiez l'apparence du document PDF&nbsp;:</legend>\n";

		//echo "<div style='float:right; '></div>";

		echo "<form method=\"post\" action=\"parametres_impression_pdf.php\" name=\"choix_parametres\">\n";
		echo add_token_field();
		echo "<input value=\"Valider les param�tres\" name=\"Valider\" type=\"submit\" /><br />\n";
		echo "<br />\n";

		if(isset($id_modele)) {
			echo "<input type='hidden' name=\"id_modele\" value=\"$id_modele\" /><br />\n";
		}
		echo "<b>Nom du mod�le</b>&nbsp;: <input type='text' name=\"nom_modele\" value=\"$nom_modele\" /><br /><br />\n";

		echo "<b>D�finition des marges du document&nbsp;:</b></p>\n";
		echo "<table style='margin-left: 1em;' border='0'>\n";
		echo "<tr><td>Marge � gauche&nbsp;:</td><td><input type=\"text\" name=\"marge_gauche\" size=\"2\" maxlength=\"2\" value=\"$marge_gauche\" /></td></tr>\n";
		echo "<tr><td>Marge � droite&nbsp;:</td><td><input type=\"text\" name=\"marge_droite\" size=\"2\" maxlength=\"2\" value=\"$marge_droite\" /></td></tr>\n";
		echo "<tr><td>Marge du haut&nbsp;:</td><td><input type=\"text\" name=\"marge_haut\" size=\"2\" maxlength=\"2\" value=\"$marge_haut\" /></td></tr>\n";
		echo "<tr><td>Marge du bas&nbsp;:</td><td><input type=\"text\" name=\"marge_bas\" size=\"2\" maxlength=\"2\" value=\"$marge_bas\" /></td></tr>\n";

		echo "<tr><td>Option marge reliure ?</td><td><input type=\"radio\" name=\"marge_reliure\" id=\"marge_reliure_1\" value=\"1\" ";
		if($marge_reliure==1) {echo "checked ";}
		echo "/><label for='marge_reliure_1'> Oui</label> <input type=\"radio\" name=\"marge_reliure\" id=\"marge_reliure_0\" value=\"0\" ";
		if($marge_reliure!=1) {echo "checked ";}
		echo "/><label for='marge_reliure_0'> Non</label></td></tr>\n";
		echo "<tr><td>Option emplacement des<br />perforations classeur  ?</td><td><input type=\"radio\" name=\"avec_emplacement_trous\" id=\"avec_emplacement_trous_1\" value=\"1\" ";
		if($avec_emplacement_trous==1) {echo "checked ";}
		echo "/><label for='avec_emplacement_trous_1'> Oui</label> <input type=\"radio\" name=\"avec_emplacement_trous\" id=\"avec_emplacement_trous_0\" value=\"0\" ";
		if($avec_emplacement_trous!=1) {echo "checked ";}
		echo "/><label for='avec_emplacement_trous_0'> Non</label></td></tr>\n";
		echo "</table>\n";

		echo "<br />\n";

		echo "<b>Informations � afficher sur le document&nbsp;:</b><br />\n";
		echo "<table style='margin-left: 1em;' border='0'>\n";
		echo "<tr><td>Afficher le professeur responsable de la classe ?</td><td><input type=\"radio\" name=\"affiche_pp\" id=\"affiche_pp_1\" value=\"1\" ";
		if($affiche_pp==1) {echo "checked ";}
		echo "/><label for='affiche_pp_1'> Oui</label> <input type=\"radio\" name=\"affiche_pp\" id=\"affiche_pp_0\" value=\"0\" ";
		if($affiche_pp!=1) {echo "checked ";}
		echo "/><label for='affiche_pp_0'> Non</label></td></tr>\n";
		echo "<tr><td valign='top'>Afficher une ligne de texte avant le tableau  ?</td><td><input type=\"radio\" name=\"avec_ligne_texte\" id=\"avec_ligne_texte_1\" value=\"1\" ";
		if($avec_ligne_texte==1) {echo "checked ";}
		echo "/><label for='avec_ligne_texte_1'> Oui</label> <input type=\"radio\" name=\"avec_ligne_texte\" id=\"avec_ligne_texte_0\" value=\"0\" ";
		if($avec_ligne_texte!=1) {echo "checked ";}
		echo "/><label for='avec_ligne_texte_0'> Non</label><br />";
		echo "Texte&nbsp;: &nbsp;<input type=\"text\" name=\"ligne_texte\" size=\"50\" value=\"$ligne_texte\" /></td></tr>\n";
		echo "<tr><td>Afficher l'effectif de la classe ?</td><td><input type=\"radio\" name=\"afficher_effectif\" id=\"afficher_effectif_1\" value=\"1\" ";
		if($afficher_effectif==1) {echo "checked ";}
		echo "/><label for='afficher_effectif_1'> Oui</label> <input type=\"radio\" name=\"afficher_effectif\" id=\"afficher_effectif_0\" value=\"0\" ";
		if($afficher_effectif!=1) {echo "checked ";}
		echo "/><label for='afficher_effectif_0'> Non</label></td></tr>\n";
		echo "</table>\n";

		echo "<br />\n";
		echo "<b>Styles du tableau&nbsp;: </b><br />\n";
		echo "<table style='margin-left: 1em;' border='0'>\n";
		echo "<tr><td>Tout sur une seule page ?</td><td><input type=\"radio\" name=\"une_seule_page\" id=\"une_seule_page_1\" value=\"1\" ";
		if($une_seule_page==1) {echo "checked ";}
		echo "/><label for='une_seule_page_1'> Oui</label> <input type=\"radio\" name=\"une_seule_page\" id=\"une_seule_page_0\" value=\"0\" ";
		if($une_seule_page!=1) {echo "checked ";}
		echo "/><label for='une_seule_page_0'> Non</label></td></tr>\n";
		echo "<tr><td>Hauteur d'une ligne&nbsp;:</td><td><input type=\"text\" name=\"h_ligne\" size=\"2\" maxlength=\"2\" value=\"$h_ligne\" /> </td></tr>\n";
		echo "<tr><td>Largeur d'une colonne&nbsp;:</td><td><input type=\"text\" name=\"l_colonne\" size=\"2\" maxlength=\"2\" value=\"$l_colonne\" /> </td></tr>\n";
		echo "<tr><td>Largeur colonne Nom / Pr�nom&nbsp;:</td><td><input type=\"text\" name=\"l_nomprenom\" size=\"2\" maxlength=\"2\" value=\"$l_nomprenom\" /> </td></tr>\n";
		echo "<tr><td>Nombre ligne(s) avant&nbsp;:</td><td><input type=\"text\" name=\"nb_ligne_avant\" size=\"2\" maxlength=\"2\" value=\"$nb_ligne_avant\" /> \n";
		echo "<tr><td>Hauteur de la premi�re ligne avant&nbsp;:</td><td><input type=\"text\" name=\"h_ligne1_avant\" size=\"2\" maxlength=\"$h_ligne1_avant\" value=\"25\" /> </td></tr>\n";
		echo "<tr><td>Nombre ligne(s) apr�s&nbsp;:</td><td><input type=\"text\" name=\"nb_ligne_apres\" size=\"2\" maxlength=\"2\" value=\"$nb_ligne_apres\" /> </td></tr>\n";
		echo "<tr><td>Quadrillage total des cellules ?</td><td><input type=\"radio\" name=\"encadrement_total_cellules\" id=\"encadrement_total_cellules_1\" value=\"1\" ";
		if($encadrement_total_cellules==1) {echo "checked ";}
		echo "/><label for='encadrement_total_cellules_1'> Oui</label> [ <input type=\"radio\" name=\"encadrement_total_cellules\" id=\"encadrement_total_cellules_0\" value=\"0\" ";
		if($encadrement_total_cellules!=1) {echo "checked ";}
		echo "/><label for='encadrement_total_cellules_0'> Non</label> \n";
		echo "<tr><td>&nbsp;</td><td>Nombre de cellules quadrill�es apr�s le nom&nbsp;: <input type=\"text\" name=\"nb_cellules_quadrillees\" size=\"2\" maxlength=\"2\" value=\"$nb_cellules_quadrillees\" /> ] </td></tr>\n";
		echo "</table>\n";
		echo "<br />\n";

		echo "<b>Informations en bas du document&nbsp;: </b><br />\n";
		echo "&nbsp;&nbsp;R�server une zone vide sous le tableau ? <input type=\"radio\" name=\"zone_vide\" id=\"zone_vide_1\" value=\"1\" ";
		if($zone_vide==1) {echo "checked ";}
		echo "/><label for='zone_vide_1'> Oui</label> <input type=\"radio\" name=\"zone_vide\" id=\"zone_vide_0\" value=\"0\" ";
		if($zone_vide!=1) {echo "checked ";}
		echo "/><label for='zone_vide_0'> Non</label><br />\n";
		echo "&nbsp;&nbsp;Hauteur de la zone&nbsp;: <input type=\"text\" name=\"hauteur_zone_finale\" size=\"2\" maxlength=\"2\" value=\"$hauteur_zone_finale\" /> (0 tout ce qui reste)<br />\n";

		//echo "<input value=\"1\" name=\"ok\" type=\"hidden\" />\n";
		echo "<input type=\"hidden\" name=\"enregistrer_parametres\" value=\"1\" />\n";
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

	die();
/*
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
*/
?>
