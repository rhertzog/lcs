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
<div class="texte"><h1>Demande de validation d'une compétence</h1></div>

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
else{if (is_eleve($login)!="true"){die("<div class=\"remarque\">L'accès à cette page est réservée aux élèves !<br><br></div>");}}

list ($idpers,$uid)= isauth();
list($user, $groups)=people_get_variables($uid, true);
$dbfulleleve="$user[fullname]"; 	  							// on stocke le nom complet de l'élève dans la variable $dbfulleleve
$dbnomeleve="$user[nom]";										// on stocke le nom de l'élève dans la variable $dbnomeleve
$dblogeleve="$user[uid]";             							// on stocke le login de l'élève dans la variable $dblogelev
$from="$user[email]"; 	          										// on stocke le mel de l'eleve dans la variable $dbfrom
																			
if ( count($groups) )															// on stocke la classe de l'élève dans la variable $dbclasse
	{for ($loop=0; $loop < count ($groups) ; $loop++)
      { if ( ereg("^Classe", $groups[$loop]["cn"]) )
			{$dbclasse="".$groups[$loop]["cn"]."";}    					}	}

if ($etab=="Lycee") {$comp="L";}
if ($etab=="Ecole") {$comp="E";}
if ($etab=="College") {$comp="C";}

$date=date("d m Y");	


$req = ("SELECT login FROM dmd_tbl WHERE login LIKE '$dblogeleve'") or die ("erreur sql ".mysql_error());
$resultat = mysql_query($req);
$nbresultat = mysql_num_rows($resultat);
if ($nbresultat>="$nbdmd")
  {print "<div class=avertissement><br><br>Vous avez actuellement $nbdmd demandes de validation en cours de traitement.<br>
  Attendez une réponse avant de faire une nouvelle demande de validation...<br><br><br></div>
   <center><img src=\"Images/iioeil.gif\" ALIGN=\"middle\" BORDER=\"0\" HEIGHT=\"90\"></center><br><br>";die;} else {}

///////////////////////////////////////////////////////// cas commentaire /////////////////////////////////////////////////////////
if ($justif=="ok")
{
	if(empty($_POST["nom"]) or empty($_POST["cpt"]) or empty($_POST["comment"]))		
  		{print "<div class=\"avertissement\"><br><br><br>Veuillez compléter tous les champs<br><br><br></div>";
  		die;
  		}
	else
		{
		$commentaire="$_POST[comment]";
										//test si demande deja en cours
		$sql = "SELECT date from dmd_tbl where cpt LIKE '$_POST[cpt]' and login LIKE '$dblogeleve'";
		$res = mysql_query($sql);
		$exist = mysql_numrows($res);
		if(!$exist)
			{								//on stocke le login du prof dans $uid, le mel du professeur dans la variable $to
										//on stocke le nom du prof dans $dbnomprof,le nomcomplet du professeur dans la variable $dbfullprof
			$users = search_people ("(cn=*$_POST[nom]*)");
			for ( $loop=0; $loop<count($users); $loop++ )
				{$uid="".$users[$loop]["uid"]."";           				list($user, $groups)=people_get_variables($uid, true);
				$to="$user[email]";
				$dbnomprof="$user[nom]";
				$dbfullprof="$user[fullname]";				}						
										//traitement
										
			mysql_query("INSERT Into dmd_tbl (login,fulleleve,nomeleve,classe,cpt,fullprof,nomprof,date) 
			VALUES ('$dblogeleve','$dbfulleleve','$dbnomeleve','$dbclasse','$_POST[cpt]','$dbfullprof','$dbnomprof','$date')") or die ("erreur requête ".mysql_error());
			print "<div class=confirm><br><br><br><br>La demande de validation de la compétence <b>$_POST[cpt]</b><br>de <b>$dbfulleleve</b> à <b>$dbfullprof</b> est enregistrée.<br><br>
			<img src=\"Images/iioeil.gif\" ALIGN=\"middle\" BORDER=\"0\" HEIGHT=\"90\"></div><br><br>";	
				
			// envoi du mel si $mel est ok
		
			if ($mel=="ok")
				{$expediteur   = "$dbfulleleve<$from>";
				$sujet="Demande de validation  d'une compétence B2I";	
				$message = "Le $date, $dbfulleleve élève de la $dbclasse vous demande de valider la compétence $_POST[cpt] du B2I $etab.
				Commentaire associé : $commentaire ";	
				mail($to,$sujet,$message,"Reply-to: $expediteur\nFrom: $expediteur\n");
				}															
			}
			else
				{print "<div class=avertissement><br><br><br><br>Il existe déja une demande de validation de la compétence <b>$_POST[cpt]</b><br>
				pour l'élève $dbfulleleve.</div><br><br>";}
		}
}
else
///////////////////////////////////////////////////////// cas pas de commentaire /////////////////////////////////////////////////////////

{
	if(empty($_POST["nom"]) or empty($_POST["cpt"]))		
  		{print "<div class=\"avertissement\"><br><br><br>Veuillez compléter tous les champs<br><br><br></div>";
  		die;
  		}
	else
		{
		$commentaire="-----";
										//test si demande deja en cours
		$sql = "SELECT date from dmd_tbl where cpt LIKE '$_POST[cpt]' and login LIKE '$dblogeleve'";
		$res = mysql_query($sql);
		$exist = mysql_numrows($res);
		if(!$exist)
			{								//on stocke le login du prof dans $uid, le mel du professeur dans la variable $to
										//on stocke le nom du prof dans $dbnomprof,le nomcomplet du professeur dans la variable $dbfullprof
			$users = search_people ("(cn=*$_POST[nom]*)");
			for ( $loop=0; $loop<count($users); $loop++ )
				{$uid="".$users[$loop]["uid"]."";           				list($user, $groups)=people_get_variables($uid, true);
				$to="$user[email]";
				$dbnomprof="$user[nom]";
				$dbfullprof="$user[fullname]";				}						
										//traitement
										
			mysql_query("INSERT Into dmd_tbl (login,fulleleve,nomeleve,classe,cpt,fullprof,nomprof,date) 
			VALUES ('$dblogeleve','$dbfulleleve','$dbnomeleve','$dbclasse','$_POST[cpt]','$dbfullprof','$dbnomprof','$date')") or die ("erreur requête ".mysql_error());
			print "<div class=confirm><br><br><br><br>La demande de validation de la compétence <b>$_POST[cpt]</b><br>de <b>$dbfulleleve</b> à <b>$dbfullprof</b> est enregistrée.<br><br>
			<img src=\"Images/iioeil.gif\" ALIGN=\"middle\" BORDER=\"0\" HEIGHT=\"90\"></div><br><br>";	
			
			// envoi du mel si $mel est ok
		
			if ($mel=="ok")
				{$expediteur   = "$dbfulleleve<$from>";
				$sujet="Demande de validation  d'une compétence B2I";	
				$message = "Le $date, $dbfulleleve élève de la $dbclasse vous demande de valider la compétence $_POST[cpt] du B2I $etab.
				Commentaire associé : $commentaire ";	
				mail($to,$sujet,$message,"Reply-to: $expediteur\nFrom: $expediteur\n");
				}															
														
			}
			else
				{print "<div class=avertissement><br><br><br><br>Il existe déja une demande de validation de la compétence <b>$_POST[cpt]</b><br>
				pour l'élève $dbfulleleve.</div><br><br>";}
		}

}


mysql_close();
?></body>
</html>
