<?php
/* lcs/statandgo.php version du :  01/10/2010 */
require ("./includes/headerauth.inc.php");
require ("../Annu/includes/ldap.inc.php");

//register global
$use=$_GET['use'];

#
# Determination de url_accueil et version du LCS
#
$query="SELECT value from applis where name='url_accueil'";
$result=@mysql_query($query);
if ($result) {
    while ($r=@mysql_fetch_array($result)) 
               $url_accueil=$r["value"];
}
$query="SELECT value from params where name='VER'";
$result=@mysql_query($query);
if ($result) {
    while ($r=@mysql_fetch_array($result)) 
               $VER=$r["value"];
}
@mysql_free_result($result);
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
    $result=mysql_query($query);
    if ($result) {
        while ( $r=mysql_fetch_object($result) ) {
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
    mysql_free_result($result);
}
if (  ! isset ($module) && ! isset($urluse) ) {
    # Cas des plugins 
    $query="SELECT chemin, name, value from applis where type='P' OR type='N' order by name";
    $result=mysql_query($query);
    if ($result) {
        while ( $r=mysql_fetch_object($result) ) {
	    if ( ($r->name == $use) ) {
	        $urluse = "../Plugins/".$r->chemin."/";
            }
        }
    }
    mysql_free_result($result);
} 
if ( ! isset ($urluse) ) $urluse=$url_accueil;
#
# Detection de l'origine de la requete
#
list ($ip_client_prefix) = explode (".", remote_ip()); 
list ($ip_serv_prefix) = explode (".",getenv("SERVER_ADDR"));
if ( $ip_client_prefix == $ip_serv_prefix) $source="lan"; else $source="wan";
#
# Determination du groupe principal de l'utilisateur connecté
#
list ($idpers, $login)= isauth();
$group=people_get_group ($login); 
#
# TimeStamp
#
$date=date("YmdHis");
#
# Enregistrement dans la table statusages
#
//$result=mysql_db_query("$DBAUTH","INSERT INTO statusages VALUES ('$group', '$use', '$date', '$source','$login')", $authlink);
$query="INSERT INTO statusages VALUES ('$group', '$use', '$date', '$source','$login')";
$result=@mysql_query($query, $authlink);
#
# Redirection
#
header("Location:$urluse");
?>
