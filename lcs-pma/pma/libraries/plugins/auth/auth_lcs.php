<?php
if (! defined('PHPMYADMIN')) {
    exit;
}
 include "/var/www/lcs/includes/headerauth.inc.php";
  if (isset($_COOKIE['LCSuser']) && isset($_SESSION['LOGIN_LCS'])) {
     $PHP_AUTH_PW = urldecode( xoft_decode($_COOKIE['LCSuser'],$key_priv) );
     $PHP_AUTH_USER =$_SESSION['LOGIN_LCS'];
     define ("AUTHLCS",'OK',true);
 }
 ?>
