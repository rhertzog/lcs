<?php
 /*===========================================
   Projet LcSE3
   Administration du serveur LCS
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
   ============================================= */
include "../Annu/includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];
if (count($_POST)>0) {
  //configuration objet
  include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
  $config = HTMLPurifier_Config::createDefault();
  $purifier = new HTMLPurifier($config);
  //purification des variables
  $filtre=$purifier->purify($_POST['filtre']);
}

include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

if (is_admin("lcs_is_admin",$login)=="Y") {
	if (isset($filtre)) {
		if ($filtre == "") $filtre = "objectclass=*";
		system("ldapsearch -x -h $ldap_server -D \"$adminDn\" -w $adminPw $filtre > /tmp/export.ldif");
		header("Content-Type: octet-stream");
		header("Content-Length: ".filesize ("/tmp/export.ldif") );
		header("Content-Disposition: attachment; filename=\"/tmp/export.ldif\"");
		include ("/tmp/export.ldif");
	}
}
?>
