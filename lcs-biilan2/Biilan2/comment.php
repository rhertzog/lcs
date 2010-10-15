<?php
/* ==========================================================
   Projet LCS : Linux Communication Server
   Plugin "Biilan2: Gestion administrative du B2i"
   par Jean-Louis ROSSIGNOL <jean-louis.rossignol@ac-caen.fr>
   et Gilles HILAIRE <gilles.hilaire@ac-caen.fr>   
   ========================================================== */
?>


<html>
<head>
<title>Gestion Administrative du B2I</title>
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<link rel="stylesheet" href="Style/style.css">
</head>
<body>
<div class="texte">
<?php 
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

require ("config.php");
require ("config2.php");

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");  

if ($etab=="Lycee") {$typetab="L"; $tblitem="items_lyc_tbl";}
if ($etab=="Ecole") {$typetab="E"; $tblitem="items_eco_tbl";}
if ($etab=="College") {$typetab="C"; $tblitem="items_clg_tbl";}

$site = "$etab.php";	
		
print"<h1>BiiLan2 - $etab</h1>";

$req1 = mysql_query("SELECT domaine FROM domaines_tbl  WHERE num LIKE $_POST[dom]") or die ("erreur req ".mysql_error());
while ( $resultat1 = mysql_fetch_array($req1))
	{
	print"<h2><b>&nbsp;&nbsp;&nbsp;&nbsp;Domaine $_POST[dom] : $resultat1[domaine].</b></h2><br>";
	}
	$req2 = mysql_query("SELECT val FROM $tblitem WHERE num LIKE $_POST[item] AND domaine LIKE $_POST[dom]") or die ("erreur req ".mysql_error());
while ( $resultat2 = mysql_fetch_array($req2))
	{
	print"<b>&nbsp;&nbsp;&nbsp;&nbsp; $typetab.$_POST[dom].$_POST[item]</b> : $resultat2[val].";
	}

////////////////////////////////////

 print "<br><br><br><br><center><a href=\"javascript:window.close()\"><b>Fermer cette Fenêtre</b></a></center><br><br>";
 
 
 ////////////////////////////////////
 
 print "<h1>Liste des compétences</h1>";print "<center>Pour plus de renseignements, le CNDP vous propose un <a href=\"http://www.b2i-doc.cndp.fr/$site\">document d'appui</a>.</center><br>";

$req3 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
$res3= mysql_numrows($req3);
print "<h2>&nbsp;&nbsp;&nbsp;&nbsp;B2i $typetab  - Liste des compétences</h2>";
while ( $resultat3 = mysql_fetch_array($req3))
	{print "<div class=affichok><b>Domaine $resultat3[num] : $resultat3[domaine].</b></div>";
	$req4 = mysql_query("SELECT id,domaine,num,val FROM $items WHERE domaine LIKE $resultat3[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
	$res4= mysql_numrows($req4);
	print "<ul>";
	while ( $resultat4 = mysql_fetch_array($req4))
		{print "<div class=affichok><li><b>Compétence $typetab.$resultat3[num].$resultat4[num]</b> : $resultat4[val].</li></div>";}
	print "</ul><br>";
	}

mysql_close();
?>
</div>
</body>
</html>
