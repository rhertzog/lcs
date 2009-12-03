<?php
/* $Id: login_sso.php 2481 2008-09-28 16:42:56Z tbelliard $
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

// Pour le multisite
if (isset($_GET["rne"])) {
	setcookie('RNE', $_GET["rne"]);
}

$niveau_arbo = 0;

// Cas particulier du single sign-out CAS
// On doit emp�cher le filtrage de $_POST['logoutRequest'], qui contient des
// caract�res sp�ciaux
if (isset($_POST) && array_key_exists('logoutRequest', $_POST)) {
    $logout_request = $_POST['logoutRequest'];
}
// Initialisations files
require_once("./lib/initialisations.inc.php");


if ($session_gepi->auth_sso && isset($logout_request)) {
    $_POST['logoutRequest'] = $logout_request;
}

# Cette page a pour vocation de g�rer les authentification SSO.
# Si l'authentification SSO n'est pas param�tr�e, on renvoie tout de suite
# vers la page de login classique.

if (!$session_gepi->auth_sso) {
	session_write_close();
	header("Location:login.php");
	die();
}

# L'instance de Session permettant de g�rer directement les authentifications
# SSO, on ne s'emb�te pas :

$auth = $session_gepi->authenticate();
if ($auth == "1") {
	# Authentification r�ussie
	session_write_close();
	header("Location:accueil.php");
	die();
} else {
	# Echec d'authentification.
	session_write_close();
	header("Location:login_failure.php?error=".$auth."&mode=sso");
	die();
}
?>