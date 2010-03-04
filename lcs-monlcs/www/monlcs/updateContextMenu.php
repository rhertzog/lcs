<?

include "includes/secure_no_header.inc.php";

if ($id == 'actu') {
	if ($ML_Adm != 'Y')
		die('Aucune action possible ...');
}
//dans les scenarios les eleves et les autres profs n'ont aucun droit
if ($id == 'scenario_choix') {

        if (is_eleve($uid)) {
        #########################################################################################################################################################################
        # Ajout pour Publication ACAD
                if (trim($activate_acad_monlcs) == '1') {
                        $content2 .="<div onclick=javascript:liste_fiches(); class=menuitems title=Fiches&nbsp;du&nbsp;depot&nbsp;acad&eacute;mique>Fiches&nbsp;associ&eacute;es</div>";
                        die(stringForJavascript($content2));
                } else { 
                        die('Aucune action possible');

                }
        #########################################################################################################################################################################

        }

	
	if ($ML_Adm != 'Y') {
	
	$sql = "select * from monlcs_db.ml_scenarios where setter='$uid' and id_scen='$id_scen';";
	$c = mysql_query($sql) or die("ERREUR $sql");
	if (mysql_num_rows($c) == 0)
		die('Aucune action possible ...');
	}

}


$content2 = "<div class=menuitems title=$id><B> Menu</B></div>";

$content2 .="<HR />";

if (is_scenarii($id)) {
	$content2 .="<div onclick=javascript:showTuto(); class=menuitems title=Tutoriels>Tutoriels</div>";
}


if (!eregi('perso',$id) ) {

$content2 .="<div onclick=javascript:desktopSave(); class=menuitems title=Sauver le bureau>Enregistrer les modifications</div>";
$content2 .="<div onclick=javascript:giveRessources('$id'); class=menuitems title=Ressources>Ressources</div>";

//notes autorisees ?
$fixNote = ($id == 'scenario_choix') && ($uid == $setter);
if (!is_eleve($uid) && ( ($id == 'bureau') || (is_scenarii($id)) || ($id == 'vs' ) || $fixNote ) )
	$content2 .="<div onclick=ajoutNote('$id'); class=menuitems title=Ajout note>Ajouter une note</div>";
}


if (is_scenarii($id)) {
	$content2 .="<div onclick=javascript:scenario('$id'); class=menuitems title=Sc&eacute;nario>Cr&eacute;er un nouveau sc&eacute;nario</div>";
#########################################################################################################################################################################
# Ajout pour Publication ACAD
	if (trim($activate_acad_monlcs) == '1') {
		$content2 .="<div onclick=javascript:scen_acad_pub(); class=menuitems title=Publication&nbsp;acad&eacute;mique>Publication&nbsp;acad&eacute;mique</div>";
		$content2 .="<div onclick=javascript:liste_fiches(); class=menuitems title=Fiches&nbsp;du&nbsp;depot&nbsp;acad&eacute;mique>Fiches&nbsp;associ&eacute;es</div>";
	}
#########################################################################################################################################################################

	}

if (($ML_Adm ==  'Y') && ($id != 'scenario_choix') )
	$content2 .="<div onclick=javascript:publish('$id'); class=menuitems title=Figer>Figer des ressources !</div>";
	




print(stringForJavascript($content2));
?>
