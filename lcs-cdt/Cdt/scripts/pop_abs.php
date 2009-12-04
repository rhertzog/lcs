<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.0 du 25/10/2009
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de consultation des absences hebdomadaires -
			_-=-_
	
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
//fichiers nécessaires à l'exploitation de l'API
	$BASEDIR="/var/www";
	//include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
	include "$BASEDIR/Annu/includes/ldap.inc.php";
	include "$BASEDIR/Annu/includes/ihm.inc.php";  
	
//si la page est appelée par son URL
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit;
elseif (isset($_GET['uid']))
	{
	//test si uid est un enfant de parent
	if (isset($_SESSION['parentde']))
		{
		$parent_ok="false";	
		foreach ( $_SESSION['parentde'] as $key => $value)
			{
			if (in_array($_GET['uid'], $value)) $parent_ok="true";
			}
		}
		//exit si l'uid passe n'est pas autorise 
		if (
			(($_SESSION['cequi']!="eleve") || ($_SESSION['login'] != $_GET['uid'])) 
			&& ($_SESSION['cequi']!="prof") 
			&& (ldap_get_right("Cdt_is_cpe",$_SESSION['login'])=="N")
			&& (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N")
			&& ( !isset($_SESSION['parentde']) || $parent_ok=="false")
			) exit;
	}
	
include ("../Includes/config.inc.php");
include ("../Includes/fonctions.inc.php");
include ("../Includes/creneau.inc.php");

//mémorisation des paramètres POST classe et matière renvoyés par le formulaire	
if (isset($_GET['fn'])) $nom =$_GET['fn'];
if (isset($_POST['nomeleve'])) $nom = $_POST['nomeleve'];
if (isset($_GET['uid'])) $potache =$_GET['uid'];
if (isset($_POST['gamin'])) $potache = $_POST['gamin'];
?>
<HTML>
<HEAD>
<TITLE></TITLE>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</HEAD>
<BODY>

<?

//Détermination du jour courant
if (isset($_REQUEST['JourCourant'])) 
	{
	$JourCourant = $_REQUEST['JourCourant'];
	//semaine suivante
	if (isset($_REQUEST['suiv'])) 
		{
		$Lundi=DebutSemaine($JourCourant + 7 * 86400);
		} 
		//semaine précédente
		elseif (isset($_REQUEST['prec']))
			{
			//tableau hebdomadaire commençant au Lundi
			
				$Lundi=DebutSemaine($JourCourant - 7 * 86400);
				
			} 
		else 
		{
		$Lundi=DebutSemaine($JourCourant);
		}
	}
	else 
	{
		$Lundi=DebutSemaine();
		$la=time();
		//if (LeJour($la)=="dimanche") $Lundi-=3600 ;
		}
		
//mémorisation de la date courante
//if (isset($_GET['ladate'])) {$Lundi=$_GET['jc'];}
//
//fin de la semaine
$Samedi = $Lundi + 432000; //5 * 86400 
//Date du début de la semaine courante
$datdebut=date('Ymd',$Lundi);
//Date de la fin de la semaine courante
$datfin=date('Ymd',$Samedi);

$datd=getdate($Lundi);
$dtdebut= date('Y',$datd['0'])."/".date('m',$datd['0'])."/".date('d',$datd['0']);
$datf=getdate($Samedi );
$dtdfin= date('Y',$datf['0'])."/".date('m',$datf['0'])."/".date('d',$datf['0']);

//Recherche des absences /retards pour la semaine courante
$rq = "SELECT M1,M2,M3,M4,M5,S1,S2,S3,S4,S5,DATE_FORMAT(date,'%d'),DATE_FORMAT(date,'%m'),DATE_FORMAT(date,'%Y'), 
motifM1,motifM2,motifM3,motifM4,motifM5,motifS1,motifS2,motifS3,motifS4,motifS5 FROM absences WHERE  
 uideleve='$potache' AND date >='$dtdebut' AND date<='$dtdfin' ORDER BY date ASC";
$res = mysql_query($rq);
$nb = mysql_num_rows($res);
//si des absences/retards ont été programmés
if ($nb>0) 
	{
	//pour chaque absence/retard programmé
	while ($row = mysql_fetch_array($res, MYSQL_NUM)) 
		{
		//détermination du timestamp 
		$tsmp=mkTime(8,0,0,$row[11],$row[10],$row[12]);
		//tsmp=mkTime(8,0,0,$row[2],$row[1],$row[3]);
		// on parcourt les jours de la semaine
		for ($j=0; $j<=5; $j++)
			{
			$jour = $Lundi + $j * 86400;
			
			// on parcourt les heures de la  journée
			for ($h=0; $h<=9; $h++)
				{
				if ($jour==$tsmp)	
				{
				$plan[$j][$h] = $row[$h];
				$why[$j][$h]= $row[$h+13];
				} 
				
				}
			}
		}
	}
	
?>

<!-- affichage du calendrier hebdomadaire -->
<FORM action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" name="planning" id="planning">
<fieldset id="field7">
<legend id="legende"><?php echo $nom;?></legend>
<div id="abs-contenu">
	<!-- Affichage des boutons -->
	<INPUT name="JourCourant" type="hidden" id="JourCourant" value="<?php echo $Lundi;?>">
	<INPUT name="gamin" type="hidden"  value="<?php echo $potache;?>">
	<INPUT name="nomeleve" type="hidden"  value="<?php echo $nom;?>">
	<TABLE id="btn-plan-cdt">
		<TR>
			<TD width="150" align="center" ><INPUT type="submit" name="prec" value="<<" class="bt50"></TD>
			<TD width="150" align="center" class="hebdo"> <?echo datefr2($Lundi)?> - <?echo datefr2($Samedi);?></TD>
			<TD width="150" align="center" ><INPUT type="submit" name="suiv" value=">>" class="bt50"></TD>
		</TR>
	</TABLE>
		
	<TABLE id="plan-cdt" CELLPADDING=1 CELLSPACING=2>
		<thead> 
			<TH>  </TH>
<?php
// Affichage des jours et dates de la semaine en haut du tableau"j-M-Y", 
	for ($i=0; $i<=5; $i++) {
		$TS = $Lundi+$i*86400;
		echo '<td width="15%" > <font face="Arial" size="2" color="#FFFFFF" >'.LeJour($TS)."</TH>\n";
	}
?>
		</thead>
		<tbody>
			<tr>
<?php
	$horaire = array("M1","M2","M3","M4","M5","S1","S2","S3","S4","S5");
	for ($h=0; $h<=9; $h++) 
		{
		//Affichage de la désignation des créneaux horaires 
		echo "<TH>".$horaire[$h]."</TH>\n";
		 		
		//Affichage du contenu des créneaux horaires
		for ($j=0; $j<=5; $j++) 
			{
			if (isset($plan[$j][$h])) 
				{
				if ($plan[$j][$h]=="A") echo '<TD class="absence"><A href="" title="'.$why[$j][$h].'">A</a></TD>'."\n"; 
				elseif ($plan[$j][$h]=="R") echo '<TD class="retard"><A href="" title="'.$why[$j][$h].'">R</a></TD>'."\n";
				elseif ($plan[$j][$h]=="") echo '<TD class="libre">-</TD>'."\n"; 
				}	
			else echo '<TD class="libre">-</TD>'."\n"; 
							
			//cellules de séparation à la mi-journée	 	 	
				if (($h==4)&&($j==5)) echo '<TR border=0><TD class="mi-jour" colspan="7"></TD></TR>';	
				}	//b
			echo "</TR>\n";
		}
?>
</tbody>
</table>
</div>
<?
echo "<SCRIPT LANGUAGE=\"JavaScript\">
		document.write('<div id=\"abs-bt\"><A HREF=\"javascript:window.close()\" id=\"bt-close\"></A></div>');
	</SCRIPT>";
?>	
</fieldset>
</FORM>

<?
Include ('../Includes/pied.inc'); 
?>
</BODY>
</HTML>