<?php
######################################################################
# TiSpiP-sKeLeT                                      #
# Auteur: Dominique (Dom) Lepaisant - MAi2008                 #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################
if (!defined("_ECRIRE_INC_VERSION")) return;


$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_TISPIPSKELET',(_DIR_PLUGINS.end($p)));

# repertoire icones . tispipskelet_conf/img_pack/
if (!defined("_DIR_IMG_TISPIPSKELET")) {
	define('_DIR_IMG_TISPIPSKELET', _DIR_PLUGIN_TISPIPSKELET.'img_pack/');
}
# repertoire local REC_MC .. rec_mc/img_pack/
if (!defined("_DIR_LOCAL_TISPIPSKELET")) {
	define('_DIR_LOCAL_TISPIPSKELET', _DIR_PLUGIN_TISPIPSKELET.'local/');
}

# bouton menu secondaire 'configuration'
function tispipskelet_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
	  // on voit le bouton dans la barre "configuration"
	  $boutons_admin['configuration']->sousmenu["cfg&cfg=tispipskelet_conf"]= new Bouton(
		"../"._DIR_PLUGIN_TISPIPSKELET."/img_pack/tispip-skelet_24.png",  // icone
		_T('tispipskelet:titre_plugin_tispipskelet')	// titre
		);
	}
	return $boutons_admin;
}

function tispipskelet_ajouterOnglets($flux) {
	include_spip('inc/urls');
	include_spip('inc/utils');

	global $connect_statut
		, $connect_toutes_rubriques
		;

	if(
		($flux['args'] == 'configuration')
		&& ($connect_statut == '0minirezo')
		&& $connect_toutes_rubriques
		) {
		$flux['data'][_TISPIPSKELET_PREFIX] = new Bouton( 
			_DIR_PLUGIN_TISPIPSKELET."/img_pack/tispip-skelet_24.png"
			, _T('tispipskelet:titre_plugin_tispipskelet')
			, generer_url_ecrire("cfg&cfg=tispipskelet_conf")
			)
			;
	}

	return ($flux);
}

// css prive
	function tispipskelet_header_prive($flux) {
		$flux.= "\n".'<script type="text/javascript" src="'._DIR_PLUGIN_TISPIPSKELET.'scripts/jquery.boxy.js" />'."\n";
		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_TISPIPSKELET.'css/boxy.css" />'."\n";
		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_TISPIPSKELET.'tispipskelet_styles.css" />'."\n";
		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_TISPIPSKELET.'css/tispip_prive.css" />'."\n";
		return $flux;
	}
	
// css public
//	function tispipskelet_insert_head($flux) {
//		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_TISPIPSKELET.'c/c.css" />'."\n";
//		$flux.= "\n".'<script type="text/javascript" src="'._DIR_PLUGIN_TISPIPSKELET.'niftycube.js"></script>'."\n";
//		$flux.= "\n".'<script type="text/javascript" src="'._DIR_PLUGIN_TISPIPSKELET.'niftyLayout.js"></script>'."\n";
//		return $flux;
//	}

?>
