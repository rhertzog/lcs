<?php 
session_name("Cdt_Lcs");
@session_start();
// On détruit toutes les variables de session 
$_SESSION = array();
// on détruit le cookie sur le navigateur 
setcookie("Cdt_Lcs","", 0,"/","",0);
// On détruit la session sur le serveur.
session_destroy();
?>