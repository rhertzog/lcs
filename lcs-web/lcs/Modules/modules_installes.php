<?
/* =============================================
   Projet LCS
   Administration serveur LCS «Liste des Modules installes»
   AdminLCS/Modules_installes.php
   Equipe Tice academie de Caen
   maj : 02/06/2009
   Distribue selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des Modules LCS</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</BODY></HTML>");
  
include("modules_commun.php");

	echo "<HTML>\n";
	echo "	<HEAD>\n";
	echo "		<TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";
	echo "		<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
	echo "		<LINK  href='boutons_style.css' rel='StyleSheet' type='text/css'>\n";
	echo "	</HEAD>\n";
    	echo "	<BODY>\n";
	echo $msgIntro;


parsage_du_fichier_xml(); // utilisee par la fonction maj_dispo afin de ne pas parser &#224; chaque appel de la fonction
//modification du type de maj
if (isset($_GET['np']))
		{
//on place la commande dans un fichier
$act_file="/usr/share/lcs/Modules/action.sh";
$job= fopen($act_file,"w");
		if (($_GET['action'])=="desact") $cmd1='echo "'.$_GET['np'].' hold" | dpkg --set-selections';
		if (($_GET['action'])=="act") $cmd1='echo "'.$_GET['np'].' install" | dpkg --set-selections';
		fputs($job,"$cmd1 \n");
		fclose($job);
		$cmd="chmod +x /usr/share/lcs/Modules/action.sh";
		exec($cmd,$l,$r);
		$cmd="/usr/bin/sudo -u root /usr/share/lcs/scripts/execution_script_plugin.sh '/usr/share/lcs/Modules/action.sh'";
		exec($cmd,$l,$r);
		$cmd="rm -f action.sh";
		exec($cmd,$l,$r);	
		}
//recherche des paquets mis en hold
$pack_hold=array();
$cmd='dpkg --get-selections | grep hold | cut  -f1 | cut -d"-" -f2';
exec($cmd,$pack_hold,$ret_val);

$query="SELECT * from applis where type='M' OR type='N' OR type='S'";
$result=mysql_query($query);
if ($result) 
	{
          if ( mysql_num_rows($result) !=0 ) {      
          // Affichage des Modules installes
          echo "<H3>Modules install&#233;s </H3>\n";
          echo "<FONT SIZE=2>\n";
          echo "<TABLE BORDER=1 WIDTH=100%>";
          while ($r=mysql_fetch_object($result))
	  	{ list ($v,$plug) = maj_dispo($r->name);
		  echo "<TR>\n";
		  echo "<TD>" . $r->name . "</TD>\n";
		  echo "<TD>" . $r->descr . "</TD>\n";
		  echo "<TD>" . $r->version . "</TD>\n";
          echo "<TD><A HREF=\"../../doc/" . $r->name . "/html/index.html\" TITLE=\"Aide\"><IMG SRC=\"../Plugins/Images/plugins_help.png\" ALT=\"Aide\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\" /></A></TD>\n";
          $nom_paquet= "lcs-".mb_strtolower($r->name);
          if ($r->type =='N' && (!in_array ($r->name, $pack_hold)))
          echo '<TD class="buttons"><a href="modules_installes.php?np='.$nom_paquet.'&action=desact" class="positive" title="D&#233;sactiver la mise &#224; jour automatique">A&nbsp;</a></TD>';
          elseif  ($r->type =='N' && (in_array ($r->name, $pack_hold)))
          echo '<TD class="buttons"><a href="modules_installes.php?np='.$nom_paquet.'&action=act" class="negative"title="Activer la mise &#224; jour automatique">M</a></TD>';
          else
          echo "<TD>&nbsp;</TD>\n";
		  if ($v != false)
		  	echo "<TD><A HREF=\"modules_install.php?p=" . $plug["serveur"] . "&n=" .$r->name  . "\"><IMG SRC=\"../Plugins/Images/plugins_maj.png\" TITLE=\"Mettre &#224; jour\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
			else
		  	echo "<TD>&nbsp;</TD>\n";
		  echo "<TD><A HREF=\"modules_installes.php?dpid=" . $r->id . "&nommod=".$r->name."\"><IMG SRC=\"../Plugins/Images/plugins_desinstall.png\" TITLE=\"Desinstaller\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
		  echo "</TR>\n";
		}
          echo "</TABLE>";
          } else {
              echo "<H3>Pas de module install&#233;.</H3>\n";      
          }
        }
        
mysql_free_result($result);
if (isset($_GET['dpid']))
		{	echo "<script type='text/javascript'>";
		echo " if (confirm('Confirmez vous la suppression du module ".$_GET['nommod']." ? ')){";	        
		echo ' location.href = "modules_desinstall.php';
		echo '"+ "?dpid=" + "'.$_GET['dpid'].'" ;} else {';
		echo ' location.href = "';
		echo $_SERVER['PHP_SELF'];
		echo '"   ;} </script> ';}
include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
