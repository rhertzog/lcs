<?
	include "includes/secure_no_header.inc.php";
	extract($_POST);

	$content = "<table id=cmd>";
	$content .="<tr>"
	."<td colspan=3 class=grise>Action</td>"
	."<td class=grise>Ressource</td>"
	."<td class=grise>Propos&eacute; par</td>"
	."<td class=grise>Cible</td>"
	."</tr>";

	$sql = "SELECT * from `monlcs_db`.`ml_ressourcesProposees` WHERE matiere='$matiere' ";
	$curseur=mysql_query($sql) or die("<ul><li>$sql requete invalide</li></ul>");
	$liste = array();
	$proposeurs = array();
	$liste_id = array();
	

	$groups = give_groupes_uid($uid);
	
	for ($x=0;$x<mysql_num_rows($curseur);$x++) {
		$R=mysql_fetch_object($curseur);
		if (dans_cible($R->cible) || ($R->setter == $uid) || ($ML_Adm == 'Y') || is_administratif($uid) ) {
			$liste[$R->id_ressource][] = $R->cible;
			$proposeurs[$R->id_ressource] = $R->setter;
			$liste_id[$R->id_ressource] = $R->id;
		}
	}

	foreach(array_keys($liste) as $key) {
		$sq = "select * from ml_ressources where id='$key'";
		$c = mysql_query($sq) or die("ERR $sq");
		$X = mysql_fetch_object($c);		
		if (eregi('lcs',$X->url))
			$X->url = $base_url.$X->url;
		$content .=	"<tr>";
		$content.="<td><div id=helpP$key class=helpP $help_img</div></td>";
		if ( ($proposeurs[$key] == $uid)  || ($ML_Adm == 'Y') )
			$content .= "<td><div onclick=deletePropose('$key');>$delete_img</div></td>";
		else
			$content .="<td>-</td>";
		$content.="<td>";
		$content.="<div onclick=view_Url('".$X->id."','".$X->url."');>$view_img</div>"
		."</td>"
		."<td class=nom>$X->titre </td>"
		."<td class=nom>$R->setter</td>";
		$cibles = implode(' ',$liste[$key]);
		$content.="<td class=nom>".$cibles."</td>"
		."</tr>";		
	}

	$content .= "</table>";
	print(stringForJavascript($content));
?>
