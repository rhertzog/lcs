<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
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
elseif ((ldap_get_right("Cdt_is_cpe",$_SESSION['login'])=="N") &&  (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N")) exit;

$tsmp=time();
$tsmp2=time() - 7776000;//j-90

// Connexion à la base de données
require_once ('../Includes/config.inc.php');

if (isset($_GET['fn'])) $nom =$_GET['fn'];

if (isset($_GET['kl']))
	{
	 $nom =$_GET['kl'];
	$filtre="cn=".$nom;
	$grp_cl=search_groups($filtre);
	if (count($grp_cl[0])==0) $grp_cl=search_groups("cn=Classe_*".$nom); 
	$uids = search_uids ("(cn=".$grp_cl[0]["cn"].")", "half");
	$users = search_people_groups ($uids,"(sn=*)","cat");

}

function affiche_abs($potache) {
global 	$dtajadebut, $dtajafin;
	//$potache=$_POST["eleve"];
				$horaire = array("M1","M2","M3","M4","M5","S1","S2","S3","S4","S5");	
			$nbabs=0;$nbrtd=0;
			foreach ( $horaire as $cle => $val)
	  			{
				$rq4= "SELECT count(*) FROM absences WHERE  $val='A'  AND uideleve='$potache' AND date >='$dtajadebut' AND date<='$dtajafin' ";
				$result4 = @mysql_query ($rq4) or die (mysql_error()); 
				while ($nb = mysql_fetch_array($result4, MYSQL_NUM)) 
 					{
 					 $nbabs+=$nb[0];
 					}
 			
 				$rq5= "SELECT count(*) FROM absences WHERE   $val='R'  AND uideleve='$potache' AND date >='$dtajadebut' AND date<='$dtajafin' ";
				$result5 = @mysql_query ($rq5) or die (mysql_error()); 
				while ($nb = mysql_fetch_array($result5, MYSQL_NUM)) 
 					{
 					 $nbrtd+=$nb[0];
 					}	
				}//fin foreach $horaire	
 			
 			if ($nbabs>0)
 				{
 				 echo $nbabs."h d'absence  - ";
 				}
 				else echo "Aucune absence - ";
 			if ($nbrtd>0)
 				{
 				if ($nbrtd>1) echo $nbrtd." retards  <BR>"; else echo $nbrtd." retard  <BR>";
 				}
				else echo "Aucun retard <BR>";
	
	$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),M1,motifM1,M2,motifM2,M3,motifM3,M4,motifM4,M5,motifM5,
	S1,motifS1,S2,motifS2,S3,motifS3,S4,motifS4,S5,motifS5,date FROM absences WHERE   uideleve='$potache' AND
	 date >='$dtajadebut' AND date<='$dtajafin' ORDER BY date ASC";
	// lancer la requête
	$result = mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ? 
	$nb2 = mysql_num_rows($result); 
	
	while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
	  { 
	  $typmat ="";
	  foreach ( $horaire as $cle => $val)
	  			{
	  			if ($ligne[2*$cle+1]==A) $typmat .=" : absence en $val ( ".$ligne[2*$cle+2]." )";
	  			elseif ($ligne[2*$cle+1]==R) $typmat .=" : retard en $val ( ".$ligne[2*$cle+2]." )";
	  			else $typmat .="";
	  			}
	 
	  echo "&nbsp;&nbsp;- Le ".LeJour(strToTime($ligne[21]))." ".$ligne[0]." " . $typmat ."<BR>";
	  }
		  }//fin function
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=windows-1252">
	<TITLE>module <(+_-)/> n°2</TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
	<LINK rel="stylesheet" type="text/css" href="../style/style.css"  media="screen">
	<link rel="stylesheet" href="../style/style_imp.css" type="text/css" media="print" />
</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<div style="position:absolute; top:0px; left:100px;z-index:3;">
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
<tr><td id="ds_calclass">
</td></tr>
</table>
</div>
<SCRIPT language="Javascript" src="../Includes/calend.js"></script>

<H1 class='title'></H1>

<?

if (isset($_GET['df'])) 
	{
$Morceauc=split('-',$_GET['df']);
	$jour_c=$Morceauc[0];
	$mois_c=$Morceauc[1];
	$an_c=$Morceauc[2];
	$dtajafin= $an_c."-".$mois_c."-".$jour_c;
	$dtajac_dif=$jour_c."-".$mois_c."-".$an_c;
	$mesdatefin= " au ". $dtajac_dif;	
	}
	

	if (isset($_GET['dd'])) 
	{
	$Morceauf=split('-',$_GET['dd']);
	$jour_f=$Morceauf[0];
	$mois_f=$Morceauf[1];
	$an_f=$Morceauf[2];
	$dtajadebut= $an_f."-".$mois_f."-".$jour_f;
	$dtajaf_dif=$jour_f."-".$mois_f."-".$an_f;
	$mesdatedebut= " du ". $dtajaf_dif;
	}
	

?></font>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
<fieldset>
<legend>
<FONT FACE="Cursive" color="#486591">  &nbsp;&nbsp;Bilan des Absences & Retards  de  <FONT color="#FF7F50"><? echo $nom;?></font>
<? echo $mesdatedebut . $mesdatefin ?> &nbsp;&nbsp;</FONT>
</legend>

<?

if (isset($_GET['dd']))
	{
	echo "<H6 class='perso'>";
	if (isset($_GET['kl']))
		{
		for ($loop=0; $loop<count($users);$loop++) {
		
		echo "<B>".$users[$loop]["fullname"] ."</b>  : ";
		affiche_abs($users[$loop]["uid"]);
		echo"<br>";
		}
		}
	else
		{
		affiche_abs($_GET['uid']);
		}
	echo "<BR>
	<SCRIPT LANGUAGE=\"JavaScript\">
	document.write('<A HREF=\"javascript:window.print()\"><B> Imprimer</A>&nbsp;&nbsp;&nbsp;');
	document.write('<A HREF=\"javascript:window.close()\">Fermer</B></A>');
	
</SCRIPT></H6>
	";
	}
?>
</fieldset>	
</FORM>
</BODY>
</HTML>



