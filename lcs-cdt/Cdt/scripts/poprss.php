<?
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 25/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script du popuprss -
			_-=-_
   ============================================= */

//contr�le des param�tres $_GET
if (!isset($_GET['id'])) exit;
else
{
// Connexion � la base de donn�es
require_once ('../Includes/config.inc.php');
$idret=explode(':',$_GET['id']);
if ($idret[1]!= substr(md5(crypt($idret[0],$Grain)),2)) 
exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Travail � faire</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>

<body>
<H1 class='title'>Travail � faire </H1>
<div id="poprss">
<?



	//r�cup�ration du travail � faire 
	
		$rq = "SELECT afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_auteur FROM cahiertxt 
		WHERE id_rubrique=$idret[0]";
		 
		// lancer la requ�te
		$result = @mysql_query ($rq) or die (mysql_error());

		// Combien y a-t-il d'enregistrements ?
		$nb2 = mysql_num_rows($result);
		if ($nb2>0)
		{
		//on fait un tableau de donn�es
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
		//on r�cup�re les donn�es
		$loop=0;
		while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
			{
			$proftaf=$enrg[0];//nom du prof
			$mattaf=$enrg[1];//mati�re
			$preftaf=$enrg[3];// pr�fixe
			
			}
	//fin r�cup
	//affichage
	
	echo('<TABLE id="tb-cdt">');
		
		$textafaire=$texttaf;//conversion du travail a faire
				 
			echo '<tbody>';
			echo '<tr><th colspan=2>Pour le '.$dattaf.'</th></TR>';
			echo '<TR><TD class="afaire">'.$mattaf.'<br />'.$preftaf.' '.$proftaf.'</TD><TD class="contenu">'.$textafaire.'</TD></TR>';
				
	echo "</td></tr></table>";
	}
	else {
	echo('<TABLE id="tb-cdt">');
			echo '<tbody>';
			echo '<tr><th colspan=2>Ce lien est obsol�te !</th></TR>';
			echo "</td></tr></table>";
	}
echo '</div>'; //fin du div conteneur taf
include ('../Includes/pied.inc');
?>
</body>
</html>
