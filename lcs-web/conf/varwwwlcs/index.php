<?
require  "../lcs/includes/headerauth.inc.php";
$query = "SELECT * from applis";
$result=@mysql_db_query("$DBAUTH",$query, $authlink);
if ($result)
    while ($r=@mysql_fetch_array($result))
                $$r["name"]=$r["value"];
else
    die ("param�tres absents de la base de donn�es");
@mysql_free_result($result);

if ( $url_redirect == "accueil.php" || $url_redirect == "../squidGuard/pageinterdite.html" )$url_accueil = $url_redirect;
?>
<html>
<head>
<title>...::: LCS :::...</title>
<LINK REL="SHORTCUT ICON" HREF="images/favicon.ico" />
</head>
<FRAMESET ROWS="105,*" BORDER="0" >
	<FRAME SRC="barre.php" NAME="barre" SCROLLING="no"></FRAME>
        <FRAME SRC="<? echo"$url_accueil"; ?>" NAME="principale"></FRAME>
</FRAMESET>
</html>
