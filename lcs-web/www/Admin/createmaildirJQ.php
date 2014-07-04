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
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

if (is_admin("lcs_is_admin",$login)=="Y" ) {

	exec ("/usr/bin/sudo /usr/share/lcs/scripts/createmaildir.sh", $MSG);

	if ( $MSG[0]==0 )
		echo "Tous les Maildirs ont d&eacute;j&agrave; &eacute;t&eacute; cr&eacute;&eacute; !";
	elseif ($MSG[0]==1)
		echo "Un Maildir a &eacute;t&eacute; cr&eacute;&eacute;.";
	else
		echo "Nombre de Maildirs cr&eacute;&eacute;s : ".$MSG[0].".";

} else {
	echo "<h3>Vous n'avez pas les droits pour ex&eacute;cuter cette action !</h3>";
}
?>


