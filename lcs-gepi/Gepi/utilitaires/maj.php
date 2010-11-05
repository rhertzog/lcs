<?php
/*
 * $Id: maj.php 5705 2010-10-21 14:54:48Z jjacquard $
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

// On indique qu'il faut cr�e des variables non prot�g�es (voir fonction cree_variables_non_protegees())
// cela ici concerne le mot de passe
$variables_non_protegees = 'yes';
$pb_maj = '';

// Initialisations files
require_once ("../lib/initialisations.inc.php");
require_once ("./update_functions.php");


// Resume session
$resultat_session = $session_gepi->security_check();

if (isset ($_POST['submit'])) {
	if (isset ($_POST['login']) && isset ($_POST['no_anti_inject_password'])) {
		$_POST['login'] = strtoupper($_POST['login']);
		$md5password = md5($NON_PROTECT['password']);
		$sql = "SELECT UPPER(login) login, password, prenom, nom, statut FROM utilisateurs WHERE (login = '" . $_POST['login'] . "' and password = '" . $md5password . "' and etat != 'inactif' and statut = 'administrateur')";

		$res_user = sql_query($sql);
		$num_row = sql_count($res_user);

		if ($num_row == 1) {
			$valid = 'yes';
			$resultat_session = "1";
			$_SESSION['login'] = $_POST['login'];
			$_SESSION['statut'] = 'administrateur';
			$_SESSION['etat'] = 'actif';
			$_SESSION['start'] = mysql_result(mysql_query("SELECT now();"),0);
			$sql = "INSERT INTO log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
					'" . $_SESSION['login'] . "',
					'".$_SESSION['start']."',
					'" . session_id() . "',
					'" . $_SERVER['REMOTE_ADDR'] . "',
					'" . $_SERVER['HTTP_USER_AGENT'] . "',
					'" . $_SERVER['HTTP_REFERER'] . "',
					'1',
					'".$_SESSION['start']."' + interval " . getSettingValue('sessionMaxLength') . " minute
				)
			;";
			$res = sql_query($sql);

		} else {
			$message = "Identifiant ou mot de passe incorrect, ou bien vous n'�tes pas administrateur.";
		}
	}
}



$valid = isset ($_POST["valid"]) ? $_POST["valid"] : 'no';
$force_maj = isset ($_POST["force_maj"]) ? $_POST["force_maj"] : '';

// Num�ro de version effective
$version_old = getSettingValue("version");
// Num�ro de version RC effective
$versionRc_old = getSettingValue("versionRc");
// Num�ro de version Beta effective
$versionBeta_old = getSettingValue("versionBeta");

$rc_old = '';
if ($versionRc_old != '') {
	$rc_old = "-RC" . $versionRc_old;
}
$rc = '';
if ($gepiRcVersion != '') {
	$rc = "-RC" . $gepiRcVersion;
}

$beta_old = '';
if ($versionBeta_old != '') {
	$beta_old = "-beta" . $versionBeta_old;
}
$beta = '';
if ($gepiBetaVersion != '') {
	$beta = "-beta" . $gepiBetaVersion;
}


echo ('
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
	<HEAD>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
		<link rel="stylesheet" href="../style.css" type="text/css" />
		<title>Mise � jour de la base de donn�e GEPI</title>
		<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
		<link rel="icon" type="image/ico" href="../favicon.ico" />
		');

if(isset($style_screen_ajout)){
	// Styles param�trables depuis l'interface:
	if($style_screen_ajout=='y'){
		// La variable $style_screen_ajout se param�tre dans le /lib/global.inc
		// C'est une s�curit�... il suffit de passer la variable � 'n' pour d�sactiver ce fichier CSS et �ventuellement r�tablir un acc�s apr�s avoir impos� une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
	}
}

echo ('
	</head>
	<body>
');


if (($resultat_session == '0') and ($valid != 'yes')) {
	echo('
		<form action="maj.php" method="POST" style="width: 100%; margin-top: 24px; margin-bottom: 48px;">
			<div class="center">
				<H2 align="center"><?php echo "Mise � jour de la base de donn�e GEPI<br />(Acc�s administrateur)"; ?></H2>
			');

	if (isset ($message)) {
		echo ("<p align=\"center\"><font color=red>" . $message . "</font></p>");
	}
	echo('
				<fieldset style="padding-top: 8px; padding-bottom: 8px; width: 40%; margin-left: auto; margin-right: auto;">
					<legend style="font-variant: small-caps;">Identifiez-vous</legend>
					<table style="width: 100%; border: 0;" cellpadding="5" cellspacing="0" summary="Tableau d\'identification">
						<tr>
							<td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="login">Identifiant</label></td>
							<td style="text-align: center; width: 60%;"><input type="text" name="login" size="16" /></td>
						</tr>
						<tr>
							<td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="no_anti_inject_password">Mot de passe</label></td>
							<td style="text-align: center; width: 60%;"><input type="password" name="no_anti_inject_password" size="16" /></td>
						</tr>
					</table>
					<input type="submit" name="submit" value="Envoyer" style="font-variant: small-caps;" />
				</fieldset>
			</div>
		</form>
	</body>
</html>
');

	die();
};

if ((isset ($_SESSION['statut'])) and ($_SESSION['statut'] != 'administrateur')) {
	if(($is_lcs_plugin!='yes')||($login_user!='admin')) {
		echo "<center><p class=grand><font color=red>Mise � jour de la base MySql de GEPI.<br />Vous n'avez pas les droits suffisants pour acc�der � cette page.</font></p></center></body></html>";
		die();
	}
}

if (isset ($_POST['maj'])) {
//if ((isset ($_POST['maj'])) || (($is_lcs_plugin!='yes')&&(isset($login_user))&&($login_user=='admin'))) {
	$pb_maj = '';
	// On commence la mise � jour
	$mess = "Mise � jour effectu�e.<br />(lisez attentivement le r�sultat de la mise � jour, en bas de cette page)";
	$result = '';
	$result_inter = '';


        // Remise � z�ro de la table des droits d'acc�s
        require 'updates/access_rights.inc.php';


	if (($force_maj == 'yes') or (quelle_maj("1.5.0"))) {
            require 'updates/144_to_150.inc.php';
	}


	if (($force_maj == 'yes') or (quelle_maj("1.5.1"))) {
            require 'updates/150_to_151.inc.php';
	}


	if (($force_maj == 'yes') or (quelle_maj("1.5.2"))) {
            require 'updates/151_to_152.inc.php';
	}

	if (($force_maj == 'yes') or (quelle_maj("1.5.3"))) {
            require 'updates/152_to_153.inc.php';
	}

	if (($force_maj == 'yes') or (quelle_maj("1.5.3.1"))) {
            require 'updates/153_to_1531.inc.php';
	}

	// Mise � jour du num�ro de version
	saveSetting("version", $gepiVersion);
	saveSetting("versionRc", $gepiRcVersion);
	saveSetting("versionBeta", $gepiBetaVersion);
	saveSetting("pb_maj", $pb_maj);
}


// Load settings
if (!loadSettings()) {
	die("Erreur chargement settings");
}

// Num�ro de version effective
$version_old = getSettingValue("version");
// Num�ro de version RC effective
$versionRc_old = getSettingValue("versionRc");
// Num�ro de version beta effective
$versionBeta_old = getSettingValue("versionBeta");

$rc_old = '';
if ($versionRc_old != '') {
	$rc_old = "-RC" . $versionRc_old;
}
$rc = '';
if ($gepiRcVersion != '') {
	$rc = "-RC" . $gepiRcVersion;
}

$beta_old = '';
if ($versionBeta_old != '') {
	$beta_old = "-beta" . $versionBeta_old;
}
$beta = '';
if ($gepiBetaVersion != '') {
	$beta = "-beta" . $gepiBetaVersion;
}

// Pb de mise � jour lors de la derni�re mise � jour
$pb_maj_bd = getSettingValue("pb_maj");

if (isset ($mess)) {
	echo "<center><p class=grand><font color=red>" . $mess . "</font></p></center>";
}
echo "<center><p class=grand>Mise � jour de la base de donn�es MySql de GEPI</p></center>";

echo "<hr /><h3>Num�ro de version actuel de la base MySql : GEPI " . $version_old . $rc_old . $beta_old . "</h3>";
echo "<hr />";
// Mise � jour de la base de donn�e

if ($pb_maj_bd != 'yes') {
	if (test_maj()) {
		echo "<h3>Mise � jour de la base de donn�es vers la version GEPI " . $gepiVersion . $rc . $beta . "</h3>";
		if (isset ($_SESSION['statut'])) {
			echo "<p>Il est vivement conseill� de faire une sauvegarde de la base MySql avant de proc�der � la mise � jour</p>";
			echo "<center><form enctype=\"multipart/form-data\" action=\"../gestion/accueil_sauve.php\" method=post name=formulaire>";
			if (getSettingValue("mode_sauvegarde") == "mysqldump") {
				echo "<input type='hidden' name='action' value='system_dump' />";
			} else {
				echo "<input type='hidden' name='action' value='dump' />";
			}
			echo "<input type=\"submit\" value=\"Lancer une sauvegarde de la base de donn�es\" /></form></center>";
		}
		echo "<p>Remarque : la proc�dure de mise � jour vers la version <b>GEPI " . $gepiVersion . $rc . $beta . "</b> est utilisable � partir d'une version GEPI 1.2 ou plus r�cente.</p>";
		echo "<form action=\"maj.php\" method=\"post\">";
		echo "<p><font color=red><b>ATTENTION : Votre base de donn�es ne semble pas �tre � jour.";
		if ($version_old != '')
		echo " Num�ro de version de la base de donn�es : GEPI " . $version_old . $rc_old . $beta_old;
		echo "</b></font><br />";
		echo "Cliquez sur le bouton suivant pour effectuer la mise � jour vers la version <b>GEPI " . $gepiVersion . $rc . $beta . "</b></p>";
		echo "<center><input type=submit value='Mettre � jour' /></center>";
		echo "<input type=hidden name='maj' value='yes' />";
		echo "<input type=hidden name='valid' value='$valid' />";
		echo "</form>";
	} else {
		echo "<h3>Mise � jour de la base de donn�es</h3>";
		echo "<p><b>Votre base de donn�es est � jour. Vous n'avez pas de mise � jour � effectuer.</b></p>";
		echo "<center><p class='grand'><b><a href='../gestion/index.php#maj'>Retour</a></b></p></center>";
		echo "<form action=\"maj.php\" method=\"post\">";
		//echo "<p><b>N�anmoins, vous pouvez forcer la mise � jour. Cette proc�dure, bien que sans risque, n'est utile que dans certains cas pr�cis.</b></font><br />";
		echo "<p><b>N�anmoins, vous pouvez forcer la mise � jour. Cette proc�dure, bien que sans risque, n'est utile que dans certains cas pr�cis.</b><br />";
		echo "Cliquez sur le bouton suivant pour effectuer la mise � jour forc�e vers la version <b>GEPI " . $gepiVersion . $rc . $beta . "</b></p>";
		echo "<center><input type=submit value='Forcer la mise � jour' /></center>";
		echo "<input type=hidden name='maj' value='yes' />";
		echo "<input type=hidden name='force_maj' value='yes' />";
		echo "<input type=hidden name='valid' value='$valid' />";
		echo "</form>";
	}
} else {
	echo "<h3>Mise � jour de la base de donn�es</h3>";
	echo "<p><b><font color = 'red'>Une ou plusieurs erreurs ont �t� rencontr�es lors de la derni�re mise � jour de la base de donn�es
.</font></b></p>";
	echo "<form action=\"maj.php\" method=\"post\">";
	echo "<p><b>Si vous pensez avoir r�gl� les probl�mes entra�nant ces erreurs, vous pouvez tenter une nouvelle mise � jour</b>";
	echo " en cliquant sur le bouton suivant pour effectuer la mise � jour vers la version <b>GEPI " . $gepiVersion . $rc . $beta . "</b>.</p>";
	echo "<center><input type=submit value='Tenter une nouvelle mise � jour' /></center>";
	echo "<input type=hidden name='maj' value='yes' />";
	echo "<input type=hidden name='force_maj' value='yes' />";
	echo "<input type=hidden name='valid' value='$valid' />";
	echo "</form>";
}
echo "<hr />";
if (isset ($result)) {
	echo "<center><table width=\"80%\" border=\"1\" cellpadding=\"5\" cellspacing=\"1\" summary='R�sultat de mise � jour'><tr><td><h2 align=\"center\">R�sultat de la mise � jour</h2>";

	if(!getSettingValue('conv_new_resp_table')){
		$sql="SELECT 1=1 FROM responsables";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			echo "<p style='font-weight:bold; color:red;'>ATTENTION:</p>\n";
			echo "<blockquote>\n";
			echo "<p>Une conversion des donn�es responsables est requise.</p>\n";
			echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
			echo "<p>Vous pouvez quand m�me prendre le temps de lire attentivement les informations de mise � jour ci-dessous.</p>\n";
			echo "</blockquote>\n";
		}
	}

	echo $result;
	echo "</td></tr></table></center>";
}
?>
</body></html>
