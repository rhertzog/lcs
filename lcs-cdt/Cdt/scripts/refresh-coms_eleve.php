<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.1 du 25/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de mise a jour du cdt eleve-
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
  
session_name("Cdt_Lcs");
@session_start();
//si la page est appelee par un utilisateur non identifiee
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit; 

//si la page est appelee par un utilisateur non prof
//elseif ($_SESSION['cequi']!="prof") exit;
 
//indique que le type de la reponse renvoyee au client sera du Texte

header("Content-Type: text/plain" ); 
//anti Cache pour HTTP/1.1
header("Cache-Control: no-cache , private");
//anti Cache pour HTTP/1.0
header("Pragma: no-cache");

if(isset($_POST['blabla']) && isset($_POST['kan']))
{
$tsmp=$_POST['kan'];
$cible=$_POST['blabla'];
//echo $cible.'-'.$tsmp;exit;
include_once("../Includes/fonctions.inc.php");
		include_once ('../Includes/markdown.php');//convertisseur text-->HTML
		// Connexion à la base de données
		require_once ('../Includes/config.inc.php');
		//créer la requête
		if ($cible!="") 
		{//élaboration de la date limite à partir de la date selectionnée
		$dat=date('Ymd',$tsmp-5184000);//2592000=nbre de secondes dans 30 jours
		$dat_now=date('YmdHis');
		if ($_SESSION['version']==">=432") setlocale(LC_TIME,"french");
		else setlocale("LC_TIME","french");
		$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date,on_off FROM cahiertxt 
		WHERE id_auteur=$cible AND date>=$dat AND datevisibi<=$dat_now ORDER BY date desc";
		 
		// lancer la requête
		$result = @mysql_query ($rq) or die (mysql_error());

		// Combien y a-t-il d'enregistrements ?
		$nb2 = mysql_num_rows($result);
		//echo '<div id="boite5elv">';
//
                 include_once  ('./contenu.php');
if (stripos($_SERVER['HTTP_USER_AGENT'], "msie"))  
{ include ('../Includes/pied.inc');}
//echo "</div>"; //fin du div boite5elv

		}
		
}
else echo "error";		
?>