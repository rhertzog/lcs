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

print "<div class=\"texte\"><h1>Supprimer les professeurs des disciplines</h1></div>";

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

if (empty($_POST["prof"]))	//on vérifie avec empty si les champs sont vides	
	{print "<div class=avertissement><br><br><br>Veuillez choisir au moins un professeur<br><br><br><br></div>";}
else
	{
	foreach ( $prof as $id )	
		{mysql_query("UPDATE  profs_tbl SET  `discipline` =  '' WHERE  fullprof='$id'");
		print "<div class=confirm><br><br><br>Opération de suppression de discipline réalisée avec succès <br> <i><b>$id</b> est supprimé(e) de sa discipline</i><br></div>";
		}
	}
		
	// si tous les profs n'ont plus de discpline, on affiche la page : mise à jour annuelle

$req = ("SELECT fullprof FROM profs_tbl WHERE discipline <> ''") or die ("erreur sql ".mysql_error());
$resulta = mysql_query($req);
$nbresultat = mysql_num_rows($resulta);
if (!$nbresultat)
	{
  	print"<div class=avertissement><br />Aucun professeur n'est affecté à une discipline<br /></div>";
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
	// Retour à la page : supprimer des disciplines
	print"<br><br>";
	print"<form method=\"post\" action=\"a_affprof0.php\">";
	print"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">";
  	print"<tr>";
    print"<td width=\"200\" height=\"17\" align =\"center\"><input type=\"submit\" name=\"button\" value=\"Supprimer les professeurs des disciplines\" style=\"width:300px\"></td><br>";
  	print"</tr>";
  	print"</table>";
	print"</form>";
	// Retour à la page de mise à jour annuelle 
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
