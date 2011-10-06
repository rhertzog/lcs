-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Jeu 06 Octobre 2011 à 13:40
-- Version du serveur: 5.0.51
-- Version de PHP: 5.2.17-0.dotdeb.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE grr_plug;
USE grr_plug;

-- --------------------------------------------------------

--
-- Structure de la table `grr_area`
--

CREATE TABLE IF NOT EXISTS `grr_area` (
  `id` int(11) NOT NULL auto_increment,
  `area_name` varchar(30) NOT NULL default '',
  `access` char(1) NOT NULL default '',
  `order_display` smallint(6) NOT NULL default '0',
  `ip_adr` varchar(15) NOT NULL default '',
  `morningstarts_area` smallint(6) NOT NULL default '0',
  `eveningends_area` smallint(6) NOT NULL default '0',
  `duree_max_resa_area` int(11) NOT NULL default '-1',
  `resolution_area` int(11) NOT NULL default '0',
  `eveningends_minutes_area` smallint(6) NOT NULL default '0',
  `weekstarts_area` smallint(6) NOT NULL default '0',
  `twentyfourhour_format_area` smallint(6) NOT NULL default '0',
  `calendar_default_values` char(1) NOT NULL default 'y',
  `enable_periods` char(1) NOT NULL default 'n',
  `display_days` varchar(7) NOT NULL default 'yyyyyyy',
  `id_type_par_defaut` int(11) NOT NULL default '-1',
  `duree_par_defaut_reservation_area` int(11) NOT NULL default '0',
  `max_booking` smallint(6) NOT NULL default '-1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `grr_area_periodes`
--

CREATE TABLE IF NOT EXISTS `grr_area_periodes` (
  `id_area` int(11) NOT NULL default '0',
  `num_periode` smallint(6) NOT NULL default '0',
  `nom_periode` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id_area`,`num_periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grr_calendar`
--

CREATE TABLE IF NOT EXISTS `grr_calendar` (
  `DAY` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grr_calendrier_jours_cycle`
--

CREATE TABLE IF NOT EXISTS `grr_calendrier_jours_cycle` (
  `DAY` int(11) NOT NULL default '0',
  `Jours` varchar(20) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grr_correspondance_statut`
--

CREATE TABLE IF NOT EXISTS `grr_correspondance_statut` (
  `id` int(11) NOT NULL auto_increment,
  `code_fonction` varchar(30) NOT NULL,
  `libelle_fonction` varchar(200) NOT NULL,
  `statut_grr` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `grr_entry`
--

CREATE TABLE IF NOT EXISTS `grr_entry` (
  `id` int(11) NOT NULL auto_increment,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `entry_type` int(11) NOT NULL default '0',
  `repeat_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `create_by` varchar(100) NOT NULL default '',
  `beneficiaire_ext` varchar(200) NOT NULL default '',
  `beneficiaire` varchar(100) NOT NULL default '',
  `name` varchar(80) NOT NULL default '',
  `type` char(2) NOT NULL default 'A',
  `description` text,
  `statut_entry` char(1) NOT NULL default '-',
  `option_reservation` int(11) NOT NULL default '0',
  `overload_desc` text,
  `moderate` tinyint(1) default '0',
  `jours` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idxStartTime` (`start_time`),
  KEY `idxEndTime` (`end_time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `grr_entry_moderate`
--

CREATE TABLE IF NOT EXISTS `grr_entry_moderate` (
  `id` int(11) NOT NULL auto_increment,
  `login_moderateur` varchar(40) NOT NULL default '',
  `motivation_moderation` text NOT NULL,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `entry_type` int(11) NOT NULL default '0',
  `repeat_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `create_by` varchar(100) NOT NULL default '',
  `beneficiaire_ext` varchar(200) NOT NULL default '',
  `beneficiaire` varchar(100) NOT NULL default '',
  `name` varchar(80) NOT NULL default '',
  `type` char(2) default NULL,
  `description` text,
  `statut_entry` char(1) NOT NULL default '-',
  `option_reservation` int(11) NOT NULL default '0',
  `overload_desc` text,
  `moderate` tinyint(1) default '0',
  PRIMARY KEY  (`id`),
  KEY `idxStartTime` (`start_time`),
  KEY `idxEndTime` (`end_time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `grr_j_mailuser_room`
--

CREATE TABLE IF NOT EXISTS `grr_j_mailuser_room` (
  `login` varchar(40) NOT NULL default '',
  `id_room` int(11) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_room`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grr_j_site_area`
--

CREATE TABLE IF NOT EXISTS `grr_j_site_area` (
  `id_site` int(11) NOT NULL default '0',
  `id_area` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_site`,`id_area`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grr_j_type_area`
--

CREATE TABLE IF NOT EXISTS `grr_j_type_area` (
  `id_type` int(11) NOT NULL default '0',
  `id_area` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grr_j_useradmin_area`
--

CREATE TABLE IF NOT EXISTS `grr_j_useradmin_area` (
  `login` varchar(40) NOT NULL default '',
  `id_area` int(11) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_area`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grr_j_useradmin_site`
--

CREATE TABLE IF NOT EXISTS `grr_j_useradmin_site` (
  `login` varchar(40) NOT NULL default '',
  `id_site` int(11) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_site`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grr_j_user_area`
--

CREATE TABLE IF NOT EXISTS `grr_j_user_area` (
  `login` varchar(40) NOT NULL default '',
  `id_area` int(11) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_area`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grr_j_user_room`
--

CREATE TABLE IF NOT EXISTS `grr_j_user_room` (
  `login` varchar(40) NOT NULL default '',
  `id_room` int(11) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_room`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grr_log`
--

CREATE TABLE IF NOT EXISTS `grr_log` (
  `LOGIN` varchar(40) NOT NULL default '',
  `START` datetime NOT NULL default '0000-00-00 00:00:00',
  `SESSION_ID` varchar(64) NOT NULL default '',
  `REMOTE_ADDR` varchar(16) NOT NULL default '',
  `USER_AGENT` varchar(255) NOT NULL default '',
  `REFERER` varchar(255) NOT NULL default '',
  `AUTOCLOSE` enum('0','1') NOT NULL default '0',
  `END` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`SESSION_ID`,`START`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grr_overload`
--

CREATE TABLE IF NOT EXISTS `grr_overload` (
  `id` int(11) NOT NULL auto_increment,
  `id_area` int(11) NOT NULL,
  `fieldname` varchar(25) NOT NULL default '',
  `fieldtype` varchar(25) NOT NULL default '',
  `fieldlist` text NOT NULL,
  `obligatoire` char(1) NOT NULL default 'n',
  `affichage` char(1) NOT NULL default 'n',
  `confidentiel` char(1) NOT NULL default 'n',
  `overload_mail` char(1) NOT NULL default 'n',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `grr_repeat`
--

CREATE TABLE IF NOT EXISTS `grr_repeat` (
  `id` int(11) NOT NULL auto_increment,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `rep_type` int(11) NOT NULL default '0',
  `end_date` int(11) NOT NULL default '0',
  `rep_opt` varchar(32) NOT NULL default '',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `create_by` varchar(100) NOT NULL default '',
  `beneficiaire_ext` varchar(200) NOT NULL default '',
  `beneficiaire` varchar(100) NOT NULL default '',
  `name` varchar(80) NOT NULL default '',
  `type` char(2) NOT NULL default 'A',
  `description` text,
  `rep_num_weeks` tinyint(4) default '0',
  `overload_desc` text,
  `jours` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `grr_room`
--

CREATE TABLE IF NOT EXISTS `grr_room` (
  `id` int(11) NOT NULL auto_increment,
  `area_id` int(11) NOT NULL default '0',
  `room_name` varchar(60) NOT NULL default '',
  `description` varchar(60) NOT NULL default '',
  `capacity` int(11) NOT NULL default '0',
  `max_booking` smallint(6) NOT NULL default '-1',
  `statut_room` char(1) NOT NULL default '1',
  `show_fic_room` char(1) NOT NULL default 'n',
  `picture_room` varchar(50) NOT NULL default '',
  `comment_room` text NOT NULL,
  `show_comment` char(1) NOT NULL default 'n',
  `delais_max_resa_room` smallint(6) NOT NULL default '-1',
  `delais_min_resa_room` smallint(6) NOT NULL default '0',
  `allow_action_in_past` char(1) NOT NULL default 'n',
  `dont_allow_modify` char(1) NOT NULL default 'n',
  `order_display` smallint(6) NOT NULL default '0',
  `delais_option_reservation` smallint(6) NOT NULL default '0',
  `type_affichage_reser` smallint(6) NOT NULL default '0',
  `moderate` tinyint(1) default '0',
  `qui_peut_reserver_pour` char(1) NOT NULL default '5',
  `active_ressource_empruntee` char(1) NOT NULL default 'y',
  `who_can_see` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `grr_setting`
--

CREATE TABLE IF NOT EXISTS `grr_setting` (
  `NAME` varchar(32) NOT NULL default '',
  `VALUE` text NOT NULL,
  PRIMARY KEY  (`NAME`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `grr_setting`
--

INSERT INTO `grr_setting` (`NAME`, `VALUE`) VALUES
('sessionMaxLength', '30'),
('automatic_mail', 'yes'),
('company', 'Nom de l''établissement'),
('webmaster_name', 'Webmestre de GRR'),
('webmaster_email', 'admin@mon.site.fr'),
('technical_support_email', 'support.technique@mon.site.fr'),
('grr_url', 'http://mon.site.fr/grr/'),
('disable_login', 'no'),
('begin_bookings', '1251763200'),
('end_bookings', '1314835200'),
('title_home_page', 'Gestion et Réservation de Ressources'),
('message_home_page', 'En raison du caractère personnel du contenu, ce site est soumis à des restrictions utilisateurs. Pour accéder aux outils de réservation, identifiez-vous :'),
('version', '1.9.7'),
('versionRC', ''),
('default_language', 'fr'),
('url_disconnect', ''),
('allow_users_modify_profil', '2'),
('allow_users_modify_email', '2'),
('allow_users_modify_mdp', '2'),
('maj194_champs_additionnels', '1'),
('maj195_champ_rep_type_grr_repeat', '1'),
('display_info_bulle', '1'),
('display_full_description', '1'),
('pview_new_windows', '1'),
('default_report_days', '30'),
('authentification_obli', '0'),
('use_fckeditor', '1'),
('visu_fiche_description', '0'),
('allow_search_level', '1'),
('allow_user_delete_after_begin', '0'),
('allow_gestionnaire_modify_del', '1'),
('javascript_info_disabled', '0'),
('javascript_info_admin_disabled', '0'),
('pass_leng', '6'),
('jour_debut_Jours/Cycles', '1'),
('nombre_jours_Jours/Cycles', '1'),
('UserAllRoomsMaxBooking', '-1'),
('jours_cycles_actif', 'Non'),
('area_list_format', 'list'),
('longueur_liste_ressources_max', '20'),
('grr_mail_Password', ''),
('grr_mail_method', 'mail'),
('grr_mail_smtp', ''),
('grr_mail_Bcc', 'n'),
('grr_mail_Username', ''),
('verif_reservation_auto', '0'),
('ConvertLdapUtf8toIso', 'y'),
('ActiveModeDiagnostic', 'n'),
('ldap_champ_recherche', 'uid'),
('ldap_champ_nom', 'sn'),
('ldap_champ_prenom', 'givenname'),
('ldap_champ_email', 'mail'),
('gestion_lien_aide', 'ext'),
('lien_aide', ''),
('display_short_description', '1'),
('remplissage_description_breve', '1'),
('acces_fiche_reservation', '0'),
('display_level_email', '0'),
('nb_calendar', '3'),
('maj196_qui_peut_reserver_pour', '1'),
('default_site', '-1'),
('default_room', '-1'),
('envoyer_email_avec_formulaire', 'no');

-- --------------------------------------------------------

--
-- Structure de la table `grr_site`
--

CREATE TABLE IF NOT EXISTS `grr_site` (
  `id` int(11) NOT NULL auto_increment,
  `sitecode` varchar(10) default NULL,
  `sitename` varchar(50) NOT NULL default '',
  `adresse_ligne1` varchar(38) default NULL,
  `adresse_ligne2` varchar(38) default NULL,
  `adresse_ligne3` varchar(38) default NULL,
  `cp` varchar(5) default NULL,
  `ville` varchar(50) default NULL,
  `pays` varchar(50) default NULL,
  `tel` varchar(25) default NULL,
  `fax` varchar(25) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `grr_type_area`
--

CREATE TABLE IF NOT EXISTS `grr_type_area` (
  `id` int(11) NOT NULL auto_increment,
  `type_name` varchar(30) NOT NULL default '',
  `order_display` smallint(6) NOT NULL default '0',
  `couleur` smallint(6) NOT NULL default '0',
  `type_letter` char(2) NOT NULL default '',
  `disponible` varchar(1) NOT NULL default '2',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `grr_type_area`
--

INSERT INTO `grr_type_area` (`id`, `type_name`, `order_display`, `couleur`, `type_letter`, `disponible`) VALUES
(1, 'Cours', 1, 1, 'A', '2'),
(2, 'Réunion', 2, 2, 'B', '2'),
(3, 'Stage', 3, 3, 'C', '2'),
(4, 'Devoir', 4, 4, 'D', '2'),
(5, 'Autre', 5, 5, 'E', '2');

-- --------------------------------------------------------

--
-- Structure de la table `grr_utilisateurs`
--

CREATE TABLE IF NOT EXISTS `grr_utilisateurs` (
  `login` varchar(40) NOT NULL default '',
  `nom` varchar(30) NOT NULL default '',
  `prenom` varchar(30) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `statut` varchar(30) NOT NULL default '',
  `etat` varchar(20) NOT NULL default '',
  `default_site` smallint(6) NOT NULL default '0',
  `default_area` smallint(6) NOT NULL default '0',
  `default_room` smallint(6) NOT NULL default '0',
  `default_style` varchar(50) NOT NULL default '',
  `default_list_type` varchar(50) NOT NULL default '',
  `default_language` char(3) NOT NULL default '',
  `source` varchar(10) NOT NULL default 'local',
  PRIMARY KEY  (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

GRANT SELECT,UPDATE,DELETE,INSERT, CREATE, DROP, INDEX, ALTER ON grr_plug.* TO grr_user@localhost IDENTIFIED BY '#PASS#';
FLUSH PRIVILEGES;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
