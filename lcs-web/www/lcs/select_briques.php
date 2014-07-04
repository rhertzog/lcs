<?
/*===========================================
   Projet LcSE3
   Administration du serveur LCS
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 23/05/2014
   ============================================= */
include "../Annu/includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];
if (count($_POST)>0) {
  //configuration objet
  include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
  $config = HTMLPurifier_Config::createDefault();
  $purifier = new HTMLPurifier($config);
  //purification des variables
 if (isset($_POST['submit'])) $submit=$purifier->purify($_POST['submit']);else $submit="";
 if (isset($_POST['url_accueil'])) $url_accueil=$purifier->purify($_POST['url_accueil']);else $url_accueil="";
 if (isset($_POST['url_logo'])) $url_logo=$purifier->purify($_POST['url_logo']);else $url_logo ="";
 }

require "./includes/headerauth.inc.php";
require "../Annu/includes/ldap.inc.php";
require "../Annu/includes/ihm.inc.php";

$html = "
<head>\n
        <title>...::: S&eacute;lection briques fonctionnelles LCS  :::...</title>\n
        <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
</head>\n
<body>\n";
$html .= "<div align='center'><h2>Configuration des liens LCS</h2></div>\n";

if ($is_admin = is_admin("Lcs_is_admin",$login)=="Y") {
	// Lecture des parametres dans la table briques
	if (!@mysql_select_db($DBAUTH, $authlink))
    		die ("S&#233;lection de base de donn&#233;es impossible.");
	$query="SELECT * from applis";
	$result=@mysql_query($query, $authlink);
	if (!isset($submit)) {
        	if ($result)
                	while ($r=mysql_fetch_array($result))
                        	$$r["name"]=$r["value"];
        	else
                	die ("param&egrave;tres absents de la base de donn&eacute;es !");
        	// Presentation du formulaire
        	$html .= "<form action='select_briques.php' method='post'>\n";
 		$html .= "  <table border='0'>\n";
        	echo $html;
        	$html = "         <tr>\n<td>Page d'accueil LCS (URL) </td><td><input size='40' type='text' name='url_accueil' value='$url_accueil'></td></tr>\n";
        	$html .= "         <tr><td> Lien Logo LCS (URL) </td><td><input size='40' type='text' name='url_logo' value='$url_logo'></td></tr>\n";
        	$html .="  </table>\n";
        	$html .="  <input type='hidden' name='submit' value='true'>\n";
                  $html .='<input name="jeton" type="hidden"  value="'.md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'" />';
        	$html .="  <input type='submit' value='Valider'>\n";
        	$html .="</form>\n";
        	echo $html;
	} else {
        	// Mise a jour des parametres dans la table applis
        	$query="SELECT * from applis where  name = 'url_logo' or name ='url_accueil'";
        	$result=mysql_query($query);
        	if ($result) {
                    while ($r=mysql_fetch_array($result)) {
		                if ($r["value"]!=$$r["name"] && $r["type"]!="P") {
	                 	    // Mise a jour de la base de donnees
	                   	    $query1="UPDATE applis SET value=\"".$$r["name"]."\" WHERE name=\"".$r["name"]."\"";
	                   	    $result1=mysql_query($query1);
	                   	    $modif=true;
	                	}
                    }
                }
        	echo $html;
        	if ($result1 && $modif)
                	print "<div class=alert_msg>".gettext("Vos choix ont &eacute;t&eacute; pris en compte ! ")."</div>\n";
        	elseif (!$modif)
              		print "<div class=alert_msg>".gettext("Aucune modification n'a &eacute;t&eacute; apport&eacute;e  ! ")."</div>\n";
        	else
        		print "<div class=error_msg>".gettext("Oops: la requete ") . "<STRONG>$query1</STRONG>" . gettext(" a provoqu&eacute; une erreur !!!")."</div>";
	}
	mysql_free_result($result);
} else
	echo "$html<div class=alert_msg>".gettext("Cette fonctionnalit&eacute;, n&eacute;cessite les droits d'administrateur du serveur LCS !")."</div>";
require ("./includes/pieds_de_page.inc.php");
?>