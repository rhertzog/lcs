<?
/* =============================================
   Projet LCS
   Administration serveur LCS «Installation d'un module»
   modules_install.php
   Equipe Tice academie de Caen
   20/03/2014
   Distribu selon les termes de la licence GPL
   ============================================= */
session_name("Lcs");
@session_start();
$string= $_SERVER['QUERY_STRING'];
$rq=  substr($string, 0, strlen($string)-32).md5($_SESSION['token'].htmlentities("/Modules/modules_installation.php"));
$rq2=  substr($string, 0, strlen($string)-32).md5($_SESSION['token'].htmlentities("/Modules/modules_refecran.php"));
include "/var/www/Annu/includes/check-token.php";
if (!check_variables()) {echo "nok";exit;}
if ( ! isset($_SESSION['login'])) {
    echo "<script type='text/javascript'>";
    echo 'alert("Suite \340 une p\351riode d\'inactivit\351 trop longue, votre session a expir\351 .\n\n Vous devez vous r\351authentifier");';
    echo 'location.href = "../lcs/logout.php"</script>';
    exit;
}
echo '<html>';
include ("flag.php");
if (isset($verrou))
    {
    if ($verrou==1)
        {
        echo "<html>\n
        <head>\n
        <meta HTTP-EQUIV='Content-Type' CONTENT='tetx/html; charset=utf-8'>\n
                <title>...::: Interface d\'administration Serveur LCS :::...</title>\n
                <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
        </head>\n
        <body>\n
                <h1>Gestion des Modules LCS</h1>\n
                <div class='alert_msg'>Un autre processus d'Installation/D&#233;sinstallation est en cours. Attendez qu'il se termine ... </div>\n
        </body>\n
        </html>\n";
        exit;
    }
}
?>

<frameset ROWS="0,*" BORDER="NO">
    <frame SRC="modules_installation.php?<?php echo $rq; ?>">
    <frame SRC="modules_refecran.php?<?php echo $rq2; ?>" NAME="ecran" ID="ecran">
</frameset>
</html>

