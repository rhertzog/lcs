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

$date0=date("j");
$date1=date("Y");
$date2=date("m");
$date=("$date0 / $date2 / $date1");

function resultat($a,$b)
{$c = ($a - $b);
return $c;}

function pourcentage($d,$e)
{$f = (($d *500)/$e);
return $f;}
$site = "$etab.php";

if ($etab=="Lycee") {$typetab="Lycée";$type="L.";}
if ($etab=="Ecole") {$typetab="École";$type="E.";}
if ($etab=="College") {$typetab="Collège";$type="C.";}

$sql0 = "SELECT id FROM resultat_tbl";
$res0 = mysql_query($sql0);
$total = mysql_numrows($res0);

$req1 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
$res1= mysql_numrows($req1);
print "<h2>&nbsp;&nbsp;&nbsp;&nbsp;B2i $typetab  - Compétences validées le $date</h2>";
while ( $resultat1 = mysql_fetch_array($req1))
	{			//boucle domaine
	print "<div class=ret1><div class=affichok><b>Domaine $resultat1[num] : $resultat1[domaine].</b></div><br>";
	$req2 = mysql_query("SELECT id,domaine,num,val FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
	$res2= mysql_numrows($req2);

	while ( $resultat2 = mysql_fetch_array($req2))
		{		//boucle item
				// on stocke la compétence
		$affich ="$type$resultat1[num].$resultat2[num]";
		//recherche si cpt validée
		$sql = "SELECT id FROM bii_tbl WHERE cpt like '$affich'";
		$res = mysql_query($sql);
		$exist = mysql_numrows($res);
		if ($exist)
			{$affichage = pourcentage($exist,$total);}
		else
			{	$affichage="1";}
					//affichage final
		print "<div class=ret2><table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" bordercolor=\"#000000\" width=\"650\" align=\"left\">
		<tr>
		<td align=\"center\" border=\"0\" height=\"10\" width=\"100\">$affich</td>
		<td background=\"Images/graph5.png\" height=\"10\" width=\"$affichage\"></td>
		<td background=\"Images/graph4.png\" height=\"10\"></td>
		<td align=\"center\" height=\"10\" width=\"50\">$exist/$total</td>
		</tr>
		</table><br><br>
		</div>";
			
		}		//fin boucle item
	print "<br><br><br>";
	print "</div></div>";
	}	//fin boucle domaine
	print "<div class=remarque><a href=\"http://www.b2i-doc.cndp.fr/$site\">Rappel des compétences</a></div><br>";
	
mysql_close();
?>

<br><br><br><br>
</div>
</body>
</html>
