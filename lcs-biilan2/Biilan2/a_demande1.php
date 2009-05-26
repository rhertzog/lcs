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
<title>Gestion Administrative du B2I au coll�ge</title>
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

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'acc�s � Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}

list ($idpers,$uid)=isauth();
list($user, $groups)=people_get_variables($uid, true);
$dbfullname="$user[fullname]"; 	  									// on stocke le nom complet de l'admin dans la variable $dbfullname
$dbnom="$user[nom]";            											// on stocke le nom de l'admin dans la variable $dbnom
$dblogin="$user[uid]";            												// on stocke le login de l'admin dans la variable $dblogin
$from="$user[email]"; 	                    								 								// on stocke le mel de l'admin dans la variable $from


$date=date("d m Y");																// on stocke la date dans la variable $date

print "<div class=\"texte\"><h1>Traitement des demandes de validation en attente</h1></div>";

function resultat($a,$b)
{$c = ($a + $b); return $c;}

if (empty($_POST["val"]))
	{die ("<div class=avertissement><br><br><br>Veuillez s�lectionner la demande � traiter.<br><br><br></div>");}
else
	{
	foreach ( $val as $id ) 
		$res= mysql_numrows($req);
		while ( $resultat1 = mysql_fetch_array($req))
			{$cpt = "$resultat1[cpt]";      							// on stocke la comp�tence demand�e
			$classe = "$resultat1[classe]";      					// on stocke la classe 
			$elogin="$resultat1[login]";    							// on stocke le login de l'�l�ve
			$fulleleve="$resultat1[fulleleve]";       			// on stocke le nom complet
			$nomeleve="$resultat1[nomeleve]";       	// on stocke le nom 
			$iddmd="$resultat1[id]"; 
      
			$to="$user[email]"; 	  // on stocke le mel de l'�l�ve la variable $to	
		

																// Cas validation de la comp�tence 
																
			$req2 = mysql_query("SELECT discipline from profs_tbl where fullprof LIKE '$dbfullname' ") or die ("erreur req ".mysql_error());
			$res2= mysql_numrows($req2);
			while ( $resultat2 = mysql_fetch_array($req2))
				{ $discip = "$resultat2[discipline]";}	 			// on stocke la discipline											

			if ($_POST["action"]=="ok")                   

				// test si eleve d�j� dans base
			
				$req3 = ("SELECT login FROM resultat_tbl WHERE login LIKE '$elogin'") or die ("erreur sql ".mysql_error());
				$resultat3 = mysql_query($req3);
				$nbresultat3 = mysql_num_rows($resultat3);
				
				if ($nbresultat3==0)	// si l'eleve n'est pas ds la base on cree
  					{mysql_query("INSERT INTO resultat_tbl VALUES ('','$elogin','1')") or die ("erreur requ�te ".mysql_error());}
				
				else					// sinon on met � jour le resultat
				
					{$req4 = mysql_query("SELECT result FROM resultat_tbl WHERE login LIKE '$elogin'") or die ("erreur sql ".mysql_error());
					$resultat4 = mysql_fetch_array($req4);	
					$val = "1";
					$note = "$resultat4[result]";
					$total=resultat($note,$val);
					mysql_query("Update resultat_tbl Set result ='$total' where login ='$elogin'");
					}
											
					// envoi du mel		// Suppression de la demande dans la table

mysql_query("Delete from dmd_tbl where id = '$iddmd' ");


					$expediteur   = "$dbprof<$from>"; 
					$message = "Le $date, $dbfullname a accept� votre demande de validation de la comp�tence $cpt du B2I $etab.";
					mail($to,$sujet,$message,"Reply-to: $expediteur\nFrom: $expediteur\n");
				}


																		// Cas de non validation de la comp�tence
																		
			if ($_POST["action"]=="ko")
				{

				// envoi du mel

				$expediteur   = "$dbprof<$from>"; 
				$message = "Le $date, $dbfullname a refus� votre demande de validation de la comp�tence $cpt du B2I $etab.";
				mail($to,$sujet,$message,"Reply-to: $expediteur\nFrom: $expediteur\n");
				}
                
                
                
                                                             //Cas pas en mesure de valider de la comp�tence
	
			if ($_POST["action"]=="no")
				{
			// envoi du mel
			
				$expediteur   = "$dbprof<$from>"; 
				$message = "Le $date, $dbfullname n'est pas en mesure de traiter votre demande de validation de la comp�tence $cpt du B2I $etab.";
				mail($to,$sujet,$message,"Reply-to: $expediteur\nFrom: $expediteur\n");
				}	
						
			}		
			
			// Suppression de la demande dans la table
		mysql_query("Delete from dmd_tbl where id = '$iddmd' ");
				
		}
	}


      
		// Message de confirmation

print "<div class=confirm><br><br><br>Op�ration r�alis�e avec succ�s</b<br><br><br><br></div>";


?>


<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form1" method="post" action="a_demande.php">
    <td width="200" height="17" align ="center"><input type="submit" name="button" value="&nbsp;&nbsp;&nbsp;Traiter une autre demande&nbsp;&nbsp;&nbsp;"><br><br></td>
  </tr></form>
</table>


<?php mysql_close(); ?>



</body>
</html>
