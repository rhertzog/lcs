<?php
/* ==========================================================
   Projet LCS : Linux Communication Server
   Plugin "Biilan2: Gestion administrative du B2i"
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

print "<div class=\"texte\"><h1>Affectation des professeurs dans les disciplines</h1></div>";

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'acc�s � Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

if (empty($_POST["prof"]) or empty($_POST["discip"]))	//on v�rifie avec empty si les champs sont vides	
	{print "<div class=avertissement><br><br><br>Veuillez compl�ter les champs.<br><br><br><br></div>";}
else
	{
	foreach ( $prof as $id )	
		{mysql_query("UPDATE  profs_tbl SET  `discipline` =  '$_POST[discip]' WHERE  fullprof='$id'");
		print "<div class=confirm><br><br><br>Op�ration r�alis�e avec succ�s :<br>
			<i><b>$id</b>  est enregistr� en<b> $_POST[discip]</b></i><br></div>";}
	}

// si tous les profs affect�s � une discipline, on affiche la page : mise � jour annuelle

$req = ("SELECT fullprof FROM profs_tbl WHERE discipline LIKE ''") or die ("erreur sql ".mysql_error());
$result = mysql_query($req);
$nbresultat = mysql_num_rows($result);
if ($nbresultat==0)
	{
	print"<div class=confirm><br />Tous les professeurs sont affect�s dans une discipline <br /></div>";
  	print"<br><br>";
  	print"<form method=\"post\" action=\"a_majan.php\">";
	print"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">";
  	print"<tr>";
    print"<td width=\"200\" height=\"17\" align =\"center\"><input type=\"submit\" name=\"button\" value=\"RETOUR\" style=\"width:300px\"></td><br>";
  	print"</tr>";
  	print"</table>";
	print"</form>";
	}
else
	{
	// Retour � la page d'affectation des disciplines
	print"<br><br>";
	print"<form method=\"post\" action=\"a_affprof.php\">";
	print"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">";
  	print"<tr>";
    print"<td width=\"200\" height=\"17\" align =\"center\"><input type=\"submit\" name=\"button\" value=\"Affecter les professeurs aux disciplines\" style=\"width:300px\"></td><br>";
  	print"</tr>";
  	print"</table>";
	print"</form>";
	// Retour � la page de mise � jour annuelle 
	print"<form method=\"post\" action=\"a_majan.php\">";
	print"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">";
  	print"<tr>";
    print"<td width=\"200\" height=\"17\" align =\"center\"><input type=\"submit\" name=\"button\" value=\"RETOUR\" style=\"width:300px\"></td><br>";
  	print"</tr>";
  	print"</table>";
	print"</form>";
	}	
mysql_close();
?>

</body>
</html>
