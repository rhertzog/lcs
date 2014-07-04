<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

  $login=$_SESSION['login'];
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  if (count($_GET)>0) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    	//purification des variables
  	$uid=$purifier->purify($_GET['uid']);
  	$toggle=$purifier->purify($_GET['toggle']);
  }

  if (is_admin("Annu_is_admin",$login)=="Y") {
	exec ("$scriptsbinpath/mysqlDbToggle.pl ". escapeshellarg($toggle) ." ". escapeshellarg($uid),$AllOutPut,$ReturnValue);
  	$jeton_bdd=md5($_SESSION['token'].htmlentities("/Annu/people.php"));
  	header("Location:people.php?uid=$uid&jeton=$jeton_bdd");
  }
  else {
	header_html();
	echo "<div class=error_msg>Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");

?>
