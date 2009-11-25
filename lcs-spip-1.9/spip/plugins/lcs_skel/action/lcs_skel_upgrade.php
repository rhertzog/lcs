<?php
######################################################################
# Lcs-Skel                                       #
# Auteur: Dominique (Dom) Lepaisant - Mai 2008                  #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################
if(!function_exists('__plugin_get_meta_infos')) {
	// renvoie les infos du plugin contenues dans les metas
	// qui contient 'dir' et 'version'
	function __plugin_get_meta_infos ($prefix) {
		if(isset($GLOBALS['meta']['plugin'])) {
			$result = unserialize($GLOBALS['meta']['plugin']);
			$prefix = strtoupper($prefix);
			if(isset($result[$prefix])) {
				return($result[$prefix]);
			}
		}
		return(false);
	}
} // end if __plugin_get_meta_infos

function lcs_skel_install($action){
	switch ($action) {
	// La base est deja cree ?
		case 'test':
			// Verifier que la version lcs_skel ..	
			if (isset($GLOBALS['meta']['plugin']['LCS_SKEL'])){
				$r = __plugin_get_meta_infos ('LCS_SKEL');
				if((!isset($r['version'])) || $r['version'] < 2.0 ){
					return false;
				}
			// Verifier les types de documents  ..	
				$ext = array("ggb","glb","gxt","mm","zir");
				foreach ($ext as $k => $val){
					$q=spip_query("SELECT titre, extension FROM spip_types_documents WHERE extension ='".$val."'");
					$rw=spip_fetch_array($q);
					if(empty($rw['extension'])){ 
						return false;
					}
					if(!file_exists($file=_DIR_IMG."icones/".$val.".png")){
						return false;
					}
				}
				return true;
			}
			break;
			
	// Installer les types de docs
		case 'install':
				$ext = array("ggb","glb","gxt","mm","zir");
				$nm = array("GeoGebra","GeoLabo","GeoNExT","FreeMind","CarMetal");
				$rep=_DIR_PLUGIN_LCS_SKEL."img_pack/icones/";
				foreach ($ext as $k => $val){
				$q=spip_query("SELECT titre, extension FROM spip_types_documents WHERE extension ='".$val."'");
					$rw=spip_fetch_array($q);
					if(empty($rw['extension'])){ 
						spip_query("INSERT INTO spip_types_documents SET extension='".$val."' , titre='".$nm[$k]."' , mime_type='application/".strtolower($nm[$k])."' , inclus='embed' , upload='oui'");
					}
				}
	// Installer les icones
							$rep_img=_DIR_PLUGIN_LCS_SKEL."img_pack/icones/";
							$ext_img = array("ggb","glb","gxt","mm","zir");
							if (!@opendir(_DIR_IMG."icones")){
								mkdir(_DIR_IMG."icones", 0777);
							}
							foreach ($ext_img as $k => $val){
								if(file_exists($file=$rep_img.$val.".png")){
									$newfile=_DIR_IMG."icones/".$val.".png";
									copy($file,$newfile);
								}
							}
			break;
			
	// Supprimer la base
		case 'uninstall':
			spip_query("DELETE FROM spip_meta WHERE spip_meta.nom = 'lcs_skel_conf'");
		break;
	}
}
?>