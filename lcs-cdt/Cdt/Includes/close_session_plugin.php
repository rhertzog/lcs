<?php 
session_name("Cdt_Lcs");
@session_start();
// On detruit toutes les variables de session 
$_SESSION = array();
// on detruit le cookie sur le navigateur 
setcookie("Cdt_Lcs","", 0,"/","",0);
// On detruit la session sur le serveur.
session_destroy();
?>