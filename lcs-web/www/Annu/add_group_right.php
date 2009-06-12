<?php
/* =============================================
   Projet LCS-SE3
   Consultation de l'annuaire LDAP
   annu/add_group_right.php
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « wawa »  olivier.lecluse@crdp.ac-caen.fr
   Equipe Tice academie de Caen
   V 1.3 maj : 29/05/2009
   Distribue selon les termes de la licence GPL
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  
//register global
if ( isset($_POST['cn']))  $cn = $_POST['cn'];
elseif ( isset($_GET['cn'])) $cn = $_GET['cn'];  
$newrights = $_POST['newrights'];
  
  header_html();

  aff_trailer ("3");

  if (ldap_get_right("lcs_is_admin",$login)=="Y") {
    if ( !$newrights ) {
      echo "<H3>D&#233;l&#233;guer un droit au groupe $cn</H3>\n";
      echo "S&#233;l&#233;ctionner les droits &#224; d&#233;l&#233;guer<BR>\n";
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
        echo "<P>Vous avez s&#233;lectionn&#233;". count($newrights)."droit(s)<BR>\n";
        for ($loop=0; $loop < count($newrights); $loop++) {
            $right=$newrights[$loop];
            echo "D&#233;l&#233;gation du droit <U>$right</U> au groupe $cn<BR>";
            $cDn = "cn=$cn,$groupsRdn,$ldap_base_dn";
            $pDn = "cn=$right,$rightsRdn,$ldap_base_dn";
            exec ("$scriptsbinpath/groupAddEntry.pl $cDn $pDn");
            echo "<BR>";  
        }
    }
  } else {
    echo "<div class=error_msg>Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
