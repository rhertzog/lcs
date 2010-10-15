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
<title>Gestion Administrative du B2I au collège</title>
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<link rel="stylesheet" href="Style/style.css">
</head>
<body>

<?php
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

require ("config.php");
require ("config2.php");
$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

list ($idpers,$login) = isauth();
  $login=strtolower($login); 
  if ($idpers == "0") {die ("<div class=\"remarque\">Vous n'êtes pas authentifié sur le LCS ! <br><br></div>");}
  else {if (is_eleve($login)!="true"){die("<div class=\"remarque\">L'accès à cette page est réservée aux élèves <br><br></div>");}}

list ($idpers,$uid)= isauth();
list($user, $groups)=people_get_variables($uid, true);
$dbfullname="$user[fullname]"; 	  																		// on stocke le nom complet de l'eleve dans la variable $dbfullname
$dbnom="$user[nom]";            			   																// on stocke le nom de l'eleve dans la variable $dbnom
$dblogin="$user[uid]";            			  																		// on stocke le login de l'eleve dans la variable $dblogin
list($user, $groups)=people_get_variables($dblogin, true);				
if ( count($groups) )															// on stocke la classe de l'élève dans la variable $dbclasse
	{for ($loop=0; $loop < count ($groups) ; $loop++)
      { if ( ereg("^Classe", $groups[$loop]["cn"]) )
			{$dbclasse="".$groups[$loop]["cn"]."";}    					}	}	
	
$date=date("d m Y");									// on stocke la date dans la variable $date

function somme($a,$b)
{$c = ($a+$b);
return $c;}

function soustraction($a,$b)
{$c = ($a-$b);
return $c;}

function pourcentage($d,$e)
{$f = (($d *500)/$e);
return $f;}

function seuil($g,$h)
{$i = ($g*$h);
return $i;}


print "<div class=\"texte\"><h1>Visualisation de l'avancement d'une classe</h1></div>";

$reqi=mysql_query("SELECT * FROM $items");  $resi=mysql_num_rows($reqi);

$seuil =seuil($pourcentage,$resi);    // on stocke le nombre d'eleve présents dans la classe dans la variable $total


	print "<div class=\"remarque\">Résultat des élèves de la <b> $dbclasse</b><br>( le $date )<br><br><br><br></div>";
	$c="0";
	$total = "0";
	$totalok = "0";
	$users = search_uids ("(cn=$dbclasse)","half");
	for ( $loop=0; $loop<count($users); $loop++ )
		{
		$tot1 = "1";
		$total = somme($total,$tot1);    // on stocke le nombre d'eleve présents dans la classe dans la variable $total
		$login = "".$users[$loop]["uid"]."";
      list($user, $groups)=people_get_variables($login, true);
      $nom="$user[fullname]";         // on stocke le nom de l'eleve dans la variable $dbeleve		
		$req = mysql_query("SELECT result from resultat_tbl where login LIKE '$login'") or die ("erreur req ".mysql_error());
      $enreg= mysql_numrows($req);
      
      // on stocke le nombre d'eleve ayant au moins un item validé dans $c
      $c=somme($enreg,$c);


		 while ( $resultat = mysql_fetch_array($req))
			{
			print "<div class=\"texte\">&nbsp;&nbsp;&nbsp;&nbsp;- $nom : <b>$resultat[result]/$resi</b></div>";
			$req1 = mysql_query("SELECT result from resultat_tbl where (login LIKE '$login' and result >$seuil)") or die ("erreur req ".mysql_error());			$test= mysql_numrows($req1);
			
			// on stocke le nombre d'eleve ayant un resultat au moins egal à 80% dans $totalok
			$totalok = somme($test,$totalok);			
			}
		}
                                      //affichage si pas d'élèves enregistrés
	if ($c=="0")
		{die("<div class=\"avertissement\"><br><br>Il n'y a aucune compétence validée ce jour dans cette classe<br><br></div>");}
	else
		{
		
		$intok = pourcentage($totalok,$total);
 		if ($intok == '0'){$finok = '1';}else{$finok = $intok;}
    
		// on stocke le nombre d'eleve ayant un resultat au moins egal à 80% dans $int
		$int=soustraction($c,$totalok); 
 
		$intko = pourcentage($int,$total);
		 if ($intko == '0'){$finko = '1';}else{$finko = $intko;}

		// on stocke le nombre d'eleve pas encore inscrit dans $x
		$x=soustraction($total,$c); 


		if ($c=="1"){$verbe="est";}else{$verbe="sont";}
		print "<div class=\"texte\"><br><br>&nbsp;&nbsp;&nbsp;&nbsp;Sur les <b>$total</b> élèves de cette classe:<br><br></div>";
		}


?>
<div class=ret1>
<table cellspacing="0" cellpadding="0" border="1" bordercolor="#000000" width="500" align="left">
<tr>
<td background="Images/graph1.png"  height="15"width="<?php print "$finok"; ?>"></td>
<td background="Images/graph3.png"  height="15"width="<?php print "$finko"; ?>"></td>
<td background="Images/graph2.png" height="15"></td>
</tr>
</table>


<br><br><br>

<?php 
if($totalok=='0') 
	{$accord="&nbsp;&nbsp;- aucun n'est actuellement susceptible d'obtenir le B2I $etab,";}
else
	{if($totalok=='1'){$accord = "&nbsp;&nbsp;- $totalok est susceptible d'obtenir le B2I $etab,";} else {$accord = "&nbsp;&nbsp;- $totalok sont susceptibles d'obtenir le B2I $etab,";}}
print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" bordercolor=\"#000000\" width=\"15\" align=\"left\">";
print "<tr><td background=\"Images/graph1.png\" height=\"15\"></td></tr></table>";
print "$accord<br><br>";
if($int=='0')
	{$accord="&nbsp;&nbsp;- aucun n'est en cours de validation,";}
else
	{if($int=='1'){$accord = "&nbsp;&nbsp;- $int est en cours de validation,";} else {$accord = "&nbsp;&nbsp;- $int sont en cours de validation,";}}
print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" bordercolor=\"#000000\" width=\"15\" align=\"left\">";
print "<tr><td background=\"Images/graph3.png\" height=\"15\"></td></tr></table>";
print "$accord<br><br>";
if($x=='0') 
	{$accord="&nbsp;&nbsp;- aucun n'a aucune compétence validée.";}
else
	{if($x=='1'){$accord = "&nbsp;&nbsp;- $x n'a aucune compétence validée";} else {$accord = "&nbsp;&nbsp;- $x n'ont aucune compétence validée";}}
print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" bordercolor=\"#000000\" width=\"15\" align=\"left\">";
print "<tr><td background=\"Images/graph2.png\" height=\"15\"></td></tr></table>";
print "$accord";
print "</div>";

mysql_close(); ?>


</body>
</html>


