<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 25/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'export html-
			_-=-_
   =================================================== */
session_name("Cdt_Lcs");
@session_start(); 
error_reporting(0);
//si la page est appelée par un utilisateur non identifié
if (!isset($_SESSION['login']) )exit;
    
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";
//si la page est appelee par un utilisateur non "direction"
if (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N") exit;			
// Connexion a la base de données
require_once ('../Includes/config.inc.php');
include_once("../Includes/fonctions.inc.php");
$filesql = "Archives_Cdt_".date("d-m-Y\_H\hi")."_".$domain.".html";
$maintenant = date('D, d M Y H:i:s') . ' GMT';

header('Content-Type: application/octet-stream');
header('Expires: ' . $maintenant);

if (mb_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
    header('Content-Disposition: attachment; filename="' . $filesql . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
} else {
    header('Content-Disposition: attachment; filename="' . $filesql . '"');
    header('Pragma: no-cache');
}

//récupération des données
$list_classe=array();

//liste des classes
$rq="SELECT DISTINCT `classe` FROM `onglets` WHERE `classe` IS NOT NULL";
$result = @mysql_query ($rq);
	if ($result)
		{
		$n=0;	
		while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
			{
			$list_classe[$n]=$enrg[0];
			$n++;
			}
		}
mysql_free_result($result);
//afficher entete
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<title></title>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
<style type="text/css">
  
a:link {
    color: #000000 }
  a:visited {
    color: #ffffff; }
  </style>
</head>
<body>

<TABLE border="4" width="100%" BORDERCOLOR="#fce3b2" rules="all">
<THEAD>
    <TR ><th colspan="5" BGCOLOR="#0d6a1e" ><a name="liste"></a>
    <H2><FONT COLOR="#fefefe"> - Archives du cahier de textes - </font></H2>
    <P><FONT COLOR="#ffffcc"> Les donn&#233;es sont class&#233;es par Classe/groupe puis par date croissante<br />
    (pensez &agrave; la fonction "Rechercher" de votre navigateur)<br />
        Liens vers les classes et groupes <br /></font></p>
<?php
for ($loop=0; $loop < count ($list_classe)  ; $loop++)
	{
	echo '<a href="#'.$list_classe[$loop].'">'.$list_classe[$loop].'</a> - ';
	}
?>
<br />-</th></TR>
<TR> <TD WIDTH=10% BORDERCOLOR="#fce3b2">Date</TD><TD WIDTH=10% BORDERCOLOR="#fce3b2">Enseignant</TD><TD WIDTH=10% BORDERCOLOR="#fce3b2">Mati&#232;re</TD>
    <TD WIDTH=35% BORDERCOLOR="#fce3b2">Contenu de la s&#233;ance </TD><TD WIDTH=35% BORDERCOLOR="#fce3b2">Travail &agrave; faire</TD></TR>
</THEAD>
<?php
for ($loop=0; $loop < count ($list_classe)  ; $loop++)
	{
echo '	
<THEAD>
    <TR ><th colspan="5" BGCOLOR="#9757b5"><a name="'.$list_classe[$loop].'">'.$list_classe[$loop].' </a></th></TR>
    
</THEAD>';

//liste des dates
$rq="SELECT date,DATE_FORMAT(date,'%d/%m/%Y') from cahiertxt WHERE id_auteur IN ( SELECT id_prof from onglets WHERE classe='$list_classe[$loop]') GROUP BY date ORDER BY date asc";

$result = @mysql_query ($rq);
	if ($result)
		{
		$list_dates=array();
		$n=0;	
		while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
			{
			$list_dates[$n]=$enrg[0];
			$list_dattes[$n]=$enrg[1];
			$n++;
			}
		}
mysql_free_result($result);
/// 
if (count ($list_dates)>0)
	{
	// pour chaque jour
	for ($loop1=0; $loop1 < count ($list_dates); $loop1++)
		{
		
		//recherche des id du jour
		$rq="SELECT id_auteur,contenu,afaire from cahiertxt WHERE date='$list_dates[$loop1]' AND 
		 id_auteur IN ( SELECT id_prof from onglets WHERE classe='$list_classe[$loop]') ORDER BY id_auteur asc";
		//if ($list_classe[$loop]=='ATI1') {echo $rq;exit;}
		$result = @mysql_query ($rq);
		if ($result)
			{
			$id=array();$cours=array();$afR=array();
			$n=0;	
			while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
				{
				$id[$n]=$enrg[0];
				$cours[$n]=$enrg[1];
				$afR[$n]=$enrg[2];
				$n++;
				}
			}
		mysql_free_result($result);	
		
		echo '<TBODY TITLE="Text">	    
			<TR>
				<TD ROWSPAN='.count($id).' WIDTH=10% BGCOLOR="#9ec3a5" BORDERCOLOR="#fce3b2">
				<P>'.LeJour(strToTime($list_dates[$loop1])).'<br />'.$list_dattes[$loop1].'</P>
				</TD>';
				// pour chaque jour
				for ($loop2=0; $loop2 < count ($id); $loop2++)
					{
					//recherche donnes auteur
					$rq="SELECT prefix,prof,matiere from onglets WHERE id_prof='$id[$loop2]'";
					//echo $rq;exit;
					$result = @mysql_query ($rq);
					if ($result)
						{
						while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
							{
							$Prof=$enrg[0]." ".$enrg[1];
							$matiere=$enrg[2];
							}
						}
					mysql_free_result($result);
					
					echo '<TD  WIDTH=10% BORDERCOLOR="#fce3b2">
					<P>'.$Prof.'</P>
					</TD>
					<TD  WIDTH=10% BORDERCOLOR="#fce3b2">
					<P>'.$matiere.'</P>
					</TD>
					<TD WIDTH=35% BORDERCOLOR="#fce3b2">
					<P>'.$cours[$loop2].'</P>
					</TD>
					<TD WIDTH=35% BORDERCOLOR="#fce3b2">
					<P>'.$afR[$loop2].'</P>
					</TD>				
			</TR>';}//fin ligne de donnees
			
			echo '</TBODY>';
		}//fin jour
	}
echo '<TR><th colspan="5"  BGCOLOR="#a4abc3" margin="5px"><a href="#liste">Retour à la liste des classe </a></th></TR>';
}
?>
</TABLE>
</body>
</html>

