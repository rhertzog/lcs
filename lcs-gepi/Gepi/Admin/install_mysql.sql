-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Jeu 03 Décembre 2009 à 12:18
-- Version du serveur: 5.0.32
-- Version de PHP: 5.2.0-8+etch16

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `gepi`
--
CREATE DATABASE `gepi_plug`;
USE gepi_plug;
-- --------------------------------------------------------

--
-- Structure de la table `absences`
--

CREATE TABLE IF NOT EXISTS `absences` (
  `login` varchar(50) NOT NULL default '',
  `periode` int(11) NOT NULL default '0',
  `nb_absences` char(2) NOT NULL default '',
  `non_justifie` char(2) NOT NULL default '',
  `nb_retards` char(2) NOT NULL default '',
  `appreciation` text NOT NULL,
  PRIMARY KEY  (`login`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `absences`
--


-- --------------------------------------------------------

--
-- Structure de la table `absences_actions`
--

CREATE TABLE IF NOT EXISTS `absences_actions` (
  `id_absence_action` int(11) NOT NULL auto_increment,
  `init_absence_action` char(2) NOT NULL default '',
  `def_absence_action` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_absence_action`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `absences_actions`
--

INSERT INTO `absences_actions` (`id_absence_action`, `init_absence_action`, `def_absence_action`) VALUES
(1, 'RC', 'Renvoi du cours'),
(2, 'RD', 'Renvoi d&eacute;finitif'),
(3, 'LP', 'Lettre aux parents'),
(4, 'CE', 'Demande de convocation de l&#039;&eacute;l&egrave;ve en vie scolaire'),
(5, 'A', 'Aucune');

-- --------------------------------------------------------

--
-- Structure de la table `absences_creneaux`
--

CREATE TABLE IF NOT EXISTS `absences_creneaux` (
  `id_definie_periode` int(11) NOT NULL auto_increment,
  `nom_definie_periode` varchar(10) NOT NULL default '',
  `heuredebut_definie_periode` time NOT NULL default '00:00:00',
  `heurefin_definie_periode` time NOT NULL default '00:00:00',
  `suivi_definie_periode` tinyint(4) NOT NULL,
  `type_creneaux` varchar(15) NOT NULL,
  PRIMARY KEY  (`id_definie_periode`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Contenu de la table `absences_creneaux`
--

INSERT INTO `absences_creneaux` (`id_definie_periode`, `nom_definie_periode`, `heuredebut_definie_periode`, `heurefin_definie_periode`, `suivi_definie_periode`, `type_creneaux`) VALUES
(1, 'M1', '08:00:00', '08:55:00', 1, 'cours'),
(2, 'M2', '08:55:00', '09:50:00', 1, 'cours'),
(3, 'M3', '10:05:00', '11:00:00', 1, 'cours'),
(4, 'M4', '11:00:00', '11:55:00', 1, 'cours'),
(5, 'M5', '11:55:00', '12:30:00', 1, 'cours'),
(6, 'S1', '13:30:00', '14:25:00', 1, 'cours'),
(7, 'S2', '14:25:00', '15:20:00', 1, 'cours'),
(8, 'S3', '15:35:00', '16:30:00', 1, 'cours'),
(9, 'S4', '16:30:00', '17:30:00', 1, 'cours'),
(10, 'S5', '17:30:00', '18:25:00', 1, 'cours'),
(11, 'P1', '09:50:00', '10:05:00', 1, 'pause'),
(12, 'P2', '15:20:00', '15:35:00', 1, 'pause'),
(13, 'R', '12:00:00', '13:00:00', 1, 'repas'),
(14, 'R1', '13:00:00', '13:30:00', 1, 'pause');

-- --------------------------------------------------------

--
-- Structure de la table `absences_creneaux_bis`
--

CREATE TABLE IF NOT EXISTS `absences_creneaux_bis` (
  `id_definie_periode` int(11) NOT NULL auto_increment,
  `nom_definie_periode` varchar(10) NOT NULL default '',
  `heuredebut_definie_periode` time NOT NULL default '00:00:00',
  `heurefin_definie_periode` time NOT NULL default '00:00:00',
  `suivi_definie_periode` tinyint(4) NOT NULL,
  `type_creneaux` varchar(15) NOT NULL,
  PRIMARY KEY  (`id_definie_periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `absences_creneaux_bis`
--


-- --------------------------------------------------------

--
-- Structure de la table `absences_eleves`
--

CREATE TABLE IF NOT EXISTS `absences_eleves` (
  `id_absence_eleve` int(11) NOT NULL auto_increment,
  `type_absence_eleve` char(1) NOT NULL default '',
  `eleve_absence_eleve` varchar(25) NOT NULL default '0',
  `justify_absence_eleve` char(3) NOT NULL default '',
  `info_justify_absence_eleve` text NOT NULL,
  `motif_absence_eleve` varchar(4) NOT NULL default '',
  `info_absence_eleve` text NOT NULL,
  `d_date_absence_eleve` date NOT NULL default '0000-00-00',
  `a_date_absence_eleve` date default NULL,
  `d_heure_absence_eleve` time default NULL,
  `a_heure_absence_eleve` time default NULL,
  `saisie_absence_eleve` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id_absence_eleve`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `absences_eleves`
--


-- --------------------------------------------------------

--
-- Structure de la table `absences_gep`
--

CREATE TABLE IF NOT EXISTS `absences_gep` (
  `id_seq` char(2) NOT NULL default '',
  `type` char(1) NOT NULL default '',
  PRIMARY KEY  (`id_seq`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `absences_gep`
--


-- --------------------------------------------------------

--
-- Structure de la table `absences_motifs`
--

CREATE TABLE IF NOT EXISTS `absences_motifs` (
  `id_motif_absence` int(11) NOT NULL auto_increment,
  `init_motif_absence` char(2) NOT NULL default '',
  `def_motif_absence` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_motif_absence`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Contenu de la table `absences_motifs`
--

INSERT INTO `absences_motifs` (`id_motif_absence`, `init_motif_absence`, `def_motif_absence`) VALUES
(1, 'A', 'Aucun motif'),
(2, 'AS', 'Accident sport'),
(3, 'AT', 'Absent en retenue'),
(4, 'C', 'Dans la cour'),
(5, 'CF', 'Convenances familiales'),
(6, 'CO', 'Convocation bureau'),
(7, 'CS', 'Compétition sportive'),
(8, 'DI', 'Dispense d''E.P.S.'),
(9, 'ET', 'Erreur d''emploi du temps'),
(10, 'EX', 'Examen'),
(11, 'H', 'Hospitalisation'),
(12, 'JP', 'Justification par le Principal'),
(13, 'MA', 'Maladie'),
(14, 'OR', 'Conseiller'),
(15, 'PR', 'Réveil'),
(16, 'RC', 'Refus de venir en cours'),
(17, 'RE', 'Renvoi'),
(18, 'RT', 'Présent en retenue'),
(19, 'RV', 'Renvoi du cours'),
(20, 'SM', 'Refus de justification'),
(21, 'SP', 'Sortie pédagogique'),
(22, 'ST', 'Stage à l''extérieur'),
(23, 'T', 'Téléphone'),
(24, 'TR', 'Transport'),
(25, 'VM', 'Visite médicale'),
(26, 'IN', 'Infirmerie');

-- --------------------------------------------------------

--
-- Structure de la table `absences_rb`
--

CREATE TABLE IF NOT EXISTS `absences_rb` (
  `id` int(5) NOT NULL auto_increment,
  `eleve_id` varchar(30) NOT NULL,
  `retard_absence` varchar(1) NOT NULL default 'A',
  `groupe_id` varchar(8) NOT NULL,
  `edt_id` int(5) NOT NULL default '0',
  `jour_semaine` varchar(10) NOT NULL,
  `creneau_id` int(5) NOT NULL,
  `debut_ts` int(11) NOT NULL,
  `fin_ts` int(11) NOT NULL,
  `date_saisie` int(20) NOT NULL,
  `login_saisie` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `absences_rb`
--


-- --------------------------------------------------------

--
-- Structure de la table `absences_repas`
--

CREATE TABLE IF NOT EXISTS `absences_repas` (
  `id` int(5) NOT NULL auto_increment,
  `date_repas` date NOT NULL default '0000-00-00',
  `id_groupe` varchar(8) NOT NULL,
  `eleve_id` varchar(30) NOT NULL,
  `pers_id` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `absences_repas`
--


-- --------------------------------------------------------

--
-- Structure de la table `aid`
--

CREATE TABLE IF NOT EXISTS `aid` (
  `id` varchar(100) NOT NULL default '',
  `nom` varchar(100) NOT NULL default '',
  `numero` varchar(8) NOT NULL default '0',
  `indice_aid` int(11) NOT NULL default '0',
  `perso1` varchar(255) NOT NULL default '',
  `perso2` varchar(255) NOT NULL default '',
  `perso3` varchar(255) NOT NULL default '',
  `productions` varchar(100) NOT NULL default '',
  `resume` text NOT NULL,
  `famille` smallint(6) NOT NULL default '0',
  `mots_cles` varchar(255) NOT NULL default '',
  `adresse1` varchar(255) NOT NULL default '',
  `adresse2` varchar(255) NOT NULL default '',
  `public_destinataire` varchar(50) NOT NULL default '',
  `contacts` text NOT NULL,
  `divers` text NOT NULL,
  `matiere1` varchar(100) NOT NULL default '',
  `matiere2` varchar(100) NOT NULL default '',
  `eleve_peut_modifier` enum('y','n') NOT NULL default 'n',
  `prof_peut_modifier` enum('y','n') NOT NULL default 'n',
  `cpe_peut_modifier` enum('y','n') NOT NULL default 'n',
  `fiche_publique` enum('y','n') NOT NULL default 'n',
  `affiche_adresse1` enum('y','n') NOT NULL default 'n',
  `en_construction` enum('y','n') NOT NULL default 'n',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `aid`
--


-- --------------------------------------------------------

--
-- Structure de la table `aid_appreciations`
--

CREATE TABLE IF NOT EXISTS `aid_appreciations` (
  `login` varchar(50) NOT NULL default '',
  `id_aid` varchar(100) NOT NULL default '',
  `periode` int(11) NOT NULL default '0',
  `appreciation` text NOT NULL,
  `statut` char(10) NOT NULL default '',
  `note` float default NULL,
  `indice_aid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_aid`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `aid_appreciations`
--


-- --------------------------------------------------------

--
-- Structure de la table `aid_config`
--

CREATE TABLE IF NOT EXISTS `aid_config` (
  `nom` char(100) NOT NULL default '',
  `nom_complet` char(100) NOT NULL default '',
  `note_max` int(11) NOT NULL default '0',
  `order_display1` char(1) NOT NULL default '0',
  `order_display2` int(11) NOT NULL default '0',
  `type_note` char(5) NOT NULL default '',
  `display_begin` int(11) NOT NULL default '0',
  `display_end` int(11) NOT NULL default '0',
  `message` varchar(40) NOT NULL default '',
  `display_nom` char(1) NOT NULL default '',
  `indice_aid` int(11) NOT NULL default '0',
  `display_bulletin` char(1) NOT NULL default 'y',
  `bull_simplifie` char(1) NOT NULL default 'y',
  `outils_complementaires` enum('y','n') NOT NULL default 'n',
  `feuille_presence` enum('y','n') NOT NULL default 'n',
  PRIMARY KEY  (`indice_aid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `aid_config`
--


-- --------------------------------------------------------

--
-- Structure de la table `aid_familles`
--

CREATE TABLE IF NOT EXISTS `aid_familles` (
  `ordre_affichage` smallint(6) NOT NULL default '0',
  `id` smallint(6) NOT NULL default '0',
  `type` varchar(250) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `aid_familles`
--

INSERT INTO `aid_familles` (`ordre_affichage`, `id`, `type`) VALUES
(0, 10, 'Information, presse'),
(1, 11, 'Philosophie et psychologie, pensée'),
(2, 12, 'Religions'),
(3, 13, 'Sciences sociales, société, humanitaire'),
(4, 14, 'Langues, langage'),
(5, 15, 'Sciences (sciences dures)'),
(6, 16, 'Techniques, sciences appliquées, médecine, cuisine...'),
(7, 17, 'Arts, loisirs et sports'),
(8, 18, 'Littérature, théâtre, poésie'),
(9, 19, 'Géographie et Histoire, civilisations anciennes');

-- --------------------------------------------------------

--
-- Structure de la table `aid_productions`
--

CREATE TABLE IF NOT EXISTS `aid_productions` (
  `id` smallint(6) NOT NULL auto_increment,
  `nom` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Contenu de la table `aid_productions`
--

INSERT INTO `aid_productions` (`id`, `nom`) VALUES
(1, 'Dossier papier'),
(2, 'Emission de radio'),
(3, 'Exposition'),
(4, 'Film'),
(5, 'Spectacle'),
(6, 'Réalisation plastique'),
(7, 'Réalisation technique ou scientifique'),
(8, 'Jeu vidéo'),
(9, 'Animation culturelle'),
(10, 'Maquette'),
(11, 'Site internet'),
(12, 'Diaporama'),
(13, 'Production musicale'),
(14, 'Production théâtrale'),
(15, 'Animation en milieu scolaire'),
(16, 'Programmation logicielle'),
(17, 'Journal');

-- --------------------------------------------------------

--
-- Structure de la table `aid_public`
--

CREATE TABLE IF NOT EXISTS `aid_public` (
  `ordre_affichage` smallint(6) NOT NULL default '0',
  `id` smallint(6) NOT NULL default '0',
  `public` varchar(100) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `aid_public`
--

INSERT INTO `aid_public` (`ordre_affichage`, `id`, `public`) VALUES
(3, 1, 'Lycéens'),
(2, 2, 'Collègiens'),
(1, 3, 'Ecoliers'),
(6, 4, 'Grand public'),
(5, 5, 'Experts (ou spécialistes)'),
(4, 6, 'Etudiants');

-- --------------------------------------------------------

--
-- Structure de la table `archivage_aids`
--

CREATE TABLE IF NOT EXISTS `archivage_aids` (
  `id` int(11) NOT NULL auto_increment,
  `annee` varchar(200) NOT NULL default '',
  `nom` varchar(100) NOT NULL default '',
  `id_type_aid` int(11) NOT NULL default '0',
  `productions` varchar(100) NOT NULL default '',
  `resume` text NOT NULL,
  `famille` smallint(6) NOT NULL default '0',
  `mots_cles` text NOT NULL,
  `adresse1` varchar(255) NOT NULL default '',
  `adresse2` varchar(255) NOT NULL default '',
  `public_destinataire` varchar(50) NOT NULL default '',
  `contacts` text NOT NULL,
  `divers` text NOT NULL,
  `matiere1` varchar(100) NOT NULL default '',
  `matiere2` varchar(100) NOT NULL default '',
  `fiche_publique` enum('y','n') NOT NULL default 'n',
  `affiche_adresse1` enum('y','n') NOT NULL default 'n',
  `en_construction` enum('y','n') NOT NULL default 'n',
  `notes_moyenne` varchar(255) NOT NULL,
  `notes_min` varchar(255) NOT NULL,
  `notes_max` varchar(255) NOT NULL,
  `responsables` text NOT NULL,
  `eleves` text NOT NULL,
  `eleves_resp` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `archivage_aids`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_aid_eleve`
--

CREATE TABLE IF NOT EXISTS `archivage_aid_eleve` (
  `id_aid` int(11) NOT NULL default '0',
  `id_eleve` varchar(255) NOT NULL,
  `eleve_resp` char(1) NOT NULL default 'n',
  PRIMARY KEY  (`id_aid`,`id_eleve`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `archivage_aid_eleve`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_appreciations_aid`
--

CREATE TABLE IF NOT EXISTS `archivage_appreciations_aid` (
  `id_eleve` varchar(255) NOT NULL,
  `annee` varchar(200) NOT NULL,
  `classe` varchar(255) NOT NULL,
  `id_aid` int(11) NOT NULL,
  `periode` int(11) NOT NULL default '0',
  `appreciation` text NOT NULL,
  `note_eleve` varchar(50) NOT NULL,
  `note_moyenne_classe` varchar(255) NOT NULL,
  `note_min_classe` varchar(255) NOT NULL,
  `note_max_classe` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_eleve`,`id_aid`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `archivage_appreciations_aid`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_disciplines`
--

CREATE TABLE IF NOT EXISTS `archivage_disciplines` (
  `id` int(11) NOT NULL auto_increment,
  `annee` varchar(200) NOT NULL,
  `INE` varchar(255) NOT NULL,
  `classe` varchar(255) NOT NULL,
  `num_periode` tinyint(4) NOT NULL,
  `nom_periode` varchar(255) NOT NULL,
  `special` varchar(255) NOT NULL,
  `matiere` varchar(255) NOT NULL,
  `prof` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `moymin` varchar(255) NOT NULL,
  `moymax` varchar(255) NOT NULL,
  `moyclasse` varchar(255) NOT NULL,
  `rang` tinyint(4) NOT NULL,
  `appreciation` text NOT NULL,
  `nb_absences` int(11) NOT NULL,
  `non_justifie` int(11) NOT NULL,
  `nb_retards` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `INE` (`INE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `archivage_disciplines`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_ects`
--

CREATE TABLE IF NOT EXISTS `archivage_ects` (
  `id` int(11) NOT NULL auto_increment,
  `annee` varchar(255) NOT NULL COMMENT 'Annee scolaire',
  `ine` varchar(255) NOT NULL COMMENT 'Identifiant de l''eleve',
  `classe` varchar(255) NOT NULL COMMENT 'Classe de l''eleve',
  `num_periode` int(11) NOT NULL COMMENT 'Identifiant de la periode',
  `nom_periode` varchar(255) NOT NULL COMMENT 'Nom complet de la periode',
  `special` varchar(255) NOT NULL COMMENT 'Cle utilisee pour isoler certaines lignes (par exemple un credit ECTS pour une periode et non une matiere)',
  `matiere` varchar(255) default NULL COMMENT 'Nom de l''enseignement',
  `profs` varchar(255) default NULL COMMENT 'Liste des profs de l''enseignement',
  `valeur` decimal(10,0) NOT NULL COMMENT 'Nombre de crÃ©dits obtenus par l''eleve',
  `mention` varchar(255) NOT NULL COMMENT 'Mention obtenue',
  PRIMARY KEY  (`id`,`ine`,`num_periode`,`special`),
  KEY `archivage_ects_FI_1` (`ine`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `archivage_ects`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_eleves`
--

CREATE TABLE IF NOT EXISTS `archivage_eleves` (
  `ine` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL default '',
  `prenom` varchar(255) NOT NULL default '',
  `sexe` char(1) NOT NULL,
  `naissance` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`ine`),
  KEY `nom` (`nom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `archivage_eleves`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_eleves2`
--

CREATE TABLE IF NOT EXISTS `archivage_eleves2` (
  `annee` varchar(50) NOT NULL default '',
  `ine` varchar(50) NOT NULL,
  `doublant` enum('-','R') NOT NULL default '-',
  `regime` varchar(255) NOT NULL,
  PRIMARY KEY  (`ine`,`annee`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `archivage_eleves2`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_types_aid`
--

CREATE TABLE IF NOT EXISTS `archivage_types_aid` (
  `id` int(11) NOT NULL auto_increment,
  `annee` varchar(200) NOT NULL default '',
  `nom` varchar(100) NOT NULL default '',
  `nom_complet` varchar(100) NOT NULL default '',
  `note_sur` int(11) NOT NULL default '0',
  `type_note` varchar(5) NOT NULL default '',
  `display_bulletin` char(1) NOT NULL default 'y',
  `outils_complementaires` enum('y','n') NOT NULL default 'n',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `archivage_types_aid`
--


-- --------------------------------------------------------

--
-- Structure de la table `ateliers_config`
--

CREATE TABLE IF NOT EXISTS `ateliers_config` (
  `nom_champ` char(100) NOT NULL default '',
  `content` char(255) NOT NULL default '',
  `param` char(100) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `ateliers_config`
--


-- --------------------------------------------------------

--
-- Structure de la table `avis_conseil_classe`
--

CREATE TABLE IF NOT EXISTS `avis_conseil_classe` (
  `login` varchar(50) NOT NULL default '',
  `periode` int(11) NOT NULL default '0',
  `avis` text NOT NULL,
  `statut` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`login`,`periode`),
  KEY `login` (`login`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `avis_conseil_classe`
--


-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE IF NOT EXISTS `classes` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `classe` varchar(100) NOT NULL default '',
  `nom_complet` varchar(100) NOT NULL default '',
  `suivi_par` varchar(50) NOT NULL default '',
  `formule` varchar(100) NOT NULL default '',
  `format_nom` varchar(5) NOT NULL default '',
  `display_rang` char(1) NOT NULL default 'n',
  `display_address` char(1) NOT NULL default 'n',
  `display_coef` char(1) NOT NULL default 'y',
  `display_mat_cat` char(1) NOT NULL default 'n',
  `display_nbdev` char(1) NOT NULL default 'n',
  `display_moy_gen` char(1) NOT NULL default 'y',
  `modele_bulletin_pdf` varchar(255) default NULL,
  `rn_nomdev` char(1) NOT NULL default 'n',
  `rn_toutcoefdev` char(1) NOT NULL default 'n',
  `rn_coefdev_si_diff` char(1) NOT NULL default 'n',
  `rn_datedev` char(1) NOT NULL default 'n',
  `rn_sign_chefetab` char(1) NOT NULL default 'n',
  `rn_sign_pp` char(1) NOT NULL default 'n',
  `rn_sign_resp` char(1) NOT NULL default 'n',
  `rn_sign_nblig` int(11) NOT NULL default '3',
  `rn_formule` text NOT NULL,
  `ects_type_formation` varchar(255) NOT NULL default '',
  `ects_parcours` varchar(255) NOT NULL default '',
  `ects_code_parcours` varchar(255) NOT NULL default '',
  `ects_domaines_etude` varchar(255) NOT NULL default '',
  `ects_fonction_signataire_attestation` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `classes`
--


-- --------------------------------------------------------

--
-- Structure de la table `cn_cahier_notes`
--

CREATE TABLE IF NOT EXISTS `cn_cahier_notes` (
  `id_cahier_notes` int(11) NOT NULL auto_increment,
  `id_groupe` int(11) NOT NULL,
  `periode` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_cahier_notes`,`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `cn_cahier_notes`
--


-- --------------------------------------------------------

--
-- Structure de la table `cn_conteneurs`
--

CREATE TABLE IF NOT EXISTS `cn_conteneurs` (
  `id` int(11) NOT NULL auto_increment,
  `id_racine` int(11) NOT NULL default '0',
  `nom_court` varchar(32) NOT NULL default '',
  `nom_complet` varchar(64) NOT NULL default '',
  `description` varchar(128) NOT NULL default '',
  `mode` char(1) NOT NULL default '2',
  `coef` decimal(3,1) NOT NULL default '1.0',
  `arrondir` char(2) NOT NULL default 's1',
  `ponderation` decimal(3,1) NOT NULL default '0.0',
  `display_parents` char(1) NOT NULL default '0',
  `display_bulletin` char(1) NOT NULL default '1',
  `parent` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `cn_conteneurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `cn_devoirs`
--

CREATE TABLE IF NOT EXISTS `cn_devoirs` (
  `id` int(11) NOT NULL auto_increment,
  `id_conteneur` int(11) NOT NULL default '0',
  `id_racine` int(11) NOT NULL default '0',
  `nom_court` varchar(32) NOT NULL default '',
  `nom_complet` varchar(64) NOT NULL default '',
  `description` varchar(128) NOT NULL default '',
  `facultatif` char(1) NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `coef` decimal(3,1) NOT NULL default '0.0',
  `note_sur` int(11) default '20',
  `ramener_sur_referentiel` char(1) NOT NULL default 'F',
  `display_parents` char(1) NOT NULL default '',
  `display_parents_app` char(1) NOT NULL default '0',
  `date_ele_resp` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `cn_devoirs`
--


-- --------------------------------------------------------

--
-- Structure de la table `cn_notes_conteneurs`
--

CREATE TABLE IF NOT EXISTS `cn_notes_conteneurs` (
  `login` varchar(50) NOT NULL default '',
  `id_conteneur` int(11) NOT NULL default '0',
  `note` float(10,1) NOT NULL default '0.0',
  `statut` char(1) NOT NULL default '',
  `comment` text NOT NULL,
  PRIMARY KEY  (`login`,`id_conteneur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `cn_notes_conteneurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `cn_notes_devoirs`
--

CREATE TABLE IF NOT EXISTS `cn_notes_devoirs` (
  `login` varchar(50) NOT NULL default '',
  `id_devoir` int(11) NOT NULL default '0',
  `note` float(10,1) NOT NULL default '0.0',
  `comment` text NOT NULL,
  `statut` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`login`,`id_devoir`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `cn_notes_devoirs`
--


-- --------------------------------------------------------

--
-- Structure de la table `commentaires_types`
--

CREATE TABLE IF NOT EXISTS `commentaires_types` (
  `id` int(11) NOT NULL auto_increment,
  `commentaire` text NOT NULL,
  `num_periode` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `commentaires_types`
--


-- --------------------------------------------------------

--
-- Structure de la table `commentaires_types_profs`
--

CREATE TABLE IF NOT EXISTS `commentaires_types_profs` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(255) NOT NULL,
  `app` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `commentaires_types_profs`
--


-- --------------------------------------------------------

--
-- Structure de la table `communes`
--

CREATE TABLE IF NOT EXISTS `communes` (
  `code_commune_insee` varchar(50) NOT NULL,
  `departement` varchar(50) NOT NULL,
  `commune` varchar(255) NOT NULL,
  PRIMARY KEY  (`code_commune_insee`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `communes`
--


-- --------------------------------------------------------

--
-- Structure de la table `ct_devoirs_entry`
--

CREATE TABLE IF NOT EXISTS `ct_devoirs_entry` (
  `id_ct` int(11) NOT NULL auto_increment,
  `id_groupe` int(11) NOT NULL,
  `date_ct` int(11) NOT NULL default '0',
  `id_login` varchar(32) NOT NULL default '',
  `contenu` text NOT NULL,
  `vise` char(1) NOT NULL default 'n',
  PRIMARY KEY  (`id_ct`),
  KEY `id_groupe` (`id_groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ct_devoirs_entry`
--


-- --------------------------------------------------------

--
-- Structure de la table `ct_documents`
--

CREATE TABLE IF NOT EXISTS `ct_documents` (
  `id` int(11) NOT NULL auto_increment,
  `id_ct` int(11) NOT NULL default '0',
  `titre` varchar(255) NOT NULL default '',
  `taille` int(11) NOT NULL default '0',
  `emplacement` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ct_documents`
--


-- --------------------------------------------------------

--
-- Structure de la table `ct_entry`
--

CREATE TABLE IF NOT EXISTS `ct_entry` (
  `id_ct` int(11) NOT NULL auto_increment,
  `heure_entry` time NOT NULL default '00:00:00',
  `id_groupe` int(11) NOT NULL,
  `date_ct` int(11) NOT NULL default '0',
  `id_login` varchar(32) NOT NULL default '',
  `contenu` text NOT NULL,
  `vise` char(1) NOT NULL default 'n',
  `visa` char(1) NOT NULL default 'n',
  PRIMARY KEY  (`id_ct`),
  KEY `id_groupe` (`id_groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ct_entry`
--


-- --------------------------------------------------------

--
-- Structure de la table `ct_types_documents`
--

CREATE TABLE IF NOT EXISTS `ct_types_documents` (
  `id_type` bigint(21) NOT NULL auto_increment,
  `titre` text NOT NULL,
  `extension` varchar(10) NOT NULL default '',
  `upload` enum('oui','non') NOT NULL default 'oui',
  PRIMARY KEY  (`id_type`),
  UNIQUE KEY `extension` (`extension`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

--
-- Contenu de la table `ct_types_documents`
--

INSERT INTO `ct_types_documents` (`id_type`, `titre`, `extension`, `upload`) VALUES
(1, 'JPEG', 'jpg', 'oui'),
(2, 'PNG', 'png', 'oui'),
(3, 'GIF', 'gif', 'oui'),
(4, 'BMP', 'bmp', 'oui'),
(5, 'Photoshop', 'psd', 'oui'),
(6, 'TIFF', 'tif', 'oui'),
(7, 'AIFF', 'aiff', 'oui'),
(8, 'Windows Media', 'asf', 'oui'),
(9, 'Windows Media', 'avi', 'oui'),
(10, 'Midi', 'mid', 'oui'),
(12, 'QuickTime', 'mov', 'oui'),
(13, 'MP3', 'mp3', 'oui'),
(14, 'MPEG', 'mpg', 'oui'),
(15, 'Ogg', 'ogg', 'oui'),
(16, 'QuickTime', 'qt', 'oui'),
(17, 'RealAudio', 'ra', 'oui'),
(18, 'RealAudio', 'ram', 'oui'),
(19, 'RealAudio', 'rm', 'oui'),
(20, 'Flash', 'swf', 'oui'),
(21, 'WAV', 'wav', 'oui'),
(22, 'Windows Media', 'wmv', 'oui'),
(23, 'Adobe Illustrator', 'ai', 'oui'),
(24, 'BZip', 'bz2', 'oui'),
(25, 'C source', 'c', 'oui'),
(26, 'Debian', 'deb', 'oui'),
(27, 'Word', 'doc', 'oui'),
(29, 'LaTeX DVI', 'dvi', 'oui'),
(30, 'PostScript', 'eps', 'oui'),
(31, 'GZ', 'gz', 'oui'),
(32, 'C header', 'h', 'oui'),
(33, 'HTML', 'html', 'oui'),
(34, 'Pascal', 'pas', 'oui'),
(35, 'PDF', 'pdf', 'oui'),
(36, 'PowerPoint', 'ppt', 'oui'),
(37, 'PostScript', 'ps', 'oui'),
(38, 'gr', 'gr', 'oui'),
(39, 'RTF', 'rtf', 'oui'),
(40, 'StarOffice', 'sdd', 'oui'),
(41, 'StarOffice', 'sdw', 'oui'),
(42, 'Stuffit', 'sit', 'oui'),
(43, 'OpenOffice Calc', 'sxc', 'oui'),
(44, 'OpenOffice Impress', 'sxi', 'oui'),
(45, 'OpenOffice', 'sxw', 'oui'),
(46, 'LaTeX', 'tex', 'oui'),
(47, 'TGZ', 'tgz', 'oui'),
(48, 'texte', 'txt', 'oui'),
(49, 'GIMP multi-layer', 'xcf', 'oui'),
(50, 'Excel', 'xls', 'oui'),
(51, 'XML', 'xml', 'oui'),
(52, 'Zip', 'zip', 'oui'),
(53, 'Texte OpenDocument', 'odt', 'oui'),
(54, 'Classeur OpenDocument', 'ods', 'oui'),
(55, 'Présentation OpenDocument', 'odp', 'oui'),
(56, 'Dessin OpenDocument', 'odg', 'oui'),
(57, 'Base de données OpenDocument', 'odb', 'oui');

-- --------------------------------------------------------

--
-- Structure de la table `droits`
--

CREATE TABLE IF NOT EXISTS `droits` (
  `id` varchar(200) NOT NULL default '',
  `administrateur` char(1) NOT NULL default '',
  `professeur` char(1) NOT NULL default '',
  `cpe` char(1) NOT NULL default '',
  `scolarite` char(1) NOT NULL default '',
  `eleve` char(1) NOT NULL default '',
  `responsable` char(1) NOT NULL default '',
  `secours` char(1) NOT NULL default '',
  `autre` char(1) NOT NULL default 'F',
  `description` varchar(255) NOT NULL default '',
  `statut` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `droits`
--

INSERT INTO `droits` (`id`, `administrateur`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `autre`, `description`, `statut`) VALUES
('/absences/index.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', ''),
('/absences/saisie_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', ''),
('/accueil_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/accueil_modules.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/accueil.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', '', ''),
('/aid/add_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/config_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/export_csv_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/index2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/modify_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/modify_aid_new.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/bulletin/edit.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1'),
('/bulletin/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1'),
('/bulletin/param_bull.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1'),
('/bulletin/verif_bulletins.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Vérification du remplissage des bulletins', ''),
('/bulletin/verrouillage.php', 'F', 'F', 'F', 'V', 'F', 'F', 'F', 'F', '(de)Verrouillage des périodes', ''),
('/cahier_notes_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des carnets de notes', ''),
('/cahier_notes/add_modif_conteneur.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/add_modif_dev.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/toutes_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/visu_releve_notes.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation et impression des relevés de notes', ''),
('/cahier_texte_admin/admin_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', ''),
('/cahier_texte_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', ''),
('/cahier_texte_admin/modify_limites.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', ''),
('/cahier_texte_admin/modify_type_doc.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', ''),
('/cahier_texte/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de texte', '1'),
('/cahier_texte/traite_doc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de texte', '1'),
('/classes/classes_ajout.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/classes_const.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/cpe_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des CPE aux classes', ''),
('/classes/duplicate_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/eleve_options.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/modify_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/modify_nom_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/modify_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/periodes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/prof_suivi.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/scol_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des comptes scolarité aux classes', ''),
('/eleves/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des élèves', ''),
('/eleves/import_eleves_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des élèves', ''),
('/eleves/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', ''),
('/eleves/modify_eleve.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', ''),
('/etablissements/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', ''),
('/etablissements/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', ''),
('/etablissements/modify_etab.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', ''),
('/groupes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des groupes', ''),
('/groupes/add_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajout de groupes', ''),
('/groupes/edit_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition de groupes', ''),
('/groupes/edit_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des élèves des groupes', ''),
('/groupes/edit_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des groupes de la classe', ''),
('/gestion/accueil_sauve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Restauration, suppression et sauvegarde de la base', ''),
('/gestion/savebackup.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Téléchargement de sauvegardes la base', ''),
('/gestion/efface_base.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Restauration, suppression et sauvegarde de la base', ''),
('/gestion/gestion_connect.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des connexions', ''),
('/gestion/help_import.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/gestion/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/gestion/import_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/gestion/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/gestion/modify_impression.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des paramètres de la feuille de bienvenue', ''),
('/gestion/param_gen.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration générale', ''),
('/gestion/traitement_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_csv/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/eleves_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_scribe/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_scribe/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_scribe/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_scribe/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_scribe/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_scribe/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_lcs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l''année scolaire', ''),
('/init_lcs/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l''année scolaire', ''),
('/init_lcs/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l''année scolaire', ''),
('/init_lcs/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l''année scolaire', ''),
('/init_lcs/affectations.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l''année scolaire', ''),
('/initialisation/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/initialisation/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/initialisation/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/initialisation/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/initialisation/prof_disc_classe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/initialisation/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/initialisation/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/initialisation/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/initialisation/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/initialisation/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/lib/confirm_query.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/matieres/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', ''),
('/matieres/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', ''),
('/matieres/modify_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', ''),
('/matieres/matieres_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/matieres/matieres_categories.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des catégories de matière', ''),
('/prepa_conseil/edit_limite.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', ''),
('/prepa_conseil/help.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', ''),
('/prepa_conseil/index1.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Visualisation des notes et appréciations', '1'),
('/prepa_conseil/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', ''),
('/prepa_conseil/index3.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', ''),
('/prepa_conseil/visu_aid.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Visualisation des notes et appréciations AID', ''),
('/prepa_conseil/visu_toutes_notes.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', ''),
('/responsables/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des responsables élèves', ''),
('/responsables/modify_resp.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des responsables élèves', ''),
('/saisie/help.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', ''),
('/saisie/import_class_csv.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', '', ''),
('/saisie/import_note_app.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', ''),
('/saisie/index.php', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'F', '', ''),
('/saisie/saisie_aid.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes et appréciations AID', ''),
('/saisie/saisie_appreciations.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des appréciations du bulletins', ''),
('/saisie/ajax_appreciations.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Sauvegarde des appréciations du bulletins', ''),
('/saisie/saisie_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', ''),
('/saisie/saisie_avis1.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', ''),
('/saisie/saisie_avis2.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', ''),
('/saisie/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes du bulletins', ''),
('/saisie/traitement_csv.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes du bulletins', ''),
('/utilisateurs/change_pwd.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/import_prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/impression_bienvenue.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/reset_passwords.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Réinitialisation des mots de passe', ''),
('/utilisateurs/modify_user.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/mon_compte.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Gestion du compte (informations personnelles, mot de passe, ...)', ''),
('/visualisation/classe_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/eleve_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/evol_eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/evol_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/index.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/stats_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/classes/classes_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/fpdf/imprime_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', '', ''),
('/etablissements/import_etab_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', ''),
('/saisie/import_app_cons.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Importation csv des avis du conseil de classe', ''),
('/messagerie/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion de la messagerie', ''),
('/absences/import_absences_gep.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', ''),
('/absences/seq_gep_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', ''),
('/utilitaires/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Maintenance', ''),
('/gestion/contacter_admin.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', '', ''),
('/mod_absences/gestion/gestion_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/impression_absences_liste.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/impression_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/select.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/ajout_ret.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/ajout_dip.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/ajout_inf.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/ajout_abs.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/bilan_absence.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/bilan.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/lettre_aux_parents.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/lib/tableau.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/lib/tableau_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_absences/admin/admin_motifs_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_absences/admin/admin_periodes_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_absences/lib/liste_absences.php', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/lib/graphiques.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/professeurs/prof_ajout_abs.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajout des absences en classe', ''),
('/mod_absences/admin/admin_actions_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des actions absences', ''),
('/mod_trombinoscopes/trombinoscopes.php', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'Visualiser le trombinoscope', ''),
('/mod_trombinoscopes/trombi_impr.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualiser le trombinoscope', ''),
('/mod_trombinoscopes/trombinoscopes_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du trombinoscope', ''),
('/cahier_notes/visu_toutes_notes2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des moyennes des carnets de notes', ''),
('/cahier_notes/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des moyennes des carnets de notes', ''),
('/utilitaires/verif_groupes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Vérification des incohérences d appartenances à des groupes', ''),
('/referencement.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Référencement de Gepi sur la base centralisée des utilisateurs de Gepi', ''),
('/utilisateurs/tab_profs_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des matieres aux professeurs', ''),
('/matieres/matieres_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation des matières depuis un fichier CSV', ''),
('/groupes/edit_class_grp_lot.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des enseignements simples par lot.', ''),
('/init_dbf_sts/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/disciplines_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/lecture_xml_sts_emp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_dbf_sts/save_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/groupes/visu_profs_class.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des équipes pédagogiques', ''),
('/groupes/popup.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des équipes pédagogiques', ''),
('/visualisation/affiche_eleve.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/draw_graphe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/groupes/mes_listes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Accès aux CSV des listes d élèves', ''),
('/groupes/get_csv.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Génération de CSV élèves', ''),
('/visualisation/choix_couleurs.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Choix des couleurs des graphiques des résultats scolaires', ''),
('/visualisation/couleur.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Choix d une couleur pour le graphique des résultats scolaires', ''),
('/gestion/config_prefs.php', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des préférences d utilisateurs', ''),
('/utilitaires/recalcul_moy_conteneurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction des moyennes des conteneurs', ''),
('/saisie/commentaires_types.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Saisie de commentaires-types', ''),
('/mod_absences/lib/fiche_eleve.php', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Fiche du suivie de l''élève', ''),
('/cahier_notes/releve_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Relevé de note au format PDF', ''),
('/impression/parametres_impression_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes PDF; réglage des paramètres', ''),
('/impression/impression_serie.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes (PDF) en série', ''),
('/impression/impression.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression rapide d une listes (PDF) ', ''),
('/impression/liste_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes (PDF)', ''),
('/init_xml/lecture_xml_sconet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/disciplines_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/lecture_xml_sts_emp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/save_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/responsables/maj_import.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', ''),
('/responsables/conversion.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Conversion des données responsables', ''),
('/utilisateurs/create_responsable.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Création des utilisateurs au statut responsable', ''),
('/utilisateurs/create_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Création des utilisateurs au statut responsable', ''),
('/utilisateurs/edit_responsable.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des utilisateurs au statut responsable', ''),
('/utilisateurs/edit_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des utilisateurs au statut élève', ''),
('/cahier_texte/consultation.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation des cahiers de texte', ''),
('/cahier_texte/see_all.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des cahiers de texte', ''),
('/cahier_texte/visu_prof_jour.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Acces_a_son_cahier_de_textes_personnel', ''),
('/gestion/droits_acces.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Paramétrage des droits d accès', ''),
('/groupes/visu_profs_eleve.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation équipe pédagogique', ''),
('/saisie/impression_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression des avis trimestrielles des conseils de classe.', ''),
('/impression/avis_pdf.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression des avis trimestrielles des conseils de classe. Module PDF', ''),
('/impression/parametres_impression_pdf_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression des avis conseil classe PDF; reglage des parametres', ''),
('/utilisateurs/password_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Export des identifiants et mots de passe en csv', ''),
('/impression/password_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Impression des identifiants et des mots de passe en PDF', ''),
('/bulletin/buletin_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Bulletin scolaire au format PDF', ''),
('/mod_absences/gestion/etiquette_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Etiquette au format PDF', ''),
('/mod_absences/lib/export_csv.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Fichier d''exportation en csv des absences', ''),
('/mod_absences/gestion/statistiques.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Statistique du module vie scolaire', '1'),
('/mod_absences/lib/graph_camembert.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique camembert', ''),
('/mod_absences/lib/graph_ligne.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique camembert', ''),
('/mod_absences/admin/admin_horaire_ouverture.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des horaires d''ouverture de l''établissement', ''),
('/mod_absences/admin/admin_config_semaines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des types de semaines', ''),
('/mod_absences/gestion/fiche_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Fiche récapitulatif des absences', ''),
('/mod_absences/lib/graph_double_ligne.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique absence et retard sur le même graphique', ''),
('/bulletin/param_bull_pdf.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'page de gestion des parametres du bulletin pdf', ''),
('/bulletin/bulletin_pdf_avec_modele_classe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'page generant le bulletin pdf en fonction du modele affecte a la classe ', ''),
('/gestion/security_panel.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Panneau de controle des atteintes a la securite', ''),
('/gestion/security_policy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'definition des politiques de securite', ''),
('/mod_absences/gestion/alert_suivi.php', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'système d''alerte de suivi d''élève', ''),
('/gestion/efface_photos.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression des photos non associées à des élèves', ''),
('/responsables/gerer_adr.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des adresses de responsables', ''),
('/responsables/choix_adr_existante.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Choix adresse de responsable existante', ''),
('/cahier_notes/export_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Export CSV/ODS du cahier de notes', ''),
('/cahier_notes/import_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Import CSV du cahier de notes', ''),
('/gestion/options_connect.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Options de connexions', ''),
('/eleves/add_eleve.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', ''),
('/saisie/export_class_ods.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Export ODS des notes/appréciations', ''),
('/gestion/gestion_temp_dir.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des dossiers temporaires d utilisateurs', ''),
('/gestion/param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des couleurs pour Gepi', ''),
('/utilisateurs/creer_remplacant.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'script de création d un remplaçant', ''),
('/mod_absences/gestion/lettre_pdf.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Publipostage des lettres d absences PDF', '1'),
('/accueil_simpl_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Page d accueil simplifiée pour les profs', ''),
('/init_xml2/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/clean_temp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/mod_annees_anterieures/conservation_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Conservation des données antérieures', ''),
('/mod_annees_anterieures/consultation_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des données d années antérieures', ''),
('/mod_annees_anterieures/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Index données antérieures', ''),
('/mod_annees_anterieures/popup_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des données antérieures', ''),
('/mod_annees_anterieures/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Activation/désactivation du module données antérieures', ''),
('/mod_annees_anterieures/nettoyer_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression de données antérieures', ''),
('/responsables/maj_import1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', ''),
('/responsables/maj_import2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', ''),
('/mod_annees_anterieures/corriger_ine.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction d INE dans la table annees_anterieures', ''),
('/mod_annees_anterieures/liste_eleves_ajax.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Recherche d élèves', ''),
('/mod_absences/lib/graph_double_ligne_fiche.php', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'F', 'Graphique de la fiche élève', '1'),
('/edt_organisation/edt_calendrier.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation du calendrier', ''),
('/edt_organisation/index_edt.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des emplois du temps', ''),
('/edt_organisation/edt_initialiser.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation des emplois du temps', ''),
('/edt_organisation/effacer_cours.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Effacer un cours des EdT', ''),
('/edt_organisation/ajouter_salle.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des salles', ''),
('/edt_organisation/edt_parametrer.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les paramètres EdT', ''),
('/edt_organisation/voir_groupe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Voir les groupes de Gepi', ''),
('/edt_organisation/modif_edt_tempo.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modification temporaire des EdT', ''),
('/edt_organisation/edt_init_xml.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation EdT par xml', ''),
('/edt_organisation/edt_init_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par csv', ''),
('/edt_organisation/edt_init_csv2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un autre csv', ''),
('/edt_organisation/edt_init_texte.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un fichier texte', ''),
('/edt_organisation/edt_init_concordance.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un fichier texte', ''),
('/edt_organisation/edt_init_concordance2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un autre fichier csv', ''),
('/edt_organisation/modifier_cours.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Modifier un cours', ''),
('/edt_organisation/modifier_cours_popup.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Modifier un cours', ''),
('/edt_organisation/edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Régler le module emploi du temps', ''),
('/edt_organisation/edt_eleve.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Régler le module emploi du temps', ''),
('/edt_organisation/edt_param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Régler les couleurs des matières (EdT)', ''),
('/edt_organisation/ajax_edtcouleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Changer les couleurs des matières (EdT)', ''),
('/absences/import_absences_sconet.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', ''),
('/mod_absences/admin/admin_config_calendrier.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définir les différentes périodes', ''),
('/bulletin/export_modele_pdf.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'exportation en csv des modeles de bulletin pdf', ''),
('/absences/consulter_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Consulter les absences', ''),
('/mod_absences/professeurs/bilan_absences_professeur.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Bilan des absences pour chaque professeur', ''),
('/mod_absences/professeurs/bilan_absences_classe.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Bilan des absences pour chaque professeur', ''),
('/mod_absences/gestion/voir_absences_viescolaire.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences du jour', ''),
('/mod_absences/gestion/bilan_absences_quotidien.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par créneau', ''),
('/mod_absences/gestion/bilan_absences_quotidien_pdf.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par créneau en pdf', ''),
('/mod_absences/gestion/bilan_absences_classe.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par classe', ''),
('/mod_absences/gestion/bilan_repas_quotidien.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter l inscription aux repas', ''),
('/mod_absences/absences.php', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'F', 'Consulter les absences de son enfant', ''),
('/mod_absences/admin/interface_abs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Paramétrer les interfaces des professeurs', ''),
('/absences/import_absences_gepi.php', 'F', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Page d''importation des absences de gepi mod_absences', '1'),
('/lib/change_mode_header.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Page AJAX pour changer la variable cacher_header', '1'),
('/saisie/recopie_moyennes.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'Recopie des moyennes', ''),
('/groupes/fusion_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fusionner des groupes', ''),
('/gestion/security_panel_archives.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'page archive du panneau de sécurité', ''),
('/lib/header_barre_menu.php/', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Barre horizontale de menu', ''),
('/responsables/corrige_ele_id.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction des ELE_ID d apres Sconet', ''),
('/mod_inscription/inscription_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '(De)activation du module inscription', ''),
('/mod_inscription/inscription_index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'accès au module configuration', ''),
('/mod_inscription/inscription_config.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration du module inscription', ''),
('/mod_inscription/help.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration du module inscription', ''),
('/aid/index_fiches.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Outils complémentaires de gestion des AIDs', ''),
('/aid/visu_fiches.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Outils complémentaires de gestion des AIDs', ''),
('/aid/modif_fiches.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Outils complémentaires de gestion des AIDs', ''),
('/aid/config_aid_fiches_projet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', ''),
('/aid/config_aid_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', ''),
('/aid/config_aid_productions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', ''),
('/aid/annees_anterieures_accueil.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Configuration des AID', ''),
('/classes/acces_appreciations.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration de la restriction d accès aux appréciations pour les élèves et responsables', ''),
('/mod_notanet/notanet_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion du module NOTANET', ''),
('/mod_notanet/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Accueil', ''),
('/mod_notanet/extract_moy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Extraction des moyennes', ''),
('/mod_notanet/corrige_extract_moy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Extraction des moyennes', ''),
('/mod_notanet/select_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Associations élèves/type de brevet', ''),
('/mod_notanet/select_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Associations matières/type de brevet', ''),
('/mod_notanet/saisie_app.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Saisie des appréciations', ''),
('/mod_notanet/generer_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Génération de CSV', ''),
('/mod_notanet/choix_generation_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Génération de CSV', ''),
('/mod_notanet/verrouillage_saisie_app.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: (Dé)Verrouillage des saisies', ''),
('/bulletin/bull_index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1'),
('/cahier_notes/visu_releve_notes_bis.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Relevé de notes', '1'),
('/cahier_notes/param_releve_html.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Paramètres du relevé de notes', '1'),
('/utilisateurs/creer_statut.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Créer des statuts personnalisés', ''),
('/utilisateurs/creer_statut_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Autoriser la création des statuts personnalisés', ''),
('/classes/changement_eleve_classe.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Changement de classe pour un élève', '1'),
('/edt_gestion_gr/edt_aff_gr.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', ''),
('/edt_gestion_gr/edt_ajax_win.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', ''),
('/edt_gestion_gr/edt_liste_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', ''),
('/edt_gestion_gr/edt_liste_profs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', ''),
('/edt_gestion_gr/edt_win.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', ''),
('/mod_notanet/saisie_avis.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Saisie avis chef etablissement', ''),
('/mod_notanet/saisie_b2i_a2.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Saisie socles B2i et A2', ''),
('/mod_notanet/poitiers/fiches_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Accès à l export NOTANET', ''),
('/mod_notanet/poitiers/param_fiche_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Paramètres d impression', ''),
('/mod_notanet/rouen/fiches_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Accès à l export NOTANET', ''),
('/eleves/liste_eleves.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Lister_des_eleves', ''),
('/eleves/visu_eleve.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Consultation_d_un_eleve', ''),
('/cahier_texte_admin/rss_cdt_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gerer les flux rss du cdt', ''),
('/matieres/suppr_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression d une matiere', ''),
('/mod_annees_anterieures/archivage_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches projets', '1'),
('/eleves/import_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation bulletin élève', ''),
('/eleves/export_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Exportation bulletin élève', ''),
('/mod_ent/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', ''),
('/mod_ent/gestion_ent_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', ''),
('/mod_ent/gestion_ent_profs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', ''),
('/mod_ent/miseajour_ent_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', ''),
('/cahier_texte_admin/visa_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page de signature des cahiers de texte', ''),
('/public/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', ''),
('/saisie/saisie_cmnt_type_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie appréciations-types pour les profs', ''),
('/mod_discipline/traiter_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Traitement', ''),
('/mod_discipline/saisie_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie incident', ''),
('/mod_discipline/occupation_lieu_heure.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Occupation lieu', ''),
('/mod_discipline/liste_sanctions_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Liste', ''),
('/mod_discipline/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Index', ''),
('/mod_discipline/incidents_sans_protagonistes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Incidents sans protagonistes', ''),
('/mod_discipline/edt_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: EDT élève', ''),
('/mod_discipline/ajout_sanction.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Ajout sanction', ''),
('/mod_discipline/saisie_sanction.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie sanction', ''),
('/mod_discipline/definir_roles.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définition des rôles', ''),
('/mod_discipline/definir_mesures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définition des mesures', ''),
('/mod_discipline/sauve_role.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg rôle incident', ''),
('/mod_discipline/definir_autres_sanctions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir types sanctions', ''),
('/mod_discipline/liste_retenues_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Liste des retenues du jour', ''),
('/mod_discipline/avertir_famille.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', ''),
('/mod_discipline/avertir_famille_html.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', ''),
('/mod_discipline/sauve_famille_avertie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg famille avertie', ''),
('/mod_discipline/discipline_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Activation/desactivation du module', ''),
('/classes/classes_ajax_lib.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page appelée via ajax.', ''),
('/saisie/saisie_secours_eleve.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie notes/appréciations pour un élève en compte secours', ''),
('/responsables/dedoublonnage_adresses.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Dédoublonnage des adresses responsables', ''),
('/mod_ooo/rapport_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : Rapport Incident', ''),
('/mod_ooo/gerer_modeles_ooo.php', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Gérer et utiliser les modèles', ''),
('/mod_ooo/ooo_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Admin', ''),
('/mod_ooo/retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : Retenue', ''),
('/mod_ooo/formulaire_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : formulaire retenue', ''),
('/mod_ooo/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo: Index : Index', ''),
('/mod_discipline/update_colonne_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Affichage d une imprimante pour le responsable d un incident', ''),
('/mod_discipline/definir_lieux.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les lieux', ''),
('/mod_notanet/fb_rouen_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Rouen', ''),
('/mod_notanet/fb_montpellier_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Montpellier', ''),
('/mod_genese_classes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Accueil', ''),
('/mod_genese_classes/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Activation/désactivation', ''),
('/mod_genese_classes/select_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Choix des options', ''),
('/mod_genese_classes/select_eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Choix des options des élèves', ''),
('/mod_genese_classes/select_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Choix des classes', ''),
('/mod_genese_classes/saisie_contraintes_opt_classe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Saisie des contraintes options/classes', ''),
('/mod_genese_classes/liste_classe_fut.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Liste des classes futures (appel ajax)', ''),
('/mod_genese_classes/affiche_listes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Affichage de listes', ''),
('/mod_genese_classes/genere_ods.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Génération d un fichier ODS de listes', ''),
('/mod_genese_classes/affect_eleves_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Affectation des élèves', ''),
('/mod_genese_classes/select_arriv_red.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Sélection des arrivants/redoublants', ''),
('/mod_genese_classes/liste_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Liste des options de classes existantes', ''),
('/mod_genese_classes/import_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Import options depuis CSV', ''),
('/eleves/import_communes.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Import des communes de naissance', ''),
('/mod_notanet/fb_lille_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Lille', ''),
('/mod_notanet/fb_creteil_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Creteil', ''),
('/mod_plugins/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajouter/enlever des plugins', ''),
('/saisie/export_cmnt_type_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Export appréciations-types pour les profs', '');

-- --------------------------------------------------------

--
-- Structure de la table `droits_aid`
--

CREATE TABLE IF NOT EXISTS `droits_aid` (
  `id` varchar(200) NOT NULL default '',
  `public` char(1) NOT NULL default '',
  `professeur` char(1) NOT NULL default '',
  `cpe` char(1) NOT NULL default '',
  `scolarite` char(1) NOT NULL default '',
  `eleve` char(1) NOT NULL default '',
  `responsable` char(1) NOT NULL default 'F',
  `secours` char(1) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `statut` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `droits_aid`
--

INSERT INTO `droits_aid` (`id`, `public`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `description`, `statut`) VALUES
('nom', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('numero', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('perso1', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('perso2', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('productions', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Production', '1'),
('resume', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Résumé', '1'),
('famille', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Famille', '1'),
('mots_cles', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Mots clés', '1'),
('adresse1', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Adresse publique', '1'),
('adresse2', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Adresse privée', '1'),
('public_destinataire', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Public destinataire', '1'),
('contacts', 'F', 'V', 'F', 'F', 'V', 'F', 'F', 'Contacts, ressources', '1'),
('divers', 'F', 'V', 'F', 'F', 'V', 'F', 'F', 'Divers', '1'),
('matiere1', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Discipline principale', '1'),
('matiere2', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Discipline secondaire', '1'),
('eleve_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A préciser', '1'),
('cpe_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A préciser', '1'),
('prof_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A préciser', '0'),
('fiche_publique', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('affiche_adresse1', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('en_construction', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('perso3', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'A préciser', '0');

-- --------------------------------------------------------

--
-- Structure de la table `droits_speciaux`
--

CREATE TABLE IF NOT EXISTS `droits_speciaux` (
  `id` int(11) NOT NULL auto_increment,
  `id_statut` int(11) NOT NULL,
  `nom_fichier` varchar(200) NOT NULL,
  `autorisation` char(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `droits_speciaux`
--


-- --------------------------------------------------------

--
-- Structure de la table `droits_statut`
--

CREATE TABLE IF NOT EXISTS `droits_statut` (
  `id` int(11) NOT NULL auto_increment,
  `nom_statut` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `droits_statut`
--


-- --------------------------------------------------------

--
-- Structure de la table `droits_utilisateurs`
--

CREATE TABLE IF NOT EXISTS `droits_utilisateurs` (
  `id` int(11) NOT NULL auto_increment,
  `id_statut` int(11) NOT NULL,
  `login_user` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `droits_utilisateurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `ects_credits`
--

CREATE TABLE IF NOT EXISTS `ects_credits` (
  `id` int(11) NOT NULL auto_increment,
  `id_eleve` int(11) NOT NULL COMMENT 'Identifiant de l''eleve',
  `num_periode` int(11) NOT NULL COMMENT 'Identifiant de la periode',
  `id_groupe` int(11) NOT NULL COMMENT 'Identifiant du groupe',
  `valeur` decimal(3,1) NOT NULL COMMENT 'Nombre de credits obtenus par l''eleve',
  `mention` varchar(255) NOT NULL COMMENT 'Mention obtenue',
  PRIMARY KEY  (`id`,`id_eleve`,`num_periode`,`id_groupe`),
  KEY `ects_credits_FI_1` (`id_eleve`),
  KEY `ects_credits_FI_2` (`id_groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ects_credits`
--


-- --------------------------------------------------------

--
-- Structure de la table `ects_global_credits`
--

CREATE TABLE IF NOT EXISTS `ects_global_credits` (
  `id` int(11) NOT NULL auto_increment,
  `id_eleve` int(11) NOT NULL COMMENT 'Identifiant de l''eleve',
  `mention` varchar(255) NOT NULL COMMENT 'Mention obtenue',
  PRIMARY KEY  (`id`,`id_eleve`),
  KEY `ects_global_credits_FI_1` (`id_eleve`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ects_global_credits`
--


-- --------------------------------------------------------

--
-- Structure de la table `edt_calendrier`
--

CREATE TABLE IF NOT EXISTS `edt_calendrier` (
  `id_calendrier` int(11) NOT NULL auto_increment,
  `classe_concerne_calendrier` text NOT NULL,
  `nom_calendrier` varchar(100) NOT NULL default '',
  `debut_calendrier_ts` varchar(11) NOT NULL,
  `fin_calendrier_ts` varchar(11) NOT NULL,
  `jourdebut_calendrier` date NOT NULL default '0000-00-00',
  `heuredebut_calendrier` time NOT NULL default '00:00:00',
  `jourfin_calendrier` date NOT NULL default '0000-00-00',
  `heurefin_calendrier` time NOT NULL default '00:00:00',
  `numero_periode` tinyint(4) NOT NULL default '0',
  `etabferme_calendrier` tinyint(4) NOT NULL,
  `etabvacances_calendrier` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id_calendrier`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `edt_calendrier`
--


-- --------------------------------------------------------

--
-- Structure de la table `edt_classes`
--

CREATE TABLE IF NOT EXISTS `edt_classes` (
  `id_edt_classe` int(11) NOT NULL auto_increment,
  `groupe_edt_classe` int(11) NOT NULL,
  `prof_edt_classe` varchar(25) NOT NULL,
  `matiere_edt_classe` varchar(10) NOT NULL,
  `semaine_edt_classe` varchar(5) NOT NULL,
  `jour_edt_classe` tinyint(4) NOT NULL,
  `datedebut_edt_classe` date NOT NULL,
  `datefin_edt_classe` date NOT NULL,
  `heuredebut_edt_classe` time NOT NULL,
  `heurefin_edt_classe` time NOT NULL,
  `salle_edt_classe` varchar(50) NOT NULL,
  PRIMARY KEY  (`id_edt_classe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `edt_classes`
--


-- --------------------------------------------------------

--
-- Structure de la table `edt_cours`
--

CREATE TABLE IF NOT EXISTS `edt_cours` (
  `id_cours` int(3) NOT NULL auto_increment,
  `id_groupe` varchar(10) NOT NULL,
  `id_salle` varchar(3) NOT NULL,
  `jour_semaine` varchar(10) NOT NULL,
  `id_definie_periode` varchar(3) NOT NULL,
  `duree` varchar(10) NOT NULL default '2',
  `heuredeb_dec` varchar(3) NOT NULL default '0',
  `id_semaine` varchar(3) NOT NULL default '0',
  `id_calendrier` varchar(3) NOT NULL default '0',
  `modif_edt` varchar(3) NOT NULL default '0',
  `login_prof` varchar(50) NOT NULL,
  PRIMARY KEY  (`id_cours`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `edt_cours`
--


-- --------------------------------------------------------

--
-- Structure de la table `edt_dates_special`
--

CREATE TABLE IF NOT EXISTS `edt_dates_special` (
  `id_edt_date_special` int(11) NOT NULL auto_increment,
  `nom_edt_date_special` varchar(200) NOT NULL,
  `debut_edt_date_special` date NOT NULL,
  `fin_edt_date_special` date NOT NULL,
  PRIMARY KEY  (`id_edt_date_special`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `edt_dates_special`
--


-- --------------------------------------------------------

--
-- Structure de la table `edt_init`
--

CREATE TABLE IF NOT EXISTS `edt_init` (
  `id_init` int(11) NOT NULL auto_increment,
  `ident_export` varchar(100) NOT NULL,
  `nom_export` varchar(200) NOT NULL,
  `nom_gepi` varchar(200) NOT NULL,
  PRIMARY KEY  (`id_init`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `edt_init`
--


-- --------------------------------------------------------

--
-- Structure de la table `edt_semaines`
--

CREATE TABLE IF NOT EXISTS `edt_semaines` (
  `id_edt_semaine` int(11) NOT NULL auto_increment,
  `num_edt_semaine` int(11) NOT NULL default '0',
  `type_edt_semaine` varchar(10) NOT NULL default '',
  `num_semaines_etab` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_edt_semaine`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=53 ;

--
-- Contenu de la table `edt_semaines`
--

INSERT INTO `edt_semaines` (`id_edt_semaine`, `num_edt_semaine`, `type_edt_semaine`, `num_semaines_etab`) VALUES
(1, 1, 'A', 0),
(2, 2, 'A', 0),
(3, 3, 'A', 0),
(4, 4, 'A', 0),
(5, 5, 'A', 0),
(6, 6, 'A', 0),
(7, 7, 'A', 0),
(8, 8, 'A', 0),
(9, 9, 'A', 0),
(10, 10, 'A', 0),
(11, 11, 'A', 0),
(12, 12, 'A', 0),
(13, 13, 'A', 0),
(14, 14, 'A', 0),
(15, 15, 'A', 0),
(16, 16, 'A', 0),
(17, 17, 'A', 0),
(18, 18, 'A', 0),
(19, 19, 'A', 0),
(20, 20, 'A', 0),
(21, 21, 'A', 0),
(22, 22, 'A', 0),
(23, 23, 'A', 0),
(24, 24, 'A', 0),
(25, 25, 'A', 0),
(26, 26, 'A', 0),
(27, 27, 'A', 0),
(28, 28, 'A', 0),
(29, 29, 'A', 0),
(30, 30, 'A', 0),
(31, 31, 'A', 0),
(32, 32, 'A', 0),
(33, 33, 'A', 0),
(34, 34, 'A', 0),
(35, 35, 'A', 0),
(36, 36, 'A', 0),
(37, 37, 'A', 0),
(38, 38, 'A', 0),
(39, 39, 'A', 0),
(40, 40, 'A', 0),
(41, 41, 'A', 0),
(42, 42, 'A', 0),
(43, 43, 'A', 0),
(44, 44, 'A', 0),
(45, 45, 'A', 0),
(46, 46, 'A', 0),
(47, 47, 'A', 0),
(48, 48, 'A', 0),
(49, 49, 'A', 0),
(50, 50, 'A', 0),
(51, 51, 'A', 0),
(52, 52, 'A', 0);

-- --------------------------------------------------------

--
-- Structure de la table `edt_setting`
--

CREATE TABLE IF NOT EXISTS `edt_setting` (
  `id` int(3) NOT NULL auto_increment,
  `reglage` varchar(30) NOT NULL,
  `valeur` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Contenu de la table `edt_setting`
--

INSERT INTO `edt_setting` (`id`, `reglage`, `valeur`) VALUES
(1, 'nom_creneaux_s', '1'),
(2, 'edt_aff_salle', 'nom'),
(3, 'edt_aff_matiere', 'long'),
(4, 'edt_aff_creneaux', 'noms'),
(5, 'edt_aff_init_infos', 'oui'),
(6, 'edt_aff_couleur', 'nb'),
(7, 'edt_aff_init_infos2', 'oui'),
(8, 'aff_cherche_salle', 'tous'),
(9, 'param_menu_edt', 'mouseover'),
(0, 'scolarite_modif_cours', 'y');

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE IF NOT EXISTS `eleves` (
  `no_gep` text,
  `login` varchar(50) NOT NULL default '',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `sexe` varchar(1) NOT NULL default '',
  `naissance` date default NULL,
  `lieu_naissance` varchar(50) NOT NULL default '',
  `elenoet` varchar(50) NOT NULL default '',
  `ereno` varchar(50) NOT NULL default '',
  `ele_id` varchar(10) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `id_eleve` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id_eleve`),
  UNIQUE KEY `login` (`login`),
  KEY `ele_id` (`ele_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `eleves`
--


-- --------------------------------------------------------

--
-- Structure de la table `eleves_groupes_settings`
--

CREATE TABLE IF NOT EXISTS `eleves_groupes_settings` (
  `login` varchar(50) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY  (`id_groupe`,`login`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `eleves_groupes_settings`
--


-- --------------------------------------------------------

--
-- Structure de la table `etablissements`
--

CREATE TABLE IF NOT EXISTS `etablissements` (
  `id` char(8) NOT NULL default '',
  `nom` char(50) NOT NULL default '',
  `niveau` char(50) NOT NULL default '',
  `type` char(50) NOT NULL default '',
  `cp` int(10) NOT NULL default '0',
  `ville` char(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `etablissements`
--

INSERT INTO `etablissements` (`id`, `nom`, `niveau`, `type`, `cp`, `ville`) VALUES
('999', 'étranger', 'aucun', 'aucun', 999, '');

-- --------------------------------------------------------

--
-- Structure de la table `etiquettes_formats`
--

CREATE TABLE IF NOT EXISTS `etiquettes_formats` (
  `id_etiquette_format` int(11) NOT NULL auto_increment,
  `nom_etiquette_format` varchar(150) NOT NULL,
  `xcote_etiquette_format` float NOT NULL,
  `ycote_etiquette_format` float NOT NULL,
  `espacementx_etiquette_format` float NOT NULL,
  `espacementy_etiquette_format` float NOT NULL,
  `largeur_etiquette_format` float NOT NULL,
  `hauteur_etiquette_format` float NOT NULL,
  `nbl_etiquette_format` tinyint(4) NOT NULL,
  `nbh_etiquette_format` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id_etiquette_format`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `etiquettes_formats`
--

INSERT INTO `etiquettes_formats` (`id_etiquette_format`, `nom_etiquette_format`, `xcote_etiquette_format`, `ycote_etiquette_format`, `espacementx_etiquette_format`, `espacementy_etiquette_format`, `largeur_etiquette_format`, `hauteur_etiquette_format`, `nbl_etiquette_format`, `nbh_etiquette_format`) VALUES
(1, 'Avery - A4 - 63,5 x 33,9 mm', 2, 2, 5, 5, 63.5, 33, 3, 8);

-- --------------------------------------------------------

--
-- Structure de la table `gc_affichages`
--

CREATE TABLE IF NOT EXISTS `gc_affichages` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `id_aff` int(11) unsigned NOT NULL,
  `id_req` int(11) unsigned NOT NULL,
  `projet` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `valeur` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gc_affichages`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_divisions`
--

CREATE TABLE IF NOT EXISTS `gc_divisions` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `projet` varchar(255) NOT NULL,
  `id_classe` smallint(6) unsigned NOT NULL,
  `classe` varchar(100) NOT NULL default '',
  `statut` enum('actuelle','future','red','arriv') NOT NULL default 'future',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gc_divisions`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_eleves_options`
--

CREATE TABLE IF NOT EXISTS `gc_eleves_options` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `login` varchar(255) NOT NULL,
  `profil` enum('GC','C','RAS','B','TB') NOT NULL default 'RAS',
  `moy` varchar(255) NOT NULL,
  `nb_absences` varchar(255) NOT NULL,
  `non_justifie` varchar(255) NOT NULL,
  `nb_retards` varchar(255) NOT NULL,
  `projet` varchar(255) NOT NULL,
  `id_classe_actuelle` varchar(255) NOT NULL,
  `classe_future` varchar(255) NOT NULL,
  `liste_opt` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gc_eleves_options`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_ele_arriv_red`
--

CREATE TABLE IF NOT EXISTS `gc_ele_arriv_red` (
  `login` varchar(255) NOT NULL,
  `statut` enum('Arriv','Red') NOT NULL,
  `projet` varchar(255) NOT NULL,
  PRIMARY KEY  (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `gc_ele_arriv_red`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_options`
--

CREATE TABLE IF NOT EXISTS `gc_options` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `projet` varchar(255) NOT NULL,
  `opt` varchar(255) NOT NULL,
  `type` enum('lv1','lv2','lv3','autre') NOT NULL,
  `obligatoire` enum('o','n') NOT NULL,
  `exclusive` smallint(6) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gc_options`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_options_classes`
--

CREATE TABLE IF NOT EXISTS `gc_options_classes` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `projet` varchar(255) NOT NULL,
  `opt_exclue` varchar(255) NOT NULL,
  `classe_future` varchar(255) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gc_options_classes`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_projets`
--

CREATE TABLE IF NOT EXISTS `gc_projets` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `projet` varchar(255) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gc_projets`
--


-- --------------------------------------------------------

--
-- Structure de la table `groupes`
--

CREATE TABLE IF NOT EXISTS `groupes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `description` text NOT NULL,
  `recalcul_rang` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `groupes`
--


-- --------------------------------------------------------

--
-- Structure de la table `horaires_etablissement`
--

CREATE TABLE IF NOT EXISTS `horaires_etablissement` (
  `id_horaire_etablissement` int(11) NOT NULL auto_increment,
  `date_horaire_etablissement` date NOT NULL,
  `jour_horaire_etablissement` varchar(15) NOT NULL,
  `ouverture_horaire_etablissement` time NOT NULL,
  `fermeture_horaire_etablissement` time NOT NULL,
  `pause_horaire_etablissement` time NOT NULL,
  `ouvert_horaire_etablissement` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id_horaire_etablissement`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `horaires_etablissement`
--

INSERT INTO `horaires_etablissement` (`id_horaire_etablissement`, `date_horaire_etablissement`, `jour_horaire_etablissement`, `ouverture_horaire_etablissement`, `fermeture_horaire_etablissement`, `pause_horaire_etablissement`, `ouvert_horaire_etablissement`) VALUES
(1, '0000-00-00', 'lundi', '08:00:00', '17:30:00', '00:45:00', 1),
(2, '0000-00-00', 'mardi', '08:00:00', '17:30:00', '00:45:00', 1),
(3, '0000-00-00', 'mercredi', '08:00:00', '12:00:00', '00:00:00', 1),
(4, '0000-00-00', 'jeudi', '08:00:00', '17:30:00', '00:45:00', 1),
(5, '0000-00-00', 'vendredi', '08:00:00', '17:30:00', '00:45:00', 1);

-- --------------------------------------------------------

--
-- Structure de la table `inscription_items`
--

CREATE TABLE IF NOT EXISTS `inscription_items` (
  `id` int(11) NOT NULL auto_increment,
  `date` varchar(10) NOT NULL default '',
  `heure` varchar(20) NOT NULL default '',
  `description` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `inscription_items`
--


-- --------------------------------------------------------

--
-- Structure de la table `inscription_j_login_items`
--

CREATE TABLE IF NOT EXISTS `inscription_j_login_items` (
  `login` varchar(20) NOT NULL default '',
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `inscription_j_login_items`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_aidcateg_utilisateurs`
--

CREATE TABLE IF NOT EXISTS `j_aidcateg_utilisateurs` (
  `indice_aid` int(11) NOT NULL,
  `id_utilisateur` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_aidcateg_utilisateurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_aid_eleves`
--

CREATE TABLE IF NOT EXISTS `j_aid_eleves` (
  `id_aid` varchar(100) NOT NULL default '',
  `login` varchar(60) NOT NULL default '',
  `indice_aid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_aid`,`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_aid_eleves`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_aid_eleves_resp`
--

CREATE TABLE IF NOT EXISTS `j_aid_eleves_resp` (
  `id_aid` varchar(100) NOT NULL default '',
  `login` varchar(60) NOT NULL default '',
  `indice_aid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_aid`,`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_aid_eleves_resp`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_aid_utilisateurs`
--

CREATE TABLE IF NOT EXISTS `j_aid_utilisateurs` (
  `id_aid` varchar(100) NOT NULL default '',
  `id_utilisateur` varchar(50) NOT NULL default '',
  `indice_aid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_aid`,`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_aid_utilisateurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_aid_utilisateurs_gest`
--

CREATE TABLE IF NOT EXISTS `j_aid_utilisateurs_gest` (
  `id_aid` varchar(100) NOT NULL default '',
  `id_utilisateur` varchar(50) NOT NULL default '',
  `indice_aid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_aid`,`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_aid_utilisateurs_gest`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_classes`
--

CREATE TABLE IF NOT EXISTS `j_eleves_classes` (
  `login` varchar(50) NOT NULL default '',
  `id_classe` int(11) NOT NULL default '0',
  `periode` int(11) NOT NULL default '0',
  `rang` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_classe`,`periode`),
  KEY `id_classe` (`id_classe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_classes`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_cpe`
--

CREATE TABLE IF NOT EXISTS `j_eleves_cpe` (
  `e_login` varchar(50) NOT NULL default '',
  `cpe_login` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`e_login`,`cpe_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_cpe`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_etablissements`
--

CREATE TABLE IF NOT EXISTS `j_eleves_etablissements` (
  `id_eleve` varchar(50) NOT NULL default '',
  `id_etablissement` varchar(8) NOT NULL default '',
  PRIMARY KEY  (`id_eleve`,`id_etablissement`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_etablissements`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_groupes`
--

CREATE TABLE IF NOT EXISTS `j_eleves_groupes` (
  `login` varchar(50) NOT NULL default '',
  `id_groupe` int(11) NOT NULL default '0',
  `periode` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_groupe`,`login`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_groupes`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_professeurs`
--

CREATE TABLE IF NOT EXISTS `j_eleves_professeurs` (
  `login` varchar(50) NOT NULL default '',
  `professeur` varchar(50) NOT NULL default '',
  `id_classe` int(11) NOT NULL default '0',
  PRIMARY KEY  (`login`,`professeur`,`id_classe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_professeurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_regime`
--

CREATE TABLE IF NOT EXISTS `j_eleves_regime` (
  `login` varchar(50) NOT NULL default '',
  `doublant` char(1) NOT NULL default '',
  `regime` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_regime`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_groupes_classes`
--

CREATE TABLE IF NOT EXISTS `j_groupes_classes` (
  `id_groupe` int(11) NOT NULL default '0',
  `id_classe` int(11) NOT NULL default '0',
  `priorite` smallint(6) NOT NULL,
  `coef` decimal(3,1) NOT NULL,
  `categorie_id` int(11) NOT NULL default '1',
  `saisie_ects` tinyint(1) NOT NULL default '0',
  `valeur_ects` decimal(3,1) NOT NULL,
  `mode_moy` enum('-','sup10','bonus') NOT NULL default '-',
  PRIMARY KEY  (`id_groupe`,`id_classe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_groupes_classes`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_groupes_matieres`
--

CREATE TABLE IF NOT EXISTS `j_groupes_matieres` (
  `id_groupe` int(11) NOT NULL default '0',
  `id_matiere` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id_groupe`,`id_matiere`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_groupes_matieres`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_groupes_professeurs`
--

CREATE TABLE IF NOT EXISTS `j_groupes_professeurs` (
  `id_groupe` int(11) NOT NULL default '0',
  `login` varchar(50) NOT NULL default '',
  `ordre_prof` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id_groupe`,`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_groupes_professeurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_matieres_categories_classes`
--

CREATE TABLE IF NOT EXISTS `j_matieres_categories_classes` (
  `categorie_id` int(11) NOT NULL default '0',
  `classe_id` int(11) NOT NULL default '0',
  `priority` smallint(6) NOT NULL default '0',
  `affiche_moyenne` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`categorie_id`,`classe_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_matieres_categories_classes`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_professeurs_matieres`
--

CREATE TABLE IF NOT EXISTS `j_professeurs_matieres` (
  `id_professeur` varchar(50) NOT NULL default '',
  `id_matiere` varchar(50) NOT NULL default '',
  `ordre_matieres` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_professeur`,`id_matiere`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_professeurs_matieres`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_scol_classes`
--

CREATE TABLE IF NOT EXISTS `j_scol_classes` (
  `login` varchar(50) NOT NULL,
  `id_classe` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_scol_classes`
--


-- --------------------------------------------------------

--
-- Structure de la table `lettres_cadres`
--

CREATE TABLE IF NOT EXISTS `lettres_cadres` (
  `id_lettre_cadre` int(11) NOT NULL auto_increment,
  `nom_lettre_cadre` varchar(150) NOT NULL,
  `x_lettre_cadre` float NOT NULL,
  `y_lettre_cadre` float NOT NULL,
  `l_lettre_cadre` float NOT NULL,
  `h_lettre_cadre` float NOT NULL,
  `texte_lettre_cadre` text NOT NULL,
  `encadre_lettre_cadre` tinyint(4) NOT NULL,
  `couleurdefond_lettre_cadre` varchar(11) NOT NULL,
  PRIMARY KEY  (`id_lettre_cadre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `lettres_cadres`
--

INSERT INTO `lettres_cadres` (`id_lettre_cadre`, `nom_lettre_cadre`, `x_lettre_cadre`, `y_lettre_cadre`, `l_lettre_cadre`, `h_lettre_cadre`, `texte_lettre_cadre`, `encadre_lettre_cadre`, `couleurdefond_lettre_cadre`) VALUES
(1, 'adresse responsable', 100, 40, 100, 5, 'A l''attention de\r\n<civilitee_court_responsable> <nom_responsable> <prenom_responsable>\r\n<adresse_responsable>\r\n<cp_responsable> <commune_responsable>\r\n', 0, '||'),
(2, 'adresse etablissement', 0, 0, 0, 0, '', 0, ''),
(3, 'datation', 0, 0, 0, 0, '', 0, ''),
(4, 'corp avertissement', 10, 70, 0, 5, '<u>Objet: </u> <g>Avertissement</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nJe me vois dans l''obligation de donner un <b>AVERTISSEMENT</b>\r\n\r\nà <g><nom_eleve> <prenom_eleve></g> élève de la classe <g><classe_eleve></g>.\r\n\r\n\r\npour la raison suivante : <g><sujet_eleve></g>\r\n\r\n<remarque_eleve>\r\n\r\n\r\n\r\nComme le prévoit le règlement intérieur de l''établissement, il pourra être sanctionné à partir de ce jour.\r\nSanction(s) possible(s) :\r\n\r\n\r\n\r\n\r\nJe vous remercie de me renvoyer cet exemplaire après l''avoir daté et signé.\r\nVeuillez agréer <nom_civilitee_long> <nom_responsable> l''assurance de ma considération distinguée.\r\n\r\n\r\n\r\nDate et signatures des parents :	', 0, '||'),
(5, 'corp blame', 10, 70, 0, 5, '<u>Objet</u>: <g>Blâme</g>\r\n\r\n\r\n<nom_civilitee_long>\r\n\r\nJe me vois dans l''obligation de donner un BLAME \r\n\r\nà <g><nom_eleve> <prenom_eleve></g> élève de la classe <g><classe_eleve></g>.\r\n\r\nDemandé par: <g><courrier_demande_par></g>\r\n\r\npour la raison suivante: <g><raison></g>\r\n\r\n<remarque>\r\n\r\nJe vous remercie de me renvoyer cet exemplaire après l''avoir daté et signé.\r\nVeuillez agréer <g><nom_civilitee_long> <nom_responsable></g> l''assurance de ma considération distinguée.\r\n\r\n<u>Date et signatures des parents:</u>\r\n\r\n\r\n\r\n\r\n\r\nNous demandons un entretien avec la personne ayant demandé la sanction OUI / NON.\r\n(La prise de rendez-vous est à votre initiative)\r\n', 0, '||'),
(6, 'corp convocation parents', 10, 70, 0, 5, '<u>Objet</u>: <g>Convocation des parents</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nVous êtes prié de prendre contact avec le Conseiller Principal d''Education dans les plus brefs délais, au sujet de <g><nom_eleve> <prenom_eleve></g> inscrit en classe de <g><classe_eleve></g>.\r\n\r\npour le motif suivant:\r\n\r\n<remarque>\r\n\r\n\r\n\r\nSans nouvelle de votre part avant le ........................................., je serai dans l''obligation de procéder à la descolarisation de l''élève, avec les conséquences qui en résulteront, jusqu''à votre rencontre.\r\n\r\n\r\nVeuillez agréer <g><nom_civilitee_long> <nom_responsable></g> l''assurance de ma considération distinguée.', 0, '||'),
(7, 'corp exclusion', 10, 70, 0, 5, '<u>Objet: </u> <g>Sanction - Exclusion de l''établissement</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nPar la présente, je tiens à vous signaler que <nom_eleve>\r\n\r\ninscrit en classe de  <classe_eleve>\r\n\r\n\r\ns''étant rendu coupable des faits suivants : \r\n\r\n<remarque>\r\n\r\n\r\n\r\nEst exclu de l''établissement,\r\nà compter du: <b><date_debut></b> à <b><heure_debut></b>,\r\njusqu''au: <b><date_fin></b> à <b><heure_fin></b>.\r\n\r\n\r\nIl devra se présenter, au bureau de la Vie Scolaire \r\n\r\nle ....................................... à ....................................... ACCOMPAGNE DE SES PARENTS.\r\n\r\n\r\n\r\n\r\nVeuillez agréer &lt;TYPEPARENT&gt; &lt;NOMPARENT&gt; l''assurance de ma considération distinguée.', 0, '||'),
(8, 'corp demande justificatif absence', 10, 70, 0, 5, '<u>Objet: </u> <g>Demande de justificatif d''absence</g>\r\n\r\n\r\n<civilitee_long_responsable>,\r\n\r\nJ''ai le regret de vous informer que <b><nom_eleve> <prenom_eleve></b>, élève en classe de <b><classe_eleve></b> n''a pas assisté au(x) cours:\r\n\r\n<liste>\r\n\r\nJe vous prie de bien vouloir me faire connaître le motif de son absence.\r\n\r\nPour permettre un contrôle efficace des présences, toute absence d''un élève doit être justifiée par sa famille, le jour même soit par téléphone, soit par écrit, soit par fax.\r\n\r\nAvant de regagner les cours, l''élève absent devra se présenter au bureau du Conseiller Principal d''Education muni de son carnet de correspondance avec un justificatif signé des parents.\r\n\r\nVeuillez agréer <civilitee_long_responsable> <nom_responsable>, l''assurance de ma considération distinguée.\r\n                                               \r\nCPE\r\n<civilitee_long_cpe> <nom_cpe> <prenom_cpe>\r\n\r\nPrière de renvoyer, par retour du courrier, le présent avis signé des parents :\r\n\r\nMotif de l''absence : \r\n________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________\r\n\r\n\r\n\r\nDate et signatures des parents :  \r\n', 0, '||'),
(10, 'signature', 100, 180, 0, 5, '<b><courrier_signe_par_fonction></b>,\r\n<courrier_signe_par>\r\n', 0, '||');

-- --------------------------------------------------------

--
-- Structure de la table `lettres_suivis`
--

CREATE TABLE IF NOT EXISTS `lettres_suivis` (
  `id_lettre_suivi` int(11) NOT NULL auto_increment,
  `lettresuitealettren_lettre_suivi` int(11) NOT NULL,
  `quirecois_lettre_suivi` varchar(50) NOT NULL,
  `partde_lettre_suivi` varchar(200) NOT NULL,
  `partdenum_lettre_suivi` text NOT NULL,
  `quiemet_lettre_suivi` varchar(150) NOT NULL,
  `emis_date_lettre_suivi` date NOT NULL,
  `emis_heure_lettre_suivi` time NOT NULL,
  `quienvoi_lettre_suivi` varchar(150) NOT NULL,
  `envoye_date_lettre_suivi` date NOT NULL,
  `envoye_heure_lettre_suivi` time NOT NULL,
  `type_lettre_suivi` int(11) NOT NULL,
  `quireception_lettre_suivi` varchar(150) NOT NULL,
  `reponse_date_lettre_suivi` date NOT NULL,
  `reponse_remarque_lettre_suivi` varchar(250) NOT NULL,
  `statu_lettre_suivi` varchar(20) NOT NULL,
  PRIMARY KEY  (`id_lettre_suivi`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `lettres_suivis`
--


-- --------------------------------------------------------

--
-- Structure de la table `lettres_tcs`
--

CREATE TABLE IF NOT EXISTS `lettres_tcs` (
  `id_lettre_tc` int(11) NOT NULL auto_increment,
  `type_lettre_tc` int(11) NOT NULL,
  `cadre_lettre_tc` int(11) NOT NULL,
  `x_lettre_tc` float NOT NULL,
  `y_lettre_tc` float NOT NULL,
  `l_lettre_tc` float NOT NULL,
  `h_lettre_tc` float NOT NULL,
  `encadre_lettre_tc` int(1) NOT NULL,
  PRIMARY KEY  (`id_lettre_tc`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=201 ;

--
-- Contenu de la table `lettres_tcs`
--

INSERT INTO `lettres_tcs` (`id_lettre_tc`, `type_lettre_tc`, `cadre_lettre_tc`, `x_lettre_tc`, `y_lettre_tc`, `l_lettre_tc`, `h_lettre_tc`, `encadre_lettre_tc`) VALUES
(1, 3, 0, 0, 0, 0, 0, 0),
(2, 3, 0, 0, 0, 0, 0, 0),
(3, 3, 0, 0, 0, 0, 0, 0),
(4, 3, 0, 0, 0, 0, 0, 0),
(5, 3, 0, 0, 0, 0, 0, 0),
(6, 3, 0, 0, 0, 0, 0, 0),
(7, 3, 0, 0, 0, 0, 0, 0),
(8, 3, 0, 0, 0, 0, 0, 0),
(9, 3, 0, 0, 0, 0, 0, 0),
(10, 3, 0, 0, 0, 0, 0, 0),
(11, 3, 0, 0, 0, 0, 0, 0),
(12, 3, 0, 0, 0, 0, 0, 0),
(13, 3, 0, 0, 0, 0, 0, 0),
(14, 3, 0, 0, 0, 0, 0, 0),
(15, 3, 0, 0, 0, 0, 0, 0),
(16, 3, 0, 0, 0, 0, 0, 0),
(17, 3, 0, 0, 0, 0, 0, 0),
(18, 3, 0, 0, 0, 0, 0, 0),
(19, 3, 0, 0, 0, 0, 0, 0),
(20, 3, 0, 0, 0, 0, 0, 0),
(21, 3, 0, 0, 0, 0, 0, 0),
(22, 3, 0, 0, 0, 0, 0, 0),
(23, 3, 0, 0, 0, 0, 0, 0),
(24, 3, 0, 0, 0, 0, 0, 0),
(25, 3, 0, 0, 0, 0, 0, 0),
(26, 3, 0, 0, 0, 0, 0, 0),
(27, 3, 0, 0, 0, 0, 0, 0),
(28, 3, 0, 0, 0, 0, 0, 0),
(29, 3, 0, 0, 0, 0, 0, 0),
(30, 3, 0, 0, 0, 0, 0, 0),
(31, 3, 0, 0, 0, 0, 0, 0),
(32, 3, 0, 0, 0, 0, 0, 0),
(33, 3, 0, 0, 0, 0, 0, 0),
(34, 3, 0, 0, 0, 0, 0, 0),
(35, 3, 0, 0, 0, 0, 0, 0),
(36, 3, 0, 0, 0, 0, 0, 0),
(37, 3, 0, 0, 0, 0, 0, 0),
(38, 3, 0, 0, 0, 0, 0, 0),
(39, 3, 0, 0, 0, 0, 0, 0),
(40, 3, 0, 0, 0, 0, 0, 0),
(41, 3, 0, 0, 0, 0, 0, 0),
(42, 3, 0, 0, 0, 0, 0, 0),
(43, 3, 0, 0, 0, 0, 0, 0),
(44, 3, 0, 0, 0, 0, 0, 0),
(45, 3, 0, 0, 0, 0, 0, 0),
(46, 3, 0, 0, 0, 0, 0, 0),
(47, 3, 0, 0, 0, 0, 0, 0),
(48, 3, 0, 0, 0, 0, 0, 0),
(49, 3, 0, 0, 0, 0, 0, 0),
(50, 3, 0, 0, 0, 0, 0, 0),
(51, 3, 0, 0, 0, 0, 0, 0),
(52, 3, 0, 0, 0, 0, 0, 0),
(53, 3, 0, 0, 0, 0, 0, 0),
(56, 3, 1, 100, 40, 100, 5, 0),
(57, 3, 4, 10, 70, 0, 5, 0),
(58, 1, 0, 0, 0, 0, 0, 0),
(59, 1, 0, 0, 0, 0, 0, 0),
(60, 1, 0, 0, 0, 0, 0, 0),
(61, 1, 0, 0, 0, 0, 0, 0),
(62, 1, 0, 0, 0, 0, 0, 0),
(63, 1, 0, 0, 0, 0, 0, 0),
(64, 1, 0, 0, 0, 0, 0, 0),
(65, 1, 1, 100, 40, 100, 5, 0),
(66, 1, 5, 10, 70, 0, 5, 0),
(68, 2, 1, 100, 40, 100, 5, 0),
(69, 2, 6, 10, 70, 0, 5, 0),
(70, 4, 1, 100, 40, 100, 5, 0),
(71, 4, 7, 10, 70, 0, 5, 0),
(72, 6, 0, 0, 0, 0, 0, 0),
(73, 6, 0, 0, 0, 0, 0, 0),
(74, 6, 0, 0, 0, 0, 0, 0),
(75, 6, 0, 0, 0, 0, 0, 0),
(76, 6, 0, 0, 0, 0, 0, 0),
(77, 6, 0, 0, 0, 0, 0, 0),
(78, 6, 0, 0, 0, 0, 0, 0),
(79, 6, 0, 0, 0, 0, 0, 0),
(80, 6, 0, 0, 0, 0, 0, 0),
(81, 6, 0, 0, 0, 0, 0, 0),
(82, 6, 0, 0, 0, 0, 0, 0),
(83, 6, 0, 0, 0, 0, 0, 0),
(84, 6, 0, 0, 0, 0, 0, 0),
(85, 6, 0, 0, 0, 0, 0, 0),
(86, 6, 0, 0, 0, 0, 0, 0),
(87, 6, 0, 0, 0, 0, 0, 0),
(88, 6, 0, 0, 0, 0, 0, 0),
(89, 6, 1, 100, 40, 100, 5, 0),
(90, 6, 8, 10, 70, 0, 5, 0),
(91, 7, 0, 0, 0, 0, 0, 0),
(92, 7, 0, 0, 0, 0, 0, 0),
(93, 7, 0, 0, 0, 0, 0, 0),
(94, 7, 0, 0, 0, 0, 0, 0),
(95, 7, 0, 0, 0, 0, 0, 0),
(96, 7, 0, 0, 0, 0, 0, 0),
(97, 7, 0, 0, 0, 0, 0, 0),
(98, 7, 0, 0, 0, 0, 0, 0),
(99, 7, 0, 0, 0, 0, 0, 0),
(100, 7, 0, 0, 0, 0, 0, 0),
(101, 7, 0, 0, 0, 0, 0, 0),
(102, 7, 0, 0, 0, 0, 0, 0),
(103, 7, 0, 0, 0, 0, 0, 0),
(104, 7, 0, 0, 0, 0, 0, 0),
(105, 7, 0, 0, 0, 0, 0, 0),
(106, 7, 0, 0, 0, 0, 0, 0),
(107, 7, 0, 0, 0, 0, 0, 0),
(108, 7, 0, 0, 0, 0, 0, 0),
(109, 7, 0, 0, 0, 0, 0, 0),
(110, 7, 0, 0, 0, 0, 0, 0),
(111, 1, 0, 0, 0, 0, 0, 0),
(112, 1, 0, 0, 0, 0, 0, 0),
(113, 1, 0, 0, 0, 0, 0, 0),
(114, 1, 0, 0, 0, 0, 0, 0),
(115, 1, 0, 0, 0, 0, 0, 0),
(116, 1, 0, 0, 0, 0, 0, 0),
(117, 1, 0, 0, 0, 0, 0, 0),
(118, 1, 0, 0, 0, 0, 0, 0),
(119, 1, 0, 0, 0, 0, 0, 0),
(120, 1, 0, 0, 0, 0, 0, 0),
(121, 1, 0, 0, 0, 0, 0, 0),
(122, 1, 0, 0, 0, 0, 0, 0),
(123, 1, 0, 0, 0, 0, 0, 0),
(124, 1, 0, 0, 0, 0, 0, 0),
(125, 1, 0, 0, 0, 0, 0, 0),
(126, 1, 0, 0, 0, 0, 0, 0),
(127, 1, 0, 0, 0, 0, 0, 0),
(128, 1, 0, 0, 0, 0, 0, 0),
(129, 1, 0, 0, 0, 0, 0, 0),
(130, 1, 0, 0, 0, 0, 0, 0),
(131, 2, 10, 100, 180, 0, 5, 0),
(132, 6, 0, 0, 0, 0, 0, 0),
(133, 6, 0, 0, 0, 0, 0, 0),
(134, 6, 0, 0, 0, 0, 0, 0),
(135, 6, 0, 0, 0, 0, 0, 0),
(136, 6, 0, 0, 0, 0, 0, 0),
(137, 6, 0, 0, 0, 0, 0, 0),
(138, 6, 0, 0, 0, 0, 0, 0),
(139, 6, 0, 0, 0, 0, 0, 0),
(140, 6, 0, 0, 0, 0, 0, 0),
(141, 6, 0, 0, 0, 0, 0, 0),
(142, 6, 0, 0, 0, 0, 0, 0),
(143, 6, 0, 0, 0, 0, 0, 0),
(144, 6, 0, 0, 0, 0, 0, 0),
(145, 6, 0, 0, 0, 0, 0, 0),
(146, 6, 0, 0, 0, 0, 0, 0),
(147, 6, 0, 0, 0, 0, 0, 0),
(148, 6, 0, 0, 0, 0, 0, 0),
(149, 6, 0, 0, 0, 0, 0, 0),
(150, 6, 0, 0, 0, 0, 0, 0),
(151, 6, 0, 0, 0, 0, 0, 0),
(152, 6, 0, 0, 0, 0, 0, 0),
(153, 6, 0, 0, 0, 0, 0, 0),
(154, 6, 0, 0, 0, 0, 0, 0),
(155, 6, 0, 0, 0, 0, 0, 0),
(156, 6, 0, 0, 0, 0, 0, 0),
(157, 6, 0, 0, 0, 0, 0, 0),
(158, 6, 0, 0, 0, 0, 0, 0),
(159, 6, 0, 0, 0, 0, 0, 0),
(160, 6, 0, 0, 0, 0, 0, 0),
(161, 6, 0, 0, 0, 0, 0, 0),
(162, 6, 0, 0, 0, 0, 0, 0),
(163, 6, 0, 0, 0, 0, 0, 0),
(164, 6, 0, 0, 0, 0, 0, 0),
(165, 6, 0, 0, 0, 0, 0, 0),
(166, 6, 0, 0, 0, 0, 0, 0),
(167, 6, 0, 0, 0, 0, 0, 0),
(168, 6, 0, 0, 0, 0, 0, 0),
(169, 6, 0, 0, 0, 0, 0, 0),
(170, 6, 0, 0, 0, 0, 0, 0),
(171, 6, 0, 0, 0, 0, 0, 0),
(172, 6, 0, 0, 0, 0, 0, 0),
(173, 6, 0, 0, 0, 0, 0, 0),
(174, 6, 0, 0, 0, 0, 0, 0),
(175, 6, 0, 0, 0, 0, 0, 0),
(176, 6, 0, 0, 0, 0, 0, 0),
(177, 6, 0, 0, 0, 0, 0, 0),
(178, 6, 0, 0, 0, 0, 0, 0),
(179, 6, 0, 0, 0, 0, 0, 0),
(180, 6, 0, 0, 0, 0, 0, 0),
(181, 6, 0, 0, 0, 0, 0, 0),
(182, 6, 0, 0, 0, 0, 0, 0),
(183, 6, 0, 0, 0, 0, 0, 0),
(184, 6, 0, 0, 0, 0, 0, 0),
(185, 6, 0, 0, 0, 0, 0, 0),
(186, 6, 0, 0, 0, 0, 0, 0),
(187, 6, 0, 0, 0, 0, 0, 0),
(188, 6, 0, 0, 0, 0, 0, 0),
(189, 6, 0, 0, 0, 0, 0, 0),
(190, 6, 0, 0, 0, 0, 0, 0),
(191, 6, 0, 0, 0, 0, 0, 0),
(192, 6, 0, 0, 0, 0, 0, 0),
(193, 6, 0, 0, 0, 0, 0, 0),
(194, 6, 0, 0, 0, 0, 0, 0),
(195, 6, 0, 0, 0, 0, 0, 0),
(196, 6, 0, 0, 0, 0, 0, 0),
(197, 6, 0, 0, 0, 0, 0, 0),
(198, 6, 0, 0, 0, 0, 0, 0),
(199, 6, 0, 0, 0, 0, 0, 0),
(200, 6, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `lettres_types`
--

CREATE TABLE IF NOT EXISTS `lettres_types` (
  `id_lettre_type` int(11) NOT NULL auto_increment,
  `titre_lettre_type` varchar(250) NOT NULL,
  `categorie_lettre_type` varchar(250) NOT NULL,
  `reponse_lettre_type` varchar(3) NOT NULL,
  PRIMARY KEY  (`id_lettre_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `lettres_types`
--

INSERT INTO `lettres_types` (`id_lettre_type`, `titre_lettre_type`, `categorie_lettre_type`, `reponse_lettre_type`) VALUES
(1, 'blame', 'sanction', ''),
(2, 'convocation des parents', 'suivi', ''),
(3, 'avertissement', 'sanction', ''),
(4, 'exclusion', 'sanction', ''),
(5, 'certificat de scolarité', 'suivi', ''),
(6, 'demande de justificatif d''absence', 'suivi', 'oui'),
(7, 'demande de justificatif de retard', 'suivi', ''),
(8, 'rapport d''incident', 'sanction', ''),
(9, 'regime de sortie', 'suivi', ''),
(10, 'retenue', 'sanction', '');

-- --------------------------------------------------------

--
-- Structure de la table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `LOGIN` varchar(50) NOT NULL default '',
  `START` datetime NOT NULL default '0000-00-00 00:00:00',
  `SESSION_ID` varchar(64) NOT NULL default '',
  `REMOTE_ADDR` varchar(16) NOT NULL default '',
  `USER_AGENT` varchar(64) NOT NULL default '',
  `REFERER` varchar(64) NOT NULL default '',
  `AUTOCLOSE` enum('0','1','2','3','4') NOT NULL default '0',
  `END` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`SESSION_ID`,`START`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `log`
--


-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

CREATE TABLE IF NOT EXISTS `matieres` (
  `matiere` varchar(255) NOT NULL default '',
  `nom_complet` varchar(200) NOT NULL default '',
  `priority` smallint(6) NOT NULL default '0',
  `categorie_id` int(11) NOT NULL default '1',
  `matiere_aid` char(1) NOT NULL default 'n',
  `matiere_atelier` char(1) NOT NULL default 'n',
  PRIMARY KEY  (`matiere`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres`
--


-- --------------------------------------------------------

--
-- Structure de la table `matieres_appreciations`
--

CREATE TABLE IF NOT EXISTS `matieres_appreciations` (
  `login` varchar(50) NOT NULL default '',
  `id_groupe` int(11) NOT NULL default '0',
  `periode` int(11) NOT NULL default '0',
  `appreciation` text NOT NULL,
  PRIMARY KEY  (`login`,`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_appreciations`
--


-- --------------------------------------------------------

--
-- Structure de la table `matieres_appreciations_acces`
--

CREATE TABLE IF NOT EXISTS `matieres_appreciations_acces` (
  `id_classe` int(11) NOT NULL,
  `statut` varchar(255) NOT NULL,
  `periode` int(11) NOT NULL,
  `date` date NOT NULL,
  `acces` enum('y','n','date','d') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_appreciations_acces`
--


-- --------------------------------------------------------

--
-- Structure de la table `matieres_appreciations_grp`
--

CREATE TABLE IF NOT EXISTS `matieres_appreciations_grp` (
  `id_groupe` int(11) NOT NULL default '0',
  `periode` int(11) NOT NULL default '0',
  `appreciation` text NOT NULL,
  PRIMARY KEY  (`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_appreciations_grp`
--


-- --------------------------------------------------------

--
-- Structure de la table `matieres_appreciations_tempo`
--

CREATE TABLE IF NOT EXISTS `matieres_appreciations_tempo` (
  `login` varchar(50) NOT NULL default '',
  `id_groupe` int(11) NOT NULL default '0',
  `periode` int(11) NOT NULL default '0',
  `appreciation` text NOT NULL,
  PRIMARY KEY  (`login`,`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_appreciations_tempo`
--


-- --------------------------------------------------------

--
-- Structure de la table `matieres_categories`
--

CREATE TABLE IF NOT EXISTS `matieres_categories` (
  `id` int(11) NOT NULL auto_increment,
  `nom_court` varchar(255) NOT NULL default '',
  `nom_complet` varchar(255) NOT NULL default '',
  `priority` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `matieres_categories`
--

INSERT INTO `matieres_categories` (`id`, `nom_court`, `nom_complet`, `priority`) VALUES
(1, 'Autres', 'Autres', 5);

-- --------------------------------------------------------

--
-- Structure de la table `matieres_notes`
--

CREATE TABLE IF NOT EXISTS `matieres_notes` (
  `login` varchar(50) NOT NULL default '',
  `id_groupe` int(11) NOT NULL default '0',
  `periode` int(11) NOT NULL default '0',
  `note` float(10,1) default NULL,
  `statut` varchar(10) NOT NULL default '',
  `rang` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_notes`
--


-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL auto_increment,
  `texte` text NOT NULL,
  `date_debut` int(11) NOT NULL default '0',
  `date_fin` int(11) NOT NULL default '0',
  `auteur` varchar(50) NOT NULL default '',
  `destinataires` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `messages`
--


-- --------------------------------------------------------

--
-- Structure de la table `miseajour`
--

CREATE TABLE IF NOT EXISTS `miseajour` (
  `id_miseajour` int(11) NOT NULL auto_increment,
  `fichier_miseajour` varchar(250) NOT NULL,
  `emplacement_miseajour` varchar(250) NOT NULL,
  `date_miseajour` date NOT NULL,
  `heure_miseajour` time NOT NULL,
  PRIMARY KEY  (`id_miseajour`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `miseajour`
--


-- --------------------------------------------------------

--
-- Structure de la table `model_bulletin`
--

CREATE TABLE IF NOT EXISTS `model_bulletin` (
  `id_model_bulletin` int(11) NOT NULL auto_increment,
  `nom_model_bulletin` varchar(100) NOT NULL default '',
  `active_bloc_datation` decimal(4,0) NOT NULL default '0',
  `active_bloc_eleve` tinyint(4) NOT NULL default '0',
  `active_bloc_adresse_parent` tinyint(4) NOT NULL default '0',
  `active_bloc_absence` tinyint(4) NOT NULL default '0',
  `active_bloc_note_appreciation` tinyint(4) NOT NULL default '0',
  `active_bloc_avis_conseil` tinyint(4) NOT NULL default '0',
  `active_bloc_chef` tinyint(4) NOT NULL default '0',
  `active_photo` tinyint(4) NOT NULL default '0',
  `active_coef_moyenne` tinyint(4) NOT NULL default '0',
  `active_nombre_note` tinyint(4) NOT NULL default '0',
  `active_nombre_note_case` tinyint(4) NOT NULL default '0',
  `active_moyenne` tinyint(4) NOT NULL default '0',
  `active_moyenne_eleve` tinyint(4) NOT NULL default '0',
  `active_moyenne_classe` tinyint(4) NOT NULL default '0',
  `active_moyenne_min` tinyint(4) NOT NULL default '0',
  `active_moyenne_max` tinyint(4) NOT NULL default '0',
  `active_regroupement_cote` tinyint(4) NOT NULL default '0',
  `active_entete_regroupement` tinyint(4) NOT NULL default '0',
  `active_moyenne_regroupement` tinyint(4) NOT NULL default '0',
  `active_rang` tinyint(4) NOT NULL default '0',
  `active_graphique_niveau` tinyint(4) NOT NULL default '0',
  `active_appreciation` tinyint(4) NOT NULL default '0',
  `affiche_doublement` tinyint(4) NOT NULL default '0',
  `affiche_date_naissance` tinyint(4) NOT NULL default '0',
  `affiche_dp` tinyint(4) NOT NULL default '0',
  `affiche_nom_court` tinyint(4) NOT NULL default '0',
  `affiche_effectif_classe` tinyint(4) NOT NULL default '0',
  `affiche_numero_impression` tinyint(4) NOT NULL default '0',
  `caractere_utilse` varchar(20) NOT NULL default '',
  `X_parent` float NOT NULL default '0',
  `Y_parent` float NOT NULL default '0',
  `X_eleve` float NOT NULL default '0',
  `Y_eleve` float NOT NULL default '0',
  `cadre_eleve` tinyint(4) NOT NULL default '0',
  `X_datation_bul` float NOT NULL default '0',
  `Y_datation_bul` float NOT NULL default '0',
  `cadre_datation_bul` tinyint(4) NOT NULL default '0',
  `hauteur_info_categorie` float NOT NULL default '0',
  `X_note_app` float NOT NULL default '0',
  `Y_note_app` float NOT NULL default '0',
  `longeur_note_app` float NOT NULL default '0',
  `hauteur_note_app` float NOT NULL default '0',
  `largeur_coef_moyenne` float NOT NULL default '0',
  `largeur_nombre_note` float NOT NULL default '0',
  `largeur_d_une_moyenne` float NOT NULL default '0',
  `largeur_niveau` float NOT NULL default '0',
  `largeur_rang` float NOT NULL default '0',
  `X_absence` float NOT NULL default '0',
  `Y_absence` float NOT NULL default '0',
  `hauteur_entete_moyenne_general` float NOT NULL default '0',
  `X_avis_cons` float NOT NULL default '0',
  `Y_avis_cons` float NOT NULL default '0',
  `longeur_avis_cons` float NOT NULL default '0',
  `hauteur_avis_cons` float NOT NULL default '0',
  `cadre_avis_cons` tinyint(4) NOT NULL default '0',
  `X_sign_chef` float NOT NULL default '0',
  `Y_sign_chef` float NOT NULL default '0',
  `longeur_sign_chef` float NOT NULL default '0',
  `hauteur_sign_chef` float NOT NULL default '0',
  `cadre_sign_chef` tinyint(4) NOT NULL default '0',
  `affiche_filigrame` tinyint(4) NOT NULL default '0',
  `texte_filigrame` varchar(100) NOT NULL default '',
  `affiche_logo_etab` tinyint(4) NOT NULL default '0',
  `entente_mel` tinyint(4) NOT NULL default '0',
  `entente_tel` tinyint(4) NOT NULL default '0',
  `entente_fax` tinyint(4) NOT NULL default '0',
  `L_max_logo` tinyint(4) NOT NULL default '0',
  `H_max_logo` tinyint(4) NOT NULL default '0',
  `toute_moyenne_meme_col` tinyint(4) NOT NULL default '0',
  `active_reperage_eleve` tinyint(4) NOT NULL default '0',
  `couleur_reperage_eleve1` smallint(6) NOT NULL default '0',
  `couleur_reperage_eleve2` smallint(6) NOT NULL default '0',
  `couleur_reperage_eleve3` smallint(6) NOT NULL default '0',
  `couleur_categorie_entete` tinyint(4) NOT NULL default '0',
  `couleur_categorie_entete1` smallint(6) NOT NULL default '0',
  `couleur_categorie_entete2` smallint(6) NOT NULL default '0',
  `couleur_categorie_entete3` smallint(6) NOT NULL default '0',
  `couleur_categorie_cote` tinyint(4) NOT NULL default '0',
  `couleur_categorie_cote1` smallint(6) NOT NULL default '0',
  `couleur_categorie_cote2` smallint(6) NOT NULL default '0',
  `couleur_categorie_cote3` smallint(6) NOT NULL default '0',
  `couleur_moy_general` tinyint(4) NOT NULL default '0',
  `couleur_moy_general1` smallint(6) NOT NULL default '0',
  `couleur_moy_general2` smallint(6) NOT NULL default '0',
  `couleur_moy_general3` smallint(6) NOT NULL default '0',
  `titre_entete_matiere` varchar(50) NOT NULL default '',
  `titre_entete_coef` varchar(20) NOT NULL default '',
  `titre_entete_nbnote` varchar(20) NOT NULL default '',
  `titre_entete_rang` varchar(20) NOT NULL default '',
  `titre_entete_appreciation` varchar(50) NOT NULL default '',
  `active_coef_sousmoyene` tinyint(4) NOT NULL default '0',
  `arrondie_choix` float NOT NULL default '0',
  `nb_chiffre_virgule` tinyint(4) NOT NULL default '0',
  `chiffre_avec_zero` tinyint(4) NOT NULL default '0',
  `autorise_sous_matiere` tinyint(4) NOT NULL default '0',
  `affichage_haut_responsable` tinyint(4) NOT NULL default '0',
  `entete_model_bulletin` tinyint(4) NOT NULL default '0',
  `ordre_entete_model_bulletin` tinyint(4) NOT NULL default '0',
  `affiche_etab_origine` tinyint(4) NOT NULL default '0',
  `imprime_pour` tinyint(4) NOT NULL default '0',
  `largeur_matiere` float NOT NULL default '0',
  `nom_etab_gras` tinyint(4) NOT NULL,
  `taille_texte_date_edition` float NOT NULL,
  `taille_texte_matiere` float NOT NULL,
  `active_moyenne_general` tinyint(4) NOT NULL,
  `titre_bloc_avis_conseil` varchar(50) NOT NULL,
  `taille_titre_bloc_avis_conseil` float NOT NULL,
  `taille_profprincipal_bloc_avis_conseil` float NOT NULL,
  `affiche_fonction_chef` tinyint(4) NOT NULL,
  `taille_texte_fonction_chef` float NOT NULL,
  `taille_texte_identitee_chef` float NOT NULL,
  `tel_image` varchar(20) NOT NULL,
  `tel_texte` varchar(20) NOT NULL,
  `fax_image` varchar(20) NOT NULL,
  `fax_texte` varchar(20) NOT NULL,
  `courrier_image` varchar(20) NOT NULL,
  `courrier_texte` varchar(20) NOT NULL,
  `largeur_bloc_eleve` float NOT NULL,
  `hauteur_bloc_eleve` float NOT NULL,
  `largeur_bloc_adresse` float NOT NULL,
  `hauteur_bloc_adresse` float NOT NULL,
  `largeur_bloc_datation` float NOT NULL,
  `hauteur_bloc_datation` float NOT NULL,
  `taille_texte_classe` float NOT NULL,
  `type_texte_classe` varchar(1) NOT NULL,
  `taille_texte_annee` float NOT NULL,
  `type_texte_annee` varchar(1) NOT NULL,
  `taille_texte_periode` float NOT NULL,
  `type_texte_periode` varchar(1) NOT NULL,
  `taille_texte_categorie_cote` float NOT NULL,
  `taille_texte_categorie` float NOT NULL,
  `type_texte_date_datation` varchar(1) NOT NULL,
  `cadre_adresse` tinyint(4) NOT NULL,
  `centrage_logo` tinyint(4) NOT NULL default '0',
  `Y_centre_logo` float NOT NULL default '18',
  `ajout_cadre_blanc_photo` tinyint(4) NOT NULL default '0',
  `affiche_moyenne_mini_general` tinyint(4) NOT NULL default '1',
  `affiche_moyenne_maxi_general` tinyint(4) NOT NULL default '1',
  `affiche_date_edition` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id_model_bulletin`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `model_bulletin`
--

INSERT INTO `model_bulletin` (`id_model_bulletin`, `nom_model_bulletin`, `active_bloc_datation`, `active_bloc_eleve`, `active_bloc_adresse_parent`, `active_bloc_absence`, `active_bloc_note_appreciation`, `active_bloc_avis_conseil`, `active_bloc_chef`, `active_photo`, `active_coef_moyenne`, `active_nombre_note`, `active_nombre_note_case`, `active_moyenne`, `active_moyenne_eleve`, `active_moyenne_classe`, `active_moyenne_min`, `active_moyenne_max`, `active_regroupement_cote`, `active_entete_regroupement`, `active_moyenne_regroupement`, `active_rang`, `active_graphique_niveau`, `active_appreciation`, `affiche_doublement`, `affiche_date_naissance`, `affiche_dp`, `affiche_nom_court`, `affiche_effectif_classe`, `affiche_numero_impression`, `caractere_utilse`, `X_parent`, `Y_parent`, `X_eleve`, `Y_eleve`, `cadre_eleve`, `X_datation_bul`, `Y_datation_bul`, `cadre_datation_bul`, `hauteur_info_categorie`, `X_note_app`, `Y_note_app`, `longeur_note_app`, `hauteur_note_app`, `largeur_coef_moyenne`, `largeur_nombre_note`, `largeur_d_une_moyenne`, `largeur_niveau`, `largeur_rang`, `X_absence`, `Y_absence`, `hauteur_entete_moyenne_general`, `X_avis_cons`, `Y_avis_cons`, `longeur_avis_cons`, `hauteur_avis_cons`, `cadre_avis_cons`, `X_sign_chef`, `Y_sign_chef`, `longeur_sign_chef`, `hauteur_sign_chef`, `cadre_sign_chef`, `affiche_filigrame`, `texte_filigrame`, `affiche_logo_etab`, `entente_mel`, `entente_tel`, `entente_fax`, `L_max_logo`, `H_max_logo`, `toute_moyenne_meme_col`, `active_reperage_eleve`, `couleur_reperage_eleve1`, `couleur_reperage_eleve2`, `couleur_reperage_eleve3`, `couleur_categorie_entete`, `couleur_categorie_entete1`, `couleur_categorie_entete2`, `couleur_categorie_entete3`, `couleur_categorie_cote`, `couleur_categorie_cote1`, `couleur_categorie_cote2`, `couleur_categorie_cote3`, `couleur_moy_general`, `couleur_moy_general1`, `couleur_moy_general2`, `couleur_moy_general3`, `titre_entete_matiere`, `titre_entete_coef`, `titre_entete_nbnote`, `titre_entete_rang`, `titre_entete_appreciation`, `active_coef_sousmoyene`, `arrondie_choix`, `nb_chiffre_virgule`, `chiffre_avec_zero`, `autorise_sous_matiere`, `affichage_haut_responsable`, `entete_model_bulletin`, `ordre_entete_model_bulletin`, `affiche_etab_origine`, `imprime_pour`, `largeur_matiere`, `nom_etab_gras`, `taille_texte_date_edition`, `taille_texte_matiere`, `active_moyenne_general`, `titre_bloc_avis_conseil`, `taille_titre_bloc_avis_conseil`, `taille_profprincipal_bloc_avis_conseil`, `affiche_fonction_chef`, `taille_texte_fonction_chef`, `taille_texte_identitee_chef`, `tel_image`, `tel_texte`, `fax_image`, `fax_texte`, `courrier_image`, `courrier_texte`, `largeur_bloc_eleve`, `hauteur_bloc_eleve`, `largeur_bloc_adresse`, `hauteur_bloc_adresse`, `largeur_bloc_datation`, `hauteur_bloc_datation`, `taille_texte_classe`, `type_texte_classe`, `taille_texte_annee`, `type_texte_annee`, `taille_texte_periode`, `type_texte_periode`, `taille_texte_categorie_cote`, `taille_texte_categorie`, `type_texte_date_datation`, `cadre_adresse`, `centrage_logo`, `Y_centre_logo`, `ajout_cadre_blanc_photo`, `affiche_moyenne_mini_general`, `affiche_moyenne_maxi_general`, `affiche_date_edition`) VALUES
(1, 'Standard', 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 18, 5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 0, 1, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 0, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 0, 0.01, 2, 0, 1, 1, 1, 1, 0, 0, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1),
(2, 'Standard avec photo', 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 18, 5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 0, 1, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 0, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 0, 0, 2, 0, 1, 1, 1, 1, 0, 0, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1),
(3, 'Affiche tout', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 16.5, 6.5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 1, 1, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 1, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 1, 0.01, 2, 0, 1, 1, 2, 1, 1, 1, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `notanet`
--

CREATE TABLE IF NOT EXISTS `notanet` (
  `login` varchar(50) NOT NULL default '',
  `ine` text NOT NULL,
  `id_mat` tinyint(4) NOT NULL,
  `notanet_mat` varchar(255) NOT NULL,
  `matiere` varchar(50) NOT NULL,
  `note` varchar(4) NOT NULL default '',
  `note_notanet` varchar(4) NOT NULL,
  `id_classe` smallint(6) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notanet`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_app`
--

CREATE TABLE IF NOT EXISTS `notanet_app` (
  `login` varchar(50) NOT NULL,
  `id_mat` tinyint(4) NOT NULL,
  `matiere` varchar(50) NOT NULL,
  `appreciation` text NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `notanet_app`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_avis`
--

CREATE TABLE IF NOT EXISTS `notanet_avis` (
  `login` varchar(50) NOT NULL,
  `favorable` enum('O','N') NOT NULL,
  `avis` text NOT NULL,
  PRIMARY KEY  (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notanet_avis`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_corresp`
--

CREATE TABLE IF NOT EXISTS `notanet_corresp` (
  `id` int(11) NOT NULL auto_increment,
  `type_brevet` tinyint(4) NOT NULL,
  `id_mat` tinyint(4) NOT NULL,
  `notanet_mat` varchar(255) NOT NULL default '',
  `matiere` varchar(50) NOT NULL default '',
  `statut` enum('imposee','optionnelle','non dispensee dans l etablissement') NOT NULL default 'imposee',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `notanet_corresp`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_ele_type`
--

CREATE TABLE IF NOT EXISTS `notanet_ele_type` (
  `login` varchar(50) NOT NULL,
  `type_brevet` tinyint(4) NOT NULL,
  PRIMARY KEY  (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notanet_ele_type`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_socles`
--

CREATE TABLE IF NOT EXISTS `notanet_socles` (
  `login` varchar(50) NOT NULL,
  `b2i` enum('MS','ME','MN','AB') NOT NULL,
  `a2` enum('MS','ME','AB') NOT NULL,
  `lv` varchar(50) NOT NULL,
  PRIMARY KEY  (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notanet_socles`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_verrou`
--

CREATE TABLE IF NOT EXISTS `notanet_verrou` (
  `id_classe` tinyint(4) NOT NULL,
  `type_brevet` tinyint(4) NOT NULL,
  `verrouillage` char(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notanet_verrou`
--


-- --------------------------------------------------------

--
-- Structure de la table `periodes`
--

CREATE TABLE IF NOT EXISTS `periodes` (
  `nom_periode` varchar(50) NOT NULL default '',
  `num_periode` int(11) NOT NULL default '0',
  `verouiller` char(1) NOT NULL default '',
  `id_classe` int(11) NOT NULL default '0',
  `date_verrouillage` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`num_periode`,`id_classe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `periodes`
--


-- --------------------------------------------------------

--
-- Structure de la table `plugins`
--

CREATE TABLE IF NOT EXISTS `plugins` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(100) NOT NULL,
  `repertoire` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `ouvert` char(1) default 'n',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `plugins`
--


-- --------------------------------------------------------

--
-- Structure de la table `plugins_autorisations`
--

CREATE TABLE IF NOT EXISTS `plugins_autorisations` (
  `id` int(11) NOT NULL auto_increment,
  `plugin_id` int(11) NOT NULL,
  `fichier` varchar(100) NOT NULL,
  `user_statut` varchar(50) NOT NULL,
  `auth` char(1) default 'n',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `plugins_autorisations`
--


-- --------------------------------------------------------

--
-- Structure de la table `plugins_menus`
--

CREATE TABLE IF NOT EXISTS `plugins_menus` (
  `id` int(11) NOT NULL auto_increment,
  `plugin_id` int(11) NOT NULL,
  `user_statut` varchar(50) NOT NULL,
  `titre_item` varchar(255) NOT NULL,
  `lien_item` varchar(255) NOT NULL,
  `description_item` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `plugins_menus`
--


-- --------------------------------------------------------

--
-- Structure de la table `preferences`
--

CREATE TABLE IF NOT EXISTS `preferences` (
  `login` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `preferences`
--


-- --------------------------------------------------------

--
-- Structure de la table `ref_wiki`
--

CREATE TABLE IF NOT EXISTS `ref_wiki` (
  `id` int(11) NOT NULL auto_increment,
  `ref` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `ref` (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ref_wiki`
--

INSERT INTO `ref_wiki` (`id`, `ref`, `url`) VALUES
(0, 'enseignement_invisible', 'http://www.sylogix.org/wiki/gepi/Enseignement_invisible');

-- --------------------------------------------------------

--
-- Structure de la table `responsables`
--

CREATE TABLE IF NOT EXISTS `responsables` (
  `ereno` varchar(10) NOT NULL default '',
  `nom1` varchar(20) NOT NULL default '',
  `prenom1` varchar(20) NOT NULL default '',
  `adr1` varchar(100) NOT NULL default '',
  `adr1_comp` varchar(100) NOT NULL default '',
  `commune1` varchar(50) NOT NULL default '',
  `cp1` varchar(6) NOT NULL default '',
  `nom2` varchar(20) NOT NULL default '',
  `prenom2` varchar(20) NOT NULL default '',
  `adr2` varchar(100) NOT NULL default '',
  `adr2_comp` varchar(100) NOT NULL default '',
  `commune2` varchar(50) NOT NULL default '',
  `cp2` varchar(6) NOT NULL default '',
  PRIMARY KEY  (`ereno`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `responsables`
--


-- --------------------------------------------------------

--
-- Structure de la table `responsables2`
--

CREATE TABLE IF NOT EXISTS `responsables2` (
  `ele_id` varchar(10) NOT NULL,
  `pers_id` varchar(10) NOT NULL,
  `resp_legal` varchar(1) NOT NULL,
  `pers_contact` varchar(1) NOT NULL,
  KEY `pers_id` (`pers_id`),
  KEY `ele_id` (`ele_id`),
  KEY `resp_legal` (`resp_legal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `responsables2`
--


-- --------------------------------------------------------

--
-- Structure de la table `resp_adr`
--

CREATE TABLE IF NOT EXISTS `resp_adr` (
  `adr_id` varchar(10) NOT NULL,
  `adr1` varchar(100) NOT NULL,
  `adr2` varchar(100) NOT NULL,
  `adr3` varchar(100) NOT NULL,
  `adr4` varchar(100) NOT NULL,
  `cp` varchar(6) NOT NULL,
  `pays` varchar(50) NOT NULL,
  `commune` varchar(50) NOT NULL,
  PRIMARY KEY  (`adr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `resp_adr`
--


-- --------------------------------------------------------

--
-- Structure de la table `resp_pers`
--

CREATE TABLE IF NOT EXISTS `resp_pers` (
  `pers_id` varchar(10) NOT NULL,
  `login` varchar(50) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `civilite` varchar(5) NOT NULL,
  `tel_pers` varchar(255) NOT NULL,
  `tel_port` varchar(255) NOT NULL,
  `tel_prof` varchar(255) NOT NULL,
  `mel` varchar(100) NOT NULL,
  `adr_id` varchar(10) NOT NULL,
  PRIMARY KEY  (`pers_id`),
  KEY `login` (`login`),
  KEY `adr_id` (`adr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `resp_pers`
--


-- --------------------------------------------------------

--
-- Structure de la table `salle_cours`
--

CREATE TABLE IF NOT EXISTS `salle_cours` (
  `id_salle` int(3) NOT NULL auto_increment,
  `numero_salle` varchar(10) NOT NULL,
  `nom_salle` varchar(50) NOT NULL,
  PRIMARY KEY  (`id_salle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `salle_cours`
--


-- --------------------------------------------------------

--
-- Structure de la table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `NAME` varchar(255) NOT NULL default '',
  `VALUE` text NOT NULL,
  PRIMARY KEY  (`NAME`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `setting`
--

INSERT INTO `setting` (`NAME`, `VALUE`) VALUES
('version', '1.5.2'),
('versionRc', ''),
('versionBeta', ''),
('sessionMaxLength', '30'),
('Impression', '<center><p class = "grand">Gestion des Elèves Par Internet</p></center>\r\n<br />\r\n<p class = "grand">Qu''est-ce que GEPI ?</p>\r\n\r\n<p>Afin d''étudier les modalités d''informatisation des bulletins scolaires : notes et appréciations via Internet, une expérimentation (baptisée Gestion des Elèves Par Internet)a été mise en place. Cette expérimentation concerne les classes suivantes : \r\n<br />* ....\r\n<br />* ....\r\n<br />\r\n<br />\r\nCeci vous concerne car vous êtes professeur enseignant dans l''une ou l''autre de ces classes.\r\n<br />\r\n<br />\r\nA partir de la réception de ce document, vous pourrez remplir les bulletins informatisés :\r\n<span class = "norme">\r\n<UL><li>soit au lycée à partir de n''importe quel poste connecté à Internet,\r\n<li>soit chez vous si vous disposez d''une connexion Internet.\r\n</ul>\r\n</span>\r\n<p class = "grand">Comment accéder au module de saisie (notes etappréciations) :</p>\r\n<span class = "norme">\r\n<UL>\r\n    <LI>Se connecter à Internet\r\n    <LI>Lancer un navigateur (FireFox de préférence, Opera, Internet Explorer, ...)\r\n    <LI>Se connecter au site : https://adresse_du_site/gepi\r\n    <LI>Après quelques instants une page apparaît vous invitant à entrer un nom d''identifiant et un mot de passe (cesinformations figurent en haut de cette page).\r\n    <br />ATTENTION : votre mot de passe est strictement confidentiel.\r\n    <br />\r\n    <br />Une fois ces informations fournies, cliquez sur le bouton "Ok".\r\n    <LI> Après quelques instants une page d''accueil apparaît.<br />\r\nLa première fois, Gepi vous demande de changer votre mot de passe.\r\nChoisissez-en un facile à retenir, mais non trivial (évitez toute date\r\nde naissance, nom d''animal familier, prénom, etc.), et contenant\r\nlettre(s), chiffre(s), et caractère(s) non alphanumérique(s).<br />\r\nLes fois suivantes, vous arriverez directement au menu général de\r\nl''application. Pour bien prendre connaissance des possibilités de\r\nl''application, n''hésitez pas à essayer tous les liens disponibles !\r\n</ul></span>\r\n<p class = "grand">Remarque :</p>\r\n<p>GEPI est prévu pour que chaque professeur ne puisse modifier les notes ou les appréciations que dans les rubriques qui le concernent et uniquement pour ses élèves.\r\n<br />\r\nJe reste à votre disposition pour tout renseignement complémentaire.\r\n    <br />\r\n    Le proviseur adjoint\r\n</p>'),
('gepiYear', '2009/2010'),
('gepiSchoolName', 'Nom du lycée/collège'),
('gepiSchoolAdress1', 'Adresse'),
('gepiSchoolAdress2', 'Boîte postale'),
('gepiSchoolZipCode', 'Code postal'),
('gepiSchoolCity', 'Ville'),
('gepiAdminAdress', 'email.admin@example.com'),
('titlesize', '14'),
('textsize', '8'),
('cellpadding', '3'),
('cellspacing', '1'),
('largeurtableau', '800'),
('col_matiere_largeur', '150'),
('begin_bookings', '1157058000'),
('end_bookings', '1188594000'),
('max_size', '307200'),
('total_max_size', '5242880'),
('col_note_largeur', '30'),
('active_cahiers_texte', 'y'),
('active_carnets_notes', 'y'),
('active_observatoire', 'n'),
('logo_etab', 'logo.gif'),
('longmin_pwd', '5'),
('duree_conservation_logs', '365'),
('GepiRubConseilProf', 'yes'),
('GepiRubConseilScol', 'yes'),
('bull_ecart_entete', '0'),
('gepi_prof_suivi', 'professeur principal'),
('GepiProfImprBul', 'no'),
('GepiProfImprBulSettings', 'no'),
('GepiScolImprBulSettings', 'yes'),
('GepiAdminImprBulSettings', 'no'),
('GepiAccesReleveScol', 'yes'),
('GepiAccesReleveCpe', 'no'),
('GepiAccesReleveProf', 'no'),
('GepiAccesReleveProfTousEleves', 'no'),
('GepiAccesReleveProfToutesClasses', 'no'),
('GepiAccesReleveProfP', 'yes'),
('page_garde_imprime', 'no'),
('page_garde_texte', 'Madame, Monsieur<br/><br/>Veuillez trouvez ci-joint le bulletin scolaire de votre enfant. Nous vous rappelons que la journ&eacute;e <span style="font-weight: bold;">Portes ouvertes</span> du Lyc&eacute;e aura lieu samedi 20 mai entre 10 h et 17 h.<br/><br/>Veuillez agr&eacute;er, Madame, Monsieur, l''expression de mes meilleurs sentiments.<br/><br/><div style="text-align: right;">Le proviseur</div>'),
('page_garde_padding_top', '4'),
('page_garde_padding_left', '11'),
('page_garde_padding_text', '6'),
('addressblock_padding_top', '400'),
('addressblock_padding_right', '200'),
('addressblock_padding_text', '200'),
('addressblock_length', '600'),
('cnv_addressblock_dim_144', 'y'),
('p_bulletin_margin', '5'),
('bull_espace_avis', '5'),
('change_ordre_aff_matieres', 'ok'),
('disable_login', 'no'),
('bull_formule_bas', 'Bulletin à conserver précieusement. Aucun duplicata ne sera délivré. - GEPI : solution libre de gestion et de suivi des résultats scolaires.'),
('delai_devoirs', '7'),
('active_module_absence', 'y'),
('active_module_absence_professeur', 'y'),
('gepiSchoolTel', '00 00 00 00 00'),
('gepiSchoolFax', '00 00 00 00 00'),
('gepiSchoolEmail', 'ce.XXXXXXXX@ac-xxxxx.fr'),
('col_boite_largeur', '120'),
('bull_mention_doublant', 'no'),
('bull_affiche_numero', 'no'),
('nombre_tentatives_connexion', '5'),
('temps_compte_verrouille', '60'),
('bull_affiche_appreciations', 'y'),
('bull_affiche_absences', 'y'),
('bull_affiche_avis', 'y'),
('bull_affiche_aid', 'y'),
('bull_affiche_formule', 'y'),
('bull_affiche_signature', 'y'),
('l_max_aff_trombinoscopes', '120'),
('h_max_aff_trombinoscopes', '160'),
('l_max_imp_trombinoscopes', '70'),
('h_max_imp_trombinoscopes', '100'),
('active_module_msj', 'n'),
('site_msj_gepi', 'http://gepi.sylogix.net/releases/'),
('rc_module_msj', 'n'),
('beta_module_msj', 'n'),
('dossier_ftp_gepi', 'gepi'),
('bull_affiche_tel', 'n'),
('bull_affiche_fax', 'n'),
('note_autre_que_sur_20', 'F'),
('gepi_denom_boite', 'boite'),
('gepi_denom_boite_genre', 'f'),
('addressblock_font_size', '12'),
('addressblock_logo_etab_prop', '50'),
('addressblock_classe_annee', '35'),
('bull_ecart_bloc_nom', '1'),
('addressblock_debug', 'n'),
('GepiAccesReleveEleve', 'yes'),
('GepiAccesCahierTexteEleve', 'yes'),
('GepiAccesReleveParent', 'yes'),
('GepiAccesCahierTexteParent', 'yes'),
('enable_password_recovery', 'no'),
('GepiPasswordReinitProf', 'no'),
('GepiPasswordReinitScolarite', 'no'),
('GepiPasswordReinitCpe', 'no'),
('GepiPasswordReinitAdmin', 'no'),
('GepiPasswordReinitEleve', 'yes'),
('GepiPasswordReinitParent', 'yes'),
('cahier_texte_acces_public', 'no'),
('GepiAccesEquipePedaEleve', 'yes'),
('GepiAccesEquipePedaEmailEleve', 'no'),
('GepiAccesEquipePedaParent', 'yes'),
('GepiAccesEquipePedaEmailParent', 'no'),
('GepiAccesBulletinSimpleParent', 'yes'),
('GepiAccesBulletinSimpleEleve', 'yes'),
('GepiAccesGraphEleve', 'yes'),
('GepiAccesGraphParent', 'yes'),
('choix_bulletin', '2'),
('min_max_moyclas', '0'),
('bull_categ_font_size_avis', '10'),
('bull_police_avis', 'Times New Roman'),
('bull_font_style_avis', 'Normal'),
('bull_affiche_eleve_une_ligne', 'yes'),
('bull_mention_nom_court', 'yes'),
('option_modele_bulletin', '2'),
('security_alert_email_admin', 'yes'),
('security_alert_email_min_level', '2'),
('security_alert1_normal_cumulated_level', '3'),
('security_alert1_normal_email_admin', 'yes'),
('security_alert1_normal_block_user', 'no'),
('security_alert1_probation_cumulated_level', '1'),
('security_alert1_probation_email_admin', 'yes'),
('security_alert1_probation_block_user', 'no'),
('security_alert2_normal_cumulated_level', '6'),
('security_alert2_normal_email_admin', 'yes'),
('security_alert2_normal_block_user', 'yes'),
('security_alert2_probation_cumulated_level', '3'),
('security_alert2_probation_email_admin', 'yes'),
('security_alert2_probation_block_user', 'yes'),
('deverouillage_auto_periode_suivante', 'n'),
('bull_intitule_app', 'Appréciations / Conseils'),
('GepiAccesMoyennesProf', 'yes'),
('GepiAccesMoyennesProfTousEleves', 'yes'),
('GepiAccesMoyennesProfToutesClasses', 'yes'),
('GepiAccesBulletinSimpleProf', 'yes'),
('GepiAccesBulletinSimpleProfTousEleves', 'no'),
('GepiAccesBulletinSimpleProfToutesClasses', 'no'),
('gepi_stylesheet', 'style'),
('edt_calendrier_ouvert', 'y'),
('scolarite_modif_cours', 'y'),
('active_annees_anterieures', 'n'),
('active_notanet', 'n'),
('longmax_login', '10'),
('autorise_edt_tous', 'y'),
('autorise_edt_admin', 'y'),
('autorise_edt_eleve', 'n'),
('utiliserMenuBarre', 'no'),
('active_absences_parents', 'no'),
('creneau_different', 'n'),
('active_inscription', 'n'),
('active_inscription_utilisateurs', 'n'),
('mod_inscription_explication', '<p> <strong>Pr&eacute;sentation des dispositifs du Lyc&eacute;e dans les coll&egrave;ges qui organisent des rencontres avec les parents.</strong> <br />\r\n<br />\r\nChacun d&rsquo;entre vous conna&icirc;t la situation dans laquelle sont plac&eacute;s les &eacute;tablissements : </p>\r\n<ul>\r\n    <li>baisse d&eacute;mographique</li>\r\n    <li>r&eacute;gulation des moyens</li>\r\n    <li>- ... </li>\r\n</ul>\r\nCette ann&eacute;e encore nous devons &ecirc;tre pr&eacute;sents dans les r&eacute;unions organis&eacute;es au sein des coll&egrave;ges afin de pr&eacute;senter nos sp&eacute;cificit&eacute;s, notre valeur ajout&eacute;e, les &eacute;volution du projet, le label international, ... <br />\r\nsur cette feuille, vous avez la possibilit&eacute; de vous inscrire afin d''intervenir dans un ou plusieurs coll&egrave;ges selon vos convenances.'),
('mod_inscription_titre', 'Intervention dans les collèges'),
('active_ateliers', 'n'),
('GepiAccesRestrAccesAppProfP', 'no'),
('l_resize_trombinoscopes', '120'),
('h_resize_trombinoscopes', '160'),
('multisite', 'n'),
('statuts_prives', 'n'),
('mod_edt_gr', 'n'),
('use_ent', 'n'),
('rss_cdt_eleve', 'n'),
('auth_locale', 'yes'),
('auth_ldap', 'no'),
('auth_sso', 'lcs'),
('ldap_write_access', 'no'),
('may_import_user_profile', 'no'),
('statut_utilisateur_defaut', 'professeur'),
('texte_visa_cdt', 'Cahier de textes visé ce jour <br />Le Principal <br /> M. XXXXX<br />'),
('visa_cdt_inter_modif_notices_visees', 'yes'),
('denomination_eleve', 'élève'),
('denomination_eleves', 'élèves'),
('denomination_professeur', 'professeur'),
('denomination_professeurs', 'professeurs'),
('denomination_responsable', 'responsable légal'),
('denomination_responsables', 'responsables légaux'),
('delais_apres_cloture', '0'),
('active_mod_ooo', 'n'),
('use_only_cdt', 'n'),
('edt_remplir_prof', 'n'),
('active_mod_genese_classes', 'y'),
('active_mod_ects', 'n'),
('GepiAccesSaisieEctsPP', 'no'),
('GepiAccesSaisieEctsScolarite', 'yes'),
('GepiAccesEditionDocsEctsPP', 'no'),
('GepiAccesEditionDocsEctsScolarite', 'yes'),
('gepiSchoolStatut', 'public'),
('gepiSchoolAcademie', ''),
('note_autre_que_sur_referentiel', 'F'),
('referentiel_note', '20');

-- --------------------------------------------------------

--
-- Structure de la table `suivi_eleve_cpe`
--

CREATE TABLE IF NOT EXISTS `suivi_eleve_cpe` (
  `id_suivi_eleve_cpe` int(11) NOT NULL auto_increment,
  `eleve_suivi_eleve_cpe` varchar(30) NOT NULL default '',
  `parqui_suivi_eleve_cpe` varchar(150) NOT NULL,
  `date_suivi_eleve_cpe` date NOT NULL default '0000-00-00',
  `heure_suivi_eleve_cpe` time NOT NULL,
  `komenti_suivi_eleve_cpe` text NOT NULL,
  `niveau_message_suivi_eleve_cpe` varchar(1) NOT NULL,
  `action_suivi_eleve_cpe` varchar(2) NOT NULL,
  `support_suivi_eleve_cpe` tinyint(4) NOT NULL,
  `courrier_suivi_eleve_cpe` int(11) NOT NULL,
  PRIMARY KEY  (`id_suivi_eleve_cpe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `suivi_eleve_cpe`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_autres_sanctions`
--

CREATE TABLE IF NOT EXISTS `s_autres_sanctions` (
  `id` int(11) NOT NULL auto_increment,
  `id_sanction` int(11) NOT NULL,
  `id_nature` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_autres_sanctions`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_communication`
--

CREATE TABLE IF NOT EXISTS `s_communication` (
  `id_communication` int(11) NOT NULL auto_increment,
  `id_incident` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `nature` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id_communication`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_communication`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_exclusions`
--

CREATE TABLE IF NOT EXISTS `s_exclusions` (
  `id_exclusion` int(11) NOT NULL auto_increment,
  `id_sanction` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `heure_debut` varchar(20) NOT NULL,
  `date_fin` date NOT NULL,
  `heure_fin` varchar(20) NOT NULL,
  `travail` text NOT NULL,
  `lieu` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_exclusion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_exclusions`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_incidents`
--

CREATE TABLE IF NOT EXISTS `s_incidents` (
  `id_incident` int(11) NOT NULL auto_increment,
  `declarant` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `heure` varchar(20) NOT NULL,
  `id_lieu` int(11) NOT NULL,
  `nature` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `etat` varchar(20) NOT NULL,
  PRIMARY KEY  (`id_incident`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_incidents`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_lieux_incidents`
--

CREATE TABLE IF NOT EXISTS `s_lieux_incidents` (
  `id` int(11) NOT NULL auto_increment,
  `lieu` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `s_lieux_incidents`
--

INSERT INTO `s_lieux_incidents` (`id`, `lieu`) VALUES
(1, 'Classe'),
(2, 'Couloir'),
(3, 'Cour'),
(4, 'Réfectoire'),
(5, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `s_mesures`
--

CREATE TABLE IF NOT EXISTS `s_mesures` (
  `id` int(11) NOT NULL auto_increment,
  `type` enum('prise','demandee') default NULL,
  `mesure` varchar(50) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `s_mesures`
--

INSERT INTO `s_mesures` (`id`, `type`, `mesure`, `commentaire`) VALUES
(1, 'prise', 'Travail supplémentaire', ''),
(2, 'prise', 'Mot dans le carnet de liaison', ''),
(3, 'demandee', 'Retenue', ''),
(4, 'demandee', 'Exclusion', '');

-- --------------------------------------------------------

--
-- Structure de la table `s_protagonistes`
--

CREATE TABLE IF NOT EXISTS `s_protagonistes` (
  `id` int(11) NOT NULL auto_increment,
  `id_incident` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `qualite` varchar(50) NOT NULL,
  `avertie` enum('N','O') NOT NULL default 'N',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_protagonistes`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_qualites`
--

CREATE TABLE IF NOT EXISTS `s_qualites` (
  `id` int(11) NOT NULL auto_increment,
  `qualite` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `s_qualites`
--

INSERT INTO `s_qualites` (`id`, `qualite`) VALUES
(1, 'Responsable'),
(2, 'Victime'),
(3, 'Témoin'),
(4, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `s_retenues`
--

CREATE TABLE IF NOT EXISTS `s_retenues` (
  `id_retenue` int(11) NOT NULL auto_increment,
  `id_sanction` int(11) NOT NULL,
  `date` date NOT NULL,
  `heure_debut` varchar(20) NOT NULL,
  `duree` float NOT NULL,
  `travail` text NOT NULL,
  `lieu` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_retenue`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_retenues`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_sanctions`
--

CREATE TABLE IF NOT EXISTS `s_sanctions` (
  `id_sanction` int(11) NOT NULL auto_increment,
  `login` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `nature` varchar(255) NOT NULL,
  `effectuee` enum('N','O') NOT NULL,
  `id_incident` int(11) NOT NULL,
  PRIMARY KEY  (`id_sanction`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_sanctions`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_traitement_incident`
--

CREATE TABLE IF NOT EXISTS `s_traitement_incident` (
  `id` int(11) NOT NULL auto_increment,
  `id_incident` int(11) NOT NULL,
  `login_ele` varchar(50) NOT NULL,
  `login_u` varchar(50) NOT NULL,
  `id_mesure` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_traitement_incident`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_travail`
--

CREATE TABLE IF NOT EXISTS `s_travail` (
  `id_travail` int(11) NOT NULL auto_increment,
  `id_sanction` int(11) NOT NULL,
  `date_retour` date NOT NULL,
  `heure_retour` varchar(20) NOT NULL,
  `travail` text NOT NULL,
  PRIMARY KEY  (`id_travail`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_travail`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_types_sanctions`
--

CREATE TABLE IF NOT EXISTS `s_types_sanctions` (
  `id_nature` int(11) NOT NULL auto_increment,
  `nature` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_nature`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `s_types_sanctions`
--

INSERT INTO `s_types_sanctions` (`id_nature`, `nature`) VALUES
(1, 'Avertissement travail'),
(2, 'Avertissement comportement');

-- --------------------------------------------------------

--
-- Structure de la table `tempo`
--

CREATE TABLE IF NOT EXISTS `tempo` (
  `id_classe` int(11) NOT NULL default '0',
  `max_periode` int(11) NOT NULL default '0',
  `num` char(32) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `tempo`
--


-- --------------------------------------------------------

--
-- Structure de la table `tempo2`
--

CREATE TABLE IF NOT EXISTS `tempo2` (
  `col1` varchar(100) NOT NULL default '',
  `col2` varchar(100) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `tempo2`
--


-- --------------------------------------------------------

--
-- Structure de la table `temp_gep_import`
--

CREATE TABLE IF NOT EXISTS `temp_gep_import` (
  `ID_TEMPO` varchar(40) NOT NULL default '',
  `LOGIN` varchar(40) NOT NULL default '',
  `ELENOM` varchar(40) NOT NULL default '',
  `ELEPRE` varchar(40) NOT NULL default '',
  `ELESEXE` varchar(40) NOT NULL default '',
  `ELEDATNAIS` varchar(40) NOT NULL default '',
  `ELENOET` varchar(40) NOT NULL default '',
  `ERENO` varchar(40) NOT NULL default '',
  `ELEDOUBL` varchar(40) NOT NULL default '',
  `ELENONAT` varchar(40) NOT NULL default '',
  `ELEREG` varchar(40) NOT NULL default '',
  `DIVCOD` varchar(40) NOT NULL default '',
  `ETOCOD_EP` varchar(40) NOT NULL default '',
  `ELEOPT1` varchar(40) NOT NULL default '',
  `ELEOPT2` varchar(40) NOT NULL default '',
  `ELEOPT3` varchar(40) NOT NULL default '',
  `ELEOPT4` varchar(40) NOT NULL default '',
  `ELEOPT5` varchar(40) NOT NULL default '',
  `ELEOPT6` varchar(40) NOT NULL default '',
  `ELEOPT7` varchar(40) NOT NULL default '',
  `ELEOPT8` varchar(40) NOT NULL default '',
  `ELEOPT9` varchar(40) NOT NULL default '',
  `ELEOPT10` varchar(40) NOT NULL default '',
  `ELEOPT11` varchar(40) NOT NULL default '',
  `ELEOPT12` varchar(40) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `temp_gep_import`
--


-- --------------------------------------------------------

--
-- Structure de la table `tentatives_intrusion`
--

CREATE TABLE IF NOT EXISTS `tentatives_intrusion` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(255) NOT NULL default '',
  `adresse_ip` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `niveau` smallint(6) NOT NULL,
  `fichier` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `statut` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`,`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `tentatives_intrusion`
--


-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `login` varchar(50) NOT NULL default '',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `civilite` varchar(5) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `show_email` varchar(3) NOT NULL default 'no',
  `statut` varchar(20) NOT NULL default '',
  `etat` varchar(20) NOT NULL default '',
  `change_mdp` char(1) NOT NULL default 'n',
  `date_verrouillage` datetime NOT NULL default '2006-01-01 00:00:00',
  `password_ticket` varchar(255) NOT NULL,
  `ticket_expiration` datetime NOT NULL,
  `niveau_alerte` smallint(6) NOT NULL default '0',
  `observation_securite` tinyint(4) NOT NULL default '0',
  `temp_dir` varchar(255) NOT NULL,
  `numind` varchar(255) NOT NULL,
  `auth_mode` enum('gepi','ldap','sso') NOT NULL default 'gepi',
  PRIMARY KEY  (`login`),
  KEY `statut` (`statut`),
  KEY `etat` (`etat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`login`, `nom`, `prenom`, `civilite`, `password`, `email`, `show_email`, `statut`, `etat`, `change_mdp`, `date_verrouillage`, `password_ticket`, `ticket_expiration`, `niveau_alerte`, `observation_securite`, `temp_dir`, `numind`, `auth_mode`) VALUES
('admin', '', '', 'M.', '', '', 'no', 'administrateur', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, '', '', 'sso');

-- --------------------------------------------------------

--
-- Structure de la table `vs_alerts_eleves`
--

CREATE TABLE IF NOT EXISTS `vs_alerts_eleves` (
  `id_alert_eleve` int(11) NOT NULL auto_increment,
  `eleve_alert_eleve` varchar(100) NOT NULL,
  `date_alert_eleve` date NOT NULL,
  `groupe_alert_eleve` int(11) NOT NULL,
  `type_alert_eleve` int(11) NOT NULL,
  `nb_trouve` int(11) NOT NULL,
  `temp_insert` varchar(100) NOT NULL,
  `etat_alert_eleve` tinyint(4) NOT NULL,
  `etatpar_alert_eleve` varchar(100) NOT NULL,
  PRIMARY KEY  (`id_alert_eleve`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `vs_alerts_eleves`
--


-- --------------------------------------------------------

--
-- Structure de la table `vs_alerts_groupes`
--

CREATE TABLE IF NOT EXISTS `vs_alerts_groupes` (
  `id_alert_groupe` int(11) NOT NULL auto_increment,
  `nom_alert_groupe` varchar(150) NOT NULL,
  `creerpar_alert_groupe` varchar(100) NOT NULL,
  PRIMARY KEY  (`id_alert_groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `vs_alerts_groupes`
--


-- --------------------------------------------------------

--
-- Structure de la table `vs_alerts_types`
--

CREATE TABLE IF NOT EXISTS `vs_alerts_types` (
  `id_alert_type` int(11) NOT NULL auto_increment,
  `groupe_alert_type` int(11) NOT NULL,
  `type_alert_type` varchar(10) NOT NULL,
  `specifisite_alert_type` varchar(25) NOT NULL,
  `eleve_concerne` text NOT NULL,
  `date_debut_comptage` date NOT NULL,
  `nb_comptage_limit` varchar(200) NOT NULL,
  PRIMARY KEY  (`id_alert_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `vs_alerts_types`
--

GRANT SELECT , INSERT , UPDATE , DELETE , CREATE , DROP , INDEX , ALTER , CREATE TEMPORARY TABLES ON gepi_plug.* TO gepi_user@localhost IDENTIFIED BY '#PASS#';
