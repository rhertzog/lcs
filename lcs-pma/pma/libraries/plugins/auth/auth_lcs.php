<?php
if (! defined('PHPMYADMIN')) {
    exit;
}

 include "/var/www/lcs/includes/headerauth.inc.php";
 list ($idpers,$login) = isauth();
 if ($idpers) {
     $_LCS['pass']= urldecode( xoft_decode($_COOKIE['LCSuser'],$key_priv) );
     $_LCS['login']=$login;
 }
 if ( isset($_LCS['login']) ) {
        $PHP_AUTH_USER =  $_LCS['login'];
        $PHP_AUTH_PW = $_LCS['pass'];
        define ("AUTHLCS",'OK',true); 
 }
    ?>
