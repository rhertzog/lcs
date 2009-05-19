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

<div class="texte"><h1>Mise à jour de la base professeurs </h1><BR></div>

<?php
if(empty($_POST["certain"]))
	{print "<div class=avertissement>Veuillez répondre à la question.</div>";}
else
	{ $variable = $_POST["certain"];
	if($variable=='oui')
		{include "Includes/basedir.inc.php";
    	include "$BASEDIR/lcs/includes/headerauth.inc.php";
		include "$BASEDIR/Annu/includes/ldap.inc.php";
    	include "$BASEDIR/Annu/includes/ihm.inc.php";
    	require ("config.php");
    
    	$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
    	mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

		list ($idpers,$username) = isauth();
		if (is_admin("Biilan2_is_admin",$username)!="Y")
  			{die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}

    	$users = search_uids ("(cn=Profs)","half");
    	for ( $loop=0; $loop<count($users); $loop++ )
			{$uid=$users[$loop]["uid"];
      		list($user, $groups)=people_get_variables($uid, true);
      		$dbfullprof="$user[fullname]";
      		$dbprof="$user[nom]";
      		$req = mysql_query("SELECT fullprof FROM profs_tbl WHERE fullprof LIKE '$dbfullprof'") or die ("erreur sql ".mysql_error());
      		$verif= mysql_numrows($req);
		 	if(!$verif)
          		{mysql_query("INSERT Into profs_tbl (fullprof,prof) VALUES ('$dbfullprof','$dbprof')") or die ("erreur requête ".mysql_error());}
      		}

		print '<div class=confirm><br><br>Opération réalisée avec succès</div>';
		mysql_close();
	 	}
		else
    		{print '<div class=avertissement><br><br>Aucune action réalisée.</div>';}
	}
    
?>

<br><br><br>
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form1" method="post" action="a_majan.php">
    <td width="200" height="17" align ="center"><input type="submit" name="button" value="&nbsp;&nbsp;&nbsp;Retour vers page de mise à jour annuelle&nbsp;&nbsp;&nbsp;"><br><br></td>
  </tr></form>
</table>

</div>
</body>
</html>
