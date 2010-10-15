<?php
//session_start();
 require_once('basedir.inc.php');
 require_once("$BASEDIR/lcs/includes/headerauth.inc.php");
 require_once("$BASEDIR/Annu/includes/ldap.inc.php");
 require_once("$BASEDIR/Annu/includes/ihm.inc.php");
 define('ACCUEIL_LCS',"/lcs/auth.php");
 require_once('config.inc.php');
 require_once('config_acad.inc.php');
 require_once('fonctions.inc.php');
 list ($idpers,$uid)= isauth();
 if ($idpers == "0")
 	redirect(ACCUEIL_LCS);
 $ML_Adm = is_admin('monlcs_is_admin',$uid);

if ($_POST)
	{
	//extract($_POST);
	if ( ($_POST['mode'] == 'other') && ($_POST['user'] != '')) {
		$uid = $_POST['user'];
		$ML_Adm = 'N';
		}
	}
 
?>
