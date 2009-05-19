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

<div class="texte"><h1>Modification de la base professeurs </h1><br><br></div>

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
 
if(empty($_POST["fullprof"]))      // verif etes vous sur ?
	{print "<div class=avertissement><br><br>Veuillez sélectionner un professeur.</div>";}
else
	{$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
	mysql_select_db($dbbase,$db) or die ("erreur de connexion base");
	foreach ( $fullprof as $id )
		{mysql_query("Delete from profs_tbl where fullprof ='$id'");
		mysql_query("Delete from dmd_tbl where fullprof ='$id'");}
	print '<div class=confirm><br><br>Opération réalisée avec succès</div>';
	}
	mysql_close();
?>

<br><br><br>
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form1" method="post" action="a_modprof.php">
    <td width="200" height="17" align ="center"><input type="submit" style="width:300px" name="button" value="&nbsp;&nbsp;&nbsp;Modifier la liste des professeurs &nbsp;&nbsp;&nbsp;"><br><br></td>
  </tr></form>
  <tr><form name="form2" method="post" action="a_majan.php">
    <td width="200" height="17" align ="center"><input type="submit" style="width:300px" name="button" value="&nbsp;&nbsp;&nbsp;Retour vers page de mise à jour annuelle&nbsp;&nbsp;&nbsp;"><br><br></td>
  </tr></form>
</table>

</div>
</body>
</html>
