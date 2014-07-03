<?php 
session_name("Lcs");
@session_start();
// On detruit toutes les variables de session 
$_SESSION = array();
// On detruit la session sur le serveur.
session_destroy();
?>
