<?php
/*
 * $Id: logout.php 3323 2009-08-05 10:06:18Z crob $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$niveau_arbo = 0;
require_once("./lib/initialisations.inc.php");
global $gepiPath;


if ($session_gepi->current_auth_mode == "sso" and $session_gepi->auth_sso == "cas") {
	$session_gepi->close(0);
    $session_gepi->logout_cas();
    die();
}

// Ajout pour le multisite
	if (isset($_COOKIE["RNE"])) {
		unset($_COOKIE['RNE']);
		setcookie('RNE', 'RNE'); // permet d'effacer le contenu du cookie.
	}

    $message = "<h1 class='gepi'>D�connexion</h1>";
	$message .= "<img src='$gepiPath/images/icons/lock-open.png' alt='lock-open' /><br/><br/>";
    if (!$_GET['auto']) {
        $session_gepi->close($_GET['auto']);
        $message .= "Vous avez ferm� votre session GEPI.<br />";
        $message .= "<a href=\"$gepiPath/login.php\">Ouvrir une nouvelle session</a>.";
    } else if ($_GET['auto']==2) {
        $session_gepi->close($_GET['auto']);
        $message .= "Vous avez �t� d�connect�. Il peut s'agir d'une mauvaise configuration de la variable \$GepiPath dans la fichier \"connect.inc.php\"<br />
        <a href='aide_gepipath.php'><b>Aide � la configuration de \$GepiPath</b></a><br /><br />";
        $message .= "<a href=\"$gepiPath/login.php\">Ouvrir une nouvelle session</a>.";
    } else if ($_GET['auto']==3) {
        $date_fermeture = date("d\/m\/Y\ \�\ H\ \h\ i");
        $debut_session = urldecode($_GET['debut_session']);
        $sql = "select now() > END TIMEOUT from log where SESSION_ID = '" . $_GET['session_id'] . "' and START = '" . $debut_session . "'";
        if (sql_query1($sql)) {
           // Le temps d'inactivit� est d�pass�
           $session_gepi->close($_GET['auto']);
           $message .= "Votre session GEPI a expir� car le temps maximum (".getSettingValue("sessionMaxLength")." minutes) sans �change avec le serveur a �t� atteint.<br /><br />Date et heure de la d�connexion : ".$date_fermeture."<br /><br />";
           $message .= "<a href=\"$gepiPath/login.php\">Ouvrir une nouvelle session</a>.";
        } else {
           $message .= "<h1 class='gepi'>Fermeture d'une fen�tre GEPI</h1>";
           $message .= "A l'heure ci-dessous, une fen�tre GEPI s'est automatiquement ferm�e par mesure de s�curit� car
           le temps maximum d'inactivit� (".getSettingValue("sessionMaxLength")." minutes) avait �t� atteint.<br /><br />
           Heure et date de fermeture de la fen�tre : ".$date_fermeture;
        }
    } else {
        $session_gepi->close($_GET['auto']);
        $message .= "Votre session GEPI a expir�, ou bien vous avez �t� d�connect�.<br />";
        if ((getSettingValue("disable_login"))=='yes') $message .=  "<br /><font color=\"red\" size=\"+1\">Le site est momentan�ment inaccessible. Veuillez nous excuser de ce d�rangement !</font><br /><br />";
        $message .= "<a href=\"$gepiPath/login.php\">Ouvrir une nouvelle session</a>.";
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<META HTTP-EQUIV="Expires" CONTENT="0" />
<title>D�connexion</title>
<link rel="stylesheet" type="text/css" href="./<?php echo getSettingValue("gepi_stylesheet");?>.css" />
<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
<link rel="icon" type="image/ico" href="./favicon.ico" />
<?php
	// Styles param�trables depuis l'interface:
	if($style_screen_ajout=='y'){
		// La variable $style_screen_ajout se param�tre dans le /lib/global.inc
		// C'est une s�curit�... il suffit de passer la variable � 'n' pour d�sactiver ce fichier CSS et �ventuellement r�tablir un acc�s apr�s avoir impos� une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />";
	}
?>
</head>
<body>
<div class="center">
<?php
echo $message;

$agent = $_SERVER['HTTP_USER_AGENT'];

if (my_eregi("msie",$agent) && !eregi("opera",$agent)) {
	echo "<div style='width: 70%; margin: auto;'>";
	echo "<p><b>Note aux utilisateurs de Microsoft Internet Explorer :</b>";
	echo "<br/>Si vous subissez des d�connexions intempestives, si vous n'arrivez pas � vous connecter � Gepi, " .
			"ou bien s'il vous faut r�p�ter plusieurs fois la proc�dure de connexion avant de pouvoir acc�der aux outils de Gepi, " .
			"il est possible que votre navigateur en soit la cause. Nous vous recommandons de t�l�charger gratuitement et d'installer <a href='http://www.mozilla-europe.org/fr/products/firefox/'>Mozilla Firefox</a>, " .
			"qui vous garantira les meilleures conditions d'utilisation de Gepi.</p>";
	echo "</div>";
}

?>
</div>
</body>
</html>