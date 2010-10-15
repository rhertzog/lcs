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
<title>Biilan</title>
<link rel="stylesheet" href="Style/style.css">
</head>
<body background="Images/texture.jpg">

<form method="post" enctype='multipart/form-data' action="a_import1.php">

<div class="texte"><h1>Restauration des donn�es</h1><BR></div>

<?php
 require_once ('/var/www/lcs/includes/headerauth.inc.php');
 include "/var/www/Annu/includes/ihm.inc.php";
 list ($idpers,$username) = isauth();
 if (is_admin("Biilan2_is_admin",$username)!="Y") 
 {die("<div class=\"remarque\">L'acc�s � Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}
?>

<div class="remarque"><center><br><br></center></div>


<br><br>

<table border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width="220" height="17" align="right"><b>Fichier sql :&nbsp;&nbsp;&nbsp;</b></td>
		<td width="220"><input STYLE="background:#F0E4C8;color:#000080" type="file" name="fichier"></td>
	</tr>
	<tr><td><br></td><td></td></tr>
	<tr>
		<td align="right"><b>Vous �tes certain ?&nbsp;&nbsp;&nbsp;</b></td>
		<td><input STYLE="background:#F0E4C8;color:#000080" type="text" name="certain"></td>
	</tr>
</table>

<br><br>

<table WIDTH=100%>
	<tr>
		<td ALIGN="MIDDLE"><IMG SRC="Images/ping.png"><BR><input type="submit" name="button" value="Restaurer les donn�es"></td>
	</tr>
</table>

<br><br>

<div class="remarque"><center>Si vous �tes certain de vouloir restaurer les donn�es,<br>
r�pondez <B>oui</B> � la question.<BR>
</center></div>

</form>
</body>
</html>
