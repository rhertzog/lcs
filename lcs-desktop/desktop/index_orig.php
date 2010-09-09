<?
require  "../lcs/includes/headerauth.inc.php";
$query = "SELECT * from applis";
$result=@mysql_db_query("$DBAUTH",$query, $authlink);
if ($result)
    while ($r=@mysql_fetch_array($result))
                $$r["name"]=$r["value"];
else
    die ("paramètres absents de la base de données");
@mysql_free_result($result);

if ( $url_redirect == "accueil.php" || $url_redirect == "../squidGuard/pageinterdite.html" ) $url_accueil = $url_redirect;

if ( $url_accueil == "accueil.php" && is_dir ("/var/www/monlcs") )  
  $url_accueil = "/monlcs/index.php";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
  <title>...::: LCS :::...</title>
  <link rel="SHORTCUT ICON" href="images/favicon.ico">
</head>
<frameset rows="90,*" border="0">
  <frame src="barre.php" name="barre" scrolling="no">
  <frame src="<? echo"$url_accueil"; ?>" name="principale">
</frameset>
</html>

