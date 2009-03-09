<?php
//Projet LCS page de parmetre des modules.

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
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
        $result=mysql_query("SELECT * from lcs_db.params WHERE id between 100 and 106 ORDER BY `id`");
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
        echo "le Service openvpn va redemarrer pour prendre en compte les modifications";
        $vpnnet==$_POST['vpnnet'];
        $vpnnetmasq==$_POST['vpnnetmasq'];
        $vpnport==$_POST['vpnport'];
        $vpncert==$_POST['vpncert'];
        $vpndh==$_POST['vpndh'];
        $vpnvlan==$_POST['vpnvlan'];
	$vpnconnexion==$_POST['vpnconnexions'];
        #exec("sudo /usr/share/lcs/sbin/lcs-openvpn-reconfig.sh $vpnnet, $vpnnetmasq, $vpnport, $vpncert, $vpndh, $vpnvlan $vpnconnexions");
        echo "$vpnnet, $vpnnetmasq, $vpnport, $vpncert, $vpndh, $vpnvlan $vpnconnexions";


}


include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
