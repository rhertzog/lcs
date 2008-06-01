<?
/* =============================================
   Projet LCS
   Administration serveur LCS «Installation d'un module»
   modules_installation.php
   Equipe Tice académie de Caen
   06/12/2007
   Distribué selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des modules LCS</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour accéder à cette fonction")."</BODY></HTML>");

include("modules_commun.php");
$version = $v;
$nommodule = $n;
$description = $d;
$md5 = $s;
$nf="lcs-".$n;
//on supprime éventuellement les résidus d'un processus interrompu  
$net=" rm /tmp/ecran_install*";
exec($net,$x,$y);
//création du fichier /tmp/ecran_install_nom_module.html
$fecran = cree_nom_fichier_ecran($nf);
creation_ecran($fecran,$msgIntro);
ecrit_ecran($fecran, "<H3>Installation de </H3> <B> " . $d ."  ". $v ." le ". date('d/m/Y à H\hi')." </B> " );
ecrit_ecran($fecran,"<H3>Exécution du script: </H3>");
//commande d'installation
$cmd= "/usr/bin/sudo -H -u root /usr/share/lcs/scripts/gestpack.sh '" .$p. "' install lcs-".$n;
//on place la commande dans le fichier jobat
$jobfile="/usr/share/lcs/Modules/jobat";
$job= fopen($jobfile,"w");
fputs($job,"$cmd \n");
fclose($job);
//lacommande d'install sera lancée en "at now" pour que ce script se termine avant le redémarrage d'apache 
$cmd_diff="/usr/bin/sudo -u root /usr/share/lcs/scripts/execution_script_plugin.sh /usr/share/lcs/Modules/job.sh";
exec($cmd_diff,$lignes_retournees,$ret_val);
//Fin
?>	
</HTML>

