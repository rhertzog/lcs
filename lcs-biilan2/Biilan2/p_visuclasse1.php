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
if ($idpers == "0") {die ("<div class=\"remarque\">Vous n'êtes pas authentifié sur le LCS ! <br><br></div>");}
else {if (is_eleve($login)=="false"){die("<div class=\"remarque\">L'accès à BiiLan est ouvert, toutefois<br>n'&eacute;tant pas professeur, vos droits sont restreints<br><br></div>");}}



$date0=date("j");
$date1=date("Y");
$date2=date("m");
$date=("$date0 / $date2 / $date1");								// on stocke la date dans la variable $date

if ($etab=="Lycee") {$typetab="Lycée";$type="L";}
if ($etab=="Ecole") {$typetab="École";$type="E";}
if ($etab=="College") {$typetab="Collège";$type="C";}

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
print"<p><a href=\"javascript:window.print()\"><img src=\"Images/pingimp.png\" BORDER=\"0\" ALIGN=\"right\"></a><br><br></p>";

$reqi=mysql_query("SELECT * FROM $items");  
$resi=mysql_num_rows($reqi);

$seuil =seuil($pourcentage,$resi);    // on stocke le nombre d'eleve présents dans la classe dans la variable $total
$seuil=floor($seuil);

if(empty($_POST["classe"]))
	{die ("<div class=\"avertissement\"><br><br><br>Veuillez choisir une classe<br><br><br></div>");}
else
	{
	print "<div class=\"remarque\">Résultat des élèves de la <b> $_POST[classe]</b><br>( le $date )<br><br><br><br></div>";
	$c="0";
	$total = "0";
	$totalok = "0";
	$filter_classe="(cn=$_POST[classe])";
	$uids = search_uids ($filter_classe, "full");
	$people = search_people_groups ($uids,$filter_people,"group");
	for ($loop=0; $loop < count($people); $loop++) 
		{
		$login = "".$people[$loop]["uid"]."";					 // on stocke le login de l'eleve
		$nom = "".$people[$loop]["fullname"]."";			 // on stocke le nom complet de l'eleve
		$tot1 = "1";
		$total = somme($total,$tot1);    // on stocke le nombre d'eleve présents dans la classe dans la variable $total
		
		$req = mysql_query("SELECT result from resultat_tbl where login LIKE '$login'") or die ("erreur req ".mysql_error());
      $enreg= mysql_numrows($req);
      
      // on stocke le nombre d'eleve ayant au moins un item validé dans $c
      $c=somme($enreg,$c);


		 while ( $resultat = mysql_fetch_array($req))
			{
			//print "<div class=\"texte\">&nbsp;&nbsp;&nbsp;&nbsp;- $nom : <b>$resultat[result]/$resi</b></div>";
			$req1 = mysql_query("SELECT result from resultat_tbl where (login LIKE '$login' and result >= $seuil)") or die ("erreur req ".mysql_error());
			$test= mysql_numrows($req1);
			
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


print "<br><br><br>";

$ligne = 0 ;// on fixe le numéro de ligne à 0 ou déterminr les ligne paires et imaires

			//Affichage 1ere ligne tableau				
$req1 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
$res1= mysql_numrows($req1);
print "<div class=ret1><table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" bordercolor=\"#000000\" align=\"left\"><tr><td width=\"200\" height=\"10\" align=\"center\">&nbsp;Nom&nbsp;</td>";
while ( $resultat1 = mysql_fetch_array($req1))
	{			//boucle domaine
	$req2 = mysql_query("SELECT num FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
	$res2= mysql_numrows($req2);
	while ( $resultat2 = mysql_fetch_array($req2))
		{		//boucle item
		$cpt ="$type.$resultat1[num].$resultat2[num]";
		print "<td width=\"20\" align=\"center\" bgcolor=\"#F3E6FA\">$type<br>$resultat1[num]<br>$resultat2[num]</td>";
		}	
	}
print"</tr>";
							//Affichage des autres lignes
	
$filter_classe="(cn=$_POST[classe])";
$uids = search_uids ($filter_classe, "full");
$people = search_people_groups ($uids,$filter_people,"group");
for ($loop=0; $loop < count($people); $loop++) 
	{
	$login = "".$people[$loop]["uid"]."";			 	  		// on stocke le login de l'eleve
	$nom = "".$people[$loop]["fullname"]."";				 // on stocke le nom de l'eleve
	
									//recherche des logins de la classe
	$req1 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
	$res1= mysql_numrows($req1);
	
	// Calcul de la parité de la ligne pour affichage couleur une ligne / 2
			$ligne = $ligne +1;
			$parit = (round($ligne /2) - ($ligne /2));
			if($parit == "0") 
				{$couleur="#CC66CC";}
				else
					{$couleur="#FCC99CC";}	
	print"<tr><td width=\"200\" height=\"10\" bgcolor=$couleur>&nbsp;$nom&nbsp;</td>";
	
	while ( $resultat1 = mysql_fetch_array($req1))
		{			//boucle domaine
		$req2 = mysql_query("SELECT num FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
		$res2= mysql_numrows($req2);
		while ( $resultat2 = mysql_fetch_array($req2))
			{		//boucle item
			$cpt ="$type.$resultat1[num].$resultat2[num]";
					//test competence
			$req3 = mysql_query("SELECT * from bii_tbl WHERE (login LIKE '$login' and cpt LIKE '$cpt')") or die ("erreur req ".mysql_error());
			$resultat = mysql_num_rows($req3);
			if ($resultat==0){$affich = "";}
			else {$affich = "X";}	
			print "<td bgcolor=$couleur width=\"20\" align=\"center\">&nbsp;<b>$affich</b>&nbsp;</td>";
			}	
		}
		 $req4 = mysql_query("SELECT result from resultat_tbl where login LIKE '$login'") or die ("erreur req ".mysql_error());
                 $resultat4 = mysql_fetch_array($req4);
                 If ($resultat4[result]>0)
                        {print"<td bgcolor=$couleur>$resultat4[result]/$resi</td>";}
                        else
                        {print"<td bgcolor=$couleur>&nbsp;&nbsp;</td>";}
                print"</tr>";		
	  }
print"</table></div>";
print "<br clear=\"all\"><br><br><br>";

mysql_close(); 
?>


</body>
</html>