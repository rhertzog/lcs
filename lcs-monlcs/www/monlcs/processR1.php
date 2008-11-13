<?
include "includes/secure_no_header.inc.php";


$content .= "<table border=0>";
$content .="<tr>"
."<td colspan=2 class=grise>Action</td>"
."<td class=grise>Titre</td>"
."<td class=grise>Url</td>"
."</tr>";
if ($_POST['id'] == 'lcs') {


require ("/var/www/lcs/includes/config.inc.php");
require_once ("/var/www/lcs/includes/functions.inc.php");
$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");

// Liens dynamiques vers les outils LCS
$query="SELECT * from applis where type='M' order by name";
$result=mysql_query($query);
if ($result) {
        while ($r=mysql_fetch_object($result)) {
	   
	         $html .= "<tr>";
	         $html .= "  <td width=80><div onclick=viewOutil('".$r->name."');>$view_img</div></td>";
		  $html .= "  <td>$r->descr</td>";
	         $html .= "  <td>../lcs/statandgo.php?use=".$r->name."</td>";
	         $html .= "</tr>";
            
        }
}

// Liens dynamiques vers les plugins installés 
$query="SELECT * from applis where type='P' order by name";
$result=mysql_query($query);
if ($result) {
        while ($r=mysql_fetch_object($result)) {
	    if (( $r->value == "1" ) and ! ( file_exists("/usr/share/lcs/Plugins/".$r->chemin."/.applihide"))) {
	         $html .= "<tr>";
	         $html .= "  <td width=80><div onclick=viewOutil('".$r->chemin."');>$view_img</div></td>";
		  $html .= "  <td>$r->descr</td>";
	         $html .= "  <td>../lcs/statandgo.php?use=".$r->name."</td>";
	         $html .= "</tr>";
            }
        }
}
mysql_free_result($result);
$content .=$html;

}
else {

$sql = "SELECT * from `monlcs_db`.`ml_ressources` ;";
$curseur=mysql_query($sql) or die("<ul><li>$sql requete invalide</li></ul>");
for ($x=0;$x<mysql_num_rows($curseur);$x++) {
$R=mysql_fetch_object($curseur);
//print_r($R);
$content.="<tr>"
//."<td><div onclick=deleteRessources(".$R->id.");>$delete_img</div></td><td>"
."<td></td><td>"
."<div onclick=viewRessource(".$R->id.");>$view_img</div>"
."</td>"
."<td>$R->titre</td>"
."<td class=nom>$R->url</td>"
."</tr>";
}//fin for
}//fin else


$content .= "</table>";


print(stringForJavascript($content));
?>
