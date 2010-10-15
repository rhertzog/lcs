-- MySQL dump 8.21
--
-- Host: localhost    Database:agendas_plug 
---------------------------------------------------------
-- Server version	3.23.49-log

--
-- Current Database:agendas_plug 
--

DROP DATABASE IF EXISTS agendas_plug;
DELETE FROM `mysql`.`user` WHERE User = 'agendas_user' AND Host = 'localhost';
DELETE FROM `mysql`.`db` WHERE User = 'agendas_user' AND Host = 'localhost';
DELETE FROM `mysql`.`tables_priv` WHERE User = 'agendas_user' AND Host = 'localhost';
DELETE FROM `mysql`.`columns_priv` WHERE User = 'agendas_user' AND Host = 'localhost';
FLUSH PRIVILEGES ;