<?
require_once("/usr/share/lcs/Plugins/Gepi/secure/connect.inc.php");
session_name("GEPI");
@session_start();
if (isset($_SESSION['start'])) {
    $res = mysql_query("update log set AUTOCLOSE = '1', END = now() where SESSION_ID = '" . session_id() . "' and START = '" . $_SESSION['start'] . "'");
}
// D�truit toutes les variables de session
session_unset();
$_SESSION = array();

// D�truit le cookie sur le navigateur
$CookieInfo = session_get_cookie_params();
@setcookie(session_name(), '', time()-3600, $CookieInfo['path']);
// d�truit la session sur le serveur
session_destroy();
?>