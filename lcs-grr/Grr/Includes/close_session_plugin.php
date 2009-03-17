<?php
require_once("/usr/share/lcs/Plugins/Grr/include/connect.inc.php");
// Nom du cookie
session_name("GRR_LCS");
@session_start();
// On enregistre le log
if (isset($_SESSION['start'])) {
    $res = mysql_query("update grr_log set AUTOCLOSE = '1', END = now() where SESSION_ID = '" . session_id() . "' and START = '" . $_SESSION['start'] . "'");
}
// On détruit toutes les variables de session
$_SESSION = array();
// on détruit le cookie sur le navigateur
$CookieInfo = session_get_cookie_params();
@setcookie(session_name(), '', time()-3600, $CookieInfo['path']);
// On détruit la session sur le serveur.
session_destroy();

