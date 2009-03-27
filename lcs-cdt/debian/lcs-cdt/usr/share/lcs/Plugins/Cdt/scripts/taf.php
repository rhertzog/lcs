<?
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script du Travail A Faire ELEVE -
   modifié le 12/01/2007
			_-=-_
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit;
include ('../Includes/data.inc.php');
//initialisation 
$tsmp=time();

function is_authorized($x) {
$flg="false";
foreach ($_SESSION['saclasse'] as $clé => $valeur)
	  { 
	  if ($valeur==$x) {
	  	$flg="true";
	 	 break;
	  	}
	  }
return $flg;
}

//contrôle des paramètres $_GET
if ((isset($_GET['div'])) && (isset($_SESSION['saclasse']))) {	
	if (is_authorized($_GET['div'])=="false")   exit;
	}
//récupération de la classse concernée
if(isset($_GET['div'])) $ch=$_GET['div'];
else exit;
?>
<! Cahier_texte/_eleve.php par Ph LECLERC - Lgt "Arcisse de Caumont" 14400 BAYEUX - philippe.leclerc1@ac-caen.fr >
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Résumé du travail à faire</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK href="../style/style.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#FFFFFF" link="#000000" vlink="#000000" alink="#000000" BACKGROUND="../images/espperso.jpg">
 <style>
<!--
a:link {text-decoration:none; color: #000FF; font-family:   arial, verdana ; font-size :10pt }
a:visited {text-decoration: none; color: #999999; font-family: arial, verdana ; font-size: 10pt}
a:active {text-decoration: none; color: #000099; font-family: arial, verdana ; font-size: 10pt}
a:hover {text-decoration: none; color: #990000; font-family: arial, verdana ; font-size: 10pt}
a.actif:link {text-decoration:none;color: #ff0000;background-color: #4169E1; font-family:   arial, verdana ; font-size : 10pt }
-->
</style>
<H1 class='title'>Travail à faire pour la classe de <?echo $ch;?> </H1>
<?

// Connexion à la base de données
require_once ('../Includes/config.inc.php');

// Créer la requête (Récupérer les rubriques de la classe) 
$rq = "SELECT prof,matiere,id_prof,prefix FROM onglets
 WHERE classe='$ch' ORDER BY 'id_prof' asc ";

 // lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?

if ($nb>0)
	{
	//on récupère les données
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$prof[$loop]=$enrg[0];//nom du prof
		$mat[$loop]=$enrg[1];//matière
		$numero[$loop]=$enrg[2];//numéro de l'onglet
		$pref[$loop]=$enrg[3];// préfixe
		$loop++;
		}

	//affichage du contenu 

	include_once ('../Includes/markdown.php');//convertisseur text-->HTML

	//élaboration des dates limites
	$dat=date('Ymd',$tsmp+432000);
	$dat2=date('Ymd',$tsmp);
	//récupération des travaux à faire pour la classe
	$ind=0;
	for ($loop=0; $loop <= count($numero) ; $loop++)
		{
		$rq = "SELECT afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique FROM cahiertxt 
		WHERE (id_auteur='$numero[$loop]') AND (datafaire<='$dat') AND (datafaire>='$dat2') AND (afaire!='')";
		 
		// lancer la requête
		$result = @mysql_query ($rq) or die (mysql_error());

		// Combien y a-t-il d'enregistrements ?
		$nb2 = mysql_num_rows($result);
		//on fait un tableau de données
		while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
			 { 
			$idtaf[$ind]=$ligne[2];
			$mattaf[$ind]=$mat[$loop];
			$preftaf[$ind]=$pref[$loop];
			$proftaf[$ind]=$prof[$loop];
			$texttaf[$ind]=$ligne[0];
			$dattaf[$ind]=$ligne[1];
			$ind++;
			}
		}
	//on trie les données par dates croissantes
	if (count($mattaf)>0)
	{
	array_multisort($dattaf,$mattaf,$idtaf,$proftaf,$preftaf,$texttaf);
	}
	//fin récup
	//affichage
	if (count($mattaf)>0)
	{
	echo('<TABLE  width="800" BORDER=1 CELLPADDING=3 CELLSPACING=1 bordercolor=#E6E6FF >');
	for ($loop=0; $loop < count($dattaf) ; $loop++)
		{	
		$textafaire=markdown($texttaf[$loop]);//conversion du travail a faire
		if (!($loop>0 &&  ($dattaf[$loop] ==$dattaf[$loop-1])))		 
		echo "   <TR>  </TR><TR></TR><TR VALIGN=TOP ><TD  align=left WIDTH=20% BGCOLOR=\"#4169E1\"> 
		<font face=\"Arial\"color=\"#FFFFFF\"size=2><B>Pour le  $dattaf[$loop]</B></font></td><td></TD></TR>";
		echo "<TR VALIGN=TOP ><TD align=left WIDTH=20% BGCOLOR=\"#ABC0C9\" ><font face='Arial' color='#0D0D47' size=2><b>$mattaf[$loop] <br>
		$preftaf[$loop] $proftaf[$loop] </b></TD><TD align=left WIDTH=80%  BGCOLOR=\"#E6E6FA\"><font face='Arial' color='#0000cc' size=2>$textafaire</font></TD></TR>";			}
	echo "</table></td></tr></table>";
	}
	else echo "<font face='Arial' color='#CC0000' size=3>Aucun travail n'est programmé pour cette classe !";
	}
	else echo "<font face='Arial' color='#CC0000' size=3>Pas encore de rubrique créée pour cette classe !";
include ('../Includes/pied.inc');
?>
</body>
</html>
