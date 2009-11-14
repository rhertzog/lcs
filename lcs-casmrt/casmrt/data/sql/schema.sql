CREATE TABLE cas_lt (id INT AUTO_INCREMENT, ticket VARCHAR(255) DEFAULT '' NOT NULL, client_hostname VARCHAR(255) DEFAULT '' NOT NULL, consumed DATETIME, created_at DATETIME, updated_at DATETIME, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE cas_pgt (id INT AUTO_INCREMENT, ticket VARCHAR(255) DEFAULT '' NOT NULL, client_hostname VARCHAR(255) DEFAULT '' NOT NULL, iou VARCHAR(255) DEFAULT '' NOT NULL, service_ticket_id INT DEFAULT 0 NOT NULL, created_at DATETIME, updated_at DATETIME, INDEX service_ticket_id_idx (service_ticket_id), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE cas_st (id INT AUTO_INCREMENT, ticket VARCHAR(255) DEFAULT '' NOT NULL, service TEXT NOT NULL, client_hostname VARCHAR(255) DEFAULT '' NOT NULL, username VARCHAR(255) DEFAULT '' NOT NULL, type VARCHAR(255) DEFAULT '' NOT NULL, consumed DATETIME, proxy_granting_ticket_id INT, tgt_id INT, created_at DATETIME, updated_at DATETIME, INDEX tgt_id_idx (tgt_id), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE cas_tgt (id INT AUTO_INCREMENT, ticket VARCHAR(255) DEFAULT '' NOT NULL, client_hostname VARCHAR(255) DEFAULT '' NOT NULL, username VARCHAR(255) DEFAULT '' NOT NULL, extra_attributes TEXT, created_at DATETIME, updated_at DATETIME, PRIMARY KEY(id)) ENGINE = INNODB;
ALTER TABLE cas_pgt ADD FOREIGN KEY (service_ticket_id) REFERENCES cas_st(id) ON DELETE CASCADE;
ALTER TABLE cas_st ADD FOREIGN KEY (tgt_id) REFERENCES cas_tgt(id) ON DELETE CASCADE;
