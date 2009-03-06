<?
/* =============================================
   Projet LCS-SE3
   Distribué selon les termes de la licence GPL
   ============================================= */
   
/* $Id: sauv.php 626 2005-10-25 07:59:18Z plouf $ */

require ("en-tete.php");

// Verifie les droits
if (is_admin("lcs_is_admin",$login)=="Y") {
$PTP="/usr/share/lcs/bcdiweb";
echo "<H1>Configuration de BCDIWEB</H1>\n";

if (isset($_GET["orgname"])) {
	system("cat $PTP/progweb/bcdiweb_Data.in | sed -e \"s/###ORGA###/".$_GET["orgname"]."/g\" | sed -e \"s/###GRIFFE###/".$_GET["griffe"]."/g\" | sed -e \"s/###MAIL###/".$_GET["mail"]."/g\" > $PTP/progweb/bcdiweb_Data.ini");
	system("cat $PTP/scripts/syncbcdi.sh.in | sed -e \"s/###IPBCDI###/".$_GET["ipbcdi"]."/g\" > $PTP/scripts/syncbcdi.sh");
	chmod ("$PTP/scripts/syncbcdi.sh",0755);
	echo "BcdiWeb configur&eacute;";
} else

{					       
/***************************************************************************************************/

echo "<form method=\"get\" action=\"config.php\">";
echo "<TABLE>\n";
echo "<TR><TD>Nom organisme</TD><TD><INPUT SIZE='50' LENGTH='50' NAME='orgname'></TD></TR>\n";
echo "<TR><TD>Code d'activation</TD><TD><INPUT SIZE='10' LENGTH='10' NAME='griffe'></TD></TR>\n";
echo "<TR><TD>Mail contact</TD><TD><INPUT SIZE='30' LENGTH='30' NAME='mail'></TD></TR>\n";
echo "<TR><TD>IP serveur BCDI</TD><TD><INPUT SIZE='15' LENGTH='15' NAME='ipbcdi'></TD></TR>\n";
echo "</TABLE>\n";
echo "<input type=\"submit\" value=\"Ok\">\n";
echo "</form>";
echo "<br><br>";
}

require ("pieds_de_page.php");
}
?>
