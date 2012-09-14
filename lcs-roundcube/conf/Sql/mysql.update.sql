-- Roundcube Webmail update script for MySQL databases

-- Updates from version 0.4.2

ALTER TABLE `users` DROP INDEX `username_index`;
ALTER TABLE `users` ADD UNIQUE `username` (`username`, `mail_host`);

ALTER TABLE `contacts` MODIFY `email` varchar(255) NOT NULL;

TRUNCATE TABLE `messages`;

-- Updates from version 0.5.1
-- Updates from version 0.5.2
-- Updates from version 0.5.3
-- Updates from version 0.5.4

ALTER TABLE `contacts` ADD `words` TEXT NULL AFTER `vcard`;
ALTER TABLE `contacts` CHANGE `vcard` `vcard` LONGTEXT /*!40101 CHARACTER SET utf8 */ NULL DEFAULT NULL;
ALTER TABLE `contactgroupmembers` ADD INDEX `contactgroupmembers_contact_index` (`contact_id`);

TRUNCATE TABLE `messages`;
TRUNCATE TABLE `cache`;

-- Updates from version 0.6

/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

ALTER TABLE `users` CHANGE `alias` `alias` varchar(128) BINARY NOT NULL;
ALTER TABLE `users` CHANGE `username` `username` varchar(128) BINARY NOT NULL;

CREATE TABLE `dictionary` (
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `language` varchar(5) NOT NULL,
  `data` longtext NOT NULL,
  CONSTRAINT `user_id_fk_dictionary` FOREIGN KEY (`user_id`)
    REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE `uniqueness` (`user_id`, `language`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;

CREATE TABLE `searches` (
  `search_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `type` int(3) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL,
  `data` text,
  PRIMARY KEY(`search_id`),
  CONSTRAINT `user_id_fk_searches` FOREIGN KEY (`user_id`)
    REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE `uniqueness` (`user_id`, `type`, `name`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;

DROP TABLE `messages`;

CREATE TABLE `cache_index` (
 `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
 `mailbox` varchar(255) BINARY NOT NULL,
 `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `valid` tinyint(1) NOT NULL DEFAULT '0',
 `data` longtext NOT NULL,
 CONSTRAINT `user_id_fk_cache_index` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
 INDEX `changed_index` (`changed`),
 PRIMARY KEY (`user_id`, `mailbox`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;

CREATE TABLE `cache_thread` (
 `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
 `mailbox` varchar(255) BINARY NOT NULL,
 `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `data` longtext NOT NULL,
 CONSTRAINT `user_id_fk_cache_thread` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
 INDEX `changed_index` (`changed`),
 PRIMARY KEY (`user_id`, `mailbox`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;

CREATE TABLE `cache_messages` (
 `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
 `mailbox` varchar(255) BINARY NOT NULL,
 `uid` int(11) UNSIGNED NOT NULL DEFAULT '0',
 `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `data` longtext NOT NULL,
 `flags` int(11) NOT NULL DEFAULT '0',
 CONSTRAINT `user_id_fk_cache_messages` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
 INDEX `changed_index` (`changed`),
 PRIMARY KEY (`user_id`, `mailbox`, `uid`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;

/*!40014 SET FOREIGN_KEY_CHECKS=1 */;

-- Updates from version 0.7-beta

ALTER TABLE `session` CHANGE `sess_id` `sess_id` varchar(128) NOT NULL;

-- Updates from version 0.7

/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

ALTER TABLE `contacts` DROP FOREIGN KEY `user_id_fk_contacts`;
ALTER TABLE `contacts` DROP INDEX `user_contacts_index`;
ALTER TABLE `contacts` MODIFY `email` text NOT NULL;
ALTER TABLE `contacts` ADD INDEX `user_contacts_index` (`user_id`,`del`);
ALTER TABLE `contacts` ADD CONSTRAINT `user_id_fk_contacts` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `cache` ALTER `user_id` DROP DEFAULT;
ALTER TABLE `cache_index` ALTER `user_id` DROP DEFAULT;
ALTER TABLE `cache_thread` ALTER `user_id` DROP DEFAULT;
ALTER TABLE `cache_messages` ALTER `user_id` DROP DEFAULT;
ALTER TABLE `contacts` ALTER `user_id` DROP DEFAULT;
ALTER TABLE `contactgroups` ALTER `user_id` DROP DEFAULT;
ALTER TABLE `contactgroupmembers` ALTER `contact_id` DROP DEFAULT;
ALTER TABLE `identities` ALTER `user_id` DROP DEFAULT;
ALTER TABLE `searches` ALTER `user_id` DROP DEFAULT;

/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
