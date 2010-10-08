-- 
-- Base de données: `maint_plug`
-- 
CREATE DATABASE maint_plug;

USE maint_plug;

-- --------------------------------------------------------
-- 
-- Structure de la table `maint_task`
-- 

CREATE TABLE `maint_task` (
  `Rid` int(10) NOT NULL auto_increment,
  `Acq` tinyint(1) NOT NULL default '0',
  `Host` varchar(16) NOT NULL default '',
  `Owner` varchar(30) NOT NULL default '',
  `OwnerMail` varchar(50) NOT NULL default '',
  `Author` varchar(30) NOT NULL default '',
  `Sector` varchar(30) NOT NULL default '',
  `Building` varchar(50) NOT NULL default '',
  `Room` varchar(30) NOT NULL default '0',
  `NumComp` tinyint(4) NOT NULL default '0',
  `Mark` varchar(30) NOT NULL default '',
  `Os` varchar(15) NOT NULL default '',
  `Cat` varchar(20) NOT NULL default '',
  `Content` text NOT NULL,
  `OpenTimeStamp` int(11) NOT NULL default '0',
  `CloseTimeStamp` int(11) NOT NULL default '0',
  `TakeTimeStamp` int(11) NOT NULL default '0',
  `BoosTimeStamp` int(11) NOT NULL default '0',
  `NumBoost` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`Rid`)
) TYPE=MyISAM AUTO_INCREMENT=262 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `maint_thread`
-- 

CREATE TABLE `maint_thread` (
  `Rid` int(10) NOT NULL auto_increment,
  `TopRid` int(10) NOT NULL default '0',
  `Author` varchar(30) NOT NULL default '',
  `Content` text NOT NULL,
  `TimeStamp` int(11) NOT NULL default '0',
  `TimeLife` int(11) NOT NULL default '0',
  `Cost` varchar(10) NOT NULL default '0',
  KEY `Rid` (`Rid`)
) TYPE=MyISAM AUTO_INCREMENT=200 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `params`
-- 

CREATE TABLE `params` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  `value` varchar(100) NOT NULL default '',
  `descr` varchar(50) NOT NULL default '',
  `cat` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=81 ;

--
-- Contenu de la table params
--

INSERT INTO params VALUES (2, 'VER_PLUG', '0.2.1', 'Version du plugin', 4);
INSERT INTO params VALUES (3, 'NAME_PLUG', 'Support Info.', 'Nom de l''apllication', 4);
-- --------------------------------------------------------

-- 
-- Structure de la table `secteur`
-- 

CREATE TABLE `secteur` (
  `id` tinyint(2) NOT NULL auto_increment,
  `descr` varchar(50) NOT NULL default '',
  KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=27 ;

-- 
-- Contenu de la table `secteur`
-- 
INSERT INTO `secteur` VALUES (1, 'Général');
INSERT INTO `secteur` VALUES (2, 'Tertiaire');
INSERT INTO `secteur` VALUES (3, 'Industriel');
INSERT INTO `secteur` VALUES (4, 'Administratif');
INSERT INTO `secteur` VALUES (5, 'Autre');

-- --------------------------------------------------------

-- 
-- Structure de la table `topologie`
-- 

CREATE TABLE `topologie` (
  `id` int(4) NOT NULL auto_increment,
  `batiment` varchar(50) NOT NULL default '',
  `etage` varchar(15) NOT NULL default '',
  `salle` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=152 ;

INSERT INTO `topologie` VALUES (1, 'A', '1er', 'A11');
INSERT INTO `topologie` VALUES (2, 'A', '1er', 'A12');
INSERT INTO `topologie` VALUES (3, 'A', '1er', 'A13');
INSERT INTO `topologie` VALUES (4, 'A', '2ème', 'A21');
INSERT INTO `topologie` VALUES (5, 'A', '2ème', 'A22');
INSERT INTO `topologie` VALUES (6, 'A', '2ème', 'A23');
INSERT INTO `topologie` VALUES (7, 'A', '3ème', 'A31');
INSERT INTO `topologie` VALUES (8, 'A', '3ème', 'A32');
INSERT INTO `topologie` VALUES (9, 'A', '3ème', 'A33');
INSERT INTO `topologie` VALUES (10, 'B', '1er', 'B11');
INSERT INTO `topologie` VALUES (11, 'B', '1er', 'B12');
INSERT INTO `topologie` VALUES (12, 'B', '1er', 'B13');
INSERT INTO `topologie` VALUES (13, 'B', '2ème', 'B21');
INSERT INTO `topologie` VALUES (14, 'B', '2ème', 'B22');
INSERT INTO `topologie` VALUES (15, 'B', '2ème', 'B23');
INSERT INTO `topologie` VALUES (16, 'B', 'RDC', 'A01');
INSERT INTO `topologie` VALUES (17, 'B', 'RDC', 'A02');
INSERT INTO `topologie` VALUES (18, 'B', 'RDC', 'A03');

GRANT SELECT,UPDATE,DELETE,INSERT ON maint_plug.* TO maint_user@localhost IDENTIFIED BY '#PASS#';
