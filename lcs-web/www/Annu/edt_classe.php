<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

  $login=$_SESSION['login'];
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  header_html();
  aff_trailer ("3");
  echo "<H4>Emploi du temps de la classe : $classe</H4>\n";
  echo "<img src=\"../edt_classe/C$classe.gif\" border=0>\n";
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
