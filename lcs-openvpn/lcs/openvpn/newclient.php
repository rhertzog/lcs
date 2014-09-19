<?php
/* =============================================
   Projet LCS-SE3
   Liste des profs pour openvpn
   Equipe Tice académie de Caen
   Derniere modifications : 17/04/2014
   Distribué selon les termes de la licence GPL
   ============================================= */
   
include "/var/www/Annu/includes/check-token.php";
if (!check_acces(1)) exit;

$login=$_SESSION['login'];
   
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php");



$html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n
          <head>\n
          <title>...::: S&#233;lection briques fonctionnelles LCS  :::...</title>\n
          <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
          </head>\n
          <body>\n";
          
echo $html;

echo "<h1>Gestion LCS OpenVPN</h1>\n";


if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc&egrave;der &agrave; cette fonction.")."</BODY></HTML>");


if (!isset($_POST['login'])) {
	$uids = search_uids ("(cn=Profs)", "half");
	$people = search_people_groups ($uids,"(sn=*)","cat");
	echo "<div align=center>";
	if (count($people)) {
    	echo "Il y a ".count($people)." membre";
    	if ( count($people) >1 ) echo "s";
    	echo " dans le groupe Profs</br>\n" ;
    	echo "<form method=post>";
    	echo "<select name=login size=1>";
    	for ($loop=0; $loop < count($people); $loop++) 
        	echo "<option value=".$people[$loop]["uid"].">".$people[$loop]["fullname"]." ".$people[$loop]["sexe"]."</option>";
     	echo "</select></br><input type='submit' value='Valider' /> ";
     	echo "</form></div>";
	} else 
        echo " <strong>Pas de membres</strong> dans le groupe Profs.<br>";
} else {
	echo "<div align=center>\n";
	// Config purifier object
	include ("/var/www/lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
	$config = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($config);
    // Purifier POST variables	
	$login=$purifier->purify($_POST['login']);
	
	echo "login : $login<br />";
	
	exec("sudo /usr/share/lcs/sbin/lcs-openvpn-generclient.sh ". escapeshellarg($login) );
        
	echo "g&eacute;n&eacute;ration du certificat pour ".$login." termin&eacute; </br>";
	echo "l'utilisateur a re&ccedil;u la proc&eacute;dure par e-mail pour utiliser le VPN";
	echo "</div>\n";
}

include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
