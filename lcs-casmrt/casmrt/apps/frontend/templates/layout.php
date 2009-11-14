<?php 
require_once('/var/www/lcs/includes/config.inc.php');
require_once('/var/www/lcs/includes/headerauth.inc.php');
require_once('/var/www/lcs/includes/functions.inc.php');

$sf_user->setAttribute('ldap_server',$ldap_server);
$sf_user->setAttribute('ldap_port',$ldap_port);
$sf_user->setAttribute('dn',$dn);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <link type="text/css" rel="stylesheet" href="/casmrt/css/themes/cas.css"/>
    <link type="text/css" rel="stylesheet" href="/casmrt/css/themes/simple/theme.css"/>
  
</head>
  <body>
    <?php echo $sf_content ?>
  </body>
</html>
