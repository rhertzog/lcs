<?php
/*
 * $Id: security_policy.php 6526 2011-02-22 18:44:39Z crob $
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Enregistrement des donn�es post�es

if (isset($_POST) and !empty($_POST)) {
	check_token();

	// Envoyer un email � l'administrateur syst�matiquement
	if (isset($_POST['security_alert_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
	if (!saveSetting(("security_alert_email_admin"), $reg)) {
		$msg = "Erreur lors de l'enregistrement de security_alert_email_admin !";
	}

	// Niveau minimal pour l'envoi du mail
	if (isset($_POST['security_alert_email_min_level'])) {
		$reg = $_POST['security_alert_email_min_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert_email_min_level"), $reg)) {
			$msg = "Erreur lors de l'enregistrement de security_alert_email_min_level !";
		}
	}

	// Niveau d'alerte 1
	
	// Utilisateur sans ant�c�dent
	
	// Seuil
	if (isset($_POST['security_alert1_normal_cumulated_level'])) {
		$reg = $_POST['security_alert1_normal_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert1_normal_cumulated_level"), $reg)) {
			$msg = "Erreur lors de l'enregistrement de security_alert1_normal_cumulated_level !";
		}
	}

	// Envoyer un email � l'administrateur
	if (isset($_POST['security_alert1_normal_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_normal_email_admin"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert1_normal_email_admin !";
    }

	// D�sactiver le compte de l'utilisateur
	if (isset($_POST['security_alert1_normal_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_normal_block_user"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert1_normal_block_user !";
    }

	// Utilisateur surveill�
	
	// Seuil
	if (isset($_POST['security_alert1_probation_cumulated_level'])) {
		$reg = $_POST['security_alert1_probation_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert1_probation_cumulated_level"), $reg)) {
			$msg = "Erreur lors de l'enregistrement de security_alert1_probation_cumulated_level !";
		}
	}

	// Envoyer un email � l'administrateur
	if (isset($_POST['security_alert1_probation_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_probation_email_admin"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert1_probation_email_admin !";
    }

	// D�sactiver le compte de l'utilisateur
	if (isset($_POST['security_alert1_probation_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_probation_block_user"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert1_probation_block_user !";
    }

	// Niveau d'alerte 2
	
	// Utilisateur sans ant�c�dent
	
	// Seuil
	if (isset($_POST['security_alert2_normal_cumulated_level'])) {
		$reg = $_POST['security_alert2_normal_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert2_normal_cumulated_level"), $reg)) {
			$msg = "Erreur lors de l'enregistrement de security_alert2_normal_cumulated_level !";
		}
	}
	
	// Envoyer un email � l'administrateur
	if (isset($_POST['security_alert2_normal_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_normal_email_admin"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert2_normal_email_admin !";
    }

	// D�sactiver le compte de l'utilisateur
	if (isset($_POST['security_alert2_normal_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_normal_block_user"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert2_normal_block_user !";
    }

	// Utilisateur surveill�
	
	// Seuil
	if (isset($_POST['security_alert2_probation_cumulated_level'])) {
		$reg = $_POST['security_alert2_probation_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert2_probation_cumulated_level"), $reg)) {
			$msg = "Erreur lors de l'enregistrement de security_alert2_probation_cumulated_level !";
		}
	}
	
	// Envoyer un email � l'administrateur
	if (isset($_POST['security_alert2_probation_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_probation_email_admin"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert2_probation_email_admin !";
    }

	// D�sactiver le compte de l'utilisateur
	if (isset($_POST['security_alert2_probation_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_probation_block_user"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert2_probation_block_user !";
    }

	if (empty($msg)) {
		$msg = "Les donn�es ont bien �t� enregistr�es.";
	}

}

//echo "\$filtrage_html=$filtrage_html<br />";
if (isset($_POST['filtrage_html'])) {
	check_token();
	if(($_POST['filtrage_html']=='inputfilter')||
		($_POST['filtrage_html']=='htmlpurifier')||
		($_POST['filtrage_html']=='pas_de_filtrage_html')) {

		if (!saveSetting(("filtrage_html"), $_POST['filtrage_html'])) {
			$msg = "Erreur lors de l'enregistrement de filtrage_html !";
		}
	}

	if (isset($_POST['utiliser_no_php_in_img'])) {
		if (!saveSetting(("utiliser_no_php_in_img"), 'y')) {
			$msg = "Erreur lors de l'enregistrement de utiliser_no_php_in_img !";
		}
	}
	else {
		if (!saveSetting(("utiliser_no_php_in_img"), 'n')) {
			$msg = "Erreur lors de l'enregistrement de utiliser_no_php_in_img !";
		}
	}

	$utiliser_no_php_in_img=getSettingValue('utiliser_no_php_in_img');
}


// Fin : if isset($_POST)

$htmlpurifier_autorise='y';
$tab_version_php=explode(".",phpversion());
if($tab_version_php[0]==4) {
	$htmlpurifier_autorise='n';
}
elseif(($tab_version_php[0]==5)&&($tab_version_php[1]==0)&&($tab_version_php[2]<5)) {
	$htmlpurifier_autorise='n';
}

$filtrage_html=getSettingValue('filtrage_html');
if(($filtrage_html=='htmlpurifier')&&($htmlpurifier_autorise=='n')) {
	saveSetting(("filtrage_html"), 'inputfilter');
	$filtrage_html='inputfilter';
}

//echo "\$filtrage_html=$filtrage_html<br />";
if(($filtrage_html!='inputfilter')&&
	($filtrage_html!='htmlpurifier')&&
	($filtrage_html!='pas_de_filtrage_html')) {
	saveSetting(("filtrage_html"), 'htmlpurifier');

	$filtrage_html=getSettingValue('filtrage_html');
}
//echo "\$filtrage_html=$filtrage_html<br />";

//**************** EN-TETE *********************
$titre_page = "Politique de s�curit�";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

//on r�cup�re le chemin de la page d'appel pour en faire le lien de retour
if(isset($_SERVER['HTTP_REFERER'])) {
	$url_retour = parse_url($_SERVER['HTTP_REFERER']);

	if($_SERVER['PHP_SELF']==$url_retour['path']) {
		$url_retour['path']='index.php#security_policy';
	}
}
else {
	$url_retour['path']='index.php#security_policy';
}
/*
foreach($url_retour as $key => $value) {
	echo "\$url_retour['$key']=$value<br />";
}
debug_var();
//$_SERVER['PHP_SELF']
*/

echo "<p class='bold'><a href='".$url_retour['path']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

echo "<form action='security_policy.php' method='post'>\n";
echo add_token_field();
echo "<center><input type='submit' value='Enregistrer' /></center>\n";

// Gestion des tentatives d'intrusion
echo "<h2>Gestion des tentatives d'intrusion</h2>\n";
echo "<div style='margin-left:3em;'>\n";
	
	// Options g�n�rales
	echo "<h3>Options g�n�rales</h3>\n";
	echo "<div style='margin-left:3em;'>\n";
		echo "<input type='checkbox' name='security_alert_email_admin' value='yes'";
		if (getSettingValue("security_alert_email_admin") == "yes") echo " CHECKED";
		echo " />\n";
		echo " Envoyer syst�matiquement un email � l'administrateur lors d'une tentative d'intrusion.<br/>\n";
		echo "Niveau de gravit� minimal pour l'envoi du mail : ";
		echo "<select name='security_alert_email_min_level' size='1'>\n";
		for ($i = 1; $i <= 3;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert_email_min_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
	echo "</div>\n";

	// Seuils d'alerte et actions � entreprendre
	echo "<h3>Seuils d'alertes</h3>\n";
	echo "<div style='margin-left:3em;'>\n";
		echo "<p>Vous pouvez d�finir deux seuils d'alerte et leurs actions associ�es. Ces seuils s'appliquent aux tentatives d'intrusion effectu�es par les utilisateurs de Gepi. Chaque tentative a un niveau de gravit� de 1 � 3 ; les seuils correspondent au cumul de ces niveaux de gravit� pour un m�me utilisateur.</p>\n";
		echo "<p>Un utilisateur peut �tre plac� en observation par l'administrateur, avec des seuils d'alerte distincts. Cela permet de d�finir une politique plus restrictive en cas de r�cidive.</p>\n";
		
		echo "<table class='normal' summary=\"Seuils d'alerte\">\n";
		echo "<tr>\n";
		echo "<th>Seuil</th>\n";
		echo "<th>Utilisateur sans ant�c�dent</th>\n";
		echo "<th>Utilisateur surveill�</th>\n";
		echo "</tr>\n";
		
		// Niveau d'alerte 1
		echo "<tr>\n";
		echo "<td>\n";
		echo "<p>Seuil 1</p>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur sans ant�c�dent
		echo "Niveau cumul� : ";
		echo "<select name='security_alert1_normal_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert1_normal_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert1_normal_email_admin' value='yes'";
		if (getSettingValue("security_alert1_normal_email_admin") == "yes") echo " CHECKED";
		echo " /> Envoyer un email � l'administrateur<br/>\n";
		echo "<input type='checkbox' name='security_alert1_normal_block_user' value='yes'";
		if (getSettingValue("security_alert1_normal_block_user") == "yes") echo " CHECKED";
		echo " /> D�sactiver le compte de l'utilisateur<br/>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur en observation
		echo "Niveau cumul� : ";
		echo "<select name='security_alert1_probation_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert1_probation_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert1_probation_email_admin' value='yes'";
		if (getSettingValue("security_alert1_probation_email_admin") == "yes") echo " CHECKED";
		echo " /> Envoyer un email � l'administrateur<br/>\n";
		echo "<input type='checkbox' name='security_alert1_probation_block_user' value='yes'";
		if (getSettingValue("security_alert1_probation_block_user") == "yes") echo " CHECKED";
		echo " /> D�sactiver le compte de l'utilisateur<br/>\n";
		echo "</td>\n";
		echo "</tr>\n";
		
		// Niveau d'alerte 2
		echo "<tr>\n";
		echo "<td>\n";
		echo "<p>Seuil 2</p>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur sans ant�c�dent
		echo "Niveau cumul� : ";
		echo "<select name='security_alert2_normal_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert2_normal_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert2_normal_email_admin' value='yes'";
		if (getSettingValue("security_alert2_normal_email_admin") == "yes") echo " CHECKED";
		echo " /> Envoyer un email � l'administrateur<br/>\n";
		echo "<input type='checkbox' name='security_alert2_normal_block_user' value='yes'";
		if (getSettingValue("security_alert2_normal_block_user") == "yes") echo " CHECKED";
		echo " /> D�sactiver le compte de l'utilisateur<br/>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur en observation
		echo "Niveau cumul� : ";
		echo "<select name='security_alert2_probation_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert2_probation_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert2_probation_email_admin' value='yes'";
		if (getSettingValue("security_alert2_probation_email_admin") == "yes") echo " CHECKED";
		echo " /> Envoyer un email � l'administrateur<br/>\n";
		echo "<input type='checkbox' name='security_alert2_probation_block_user' value='yes'";
		if (getSettingValue("security_alert2_probation_block_user") == "yes") echo " CHECKED";
		echo " /> D�sactiver le compte de l'utilisateur<br/>\n";
		echo "</td>\n";
		echo "</tr>\n";
		
		echo "</table>\n";
		echo "<br/><br/>\n";
	echo "</div>\n";
echo "</div>\n";

// Filtrage HTML
echo "<h2>Filtrage HTML</h2>\n";
echo "<div style='margin-left:3em;'>\n";

	echo "<p>Pour pr�venir des tentatives d'injection de code HTML malicieux dans les formulaires, GEPI propose deux dispositifs&nbsp;:</p>\n";
	echo "<table summary='Mode de filtrage'>\n";
	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='filtrage_html' id='filtrage_html_inputfilter' value='inputfilter' ";
	if($filtrage_html=='inputfilter') {echo "checked ";}
	echo "/>\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='filtrage_html_inputfilter'> InputFilter (<i>php4/php5</i>)</label><br />\n";
	echo "<span style='font-size:small'>\n";
	echo "Ce dispositif n'autorise que les balises et attributs suivants&nbsp;:<br />";
	echo "<b>Balises&nbsp;:</b> ";
	for($i=0;$i<count($aAllowedTags);$i++) {
		if($i>0) {echo ", ";}
		echo $aAllowedTags[$i];
	}
	echo "<br />\n";
	echo "<b>Attributs&nbsp;:</b> ";
	for($i=0;$i<count($aAllowedAttr);$i++) {
		if($i>0) {echo ", ";}
		echo $aAllowedAttr[$i];
	}
	echo "</span>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";

	if($htmlpurifier_autorise=='n') {
		echo "<img src='../images/disabled.png' width='20' height='20' alt='Mode non accessible' />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo " HTMLpurifier (<i color='red'>php>=5.0.5</i>)<br />\n";
	}
	else {
		echo "<input type='radio' name='filtrage_html' id='filtrage_html_htmlpurifier' value='htmlpurifier' ";
		if($filtrage_html=='htmlpurifier') {echo "checked ";}
		echo "/>\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='filtrage_html_htmlpurifier'> HTMLpurifier (<i>php>=5.0.5</i>)</label><br />\n";
	}
	echo "<span style='font-size:small'>\n";
	echo "Plus complet qu'InputFilter dans les filtrages r�alis�s.<br />\n";
	echo "Il tente �galement de rendre le code HTML plus correct/valide au sens W3C.<br />\n";
	echo "<i>A noter&nbsp;:</i> HTMLpurifier ne fonctionne pas bien lorsque les magic_quotes_gpc sont activ�es (<i>cf. <a href='http://htmlpurifier.org/docs#toclink4'>http://htmlpurifier.org/docs#toclink4</a></i>).<br />\nCe dispositif doit dispara�tre � terme avec PHP6, mais s'il est activ�, on bascule de HTMLpurifier � InputFilter pour �viter le probl�me.";
	echo "</span>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='filtrage_html' id='pas_de_filtrage_html' value='pas_de_filtrage_html' ";
	if($filtrage_html=='pas_de_filtrage_html') {echo "checked ";}
	echo "/>\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='pas_de_filtrage_html'> Pas de filtrage HTML</label><br />\n";
	echo "<span style='font-size:small'>\n";
	echo "Si vous optez pour ce choix, il est possible � des utilisateurs malintentionn�s de d�poser dans les formulaires du code dangereux.<br />\n";
	echo "</span>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";

	echo "<p style='font-weight:bold; color:red;'>Il est tr�s fortement d�conseill� de d�sactiver le filtrage.</p>\n";

	echo "<p><input type='checkbox' id='utiliser_no_php_in_img' name='utiliser_no_php_in_img' value='y' ";
	if($utiliser_no_php_in_img=='y') {echo "checked ";}
	echo "/><label for='utiliser_no_php_in_img'> Interdire d'ins�rer dans des appr�ciations, des notices de cahiers de textes des images g�n�r�es par PHP</label>.</p>\n";
echo "</div>\n";

echo "<center><input type='submit' value='Enregistrer' /></center>\n";
echo "</form>\n";
echo "<br/><br/><br/>\n";
require("../lib/footer.inc.php");
?>