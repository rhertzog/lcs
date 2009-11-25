<?php


	// player_mes_options.php

	// $LastChangedRevision: 19360 $
	// $LastChangedBy: paladin@quesaco.org $
	// $LastChangedDate: 2008-03-21 15:50:47 +0100 (ven, 21 mar 2008) $


	define("_PLAYER_PREFIX", "player");	
	define("_DIR_PLUGIN_PLAYER_IMAGES", _DIR_PLUGIN_PLAYER."images/");
	define("_DIR_PLUGIN_PLAYER_JAVASCRIPT", _DIR_PLUGIN_PLAYER."javascript/");
	define("_PLAYER_LANG", _PLAYER_PREFIX.":");
	
	define("_PLAYER_META_PREFERENCES", "player_preferences");

	define("_PLAYER_FLV_LECTEUR_DEFAULT", "player_flv_maxi.swf");
	
	define("_PLAYER_FLV_LECTEURS", 
		serialize(
			array(
				'mini' => array(
					'label' => _T(_PLAYER_LANG."mini")
					, 'value' => "player_flv_mini.swf"
				)
				, 'normal' => array(
					'label' => _T(_PLAYER_LANG."normal")
					, 'value' => "player_flv.swf"
				)
				, 'maxi' => array(
					'label' => _T(_PLAYER_LANG."maxi")
					, 'value' => _PLAYER_FLV_LECTEUR_DEFAULT
				)
				, 'multi' => array(
					'label' => _T(_PLAYER_LANG."multi")
					, 'value' => "player_flv_multi.swf"
				)
			)
		)
	);
?>