<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 10/04/2014
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de mise a jour du cdt eleve-
			_-=-_
   =================================================== */

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
session_name("Lcs");
@session_start();
//si la page est appelee par un utilisateur non identifiee
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit;
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
    // Connexion a la base de donnees
    require_once ('../Includes/config.inc.php');
    if ($cible!="")
        {

        $dat=date('Ymd',$tsmp-5184000);//2592000=nbre de secondes dans 30 jours
        $dat_now=date('YmdHis');
        if ($_SESSION['version']==">=432") setlocale(LC_TIME,"french");
        else setlocale("LC_TIME","french");
        $rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date,on_off FROM cahiertxt
        WHERE id_auteur=$cible AND date>=$dat AND datevisibi<=$dat_now ORDER BY date desc";
        $result = @mysqli_query($GLOBALS["___mysqli_ston"], $rq) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
        // Combien y a-t-il d'enregistrements ?
        $nb2 = mysqli_num_rows($result);
        include_once  ('./contenu.php');
        if (stripos($_SERVER['HTTP_USER_AGENT'], "msie"))  include ('../Includes/pied.inc');
        }
    }
else echo "error";
?>