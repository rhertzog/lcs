<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/adm_WebPerso.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.4 maj : 29/03/2004
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  if ($toggle== 0 ) {
    exec ("/usr/bin/sudo /usr/share/lcs/scripts/webperso.sh $uid 0");
  } else {
    exec ("/usr/bin/sudo /usr/share/lcs/scripts/webperso.sh $uid 1");    
  }
  header("Location:people.php?uid=$uid");

?>