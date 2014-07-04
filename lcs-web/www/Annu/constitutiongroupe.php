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

header_html();
aff_trailer ("8");

if (count($_POST)>0) {
	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    	//purification des variables
  	$cn=$purifier->purify($_POST['cn']);
  	if (count($_POST['eleves'])>0)$eleves=$purifier->purifyArray($_POST['eleves']);
}

if (is_admin("Annu_is_admin",$login)=="Y") {
	// Ajout des membres au groupe
	echo "<h4>Ajout des membres au groupe : <a href=\"/Annu/group.php?filter=$cn&jeton=".md5($_SESSION['token'].htmlentities("/Annu/group.php"))."\">$cn</a></h4>\n";
	for ($loop=0; $loop < count ($eleves) ; $loop++) {
		exec("$scriptsbinpath/groupAddUser.pl  ". escapeshellarg($eleves[$loop]) ." ". escapeshellarg($cn) ,$AllOutPut,$ReturnValue);
		echo  "Ajout de l'utilisateur&nbsp;".$eleves[$loop]."&nbsp;";
		if ($ReturnValue == 0 ) {
			echo "<strong>R&#233;ussi</strong><br/>";
		} else {
			echo "</strong><font color=\"orange\">Echec</font></strong><br/>"; $err++;
		}
	}
}//fin is_admin
include ("../lcs/includes/pieds_de_page.inc.php");
?>
