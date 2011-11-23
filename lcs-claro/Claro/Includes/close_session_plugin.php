<?
require_once  dirname(__FILE__) . '/../platform/conf/claro_main.conf.php';
$sessid=$GLOBALS['platform_id'];
session_name($sessid);
@session_start();
#// On détruit toutes les variables de session
$_SESSION = array();
#// On détruit la session sur le serveur.
session_destroy();
?>
