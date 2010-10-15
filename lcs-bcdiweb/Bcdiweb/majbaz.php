<?
/* =============================================
   Projet LCS-SE3
   Distribué selon les termes de la licence GPL
   ============================================= */
   
/* $Id: sauv.php 626 2005-10-25 07:59:18Z plouf $ */

require ("en-tete.php");

// Verifie les droits
if (is_admin("lcs_is_admin",$login)=="Y") {
$PTP="/usr/share/lcs/Plugins/Bcdiweb";
echo "<H1>Actualisation des donn&eacute;es</H1>\n";

if (isset($_GET["rsync"])) {
	system("$PTP/scripts/syncbcdi.sh");
} else if (isset($_GET["cron"])) {
	system("echo \"$min $hour * * 0-5 www-data  /usr/share/lcs/Plugins/Bcdiweb/scripts/syncbcdi.sh\" > /etc/cron.d/bcdiweb");
} else

{					       
/***************************************************************************************************/

echo "<form method=\"get\" action=\"majbaz.php\">";
echo "<H2>Mise a jour manuelle</H2>";
echo "Mise a jour manuelle imm&eacute;diate de la base Data sur Lcs";
echo "<INPUT type='hidden' name='rsync' value='1'>";
echo "&nbsp;<input type=\"submit\" value=\"--> GO\">\n";
echo "</form>";
echo "<br>";

echo "<form method=\"get\" action=\"majbaz.php\">";
echo "<H2>Mise a jour automatique</H2>";
echo "Heure de mise a jour quotidienne ";
echo "<INPUT type='hidden' name='cron' value='1'>";
echo "<select size='1' name='hour'>";
for ($i=0;$i<24;$i++) {
echo "<OPTION VALUE=$i>$i</OPTION>";
}
echo "</select> h ";
echo "<select size='1' name='min'>";
for ($i=0;$i<60;$i+=5) {
echo "<OPTION VALUE=$i>$i</OPTION>";
}
echo "</select> mn ";
echo "&nbsp;<input type=\"submit\" value=\"Enregistrer\">\n";
echo "</form>";
echo "<br><br>";
}

require ("pieds_de_page.php");
}
?>
