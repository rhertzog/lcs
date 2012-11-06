<?php

//Projet LCS page de parmetre des modules.

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php");
header_html();
$msgIntro = "<H1>Gestion mode S&eacutecurit&eacute</H1>\n";
list ($idpers, $login)= isauth();
function mktable($title, $content) {
        echo "<h3>$title</h3>\n";
        echo $content;
}
if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc&egrave;der &agrave; cette fonction.")."</BODY></HTML>");


function module_enabled($name) {
    return function_exists('apache_get_modules') && in_array($name, apache_get_modules());
}
 

if (module_enabled('mod_security2')) {
    echo "Le module de s&eacutecurit&eacute est actif.";
	if (!isset($_POST['disable']))
	{
	echo "<form method=post>";
	 echo "</select></br><input name=disable type='submit' value='Désactiver' /> ";
	echo "</form>";
	}
	else {
	echo "Le mode s&eacute;curit&eacute; est en cours de d&eacute;sactivation. </br>
	le serveur Web va &ecirc;tre relanc&eacute";
	exec("sudo /usr/share/lcs/sbin/lcs-disable-apache-security ");
	}

} else {
    echo "L'environnement actuel ne dispose pas du module de s&eacute;curit&eacute;.";
 "Le module de s&eacutecurit&eacute est actif.";
        if (!isset($_POST['enable']))
        {
	echo "<form method=post>";
         echo "</select></br><input name=enable type='submit' value='Activer' /> ";
         echo "</form>";
        }
        else {
	        exec("sudo /usr/share/lcs/sbin/lcs-enable-apache-security ");
        }
}

include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
