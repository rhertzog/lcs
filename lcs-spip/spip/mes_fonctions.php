<?php
//cas d'une maj (on teste la presence de lcs_skel_conf ds la table spip_meta)
	if(!$GLOBAL['meta']['lcs_skel_conf'] ){ //a remplacer par lcs_skel_conf
//    $command='cp /usr/share/lcs/spip/tmp /usr/share/lcs/spip/tmp_bak'; 
		$command='cp -r '._DIR_RACINE.'tmp '._DIR_RACINE.'tmp_bak'; 
//		exec($command);
//    $command2='rm -r /usr/share/lcs/spip/tmp/*'; 
//		exec($command2);
	}
	

// Installer la conf de spip par défaut
// on ne place la conf que ds le cas d'une install neuve
	if(!$GLOBALS['meta']['tispipskelet_conf']) {
		echo '<div style="width:100%;height:100%;padding-top:150px;margin-top:-20px;text-align:center;background:#f8f8ff url(./squelettes/i/loading.gif) center center no-repeat;"><div style="margin: auto;padding:40px;width:400px;border:3px inset #aaa;background:#fefefe;">Le Forum-Lcs est en cours d\'installation... Veuillez patienter...<span style="margin:auto;display:block;color:#cc0000;font-weight:bold;">N&rsquo;oubliez pas de vider le cache apr&egrave;s avoir valid&eacute; les plugins</span></div></div><img src="http://local.net/spip/squelettes/i/loading.gif" alt="" style="margin:auto:" />';		
		
		# on passe les plugins indispensables dans la table meta
		$agd=array('dir'=>'auto/agenda_2_0', 'etat'=>'stable', 'nom'=>'Agenda 2.0', 'version'=>'2.0.3');
		$bst=array('dir'=>'auto/boucles_sans_tables', 'etat'=>'test', 'nom'=>'Boucles sans tables', 'version'=>'1.0');
		$pal=array('dir'=>'auto/palette', 'etat'=>'stable', 'nom'=>'Palette', 'version'=>'1.2.1');
		$cs2=array('dir'=>'auto/champs_extra2', 'etat'=>'test', 'nom'=>'Champs Extras2', 'version'=>'0.8.4');
		$cfg=array('dir'=>'auto/cfg', 'etat'=>'stable', 'nom'=>'<multi> [fr]cfg: moteur de configuration [de]cfg: Konfigurationsmotor </multi>','version'=>'1.12.5');
		$dd=array('dir'=>'auto/download_dump', 'etat'=>'dev', 'nom'=>'Dump Download', 'version'=>'0.2');
		$mtc=array('dir'=>'auto/mots_techniques', 'etat'=>'test', 'nom'=>'Mots techniques pour SPIP 2', 'version'=>'0.7');
		$sni=array('dir'=>'auto/snippets', 'etat'=>'test', 'nom'=>'Snippets', 'version'=>'0.1');
		$spb=array('dir'=>'auto/spip-bonux', 'etat'=>'stable', 'nom'=>'SPIP Bonux 2.0', 'version'=>'1.8');
		$tsp=array('dir'=>'auto/tispip_skelet_lcs', 'etat'=>'test', 'nom'=>'TiSpiP-sKeLeT', 'version'=>'2.0.5');
		
		$plugin=array ('BOUCLESSANSTABLES'=>$bst, 'PALETTE'=>$pal, 'CEXTRAS'=>$cs2,'CFG'=>$cfg, 'DD'=>$dd, 'SNIPPETS'=>$sni, 'SPIP_BONUX'=>$spb, 'TISPIPSKELET'=>$tsp);
		ecrire_meta('plugin',  serialize($plugin));
		ecrire_meta('plugin_header', 'bouclessanstables(1.0),cfg(1.12.5),cextras(0.8.4),couteau_suisse(1.8.09.01),dd(0.2),palette(1.2.1),snippets(0.1),spip_bonux(1.8),tispipskelet(2.0.5),agenda(2.0.3)');
		ecrire_metas();

		# Installer la conf de spip par défaut
		$conf_spip_defaut= array(
			'activer_breves'=> 'oui',
			'config_precise_groupes'=> 'oui',
			'articles_descriptif'=> 'oui',
			'articles_chapeau'=> 'oui',
			'articles_texte'=> 'oui',
			'articles_ps'=> 'oui',
			'articles_mots'=> 'oui',
			'articles_urlref'=> 'oui',
			'articles_redirection'=> 'oui',
			'creer_preview'=> 'oui',
			'taille_preview'=> '150',
			'rubriques_texte'=> 'oui',
			'forums_titre'=> 'oui',
			'forums_texte'=> 'oui',
			'forums_afficher_barre'=> 'oui',
			'activer_sites'=> 'oui',
			'proposer_sites'=> '0',
			'activer_syndic'=> 'oui',
			'moderation_sites'=> 'non',
			'forums_publics'=> 'posteriori',
			'accepter_visiteurs'=> 'oui',
			'forum_prive'=> 'oui',
			'forum_prive_objets'=> 'oui',
			'messagerie_agenda'=> 'oui',
			'activer_statistiques'=> 'oui',
			'documents_article'=> 'oui',
			'config_precise_groupes'=> 'oui',
			'gd_formats_read'=> 'gif,jpg,png',
			'gd_formats'=> 'gif,jpg,png',
			'formats_graphiques'=> 'gif,jpg,png',
			'image_process'=> 'gd2',
			'max_taille_vignettes_test'=> '2016400',
			'max_taille_vignettes'=> '1904400',
			'max_taille_vignettes_echec'=> '2073600'
		);
		foreach($conf_spip_defaut as $k => $val){
			ecrire_meta($k , $val);
		}					
		ecrire_metas();
		
		# Installer la conf d'affichage apr defaut
		$sommaire=array(
			'affedito' => 'oui',
			'afflistart' => 'oui',
			'ctncol_d_1' => 'prive',
//			'ctncol_d_2' => 'agenda',
			'edito' => 'texte',
			'mot_edito' => 'Editorial',
			'nbartedito' => '1',
			'nbarticles' => '5',
			'pos_menu' => 'gauche'
		);

		$menus=array(
			'affmenuv' => 'oui',
			'img_menu' => 'folder',
			'larg_page' => '1024',
			'lien_accueil_menu' => 'oui',
			'nb_cols' => '3',
			'type_menu' => 'menu-dom'
		);
					
		$entete= array(
//			'affgradient_entete'=> 'oui',
			'affimgbg_entete'=> 'oui',
			'affimg_entete'=> 'oui',
			'afflogosite'=> 'oui',
			'afftitresite'=> 'oui',
			'color_texte_entete'=> '#000000',
			'couleur_entete'=> '#f8f8ff',
//			'dirgradient_entete'=> 'horizontal',
//			'gradientend_entete'=> '#fdb218',
//			'gradientlenght_entete'=> '40',
//			'gradientstart_entete'=> '#ff5400',
			'hauteur_entete'=> '80',
//			'img_entete_bg'=> 'config/tispip_config_entete_commentaire/img_entete_bg.png',
			'img_entete' => 'config/tispip_config_entete_commentaire/img_entete.png',
			'largeur_entete'=> 'ecran',
			'largeur_ctn_entete' => 'page',
			'largeur_titre'=> '600',
			'logo_entete'=> 'config/tispip_config_entete_bg/logo_entete.png',
			'logo_entete_pos_h'=> 'center',
			'logo_entete_pos_v'=> 'bottom',
			'logo_entete_repeat'=> 'no-repeat',
			'police_titre'=> 'WCManoNegraBoldBta',
			'posgradient_entete'=> 'top',
			'taille_titre'=> '40',
			'titre_couleur'=> '#ffffff',
			'titre_pos_h'=> '130',
			'titre_pos_v'=> '-4'
		);
					
		$rubrique=array(
			'afflistart' => 'oui',
//			'ctncol_d_1' => 'mots',
			'ctncol_d_1' => 'sites',
//			'ctncol_g_1' => 'agenda',
			'intro_long_sites_col_d_2' => '150',
			'nb_sites_col_d_2' => '5',
			'nb_syndic_col_g_2' => '5',
			'nbarticles' => '5',
			'pos_menu' => 'gauche',
//			'type_mots_col_d_1' => 'mots_acc'
		);
		$article=array(
			'ctncol_d_1' => 'meme-rub',
			'nb_cols_art' => '3',
			'pos_menu' => 'gauche',
			'textart_justify' => 'textart_justify'
		);
		$couleurs= array(
			'color1' => '#f8f8ff',
			'color2' => '#fff',
			'color3' => '#777777',
			'color26' => '#ff6600',
			'color93' => '#222222',
			'color30' => '#fdb218',
			'color31' => '#666666',
			'color33' => '#fdf3be',
			'color34' => '#666666',
			'color35' => '#f4f4fa',
			'color36' => '#666666',
			'color37' => '#f8f8ff',
			'color38' => '#999999',
			'color39' => '#ffffff',
			'color4' => '#111111',
			'color5' => '#ff6600',
			'color50' => '#f8f8ff',
			'color51' => '#06305b',
			'color52' => '#ffffff',
			'color53' => '#444546',
			'color70' => '#f8f8ff',
			'color71' => '#fdb218',
			'color72' => '#06305b',
			'color73' => '#ffffff',
			'color74' => '#444444',
			'color_bord_lien_menu' => '#999999',
			'color_bord_lien_menu_expose' => '#ff6600',
			'color_bord_lien_menu_ouvert' => '#666666',
			'color_bord_lien_menu_survol' => '#aaaaaa',
			'color_fond_lien_menu' => '#ffffff',
			'color_fond_lien_menu_expose' => '#666666',
			'color_fond_lien_menu_ouvert' => '#eeeeee',
			'color_fond_lien_menu_survol' => '#222222',
			'color_text_lien_menu' => '#999999',
			'color_text_lien_menu_expose' => '#ff6600',
			'color_text_lien_menu_ouvert' => '#333333',
			'color_text_lien_menu_survol' => '#aaaaaa',
			'indent_menu' => '10',
			'larg_bord_menu_bottom' => '1',
			'larg_bord_menu_left' => '1',
			'larg_bord_menu_right' => '1',
			'larg_bord_menu_top' => '1',
			'marge_v_menu' => '5'
		);
					
		$upload= array(
			'aff_bg_page' => 'oui',
			'img_body' => 'config/tispip_config_bg_page/img_body.png',
			'img_body_pos_h' => 'center',
			'img_body_pos_v' => 'top',
			'img_body_repeat' => 'repeat-y'
		);
		
		$css= array('conf_css_01' => 'div#container{border-top:1px solid #4d4d4d}');

		$tispip=array('sommaire'=>$sommaire, 'menus'=>$menus, 'entete'=>$entete, 'rubriques'=>$rubrique, 'articles'=>$article, 'upload'=>$upload, 'couleurs'=>$couleurs, 'css'=>$css, 'nb_cols'=>'3', 'affmenuv'=>'oui', 'type_menu'=>'menu-dom', 'img_menu'=>'folder', 'larg_page'=>'1024', 'lien_accueil_menu'=>'oui', 'affonglets'=>'oui', 'accueil'=>'oui', 'prive'=>'oui' );
		ecrire_meta('tispipskelet_conf', serialize($tispip));
		ecrire_metas();
		

		#On redirige vers la page de validation des plugins
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
		echo "<!--\n";
		echo "parent.principale.window.location.href = '../spip/ecrire/?exec=admin_plugin';\n";
		echo "//-->\n";
		echo "</script>\n";
	}					
		
?>
