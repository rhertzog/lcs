<?php
//Projet LCS page de parmetre des modules.

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php");
header_html();
$msgIntro = "<H1>Gestion LCS OpenVPN</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acci?1/2der i?1/2 cette fonction")."</BODY></HTML>");

function mktable($title, $content) {
        echo "<h3>$title</h3>\n";
        echo $content;
}

if (!isset($_POST['submit'])) {
        echo "Parametre de Lcs-OpenVPN ";
        echo "<table border=\"0\"><form method=post>";
        $result=mysql_query("SELECT * FROM lcs_db.params WHERE name IN ('vpnnet', 'vpnnetmasq', 'vpnport', 'vpncert', 'vpndh', 'vpnvlan', 'vpnconnexions', 'vpnclienttoclient', 'vpnmasquerade' , 'vpnwins', 'vpndnsclient') ORDER BY `id`");
        if ($result) {
                while ($r=mysql_fetch_array($result)) {
                        echo "<tr><td colspan=\"2\">".$r["descr"]." (<em><font color=\"red\">".$r["name"]."</font></em>)</td>";
                        echo "<td><input type=\"text\" size=\"50\" value=\"".$r["value"]."\" name=\"form_".$r["name"]."\"</td></tr>\n";        
                //echo "<td><input TYPE=\"text\" size=\"50\" value=\"".$r["value"]."\" NAME=\"".$r["name"]."</td></tr>\n";
                }
        }
        echo "</table>";
        echo "<input type='submit' value='Valider'>";
        echo "<input type='hidden' value=1 name='submit'></form>";
}

if (isset($_POST['submit'])) {
        echo "Traitement des modification";
        echo "le Service openvpn va redemarrer pour prendre en compte les modifications </br>";
        $vpnnet=$_POST['form_vpnnet'];
        $vpnnetmasq=$_POST['form_vpnnetmasq'];
        $vpnport=$_POST['form_vpnport'];
        $vpncert=$_POST['form_vpncert'];
        $vpndh==$_POST['form_vpndh'];
        $vpnvlan=$_POST['form_vpnvlan'];
	$clienttoclient=$_POST['form_vpnclienttoclient'];
	$vpnconnexions=$_POST['form_vpnconnexions'];
	$vpnmasquerade=$_POST['form_vpnmasquerade'];
	$vpnwins=$_POST['form_vpnwins'];
	$vpndnsclient=$_POST['form_vpndnsclient'];
	exec("sudo -H /usr/share/lcs/sbin/lcs-openvpn-reconfig $vpnport $vpnnet $vpnnetmasq $vpndh $vpnvlan $vpnconnexions $clienttoclient $vpncert $vpnmasquerade $vpnwins $vpndnsclient");
        
        echo "port : $vpnport </br>
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
	
	echo "<p>Changements valide.</p>";
		
}

include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
