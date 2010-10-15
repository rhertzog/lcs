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
<meta name="generator" content="Bluefish 1.0.7">
<meta http-EQUIV="Refresh" CONTENT="3; url=vide.html">
<title>Gestion Administrative du B2I</title>
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

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}

$db=@mysql_connect($dbserver , $dbuser, $dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

print " <div class=\"texte\"><h1>Configuration de Biilan </h1><BR>";

$i ="$items";
$d ="$discip";
$e ="$etab";
$n="$nbdmd";
$m ="$_POST[mel]";
$j="$justif";
$p ="0.8";
	
$fichier = "config2.php" ;  						//declaration des noms des fichiers
$fp = fopen($fichier,'w');							//ouverture du fichier en écriture
fwrite($fp,"<?"); 									//ecriture dans le fichier
fwrite($fp,"\n"); 									//passage à la ligne
fwrite($fp,"\$items=\"$i\";");				 	
fwrite($fp,"\n"); 							
fwrite($fp,"\$discip=\"$d\";"); 					
fwrite($fp,"\n"); 													
fwrite($fp,"\$etab=\"$e\";");	 	
fwrite($fp,"\n"); 													
fwrite($fp,"\$nbdmd=\"$n\";"); 	
fwrite($fp,"\n"); 													
fwrite($fp,"\$mel=\"$m\";");		 					fwrite($fp,"\n"); 
fwrite($fp,"\$justif=\"$j\";");					fwrite($fp,"\n"); 												
fwrite($fp,"\$pourcentage=\"$p\";");	 					fwrite($fp,"\n");	
fwrite($fp,"?>"); 										
fwrite($fp,"\n"); 
fclose($fp);
	
if ($m=="ok") {print "<div class=confirm><br><br><br>La messagerie à destination des professeurs est<b> activée </b><br><br><br></div>";}
else {print "<div class=confirm><br><br><br>La messagerie à destination des professeurs est<b> désactivée </b><br><br><br></div>";}

mysql_close();
?>

</div>
</body>
</html>
