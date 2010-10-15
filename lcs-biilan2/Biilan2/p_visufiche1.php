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
<form method="post" action="p_visufiche2.php">

<?php
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

require ("config.php");
require ("config2.php");

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

list ($idpers,$login) = isauth();
if ($idpers == "0") {die ("<div class=\"remarque\">Vous n'êtes pas authentifié sur le LCS ! <br><br></div>");}
else {if (is_eleve($login)=="false"){die("<div class=\"remarque\">L'accès à BiiLan est ouvert, toutefois<br>n'&eacute;tant pas professeur, vos droits sont restreints<br><br></div>");}}

list ($idpers,$uid)= isauth();
list($user, $groups)=people_get_variables($uid, true);
$dbfullname="$user[fullname]"; 	  		// on stocke le nom complet du prof dans la variable $dbfullname
$dbnom="$user[nom]";            			   // on stocke le nom du prof dans la variable $dbnom
$dblogin="$user[uid]";            			  		// on stocke le login du prof dans la variable $dblogin
$date=date("d m Y");									// on stocke la date dans la variable $date

print "<div class=\"texte\"><h1>Visualisation de la fiche de position d'un élève</h1></div>";



if(empty($_POST["classe"]))
  {die ("<div class=\"avertissement\"><br><br><br>Veuillez choisir une classe<br><br><br></div>");}
else
  {
  print "<div class=\"texte\"><center><br><br><b>Sélectionner un élève de la $_POST[classe]<b><br><br></center></div>";
  print "<center><select size=\"10\" STYLE=\"background:#F0E4C8;color:#000080\" NAME=\"login\">";

  $filter_classe="(cn=$_POST[classe])";
	$uids = search_uids ($filter_classe, "full");
	$people = search_people_groups ($uids,$filter_people,"group");
	for ($loop=0; $loop < count($people); $loop++) 
		{echo "<li><option value=\"".$people[$loop]["uid"]."\">".$people[$loop]["fullname"]."</option><br>";}
	 print "</select></center>";
  print "</select></center>";
  }
?>

<br><br><br>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  <td width="200"ALIGN="MIDDLE"><IMG SRC="Images/ping.png"><BR>
  <input type="submit" name="button" value="Valider">
  </td>
</table>




<?php mysql_close(); ?>
</form>
</div>
</body>
</html>
