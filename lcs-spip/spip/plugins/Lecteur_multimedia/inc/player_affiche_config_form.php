<?php

	// inc/player_affiche_config_form.php
	
	// $LastChangedRevision: 19362 $
	// $LastChangedBy: paladin@quesaco.org $
	// $LastChangedDate: 2008-03-21 16:34:27 +0100 (ven, 21 mar 2008) $

// CP-20080321 : correction style (tous les formul en espace priv� utilisent verdana2)
// CP-20080321 : d�placement de la fonction initiale ici. Peut-�tre � recoler en page admin ?

function player_affiche_config_form ($exec_page){

		global $spip_lang_right, $spip_lang_left;
		
	  if ($player = _request('player')) {
			ecrire_meta('player', $player);
			if(version_compare($GLOBALS['spip_version_code'],'1.9300','<')) { 
				include_spip("inc/meta");
				ecrire_metas();
			}
		}
		
		$player_ = $GLOBALS['meta']['player'];
		
		$out = ""
			. debut_cadre_trait_couleur(_DIR_PLUGIN_PLAYER_IMAGES."player-son-24.png", true, "", "Player Audio")
			. "<form action='".generer_url_ecrire($exec_page)."' method='post' class='verdana2'><div>"
			. bouton_radio("player", "neoplayer", "Neolao player", $player_ == "neoplayer")
			. "<br />"
			. bouton_radio("player", "dewplayer", "Dew player", $player_ == "dewplayer")
			. "<br />"
			. bouton_radio("player", "pixplayer", "One pixel out player", $player_ == "pixplayer")
			. "<br />"
			. bouton_radio("player", "eraplayer", "Erational player", $player_ == "eraplayer")
			. "<div style='text-align:$spip_lang_right'><input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' /></div>"
			. "</div></form>"
			. fin_cadre_trait_couleur(true)
			;

		return ($out);
}

?>