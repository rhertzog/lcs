-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mar 07 Octobre 2008 à 11:56
-- Version du serveur: 5.0.32
-- Version de PHP: 5.2.0-8+etch11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `monlcs_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `ml_acad_propose`
--

CREATE TABLE IF NOT EXISTS `ml_acad_propose` (
  `id` int(11) NOT NULL auto_increment,
  `jeton` varchar(40) NOT NULL,
  `etab` varchar(255) NOT NULL,
  `uid` varchar(10) NOT NULL,
  `id_ress` int(11) NOT NULL,
  `type` enum('ress','scen') NOT NULL default 'scen',
  `menu` varchar(128) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ml_acad_propose`
--


-- --------------------------------------------------------

--
-- Structure de la table `ml_droits`
--

CREATE TABLE IF NOT EXISTS `ml_droits` (
  `id` int(11) NOT NULL auto_increment,
  `groupe` enum('P','E','A','G','M') NOT NULL default 'P',
  `propose` varchar(4) default NULL,
  `impose` varchar(4) default NULL,
  `cible` varchar(128) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `ml_droits`
--

REPLACE INTO `ml_droits` (`id`, `groupe`, `propose`, `impose`, `cible`) VALUES
(1, 'P', 'PE', 'E', 'ldap_depend'),
(2, 'A', 'PEGA', 'PEG', 'all'),
(3, 'G', NULL, NULL, NULL),
(4, 'E', NULL, NULL, NULL),
(5, 'M', 'PEGA', 'PEGA', 'all');

-- --------------------------------------------------------

--
-- Structure de la table `ml_geometry`
--

CREATE TABLE IF NOT EXISTS `ml_geometry` (
  `id` int(11) NOT NULL auto_increment,
  `id_menu` int(11) NOT NULL default '0',
  `id_ressource` int(11) NOT NULL default '0',
  `user` varchar(128) NOT NULL default '',
  `x` int(11) NOT NULL default '0',
  `y` int(11) NOT NULL default '0',
  `z` int(11) default '50',
  `w` int(11) NOT NULL default '0',
  `h` int(11) NOT NULL default '0',
  `min` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=133 ;

--
-- Contenu de la table `ml_geometry`
--

REPLACE INTO `ml_geometry` (`id`, `id_menu`, `id_ressource`, `user`, `x`, `y`, `z`, `w`, `h`, `min`) VALUES
(3, 9, 36, 'skel_user', 5, 60, 50, 370, 136, 'N'),
(4, 11, 47, 'skel_user', 5, 60, 50, 291, 243, 'N'),
(5, 3, 122, 'skel_user', 8, 58, 50, 400, 250, 'N'),
(130, 1, 192, 'boss', 170, 318, 50, 526, 236, 'N'),
(131, 1, 2, 'boss', 9, 62, 50, 155, 487, 'N'),
(129, 165, 47, 'admin', 5, 60, 50, 275, 233, 'N'),
(50, 9, 36, 'admin', 5, 60, 50, 370, 136, 'N'),
(116, 1, 192, 'skel_user', 170, 318, 50, 526, 236, 'N'),
(132, 1, 193, 'boss', 172, 65, 50, 525, 207, 'N'),
(118, 1, 193, 'skel_user', 172, 65, 50, 525, 207, 'N'),
(85, 12, 84, 'skel_user', 6, 56, 50, 441, 326, 'N'),
(78, 91, 2, 'admin', 5, 60, 50, 225, 267, 'N'),
(80, 98, 2, 'skel_user', 5, 60, 50, 162, 280, 'N'),
(117, 1, 2, 'skel_user', 9, 62, 50, 155, 487, 'N'),
(96, 165, 47, 'skel_user', 5, 60, 50, 275, 233, 'N');

-- --------------------------------------------------------

--
-- Structure de la table `ml_notes`
--

CREATE TABLE IF NOT EXISTS `ml_notes` (
  `id` int(11) NOT NULL auto_increment,
  `menu` varchar(128) NOT NULL default '0',
  `cible` varchar(255) default NULL,
  `titre` varchar(125) NOT NULL default 'Ma note',
  `x` int(11) NOT NULL default '10',
  `y` int(11) NOT NULL default '10',
  `z` int(11) default '50',
  `w` int(11) NOT NULL default '300',
  `h` int(11) NOT NULL default '200',
  `min` enum('Y','N') NOT NULL default 'N',
  `setter` varchar(128) NOT NULL default 'admin',
  `msg` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=62 ;

--
-- Contenu de la table `ml_notes`
--

REPLACE INTO `ml_notes` (`id`, `menu`, `cible`, `titre`, `x`, `y`, `z`, `w`, `h`, `min`, `setter`, `msg`) VALUES
(3, 'scenario_choix', '', 'Ma note', 376, 57, 50, 218, 166, 'N', 'tacks', '<p><strong>CONSIGNES DE TRAVAIL</strong></p><hr width="100%" size="2" /><div align="center"><ol><li><strong>Calculer la vitesse moyenne de la plus grosse bulle.</strong></li></ol></div><p align="center"><img alt="" src="/monlcs/fckeditor/editor/images/smiley/msn/teeth_smile.gif" /> Bon courage.</p><p>&nbsp;</p>'),
(11, 'scenario_choix', '', 'Instructions.', 640, 55, 50, 452, 280, 'N', 'admin', '<div style="padding: 15px; background-color: rgb(102, 102, 255);"><p><strong>MISSION IMPOSSIBLE</strong></p> <hr width="100%" size="2" /> <ol>     <li>Votre mission, si vous l''acceptez, sera de retrouver la tour Eiffel, a l''aide de Google Maps.</li>     <li>Cherchez ensuite le chateau de Versailles.</li>     <li>Ensuite rendez au CRDP.</li>     <li>Les meilleurs doivent trouver les pyramides de Giseh. (INDICE &agrave; l''Ouest du Caire route n&deg; 10)</li>     <li>E.T. doit trouver sa maison.</li> </ol> <blockquote> <p>Bon courage <img src="/monlcs/fckeditor/editor/images/smiley/msn/teeth_smile.gif" alt="" />.</p> </blockquote> <p><strong><br /> </strong></p></div>'),
(14, 'scenario_choix', '', 'Consignes', 6, 319, 50, 369, 138, '', 'admin', '<p><strong>Pistes de travail <br /></strong></p><hr width="100%" size="2" /><ol><li>A</li><li>B</li></ol><p>&nbsp;</p>'),
(15, 'scenario_choix', '', 'Consignes', 522, 56, 50, 290, 125, '', 'tacks', '<div style="padding: 15px;"><p><u><strong>TRAVAIL</strong></u></p> <p>A l''aide de l''animation ci-contre :</p> <ol>     <li>D&eacute;crire le r&ocirc;le du rapporteur.</li> </ol></div>');

-- --------------------------------------------------------

--
-- Structure de la table `ml_ressources`
--

CREATE TABLE IF NOT EXISTS `ml_ressources` (
  `id` int(11) NOT NULL auto_increment,
  `titre` varchar(125) NOT NULL default '',
  `url` text NOT NULL,
  `RSS_template` varchar(125) NOT NULL default '',
  `owner` varchar(125) NOT NULL default 'admin',
  `statut` enum('private','public') NOT NULL default 'private',
  `ajoutee_le` date NOT NULL default '2008-04-01',
  `url_vignette` text,
  `descr` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=202 ;

--
-- Contenu de la table `ml_ressources`
--

REPLACE INTO `ml_ressources` (`id`, `titre`, `url`, `RSS_template`, `owner`, `statut`, `ajoutee_le`, `url_vignette`, `descr`) VALUES
(1, 'Mon LCS Accueil', '../lcs/statandgo.php?use=Accueil2', 'null', 'admin', 'private', '2008-04-01', NULL, 'Page d''accueil de MonLCS.'),
(2, 'Bulles', '../monlcs/modules/bulles.html', 'null', 'admin', 'public', '2008-04-01', NULL, 'Animation DHTML futilitaire.'),
(9, 'Meuh', '../monlcs/modules/boitam.eu.v3.rose.swf', 'null', 'admin', 'private', '2008-04-01', NULL, 'Meuh'),
(12, 'Livre num&eacute;rique ', '../monlcs/modules/Didapages/index.html', 'null', 'admin', 'private', '2008-04-01', NULL, 'Livre ecrit avec l''outil Didapages.'),
(67, 'Serpents et Echelles', '..//monlcs/modules/test.htm', 'null', 'admin', 'private', '2008-04-01', NULL, 'Exemple de jeu serpents et echelles.'),
(30, 'Prog', 'http://www.lamoooche.com/getRSS.php?idnews=241', 'RSS_no_img', 'admin', 'private', '2008-04-01', NULL, NULL),
(25, 'Wikip&eacute;dia ', 'http://fr.wikipedia.org/wiki/Accueil', 'null', 'admin', 'public', '2008-04-01', NULL, 'Lien vers Wikipedia.'),
(34, 'La M&eacute;t&eacute;o ', '../monlcs/modules/meteo.html', 'null', 'admin', 'private', '2008-04-01', NULL, 'Widget M&eacute;t&eacute;o.'),
(35, 'D&eacute;veloppment durable ', 'http://www2.ac-rennes.fr/eedd/actu/YAB.htm', 'null', 'admin', 'public', '2008-04-01', NULL, 'Photographies concernant le developpement durable.'),
(36, 'Calculatrice', '../monlcs/modules/pcalc.html', 'null', 'admin', 'private', '2008-04-01', NULL, 'Widget Calculatrice'),
(37, 'Horloge', '../monlcs/modules/clock.html', 'null', 'admin', 'private', '2008-04-01', NULL, 'Widget Horloge.'),
(38, 'Le Monde', 'http://www.lamoooche.com/getRSS.php?idnews=5', 'RSS_no_img', 'admin', 'public', '2008-04-01', NULL, NULL),
(122, 'Nomenclature de Mendeljev ', 'http://www.cite-sciences.fr/francais/ala_cite/expo/tempo/aluminium/science/mendeleiev/mendeleiev.swf', 'null', 'admin', 'private', '2008-04-01', NULL, 'Tableau de Mendeljev des elements chimiques.'),
(40, 'RSS le monde', 'http://www.lemonde.fr/rss/sequence/0,2-651865,1-0,0.xml', 'RSS_img', 'admin', 'public', '2008-04-01', NULL, NULL),
(95, 'Environnement', 'http://www.lemonde.fr/rss/sequence/0,2-959155,1-0,0.xml', 'RSS_no_img', 'admin', 'public', '2008-04-01', NULL, 'Fil RSS environnement.'),
(42, 'SuDoKu', '../monlcs/modules/sudoku.html', 'null', 'admin', 'private', '2008-04-01', NULL, 'Jeu du SuDoKu.'),
(43, 'Acad&eacute;mie de caen ', 'http://www.ac-caen.fr/fluxRSS.xml', 'RSS_no_img', 'admin', 'public', '2008-04-01', NULL, 'Fil RSS academique'),
(45, 'Solitaire', '../monlcs/modules/game_solitaire/game_solitaire.html', 'null', 'admin', 'private', '2008-04-01', NULL, 'Jeu solitaire.'),
(47, 'Echecs', '../monlcs/modules/chess/index.php', 'null', 'admin', 'private', '2008-04-01', '../monlcs/modules/chess/images/ChessSet_Cover.jpg', 'Jeu d''echecs.'),
(48, 'Tetris', '../monlcs/modules/tetris.html', 'null', 'admin', 'private', '2008-04-01', NULL, 'Jeu Tetris.'),
(49, 'Sokoban ', '../monlcs/modules/sokoban/sokoban.html', 'null', 'admin', 'private', '2008-04-01', NULL, 'Jeu SokoBan'),
(50, 'Flash PacMan', 'http://bulltricker.free.fr/jeuxjava/pacmanFLASH/pacman.swf', 'null', 'admin', 'private', '2008-04-01', NULL, 'Jeu PacMan.'),
(111, 'Synthèse Lune', '../monlcs/modules/lune/Lune_Phases_G.jpg', 'null', 'admin', 'private', '2008-04-01', NULL, 'Document de synthese sur la Lune.'),
(112, 'Google maps', 'http://pagesperso-orange.fr/s.tck/index.html', 'null', 'admin', 'private', '2008-04-01', NULL, 'Utilitaire Google Maps.'),
(57, 'Animations phase lunaire.', 'http://www.mcq.org/societe/phases_lunaires/phaseslunaires4.swf', 'null', 'admin', 'private', '2008-04-01', NULL, 'Animation Flash sur le mouvement relatif de la Lune.'),
(58, 'Lune', '../monlcs/modules/lune/lune.htm', 'null', 'admin', 'private', '2008-04-01', NULL, 'Widget Lune.'),
(59, 'Wikip&eacute;dia Lune ', 'http://fr.wikipedia.org/wiki/Phase_lunaire', 'null', 'admin', 'private', '2008-04-01', NULL, 'Article Wikipedia sur la Lune.'),
(60, 'Piano', '../monlcs/modules/keyboard5.swf', 'null', 'admin', 'private', '2008-04-01', NULL, 'Piano virtuel.'),
(68, 'Ma Normandie', 'http://lcs.delamine.clg14.ac-caen.fr/~admin/map.html', 'null', 'admin', 'private', '2008-04-01', NULL, 'Normandie sur Google Maps.'),
(84, 'Aide', '../monlcs/Aide/', 'null', 'mrT', 'private', '2008-04-01', NULL, NULL),
(85, 'Aide_Prof', '../monlcs/Aide/Prof/', 'null', 'mrT', 'private', '2008-04-01', NULL, NULL),
(88, 'Tours de Hanoi', 'http://www.videosdroles.org/jeux/137.SWF', 'null', 'tacks', 'private', '2008-04-01', NULL, 'Jeu Flash Tours de Hanoi.'),
(89, 'Mini Ajax', 'http://feeds.feedburner.com/miniajax', 'RSS_no_img', 'tacks', 'private', '2008-04-01', NULL, NULL),
(97, 'Ruby', 'http://www.developpez.net/forums/external.php?type=RSS2&forumids=235', 'RSS_no_img', 'admin', 'private', '2008-04-01', NULL, NULL),
(110, 'Rechercher sur la toile.', 'http://www.google.fr', 'null', 'admin', 'public', '2008-04-01', NULL, 'Moteur de recherche GOOGLE.'),
(130, 'Rss_sudoku', 'http://www.zen-sudoku.be/rss.xml', 'RSS_no_img', 'admin', 'private', '2008-04-01', NULL, NULL),
(114, 'Pyramides Giseh', 'http://pagesperso-orange.fr/s.tck/egypt.html', 'null', 'admin', 'private', '2008-04-01', NULL, 'Pyramides localisees sur Google Maps.'),
(135, 'Utilisation de Biilan', 'http://lcs.monnet.clg14.ac-caen.fr/~oda_fons/', 'null', 'admin', 'private', '2008-04-01', NULL, 'Document d''aide pour l''utilisation de Biilan.'),
(136, 'MoyenAge1', 'http://education.france5.fr/moyenage/illustrations/labo.swf', 'null', 'admin', 'private', '2008-04-01', NULL, 'Exercice sur le Moyen Age en Flash.'),
(118, 'Le muscle', 'http://upload.wikimedia.org/wikipedia/commons/thumb/c/c0/Skeletal_muscle.jpg/784px-Skeletal_muscle.jpg', 'null', 'labidipf', 'private', '2008-04-01', NULL, NULL),
(120, 'Meteo', 'http://www.meteo.fr', 'null', 'lecluseo', 'private', '2008-04-01', NULL, NULL),
(137, 'MoyenAge2', 'http://education.france5.fr/moyenage/illustrations/labo02.swf', 'null', 'admin', 'private', '2008-04-01', NULL, 'Exercice sur le moyen-age en Flash.'),
(123, 'Courant &eacute;lectrique ', 'http://www.col-bugatti-molsheim.ac-strasbourg.fr/flash/courant3/', 'null', 'admin', 'private', '2008-04-01', NULL, 'Animation Flash sur le courant &eacute;lectrique'),
(124, 'Systeme_solaire', 'http://education.france5.fr/soleil/syssol.swf', 'null', 'admin', 'private', '2008-04-01', NULL, 'Document de synthese sur le systeme solaire.'),
(125, 'Techno_montage', 'http://education.france5.fr/MINTE/MINTE10900/page_10900_71561.cfm', 'null', 'admin', 'private', '2008-04-01', NULL, 'Animation Flash traitant du montage d''on objet en Technologie.'),
(126, 'Histoire des techniques', 'http://education.france5.fr/MINTE/MINTE10899/page_10899_71550.cfm', 'null', 'admin', 'private', '2008-04-01', NULL, 'Animation interactive Flash sur l''histoire des techniques.'),
(127, 'FicheHistInventions', 'http://education.france5.fr/SITHE/SITHE10976/Inventions_ficheEnseignant.pdf', 'null', 'tacks', 'private', '2008-04-01', NULL, 'Fiche descriptive de l''activit&eacute; "Histoire des inventions".'),
(128, 'Emballages', 'http://education.france5.fr/MINTE/MINTE10901/page_10901_71560.cfm', 'null', 'admin', 'private', '2008-04-01', NULL, 'Animation sur les emballages.'),
(131, 'Rss_asnieres', 'http://www.dailymotion.com/rss/JOSIANE-FISCHER/1', 'RSS_no_img', 'admin', 'public', '2008-04-01', NULL, NULL),
(132, 'EdCiv_Loi .', 'http://www.junior.senat.fr/loi/da/parcours.swf', 'null', 'tacks', 'private', '2008-04-01', NULL, 'Animation Flash sur l''elaboration d''une loi.'),
(138, 'MoyenAge_Quizz', 'http://education.france5.fr/moyenage/illustrations/quiz.swf', 'null', 'tacks', 'private', '2008-04-01', NULL, 'Quizz sur le moyen-age.'),
(139, 'Moyen_Age_Video', 'http://education.france5.fr/moyenage/videos/ste_feod_03_b.htm', 'null', 'tacks', 'private', '2008-04-01', NULL, 'Video sur le moyen-age.'),
(190, 'Doc_MonLCS', '/doc/monlcs/html/', 'null', 'tacks', 'public', '2008-07-25', '', 'Doc Interne de  MonLCS.'),
(192, 'Applis', '/lcs/applis.php', 'null', 'admin', 'public', '2008-08-01', '', 'Page applications du Lcs.'),
(193, 'Accueil', '/lcs/accueil.php', 'null', 'admin', 'public', '2008-08-01', '', 'Page d''accueil du LCS.'),
(201, 'Note_admin_Bienvenue', 'http://LcEtch.virtual.net/~admin/Documents/file/16_Bienvenue.html', 'null', 'admin', 'private', '2008-08-06', NULL, NULL),
(200, 'Annuaire.', '/Annu/', 'null', 'tacks', 'public', '2008-08-06', '', 'Outil annuaire du LCS.');

-- --------------------------------------------------------

--
-- Structure de la table `ml_ressourcesAffect`
--

CREATE TABLE IF NOT EXISTS `ml_ressourcesAffect` (
  `id` int(11) NOT NULL auto_increment,
  `id_ressource` int(11) NOT NULL default '0',
  `id_menu` int(11) NOT NULL default '0',
  `cible` varchar(255) NOT NULL default 'admin',
  `x` int(11) NOT NULL default '10',
  `y` int(11) NOT NULL default '10',
  `z` int(11) default '50',
  `w` int(11) NOT NULL default '300',
  `h` int(11) NOT NULL default '200',
  `min` enum('Y','N') NOT NULL default 'N',
  `setter` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Contenu de la table `ml_ressourcesAffect`
--

REPLACE INTO `ml_ressourcesAffect` (`id`, `id_ressource`, `id_menu`, `cible`, `x`, `y`, `z`, `w`, `h`, `min`, `setter`) VALUES
(2, 84, 12, 'lcs-users', 7, 58, 50, 441, 326, 'N', 'mrT'),
(3, 85, 12, 'Profs', 450, 58, 50, 441, 326, 'N', 'mrT');

-- --------------------------------------------------------

--
-- Structure de la table `ml_ressourcesProposees`
--

CREATE TABLE IF NOT EXISTS `ml_ressourcesProposees` (
  `id` int(11) NOT NULL auto_increment,
  `id_ressource` int(11) NOT NULL default '0',
  `id_menu` int(11) NOT NULL default '0',
  `cible` varchar(255) NOT NULL default 'lcs-users',
  `setter` varchar(128) NOT NULL default '',
  `matiere` varchar(128) NOT NULL default '-1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

--
-- Contenu de la table `ml_ressourcesProposees`
--

REPLACE INTO `ml_ressourcesProposees` (`id`, `id_ressource`, `id_menu`, `cible`, `setter`, `matiere`) VALUES
(1, 2, 1, 'Eleves', 'admin', '-1'),
(2, 2, 1, 'Profs', 'admin', '-1'),
(3, 123, 3, 'Eleves', 'admin', 'Matiere_PH-CH'),
(4, 123, 3, 'Profs', 'admin', 'Matiere_PH-CH'),
(5, 122, 3, 'Eleves', 'admin', 'Matiere_PH-CH'),
(6, 122, 3, 'Profs', 'admin', 'Matiere_PH-CH'),
(7, 126, 3, 'Eleves', 'admin', 'Matiere_TECHN'),
(8, 126, 3, 'Profs', 'admin', 'Matiere_TECHN'),
(9, 128, 3, 'Eleves', 'admin', 'Matiere_TECHN'),
(10, 128, 3, 'Profs', 'admin', 'Matiere_TECHN'),
(11, 9, 9, 'lcs-users', 'admin', '-1'),
(12, 2, 9, 'lcs-users', 'admin', '-1'),
(13, 36, 9, 'lcs-users', 'admin', '-1'),
(14, 37, 9, 'lcs-users', 'admin', '-1'),
(15, 34, 9, 'lcs-users', 'admin', '-1'),
(16, 60, 9, 'lcs-users', 'admin', '-1'),
(17, 47, 11, 'lcs-users', 'admin', '-1'),
(18, 50, 11, 'Profs', 'admin', '-1'),
(19, 42, 11, 'Profs', 'admin', '-1'),
(20, 48, 11, 'Profs', 'admin', '-1'),
(21, 45, 11, 'Profs', 'admin', '-1'),
(22, 67, 11, 'Eleves', 'admin', '-1'),
(23, 67, 11, 'Profs', 'admin', '-1'),
(46, 50, 141, 'lcs-users', 'admin', '-1'),
(48, 50, 165, 'lcs-users', 'admin', '-1'),
(49, 42, 165, 'lcs-users', 'admin', '-1'),
(50, 48, 165, 'lcs-users', 'admin', '-1');

-- --------------------------------------------------------

--
-- Structure de la table `ml_rss`
--

CREATE TABLE IF NOT EXISTS `ml_rss` (
  `id` int(11) NOT NULL auto_increment,
  `user` varchar(128) NOT NULL default '',
  `url` text NOT NULL,
  `type` enum('perso','propose') NOT NULL default 'perso',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55 ;

--
-- Contenu de la table `ml_rss`
--

REPLACE INTO `ml_rss` (`id`, `user`, `url`, `type`) VALUES
(44, 'admin', 'http://www.dailymotion.com/rss/JOSIANE-FISCHER/1', 'perso'),
(43, 'admin', 'http://www.lemonde.fr/rss/sequence/0,2-651865,1-0,0.xml', 'perso');

-- --------------------------------------------------------

--
-- Structure de la table `ml_scenarios`
--

CREATE TABLE IF NOT EXISTS `ml_scenarios` (
  `id` int(11) NOT NULL auto_increment,
  `id_scen` int(11) NOT NULL,
  `id_ressource` int(11) NOT NULL default '0',
  `cible` varchar(255) NOT NULL default 'admin',
  `type` varchar(125) NOT NULL default 'ressource',
  `matiere` varchar(255) NOT NULL default '?',
  `titre` varchar(255) NOT NULL default 'nouveau scenario',
  `x` int(11) NOT NULL default '10',
  `y` int(11) NOT NULL default '10',
  `z` int(11) NOT NULL default '50',
  `w` int(11) NOT NULL default '300',
  `h` int(11) NOT NULL default '200',
  `min` enum('Y','N') NOT NULL default 'N',
  `setter` varchar(125) NOT NULL default '',
  `descr` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=153 ;

--
-- Contenu de la table `ml_scenarios`
--

REPLACE INTO `ml_scenarios` (`id`, `id_scen`, `id_ressource`, `cible`, `type`, `matiere`, `titre`, `x`, `y`, `z`, `w`, `h`, `min`, `setter`, `descr`) VALUES
(34, 0, 37, 'Eleves', 'ressource', 'Matiere_MATHS', 'Math1', 499, 243, 50, 219, 163, 'N', 'tacks', 'Calcul de vitesse moyenne loufoque.'),
(19, 0, 36, 'Eleves', 'ressource', 'Matiere_MATHS', 'Math1', 8, 57, 50, 362, 140, 'N', 'tacks', 'Calcul de vitesse moyenne loufoque.'),
(33, 0, 3, 'Eleves', 'note', 'Matiere_MATHS', 'Math1', 376, 57, 50, 345, 139, 'N', 'tacks', 'Calcul de vitesse moyenne loufoque.'),
(21, 0, 2, 'Eleves', 'ressource', 'Matiere_MATHS', 'Math1', 9, 243, 50, 483, 165, 'N', 'tacks', 'Calcul de vitesse moyenne loufoque.'),
(47, 1, 12, 'Eleves', 'ressource', 'Matiere_FRANC', 'Lecture', 12, 59, 50, 428, 374, 'N', 'admin', 'Exemple de livre realise avec Didapages.'),
(52, 1, 12, 'Profs', 'ressource', 'Matiere_FRANC', 'Lecture', 12, 59, 50, 428, 374, 'N', 'admin', 'Exemple de livre realise avec Didapages.'),
(53, 2, 59, 'Eleves', 'ressource', 'Matiere_PH-CH', 'Lune', 383, 57, 50, 500, 403, 'N', 'admin', 'D&eacute;couverte des phases de la lune.'),
(56, 2, 57, 'Eleves', 'ressource', 'Matiere_PH-CH', 'Lune', 6, 60, 50, 369, 210, 'N', 'admin', 'D&eacute;couverte des phases de la lune.'),
(60, 3, 112, 'Eleves', 'ressource', 'Matiere_HIGEO', 'Reperage', 3, 55, 50, 632, 423, 'N', 'admin', 'Epreuve de rep&eacute;rage avec Google Maps.'),
(61, 3, 11, 'Eleves', 'note', 'Matiere_HIGEO', 'Reperage', 650, 55, 50, 432, 423, 'N', 'admin', 'Epreuve de rep&eacute;rage avec Google Maps.'),
(62, 3, 112, 'Profs', 'ressource', 'Matiere_HIGEO', 'Reperage', 3, 55, 50, 632, 423, 'N', 'admin', 'Epreuve de rep&eacute;rage avec Google Maps.'),
(63, 3, 11, 'Profs', 'note', 'Matiere_HIGEO', 'Reperage', 650, 55, 50, 432, 423, 'N', 'admin', 'Epreuve de rep&eacute;rage avec Google Maps.'),
(78, 2, 14, 'Eleves', 'note', 'Matiere_PH-CH', 'Lune', 7, 318, 50, 369, 138, 'N', 'admin', 'D&eacute;couverte des phases de la lune.'),
(79, 2, 14, 'Eleves', 'note', 'Matiere_PH-CH', 'Lune', 7, 318, 50, 369, 138, 'N', 'admin', 'D&eacute;couverte des phases de la lune.'),
(80, 2, 14, 'Eleves', 'note', 'Matiere_PH-CH', 'Lune', 7, 318, 50, 369, 138, 'N', 'admin', 'D&eacute;couverte des phases de la lune.'),
(81, 4, 132, 'Eleves', 'ressource', 'Matiere_EDCIV', 'La_loi', 5, 56, 50, 512, 473, 'N', 'tacks', 'Le parcours d''une loi.'),
(84, 4, 15, 'Eleves', 'note', 'Matiere_EDCIV', 'La_loi', 522, 56, 50, 290, 125, 'N', 'tacks', 'Le parcours d''une loi.'),
(97, 5, 136, 'Eleves', 'ressource', 'Matiere_HIGEO', 'Moyen age', 5, 55, 50, 400, 300, 'N', 'admin', 'Epreuve sur le moyen-&acirc;ge.'),
(101, 5, 138, 'Eleves', 'ressource', 'Matiere_HIGEO', 'Moyen age', 5, 398, 50, 400, 300, 'N', 'admin', 'Epreuve sur le moyen-&acirc;ge.'),
(102, 5, 137, 'Eleves', 'ressource', 'Matiere_HIGEO', 'Moyen age', 411, 55, 50, 400, 300, 'N', 'admin', 'Epreuve sur le moyen-&acirc;ge.');

-- --------------------------------------------------------

--
-- Structure de la table `ml_shared`
--

CREATE TABLE IF NOT EXISTS `ml_shared` (
  `id` int(11) NOT NULL auto_increment,
  `id_menu` int(11) NOT NULL,
  `cible` text NOT NULL,
  `setter` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ml_shared`
--


-- --------------------------------------------------------

--
-- Structure de la table `ml_tabs`
--

CREATE TABLE IF NOT EXISTS `ml_tabs` (
  `id` int(11) NOT NULL auto_increment,
  `id_tab` int(11) NOT NULL default '0',
  `user` varchar(128) NOT NULL default '?',
  `caption` varchar(128) NOT NULL default '?',
  `nom` varchar(255) NOT NULL default '?',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=180 ;

--
-- Contenu de la table `ml_tabs`
--

REPLACE INTO `ml_tabs` (`id`, `id_tab`, `user`, `caption`, `nom`) VALUES
(2, 1, 'all', 'Actualit&eacute;s', 'actu'),
(1, 1, 'all', 'Mon Bureau LCS', 'bureau'),
(5, 2, 'all', 'Ress. Acad&eacute;miques', 'crdp'),
(4, 2, 'all', 'fils RSS', 'rss'),
(3, 2, 'all', 'Ress p&eacute;dagogiques', 'peda'),
(6, 2, 'all', 'Vie Scolaire', 'vs'),
(9, 3, 'all', 'Couteau suisse', 'suisse'),
(10, 4, 'all', 'Choisir', 'scenario_choix'),
(7, 3, 'all', 'Outils LCS', 'lcs'),
(179, 79, 'all', 'Transversal', 'transversal'),
(12, 6, 'all', 'Guide utilisation', 'guide'),
(165, 80, 'all', 'Jeux', 'jeux'),
(19, 2, 'all', 'Orientation', 'orientation'),
(20, 2, 'all', 'Presse', 'presse'),
(21, 2, 'all', 'Vie locale', 'vie_locale'),
(163, 79, 'all', 'Portail', 'portail');

-- --------------------------------------------------------

--
-- Structure de la table `ml_zones`
--

CREATE TABLE IF NOT EXISTS `ml_zones` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(128) NOT NULL default '-',
  `user` varchar(128) NOT NULL default 'all',
  `rang` int(11) NOT NULL default '0',
  `status` enum('fixed','etab','perso') NOT NULL default 'perso',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=84 ;

--
-- Contenu de la table `ml_zones`
--

REPLACE INTO `ml_zones` (`id`, `nom`, `user`, `rang`, `status`) VALUES
(1, 'Accueil', 'all', 1, 'fixed'),
(2, 'Ressources', 'all', 2, 'fixed'),
(3, 'Applications', 'all', 3, 'fixed'),
(4, 'Sc&eacute;narios', 'all', 4, 'fixed'),
(6, 'Aide', 'all', 5, 'fixed'),
(79, 'CDI', 'all', 6, 'etab'),
(80, 'Divers', 'all', 7, 'etab');
