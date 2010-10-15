 <?php
 /* =============================================
   Projet LcSE3 : Export LDIF
   AdminLCS/import_ldif.php
   Equipe Tice academie de Caen
   V 1.4 maj : 08/06/2009
   Distribue selon les termes de la licence GPL
   ============================================= */

include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

list ($idpers, $login)= isauth();
if ($idpers == "0")    header("Location:$urlauth");

$ldiffile= $_FILES['ldiffile']["tmp_name"];
//if (isset ($ldiffile)) {echo $ldiffile;exit;}
echo "<HTML>\n
		<HEAD>
			<TITLE>...::: Adminstration LCS :::...</TITLE>
			<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
		</HEAD>
      <BODY>\n";

if (is_admin("lcs_is_admin",$login)=="Y") {

	if (isset ($ldiffile)) {
        // upload du fichier
   	echo "<H3>Publication du fichier</H3>";
   	echo "<PRE>\n";
   	system ("ldapadd -x -c -h $ldap_server -D $adminDn -w $adminPw -f $ldiffile");
   	//
   	echo "</PRE>\n";
   	unlink ("$ldiffile");
	} else {
   	// Affichage du formulaire d'exportation LDAP
      echo "<H3>Importation dans l'annnuaire</H3>";
		// Filtrage des noms
		echo "<FORM action=\"import_ldif.php\" method=\"post\" ENCTYPE=\"multipart/form-data\">\n";
		echo "<P>Attention, les donn&#233;es existantes seront &#233;cras&#233;es!! <P>N'effectuez cette action qu'en connaissance de cause";
		echo "<P>Fichier ldif &#224; importer : <input name='ldiffile' type='file'>";
		echo "<DIV align='center'><INPUT type='submit' VALUE='Importer le fichier!!'></DIV>\n";
		echo "</FORM>\n";
  }
} else echo "Vous n'avez pas les droits n&#233;cessaires pour cette action...";

include ("../lcs/includes/pieds_de_page.inc.php");
?>
