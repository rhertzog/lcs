 <?
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
                                    echo '<input name="jeton" type="hidden"  value="'.md5($_SESSION['token'].htmlentities('/Admin/export.php')).'" />';
		echo "<input type=\"submit\" value=\"Valider\">\n";
		echo "</FORM>\n";
  }
} else echo "Vous n'avez pas les droits n&#233;cessaires pour cette action...";
include ("../lcs/includes/pieds_de_page.inc.php");
?>
