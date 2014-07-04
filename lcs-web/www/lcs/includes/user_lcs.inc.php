<?php
/*===========================================
   Projet LcSE3
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 28/03/2014
   ============================================= */

include ("/var/www/lcs/includes/config.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");

function auth_lcs()
{
	/* Test si une authentification est faite
		- Si oui, renvoie le login
	*/

        global $authlink, $DBAUTH;
        $login="";
        if (! empty($_COOKIE["LCSAuth"])) {
        if (!@mysql_select_db($DBAUTH, $authlink))
                die ("S&#233;lection de base de donn&#233;es impossible.");
        $cookie_auth = $_COOKIE["LCSAuth"];
        $sess = mysql_real_escape_string($cookie_auth);
        if (strcmp($sess,$cookie_auth)!=0) {
            echo "*** Security : Tentative d'intrusion detectee ! <br/>\n";
            echo "Votre cookie de valeur :".htmlspecialchars($cookie_auth)." est invalide<br/>\n";
            if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
            else if (getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
            else if (getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
            else $ip = "UNKNOWN";
            error_log(("d-m-Y H:i:s"). " [Error token] [client $ip] [cookie ".addslashes($cookie_auth)."] \n",3,"/var/log/lcs/check.log");
            die("IP : $ip <br>");
            }
        $query="SELECT remote_ip, idpers FROM sessions WHERE sess='$sess'";
        $result=@mysql_query($query,$authlink);
        if ($result && @mysql_num_rows($result) ) {
        // Split ip variable too take first ip only
        // (BDD field is too short on multiple proxy case)
        list($ip_session,$null) = preg_split("/,/",mysql_result($result,0,0),2);
        list($first_remote_ip,$null) = preg_split("/,/",remote_ip(),2);
        if ( $ip_session == $first_remote_ip ) {
                $idpers =  mysql_result($result,0,1);
                # Recherche le login dans la base personne  et  interrogation de l'annuaire
                $idpers=mysql_real_escape_string($idpers);
                $query="SELECT login FROM personne WHERE id=$idpers";
                $result=@mysql_query($query, $authlink);
                if ($result && @mysql_num_rows($result)) {
                        $login=@mysql_result($result,0,0);
                        @mysql_free_result($result);
                        $ret=$login;
                }
        }
        }
        return $ret;
	}
}

?>