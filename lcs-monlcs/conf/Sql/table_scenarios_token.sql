--
-- Structure de la table `ml_scenarios_token`
--

CREATE TABLE IF NOT EXISTS `ml_scenarios_token` (
  `id_scen` int(11) NOT NULL,
  `token` varchar(50) NOT NULL,
  UNIQUE KEY `id_scen` (`id_scen`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
