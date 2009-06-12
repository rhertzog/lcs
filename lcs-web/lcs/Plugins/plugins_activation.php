<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Activation/desactivation des plugins installes»
   AdminLCS/plugins_activation.php
   Equipe Tice academie de Caen
   maj : 02/06/2009
   Distribue selon les termes de la licence GPL
   ============================================= */
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des Plugins LCS</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</BODY></HTML>");
$a=$_GET['a'];
$pid=$_GET['pid'];
mysql_query("UPDATE applis SET value='$a' WHERE id='$pid';") or die("Erreur lors de l'activation/d&#233;sactivation du plugin...");
header("location:plugins_installes.php");
?>
