<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/del_group.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   Equipe Tice academie de Caen
   V 1.3 maj : 29/05/2009
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

  $cn=$_GET['cn'];

  header_html();
  aff_trailer ("6");
  if (is_admin("Annu_is_admin",$login)=="Y") {
    if ( $cn !="Eleves" && $cn !="Profs" && $cn !="Administratifs" ) {
      exec ("$scriptsbinpath/groupDel.pl $cn",$AllOutPut,$ReturnValue);
      if ($ReturnValue == "0") {
        echo "<strong>Le groupe $cn a &#233;t&#233; supprim&#233; avec succ&#232;s.</strong><br>\n";
      } else {
        echo "<div class='error_msg'>Echec de la suppression <font color='black'>(type d'erreur : $ReturnValue)</font>, veuillez contacter <A HREF='mailto:$MelAdminLCS?subject=PB changement mot de passe'>l'administrateur du syst&#232;me</A></div><BR>\n";
      }
    } else {
      echo "<div class=error_msg>La suppression des groups principaux (Eleves, Profs, Administratifs) n'est pas autoris&#233;e !</div>";
    }
  } else {
    echo "<div class=error_msg>Cette fonctionnalit&#233;, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
  }

  include ("../lcs/includes/pieds_de_page.inc.php");
?>
