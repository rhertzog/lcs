<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/del_user.php
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
  aff_trailer ("3");
  if (is_admin("Annu_is_admin",$login)=="Y") {
    // suppression d'un d'utilisateur
    if ($uid == "admin" )  {
      echo "<div class=error_msg>Vous ne pouvez pas effacer le compte administrateur !</div>";
    } elseif (!$uid)  {
      echo "<div class=error_msg>Vous devez préciser le login du compte a effacer ! !</div>";
    } else {
        exec ("$scriptsbinpath/userDel.pl $uid",$AllOutPut,$ReturnValue);
        if ($ReturnValue == "0") {
          echo "Le compte <strong>$uid</strong> a été effacé avec succès !<BR>\n";
        } else {
          echo "<div class=error_msg>
                  Echec, l'utilisateur $uid n'a pas été effacé !
                  (type d'erreur : $ReturnValue), veuillez contacter
                  <A HREF='mailto:$MelAdminLCS?subject=Effacement utilisateur $uid'>
                  l'administrateur du système</A>
                </div><BR>\n";
        }
    }
  } else {
    echo "<div class=error_msg>Cette fonctionnalité, nécessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
