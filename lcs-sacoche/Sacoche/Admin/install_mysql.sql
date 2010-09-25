-- MySQL dump 10.11
--
-- Host: localhost    Database: sacoche_plug
-- ------------------------------------------------------
-- Server version	5.0.32-Debian_7etch12-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


CREATE DATABASE `sacoche_plug` ;
USE `sacoche_plug`;

--
-- Table structure for table `sacoche_demande`
--

DROP TABLE IF EXISTS `sacoche_demande`;
CREATE TABLE `sacoche_demande` (
  `demande_id` mediumint(8) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL,
  `matiere_id` smallint(5) unsigned NOT NULL,
  `item_id` mediumint(8) unsigned NOT NULL,
  `demande_date` date NOT NULL,
  `demande_score` tinyint(3) unsigned default NULL COMMENT 'Sert à mémoriser le score avant réévaluation pour ne pas avoir à le recalculer ; valeur null si item non évalué.',
  `demande_statut` enum('eleve','prof') collate utf8_unicode_ci NOT NULL COMMENT '''eleve'' pour une demande d''élève ; ''prof'' pour une prévision d''évaluation par le prof ; une annulation de l''élève ou du prof efface l''enregistrement',
  PRIMARY KEY  (`demande_id`),
  UNIQUE KEY `demande_key` (`user_id`,`matiere_id`,`item_id`),
  KEY `user_id` (`user_id`),
  KEY `matiere_id` (`matiere_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_demande`
--

LOCK TABLES `sacoche_demande` WRITE;
/*!40000 ALTER TABLE `sacoche_demande` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_demande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_devoir`
--

DROP TABLE IF EXISTS `sacoche_devoir`;
CREATE TABLE `sacoche_devoir` (
  `devoir_id` mediumint(8) unsigned NOT NULL auto_increment,
  `prof_id` mediumint(8) unsigned NOT NULL,
  `groupe_id` mediumint(8) unsigned NOT NULL,
  `devoir_date` date NOT NULL,
  `devoir_info` varchar(60) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`devoir_id`),
  KEY `prof_id` (`prof_id`),
  KEY `groupe_id` (`groupe_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_devoir`
--

LOCK TABLES `sacoche_devoir` WRITE;
/*!40000 ALTER TABLE `sacoche_devoir` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_devoir` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_groupe`
--

DROP TABLE IF EXISTS `sacoche_groupe`;
CREATE TABLE `sacoche_groupe` (
  `groupe_id` mediumint(8) unsigned NOT NULL auto_increment,
  `groupe_type` enum('classe','groupe','besoin','eval') collate utf8_unicode_ci NOT NULL,
  `groupe_prof_id` mediumint(8) unsigned NOT NULL COMMENT 'Id du prof dans le cas d''un groupe de type ''eval'' ; 0 sinon.',
  `groupe_ref` char(8) collate utf8_unicode_ci NOT NULL,
  `groupe_nom` varchar(20) collate utf8_unicode_ci NOT NULL,
  `niveau_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`groupe_id`),
  KEY `niveau_id` (`niveau_id`),
  KEY `groupe_type` (`groupe_type`),
  KEY `groupe_prof_id` (`groupe_prof_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_groupe`
--

LOCK TABLES `sacoche_groupe` WRITE;
/*!40000 ALTER TABLE `sacoche_groupe` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_groupe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_jointure_devoir_item`
--

DROP TABLE IF EXISTS `sacoche_jointure_devoir_item`;
CREATE TABLE `sacoche_jointure_devoir_item` (
  `devoir_id` mediumint(8) unsigned NOT NULL,
  `item_id` mediumint(8) unsigned NOT NULL,
  `jointure_ordre` tinyint(3) unsigned NOT NULL default '0',
  UNIQUE KEY `devoir_item_key` (`devoir_id`,`item_id`),
  KEY `devoir_id` (`devoir_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_jointure_devoir_item`
--

LOCK TABLES `sacoche_jointure_devoir_item` WRITE;
/*!40000 ALTER TABLE `sacoche_jointure_devoir_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_jointure_devoir_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_jointure_groupe_periode`
--

DROP TABLE IF EXISTS `sacoche_jointure_groupe_periode`;
CREATE TABLE `sacoche_jointure_groupe_periode` (
  `groupe_id` mediumint(8) unsigned NOT NULL,
  `periode_id` mediumint(8) unsigned NOT NULL,
  `jointure_date_debut` date NOT NULL,
  `jointure_date_fin` date NOT NULL,
  UNIQUE KEY `groupe_periode_key` (`groupe_id`,`periode_id`),
  KEY `groupe_id` (`groupe_id`),
  KEY `periode_id` (`periode_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_jointure_groupe_periode`
--

LOCK TABLES `sacoche_jointure_groupe_periode` WRITE;
/*!40000 ALTER TABLE `sacoche_jointure_groupe_periode` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_jointure_groupe_periode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_jointure_user_entree`
--

DROP TABLE IF EXISTS `sacoche_jointure_user_entree`;
CREATE TABLE `sacoche_jointure_user_entree` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `entree_id` smallint(5) unsigned NOT NULL,
  `validation_entree_etat` tinyint(1) NOT NULL COMMENT '1 si validation positive ; 0 si validation n�gative.',
  `validation_entree_date` date NOT NULL,
  `validation_entree_info` tinytext collate utf8_unicode_ci NOT NULL COMMENT 'Enregistrement statique du nom du validateur, conserv� les ann�es suivantes.',
  UNIQUE KEY `validation_entree_key` (`user_id`,`entree_id`),
  KEY `user_id` (`user_id`),
  KEY `entree_id` (`entree_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_jointure_user_entree`
--

LOCK TABLES `sacoche_jointure_user_entree` WRITE;
/*!40000 ALTER TABLE `sacoche_jointure_user_entree` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_jointure_user_entree` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_jointure_user_groupe`
--

DROP TABLE IF EXISTS `sacoche_jointure_user_groupe`;
CREATE TABLE `sacoche_jointure_user_groupe` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `groupe_id` mediumint(8) unsigned NOT NULL,
  `jointure_pp` tinyint(1) NOT NULL,
  UNIQUE KEY `user_groupe_key` (`user_id`,`groupe_id`),
  KEY `user_id` (`user_id`),
  KEY `groupe_id` (`groupe_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_jointure_user_groupe`
--

LOCK TABLES `sacoche_jointure_user_groupe` WRITE;
/*!40000 ALTER TABLE `sacoche_jointure_user_groupe` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_jointure_user_groupe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_jointure_user_matiere`
--

DROP TABLE IF EXISTS `sacoche_jointure_user_matiere`;
CREATE TABLE `sacoche_jointure_user_matiere` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `matiere_id` smallint(5) unsigned NOT NULL,
  `jointure_coord` tinyint(1) NOT NULL,
  UNIQUE KEY `user_matiere_key` (`user_id`,`matiere_id`),
  KEY `user_id` (`user_id`),
  KEY `matiere_id` (`matiere_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_jointure_user_matiere`
--

LOCK TABLES `sacoche_jointure_user_matiere` WRITE;
/*!40000 ALTER TABLE `sacoche_jointure_user_matiere` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_jointure_user_matiere` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_jointure_user_pilier`
--

DROP TABLE IF EXISTS `sacoche_jointure_user_pilier`;
CREATE TABLE `sacoche_jointure_user_pilier` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `pilier_id` smallint(5) unsigned NOT NULL,
  `validation_pilier_etat` tinyint(1) NOT NULL COMMENT '1 si validation positive ; 0 si validation n�gative.',
  `validation_pilier_date` date NOT NULL,
  `validation_pilier_info` tinytext collate utf8_unicode_ci NOT NULL COMMENT 'Enregistrement statique du nom du validateur, conserv� les ann�es suivantes.',
  UNIQUE KEY `validation_pilier_key` (`user_id`,`pilier_id`),
  KEY `user_id` (`user_id`),
  KEY `pilier_id` (`pilier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_jointure_user_pilier`
--

LOCK TABLES `sacoche_jointure_user_pilier` WRITE;
/*!40000 ALTER TABLE `sacoche_jointure_user_pilier` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_jointure_user_pilier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_matiere`
--

DROP TABLE IF EXISTS `sacoche_matiere`;
CREATE TABLE `sacoche_matiere` (
  `matiere_id` smallint(5) unsigned NOT NULL auto_increment,
  `matiere_partage` tinyint(1) NOT NULL default '1',
  `matiere_transversal` tinyint(1) NOT NULL default '0',
  `matiere_ref` varchar(5) collate utf8_unicode_ci NOT NULL,
  `matiere_nom` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`matiere_id`)
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_matiere`
--

LOCK TABLES `sacoche_matiere` WRITE;
/*!40000 ALTER TABLE `sacoche_matiere` DISABLE KEYS */;
INSERT INTO `sacoche_matiere` VALUES (1,1,0,'A-PLA','Arts plastiques'),(2,1,0,'AGL1','Anglais LV1'),(3,1,0,'AGL2','Anglais LV2'),(4,1,0,'ALL1','Allemand LV1'),(5,1,0,'ALL2','Allemand LV2'),(6,1,0,'DECP3','Découverte professionnelle 3h'),(7,1,0,'EDMUS','Education musicale'),(8,1,0,'EPS','Education physique et sportive'),(9,1,0,'ESP1','Espagnol LV1'),(10,1,0,'ESP2','Espagnol LV2'),(11,1,0,'FRANC','Français'),(12,1,0,'HIGEO','Histoire et géographie'),(13,1,0,'LATIN','Latin'),(14,1,0,'MATHS','Mathématiques'),(15,1,0,'PH-CH','Physique-chimie'),(16,1,0,'SVT','Sciences de la vie et de la terre'),(17,1,0,'TECHN','Technologie'),(18,1,0,'VISCO','Vie scolaire'),(19,1,0,'DECP6','Découverte professionnelle 6h'),(20,1,0,'GREC','Grec ancien'),(21,1,0,'ITA1','Italien LV1'),(22,1,0,'ITA2','Italien LV2'),(23,1,0,'EDCIV','Education civique'),(24,1,0,'IDNCH','IDD nature corps humain'),(25,1,0,'IDARH','IDD arts humanité'),(26,1,0,'IDLCI','IDD langues civilisations'),(27,1,0,'IDCTQ','IDD création techniques'),(28,1,0,'IDAUT','IDD autres'),(29,1,0,'PHILO','Philosophie'),(30,1,0,'SES','Sciences economiques et sociales'),(31,1,0,'HIART','Histoire des arts'),(32,1,0,'RUS1','Russe LV1'),(33,1,0,'RUS2','Russe LV2'),(34,1,0,'DOC','Documentation'),(35,1,0,'POR1','Portugais LV1'),(36,1,0,'POR2','Portugais LV2'),(37,1,0,'CHI1','Chinois LV1'),(38,1,0,'CHI2','Chinois LV2'),(39,1,0,'OCCR','Occitan'),(40,1,0,'VSPRO','Vie sociale et professionnelle'),(41,1,0,'G-TPR','Enseignement technologique-professionnel'),(99,1,1,'TRANS','Transversal');
/*!40000 ALTER TABLE `sacoche_matiere` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_niveau`
--

DROP TABLE IF EXISTS `sacoche_niveau`;
CREATE TABLE `sacoche_niveau` (
  `niveau_id` tinyint(3) unsigned NOT NULL auto_increment,
  `palier_id` tinyint(3) unsigned NOT NULL,
  `niveau_ordre` tinyint(3) unsigned NOT NULL,
  `niveau_ref` varchar(5) collate utf8_unicode_ci NOT NULL,
  `code_mef` char(11) collate utf8_unicode_ci NOT NULL COMMENT 'Masque à comparer avec le code_mef d''une classe (nomenclature Sconet).',
  `niveau_nom` varchar(55) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`niveau_id`),
  KEY `palier_id` (`palier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=117 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_niveau`
--

LOCK TABLES `sacoche_niveau` WRITE;
/*!40000 ALTER TABLE `sacoche_niveau` DISABLE KEYS */;
INSERT INTO `sacoche_niveau` VALUES (1,1,9,'P1','','Palier 1 (PS - CE1)'),(2,2,29,'P2','','Palier 2 (CE2 - CM2)'),(3,3,69,'P3','','Palier 3 (6e - 3e)'),(4,4,159,'P4','','Palier 4 (2nde - Tle)'),(11,0,1,'PS','0001000131.','Maternelle, petite section'),(12,0,2,'MS','0001000132.','Maternelle, moyenne section'),(13,0,3,'GS','0001000133.','Maternelle, grande section'),(14,0,4,'CP','0011000211.','Cours préparatoire'),(15,0,5,'CE1','0021000221.','Cours élémentaire 1e année'),(16,0,11,'CE2','0021000222.','Cours élémentaire 2e année'),(17,0,12,'CM1','0031000221.','Cours moyen 1e année'),(18,0,13,'CM2','0031000222.','Cours moyen 2e année'),(19,0,21,'INIT','0601000311.','Initiation'),(20,0,22,'ADAP','0611000411.','Adaptation'),(21,0,23,'CLIS','0621000511.','Classe d\'intégration scolaire'),(31,0,31,'6','100100..11.','Sixième'),(32,0,32,'5','101100..11.','Cinquième'),(33,0,33,'4','102100..11.','Quatrième'),(34,0,34,'4AS','102100..11.','Quatrième d\'aide et de soutien'),(35,0,35,'3','103100..11.','Troisième'),(36,0,41,'3I','104100..11.','Troisième d\'insertion'),(37,0,42,'REL','105100..11.','Classe / Atelier relais'),(38,0,43,'UPI','106100..11.','Unité pédagogique d\'intégration'),(41,0,51,'6S','1641000211.','Sixième SEGPA'),(42,0,52,'5S','1651000211.','Cinquième SEGPA'),(43,0,53,'4S','1661000211.','Quatrième SEGPA'),(44,0,54,'3S','167...9911.','Troisième SEGPA'),(51,0,61,'3PVP','110.....22.','Troisième préparatoire à la voie professionnelle'),(52,0,62,'CPA','112..99911.','Classe préparatoire à l\'apprentissage'),(53,0,63,'CLIPA','113..99911.','Classe d\'initiation pré-professionnelle en alternance'),(54,0,64,'FAJ','114..99911.','Formation d\'apprenti junior'),(61,0,71,'2','20010...11.','Seconde de détermination'),(62,0,81,'1S','20111...11.','Première S'),(63,0,82,'1ES','20112...11.','Première ES'),(64,0,83,'1L','20113...11.','Première L'),(65,0,91,'TS','20211...11.','Terminale S'),(66,0,92,'TES','20212...11.','Terminale ES'),(67,0,93,'TL','20213...11.','Terminale L'),(71,0,101,'2T','210.....11.','Seconde technologique / musique'),(72,0,102,'2BT','220.....11.','Seconde BT'),(73,0,111,'1ST','211.....11.','Première STI / STL / STG'),(74,0,112,'1T','213.....11.','Première technologique'),(75,0,113,'1BT','221.....11.','Première BT'),(76,0,114,'1BTA','223.....11.','Première BTA'),(77,0,115,'1ADN','231.....11.','Première d\'adaptation BTN'),(78,0,116,'1AD','232.....11.','Première d\'adaptation BT'),(79,0,121,'TST','212.....11.','Terminale STI / STL / STG'),(80,0,122,'TT','214.....11.','Terminale technologique'),(81,0,123,'TBT','222.....11.','Terminale BT'),(82,0,124,'TBTA','224.....11.','Terminale BTA'),(91,0,131,'1CAP1','240.....11.','CAP 1 an'),(92,0,132,'1CAP2','241.....21.','CAP 2 ans, 1e année'),(93,0,133,'2CAP2','241.....22.','CAP 2 ans, 2e année'),(94,0,134,'1CAP3','242.....31.','CAP 3 ans, 1e année'),(95,0,135,'2CAP3','242.....32.','CAP 3 ans, 2e année'),(96,0,136,'3CAP3','242.....33.','CAP 3 ans, 3e année'),(101,0,141,'BEP1','243.....11.','BEP 1 an'),(102,0,142,'2BEP','244.....21.','BEP 2 ans, 1e année (seconde)'),(103,0,143,'TBEP','244.....22.','BEP 2 ans, 2e année (terminale)'),(111,0,151,'1PRO1','245.....11.','Bac Pro 1 an'),(112,0,152,'1PRO2','246.....21.','Bac Pro 2 ans, 1e année'),(113,0,153,'2PRO2','246.....22.','Bac Pro 2 ans, 2e année (terminale)'),(114,0,154,'1PRO3','247.....31.','Bac Pro 3 ans, 1e année (seconde pro)'),(115,0,155,'2PRO3','247.....32.','Bac Pro 3 ans, 2e année (première pro)'),(116,0,156,'3PRO3','247.....33.','Bac Pro 3 ans, 3e année (terminale pro)');
/*!40000 ALTER TABLE `sacoche_niveau` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_parametre`
--

DROP TABLE IF EXISTS `sacoche_parametre`;
CREATE TABLE `sacoche_parametre` (
  `parametre_nom` varchar(25) collate utf8_unicode_ci NOT NULL,
  `parametre_valeur` tinytext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`parametre_nom`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_parametre`
--

LOCK TABLES `sacoche_parametre` WRITE;
/*!40000 ALTER TABLE `sacoche_parametre` DISABLE KEYS */;
INSERT INTO `sacoche_parametre` VALUES ('version_base','2010-08-06'),('sesamath_id','0'),('sesamath_uai',''),('sesamath_type_nom',''),('sesamath_key',''),('uai',''),('denomination','a compléter'),('connexion_mode','cas'),('connexion_nom','perso'),('modele_professeur','ppp.nnnnnnnn'),('modele_eleve','ppp.nnnnnnnn'),('matieres','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,99'),('niveaux','31,32,33,35'),('paliers','3'),('profil_validation_entree','directeur,professeur'),('profil_validation_pilier','directeur,profprincipal'),('eleve_options','BilanMoyenneScore,BilanPourcentageAcquis,SoclePourcentageAcquis,SocleEtatValidation'),('eleve_demandes','0'),('duree_inactivite','30'),('calcul_valeur_RR','0'),('calcul_valeur_R','33'),('calcul_valeur_V','67'),('calcul_valeur_VV','100'),('calcul_seuil_R','40'),('calcul_seuil_V','60'),('calcul_methode','geometrique'),('calcul_limite','5'),('cas_serveur_host','@hostname@'),('cas_serveur_port','8443'),('cas_serveur_root',''),('css_background-color_NA','#ff9999'),('css_background-color_VA','#ffdd33'),('css_background-color_A','#99ff99'),('css_note_style','Lomer');
/*!40000 ALTER TABLE `sacoche_parametre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_periode`
--

DROP TABLE IF EXISTS `sacoche_periode`;
CREATE TABLE `sacoche_periode` (
  `periode_id` mediumint(8) unsigned NOT NULL auto_increment,
  `periode_ordre` tinyint(3) unsigned NOT NULL default '1',
  `periode_nom` varchar(40) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`periode_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_periode`
--

LOCK TABLES `sacoche_periode` WRITE;
/*!40000 ALTER TABLE `sacoche_periode` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_periode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_referentiel`
--

DROP TABLE IF EXISTS `sacoche_referentiel`;
CREATE TABLE `sacoche_referentiel` (
  `matiere_id` smallint(5) unsigned NOT NULL,
  `niveau_id` tinyint(3) unsigned NOT NULL,
  `referentiel_partage_etat` enum('bof','non','oui','hs') collate utf8_unicode_ci NOT NULL COMMENT '''oui'' = référentiel partagé sur le serveur communautaire ; ''non'' = référentiel non partagé avec la communauté ; ''bof'' = référentiel dont le partage est sans intérêt (pas novateur) ; ''hs'' = référentiel dont le partage est sans objet (matière spécifique)',
  `referentiel_partage_date` date NOT NULL,
  `referentiel_calcul_methode` enum('geometrique','arithmetique','classique','bestof1','bestof2','bestof3') collate utf8_unicode_ci NOT NULL default 'geometrique' COMMENT 'Coefficients en progression géométrique, arithmetique, ou moyenne classique non pondérée, ou conservation des meilleurs scores. Valeur surclassant la configuration par défaut.',
  `referentiel_calcul_limite` tinyint(3) unsigned NOT NULL default '5' COMMENT 'Nombre maximum de dernières évaluations prises en comptes (0 pour les prendre toutes). Valeur surclassant la configuration par défaut.',
  UNIQUE KEY `referentiel_id` (`matiere_id`,`niveau_id`),
  KEY `matiere_id` (`matiere_id`),
  KEY `niveau_id` (`niveau_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_referentiel`
--

LOCK TABLES `sacoche_referentiel` WRITE;
/*!40000 ALTER TABLE `sacoche_referentiel` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_referentiel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_referentiel_domaine`
--

DROP TABLE IF EXISTS `sacoche_referentiel_domaine`;
CREATE TABLE `sacoche_referentiel_domaine` (
  `domaine_id` smallint(5) unsigned NOT NULL auto_increment,
  `matiere_id` smallint(5) unsigned NOT NULL,
  `niveau_id` tinyint(3) unsigned NOT NULL,
  `domaine_ordre` tinyint(3) unsigned NOT NULL COMMENT 'Commence à 1.',
  `domaine_ref` char(1) collate utf8_unicode_ci NOT NULL,
  `domaine_nom` varchar(128) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`domaine_id`),
  KEY `matiere_id` (`matiere_id`),
  KEY `niveau_id` (`niveau_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_referentiel_domaine`
--

LOCK TABLES `sacoche_referentiel_domaine` WRITE;
/*!40000 ALTER TABLE `sacoche_referentiel_domaine` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_referentiel_domaine` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_referentiel_item`
--

DROP TABLE IF EXISTS `sacoche_referentiel_item`;
CREATE TABLE `sacoche_referentiel_item` (
  `item_id` mediumint(8) unsigned NOT NULL auto_increment,
  `theme_id` smallint(5) unsigned NOT NULL,
  `entree_id` smallint(5) unsigned NOT NULL,
  `item_ordre` tinyint(3) unsigned NOT NULL COMMENT 'Commence à 0.',
  `item_nom` tinytext collate utf8_unicode_ci NOT NULL,
  `item_coef` tinyint(3) unsigned NOT NULL default '1',
  `item_cart` tinyint(1) NOT NULL default '1' COMMENT '0 pour empêcher les élèves de demander une évaluation sur cet item.',
  `item_lien` tinytext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`item_id`),
  KEY `theme_id` (`theme_id`),
  KEY `entree_id` (`entree_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_referentiel_item`
--

LOCK TABLES `sacoche_referentiel_item` WRITE;
/*!40000 ALTER TABLE `sacoche_referentiel_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_referentiel_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_referentiel_theme`
--

DROP TABLE IF EXISTS `sacoche_referentiel_theme`;
CREATE TABLE `sacoche_referentiel_theme` (
  `theme_id` smallint(5) unsigned NOT NULL auto_increment,
  `domaine_id` smallint(5) unsigned NOT NULL,
  `theme_ordre` tinyint(3) unsigned NOT NULL COMMENT 'Commence à 1.',
  `theme_nom` varchar(128) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`theme_id`),
  KEY `domaine_id` (`domaine_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_referentiel_theme`
--

LOCK TABLES `sacoche_referentiel_theme` WRITE;
/*!40000 ALTER TABLE `sacoche_referentiel_theme` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_referentiel_theme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_saisie`
--

DROP TABLE IF EXISTS `sacoche_saisie`;
CREATE TABLE `sacoche_saisie` (
  `prof_id` mediumint(8) unsigned NOT NULL,
  `eleve_id` mediumint(8) unsigned NOT NULL,
  `devoir_id` mediumint(8) unsigned NOT NULL,
  `item_id` mediumint(8) unsigned NOT NULL,
  `saisie_date` date NOT NULL,
  `saisie_note` enum('VV','V','R','RR','ABS','NN','DISP','REQ') collate utf8_unicode_ci NOT NULL,
  `saisie_info` tinytext collate utf8_unicode_ci NOT NULL COMMENT 'Enregistrement statique du nom du devoir et du professeur, conserv� les ann�es suivantes.',
  UNIQUE KEY `saisie_key` (`eleve_id`,`devoir_id`,`item_id`),
  KEY `prof_id` (`prof_id`),
  KEY `eleve_id` (`eleve_id`),
  KEY `devoir_id` (`devoir_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_saisie`
--

LOCK TABLES `sacoche_saisie` WRITE;
/*!40000 ALTER TABLE `sacoche_saisie` DISABLE KEYS */;
/*!40000 ALTER TABLE `sacoche_saisie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_socle_entree`
--

DROP TABLE IF EXISTS `sacoche_socle_entree`;
CREATE TABLE `sacoche_socle_entree` (
  `entree_id` smallint(5) unsigned NOT NULL auto_increment,
  `section_id` smallint(5) unsigned NOT NULL,
  `entree_ordre` tinyint(3) unsigned NOT NULL COMMENT 'Commence à 0.',
  `entree_nom` tinytext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`entree_id`),
  KEY `section_id` (`section_id`)
) ENGINE=MyISAM AUTO_INCREMENT=294 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_socle_entree`
--

LOCK TABLES `sacoche_socle_entree` WRITE;
/*!40000 ALTER TABLE `sacoche_socle_entree` DISABLE KEYS */;
INSERT INTO `sacoche_socle_entree` VALUES (1,1,0,'S’exprimer clairement à l’oral en utilisant un vocabulaire approprié.'),(2,1,1,'Participer en classe à un échange verbal en respectant les règles de la communication.'),(3,1,2,'Dire de mémoire quelques textes en prose ou poèmes courts.'),(4,2,0,'Lire seul, à haute voix, un texte comprenant des mots connus et inconnus.'),(5,2,1,'Lire seul et écouter lire des textes du patrimoine et des œuvres intégrales de la littérature de jeunesse adaptés à son âge.'),(6,2,2,'Lire seul et comprendre un énoncé, une consigne simple.'),(7,2,3,'Dégager le thème d’un paragraphe ou d’un texte court.'),(8,2,4,'Lire silencieusement un texte en déchiffrant les mots inconnus et manifester sa compréhension dans un résumé, une reformulation, des réponses à des questions.'),(9,3,0,'Copier un texte court sans erreur dans une écriture cursive lisible et avec une présentation soignée.'),(10,3,1,'Utiliser ses connaissances pour mieux écrire un texte court.'),(11,3,2,'Écrire de manière autonome un texte de cinq à dix lignes.'),(12,4,0,'Utiliser des mots précis pour s’exprimer.'),(13,4,1,'Donner des synonymes.'),(14,4,2,'Trouver un mot de sens opposé.'),(15,4,3,'Regrouper des mots par familles.'),(16,4,4,'Commencer à utiliser l’ordre alphabétique.'),(17,5,0,'Identifier la phrase, le verbe, le nom, l’article, l’adjectif qualificatif, le pronom personnel (sujet).'),(18,5,1,'Repérer le verbe d’une phrase et son sujet.'),(19,5,2,'Conjuguer les verbes du 1er groupe, être et avoir, au présent, au futur, au passé composé de l’indicatif ; conjuguer les verbes faire, aller, dire, venir, au présent de l’indicatif.'),(20,5,3,'Distinguer le présent du futur et du passé.'),(21,6,0,'Écrire en respectant les correspondances entre lettres et sons et les règles relatives à la valeur des lettres.'),(22,6,1,'Écrire sans erreur des mots mémorisés.'),(23,6,2,'Orthographier correctement des formes conjuguées, respecter l’accord entre le sujet et le verbe, ainsi que les accords en genre et en nombre dans le groupe nominal.'),(24,7,0,'Écrire, nommer, comparer, ranger les nombres entiers naturels inférieurs à 1000.'),(25,7,1,'Résoudre des problèmes de dénombrement.'),(26,7,2,'Calculer : addition, soustraction, multiplication.'),(27,7,3,'Diviser par 2 et par 5 dans le cas où le quotient exact est entier.'),(28,7,4,'Restituer et utiliser les tables d’addition et de multiplication par 2, 3, 4 et 5.'),(29,7,5,'Calculer mentalement en utilisant des additions, des soustractions et des multiplications simples.'),(30,7,6,'Résoudre des problèmes relevant de l’addition, de la soustraction et de la multiplication.'),(31,7,7,'Utiliser les fonctions de base de la calculatrice.'),(32,8,0,'Situer un objet par rapport à soi ou à un autre objet, donner sa position et décrire son déplacement.'),(33,8,1,'Reconnaître, nommer et décrire les figures planes et les solides usuels.'),(34,8,2,'Utiliser la règle et l’équerre pour tracer avec soin et précision un carré, un rectangle, un triangle rectangle.'),(35,8,3,'Percevoir et reconnaître quelques relations et propriétés géométriques : alignement, angle droit, axe de symétrie, égalité de longueurs.'),(36,8,4,'Repérer des cases, des nœuds d’un quadrillage.'),(37,8,5,'Résoudre un problème géométrique.'),(38,9,0,'Utiliser les unités usuelles de mesure ; estimer une mesure.'),(39,9,1,'Être précis et soigneux dans les mesures et les calculs.'),(40,9,2,'Résoudre des problèmes de longueur et de masse.'),(41,10,0,'Utiliser un tableau, un graphique.'),(42,10,1,'Organiser les données d’un énoncé.'),(43,11,0,'Reconnaître les emblèmes et les symboles de la République française.'),(44,12,0,'Respecter les autres et les règles de la vie collective.'),(45,12,1,'Pratiquer un jeu ou un sport collectif en en respectant les règles.'),(46,12,2,'Appliquer les codes de la politesse dans ses relations avec ses camarades, avec les adultes de l’école et hors de l’école, avec le maître au sein de la classe.'),(47,13,0,'S’exprimer à l’oral comme à l’écrit dans un vocabulaire approprié et précis.'),(48,13,1,'Prendre la parole en respectant le niveau de langue adapté.'),(49,13,2,'Répondre à une question par une phrase complète à l’oral.'),(50,13,3,'Prendre part à un dialogue : prendre la parole devant les autres, écouter autrui, formuler et justifier un point de vue.'),(51,13,4,'Dire de mémoire, de façon expressive, une dizaine de poèmes et de textes en prose.'),(52,14,0,'Lire avec aisance (à haute voix, silencieusement) un texte.'),(53,14,1,'Lire seul des textes du patrimoine et des œuvres intégrales de la littérature de jeunesse, adaptés à son âge.'),(54,14,2,'Lire seul et comprendre un énoncé, une consigne.'),(55,14,3,'Dégager le thème d’un texte.'),(56,14,4,'Repérer dans un texte des informations explicites.'),(57,14,5,'Inférer des informations nouvelles (implicites).'),(58,14,6,'Repérer les effets de choix formels (emploi de certains mots, utilisation d’un niveau de langue).'),(59,14,7,'Utiliser ses connaissances pour réfléchir sur un texte, mieux le comprendre.'),(60,14,8,'Effectuer, seul, des recherches dans des ouvrages documentaires (livres, produits multimédia).'),(61,14,9,'Se repérer dans une bibliothèque, une médiathèque.'),(62,15,0,'Copier sans erreur un texte d’au moins quinze lignes en lui donnant une présentation adaptée.'),(63,15,1,'Utiliser ses connaissances pour réfléchir sur un texte, mieux l’écrire.'),(64,15,2,'Répondre à une question par une phrase complète à l’écrit.'),(65,15,3,'Rédiger un texte d’une quinzaine de lignes (récit, description, dialogue, texte poétique, compte rendu) en utilisant ses connaissances en vocabulaire et en grammaire.'),(66,16,0,'Comprendre des mots nouveaux et les utiliser à bon escient.'),(67,16,1,'Maîtriser quelques relations de sens entre les mots.'),(68,16,2,'Maîtriser quelques relations concernant la forme et le sens des mots.'),(69,16,3,'Savoir utiliser un dictionnaire papier ou numérique.'),(70,17,0,'Distinguer les mots selon leur nature.'),(71,17,1,'Identifier les fonctions des mots dans la phrase.'),(72,17,2,'Conjuguer les verbes, utiliser les temps à bon escient.'),(73,18,0,'Maîtriser l’orthographe grammaticale.'),(74,18,1,'Maîtriser l’orthographe lexicale.'),(75,18,2,'Orthographier correctement un texte simple de dix lignes – lors de sa rédaction ou de sa dictée – en se référant aux règles connues d’orthographe et de grammaire ainsi qu’à la connaissance du vocabulaire.'),(76,19,0,'Communiquer, au besoin avec des pauses pour chercher ses mots.'),(77,19,1,'Se présenter ; présenter quelqu’un ; demander à quelqu’un de ses nouvelles en utilisant les formes de politesse les plus élémentaires ; accueil et prise de congé.'),(78,19,2,'Répondre à des questions et en poser (sujets familiers ou besoins immédiats).'),(79,19,3,'Épeler des mots familiers.'),(80,20,0,'Comprendre les consignes de classe.'),(81,20,1,'Comprendre des mots familiers et des expressions très courantes.'),(82,20,2,'Suivre des instructions courtes et simples.'),(83,21,0,'Reproduire un modèle oral.'),(84,21,1,'Utiliser des expressions et des phrases proches des modèles rencontrés lors des apprentissages.'),(85,21,2,'Lire à haute voix et de manière expressive un texte bref après répétition.'),(86,22,0,'Comprendre des textes courts et simples en s’appuyant sur des éléments connus (indications, informations).'),(87,22,1,'Se faire une idée du contenu d’un texte informatif simple, accompagné éventuellement d’un document visuel.'),(88,23,0,'Copier des mots isolés et des textes courts.'),(89,23,1,'Écrire un message électronique simple ou une courte carte postale en référence à des modèles.'),(90,23,2,'Renseigner un questionnaire.'),(91,23,3,'Produire de manière autonome quelques phrases.'),(92,23,4,'Écrire sous la dictée des expressions connues.'),(93,24,0,'Écrire, nommer, comparer et utiliser les nombres entiers, les nombres décimaux (jusqu’au centième) et quelques fractions simples.'),(94,24,1,'Restituer les tables d’addition et de multiplication de 2 à 9.'),(95,24,2,'Utiliser les techniques opératoires des quatre opérations sur les nombres entiers et décimaux (pour la division, le diviseur est un nombre entier).'),(96,24,3,'Ajouter deux fractions décimales ou deux fractions simples de même dénominateur.'),(97,24,4,'Calculer mentalement en utilisant les quatre opérations.'),(98,24,5,'Estimer l’ordre de grandeur d’un résultat.'),(99,24,6,'Résoudre des problèmes relevant des quatre opérations.'),(100,24,7,'Utiliser une calculatrice.'),(101,25,0,'Reconnaître, décrire et nommer les figures et solides usuels.'),(102,25,1,'Utiliser la règle, l’équerre et le compas pour vérifier la nature de figures planes usuelles et les construire avec soin et précision.'),(103,25,2,'Percevoir et reconnaitre parallèles et perpendiculaires.'),(104,25,3,'Résoudre des problèmes de reproduction, de construction.'),(105,26,0,'Utiliser des instruments de mesure.'),(106,26,1,'Connaître et utiliser les formules du périmètre et de l’aire d’un carré, d’un rectangle et d’un triangle.'),(107,26,2,'Utiliser les unités de mesures usuelles.'),(108,26,3,'Résoudre des problèmes dont la résolution implique des conversions.'),(109,27,0,'Lire, interpréter et construire quelques représentations simples : tableaux, graphiques.'),(110,27,1,'Savoir organiser des informations numériques ou géométriques, justifier et apprécier la vraisemblance d’un résultat.'),(111,27,2,'Résoudre un problème mettant en jeu une situation de proportionnalité.'),(112,28,0,'Pratiquer une démarche d’investigation : savoir observer, questionner.'),(113,28,1,'Manipuler et expérimenter, formuler une hypothèse et la tester, argumenter, mettre à l’essai plusieurs pistes de solutions.'),(114,28,2,'Exprimer et exploiter les résultats d’une mesure et d’une recherche en utilisant un vocabulaire scientifique à l’écrit ou à l’oral.'),(115,29,0,'Le ciel et la Terre.'),(116,29,1,'La matière.'),(117,29,2,'L’énergie.'),(118,29,3,'L’unité et la diversité du vivant.'),(119,29,4,'Le fonctionnement du vivant.'),(120,29,5,'Le fonctionnement du corps humain et la santé.'),(121,29,6,'Les êtres vivants dans leur environnement.'),(122,29,7,'Les objets techniques.'),(123,30,0,'Mobiliser ses connaissances pour comprendre quelques questions liées à l’environnement et au développement durable et agir en conséquence.'),(124,31,0,'Connaitre et maîtriser les fonctions de base d’un ordinateur et de ses périphériques.'),(125,32,0,'Prendre conscience des enjeux citoyens de l’usage de l’informatique et de l’internet et adopter une attitude critique face aux résultats obtenus.'),(126,33,0,'Produire un document numérique : texte, image, son.'),(127,33,1,'Utiliser l’outil informatique pour présenter un travail.'),(128,34,0,'Lire un document numérique.'),(129,34,1,'Chercher des informations par voie électronique.'),(130,34,2,'Découvrir les richesses et les limites des ressources de l’internet.'),(131,35,0,'Échanger avec les technologies de l’information et de la communication.'),(133,36,1,'Identifier les périodes de l’histoire au programme.'),(134,36,2,'Connaître et mémoriser les principaux repères chronologiques (évènements et personnages).'),(135,36,3,'Connaître les principaux caractères géographiques physiques et humains de la région où vit l’élève, de la France et de l’Union européenne, les repérer sur des cartes à différentes échelles.'),(136,36,4,'Comprendre une ou deux questions liées au développement durable et agir en conséquence (l’eau dans la commune, la réduction et le recyclage des déchets).'),(137,37,0,'Lire des œuvres majeures du patrimoine et de la littérature pour la jeunesse.'),(138,37,1,'Établir des liens entre les textes lus.'),(132,73,0,'Lire et utiliser textes, cartes, croquis, graphiques.'),(139,38,0,'Distinguer les grandes catégories de la création artistique (littérature, musique, danse, théâtre, cinéma, dessin, peinture, sculpture, architecture).'),(140,38,1,'Reconnaître et décrire des œuvres préalablement étudiées.'),(141,38,2,'Pratiquer le dessin et diverses formes d’expressions visuelles et plastiques.'),(142,38,3,'Interpréter de mémoire une chanson, participer à un jeu rythmique ; repérer des éléments musicaux caractéristiques simples.'),(143,38,4,'Inventer et réaliser des textes, des œuvres plastiques, des chorégraphies ou des enchaînements, à visée artistique ou expressive.'),(144,39,0,'Reconnaître les symboles de la République et de l’Union européenne.'),(145,39,1,'Comprendre les notions de droits et de devoirs, les accepter et les mettre en application.'),(146,39,2,'Avoir conscience de la dignité de la personne humaine et en tirer les conséquences au quotidien.'),(147,40,0,'Respecter les règles de la vie collective.'),(148,40,1,'Respecter tous les autres, et notamment appliquer les principes de l’égalité des filles et des garçons.'),(149,41,0,'Respecter des consignes simples, en autonomie.'),(150,41,1,'Être persévérant dans toutes les activités.'),(151,41,2,'Commencer à savoir s’autoévaluer dans des situations simples.'),(152,41,3,'Soutenir une écoute prolongée (lecture, musique, spectacle, etc.).'),(153,42,0,'S’impliquer dans un projet individuel ou collectif.'),(154,43,0,'Se respecter en respectant les principales règles d’hygiène de vie ; accomplir les gestes quotidiens sans risquer de se faire mal.'),(155,43,1,'Réaliser une performance mesurée dans les activités athlétiques et en natation.'),(156,43,2,'Se déplacer en s’adaptant à l’environnement.'),(157,44,0,'Adapter son mode de lecture à la nature du texte proposé et à l’objectif poursuivi.'),(282,44,1,'Repérer les informations dans un texte à partir des éléments explicites et des éléments implicites nécessaires.'),(158,44,2,'Utiliser ses capacités de raisonnement, ses connaissances sur la langue, savoir faire appel à des outils appropriés pour lire.'),(159,44,3,'Dégager, par écrit ou oralement, l’essentiel d’un texte lu.'),(160,44,4,'Manifester, par des moyens divers, sa compréhension de textes variés.'),(163,45,0,'Reproduire un document sans erreur et avec une présentation adaptée.'),(164,45,1,'Écrire lisiblement un texte, spontanément ou sous la dictée, en respectant l’orthographe et la grammaire.'),(166,45,2,'Rédiger un texte bref, cohérent et ponctué, en réponse à une question ou à partir de consignes données.'),(167,45,3,'Utiliser ses capacités de raisonnement, ses connaissances sur la langue, savoir faire appel à des outils variés pour améliorer son texte.'),(283,46,0,'Formuler clairement un propos simple.'),(170,46,1,'Développer de façon suivie un propos en public sur un sujet déterminé.'),(171,46,2,'Adapter sa prise de parole à la situation de communication.'),(172,46,3,'Participer à un débat, à un échange verbal.'),(177,48,0,'Établir un contact social.'),(178,48,1,'Dialoguer sur des sujets familiers.'),(179,48,2,'Demander et donner des informations.'),(180,48,3,'Réagir à des propositions.'),(181,49,0,'Comprendre un message oral pour réaliser une tâche.'),(182,49,1,'Comprendre les points essentiels d’un message oral (conversation, information, récit, exposé).'),(183,50,0,'Reproduire un modèle oral.'),(184,50,1,'Décrire, raconter, expliquer.'),(185,50,2,'Présenter un projet et lire à haute voix.'),(186,51,0,'Comprendre le sens général de documents écrits.'),(187,51,1,'Savoir repérer des informations dans un texte.'),(188,52,0,'Copier, écrire sous la dictée.'),(189,52,1,'Renseigner un questionnaire.'),(190,52,2,'Écrire un message simple.'),(191,52,3,'Rendre compte de faits.'),(192,52,4,'Écrire un court récit, une description.'),(193,53,0,'Rechercher, extraire et organiser l’information utile.'),(194,53,1,'Réaliser, manipuler, mesurer, calculer, appliquer des consignes.'),(195,53,2,'Raisonner, argumenter, pratiquer une démarche expérimentale ou technologique, démontrer.'),(196,53,3,'Présenter la démarche suivie, les résultats obtenus, communiquer à l’aide d’un langage adapté.'),(197,54,0,'Organisation et gestion de données : reconnaître des situations de proportionnalité, utiliser des pourcentages, des tableaux, des graphiques ; exploiter des données statistiques et aborder des situations simples de probabilité.'),(202,54,1,'Nombres et calculs : connaître et utiliser les nombres entiers, décimaux et fractionnaires ; mener à bien un calcul mental, à la main, à la calculatrice, avec un ordinateur.'),(205,54,2,'Géométrie : connaître et représenter des figures géométriques et des objets de l’espace ; utiliser leurs propriétés.'),(208,54,3,'Grandeurs et mesures : réaliser des mesures (longueurs, durées, …), calculer des valeurs (volumes, vitesses, …) en utilisant différentes unités.'),(210,55,0,'L’univers et la Terre : organisation de l’univers ; structure et évolution au cours des temps géologiques de la Terre, phénomènes physiques.'),(211,55,1,'La matière : principales caractéristiques, états et transformations ; propriétés physiques et chimiques de la matière et des matériaux ; comportement électrique, interactions avec la lumière.'),(212,55,2,'Le vivant : unité d’organisation et diversité ; fonctionnement des organismes vivants, évolution des espèces, organisation et fonctionnement du corps humain.'),(213,55,3,'L’énergie : différentes formes d’énergie, notamment l’énergie électrique, et transformations d’une forme à une autre.'),(214,55,4,'Les objets techniques : analyse, conception et réalisation ; fonctionnement et conditions d’utilisation.'),(215,56,0,'Mobiliser ses connaissances pour comprendre des questions liées à l’environnement et au développement durable.'),(218,57,0,'Utiliser, gérer des espaces de stockage à disposition.'),(220,57,1,'Utiliser les périphériques à disposition.'),(216,57,2,'Utiliser les logiciels et les services à disposition.'),(222,58,0,'Connaître et respecter les règles élémentaires du droit relatif à sa pratique.'),(223,58,1,'Protéger sa personne et ses données.'),(225,58,2,'Faire preuve d’esprit critique face à l’information et à son traitement.'),(228,58,3,'Participer à des travaux collaboratifs en connaissant les enjeux et en respectant les règles.'),(229,59,0,'Saisir et mettre en page un texte.'),(235,59,1,'Traiter une image, un son ou une vidéo.'),(231,59,2,'Organiser la composition du document, prévoir sa présentation en fonction de sa destination.'),(234,59,3,'Différencier une situation simulée ou modélisée d’une situation réelle.'),(236,60,0,'Consulter des bases de données documentaires en mode simple (plein texte).'),(239,60,1,'Identifier, trier et évaluer des ressources.'),(238,60,2,'Chercher et sélectionner l’information demandée.'),(243,61,0,'Écrire, envoyer, diffuser, publier.'),(242,61,1,'Recevoir un commentaire, un message y compris avec pièces jointes.'),(284,61,2,'Exploiter les spécificités des différentes situations de communication en temps réel ou différé.'),(245,62,0,'Relevant de l’espace : les grands ensembles physiques et humains et les grands types d’aménagements dans le monde, les principales caractéristiques géographiques de la France et de l’Europe.'),(249,62,1,'Relevant du temps : les différentes périodes de l’histoire de l’humanité ; les grands traits de l’histoire (politique, sociale, économique, littéraire, artistique, culturelle) de la France et de l’Europe.'),(251,62,2,'Relevant de la culture littéraire : œuvres littéraires du patrimoine.'),(252,62,3,'Relevant de la culture artistique : œuvres picturales, musicales, scéniques, architecturales ou cinématographiques du patrimoine.'),(285,62,4,'Relevant de la culture civique : droits de l’Homme ; formes d’organisation politique, économique et sociale dans l’Union européenne ; place et rôle de l’État en France ; mondialisation ; développement durable.'),(286,67,0,'Situer des événements, des œuvres littéraires ou artistiques, des découvertes scientifiques ou techniques, des ensembles géographiques.'),(256,67,1,'Identifier la diversité des civilisations, des langues, des sociétés, des religions.'),(287,67,2,'Établir des liens entre les œuvres (littéraires, artistiques) pour mieux les comprendre.'),(259,67,3,'Mobiliser ses connaissances pour donner du sens à l’actualité.'),(255,66,0,'Lire et employer différents langages : textes – graphiques – cartes – images – musique.'),(293,66,1,'Connaître et pratiquer diverses formes d’expression à visée littéraire.'),(254,66,2,'Connaître et pratiquer diverses formes d’expression à visée artistique.'),(288,74,0,'Être sensible aux enjeux esthétiques et humains d’un texte littéraire.'),(289,74,1,'Être sensible aux enjeux esthétiques et humains d’une œuvre artistique.'),(290,74,2,'Être capable de porter un regard critique sur un fait, un document, une œuvre.'),(291,74,3,'Manifester sa curiosité pour l’actualité et pour les activités culturelles ou artistiques.'),(260,68,0,'Principaux droits de l’Homme et du citoyen.'),(261,68,1,'Valeurs, symboles, institutions de la République.'),(262,68,2,'Règles fondamentales de la démocratie et de la justice.'),(263,68,3,'Grandes institutions de l’Union européenne et rôle des grands organismes internationaux.'),(264,68,4,'Rôle de la défense nationale.'),(265,68,5,'Fonctionnement et rôle de différents médias.'),(266,69,0,'Respecter les règles de la vie collective.'),(267,69,1,'Comprendre l’importance du respect mutuel et accepter toutes les différences.'),(268,69,2,'Connaître des comportements favorables à sa santé et sa sécurité.'),(269,69,3,'Connaître quelques notions juridiques de base.'),(270,69,4,'Savoir utiliser quelques notions économiques et budgétaires de base.'),(272,70,0,'Se familiariser avec l’environnement économique, les entreprises, les métiers de secteurs et de niveaux de qualification variés.'),(273,70,1,'Connaître les parcours de formation correspondant à ces métiers et les possibilités de s’y intégrer.'),(292,70,2,'Savoir s’autoévaluer et être capable de décrire ses intérêts, ses compétences et ses acquis.'),(274,71,0,'Être autonome dans son travail : savoir l’organiser, le planifier, l’anticiper, rechercher et sélectionner des informations utiles.'),(275,71,1,'Identifier ses points forts et ses points faibles dans des situations variées.'),(277,71,2,'Mobiliser à bon escient ses capacités motrices dans le cadre d’une pratique physique (sportive ou artistique) adaptée à son potentiel.'),(276,71,3,'Savoir nager.'),(278,72,0,'S’engager dans un projet individuel.'),(279,72,1,'S’intégrer et coopérer dans un projet collectif.'),(280,72,2,'Manifester curiosité, créativité, motivation à travers des activités conduites ou reconnues par l’établissement.'),(281,72,3,'Assumer des rôles, prendre des initiatives et des décisions.');
/*!40000 ALTER TABLE `sacoche_socle_entree` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_socle_palier`
--

DROP TABLE IF EXISTS `sacoche_socle_palier`;
CREATE TABLE `sacoche_socle_palier` (
  `palier_id` tinyint(3) unsigned NOT NULL auto_increment,
  `palier_ordre` tinyint(3) unsigned NOT NULL,
  `palier_nom` varchar(30) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`palier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_socle_palier`
--

LOCK TABLES `sacoche_socle_palier` WRITE;
/*!40000 ALTER TABLE `sacoche_socle_palier` DISABLE KEYS */;
INSERT INTO `sacoche_socle_palier` VALUES (1,1,'Palier 1 (fin CE1)'),(2,2,'Palier 2 (fin CM2)'),(3,3,'Palier 3 (fin troisième)');
/*!40000 ALTER TABLE `sacoche_socle_palier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_socle_pilier`
--

DROP TABLE IF EXISTS `sacoche_socle_pilier`;
CREATE TABLE `sacoche_socle_pilier` (
  `pilier_id` smallint(5) unsigned NOT NULL auto_increment,
  `palier_id` tinyint(3) unsigned NOT NULL,
  `pilier_ordre` tinyint(3) unsigned NOT NULL COMMENT 'Commence à 1.',
  `pilier_ref` varchar(2) collate utf8_unicode_ci NOT NULL,
  `pilier_nom` varchar(128) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`pilier_id`),
  KEY `palier_id` (`palier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_socle_pilier`
--

LOCK TABLES `sacoche_socle_pilier` WRITE;
/*!40000 ALTER TABLE `sacoche_socle_pilier` DISABLE KEYS */;
INSERT INTO `sacoche_socle_pilier` VALUES (1,1,1,'1','Compétence 1 – La maîtrise de la langue française'),(2,1,2,'3','Compétence 3 – Les principaux éléments de mathématiques'),(3,1,3,'6','Compétence 6 – Les compétences sociales et civiques'),(4,2,1,'1','Compétence 1 – La maîtrise de la langue française'),(5,2,2,'2','Compétence 2 – La pratique d’une langue vivante étrangère (niveau A1)'),(6,2,3,'3a','Compétence 3a – Les principaux éléments de mathématiques'),(7,2,4,'3b','Compétence 3b – La culture scientifique et technologique'),(8,2,5,'4','Compétence 4 – La maîtrise des techniques usuelles de l’information et de la communication (B2i niveau école)'),(9,2,6,'5','Compétence 5 – La culture humaniste'),(10,2,7,'6','Compétence 6 – Les compétences sociales et civiques'),(11,2,8,'7','Compétence 7 – L’autonomie et l’initiative'),(12,3,1,'1','Compétence 1 – La maîtrise de la langue française'),(13,3,2,'2','Compétence 2 – La pratique d’une langue vivante étrangère (niveau A2)'),(14,3,3,'3','Compétence 3 – Les principaux éléments de mathématiques et la culture scientifique et technologique'),(15,3,4,'4','Compétence 4 – La maîtrise des techniques usuelles de l’information et de la communication (B2i)'),(16,3,5,'5','Compétence 5 – La culture humaniste'),(17,3,6,'6','Compétence 6 – Les compétences sociales et civiques'),(18,3,7,'7','Compétence 7 – L’autonomie et l’initiative');
/*!40000 ALTER TABLE `sacoche_socle_pilier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_socle_section`
--

DROP TABLE IF EXISTS `sacoche_socle_section`;
CREATE TABLE `sacoche_socle_section` (
  `section_id` smallint(5) unsigned NOT NULL auto_increment,
  `pilier_id` tinyint(3) unsigned NOT NULL,
  `section_ordre` tinyint(3) unsigned NOT NULL COMMENT 'Commence à 1.',
  `section_nom` varchar(128) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`section_id`),
  KEY `pilier_id` (`pilier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sacoche_socle_section`
--

LOCK TABLES `sacoche_socle_section` WRITE;
/*!40000 ALTER TABLE `sacoche_socle_section` DISABLE KEYS */;
INSERT INTO `sacoche_socle_section` VALUES (1,1,1,'Dire'),(2,1,2,'Lire'),(3,1,3,'Écrire'),(4,1,4,'Étude de la langue - vocabulaire'),(5,1,5,'Étude de la langue - grammaire'),(6,1,6,'Étude de la langue - orthographe'),(7,2,1,'Nombres et calcul'),(8,2,2,'Géométrie'),(9,2,3,'Grandeurs et mesures'),(10,2,4,'Organisation et gestion de données'),(11,3,1,'Connaître les principes et fondements de la vie civique et sociale'),(12,3,2,'Avoir un comportement responsable'),(13,4,1,'Dire'),(14,4,2,'Lire'),(15,4,3,'Écrire'),(16,4,4,'Étude de la langue - vocabulaire'),(17,4,5,'Étude de la langue - grammaire'),(18,4,6,'Étude de la langue - orthographe'),(19,5,1,'Réagir et dialoguer'),(20,5,2,'Comprendre à l’oral'),(21,5,3,'Parler en continu'),(22,5,4,'Lire'),(23,5,5,'Écrire'),(24,6,1,'Nombres et calcul'),(25,6,2,'Géométrie'),(26,6,3,'Grandeurs et mesures'),(27,6,4,'Organisation et gestion de données'),(28,7,1,'Pratiquer une démarche scientifique ou technologique'),(29,7,2,'Maîtriser des connaissances dans divers domaines scientifiques et les mobiliser dans des contextes scientifiques différents et d'),(30,7,3,'Environnement et développement durable'),(31,8,1,'S’approprier un environnement informatique de travail'),(32,8,2,'Adopter une attitude responsable'),(33,8,3,'Créer, produire, traiter, exploiter des données'),(34,8,4,'S’informer, se documenter'),(35,8,5,'Communiquer, échanger'),(36,9,1,'Avoir des repères relevant du temps et de l’espace'),(37,9,2,'Avoir des repères littéraires'),(73,9,3,'Lire et pratiquer différents langages'),(38,9,4,'Pratiquer les arts et avoir des repères en histoire des arts'),(39,10,1,'Connaître les principes et fondements de la vie civique et sociale'),(40,10,2,'Avoir un comportement responsable'),(41,11,1,'S’appuyer sur des méthodes de travail pour être autonome'),(42,11,2,'Faire preuve d’initiative'),(43,11,3,'Avoir une bonne maîtrise de son corps et une pratique physique (sportive ou artistique)'),(44,12,1,'Lire'),(45,12,2,'Écrire'),(46,12,3,'Dire'),(48,13,1,'Réagir et dialoguer'),(49,13,2,'Écouter et comprendre'),(50,13,3,'Parler en continu'),(51,13,4,'Lire'),(52,13,5,'Écrire'),(53,14,1,'Pratiquer une démarche scientifique et technologique, résoudre des problèmes'),(54,14,2,'Savoir utiliser des connaissances et des compétences mathématiques'),(55,14,3,'Savoir utiliser des connaissances dans divers domaines scientifiques'),(56,14,4,'Environnement et développement durable'),(57,15,1,'S’approprier un environnement informatique de travail'),(58,15,2,'Adopter une attitude responsable'),(59,15,3,'Créer, produire, traiter, exploiter des données'),(60,15,4,'S’informer, se documenter'),(61,15,5,'Communiquer, échanger'),(62,16,1,'Avoir des connaissances et des repères'),(67,16,2,'Situer dans le temps, l’espace, les civilisations'),(66,16,3,'Lire et pratiquer différents langages'),(74,16,4,'Faire preuve de sensibilité, d’esprit critique, de curiosité'),(68,17,1,'Connaître les principes et fondements de la vie civique et sociale'),(69,17,2,'Avoir un comportement responsable'),(70,18,1,'Être acteur de son parcours de formation et d’orientation'),(71,18,2,'Être capable de mobiliser ses ressources intellectuelles et physiques dans diverses situations'),(72,18,3,'Faire preuve d’initiative');
/*!40000 ALTER TABLE `sacoche_socle_section` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sacoche_user`
--

DROP TABLE IF EXISTS `sacoche_user`;
CREATE TABLE `sacoche_user` (
  `user_id` mediumint(8) unsigned NOT NULL auto_increment,
  `user_num_sconet` mediumint(8) unsigned NOT NULL COMMENT 'ELENOET pour un élève (entre 2000 et 5000 ; parfois appelé n° GEP avec un 0 devant) ou INDIVIDU_ID pour un prof (dépasse parfois une capacité SMALLINT UNSIGNED)',
  `user_reference` char(11) collate utf8_unicode_ci NOT NULL COMMENT 'Dans Sconet, ID_NATIONAL pour un élève (pour un prof ce pourrait être le NUMEN mais il n''est pas renseigné et il faudrait deux caractères de plus). Ce champ sert aussi pour un import tableur.',
  `user_profil` enum('eleve','professeur','directeur','administrateur') collate utf8_unicode_ci NOT NULL,
  `user_nom` varchar(20) collate utf8_unicode_ci NOT NULL,
  `user_prenom` varchar(20) collate utf8_unicode_ci NOT NULL,
  `user_login` varchar(20) collate utf8_unicode_ci NOT NULL,
  `user_password` char(32) collate utf8_unicode_ci NOT NULL,
  `user_statut` tinyint(1) NOT NULL default '1',
  `user_tentative_date` datetime NOT NULL,
  `user_connexion_date` datetime NOT NULL,
  `eleve_classe_id` mediumint(8) unsigned NOT NULL,
  `user_id_ent` varchar(32) collate utf8_unicode_ci NOT NULL COMMENT 'Paramètre renvoyé après une identification CAS depuis un ENT (ça peut être le login, mais ça peut aussi être un numéro interne à l''ENT...).',
  `user_id_gepi` varchar(32) collate utf8_unicode_ci NOT NULL COMMENT 'Login de l''utilisateur dans Gepi utilisé pour un transfert note/moyenne vers un bulletin.',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_login` (`user_login`),
  KEY `user_profil` (`user_profil`),
  KEY `user_statut` (`user_statut`),
  KEY `user_id_ent` (`user_id_ent`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




--
-- Dumping data for table `sacoche_user`
--

LOCK TABLES `sacoche_user` WRITE;
/*!40000 ALTER TABLE `sacoche_user` DISABLE KEYS */;
INSERT INTO `sacoche_user` VALUES (1,0,'','administrateur','A modifier','A modifier','admin','#passadmin#',1,'2010-09-23 01:46:12','2010-09-23 03:51:14',0,'','');
/*!40000 ALTER TABLE `sacoche_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

GRANT SELECT,UPDATE,DELETE,INSERT,CREATE,DROP ON sacoche_plug.* TO sacoche_user@localhost IDENTIFIED BY '#PASS#';

-- Dump completed on 2010-09-23  4:00:05
