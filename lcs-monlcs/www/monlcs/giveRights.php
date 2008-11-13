<?
include "includes/secure.inc.php";

$sql = "SELECT * from `monlcs_db`.`ml_droits` ORDER BY identifiant ASC;";
$curseur=mysql_query($sql) or die("<ul><li>$sql requete invalide</li></ul>");
$content = "<table border=0>";
$content .="<tr>"
."<td class=grise>Utilsateur ou Groupe</td>"
."<td class=grise>Identifiant</td>"
."<td class=grise>Peut proposer</td>"
."<td class=grise>Peut imposer</td>"
."<td class=grise>Cible</td>"
."<td class=grise>Type de document</td>"
."<tr>";
for ($x=0;$x<mysql_num_rows($curseur);$x++) {
$R=mysql_fetch_object($curseur);
//print_r($R);
$content.="<tr>";
if ($R->user_group == 'user') {
$content.="<td><div onclick=deleteRights(".$R->id.");>$delete_img</div> $user_img</td>";
}
else {

$content.="<td><div onclick=deleteRights(".$R->id.");>$delete_img</div> $group_img</td>";

}

if ($R->can_propose =='Y') { 
	$plus1 = 'checked' ;
}
else {
	$plus1 ='';
}
if ($R->can_impose =='Y') { 
	$plus2 = 'checked'; 
}
else {
	$plus2 ='';
}

$content .="<td>$R->identifiant</td>"
."<td><input id=propose$R->id type=checkbox $plus1 /></td>"
."<td><input id=impose$R->id  type=checkbox $plus2 /></td>"
."<td>".($R->cible)."</td>"
."<td>$R->doc_type</td>"
."</tr>";
}
$content .="<tr><td>".$group_img."<BR /><A href=# onclick=javascript:addGroup();>Ajouter un groupe</A></td>";
$content .="<td>Groupe:<select id=filter name=filter onchange=javascript:giveGroups();>"
."<option value=null>---</option>"
."<option value=Equipe>Equipe</option>"
."<option value=Classe>Classe</option>"
."<option value=Administ>Administratifs</option>"
."<option value=Profs>Profs</option>"
."<option value=Eleves>Eleves</option>"
."<option value=Matiere>Matiere</option>"
."<option value=Cours>Cours</option>"
."</select>";
$content .= "<select id=selectGroups name=selectGroups></select></td>";

$content .= "<td><input id=proposeNew  type=checkbox  /></td>";
$content .="<td><input id=imposeNew  type=checkbox  /></td>";

$content .="<td>Groupe:<select id=filter2 name=filter2 onchange=javascript:giveGroups2();>"
."<option value=null>---</option>"
."<option value=Equipe>Equipe</option>"
."<option value=Classe>Classe</option>"
."<option value=Administ>Administratifs</option>"
."<option value=Profs>Profs</option>"
."<option value=Eleves>Eleves</option>"
."<option value=Matiere>Matiere</option>"
."<option value=Cours>Cours</option>"
."</select>";
$content .= "<select id=cible></select></td>";
$content .= "<td><select id=doc>";
$content .= "<option value=tout>Tout</option>";
$content .= "<option value=application>Application</option>";
$content .= "<option value=information>Information</option>";
$content .= "<option value=rss>Flux RSS</option>";

$content .= "</select></td>";

$content .= "</tr></table>";

print(stringForJavascript($content));
?>
