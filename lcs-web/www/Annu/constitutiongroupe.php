<?
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/constitutiongroupe.php
   Equipe Tice academie de Caen
   V 1.4 maj : 27/06/2009
   ============================================= */
include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

header_html();
aff_trailer ("8");
$cn=$_POST['cn'];
$eleves=$_POST['eleves'];
if (is_admin("Annu_is_admin",$login)=="Y") { 
	// Ajout des membres au groupe
	echo "<H4>Ajout des membres au groupe : <A href=\"/Annu/group.php?filter=$cn\">$cn</A></H4>\n";
	for ($loop=0; $loop < count ($eleves) ; $loop++) {
		exec("$scriptsbinpath/groupAddUser.pl  $eleves[$loop] $cn" ,$AllOutPut,$ReturnValue);
		echo  "Ajout de l'utilisateur&nbsp;".$eleves[$loop]."&nbsp;";
		if ($ReturnValue == 0 ) {
			echo "<strong>R&#233;ussi</strong><BR>";
		} else { 
			echo "</strong><font color=\"orange\">Echec</font></strong><BR>"; $err++; 
		}
	}
}//fin is_admin
include ("../lcs/includes/pieds_de_page.inc.php");
?>
