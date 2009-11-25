<?php

#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice�.!vanneufville�@!laposte�.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

// Noter :
// outils/mon_outil.php : inclus par les pipelines de l'outil
// outils/mon_outil_options.php : inclus par cout_options.php
// outils/mon_outil_fonctions.php : inclus par cout_fonctions.php

cs_log("inclusion de config_outils.php");
//-----------------------------------------------------------------------------//
//                               options                                       //
//-----------------------------------------------------------------------------//
/*
add_outil( array(
	'id' => 'revision_nbsp',
	'code:options' => '$GLOBALS["activer_revision_nbsp"] = true; $GLOBALS["test_i18n"] = true ;',
	'categorie' => 'admin',
));
*/

	// ici on a besoin d'une case input. La variable est : dossier_squelettes
	// a la toute premiere activation de l'outil, la valeur sera : $GLOBALS['dossier_squelettes']
add_variable( array(
	'nom' => 'dossier_squelettes',
	'format' => _format_CHAINE,
	'defaut' => "\$GLOBALS['dossier_squelettes']",
	'code' => "\$GLOBALS['dossier_squelettes']=%s;",
));
add_outil( array(
	'id' => 'dossier_squelettes',
	'code:spip_options' => '%%dossier_squelettes%%',
	'categorie' => 'admin',
));

/*
add_variable( array(
	'nom' => 'cookie_prefix',
	'format' => _format_CHAINE,
	'defaut' => "'spip'",
	'code' => "\$GLOBALS['cookie_prefix']=%s;",
));
add_outil( array(
	'id' => 'cookie_prefix',
	'code:options' => "%%cookie_prefix%%",
	'categorie' => 'admin',
));
*/

add_outil( array(
	'id' => 'supprimer_numero',
	/* inserer :
		$table_des_traitements['TITRE'][]= 'typo(supprimer_numero(%s))';
		$table_des_traitements['TYPE']['mots']= 'typo(supprimer_numero(%s))';
		$table_des_traitements['NOM'][]= 'typo(supprimer_numero(%s))'; */
	'traitement:TITRE:pre_typo,
	 traitement:TITRE/mots:pre_typo,
	 traitement:NOM:pre_typo,
	 traitement:TYPE/mots:pre_typo' => 'supprimer_numero',
	'categorie' => 'public',
));

add_variables( array(
	'nom' => 'paragrapher',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non', -1 => 'couteauprive:par_defaut'),
	'defaut' => "-1",
	'code:%s>=0' => "\$GLOBALS['toujours_paragrapher']=%s;",
));
add_outil( array(
	'id' => 'paragrapher2',
	'code:spip_options' => '%%paragrapher%%',
	'categorie' => 'admin',
));

add_outil( array(
	'id' => 'forcer_langue',
	'code:spip_options' => "\$GLOBALS['forcer_lang']=true;",
	'categorie' => 'public',
));

add_variables( array(
	'nom' => 'webmestres',
	'format' => _format_CHAINE,
	'defaut' => '"1"',
	'code:strlen(%s)' => "define('_ID_WEBMESTRES', %s);",
	'code:!strlen(%s)' => "define('_ID_WEBMESTRES', 1);",
));
add_outil( array(
	'id' => 'webmestres',
	'code:spip_options' => '%%webmestres%%',
	'categorie' => 'admin',
	// non supporte avant la version 1.92
	'version-min' => '1.9200',
	'autoriser' => "cout_autoriser('webmestre')",
));

add_outil( array(
	'id' => 'insert_head',
	'code:options' => "\$GLOBALS['spip_pipeline']['affichage_final'] .= '|f_insert_head';",
	'categorie' => 'spip',
));

	// ici on a besoin d'une case input. La variable est : suite_introduction
	// a la toute premiere activation de l'outil, la valeur sera : '&nbsp;(...)'
add_variables( array(
	'nom' => 'suite_introduction',
	'format' => _format_CHAINE,
	'defaut' => '"&nbsp;(...)"',
	'code' => "define('_INTRODUCTION_SUITE', %s);\n",
), array(
	'nom' => 'lgr_introduction',
	'format' => _format_NOMBRE,
	'defaut' => 100,
	'code:%s && %s!=100' => "define('_INTRODUCTION_LGR', %s);\n",
), array(
	'nom' => 'lien_introduction',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code' => "define('_INTRODUCTION_LIEN', %s);",
));
add_outil( array(
	'id' => 'introduction',
	'code:options' => "%%lgr_introduction%%%%suite_introduction%%%%lien_introduction%%",
	'categorie' => 'spip',
));

	// ici on a besoin de deux boutons radio : _T('icone_interface_simple') et _T('icone_interface_complet')
add_variable( array(
	'nom' => 'radio_set_options4',
	'format' => _format_CHAINE,
	'radio' => array('basiques' => 'icone_interface_simple', 'avancees' => 'icone_interface_complet'),
	'defaut' => '"avancees"',
	'code' => "\$_GET['set_options']=\$GLOBALS['set_options']=%s;",
));
add_outil( array(
	'id' => 'set_options',
	'auteur' 	 => 'Vincent Ramos',
	'code:options' => "%%radio_set_options4%%",
	'categorie' => 'interface',
	// pipeline pour retirer en JavaScript le bouton de controle de l'interface
	'pipeline:header_prive' => 'set_options_header_prive',
	// non supporte a partir de la version 1.93
	'version-max' => '1.9299',
));

add_outil( array(
	'id' => 'simpl_interface',
	'code:spip_options' => "define('_ACTIVER_PUCE_RAPIDE', false);",
	'categorie' => 'interface',
	'version-min' => '1.9300',
));

add_outil( array(
	'id' => 'icone_visiter',
	'categorie' => 'interface',
	'pipeline:header_prive' => 'icone_visiter_header_prive',
));

add_variables( array(
	'nom' => 'tri_articles',
	'format' => _format_CHAINE,
	'radio' => array(
		'date_modif DESC' => 'couteauprive:tri_modif',
		'0+titre,titre' => 'couteauprive:tri_titre',
		'date DESC' => 'couteauprive:tri_publi', 
		'perso' => 'couteauprive:tri_perso' ),
	'radio/ligne' => 1,
	'defaut' => "'date DESC'", //"'0+titre,titre'",
	'code:%s!="perso"' => "define('_TRI_ARTICLES_RUBRIQUE', %s);\n",
), array(
	'nom' => 'tri_perso',
	'format' => _format_CHAINE,
	'defaut' => '',
	'code:strlen(%s)' => "define('_TRI_ARTICLES_RUBRIQUE', %s);",
));
add_outil( array(
	'id' => 'tri_articles',
	'code:spip_options' => "%%tri_articles%%%%tri_perso%%",
	'categorie' => 'interface',
	'version-min' => '1.9300',
));

	// ici on a besoin de trois boutons radio : _T('couteauprive:js_jamais'), _T('couteauprive:js_defaut') et _T('couteauprive:js_toujours')
add_variable( array(
	'nom' => 'radio_filtrer_javascript3',
	'format' => _format_NOMBRE,
	'radio' => array(-1 => 'couteauprive:js_jamais', 0 => 'couteauprive:js_defaut', 1 => 'couteauprive:js_toujours'),
	'defaut' => 0,
	// si la variable est non nulle, on code...
	'code:%s' => "\$GLOBALS['filtrer_javascript']=%s;",
));
add_outil( array(
	'id' => 'filtrer_javascript',
	'code:options' => "%%radio_filtrer_javascript3%%",
	'categorie' => 'admin',
	'version-min' => '1.9200',
));

	// ici on a besoin d'une case input. La variable est : forum_lgrmaxi
	// a la toute premiere activation de l'outil, la valeur sera : 0 (aucune limite)
add_variable( array(
	'nom' => 'forum_lgrmaxi',
	'format' => _format_NOMBRE,
	'defaut' => 0,
	'code:%s' => "define('_FORUM_LONGUEUR_MAXI', %s);",
));
add_outil( array(
	'id' => 'forum_lgrmaxi',
	'code:spip_options' => "%%forum_lgrmaxi%%",
	'categorie' => 'admin',
	'version-min' => '1.9200',
));

add_variables( array(
	'nom' => 'auteur_forum_nom',
	'check' => 'couteauprive:auteur_forum_nom',
	'defaut' => 1,
), array(
	'nom' => 'auteur_forum_email',
	'check' => 'couteauprive:auteur_forum_email',
	'defaut' => 0,
), array(
	'nom' => 'auteur_forum_deux',
	'check' => 'couteauprive:auteur_forum_deux',
	'defaut' => 0,
));
add_outil( array(
	'id' => 'auteur_forum',
	'categorie'	 => 'admin',
	'jquery'	=> 'oui',
	'code:jq_init' => 'cs_auteur_forum.apply(this);',
	'code:js' => "var cs_verif_email = %%auteur_forum_email%%;\nvar cs_verif_nom = %%auteur_forum_nom%%;\nvar cs_verif_deux = %%auteur_forum_deux%%;",
));

	// ici on a besoin de trois boutons radio : _T('couteauprive:par_defaut'), _T('couteauprive:sf_amont') et _T('couteauprive:sf_tous')
add_variable( array(
	'nom' => 'radio_suivi_forums3',
	'format' => _format_CHAINE,
	'radio' => array('defaut' => 'couteauprive:par_defaut', '_SUIVI_FORUMS_REPONSES' => 'couteauprive:sf_amont', '_SUIVI_FORUM_THREAD' => 'couteauprive:sf_tous'),
	'defaut' => '"defaut"',
	// si la variable est differente de 'defaut' alors on codera le define
	'code:%s!=="defaut"' => "define(%s, true);",
));
add_outil( array(
	'id' => 'suivi_forums',
	'code:options' => "%%radio_suivi_forums3%%",
	'categorie' => 'admin',
	// effectif que dans la version 1.92 (cf : plugin notifications)
	'version-min' => '1.9200',
	'version-max' => '1.9299',
));

add_variable( array(
	'nom' => 'spam_mots',
	'format' => _format_CHAINE,
	'lignes' => 8,
	'defaut' => '"sucking blowjob superbabe ejakulation fucking (asses)"',
	'code' => "define('_spam_MOTS', %s);",
));
add_outil( array(
	'id' => 'spam',
	'code:options' => '%%spam_mots%%',
	'categorie' => 'admin',
));

add_outil( array(
	'id' => 'no_IP',
	'code:spip_options' => '$ip = substr(md5($ip),0,16);',
	'categorie' => 'admin',
));

add_outil( array(
	'id' => 'flock',
	'code:spip_options' => "define('_SPIP_FLOCK',false);",
	'categorie' => 'admin',
	'version-min' => '1.9300',
));

add_variables( array(
	'nom' => 'log_couteau_suisse',
	'check' => 'couteauprive:cs_log_couteau_suisse',
	'defaut' => 0,
), array(
	'nom' => 'spip_options_on',
	'check' => 'couteauprive:cs_spip_options_on',
	'defaut' => 0,
), array(
	'nom' => 'distant_off',
	'check' => 'couteauprive:cs_distant_off',
	'defaut' => 0,
	'code:%s' => "define('_CS_PAS_DE_DISTANT','oui');",
));
add_outil( array(
	'id' => 'cs_comportement',
	'code:spip_options' => "%%distant_off%%",
));


add_outil( array(
	'id' => 'xml',
	'code:options' => "\$GLOBALS['xhtml']='sax';",
	'auteur' => 'Ma&iuml;eul Rouquette',
	'categorie' =>'public',
	'version-min' => '1.9200',
));

add_outil( array(
	'id' => 'f_jQuery',
	'code:options' => "\$GLOBALS['spip_pipeline']['insert_head'] = str_replace('|f_jQuery', '', \$GLOBALS['spip_pipeline']['insert_head']);",
	'auteur' => 'Fil',
	'categorie' =>'public',
	'version-min' => '1.9200',
));

add_variables( array(
	'nom' => 'prive_travaux',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'couteauprive:tous', 1 => 'couteauprive:admins_seuls'),
	'defaut' => 0,
	'code:%s' => "define('_en_travaux_PRIVE', %s);\n",
), array(
	'nom' => 'admin_travaux',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'couteauprive:tous', 1 => 'couteauprive:sauf_admin'),
	'defaut' => 0,
	'code:%s' => "define('_en_travaux_ADMIN', %s);\n",
), array(
	'nom' => 'message_travaux',
	'format' => _format_CHAINE,
	'defaut' => "_T('couteauprive:travaux_prochainement')",
	'lignes' => 3,
	'code' => "\$tr_message=%s;\n",
), array(
	'nom' => 'titre_travaux',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'couteauprive:travaux_titre', 0 => 'couteauprive:travaux_nom_site'),
	'defaut' => 1,
	'code:%s' => "define('_en_travaux_TITRE', %s);",
));
add_outil( array(
	'id' => 'en_travaux',
	'code:options' => "%%message_travaux%%%%prive_travaux%%%%admin_travaux%%%%titre_travaux%%",
	'categorie' => 'admin',
	'auteur' => "Arnaud Ventre pour l'id&eacute;e originale",
));

add_variables( array(
	'nom' => 'bp_tri_auteurs',
	'check' => 'couteauprive:bp_tri_auteurs',
	'defaut' => 1,
	'code:%s' => "define('boites_privees_TRI_AUTEURS', %s);\n",
), array(
	'nom' => 'bp_urls_propres',
	'check' => 'couteauprive:bp_urls_propres',
	'defaut' => 1,
	'code:%s' => "define('boites_privees_URLS_PROPRES', %s);\n",
), array(
	'nom' => 'cs_rss',
	'check' => 'couteauprive:rss_var',
	'defaut' => 1,
	'code:%s' => "define('boites_privees_CS', %s);\n",
), array(
	'nom' => 'format_spip',
	'check' => 'couteauprive:format_spip',
	'defaut' => 1,
	'code:%s' => "define('boites_privees_ARTICLES', %s);\n",
), array(
	'nom' => 'stat_auteurs',
	'check' => 'couteauprive:stat_auteurs',
	'defaut' => 1,
	'code:%s' => "define('boites_privees_AUTEURS', %s);\n",
), array(
	'nom' => 'qui_webmasters',
	'check' => 'couteauprive:qui_webmestres',
	'defaut' => 1,
	'code:%s' => "define('boites_privees_WEBMASTERS', %s);\n",
));
add_outil( array(
	'id' => 'boites_privees',
	'auteur'=>'Pat, Joseph LARMARANGE (format SPIP)',
	'contrib' => 2564,
	'code:options' => "%%cs_rss%%%%format_spip%%%%stat_auteurs%%%%qui_webmasters%%%%bp_urls_propres%%%%bp_tri_auteurs%%",
	'categorie' => 'interface',
	'pipeline:affiche_milieu' => 'boites_privees_affiche_milieu',
	'pipeline:affiche_droite' => 'boites_privees_affiche_droite',
	'pipeline:affiche_gauche' => 'boites_privees_affiche_gauche',
));

add_variables( array(
	'nom' => 'max_auteurs_page',
	'format' => _format_NOMBRE,
	'defaut' => 30,
	'code:%s' => "@define('MAX_AUTEURS_PAR_PAGE', %s);\n",
), array(
	'nom' => 'auteurs_0',	'check' => 'info_administrateurs',	'defaut' => 1,	'code:%s' => "'0minirezo',",
), array(
	'nom' => 'auteurs_1',	'check' => 'info_redacteurs',	'defaut' => 1,	'code:%s' => "'1comite',",
), array(
	'nom' => 'auteurs_5',	'check' => 'info_statut_site_4',	'defaut' => 1,	'code:%s' => "'5poubelle',",
), array(
	'nom' => 'auteurs_6',	'check' => 'info_visiteurs',	'defaut' => 0,	'code:%s' => "'6forum',",
), array(
	'nom' => 'auteurs_n',	'check' => 'couteauprive:nouveaux',	'defaut' => 0,	'code:%s' => "'nouveau',",
), array(
	'nom' => 'auteurs_tout_voir',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'couteauprive:statuts_tous', 0 => 'couteauprive:statuts_spip'),
	'radio/ligne' => 1,
	'defaut' => 0,
//	'code:!%s' => "@define('AUTEURS_DEFAUT', join(\$temp_auteurs,','));",
	'code:!%s' => "if (_request('exec')=='auteurs' && !_request('statut')) \$_GET['statut'] = join(\$temp_auteurs,',');",
	'code:%s' => "if (_request('exec')=='auteurs' && !_request('statut')) \$_GET['statut'] = '!foo';",
));
add_outil( array(
	'id' => 'auteurs',
	'code:options' => "%%max_auteurs_page%%\$temp_auteurs=array(%%auteurs_0%%%%auteurs_1%%%%auteurs_5%%%%auteurs_6%%%%auteurs_n%%); %%auteurs_tout_voir%% unset(\$temp_auteurs);",
	'categorie' => 'interface',
	'version-min' => '1.9300',
//	'pipeline:affiche_milieu' => 'auteurs_affiche_milieu',
));

//-----------------------------------------------------------------------------//
//                               fonctions                                     //
//-----------------------------------------------------------------------------//

add_outil( array(
	'id' => 'verstexte',
	'auteur' => 'C&eacute;dric MORIN',
	'categorie' => 'spip',
));

add_outil( array(
	'id' => 'orientation',
	'auteur' 	 => 'Pierre Andrews (Mortimer) &amp; IZO',
	'categorie' => 'spip',
));

add_variable( array(
	'nom' => 'balise_decoupe',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code:%s' => "define('_decoupe_BALISE', %s);\n",
));
add_outil( array(
	'id' => 'decoupe',
	'contrib'	=> 2135,
	'code:options' => "%%balise_decoupe%%define('_onglets_FIN', '<span class=\'_fooonglets\'></span>');\n@define('_decoupe_SEPARATEUR', '++++');
if(!defined('_SPIP19300') && isset(\$_GET['var_recherche'])) {
	include_spip('inc/headers');
	redirige_par_entete(str_replace('var_recherche=', 'decoupe_recherche=', \$GLOBALS['REQUEST_URI']));
}",
	'code:css' => "div.pagination {display:block; text-align:center; }
div.pagination img { border:0pt none; margin:0pt; padding:0pt; }
span.cs_pagination_off {color: lightgrey; font-weight: bold; text-decoration: underline;} ",
	// construction des onglets
	'code:jq_init' => "onglets_init.apply(this);",
	// pour les balises #TEXTE : $table_des_traitements['TEXTE'] = 'cs_decoupe(propre(%s))';
	// pour les articles, breves et rubriques : $table_des_traitements['TEXTE']['articles'] = 'cs_decoupe(propre(%s))';
	'traitement:TEXTE:post_propre,
	 traitement:TEXTE/articles:post_propre,
	 traitement:TEXTE/breves:post_propre,
	 traitement:TEXTE/rubriques:post_propre' => 'cs_decoupe',
	// pour les balises #TEXTE : $table_des_traitements['TEXTE'] = 'propre(cs_onglets(%s))';
	// pour les articles, breves et rubriques : $table_des_traitements['TEXTE']['articles'] = 'propre(cs_onglets(%s))';
	'traitement:TEXTE:pre_propre,
	 traitement:TEXTE/articles:pre_propre,
	 traitement:TEXTE/breves:pre_propre,
	 traitement:TEXTE/rubriques:pre_propre' => 'cs_onglets',
	'categorie' => 'typo-racc',
	'pipeline:bt_toolbox' => 'decoupe_BarreTypo',
	'pipeline:nettoyer_raccourcis_typo' => 'decoupe_nettoyer_raccourcis',
));

// couplage avec l'outil 'decoupe', donc 'sommaire' doit etre place juste apres :
// il faut inserer le sommaire dans l'article et ensuite seulement choisir la page
add_variables( array(
	'nom' => 'lgr_sommaire',
	'format' => _format_NOMBRE,
	'defaut' => 30,
	'code:%s>=9 && %s<=99' => "define('_sommaire_NB_CARACTERES', %s);\n",
), array(
	'nom' => 'auto_sommaire',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 1,
	'code:%s' => "define('_sommaire_AUTOMATIQUE', %s);\n",
), array(
	'nom' => 'balise_sommaire',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code:%s' => "define('_sommaire_BALISE', %s);",
));
include_spip('inc/filtres');
add_outil( array(
	'id' => 'sommaire',
	'contrib'	=> 2378,
	'code:options' => "define('_sommaire_REM', '<span class=\'_foosommaire\'></span>');\ndefine('_CS_SANS_SOMMAIRE', '[!sommaire]');\ndefine('_CS_AVEC_SOMMAIRE', '[sommaire]');\n%%lgr_sommaire%%%%auto_sommaire%%%%balise_sommaire%%",
	'code:jq' => 'if(jQuery("div.cs_sommaire").length) {
		// s\'il y a un sommaire, on cache la navigation haute sur les pages
		jQuery("div.decoupe_haut").css("display", "none");
		// utilisation des cookies pour conserver l\'etat du sommaire si on quitte la page
		jQuery.getScript(cs_CookiePlugin, cs_sommaire_cookie);
	}',
	'code:jq_init' => 'cs_sommaire_init.apply(this);',
	// inserer : $table_des_traitements['TEXTE']['articles']= 'sommaire_d_article(propre(%s))';
	// idem pour les breves et les rubriques
	'traitement:TEXTE/articles:post_propre,
	 traitement:TEXTE/breves:post_propre,
	 traitement:TEXTE/rubriques:post_propre' => 'sommaire_d_article',
	'traitement:CS_SOMMAIRE:post_propre' => 'sommaire_d_article_balise',
	'categorie' => 'typo-corr',
	'pipeline:nettoyer_raccourcis_typo' => 'sommaire_nettoyer_raccourcis',
));

//-----------------------------------------------------------------------------//
//                               PUBLIC                                        //
//-----------------------------------------------------------------------------//

// TODO : gestion du jQuery dans la fonction a revoir ?
add_outil( array(
	'id' => 'desactiver_flash',
	'auteur' 	 => 'C&eacute;dric MORIN',
	'categorie'	 => 'public',
	'jquery'	=> 'oui',
	// fonction InhibeFlash_init() codee dans desactiver_flash.js : executee lors du chargement de la page et a chaque hit ajax
	'code:jq_init' => 'InhibeFlash_init.apply(this);',
));

add_variables( array(
	'nom' => 'radio_target_blank3',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code' => '$GLOBALS["tweak_target_blank"]=%s;',
), array(
	'nom' => 'url_glossaire_externe2',
	'format' => _format_CHAINE,
	'defaut' => '""',
	'code:strlen(%s)' => '$GLOBALS["url_glossaire_externe"]=%s;',
), array(
	'nom' => 'enveloppe_mails',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non', -1 => 'couteauprive:par_defaut'),
	'defaut' => -1,
	// Code pour le CSS
	'code:%s>0' => 'a.spip_mail:before{content:"\002709" !important;}',
	'code:%s===0' => 'a.spip_mail:before{content:"" !important;}',
));
add_outil( array(
	'id' => 'SPIP_liens',
	'categorie' => 'public',
	'contrib'	=> 2443,
	'jquery'	=> 'oui',
	'description' => '<:SPIP_liens::>'.(defined('_SPIP19300')?'<:SPIP_liens:1:>':''),
	'code:options' => "%%radio_target_blank3%%\n%%url_glossaire_externe2%%",
	'code:jq_init' => 'if (%%radio_target_blank3%%) { if(!cs_prive) jQuery("a.spip_out,a.spip_url,a.spip_glossaire",this).attr("target", "_blank"); }',
	'code:css' => defined('_SPIP19300')?'[[%enveloppe_mails%]]':'',
));

//-----------------------------------------------------------------------------//
//                               NOISETTES                                     //
//-----------------------------------------------------------------------------//

add_outil( array(
	'id' => 'visiteurs_connectes',
	'auteur' => "Phil d'apr&egrave;s spip-contrib",
	'categorie' => 'public',
	'code:options' => "define('_VISITEURS_CONNECTES',1);
function cs_compter_visiteurs(){ return count(preg_files(_DIR_TMP.'visites/','.')); }
function action_visiteurs_connectes(){ echo cs_compter_visiteurs(); return true; }",
	'version-min' => '1.9200', // pour la balise #ARRAY
	//	une mise a jour toutes les 120 sec ?
/*	'code:js' => 'function Timer_visiteurs_connectes(){
		jQuery("span.cs_nb_visiteurs").load("spip.php?action=visiteurs_connectes");
		setTimeout("Timer_visiteurs_connectes()",120000);					
}',
	'code:jq' => ' if(jQuery("span.cs_nb_visiteurs").length) Timer_visiteurs_connectes(); ',
	'jquery' => 'oui',*/
));

//-----------------------------------------------------------------------------//
//                               TYPO                                          //
//-----------------------------------------------------------------------------//

add_outil( array(
	'id' => 'toutmulti',
	'categorie'	 => 'typo-racc',
	'pipeline:pre_typo' => 'ToutMulti_pre_typo',
));

add_outil( array(
	'id' => 'pucesli',
	'auteur' 	 => "J&eacute;r&ocirc;me Combaz pour l'id&eacute;e originale",
	'categorie'	 => 'typo-corr',
	'pipelinecode:pre_typo' => 'if (strpos($flux, "-")!==false) $flux = cs_echappe_balises("", "pucesli_remplace", $flux);',
	'code:options' => 'function pucesli_remplace($texte) {	return preg_replace(\'/^-\s*(?![-*#])/m\', \'-* \', $texte); }',
));

add_outil( array(
    'id' => 'citations_bb',
    'auteur'	=> 'Bertrand Marne, Romy T&ecirc;tue',
    'categorie'	=> 'typo-corr',
	'code:css'	=> '/* fr */
	q:lang(fr):before { content: "\00AB\A0"; }
	q:lang(fr):after { content: "\A0\00BB"; }
	q:lang(fr) q:before { content: "\201C"; }
	q:lang(fr) q:after { content: "\201D"; }
	q:lang(fr) q q:before { content: "\2018"; }
	q:lang(fr) q q:after { content: "\2019"; }
	/* IE */
	* html q { font-style: italic; }
	*+html q { font-style: italic; }', 
    'pipelinecode:pre_propre' => 'if (strpos($flux, "<qu")!==false) $flux=cs_echappe_balises("", "citations_bb_rempl", $flux);',
	// Remplacer <quote> par <q> quand il n'y a pas de retour a la ligne (3 niveaux, preg sans l'option s) 
    'code:options' => 'function citations_bb_rempl($texte){
	$texte = preg_replace($a="/<quote>(.*?)<\/quote>/", $b="<q>\$1</q>", $texte);
	if (strpos($texte, "<qu")!==false) {
		$texte = preg_replace($a, $b, $texte);
		if (strpos($texte, "<qu")!==false) $texte = preg_replace($a, $b, $texte);
	}
	return $texte;
}',
)); 

add_variable( array(
	'nom' => 'decoration_styles',
	'format' => _format_CHAINE,
	'lignes' => 8,
	'defaut' => '"span.sc = font-variant:small-caps;
span.souligne = text-decoration:underline;
span.barre = text-decoration:line-through;
span.dessus = text-decoration:overline;
span.clignote = text-decoration:blink;
span.surfluo = background-color:#ffff00; padding:0px 2px;
span.surgris = background-color:#EAEAEC; padding:0px 2px;
fluo = surfluo"',
	'code' => "define('_decoration_BALISES', %s);",
));
add_outil( array(
	'id' => 'decoration',
	'auteur' 	 => 'izo@aucuneid.net, Pat',
	'contrib'	=> 2427,
	'categorie'	 => 'typo-racc',
	'code:options' => "%%decoration_styles%%",
	'pipeline:pre_typo' => 'decoration_pre_typo',
	'pipeline:bt_toolbox' => 'decoration_BarreTypo',
));

add_variables( array(
	'nom' => 'couleurs_fonds',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non' ),
	'defaut' => 1,
	'code' => "define('_COULEURS_FONDS', %s);\n",
), array(
	'nom' => 'set_couleurs',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'couteauprive:toutes_couleurs', 1 => 'couteauprive:certaines_couleurs'),
	'radio/ligne' => 1,
	'defaut' => 0,
	'code' => "define('_COULEURS_SET', %s);\n",
), array(
	'nom' => 'couleurs_perso',
	'format' => _format_CHAINE,
	'lignes' => 3,
	'defaut' => '"gris, rouge"',
	'code' => "define('_COULEURS_PERSO', %s);",
));
add_outil( array(
	'id' => 'couleurs',
	'auteur' 	 => 'Aur&eacute;lien PIERARD (id&eacute;e originale), Pat',
	'categorie'	 => 'typo-racc',
	'contrib'	=> 2427,
	'pipeline:pre_typo' => 'couleurs_pre_typo',
	'pipeline:nettoyer_raccourcis_typo' => 'couleurs_nettoyer_raccourcis',
	'pipeline:bt_toolbox' => 'couleurs_BarreTypo',
	'code:options' => "%%couleurs_fonds%%%%set_couleurs%%%%couleurs_perso%%",
	'code:fonctions' => "// aide le Couteau Suisse a calculer la balise #INTRODUCTION
include_spip('outils/couleurs');
\$GLOBALS['cs_introduire'][] = 'couleurs_nettoyer_raccourcis';
",
));

// outil essentiellement fran�ais. D'autres langues peuvent etre ajoutees dans outils/typo_exposants.php
add_variable( array(
	'nom' => 'expo_bofbof',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non' ),
	'defaut' => 0,
	'code:%s' => "define('_CS_EXPO_BOFBOF', %s);",
));
add_outil( array(
	'id' => 'typo_exposants',
	'auteur' 	 => 'Vincent Ramos, Pat',
	'categorie'	 => 'typo-corr',
	'contrib'	=> 1564,
	'code:options' => '%%expo_bofbof%%',
	'pipeline:post_typo' => 'typo_exposants',
	'code:css' => 'sup.typo_exposants { font-size:75%; font-variant:normal; vertical-align:super; }',
));

add_outil( array(
	'id' => 'guillemets',
	'auteur' 	 => 'Vincent Ramos',
	'categorie'	 => 'typo-corr',
	'pipeline:post_typo' => 'typo_guillemets',
));

add_variables( array(
	'nom' => 'liens_interrogation',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 1,
	'code:%s' => "\$GLOBALS['liens_interrogation']=true;\n",
), array(
	'nom' => 'liens_orphelins',
	'format' => _format_NOMBRE,
	'radio' => array(-1 => 'item_non', 0 => 'couteauprive:basique', 1 => 'couteauprive:etendu', -2 => 'couteauprive:par_defaut'),
	'defaut' => 0,
	'code' => '$GLOBALS["liens_orphelins"]=%s;',
		// empeche SPIP de convertir les URLs orphelines (URLs brutes)
	'code:%s<>-2' => defined('_SPIP19300')?"\$GLOBALS['spip_pipeline']['pre_liens']=str_replace('|traiter_raccourci_liens','',\$GLOBALS['spip_pipeline']['pre_liens']);":'',
));
// attention : liens_orphelins doit etre place avant mailcrypt ou liens_en_clair
add_outil( array(
	'id' => 'liens_orphelins',
	'categorie'	 => 'typo-corr',
	'contrib'	=> 2443,
	'code:options' => '%%liens_interrogation%%%%liens_orphelins%%',
	'pipeline:pre_propre' => 'liens_orphelins_pipeline',
	'traitement:EMAIL' => 'expanser_liens(liens_orphelins',
 	'pipeline:pre_typo'   => 'interro_pre_typo',
 	'pipeline:post_propre'   => 'interro_post_propre',
));

add_outil( array(
	'id' => 'filets_sep',
	'auteur' 	 => 'FredoMkb',
	'categorie'	 => 'typo-racc',
	'contrib'	=> 1563,
	'pipeline:pre_typo' => 'filets_sep',
	'pipeline:bt_toolbox' => 'filets_sep_BarreTypo',
));

add_outil( array(
	'id' => 'smileys',
	'auteur' 	 => "Sylvain pour l'id&eacute;e originale",
	'categorie'	 => 'typo-corr',
	'contrib'	=> 1561,
	'code:css' => "table.cs_smileys td {text-align:center; font-size:90%; font-weight:bold;}",
	'pipeline:pre_typo' => 'cs_smileys_pre_typo',
	'pipeline:bt_toolbox' => 'cs_smileys_BarreTypo',
));

add_outil( array(
	'id' => 'chatons',
	'auteur' 	 => "BoOz pour l'id&eacute;e originale",
	'categorie'	 => 'typo-racc',
	'pipeline:pre_typo' => 'chatons_pre_typo',
	'pipeline:bt_toolbox' => 'chatons_BarreTypo',
));

add_variables( array(
	'nom' => 'glossaire_groupes',
	'format' => _format_CHAINE,
	'defaut' => "'Glossaire'",
	'code' => "\$GLOBALS['glossaire_groupes']=%s;\n",
), array(
	'nom' => 'glossaire_limite',
	'format' => _format_NOMBRE,
	'defaut' => 0,
	'code:%s>0' => "define('_GLOSSAIRE_LIMITE', %s);\n",
), array(
	'nom' => 'glossaire_js',
	'radio' => array(0 => 'couteauprive:glossaire_css', 1 => 'couteauprive:glossaire_js'),
	'format' => _format_NOMBRE,
	'defaut' => 1,
	'code:%s' => "define('_GLOSSAIRE_JS', %s);",
));
add_outil( array(
	'id' => 'glossaire',
	'categorie'	=> 'typo-corr',
	'contrib'	=> 2206,
	'code:options' => "@define('_CS_SANS_GLOSSAIRE', '[!glossaire]');\n%%glossaire_limite%%%%glossaire_groupes%%%%glossaire_js%%",
//	'traitement:LIEU:post_propre' => 'cs_glossaire',
	// sans oublier les articles, les breves et les rubriques :
	// SPIP ne considere pas que la definition precedente est un tronc commun...
	// meme traitement au chapo des articles...
	'traitement:TEXTE:post_propre,
	 traitement:TEXTE/articles:post_propre,
	 traitement:TEXTE/breves:post_propre,
	 traitement:TEXTE/rubriques:post_propre,
	 traitement:CHAPO:post_propre' => 'cs_glossaire',
	// Precaution pour les articles virtuels
	'traitement:CHAPO:pre_propre' => 'nettoyer_chapo',
	// Mise en forme des titres
	'traitement:TITRE/mots:post_typo' => 'cs_glossaire_titres',
	'code:css' =>  'a.cs_glossaire:after {display:none;}',
	// fonction glossaire_init() codee dans glossaire.js : executee lors du chargement de la page et a chaque hit ajax
	'code:jq_init' => 'glossaire_init.apply(this);',
	'pipelinecode:nettoyer_raccourcis_typo' => '$flux=str_replace(_CS_SANS_GLOSSAIRE, "", $flux);',
));

// attention : mailcrypt doit etre place apres liens_orphelins
add_outil( array(
	'id' => 'mailcrypt',
	'categorie'	=> 'typo-corr',
	'auteur' 	=> "Alexis Roussel, Paolo, Pat",
	'contrib'	=> 2443,
	'jquery'	=> 'oui',
	'pipelinecode:post_propre' => "if(strpos(\$flux, '@')!==false) \$flux=cs_echappe_balises('', 'mailcrypt', \$flux);",
	'code:js' => "function lancerlien(a,b){ x='ma'+'ilto'+':'+a+'@'+b; return x; }",
	// jQuery pour remplacer l'arobase image par l'arobase texte
	// ... puis arranger un peu le title qui a ete protege
	'code:jq_init' => "jQuery('span.spancrypt', this).attr('class','cryptOK').html('&#6'+'4;');
	jQuery(\"a[\"+cs_sel_jQuery+\"title*='..']\", this).each(function () {
		this.title = this.title.replace(/\.\..t\.\./,'[@]');
	});",
	'code:css' => 'span.spancrypt {background:transparent url(' . url_absolue(find_in_path('img/mailcrypt/leure.gif'))
		. ') no-repeat scroll 0.1em center; padding-left:12px; text-decoration:none;}',
	'traitement:EMAIL' => 'mailcrypt',
)); 


// attention : liens_en_clair doit etre place apres tous les outils traitant des liens
add_outil( array(
	'id' => 'liens_en_clair',
	'categorie'	 => 'spip',
	'contrib'	=> 2443,
	'pipeline:post_propre' => 'liens_en_clair_post_propre',
	'code:css' => 'a.spip_out:after {display:none;}',
)); 

add_variables( array(
	'nom' => 'bloc_h4',
	'format' => _format_CHAINE,
	'defaut' => '"h4"',
	'code:preg_match(\',^h\d$,i\', trim(%s))' => "define('_BLOC_TITRE_H', %s);\n",
), array(
	'nom' => 'bloc_unique',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
), array(
	'nom' => 'blocs_cookie',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
));
add_outil( array(
	'id' => 'blocs',
	'categorie'	=> 'typo-racc',
	'contrib' => 2583,
	'code:options' => "%%bloc_h4%%",
	// fonction blocs_init() codee dans blocs.js : executee lors du chargement de la page et a chaque hit ajax
	'code:js' => "var blocs_replier_tout = %%bloc_unique%%;",
	'code:jq_init' => 'blocs_init.apply(this);',
	// utilisation des cookies pour conserver l\'etat des blocs numerotes si on quitte la page
	'code:jq' => 'if(%%blocs_cookie%%) { if(jQuery("div.cs_blocs").length)
		jQuery.getScript(cs_CookiePlugin, cs_blocs_cookie); }',
	'jquery' => 'oui',
	'pipeline:pre_typo' => 'blocs_pre_typo',
	'pipeline:bt_toolbox' => 'blocs_BarreTypo',
)); 

add_variables( array(	// variable utilisee par 'pipelinecode:insert_head'
	'nom' => 'scrollTo',
	'check' => 'couteauprive:jq_scrollTo',
	'defaut' => 1,
	'format' => _format_NOMBRE,
), array(	// variable utilisee par 'pipelinecode:insert_head'
	'nom' => 'LocalScroll',
	'check' => 'couteauprive:jq_localScroll',
	'defaut' => 1,
	'format' => _format_NOMBRE,
));
add_outil( array(
	'id' => 'soft_scroller',
	'categorie'	=> 'public',
	'jquery'	=> 'oui',
	'pipelinecode:insert_head' => 'if(%%scrollTo%%) {$flux.=\'<script src="'.url_absolue(find_in_path("outils/jquery.scrollto.js")).'" type="text/javascript"></script>\'."\n";}
if(%%LocalScroll%%) {$flux.=\'<script src="'.url_absolue(find_in_path("outils/jquery.localscroll.js")).'" type="text/javascript"></script>\'."\n";}',
	'code:js' => 'function soft_scroller_init() { if(typeof jQuery.localScroll=="function") jQuery.localScroll({hash: true}); }',
	'code:jq_init' => 'soft_scroller_init.apply(this);',
));

// http://www.malsup.com/jquery/corner/
add_variables( array(
	'nom' => 'jcorner_classes',
	'format' => _format_CHAINE,
	'lignes' => 10,
	'defaut' => defined('_SPIP19300')?'"// coins ronds aux formulaires
.formulaire_inscription, .formulaire_forum, .formulaire_ecrire_auteur

// colorisation de la dist de SPIP 2.0 en ajoutant un parent
\".chapo, .texte\" = wrap(\'<div class=\"jc_parent\" style=\"padding:4px; background-color:#ffe0c0; margin:4px 0;\"></div>\')
\".menu\" = wrap(\'<div class=\"jc_parent\" style=\"padding:4px; background-color:lightBlue; margin:4px 0;\"></div>\')

// coins ronds aux parents !
.jc_parent"'
		:'" // coins ronds pour les menus de navigation
.rubriques, .breves, .syndic, .forums, .divers

 // en couleurs sur l\'accueil
.liste-articles li .texte = css(\'background-color\', \'#E0F0F0\') .corner()

// colorisation de la dist de SPIP 1.92 en ajoutant un parent
\"#contenu .texte\" = wrap(\'<div class=\"jc_parent\" style=\"padding:4px; background-color:#E0F0F0; margin:4px 0;\"></div>\')

// coins ronds aux parents !
.jc_parent"',
	'code' => "define('_jcorner_CLASSES', %s);",
), array(	// variable utilisee par 'pipelinecode:insert_head'
	'nom' => 'jcorner_plugin',
	'check' => 'couteauprive:jcorner_plugin',
	'defaut' => 1,
	'format' => _format_NOMBRE,
));
add_outil( array(
	'id' => 'jcorner',
	'categorie'	=> 'public',
	'jquery'	=> 'oui',
	'contrib'	=> 2987,
	'code:options' => "%%jcorner_classes%%",
	'pipelinecode:insert_head' => 'if(%%jcorner_plugin%%) {$flux.=\'<script src="'.url_absolue(find_in_path("outils/jquery.corner.js")).'" type="text/javascript"></script>\'."\n";}',
	'pipeline:insert_head' => 'jcorner_insert_head',
	// jcorner_init() n'est disponible qu'en partie publique
	'code:jq_init' => 'if(typeof jcorner_init=="function") jcorner_init.apply(this);',
));

add_variables( array(
	'nom' => 'insertions',
	'format' => _format_CHAINE,
	'lignes' => 8,
	'defaut' => '"coeur = c&oelig;ur
manoeuvre = man&oelig;uvre
(oeuvre(s?|r?)) = &oelig;uvre$1
(O(E|e)uvre(s?|r?)\b/ = &OElig;uvre$2
((h|H)uits) = $1uit
/\b(c|C|m.c|M.c|rec|Rec)onn?aiss?a(nce|nces|nt|nts|nte|ntes|ble)\b/ = $1onnaissa$2
/\boeuf(s?)\b/ = &oelig;uf$1
"',
	'code' => "define('_insertions_LISTE', %s);",
));
add_outil( array(
	'id' => 'insertions',
	'categorie'	 => 'typo-corr',
	'code:options' => "%%insertions%%",
	'traitement:TEXTE:pre_propre' => 'insertions_pre_propre',
	// sans oublier les articles, les breves et les rubriques :
	// SPIP ne considere pas que la definition precedente est un tronc commun...
	'traitement:TEXTE/articles:pre_propre,
	 traitement:TEXTE/breves:pre_propre,
	 traitement:TEXTE/rubriques:pre_propre' => 'insertions_pre_propre',
));

// le plugin moderation moderee dans le couteau suisse
include_spip('inc/charsets');
add_outil( array(
	'id' => 'moderation_moderee',
	'auteur' => 'Yohann(potter64)',
	'categorie' => 'admin',
	'version-min' => '1.9300',
	'code:options' => '%%moderation_admin%%%%moderation_redac%%%%moderation_visit%%',
	'code:jq_init' => 'if (window.location.search.match(/page=forum/)!=null) jQuery("legend:contains(\''.addslashes(unicode2charset(html2unicode(_T('bouton_radio_modere_priori')))).'\')", this).next().html(\''.addslashes(_T('couteauprive:moderation_message')).'\');',
	'pipeline:pre_edition' => 'moderation_vip',
));
add_variables( array(
	'nom' => 'moderation_admin',
	'check' => 'couteauprive:moderation_admins',
	'defaut' => 1,
	'code:%s' => "define('_MOD_MOD_0minirezo',%s);",
), array(
	'nom' => 'moderation_redac',
	'check' => 'couteauprive:moderation_redacs',
	'defaut' => 0,
	'code:%s' => "define('_MOD_MOD_1comite',%s);",
), array(
	'nom' => 'moderation_visit',
	'check' => 'couteauprive:moderation_visits',
	'defaut' => 0,
	'code:%s' => "define('_MOD_MOD_6forum',%s);",
));

add_outil( array(
	'id' => 'titre_parent',
	'categorie' => 'spip',
	'contrib' => 2900,
	'code:options' => '%%titres_etendus%%',
));
add_variable( array(
	'nom' => 'titres_etendus',
	'check' => 'couteauprive:titres_etendus',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code:%s' => "define('_PARENTS_ETENDUS',%s);",
));

add_outil( array(
	'id' => 'corbeille',
	'categorie' => 'admin',
	'version-min' => '1.9300',
	'code:options' => "%%arret_optimisation%%",
));
add_variable( array(
	'nom' => 'arret_optimisation',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code:%s' => "define('_CORBEILLE_SANS_OPTIM', 1);
if(!function_exists('genie_optimiser')) { 
	// surcharge de la fonction d'optimisation de SPIP (inc/optimiser.php)
	function genie_optimiser(\$t='foo'){ include_spip('optimiser','genie'); optimiser_base_une_table(); return -(mktime(2,0,0) + rand(0, 3600*4)); }\n}",
));

add_outil( array(
	'id' => 'trousse_balises',
	'categorie' => 'spip',
	'contrib' => 3005,
));

add_outil( array(
	'id' => 'horloge',
	'categorie' => 'spip',
	'contrib' => 2998,
	'pipelinecode:insert_head,
	 pipelinecode:header_prive' => '$flux.=\'<script type="text/javascript" src="\'.generer_url_public(\'cout_dates.js\',\'lang=\'.$GLOBALS[\'spip_lang\']).\'"></script>
<script type="text/javascript" src="'.url_absolue(find_in_path("outils/jquery.jclock.js")).'"></script>\'."\n";',
	'code:jq_init' => 'jclock_init.apply(this);',
));

//reglage du nombre de case pour le brouteur
add_outil( array(
	'id' => 'brouteur',
	'categorie' => 'interface',
	'code:options' => "%%rubrique_brouteur%%"
));
add_variable( array(
	'nom' => 'rubrique_brouteur',
	'format' => _format_NOMBRE,
	'defaut' => 20,
	'code:%s' => "define('_SPIP_SELECT_RUBRIQUES', %s);"
));

// Recuperer tous les outils de la forme outils/monoutil_config.php
foreach (find_all_in_path('outils/', '\w+_config\.php$') as $f) 
if (preg_match(',^([^.]*)_config$,',basename($f,'.php'),$regs)){
	include $f;
	if(function_exists($cs_temp=$regs[1].'_add_outil')) {
		$cs_temp = $cs_temp();
		$cs_temp['id'] = $regs[1];
		add_outil($cs_temp);
	}
	if(function_exists($cs_temp='add_variable_'.$regs[1])) add_variable($cs_temp());
	if(function_exists($cs_temp='add_variables_'.$regs[1])) add_variables($cs_temp());
}

// Nettoyage
unset($cs_temp);

// Ajout des outils personnalises sous forme globale
if(isset($GLOBALS['mes_outils'])) {
	foreach($GLOBALS['mes_outils'] as $id=>$outil) {
		$outil['id'] = $id;
		if(strlen($outil['nom'])) $outil['nom'] = "<i>$outil[nom]</i>";
		add_outil($outil);
	}
	unset($GLOBALS['mes_outils']);
}


// Idees d'ajouts :
// http://archives.rezo.net/spip-core.mbox/
// http://www.spip-contrib.net/Citations
// http://www.spip-contrib.net/la-balise-LESMOTS et d'autres balises #MAINTENANT #LESADMINISTRATEURS #LESREDACTEURS #LESVISITEURS
// http://www.spip-contrib.net/Ajouter-une-lettrine-aux-articles
// http://www.spip-contrib.net/Generation-automatique-de
// http://www.spip-contrib.net/Balise-LOGO-ARTICLE-ORITRAD
// boutonstexte

//global $cs_variables; cs_log($cs_variables, 'cs_variables :');
?>