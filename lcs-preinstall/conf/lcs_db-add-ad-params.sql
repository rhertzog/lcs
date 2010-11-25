USE `lcs_db`;
--
INSERT INTO `params` (`name`, `value`, `srv_id`, `descr`, `cat`) VALUES
('ad_auth_delegation', 'false', 0, 'Authentification déportée au serveur Active Directory (true/false)', 2),
('ad_server', '', 0, 'Adresse du serveur Active Directory', 2),
('ad_base_dn', '', 0, 'Dn de base de l''annuaire Active Directory', 2),
('ad_bind_dn', '', 0, 'Dn employé pour la connexion', 2),
('ad_bind_pw', '', 0, 'Mot de passe de connexion', 2);
