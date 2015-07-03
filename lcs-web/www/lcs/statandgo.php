<?php
/*===========================================
   Projet LcSE3
   Administration du serveur LCS
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
  Derniere modification : 04/04/2014
   ============================================= */
include "../Annu/includes/check-token.php";
if (!check_acces(1)) exit;

$login=$_SESSION['login'];
require ("./includes/headerauth.inc.php");
require ("../Annu/includes/ldap.inc.php");
if (count($_GET)>0) {
        //configuration objet
        include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        //purification des variables
        $use=$purifier->purify($_GET['use']);
}
#
# Determination de url_accueil et version du LCS
#
$query="SELECT value from applis where name='url_accueil'";
$result=@mysqli_query($GLOBALS["___mysqli_ston"], $query);
if ($result) {
    while ($r=@mysqli_fetch_array($result))
               $url_accueil=$r["value"];
}
$query="SELECT value from params where name='VER'";
$result=@mysqli_query($GLOBALS["___mysqli_ston"], $query);
if ($result) {
    while ($r=@mysqli_fetch_array($result))
               $VER=$r["value"];
}
@((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
#
# Traitement des applis non referencees dans la table params
# comme module ou plugin
#
if ( mb_ereg ("^1" , $VER ) ) {
  # Cas des LCS 1.x traitement de $use Forum ou mail
  if ($use=="Forum") $urluse="../Forum/";
  elseif ( $use=="mail" )  $urluse="../squirrelmail/src/redirect.php";
  $urldoc="../aide_fr/frames.html";
} else $urldoc="../doc/";

if ($use=="Applis") $urluse="applis.php";
elseif ($use=="Accueil") {
  if ( is_dir ("/var/www/monlcs") ) $urluse="/monlcs/"; else $urluse="/lcs/accueil.php";
} elseif ($use=="Accueil2") $urluse="/lcs/accueil.php";
elseif ($use=="Annu") $urluse="../Annu/";
elseif ($use=="Admin") $urluse="../Admin/index.php";
elseif ($use=="Aide") $urluse=$urldoc;
elseif ( ! isset($urluse) ) {
    # Cas des paquets modules
    $query="SELECT  name, value from applis where type='M' or type='S' order by name";
    $result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
    if ($result) {
        while ( $r=mysqli_fetch_object($result) ) {
	    if ( ($r->name == $use) ) {
                # en attendant de rectifier les paquets modules
                if ( $r->name=="smbwebclient" )
                    $urluse = "../".$r->name."/smbwebclient.php";
                elseif ( $r->name=="squirrelmail" )
                    $urluse="../squirrelmail/src/redirect.php";
                else $urluse = "../".$r->name."/";
                $module=1;
            }
        }
    }
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
}
if (  ! isset ($module) && ! isset($urluse) ) {
    # Cas des plugins
    $query="SELECT chemin, name, value from applis where type='P' OR type='N' order by name";
    $result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
    if ($result) {
        while ( $r=mysqli_fetch_object($result) ) {
	    if ( ($r->name == $use) ) {
	        $urluse = "../Plugins/".$r->chemin."/";
            }
        }
    }
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
}
if ( ! isset ($urluse) ) $urluse=$url_accueil;
#
# Detection de l'origine de la requete
#
list ($ip_client_prefix) = explode (".", remote_ip());
list ($ip_serv_prefix) = explode (".",getenv("SERVER_ADDR"));
if ( $ip_client_prefix == $ip_serv_prefix) $source="lan"; else $source="wan";
#
# Determination du groupe principal de l'utilisateur connecte
#
$group=people_get_group ($login);
#
# TimeStamp
#
$date=date("YmdHis");
#
# Enregistrement dans la table statusages
#
$use=((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $use) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
$query="INSERT INTO statusages VALUES ('$group', '$use', '$date', '$source','$login')";
$result=@mysqli_query( $authlink, $query);
#
# Redirection
#
header("Location:$urluse");
?>
