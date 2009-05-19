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

<div class="texte"><h1>Suppression d'une discipline</h1><BR></div>

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

if (empty($_POST["discip0"]))	//on vérifie avec empty si les champs sont vides
	{print "<div class=avertissement><br><br><br><br>Veuillez choisir au moins une discipline<br><br><br><br></div>"; die;}
else
	{
	print "<br><br><br><br>";
	foreach ( $discip0 as $disc ) 		{
		$sql = "SELECT discipline,id from disc_tbl where discipline LIKE '$disc' ";
		$res = mysql_query($sql);
		while ( $resultat = mysql_fetch_array($res))
			{
			$id=$resultat["id"] ;
			print "<div class=confirm><br>La discipline <b>$disc</b> est supprimée</div>";
			mysql_query("UPDATE disc_tbl SET $etab ='0' WHERE id =$id") or die ("erreur requête ".mysql_error());
			mysql_query("DELETE FROM $discip WHERE discip='$disc'") or die ("erreur requête ".mysql_error());
			}
		}
	}
					
mysql_close();	
?>

<br><br><br><br>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form1" method="post" action="a_discip.php">
    <td width="200" height="17" align ="center"><input type="submit" name="button" value="&nbsp;&nbsp;&nbsp;Retour&nbsp;&nbsp;&nbsp;"><br><br></td>
  </tr></form>
</table>

</body>
</html>
