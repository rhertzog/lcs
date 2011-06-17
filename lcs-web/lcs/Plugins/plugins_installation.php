<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Installation d'un plugin»
   AdminLCS/plugins_installation.php
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

$p=$_GET['p'];
$v=$_GET['v'];
$n=$_GET['n'];
$d=$_GET['d'];
$s=$_GET['s'];
  
include("plugins_commun.php");
$version = $v;
$nomplugin = $n;
$description = $d;
$md5 = $s;
$pu = get_nom_de_fichier($p);
$flog = creation_log($pu,$version,1); // il s'agit d'un log d'installation
$fecran = cree_nom_fichier_ecran($pu);
creation_ecran($fecran,$msgIntro);
				 

ecrit_ecran($fecran, "<H3>Installation du plugin : </H3>");
ecrit_ecran($fecran, "<DIR><DIR>" . $p . "</DIR></DIR>");
ecrit_ecran($fecran,"<DIR><DIR><DIR>Version : $v</DIR></DIR></DIR>");

if (!isset($etape))
	$etape = 1;
	
if ($etape == 1) // on rapatrie le tgz
	{
		ecrit_ecran($fecran, "<H3>T&#233;l&#233;chargement du plugin : </H3>");
		ecrit_log($flog,">>>T&#233;l&#233;chargement du plugin :");
                $cmd = "/usr/bin/wget -T 5 -t 2 ". $p . " -O $chemin_des_uploads/$pu"; 
                ecrit_ecran($fecran,"<B>Commande : </B><PRE>$cmd</PRE><BR>");
                ecrit_log($flog,"Commande : $cmd");
		exec($cmd,$lignes_retournees,$ret_val);
		ecrit_ecran($fecran,"<DIR><DIR><PRE>");
		for($i = 0; $i < count($lignes_retournees); $i++)
			ecrit_ecran($fecran, $lignes_retournees[$i] . "<BR>");
		$repdes = mb_substr($lignes_retournees[0],0,mb_strlen($lignes_retournees[0])-1);
		ecrit_ecran($fecran,"</PRE></DIR></DIR>");
		$texte = implode("\r\n",$lignes_retournees);
		ecrit_log($flog,$texte);
		ecrit_log($flog,"Retour de l'&#233;x&#233;cution : $ret_val");
		ecrit_ecran($fecran,"<B>Retour de l'execution : </B> " . ($ret_val == 0 ? "OK" : "KO"));                
                if ($ret_val == 0)
			$etape = 2;      
	}
//sleep(10);	
if ($etape == 2) // on verifie la somme de controle
	{
		ecrit_ecran($fecran,"<H3>V&#233;rification de l'int&#233;grit&#233; de l'archive t&#233;l&#233;charg&#233;e : </H3>");
		ecrit_log($flog,">>> V&#233;rification de l'int&#233;grit&#233; de l'archive t&#233;l&#233;charg&#233;e :");
		$lignes_retournees = array();
		$cmd = "cd " . $chemin_des_uploads . ";/usr/bin/md5sum $pu";
		ecrit_log($flog,"Commande : $cmd");
		exec($cmd,$lignes_retournees,$ret_val);
		$texte = implode("\r\n",$lignes_retournees);
		ecrit_log($flog,$texte);
		ecrit_log($flog,"Retour de l'&#233;x&#233;cution : $ret_val");
		if ($ret_val == 0 && $s == $lignes_retournees[0] ) {
                        ecrit_ecran($fecran, "<DIR><DIR>L'archive $pu est conforme.</DIR></DIR>" );
                        ecrit_log($flog,">>> L'archive $pu est conforme. " );
			$etape = 3;
                } else {
 		 // Supprimer le fichier downloaD
                  ecrit_ecran($fecran,"<DIR><DIR><B> Suppression de l'archive : $pu car elle n'est pas conforme !</B></DIR></DIR> "); 
                  ecrit_log($flog,">>> Suppression de l'archive : $pu car elle n'est pas conforme ! ");
                  unlink ("$chemin_des_uploads/$pu");               
                }                                            
                
	}
//sleep(10);
if ($etape == 3) // on desarchive le tgz
	{
		ecrit_ecran($fecran,"<H3>D&#233;sarchivage du plugin : </H3>");
		ecrit_log($flog,">>>D&#233;sarchivage du plugin :");
		$lignes_retournees = array();
		$cmd = "cd " . $chemin_de_desarchivage_des_plugins . "; tar xvzf " . $chemin_des_uploads . "/" . $pu . "";
		ecrit_ecran($fecran,"<B>Commande : </B><PRE>$cmd</PRE><BR>");
		ecrit_log($flog,"Commande : $cmd");
		exec($cmd,$lignes_retournees,$ret_val);
		ecrit_ecran($fecran,"<DIR><DIR><PRE>");
		for($i = 0; $i < count($lignes_retournees); $i++)
			ecrit_ecran($fecran, $lignes_retournees[$i] . "<BR>");
		$repdes = mb_substr($lignes_retournees[0],0,mb_strlen($lignes_retournees[0])-1);
		ecrit_ecran($fecran,"</PRE></DIR></DIR>");
		$texte = implode("\r\n",$lignes_retournees);
		ecrit_log($flog,$texte);
		ecrit_log($flog,"Retour de l'&#233;x&#233;cution : $ret_val");
		ecrit_ecran($fecran,"<B>Retour de l'execution : </B> " . ($ret_val == 0 ? "OK" : "KO"));
		// Supprimer le fichier downloaD
                ecrit_log($flog,">>> Suppression de l'archive : $chemin_des_uploads/$pu");
                unlink ("$chemin_des_uploads/$pu");
		if ($ret_val == 0)
			$etape = 4;
	}
//sleep(10);
if ($etape == 4) // ex&#233;cution du script patchconf 
	{
		ecrit_ecran($fecran,"<H3>Execution du script patchconf du plugin : </H3>");
                ecrit_log($flog,">>> Execution du script patchconf du plugin :");
                
		$script = $chemin_de_desarchivage_des_plugins . "/" . $repdes . "/" . $chemin_administration_plugin . "/" . $fichier_script_patchconf_plugin;
	        if (file_exists($script))
	              {
	                 ecrit_ecran($fecran,"<DIR><DIR>Execution du script patchconf</DIR></DIR>");
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
	                 ecrit_log($flog,"Retour de l'ex&#233;cution : $ret_val");
	                 ecrit_ecran($fecran, "<B>Retour de l'execution : </B> " . ($ret_val == 0 ? "OK" : "KO"));
	              }
	         else
	              ecrit_ecran($fecran,"<DIR><DIR>Aucun script patchconf &#224; ex&#233;cuter...</DIR></DIR>");
                 if (!isset($ret_val) || $ret_val == 0)
                      $etape = 5;
	}
if ($etape == 5) // s'il y a une base mysql, installation
        {
                ecrit_ecran($fecran, "<H3>Installation de la base MySQL du plugin : </H3>");
		ecrit_log($flog,">>> Installation de la base MySQL :");
		$fichier_base = $chemin_de_desarchivage_des_plugins . "/" . $repdes . "/" . $chemin_administration_plugin . "/" . $fichier_installation_mysql_plugin;
		if (file_exists($fichier_base))
			{
			  ecrit_ecran($fecran, "<DIR><DIR>Installation de la base du plugin</DIR></DIR>");
			  $res = get_mysql_root_pwd(); // login est dans $res[0], password est dans $res[1]
			  $cmd = "mysql -f -u " . $res[0] . " --password=" . $res[1] . " < $fichier_base";
			  $cmdaff = "mysql -f -u " . $res[0] . " --password=\"xxxxxx\" < $fichier_base";
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
			  ecrit_log($flog,"Retour de l'ex&#233;cution : $ret_val");
			  ecrit_ecran($fecran,"<B>Retour de l'ex&#233;cution : </B> " . ($ret_val == 0 ? "OK" : "KO"));
			}
		else
			ecrit_ecran($fecran,"<DIR><DIR>Aucune base &#224; installer...</DIR></DIR>");
		if (!isset($ret_val) || $ret_val == 0)
			$etape = 6;
	}
//sleep(10);	
if ($etape == 6) // Execution du script d'install
	{
		ecrit_ecran($fecran,"<H3>Execution du script d'installation du plugin : </H3>");
		ecrit_log($flog,">>> Execution du script d'installation du plugin :");
		$script = $chemin_de_desarchivage_des_plugins . "/" . $repdes . "/" . $chemin_administration_plugin . "/" . $fichier_script_install_plugin;
		if (file_exists($script))
			{
			  ecrit_ecran($fecran,"<DIR><DIR>Execution du script d'install</DIR></DIR>");
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
			  ecrit_log($flog,"Retour de l'ex&#233;cution : $ret_val");
			  ecrit_ecran($fecran, "<B>Retour de l'execution : </B> " . ($ret_val == 0 ? "OK" : "KO"));
			}
		else 
		           ecrit_ecran($fecran,"<DIR><DIR>Aucun script d'install &#224; ex&#233;cuter...</DIR></DIR>");
		if (!isset($ret_val) || $ret_val == 0)
		        $etape = 7;
	}
//sleep(10);	
if ($etape == 7) // Insertion d'entrees dans l'annuaire ldap
	{
		ecrit_ecran($fecran,"<H3>Insertion d'entr&#233;es dans l'annuaire LDAP : </H3>");
		ecrit_log($flog,">>> Insertion d'entr&#233;es dans l'annuaire LDAP :");
		$fichier_ldap = $chemin_de_desarchivage_des_plugins . "/" . $repdes . "/" . $chemin_administration_plugin . "/" . $fichier_installation_ldap_plugin;
		if (file_exists($fichier_ldap))
			{
			  ecrit_ecran($fecran, "<DIR><DIR>Insertion des entr&#233;es ldap du plugin</DIR></DIR>");
			  $lignes_retournees = array();
			  $cmd = "ldapadd -x -c -h $ldap_server -D $adminDn -w $adminPw -f $fichier_ldap";
			  $cmdaff= "ldapadd -x -c -h $ldap_server -D $adminDn -w \"XXXXXX\" -f $fichier_ldap";
			  ecrit_ecran($fecran, "<B>Commande : </B><PRE>$cmdaff</PRE><BR>");
			  ecrit_log($flog,"Commande : $cmdaff");
		          exec($cmd,$lignes_retournees,$ret_val);
			  ecrit_ecran($fecran, "<DIR><DIR><PRE>");
			  for($i = 0; $i < count($lignes_retournees); $i++)
			       ecrit_ecran($fecran, $lignes_retournees[$i] . "<BR>");
			  ecrit_ecran($fecran,"</PRE></DIR></DIR>");
			  $texte = implode("\r\n",$lignes_retournees);
			  ecrit_log($flog,$texte);
			  ecrit_log($flog,"Retour de l'ex&#233;cution : $ret_val");
			  ecrit_ecran($fecran,"<B>Retour de l'execution : </B> " . ($ret_val == 0 ? "OK" : "KO"));
		    	}
		else 
		          ecrit_ecran($fecran,"<DIR><DIR>Aucune entr&#233;e ldap &#224; ins&#233;rer...</DIR></DIR>");
		if (!isset($ret_val) || $ret_val == 0)
		       $etape = 8;
	}        
        
        
//sleep(10);
if ($etape == 8)
	{
		ecrit_ecran($fecran,"<H3>Enregistrement du plugin dans L.C.S.</H3>\n");
		ecrit_log($flog,">>> Enregistrement du plugin dans L.C.S. :");
		// recherche du num&#233;ro de maj de la version installee
		$script = $chemin_de_desarchivage_des_plugins . "/" . $repdes . "/" . $chemin_maj_plugin . "/" . $fichier_script_maj_plugin;
		$vmaj = 1;
		while (file_exists($script . ".$vmaj"))
			{
				$vmaj++;
			}
		$vmaj--;
		$cmd = "INSERT INTO applis (name,value,descr,num_maj,version,chemin,type) VALUES ('$nomplugin','1','$description',$vmaj,'$version','$repdes','P');";
		ecrit_ecran($fecran,"<B>Commande : </B>$cmd<BR>");
		ecrit_log($flog,"Commande : $cmd");
		if(mysql_query($cmd))
			{
			  ecrit_log($flog,"Retour de l'ex&#233;cution : OK");
			  ecrit_ecran($fecran,"Retour de l'ex&#233;cution : OK");
			  $etape = 9;
			}
		else
			{
			   ecrit_log($flog,"Retour de l'ex&#233;cution : KO : " . mysql_error());
			   ecrit_ecran($fecran,"Retour de l'ex&#233;cution : KO" . mysql_error());
		        }
	}      	
if ($etape == 9) // fin de l'installation
	{
		ecrit_ecran($fecran,"<H3 ALIGN=\"CENTER\">FIN de l'Installation</H3>");
	}
else
	{
	        ecrit_ecran($fecran,"<H3 ALIGN=\"CENTER\">>>> Erreur lors de l'installation <<<</H3>");
        }
?>	
<BODY>
<SCRIPT LANGUAGE="JavaScript">
	<!--
		parent.ecran.location.href="plugins_refecran.php?stop=1&<?php echo $_SERVER['QUERY_STRING']; ?>";
	//-->
</SCRIPT>
</BODY>
</HTML>

