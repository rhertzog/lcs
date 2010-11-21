DROP TABLE IF EXISTS sacoche_matiere;

CREATE TABLE sacoche_matiere (
	matiere_id          SMALLINT(5) UNSIGNED                NOT NULL AUTO_INCREMENT,
	matiere_partage     TINYINT(1)  UNSIGNED                NOT NULL DEFAULT 1,
	matiere_transversal TINYINT(1)  UNSIGNED                NOT NULL DEFAULT 0,
	matiere_ordre       TINYINT(3)  UNSIGNED                NOT NULL DEFAULT 255,
	matiere_ref         VARCHAR(5)  COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
	matiere_nom         VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
	PRIMARY KEY (matiere_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE sacoche_matiere DISABLE KEYS;

INSERT INTO sacoche_matiere VALUES 
(  1, 1, 0, 255, "A-PLA", "Arts plastiques"),
(  2, 1, 0, 255,  "AGL1", "Anglais LV1"),
(  3, 1, 0, 255,  "AGL2", "Anglais LV2"),
(  4, 1, 0, 255,  "ALL1", "Allemand LV1"),
(  5, 1, 0, 255,  "ALL2", "Allemand LV2"),
(  6, 1, 0, 255, "DECP3", "Découverte professionnelle 3h"),
(  7, 1, 0, 255, "EDMUS", "Education musicale"),
(  8, 1, 0, 255,   "EPS", "Education physique et sportive"),
(  9, 1, 0, 255,  "ESP1", "Espagnol LV1"),
( 10, 1, 0, 255,  "ESP2", "Espagnol LV2"),
( 11, 1, 0, 255, "FRANC", "Français"),
( 12, 1, 0, 255, "HIGEO", "Histoire et géographie"),
( 13, 1, 0, 255, "LATIN", "Latin"),
( 14, 1, 0, 255, "MATHS", "Mathématiques"),
( 15, 1, 0, 255, "PH-CH", "Physique-chimie"),
( 16, 1, 0, 255,   "SVT", "Sciences de la vie et de la terre"),
( 17, 1, 0, 255, "TECHN", "Technologie"),
( 18, 1, 0, 255, "VISCO", "Vie scolaire"),
( 19, 1, 0, 255, "DECP6", "Découverte professionnelle 6h"),
( 20, 1, 0, 255,  "GREC", "Grec ancien"),
( 21, 1, 0, 255,  "ITA1", "Italien LV1"),
( 22, 1, 0, 255,  "ITA2", "Italien LV2"),
( 23, 1, 0, 255, "EDCIV", "Education civique"),
( 24, 1, 0, 255, "IDNCH", "IDD nature corps humain"),
( 25, 1, 0, 255, "IDARH", "IDD arts humanité"),
( 26, 1, 0, 255, "IDLCI", "IDD langues civilisations"),
( 27, 1, 0, 255, "IDCTQ", "IDD création techniques"),
( 28, 1, 0, 255, "IDAUT", "IDD autres"),
( 29, 1, 0, 255, "PHILO", "Philosophie"),
( 30, 1, 0, 255,   "SES", "Sciences economiques et sociales"),
( 31, 1, 0, 255, "HIART", "Histoire des arts"),
( 32, 1, 0, 255,  "RUS1", "Russe LV1"),
( 33, 1, 0, 255,  "RUS2", "Russe LV2"),
( 34, 1, 0, 255,   "DOC", "Documentation"),
( 35, 1, 0, 255,  "POR1", "Portugais LV1"),
( 36, 1, 0, 255,  "POR2", "Portugais LV2"),
( 37, 1, 0, 255,  "CHI1", "Chinois LV1"),
( 38, 1, 0, 255,  "CHI2", "Chinois LV2"),
( 39, 1, 0, 255,  "OCCR", "Occitan"),
( 40, 1, 0, 255, "VSPRO", "Vie sociale et professionnelle"),
( 41, 1, 0, 255, "G-TPR", "Enseignement technologique-professionnel"),
( 42, 1, 0, 255,  "INFO", "Informatique"),
( 99, 1, 1, 255, "TRANS", "Transversal");

ALTER TABLE sacoche_matiere ENABLE KEYS;
