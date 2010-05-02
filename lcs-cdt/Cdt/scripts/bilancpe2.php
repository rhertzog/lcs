<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.0 du 29/04/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script bilan absences pour cpe-
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); 

session_name("Cdt_Lcs");
@session_start();
include "../Includes/functions2.inc.php";
include "../Includes/fonctions.inc.php";
//fichiers nécessaires à l'exploitation de l'API
	$BASEDIR="/var/www";
	//include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
	include "$BASEDIR/Annu/includes/ldap.inc.php";
	include "$BASEDIR/Annu/includes/ihm.inc.php";  
	
//si la page est appelée par un utilisateur non identifié ou non autorise
if (!isset($_SESSION['login'])) exit;
elseif ((ldap_get_right("Cdt_is_cpe",$_SESSION['login'])=="N") && (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N")) exit;

$tsmp=time();
$tsmp2=time() - 7776000;//j-90

// Connexion à la base de données
require_once ('../Includes/config.inc.php');
//$tab_cla=array("2DE","1ES","TES");
if (isset($_GET['kr'])) 
	{
$tab_kren=split('-',$_GET['kr']);
	}


if (isset($_GET['dkr'])) 
	{
$Morceauc=split('/',$_GET['dkr']);
	$datsql= $Morceauc[2]."/".$Morceauc[1]."/".$Morceauc[0];
	
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" >
	<TITLE></TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
	<LINK rel="stylesheet" type="text/css" href="../style/style.css"  media="screen">
	<link rel="stylesheet" href="../style/style_imp.css" type="text/css" media="print" />
		<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<H1 class='title'></H1>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<fieldset id="field7">
<legend id="legende">Bilan des Absences & Retards  du <? echo $_GET['dkr'];?></legend>

<?

if (isset($_GET['dkr']))
	{
	$aucun="true";
	echo '<div id="abs-contenu">';
	//nom du creneau
	foreach ( $tab_kren as $cle => $valcren)
			{	
			$tab_cla=array();	
			//recherche des classes avec absents ou retardataires
			$rq = "SELECT DISTINCT classe FROM absences WHERE date='$datsql' AND ".$valcren."!='' ORDER BY date ASC";
			// lancer la requête
			$result = mysql_query ($rq) or die (mysql_error());
			if (mysql_num_rows($result)>0)
			{
			$aucun="false";
			echo "<h2>".$valcren."</h2>";
			echo "<UL>";
			$loop=0;
			while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
				{
				$tab_cla[$loop]= $enrg[0];$loop++;
				}
	 		foreach ( $tab_cla as $cle => $valcla)
					{
					echo "<li>".$valcla."";
					echo "<ul><li>Absents : ";
					//recherche des absents de la classe
					$rq = "SELECT uideleve FROM absences WHERE date='$datsql' AND ".$valcren."='A' AND classe='$valcla' ORDER BY id_abs ASC";
					// lancer la requête
					$result = mysql_query ($rq) or die (mysql_error());
					$nb = mysql_num_rows($result);
					if ($nb==0) echo "aucun";
					else  
					while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
						{
						list($user, $groups)=people_get_variables($enrg[0], false);
						echo $user["fullname"]."; ";
						}
					echo "<li>Retards : ";
					//recherche des retardataires//
					$rq = "SELECT uideleve FROM absences WHERE date='$datsql' AND ".$valcren."='R' AND classe='$valcla' ORDER BY id_abs ASC";
					// lancer la requête
					$result = mysql_query ($rq) or die (mysql_error());
					$nb = mysql_num_rows($result);
					if ($nb==0) echo "aucun";
					else  
					while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
						{
						list($user, $groups)=people_get_variables($enrg[0], false);
						echo $user["fullname"]."; ";
						}
					echo "</ul>";
					}//fin each class
				echo "</ul>";
				}
			}//fin each creneau
	if ($aucun=="true") echo "Pas de donn&eacute;es pour le(s) cr&eacute;neau(x) s&eacute;lectionn&eacute;(s) !<BR>";
	echo '<div > <h5> N\'apparaissent que les classes pour lesquelles l\'appel a &eacute;t&eacute; fait !</h5></div>'; 

	echo "<SCRIPT LANGUAGE=\"JavaScript\">
		document.write('<div id=\"abs-bt\"><A HREF=\"javascript:window.print()\" id=\"bt-imp\"></A>');
		document.write('<A HREF=\"javascript:window.close()\" id=\"bt-close\"></A></div>');
	</SCRIPT>";
	echo '</div>';
	}
?>
</fieldset>	
</FORM>
</BODY>
</HTML>



