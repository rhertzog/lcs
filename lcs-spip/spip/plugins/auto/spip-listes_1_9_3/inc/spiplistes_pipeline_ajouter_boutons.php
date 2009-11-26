<?php
// inc/spiplistes_pipeline_ajouter_boutons.php
/*
	SPIP-Listes pipeline
	
	Nota: plugin.xml en cache.
		si modif plugin.xml, il faut parfois r�activer le plugin (config/plugin: d�sactiver/activer)
	
*/
// $LastChangedRevision: 21326 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2008-07-07 08:04:40 +0200 (lun, 07 jui 2008) $

include_spip('inc/spiplistes_api_globales');

function spiplistes_ajouterBoutons($boutons_admin) {

	if($GLOBALS['connect_statut'] == "0minirezo") {
	// affiche le bouton dans "Edition"
		$boutons_admin['naviguer']->sousmenu['spiplistes'] = new Bouton(
			_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_listes-24.gif"  // icone
			, _T('spiplistes:listes_de_diffusion_')	// titre
			, _SPIPLISTES_EXEC_COURRIERS_LISTE
		);
	}
	return ($boutons_admin);
}

?>