<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 10/04/2014
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de chargement du modele-
			_-=-_
   =================================================== */

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");

session_name("Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit;
//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
//indique que le type de la reponse renvoyee au client sera du Texte
header('Content-Type: text/plain');

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
        //$config->set('Core.Encoding', 'ISO-8859-15');
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $purifier = new HTMLPurifier($config);
        $cible= $purifier->purify($Cib);
        }
    $rq = "SELECT   mod_cours,mod_afaire from onglets  WHERE id_prof='$cible' AND login='{$_SESSION['login']}'";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $rq);
    if (mysqli_num_rows($result)>0)
        {
        $row = mysqli_fetch_array($result,  MYSQLI_NUM);
        echo "<span id='mod_c'>" .  utf8_encode($row[0]) . "</span>\n";
        echo "<span id='mod_af'>" . utf8_encode($row[1]) . "</span>\n";
        }
    if (!$result)  // Si l'enregistrement est incorrect
        {
        ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
        echo "NOK";
        exit();
        }
    }
 ?>
