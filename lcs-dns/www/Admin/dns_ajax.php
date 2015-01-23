<?php
/*===========================================
   Projet LcSE3
   Administration du serveur LCS
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 23/01/2015
   ============================================= */
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
include "../Annu/includes/check-token.php";
if (!check_acces()) {exit;}

if (count($_POST)>0) {
  //configuration objet
  include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
  $config = HTMLPurifier_Config::createDefault();
  $purifier = new HTMLPurifier($config);
  //purification des variables
  $action=$purifier->purify($_POST['action']);
  $contenu=$purifier->purify($_POST['cont']);
}

if ($_POST['action']=="edite") {
    exec ("/usr/bin/sudo /usr/sbin/lcs-dns-gencsv" , $AllOutput, $ReturnValue);
    if($fichier_ok = @fopen("/home/admin/Documents/ZonesDNS/localnetdb.csv", "r")) {
        while (($buffer = fgets($fichier_ok)) !== false) {
            echo $buffer;
        }
        if (!feof($fichier_ok)) {
            echo "NOK";
        }
        fclose($fichier_ok);
    }
    else echo "NOK";
    exit;
}

if ($_POST['action']=="majz") {
    if($fichier_ok = fopen("/home/admin/Documents/ZonesDNS/localnetdb.csv", "w+")) {
        fwrite($fichier_ok, $contenu);
        fclose($fichier_ok);
        exec ("/usr/bin/sudo /usr/sbin/lcs-dns-genlocalzone" , $AllOutput, $ReturnValue);
        echo $ReturnValue;
        exit;
    }
    else echo "1";
}
?>