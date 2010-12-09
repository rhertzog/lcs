<?php
/* =============================================
   Projet LCS-SE3
   Distribue selon les termes de la licence GPL
   ============================================= */
   
/* $Id: sauv.php 626 2005-10-25 07:59:18Z plouf $ */

require ("en-tete.php");
$min=$_GET["$min"];
$hour=$_GET["$hour"];
// Verifie les droits
if (is_admin("lcs_is_admin",$login)=="Y") {
$PTP="/usr/share/lcs/Plugins/Bcdiweb";
echo "<H1>Actualisation des donn&eacute;es</H1>\n";

if (isset($_GET["rsync"])) {
	system("$PTP/scripts/syncbcdi.sh");
} else if (isset($_GET["cron"])) {
	system("echo \"$min $hour * * 0-5 www-data  /usr/share/lcs/Plugins/Bcdiweb/scripts/syncbcdi.sh\" > /etc/cron.d/bcdiweb");
	echo "L'heure de mise &#224; jour  a &#233;t&#233; enregistr&#233;e .";
} 
					       
/***************************************************************************************************/
$set_minute=exec("if [ -f /etc/cron.d/bcdiweb ]; then cut -f 1 -d \" \" /etc/cron.d/bcdiweb ; fi");
$set_hour=exec("if [ -f /etc/cron.d/bcdiweb ]; then cut -f 2 -d \" \" /etc/cron.d/bcdiweb; fi");

echo "<form method=\"get\" action=\"majbaz.php\">";
echo "<H2>Mise &#224; jour manuelle</H2>";
echo "Mise &#224; jour manuelle imm&eacute;diate de la base Data sur Lcs";
echo "<INPUT type='hidden' name='rsync' value='1'>";
echo "&nbsp;<input type=\"submit\" value=\"--> GO\">\n";
echo "</form>";
echo "<br>";

echo "<form method=\"get\" action=\"majbaz.php\">";
echo "<H2>Mise &#224; jour automatique</H2>";
echo "Heure de mise &#224; jour ";
echo "<INPUT type='hidden' name='cron' value='1'>";
echo "<select size='1' name='hour'>";
echo "<OPTION VALUE='*'>*</OPTION>";
for ($i=0;$i<24;$i++) {
 if (strcmp("$i","$set_hour") == 0) {
 echo "<OPTION VALUE=$i SELECTED>$i</OPTION>";
 } else
 {
 echo "<OPTION VALUE=$i>$i</OPTION>";
 }

}
echo "</select> h ";
echo "<select size='1' name='min'>";
for ($i=0;$i<60;$i+=5) {
 if (strcmp("$i","$set_minute") == 0) {
 echo "<OPTION VALUE=$i SELECTED>$i</OPTION>";
 } else
 {
 echo "<OPTION VALUE=$i>$i</OPTION>";
 }
}
echo "</select> mn ";
echo "&nbsp;<input type=\"submit\" value=\"Enregistrer\">\n";
echo "</form>";
echo "(R&#233;gler l'heure sur * pour faire mise &#224; jour toutes les heures)";
echo "<br><br>";

$filename = "synchro.log"; 
if (file_exists($filename)) {
	echo "<H2>Compte-rendu de la mise &#224; jour</H2>";
	$handle = fopen ($filename, "r"); 
	$contenu = fread ($handle, filesize ($filename));
	fclose ($handle);
	echo utf8_decode($contenu);
	}
echo "<br><br>";

require ("pieds_de_page.php");
}
?>
