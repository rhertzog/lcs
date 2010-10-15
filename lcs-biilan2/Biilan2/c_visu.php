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

<div class="texte"><h1>Résultat des élèves enregistrés</h1></div><p><a href="javascript:window.print()"><img src="Images/pingimp.png" BORDER="0" ALIGN="right"></a><br></p>
<?php
require ("config.php");
require ("config2.php");

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

$date0=date("j");
$date1=date("Y");
$date2=date("m");
$date=("$date0 / $date2 / $date1");

function pourcentage($d,$e)
{$f = (($d *500)/$e);
return $f;}

$req0=mysql_query("SELECT * FROM $items");
$total=mysql_num_rows($req0);
$b="1";
$boucle = ($total + $b);

$site = "$etab.php";
if ($etab=="Lycee") {$typetab="Lycée";$type="L.";}
if ($etab=="Ecole") {$typetab="École";$type="E.";}
if ($etab=="College") {$typetab="Collège";$type="C.";}

$req1=mysql_query("SELECT * FROM resultat_tbl");
$total1=mysql_num_rows($req1);

print "<h2>&nbsp;&nbsp;&nbsp;&nbsp;B2i $typetab  - Position actuelle des $total1 élèves inscrits le $date</h2><br><br>";

for ($loop=1; $loop <$boucle ; $loop++)
	{
			$sql = "SELECT id FROM resultat_tbl WHERE result like '$loop'";
			$res = mysql_query($sql);
			$nb = mysql_numrows($res);
			$resultat=round($nb*100/$total1,1);
		    if ($nb)
		       {$affichage = $resultat*7;} 
		            // 7 = largeur du tableau - les 2 colonnes / 100 pour affichage correct du bandeau
		        else
		            {$affichage="1";}
			print "<div class=ret1>
		    <table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" width=\"850\" align=\"left\">
		    <tr>
		    <td align=\"center\" border=\"0\" height=\"10\" width=\"70\">$loop/$total</td>
		    <td background=\"Images/graph5.png\" height=\"10\" width=\"$affichage\"></td>
		    <td background=\"Images/graph4.png\" height=\"10\"></td>
		    <td align=\"right\" height=\"10\" width=\"80\">$resultat  % &nbsp;</td>
		    </tr>
		    </table>
			</div>
			<br>
	";
	}
mysql_close();
?>
<br clear="all"><br><br><br><br>
</body>
</html>
