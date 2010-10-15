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

function tispipskelet_install($action){
	switch ($action) {
//Verifications
		case 'test':
			// Verifier la version tispipskelet ..	
		if(isset($GLOBALS['meta']['plugin'])) {
			$result = unserialize($GLOBALS['meta']['plugin']);
			if((isset($result['TISPIPSKELET']['version'])) && (str_replace('.','',$result['TISPIPSKELET']['version']) >= '205')) {
//				echo "<br />On passe";
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
		
		case 'install':
			$result = unserialize($GLOBALS['meta']['plugin']);
			file_exists(_DIR_RACINE.'squelettes/svn_revision.xml') ? $rev_installee = simplexml_load_file(_DIR_RACINE.'squelettes/svn_revision.xml')->logentry[0]['revision'] : '' ;

			echo "<div class='cadre cadre-e'><div class='cadre-padding'>". icone_horizontale(_T("tispipskelet:affichage_personnaliser"), "?exec=cfg&cfg=TiSpiP_Pages", "", _DIR_PLUGIN_TISPIPSKELET . "img_pack/tispip-skelet_24.png", false, "")."<div class='titrem impliable'><em>TispiP v".$result['TISPIPSKELET']['version']." [".$rev_installee."] install&eacute;</em></div>";
			
		// maj du skel
			file_exists(_DIR_PLUGIN_TISPIPSKELET.'svn_revision.xml') ? $rev_maj = simplexml_load_file(_DIR_PLUGIN_TISPIPSKELET.'svn_revision.xml')->logentry[0]['revision'] : '' ;
	
				if((int)$rev_installee <= 2955) {
					ecrire_config('tispipskelet_conf/entete/affimg_entete','oui');
					ecrire_config('tispipskelet_conf/entete/img_entete', 'config/tispip_config_entete_commentaire/img_entete.png');
					ecrire_config('tispipskelet_conf/entete/taille_titre','40');
					ecrire_config('tispipskelet_conf/entete/titre_pos_v', '-4');
					lire_metas();
					$cmd="cp -r "._DIR_PLUGIN_TISPIPSKELET."img_config_maj/* "._DIR_IMG."config";
				echo "<br />Copie des images...";
					exec($cmd);
				echo "<br />Mise &agrave; jour des images effectu&eacute;e";
				}
			if((int)$rev_maj > (int)$rev_installee) {
				echo "<br />revision install&eacute =".$rev_installee."<br />nouvelle revision=".$rev_maj;
				$command="cp -r -b "._DIR_PLUGIN_TISPIPSKELET."squelettes_maj/* "._DIR_RACINE."squelettes";
				echo "<br />Copie des fichiers du squelette...";
				exec($command);
				echo "<br />Mise &agrave; jour du squelette effectu&eacute;e";
				$cmd_rev="cp -r -b "._DIR_PLUGIN_TISPIPSKELET."svn_revision.xml "._DIR_RACINE."squelettes/svn_revision.xml";
				exec($cmd_rev);
				echo "<br /> Revision installée : ".simplexml_load_file(_DIR_PLUGIN_TISPIPSKELET.'svn_revision.xml')->logentry[0]['revision'];
				
			}
			echo "</div></div>";
			
		// Installer les types de docs
				$ext = array("ggb","glb","gxt","mm","zir");
				$nm = array("GeoGebra","GeoLabo","GeoNExT","FreeMind","CarMetal");
				$rep=_DIR_PLUGIN_TISPIPSKELET."img_pack/icones/";
				foreach ($ext as $k => $val){
				$q=spip_query("SELECT titre, extension FROM spip_types_documents WHERE extension ='".$val."'");
					$rw=spip_fetch_array($q);
					if(empty($rw['extension'])){ 
						spip_query("INSERT INTO spip_types_documents SET extension='".$val."' , titre='".$nm[$k]."' , mime_type='application/".strtolower($nm[$k])."' , inclus='embed' , upload='oui'");
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
			break;
			
	// Supprimer la base
		case 'uninstall':
			spip_query("DELETE FROM spip_meta WHERE spip_meta.nom = 'tispipskelet_conf'");
		break;
	}
}
?>