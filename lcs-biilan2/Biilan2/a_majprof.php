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

<form method="post" action="a_majprof1.php">
<div class="texte"><h1>Mise à jour de la base professeurs </h1><BR></div>

<?php
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\">L'accès à BiiLan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}
?>

<div class="remarque">Cette page vous permet d'importer tous les comptes de type Profs présents sur l'annuaire du LCS<br>
dans la liste des professeurs susceptibles de valider des compétences du B2I.<br><br>
Une fois la liste importée, vous aurez la possibilité de la modifier en utilisant la page  <i>"Modifier la liste professeurs" dans "Mise à jour annuelle"</i>.<br><br><br><br></div>

<table border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width="200" height="17"><b>Vous êtes certain ?</b></td>
		<td><input STYLE="background:#F0E4C8;color:#000080" type="text" name="certain"></td>
	</tr>
</table>

<br><br>

<table WIDTH=100%>
	<tr></tr><td ALIGN="MIDDLE"><IMG SRC="Images/ping.png"><BR><input type="submit" name="button" value="Importer les données"></tr>
</table>

<br><br><br>

<div class="remarque"><center>Si vous êtes certain de vouloir importer les données,<br>répondez <B>oui</B> à la question.<br></center></div>
</form>

</div>
</body>
</html>
