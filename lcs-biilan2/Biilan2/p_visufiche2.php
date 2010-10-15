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

list ($idpers,$uid)= isauth();
list($user, $groups)=people_get_variables($uid, true);
$dbfullname="$user[fullname]"; 	  		// on stocke le nom complet du prof dans la variable $dbfullname
$dbnom="$user[nom]";            			   // on stocke le nom du prof dans la variable $dbnom
$dblogin="$user[uid]";            			  		// on stocke le login du prof dans la variable $dblogin
$date=date("d m Y");									// on stocke la date dans la variable $date
$uid="$_POST[login]";             				 // on stocke le login de l'élève dans la variable $uid

$site = "$etab.php";

if ($etab=="Lycee") {$typetab="Lycée"; $i_etab="L";}
if ($etab=="Ecole") {$typetab="École"; $i_etab="E";}
if ($etab=="College") {$typetab="Collège"; $i_etab="C";}


if(empty($_POST["login"]))
{die ("<div class=\"texte\"><h1>Fiche de position d'un élève</h1></div><br><br>
<div class=\"avertissement\"><br><br><br>Veuillez choisir un élève<br><br><br></div>");}
else
{


list($user, $groups)=people_get_variables($_POST[login], true);
$dbeleve="$user[fullname]";       // on stocke le nom de l'eleve dans la variable $dbeleve

list($user, $groups)=people_get_variables($_POST[login], true);
  if ( count($groups) )
    {
    for ($loop=0; $loop < count ($groups) ; $loop++)
      {
      if ( ereg("^Classe", $groups[$loop]["cn"]) )
        {$dbclasse="".$groups[$loop]["cn"]."";}    // on stocke la classe de l'élève dans la variable $dbclasse
      }
    }


// Affichage du bandeau Principal
print"<div class=\"texte\"><h1>Fiche de position de $dbeleve - $dbclasse</h1><p><a href=\"javascript:window.print()\"><img src=\"Images/pingimp.png\" BORDER=\"0\" ALIGN=\"right\"></a><br><br></p>";

Print "<br><br>";

// Affichage des compétences validées


print "<h2>&nbsp;&nbsp;&nbsp;&nbsp;Tableau récapitulatif du $date</h2>";

// Affichage des compétences

$req1 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
while ( $resultat1 = mysql_fetch_array($req1))
	{
		print"<h2><b>&nbsp;&nbsp;&nbsp;&nbsp;Domaine $resultat1[num] : $resultat1[domaine].</b></h2>";
		$req2 = mysql_query("SELECT id,domaine,num,val FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
		print "<ul>";
		while ( $resultat2 = mysql_fetch_array($req2))
			{
				print "<form method=\"post\" action=\"comment.php\" target=\"_blank\">";
				
				$affich ="$i_etab.$resultat1[num].$resultat2[num]"; // on stocke la compétence				
				
				$numdom ="$resultat1[num]";
				$numitem ="$resultat2[num]";
				print "<input type=\"hidden\" name=\"dom\" value=\"$numdom\">";
				print "<input type=\"hidden\" name=\"item\" value=\"$numitem\">";
				
				//recherche si cpt est validée
				$req20 = mysql_query("SELECT id,login,cpt,date,fullprof,discip FROM bii_tbl WHERE (login LIKE '$uid' and cpt like '$affich')")or die ("erreur req ".mysql_err());
				$resultat20 = mysql_fetch_array($req20);
				$exist = mysql_numrows($req20);
				if ($exist)
					{
						//Si trouvée on défini le message à afficher
						$resultat="Validée le $resultat20[date] par $resultat20[fullprof] en $resultat20[discip]"; 
						print "<div class=affichok><li><input type=\"submit\" value=\"Rappel\">&nbsp;&nbsp;<b>$affich : </b>$resultat</li></div>";						
					}
					else
						{
							//si pas trouvée on cherche dans la table des demandes
							$req200 = "SELECT id,login,cpt,date,fullprof FROM dmd_tbl WHERE (login LIKE '$uid' and cpt like '$affich')";
							$res200 = mysql_query($req200);
							$exist1 = mysql_numrows($res200);
							if ($exist1)
								{
									$resultat="Compétence en cours de validation";
									print "<div class=affichencours><li><b>$affich : </b>$resultat</li></div>";								}
								else
										//Compétence non validée								
									{
										$resultat="Compétence non validée";
										print "<div class=affichko><li><input type=\"submit\" value=\"Rappel\">&nbsp;&nbsp;<b>$affich : </b>$resultat</li></div>";
									}
						}
						print "</form>";
						
			}
		print "</ul>";
			
	}

//Affichage du résultat des compétence attestées

$req4 = mysql_query("SELECT id,result FROM resultat_tbl WHERE login LIKE '$uid'") or die ("erreur req ".mysql_error());
//$res4= mysql_numrows($req4);

$req5=mysql_query("SELECT * FROM $items");
$res5=mysql_num_rows($req5);

print "<h2>&nbsp;&nbsp;&nbsp;&nbsp;RESULTAT</h2>";

		$resultat4 = mysql_fetch_array($req4);
		$total=$resultat4[result];

// Validation du BII
$res6=$pourcentage*$res5; // Calcul du nombre d'items pour valider le BII
$res6=floor($res6); // on prend l'entier inférieur

//test d'un minimum de 50 % des items par Domaines

//On cherche le nombre de compétence par niveau 

	$req1 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
	$ok = 0; // test pour savoir si 50 % sur tous les domaines
	while ( $resultat1 = mysql_fetch_array($req1))
		{ 		
			$totalitem = 0;
			$req2 = mysql_query("SELECT id,domaine,num,val FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
			$d = 0; // on initialise le nombre de compétence pour le domaine à 0
			while ( $resultat2 = mysql_fetch_array($req2))
				{
				    $totalitem = $totalitem + 1;
				    $affich ="$i_etab.$resultat1[num].$resultat2[num]"; // on stocke la compétence
					//recherche si cpt est validée
					$req20 = mysql_query("SELECT login,cpt FROM bii_tbl WHERE (login LIKE '$uid' and cpt like '$affich')") or die ("erreur req ".mysql_err());
					$resultat20 = mysql_fetch_array($req20);
					$exist = mysql_numrows($req20);
					if ($exist)
						{
							$d = $d + 1; 					
						}					
					
							
				}
				if 	($d < $totalitem * 0.5)
						{$ok= 1;}
			}


// Affichage des logos en fonction du score
	if ($total) // si une valeur dans la table résultat_tbl
		{
			if ($total==1) // si 1 seule validation
    			{
           			$img="<img src=\"Images/iiko.gif\" ALIGN=\"middle\" BORDER=\"0\" HEIGHT=\"80\">";
					$texte="1 compétence attestée sur $res5";    
					$valid="BII NON VALIDE";        				
				}
				
				
				
				else if ($total>=$res6) // Le BII peut-être Validé
		     				{
		     					if ($ok == 0)
		     					{
		     						$img="<img src=\"Images/iiok.gif\" ALIGN=\"middle\" BORDER=\"0\" HEIGHT=\"80\">";
									$texte="$total compétences attestées sur $res5";    
									$valid="BII SUJET A VALIDATION"; 
		     					}   
		     					else
		     					{
		     						$img="<img src=\"Images/iiko.gif\" ALIGN=\"middle\" BORDER=\"0\" HEIGHT=\"80\">";
									$texte="$total compétences attestées sur $res5";    
									$valid="BII NON VALIDE (Moins de 50 % dans un des domaines)"; 
		     					}         				
		      				}
			       			else // Items insuffisants pour une validation
			          			{
			          				$img="<img src=\"Images/iiko.gif\" ALIGN=\"middle\" BORDER=\"0\" HEIGHT=\"80\">";
									$texte="$total compétences attestées sur $res5"; 
									$valid="BII NON VALIDE";  		
			          			}
		}	
          	else // Aucun item de validé
				{
	          		$img="<img src=\"Images/iiko.gif\" ALIGN=\"middle\" BORDER=\"0\" HEIGHT=\"80\">";
					$texte="ATTENTION, BII NON COMMENCE";
					//$valid="BII NON VALIDE";  
				}
		
// On affiche le logo et le commentaire associé
print"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tr>";
print"<td align=\"center\">$img</td></tr>";
print"<tr><td>&nbsp; </td></tr>";
print"<tr><td align=\"center\"><font color= \"#FF0000\">$texte</font></td></tr>";
print"<tr><td>&nbsp; </td></tr>";
print"<tr><td align=\"center\"><b><font color= \"#FF0000\">$valid</font></b></td></tr>";
print"</table>";
}
?>
<?php mysql_close(); ?>

<br><br><br>

</div>
</body>
</html>
