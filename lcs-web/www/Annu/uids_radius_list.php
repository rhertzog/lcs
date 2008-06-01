<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/uids_radius_lis.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   Version du 16/06/2006
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";


  list ($idpers)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  header_html();
  $uids = search_radius ("uid=*");
  if (count($uids)) {
    echo "<strong>Nbr uids dans la branche radius</strong> = ".count($uids)."<br>\n";
    for ($loop=0; $loop<count($uids);$loop++) {
        echo "<ul>\n";
            echo "<li>uid = ".$uids[$loop]["uid"]." Vlan n° :".$uids[$loop]["radiustunnelprivategroupid"]."</li>\n";
        echo "</ul>\n";
    }
  } else echo "rien trouvé :(... Dommage! essaie encore...<br>\n";

  include ("../lcs/includes/pieds_de_page.inc.php");
?>