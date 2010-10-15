USE monlcs_db;
ALTER TABLE `ml_scenarios` ADD `enabled` TINYINT NOT NULL DEFAULT '1' AFTER `id_ressource` 
