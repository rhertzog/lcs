<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.1 du 25/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de mise a jour du cdt archives eleve-
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
  
session_name("Cdt_Lcs");
@session_start();
//si la page est appelee par un utilisateur non identifiee
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
 
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
$ann_arch=$_POST['thean_arch'];
// Connexion � la base de donnees
require_once ('../Includes/config.inc.php');

include_once("../Includes/fonctions.inc.php");
		//affichage du contenu du cahier de textes
	
			//include_once ('../Includes/markdown.php');//convertisseur text-->HTML
	
			//cr�er la requ�te
			if ($cible!="")
			{//�laboration de la date limite a partir de la date selectionn�e
			if ($_SESSION['version']==">=432") setlocale(LC_TIME,"french");
			else setlocale("LC_TIME","french");
			$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date,date FROM cahiertxt".$ann_arch."
			WHERE id_auteur=$cible  ORDER BY date asc";

			// lancer la requ�te
			$result = @mysql_query ($rq) or die (mysql_error());

			// Combien y a-t-il d'enregistrements ?
			$nb2 = mysql_num_rows($result);
			//echo '<div id="boite5elv">';
			echo '<table id="tb-cdt" cellpadding="1" cellspacing="2">';
			while ($ligne = mysql_fetch_array($result, MYSQL_NUM))
				{
				  $textcours=utf8_encode(stripslashes($ligne[1]));
				  //$textcours=$ligne[1];
				  $textafaire=utf8_encode(stripslashes($ligne[2]));
				  //$day="1,0,0,12,1,2007";echo $day;
				  $jour=LeJour(strToTime($ligne[5]));
				  //debut
				  if ($ligne[1]!="") {
                                  echo '<tbody><tr><th colspan="2"></th></tr></tbody>';
                                  echo '<tbody>';
                                  echo '<tr>';
                                  //affichage de la seance
                                  echo '<td class="seance">S&eacute;ance du <br/>'.$jour.'&nbsp;'.$ligne[0].'<br />Visible le '.$ligne[7].' </td>';
                                  if($ligne[1]!="" && $ligne[6]==1) echo '<td class="contenu2">';
                                  elseif($ligne[1]!="" && $ligne[6]==2) echo '<td class="contenu3">';
                                  else echo '<td class="contenu">';
                                  echo $textcours.'</td></tr>';
                                  //affichage, s'il existe, du travail a effectuer
                                  if ($ligne[2]!="") {
                                  echo '<tr><td class="afaire">A faire pour le :<br />'.$ligne[3].'</td><td class="contenu">';
                                  echo $textafaire.'</td></tr>';
                                  }
                                  //fin

				  echo '</tbody>';
                                  echo '<tbody><tr><th colspan="2"><hr /></th></tr></tbody>';
				  	}
				  else
				   {
				  echo '<tbody><tr><th colspan="2"></th></tr></tbody>';
                                  echo '<tbody>';
                                  echo '<tr>';
                                  //affichage de la seance
                                  echo '<td class="afaire">Donn&eacute; le :&nbsp;'.$ligne[0].'<br />Visible le '.$ligne[7];
                                  //affichage, s'il existe, du travail a effectuer
                                  if ($ligne[2]!="") {
                                  echo '<br/>Pour le :&nbsp;'.$ligne[3].'</td>';
                                  if($ligne[6]==1) echo '<td class="contenu2">';
                                  elseif($ligne[6]==2) echo '<td class="contenu3">';
                                  else echo '<td class="contenu">';
                                  echo $textafaire.'</td></tr>';
                                  }
                                  //fin
				  echo '</tbody>';
                                   echo '<tbody><tr><th colspan="2"><hr /></th></tr></tbody>';
				  	}
			} //fin du while
	echo '</table>';
 include ('../Includes/pied.inc');
}
}
else echo "error";		
?>