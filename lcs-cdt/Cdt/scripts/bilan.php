<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script bilan absences -
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); 
session_name("Cdt_Lcs");
@session_start();

//si la page est appelée par un utilisateur non identifié
if (!isset($_SESSION['login']) )exit;

//si la page est appelée par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;


include "../Includes/functions2.inc.php";
include "../Includes/fonctions.inc.php";
$tsmp=time();
$tsmp2=time() - 7776000;//j-90

//fichiers nécessaires à l'exploitation de l'API
	$BASEDIR="/var/www";
	//include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
	include "$BASEDIR/Annu/includes/ldap.inc.php";
	include "$BASEDIR/Annu/includes/ihm.inc.php";  



// Connexion à la base de données
require_once ('../Includes/config.inc.php');

if (isset($_GET['fn'])) $nom =$_GET['fn'];
if (isset($_POST['nomeleve'])) $nom = $_POST['nomeleve'];
if (isset($_GET['cl']) || isset($_POST["klas"]))
	{
	if (isset($_GET['cl'])) $nom =$_GET['cl'];
	if (isset($_POST["klas"])) $nom =$_POST["klas"];
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
				$rq4= "SELECT count(*) FROM absences WHERE uidprof='{$_SESSION['login']}'  AND  $val='A'  AND uideleve='$potache' AND date >='$dtajadebut' AND date<='$dtajafin' ";
				$result4 = @mysql_query ($rq4) or die (mysql_error()); 
				while ($nb = mysql_fetch_array($result4, MYSQL_NUM)) 
 					{
 					 $nbabs+=$nb[0];
 					}
 			
 				$rq5= "SELECT count(*) FROM absences WHERE uidprof='{$_SESSION['login']}'  AND  $val='R'  AND uideleve='$potache' AND date >='$dtajadebut' AND date<='$dtajafin' ";
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
	S1,motifS1,S2,motifS2,S3,motifS3,S4,motifS4,S5,motifS5,date FROM absences WHERE  uidprof='{$_SESSION['login']}' 
	AND uideleve='$potache' AND date >='$dtajadebut' AND date<='$dtajafin' ORDER BY date ASC";
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
if (!isset($_POST['datejavac_dif'])) 
	{	
//date au jour courant

	$datjc_dif=getdate($tsmp);
	$jo_dif=date('d',$datjc_dif['0']);
	$mo_dif=date('m',$datjc_dif['0']);
	$ann_dif=date('Y',$datjc_dif['0']);
	$dtajac_dif=$jo_dif."-".$mo_dif."-".$ann_dif;
	$dtajafin= $ann_dif."-".$mo_dif."-".$jo_dif;
	}
	
else
	{
	
	$Morceauc=split('-',$_POST['datejavac_dif']);
	$jour_c=$Morceauc[0];
	$mois_c=$Morceauc[1];
	$an_c=$Morceauc[2];
	$dtajafin= $an_c."-".$mois_c."-".$jour_c;
	$dtajac_dif=$jour_c."-".$mois_c."-".$an_c;
	$mesdatefin= " au ". $dtajac_dif;
	
	}
if (!isset($_POST['datejavaf_dif'])) 
	{	
	
	//date à j-90
	$datjf_dif=getdate($tsmp2);
	$jof_dif=date('d',$datjf_dif['0']);
	$mof_dif=date('m',$datjf_dif['0']);
	$annf_dif=date('Y',$datjf_dif['0']);
	$dtajaf_dif=$jof_dif."-".$mof_dif."-".$annf_dif;
	$dtajadebut= $annf_dif."-".$mof_dif."-".$jof_dif;
		
	}
else
	{
	$Morceauf=split('-',$_POST['datejavaf_dif']);
	$jour_f=$Morceauf[0];
	$mois_f=$Morceauf[1];
	$an_f=$Morceauf[2];
	$dtajadebut= $an_f."-".$mois_f."-".$jour_f;
	$dtajaf_dif=$jour_f."-".$mois_f."-".$an_f;
	$mesdatedebut= " du ". $dtajaf_dif;
	}



?></font>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<fieldset>
<legend>
<FONT FACE="Cursive" color="#486591">  &nbsp;&nbsp;Bilan des Absences & Retards  de  <FONT color="#FF7F50"><? echo $nom;?></font>
<? echo $mesdatedebut . $mesdatefin ?> &nbsp;&nbsp;</FONT>
</legend>

<?
//affichage du formulaire


if (!isset($_POST['Valider'])) 
	{
	//choix période
	
		echo '<H4> Pour la p&eacute;riode du <input onclick="ds_sh(this);" size="9" name="datejavaf_dif" value="'.$dtajaf_dif.'"readonly="readonly" style="cursor: text"/>';
		echo '&nbsp;&nbsp;&nbsp;au &nbsp;&nbsp;&nbsp;<input onclick="ds_sh(this);" size="9" name="datejavac_dif" value="'.$dtajac_dif.'"readonly="readonly" style="cursor: text"/>
		<INPUT name="eleve" type="hidden" " value="'.$_GET['uid'].'"/><INPUT name="nomeleve" type="hidden" " value="'.$nom.'"/>';
		if (isset($_GET['cl'])) 
			{
			echo ' <INPUT name="klas" type="hidden" " value="'.$_GET['cl'].'"/>';
			
			}
		echo '<input type="submit" name="Valider" value="Valider" ></H4>';
	}
//si clic sur le bouton Valider
if ((isset($_POST['Valider']))||(isset($_GET['dd'])))
	{
	echo "<H6 class='perso'>";
	if (isset($_POST["klas"]))
		{
		for ($loop=0; $loop<count($users);$loop++) {
		
		echo "<B>".$users[$loop]["fullname"] ."</b>  : ";
		affiche_abs($users[$loop]["uid"]);
		echo"<br>";
		}
		}
	else
		{
		affiche_abs($_POST["eleve"]);
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



