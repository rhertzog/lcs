<?
        /* Réalise la connexion à la base d'authentification */
        /* et charge les fonctions d'authentification */
        require ("/var/www/lcs/includes/config.inc.php");
        require_once ("/var/www/lcs/includes/functions.inc.php");
        $authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");
?>
