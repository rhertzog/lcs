<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.0 du 29/04/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'enregistrement du modèle-
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
  
session_name("Cdt_Lcs");
@session_start();
//si la page est appeleee par un utilisateur non identifiÃ©
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
//indique que le type de la reponse renvoyee au client sera du Texte
header("Content-Type: text/plain" ); 
//anti Cache pour HTTP/1.1
header("Cache-Control: no-cache , private");
//anti Cache pour HTTP/1.0
header("Pragma: no-cache");
if(isset($_REQUEST['coursmod']) && isset($_REQUEST['afmod']) && isset($_REQUEST['cibl'])  )
{
if (get_magic_quotes_gpc()) require_once("/usr/share/lcs/Plugins/Cdt/Includes/class.inputfilter_clean.php");
else require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';
// Connexion a la base de donnees
	require_once ('../Includes/config.inc.php');
	if (get_magic_quotes_gpc())
		    {
			$Contenucours  =htmlentities($_REQUEST['coursmod']);
			$Contenuaf  =htmlentities($_REQUEST['afmod']);
			$Cib  =htmlentities($_REQUEST['cibl']);
			$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
			$cont1 = $oMyFilter->process($Contenucours);
			$cont2 = $oMyFilter->process($Contenuaf);
			$cible = $oMyFilter->process($Cib);
			}
		else
			{
			// htlmpurifier
			$Contenucours  = addSlashes($_REQUEST['coursmod']);
			$Contenuaf  = addSlashes($_REQUEST['afmod']);
			$Cib = addSlashes($_REQUEST['cibl']);
			$config = HTMLPurifier_Config::createDefault();
	    	$config->set('Core.Encoding', 'ISO-8859-15'); 
	    	$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
	   		$purifier = new HTMLPurifier($config);
	   		//$Cours = addSlashes($Cours);
	   		$cont1 = $purifier->purify($Contenucours);
	   		$cont2 = $purifier->purify($Contenuaf);
	   		$cible= $purifier->purify($Cib);
	   		}	
//		
		
		//$cible= $_REQUEST['cibl'];
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