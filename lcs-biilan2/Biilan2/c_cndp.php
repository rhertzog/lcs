<?php
/* ==========================================================
   Projet LCS : Linux Communication Server
   Plugin "Biilan2 : Gestion administrative du B2i"
   par Jean-Louis ROSSIGNOL <jean-louis.rossignol@ac-caen.fr>
   et Gilles HILAIRE <gilles.hilaire@ac-caen.fr>   
   ========================================================== */
?>

<html>
<head>
<title>Gestion Administrative du B2I</title>
<link rel="stylesheet" href="Style/style.css">
</head>
<body>

<div class="texte"><h1>Liste des compétences</h1><p><a href="javascript:window.print()"><img src="Images/pingimp.png" BORDER="0" ALIGN="right"></a><br></p>

<?php
require ("config.php");
require ("config2.php");

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

$site = "$etab.php";
if ($etab=="Lycee") {$typetab="Lycée"; $type="L";}
if ($etab=="Ecole") {$typetab="École"; $type="E";}
if ($etab=="College") {$typetab="Collège"; $type="C";}

print "<center>Pour plus de renseignements, le CNDP vous propose un <a href=\"http://www.b2i-doc.cndp.fr/$site\">document d'appui</a>.</center><br>";

$req1 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
$res1= mysql_numrows($req1);
print "<h2>&nbsp;&nbsp;&nbsp;&nbsp;B2i $typetab  - Liste des compétences</h2>";
while ( $resultat1 = mysql_fetch_array($req1))
	{print "<div class=affichok><b>Domaine $resultat1[num] : $resultat1[domaine].</b></div>";
	$req2 = mysql_query("SELECT id,domaine,num,val FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
	$res2= mysql_numrows($req2);
	print "<ul>";
	while ( $resultat2 = mysql_fetch_array($req2))
		{print "<div class=affichok><li><b>Compétence $type.$resultat1[num].$resultat2[num]</b> : $resultat2[val].</li></div>";}
	print "</ul><br>";
	}
mysql_close();
?>

<br><br><br><br>
</div>
</body>
</html>
