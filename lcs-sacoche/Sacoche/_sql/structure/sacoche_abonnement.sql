DROP TABLE IF EXISTS sacoche_abonnement;

-- Attention : pas d`apostrophes dans les lignes commentées sinon on peut obtenir un bug d`analyse dans la classe pdo de SebR : "SQLSTATE[HY093]: Invalid parameter number: no parameters were bound ..."

CREATE TABLE sacoche_abonnement (
  abonnement_ref           VARCHAR(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
  abonnement_obligatoire   TINYINT(1)  UNSIGNED                NOT NULL DEFAULT 0   COMMENT "Visibilité obligatoire ou au choix de l'utilisateur.",
  abonnement_courriel_only TINYINT(1)  UNSIGNED                NOT NULL DEFAULT 0   COMMENT "Abonnement obligatoire par courriel ou indication possible en page d'accueil.",
  abonnement_profils       VARCHAR(63) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
  abonnement_objet         VARCHAR(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
  abonnement_descriptif    VARCHAR(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
  PRIMARY KEY (abonnement_ref)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT="En cas d'abonnement d'un utilisateur (sacoche_jointure_user_abonnement), les notifications sont dans la table sacoche_notification.";

ALTER TABLE sacoche_abonnement DISABLE KEYS;

INSERT INTO sacoche_abonnement VALUES 
("message_accueil"            , 0, 1, "professeur,directeur,parent,eleve", "message d'accueil"                   , "Message d'accueil nouveau ou modifié."),
("bilan_officiel_statut"      , 0, 0, "professeur,directeur"             , "bilan officiel, étape de saisie"     , "Changement de statut d'un bilan officiel."),
("bilan_officiel_appreciation", 1, 0, "professeur,directeur"             , "bilan officiel, souci d'appréciation", "Signalement d'un souci pour une appréciation d'un bilan officiel."),
("referentiel_edition"        , 0, 0, "professeur"                       , "modification de référentiel"         , "Modification de référentiel (y compris import / suppression)."),
("demande_evaluation_eleve"   , 0, 1, "professeur"                       , "demande d'évaluation formulée"       , "Demande d'évaluation formulée ou retirée."),
("devoir_autoevaluation_eleve", 0, 0, "professeur"                       , "auto-évaluation effectuée"           , "Auto-évaluation effectuée par un élève."),
("devoir_prof_partage"        , 0, 0, "professeur"                       , "devoir partagé"                      , "Partage d'un devoir par un collègue."),
("devoir_edition"             , 0, 0, "parent,eleve"                     , "devoir préparé"                      , "Création ou modification d'un devoir."), -- si la date de visibilité le permet
("devoir_saisie"              , 0, 0, "parent,eleve"                     , "saisie de résultats"                 , "Saisie de notes ou de commentaires d'une évaluation."), -- si la date de visibilité le permet
("demande_evaluation_prof"    , 0, 1, "eleve"                            , "demande d'évaluation traitée"        , "Demande d'évaluation traitée (préparation d'un devoir ou rejet)."),
("bilan_officiel_visible"     , 0, 1, "parent,eleve"                     , "bilan officiel disponible"           , "Bilan officiel disponible au format PDF."), -- uniquement si droit d`accès
("action_sensible"            , 0, 0, "administrateur"                   , "action sensible effectuée"           , "Action sensible effectuée par un enseignant."),
("action_admin"               , 0, 0, "administrateur"                   , "action d'administration"             , "Action effectuée par un autre administrateur."),
("contact_externe"            , 1, 0, "administrateur"                   , "contact externe"                     , "Contact effectué depuis la page d'authentification");

ALTER TABLE sacoche_abonnement ENABLE KEYS;
