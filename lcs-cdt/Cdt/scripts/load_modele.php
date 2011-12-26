<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 31/12/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de chargement du modele-
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
  
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit;
//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
//indique que le type de la reponse renvoyee au client sera du Texte
header('Content-Type: text/xml'); 

//anti Cache pour HTTP/1.1
header("Cache-Control: no-cache , private");
//anti Cache pour HTTP/1.0
header("Pragma: no-cache");

function xml_character_encode($string, $trans='') {
    $trans = (is_array($trans)) ? $trans : get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
    foreach ($trans as $k=>$v)
    $trans[$k]= "&#".ord($k).";";
    return strtr($string, $trans);
}  

if ( isset($_POST['cibl']))
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
        $Cib = $_REQUEST['cibl'];
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'ISO-8859-15'); 
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $purifier = new HTMLPurifier($config);
        $cible= $purifier->purify($Cib);
        }	
    $rq = "SELECT   mod_cours,mod_afaire from onglets  WHERE id_prof='$cible' AND login='{$_SESSION['login']}'";
    $result = mysql_query($rq);
    if (mysql_num_rows($result)>0)
        {  
        $row = mysql_fetch_array($result, MYSQL_NUM);//) 
        echo "<?xml version=\"1.0\"?>\n";
        echo "<modele>\n";
        echo "<donnee>" . xml_character_encode($row[0]) . "</donnee>\n";
        echo "<donnee>" .  xml_character_encode($row[1]) . "</donnee>\n";
        echo "</modele>\n";
        }
    if (!$result)  // Si l'enregistrement est incorrect
        { 
        mysql_close();                         
        echo "NOK";
        exit();
        }
    }
 ?>