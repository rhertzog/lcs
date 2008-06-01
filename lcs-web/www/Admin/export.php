<?php
 /* =============================================
   Projet LcSE3 : Export LDIF
   AdminLCS/export_ldif.php
   Equipe Tice académie de Caen
   V 1.4 maj : 02/02/2004
   Distribué selon les termes de la licence GPL
   ============================================= */

include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

list ($idpers, $login)= isauth();
if ($idpers == "0") header("Location:$urlauth");


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
