<?
 /* lcs/select_briques.php derniere mise a jour : 18/09/2008
conformité UTF8 et register_long_arrays = Off
 */
require "./includes/headerauth.inc.php";
require "../Annu/includes/ldap.inc.php";
require "../Annu/includes/ihm.inc.php";

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

//conformité register_long_arrays =off
if (isset($_POST['squirrelmail'])) $squirrelmail=$_POST['squirrelmail']; else $squirrelmail="" ;
if (isset($_POST['submit'])) $submit=$_POST['submit'];else $submit="";
if (isset($_POST['url_accueil'])) $url_accueil=$_POST['url_accueil'];else $url_accueil="";
if (isset($_POST['url_logo'])) $url_logo=$_POST['url_logo'];else $url_logo ="";

$html = "
<head>\n
        <title>...::: S&eacute;lection briques fonctionnelles LCS  :::...</title>\n
        <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
</head>\n
<body>\n";
$html .= "<div align='center'><h2>Configuration des liens et validation webmail</h2></div>\n";

if ($is_admin = is_admin("Lcs_is_admin",$login)=="Y") {
	// Lecture des paramètres dans la table briques
	$query="SELECT * from applis";
	$result=@mysql_db_query("$DBAUTH",$query, $authlink);
	if (!$submit) {
        	if ($result)
                	while ($r=mysql_fetch_array($result))
                        	$$r["name"]=$r["value"];				
        	else
                	die ("param&egrave;tres absents de la base de donn&eacute;es !");
        	// Presentation du formulaire
        	$html .= "<form action='select_briques.php' method='post'>\n";
 		$html .= "  <table border='0'>\n";
        	echo $html;

        	echo "        <tr><td>Webmail (squirrelmail)</td><td><input type='checkbox' name='squirrelmail'";
        	if ($squirrelmail=="1") echo "checked";
        	echo " ></td></tr>\n";

        	$html = "         <tr>\n<td>Page d'accueil LCS (URL) </td><td><input size='40' type='text' name='url_accueil' value='$url_accueil'></td></tr>\n";
        	$html .= "         <tr><td> Lien Logo LCS (URL) </td><td><input size='40' type='text' name='url_logo' value='$url_logo'></td></tr>\n";		
        	$html .="  </table>\n";
        	$html .="  <input type='hidden' name='submit' value='true'>\n";
        	$html .="  <input type='submit' value='Valider'>\n";
        	$html .="</form>\n";
        	echo $html;
	} else {	
        	// Mise a jour des parametres dans la table applis
        	$query="SELECT * from applis where name = 'squirrelmail' or name = 'url_logo' or name ='url_accueil'";
        	$result=mysql_query($query);
        	if ($result) {
                    while ($r=mysql_fetch_array($result)) {
                        if ( $r["name"] == "squirrelmail" ) 
                          if  ($$r["name"] == "on") $$r["name"] ="1"; else $$r["name"] ="0";
                        #echo "DEBUG >> ".$r["name"]." ".$$r["name"]." ".$r["value"]."<br>";
	                if ($r["value"]!=$$r["name"] && $r["type"]!="P") {
                 	    // Mise à jour de la base de données
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
