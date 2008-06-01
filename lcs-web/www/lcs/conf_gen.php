<? /* lcs/conf_gen.php maj : 13/10/2003 */
require "./includes/headerauth.inc.php";
require "../Annu/includes/ldap.inc.php";
require "../Annu/includes/ihm.inc.php";

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

$html = "
<head>\n
        <title>...::: Configuration générale LCS  :::...</title>\n
        <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
</head>\n
<body>\n";

if ($is_admin = is_admin("Lcs_is_admin",$login)=="Y") {
	$html .= "<div align='center'><h2>Configuration générale LCS</h2></div>\n";
	$html .= "<br><br><br><blockquote>\n";
	$html .="<ul>\n";
	$html .="<li><a href='" . $baseurl . "lcs/setup_keys.php'>Renouvellement clés d'authentification</a><br>\n";
	$html .="<li><a href='" . $baseurl . "lcs/select_briques.php'>Choix des applications disponibles dans la barre de menu LCS</a><br>\n";
	$html .="</ul>\n";
	$html .= "</blockquote><br><br><br>\n";
	echo $html;
} else {
        echo "$html<div class=alert_msg>".gettext("Cette fonctionnalité, nécessite les droits d'administrateur du serveur LCS !")."</div>";
}
require ("./includes/pieds_de_page.inc.php");
?>
