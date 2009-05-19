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

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

if ($etab=="Lycee") {$typetab="Lycée";}
if ($etab=="Ecole") {$typetab="École";}
if ($etab=="College") {$typetab="Collège";}
print "<div class=\"texte\"><h1>Configuration de Biilan $typetab</h1></div>";
?>

<br><br>

<form method="post" action="a_supdiscip.php">
<table border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td>Supprimer la discipline &nbsp;&nbsp;&nbsp;</td>    
 		 	<?php    
        	print "<td><select size=\"10\" STYLE=\"background:#F0E4C8;color:#000080\" NAME=\"discip0[]\" MULTIPLE>";
			$req = mysql_query("SELECT discipline from disc_tbl where $etab =1 order by discipline ASC") or die ("erreur sql ".mysql_error());
			while ( $resultat = mysql_fetch_array($req))
			{echo "<option value=\"" . $resultat["discipline"] . "\">" . $resultat["discipline"] . " </option>";}
			?></td>
		<td >&nbsp;&nbsp;&nbsp;<input type="image" src="Images/disabled.gif"></td>
 	</tr>
</table>
</form>

<br>

<form method="post" action="a_ajtdiscip.php">
<table border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td>Ajouter la discipline &nbsp;&nbsp;&nbsp;</td>    
 		 	<?php    
        	print "<td><select size=\"10\" STYLE=\"background:#F0E4C8;color:#000080\" NAME=\"discip1[]\" MULTIPLE>";
			$req1 = mysql_query("SELECT discipline from disc_tbl where $etab =0 order by discipline ASC") or die ("erreur sql ".mysql_error());
			while ( $resultat1 = mysql_fetch_array($req1))
			{echo "<option value=\"" . $resultat1["discipline"] . "\">" . $resultat1["discipline"] . " </option>";}
			mysql_close();
			?></td>
		<td >&nbsp;&nbsp;&nbsp;<input type="image" src="Images/enabled.gif"></td>
 	</tr>
</table>
</form>

<br>

</body>
</html>
