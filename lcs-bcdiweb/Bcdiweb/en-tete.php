<?
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
require ("/var/www/Annu/includes/ihm.inc.php");


	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\">\n";
	echo "<HTML>\n";
	echo "  <HEAD>\n";
        echo "    <META HTTP-EQUIV=\"Content-Type\" CONTENT=\"tetx/html; charset=ISO-8859-1\">\n";
        echo "    <LINK  href=\"/style.css\" rel=StyleSheet type=\"text/css\">\n";
	echo "    <TITLE>BcdiWeb</TITLE>\n";
	echo "  </HEAD>\n";
	echo "<BODY>\n";
        echo "<DIV id=\"container\">\n";
        
$login=isauth();
$login=$login[1];
// Prise en compte de la page demandée initialement - leb 25/6/2005
if ($login == "") {
        //      header("Location:$urlauth");
        $request = $PHP_SELF;
        if ( $_SERVER['QUERY_STRING'] != "") $request .= "?".$_SERVER['QUERY_STRING'];
        echo "<script language=\"JavaScript\" type=\"text/javascript\">\n<!--\n";
        echo "top.location.href = '$urlauth?request=" . rawurlencode($request) . "';\n";
        echo "//-->\n</script>\n";
} 
?>
