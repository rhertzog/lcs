<?php
/* lcs/logout.php version du : 19/03/2015*/
require ("./includes/headerauth.inc.php");
// Liste des applis installees
$spip=$squirrelmail=$roundcube="";
$result=@((mysqli_query( $authlink, "USE $DBAUTH")) ? mysqli_query( $authlink, "SELECT * from applis") : false);
if ($result)
	while ($r=mysqli_fetch_array($result))
		$$r["name"]=$r["value"];
else
	die ("param&#232;tres absents de la base de donn&#233;es");
((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
// Close session LCS
close_session();
// HTML Header
$html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
$html .= "<html>\n";
$html .= "<head>\n";
$html .= "<title>...::: Logout LCS :::...</title>\n";
$html .= " <meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"tetx/html; charset=utf-8\">\n";
$html .=  "<script type=\"text/javascript\" src=\"../libjs/jquery/jquery.js\"></script>\n";
$html .= "</head>\n";
$html .= "<body>\n";
echo $html;
// Requetes de logout
$javascript = "<script language='JavaScript' type='text/javascript'>\n";
$javascript .= "// <![CDATA[\n";
if ( $roundcube=="1" && $auth_mod != "ENT" ) {
	// Logout roundcube si mod_auth est different d'ENT
	$URLROUNDCUBE = '../roundcube/?_task=logout';
	$javascript .= "	$.ajax({\n";
	$javascript .= "	type: 'POST',\n";
	$javascript .= "    url : '$URLROUNDCUBE',\n";
	$javascript .= "    async: false,\n";
 	$javascript .= "	error: function() {\n";
 	$javascript .= "		console.log('Echec logout ROUNDCUBE');\n";
  	$javascript .= "		}\n";
  	$javascript .= "	});\n";
}
// Redirection
if ( $spip=="1" ) {
	$javascript .= "top.location.href = '../spip/?action=logout&logout=prive';\n";
} else {
	$javascript .= "top.location.href = '../lcs/index.php?url_redirect=accueil.php';\n";	
}
$javascript .= "//]]>\n";
$javascript .= "</script>\n";
echo $javascript;
echo "</body>\n</html>\n";
?>