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
# repertoire local TISPIP 
if (!defined("_DIR_LOCAL_TISPIPSKELET")) {
	define('_DIR_LOCAL_TISPIPSKELET', _DIR_PLUGIN_TISPIPSKELET.'local/');
}

# bouton menu secondaire 'configuration'
function tispipskelet_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
	  // on voit le bouton dans la barre "configuration"
	  $boutons_admin['configuration']->sousmenu["cfg&cfg=TiSpiP_Pages"]= new Bouton(
		_DIR_PLUGIN_TISPIPSKELET."/img_pack/tispip-skelet_24.png",  // icone
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
			, generer_url_ecrire("cfg&cfg=TiSpiP_Pages")
			)
			;
	}

	return ($flux);
}
// FONCTION qui va lire le fichier XML d'annonce de maj aux admins et le placer dans un tableau

function lit_xml ($fichier,$item,$champs) {
   // on lit le fichier
   if($chaine = @implode("",@file($fichier))) {
      // on explode sur <item>
       $tmp = preg_split("/<\/?".$item.">/",$chaine);
      // pour chaque <item>
      for($i=1;$i<sizeof($tmp)-1;$i+=2)
         // on lit les champs demandés <champ>
         foreach($champs as $champ) {
            $tmp2 = preg_split("/<\/?".$champ.">/",$tmp[$i]);
            // on ajoute l'élément au tableau
            $tmp3[$i-1][] = @$tmp2[1];
         }
      // et on retourne le tableau dans la fonction
      return $tmp3;
   }
}

function affiche_message_admins () {
		// Lecture du FLUX XML sur le serveur
	$mess_xml = lit_xml("http://tispip.etab.ac-caen.fr/depot/message_lcs_admins.xml","message",array("titre", "date", "sujet", "version", "revision", "auteur", "texte", "url"));
	file_exists(_DIR_RACINE.'squelettes/svn_revision.xml') ? $rev = simplexml_load_file(_DIR_RACINE.'squelettes/svn_revision.xml')->logentry[0]['revision'] : '' ;
	if ($mess_xml !=''){
		foreach($mess_xml as $row) {
			// contenu du message
			$mess_titre= $row[0];
			$mess_date= $row[1];
			$mess_sujet= $row[2];
			$mess_version= $row[3];
			$mess_revision= $row[4];
			$mess_auteur= $row[5];
			$mess_texte= $row[6];
			$mess_url= $row[7];
		 }
		if ( $mess_revision != $rev) {
			$ret = "<div class='cadre cadre-e' style='padding:5px;'>";
			$ret .= icone_horizontale(_T($mess_titre), generer_url_ecrire("admin_plugin"), _DIR_PLUGIN_TISPIPSKELET."img_pack/important_48.png", "", false);
			$ret .= "<div class='cadre_padding' style='background:#fff;'>";
			$ret .= "<blockquote style='margin:10px 5px 5px;padding:5px;border:1px solid #000;'><strong>".$mess_sujet."</strong></blockquote>";
			$p_ret="<div class='cadre_padding' style='background:#eee;'>";
			$p_ret.="<span class='item_nom'>Date : </span><span class='item_valeur'>".$mess_date.$mess_xml[1]."</span>";
			$p_ret.="<span class='item_nom'>Version : </span><span class='item_valeur'>".$mess_version."</span>";
			$p_ret.="<span class='item_nom'>Revision : </span><span class='item_valeur'>".$mess_revision."</span>";
			$p_ret.="<span class='item_nom'>Auteur : </span><span class='item_valeur'>".$mess_auteur."</span>";
			$p_ret.="<span class='item_nom'>Version install&eacute;e : </span><span class='item_valeur'>".$rev."</span><br style='clear:both;' />";
			$p_ret .= "</div>";
			$ret.=$p_ret;
			$ret .="<p>". $mess_texte."</p>";
			$ret .= "<input type='text' value='".$mess_url."' />";
			$ret .= "</div></div>";
		}
	}
	return $ret;
}

//affichage dans la colonnes de droite de l'espace prive
	function tispipskelet_affiche_droite($flux){
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]){
			$exec = $flux["args"]["exec"];
			
			if ($exec == "accueil") {
				$data = $flux["data"];
				
				#$ret = affiche_message_admins();
				$ret = "";
		
				$flux["data"] = $data.$ret;
			}
			if ($exec == "acces_restreint") {
				$data = $flux["data"];
				
				$ret = link_zones_groupes();
		
				$flux["data"] = $data.$ret;
			}

		}	
		
		$id_rubrique = $flux['args']['id_rubrique'];
		if ($flux['args']['exec']=='naviguer' AND $id_rubrique > 0) {
			$out="<div style='border:1px solid #999;background:white;'>";
			$out .= icone_horizontale(_T("tispipskelet:affichage_personnaliser"), "#", "", _DIR_PLUGIN_TISPIPSKELET . "img_pack/tispip-skelet_24.png", false, "onclick='$(\"#boite_affichage_tispip\").slideToggle(\"fast\"); return false;'");
			$out.="<div id='boite_affichage_tispip' style='display:none;'>";
		
			$out.= recuperer_fond('formulaires/tispip_obo_rubriques_rubrique',array('id_rubrique'=>$id_rubrique,'icone'=>$icone));
			lire_config('activer_sites')=='oui' ?$out.= recuperer_fond('formulaires/tispip_obo_sites_rubrique',array('id_rubrique'=>$id_rubrique,'icone'=>$icone)) : '';
			lire_config('documents_rubrique')=='oui' ? $out.= recuperer_fond('formulaires/tispip_obo_docs_rubrique',array('id_rubrique'=>$id_rubrique,'icone'=>$icone)) : '';
	
			$out.= "</div></div>";
			$flux['data'] .= $out;
		}
		
		#pour les pages articles
		$id_article = $flux['args']['id_article'];
		if ($flux['args']['exec']=='articles' AND $id_article > 0) {
			$out="<div style='border:1px solid #999;background:white;'>";
			$out .= icone_horizontale(_T("tispipskelet:affichage_personnaliser"), "#", "", _DIR_PLUGIN_TISPIPSKELET . "img_pack/tispip-skelet_24.png", false, "onclick='$(\"#boite_affichage_tispip\").slideToggle(\"fast\"); return false;'");
			$out.="<div id='boite_affichage_tispip' style='display:none;'>";
		
			$result = unserialize($GLOBALS['meta']['plugin']);
			if(isset($result['CRAYONS']['version'])) {
				$out.= recuperer_fond('formulaires/tispip_obo_wiki_article',array('id_article'=>$id_article,'icone'=>$icone));
			}
			lire_config('documents_article')=='oui' ? $out.= recuperer_fond('formulaires/tispip_obo_docs_article',array('id_article'=>$id_article,'icone'=>$icone)) : '';
			lire_config('nb_cols_art')=='3' ? $out.= recuperer_fond('formulaires/tispip_obo_nbcols_art',array('id_article'=>$id_article,'icone'=>$icone)) : '';
			$out.= "</div></div>";
			$flux['data'] .= $out;
		}
		return $flux;
	}
/*
	function tispipskelet_affiche_milieu($flux){
		include_spip('inc/autoriser');
		include_spip('inc/utils');
		include_spip('inc/composer');
		include_spip('inc/assembler');
		if ($flux["args"]["exec"]=="accueil" )
		{
			$flux["data"] .="<h1>Information TiSpiP</h1>";
			//recuperer_fond("prive/exec/menu");
		}
		return $flux;
	}
*/

// css prive
	function tispipskelet_header_prive($flux) {
		$flux.= "\n".'<script type="text/javascript" src="'._DIR_PLUGIN_TISPIPSKELET.'scripts/jquery.boxy.js" />'."\n";
		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_TISPIPSKELET.'css/boxy.css" />'."\n";
		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_TISPIPSKELET.'css/tispip_prive.css" />'."\n";
		$flux.="\n".'<script type="text/javascript">'."\n";
		$flux.=' $(document).ready(function() { 
			$("#bandeau_couleur6").attr("style","display:none;");
			});'."\n";
		$flux.="\n".'</script>'."\n";
		return $flux;
	}
	
//pour LCS on inserre le bouton MontrerCacher pour le top-frame
function tispipskelet_insert_head($flux) {
		$flux.='<script type="text/javascript">';
		$flux.=' $(document).ready(function() {$("body").append( $(\'<div id="onoff_frame" class="up" onclick="javascript:ShowHideFrame();"></div>\') ); $("li#deconnect").attr("style","display:none;");  });';
		 $flux.="function ShowHideFrame() {	if (up == true) {	window.top.document.body.rows = '0,*';up = false;$('#onoff_frame').addClass('down');	return;} else {	window.top.document.body.rows = '90,*';	up=true;$('#onoff_frame').removeClass('down');return;}}up=true;";
		$flux.='</script>';

		
		return $flux;
	}

?>
