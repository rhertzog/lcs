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

<form method="post" action="p_modif3.php">

<div class="texte"><h1>Modification d'une fiche de position</h1><br></div>
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
$dbnom="$user[nom]";            	    // on stocke le nom du prof dans la variable $dbnom
$dblogin="$user[uid]";                  // on stocke le login du prof dans la variable $dblogin
$date=date("d m Y");			    	// on stocke la date dans la variable $date
$uid="$_POST[login]";                  	// on stocke le login de l'élève dans la variable $uid

$site = "$etab.php";
if ($etab=="Lycee") {$typetab="Lycée"; $i_etab="L";}
if ($etab=="Ecole") {$typetab="École"; $i_etab="E";}
if ($etab=="College") {$typetab="Collège"; $i_etab="C";}


list($user, $groups)=people_get_variables($_POST[login], true);
$dbeleve="$user[fullname]";       // on stocke le nom de l'eleve dans la variable $dbeleve

list($user, $groups)=people_get_variables($_POST[login], true);
  if ( count($groups) )
    {
    for ($loop=0; $loop < count ($groups) ; $loop++)
      {
      if ( ereg("^Classe", $groups[$loop]["cn"]) )
        {$dbclasse="".$groups[$loop]["cn"]."";}    // on stocke la classe de l'élève dans la variable $dbclasse
      }
    }
    
    
    
if(empty($_POST["login"]))
  {die ("<div class=\"avertissement\"><br><br><br>Veuillez choisir un élève<br><br><br></div>");}
  else
  {
	print "<input type=\"HIDDEN\" name=\"login\" value=\"$uid\">";
	$req1 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
	while ( $resultat1 = mysql_fetch_array($req1))
		{ 	
			print"<h2><b>&nbsp;&nbsp;&nbsp;&nbsp;Domaine $resultat1[num] : $resultat1[domaine].</b></h2>";	
			$req2 = mysql_query("SELECT id,domaine,num,val FROM $items WHERE domaine LIKE $resultat1[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
		while ( $resultat2 = mysql_fetch_array($req2))
			{
				
				    $affich ="$i_etab.$resultat1[num].$resultat2[num]"; // on stocke la compétence
					$chekboxname = $resultat1[num].$resultat2[num];					
					//recherche si cpt est validée
					$req20 = mysql_query("SELECT login,cpt FROM bii_tbl WHERE (login LIKE '$uid' and cpt like '$affich')") or die ("erreur req ".mysql_err());
					$resultat20 = mysql_fetch_array($req20);
					$exist = mysql_numrows($req20);
					//print"nb = : $chekboxname";
					if ($exist)
						{
						//Si trouvée on affiche la cpt avec une coche verte
						print "&nbsp;&nbsp;<INPUT TYPE=CHECKBOX NAME=\"$chekboxname\"  value=\"1\" >&nbsp;&nbsp;$affich&nbsp;<img SRC=\"Images/enab.gif\">&nbsp;&nbsp;&nbsp;";						
						}
					else
						{
						//Si pas trouvée on affiche la cpt avec une croix rouge
						print "&nbsp;&nbsp;<INPUT TYPE=CHECKBOX NAME=\"$chekboxname\" value=\"1\">&nbsp;&nbsp;$affich&nbsp;<img SRC=\"Images/disab.gif\">&nbsp;&nbsp;&nbsp;";	
						}
				}			
print "<br><br>";
		} 
   }
?>

<br>
<table border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td valign="middle">
			<select STYLE="background:#F0E4C8;color:#000080" NAME="action" >
				<option VALUE="ok">Valider des compétences</option>
				<option VALUE="ko">Annuler des compétences</option>
			</select>
		</td>
		<td width="200"ALIGN="MIDDLE"><IMG SRC="Images/ping.png"><BR>
 			<input type="submit" name="button" value="Confirmer">
  		</td>
  	</tr>
</table>

<?php mysql_close(); ?>

</form>
</div>
</body>
</html>
