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

print "<div class=\"texte\"><h1>Délivrance d'une attestation B2I</h1></div>";

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}
  
$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

//determination du seuil de competences
$req2=mysql_query("SELECT * FROM $items");
$res2=mysql_num_rows($req2);
$attest1 = $pourcentage * $res2;
$seuil = floor($attest1);


?>

<form method="post" action="a_attest1.php">

<br><br>

<?php
print "<div class=\"texte\"><center><br><br><b>Sélectionner la classe :<b><br><br></center></div>";
print "<center><select size=\"6\" STYLE=\"background:#F0E4C8;color:#000080\" NAME=\"classe\">";

list ($idpers)= isauth();
$filter = "(cn=Classe_*$group)";
$groups=search_groups($filter);

for ($loop=0; $loop < count($groups); $loop++)
{
$classe=$groups[$loop]["cn"];

//recherche si login/classe présent dans table resultat
$test=0; 
$users = search_uids ("(cn=$classe)","half");
   	for ( $loop1=0; $loop1<count($users); $loop1++ )
		{
    	$login = "".$users[$loop1]["uid"].""; // on stocke le login de l'eleve dans la variable $login
      $req = mysql_query("SELECT * from resultat_tbl WHERE login = '$login' AND result >=$seuil") or die ("erreur sql ".mysql_error());
		$res = mysql_numrows($req);
			if (!$res)
				{}
			else
      		{$test=1;}
		}	
if ($test==1)
	{echo "<option value=\"".$groups[$loop]["cn"]."\">".$groups[$loop]["cn"].",&nbsp;".$groups[$loop]["description"]."</option>";}
}

print "</select></center>";

?>

<br><br>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  <td width="200"ALIGN="MIDDLE"><IMG SRC="Images/ping.png"><BR>
  <input type="submit" name="button" value="Valider">
  </td>
</table>

</form>
</body>
</html>
