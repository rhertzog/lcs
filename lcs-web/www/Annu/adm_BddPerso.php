<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/adm_BddPerso.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 0.1.1 maj : 12/09/2002
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  exec ("$scriptsbinpath/mysqlDbToggle.pl $toggle $uid",$AllOutPut,$ReturnValue);
  header("Location:people.php?uid=$uid");

?>