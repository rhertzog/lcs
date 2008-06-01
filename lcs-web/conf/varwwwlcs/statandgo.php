<?php
/* lcs/statandgo.php version du :  29/01/2008 */
require ("./includes/headerauth.inc.php");
require ("../Annu/includes/ldap.inc.php");
#
# Sélection du chemin
#
# Traitement des applis non référencées dans la table params
if ($use=="Applis") $urluse="applis.php";
elseif ($use=="Annu") $urluse="../Annu/";
elseif ($use=="Forum") $urluse="../Forum/";
elseif ($use=="Admin") $urluse="../Admin/index.php";
elseif ($use=="Aide") $urluse="../lcs-doc/";
elseif ( ! isset($urluse) ) {
    # Cas des paquets modules
    #echo "on examine le cas des modules $use<br>";
    $query="SELECT  name, value from applis where type='M' order by name";
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
    #echo "on examine le cas des plugins<br>";
    $query="SELECT chemin, name, value from applis where type='P' order by name";
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
if ( $use=="Accueil" || ! isset ($urluse) ) $urluse="accueil.php";
#
# Détection de l'origine de la requete
#
list ($ip_client_prefix) = explode (".", remote_ip()); 
list ($ip_serv_prefix) = explode (".",getenv("SERVER_ADDR"));
if ( $ip_client_prefix == $ip_serv_prefix) $source="lan"; else $source="wan";
#
# Détermination du groupe principal de l'utilisateur connecté
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
$result=mysql_db_query("$DBAUTH","INSERT INTO statusages VALUES ('$group', '$use', '$date', '$source')", $authlink);
#
# Redirection
#
header("Location:$urluse");
?>
