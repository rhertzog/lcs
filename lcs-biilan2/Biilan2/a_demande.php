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

<form method="post" action="a_demande1.php">
<div class="texte"><h1>Liste des demandes de validation en attente</h1><BR></div>

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
{die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}
 
if ($etab=="Lycee") {$comp="L";}
if ($etab=="Ecole") {$comp="E";}
if ($etab=="College") {$comp="C";}

$req = ("SELECT fullprof from dmd_tbl") or die ("erreur sql ".mysql_error());
$resultat = mysql_query($req);
$nbresultat = mysql_num_rows($resultat);

if ($nbresultat==0)
  {print "<div class=confirm><br><br>Il n'y a pas de demande en cours<br><br><br></div>";die;}
else
	{
	$req1 = mysql_query("SELECT fullprof from profs_tbl order by prof ASC ") or die ("erreur sql ".mysql_error());
	while ( $resultat1 = mysql_fetch_array($req1))
		{
		$req2 = ("SELECT login from dmd_tbl WHERE fullprof LIKE '$resultat1[fullprof]'") or die ("erreur sql ".mysql_error());
		$resultat2 = mysql_query($req2);
		$nbresultat2 = mysql_num_rows($resultat2);
    
		if ($nbresultat2==0)
			{}
		else
			{
			print "<div class=avertissement>Il y a $nbresultat2 demande(s) en cours pour $resultat1[fullprof]<br></div>";
			print "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tr>
			<td><select size=\"5\" STYLE=\"background:#F0E4C8;color:#000080\" NAME=\"val[]\" MULTIPLE>";
			$req3 = mysql_query("SELECT id,login,fulleleve,classe,cpt,fullprof,date from dmd_tbl WHERE fullprof LIKE '$resultat1[fullprof]' order by cpt ASC") or die ("erreur sql ".mysql_error());
			 while ( $resultat3 = mysql_fetch_array($req3))
				{echo "<option value=\"" . $resultat3["id"] . "\">Compétence " . $resultat3["cpt"] . " demandée le " . $resultat3["date"] . " par  " . $resultat3["fulleleve"] . "   (" . $resultat3["classe"] . ") </option>";
				}
				print "</td></tr></table><br><br>";
				
			}
		}

	}

?>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><td align="center" STYLE="color:#000080;"><a href="c_cndp.php">Rappel des compétences.</a></td></tr>
</table>

<table border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td valign="middle"><select STYLE="background:#F0E4C8;color:#000080" NAME="action" >
        		<option VALUE="ok">Valider la demande</option>
        		<option VALUE="ko">Ne pas valider la demande</option>
        		<option VALUE="no">N'est pas en mesure de valider la demande</option></select></td>
		<td></td>
		<td width="200"ALIGN="MIDDLE"><IMG SRC="Images/ping.png"><BR><input type="submit" name="button" value="Confirmer"></td>		
  	</tr>
</table>

<br>

<?php mysql_close(); ?>

</form>
</body>
</html>
