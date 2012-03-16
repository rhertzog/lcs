<?php
/* lcs/logout_ent.php version du :  16/03/2012 */
include ("./includes/headerauth.inc.php");
//Logout  ENT CAS service
include_once('/usr/share/php/CAS/CAS.php');
#phpCAS::setDebug();
// initialise phpCAS
phpCAS::client(CAS_VERSION_2_0,$ent_hostname,intval($ent_port),$ent_uri);
// no SSL validation for the CAS server
phpCAS::setNoCasServerValidation();
#phpCAS::handleLogoutRequests(true, array($hostname.".".$domain));
// logout if desired
phpCAS::logout(array('url'=>$baseurl));
?>