<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Desinstallation d'un module»
   Modules_desinstallation.php
   Equipe Tice academie de Caen
   02/06/2009
   Distribue selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des Modules LCS</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</BODY></HTML>");

$dpid=$_GET['dpid'];

include("modules_commun.php");
$result = mysql_query("SELECT * FROM applis WHERE id='$dpid'");
if (!$result) die("Erreur lors de la requ&#232;te MySQL");
$row = mysql_fetch_object($result);
$version = $row->version;
$nommodule= $row->name;
$description = $row->descr;
$repdes = $row->chemin;
mysql_free_result($result);
//on supprime eventuellement les residus d'un processus interrompu  
$net=" rm -f /tmp/ecran_install*";
exec($net,$x,$y);
//creation du fichier /tmp/ecran_install_nom_module.html
$nf="lcs-".$nommodule;
$fecran = cree_nom_fichier_ecran($nf);
creation_ecran($fecran,$msgIntro);
ecrit_ecran($fecran, "<H3>D&#233;sinstallation de </H3><B>" . $nommodule." ".$version. " le ". date('d/m/Y &#224; H\hi')."</B>");
ecrit_ecran($fecran,"<H3>Execution du script</H3>");
//commande de desinstallation
$cmd= "/usr/bin/sudo -H -u root /usr/share/lcs/scripts/gestpack.sh 'deb http://lcs.crdp.ac-caen.fr/etch Lcs main' 'remove  --purge' lcs-".$nommodule;
//on place la commande dans le fichier jobat
$jobfile="/usr/share/lcs/Modules/jobat";
$job= fopen($jobfile,"w");
fputs($job,"$cmd \n");
fclose($job);
//la commande desinstall sera lancee en "at now" pour que ce script se termine avant le redemarrage d'apache 
$cmd_diff="/usr/bin/sudo -u root /usr/share/lcs/scripts/execution_script_plugin.sh /usr/share/lcs/Modules/job.sh";
exec($cmd_diff,$lignes_retournees,$ret_val);
?>	
</HTML>

