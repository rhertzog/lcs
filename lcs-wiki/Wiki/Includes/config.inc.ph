<?php
/* =============================================
   Projet LcSe3 :
   		Linux Communication Server
		Serveur Samba Edu 3
*/
# Parametre de DEBUG
$DEBUG = false;
# Param�tres MySQL
#-------------------------
$HOSTAUTH 	= "localhost";
$USERAUTH	= "wikiuser";
$PASSAUTH	= "wiphobio";
$DBAUTHWIKI	= "wikini_lcs";
# Chemin
$PATH2PLUG      = "/usr/share/lcs/Plugins/Wiki";

$NAME_PLUG	= "Wiki";
$VER_PLUG	= "0.3";

$authlinkwiki = @mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");
$resultwiki = @mysql_select_db($DBAUTHWIKI)or die("Impossible de se connecter � la base $DBAUTHWIKI.") ;

# Recuperation des parametres Cat�gorie 1 de l'appli depuis la table params de la bdd
# -----------------------------------------------------------------------
$result = @mysql_query("SELECT * from params WHERE cat='1' OR cat='4'");
if ($result)
while ($r = @mysql_fetch_array($result))
$$r["name"]=$r["value"];
else
die ("param�tres absents de la base de donn�e");
@mysql_free_result($result);
?>
