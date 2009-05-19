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

list ($idpers,$uid)= isauth();
list($user, $groups)=people_get_variables($uid, true);
$dbfullname="$user[fullname]"; 	  									// on stocke le nom complet du prof dans la variable $dbfullname
$dbnom="$user[nom]";            											// on stocke le nom du prof dans la variable $dbnom
$dblogin="$user[uid]";            												// on stocke le login du prof dans la variable $dblogin
$from="$user[email]"; 	                    								 	// on stocke le mel du prof dans la variable $from
$sujet="Votre demande de validation  d'une compétence B2I";  // objet du message à envoyer 

$date=date("d m Y");																// on stocke la date dans la variable $date

print "<div class=\"texte\"><h1>Traitement des demandes de validation en attente</h1></div>";

function resultat($a,$b)
{$c = ($a + $b); return $c;}


if (empty($_POST["val"]))
  {die ("<div class=avertissement><br><br><br>Veuillez sélectionner la demande à traiter.<br><br><br></div>");}
else
	{
	foreach ( $val as $id ) 		{$req = mysql_query("SELECT id,login,fulleleve,nomeleve,classe,cpt from dmd_tbl where id LIKE '$id' ") or die ("erreur req ".mysql_error());
		$res= mysql_numrows($req);
		while ( $resultat1 = mysql_fetch_array($req))
			{ $cpt = "$resultat1[cpt]";      			// on stocke la compétence demandée
			$classe = "$resultat1[classe]";      		// on stocke la classe 
			$elogin="$resultat1[login]";    			// on stocke le login de l'élève
			$fulleleve="$resultat1[fulleleve]";       // on stocke le nom complet
			$nomeleve="$resultat1[nomeleve]";       	// on stocke le nom 
			$iddmd="$resultat1[id]"; 
      			list($user, $groups)=people_get_variables($elogin, true);
			$to="$user[email]"; 	  // on stocke le mel de l'élève la variable $to	
		

																// Cas validation de la compétence 
																
			$req2 = mysql_query("SELECT discipline from profs_tbl where fullprof LIKE '$dbfullname' ") or die ("erreur req ".mysql_error());
			$res2= mysql_numrows($req2);
			while ( $resultat2 = mysql_fetch_array($req2))
				{ $discip = "$resultat2[discipline]";}	 			// on stocke la discipline											

			if ($_POST["action"]=="ok")                   				{ mysql_query("INSERT INTO bii_tbl VALUES ('','$elogin','$nomeleve','$fulleleve','$cpt','$date','$dbfullname','$discip')") or die ("erreur requête ".mysql_error());

				// test si eleve déjà dans base
			
				$req3 = ("SELECT login FROM resultat_tbl WHERE login LIKE '$elogin'") or die ("erreur sql ".mysql_error());
				$resultat3 = mysql_query($req3);
				$nbresultat3 = mysql_num_rows($resultat3);
				if ($nbresultat3==0)	// si l'eleve n'est pas ds la base on cree
  					{mysql_query("INSERT INTO resultat_tbl VALUES ('','$elogin','1')") or die ("erreur requête ".mysql_error());}
				else					// sinon on met à jour le resultat
					{$req4 = mysql_query("SELECT result FROM resultat_tbl WHERE login LIKE '$elogin'") or die ("erreur sql ".mysql_error());
					$resultat4 = mysql_fetch_array($req4);	
					$val = "1";
					$note = "$resultat4[result]";
					$total=resultat($note,$val);
					mysql_query("Update resultat_tbl Set result ='$total' where login ='$elogin'");
					}
						
					
					// envoi du mel

					$expediteur   = "$dbfullname<$from>"; 
					$message = "Le $date, $dbfullname a accepté votre demande de validation de la compétence $cpt du B2I $etab.";
					mail($to,$sujet,$message,"Reply-to: $expediteur\nFrom: $expediteur\n");
				}


																		// Cas de non validation de la compétence
																		
			if ($_POST["action"]=="ko")
				{

				// envoi du mel

				$expediteur   = "$dbfullname<$from>"; 
				$message = "Le $date, $dbfullname a refusé votre demande de validation de la compétence $cpt du B2I $etab.";
				mail($to,$sujet,$message,"Reply-to: $expediteur\nFrom: $expediteur\n");
				}
                
                
                			if ($_POST["action"]=="no")
				{
			// envoi du mel
			
				$expediteur   = "$dbfullname<$from>"; 
				$message = "Le $date, $dbfullname n'est pas en mesure de traiter votre demande de validation de la compétence $cpt du B2I $etab.";
				mail($to,$sujet,$message,"Reply-to: $expediteur\nFrom: $expediteur\n");
				}
				
			}
			
		// Suppression de la demande dans la table		mysql_query("Delete from dmd_tbl where id = '$iddmd' ");
		
		}
	}


      
		// Message de confirmation

print "<div class=confirm><br><br><br>Opération réalisée avec succès</b<br><br><br><br></div>";


?>


<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form1" method="post" action="p_demande.php">
    <td width="200" height="17" align ="center"><input type="submit" name="button" value="&nbsp;&nbsp;&nbsp;Traiter une autre demande&nbsp;&nbsp;&nbsp;"><br><br></td>
  </tr></form>
</table>


<?php mysql_close(); ?>


</body>
</html>

