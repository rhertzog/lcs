<?
session_start();
include "includes/secure_no_header.inc.php";
$content = "<table id=cmd>";
$content .="<tr>"
."<td colspan=4 class=grise>Action</td>"
."<td class=grise>Titre</td>"
."<td class=grise>Propos&eacute; par</td>"
."<td class=grise>Cible</td>"
."</tr>";

extract($_POST);

if (!empty($matiere))
	$_SESSION['SCEN_MAT'] = $matiere;

$ids = array();
$sql = "SELECT * from `monlcs_db`.`ml_scenarios` WHERE matiere='$matiere' ;";
$curseur=mysql_query($sql) or die("ERREUR $sql");


for ($x=0;$x<mysql_num_rows($curseur);$x++) {
$R=mysql_fetch_object($curseur);
$groups = give_groupes_uid($uid);


if (dans_cible($R->cible) || ($uid == $R->setter)  || ($ML_Adm == 'Y') || is_administratif($uid)) {
if (!in_array($R->id_scen,$ids))
		{
			if (!is_eleve($uid) || (is_eleve($uid) && ($R->enabled == 1)) )

				$ids[] = $R->id_scen;
		}
}
}//fin for



for ($x=0;$x<count($ids);$x++) {
		$cibles = array(); 
		$sq = "SELECT * from `monlcs_db`.`ml_scenarios` WHERE matiere='$matiere' and id_scen='$ids[$x]' ;";
		$c = mysql_query($sq) or die("ERR $sq");
		$R=mysql_fetch_object($c);
		
		$titre2 = stringForJavascript($R->titre);
		$titre2 = ereg_replace(' ', '+', $titre2);
		$content.="<tr>";

		$content.="<td><div id=helpS$R->id class=helpS $help_img</div></td>";

		
		if ( ($R->setter == $uid) || ($ML_Adm == 'Y') )
			$content .= "<td><div onclick=deleteScen('".$R->id_scen."');>$delete_img</div></td>";
		else
			$content .= "<td>-</td>";

		if ($R->enabled == 1)
			$prefix = 'un';
		else
			$prefix = "";

		$commute_img = "<img id=img_lock".$R->id_scen." width=20px; src=/monlcs/images/".$prefix."locked.png></img>";

		if ( ($R->setter == $uid) || ($ML_Adm == 'Y') )
			$content .= "<td><div onclick=commuteScen('".$R->id_scen."');>$commute_img</div></td>";
		else
			$content .= "<td>-</td>";

		
		$content.="<td><div onclick=viewScen('".$R->id_scen."');>$view_img</div></td><td><div id=scen_$R->id_scen>$R->titre</div>";
		if ( ($R->setter == $uid) || ($ML_Adm == 'Y')  )
			$content .= "<div onclick=renameScenario('".$R->id_scen."');>$rename_img</div>";


		$content .="</td>";
		$content .="<td class=nom>$R->setter</td>";

		for ($xx=0;$xx<mysql_num_rows($c);$xx++) {
		$cible = mysql_result($c,$xx,'cible');
		if (!in_array($cible,$cibles))
			$cibles[] = $cible;
		}//for curseur
		
		$content.="<td class=nom>".implode(':',$cibles)."</td>"
		."</tr>";
		
		}//for titres
$content .= "</table>";


print(stringForJavascript($content));
?>
