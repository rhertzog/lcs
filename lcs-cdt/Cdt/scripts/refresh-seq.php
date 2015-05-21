<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 10/04/2014
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de maj du contenu d'une sequence
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
session_name("Lcs");
@session_start();
//si la page est appelee par un utilisateur non identifiee
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit; 
//indique que le type de la reponse renvoyee au client sera du Texte
header("Content-Type: text/plain" ); 
//anti Cache pour HTTP/1.1
header("Cache-Control: no-cache , private");
//anti Cache pour HTTP/1.0
header("Pragma: no-cache");
if (isset($_POST['rqt']) && isset($_POST['sens']) && isset($_POST['sens']))
    {
    $buton = (empty($_POST['buttons'])) ? false : $_POST['buttons'];
    include_once("../Includes/fonctions.inc.php");
    require_once ('../Includes/config.inc.php');		
    $rq2 = "SELECT id_rubrique FROM cahiertxt  WHERE seq_id=".stripslashes($_POST['rqt'])." order by date " .stripslashes($_POST['sens']) ;
    $result2 = @mysqli_query($GLOBALS["___mysqli_ston"], $rq2) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    while ($ligne = mysqli_fetch_array($result2,  MYSQLI_NUM))
      {
        Affiche_seance_seq ($ligne[0],$buton,$_POST['tiket']);
      }
    }
else echo "error";		
?>