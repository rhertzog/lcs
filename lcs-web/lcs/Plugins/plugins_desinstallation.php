<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Desinstallation d'un plugin»
   AdminLCS/plugins_desinstallation.php
   Equipe Tice académie de Caen
   maj : 18/05/2007
   Distribué selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des Plugins LCS</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour accéder à cette fonction")."</BODY></HTML>");

include("plugins_commun.php");
$result = mysql_query("SELECT * FROM applis WHERE id='$dpid'");
if (!$result) die("Erreur lors de la requète MySQL");
$row = mysql_fetch_object($result);
$version = $row->version;
$nomplugin = $row->name;
$description = $row->descr;
$repdes = $row->chemin;
mysql_free_result($result);
$flog = creation_log($nomplugin,$version,2); // il s'agit d'un log de suppression
$fecran = cree_nom_fichier_ecran($dpid);
creation_ecran($fecran,$msgIntro);
				 

ecrit_ecran($fecran, "<H3>Desinstallation du plugin : </H3>");
ecrit_ecran($fecran, "<DIR><DIR>" . $nomplugin . "</DIR></DIR>");
ecrit_ecran($fecran,"<DIR><DIR><DIR>Version : $version</DIR></DIR></DIR>");

if (!isset($etape))
	$etape = 1;

if ($etape == 1) // Desinscription du plugin de L.C.S.
        {
	  ecrit_ecran($fecran,"<H3>Suppression du plugin dans L.C.S.</H3>\n");
          ecrit_log($flog,">>> Suppression du plugin dans L.C.S. :");
          $cmd = "DELETE FROM applis WHERE id=$dpid";
          ecrit_ecran($fecran,"<B>Commande : </B>$cmd<BR>");
	  ecrit_log($flog,"Commande : $cmd");
          if(mysql_query($cmd))
          //if (true)
	         {
	           ecrit_log($flog,"Retour de l'exécution : OK");
	           ecrit_ecran($fecran,"Retour de l'exécution : OK");
	           $etape = 2;
	         }
	   else
	         {
	           ecrit_log($flog,"Retour de l'exécution : KO : " . mysql_error());
	           ecrit_ecran($fecran,"Retour de l'exécution : KO : " . mysql_error());
	         }
         }

if ($etape == 2) // Execution du script de désinstallation
        {
           ecrit_ecran($fecran,"<H3>Execution du script de désinstall du plugin : </H3>");
           ecrit_log($flog,">>> Execution du script de desinstallation du plugin :");
           $script = $chemin_de_desarchivage_des_plugins . "/" . $repdes . "/" . $chemin_administration_plugin . "/" . $fichier_script_desinstall_plugin;
	   if (file_exists($script))
		{
		   ecrit_ecran($fecran,"<DIR><DIR>Execution du script de desinstall</DIR></DIR>");
		   $lignes_retournees = array();
		   $cmd = "cd $chemin_de_desarchivage_des_plugins/$repdes/$chemin_administration_plugin; /usr/bin/sudo -H -u root " . $fichier_script_sudo . " " . $script;
		   ecrit_ecran($fecran,"<B>Commande : </B> $cmd<BR>");
		   ecrit_log($flog,"Commande : $cmd");
		   exec($cmd,$lignes_retournees,$ret_val);
		   ecrit_ecran($fecran, "<DIR><DIR><PRE>");
		   for($i = 0; $i < count($lignes_retournees); $i++)
			  ecrit_ecran($fecran, $lignes_retournees[$i] . "<BR>");
		   ecrit_ecran($fecran, "</PRE></DIR></DIR>");
		   $texte = implode("\r\n",$lignes_retournees);
		   ecrit_log($flog,$texte);
		   ecrit_log($flog,"Retour de l'exécution : $ret_val");
		   ecrit_ecran($fecran, "<B>Retour de l'execution : </B> " . ($ret_val == 0 ? "OK" : "KO"));
		 }
	    else 
	         ecrit_ecran($fecran,"<DIR><DIR>Aucun script de desinstall à exécuter...</DIR></DIR>");
	    if (!isset($ret_val) || $ret_val == 0)
	         $etape = 3;
	 }

if ($etape == 3) // Suppression d'entrée dans l'annuaire ldap
        {
          ecrit_ecran($fecran,"<H3>Suppression d'entrées dans l'annuaire LDAP : </H3>");
          ecrit_log($flog,">>> Suppression d'entrées dans l'annuaire LDAP :");
          $fichier_ldap = $chemin_de_desarchivage_des_plugins . "/" . $repdes . "/" . $chemin_administration_plugin . "/" . $fichier_desinstallation_ldap_plugin;
          if (file_exists($fichier_ldap))
                {
                   ecrit_ecran($fecran, "<DIR><DIR>Suppression des entrées ldap du plugin</DIR></DIR>");
                   $lignes_retournees = array();
                   $cmd = "ldapmodify -x -c -h $ldap_server -D $adminDn -w $adminPw -f $fichier_ldap";
                   $cmdaff= "ldapmodify -x -c -h $ldap_server -D $adminDn -w \"XXXXXX\" -f $fichier_ldap";
                   ecrit_ecran($fecran, "<B>Commande : </B><PRE>$cmdaff</PRE><BR>");
                   ecrit_log($flog,"Commande : $cmdaff");
                   exec($cmd,$lignes_retournees,$ret_val);
                   ecrit_ecran($fecran, "<DIR><DIR><PRE>");
                   for($i = 0; $i < count($lignes_retournees); $i++)
                        ecrit_ecran($fecran, $lignes_retournees[$i] . "<BR>");
                   ecrit_ecran($fecran,"</PRE></DIR></DIR>");
                   $texte = implode("\r\n",$lignes_retournees);
                   ecrit_log($flog,$texte);
                   ecrit_log($flog,"Retour de l'exécution : $ret_val");
                   ecrit_ecran($fecran,"<B>Retour de l'execution : </B> " . ($ret_val == 0 ? "OK" : "KO"));
                }
           else 
                ecrit_ecran($fecran,"<DIR><DIR>Aucune entrée ldap à supprimer...</DIR></DIR>");
           if (!isset($ret_val) || $ret_val == 0)
                $etape = 4;
        }

if ($etape == 4) // s'il y a une base mysql, desinstallation
        {
	  ecrit_ecran($fecran, "<H3>Desinstallation de la base MySQL du plugin : </H3>");
	  ecrit_log($flog,">>> Desinstallation de la base MySQL :");
	  $fichier_base = $chemin_de_desarchivage_des_plugins . "/" . $repdes . "/" . $chemin_administration_plugin . "/" . $fichier_desinstallation_mysql_plugin;
	  if (file_exists($fichier_base))
	       {
	         ecrit_ecran($fecran, "<DIR><DIR>Desinstallation de la base du plugin</DIR></DIR>");
	         $res = get_mysql_root_pwd(); // login est dans $res[0], password est dans $res[1]
	         $cmd = "mysql -u " . $res[0] . " --password=" . $res[1] . " < $fichier_base";
	         $cmdaff = "mysql -u " . $res[0] . " --password=\"xxxxxx\" < $fichier_base";
	         ecrit_ecran($fecran, "<B>Commande : </B><PRE>$cmdaff</PRE><BR>");
	         ecrit_log($flog,"Commande : $cmdaff");
	         $lignes_retournees = array();
	         exec($cmd,$lignes_retournees,$ret_val);
	         ecrit_ecran($fecran,"<DIR><DIR><PRE>");
	         for($i = 0; $i < count($lignes_retournees); $i++)
		         ecrit_ecran($fecran, $lignes_retournees[$i] . "<BR>");
		 ecrit_ecran($fecran,"</PRE></DIR></DIR>");
                 $texte = implode("\r\n",$lignes_retournees);
                 ecrit_log($flog,$texte);
                 ecrit_log($flog,"Retour de l'exécution : $ret_val");
                 ecrit_ecran($fecran,"<B>Retour de l'execution : </B> " . ($ret_val == 0 ? "OK" : "KO"));
              }
         else
             ecrit_ecran($fecran,"<DIR><DIR>Aucune base à desinstaller...</DIR></DIR>");
         if (!isset($ret_val) || $ret_val == 0)
                $etape = 5;
       }

if ($etape == 5) // on supprime le répertoire du plugin
        {
	  ecrit_ecran($fecran,"<H3>Suppression du répertoire du plugin : </H3>");
          ecrit_log($flog,">>> Suppression du répertoire du plugin :");
          $lignes_retournees = array();
          $cmd = "cd " . $chemin_de_desarchivage_des_plugins . "; rm -rvf " . $repdes;
          ecrit_ecran($fecran,"<B>Commande : </B><PRE>$cmd</PRE><BR>");
          ecrit_log($flog,"Commande : $cmd");
          exec($cmd,$lignes_retournees,$ret_val);
          ecrit_ecran($fecran,"<DIR><DIR><PRE>");
          for($i = 0; $i < count($lignes_retournees); $i++)
               ecrit_ecran($fecran, $lignes_retournees[$i] . "<BR>");
          ecrit_ecran($fecran,"</PRE></DIR></DIR>");
          $texte = implode("\r\n",$lignes_retournees);
          ecrit_log($flog,$texte);
          ecrit_log($flog,"Retour de l'éxécution : $ret_val");
          ecrit_ecran($fecran,"<B>Retour de l'execution : </B> " . ($ret_val == 0 ? "OK" : "KO"));
          if ($ret_val == 0)
               $etape = 6;
       } 
       
if ($etape == 6) // fin de la desinstallation
	{
		ecrit_ecran($fecran,"<H3 ALIGN=\"CENTER\">FIN de la Désinstallation</H3>");
	}
else
	{
	        ecrit_ecran($fecran,"<H3 ALIGN=\"CENTER\">>>> Erreur lors de la Désinstallation <<<</H3>");
        }
?>	
<BODY>
<SCRIPT LANGUAGE="JavaScript">
	<!--
		parent.ecran.location.href="plugins_refecran.php?stop=1&<?php echo $QUERY_STRING; ?>";
	//-->
</SCRIPT>
</BODY>
</HTML>

