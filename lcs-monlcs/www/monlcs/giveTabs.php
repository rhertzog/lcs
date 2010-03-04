<?
include "includes/secure_no_header.inc.php";

$content2 ="";

$sqlx = "SELECT * from `ml_zones` where user='all' or user='$uid' order by rang ;";

$curseur=mysql_query($sqlx) or die("$sql requete invalide");

for ($x=0;$x<mysql_num_rows($curseur);$x++) {
	$R = mysql_fetch_object($curseur);
	$id = $R->rang;
	$u = $R->user;
	$nom_onglet = $R->nom;

$noms = array();
$captions = array();

$sql2 =  "SELECT * from `monlcs_db`.`ml_tabs` WHERE `id_tab`='$R->id' and `user`='all' ORDER BY id;";
$curseur2=mysql_query($sql2) or die("$sql2 requete invalide");

for ($y=0;$y<mysql_num_rows($curseur2);$y++) {
	$RR=mysql_fetch_object($curseur2);
	$noms[] = $RR->nom;
	$captions[] = $RR->caption;
}

$sql3 =  "SELECT * from `monlcs_db`.`ml_tabs` WHERE `id_tab`='$R->id' and `user`='$uid' ORDER BY id;";
$curseur3=mysql_query($sql3) or die("$sql3 requete invalide");

for ($y=0;$y<mysql_num_rows($curseur3);$y++) {
	$RR=mysql_fetch_object($curseur3);
	$noms[] = $RR->nom;
	$captions[] = $RR->caption;
}


//montage du menu

$content2 .="<div id=submenu_".$id.">";
for($xx=0; $xx<count($noms);$xx++) {
	$content2.="	<a href=# id=".$noms[$xx]." class=tabs >".$captions[$xx]."</a>";
	}

if (!is_eleve($uid)  && ($u == 'all') && ($nom_onglet != 'Sc&eacute;narios')) {
	$content2.="	<a href=# id=perso".($id-1)." class=tabs >Proposer</a>";

}

if (trim($activate_acad_monlcs) == '1') {
	if (!is_eleve($uid)  && ($u == 'all') && ($nom_onglet == 'Sc&eacute;narios')) {
		$content2.="	<a href=# id=import_acad class=tabs >Importer du d&#233;pot acad&#233;mique</a>";
	}
	//if (($u == 'all') && ($nom_onglet == 'Sc&eacute;narios')) {
	//	$content2.="	<a href=# id=fiches_acad class=tabs >Fiches associ&#233;es</a>";
	//}
}


$content2.="</div>";

}

	
	print(stringForJavascript($content2));
?>
