<?php /* lcs/applis.php derniere mise a jour : 11/01/2008 Philippe Leclerc */
include ("./includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");
include ("./includes/jlcipher.inc.php");

// Lecture de la table applis
$result=@mysql_db_query("$DBAUTH","SELECT * from applis", $authlink);
if ($result)
    while ($r=mysql_fetch_array($result))
                $$r["name"]=$r["value"];
else
    die ("paramètres absents de la base de données");
mysql_free_result($result);
// verification de l'authentification
list ($idpers, $login)= isauth();
if ($idpers == "0")    header("Location:$urlauth");
$html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
$html .= "<html>\n";
$html .= "<body>\n";
$html .= "  <meta http-equiv=\"Content-Type\" CONTENT=\"tetx/html; charset=ISO-8859-1\">\n";
$html .= "  <title>Applications LCS</title>\n";
$html .= "  <link  href='../style.css' rel='StyleSheet' type='text/css'>\n";
$html .= "</head>\n";
$html .= "<body>\n";
$html  .= "<div style=\"min-height:320px;\">\n";
$html  .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\">\n";
// Appli Annuaire
$html .= "<tr>\n";
$html .= "  <td width=\"80\"><img src=\"images/bt-V2-3.jpg\" alt=\"Annuaire\" width=\"75\" height=\"75\"></td>\n";
$html .= "  <td><a href=\"statandgo.php?use=Annu\">Annuaire des utilisateurs</a></td>\n";
//modif Phil
//$html .= "</tr>\n";
// Liens dynamiques vers les plugins installés 
$query="SELECT * from applis where type='P'AND value='1' order by name";//Modif : on ne sélectionne que les plugins actifs
$result=mysql_query($query);
if ($result) {
		$flip=true;//on met un flip-flop sur flip
        while ($r=mysql_fetch_object($result)) {
	    if (( $r->value == "1" ) and ! ( file_exists("/usr/share/lcs/Plugins/".$r->chemin."/.applihide")))
			{
			//$html .= "<tr>\n";
	         $html .= "  <td width=\"80\"><img src=\"../Plugins/".$r->chemin."/Images/plugin_icon.png\" alt=\"".$r->descr.
			 "\" width=\"75\" 	height=\"75\"></td>\n";
	         $html .= "  <td><a href=\"statandgo.php?use=".$r->name."\">".$r->descr."</a></td>\n";
	         if ($flip) $html .= "</tr>\n";
			}
			$flip=!$flip;//on flop
			//fin Modif phil
        }
}
mysql_free_result($result);
$html .= "</table>\n</div>";
echo $html;
include ("./includes/pieds_de_page.inc.php");
?>
