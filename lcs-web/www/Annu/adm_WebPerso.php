<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/adm_WebPerso.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   Equipe Tice academie de Caen
   V 1.4 maj : 30/05/2009
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  
  $uid=$_GET['uid'];
  $toggle=$_GET['toggle'];
  if (is_admin("Annu_is_admin",$login)=="Y") {
  if ($toggle== 0 ) {
    exec ("/usr/bin/sudo /usr/share/lcs/scripts/webperso.sh $uid 0");
  } else {
    exec ("/usr/bin/sudo /usr/share/lcs/scripts/webperso.sh $uid 1");    
  }
  header("Location:people.php?uid=$uid");
  }
  else {
  	header_html();
    echo "<div class=error_msg>Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>