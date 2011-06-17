<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Liste des plugins disponibles»
   AdminLCS/plugins_dispo.php
   Equipe Tice academie de Caen
   maj : 02/06/2009
   Distribue selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<h1>Gestion des Plugins LCS</h1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</body></html>");

$aff_der_ver=$_POST['aff_der_ver'];

include("plugins_commun.php");
        echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
	echo "<HTML>\n";
	echo "	<HEAD>\n";
        echo "    <META HTTP-EQUIV=\"Content-Type\" CONTENT=\"tetx/html; charset=utf-8\">\n";
	echo "	  <TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";
	echo "	  <LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
	echo "	</HEAD>\n";
    	echo "	<BODY>\n";
	echo $msgIntro;

parsage_du_fichier_xml();

// recherche des plugins installes
$result = mysql_query("SELECT name,version FROM applis WHERE type='P'");
while($row = mysql_fetch_object($result))
	  $plugins_installes[$row->name] = $row->version;
mysql_free_result($result);

reset($plugins);

if ( count($plugins) > count($plugins_installes ) ) {
  // affichage du choix de visualisation
  echo "<H3>Type d'affichage</H3>\n";
  echo "<FORM ACTION=\"".$_SERVER['PHP_SELF']."\" METHOD=\"POST\" NAME=\"form\">\n";
  echo "<TABLE WIDTH=\"100%\" BORDER=\"0\">\n";
  echo "<TR ALIGN=\"CENTER\">";
  echo "<TD><INPUT TYPE=\"RADIO\" NAME=\"aff_der_ver\" VALUE=\"0\" " . ($aff_der_ver == 0 ? "CHECKED" : "") . " onClick=\"form.submit()\">Toutes les versions</TD>\n";
  echo "<TD><INPUT TYPE=\"RADIO\" NAME=\"aff_der_ver\" VALUE=\"1\" " . ($aff_der_ver == 1 || !isset($aff_der_ver) ?  "CHECKED" : "")  ." onClick=\"form.submit()\">Seulement la derni&#232;re version</TD>\n";
  echo "</TR>\n";
  echo "</TABLE>\n";
  echo "</FORM>\n";
  // Affichage des plugins suivant le choix de visualiation
  echo "<H3>Plugins disponibles</H3>\n";
  // creation du tableau suivant les plugins dispos et deja installes
  echo "<FONT SIZE=2>\n";
  echo "<TABLE BORDER=1 WIDTH=100%>";

  if (!isset($aff_der_ver)) $aff_der_ver = 1;

  while(list($nomplugin,$plugin) = each($plugins))	
	{
	  if (!@array_key_exists($nomplugin,$plugins_installes))
	  	{
	  	  echo "<TR>\n";
	  	  if ($aff_der_ver == 0)
	  		  $nbversion = count($plugin["version"]);
	  	  else
	  		  $nbversion = 1;
	  	  echo "<TD ROWSPAN=$nbversion>$nomplugin</TD>\n";
	  	  echo "<TD ROWSPAN=$nbversion>" . $plugin["intitule"] . "</TD>\n";
	  	  $cptver = 0;
	   	  uksort($plugin["version"],"version_moins_recente");
          	  while(list($version,$plug) = each($plugin["version"]))
			{
		  	  if ($cptver > 0 && $aff_der_ver == 0)
				{
			  	  echo "</TR>\n";
			 	  echo "<TR>\n";
			  	  $cptver++;
				}
		  	  else
		  		  $cptver = 1;
		   	  echo "<TD>$version</TD>\n";
		   	  echo "<TD><A HREF=\"" . $plug["aide"] . "\" ALT=\"Aide\"><IMG SRC=\"Images/plugins_help.png\" TITLE=\"Aide\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\" /></A></TD>\n";
		   	  echo "<TD><A HREF=\"plugins_install.php?p=" . $plug["serveur"] . "&s=" . $plug["md5"] . "&v=" . $version . "&n=" . $nomplugin . "&d=" . $plugin["intitule"] . "\"><IMG SRC=\"Images/plugins_install.png\" TITLE=\"Installer\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
		   	  if ($aff_der_ver == 1) break;
	  		}
	  	  echo "</TR>\n";
		}
	} 
  echo "</TABLE>";
} else echo "<DIV class=\"alert_msg\">Pas de Plugins disponibles !</DIV>\n";
include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>