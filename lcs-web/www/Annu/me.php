<?
/* =============================================
   Projet LCS-SE3
   Consultation de l'annuaire LDAP
   Annu/me.php
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   « wawa »  olivier.lecluse@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 0.1.1 maj : 04/09/2002
   Distribué selon les termes de la licence GPL
   ============================================= */

include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
list ($idpers) = isauth();
if ($idpers) {
  // Recherche du login dans la table personne de la base lcs_db
  $query="SELECT login FROM personne WHERE id=$idpers";
  $result=mysql_query($query);
  if ($result && mysql_num_rows($result)) {
    $login=mysql_result($result,0,0);
    mysql_free_result($result);
    mysql_close($authlink);
  }
}
if ($login == "") header("Location:$urlauth");
else header("Location:people.php?uid=$login");
?>
