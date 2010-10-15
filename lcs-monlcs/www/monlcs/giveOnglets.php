<?
include("includes/secure_no_header.inc.php");
$sql = "SELECT * from `ml_zones` where user='all' or user='$uid' order by `rang`;";
$curseur=mysql_query($sql) or die("<ul><li>$sql requete invalide</li></ul>");


for ($x=0;$x<mysql_num_rows($curseur);$x++) {
	$R = mysql_fetch_object($curseur);
	$id=$R->id;
	$status = $R->status;
	$u=$R->user;
	$content2 .="<a>".$R->nom;
	if ( ( $status == 'etab') && ($ML_Adm == "Y") )
		$content2 .= "<span id=rt$id class=gest_tab onclick=gest_tab_etab($id) >&nbsp;&nbsp;&nbsp;</span>";
	if ( $u != 'all')
		$content2 .= "<span id=rt$id class=gest_tab onclick=gest_tab($id) >&nbsp;&nbsp;&nbsp;</span>";
	$content2 .="</a>";
	
}



print(stringForJavascript($content2));
?>
