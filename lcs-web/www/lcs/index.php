<?
$url_redirect=$_GET['url_redirect'];
require  "../lcs/includes/headerauth.inc.php";
$query = "SELECT * from applis";
$result=@mysql_db_query("$DBAUTH",$query, $authlink);
if ($result)
    while ($r=@mysql_fetch_array($result))
                $$r["name"]=$r["value"];
else
    die ("parametres absents de la base de donnees");
@mysql_free_result($result);

if ( $desktop == 1 ) header("Location:../desktop");

if ( $url_redirect == "accueil.php" || $url_redirect == "../squidGuard/pageinterdite.html" ) $url_accueil = $url_redirect;

if ( $url_accueil == "accueil.php" && $monlcs== 1 )  
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

