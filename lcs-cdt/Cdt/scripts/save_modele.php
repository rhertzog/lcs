<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'enregistrement du modèle-
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
  
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit;
//si la page est appeleee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
//indique que le type de la reponse renvoyee au client sera du Texte
header("Content-Type: text/plain" ); 
//anti Cache pour HTTP/1.1
header("Cache-Control: no-cache , private");
//anti Cache pour HTTP/1.0
header("Pragma: no-cache");
if(isset($_POST['coursmod']) && isset($_POST['afmod']) && isset($_POST['cibl'])  )
{
if (get_magic_quotes_gpc()) require_once("/usr/share/lcs/Plugins/Cdt/Includes/class.inputfilter_clean.php");
else require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';
// Connexion a la base de donnees
	require_once ('../Includes/config.inc.php');
	if (get_magic_quotes_gpc())
		    {
			$Contenucours  =htmlentities($_POST['coursmod']);
			$Contenuaf  =htmlentities($_POST['afmod']);
			$Cib  =htmlentities($_POST['cibl']);
			$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
			$cont1 = $oMyFilter->process($Contenucours);
			$cont2 = $oMyFilter->process($Contenuaf);
			$cible = $oMyFilter->process($Cib);
			}
		else
			{
			// htlmpurifier
			$Contenucours  = $_POST['coursmod'];
			$Contenuaf  =$_POST['afmod'];
			$Cib = addSlashes($_POST['cibl']);
			$config = HTMLPurifier_Config::createDefault();
                                                      $config->set('Core.Encoding', 'ISO-8859-15'); 
                                                      $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
	   		$purifier = new HTMLPurifier($config);
	   		//$Cours = addSlashes($Cours);
	   		$cont1 = $purifier->purify($Contenucours);
	   		$cont1 = mysql_real_escape_string($cont1);
	   		$cont2 = $purifier->purify($Contenuaf);
	   		$cont2 = mysql_real_escape_string($cont2);
	   		$cible= $purifier->purify($Cib);
	   		}	

		$rq = "UPDATE  onglets SET mod_cours='$cont1', mod_afaire='$cont2' WHERE id_prof='$cible'";
		
	// lancer la requete
		$result = mysql_query($rq); 
		if (!$result)  // Si l'enregistrement est incorrect
			{  // refermer la connexion avec la base de donnees
			mysql_close();                         
			 echo "<p>Votre mod&#232; n'a pas pu &#234;tre enregistr&#233; !".
			 "<p></p>" . mysql_error() . "<p></p>";
			//sortir	
			exit();
			}
			else echo 'OK';

}
 
?>