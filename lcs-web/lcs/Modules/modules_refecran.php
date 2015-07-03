<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Desinstallation d'un module»
   modules_refecran.php
   Equipe Tice academie de Caen
   28/03/2014
   Distribue selon les termes de la licence GPL
   ============================================= */
session_name("Lcs");
@session_start();
include "/var/www/Annu/includes/check-token.php";
if (!check_variables()) exit;
if ( ! isset($_SESSION['login'])) {
    echo "<script type='text/javascript'>";
    echo 'alert("Suite \340 une p\351riode d\'inactivit\351 trop longue, votre session a expir\351 .\n\n Vous devez vous r\351authentifier");';
    echo 'location.href = "../lcs/logout.php"</script>';
    exit;
}
if (count($_GET)>0) {
        //configuration objet
        include ("/var/www/lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        //purification des variables
        if (isset($_GET['dpid'])) $dpid=$purifier->purify($_GET['dpid']);
        if (isset($_GET['p'])) $p=$purifier->purify($_GET['p']);
        if (isset($_GET['v'])) $v=$purifier->purify($_GET['v']);
        if (isset($_GET['n'])) $n=$purifier->purify($_GET['n']);
        if (isset($_GET['d'])) $d=$purifier->purify($_GET['d']);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>...::: Interface d'administration Serveur LCS :::...</title>
    <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>
<?php

include ("/var/www/lcs/includes/headerauth.inc.php");
include("modules_commun.php");
include ("flag.php");
if(empty($_SERVER['QUERY_STRING'])) $QSTRING = "";
else $QSTRING = $_SERVER['QUERY_STRING'];
?>
<script TYPE="text/javascript">
<!--
    autorefresh = parseInt('3');
    if (autorefresh > 0)
            {
            <?php
            if ($flip!="1")
            echo "  setTimeout(\"self.location.href = self.location.protocol + '//' + self.location.host + self.location.pathname + '" . ($QSTRING=="" ? "" : "?$QSTRING") . "'\", 1000 * autorefresh);";
            ?>
             }
 // -->
</script>
</head>
<body>
<?php
if (isset($p)) { // il s'agit d'une installation
        $nf="lcs-".$n;
        $fecran = cree_nom_fichier_ecran($nf);
        $MSG = "Installation";
        }
if (isset($dpid))   { // il s'agit d'une desinstallation
$dpid=((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $dpid) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
if (!isset($_SESSION['nommod'])) {
        $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM applis WHERE id='$dpid'");
        if (!$result) die("Erreur lors de la requ&#234;te MySQL");
        $row = mysqli_fetch_object($result);
        $_SESSION['nommod']= $row->name;
        }

        $nf="lcs-".$_SESSION['nommod'];
        $fecran = cree_nom_fichier_ecran($nf);
        $MSG = "Desinstallation";
}

if (file_exists($fecran))
    {
      $df = fopen($fecran,"r");
      fpassthru($df);
    }

if ($flip!="1")
    {
    echo "<DIV style=\"text-align: center; height:80px\"><IMG ALT=\"Patientez...\" SRC=\"../Modules/Images/patientez.gif\"></DIV>\n";
    echo "<DIV class=\"alert_msg\">$MSG module LCS, patientez quelques minutes ...</DIV>\n";
    }
?>
<script TYPE="text/javascript">
<!--
self.scrollTo(0,1000000);
//-->
</script>
<?
include ("/var/www/lcs/includes/pieds_de_page.inc.php");
if ($flip=="1")
    {
    $cmd= "/usr/bin/sudo -H -u root /usr/share/lcs/scripts/fin_mod.sh " .escapeshellarg($fecran)." ".escapeshellarg("/usr/share/lcs/Modules/Logs/".date('Ymd_His')."_".$MSG."_".$nf.".html");
    //$Cmd="mv ".$fecran. " /usr/share/lcs/Modules/Logs/".date('Ymd_His')."_".$MSG."_".$nf.".html";
    exec($cmd);

    }
?>
