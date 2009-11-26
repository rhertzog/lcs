-- phpMyAdmin SQL Dump
-- version 3.1.4
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 06 Novembre 2009 à 23:32
-- Version du serveur: 5.0.32
-- Version de PHP: 5.2.0-8+etch15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
USE `lcs_db`;
--

-- --------------------------------------------------------

--
-- Structure de la table `redirmail`
--

CREATE TABLE IF NOT EXISTS `redirmail` (
  `id` smallint(6) NOT NULL auto_increment,
  `faitpar` text NOT NULL,
  `pour` text NOT NULL,
  `vers` text NOT NULL,
  `copie` varchar(3) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `remote_ip` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;
