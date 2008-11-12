<?php
######################################################################
# Lcs-Skel                                      #
# Auteur: Dominique (Dom) Lepaisant - MAi2008                 #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################
if (!defined("_ECRIRE_INC_VERSION")) return;


$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_LCS_SKEL',(_DIR_PLUGINS.end($p)));

# repertoire icones . lcs_skel_conf/img_pack/
if (!defined("_DIR_IMG_LCS_SKEL")) {
	define('_DIR_IMG_LCS_SKEL', _DIR_PLUGIN_LCS_SKEL.'img_pack/');
}
# repertoire local REC_MC .. rec_mc/img_pack/
if (!defined("_DIR_LOCAL_LCS_SKEL")) {
	define('_DIR_LOCAL_LCS_SKEL', _DIR_PLUGIN_LCS_SKEL.'local/');
}

# bouton menu secondaire 'configuration'
function lcs_skel_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
	  // on voit le bouton dans la barre "configuration"
	  $boutons_admin['configuration']->sousmenu["cfg&cfg=lcs_skel_conf"]= new Bouton(
		"../"._DIR_PLUGIN_LCS_SKEL."/img_pack/logo_lsc_24.png",  // icone
		_T('lcsskel:titre_plugin_lcs_skel')	// titre
		);
	}
	return $boutons_admin;
}

function lcs_skel_ajouterOnglets($flux) {
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
		$flux['data'][_LCS_SKEL_PREFIX] = new Bouton( 
			_DIR_PLUGIN_LCS_SKEL."/img_pack/logo_lsc_24.png"
			, _T('lcsskel:titre_plugin_lcs_skel')
			, generer_url_ecrire("cfg&cfg=lcs_skel_conf")
			)
			;
	}

	return ($flux);
}

// css prive
	function lcs_skel_header_prive($flux) {
		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_LCS_SKEL.'lcs_skel_styles.css" />'."\n";
		return $flux;
	}
	
// css public
	function lcs_skel_insert_head($flux) {
//		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_LCS_SKEL.'c/c.css" />'."\n";
//		$flux.= "\n".'<script type="text/javascript" src="'._DIR_PLUGIN_LCS_SKEL.'niftycube.js"></script>'."\n";
//		$flux.= "\n".'<script type="text/javascript" src="'._DIR_PLUGIN_LCS_SKEL.'niftyLayout.js"></script>'."\n";
		return $flux;
	}

?>
