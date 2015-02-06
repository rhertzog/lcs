<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Module lcs-maintenance
   06/02/2015
   =================================================== */
Function check_variables() {
$ticket=true;
if (count($_POST)>0){
        if (md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])) != $_POST['jeton']  ) {
            $ticket=false;
            $faux_jeton="POST-".$_POST['jeton'];
        }
}

if (count($_GET)>0) {
        if ($_GET['jeton'] != md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])) ) {
            $ticket=false;
            $faux_jeton="GET-".$_GET['jeton'];
            }
}

 if (count($_FILES)>0) {
        if ($_POST['jeton'] != md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])) ) {
            $ticket=false;
            $faux_jeton="Files-".$_POST['jeton'];
            }
}

if (isset($_SESSION['token']) XOR isset($_SESSION['login']) )  {
    $ticket=false;$faux_jeton="Faux_token_session";
    }

if (!$ticket) {
    if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
               else if (getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
               else if (getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
               else $ip = "UNKNOWN";
    error_log(date("d-m-Y H:i:s"). " [Error token] [client $ip] [login=".$_SESSION['login']."] [jeton=".addslashes($faux_jeton)."] [script=".$_SERVER['PHP_SELF']."]\n",3,"/var/log/lcs/check.log");
}
return $ticket;
}

Function check_acces($no_check_token=false) {
    session_name("Lcs");
    @session_start();
    if ((!$no_check_token && check_variables()) || $no_check_token) {
        if ( !isset($_SESSION['login'])) {
            echo "<script type='text/javascript'>";
            echo 'alert("Suite \340 une p\351riode d\'inactivit\351 trop longue, votre session a expir\351 .\n\n Vous devez vous r\351authentifier");';
            echo 'location.href = "../../lcs/logout.php"</script>';
            return false;
            }
            else return true;
        }
    else return false;
    }
?>
