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

<form method="post" action="e_demande1.php">
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
else{if (is_eleve($login)!="true"){die("<div class=\"remarque\">L'accès à cette page est réservée aux élèves <br><br></div>");}}

list ($idpers,$uid)= isauth();

list($user, $groups)=people_get_variables($uid, true);
$dbfullname="$user[fullname]"; 	  // on stocke le nom complet de l'élève dans la variable $dbfullname
$dblogin="$user[uid]";             // on stocke le login de l'élève dans la variable $dblogin

if ($etab=="Lycee") {$comp="L";}
if ($etab=="Ecole") {$comp="E";}
if ($etab=="College") {$comp="C";}


list($user, $groups)=people_get_variables($dblogin, true);
if ( count($groups) )
	{ for ($loop=0; $loop < count ($groups) ; $loop++)
		if ( ereg("^Classe", $groups[$loop]["cn"]) )
		{echo "<div class=confirm><br><b>$dbfullname</b> élève de ".$groups[$loop]["cn"].", ";}	}

$req = ("SELECT login FROM dmd_tbl WHERE login LIKE '$dblogin'") or die ("erreur sql ".mysql_error());
$resultat = mysql_query($req);
$nbresultat = mysql_num_rows($resultat);

if ($nbresultat>="$nbdmd")
  {print "<div class=avertissement><br><br>Vous avez actuellement $nbdmd demandes de validation en cours de traitement.<br>
  Attendez une réponse avant de faire une nouvelle demande de validation...<br><br><br></div>
   <center><img src=\"Images/iioeil.gif\" ALIGN=\"middle\" BORDER=\"0\" HEIGHT=\"90\"></center><br><br>";die;}
else {}

$req4 = mysql_query("SELECT result FROM resultat_tbl WHERE login LIKE '$dblogin'") or die ("erreur req ".mysql_error());
$resultat4 = mysql_fetch_array($req4);
$totalelv=$resultat4[result];

$req5=mysql_query("SELECT * FROM $items");
$total=mysql_num_rows($req5);


// test si tts cpt validees

if($total==$totalelv)
	{die("<div class=\"avertisssement\"><br><br><br>Toutes vos compétences sont validées<br><br><br>
	<IMG align=\"middle\" SRC=\"Images/iiok.gif\">
	<br><br><br></div>");
	}


										//cas eleve non inscrit

?>


<br><br>
 <table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
     <td><div class=affichok>pense avoir atteint la compétence &nbsp;&nbsp;</div></td>
<td><select size="3" STYLE="background:#F0E4C8;color:#000080" NAME="cpt">

<?php
										//modification actuelle


$req1 = mysql_query("SELECT num FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
$res1= mysql_numrows($req1);
while ( $resultat1 = mysql_fetch_array($req1))
{
	$req2 = mysql_query("SELECT num FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
	$res2= mysql_numrows($req2);
	while ( $resultat2 = mysql_fetch_array($req2))
	{
		$aff="$comp.$resultat1[num].$resultat2[num]"; 			//affichage de la compétence
																						
														//test si la demande est déja dans la table bii																						
																						
		$req3 = ("SELECT id FROM bii_tbl WHERE cpt like '$aff' and login like '$dblogin'") or die ("erreur sql ".mysql_error());
		$res3 = mysql_query($req3);
		$exist1 = mysql_num_rows($res3);
		if($exist1<>0)
			{	}
		else
			{
														//test si la demande est déja dans la table dmd																	
			
				$req4 = ("SELECT id FROM dmd_tbl WHERE cpt like '$aff' and login like '$dblogin'") or die ("erreur sql ".mysql_error());
				$res4 = mysql_query($req4);
				$exist2 = mysql_num_rows($res4);
				if($exist2<>0)
					{}
				else
					{echo "<option value=\"$aff\">&nbsp;&nbsp;$aff&nbsp;&nbsp;</option>";}		
			}
								

	}
				
}



?>
</td>
</tr>
</table>

<br>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
     <td><div class=affichok>et demande la validation à : &nbsp;&nbsp;</div></td>
     <td><select size="5" STYLE="background:#F0E4C8;color:#000080" NAME="nom">
            <?php
            		$req = mysql_query("SELECT fullprof from profs_tbl order by prof ASC ") or die ("erreur sql ".mysql_error());
            		while ( $resultat = mysql_fetch_array($req))
            			{
            				echo "<option value=\"" . $resultat["fullprof"] . "\">" . $resultat["fullprof"] . "</option>";
            			}
            ?>
    </td>
  </tr>
 </table>

<br>

<?php
//affichage case comment si besoin
 if ($justif=="ok")
 	{
	print"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">";
  		print"<tr>";
     		print"<td><div class=affichok>Commentaire : &nbsp;&nbsp;</div></td>";
			print"<td><input type=\"text\" STYLE=\"background:#F0E4C8;color:#000080\" size=\"35\" name=\"comment\"> </td>";
 		print"</tr>";
	print"</table>";
	}
?>


<br>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td width="200"ALIGN="MIDDLE"><IMG SRC="Images/ping.png"><BR>
    <input type="submit" name="button" value="Demander la validation">
    </td>
  </tr>
</table>


<?php
mysql_close();
?>

</form>
</body>
</html>
