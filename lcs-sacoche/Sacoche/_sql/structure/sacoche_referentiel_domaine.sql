DROP TABLE IF EXISTS sacoche_referentiel_domaine;

CREATE TABLE sacoche_referentiel_domaine (
	domaine_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	matiere_id SMALLINT(5) UNSIGNED NOT NULL,
	niveau_id TINYINT(3) UNSIGNED NOT NULL,
	domaine_ordre TINYINT(3) UNSIGNED NOT NULL COMMENT "Commence à 1.",
	domaine_ref CHAR(1) COLLATE utf8_unicode_ci NOT NULL,
	domaine_nom VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (domaine_id),
	KEY matiere_id (matiere_id),
	KEY niveau_id (niveau_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
