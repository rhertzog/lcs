 <?
 /* =============================================
   Projet SE3 : Export LDIF
   AdminLCS/export_ldif.php
   Equipe Tice academie de Caen
   V 1.4 maj : 02/02/2004
   Distribue selon les termes de la licence GPL
   ============================================= */

include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

list ($idpers, $login)= isauth();
if ($idpers == "0")    header("Location:$urlauth");
echo "<HTML>\n
		<HEAD>
			<TITLE>...::: Adminstration LCS :::...</TITLE>
			<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
		</HEAD>
      <BODY>\n";


if (is_admin("lcs_is_admin",$login)=="Y") {
    // Affichage du formulaire d'exportation LDAP
   if (!isset($filtre)) {
        echo "<H3>Exportation (sauvegarde) d'annnuaire</H3>";
		// Filtrage des noms
		 echo "<FORM action=\"export.php\" method=\"post\">\n";
		echo "<P>Si vous laissez vide le champ filtre, la totalit&#233; de l'annuaire sera export&#233;\n";
		echo "<P>Filtre LDAP: <INPUT TYPE=\"text\" NAME=\"filtre\"\n SIZE=\"60\">";
		echo "<input type=\"submit\" value=\"Valider\">\n";
		echo "</FORM>\n";
  }
} else echo "Vous n'avez pas les droits n&#233;cessaires pour cette action...";
include ("../lcs/includes/pieds_de_page.inc.php");
?>
