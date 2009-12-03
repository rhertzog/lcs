<?php
/* $Id$
*
* Copyright 2001, 2008 Thomas Belliard
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

$niveau_arbo = 0;

// Initialisations files
require_once("./lib/initialisations.inc.php");


# On arrive sur cette page en cas d'�chec de la proc�dure d'authentification.
# Le principe est simple : on affiche un message d'erreur selon le code pass�
# en URL, et on propose un lien vers le formulaire de login.

# On initialise les messages :

# Compte bloqu� : trop de tentatives infructueuses.
if ($_GET['error'] == '2') {
	$message = 'Trop de tentatives de connexion infructueuses : votre compte est momentan�ment verrouill�.';

# IP liste noire
} elseif ($_GET['error'] == '3') {
	$message = 'Connexion impossible : vous tentez de vous connecter � partir d\'une adresse IP interdite.';

# Auth externe r�ussie, mais utilisateur 'inactif'
} elseif ($_GET['error'] == '4') {
	$message = 'Vous avez bien �t� identifi� mais <b>votre compte a �t� d�sactiv�</b>. Impossible de continuer. Veuillez signaler ce probl�me � l\'administrateur du site.';

# Auth externe r�ussie, mais inconsistence dans le mode d'authentification
} elseif ($_GET['error'] == '5') {
	$message = 'Vous avez bien �t� identifi� mais votre compte utilisateur est param�tr� pour utiliser un autre mode d\'authentification. Si vous pensez qu\'il s\'agit d\'une erreur, veuillez signaler ce probl�me � l\'administrateur du site.';
    if ($_GET['mode'] == "sso" && ($session_gepi->auth_locale || $block_sso)) {
		$message .= "<br /><br />Si vous poss�dez un compte local d'acc�s � GEPI, vous pouvez n�anmoins <b><a href='./login.php'>acc�der � la page de connexion de GEPI</a></b>.";
    }
# Auth externe r�ussie, mais compte inexistant en local et import impossible
} elseif ($_GET['error'] == '6') {
	$message = 'Vous avez bien �t� identifi� mais la mise � jour de votre profil dans GEPI n\'a pas pu s\'effectuer correctement. Impossible de continuer. Veuillez signaler ce probl�me � l\'administrateur du site.';

# L'administrateur a d�sactiv� les connexions � Gepi
} elseif ($_GET['error'] == '7') {
	$message = 'GEPI est momentan�ment inaccessible.';

# Multisite : impossible de d�terminer le RNE
} elseif ($_GET['error'] == '8') {
	$message = 'Vous avez �t� correctement authentifi�, mais votre compte n\'a pas pu �tre associ� avec un �tablissement configur� sur ce Gepi. Impossible de continuer. Veuillez signaler ce probl�me � l\'administrateur du site.';

# Simple �chec de l'authentification.
} elseif ($_GET['error'] == '9') {
	$message = 'Vous n\'avez pas �t� authentifi�. V�rifiez votre login/mot de passe.';

// Quand un statut 'autre' n'a pas de statut personnalis�
} elseif ($_GET['error'] == '10') {
	$message = 'Vous n\'avez pas de droits suffisants pour entrer, veuillez contacter l\'administrateur de Gepi.';

} else {
	$message = 'Une erreur ind�termin�e s\'est produite lors de votre authentification.';
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<title>�chec de la connexion � Gepi</title>
<link rel="stylesheet" type="text/css" href="./<?php echo getSettingValue("gepi_stylesheet");?>.css" />
<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
<link rel="icon" type="image/ico" href="./favicon.ico" />
<?php
	// Styles param�trables depuis l'interface:
	//if($style_screen_ajout=='y'){
	if(($style_screen_ajout=='y')&&(file_exists('./style_screen_ajout.css'))) {
		// La variable $style_screen_ajout se param�tre dans le /lib/global.inc
		// C'est une s�curit�... il suffit de passer la variable � 'n' pour d�sactiver ce fichier CSS et �ventuellement r�tablir un acc�s apr�s avoir impos� une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />";
	}
?>
</head>
<body>
<div class="center">
<h1>�chec de la connexion � Gepi</h1>
<p style="color: red;">
<?php
echo $message;
?>
</p>
<p>
<?php
if (isset($_GET['mode']) && $_GET['mode'] == "sso") {
	echo "<a href='login_sso.php'>Retourner � la page d'authentification</a>";
	if ($session_gepi->auth_locale || $session_gepi->auth_ldap) {
		echo "</p><p><a href='login.php'>Acc�der au formulaire d'authentification locale</a>";
	}
} else {
	echo "<a href='login.php'>Retourner � la page d'authentification</a>";
}
?>
</p>
</div>
</body>
</html>