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

if ($etab=="Lycee") {$typetab="Lycée"; $i_etab="L";}
if ($etab=="Ecole") {$typetab="École"; $i_etab="E";}
if ($etab=="College") {$typetab="Collège"; $i_etab="C";}

function resultat($a,$b)
{$c = ($a + $b); return $c;}

$site = "$etab.php";
if ($etab=="Lycee") {$typetab="Lycée"; $i_etab="L";}
if ($etab=="Ecole") {$typetab="École"; $i_etab="E";}
if ($etab=="College") {$typetab="Collège"; $i_etab="C";}

print "<div class=\"texte\"><h1>Validation par lot</h1></div>";

list ($idpers,$uid)= isauth();
list($user, $groups)=people_get_variables($uid, true);
$dbfullname="$user[fullname]"; 	  		// on stocke le nom complet du prof dans la variable $dbfullname
$dbnom="$user[nom]";            	    		// on stocke le nom du prof dans la variable $dbnom
$dblogin="$user[uid]";                 		 // on stocke le login du prof dans la variable $dblogin
$date=date("d m Y");			    				// on stocke la date dans la variable $date

$from="$user[email]"; 	                    								 	// on stocke le mel du prof dans la variable $from
$expediteur = "$dbfullname<$from>"; 

// Recherche discipline du professeur

$discip ="-----";
$req0 = mysql_query("SELECT discipline from profs_tbl where fullprof LIKE '$dbfullname' ") or die ("erreur req ".mysql_error());
$res0 = mysql_numrows($req0);
while ( $resultat0 = mysql_fetch_array($req0))
{ $discip = "$resultat0[discipline]";}	 			// on stocke la discipline			

//debut boucle eleve
if(empty($_POST["loginelev"]))
	{die ("<div class=\"avertissement\"><br><br><br>Veuillez choisir un élève<br><br><br></div>");}
else
  {
  foreach ( $loginelev as $idelev ) 
		{

		list($user, $groups)=people_get_variables($idelev, true);
		$eleve="$user[nom]"; 
		$dbeleve="$user[fullname]";       // on stocke le nom de l'eleve dans la variable $dbeleve
		$to="$user[email]"; 	 						 // on stocke le mel de l'élève la variable $to	
		list($user, $groups)=people_get_variables($idelev, true);
 		 if ( count($groups) )
    		{
   		 for ($loop=0; $loop < count ($groups) ; $loop++)
      		{
      		if ( ereg("^Classe", $groups[$loop]["cn"]) )
        			{$dbclasse="".$groups[$loop]["cn"]."";}    // on stocke la classe de l'élève dans la variable $dbclasse
      		}
    		}
    		print"<div class=confirm><b><br>Traitement pour l'élève $dbeleve</b><br>";
			// debut traitement compétence    	
			$req1 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
	while ( $resultat1 = mysql_fetch_array($req1))
		{ 	//boucle 1
			$req2 = mysql_query("SELECT id,domaine,num,val FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
		while ( $resultat2 = mysql_fetch_array($req2))
			{ //boucle 2
				    $affich ="$i_etab.$resultat2[domaine].$resultat2[num]"; // on stocke la compétence
					$chekboxname = $resultat1[num].$resultat2[num];	
					//print"affich1=$affich<br>";
					//print"R1num=$resultat1[num] - R2num=$resultat2[num]<br>";							
					// on stocke la compétence	 			
					//recherche si cpt est validée
					$req20 = mysql_query("SELECT login,cpt FROM bii_tbl WHERE (login LIKE '$idelev' and cpt like '$affich')") or die ("erreur req ".mysql_err());
					$resultat20 = mysql_fetch_array($req20);
					$exist = mysql_numrows($req20);
					if (!$exist)
						{	// IF 1		
						if	($_POST[$chekboxname]=="1")
							{		//Traitement
							$cpt ="$i_etab.$resultat2[domaine].$resultat2[num]";	
							//print"affich2=$affich<br>";	
							//print"R2dom=$resultat2[domaine] - R2num=$resultat2[num]<br>";	
						mysql_query("INSERT INTO bii_tbl VALUES ('','$idelev','$eleve','$dbeleve','$cpt','$date','$dbfullname','$discip')") or die ("erreur requête ".mysql_error());					
							$req3 = ("SELECT login FROM resultat_tbl WHERE login LIKE '$idelev'") or die ("erreur sql ".mysql_error());
							$resultat3 = mysql_query($req3);
							$nbresultat3 = mysql_num_rows($resultat3);
							if ($nbresultat3==0)		// si l'eleve n'est pas ds la base on cree	
								{mysql_query("INSERT INTO resultat_tbl VALUES ('','$idelev','1')") or die ("erreur requête ".mysql_error());}
							else
								{
								$req4 = mysql_query("SELECT result FROM resultat_tbl WHERE login LIKE '$idelev'") or die ("erreur sql ".mysql_error());
								$resultat4 = mysql_fetch_array($req4);	
								$val = "1";
								$note = "$resultat4[result]";
								$total=resultat($note,$val);
								mysql_query("Update resultat_tbl Set result ='$total' where login ='$idelev'");
								}										
							// Affichage information						
							print "<div class=confirm><font color=red>Validation de la comptétence $affich </font></div>";									
							}		// fin traitement	
						} // fin IF 1
						else 
							{
							print"<div class=confirm>Compétence $affich déjà acquise</div>";
							}
				} // Fin boucle 2
		}//fin traitement competence	Fin boucle 1
		 		}//fin boucle eleve
		 		
		 		
	}


print "<br>";

mysql_close();
?>

<br>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form1" method="post" action="p_lot.php">
    <td width="200" height="17" align ="center"><input type="submit" name="button" value="&nbsp;&nbsp;&nbsp;Continuer&nbsp;&nbsp;&nbsp;"><br><br>
    </td>
  </tr></form>
</table>

 
</body>
</html>
