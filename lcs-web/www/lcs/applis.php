<?php 
/* lcs/applis.php derniere mise a jour : 30/09/2010 */

include ("./includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");
include ("./includes/jlcipher.inc.php");

define('NB_COLONNES',3);

// Lecture de la table applis
if (!@mysql_select_db($DBAUTH, $authlink)) 
    die ("S&#233;lection de base de donn&#233;es impossible.");
$query="SELECT * from applis";
$result=@mysql_query($query, $authlink);
if ($result)
    while ($r=@mysql_fetch_array($result))
                $$r["name"]=$r["value"];
else
    die ("param&#232;tres absents de la base de donn&#233;es");
@mysql_free_result($result);

// Verification de l'authentification
list ($idpers, $login)= isauth();
if ($idpers == "0")    
    header("Location:$urlauth");
elseif ( pwdMustChange($login) ) header("Location:../Annu/must_change_default_pwd.php");

$html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
$html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n";
$html .= "<head>\n";
$html .= "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"/>\n";
$html .= "  <title>Applications LCS</title>\n";
$html .= "  <link  href='../style.css' rel='StyleSheet' type='text/css'/>\n";
$html .= "</head>\n";
$html .= "<body style=\"background:#F8F8FF;\">\n";
$html  .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\">\n";

#############
$liste = array();
$liste['Liens'] = array();
$liste['Images'] = array();
$liste['Titres'] = array();
#############

// Appli Annuaire
$liste['Images'][] = "images/bt-V2-3.png";
$liste['Liens'][] = "statandgo.php?use=Annu";
$liste['Titres'][] = "Annuaire des utilisateurs";

// Affichage des Menus users non privilegies

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
  $liste['Titres'][] = "Gestion base de donn&#233;es";
}
if ( $se3netbios != "" && $se3domain != "" && $smbwebclient ) {
  $liste['Images'][] = "images/bt-V1-4.jpg";
  $liste['Liens'][] = "statandgo.php?use=smbwebclient";
  $liste['Titres'][] = "Client SE3";
}

// Liens dynamiques vers les plugins installes 
$query="SELECT * from applis where type='P' OR type='N' order by name";
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
    $html   .= "<td width=\"80\"><img alt=\"".$liste['Titres'][$x]."\" src=\"".$liste['Images'][$x]."\" align=\"middle\"/></td>\n";
    $html   .="<td><a href=\"".$liste['Liens'][$x]."\">".$liste['Titres'][$x]."</a></td>\n";
    if (($x % $nbCol) == ($nbCol-1))
      $html .= "</tr>\n";
}
if ($x%2 == 1) $html .= "</tr>\n";
$html .= "</table>\n";
echo $html;
include ("./includes/pieds_de_page.inc.php");
?>
