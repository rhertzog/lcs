-- MySQL dump 10.11
--
-- Host: localhost    Database: ggt_plug
-- ------------------------------------------------------
-- Server version	5.0.51a-24+lenny5

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

--
-- Table structure for table `ateliers`
--

CREATE DATABASE `ggt_plug` ;
USE `ggt_plug`;

DROP TABLE IF EXISTS `ateliers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ateliers` (
  `id_at` smallint(6) NOT NULL auto_increment,
  `nom` varchar(50) collate utf8_unicode_ci NOT NULL,
  `description` blob NOT NULL,
  `prof` varchar(30) collate utf8_unicode_ci NOT NULL,
  `niveau` smallint(6) NOT NULL,
  `is_propose` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id_at`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ateliers`
--

LOCK TABLES `ateliers` WRITE;
/*!40000 ALTER TABLE `ateliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ateliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscriptions`
--

DROP TABLE IF EXISTS `inscriptions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `inscriptions` (
  `id_insc` smallint(6) NOT NULL auto_increment,
  `eleve` varchar(50) collate utf8_unicode_ci NOT NULL,
  `login` varchar(30) collate utf8_unicode_ci NOT NULL,
  `classe` varchar(50) collate utf8_unicode_ci NOT NULL,
  `niveau` smallint(6) NOT NULL,
  `v1` smallint(6) NOT NULL,
  `v2` smallint(6) NOT NULL,
  `v3` smallint(6) NOT NULL,
  PRIMARY KEY  (`id_insc`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `inscriptions`
--

LOCK TABLES `inscriptions` WRITE;
/*!40000 ALTER TABLE `inscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `inscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listes`
--

DROP TABLE IF EXISTS `listes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `listes` (
  `id_liste` smallint(6) NOT NULL auto_increment,
  `niveau` smallint(6) NOT NULL,
  `html` blob NOT NULL,
  PRIMARY KEY  (`id_liste`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `listes`
--

LOCK TABLES `listes` WRITE;
/*!40000 ALTER TABLE `listes` DISABLE KEYS */;
/*!40000 ALTER TABLE `listes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `niveaux`
--

DROP TABLE IF EXISTS `niveaux`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `niveaux` (
  `id_niv` smallint(6) NOT NULL auto_increment,
  `nom` varchar(30) collate utf8_unicode_ci NOT NULL,
  `ordre` smallint(6) NOT NULL,
  `coordinateur` varchar(50) collate utf8_unicode_ci NOT NULL,
  `Dbut` date NOT NULL,
  `F1` date NOT NULL,
  PRIMARY KEY  (`id_niv`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `niveaux`
--

LOCK TABLES `niveaux` WRITE;
/*!40000 ALTER TABLE `niveaux` DISABLE KEYS */;
/*!40000 ALTER TABLE `niveaux` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proposes`
--

DROP TABLE IF EXISTS `proposes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `proposes` (
  `id_prop` smallint(6) NOT NULL auto_increment,
  `id_nivo` smallint(6) NOT NULL,
  `id_atelier` text collate utf8_unicode_ci NOT NULL,
  `frome` date NOT NULL,
  `too` date NOT NULL,
  `classe` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id_prop`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `proposes`
--

LOCK TABLES `proposes` WRITE;
/*!40000 ALTER TABLE `proposes` DISABLE KEYS */;
/*!40000 ALTER TABLE `proposes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repartition`
--

DROP TABLE IF EXISTS `repartition`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `repartition` (
  `id` smallint(6) NOT NULL auto_increment,
  `at_id` varchar(6) collate utf8_unicode_ci NOT NULL,
  `eleve_id` varchar(50) collate utf8_unicode_ci NOT NULL,
  `niveau` smallint(6) NOT NULL,
  `deb` date NOT NULL,
  `fin` date NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `repartition`
--

LOCK TABLES `repartition` WRITE;
/*!40000 ALTER TABLE `repartition` DISABLE KEYS */;
/*!40000 ALTER TABLE `repartition` ENABLE KEYS */;
UNLOCK TABLES;

GRANT SELECT,UPDATE,DELETE,INSERT, CREATE, DROP, INDEX, ALTER ON ggt_plug.* TO ggt_user@localhost IDENTIFIED BY '#PASS#';
FLUSH PRIVILEGES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-01-18 17:59:47

