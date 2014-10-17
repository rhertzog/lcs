<?php
# /var/www/Admin/importcertssl.php derniere version du : 16/10/2014
include "../Annu/includes/check-token.php";
if (!check_acces()) exit; 

$login=$_SESSION['login'];

include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

$DEBUG = false;

$extensions = array('.pem', '.crt');
$keystore="/usr/share/lcs/certmanager/";

// Messages d'aide
function msgaide($msg) {
    return ("&nbsp;<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('".$msg."')")."\"><img name=\"action_image2\"  src=\"../images/help-info.gif\"></u>");
}

$msg1="Importation d'un certificat SSL pour les services LCS (CAS, apache-ssl, imap-ssl).";

if ($idpers == "0") header("Location:$urlauth");
$html = "
	  <head>\n
	  <title>...::: Importation d'un certificat SSL  :::...</title>\n
	  <meta http-equiv='content-type' content='text/html;charset=utf-8' />
	  <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
	  </head>\n
	  <body>\n";
$html .= "<div id='container'><h2>LCS gestion des certificats SSL</h2>\n";
echo $html;
if (is_admin("system_is_admin",$login)=="Y") {
	$html  = "<h3>Importation d'un certificat SSL</h3>\n";
	$html .= "<form action=\"importcertssl.php\" method=\"post\" enctype=\"multipart/form-data\">\n";  
	$html .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"10000\" />\n";
	$html .= "<p>Certificat SSL (.crt) <input name=\"userfile[]\" type=\"file\" /><p />\n";
	$html .= "<p>Clé privée (.pem) <input name=\"userfile[]\" type=\"file\" /><p />\n";
	$html .= "<p>Mot de passe de la clé privée <input type=\"password\" name=\"pwdkey\"/></p>\n";
	$html .= "<p>Certificat racine de l'autorité de certification (.pem) <input name=\"userfile[]\" type=\"file\" /><p />\n";	
	$html .= "<input name=\"jeton\" type=\"hidden\"  value=\"". md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF']))."\" />\n";    
   $html .= "<input type=\"submit\" value=\"Importer le certificat\" />\n"; 
	echo $html;



if (is_uploaded_file($_FILES["userfile"]["tmp_name"][0]) && is_uploaded_file($_FILES["userfile"]["tmp_name"][1]) && is_uploaded_file($_FILES["userfile"]["tmp_name"][2]) ) {
	// Messages debug
	if ( $DEBUG == "true" ) {
		echo "crt :".$_FILES['userfile']['name'][0]."<br />";
		echo "path :".$_FILES["userfile"]["tmp_name"][0]."<br />";
		echo "size :".$_FILES["userfile"]["size"][0]."<br />";
		echo "key :".$_FILES['userfile']['name'][1]."<br />"; 
		echo "path :".$_FILES["userfile"]["tmp_name"][1]."<br />";
		echo "size :".$_FILES["userfile"]["size"][1]."<br />";
	}
	// Verification de la taille
	if ( ! $_POST[MAX_FILE_SIZE] >= $_FILES["userfile"]["size"][0] || ! $_POST[MAX_FILE_SIZE] >= $_FILES["userfile"]["size"][1] || ! $_POST[MAX_FILE_SIZE] >= $_FILES["userfile"]["size"][2] ) 
		echo "Fichiers trop volumineux ! <br />";
	
	// Verification des extensions
	if (  strrchr($_FILES['userfile']['name'][0], '.')  != ".crt" ) {
		echo "Veuillez importer un certificat avec l'extension .crt !<br/>";
		$ERR = true;
	}
	
	if (  strrchr($_FILES['userfile']['name'][1], '.')  != ".pem" ) {
		echo "Veuillez importer une clé privée avec l'extension .pem !<br/>";
	    $ERR = true;	
	}
	
	if (  strrchr($_FILES['userfile']['name'][2], '.')  != ".pem" ) {
		echo "Veuillez importer un certificat racine avec l'extension .pem !<br/>";
		$ERR = true;
	}		

	// Verification de la présence du champ password
	if ( ! $_POST["pwdkey"] ) {
		echo "Veuillez fournir un mot de passe pour la clé privée de votre certificat !<br/>";
		$ERR = true;
	}
	
	// Recherche du nom de fichier sans l'extension 
	$namecert = str_replace(".crt", "", $_FILES['userfile']['name'][0]);
	
	if ( $ERR == false ) {
		// Deplacement du fichier
		if ( move_uploaded_file($_FILES['userfile']['tmp_name'][0], $keystore . $_FILES['userfile']['name'][0]) &&
		 	move_uploaded_file($_FILES['userfile']['tmp_name'][1], $keystore . $_FILES['userfile']['name'][1]) &&
		 	move_uploaded_file($_FILES['userfile']['tmp_name'][2], $keystore . $namecert . "_trusted_ca.pem")
	   	 ) {
			// Traitement du certificat
			exec("/usr/bin/sudo /usr/sbin/lcs-certmanager -i ". escapeshellarg($_FILES['userfile']['name'][0]) ." ". escapeshellarg($_FILES['userfile']['name'][1]) ." ". escapeshellarg($_POST["pwdkey"]) ." ". escapeshellarg($namecert));
			if ( $DEBUG == "true" ) echo "/usr/bin/sudo /usr/sbin/lcs-certmanager -i ".$_FILES['userfile']['name'][0]." ".$_FILES['userfile']['name'][1]." ".$_POST["pwdkey"]." ".$namecert. " ";
			echo "Import du certificat réussi.<br />";
		} else 
			echo "Echec de la mise en place de ce certificat !<br />";
	}

}	



}// fin is_admin
else echo "Vous n'avez pas les droits nécessaires pour ordonner cette action...";
echo "</div><!-- Fin container-->\n";
include ("../lcs/includes/pieds_de_page.inc.php");
?>