<?php
/* lcs/logout.php version du : 14/12/2011*/
require ("./includes/headerauth.inc.php");
// Liste des applis installees
$result=@mysql_db_query("$DBAUTH","SELECT * from applis", $authlink);
if ($result)
	while ($r=mysql_fetch_array($result))
		$$r["name"]=$r["value"];
else
	die ("param&#232;tres absents de la base de donn&#233;es");
mysql_free_result($result);
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
// Requetes de logout
$javascript = "<script language='JavaScript' type='text/javascript'>\n";
$javascript .= "// <![CDATA[\n";
if ( $spip=="1" ) {
	// Logout spip
	$URLSPIP = '../spip/?action=logout&logout=prive';
	$javascript .= "	$.ajax({\n";
	$javascript .= "	type: 'POST',\n";
	$javascript .= "    url : '$URLSPIP',\n";
	$javascript .= "    async: false,\n";
 	$javascript .= "	error: function() {\n";
 	$javascript .= "		console.log('Echec logout SPIP');\n";
  	$javascript .= "		}\n";
  	$javascript .= "	});\n";
}
if ( $squirrelmail=="1" ) {
	// Logout squirrelmail
	$URLSQUIRRELMAIL ='../squirrelmail/src/signout.php';
	$javascript .= "	$.ajax({\n";
	$javascript .= "	type: 'POST',\n";
	$javascript .= "    url : '$URLSQUIRRELMAIL',\n";
	$javascript .= "    async: false,\n";
 	$javascript .= "	error: function() {\n";
 	$javascript .= "		console.log('Echec logout SQUIRRELMAIL');\n";
  	$javascript .= "		}\n";
  	$javascript .= "	});\n";
}

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
if ( $auth_mod != "ENT" )
	$javascript .= "top.location.href = '../lcs/index.php?url_redirect=accueil.php';\n";
else {
	$javascript .= "top.location.href = '../lcs/logout_ent.php';\n";
}
$javascript .= "//]]>\n";
$javascript .= "</script>\n";
echo $javascript;
echo "</body>\n</html>\n";
?>