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
<title>Gestion Administrative du B2I au collège</title>
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<link rel="stylesheet" href="Style/style.css">
</head>
<body>
<form method="post" action="a_visufiche1.php">

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
  
$date=date("d m Y");					// on stocke la date dans la variable $date
list ($idpers,$uid)= isauth();
list($user, $groups)=people_get_variables($uid, true);
$dbfullname="$user[fullname]"; 	  		// on stocke le nom complet de l'admin dans la variable $dbfullname
$dbnom="$user[nom]";            			   // on stocke le nom de l'admin dans la variable $dbnom
$dblogin="$user[uid]";            			  		// on stocke le login de l'admin dans la variable $dblogin
print "<div class=\"texte\"><h1>Visualisation de la fiche de position d'un élève</h1></div>";
?>

<br><br>


<?php

print "<div class=\"texte\"><center><br><br><b>Sélectionner la classe de l'élève :<b><br><br></center></div>";
print "<center><select size=\"6\" STYLE=\"background:#F0E4C8;color:#000080\" NAME=\"classe\">";

list ($idpers)= isauth();
$filter = "(cn=Classe_*$group)";
$groups=search_groups($filter);

for ($loop=0; $loop < count($groups); $loop++)
  {
    echo "<option value=\"".$groups[$loop]["cn"]."\">".$groups[$loop]["cn"].",&nbsp;".$groups[$loop]["description"]."</option>";
  }

print "</select></center>";

?>

<br><br><br>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  <td width="200"ALIGN="MIDDLE"><IMG SRC="Images/ping.png"><BR>
  <input type="submit" name="button" value="Valider">
  </td>
</table>


<?php mysql_close(); ?>

</form>
</body>
</html>
