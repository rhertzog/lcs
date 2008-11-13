<?php

include("includes/secure_no_header.inc.php");

	if ($_POST)
		extract($_POST);

	//liste des groupes de l'utilisateur

	

	if ( ($ML_Adm == 'Y') || is_administratif($uid) ) {
    	$groups = search_groups("cn=*");
    	$gr = array();
			foreach($groups as $group) {
				$gr[] = $group['cn'];
			}
			////remouliner le $groups pour presenter Admins Profs Eleves
			
			$flux = implode('#', $gr);
			$pattern = array("Profs#","Eleves#","Admins#");
			$repl = array("","","");
			$flux = str_replace($pattern, $repl, $flux);
			$flux = "Admins#Eleves#Profs#".$flux;
			$gr = explode('#',$flux);
			$groups = array();
			for ($x=0;$x<count($gr);$x++) {
				$g = array();
				$g['cn'] = $gr[$x];
				$groups[] = $g;
			}
    } else {
    list($user,$groups)=people_get_variables($uid, true);
    }


$liste = "<select multiple size=4 name=liste_share id=liste_share>";

foreach($groups as $group) {
$eq = $group['cn'];

if ($ML_Adm != 'Y') {
if (eregi('Equipe',$eq)) {
    $info = explode('_',$eq);
    $info[0] = 'Classe';
    $eq2 = implode('_',$info);     
    $liste .="<option value='".$eq2. "' class='group'>$eq2</option>";
    }
    }

$liste .="<option value='".$eq. "' class='group'>$eq</option>";
    
}


	$sql = "select * from monlcs_db.ml_zones where id='$id';";
	$c = mysql_query($sql) or die("ERREUR $sql");
	$R1 = mysql_fetch_object($c);
	
	$content ="<html><body><table border=0>";
	$content .="<tr><td><strong>Onglet:</strong>$R1->nom</td>$bourre1$bourre1<td><A href=# onclick=remove_tab($id);>$delete_img</A></td></tr>";
	$content .="<tr><td colspan=7><strong>Sous-menus:</strong></td></tr>";

	$sql2 = "select * from monlcs_db.ml_tabs where id_tab='$R1->id';";
	$c2 = mysql_query($sql2) or die("ERREUR $sql");
	for ($x=0;$x<mysql_num_rows($c2);$x++) {
	$R = mysql_fetch_object($c2);
	$content .="<tr>$bourre1<td><strong>Menu:</strong>&nbsp;$R->caption</td>";
	if (mysql_num_rows($c2) > 1)
		$content .="$bourre1<td><A href=# onclick=remove_sub_tab($R->id);>$delete_img</A></td>";
	
	$content .="</tr>";


	}
	$content .="<tr>$bourre1<td><strong>Menu:</strong>&nbsp;<input size=7 id=caption_new_tab value=? /></td>";
	$content .="$bourre1<td><A href=# onclick=add_sub_tab($R1->id);>$add_img</a></td></tr>";
		
	
	
	$content .="</table></body></html>";
	
	print(stringForJavascript($content));
		
    	 
?>
