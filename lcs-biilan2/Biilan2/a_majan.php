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
print "<div class=\"texte\"><h1>Mise à Jour Annuelle de Biilan $typetab</h1></div>"; 
?>  
  
<br><br><br><br>
<form method="post" action="a_majprof.php">
<table border="0" cellspacing="0" cellpadding="0" align="center">
	  <tr><td width="200" height="17" align ="center"><input type="submit" name="button" value="Mettre à jour la liste des professeurs" style="width:300px"><br><br></td></tr>
</table>
</form>

<form method="post" action="a_modprof.php">
<table border="0" cellspacing="0" cellpadding="0" align="center">
	<tr><td width="200" height="17" align ="center"><input type="submit" name="button" value="Modifier la liste professeurs" style="width:300px"><br><br></td></tr>
</table>
</form>

<?php   
 
 /*On affiche pas le bouton Affecter si tous les professeurs ont une discipline d'affectée */
        	
$req = mysql_query("SELECT discipline,fullprof from profs_tbl where discipline like ''") or die ("erreur sql ".mysql_error());			
$res= mysql_numrows($req);
if (!$res) {}
else 
	{Print"<form method=\"post\" action=\"a_affprof.php\">";
	Print"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">";
	Print"<tr>";
	Print"<td width=\"200\" height=\"17\" align =\"center\"><input type=\"submit\" name=\"button\" value=\"Affecter les professeurs aux disciplines\" style=\"width:300px\">";
	Print"<br><br>";
	Print"</td>";
	Print"</tr>";
	Print"</table>";
	Print"</form>";										
	}
 /*On affiche pas le bouton Supprimer si aucune discipline d'affectée */

$req1 = mysql_query("SELECT prof from profs_tbl") or die ("erreur sql ".mysql_error());			
$res1= mysql_numrows($req1);
if ($res1==$res){}
else 
	{Print"<form method=\"post\" action=\"a_affprof0.php\">";
	Print"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">";
	Print"<tr>";
	Print"<td width=\"200\" height=\"17\" align =\"center\"><input type=\"submit\" name=\"button\" value=\"Supprimer les professeurs des disciplines\" style=\"width:300px\">";
	Print"<br><br>";
	Print"</td>";
	Print"</tr>";
	Print"</table>";
	Print"</form>";	
	}
mysql_close();
?>

<form method="post" action="a_orphelin.php">
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><td width="200" height="17" align ="center"><input type="submit" name="button" value="Mettre à jour la liste des élèves"style="width:300px"><br><br></td></tr>
</table>

</form>
</body>
</html>
