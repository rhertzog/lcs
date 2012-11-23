<?php

//Projet LCS activation/desactivation du mode securite d'apache.

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php");

$reload=$_POST['reload'] ;

list ($idpers, $login)= isauth();

$html = "
	  <head>\n
	  	<title>...::: LCS sécurité  :::...</title>\n
	  	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
echo $html;	  	

if (  isset($reload) ) {
	echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"1;url='$PHP_SELF'\">\n";	
} elseif ( file_exists("/tmp/disablemodesecurity.lock") || file_exists("/tmp/enablemodesecurity.lock") )
	echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"10;url='$PHP_SELF'\">\n";

$html = "	  	
	    <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
	  </head>\n
	  <body>\n";
$html .= "<div id='container'>\n<h1>Sécurité serveur web LCS</h1>\n";
echo $html;

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
    echo "<h2>Le module de s&eacutecurit&eacute est actif.</h2>";
    if ( file_exists("/tmp/disablemodesecurity.lock") ) {
    	#echo "D&eacute;sactivation du mode s&eacute;curit&eacute; en cours !..<br />";
        echo "<div align='center'>\n
				<img src=\"Images/wait.gif\" title=\"Patientez...\" align=\"middle\" border=\"0\" ALT=\"Patientez\">\n&nbsp;D&eacute;sactivation du mode s&eacute;curit&eacute; en cours ! Veuillez patienter...
        	</div>\n";
    } else {    	
		if (!isset($_POST['disable']))
		{
			echo "<form name ='security' action='mod_security.php' method='post'>\n";
			echo "	</select></br><input name=disable type='submit' value='Désactiver le mode sécurité' />\n";
			echo "<input type=\"hidden\" name=\"reload\" value=\"true\">\n";
			echo "</form>\n";
		}
		else {
			echo "Le mode s&eacute;curit&eacute; est en cours de d&eacute;sactivation. </br>
			  Le serveur Web va &ecirc;tre relanc&eacute...<br />";
			exec("sudo /usr/sbin/lcs-disable-apache-security ");
		}
    }

} else {
    echo "<h2>L'environnement actuel ne dispose pas du module de s&eacute;curit&eacute;.</h2>";
        if ( file_exists("/tmp/enablemodesecurity.lock") ) {
        	echo "<div align='center'>
        			<img src=\"Images/wait.gif\" title=\"Patientez...\" align=\"middle\" border=\"0\" ALT=\"Patientez\">&nbsp;Activation du mode s&eacute;curit&eacute; en cours ! Veuillez patienter...
        		</div>";
        } else {	       
        	if (!isset($_POST['enable']))
        	{
				echo "<form name ='security' action='mod_security.php' method='post'>\n";
         		echo "	</select></br><input name=enable type='submit' value='Activer le mode sécurité' />\n";
         		echo "<input type=\"hidden\" name=\"reload\" value=\"true\">\n";
         		echo "</form>\n";
        	}
        	else {
	        	exec("sudo /usr/sbin/lcs-enable-apache-security ");
        	}
        }
}
echo "<a href='../doc/security/html/'>Documentation module LCS sécurité</a>\n";
echo "</div><!-- Fin container-->\n";
include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
