<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.1 du 4/6/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de sauvegarde de la bdd-
			_-=-_
   =================================================== */
session_name("Cdt_Lcs");
@session_start(); 
//error_reporting(0);
//si la page est appelée par un utilisateur non identifié
if (!isset($_SESSION['login']) )exit;

//si la page est appelée par un utilisateur non admin
elseif ($_SESSION['login']!="admin") exit;
    
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";
			
// Connexion a la base de données
require_once ('../Includes/config.inc.php');

function get_key() 
		  	{
			$val=array();
		    $cmd = "cat /etc/LcSeConfig.ph | grep 'mysqlServerPw ' | cut -d\"'\" -f2";
		    exec($cmd,$val,$ret_val);
			return $val[0];
			}

$filesql = DB_NAME."-".$VER_PLUG."_".date("d-m-Y\_H\hi").".sql";
$maintenant = date('D, d M Y H:i:s') . ' GMT';

header('Content-Type: application/octet-stream');
header('Expires: ' . $maintenant);

if (mb_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
    header('Content-Disposition: inline; filename="' . $filesql . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
} else {
    header('Content-Disposition: attachment; filename="' . $filesql . '"');
    header('Pragma: no-cache');
}
$res = get_key();
$cmd="mysqldump -uroot -p".$res." --opt ". DB_NAME;
passthru($cmd);

?>
