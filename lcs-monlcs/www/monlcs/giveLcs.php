<?
include "includes/secure_no_header.inc.php";


$sql = "select * from ml_tabs where nom='lcs';";
$curseur=mysql_query($sql) or die(stringForJavascript("ERR $sql"));
if ( mysql_num_rows($curseur) != 0 ) {
	$idTab = mysql_result($curseur,0,'id');
}


require ("/var/www/lcs/includes/config.inc.php");
require_once ("/var/www/lcs/includes/functions.inc.php");
$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");



$html = "<table id=cmd>";

// Liens dynamiques vers les outils LCS
$query="SELECT * from applis where type='M' or type='P' order by name";
$result=mysql_query($query);
if ($result) {
        while ($r=mysql_fetch_object($result)) {
	   	  if (!eregi('pla',$r->name) && ($r->name != 'monlcs')) {	
	         $html .= "<tr>";
	         $html .= "  <td width=80><div onclick=viewOutil('".$r->name."');>$view_img</div></td>";
		  $html .= "  <td>$r->descr</td>";
	         $html .= "  <td>../lcs/statandgo.php?use=".$r->name."</td>";
	         $html .= "</tr>";
            	}
        }
}

mysql_free_result($result);
$content .=$html;




$content .= "</table>";

if ( $ML_Adm == 'Y' ) {
	
	$cx = mysql_connect($host,$userDB,$passDB) or die('ERREUR ACCES SQL');
	mysql_select_db($DB) or die('choix base ko');

	$sqlRessImp = "SELECT * FROM ml_ressourcesAffect WHERE id_menu='$idTab'";
	$cxRessImp = mysql_query($sqlRessImp) or die ("ERR $sqlRessImp");
	if (mysql_num_rows($cxRessImp) > 0 ) {
	$content .= "<br /><p><b>Imposées:</b></p>";
	$content .= "<table id=cmd>";
	
	while($R = mysql_fetch_object($cxRessImp)) {
		$content.="<tr><td><div onclick=deleteImpose('".$R->id."')>$delete_img</div></td><td>".giveLcsName($R->id_ressource)."</td><td>$R->setter</td><td>$R->cible</td></tr>";
	
		
		

	}//while
	$content .= "</table>";
	}//if result
}


print(stringForJavascript($content));
?>
