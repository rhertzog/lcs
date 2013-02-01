<?
# /var/www/Admin/gencertssl.php derniere version du : 18/01/2013
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

list ($idpers, $login)= isauth();

$newcert = $_GET['newcert'];

// Messages d'aide
function msgaide($msg) {
    return ("&nbsp;<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('".$msg."')")."\"><img name=\"action_image2\"  src=\"../images/help-info.gif\"></u>");
}
$msg1="Régénération du certificat SSL pour les services LCS (CAS, apache-ssl, imap-ssl) une période de 365 jours.";

if ($idpers == "0") header("Location:$urlauth");
$html = "
	  <head>\n
	  <title>...::: Génération certificat apache ssl  :::...</title>\n
	  <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
	  </head>\n
	  <body>\n";
$html .= "<div id='container'><h2>Services intranet LCS</h2>\n";
echo $html;
if (is_admin("system_is_admin",$login)=="Y") {
    $html = "<h3>Regénération certificat SSL LCS</h3>\n";
    if ( ! isset($newcert) ) {
        $html .= "<ul>\n<li>\n";  
        $html .= "\t<a href=\"gencertssl.php?newcert=1\">Lancer la régénération du certificat.</a>".msgaide($msg1)."\n";
        $html .= "</ul>\n</li>\n";
        echo $html;
    } else {
            #$hostname
            #$domain
            $fqn="$hostname.$domain";
            #$country
            #$province
            #$locality
            #$organization
            #$organizationalunit

        exec("/usr/bin/sudo /usr/sbin/lcs-certssl-gen '$fqn' '$domain' '$country' '$province' '$locality' '$organization' '$organizationalunit'");
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