<?php

/* =============================================
   Projet LCS : Linux Communication Server
   lcs/includes/user_lcs.inc.php
   Equipe Tice académie de Caen
   V 1.0 maj : 10/01/2003
============================================= */
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

       global $HTTP_COOKIE_VARS, $authlink,$DBAUTH;

       $ret= "";
       if (!empty($HTTP_COOKIE_VARS["LCSAuth"])) {
               $sess=$HTTP_COOKIE_VARS["LCSAuth"];
               $result=mysql_db_query("$DBAUTH","SELECT  idpers,  remote_ip FROM sessions WHERE sess='$sess'", $authlink);
               if ($result && mysql_num_rows($result) ) {
                       $ip_session = mysql_result($result,0,1);
                       $idpers = mysql_result($result,0,0);
                       mysql_free_result($result);
                       if ( $ip_session == client_ip() ) {
                               # Recherche le login dans la base personne  et  interrogation de l'annuaire
                               $result=mysql_db_query("$DBAUTH","SELECT login FROM personne WHERE id=$idpers", $authlink);
                               if ($result && mysql_num_rows($result)) {
                                       $login=mysql_result($result,0,0);
                                       mysql_free_result($result);
                                       $ret=$login;
                               }
                       }
               }
               return  $ret;
        }
}

?>