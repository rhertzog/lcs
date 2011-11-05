-- MySQL dump 8.21
--
-- Host: localhost    Database:gepi_plug 
-- -------------------------------------------------------
-- Server version	3.23.49-log

--
-- Current Database:gepi_plug 
--

DROP DATABASE IF EXISTS gepi_plug;
DELETE FROM `mysql`.`user` WHERE User = 'gepi_user' AND Host = 'localhost';
DELETE FROM `mysql`.`db` WHERE User = 'gepi_user' AND Host = 'localhost';
DELETE FROM `mysql`.`tables_priv` WHERE User = 'gepi_user' AND Host = 'localhost';
DELETE FROM `mysql`.`columns_priv` WHERE User = 'gepi_user'  AND Host = 'localhost';
FLUSH PRIVILEGES ;
