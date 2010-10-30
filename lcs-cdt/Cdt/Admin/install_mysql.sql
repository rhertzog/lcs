-- phpMyAdmin SQL Dump
-- version 3.3.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- G�n�r� le : Ven 04 Juin 2010 � 15:08
-- Version du serveur: 5.0.32
-- Version de PHP: 5.2.0-8+etch16

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de donn�es: `cdt_plug`
--

-- --------------------------------------------------------
CREATE DATABASE `cdt_plug` ;
USE `cdt_plug`;
--
-- Structure de la table `absences`
--

CREATE TABLE IF NOT EXISTS `absences` (
  `id_abs` int(11) NOT NULL auto_increment,
  `date` date NOT NULL,
  `uidprof` varchar(30) NOT NULL,
  `uideleve` varchar(30) NOT NULL,
  `classe` varchar(30) NOT NULL,
  `M1` varchar(1) NOT NULL,
  `motifM1` varchar(30) default NULL,
  `M2` varchar(1) NOT NULL,
  `motifM2` varchar(30) default NULL,
  `M3` varchar(1) NOT NULL,
  `motifM3` varchar(30) default NULL,
  `M4` varchar(1) NOT NULL,
  `motifM4` varchar(30) default NULL,
  `M5` varchar(1) NOT NULL,
  `motifM5` varchar(30) default NULL,
  `S1` varchar(1) NOT NULL,
  `motifS1` varchar(30) default NULL,
  `S2` varchar(1) NOT NULL,
  `motifS2` varchar(30) default NULL,
  `S3` varchar(1) NOT NULL,
  `motifS3` varchar(30) default NULL,
  `S4` varchar(1) NOT NULL,
  `motifS4` varchar(30) default NULL,
  `S5` varchar(1) NOT NULL,
  `motifS5` varchar(30) default NULL,
  PRIMARY KEY  (`id_abs`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cahiertxt`
--

CREATE TABLE IF NOT EXISTS `cahiertxt` (
  `id_rubrique` mediumint(8) unsigned NOT NULL auto_increment,
  `id_auteur` smallint(6) NOT NULL default '0',
  `login` varchar(30) NOT NULL default '',
  `date` date NOT NULL default '0000-00-00',
  `contenu` blob NOT NULL,
  `afaire` blob,
  `datafaire` date NOT NULL default '0000-00-00',
  `on_off` tinyint(2) NOT NULL default '0',
  `datevisibi` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_rubrique`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `devoir`
--

CREATE TABLE IF NOT EXISTS `devoir` (
  `id_ds` int(11) NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `creneau` int(11) NOT NULL default '0',
  `login` varchar(30) NOT NULL default '',
  `matiere` varchar(30) NOT NULL default '',
  `sujet` varchar(30) NOT NULL default '',
  `classe` varchar(30) NOT NULL default '',
  `durée` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id_ds`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `onglets`
--

CREATE TABLE IF NOT EXISTS `onglets` (
  `id_prof` mediumint(8) unsigned NOT NULL auto_increment,
  `login` varchar(30) NOT NULL default '',
  `prof` varchar(30) NOT NULL default '',
  `classe` varchar(30) NOT NULL default '',
  `matiere` varchar(30) NOT NULL default '',
  `prefix` varchar(10) default NULL,
  `restreint` tinyint(1) NOT NULL default '0',
  `postit` blob,
  `visa` tinyint(2) NOT NULL default '0',
  `datevisa` date default '0000-00-00',
  `mod_cours` blob NOT NULL,
  `mod_afaire` blob NOT NULL,
  `edt` varchar(50) default NULL,
  PRIMARY KEY  (`id_prof`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `postit_eleve`
--

CREATE TABLE IF NOT EXISTS `postit_eleve` (
  `id` mediumint(9) NOT NULL auto_increment,
  `login` varchar(30) default NULL,
  `texte` blob,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

GRANT SELECT,UPDATE,DELETE,INSERT,CREATE,DROP ON cdt_plug.* TO cdt_user@localhost IDENTIFIED BY '#PASS#';
GRANT SELECT,UPDATE,DELETE,INSERT,CREATE,DROP ON cdt_plug.* TO admin@localhost ;

