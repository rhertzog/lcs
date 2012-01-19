<?php
/* ==================================================
   Projet LCS : Linux Communication Server
  Plugin "Gestion ateliers AP"
  VERSION 1.0 du 15/12/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   -  gestion des ateliers -
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
session_name("gestap_Lcs");
@session_start();

//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

//include "../Includes/functions2.inc.php";
$tsmp=time();
$ind=0;
$tsmp2=time() + 604800;//j+7

include ("/var/www/lcs/includes/user_lcs.inc.php");
include ("/var/www/lcs/includes/functions.inc.php");
// Connexion a la base de donnaes
require_once ('../Includes/config.inc.php');
$rq = "SELECT nom,DATE_FORMAT(Dbut,'%d/%m/%Y') , DATE_FORMAT(F1,'%d/%m/%Y')  FROM niveaux WHERE id_niv='".$_GET['niveau']."'";
        // lancer la requete
        $result = @mysql_query ($rq) or die (mysql_error());
        // Combien y a-t-il d'enregistrements ?
        if (mysql_num_rows($result)>0)
            {
            $row = mysql_fetch_array($result, MYSQL_NUM);//)
            $ddbut_d=($row[1] == "00/00/0000" ) ? '' : $row[1];
            $dfin_d=($row[2] == "00/00/0000" ) ? '' : $row[2];
            }
echo '<h2 class="title">Répartition des élèves </h2>';
echo '<h3> Groupe de travail : '.$row[0].' </h3>';
if ($ddbut_d != ""&& $dfin_d != "" ) echo '<h3>Pour la période du '.$ddbut_d.'  au '.$dfin_d.' </h3>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Gestion des groupes de travail : impression</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../style/imp.css" rel="stylesheet" type="text/css" media="screen" />
 </head>
<body>
    <p></p>
<?php
$rq = "SELECT id_liste FROM listes where niveau='".$_GET['niveau']."'";
        $result = @mysql_query ($rq) or die (mysql_error());
             if (mysql_num_rows($result)>0) {
          $rq3 = "SELECT html FROM listes where niveau='".$_GET['niveau']."'";
          $result3 = @mysql_query ($rq3) or die (mysql_error());
          $enrg3 = mysql_fetch_array($result3, MYSQL_NUM);
          echo "<span id='dbu'>" .$enrg3[0]. "</span>\n";

             }
echo '<script type="text/javascript">';
echo 'window.print();';
echo '</script>';
?>
</body>
</html>
