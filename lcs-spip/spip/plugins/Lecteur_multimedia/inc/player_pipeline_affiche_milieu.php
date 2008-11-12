<?php 

	// inc/player_pipeline_affiche_milieu.php
	
	// $LastChangedRevision: 19360 $
	// $LastChangedBy: paladin@quesaco.org $
	// $LastChangedDate: 2008-03-21 15:50:47 +0100 (ven, 21 mar 2008) $

	
if (!defined("_ECRIRE_INC_VERSION")) return;

// pipeline (plugin.xml)
// Ajoute la boite en fin de page de configuration Fonctions avanחיes
function player_affiche_milieu ($flux) {

	$exec = $flux['args']['exec'];

	if ($exec == 'config_fonctions'){	
		include_spip('inc/player_affiche_config_form');
		$flux['data'] .= player_affiche_config_form($exec);
	}

	return($flux);
}

?>