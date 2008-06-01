<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Activation/desactivation des plugins installes»
   AdminLCS/plugins_activation.php
   Equipe Tice académie de Caen
   maj : 06/12/2004
   Distribué selon les termes de la licence GPL
   ============================================= */
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des Plugins LCS</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour accéder à cette fonction")."</BODY></HTML>");

mysql_query("UPDATE applis SET value='$a' WHERE id='$pid';") or die("Erreur lors de l'activation/désactivation du plugin...");
header("location:plugins_installes.php");
?>
