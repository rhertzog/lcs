DROP DATABASE IF EXISTS claro_plug; 
DELETE FROM `mysql`.`user` WHERE User = 'claro_user' AND Host = 'localhost'; 
DELETE FROM `mysql`.`db` WHERE User = 'claro_user' AND Host = 'localhost'; 
DELETE FROM `mysql`.`tables_priv` WHERE User = 'claro_user' AND Host = 'localhost'; 
DELETE FROM `mysql`.`columns_priv` WHERE User = 'claro_user' AND Host = 'localhost';
FLUSH PRIVILEGES ;
