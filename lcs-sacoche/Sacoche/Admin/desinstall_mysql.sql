-- MySQL dump 8.21
--
-- Host: localhost    Database:cdt_plug 
---------------------------------------------------------
-- Server version	3.23.49-log

--
-- Current Database:sacoche_plug 
--

DROP DATABASE IF EXISTS sacoche_plug;
DELETE FROM `mysql`.`user` WHERE User = 'sacoche_user' AND Host = 'localhost';
DELETE FROM `mysql`.`db` WHERE User = 'sacoche_user' AND Host = 'localhost';
DELETE FROM `mysql`.`tables_priv` WHERE User = 'sacoche_user' AND Host = 'localhost';
DELETE FROM `mysql`.`columns_priv` WHERE User = 'sacoche_user' AND Host = 'localhost';
FLUSH PRIVILEGES ;
