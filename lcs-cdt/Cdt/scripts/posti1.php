<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 31/12/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de mise a jour du post-it-
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) {echo "Erreur";exit;}
//si la page est appeleee par un utilisateur non identife
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
//indique que le type de la reponse renvoyee au client sera du Texte
header("Content-Type: text/plain" ); 
//anti Cache pour HTTP/1.1
header("Cache-Control: no-cache , private");
//anti Cache pour HTTP/1.0
header("Pragma: no-cache");
if(isset($_POST['blabla']) && isset($_POST['cibl']))
    {
    if (get_magic_quotes_gpc()) require_once("/usr/share/lcs/Plugins/Cdt/Includes/class.inputfilter_clean.php");
    else require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';
    // Connexion a la base de donnees
    require_once ('../Includes/config.inc.php');
    //Creer la requete pour la mise a  jour des donnees	
    if (get_magic_quotes_gpc())
        {
        $Contenu  =htmlentities($_POST['blabla']);
        $Cib  =htmlentities($_POST['cibl']);
        $oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
        $cont = $oMyFilter->process($Contenu);
        $cible = $oMyFilter->process($Cib);
        }
    else
        {
        // htlmpurifier
        $Contenu = $_POST['blabla'];
        $Cib = addSlashes($_POST['cibl']);
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'ISO-8859-15'); 
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $purifier = new HTMLPurifier($config);
        $cont = $purifier->purify($Contenu);
        $cible= $purifier->purify($Cib);
        $cont = mysql_real_escape_string($cont);
        }

    $cible= $_POST['cibl'];
    $rq = "UPDATE  onglets SET postit='$cont' WHERE id_prof='$cible'";
    // lancer la requete
    $result = mysql_query($rq); 
    if (!$result)  // Si l'enregistrement est incorrect
        {  // refermer la connexion avec la base de donnees
        mysql_close();                         
        echo "<p>Votre postit n'a pas pu etre enregistre !".
        "<p></p>" . mysql_error() . "<p></p>";
        //sortir	
        exit();
        }
    else echo 'OK';
    }
 ?>