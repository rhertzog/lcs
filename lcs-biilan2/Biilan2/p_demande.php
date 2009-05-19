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
<title>Gestion Administrative du B2I au coll�ge</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
 <link rel="stylesheet" href="Style/style.css">
</head>
<body>

<form method="post" action="p_demande1.php">
<div class="texte"><h1>Liste des demandes de validation en attente</h1><BR></div>

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
if ($idpers == "0") {die ("<div class=\"remarque\">Vous n'�tes pas authentifi� sur le LCS ! <br><br></div>");}
else {if (is_eleve($login)=="false"){die("<div class=\"remarque\">L'acc�s � BiiLan est ouvert, toutefois<br>n'&eacute;tant pas professeur, vos droits sont restreints<br><br></div>");}}

list ($idpers,$uid)= isauth();
list($user, $groups)=people_get_variables($uid, true);
$dbfullname="$user[fullname]"; 	  					// on stocke le nom complet du prof dans la variable $dbfullname
$dblogin="$user[uid]";            			 				   // on stocke le login du prof dans la variable $dblogin
if ($etab=="Lycee") {$comp="L";}
if ($etab=="Ecole") {$comp="E";}
if ($etab=="College") {$comp="C";}

$req = ("SELECT fullprof from dmd_tbl WHERE fullprof LIKE '$dbfullname'") or die ("erreur sql ".mysql_error());
$resultat = mysql_query($req);
$nbresultat = mysql_num_rows($resultat);

if ($nbresultat==0)
  {print "<div class=confirm><br><br>Il n'y a pas de demande en cours<br><br><br></div>";die;}
else
  {if ($nbresultat==1) {$s="demande";} else {$s="demandes";}
	print "<div class=avertissement>Vous avez actuellement $nbresultat $s en cours<br><br><br></div>";}
?>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td><b>S�lectionner les demandes � traiter :</b><br><br></td>
  </tr>
  <tr>
		<td><select size="7" STYLE="background:#F0E4C8;color:#000080" NAME="val[]" MULTIPLE>
            <?php
            $req = mysql_query("SELECT id,login,fulleleve,classe,cpt,fullprof,date from dmd_tbl WHERE fullprof LIKE '$dbfullname' order by cpt ASC") or die ("erreur sql ".mysql_error());
            while ( $resultat = mysql_fetch_array($req))
            {echo "<option value=\"" . $resultat["id"] . "\">Comp�tence " . $resultat["cpt"] . " demand�e le " . $resultat["date"] . " par  " . $resultat["fulleleve"] . "   (" . $resultat["classe"] . ") </option>";}
            ?>
            </td>	</tr>
	<tr>
		<td align="center" STYLE="color:#000080;"><a href="c_cndp.php"><br>Rappel des comp�tences.</a></td>
	</tr>
	<tr><td><br></td></tr>

</table>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  	<tr>
		<td valign="middle"><select STYLE="background:#F0E4C8;color:#000080" NAME="action" >
        		<option VALUE="ok">Valider la demande</option>
        		<option VALUE="ko">Ne pas valider la demande</option>
        		<option VALUE="no">N'est pas en mesure de valider la demande</option>
        		</select></td>
		<td></td>
		<td width="200"ALIGN="MIDDLE"><IMG SRC="Images/ping.png"><BR>
  			<input type="submit" name="button" value="Confirmer"></td>		
  	</tr>
</table>

<br>

<?php mysql_close(); ?>

</form>
</body>
</html>
