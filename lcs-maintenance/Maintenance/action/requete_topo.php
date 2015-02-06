<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Module lcs-maintenance
   - script execution requetes ajax
                _-=-_
  06/02/2015
   =================================================== */
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
include "../Includes/checking.php";
if (! check_acces()) exit;

include "../Includes/basedir.inc.php";
include ("$BASEDIR/lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
if (count($_POST)>0) {
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    $idbat=$purifier->purify($_POST['id_batiment']);
    $idetage=$purifier->purify($_POST['id_etage']);
    }
include "../Includes/config.inc.php";
// connexion à la base de données
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=maint_plug', $USERAUTH,$PASSAUTH);
    } catch(Exception $e) {
        exit('Impossible de se connecter à la base de données.');
    }
$json = array();

//traitement des requêtes
if($idbat!="" && $idetage=="") {
    $requete = "SELECT etage from topologie WHERE batiment='$idbat' GROUP BY etage ASC ";
    $resultat = $bdd->query($requete) or die(print_r($bdd->errorInfo()));
    while($donnees = $resultat->fetch(PDO::FETCH_ASSOC)) {
       $json[$donnees['etage']][] = utf8_encode($donnees['etage']);
    }
    echo json_encode($json);
    exit;
 }

if($idbat!="" && $idetage!="") {
    $id = htmlentities(($_POST['id_batiment']));
    $id2=htmlentities(($_POST['id_etage']));
    $requete = "SELECT salle from topologie WHERE batiment='$idbat'  AND etage='$idetage' ORDER BY salle ASC ";
    $resultat = $bdd->query($requete) or die(print_r($bdd->errorInfo()));
    while($donnees = $resultat->fetch(PDO::FETCH_ASSOC)) {
        $json[$donnees['salle']][] = utf8_encode($donnees['salle']);
    }
     echo json_encode($json);
     exit;
}
?>


