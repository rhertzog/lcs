<?php
/* lcs/logout.php version du :  09/12/2011*/
require ("./includes/headerauth.inc.php");
// Logout LCS session 
list ($idpers,$login)= isauth();
close_session($idpers);
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

$javascript = "<script language='JavaScript' type='text/javascript'>\n";
if ( ! is_dir('/usr/share/lcs/spip') ) {
	// Logout spip
	$URLSPIP = $baseurl.'spip/?action=logout&logout=prive';
	$javascript .= "// <![CDATA[\n";
	$javascript .= "	$.ajax({\n";
	$javascript .= "		type: 'POST',\n";
	$javascript .= "        url : '$URLSPIP',\n";
	$javascript .= "        async: true,\n";
 	$javascript .= "        error: function() {\n";
 	$javascript .= "        			alert('Echec logout espace prive SPIP');\n";
  	$javascript .= "        		}\n";
  	$javascript .= "	});\n";
  	$javascript .= "//]]>\n";
}
// Redirection
$javascript .= "top.location.href = '../lcs/index.php?url_redirect=accueil.php';\n";
$javascript .= "</script>\n";
echo $javascript;
echo "</body>\n</html>\n";
?>