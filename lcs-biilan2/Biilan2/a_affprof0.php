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

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

print "<div class=\"texte\"><h1>Suppression des professeurs des disciplines</h1></div>";

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}
?>

<br><br>

<form method="post" action="a_affprof01.php">
<table border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td>Supprimer &nbsp;&nbsp;&nbsp;</td>    
 		 	<?php    
        	print "<td><select size=\"10\" STYLE=\"background:#F0E4C8;color:#000080\" NAME=\"prof[]\" MULTIPLE>";
			$req1 = mysql_query("SELECT discipline,fullprof from profs_tbl order by prof ASC") or die ("erreur sql ".mysql_error());
			while ( $resultat1 = mysql_fetch_array($req1))
			{
			if($resultat1[discipline]=="") {}
			else {echo "<option value=\"" . $resultat1["fullprof"] . "\">" . $resultat1["fullprof"] . " </option>";}
			}
			print "</td><td>&nbsp;&nbsp;&nbsp;de sa discipline&nbsp;&nbsp;&nbsp;</td>"; 
			mysql_close();
			?></td>
		<td >&nbsp;&nbsp;&nbsp;<input type="image" src="Images/disabled.gif"></td>
 	</tr>
</table>

</form>
</body>
</html>
