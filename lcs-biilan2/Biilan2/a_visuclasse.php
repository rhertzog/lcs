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

list ($idpers,$username) = isauth();
  if (is_admin("Biilan2_is_admin",$username)!="Y")
  {die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}
  
$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");  

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

print "<div class=\"texte\"><h1>Visualisation de l'avancement des classes</h1></div>";

$reqi=mysql_query("SELECT * FROM $items");  $resi=mysql_num_rows($reqi);
$seuil =seuil($pourcentage,$resi);    // on stocke le nombre d'eleve présents dans la classe dans la variable $total
$seuil=floor($seuil);

print "<br><br><br>";

$filter = "(cn=Classe_*$group)";
$groups=search_groups($filter);

for ($loop=0; $loop < count($groups); $loop++)
  {
  $dbclasse="".$groups[$loop]["cn"]."";															// on stocke la classe dans la variable $dbclasse

	print "<div class=\"affichok\">&nbsp;&nbsp;&nbsp;&nbsp;Résultat des élèves de la <b> $dbclasse</b><br><br></div>";

	$c="0";
	$total = "0";
	$totalok = "0";
	$users = search_uids ("(cn=$dbclasse)","half");
	for ( $loop1=0; $loop1<count($users); $loop1++ )
		{
			$tot1 = "1";
			$total = somme($total,$tot1);    // on stocke le nombre d'eleve présents dans la classe dans la variable $total
			$login = "".$users[$loop1]["uid"]."";
    		list($user, $groups1)=people_get_variables($login, true);
     		$nom="$user[fullname]";         // on stocke le nom de l'eleve dans la variable $dbeleve		
			$req = mysql_query("SELECT result from resultat_tbl where login LIKE '$login'") or die ("erreur req ".mysql_error());
	      $enreg= mysql_numrows($req);
      
    	  // on stocke le nombre d'eleve ayant au moins un item validé dans $c
      	
      	$c=somme($enreg,$c);

		 	while ( $resultat = mysql_fetch_array($req))
				{
					$req1 = mysql_query("SELECT result from resultat_tbl where (login LIKE '$login' and result >= $seuil)") or die ("erreur req ".mysql_error());					$test= mysql_numrows($req1);
			
					// on stocke le nombre d'eleve ayant un resultat au moins egal à 80% dans $totalok
					$totalok = somme($test,$totalok);			
				}
			}
			$intok = pourcentage($totalok,$total);
 			if ($intok == '0'){$finok = '1';}else{$finok = $intok;}
    
			// on stocke le nombre d'eleve ayant un resultat au moins egal à 80% dans $int
			$int=soustraction($c,$totalok); 
 
			$intko = pourcentage($int,$total);
		 	if ($intko == '0'){$finko = '1';}else{$finko = $intko;}

			// on stocke le nombre d'eleve pas encore inscrit dans $x
			$x=soustraction($total,$c); 
		
			if ($c=="1"){$verbe="est";}else{$verbe="sont";}
			print "<div class=\"texte\">&nbsp;&nbsp;&nbsp;&nbsp;Sur les <b>$total</b> élèves de cette classe:<br><br></div>";

			print "<div class=ret1>";
			print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" bordercolor=\"#000000\" width=\"500\" align=\"left\">";
			print "<tr>";
			print "<td background=\"Images/graph1.png\"  height=\"15\" width=\"$finok\"></td>";
			print "<td background=\"Images/graph3.png\"  height=\"15\" width=\"$finko\"></td>";
			print "<td background=\"Images/graph2.png\" height=\"15\"></td>";
			print "</tr>";
			print "</table>";

			print "<br><br><br>";


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
			print"<br><br><br>-----------------------------------------------------------------------------------------<br><br><br>";
			print "</div>";
	}

mysql_close(); ?>

</form>
</body>
</html>
