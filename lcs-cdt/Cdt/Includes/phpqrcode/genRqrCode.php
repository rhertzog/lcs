<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/11/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de generation Qrcode -
   			_-=-_
   "Valid XHTML 1.0 Strict"
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
if ((!isset($_SESSION['version']))  ) exit;
if ( ! mb_ereg('Plugins/Cdt/index.php',$_GET['qrurl'])) $_GET['qrurl']= 'y a un os';

include "qrlib.php";
// creates code image and outputs it directly into browser
QRcode::png(str_replace("*amp*", "&", $_GET['qrurl'])); 
?>
