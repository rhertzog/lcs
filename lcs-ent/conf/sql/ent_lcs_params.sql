USE `lcs_db`;
--
INSERT INTO `params` (`name`, `value`, `srv_id`, `descr`, `cat`) VALUES
('auth_mod', 'LCS', 0, 'Mode d''authentification LCS (LCS/ENT)', 6),
('ent_hostname', 'ent.crdp.ac-caen.fr', 0, 'Nom d''hôte ENT', 6),
('ent_port', '443', 0, 'Port d''écoute de l''ENT', 6),
('ent_uri', 'connexion', 0, 'URI ENT', 6);
