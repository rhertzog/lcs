<? 
session_name("'SACoche-session'");
@session_start();
// On détruit toutes les variables de session 
$_SESSION = array();
// on détruit le cookie sur le navigateur 
setcookie(session_name(),'',time()-42000,'/');
// On détruit la session sur le serveur.
session_destroy();
?>