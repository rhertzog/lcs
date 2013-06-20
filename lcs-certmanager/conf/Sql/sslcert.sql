--
-- Table structure for table `sslcert`
--

DROP TABLE IF EXISTS `sslcert`;
CREATE TABLE `sslcert` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `sel` tinyint(1) NOT NULL,
  `name` varchar(250) NOT NULL,
  `notbefore` varchar(250) NOT NULL,
  `notafter` varchar(250) NOT NULL,
  `description` text NOT NULL,
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
