<?
        /* R�alise la connexion � la base d'authentification */
        /* et charge les fonctions d'authentification */
        require ("/var/www/lcs/includes/config.inc.php");
        require_once ("/var/www/lcs/includes/functions.inc.php");
        if (!$authlink) $authlink=($GLOBALS["___mysqli_ston"] = mysqli_connect("$HOSTAUTH",  "$USERAUTH",  "$PASSAUTH"));
?>