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

<form method="post" action="p_lot2.php">

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
$dbnom="$user[nom]";            		// on stocke le nom du prof dans la variable $dbnom
$dblogin="$user[uid]";           	  	// on stocke le login du prof dans la variable $dblogin
$date=date("d m Y");					// on stocke la date dans la variable $date

$site = "$etab.php";
if ($etab=="Lycee") {$typetab="Lycée"; $i_etab="L";}
if ($etab=="Ecole") {$typetab="École"; $i_etab="E";}
if ($etab=="College") {$typetab="Collège"; $i_etab="C";}

print "<div class=\"texte\"><h1>Validation par lot</h1></div>";



if(empty($_POST["classe"]))
  {die ("<div class=\"avertissement\"><br><br><br>Veuillez choisir une classe<br><br><br></div>");}
else
  {
 	print "<div class=\"texte\"><left>&nbsp;&nbsp;&nbsp;&nbsp;<b>1 - Sélectionner ci-dessous les élèves concernés par la validation :</b><br><br></center>";
	print "<center><select size=\"10\" STYLE=\"background:#F0E4C8;color:#000080\" NAME=\"loginelev[]\" MULTIPLE>";
  	$filter_classe="(cn=$_POST[classe])";
	$uids = search_uids ($filter_classe, "full");
	$people = search_people_groups ($uids,$filter_people,"group");
	for ($loop=0; $loop < count($people); $loop++) 
		{echo "<li><option value=\"".$people[$loop]["uid"]."\">".$people[$loop]["fullname"]."</option><br>";}
		print "</select></center>";
		print "</select></center>";
		print "<br><br></select></center>";
		print "<div class=\"texte\"><left>&nbsp;&nbsp;&nbsp;&nbsp;<b>2 - Cocher ci dessous les compétences à valider :</b></center>";

		
		$req1 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
while ( $resultat1 = mysql_fetch_array($req1))
	{
		print"<h2><b>&nbsp;&nbsp;&nbsp;&nbsp;Domaine $resultat1[num] : $resultat1[domaine].</b></h2>";
		$req2 = mysql_query("SELECT id,domaine,num,val FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
		while ( $resultat2 = mysql_fetch_array($req2))
			{
				$affich ="$i_etab.$resultat2[domaine].$resultat2[num]"; // on stocke la compétence				
				$chekboxname = $resultat2[domaine].$resultat2[num];					
					 // on affiche la compétence	 			
				print "&nbsp;&nbsp;<INPUT TYPE=CHECKBOX NAME=\"$chekboxname\"  value=\"1\" >&nbsp;&nbsp;$affich&nbsp;";						
				}	
			}

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
