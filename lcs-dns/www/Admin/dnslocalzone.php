<?php
include "../Annu/includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];


include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

// Purifier
if (count($_GET)>0) {
  //configuration objet
  include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
  $config = HTMLPurifier_Config::createDefault();
  $purifier = new HTMLPurifier($config);
  //purification des variables
  $do=$purifier->purify($_GET['do']);
}
// Messages d'aide
function msgaide($msg) {
    return ("&nbsp;<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('".$msg."')")."\"><img name=\"action_image2\"  src=\"../images/help-info.gif\" ALT=\"Infos\"></u>");
}
$msg1="G&#233;n&#232;re un fichier CSV de la zone DNS locale disponible dans le r&#233;pertoire <b>Documents > ZoneDNS</b> du compte administrateur.";
$msg2="Met &#224; jour la zone DNS locale &#224; partir du fichier CSV.";

$html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n
	  <head>\n
	  <title>...::: Gestion zone DNS locale LCS  :::...</title>\n
	  <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
	  </head>\n
	  <body>\n";
$html .= "<div id='container'><h2>Gestion zone locale DNS LCS</h2>\n";
echo $html;
if (is_admin("system_is_admin",$login)=="Y") {
	$html = "<ul>\n";
	$html .= "<li><a href='dnslocalzone.php?do=csv&jeton=".md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF']))." ' target ='main'>G&#233;n&#233;ration d'un fichier CSV de la zone DNS locale</a>".msgaide($msg1)."</li>\n";
	$html .= "<li><a href='dnslocalzone.php?do=majz&jeton=".md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF']))." ' target ='main'>Mise a jour zone DNS locale</a>".msgaide($msg2)."</li>\n";
	$html .= "</ul>\n";
	$html .= "<h2>Edition zone DNS locale</h2>\n";
	$html .= "<p>Pour &#233;diter et modifier la zone DNS locale, utiliser le module <strong>elfinder</strong> pour modifier le fichier CSV de la zone locale puis, clicker sur &#171; Mise  &#224; jour zone DNS locale &#187;.</p>\n";
	echo $html;	
	if ( $do == "csv" ) {
		echo "gen csv";
		exec ("/usr/bin/sudo /usr/sbin/lcs-dns-gencsv" , $AllOutput, $ReturnValue);
	} elseif ( $do == "majz" ) {
		exec ("/usr/bin/sudo /usr/sbin/lcs-dns-genlocalzone" , $AllOutput, $ReturnValue);
	}
	
}// fin is_admin
else echo "Vous n'avez pas les droits n&#233;cessaires pour ordonner cette action...";
echo "</div><!-- Fin container-->\n";
include ("../lcs/includes/pieds_de_page.inc.php");
?>
