<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 25/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script du Travail A Faire ELEVE -
   			_-=-_
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit;
include ('../Includes/data.inc.php');
include "../Includes/functions2.inc.php";
include ("/var/www/Annu/includes/ldap.inc.php");	
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php"); 
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Résumé du travail à faire</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>

<body>
<H1 class='title'>Travail à faire pour la classe de <?echo $ch;?> </H1>
<div id="taf">
<?php

// Connexion à la base de données
require_once ('../Includes/config.inc.php');
if (( isset($_SESSION['parentde']))  && (!isset($_SESSION['login'])))
	{
	
	foreach ( $_SESSION['parentde'] as $cle => $valcla)
		{
		if ($valcla[2]==$ch)
			{
			$uid_actif=$valcla[0];
			}
		}	
	}
	
if ($_SESSION['cequi']=="eleve") $uid_actif=$_SESSION['login'];	

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
//modif grp
	}
//recherche des onglets "cours d'un eleve"
if ($uid_actif!="") {
	 $groups=people_get_cours($uid_actif);
if ( count($groups) > 0 ) {
    for ($loopo=0; $loopo < count ($groups) ; $loopo++) {
      $rq = "SELECT prof,matiere,id_prof,prefix FROM onglets
		WHERE classe='{$groups[$loopo]["cn"]}' ORDER BY 'id_prof' asc ";
		$result = @mysql_query ($rq) or die (mysql_error());
		$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ? 
		if ($nb>0)
			{
			//on récupère les données 
			while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$prof[$loop]=$enrg[0];//nom du prof
		$mat[$loop]=$enrg[1];//matière
		$numero[$loop]=$enrg[2];//numéro de l'onglet
		$pref[$loop]=$enrg[3];// préfixe
		$loop++;
		}
			}
      
	}
}

//fin onglets cours eleve 
}
else 
	{
	//recherche des onglets "Cours" de la classe
	
	if (!mb_ereg("^Classe",$ch)) {
	$grp_cl=search_groups("cn=Classe_*".$ch);
	$grp_cl=$grp_cl[0]["cn"];
	}
	else $grp_cl=$ch;
	$uids = search_uids ("(cn=".$grp_cl.")", "half");	
	$liste_cours=array();
	$i=0;
	for ($loup=0; $loup < count($uids); $loup++)
		{
		$logun= $uids[$loup]["uid"];
		if (is_eleve($logun)) 
			{
			$groops=people_get_cours($logun);
			if (count($groops))
				{
				for($n=0; $n<count($groops); $n++)
					{ 
					if (!in_array($groops[$n]["cn"], $liste_cours)) 
						{	
						$liste_cours[$i]=$groops[$n]["cn"];
						$i++;
						}
					}
				}
			}
		}
	
	if (count($liste_cours)>0)
		{
		for($n=0; $n<count($liste_cours); $n++)
			{
			$rq = "SELECT prof,matiere,id_prof,prefix FROM onglets
		WHERE classe='{$liste_cours[$n]}' ORDER BY 'id_prof' asc ";
			$result = @mysql_query ($rq) or die (mysql_error());
			$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ? 
			if ($nb>0)
				{
			//on récupère les données 
				while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$prof[$loop]=$enrg[0];//nom du prof
		$mat[$loop]=$enrg[1];//matière
		$numero[$loop]=$enrg[2];//numéro de l'onglet
		$pref[$loop]=$enrg[3];// préfixe
		$loop++;
		}
				}
			}
		}
}
//fin modif
if (count($numero)>0)
{
//eom

	//affichage du contenu 

	include_once ('../Includes/markdown.php');//convertisseur text-->HTML

	//élaboration des dates limites
	$dat=date('Ymd',$tsmp+1209600);//J+15
	//echo $dat;exit;
	$dat2=date('YmdHis',$tsmp);
	//récupération des travaux à faire pour la classe
	$ind=0;
	for ($loop=0; $loop < count($numero) ; $loop++)
		{
		$rq = "SELECT afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,datafaire FROM cahiertxt 
		WHERE (id_auteur='$numero[$loop]') AND (datafaire<='$dat') AND (datafaire>='$dat2') AND (afaire!='') AND datevisibi<='$dat2'";
		 		 
		// lancer la requête
		$result = mysql_query ($rq) or die (mysql_error());

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
			$tsmpaf[$ind]=$ligne[3];
			$ind++;
			}
		}
	//on trie les données par dates croissantes
	if (count($mattaf)>0)
	{
	array_multisort($tsmpaf,$dattaf,$mattaf,$idtaf,$proftaf,$preftaf,$texttaf);
	}
	//fin récup
	//affichage
	if (count($mattaf)>0)
	{
	echo('<TABLE id="tb-cdt">');
	for ($loop=0; $loop < count($dattaf) ; $loop++)
		{	
		$textafaire=markdown($texttaf[$loop]);//conversion du travail a faire
		if (!($loop>0 &&  ($dattaf[$loop] ==$dattaf[$loop-1])))
			{		 
			echo '<tbody>';
			echo '<tr><th colspan=2>Pour le '.$dattaf[$loop].'</th></TR>';}
			echo '<TR><TD class="afaire">'.$mattaf[$loop].'<br />'.$preftaf[$loop].' '.$proftaf[$loop].'</TD><TD class="contenu">'.$textafaire.'</TD></TR>';
			
		}
	echo "</td></tr></table>";
	}
	else echo "<font face='Arial' color='#CC0000' size=3>Aucun travail n'est programmé pour cette classe !";
	}
	else echo "<font face='Arial' color='#CC0000' size=3>La rubrique n'est pas encore créée pour cette classe !";
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
		document.write('<div id=\"bt-abs\"><A HREF=\"javascript:window.close()\" id=\"bt-close\"></A></div>');
	</SCRIPT>";

echo '</div>'; //fin du div conteneur taf
include ('../Includes/pied.inc');
?>
</body>
</html>
