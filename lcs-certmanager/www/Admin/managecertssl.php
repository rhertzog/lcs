<?
# /var/www/Admin/managecertssl.php derniere version du : 12/04/2013
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

list ($idpers, $login)= isauth();

$newcert = $_GET['newcert'];

// Messages d'aide
function msgaide($msg) {
    return ("&nbsp;<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('".$msg."')")."\"><img name=\"action_image2\"  src=\"../images/help-info.gif\"></u>");
}
$msg1="";


function mktable($title, $content) {
	echo "<h3>$title</h3>\n";
	echo $content;
}



if ($idpers == "0") header("Location:$urlauth");
$html = "
	  <head>\n
	  <title>...::: Gestion certificats SSL  :::...</title>\n
	  <meta http-equiv='content-type' content='text/html;charset=utf-8' />\n
	  <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
	  </head>\n
	  <body>\n";
$html .= "<div id='container'><h2>Gestion certificats SSL LCS</h2>\n";
echo $html;
if (is_admin("system_is_admin",$login)=="Y") {

    $query="SELECT * from sslcert";
	$result=mysql_query($query);
    if ($result) {
		while ($r=mysql_fetch_array($result)) {
			echo $r['id']." ";
			echo $r['name']. " ";
			echo $r['before']. " ";
			echo $r['after']. " ";
			echo $r['description']. " ";
			echo $r['sel']." ";

			echo "<br />";
		}
    }	
    mysql_free_result($result);

}// fin is_admin
else echo "Vous n'avez pas les droits nécessaires pour ordonner cette action...";
echo "</div><!-- Fin container-->\n";
include ("../lcs/includes/pieds_de_page.inc.php");
?>