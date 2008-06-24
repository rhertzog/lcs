<?php 
/* lcs/applis.php derniere mise a jour : 24/06/2008 */

include ("./includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");
include ("./includes/jlcipher.inc.php");

define('NB_COLONNES',3);

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
$html .= "<HTML>\n";
$html .= "<HEAD>\n";
$html .= "  <META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=ISO-8859-1\">\n";
$html .= "  <TITLE>Applications LCS</TITLE>\n";
$html .= "  <LINK  href='../style.css' rel='StyleSheet' type='text/css'>\n";
$html .= "</HEAD>\n";
$html .= "<BODY style=\"background:#F8F8FF;\">\n";
$html  .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\">\n";

#############
$liste = array();
$liste['Liens'] = array();
$liste['Images'] = array();
$liste['Titres'] = array();
#############


// Appli Annuaire
$liste['Images'][] = "images/bt-V2-3.jpg";
$liste['Liens'][] = "statandgo.php?use=Annu";
$liste['Titres'][] = "Annuaire des utilisateurs";

// Affichage des Menus users non privilégiés

  // lecture lcs_applis 
  $query="SELECT  name, value from applis where type='M' order by name";
  $result=mysql_query($query);
  if ($result) {
        while ( $r=mysql_fetch_object($result) ) {
            if ( $r->name == "clientftp" ) $ftpclient = true;
            if ( $r->name == "pma" ) $pma = true;
            if ( $r->name == "smbwebclient" ) $smbwebclient = true;            
        }
    }
    mysql_free_result($result);

if ( $ftpclient ) {
  $liste['Images'][] = "images/bt-V1-2.jpg";
  $liste['Liens'][] = "statandgo.php?use=clientftp";
  $liste['Titres'][] = "Client FTP";
}
if ( $pma ) {
  $liste['Images'][] = "images/bt-V1-3.jpg";
  $liste['Liens'][] = "statandgo.php?use=pma";
  $liste['Titres'][] = "Gestion base de données";
}
if ( $se3netbios != "" && $se3domain != "" && $smbwebclient ) {
  $liste['Images'][] = "images/bt-V1-4.jpg";
  $liste['Liens'][] = "statandgo.php?use=smbwebclient";
  $liste['Titres'][] = "Client SE3";
}

// Liens dynamiques vers les plugins installés 
$query="SELECT * from applis where type='P' order by name";
$result=mysql_query($query);
if ($result) {
        while ($r=mysql_fetch_object($result)) {
        if (( $r->value == "1" ) and ! ( file_exists("/usr/share/lcs/Plugins/".$r->chemin."/.applihide"))) {
        $liste['Images'][] = "../Plugins/".$r->chemin."/Images/plugin_icon.png";
        $liste['Liens'][] = "statandgo.php?use=".$r->name;
        $liste['Titres'][] = $r->descr;

            }
        }
}
mysql_free_result($result);

if (NB_COLONNES == 0)
    $nbCol = 1;
else
    $nbCol = NB_COLONNES;

array_multisort($liste['Titres'],$liste['Liens'],$liste['Images']);
for ($x=0;$x<count($liste['Titres']);$x++) {
if (($x % $nbCol) == 0)
    $html   .= "<tr>\n";
    $html   .= "<td width=\"60\"><img alt=\"".$liste['Titres'][$x]."\" src=\"".$liste['Images'][$x]."\" align=\"middle\"></td>\n";
    $html   .="<td><a href=\"".$liste['Liens'][$x]."\">".$liste['Titres'][$x]."</a></td>\n";
if (($x % $nbCol) == ($nbCol-1))
    $html .= "</tr>\n";
}


$html .= "</tr>\n</table>\n";

echo $html;



include ("./includes/pieds_de_page.inc.php");
?>