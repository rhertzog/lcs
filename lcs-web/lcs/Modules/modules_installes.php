<?
/* =============================================
   Projet LCS
   Administration serveur LCS �Liste des Modules installes�
   AdminLCS/Modules_installes.php
   Equipe Tice acad�mie de Caen
   maj : 22/04/2008
   Distribu� selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des Modules LCS</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc�der � cette fonction")."</BODY></HTML>");
  
include("modules_commun.php");

	echo "<HTML>\n";
	echo "	<HEAD>\n";
	echo "		<TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";
	echo "		<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
	echo "	</HEAD>\n";
    	echo "	<BODY>\n";
	echo $msgIntro;


parsage_du_fichier_xml(); // utilis�e par la fonction maj_dispo afin de ne pas parser � chaque appel de la fonction

$query="SELECT * from applis where type='M' OR type='S'";
$result=mysql_query($query);
if ($result) 
	{
          if ( mysql_num_rows($result) !=0 ) {      
          // Affichage des Modules install�s
          echo "<H3>Modules install�s </H3>\n";
          echo "<FONT SIZE=2>\n";
          echo "<TABLE BORDER=1 WIDTH=100%>";
          while ($r=mysql_fetch_object($result))
	  	{ list ($v,$plug) = maj_dispo($r->name);
		  echo "<TR>\n";
		  echo "<TD>" . $r->name . "</TD>\n";
		  echo "<TD>" . $r->descr . "</TD>\n";
		  echo "<TD>" . $r->version . "</TD>\n";
          echo "<TD><A HREF=\"../../doc/" . $r->name . "/html/index.html\" TITLE=\"Aide\"><IMG SRC=\"../Plugins/Images/plugins_help.png\" ALT=\"Aide\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\" /></A></TD>\n";
		  if ($v != false)
		  	echo "<TD><A HREF=\"modules_install.php?p=" . $plug["serveur"] . "&n=" .$r->name  . "\"><IMG SRC=\"../Plugins/Images/plugins_maj.png\" TITLE=\"Mettre � jour\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
			else
		  	echo "<TD>&nbsp;</TD>\n";
		  echo "<TD><A HREF=\"modules_installes.php?dpid=" . $r->id . "&nommod=".$r->name."\"><IMG SRC=\"../Plugins/Images/plugins_desinstall.png\" TITLE=\"Desinstaller\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
		  echo "</TR>\n";
		}
          echo "</TABLE>";
          } else {
              echo "<H3>Pas de module install�.</H3>\n";      
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
