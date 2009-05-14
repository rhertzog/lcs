<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/del_user.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   Equipe Tice academie de Caen
   derniere modification : 14 Mai 2009
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

  header_html();
  aff_trailer ("3");
  if (is_admin("Annu_is_admin",$login)=="Y") {
    $uid=$_GET['uid'];
    // suppression d'un d'utilisateur
    if ($uid == "admin" )  {
      echo "<div class=error_msg>Vous ne pouvez pas effacer le compte administrateur !</div>";
    } elseif (!$uid)  {
      echo "<div class=error_msg>Vous devez pr&eacute;ciser le login du compte &agrave; effacer !</div>";
    } else {
        exec ("/usr/bin/sudo $scriptsbinpath/userDel.pl $uid",$AllOutPut,$ReturnValue);
        if ($ReturnValue == "0") {
          echo "Le compte <strong>$uid</strong> a &eacute;t&eacute; effac&eacute; avec succ&eagrave;s !<br />\n";
        } else {
          echo "<div class=error_msg>
                  Echec, l'utilisateur $uid n'a pas &eacute;t&eacute; effac&eacute; !
                  (type d'erreur : $ReturnValue), veuillez contacter
                  <a href='mailto:$MelAdminLCS?subject=Effacement utilisateur $uid'>
                  l'administrateur du syst&eagrave;me</a>
                </div>\n<br />\n";
        }
    }
  } else {
    echo "<div class=error_msg>Cette fonctionnalit&eacute;, nécessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
