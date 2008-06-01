<?php
/* =============================================
   Projet LCS-SE3
   Consultation de l'annuaire LDAP
   annu/add_user_right.php
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   « wawa »  olivier.lecluse@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.4 maj : 08/10/2004
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
    if ($action == "AddRights") {
      // Inscription des droits dans l'annuaire   
      echo "<H3>Inscription des droits pour <U>$uid</U></H3>";
      echo "<P>Vous avez sélectionné ". count($newrights)." droit(s)\n";
      for ($loop=0; $loop < count($newrights); $loop++) {
        $right=$newrights[$loop];
        echo "<div style='margin-left: 40px;'>Délégation du droit <U>$right</U> à l'utilisateur $uid</div>\n";
        $cDn = "uid=$uid,$peopleRdn,$ldap_base_dn";
        $pDn = "cn=$right,$rightsRdn,$ldap_base_dn";
        exec ("$scriptsbinpath/groupAddEntry.pl \"$cDn\" \"$pDn\"");
        echo "<BR>\n";  
      }
    }
    if ( $action == "DelRights" ) {
      // Suppression des droits dans l'annuaire   
      echo "<H3>Suppression des droits pour <U>$uid</U></H3>";
      echo "<P>Vous avez sélectionné ". count($delrights)." droit(s)<BR>\n";
      for ($loop=0; $loop < count($delrights); $loop++) {
        $right=$delrights[$loop];
        echo "<div style='margin-left: 40px;'>Suppression du droit <U>$right</U> pour l'utilisateur $uid</div>\n";
        $cDn = "uid=$uid,$peopleRdn,$ldap_base_dn";
        $pDn = "cn=$right,$rightsRdn,$ldap_base_dn";
        exec ("$scriptsbinpath/groupDelEntry.pl \"$cDn\" \"$pDn\"");
        echo "<BR>\n";  
      }
    } 
    list($user, $groups)=people_get_variables($uid, true);
    // Affichage du nom et de la description de l'utilisateur
    echo "<H3>Délégation de droits à ". $user["fullname"] ." (<U>$uid</U>)</H3>\n";
    echo "<P>Sélectionnez les droits à supprimer (liste de gauche) ou à ajouter (liste de droite) ";
    echo "et validez à l'aide du bouton correspondant.</P>\n";
    // Lecture des droits disponibles
    $userDn="uid=$uid,$peopleRdn,$ldap_base_dn";     
    $list_possible_rights=search_machines("(!(member=$userDn))","rights");
    $list_current_rights=search_machines("(member=$userDn)","rights");
    ?>
<FORM method="post">
  <INPUT TYPE="hidden" VALUE="<? echo $uid;?>" NAME="uid">
  <INPUT TYPE="hidden" NAME="action">
  <TABLE BORDER=1 CELLPADDING=3 CELLSPACING=1 RULES=COLS style='margin-left: 40px;' ><TR>
  <TH>Droits actuels</TH>
  <TH>Droits disponibles</TH></TR>
  <TR><TD VALIGN="TOP">
<?  if   ( count($list_current_rights)>15) $size=15; else $size=count($list_current_rights);
    if ( $size>0) { 
      echo "<SELECT NAME=\"delrights[]\" SIZE=\"$size\" multiple=\"multiple\">";
      for ($loop=0; $loop < count($list_current_rights); $loop++) {
          echo "<option value=".$list_current_rights[$loop]["cn"].">".$list_current_rights[$loop]["cn"]."\n";
      }
?>
  </SELECT><BR><BR>
  <input type="submit" value="Retirer ces droits" onClick="this.form.action.value ='DelRights';return true;">
<?
    } else {
      echo "<U>$uid</U> n'a aucun droit";
    }
?>
  </TD><TD VALIGN="TOP">
<?  if   ( count($list_possible_rights)>15) $size=15; else $size=count($list_possible_rights);
    if ( $size>0) { 
      echo "<SELECT NAME=\"newrights[]\" SIZE=\"$size\" multiple=\"multiple\">";
      for ($loop=0; $loop < count($list_possible_rights); $loop++) {
          echo "<option value=".$list_possible_rights[$loop]["cn"].">".$list_possible_rights[$loop]["cn"]."\n";
      }
?>
  </SELECT><BR><BR>
  <input type="submit" value="Ajouter ces droits" onClick="this.form.action.value ='AddRights';return true;">
<?
    } else {
      echo "<U>$uid</U> a tous les droits";
    }
?>
  </TD></TR>
  </TABLE> 
</FORM>
<? 

  } else {
    echo "<div class=error_msg>Cette application, nécessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>