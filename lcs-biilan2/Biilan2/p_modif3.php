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



<div class="texte"><h1>Modification d'une fiche de position</h1><br></div>


<?php
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

require ("config.php");
require ("config2.php");

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

if ($etab=="Lycee") {$typetab="Lycée"; $i_etab="L";}
if ($etab=="Ecole") {$typetab="École"; $i_etab="E";}
if ($etab=="College") {$typetab="Collège"; $i_etab="C";}

list ($idpers,$login) = isauth();
 if ($idpers == "0") {die ("<div class=\"remarque\">Vous n'êtes pas authentifié sur le LCS ! <br><br></div>");}
 else {if (is_eleve($login)=="false"){die("<div class=\"remarque\">L'accès à BiiLan est ouvert, toutefois<br>n'&eacute;tant pas professeur, vos droits sont restreints<br><br></div>");}}


function resultat($a,$b)
{$c = ($a + $b); return $c;}

list ($idpers,$uid)= isauth();
list($user, $groups)=people_get_variables($uid, true);
$dbfullname="$user[fullname]"; 	  		// on stocke le nom complet du prof dans la variable $dbfullname
$dbnom="$user[nom]";            	    // on stocke le nom du prof dans la variable $dbnom
$dblogin="$user[uid]";                  // on stocke le login du prof dans la variable $dblogin
$date=date("d m Y");			    	// on stocke la date dans la variable $date

$uid="$_POST[login]";                  	// on stocke le login de l'élève dans la variable $uid

$from="$user[email]"; 	                    								 	// on stocke le mel du prof dans la variable $from
$expediteur = "$dbfullname<$from>"; 

// Recherche discipline du professeur
$discip ="-----";
$req0 = mysql_query("SELECT discipline from profs_tbl where fullprof LIKE '$dbfullname' ") or die ("erreur req ".mysql_error());
$res0 = mysql_numrows($req0);
while ( $resultat0 = mysql_fetch_array($req0))
{ $discip = "$resultat0[discipline]";}	 			// on stocke la discipline			




$site = "$etab.php";
if ($etab=="Lycee") {$typetab="Lycée"; $i_etab="L";}
if ($etab=="Ecole") {$typetab="École"; $i_etab="E";}
if ($etab=="College") {$typetab="Collège"; $i_etab="C";}


list($user, $groups)=people_get_variables($_POST[login], true);
$eleve="$user[nom]"; 
$dbeleve="$user[fullname]";       // on stocke le nom de l'eleve dans la variable $dbeleve
$to="$user[email]"; 	  // on stocke le mel de l'élève la variable $to	


list($user, $groups)=people_get_variables($_POST[login], true);
  if ( count($groups) )
    {
    for ($loop=0; $loop < count ($groups) ; $loop++)
      {
      if ( ereg("^Classe", $groups[$loop]["cn"]) )
        {$dbclasse="".$groups[$loop]["cn"]."";}    // on stocke la classe de l'élève dans la variable $dbclasse
      }
    }

print "<br><br><br><br>";

		  //cas suppression de competences
if ($_POST[action]=="ko")
	{
	$req1 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
	while ( $resultat1 = mysql_fetch_array($req1))
		{ 	
			print"<h2><b>&nbsp;&nbsp;&nbsp;&nbsp;Domaine $resultat1[num] : $resultat1[domaine].</b></h2>";	
			$req2 = mysql_query("SELECT id,domaine,num,val FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
		while ( $resultat2 = mysql_fetch_array($req2))
			{
				    $affich ="$i_etab.$resultat1[num].$resultat2[num]"; // on stocke la compétence
					$chekboxname = $resultat1[num].$resultat2[num];		
					// on stocke la compétence	 			
					//recherche si cpt est validée
					$req20 = mysql_query("SELECT login,cpt FROM bii_tbl WHERE (login LIKE '$uid' and cpt like '$affich')") or die ("erreur req ".mysql_err());
					$resultat20 = mysql_fetch_array($req20);
					$exist = mysql_numrows($req20);
					if ($exist)
						{
					
						if	($_POST[$chekboxname]=="1")
							{	//Traitement
							$cpt ="$i_etab.$resultat2[domaine].$resultat2[num]";
							mysql_query("Delete from bii_tbl where cpt = '$cpt' and login = '$uid'");
							$req4 = mysql_query("SELECT result FROM resultat_tbl WHERE login LIKE '$uid'") or die ("erreur sql ".mysql_error());
							$resultat4 = mysql_fetch_array($req4);	
							$val = "-1";
							$note = "$resultat4[result]";
							$total=resultat($note,$val);
							mysql_query("Update resultat_tbl Set result ='$total' where login ='$uid'");
							
							// envoi du mel
							$expediteur   = "$dbfullname<$from>"; 
							$sujet="Suppression  d'une compétence B2I"; 
							$message = "Le $date, $dbfullname vous a supprimé la compétence $cpt du B2I $etab.";
							mail($to,$sujet,$message,"Reply-to: $expediteur\nFrom: $expediteur\n");
							
							print "<div class=confirm>Compétence <b>$cpt</b> supprimée<br></div>";
							}					
						}
				
				}
		}
	}


		//cas validation de competences
else
	{
	$req1 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
	while ( $resultat1 = mysql_fetch_array($req1))
		{ 	
			print"<h2><b>&nbsp;&nbsp;&nbsp;&nbsp;Domaine $resultat1[num] : $resultat1[domaine].</b></h2>";	
			$req2 = mysql_query("SELECT id,domaine,num,val FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
		while ( $resultat2 = mysql_fetch_array($req2))
			{
				    $affich ="$i_etab.$resultat1[num].$resultat2[num]"; // on stocke la compétence
					$chekboxname = $resultat1[num].$resultat2[num];						
					// on stocke la compétence	 			
					//recherche si cpt est validée
					$req20 = mysql_query("SELECT login,cpt FROM bii_tbl WHERE (login LIKE '$uid' and cpt like '$affich')") or die ("erreur req ".mysql_err());
					$resultat20 = mysql_fetch_array($req20);
					$exist = mysql_numrows($req20);
					if (!$exist)
						{
									
						if	($_POST[$chekboxname]=="1")
							{		//Traitement
							$cpt ="$i_etab.$resultat2[domaine].$resultat2[num]";
							mysql_query("INSERT INTO bii_tbl VALUES ('','$uid','$uid','$dbeleve','$cpt','$date','$dbfullname','$discip')") or die ("erreur requête ".mysql_error());
							
							$req3 = ("SELECT login FROM resultat_tbl WHERE login LIKE '$uid'") or die ("erreur sql ".mysql_error());
							$resultat3 = mysql_query($req3);
							$nbresultat3 = mysql_num_rows($resultat3);
							if ($nbresultat3==0)		// si l'eleve n'est pas ds la base on cree
								
								{mysql_query("INSERT INTO resultat_tbl VALUES ('','$uid','1')") or die ("erreur requête ".mysql_error());}
							else
								{$req4 = mysql_query("SELECT result FROM resultat_tbl WHERE login LIKE '$uid'") or die ("erreur sql ".mysql_error());
								$resultat4 = mysql_fetch_array($req4);	
								$val = "1";
								$note = "$resultat4[result]";
								$total=resultat($note,$val);
								mysql_query("Update resultat_tbl Set result ='$total' where login ='$uid'");
								}					
							// envoi du mel
							$expediteur   = "$dbfullname<$from>"; 
							$sujet="Validation  d'une compétence B2I"; 
							$message = "Le $date, $dbfullname vous a validé la compétence $cpt du B2I $etab.";
							mail($to,$sujet,$message,"Reply-to: $expediteur\nFrom: $expediteur\n");	
							
							print "<div class=confirm>Compétence <b>$cpt</b> validée<br></div>";									
							}					
						}
				}
		}
	}

mysql_close();
?>

<br><br><br><br><br><br>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form1" method="post" action="p_modif.php">
    <td width="200" height="17" align ="center"><input type="submit" name="button" value="&nbsp;&nbsp;&nbsp;Continuer&nbsp;&nbsp;&nbsp;"><br><br>
    </td>
  </tr></form>
</table>



 
</body>
</html>
