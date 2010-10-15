 <?php
 include "basedir.inc.php";
 include "fonctions.inc.php";
 include "$BASEDIR/lcs/includes/headerauth.inc.php";
 include "$BASEDIR/Annu/includes/ldap.inc.php";
 include "$BASEDIR/Annu/includes/ihm.inc.php";
 define('ACCUEIL_LCS',"../../lcs/auth.php");
 
 require_once('config.inc.php');


 list ($idpers,$uid)= isauth();
       if ($idpers == "0") 
		header("location: ".ACCUEIL_LCS);
 $ML_Adm = is_admin('monlcs_is_admin',$uid);	
		
?>