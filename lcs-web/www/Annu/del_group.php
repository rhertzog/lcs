<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/del_group.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.3 maj : 10/10/2003
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

  header_html();
  aff_trailer ("6");
  if (is_admin("Annu_is_admin",$login)=="Y") {
    if ( $cn !="Eleves" && $cn !="Profs" && $cn !="Administratifs" ) {
      exec ("$scriptsbinpath/groupDel.pl $cn",$AllOutPut,$ReturnValue);
      if ($ReturnValue == "0") {
        echo "<strong>Le groupe $cn a été supprimé avec succès.</strong><br>\n";
      } else {
        echo "<div class='error_msg'>Echec de la suppression <font color='black'>(type d'erreur : $ReturnValue)</font>, veuillez contacter <A HREF='mailto:$MelAdminLCS?subject=PB changement mot de passe'>l'administrateur du système</A></div><BR>\n";
      }
    } else {
      echo "<div class=error_msg>La suppression des groups principaux (Eleves, Profs, Administratifs) n'est pas autorisée !</div>";
    }
  } else {
    echo "<div class=error_msg>Cette fonctionnalité, nécessite les droits d'administrateur du serveur LCS !</div>";
  }

  include ("../lcs/includes/pieds_de_page.inc.php");
?>
