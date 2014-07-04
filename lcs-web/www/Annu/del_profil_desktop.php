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
}

header_html();
aff_trailer ("3");

if (is_admin("Annu_is_admin",$login) == "Y" ) {
	if ( isset ( $uid ) && preg_match("#^[A-Za-z0-9._-]{3,19}$#", $uid)) {
		list($user, $groups)=people_get_variables($uid, true);
		$mask = "/home/$uid/Profile/*.json";
    	@array_map( "unlink", glob( $mask ) );
    	echo "<p>Le profil du bureau de <b><i>".$user["fullname"]."</i></b> a &#233;t&#233; r&#233;initialis&#233;.</p>";
	} else echo "<div class=error_msg>ERREUR !</div>";
} else
	echo "<div class=error_msg>Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";

include ("../lcs/includes/pieds_de_page.inc.php");
?>
