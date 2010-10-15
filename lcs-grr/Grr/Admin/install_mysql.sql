CREATE DATABASE grr_plug;
USE grr_plug;
--
-- Table structure for table `grr_area`
--

DROP TABLE IF EXISTS `grr_area`;
CREATE TABLE `grr_area` (
  `id` int(11) NOT NULL auto_increment,
  `area_name` varchar(30) NOT NULL default '',
  `access` char(1) NOT NULL default '',
  `order_display` smallint(6) NOT NULL default '0',
  `ip_adr` varchar(15) NOT NULL default '',
  `morningstarts_area` smallint(6) NOT NULL default '0',
  `eveningends_area` smallint(6) NOT NULL default '0',
  `resolution_area` int(11) NOT NULL default '0',
  `eveningends_minutes_area` smallint(6) NOT NULL default '0',
  `weekstarts_area` smallint(6) NOT NULL default '0',
  `twentyfourhour_format_area` smallint(6) NOT NULL default '0',
  `calendar_default_values` char(1) NOT NULL default 'y',
  `enable_periods` char(1) NOT NULL default 'n',
  `display_days` varchar(7) NOT NULL default 'yyyyyyy',
  `id_type_par_defaut` int(11) NOT NULL default '-1',
  `duree_max_resa_area` int(11) NOT NULL default '-1',
  `duree_par_defaut_reservation_area` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_area`
--

LOCK TABLES `grr_area` WRITE;
/*!40000 ALTER TABLE `grr_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_area_periodes`
--

DROP TABLE IF EXISTS `grr_area_periodes`;
CREATE TABLE `grr_area_periodes` (
  `id_area` int(11) NOT NULL default '0',
  `num_periode` smallint(6) NOT NULL default '0',
  `nom_periode` varchar(100) NOT NULL default ''
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_area_periodes`
--

LOCK TABLES `grr_area_periodes` WRITE;
/*!40000 ALTER TABLE `grr_area_periodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_area_periodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_calendar`
--

DROP TABLE IF EXISTS `grr_calendar`;
CREATE TABLE `grr_calendar` (
  `DAY` int(11) NOT NULL default '0'
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_calendar`
--

LOCK TABLES `grr_calendar` WRITE;
/*!40000 ALTER TABLE `grr_calendar` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_calendar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_calendrier_jours_cycle`
--

DROP TABLE IF EXISTS `grr_calendrier_jours_cycle`;
CREATE TABLE `grr_calendrier_jours_cycle` (
  `DAY` int(11) NOT NULL default '0',
  `Jours` varchar(20) default NULL
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_calendrier_jours_cycle`
--

LOCK TABLES `grr_calendrier_jours_cycle` WRITE;
/*!40000 ALTER TABLE `grr_calendrier_jours_cycle` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_calendrier_jours_cycle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_entry`
--

DROP TABLE IF EXISTS `grr_entry`;
CREATE TABLE `grr_entry` (
  `id` int(11) NOT NULL auto_increment,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `entry_type` int(11) NOT NULL default '0',
  `repeat_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp NOT NULL ,
  `create_by` varchar(25) NOT NULL default '',
  `beneficiaire_ext` varchar(200) NOT NULL,
  `beneficiaire` varchar(100) NOT NULL,
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
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_entry`
--

LOCK TABLES `grr_entry` WRITE;
/*!40000 ALTER TABLE `grr_entry` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_entry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_entry_moderate`
--

DROP TABLE IF EXISTS `grr_entry_moderate`;
CREATE TABLE `grr_entry_moderate` (
  `id` int(11) NOT NULL auto_increment,
  `login_moderateur` varchar(40) NOT NULL default '',
  `motivation_moderation` text NOT NULL,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `entry_type` int(11) NOT NULL default '0',
  `repeat_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp NOT NULL ,
  `create_by` varchar(25) NOT NULL default '',
  `beneficiaire_ext` varchar(200) NOT NULL,
  `beneficiaire` varchar(100) NOT NULL,
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
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_entry_moderate`
--

LOCK TABLES `grr_entry_moderate` WRITE;
/*!40000 ALTER TABLE `grr_entry_moderate` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_entry_moderate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_j_mailuser_room`
--

DROP TABLE IF EXISTS `grr_j_mailuser_room`;
CREATE TABLE `grr_j_mailuser_room` (
  `login` varchar(40) NOT NULL default '',
  `id_room` int(11) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_room`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_j_mailuser_room`
--

LOCK TABLES `grr_j_mailuser_room` WRITE;
/*!40000 ALTER TABLE `grr_j_mailuser_room` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_j_mailuser_room` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_j_type_area`
--

DROP TABLE IF EXISTS `grr_j_type_area`;
CREATE TABLE `grr_j_type_area` (
  `id_type` int(11) NOT NULL default '0',
  `id_area` int(11) NOT NULL default '0'
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_j_type_area`
--

LOCK TABLES `grr_j_type_area` WRITE;
/*!40000 ALTER TABLE `grr_j_type_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_j_type_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_j_user_area`
--

DROP TABLE IF EXISTS `grr_j_user_area`;
CREATE TABLE `grr_j_user_area` (
  `login` varchar(40) NOT NULL default '',
  `id_area` int(11) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_area`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_j_user_area`
--

LOCK TABLES `grr_j_user_area` WRITE;
/*!40000 ALTER TABLE `grr_j_user_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_j_user_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_j_user_room`
--

DROP TABLE IF EXISTS `grr_j_user_room`;
CREATE TABLE `grr_j_user_room` (
  `login` varchar(40) NOT NULL default '',
  `id_room` int(11) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_room`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_j_user_room`
--

LOCK TABLES `grr_j_user_room` WRITE;
/*!40000 ALTER TABLE `grr_j_user_room` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_j_user_room` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_j_useradmin_area`
--

DROP TABLE IF EXISTS `grr_j_useradmin_area`;
CREATE TABLE `grr_j_useradmin_area` (
  `login` varchar(40) NOT NULL default '',
  `id_area` int(11) NOT NULL default '0',
  PRIMARY KEY  (`login`,`id_area`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_j_useradmin_area`
--

LOCK TABLES `grr_j_useradmin_area` WRITE;
/*!40000 ALTER TABLE `grr_j_useradmin_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_j_useradmin_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_log`
--

DROP TABLE IF EXISTS `grr_log`;
CREATE TABLE `grr_log` (
  `LOGIN` varchar(40) NOT NULL default '',
  `START` datetime NOT NULL default '0000-00-00 00:00:00',
  `SESSION_ID` varchar(64) NOT NULL default '',
  `REMOTE_ADDR` varchar(16) NOT NULL default '',
  `USER_AGENT` varchar(255) default NULL,
  `REFERER` varchar(255) default NULL,
  `AUTOCLOSE` enum('0','1') NOT NULL default '0',
  `END` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`SESSION_ID`,`START`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_log`
--

LOCK TABLES `grr_log` WRITE;
/*!40000 ALTER TABLE `grr_log` DISABLE KEYS */;
INSERT INTO `grr_log` VALUES ('ADMIN','2008-12-01 04:00:04','a6562024718d56215c7ad1cb8c586c53','192.168.2.1','Mozilla/5.0 (X11; U; Linux i686; fr; rv:1.8.1.18) Gecko/20081030 Iceweasel/2.0.0.18 (Debian-2.0.0.18','http://localhost:2994/Plugins/Grr/','1','2008-12-01 04:30:24');
/*!40000 ALTER TABLE `grr_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_overload`
--

DROP TABLE IF EXISTS `grr_overload`;
CREATE TABLE `grr_overload` (
  `id` int(11) NOT NULL auto_increment,
  `id_area` int(11) NOT NULL,
  `fieldname` varchar(25) NOT NULL default '',
  `fieldtype` varchar(25) NOT NULL default '',
  `fieldlist` text NOT NULL,
  `obligatoire` char(1) NOT NULL default 'n',
  `affichage` char(1) NOT NULL default 'n',
  `overload_mail` char(1) NOT NULL default 'n',
  `confidentiel` char(1) NOT NULL default 'n',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_overload`
--

LOCK TABLES `grr_overload` WRITE;
/*!40000 ALTER TABLE `grr_overload` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_overload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_repeat`
--

DROP TABLE IF EXISTS `grr_repeat`;
CREATE TABLE `grr_repeat` (
  `id` int(11) NOT NULL auto_increment,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `rep_type` int(11) NOT NULL default '0',
  `end_date` int(11) NOT NULL default '0',
  `rep_opt` varchar(32) NOT NULL default '',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp NOT NULL ,
  `create_by` varchar(25) NOT NULL default '',
  `beneficiaire_ext` varchar(200) NOT NULL,
  `beneficiaire` varchar(100) NOT NULL,
  `name` varchar(80) NOT NULL default '',
  `type` char(2) NOT NULL default 'A',
  `description` text,
  `rep_num_weeks` tinyint(4) default '0',
  `overload_desc` text,
  `jours` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_repeat`
--

LOCK TABLES `grr_repeat` WRITE;
/*!40000 ALTER TABLE `grr_repeat` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_repeat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_room`
--

DROP TABLE IF EXISTS `grr_room`;
CREATE TABLE `grr_room` (
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
  `delais_max_resa_room` smallint(6) NOT NULL default '-1',
  `delais_min_resa_room` smallint(6) NOT NULL default '0',
  `allow_action_in_past` char(1) NOT NULL default 'n',
  `dont_allow_modify` char(1) NOT NULL default 'n',
  `order_display` smallint(6) NOT NULL default '0',
  `delais_option_reservation` smallint(6) NOT NULL default '0',
  `type_affichage_reser` smallint(6) NOT NULL default '0',
  `moderate` tinyint(1) default '0',
  `qui_peut_reserver_pour` varchar(1) NOT NULL default '5',
  `active_ressource_empruntee` char(1) NOT NULL default 'y',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_room`
--

LOCK TABLES `grr_room` WRITE;
/*!40000 ALTER TABLE `grr_room` DISABLE KEYS */;
/*!40000 ALTER TABLE `grr_room` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_setting`
--

DROP TABLE IF EXISTS `grr_setting`;
CREATE TABLE `grr_setting` (
  `NAME` varchar(32) NOT NULL default '',
  `VALUE` text NOT NULL,
  PRIMARY KEY  (`NAME`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `grr_setting`
--

LOCK TABLES `grr_setting` WRITE;
/*!40000 ALTER TABLE `grr_setting` DISABLE KEYS */;
INSERT INTO `grr_setting` VALUES ('sessionMaxLength','30'),('automatic_mail','yes'),('company','Nom de l\'établissement'),('webmaster_name','Webmestre de GRR'),('disable_login','no'),('begin_bookings','1183240800'),('end_bookings','1222725600'),('title_home_page','Gestion et Réservation de Ressources'),('message_home_page','En raison du caractère personnel du contenu, ce site est soumis à des restrictions utilisateurs. Pour accéder aux outils de réservation, identifiez-vous :'),('version','1.9.5'),('versionRC',''),('default_language','fr'),('url_disconnect',''),('allow_users_modify_profil','2'),('allow_users_modify_mdp','2'),('maj194_champs_additionnels','1'),('display_info_bulle','1'),('display_full_description','1'),('pview_new_windows','1'),('default_report_days','30'),('authentification_obli','1'),('use_fckeditor','1'),('visu_fiche_description','0'),('allow_search_level','1'),('allow_user_delete_after_begin','0'),('allow_gestionnaire_modify_del','1'),('javascript_info_disabled','0'),('javascript_info_admin_disabled','0'),('pass_leng','6'),('allow_users_modify_email','2'),('jour_debut_Jours/Cycles','1'),('nombre_jours_Jours/Cycles','1'),('UserAllRoomsMaxBooking','-1'),('jours_cycles_actif','Non'),('grr_mail_Password',''),('grr_mail_method','mail'),('grr_mail_smtp',''),('grr_mail_Bcc','n'),('grr_mail_Username',''),('verif_reservation_auto','0'),('ConvertLdapUtf8toIso','y'),('ActiveModeDiagnostic','n'),('ldap_champ_nom','sn'),('ldap_champ_prenom','givenname'),('ldap_champ_email','mail'),('gestion_lien_aide','ext'),('lien_aide',''),('display_short_description','1'),('remplissage_description_breve','1'),('ldap_champ_recherche','uid'),('maj195_champ_rep_type_grr_repeat','1');
/*!40000 ALTER TABLE `grr_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_type_area`
--

DROP TABLE IF EXISTS `grr_type_area`;
CREATE TABLE `grr_type_area` (
  `id` int(11) NOT NULL auto_increment,
  `type_name` varchar(30) NOT NULL default '',
  `order_display` smallint(6) NOT NULL default '0',
  `couleur` smallint(6) NOT NULL default '0',
  `type_letter` char(2) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 ;

--
-- Dumping data for table `grr_type_area`
--

LOCK TABLES `grr_type_area` WRITE;
/*!40000 ALTER TABLE `grr_type_area` DISABLE KEYS */;
INSERT INTO `grr_type_area` VALUES (1,'Cours',1,1,'A'),(2,'Réunion',2,2,'B'),(3,'Stage',3,3,'C'),(4,'Devoir',4,4,'D'),(5,'Autre',5,5,'E');
/*!40000 ALTER TABLE `grr_type_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grr_utilisateurs`
--

DROP TABLE IF EXISTS `grr_utilisateurs`;
CREATE TABLE `grr_utilisateurs` (
  `login` varchar(40) NOT NULL default '',
  `nom` varchar(30) NOT NULL default '',
  `prenom` varchar(30) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `statut` varchar(30) NOT NULL default '',
  `etat` varchar(20) NOT NULL default '',
  `default_area` smallint(6) NOT NULL default '0',
  `default_room` smallint(6) NOT NULL default '0',
  `default_style` varchar(50) NOT NULL default '',
  `default_list_type` varchar(50) NOT NULL default '',
  `default_language` char(3) NOT NULL default '',
  `source` varchar(10) NOT NULL default 'local',
  PRIMARY KEY  (`login`)
) ENGINE=MyISAM ;

GRANT SELECT,UPDATE,DELETE,INSERT, CREATE, DROP, INDEX, ALTER ON grr_plug.* TO grr_user@localhost IDENTIFIED BY '#PASS#';
FLUSH PRIVILEGES;
