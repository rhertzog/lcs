<?
/*===========================================
   Projet LcSE3
   Administration du serveur LCS
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 15/05/2014
   ============================================= */
//configuration objet
  include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
  $config = HTMLPurifier_Config::createDefault();
  $purifier = new HTMLPurifier($config);
  //purification des variables
$url_redirect=(isset($_GET['url_redirect'])) ? $purifier->purify($_GET['url_redirect']) : '';
require  "../lcs/includes/headerauth.inc.php";
//init variables
$desktop=$monlcs=0;
$query = "SELECT * from applis";
$result=@mysqli_query( $authlink, $query);
if ($result)
    while ($r=@mysqli_fetch_array($result))
                $$r["name"]=$r["value"];
else
    die ("parametres absents de la base de donnees");
@((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

// Redirects IE5 and IE6 users.
$ua = $_SERVER['HTTP_USER_AGENT'];
$ie5 = mb_strpos($ua, 'MSIE 5') !== false;
$ie6 = mb_strpos($ua, 'MSIE 6') !== false;
// Redirects to desktop if not IE.
if ( $desktop ==1 && ! $ie5 && ! $ie6 ) header("Location:../desktop/");
if ( $url_redirect == "accueil.php" || $url_redirect == "../squidGuard/pageinterdite.html" ) $url_accueil = $url_redirect;
if ( $url_accueil == "accueil.php" && $monlcs== 1 ) $url_accueil = "/monlcs/index.php";
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

