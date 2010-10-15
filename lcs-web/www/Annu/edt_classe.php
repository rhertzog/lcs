<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/edt_classe.php
   Affichage de l'emploi du temps des classes
   D'après une proposition de Pascal Pottier <pascal.pottier@etab.ac-caen.fr>
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.0 maj : 16/09/2002
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

  header_html();
  aff_trailer ("3");
  echo "<H4>Emploi du temps de la classe : $classe</H4>\n";
  echo "<img src=\"../edt_classe/C$classe.gif\" border=0>\n";
  include ("../lcs/includes/pieds_de_page.inc.php");
?>