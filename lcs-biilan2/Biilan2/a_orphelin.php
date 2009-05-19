<?php
/* ==========================================================
   Projet LCS : Linux Communication Server
   Plugin "Biilan : Gestion administrative du B2i"
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
<form method="post" action="a_orphelin1.php">

<?php
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

require ("config.php");
require ("config2.php");

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'acc�s � Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base"); 
 
if ($etab=="Lycee") {$typetab="Lyc�e";}
if ($etab=="Ecole") {$typetab="�cole";}
if ($etab=="College") {$typetab="Coll�ge";}

print "<div class=\"texte\"><h1>Mise � Jour Annuelle de Biilan $typetab --- Suppression des comptes orphelins</h1></div>"; 
?>  
  
<div class="remarque"><br><br><br>Cette page vous permet de supprimer de la base les �l�ves qui ont quitt� l'�tablissement<br><br><br><br></div>

<table border="0" cellspacing="0" cellpadding="0" align="center">  <tr>
    <td width="200" height="17"><b>Vous �tes certain ?</b></td>
    <td><input STYLE="background:#F0E4C8;color:#000080" type="text" name="certain"></td>
  </tr>
</table>

<br><br><br><br>

<table WIDTH=100%>
  <tr>
    <td ALIGN="MIDDLE"><IMG SRC="Images/ping.png"><BR>
    <input type="submit" name="button" value="Mettre � jour la base">
  </tr>
</table>

<br><br><br>

<div class="remarque"><center>Si vous �tes certain de vouloir supprimer les comptes orphelins,<br>r�pondez <B>oui</B> � la question.<br>
</center></div>

</div>
</form>
</body>
</html>
