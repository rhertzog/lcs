<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 10/10/2014
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d action sur les sequences-
			_-=-_
   =================================================== */

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
session_name("Lcs");
@session_start();
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
// Connexion a la base de donnees
require_once ('../Includes/config.inc.php');
if (get_magic_quotes_gpc()) require_once("../Includes/class.inputfilter_clean.php");
else require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';
// lecture bdd
if (isset($_POST['num_seq']) && (isset($_POST['action'])))
    {
    if ($_POST['action'] == "lire")
        {
        $ru=$_POST['num_seq'];
        $rq = "SELECT titrecourt,titre,contenu FROM sequences
        WHERE id_seq='$ru'";
        // lancer la requete
        $result = @mysqli_query($GLOBALS["___mysqli_ston"], $rq) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
        // Combien y a-t-il d'enregistrements ?
        if (mysqli_num_rows($result)>0)
            {
            $row = mysqli_fetch_array($result,  MYSQLI_NUM);//)
            /*echo "<?xml version=\"1.0\"?>\n";
            echo "<sequence>\n";*/
            echo "<span id='sht'>" .   utf8_encode($row[0]) . "</span>\n";
            echo "<span id='lgt'>" .  utf8_encode($row[1]) . "</span>\n";
            echo "<span id='dn'>" .  utf8_encode($row[2]) . "</span>\n";
            //echo "</sequences>\n";
            }
        else echo "Erreur de lecture";
        exit;
        }

    //update ordre
    if ($_POST['action'] == "up_ordre" )
        {
        $ru=$_POST['num_seq'];
        $ord=$_POST['posission'];
        $rq = "UPDATE  sequences SET ordre='$ord' WHERE id_seq='$ru'";
        $result = mysqli_query($GLOBALS["___mysqli_ston"], $rq);
        if (!$result)  // Si l'enregistrement est incorrect
            {
            echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . ((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) ;
            ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);     // refermer la connexion avec la base de donnees
            }
        else echo "OK";
        exit;
        }

    //deplacement d'une sequence
    if ($_POST['action'] == "deplace")
        {
        $ru=$_POST['num_seq'];
        $dest=$_POST['ong_dest'];
        $rq = "UPDATE sequences SET id_ong = '$dest' WHERE id_seq='$ru';";
        $result = mysqli_query($GLOBALS["___mysqli_ston"], $rq);
         if (!$result)  // Si l'enregistrement est incorrect
            {
            echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . ((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) ;
            ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);     // refermer la connexion avec la base de donnees
            }
        else echo "OK";
        exit;
        }

    //copier/coller d'une sequence
    if ($_POST['action'] == "ajoute")
        {
        $dest=$_POST['ong_dest'];
        $ru=$_POST['num_seq'];
        $rq = "SELECT titrecourt,titre,contenu FROM sequences WHERE id_seq='$ru'";
        // lancer la requete
        $result = @mysqli_query($GLOBALS["___mysqli_ston"], $rq) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
        // Combien y a-t-il d'enregistrements ?
        if (mysqli_num_rows($result)>0)
            {
            $row = mysqli_fetch_array($result,  MYSQLI_NUM);
            $d0=mysqli_real_escape_string($dbc, $row[0]);
            $d1=mysqli_real_escape_string($dbc, $row[1]);
            $d2=mysqli_real_escape_string($dbc, $row[2]);
            }
        $rq = "INSERT INTO sequences (id_ong,titrecourt,titre,contenu)
        VALUES ( '$dest','$d0','$d1' ,'$d2')";
        $result = mysqli_query($GLOBALS["___mysqli_ston"], $rq);
       if (!$result)  // Si l'enregistrement est incorrect
            {
            echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . ((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) ;
            ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);     // refermer la connexion avec la base de donnees
            }
        else echo "OK";
        exit;
        }

    //test si des seances sont associees a une sequence
     if ($_POST['action'] == "test")
        {
         $ru=$_POST['num_seq'];
         $rq = "SELECT count(*) FROM cahiertxt WHERE seq_id='$ru'";
         // lancer la requete
        $result = @mysqli_query($GLOBALS["___mysqli_ston"], $rq) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
        // Combien y a-t-il d'enregistrements ?
        if (mysqli_num_rows($result)>0)
            {
            $rowt = mysqli_fetch_array($result,  MYSQLI_NUM);
            echo $rowt[0];
            }
         else echo "NOK";
         exit;
        }
    }

//suppression
if ($_POST['action'] == "delete")
    {
    $ru=$_POST['num_seq'];
    $rq= "DELETE FROM sequences WHERE id_seq ='$ru' ";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $rq);
    if (!$result)  // Si l'enregistrement est incorrect
        {
        echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . ((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) ;
        ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);     // refermer la connexion avec la base de donnees
        }
    else echo "OK";
    exit;
    }

 //enregistrement/mise a jour d'une sequence
if (isset($_POST['titre1']) && (isset($_POST['titre2'])) && (isset($_POST['descript'])) && (isset($_POST['action'])))
    {
    if (get_magic_quotes_gpc())
        {
        $Title1  =htmlentities(utf8_decode($_POST['titre1']));
        $Title2  =htmlentities(utf8_decode($_POST['titre2']));
        $Desc  =htmlentities($_POST['descript']);
        $oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
        $cont1 = $oMyFilter->process($Title1);
        $cont2 = $oMyFilter->process($Title2);
        $cont3 = $oMyFilter->process($Desc);
        }
    else
        {
        // htlmpurifier
        $Title1  = $_POST['titre1'];
        $Title2  = $_POST['titre2'];
        $Desc = $_POST['descript'];
        $config = HTMLPurifier_Config::createDefault();
        //$config->set('Core.Encoding', 'ISO-8859-15');
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $purifier = new HTMLPurifier($config);
        //$Cours = addSlashes($Cours);
        $cont1 = $purifier->purify($Title1);
        $cont1 = mysqli_real_escape_string($dbc, $cont1);
        $cont1= utf8_decode($cont1);
	$cont2 = $purifier->purify($Title2);
        $cont2 = mysqli_real_escape_string($dbc, $cont2);
	$cont2= utf8_decode($cont2);
        $cont3= $purifier->purify($Desc);
        $cont3 = mysqli_real_escape_string($dbc, $cont3);
	$cont3= utf8_decode($cont3);
        }

    if ($_POST['action'] == "save")
        {
        $rq = "INSERT INTO sequences (id_ong,titre,titrecourt,contenu)
        VALUES ( '{$_POST["num_rub"]}','$cont2', '$cont1', '$cont3')";
         }

    if ($_POST['action'] == "update")
        {
        $cible=$_POST["num_rub"];
        $rq = "UPDATE  sequences SET titrecourt='$cont1', titre='$cont2', contenu='$cont3' WHERE id_seq='$cible'";
        }
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $rq);
    if (!$result)  // Si l'enregistrement est incorrect
        {
        echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . ((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) ;
        ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);     // refermer la connexion avec la base de donnees
        }
    else echo "OK";
    exit;
    }
?>
