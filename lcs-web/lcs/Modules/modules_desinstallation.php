<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Desinstallation d'un plugin»
   Modules_desinstallation.php
   Equipe Tice académie de Caen
   06/12/2007
   Distribué selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des Modules LCS</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour accéder à cette fonction")."</BODY></HTML>");


include("modules_commun.php");
$result = mysql_query("SELECT * FROM applis WHERE id='$dpid'");
if (!$result) die("Erreur lors de la requète MySQL");
$row = mysql_fetch_object($result);
$version = $row->version;
$nommodule= $row->name;
$description = $row->descr;
$repdes = $row->chemin;
mysql_free_result($result);
//on supprime éventuellement les résidus d'un processus interrompu  
$net=" rm /tmp/ecran_install*";
exec($net,$x,$y);
//création du fichier /tmp/ecran_install_nom_module.html
$nf="lcs-".$nommodule;
$fecran = cree_nom_fichier_ecran($nf);
creation_ecran($fecran,$msgIntro);
ecrit_ecran($fecran, "<H3>Desinstallation de </H3><B>" . $nommodule." ".$version. " le ". date('d/m/Y à H\hi')."</B>");
ecrit_ecran($fecran,"<H3>Execution du script</H3>");
//commande de désinstallation
$cmd= "/usr/bin/sudo -H -u root /usr/share/lcs/scripts/gestpack.sh 'deb http://lcs.crdp.ac-caen.fr/etch Lcs main' 'remove  --purge' lcs-".$nommodule;
//on place la commande dans le fichier jobat
$jobfile="/usr/share/lcs/Modules/jobat";
$job= fopen($jobfile,"w");
fputs($job,"$cmd \n");
fclose($job);
//la commande desinstall sera lancée en "at now" pour que ce script se termine avant le redémarrage d'apache 
$cmd_diff="/usr/bin/sudo -u root /usr/share/lcs/scripts/execution_script_plugin.sh /usr/share/lcs/Modules/job.sh";
exec($cmd_diff,$lignes_retournees,$ret_val);
?>	
</HTML>

