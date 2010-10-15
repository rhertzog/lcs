 <?php
 include "basedir.inc.php";
 include "fonctions.inc.php";
 include "$BASEDIR/lcs/includes/headerauth.inc.php";
 include "$BASEDIR/Annu/includes/ldap.inc.php";
 include "$BASEDIR/Annu/includes/ihm.inc.php";
 define('ACCUEIL_LCS',"../../lcs/auth.php");
 
require_once('config.inc.php');

header("Content-type: text/html");

//require ("/var/www/lcs/includes/config.inc.php");
//require_once ("/var/www/lcs/includes/functions.inc.php");
//$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");
 
 list ($idpers,$uid)= isauth();
        if ($idpers == "0") header("location: ".ACCUEIL_LCS);
		
  $ML_Adm = is_admin('monlcs_is_admin',$uid);
		
		
?>