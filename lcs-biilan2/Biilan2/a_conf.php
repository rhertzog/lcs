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

print "<div class=\"texte\"><h1>Configuration de Biilan </h1></div>";

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

?>

<br><br><br>

<form method="post" action="a_conf1.php">
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><td width="150"><input name="etab" value="Ecole" type="radio" <?php if ($etab=="Ecole") {print "checked";}?> >&nbsp;&nbsp;École</td><td rowspan="3" width="400">Sélectionner le type d'établissement&nbsp;&nbsp;&nbsp;<input type="image" src="Images/enabled.gif"></td></tr>
  <tr><td width="150"><input name="etab" value="College" type="radio" <?php if ($etab=="College") {print "checked";}?> >&nbsp;&nbsp;Collège</td></tr>
  <tr><td width="150"><input name="etab" value="Lycee" type="radio" <?php if ($etab=="Lycee") {print "checked";}?> >&nbsp;&nbsp;Lycée</td></tr>
</table>
</form>
				          
<br><br><center>----------------------------------------------------------------------------------</center><br><br>

<form method="post" action="a_nbcpt.php">
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="20" align="right">Nombre maximum de demandes   &nbsp;</td>
    <td><select size="1" STYLE="background:#F0E4C8;color:#000080" NAME="nb">
    <?php print "<option value=\"$nbdmd\" selected>$nbdmd</option>"; 
    	if($nbdmd=="1"){}else print " <option value=\"1\" >1</option>";
		if($nbdmd=="2"){}else print " <option value=\"2\" >2</option>";
		if($nbdmd=="3"){}else print " <option value=\"3\" >3</option>";
		if($nbdmd=="4"){}else print " <option value=\"4\" >4</option>";
		if($nbdmd=="5"){}else print " <option value=\"5\" >5</option>";
		if($nbdmd=="6"){}else print " <option value=\"6\" >6</option>";
		if($nbdmd=="7"){}else print " <option value=\"7\" >7</option>";
		if($nbdmd=="8"){}else print " <option value=\"8\" >8</option>";
     
     ?></td>
    <td  align="right">&nbsp;&nbsp;&nbsp;<input type="image" src="Images/enabled.gif"></td>
  </tr>
</table>
</form>
				          
<br><br><center>----------------------------------------------------------------------------------</center><br><br>

<form method="post" action="a_mel.php">
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><td width="150"><input name="mel" value="ok" type="radio" <?php if ($mel=="ok") {print "checked";}?>>&nbsp;&nbsp;Activer</td><td rowspan="2" width="600">la messagerie à destination des professeurs&nbsp;&nbsp;&nbsp;<input type="image" src="Images/enabled.gif"></td></tr>
  <tr><td width="150"><input name="mel" value="ko" type="radio"<?php if ($mel=="ko") {print "checked";}?>>&nbsp;&nbsp;Désactiver</td></tr>
</table>
</form>

<?php

if ($mel=="ok")
	{
	print"<form method=\"post\" action=\"a_justif.php\">";
	print"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">";
	print"  <tr><td width=\"150\"><input name=\"justif\" value=\"ok\" type=\"radio\""; 
 	if ($justif=="ok") {print "checked";}
	print">&nbsp;&nbsp;Activer</td><td rowspan=\"2\" width=\"600\">l'envoi de commentaires lors d'une demande de validation&nbsp;&nbsp;&nbsp;<input type=\"image\" src=\"Images/enabled.gif\"></td></tr>";
	print"  <tr><td width=\"150\"><input name=\"justif\" value=\"ko\" type=\"radio\"";
	if ($justif=="ko") {print "checked";}
	print">&nbsp;&nbsp;Désactiver</td></tr>";
	print"</table>";
	print"</form>";
	}
	
?>

</body>
</html>
