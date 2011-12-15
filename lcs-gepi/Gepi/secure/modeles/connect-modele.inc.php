<?php
# Une fois renseign�, pensez � renommer ce fichier connect-modele.inc.php
# en connect.inc.php dans le repertoire secure
#
# ============
# Premier cas : vous �tes en configuration mono-site
# (une installation de Gepi / un �tablissement)
#
# 1- Laissez la variable $multisite � "n",
$multisite = "n";
#
# 2- Renseignez les cinq variables suivantes
# ($dbHost, $dbDb, $dbUser, $dbPass, et $gepiPath)
# selon votre configuration
#
# Le nom du serveur qui h�berge votre base mysql.
# (si c'est le m�me que celui qui h�berge les scripts php, mettre "localhost")
$dbHost="localhost";
# Le nom de votre base mysql
$dbDb="gepi";
# Le nom de l'utilisateur mysql qui a les droits sur la base
$dbUser="gepi";
# Le mot de passe de l'utilisateur mysql ci-dessus
$dbPass="gepi";
# Chemin relatif vers GEPI
$gepiPath="/gepi";

/**
 * Connexion permanente � la base
 * 
 * D�commenter la ligne et remplacer NULL par "oui" pour activer les connexions non permanentes
 *
 * @global int $GLOBALS['db_nopersist']
 * @name $db_nopersist
 */
//$GLOBALS['db_nopersist']=NULL;


/* Base de l'URL (sans le chemin relatif d�fini ci-dessus)
 * Cette variable est utile dans le cas de l'installation derri�re un reverse proxy,
 * ce qui peut induire en erreur les m�canismes de d�tection automatique
 * de l'adresse. Si cette variable n'est pas d�fini, les m�canismes automatiques
 * seront utilis�s.
 */
#$gepiBaseUrl = 'https://mongepi.fr'

# ============
# Deuxi�me cas : vous �tes en configuration multi-site
# (une installation de Gepi / plusieurs �tablissements)
#
# 1- Passez la variable $multisite � "y",
#    Remplacez "n" par "y" dans la ligne [$multisite = "n";]
#    situ�e au 1- du premier cas ci-dessus
#    ou d�-commentez -retirez le "# " en d�but de ligne- la ligne ci-dessous
#$multisite = "y";
#
# 2- Renseignez le fichier /secure/multisite.ini comme indiqu�
#
# 3- Modifiez la valeur "multisite" de la table "settings"
# en passant (via phpmyadmin par ex.) la commande sql suivante :
# UPDATE `nombase`.`setting` SET `VALUE` = 'y' WHERE NAME = 'multisite' LIMIT 1 ;
# d�commentez toute la portion de code suivante pour le multisite
/*
if ($multisite == "y" AND $_SERVER["SCRIPT_NAME"] != "/login.php") {
	$RNE = isset($_REQUEST['rne']) ? $_REQUEST['rne'] : (isset($_REQUEST['RNE']) ? $_REQUEST['RNE'] : (isset($_REQUEST['organization']) ? $_REQUEST['organization'] : (isset($_COOKIE['RNE']) ? $_COOKIE['RNE'] : NULL)));
	if (!isset($RNE) || $RNE == 'RNE') {
		echo 'Erreur : Numero d\'etablissement manquant dans la requete (parametre rne ou organization).'; die();
	} else {
		setcookie('RNE', $RNE, null, '/');
		$_COOKIE['RNE'] = $RNE;
		$init = parse_ini_file(dirname(__FILE__)."/../secure/multisite.ini.php", TRUE);
		if (	!isset($init[$RNE]["nomhote"]) ||
			!isset($init[$RNE]["nombase"]) ||
			!isset($init[$RNE]["mysqluser"]) ||
			!isset($init[$RNE]["mysqlmdp"]) ||
			!isset($init[$RNE]["pathname"])
			) {
			echo 'Erreur : Numero d\'etablissement '.$RNE.' non trouve dans la configuration'; die();
		}
		$dbHost		= $init[$RNE]["nomhote"];
		$dbDb		= $init[$RNE]["nombase"];
		$dbUser		= $init[$RNE]["mysqluser"];
		$dbPass		= $init[$RNE]["mysqlmdp"];
		$gepiPath	= $init[$RNE]["pathname"];
	}
}
 */
#

$mode_debug = false;
$debug_log_file = '/var/log/gepi.log';

// En mettant la variable suivante � 'dump' ou 'system_dump', vous pouvez
// forcer le type de sauvegarde r�alis�, sans laisser le choix �
// l'administrateur.
// - dump : le backup bas� sur un ensemble de requ�tes SQL (lourd, mais passe partout)
// - system_dump : utilise la commande syst�me 'mysqldump', beaucoup plus rapide mais n�cessite un syst�me permettant l'ex�cution de la commande
$force_backup_mode = false;
?>
