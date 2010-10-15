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
<link rel="stylesheet" href="../Style/style.css">
</head>
<body>

<?php
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

require ("../config.php");

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}

print "<div class=\"texte\"><h1>Récupération des données à partir de Biilan</h1></div>";

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");



	$req0=mysql_query("SELECT login,result FROM resultat_tbl") ;
	while ( $resultat0 = mysql_fetch_array($req0))
				//recup login et resultat
			{
			$login=$resultat0[login] ;
			$resultat1=$resultat0[result] ;
			
			list($user, $groups)=people_get_variables($login, true);
			$nomcomplet="$user[fullname]";
			
			$req = ("SELECT id FROM bii_tbl WHERE login LIKE '$login'") or die ("erreur sql ".mysql_error());
			$resultat = mysql_query($req);
			$resultat2 = mysql_num_rows($resultat);
			
			if ($resultat1==$resultat2) 
				{
				print "<div class=\"affichok\">&nbsp;&nbsp;&nbsp;&nbsp;$nomcomplet : OK <br></div>";
				}
			else
				{
				list($user, $groups)=people_get_variables($login, true);
				if ( count($groups) )                                         // on stocke la classe de l'élève dans la variable $dbclasse
				        {for ($loop=0; $loop < count ($groups) ; $loop++)
				      { if ( ereg("^Classe", $groups[$loop]["cn"]) )
 				              {$classe="".$groups[$loop]["cn"]."";}    
 				     }
 				       }

				print "<div class=\"affichko\">&nbsp;&nbsp;&nbsp;&nbsp;$nomcomplet --- $classe --- login $login : <b>KO</b> (résultat = $resultat1 pour $resultat2 items validé(s)<br></div>";
				}			
		
			}



print "<br><br><br><h1>Opération terminée --- Bonne utilisation </h1>";


mysql_close();
?>
<br><br><br>
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form2" method="post" action="../index.php" target="_parent">
    <td width="200" height="17" align ="center"><input type="submit" style="width:300px" name="button" value="&nbsp;&nbsp;&nbsp;RETOUR VERS BIILAN&nbsp;&nbsp;&nbsp;"><br><br></td>
  </tr></form>
</table>

<br><br>


</body>
</html>
