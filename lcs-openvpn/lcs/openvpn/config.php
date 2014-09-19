<?php
//Projet LCS page de parmetre des modules.

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


if (!isset($_POST['submit'])) {
        echo "<table border=\"0\"><form method=post>";
        $result=mysql_query("SELECT * FROM lcs_db.params WHERE name IN ('vpnnet', 'vpnnetmasq', 'vpnport', 'vpncert', 'vpndh', 'vpnvlan', 'vpnconnexions', 'vpnclienttoclient', 'vpnmasquerade' , 'vpnwins', 'vpndnsclient') ORDER BY `id`");
        if ($result) {
                while ($r=mysql_fetch_array($result)) {
                        echo "<tr><td colspan=\"2\">".$r["descr"]." (<em><font color=\"red\">".$r["name"]."</font></em>)</td>";
                        echo "<td><input type=\"text\" size=\"50\" value=\"".$r["value"]."\" name=\"form_".$r["name"]."\"</td></tr>\n";        
                }
        }
        echo "</table>";
        echo "<input type='submit' value='Valider'>";
        echo "<input type='hidden' value=1 name='submit'></form>";
}

if (isset($_POST['submit'])) {
	echo "<h2>Traitement des modifications</h2>";
	echo "<p>Le Service openvpn va redemarrer pour prendre en compte les modifications.</p>";
	
	// Config purifier object
	include ("/var/www/lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
	$config = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($config);
	// Purifier POST variables
	$vpnnet=$purifier->purify($_POST['form_vpnnet']);
   $vpnnetmasq=$purifier->purify($_POST['form_vpnnetmasq']);
   $vpnport=$purifier->purify($_POST['form_vpnport']);
   $vpncert=$purifier->purify($_POST['form_vpncert']);
   $vpndh=$purifier->purify($_POST['form_vpndh']);
   $vpnvlan=$purifier->purify($_POST['form_vpnvlan']);
	$clienttoclient=$purifier->purify($_POST['form_vpnclienttoclient']);
	$vpnconnexions=$purifier->purify($_POST['form_vpnconnexions']);
	$vpnmasquerade=$purifier->purify($_POST['form_vpnmasquerade']);
	$vpnwins=$purifier->purify($_POST['form_vpnwins']);
	$vpndnsclient=$purifier->purify($_POST['form_vpndnsclient']);
	
	exec("sudo -H /usr/share/lcs/sbin/lcs-openvpn-reconfig ". escapeshellarg($vpnport) ." ". escapeshellarg($vpnnet) ." ". escapeshellarg($vpnnetmasq) ." ". escapeshellarg($vpndh) ." ". escapeshellarg($vpnvlan) ." ". escapeshellarg($vpnconnexions) ." ". escapeshellarg($clienttoclient) ." ". escapeshellarg($vpncert) ." ". escapeshellarg($vpnmasquerade) ." ". escapeshellarg( $vpnwins) ." ". escapeshellarg($vpndnsclient) );
	
	echo "port : $vpnport</br>
	netmasq : $vpnnetmasq</br>
	netwok : $vpnnet </br>
	netmasq : $vpnnetmasq </br>
	certif : $vpncert </br>
	dh :  $vpndh </br>
	vlan : $vpnvlan </br>
	nb de connexions : $vpnconnexions </br> 
	connexion entre les clients : $clienttoclient </br>
	option masquerade : $vpnmasquerade</br>
	option WINS : $vpnwins</br>
	option DNS : $vpndnsclient</br>";
	

	echo "<p>Changements valides.</p>\n";
		
}

include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
