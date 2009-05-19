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

<div class="texte"><h1>Suppression d'items validés accidentellement </h1><br><br></div>

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

if(empty($id))
	{die ("<div class=\"avertissement\"><br><br><br>Veuillez choisir au moins un item !<br><br><br></div>");}
else
	{
	foreach ( $id as $idsup)  
		{  
		$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
		mysql_select_db($dbbase,$db) or die ("erreur de connexion base");
		
		$req = mysql_query("SELECT login from bii_tbl  WHERE id LIKE '$idsup'") or die ("erreur sql ".mysql_error());
		while ( $resultat = mysql_fetch_array($req))
			{$login= $resultat["login"];
			$req1 = mysql_query("SELECT result from resultat_tbl  WHERE login LIKE '$login'") or die ("erreur sql ".mysql_error());
			while ( $resultat1 = mysql_fetch_array($req1))
				{$result= $resultat1["result"];
				$newresult=$result-1;
				}
			}
		mysql_query("Delete from bii_tbl where id ='$idsup'");	
		mysql_query("UPDATE resultat_tbl SET result ='$newresult' WHERE login='$login'") or die ("erreur requête ".mysql_error());
		}	
	}
print '<div class=confirm><br><br>Opération réalisée avec succès</div>';
mysql_close();
?>

<br><br><br>
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form2" method="post" action="rectiprof.php">
    <td width="200" height="17" align ="center"><input type="submit" style="width:300px" name="button" value="&nbsp;&nbsp;&nbsp;Poursuivre&nbsp;&nbsp;&nbsp;"><br><br></td>
  </tr></form>
</table>

</div>
</body>
</html>
