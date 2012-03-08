-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mar 22 Février 2011 à 17:26
-- Version du serveur: 5.0.51
-- Version de PHP: 5.2.6-1+lenny9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `lcs_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `ent_lcs`
--

DROP TABLE IF EXISTS `ent_lcs`;
CREATE TABLE IF NOT EXISTS `ent_lcs` (
  `id` mediumint(9) NOT NULL auto_increment,
  `id_ent` varchar(50) NOT NULL,
  `login_lcs` varchar(50) NOT NULL,
  `token` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;
