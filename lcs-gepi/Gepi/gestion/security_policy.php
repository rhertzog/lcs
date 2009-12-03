<?php
/*
 * $Id: security_policy.php 2147 2008-07-23 09:01:04Z tbelliard $
 *
 * Copyright 2001-2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Enregistrement des donn�es post�es

if (isset($_POST) and !empty($_POST)) {

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

} // Fin : if isset($_POST)
//**************** EN-TETE *********************
$titre_page = "Politique de s�curit�";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//on r�cup�re le chemin de la page d'appel pour en faire le lien de retour
if(isset($_SERVER['HTTP_REFERER'])) {
	$url_retour = parse_url($_SERVER['HTTP_REFERER']);

	if($_SERVER['PHP_SELF']==$url_retour['path']) {
		$url_retour['path']='index.php';
	}
}
else {
	$url_retour['path']='index.php';
}
/*
foreach($url_retour as $key => $value) {
	echo "\$url_retour['$key']=$value<br />";
}
debug_var();
//$_SERVER['PHP_SELF']
*/

echo "<p class='bold'><a href='".$url_retour['path']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

echo "<form action='security_policy.php' method='post'>";
echo "<center><input type='submit' value='Enregistrer' /></center>";

// Options g�n�rales
echo "<h2>Options g�n�rales</h2>";
echo "<input type='checkbox' name='security_alert_email_admin' value='yes'";
if (getSettingValue("security_alert_email_admin") == "yes") echo " CHECKED";
echo " />";
echo " Envoyer syst�matiquement un email � l'administrateur lors d'une tentative d'intrusion.<br/>\n";
echo "Niveau de gravit� minimal pour l'envoi du mail : ";
echo "<select name='security_alert_email_min_level' size='1'>";
for ($i = 1; $i <= 3;$i++) {
	echo "<option value='$i'";
	if (getSettingValue("security_alert_email_min_level") == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
// Seuils d'alerte et actions � entreprendre
echo "<h2>Seuils d'alertes</h2>";
echo "<p>Vous pouvez d�finir deux seuils d'alerte et leurs actions associ�es. Ces seuils s'appliquent aux tentatives d'intrusion effectu�es par les utilisateurs de Gepi. Chaque tentative a un niveau de gravit� de 1 � 3 ; les seuils correspondent au cumul de ces niveaux de gravit� pour un m�me utilisateur.</p>";
echo "<p>Un utilisateur peut �tre plac� en observation par l'administrateur, avec des seuils d'alerte distincts. Cela permet de d�finir une politique plus restrictive en cas de r�cidive.</p>";

echo "<table class='normal' summary=\"Seuils d'alerte\">";
echo "<tr><th>Seuil</th><th>Utilisateur sans ant�c�dent</th><th>Utilisateur surveill�</th></tr>";

// Niveau d'alerte 1
echo "<tr><td>";
echo "<p>Seuil 1</p>";
echo "</td><td>";

// Utilisateur sans ant�c�dent
echo "Niveau cumul� : ";
echo "<select name='security_alert1_normal_cumulated_level' size='1'>";
for ($i = 1; $i <= 15;$i++) {
	echo "<option value='$i'";
	if (getSettingValue("security_alert1_normal_cumulated_level") == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
echo "<br/>Actions :<br/>";
echo "<input type='checkbox' name='security_alert1_normal_email_admin' value='yes'";
if (getSettingValue("security_alert1_normal_email_admin") == "yes") echo " CHECKED";
echo " /> Envoyer un email � l'administrateur<br/>";
echo "<input type='checkbox' name='security_alert1_normal_block_user' value='yes'";
if (getSettingValue("security_alert1_normal_block_user") == "yes") echo " CHECKED";
echo " /> D�sactiver le compte de l'utilisateur<br/>";
echo "</td><td>";

// Utilisateur en observation
echo "Niveau cumul� : ";
echo "<select name='security_alert1_probation_cumulated_level' size='1'>";
for ($i = 1; $i <= 15;$i++) {
	echo "<option value='$i'";
	if (getSettingValue("security_alert1_probation_cumulated_level") == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
echo "<br/>Actions :<br/>";
echo "<input type='checkbox' name='security_alert1_probation_email_admin' value='yes'";
if (getSettingValue("security_alert1_probation_email_admin") == "yes") echo " CHECKED";
echo " /> Envoyer un email � l'administrateur<br/>";
echo "<input type='checkbox' name='security_alert1_probation_block_user' value='yes'";
if (getSettingValue("security_alert1_probation_block_user") == "yes") echo " CHECKED";
echo " /> D�sactiver le compte de l'utilisateur<br/>";
echo "</td></tr>";

// Niveau d'alerte 2
echo "<tr><td>";
echo "<p>Seuil 2</p>";
echo "</td><td>";

// Utilisateur sans ant�c�dent
echo "Niveau cumul� : ";
echo "<select name='security_alert2_normal_cumulated_level' size='1'>";
for ($i = 1; $i <= 15;$i++) {
	echo "<option value='$i'";
	if (getSettingValue("security_alert2_normal_cumulated_level") == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
echo "<br/>Actions :<br/>";
echo "<input type='checkbox' name='security_alert2_normal_email_admin' value='yes'";
if (getSettingValue("security_alert2_normal_email_admin") == "yes") echo " CHECKED";
echo " /> Envoyer un email � l'administrateur<br/>";
echo "<input type='checkbox' name='security_alert2_normal_block_user' value='yes'";
if (getSettingValue("security_alert2_normal_block_user") == "yes") echo " CHECKED";
echo " /> D�sactiver le compte de l'utilisateur<br/>";
echo "</td><td>";

// Utilisateur en observation
echo "Niveau cumul� : ";
echo "<select name='security_alert2_probation_cumulated_level' size='1'>";
for ($i = 1; $i <= 15;$i++) {
	echo "<option value='$i'";
	if (getSettingValue("security_alert2_probation_cumulated_level") == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
echo "<br/>Actions :<br/>";
echo "<input type='checkbox' name='security_alert2_probation_email_admin' value='yes'";
if (getSettingValue("security_alert2_probation_email_admin") == "yes") echo " CHECKED";
echo " /> Envoyer un email � l'administrateur<br/>";
echo "<input type='checkbox' name='security_alert2_probation_block_user' value='yes'";
if (getSettingValue("security_alert2_probation_block_user") == "yes") echo " CHECKED";
echo " /> D�sactiver le compte de l'utilisateur<br/>";
echo "</td></tr>";

echo "</table>";
echo "<br/><br/>";
echo "<center><input type='submit' value='Enregistrer' /></center>";
echo "</form>";
echo "<br/><br/><br/>";
require("../lib/footer.inc.php");
?>