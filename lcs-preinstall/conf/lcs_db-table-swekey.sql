--
USE `lcs_db`;
-- 
--
CREATE TABLE IF NOT EXISTS `swekey` (
`login` varchar(32) NOT NULL,
`id_swekey` varchar(32) NOT NULL,
KEY `id_swekey` (`id_swekey`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;