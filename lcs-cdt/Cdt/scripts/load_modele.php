<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.1 du 4/6/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de chargement du modèle-
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
header('Content-Type: text/xml'); 

//anti Cache pour HTTP/1.1
header("Cache-Control: no-cache , private");
//anti Cache pour HTTP/1.0
header("Pragma: no-cache");
if( isset($_REQUEST['cibl'])  )
	{
	if (get_magic_quotes_gpc()) require_once("/usr/share/lcs/Plugins/Cdt/Includes/class.inputfilter_clean.php");
	else require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';
	// Connexion a la base de donnees
	require_once ('../Includes/config.inc.php');
	//Creer la requete 	
	if (get_magic_quotes_gpc())
		    {
			$Cib  =htmlentities($_REQUEST['cibl']);
			$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
			$cible = $oMyFilter->process($Cib);
			}
		else
			{
			// htlmpurifier
			$Cib = addSlashes($_REQUEST['cibl']);
			$config = HTMLPurifier_Config::createDefault();
	    	$config->set('Core.Encoding', 'ISO-8859-15'); 
	    	$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
	   		$purifier = new HTMLPurifier($config);
	   		$cible= $purifier->purify($Cib);
	   		}	
	   		
	   		$rq = "SELECT   mod_cours,mod_afaire from onglets  WHERE id_prof='$cible' AND login='{$_SESSION['login']}'";
	
	// lancer la requete
	$result = mysql_query($rq);
	if (mysql_num_rows($result)>0)
		{  
		$row = mysql_fetch_array($result, MYSQL_NUM);//) 
		echo "<?xml version=\"1.0\"?>\n";
		echo "<modele>\n";
		echo "<donnee>" . htmlentities($row[0]) . "</donnee>\n";
		echo "<donnee>" . htmlentities($row[1]) . "</donnee>\n";
		echo "</modele>\n";
		}
	if (!$result)  // Si l'enregistrement est incorrect
		{  // refermer la connexion avec la base de donnees
		mysql_close();                         
		 echo "NOK";
		//sortir	
		exit();
		}
	}
 
?>