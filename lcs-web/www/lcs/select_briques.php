<? /* lcs/select_briques.php derniere mise a jour : 10/12/2007 */
require "./includes/headerauth.inc.php";
require "../Annu/includes/ldap.inc.php";
require "../Annu/includes/ihm.inc.php";

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

$html = "
<head>\n
        <title>...::: Sélection briques fonctionnelles LCS  :::...</title>\n
        <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
</head>\n
<body>\n";
$html .= "<div align='center'><h2>Choix des applications LCS</h2></div>\n";

if ($is_admin = is_admin("Lcs_is_admin",$login)=="Y") {
	// Lecture des paramètres dans la table briques
	$query="SELECT * from applis";
	$result=@mysql_db_query("$DBAUTH",$query, $authlink);
	if (!$submit) {
        	if ($result)
                	while ($r=mysql_fetch_array($result))
                        	$$r["name"]=$r["value"];				
        	else
                	die ("paramètres absents de la base de données !");
        	// Presentation du formulaire
        	$html .= "<form action='select_briques.php' method='post'>\n";
 		$html .= "  <table border='0'>\n";
        	echo $html;
/*
                echo "            <tr><td>Forum</td><td>\n";
                echo "                   <select name='forum'>\n";
                echo "                           <option value='phpweblog' ";
                if ($phpweblog==1) echo "selected";
                echo ">phpweblog</option>\n";
                echo "                           <option value='spip' ";
                if ($spip==1) echo "selected";
                echo ">spip</option>\n";
                echo "                   </select>\n</td></tr>\n";
*/
        	echo "        <tr><td>Webmail (squirrelmail)</td><td><input type='checkbox' name='mail'";
        	if ($mail=="1") echo "checked";
        	echo " ></td></tr>\n";

		if ( file_exists("/var/www/burpeda") ) {
        		echo "         <tr><td>Bureau p&eacute;dagogique (burpeda)</td><td><input type='checkbox' name='burpeda'";
        		if ($burpeda=="1") echo "checked";
        		echo "></td></tr>\n";
		}
		// Choix de la barre a Mine
		$html = "<tr>\n<td>S&eacute;lection barre d'ic&ocirc;nes </td>\n<td>\n";
		$html .= "<select name='barreamine'>\n";
  		$html .= "<option value='barre1' ";
                	if ($barre1==1) $html .= "selected";
    		$html .=">Version 1</option>\n";
      		$html .= "<option value='barre2' ";
                	if ($barre2==1) $html .= "selected";
        	$html .= ">Version 2</option>\n";
         	$html .= "</select>\n</td></tr>\n";
        	$html .= "         <tr><td>Page d'accueil LCS (URL) </td><td><input size='50' type='text' name='url_accueil' value='$url_accueil'></td></tr>\n";
        	$html .= "         <tr><td> Lien Logo LCS (URL) </td><td><input size='50' type='text' name='url_logo' value='$url_logo'></td></tr>\n";		
        	$html .="  </table>\n";
        	$html .="  <input type='hidden' name='submit' value='true'>\n";
        	$html .="  <input type='submit' value='Valider'>\n";
        	$html .="</form>\n";
        	echo $html;
	} else {
        	// Mise a jour des parametres dans la table briques
        	$query="SELECT * from applis";
        	$result=mysql_query($query);
        	if ($result) {
                    while ($r=mysql_fetch_array($result)) {
                        #echo "DEBUG >> ".$r["name"]." ".$$r["name"]." ".$r["value"]."<br>";
                        if  ($$r["name"] == "on") $$r["name"] ="1"; elseif ($r["name"] !="url_accueil" && $r["name"] !="url_logo" ) $$r["name"] ="0";
 #               	if ($forum=="phpweblog" && $r["name"]=="phpweblog") $$r["name"]="1";
 #               	if ($forum=="spip" && $r["name"]=="spip") $$r["name"]="1";
                	if ($barreamine=="barre1" && $r["name"]=="barre1") $$r["name"]="1";
                	if ($barreamine=="barre2" && $r["name"]=="barre2") $$r["name"]="1";
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
                	print "<div class=alert_msg>".gettext("Vos choix ont été pris en compte ! ")."</div>\n";
        	elseif (!$modif)
              		print "<div class=alert_msg>".gettext("Aucune modification n'a été apportée  ! ")."</div>\n";
        	else
        		print "<div class=error_msg>".gettext("Oops: la requete ") . "<STRONG>$query1</STRONG>" . gettext(" a provoqué une erreur !!!")."</div>";
	}
	mysql_free_result($result);
} else 
	echo "$html<div class=alert_msg>".gettext("Cette fonctionnalité, nécessite les droits d'administrateur du serveur LCS !")."</div>";
require ("./includes/pieds_de_page.inc.php");
?>
e.inc.php");
?>
