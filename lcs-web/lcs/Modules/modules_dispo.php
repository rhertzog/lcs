<?
/* =============================================
   Projet LCS
   Administration serveur LCS «Liste des Modules disponibles»
   AdminLCS/Modules_dispo.php
   Equipe Tice academie de Caen
   10/12/2010
   Distribue selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des Modules LCS</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</body></html>");

include("modules_commun.php");
        echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
	echo "<HTML>\n";
	echo "	<head>\n";
        echo "		<meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"tetx/html; charset=utf-8\">\n";
	echo "		<title>...::: Interface d'administration Serveur LCS :::...</title>\n";
	echo "		<link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
	echo "		<link  href='boutons_style.css' rel='StyleSheet' type='text/css'>\n";
	echo "		<link rel='stylesheet' href='../Admin/style/stylesort.css' />\n";
	echo "		<script type='text/javascript' src='../Admin/js/script.js'></script>\n";
	echo "	</head>\n";
    	echo "	<body>\n";
	echo $msgIntro;

parsage_du_fichier_xml();

// recherche des Modules installes
$result = mysql_query("SELECT name,version FROM applis WHERE type='M' OR type='N' OR type='S'");
while($row = mysql_fetch_object($result))
	  $Modules_installes[$row->name] = $row->version;
mysql_free_result($result);

reset($Modules);
$mod_dispo=false;
echo "<H3>Modules disponibles</H3>\n";

// creation du tableau suivant les Modules dispos et deja installes
echo '<div id="wrapper">
	<table cellpadding="0" cellspacing="0"  class="sortable" id="sorter">';
echo '<th>Nom</th><Th>Description</Th><Th class="nosort">Version</Th><Th class="nosort">Aide</Th><Th class="nosort">Action</Th></TR>';

while(list($nomModule,$Module) = each($Modules))	
	{
	if (!@array_key_exists($nomModule,$Modules_installes))
	  	{
	  	$mod_dispo=true;
	  	  echo "<TR>\n";
	  	  echo "<TD >$nomModule</TD>\n";
	  	  echo "<TD >" . $Module["intitule"] . "</TD>\n";
	  	   while(list($version,$Mod) = each($Module["version"]))
			{
		  	  echo "<TD>$version</TD>\n";
		   	  echo "<TD class=\"centr\"><A HREF=\"" . $Mod["aide"] . "\" ALT=\"Aide\"><IMG SRC=\"../Plugins/Images/plugins_help.png\" TITLE=\"Aide\" BORDER=\"0\" WIDTH=\"29\" 
		   	  HEIGHT=\"28\" /></A></TD>\n";
		   	  echo "<TD class=\"centr\"><A HREF=\"modules_install.php?p=" . $Mod["serveur"] . "&v=" . $version . "&n=" . $nomModule . "&d=" . $Module["intitule"] . "\">
		   	  <IMG SRC=\"../Plugins/Images/plugins_install.png\" TITLE=\"Installer\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
		   	  }
	  	  echo "</TR>\n";
		}
	}
	
 if (!$mod_dispo) echo "<DIV class=\"alert_msg\">AUCUN dans la branche s&#233;lectionn&#233;e. V&#233;rifiez la valeur de <b>urlmajmod</b> dans les param&#232;tres serveur !</DIV>\n";
 echo "</TABLE>";
 echo '</div>
			<script type="text/javascript">
			var sorter=new table.sorter("sorter");
			sorter.init("sorter",0);
			</script>';
include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
