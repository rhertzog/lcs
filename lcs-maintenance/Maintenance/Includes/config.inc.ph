<?php
/* =============================================
   Projet LcSe3 :
   		Linux Communication Server
		Serveur Samba Edu 3

   Application support utilisateurs
   et Maintenance Informatique

   config.inc.php

   [Maint_App CoreTeam]
   Lycée Pierre & Marie Curie Saint-Lô (Manche 50)
   jLCF >:>		jlcf@olsc.org
   Dom		        dom.lepaisant@libertysurf.fr
   RomRom		romuald.jourin@etab.ac-caen.fr
   V 0.2 maj : 18/09/2009
   ============================================= */
# Parametre de DEBUG
$DEBUG = false;
# Paramètres MySQL
#-------------------------
$HOSTAUTH 	= "localhost";
$USERAUTH	= "maint_user";
$PASSAUTH	= "#PASS#";
$DBAUTHMAINT	= "maint_plug";
# Chemin
$PATH2PLUG      = "/usr/share/lcs/Plugins/Maintenance";

$authlinkmaint = @mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");
$resultmaint = @mysql_select_db($DBAUTHMAINT)or die("Impossible de se connecter à la base $DBAUTHMAINT.") ;

# Recuperation des parametres Catégorie 1 de l'appli depuis la table params de la bdd
# -----------------------------------------------------------------------
$result = @mysql_query("SELECT * from params WHERE cat='1' OR cat='4'");
if ($result)
while ($r = @mysql_fetch_array($result))
$$r["name"]=$r["value"];
else
die ("paramètres absents de la base de donnée");
@mysql_free_result($result);
?>
