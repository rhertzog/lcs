<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script du popuprss -
			_-=-_
  "Valid XHTML 1.0 Strict"
   ============================================= */

//controle des parametres $_GET
if (!isset($_GET['id'])) exit;
else
{
// Connexion a la base de donnees
require_once ('../Includes/config.inc.php');
$idret=explode(':',$_GET['id']);
if ($idret[1]!= substr(md5(crypt($idret[0],$Grain)),2)) 
exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>TAF</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>

<body>
<h1 class='title'>Travail &#224; faire </h1>
<div id="poprss">
<?php

	//recuperation du travail a faire
	
		$rq = "SELECT afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_auteur FROM cahiertxt 
		WHERE id_rubrique=$idret[0]";
		 
		// lancer la requete
		$result = @mysql_query ($rq) or die (mysql_error());

		// Combien y a-t-il d'enregistrements ?
		$nb2 = mysql_num_rows($result);
		if ($nb2>0)
		{
		//on fait un tableau de donnees
		while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
			 { 
			$texttaf=$ligne[0];
			$dattaf=$ligne[1];
			$idproftaf=$ligne[2];
			}
		$rq = "SELECT prof,matiere,id_prof,prefix FROM onglets
 		WHERE id_prof='$idproftaf' ";
		$result = @mysql_query ($rq) or die (mysql_error());
		$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?
		//on recupere les donnees
		$loop=0;
		while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
			{
			$proftaf=$enrg[0];//nom du prof
			$mattaf=$enrg[1];//matiere
			$preftaf=$enrg[3];// prefixe
			
			}
                //fin recup
                //affichage
                echo('<table id="tb-cdt">');
                $textafaire=$texttaf;//conversion du travail a faire
                echo '<tbody>';
                echo '<tr><th colspan="2">Pour le '.$dattaf.'</th></tr>';
                echo '<tr><td class="afaire">'.$mattaf.'<br />'.$preftaf.' '.$proftaf.'</td><td class="contenu">'.$textafaire.'</td></tr>';
                echo "</tbody></table>";
                }
                else
                {
                echo('<table id="tb-cdt">');
                echo '<tbody>';
                echo '<tr><th colspan="2">Ce lien est obsol&#232;te !</th></tr>';
                echo "</tbody></table>";
                }
?>
</div> <!-- fin conteneur -->
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>
