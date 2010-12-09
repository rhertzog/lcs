<?php
/* lcs/includes/user_lcs.inc.php */

include ("/var/www/lcs/includes/config.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");

function client_ip()
{
        if(getenv("HTTP_CLIENT_IP")) {
                $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
                $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else {
                $ip = getenv("REMOTE_ADDR");
        }
        return $ip;
}

function auth_lcs()
{
        /* Teste si une authentification est faite
                - Si oui, renvoie le login
        */

       global $authlink,$DBAUTH;

       $ret= "";
       if (!empty($_COOKIE["LCSAuth"])) {
               $sess=$_COOKIE["LCSAuth"];
	       $query="SELECT  idpers,  remote_ip FROM sessions WHERE sess='$sess'";

               $result=@mysql_query($query, $authlink);
               if ($result && @mysql_num_rows($result) ) {
                       $ip_session = @mysql_result($result,0,1);
                       $idpers = @mysql_result($result,0,0);
                       @mysql_free_result($result);
                       if ( $ip_session == client_ip() ) {
						# Recherche le login dans la base personne  et  interrogation de l'annuaire
			       		$query="SELECT login FROM personne WHERE id=$idpers";
                               $result=@mysql_query($query, $authlink);
                               if ($result && @mysql_num_rows($result)) {
                                       $login=@mysql_result($result,0,0);
                                       @mysql_free_result($result);
                                       $ret=$login;
                               }
                       }
               }
               return  $ret;
        }
}

?>