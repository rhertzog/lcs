<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];
include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";

if (is_admin("system_is_admin",$login)!="Y")
	die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</BODY></HTML>");

header_html();
$uids = search_radius ("uid=*");

if (count($uids)) {
    echo "<strong>Nbr uids dans la branche radius</strong> = ".count($uids)."<br>\n";
    for ($loop=0; $loop<count($uids);$loop++) {
        echo "<ul>\n";
            echo "<li>uid = ".$uids[$loop]["uid"]." Vlan n� :".$uids[$loop]["radiustunnelprivategroupid"]."</li>\n";
        echo "</ul>\n";
    }
} else echo "rien trouv&#233; :(... Dommage! essaie encore...<br>\n";

include ("../lcs/includes/pieds_de_page.inc.php");
?>
