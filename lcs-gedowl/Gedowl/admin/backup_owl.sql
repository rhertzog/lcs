-- MySQL dump 10.11
--
-- Host: localhost    Database: owl_plug
-- ------------------------------------------------------
-- Server version	5.0.32-Debian_7etch3-log

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
-- Table structure for table `active_sessions`
--

DROP TABLE IF EXISTS `active_sessions`;
CREATE TABLE `active_sessions` (
  `sessid` char(32) NOT NULL default '',
  `usid` char(25) default NULL,
  `lastused` int(10) unsigned default NULL,
  `ip` char(16) default NULL,
  `currentdb` int(4) default NULL,
  PRIMARY KEY  (`sessid`)
);

--
-- Dumping data for table `active_sessions`
--

LOCK TABLES `active_sessions` WRITE;
/*!40000 ALTER TABLE `active_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `active_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `advanced_acl`
--

DROP TABLE IF EXISTS `advanced_acl`;
CREATE TABLE `advanced_acl` (
  `group_id` int(4) default NULL,
  `user_id` int(4) default NULL,
  `file_id` int(4) default NULL,
  `folder_id` int(4) default NULL,
  `owlread` int(4) default '0',
  `owlwrite` int(4) default '0',
  `owlviewlog` int(4) default '0',
  `owldelete` int(4) default '0',
  `owlcopy` int(4) default '0',
  `owlmove` int(4) default '0',
  `owlproperties` int(4) default '0',
  `owlupdate` int(4) default '0',
  `owlcomment` int(4) default '0',
  `owlcheckin` int(4) default '0',
  `owlemail` int(4) default '0',
  `owlrelsearch` int(4) default '0',
  `owlsetacl` int(4) default '0',
  `owlmonitor` int(4) default '0',
  KEY `acl_folderid` (`folder_id`),
  KEY `acl_fileid` (`file_id`),
  KEY `acl_userid` (`user_id`),
  KEY `acl_groupid_index` (`group_id`)
);

--
-- Dumping data for table `advanced_acl`
--

LOCK TABLES `advanced_acl` WRITE;
/*!40000 ALTER TABLE `advanced_acl` DISABLE KEYS */;
/*!40000 ALTER TABLE `advanced_acl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(4) NOT NULL auto_increment,
  `fid` int(4) NOT NULL default '0',
  `userid` int(4) default NULL,
  `comment_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `comments` text NOT NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docfields`
--

DROP TABLE IF EXISTS `docfields`;
CREATE TABLE `docfields` (
  `id` int(4) NOT NULL auto_increment,
  `doc_type_id` int(4) NOT NULL default '0',
  `field_name` char(80) NOT NULL default '',
  `field_position` int(4) NOT NULL default '0',
  `field_type` char(80) NOT NULL default '',
  `field_values` text NOT NULL,
  `field_size` bigint(20) NOT NULL default '0',
  `searchable` int(4) NOT NULL default '0',
  `show_desc` int(4) NOT NULL default '0',
  `required` int(4) NOT NULL default '0',
  `show_in_list` int(4) default NULL,
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `docfields`
--

LOCK TABLES `docfields` WRITE;
/*!40000 ALTER TABLE `docfields` DISABLE KEYS */;
/*!40000 ALTER TABLE `docfields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docfieldslabel`
--

DROP TABLE IF EXISTS `docfieldslabel`;
CREATE TABLE `docfieldslabel` (
  `doc_field_id` int(4) NOT NULL default '0',
  `field_label` char(80) NOT NULL default '',
  `locale` char(80) NOT NULL default ''
) ;

--
-- Dumping data for table `docfieldslabel`
--

LOCK TABLES `docfieldslabel` WRITE;
/*!40000 ALTER TABLE `docfieldslabel` DISABLE KEYS */;
/*!40000 ALTER TABLE `docfieldslabel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docfieldvalues`
--

DROP TABLE IF EXISTS `docfieldvalues`;
CREATE TABLE `docfieldvalues` (
  `id` int(4) NOT NULL auto_increment,
  `file_id` int(4) NOT NULL default '0',
  `field_name` char(80) NOT NULL default '',
  `field_value` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `docvalue_fileid` (`file_id`)
);

--
-- Dumping data for table `docfieldvalues`
--

LOCK TABLES `docfieldvalues` WRITE;
/*!40000 ALTER TABLE `docfieldvalues` DISABLE KEYS */;
/*!40000 ALTER TABLE `docfieldvalues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctype`
--

DROP TABLE IF EXISTS `doctype`;
CREATE TABLE `doctype` (
  `doc_type_id` int(4) NOT NULL auto_increment,
  `doc_type_name` char(255) NOT NULL default '',
  PRIMARY KEY  (`doc_type_id`)
) ;

--
-- Dumping data for table `doctype`
--

LOCK TABLES `doctype` WRITE;
/*!40000 ALTER TABLE `doctype` DISABLE KEYS */;
INSERT INTO `doctype` VALUES (1,'Default');
/*!40000 ALTER TABLE `doctype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `userid` int(4) NOT NULL default '0',
  `folder_id` int(4) NOT NULL default '1'
) ;

--
-- Dumping data for table `favorites`
--

LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
INSERT INTO `favorites` VALUES (3,1),(1,13),(1,3),(73,33),(73,1);
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filedata`
--

DROP TABLE IF EXISTS `filedata`;
CREATE TABLE `filedata` (
  `id` int(4) NOT NULL default '0',
  `compressed` int(4) NOT NULL default '0',
  `data` longblob,
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `filedata`
--

LOCK TABLES `filedata` WRITE;
/*!40000 ALTER TABLE `filedata` DISABLE KEYS */;
/*!40000 ALTER TABLE `filedata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int(4) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `filename` varchar(255) NOT NULL default '',
  `f_size` bigint(20) NOT NULL default '0',
  `creatorid` int(4) NOT NULL default '0',
  `parent` int(4) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `description` text NOT NULL,
  `metadata` text NOT NULL,
  `security` int(4) NOT NULL default '0',
  `groupid` int(4) NOT NULL default '0',
  `smodified` datetime NOT NULL default '0000-00-00 00:00:00',
  `checked_out` int(4) NOT NULL default '0',
  `major_revision` int(4) NOT NULL default '0',
  `minor_revision` int(4) NOT NULL default '1',
  `url` int(4) NOT NULL default '0',
  `password` varchar(50) NOT NULL default '',
  `doctype` int(4) default NULL,
  `updatorid` int(4) default NULL,
  `linkedto` int(4) default NULL,
  `approved` int(4) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `fileid_index` (`id`),
  KEY `parentid_index` (`parent`),
  KEY `files_filetype` (`url`)
);

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `folders`
--

DROP TABLE IF EXISTS `folders`;
CREATE TABLE `folders` (
  `id` int(4) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `parent` int(4) NOT NULL default '0',
  `description` text NOT NULL,
  `security` varchar(5) NOT NULL default '',
  `groupid` int(4) NOT NULL default '0',
  `creatorid` int(4) NOT NULL default '0',
  `password` varchar(50) NOT NULL default '',
  `smodified` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `folderid_index` (`id`)
);

--
-- Dumping data for table `folders`
--

LOCK TABLES `folders` WRITE;
/*!40000 ALTER TABLE `folders` DISABLE KEYS */;
INSERT INTO `folders` VALUES (1,'Documents',0,'','50',0,1,'','2005-04-22 08:13:42');
/*!40000 ALTER TABLE `folders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(4) NOT NULL auto_increment,
  `name` char(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (0,'Administrators');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `html`
--

DROP TABLE IF EXISTS `html`;
CREATE TABLE `html` (
  `id` int(4) NOT NULL auto_increment,
  `table_expand_width` char(15) default NULL,
  `table_collapse_width` char(15) default NULL,
  `body_background` char(255) default NULL,
  `owl_logo` char(255) default NULL,
  `body_textcolor` char(15) default NULL,
  `body_link` char(15) default NULL,
  `body_vlink` char(15) default NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `html`
--

LOCK TABLES `html` WRITE;
/*!40000 ALTER TABLE `html` DISABLE KEYS */;
INSERT INTO `html` VALUES (1,'90%','50%','','owl_logo1.gif','#000000','#000000','#000000');
/*!40000 ALTER TABLE `html` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membergroup`
--

DROP TABLE IF EXISTS `membergroup`;
CREATE TABLE `membergroup` (
  `userid` int(4) NOT NULL default '0',
  `groupid` int(4) default NULL,
  `groupadmin` int(4) default NULL
) ;

--
-- Dumping data for table `membergroup`
--

LOCK TABLES `membergroup` WRITE;
/*!40000 ALTER TABLE `membergroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `membergroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metakeywords`
--

DROP TABLE IF EXISTS `metakeywords`;
CREATE TABLE `metakeywords` (
  `keyword_id` int(4) NOT NULL auto_increment,
  `keyword_text` char(255) NOT NULL default '',
  PRIMARY KEY  (`keyword_id`)
) ;

--
-- Dumping data for table `metakeywords`
--

LOCK TABLES `metakeywords` WRITE;
/*!40000 ALTER TABLE `metakeywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `metakeywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mimes`
--

DROP TABLE IF EXISTS `mimes`;
CREATE TABLE `mimes` (
  `filetype` char(10) NOT NULL default '',
  `mimetype` char(50) NOT NULL default '',
  PRIMARY KEY  (`filetype`)
) ;

--
-- Dumping data for table `mimes`
--

LOCK TABLES `mimes` WRITE;
/*!40000 ALTER TABLE `mimes` DISABLE KEYS */;
INSERT INTO `mimes` VALUES ('ai','application/postscript'),('aif','audio/x-aiff'),('aifc','audio/x-aiff'),('aiff','audio/x-aiff'),('asc','text/plain'),('au','audio/basic'),('avi','video/x-msvideo'),('bcpio','application/x-bcpio'),('bin','application/octet-stream'),('bmp','image/bmp'),('cdf','application/x-netcdf'),('class','application/octet-stream'),('cpio','application/x-cpio'),('cpt','application/mac-compactpro'),('csh','application/x-csh'),('css','text/css'),('dcr','application/x-director'),('dir','application/x-director'),('dms','application/octet-stream'),('doc','application/msword'),('dvi','application/x-dvi'),('dxr','application/x-director'),('eps','application/postscript'),('etx','text/x-setext'),('exe','application/octet-stream'),('ez','application/andrew-inset'),('gif','image/gif'),('gtar','application/x-gtar'),('hdf','application/x-hdf'),('hqx','application/mac-binhex40'),('htm','text/html'),('html','text/html'),('ice','x-conference/x-cooltalk'),('ief','image/ief'),('iges','model/iges'),('igs','model/iges'),('jpe','image/jpeg'),('jpeg','image/jpeg'),('jpg','image/jpeg'),('js','application/x-javascript'),('kar','audio/midi'),('latex','application/x-latex'),('lha','application/octet-stream'),('lzh','application/octet-stream'),('man','application/x-troff-man'),('me','application/x-troff-me'),('mesh','model/mesh'),('mid','audio/midi'),('midi','audio/midi'),('mif','application/vnd.mif'),('mov','video/quicktime'),('movie','video/x-sgi-movie'),('mp2','audio/mpeg'),('mp3','audio/mpeg'),('mpe','video/mpeg'),('mpeg','video/mpeg'),('mpg','video/mpeg'),('mpga','audio/mpeg'),('ms','application/x-troff-ms'),('msh','model/mesh'),('nc','application/x-netcdf'),('oda','application/oda'),('pbm','image/x-portable-bitmap'),('pdb','chemical/x-pdb'),('pdf','application/pdf'),('pgm','image/x-portable-graymap'),('pgn','application/x-chess-pgn'),('png','image/png'),('pnm','image/x-portable-anymap'),('ppm','image/x-portable-pixmap'),('ppt','application/vnd.ms-powerpoint'),('ps','application/postscript'),('qt','video/quicktime'),('ra','audio/x-realaudio'),('ram','audio/x-pn-realaudio'),('ras','image/x-cmu-raster'),('rgb','image/x-rgb'),('rm','audio/x-pn-realaudio'),('roff','application/x-troff'),('rpm','audio/x-pn-realaudio-plugin'),('rtf','text/rtf'),('rtx','text/richtext'),('sgm','text/sgml'),('sgml','text/sgml'),('sh','application/x-sh'),('shar','application/x-shar'),('silo','model/mesh'),('sit','application/x-stuffit'),('skd','application/x-koan'),('skm','application/x-koan'),('skp','application/x-koan'),('skt','application/x-koan'),('smi','application/smil'),('smil','application/smil'),('snd','audio/basic'),('spl','application/x-futuresplash'),('src','application/x-wais-source'),('sv4cpio','application/x-sv4cpio'),('sv4crc','application/x-sv4crc'),('swf','application/x-shockwave-flash'),('t','application/x-troff'),('tar','application/x-tar'),('tcl','application/x-tcl'),('tex','application/x-tex'),('texi','application/x-texinfo'),('texinfo','application/x-texinfo'),('tif','image/tiff'),('tiff','image/tiff'),('tr','application/x-troff'),('tsv','text/tab-separated-values'),('txt','text/plain'),('ustar','application/x-ustar'),('vcd','application/x-cdlink'),('vrml','model/vrml'),('wav','audio/x-wav'),('wrl','model/vrml'),('xbm','image/x-xbitmap'),('xls','application/vnd.ms-excel'),('xml','text/xml'),('xpm','image/x-xpixmap'),('xwd','image/x-xwindowdump'),('xyz','chemical/x-pdb'),('zip','application/zip'),('gz','application/x-gzip'),('tgz','application/x-gzip'),('sxw','application/vnd.sun.xml.writer'),('stw','application/vnd.sun.xml.writer.template'),('sxg','application/vnd.sun.xml.writer.global'),('sxc','application/vnd.sun.xml.calc'),('stc','application/vnd.sun.xml.calc.template'),('sxi','application/vnd.sun.xml.impress'),('sti','application/vnd.sun.xml.impress.template'),('sxd','application/vnd.sun.xml.draw'),('std','application/vnd.sun.xml.draw.template'),('sxm','application/vnd.sun.xml.math'),('wpd','application/wordperfect');
/*!40000 ALTER TABLE `mimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monitored_file`
--

DROP TABLE IF EXISTS `monitored_file`;
CREATE TABLE `monitored_file` (
  `id` int(4) NOT NULL auto_increment,
  `userid` int(4) NOT NULL default '0',
  `fid` int(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `monitored_file`
--

LOCK TABLES `monitored_file` WRITE;
/*!40000 ALTER TABLE `monitored_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `monitored_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monitored_folder`
--

DROP TABLE IF EXISTS `monitored_folder`;
CREATE TABLE `monitored_folder` (
  `id` int(4) NOT NULL auto_increment,
  `userid` int(4) NOT NULL default '0',
  `fid` int(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `monitored_folder`
--

LOCK TABLES `monitored_folder` WRITE;
/*!40000 ALTER TABLE `monitored_folder` DISABLE KEYS */;
/*!40000 ALTER TABLE `monitored_folder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(4) NOT NULL auto_increment,
  `gid` int(4) NOT NULL default '0',
  `news_title` varchar(255) NOT NULL default '',
  `news_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `news` text NOT NULL,
  `news_end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `old_groups`
--

DROP TABLE IF EXISTS `old_groups`;
CREATE TABLE `old_groups` (
  `id` int(4) NOT NULL auto_increment,
  `name` char(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `old_groups`
--

LOCK TABLES `old_groups` WRITE;
/*!40000 ALTER TABLE `old_groups` DISABLE KEYS */;
INSERT INTO `old_groups` VALUES (0,'Administrators'),(3,'Profs'),(5,'classe1'),(6,'classe2'),(106,'Classe_LGT_02DE3'),(105,'Classe_LGT_02DE2'),(104,'Classe_LGT_02DE1');
/*!40000 ALTER TABLE `old_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `old_users`
--

DROP TABLE IF EXISTS `old_users`;
CREATE TABLE `old_users` (
  `id` int(4) NOT NULL auto_increment,
  `groupid` varchar(10) NOT NULL default '',
  `username` varchar(20) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  `quota_max` bigint(20) unsigned NOT NULL default '0',
  `quota_current` bigint(20) default NULL,
  `email` varchar(255) default NULL,
  `notify` int(4) default NULL,
  `attachfile` int(4) default NULL,
  `disabled` int(4) default NULL,
  `noprefaccess` int(4) default '0',
  `language` varchar(15) default NULL,
  `maxsessions` int(4) default '0',
  `lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `curlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastnews` int(4) NOT NULL default '0',
  `newsadmin` int(4) NOT NULL default '0',
  `comment_notify` int(4) NOT NULL default '0',
  `buttonstyle` varchar(255) default NULL,
  `homedir` int(4) default NULL,
  `firstdir` int(4) default NULL,
  `email_tool` int(4) default NULL,
  `change_paswd_at_login` int(4) default NULL,
  `login_failed` int(4) default NULL,
  `passwd_last_changed` datetime NOT NULL default '0000-00-00 00:00:00',
  `expire_account` varchar(80) default NULL,
  `user_auth` char(2) default NULL,
  `logintonewrec` int(4) default NULL,
  `groupadmin` int(4) default NULL,
  `user_offset` varchar(4) default NULL,
  `useradmin` int(4) default NULL,
  `viewlogs` int(4) default NULL,
  `viewreports` int(4) default NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `old_users`
--

LOCK TABLES `old_users` WRITE;
/*!40000 ALTER TABLE `old_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `old_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `owl_log`
--

DROP TABLE IF EXISTS `owl_log`;
CREATE TABLE `owl_log` (
  `id` int(4) NOT NULL auto_increment,
  `userid` int(4) default NULL,
  `filename` varchar(255) default NULL,
  `parent` int(4) default NULL,
  `action` varchar(40) default NULL,
  `details` text,
  `ip` varchar(16) default NULL,
  `agent` varchar(255) default NULL,
  `logdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `owl_log`
--

LOCK TABLES `owl_log` WRITE;
/*!40000 ALTER TABLE `owl_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `owl_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peerreview`
--

DROP TABLE IF EXISTS `peerreview`;
CREATE TABLE `peerreview` (
  `reviewer_id` int(4) NOT NULL default '0',
  `file_id` int(4) NOT NULL default '0',
  `status` int(4) NOT NULL default '0'
) ;

--
-- Dumping data for table `peerreview`
--

LOCK TABLES `peerreview` WRITE;
/*!40000 ALTER TABLE `peerreview` DISABLE KEYS */;
/*!40000 ALTER TABLE `peerreview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prefs`
--

DROP TABLE IF EXISTS `prefs`;
CREATE TABLE `prefs` (
  `id` int(4) NOT NULL auto_increment,
  `email_from` varchar(80) default NULL,
  `email_fromname` varchar(80) default NULL,
  `email_replyto` varchar(80) default NULL,
  `email_server` varchar(80) default NULL,
  `email_subject` varchar(60) default NULL,
  `lookathd` varchar(15) default NULL,
  `lookathddel` int(4) default NULL,
  `def_file_security` int(4) default NULL,
  `def_file_group_owner` int(4) default NULL,
  `def_file_owner` int(4) default NULL,
  `def_file_title` varchar(40) default NULL,
  `def_file_meta` varchar(40) default NULL,
  `def_fold_security` int(4) default NULL,
  `def_fold_group_owner` int(4) default NULL,
  `def_fold_owner` int(4) default NULL,
  `max_filesize` int(4) default NULL,
  `tmpdir` varchar(255) default NULL,
  `timeout` int(4) default NULL,
  `expand` int(4) default NULL,
  `version_control` int(4) default NULL,
  `restrict_view` int(4) default NULL,
  `hide_backup` int(4) default NULL,
  `dbdump_path` varchar(80) default NULL,
  `gzip_path` varchar(80) default NULL,
  `tar_path` varchar(80) default NULL,
  `unzip_path` varchar(80) default NULL,
  `pod2html_path` varchar(80) default NULL,
  `pdftotext_path` varchar(80) default NULL,
  `wordtotext_path` varchar(80) default NULL,
  `file_perm` int(4) default NULL,
  `folder_perm` int(4) default NULL,
  `logging` int(4) default NULL,
  `log_file` int(4) default NULL,
  `log_login` int(4) default NULL,
  `log_rec_per_page` int(4) default NULL,
  `rec_per_page` int(4) default NULL,
  `self_reg` int(4) default NULL,
  `self_reg_quota` int(4) default NULL,
  `self_reg_notify` int(4) default NULL,
  `self_reg_attachfile` int(4) default NULL,
  `self_reg_disabled` int(4) default NULL,
  `self_reg_noprefacces` int(4) default NULL,
  `self_reg_maxsessions` int(4) default NULL,
  `self_reg_group` int(4) default NULL,
  `anon_ro` int(4) default NULL,
  `anon_user` int(4) default NULL,
  `file_admin_group` int(4) default NULL,
  `forgot_pass` int(4) default NULL,
  `collect_trash` int(4) default NULL,
  `trash_can_location` varchar(80) default NULL,
  `allow_popup` int(4) default NULL,
  `allow_custpopup` int(5) default NULL,
  `status_bar_location` int(4) default NULL,
  `remember_me` int(4) default NULL,
  `cookie_timeout` int(4) default NULL,
  `use_smtp` int(4) default NULL,
  `use_smtp_auth` int(4) default NULL,
  `smtp_passwd` varchar(40) default NULL,
  `search_bar` int(4) default NULL,
  `bulk_buttons` int(4) default NULL,
  `action_buttons` int(4) default NULL,
  `folder_tools` int(4) default NULL,
  `pref_bar` int(4) default NULL,
  `smtp_auth_login` varchar(50) default NULL,
  `expand_disp_status` int(4) default NULL,
  `expand_disp_doc_num` int(4) default NULL,
  `expand_disp_doc_type` int(4) default NULL,
  `expand_disp_title` int(4) default NULL,
  `expand_disp_version` int(4) default NULL,
  `expand_disp_file` int(4) default NULL,
  `expand_disp_size` int(4) default NULL,
  `expand_disp_posted` int(4) default NULL,
  `expand_disp_modified` int(4) default NULL,
  `expand_disp_action` int(4) default NULL,
  `expand_disp_held` int(4) default NULL,
  `collapse_disp_status` int(4) default NULL,
  `collapse_disp_doc_num` int(4) default NULL,
  `collapse_disp_doc_type` int(4) default NULL,
  `collapse_disp_title` int(4) default NULL,
  `collapse_disp_version` int(4) default NULL,
  `collapse_disp_file` int(4) default NULL,
  `collapse_disp_size` int(4) default NULL,
  `collapse_disp_posted` int(4) default NULL,
  `collapse_disp_modified` int(4) default NULL,
  `collapse_disp_action` int(4) default NULL,
  `collapse_disp_held` int(4) default NULL,
  `expand_search_disp_score` int(4) default NULL,
  `expand_search_disp_folder_path` int(4) default NULL,
  `expand_search_disp_doc_type` int(4) default NULL,
  `expand_search_disp_file` int(4) default NULL,
  `expand_search_disp_size` int(4) default NULL,
  `expand_search_disp_posted` int(4) default NULL,
  `expand_search_disp_modified` int(4) default NULL,
  `expand_search_disp_action` int(4) default NULL,
  `collapse_search_disp_score` int(4) default NULL,
  `colps_search_disp_fld_path` int(4) default NULL,
  `collapse_search_disp_doc_type` int(4) default NULL,
  `collapse_search_disp_file` int(4) default NULL,
  `collapse_search_disp_size` int(4) default NULL,
  `collapse_search_disp_posted` int(4) default NULL,
  `collapse_search_disp_modified` int(4) default NULL,
  `collapse_search_disp_action` int(4) default NULL,
  `hide_folder_doc_count` int(4) default NULL,
  `old_action_icons` int(4) default NULL,
  `search_result_folders` int(4) default NULL,
  `restore_file_prefix` varchar(50) default NULL,
  `major_revision` int(4) default NULL,
  `minor_revision` int(4) default NULL,
  `doc_id_prefix` varchar(10) default NULL,
  `doc_id_num_digits` int(4) default NULL,
  `view_doc_in_new_window` int(4) default NULL,
  `admin_login_to_browse_page` int(4) default NULL,
  `save_keywords_to_db` int(4) default NULL,
  `self_reg_homedir` int(4) default NULL,
  `self_reg_firstdir` int(4) default NULL,
  `virus_path` varchar(80) default NULL,
  `peer_review` int(4) default NULL,
  `peer_opt` int(4) default NULL,
  `folder_size` int(4) default NULL,
  `download_folder_zip` int(4) default NULL,
  `display_password_override` int(4) default NULL,
  `thumb_disp_status` int(4) default NULL,
  `thumb_disp_doc_num` int(4) default NULL,
  `thumb_disp_image_info` int(4) default NULL,
  `thumb_disp_version` int(4) default NULL,
  `thumb_disp_size` int(4) default NULL,
  `thumb_disp_posted` int(4) default NULL,
  `thumb_disp_modified` int(4) default NULL,
  `thumb_disp_action` int(4) default NULL,
  `thumb_disp_held` int(4) default NULL,
  `thumbnails_tool_path` varchar(255) default NULL,
  `thumbnails_video_tool_path` varchar(255) default NULL,
  `thumbnails_video_tool_opt` varchar(255) default NULL,
  `thumbnails` int(4) default NULL,
  `thumbnails_small_width` int(4) default NULL,
  `thumbnails_med_width` int(4) default NULL,
  `thumbnails_large_width` int(4) default NULL,
  `thumbnail_view_columns` int(4) default NULL,
  `rtftotext_path` varchar(250) default NULL,
  `min_pass_length` int(4) default NULL,
  `min_username_length` int(4) default NULL,
  `min_pass_numeric` int(4) default NULL,
  `min_pass_special` int(4) default NULL,
  `enable_lock_account` int(4) default NULL,
  `lock_account_bad_password` int(4) default NULL,
  `track_user_passwords` int(4) default NULL,
  `change_password_every` int(4) default NULL,
  `folderdescreq` int(4) default NULL,
  `show_user_info` int(4) default NULL,
  `filedescreq` int(4) default NULL,
  `collapse_search_disp_doc_num` int(4) default NULL,
  `expand_search_disp_doc_num` int(4) default NULL,
  `colps_search_disp_doc_fields` int(4) default NULL,
  `expand_search_disp_doc_fields` int(4) default NULL,
  `collapse_disp_doc_fields` int(4) default NULL,
  `expand_disp_doc_fields` int(4) default NULL,
  `self_create_homedir` int(4) default NULL,
  `self_captcha` int(4) default NULL,
  `info_panel_wide` int(4) default NULL,
  `track_favorites` int(4) default NULL,
  `expand_disp_updated` int(4) default NULL,
  `collapse_disp_updated` int(4) default NULL,
  `expand_search_disp_updated` int(4) default NULL,
  `collapse_search_disp_updated` int(4) default NULL,
  `thumb_disp_updated` int(4) default NULL,
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `prefs`
--

LOCK TABLES `prefs` WRITE;
/*!40000 ALTER TABLE `prefs` DISABLE KEYS */;
INSERT INTO `prefs` VALUES (1,'owl@hostname','OWL','','lcs.hostname','[OWL] : MAIL AUTOMATIQUE','false',1,0,0,1,'<font color=red>No Info</font>','not in',0,0,1,51200000,'/tmp',600,1,1,0,0,'/usr/bin/mysqldump','/usr/bin/gzip','/bin/tar','/usr/bin/unzip','','/usr/bin/pdftotext','',0,0,1,1,1,25,0,0,0,0,0,0,0,0,1,2,2,0,0,0,'',1,1,1,0,30,1,0,'',2,1,1,1,1,'',1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,0,1,'0',1,0,'n° ',5,0,0,0,1,1,'',0,1,1,1,0,1,0,1,1,1,1,1,1,1,'/usr/bin/convert','/usr/local/bin/mplayer',' -vo png -ss 0:05 -frames 2 -nosound -really-quiet',1,25,50,100,4,'/usr/local/bin/unrtf',8,8,0,0,0,4,10,0,0,0,0,0,0,0,0,0,0,0,0,1,1,0,0,0,0,0);
/*!40000 ALTER TABLE `prefs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `searchidx`
--

DROP TABLE IF EXISTS `searchidx`;
CREATE TABLE `searchidx` (
  `wordid` int(4) default NULL,
  `owlfileid` int(4) default NULL,
  KEY `search_fileid` (`owlfileid`)
) ;

--
-- Dumping data for table `searchidx`
--

LOCK TABLES `searchidx` WRITE;
/*!40000 ALTER TABLE `searchidx` DISABLE KEYS */;
/*!40000 ALTER TABLE `searchidx` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trackoldpasswd`
--

DROP TABLE IF EXISTS `trackoldpasswd`;
CREATE TABLE `trackoldpasswd` (
  `id` int(4) NOT NULL auto_increment,
  `userid` int(4) NOT NULL default '0',
  `password` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `trackoldpasswd`
--

LOCK TABLES `trackoldpasswd` WRITE;
/*!40000 ALTER TABLE `trackoldpasswd` DISABLE KEYS */;
/*!40000 ALTER TABLE `trackoldpasswd` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(4) NOT NULL auto_increment,
  `groupid` varchar(10) NOT NULL default '',
  `username` varchar(20) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  `quota_max` bigint(20) unsigned NOT NULL default '0',
  `quota_current` bigint(20) default NULL,
  `email` varchar(255) default NULL,
  `notify` int(4) default NULL,
  `attachfile` int(4) default NULL,
  `disabled` int(4) default NULL,
  `noprefaccess` int(4) default '0',
  `language` varchar(15) default NULL,
  `maxsessions` int(4) default '0',
  `lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `curlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastnews` int(4) NOT NULL default '0',
  `newsadmin` int(4) NOT NULL default '0',
  `comment_notify` int(4) NOT NULL default '0',
  `buttonstyle` varchar(255) default NULL,
  `homedir` int(4) default NULL,
  `firstdir` int(4) default NULL,
  `email_tool` int(4) default NULL,
  `change_paswd_at_login` int(4) default NULL,
  `login_failed` int(4) default NULL,
  `passwd_last_changed` datetime NOT NULL default '0000-00-00 00:00:00',
  `expire_account` varchar(80) default NULL,
  `user_auth` char(2) default NULL,
  `logintonewrec` int(4) default NULL,
  `groupadmin` int(4) default NULL,
  `user_offset` varchar(4) default NULL,
  `useradmin` int(4) default NULL,
  `viewlogs` int(4) default NULL,
  `viewreports` int(4) default NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'0','admin','Administrator','d41d8cd98f00b204e9800998ecf8427e',0,0,'admin',1,0,0,0,'French',0,'2008-03-28 11:47:34','2008-03-28 12:02:44',8,0,1,'rsdx_blue1',1,4,1,0,0,'2005-04-10 22:28:40','','',0,0,1,0,0,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wordidx`
--

DROP TABLE IF EXISTS `wordidx`;
CREATE TABLE `wordidx` (
  `wordid` int(4) default NULL,
  `word` char(128) character set latin1  NOT NULL default '',
  UNIQUE KEY `word_index` (`word`)
) ;

--
-- Dumping data for table `wordidx`
--

LOCK TABLES `wordidx` WRITE;
/*!40000 ALTER TABLE `wordidx` DISABLE KEYS */;
/*!40000 ALTER TABLE `wordidx` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-03-28 11:36:43
