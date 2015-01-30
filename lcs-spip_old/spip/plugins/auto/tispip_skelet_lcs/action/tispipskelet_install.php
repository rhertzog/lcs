<?php
######################################################################
# TiSpiP-sKeLeT                                        #
# Auteur: Dominique (Dom) Lepaisant - Mai 2008                  #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################

###############################
# A revoir pour ecriture spip2
###############################

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function tispipskelet_upgrade($nom_meta_base_version,$version_cible){
			
		// Installer les types de docs
				$ext = array("ggb","glb","gxt","mm","zir");
				$nm = array("GeoGebra","GeoLabo","GeoNExT","FreeMind","CarMetal");
				$rep=_DIR_PLUGIN_TISPIPSKELET."img_pack/icones/";
				foreach ($ext as $k => $val){
					// on verifie si le user est deja dans cette zone
					$champs = array('titre', 'extension');
					$where = array( 'extension='.$val);
					$row_type_docs = sql_fetsel($champs, "spip_types_documents", $where);
					if (!$row_type_docs){
						$vals['extension']=$val;
						$vals['titre']=$nm[$k];
						$vals['mime_type']="application/".strtolower($nm[$k]);
						$vals['inclus']="embed";
						$vals['upload']="oui";
						$type_documents = sql_insertq("spip_types_documents", $vals,'',$serveur='connect',$option=true);
					}
				}

	// Installer les icones
							$rep_img=_DIR_PLUGIN_TISPIPSKELET."img_pack/icones/";
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
}

?>