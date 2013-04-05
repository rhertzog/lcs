DROP TABLE IF EXISTS sacoche_parametre;

CREATE TABLE sacoche_parametre (
  parametre_nom    VARCHAR(50)  COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
  parametre_valeur VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
  PRIMARY KEY (parametre_nom)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE sacoche_parametre DISABLE KEYS;

INSERT INTO sacoche_parametre VALUES 
( "version_base"                                  , "" ),
( "sesamath_id"                                   , "0" ),
( "sesamath_uai"                                  , "" ),
( "sesamath_type_nom"                             , "" ),
( "sesamath_key"                                  , "" ),
( "webmestre_uai"                                 , "" ),
( "webmestre_denomination"                        , "" ),
( "etablissement_denomination"                    , "" ),
( "etablissement_adresse1"                        , "" ),
( "etablissement_adresse2"                        , "" ),
( "etablissement_adresse3"                        , "" ),
( "etablissement_telephone"                       , "" ),
( "etablissement_fax"                             , "" ),
( "etablissement_courriel"                        , "" ),
( "mois_bascule_annee_scolaire"                   , "8" ),
( "annee_utilisation_numero"                      , "1" ),
( "connexion_mode"                                , "normal" ),
( "connexion_nom"                                 , "sacoche" ),
( "connexion_departement"                         , "" ),
( "droit_modifier_mdp"                            , "DIR,ENS,DOC,EDU,TUT,ELV" ),
( "droit_validation_entree"                       , "DIR,ENS,DOC,EDU" ),
( "droit_validation_pilier"                       , "DIR,ENS,ONLY_PP" ),
( "droit_annulation_pilier"                       , "DIR" ),
( "droit_affecter_langue"                         , "DIR,ENS,ONLY_LV" ),
( "droit_gerer_referentiel"                       , "ENS,DOC,EDU,ONLY_COORD" ),
( "droit_gerer_ressource"                         , "ENS,DOC,EDU" ),
( "droit_voir_referentiels"                       , "DIR,ENS,DOC,EDU,TUT,ELV" ),
( "droit_voir_grilles_items"                      , "DIR,ENS,DOC,EDU,TUT,ELV" ),
( "droit_voir_score_bilan"                        , "DIR,ENS,DOC,EDU,TUT,ELV" ),
( "droit_voir_algorithme"                         , "DIR,ENS,DOC,EDU,TUT,ELV" ),
( "droit_releve_etat_acquisition"                 , "TUT,ELV" ),
( "droit_releve_moyenne_score"                    , "TUT,ELV" ),
( "droit_releve_pourcentage_acquis"               , "TUT,ELV" ),
( "droit_releve_conversion_sur_20"                , "" ),
( "droit_socle_acces"                             , "TUT,ELV" ),
( "droit_socle_pourcentage_acquis"                , "TUT,ELV" ),
( "droit_socle_etat_validation"                   , "" ),
( "droit_officiel_releve_modifier_statut"         , "DIR" ),
( "droit_officiel_releve_corriger_appreciation"   , "DIR" ),
( "droit_officiel_releve_appreciation_generale"   , "DIR,ENS,ONLY_PP" ),
( "droit_officiel_releve_impression_pdf"          , "DIR" ),
( "droit_officiel_bulletin_modifier_statut"       , "DIR" ),
( "droit_officiel_bulletin_corriger_appreciation" , "DIR" ),
( "droit_officiel_bulletin_appreciation_generale" , "DIR,ENS,ONLY_PP" ),
( "droit_officiel_bulletin_impression_pdf"        , "DIR" ),
( "droit_officiel_socle_modifier_statut"          , "DIR" ),
( "droit_officiel_socle_corriger_appreciation"    , "DIR" ),
( "droit_officiel_socle_appreciation_generale"    , "DIR,ENS,ONLY_PP" ),
( "droit_officiel_socle_impression_pdf"           , "DIR" ),
( "droit_officiel_releve_voir_archive"            , "DIR,ENS,DOC,EDU" ),
( "droit_officiel_bulletin_voir_archive"          , "DIR,ENS,DOC,EDU" ),
( "droit_officiel_socle_voir_archive"             , "DIR,ENS,DOC,EDU" ),
( "droit_officiel_saisir_assiduite"               , "DIR,EDU" ),
( "calcul_valeur_RR"                              , "0" ),
( "calcul_valeur_R"                               , "33" ),
( "calcul_valeur_V"                               , "67" ),
( "calcul_valeur_VV"                              , "100" ),
( "calcul_seuil_R"                                , "40" ),
( "calcul_seuil_V"                                , "60" ),
( "calcul_methode"                                , "geometrique" ),
( "calcul_limite"                                 , "5" ),
( "calcul_retroactif"                             , "1" ),
( "cas_serveur_host"                              , "" ),
( "cas_serveur_port"                              , "" ),
( "cas_serveur_root"                              , "" ),
( "cas_serveur_url_login"                         , "" ),
( "cas_serveur_url_logout"                        , "" ),
( "cas_serveur_url_validate"                      , "" ),
( "css_background-color_NA"                       , "#ff9999" ),
( "css_background-color_VA"                       , "#ffdd33" ),
( "css_background-color_A"                        , "#99ff99" ),
( "gepi_url"                                      , "" ),
( "gepi_rne"                                      , "" ),
( "gepi_certificat_empreinte"                     , "" ),
( "liste_paliers_actifs"                          , "" ),
( "note_image_style"                              , "Lomer" ),
( "note_texte_RR"                                 , "RR" ),
( "note_texte_R"                                  , "R" ),
( "note_texte_V"                                  , "V" ),
( "note_texte_VV"                                 , "VV" ),
( "note_legende_RR"                               , "Très insuffisant." ),
( "note_legende_R"                                , "Insuffisant." ),
( "note_legende_V"                                , "Satisfaisant." ),
( "note_legende_VV"                               , "Très satisfaisant." ),
( "acquis_texte_NA"                               , "NA" ),
( "acquis_texte_VA"                               , "VA" ),
( "acquis_texte_A"                                , "A" ),
( "acquis_legende_NA"                             , "Non acquis." ),
( "acquis_legende_VA"                             , "Partiellement acquis." ),
( "acquis_legende_A"                              , "Acquis." ),
( "enveloppe_horizontal_gauche"                   , "110" ),
( "enveloppe_horizontal_milieu"                   , "100" ),
( "enveloppe_horizontal_droite"                   , "20" ),
( "enveloppe_vertical_haut"                       , "50" ),
( "enveloppe_vertical_milieu"                     , "45" ),
( "enveloppe_vertical_bas"                        , "20" ),
( "officiel_infos_etablissement"                  , "adresse,telephone,fax,courriel" ),
( "officiel_infos_responsables"                   , "non" ),
( "officiel_nombre_exemplaires"                   , "un" ),
( "officiel_tampon_signature"                     , "signature_ou_tampon" ),
( "officiel_marge_gauche"                         , "5" ),
( "officiel_marge_droite"                         , "5" ),
( "officiel_marge_haut"                           , "5" ),
( "officiel_marge_bas"                            , "10" ),
( "officiel_releve_only_socle"                    , "0" ),
( "officiel_releve_retroactif"                    , "non" ),
( "officiel_releve_appreciation_rubrique"         , "300" ),
( "officiel_releve_appreciation_generale"         , "400" ),
( "officiel_releve_ligne_supplementaire"          , "" ),
( "officiel_releve_assiduite"                     , "0" ),
( "officiel_releve_etat_acquisition"              , "1" ),
( "officiel_releve_moyenne_scores"                , "1" ),
( "officiel_releve_pourcentage_acquis"            , "1" ),
( "officiel_releve_conversion_sur_20"             , "0" ),
( "officiel_releve_cases_nb"                      , "4" ),
( "officiel_releve_aff_coef"                      , "0" ),
( "officiel_releve_aff_socle"                     , "1" ),
( "officiel_releve_aff_domaine"                   , "0" ),
( "officiel_releve_aff_theme"                     , "0" ),
( "officiel_releve_couleur"                       , "oui" ),
( "officiel_releve_legende"                       , "oui" ),
( "officiel_bulletin_fusion_niveaux"              , "1" ),
( "officiel_bulletin_only_socle"                  , "0" ),
( "officiel_bulletin_retroactif"                  , "non" ),
( "officiel_bulletin_appreciation_rubrique"       , "200" ),
( "officiel_bulletin_appreciation_generale"       , "400" ),
( "officiel_bulletin_ligne_supplementaire"        , "" ),
( "officiel_bulletin_assiduite"                   , "0" ),
( "officiel_bulletin_barre_acquisitions"          , "1" ),
( "officiel_bulletin_acquis_texte_nombre"         , "1" ),
( "officiel_bulletin_acquis_texte_code"           , "1" ),
( "officiel_bulletin_moyenne_scores"              , "1" ),
( "officiel_bulletin_conversion_sur_20"           , "1" ),
( "officiel_bulletin_moyenne_classe"              , "1" ),
( "officiel_bulletin_moyenne_generale"            , "0" ),
( "officiel_bulletin_couleur"                     , "oui" ),
( "officiel_bulletin_legende"                     , "oui" ),
( "officiel_socle_appreciation_rubrique"          , "0" ),
( "officiel_socle_appreciation_generale"          , "400" ),
( "officiel_socle_ligne_supplementaire"           , "" ),
( "officiel_socle_assiduite"                      , "0" ),
( "officiel_socle_only_presence"                  , "0" ),
( "officiel_socle_pourcentage_acquis"             , "1" ),
( "officiel_socle_etat_validation"                , "1" ),
( "officiel_socle_couleur"                        , "oui" ),
( "officiel_socle_legende"                        , "oui" );

ALTER TABLE sacoche_parametre ENABLE KEYS;
