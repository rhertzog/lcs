<?php
/* =============================================
   Projet LCS-SE3
   Consultation de l'annuaire LDAP
   annu/add_group_right.php
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   « wawa »  olivier.lecluse@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.3 maj : 05/01/2004
   Distribué selon les termes de la licence GPL
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  header_html();

  aff_trailer ("3");

  if (ldap_get_right("lcs_is_admin",$login)=="Y") {
    if ( !$newrights ) {
      echo "<H3>Déléguer un droit au groupe $cn</H3>\n";
      echo "Séléctionner les droits à déléguer<BR>\n";
      // Lecture des droits disponibles
      $userDn="cn=$cn,$groupsRdn,$ldap_base_dn";
      $list_rights=search_machines("(!(member=$userDn))","rights");
      if ( count($list_rights)>0) {
        echo "<FORM method=\"post\">\n";          
        if   ( count($list_rights)>15) $size=15; else $size=count($list_rights);
        echo "<SELECT NAME=\"newrights[]\" SIZE=\"$size\" multiple=\"multiple\">";
        for ($loop=0; $loop < count($list_rights); $loop++) {
            echo "<option value=".$list_rights[$loop]["cn"].">".$list_rights[$loop]["cn"]."\n";
        }
        echo "</SELECT>&nbsp;&nbsp;\n";           
        echo "<INPUT TYPE=\"hidden\" VALUE=\"$cn\" NAME=\"cn\">";
        echo "<input type=\"submit\" value=\"Valider\">\n";
        echo "</FORM>\n";
      }
    }  else  {
        // Inscription des droits dans l'annuaire   
        echo "<H3>Inscription des droits pour <U>$cn</U></H3>";
        echo "<P>Vous avez sélectionné". count($newrights)."droit(s)<BR>\n";
        for ($loop=0; $loop < count($newrights); $loop++) {
            $right=$newrights[$loop];
            echo "Délégation du droit <U>$right</U> au groupe $cn<BR>";
            $cDn = "cn=$cn,$groupsRdn,$ldap_base_dn";
            $pDn = "cn=$right,$rightsRdn,$ldap_base_dn";
            exec ("$scriptsbinpath/groupAddEntry.pl $cDn $pDn");
            echo "<BR>";  
        }
    }
  } else {
    echo "<div class=error_msg>Cette application, nécessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
