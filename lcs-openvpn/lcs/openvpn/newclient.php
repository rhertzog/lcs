<?php
//Projet LCS
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion LCS OpenVPN</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc�der � cette fonction")."</BODY></HTML>");

function mktable($title, $content) {
        echo "<h3>$title</h3>\n";
        echo $content;
}


include ("/var/www/lcs/includes/pieds_de_page.inc.php");

?>
