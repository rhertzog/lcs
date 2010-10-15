 <?php
 require_once('basedir.inc.php');
 require_once("$BASEDIR/lcs/includes/headerauth.inc.php");
 require_once("$BASEDIR/Annu/includes/ldap.inc.php");
 require_once("$BASEDIR/Annu/includes/ihm.inc.php");
 define('ACCUEIL_LCS',"../../lcs/auth.php");
 require_once('config.inc.php');
 require_once('fonctions.inc.php');
 $ML_Adm = is_admin('monlcs_is_admin',$uid);
 
 list ($idpers,$login)= isauth();
 if ($idpers == "0" || (!isset($_GET['login']) || (is_eleve($login))))
 	redirect(ACCUEIL_LCS);
//ajouter sécurité eleve pas concerné par uid
$uid = $_GET['login'];
 
?>