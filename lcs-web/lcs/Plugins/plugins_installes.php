<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Liste des plugins installes»
   AdminLCS/plugins_installes.php
   Equipe Tice académie de Caen
   maj : 22/04/2008
   Distribué selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des Plugins LCS</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour accéder à cette fonction")."</BODY></HTML>");

include("plugins_commun.php");

	echo "<HTML>\n";
	echo "	<HEAD>\n";
	echo "		<TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";
	echo "		<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
	echo "	</HEAD>\n";
    	echo "	<BODY>\n";
	echo $msgIntro;


parsage_du_fichier_xml(); // utilisée par la fonction maj_dispo afin de ne pas parser à chaque appel de la fonction
$query="SELECT * from applis where type='P'";
$result=mysql_query($query);
if ($result) 
	{
          if ( mysql_num_rows($result) !=0 ) {      
          // Affichage des plugins installés
          echo "<H3>Plugins installés </H3>\n";
          echo "<FONT SIZE=2>\n";
          echo "<TABLE BORDER=1 WIDTH=100%>";
          while ($r=mysql_fetch_object($result))
	  	{ list ($v,$plug) = maj_dispo($r->name);
		  echo "<TR>\n";
		  echo "<TD>" . $r->name . "</TD>\n";
		  echo "<TD>" . $r->descr . "</TD>\n";
		  echo "<TD>" . $r->version . "</TD>\n";
                  
 		  echo "<TD><A HREF=\"" . $plug["aide"] . "\" TITLE=\"Aide\"><IMG SRC=\"Images/plugins_help.png\" ALT=\"Aide\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\" /></A></TD>\n";
		  if ($r->value != "0")
		  	echo "<TD><A HREF=\"plugins_activation.php?pid=" . $r->id . "&a=0\"><IMG SRC=\"Images/plugins_desactiver.png\" TITLE=\"Désactiver\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
		  else
		  	echo "<TD><A HREF=\"plugins_activation.php?pid=" . $r->id . "&a=1\"><IMG SRC=\"Images/plugins_activer.png\" TITLE=\"Activer\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
		  #$v = maj_dispo($r->name);	
		  if ($v != false)
		  	echo "<TD><A HREF=\"plugins_maj.php?majp=" . $r->name . "&v=" . $v . "\"><IMG SRC=\"Images/plugins_maj.png\" TITLE=\"Mettre à jour\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
		  else
		  	echo "<TD>&nbsp;</TD>\n";
		  echo "<TD><A HREF=\"plugins_installes.php?dpid=" . $r->id . "&nomplug=".$r->name."\"><IMG SRC=\"Images/plugins_desinstall.png\" TITLE=\"Desinstaller\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
		  echo "</TR>\n";
		}
          echo "</TABLE>";
          } else {
              echo "<H3>Pas de plugin installé.</H3>\n";      
          }
        }
        
mysql_free_result($result);
if (isset($_GET['dpid']))
		{	echo "<script type='text/javascript'>";
		echo " if (confirm('Confirmez vous la suppression du plugin ".$_GET['nomplug']." ? ')){";	        
		echo ' location.href = "plugins_desinstall.php';
		echo '"+ "?dpid=" + "'.$_GET['dpid'].'" ;} else {';
		echo ' location.href = "';
		echo $_SERVER['PHP_SELF'];
		echo '"   ;} </script> ';}

include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
