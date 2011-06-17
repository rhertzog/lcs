<?php
/* createmaildirJQ.php */

include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

list ($idpers,$login)= isauth();
if ($idpers != "0" && is_admin("lcs_is_admin",$login)=="Y" ) {

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


