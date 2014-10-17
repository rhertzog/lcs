<?
# /var/www/Admin/gencertssl.php derniere version du : 16/10/2014

include "../Annu/includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];

  
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

# Purify 
if (count($_GET)>0) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    //purification des variables
  	$newcert=$purifier->purify($_GET['newcert']);
}

// Messages d'aide
function msgaide($msg) {
    return ("&nbsp;<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('".$msg."')")."\"><img name=\"action_image2\"  src=\"../images/help-info.gif\"></u>");
}
$msg1="Régénération du certificat SSL pour les services LCS (CAS, apache-ssl, imap-ssl) une période de 365 jours.";

$html = "
	  <head>\n
	  <title>...::: Génération certificat apache ssl  :::...</title>\n
	  <meta http-equiv='content-type' content='text/html;charset=utf-8' />\n
	  <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
	  </head>\n
	  <body>\n";
$html .= "<div id='container'><h2>Services intranet LCS</h2>\n";
echo $html;
if (is_admin("system_is_admin",$login)=="Y") {
	$html = "<h3>Regénération certificat SSL LCS</h3>\n";
	if ( ! isset($newcert) ) {
		$html .= "<ul>\n<li>\n";  
		$html .= "\t<a href=\"gencertssl.php?newcert=1&jeton=".md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF']))."\">Lancer la régénération du certificat.</a>".msgaide($msg1)."\n";
		$html .= "</ul>\n</li>\n";
		echo $html;
	} else {
		exec("/usr/bin/sudo /usr/sbin/lcs-certmanager -c");
		$html .= "<ul>\n<li>\n";  
		$html .= "\tLe certificat SSL du serveur LCS a été regénéré pour une période de 365 jours. \n";
		$html .= "</ul>\n</li>\n";
		echo $html;        
	}
}// fin is_admin
else echo "Vous n'avez pas les droits nécessaires pour ordonner cette action...";
echo "</div><!-- Fin container-->\n";
include ("../lcs/includes/pieds_de_page.inc.php");
?>