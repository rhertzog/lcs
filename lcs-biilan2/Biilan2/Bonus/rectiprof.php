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
<link rel="stylesheet" href="../Style/style.css">
</head>
<body>

<form method="post" action="rectiprof1.php">
<div class="texte"><h1>Suppression d'items validés accidentellement</h1><br><br></div>

<div class="remarque">Cette page vous permet de supprimer des items validés par erreur par un enseignant<br><br></div>

<?php
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

require ("../config.php");
require ("../config2.php");

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

print "<div class=\"texte\"><center><b>Choisir l'enseignant :<b><br><br></center></div>";
print "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tr><td><select size=\"6\" STYLE=\"background:#F0E4C8;color:#000080\" NAME=\"fullprof\">";
$req = mysql_query("SELECT fullprof,prof from profs_tbl  order by prof ASC") or die ("erreur sql ".mysql_error());
while ( $resultat = mysql_fetch_array($req))
	{echo "<option value=\"" . $resultat["fullprof"] . "\">" . $resultat["fullprof"] . " </option>";}
print "</td></tr></table><br><br>";
mysql_close();
?>


<table WIDTH=100%>
  <td ALIGN="MIDDLE"><IMG SRC="../Images/ping.png"><BR><input type="submit" name="button" value="Continuer"></td>
</table>

<br><br><br>

</form>
</div>
</body>
</html>
