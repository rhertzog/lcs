

--
-- Base de donnees: `claro_plug`
--
CREATE DATABASE claro_plug;
USE claro_plug;
-- --------------------------------------------------------

--
-- Structure de la table `cl_category`
--

CREATE TABLE IF NOT EXISTS `cl_category` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `code` varchar(12) NOT NULL default '',
  `idParent` int(11) default '0',
  `rank` int(11) NOT NULL default '0',
  `visible` tinyint(1) NOT NULL default '1',
  `canHaveCoursesChild` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `cl_category`
--

INSERT INTO `cl_category` (`id`, `name`, `code`, `idParent`, `rank`, `visible`, `canHaveCoursesChild`) VALUES
(0, 'Root', 'ROOT', NULL, 0, 0, 0),
(2, 'Sciences', 'SC', 0, 1, 1, 1),
(3, 'Economics', 'ECO', 0, 2, 1, 1),
(4, 'Humanities', 'HUMA', 0, 3, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `cl_class`
--

CREATE TABLE IF NOT EXISTS `cl_class` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `class_parent_id` int(11) default NULL,
  `class_level` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='classe_id, name, classe_parent_id, classe_level' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_config_file`
--

CREATE TABLE IF NOT EXISTS `cl_config_file` (
  `config_code` varchar(30) NOT NULL default '',
  `config_hash` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`config_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=48;

--
-- Contenu de la table `cl_config_file`
--

INSERT INTO `cl_config_file` (`config_code`, `config_hash`) VALUES
('CLRSS', 'b01c49c19d5a52561d486bc7862d23e6'),
('CLSSO', 'aa74fe821d77f1ad0d9ea5afc6cf0f90'),
('CLICAL', 'c37302c16de0d9c3ba8e4fb4258e10db'),
('CLMSG', 'f066b2899663db129933199a727c5ebc'),
('CLGRP', 'f0ed96a318fe72116613ddec4d959683'),
('CLAUTH', '55f1c5634e41f0becbbc81fdd6a690b2'),
('CLKCACHE', '215ba1560113c91d0361e870b3bd8708'),
('CLMAIN', '0de6e0038cea26e41170812013a4e434'),
('CLPROFIL', '7c41f1f376925e457d5f92f577c17987'),
('CLHOME', 'c7a1b914da3924f3162c0212e66523fa'),
('CLCRS', '5ffa7310ef5bace082af80b58ba768c5'),
('CLCAS', '1fed0403188d5aa27d237d462769682b'),
('CLANN', '9cb48e37d368cb3cebe596f9e39cba14'),
('CLDOC', 'f9eb3bcec4227c996d978a3a3d226631'),
('CLQWZ', 'fceb41eda7b9e142c9a1b9b98a380bd5'),
('CLLNP', '245fa20ff5e76860ac5b9f14f682813b'),
('CLWRK', '1c2419f2bcd9d94910437556d868b254'),
('CLFRM', '7150765500a857314630e11e91366f9e'),
('CLUSR', 'c69aad9d24d6c7ada656565c31eb5805'),
('CLWIKI', '59a3ebe978d77d2fa647d4a004e4e50a'),
('CLCHAT', 'f44a58c2879c8b0974c4d813367ff700');

-- --------------------------------------------------------

--
-- Structure de la table `cl_cours`
--

CREATE TABLE IF NOT EXISTS `cl_cours` (
  `cours_id` int(11) NOT NULL auto_increment,
  `code` varchar(40) default NULL,
  `isSourceCourse` tinyint(4) NOT NULL default '0',
  `sourceCourseId` int(11) default NULL,
  `administrativeNumber` varchar(40) default NULL,
  `directory` varchar(20) default NULL,
  `dbName` varchar(40) default NULL,
  `language` varchar(15) default NULL,
  `intitule` varchar(250) default NULL,
  `titulaires` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `extLinkName` varchar(30) default NULL,
  `extLinkUrl` varchar(180) default NULL,
  `visibility` enum('visible','invisible') NOT NULL default 'visible',
  `access` enum('public','private','platform') NOT NULL default 'public',
  `registration` enum('open','close','validation') NOT NULL default 'open',
  `registrationKey` varchar(255) default NULL,
  `diskQuota` int(10) unsigned default NULL,
  `versionDb` varchar(250) NOT NULL default 'NEVER SET',
  `versionClaro` varchar(250) NOT NULL default 'NEVER SET',
  `lastVisit` datetime default NULL,
  `lastEdit` datetime default NULL,
  `creationDate` datetime default NULL,
  `expirationDate` datetime default NULL,
  `defaultProfileId` int(11) NOT NULL,
  `status` enum('enable','pending','disable','trash','date') NOT NULL default 'enable',
  `userLimit` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cours_id`),
  KEY `administrativeNumber` (`administrativeNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='data of courses' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_coursehomepage_portlet`
--

CREATE TABLE IF NOT EXISTS `cl_coursehomepage_portlet` (
  `label` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`label`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `cl_coursehomepage_portlet`
--

INSERT INTO `cl_coursehomepage_portlet` (`label`, `name`) VALUES
('CLTI', 'Headlines'),
('CLCAL', 'Calendar'),
('CLANN', 'Announcements');

-- --------------------------------------------------------

--
-- Structure de la table `cl_course_tool`
--

CREATE TABLE IF NOT EXISTS `cl_course_tool` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `claro_label` varchar(8) NOT NULL default '',
  `script_url` varchar(255) NOT NULL default '',
  `icon` varchar(255) default NULL,
  `def_access` enum('ALL','COURSE_MEMBER','GROUP_MEMBER','GROUP_TUTOR','COURSE_ADMIN','PLATFORM_ADMIN') NOT NULL default 'ALL',
  `def_rank` int(10) unsigned default NULL,
  `add_in_course` enum('MANUAL','AUTOMATIC') NOT NULL default 'AUTOMATIC',
  `access_manager` enum('PLATFORM_ADMIN','COURSE_ADMIN') NOT NULL default 'COURSE_ADMIN',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `claro_label` (`claro_label`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='based definiton of the claroline tool used in each course' AUTO_INCREMENT=13 ;

--
-- Contenu de la table `cl_course_tool`
--

INSERT INTO `cl_course_tool` (`id`, `claro_label`, `script_url`, `icon`, `def_access`, `def_rank`, `add_in_course`, `access_manager`) VALUES
(1, 'CLDSC', 'index.php', 'icon.png', 'ALL', 1, 'AUTOMATIC', 'COURSE_ADMIN'),
(2, 'CLCAL', 'agenda.php', 'icon.png', 'ALL', 2, 'AUTOMATIC', 'COURSE_ADMIN'),
(3, 'CLANN', 'announcements.php', 'icon.png', 'ALL', 3, 'AUTOMATIC', 'COURSE_ADMIN'),
(4, 'CLDOC', 'document.php', 'icon.png', 'ALL', 4, 'AUTOMATIC', 'COURSE_ADMIN'),
(5, 'CLQWZ', 'exercise.php', 'icon.png', 'ALL', 5, 'AUTOMATIC', 'COURSE_ADMIN'),
(6, 'CLLNP', 'learningPathList.php', 'icon.png', 'ALL', 6, 'AUTOMATIC', 'COURSE_ADMIN'),
(7, 'CLWRK', 'work.php', 'icon.png', 'ALL', 7, 'AUTOMATIC', 'COURSE_ADMIN'),
(8, 'CLFRM', 'index.php', 'icon.png', 'ALL', 8, 'AUTOMATIC', 'COURSE_ADMIN'),
(9, 'CLGRP', 'group.php', 'icon.png', 'ALL', 9, 'AUTOMATIC', 'COURSE_ADMIN'),
(10, 'CLUSR', 'user.php', 'icon.png', 'ALL', 10, 'AUTOMATIC', 'COURSE_ADMIN'),
(11, 'CLWIKI', 'wiki.php', 'icon.png', 'ALL', 11, 'AUTOMATIC', 'COURSE_ADMIN'),
(12, 'CLCHAT', 'index.php', 'icon.png', 'ALL', 12, 'AUTOMATIC', 'COURSE_ADMIN');

-- --------------------------------------------------------

--
-- Structure de la table `cl_desktop_portlet`
--

CREATE TABLE IF NOT EXISTS `cl_desktop_portlet` (
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `rank` int(11) NOT NULL,
  `visibility` enum('visible','invisible') NOT NULL default 'visible',
  `activated` int(11) NOT NULL default '1',
  PRIMARY KEY  (`label`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `cl_desktop_portlet_data`
--

CREATE TABLE IF NOT EXISTS `cl_desktop_portlet_data` (
  `id` int(11) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL,
  `idUser` int(11) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_dock`
--

CREATE TABLE IF NOT EXISTS `cl_dock` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `module_id` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `rank` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_event_resource`
--

CREATE TABLE IF NOT EXISTS `cl_event_resource` (
  `event_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `tool_id` int(11) NOT NULL,
  `course_code` varchar(40) NOT NULL,
  PRIMARY KEY  (`event_id`,`resource_id`,`tool_id`,`course_code`),
  UNIQUE KEY `event_id` (`event_id`,`course_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `cl_im_message`
--

CREATE TABLE IF NOT EXISTS `cl_im_message` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `send_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `course` varchar(40) default NULL,
  `group` int(11) default NULL,
  `tools` char(8) default NULL,
  PRIMARY KEY  (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_im_message_status`
--

CREATE TABLE IF NOT EXISTS `cl_im_message_status` (
  `user_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `is_read` tinyint(4) NOT NULL default '0',
  `is_deleted` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `cl_im_recipient`
--

CREATE TABLE IF NOT EXISTS `cl_im_recipient` (
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sent_to` enum('toUser','toGroup','toCourse','toAll') NOT NULL,
  PRIMARY KEY  (`message_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `cl_log`
--

CREATE TABLE IF NOT EXISTS `cl_log` (
  `id` int(11) NOT NULL auto_increment,
  `course_code` varchar(40) default NULL,
  `tool_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `ip` varchar(15) default NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` varchar(60) NOT NULL default '',
  `data` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_code`),
  KEY `user_log` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_module`
--

CREATE TABLE IF NOT EXISTS `cl_module` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `label` varchar(8) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `activation` enum('activated','desactivated') NOT NULL default 'desactivated',
  `type` varchar(10) NOT NULL default 'applet',
  `script_url` char(255) NOT NULL default 'entry.php',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `cl_module`
--

INSERT INTO `cl_module` (`id`, `label`, `name`, `activation`, `type`, `script_url`) VALUES
(1, 'CLDSC', 'Course description', 'activated', 'tool', 'index.php'),
(2, 'CLCAL', 'Agenda', 'activated', 'tool', 'agenda.php'),
(3, 'CLANN', 'Announcements', 'activated', 'tool', 'announcements.php'),
(4, 'CLDOC', 'Documents and Links', 'activated', 'tool', 'document.php'),
(5, 'CLQWZ', 'Exercises', 'activated', 'tool', 'exercise.php'),
(6, 'CLLNP', 'Learning path', 'activated', 'tool', 'learningPathList.php'),
(7, 'CLWRK', 'Assignments', 'activated', 'tool', 'work.php'),
(8, 'CLFRM', 'Forums', 'activated', 'tool', 'index.php'),
(9, 'CLGRP', 'Groups', 'activated', 'tool', 'group.php'),
(10, 'CLUSR', 'Users', 'activated', 'tool', 'user.php'),
(11, 'CLWIKI', 'Wiki', 'activated', 'tool', 'wiki.php'),
(12, 'CLCHAT', 'Chat', 'activated', 'tool', 'index.php');

-- --------------------------------------------------------

--
-- Structure de la table `cl_module_contexts`
--

CREATE TABLE IF NOT EXISTS `cl_module_contexts` (
  `module_id` int(10) unsigned NOT NULL,
  `context` varchar(60) NOT NULL default 'course',
  PRIMARY KEY  (`module_id`,`context`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `cl_module_contexts`
--

INSERT INTO `cl_module_contexts` (`module_id`, `context`) VALUES
(1, 'course'),
(2, 'course'),
(3, 'course'),
(4, 'course'),
(4, 'group'),
(5, 'course'),
(6, 'course'),
(7, 'course'),
(8, 'course'),
(8, 'group'),
(9, 'course'),
(10, 'course'),
(11, 'course'),
(11, 'group'),
(12, 'course'),
(12, 'group');

-- --------------------------------------------------------

--
-- Structure de la table `cl_module_info`
--

CREATE TABLE IF NOT EXISTS `cl_module_info` (
  `id` smallint(6) NOT NULL auto_increment,
  `module_id` smallint(6) NOT NULL default '0',
  `version` varchar(10) NOT NULL default '',
  `author` varchar(50) default NULL,
  `author_email` varchar(100) default NULL,
  `author_website` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `website` varchar(255) default NULL,
  `license` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `cl_module_info`
--

INSERT INTO `cl_module_info` (`id`, `module_id`, `version`, `author`, `author_email`, `author_website`, `description`, `website`, `license`) VALUES
(1, 1, '1.9', 'Claro team', 'devteam@claroline.net', 'http://www.claroline.net/', '\n  ', 'http://wiki.claroline.net/index.php/CLDSC', 'GPL'),
(2, 2, '3.0', 'Claro team', 'devteam@claroline.net', 'http://www.claroline.net/', '\n  ', 'http://wiki.claroline.net/index.php/CLCAL', 'GPL'),
(3, 3, '3.0', 'Claro team', 'devteam@claroline.net', 'http://www.claroline.net/', '\n  ', 'http://wiki.claroline.net/index.php/CLANN', 'GPL'),
(4, 4, '4.0', 'Claro team', 'devteam@claroline.net', 'http://www.claroline.net/', '\n     This tool is an original tool of claroline\n     It''s able to store and manage local ressoures like file, url.\n     Can  manage upload, zip, images, url, subdirectory\n     Ca edit html files\n  ', 'http://wiki.claroline.net/index.php/CLDOC', 'GPL'),
(5, 5, '1.8', 'Claro team', 'devteam@claroline.net', 'http://www.claroline.net/', '\n  ', 'http://wiki.claroline.net/index.php/CLQWZ', 'GPL'),
(6, 6, '1.0', 'Claro team', 'devteam@claroline.net', 'http://www.claroline.net/', '\n  ', 'http://www.claroline.net/wiki/CLLNP/', 'GPL'),
(7, 7, '1.8', 'Claro team', 'devteam@claroline.net', 'http://www.claroline.net/', '\n  ', '', 'GPL'),
(8, 8, '1.8', 'Claro team', 'devteam@claroline.net', 'http://www.claroline.net/', '\n  ', 'http://wiki.claroline.net/index.php/CLFRM', 'GPL'),
(9, 9, '1.8', 'Claro team', 'devteam@claroline.net', 'http://www.claroline.net/', '\n        This tool allows group-based activities and group management in Claroline\n    ', 'http://wiki.claroline.net/index.php/CLGRP', 'GPL'),
(10, 10, '4.0', 'Claro team', 'devteam@claroline.net', 'http://www.claroline.net/', '\n  ', 'http://wiki.claroline.net/index.php/CLUSR', 'GPL'),
(11, 11, '2.0', 'Frederic Minne', 'zefredz@claroline.net', 'http://wiki.claroline.net/index.php/CLWIKI', '\n     This is the original Wiki tool for the Claroline platform. It allows\n     online collaborative edition of web pages using a simplified Wiki\n     syntax based on Olivier Meunier''s wiki2xhtml renderer from the\n     Dotclear blog project.\n  ', 'http://wiki.claroline.net/index.php/CLWIKI', 'GPL'),
(12, 12, '1.0', 'Sebastien Piraux', 'seb@claroline.net', 'http://www.claroline.net', '\n    \n  ', '', 'GPL');

-- --------------------------------------------------------

--
-- Structure de la table `cl_notify`
--

CREATE TABLE IF NOT EXISTS `cl_notify` (
  `id` int(11) NOT NULL auto_increment,
  `course_code` varchar(40) NOT NULL default '0',
  `tool_id` int(11) NOT NULL default '0',
  `ressource_id` varchar(255) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `date` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_property_definition`
--

CREATE TABLE IF NOT EXISTS `cl_property_definition` (
  `propertyId` varchar(50) NOT NULL default '',
  `contextScope` varchar(10) NOT NULL default '',
  `label` varchar(50) NOT NULL default '',
  `type` varchar(10) NOT NULL default '',
  `defaultValue` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `required` tinyint(1) NOT NULL default '0',
  `rank` int(10) unsigned NOT NULL default '0',
  `acceptedValue` text NOT NULL,
  PRIMARY KEY  (`contextScope`,`propertyId`),
  KEY `rank` (`rank`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `cl_rel_class_user`
--

CREATE TABLE IF NOT EXISTS `cl_rel_class_user` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `class_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `class_id` (`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_rel_course_category`
--

CREATE TABLE IF NOT EXISTS `cl_rel_course_category` (
  `courseId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `rootCourse` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`courseId`,`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `cl_rel_course_class`
--

CREATE TABLE IF NOT EXISTS `cl_rel_course_class` (
  `courseId` varchar(40) NOT NULL,
  `classId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`courseId`,`classId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `cl_rel_course_portlet`
--

CREATE TABLE IF NOT EXISTS `cl_rel_course_portlet` (
  `id` int(11) NOT NULL auto_increment,
  `courseId` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `courseId` (`courseId`,`label`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_rel_course_user`
--

CREATE TABLE IF NOT EXISTS `cl_rel_course_user` (
  `code_cours` varchar(40) NOT NULL default '0',
  `user_id` int(11) unsigned NOT NULL default '0',
  `profile_id` int(11) NOT NULL,
  `role` varchar(60) default NULL,
  `team` int(11) NOT NULL default '0',
  `tutor` int(11) NOT NULL default '0',
  `count_user_enrol` int(11) NOT NULL default '0',
  `count_class_enrol` int(11) NOT NULL default '0',
  `isPending` tinyint(4) NOT NULL default '0',
  `isCourseManager` tinyint(4) NOT NULL default '0',
  `enrollment_date` datetime default NULL,
  PRIMARY KEY  (`code_cours`,`user_id`),
  KEY `isCourseManager` (`isCourseManager`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `cl_right_action`
--

CREATE TABLE IF NOT EXISTS `cl_right_action` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` varchar(255) default '',
  `tool_id` int(11) default NULL,
  `rank` int(11) default '0',
  `type` enum('COURSE','PLATFORM') NOT NULL default 'COURSE',
  PRIMARY KEY  (`id`),
  KEY `tool_id` (`tool_id`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Contenu de la table `cl_right_action`
--

INSERT INTO `cl_right_action` (`id`, `name`, `description`, `tool_id`, `rank`, `type`) VALUES
(1, 'read', '', 1, 0, 'COURSE'),
(2, 'edit', '', 1, 0, 'COURSE'),
(3, 'read', '', 2, 0, 'COURSE'),
(4, 'edit', '', 2, 0, 'COURSE'),
(5, 'read', '', 3, 0, 'COURSE'),
(6, 'edit', '', 3, 0, 'COURSE'),
(7, 'read', '', 4, 0, 'COURSE'),
(8, 'edit', '', 4, 0, 'COURSE'),
(9, 'read', '', 5, 0, 'COURSE'),
(10, 'edit', '', 5, 0, 'COURSE'),
(11, 'read', '', 6, 0, 'COURSE'),
(12, 'edit', '', 6, 0, 'COURSE'),
(13, 'read', '', 7, 0, 'COURSE'),
(14, 'edit', '', 7, 0, 'COURSE'),
(15, 'read', '', 8, 0, 'COURSE'),
(16, 'edit', '', 8, 0, 'COURSE'),
(17, 'read', '', 9, 0, 'COURSE'),
(18, 'edit', '', 9, 0, 'COURSE'),
(19, 'read', '', 10, 0, 'COURSE'),
(20, 'edit', '', 10, 0, 'COURSE'),
(21, 'read', '', 11, 0, 'COURSE'),
(22, 'edit', '', 11, 0, 'COURSE'),
(23, 'read', '', 12, 0, 'COURSE'),
(24, 'edit', '', 12, 0, 'COURSE');

-- --------------------------------------------------------

--
-- Structure de la table `cl_right_profile`
--

CREATE TABLE IF NOT EXISTS `cl_right_profile` (
  `profile_id` int(11) NOT NULL auto_increment,
  `type` enum('COURSE','PLATFORM') NOT NULL default 'COURSE',
  `name` varchar(255) NOT NULL default '',
  `label` varchar(50) NOT NULL default '',
  `description` varchar(255) default '',
  `courseManager` tinyint(4) default '0',
  `mailingList` tinyint(4) default '0',
  `userlistPublic` tinyint(4) default '0',
  `groupTutor` tinyint(4) default '0',
  `locked` tinyint(4) default '0',
  `required` tinyint(4) default '0',
  PRIMARY KEY  (`profile_id`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `cl_right_profile`
--

INSERT INTO `cl_right_profile` (`profile_id`, `type`, `name`, `label`, `description`, `courseManager`, `mailingList`, `userlistPublic`, `groupTutor`, `locked`, `required`) VALUES
(1, 'COURSE', 'Anonymous', 'anonymous', 'Course visitor (the user has no account on the platform)', 0, 1, 1, 0, 0, 1),
(2, 'COURSE', 'Guest', 'guest', 'Course visitor (the user has an account on the platform, but is not enrolled in the course)', 0, 1, 1, 0, 0, 1),
(3, 'COURSE', 'User', 'user', 'Course member (the user is actually enrolled in the course)', 0, 1, 1, 0, 0, 1),
(4, 'COURSE', 'Manager', 'manager', 'Course Administrator', 1, 1, 1, 0, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `cl_right_rel_profile_action`
--

CREATE TABLE IF NOT EXISTS `cl_right_rel_profile_action` (
  `profile_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `courseId` varchar(40) NOT NULL default '',
  `value` tinyint(4) default '0',
  PRIMARY KEY  (`profile_id`,`action_id`,`courseId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `cl_right_rel_profile_action`
--

INSERT INTO `cl_right_rel_profile_action` (`profile_id`, `action_id`, `courseId`, `value`) VALUES
(1, 24, '', 0),
(2, 24, '', 0),
(3, 24, '', 0),
(4, 24, '', 1),
(1, 23, '', 1),
(2, 23, '', 1),
(3, 23, '', 1),
(4, 23, '', 1),
(1, 22, '', 0),
(1, 21, '', 1),
(2, 22, '', 0),
(2, 21, '', 1),
(3, 22, '', 0),
(3, 21, '', 1),
(4, 22, '', 1),
(1, 20, '', 0),
(1, 19, '', 1),
(1, 18, '', 0),
(2, 20, '', 0),
(2, 19, '', 1),
(3, 20, '', 0),
(3, 19, '', 1),
(4, 21, '', 1),
(1, 17, '', 1),
(1, 16, '', 0),
(1, 15, '', 1),
(2, 18, '', 0),
(2, 17, '', 1),
(3, 18, '', 0),
(3, 17, '', 1),
(3, 16, '', 0),
(4, 20, '', 1),
(4, 19, '', 1),
(4, 18, '', 1),
(4, 17, '', 1),
(1, 14, '', 0),
(1, 13, '', 1),
(2, 16, '', 0),
(2, 15, '', 1),
(2, 14, '', 0),
(3, 15, '', 1),
(3, 14, '', 0),
(3, 13, '', 1),
(3, 11, '', 1),
(4, 16, '', 1),
(4, 15, '', 1),
(4, 14, '', 1),
(4, 13, '', 1),
(1, 11, '', 1),
(1, 12, '', 0),
(2, 13, '', 1),
(2, 11, '', 1),
(2, 12, '', 0),
(2, 10, '', 0),
(2, 9, '', 1),
(3, 12, '', 0),
(4, 11, '', 1),
(4, 12, '', 1),
(1, 10, '', 0),
(1, 9, '', 1),
(2, 8, '', 0),
(3, 10, '', 0),
(3, 9, '', 1),
(4, 10, '', 1),
(4, 9, '', 1),
(1, 8, '', 0),
(1, 7, '', 1),
(2, 7, '', 1),
(3, 8, '', 0),
(3, 7, '', 1),
(4, 8, '', 1),
(4, 7, '', 1),
(1, 6, '', 0),
(1, 5, '', 1),
(2, 6, '', 0),
(2, 5, '', 1),
(3, 6, '', 0),
(3, 5, '', 1),
(4, 6, '', 1),
(4, 5, '', 1),
(1, 4, '', 0),
(1, 3, '', 1),
(2, 4, '', 0),
(2, 3, '', 1),
(3, 4, '', 0),
(3, 3, '', 1),
(4, 4, '', 1),
(4, 3, '', 1),
(1, 2, '', 0),
(1, 1, '', 1),
(2, 2, '', 0),
(2, 1, '', 1),
(3, 2, '', 0),
(3, 1, '', 1),
(4, 2, '', 1),
(4, 1, '', 1);

-- --------------------------------------------------------

--
-- Structure de la table `cl_sso`
--

CREATE TABLE IF NOT EXISTS `cl_sso` (
  `id` int(11) NOT NULL auto_increment,
  `cookie` varchar(255) NOT NULL default '',
  `rec_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_tracking_event`
--

CREATE TABLE IF NOT EXISTS `cl_tracking_event` (
  `id` int(11) NOT NULL auto_increment,
  `course_code` varchar(40) default NULL,
  `tool_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` varchar(60) NOT NULL default '',
  `data` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_code`),
  KEY `user_tracking` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_upgrade_status`
--

CREATE TABLE IF NOT EXISTS `cl_upgrade_status` (
  `id` int(11) NOT NULL auto_increment,
  `cid` varchar(40) NOT NULL,
  `claro_label` varchar(8) default NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `cl_user`
--

CREATE TABLE IF NOT EXISTS `cl_user` (
  `user_id` int(11) unsigned NOT NULL auto_increment,
  `nom` varchar(60) default NULL,
  `prenom` varchar(60) default NULL,
  `username` varchar(20) default 'empty',
  `password` varchar(50) default 'empty',
  `language` varchar(15) default NULL,
  `authSource` varchar(50) default 'claroline',
  `email` varchar(255) default NULL,
  `officialCode` varchar(255) default NULL,
  `officialEmail` varchar(255) default NULL,
  `phoneNumber` varchar(30) default NULL,
  `pictureUri` varchar(250) default NULL,
  `creatorId` int(11) unsigned default NULL,
  `isPlatformAdmin` tinyint(4) default '0',
  `isCourseCreator` tinyint(4) default '0',
  PRIMARY KEY  (`user_id`),
  KEY `loginpass` (`username`,`password`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `cl_user`
--

INSERT INTO `cl_user` (`user_id`, `nom`, `prenom`, `username`, `password`, `language`, `authSource`, `email`, `officialCode`, `officialEmail`, `phoneNumber`, `pictureUri`, `creatorId`, `isPlatformAdmin`, `isCourseCreator`) VALUES
(1, 'lcs', 'admin', 'admin', 'toto', '', 'CAS', 'admin@#DOMAIN#', '', '', '', NULL, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `cl_user_property`
--

CREATE TABLE IF NOT EXISTS `cl_user_property` (
  `userId` int(10) unsigned NOT NULL default '0',
  `propertyId` varchar(255) NOT NULL default '',
  `propertyValue` varchar(255) NOT NULL default '',
  `scope` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`scope`,`propertyId`,`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




GRANT SELECT,UPDATE,DELETE,INSERT,ALTER,CREATE,DROP,INDEX,CREATE TEMPORARY TABLEs,LOCK TABLES ON claro_plug.* TO claro_user@localhost IDENTIFIED BY 
'#DBPWD#';
