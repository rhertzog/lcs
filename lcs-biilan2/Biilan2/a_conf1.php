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
<meta http-EQUIV="Refresh" CONTENT="2; url=vide.html">
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

if(empty($_POST["etab"]))//on vérifie avec empty si les champs sont vides
	{print "<div class=avertissement><br><br><br><br>Veuillez faire un choix.</div>";} 			//si un des champs n'est pas rempli, message d'erreur
else   //table recens_tbl
	{$nb ="$nbdmd";
	$etabap="$_POST[etab]";
	if ($_POST["etab"]=="Ecole") {$i ="items_eco_tbl"; $d ="disc_eco_tbl";}
	else {if ($_POST["etab"]=="College") {$i ="items_clg_tbl"; $d ="disc_clg_tbl";}
	else {if ($_POST["etab"]=="Lycee") {$i ="items_lyc_tbl"; $d ="disc_lyc_tbl";}
	else {}}}
	
	$m ="$mel";
	$j ="$justif";		
	$p ="0.8";	
	
	$fichier = "config2.php" ;  							//declaration des noms des fichiers
	$fp = fopen($fichier,'w');								//ouverture du fichier en écriture
	fwrite($fp,"<?"); 										//ecriture dans le fichier
	fwrite($fp,"\n"); 										//passage à la ligne
	fwrite($fp,"\$items=\"$i\";");				 		
	fwrite($fp,"\n"); 													
	fwrite($fp,"\$discip=\"$d\";"); 						
	fwrite($fp,"\n"); 													
	fwrite($fp,"\$etab=\"$etabap\";");	 		
	fwrite($fp,"\n"); 													
	fwrite($fp,"\$nbdmd=\"$nb\";"); 				
	fwrite($fp,"\n"); 																								
	fwrite($fp,"\$mel=\"$m\";");	
	fwrite($fp,"\n"); 	 						
	fwrite($fp,"\$justif=\"$j\";");	 						fwrite($fp,"\n");	 						
	fwrite($fp,"\$pourcentage=\"$p\";");	 						fwrite($fp,"\n");		
	fwrite($fp,"?>"); 												
	fwrite($fp,"\n"); 													
	fclose($fp);
	
	if ($etabap=="Lycee") {$typetab="Lycée";}
	if ($etabap=="Ecole") {$typetab="École";}
	if ($etabap=="College") {$typetab="Collège";}
	print "<div class=confirm><br><br><br>Biilan est configuré en mode <b>$typetab</b><br><br><br></div>";
	}

mysql_close();
?>

</div>
</body>
</html>
