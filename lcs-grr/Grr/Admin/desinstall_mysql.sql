--
-- Current Database: grr_plug
--

DROP DATABASE IF EXISTS grr_plug;

DELETE FROM mysql.user WHERE `User` = 'grr_user';
DELETE FROM mysql.db WHERE `User` = 'grr_user';
