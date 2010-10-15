<?php
include "./include/connect.inc.php";
include "./include/config.inc.php";
include "./include/misc.inc.php";
include "./include/functions.inc.php";
include "./include/$dbsys.inc.php";
// Settings
require_once("./include/settings.inc.php");
//Chargement des valeurs de la table settingS
if (!loadSettings())
    die("Erreur chargement settings");
// Session related functions
require_once("./include/session.inc.php");
// Resume session
if (!grr_resumeSession()) {
    header("Location: ./logout.php?auto=1");
    die();
};
// Paramètres langage
include "./include/language.inc.php";
?>