<?

include "includes/secure_no_header.inc.php";

if ($_POST)
	extract($_POST);

$content2 = "<div class=menuitems title=$id><B> Menu</B></div>";
$content2 .="<HR />";


if ($id == 'actu') {
	if ( $ML_Adm != 'Y' )
		die('Aucune action possible ...');
	}

if ($id == 'scenario_choix') {
	if (is_eleve($uid)) { 
        	if (trim($activate_acad_monlcs) == '1') {                                                                
                	$content2 .="<div onclick=javascript:liste_fiches(); class=menuitems title=Ressources&nbsp;du&nbsp;depot&nbsp;acad&eacute;mique>Ressources&nbsp;associ&eacute;es</div>";
			die(stringForJavascript($content2));
        	} else 
			die('Aucune action possible');
	}
		
}
	



if ($id == 'rss') {
	if ($mode == 'user')
		$content2 .="<div onclick=javascript:rssSave(); class=menuitems title=Sauver_les_flux>Enregistrer les modifications</div>";
	$content2 .="<div onclick=javascript:giveRessources('$id'); class=menuitems title=Ressources>Ressources</div>";
}

if ($id == 'import_acad' && (trim($activate_acad_monlcs) == '1') ) {
	$content2 .="<div onclick=javascript:fetchScen(); class=menuitems title=Importer_ce_sc&#233;nario>Importer ce sc&#233;nario.</div>";
	die(stringForJavascript($content2));
}

if (is_scenarii($id)) {
	$content2 .="<div onclick=javascript:showTuto(); class=menuitems title=Tutoriels>Tutoriels</div>";
}

if (!eregi('perso',$id) && !eregi('rss',$id) || is_scenarii($id)) {

	if ($mode == 'user')
		$content2 .="<div onclick=javascript:desktopSave(); class=menuitems title=Sauver_le_bureau>Enregistrer les modifications </div>";
	if ( ($ML_Adm == 'Y') && ($id != 'rss') && (!is_perso_tab($id)) && !is_scenarii($id) )
		$content2 .="<div onclick=javascript:defaultSave(); class=menuitems title=Par_défaut>Proposer par défaut</div>";
	if ( is_administratif($uid)  && ($id == 'vs') )
		$content2 .="<div onclick=javascript:defaultSave(); class=menuitems title=Par_défaut>Proposer par défaut</div>";

	$content2 .="<div onclick=javascript:giveRessources('$id'); class=menuitems title=Ressources>Ressources</div>";


	if (!is_eleve($uid) && ( ($id == 'bureau') || (is_scenarii($id)) || ($id == 'vs' ) || ($id == 'scenario_choix') || is_perso_tab($id) || ( $ML_Adm == "Y" ) ))
		$content2 .="<div onclick=ajoutNote('$id'); class=menuitems title=Ajout_note>Ajouter une note</div>";
}


if (is_scenarii($id)) {
	$content2 .="<div onclick=javascript:scenario('$id'); class=menuitems title=Sc&eacute;nario>Cr&eacute;er un nouveau sc&eacute;nario</div>";
#########################################################################################################################################################################
# Ajout pour Publication ACAD
	if (trim($activate_acad_monlcs) == '1') {
		$content2 .="<div onclick=javascript:scen_acad_pub(); class=menuitems title=Publication&nbsp;acad&eacute;mique>Publication&nbsp;acad&eacute;mique</div>";
		$content2 .="<div onclick=javascript:liste_fiches(); class=menuitems title=Ressources&nbsp;du&nbsp;depot&nbsp;acad&eacute;mique>Ressources&nbsp;associ&eacute;es</div>";
	}
#########################################################################################################################################################################
	}
	 
if (($ML_Adm =='Y') && ($id != 'scenario_choix') && (!is_perso_tab($id)) && !eregi('perso',$id))
	$content2 .="<div onclick=javascript:publish('$id'); class=menuitems title=\"Figer\">Figer des ressources !</div>";
	

print(stringForJavascript($content2));
?>
