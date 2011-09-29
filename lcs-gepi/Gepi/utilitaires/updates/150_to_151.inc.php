<?php
/**
 * Fichier de mise � jour de la version 1.5.0 � la version 1.5.1
 * 
 * $Id:  $
 *
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

		$result .= "<h3 class='titreMaJ'>Mise � jour vers la version 1.5.1" . $rc . $beta . " :</h3>";

		$result .= "&nbsp;->Ajout du champ rn_nomdev � la table classes<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'rn_nomdev'"));
		if ($test1 == 0) {
			$query3 = mysql_query("ALTER TABLE `classes` ADD `rn_nomdev` CHAR( 1 ) NOT NULL DEFAULT 'n';");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout du champ rn_toutcoefdev � la table classes<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'rn_toutcoefdev'"));
		if ($test1 == 0) {
			$query3 = mysql_query("ALTER TABLE `classes` ADD `rn_toutcoefdev` CHAR( 1 ) NOT NULL DEFAULT 'n';");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout du champ rn_coefdev_si_diff � la table classes<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'rn_coefdev_si_diff'"));
		if ($test1 == 0) {
			$query3 = mysql_query("ALTER TABLE `classes` ADD `rn_coefdev_si_diff` CHAR( 1 ) NOT NULL DEFAULT 'n';");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout du champ rn_datedev � la table classes<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'rn_datedev'"));
		if ($test1 == 0) {
			$query3 = mysql_query("ALTER TABLE `classes` ADD `rn_datedev` CHAR( 1 ) NOT NULL DEFAULT 'n';");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout du champ rn_sign_chefetab � la table classes<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'rn_sign_chefetab'"));
		if ($test1 == 0) {
			$query3 = mysql_query("ALTER TABLE `classes` ADD `rn_sign_chefetab` CHAR( 1 ) NOT NULL DEFAULT 'n';");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout du champ rn_sign_pp � la table classes<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'rn_sign_pp'"));
		if ($test1 == 0) {
			$query3 = mysql_query("ALTER TABLE `classes` ADD `rn_sign_pp` CHAR( 1 ) NOT NULL DEFAULT 'n';");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout du champ rn_sign_resp � la table classes<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'rn_sign_resp'"));
		if ($test1 == 0) {
			$query3 = mysql_query("ALTER TABLE `classes` ADD `rn_sign_resp` CHAR( 1 ) NOT NULL DEFAULT 'n';");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout du champ rn_sign_nblig � la table classes<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'rn_sign_nblig'"));
		if ($test1 == 0) {
			$query3 = mysql_query("ALTER TABLE `classes` ADD `rn_sign_nblig` INT( 11 ) NOT NULL DEFAULT '3';");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout du champ rn_formule � la table classes<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'rn_formule'"));
		if ($test1 == 0) {
			$query3 = mysql_query("ALTER TABLE `classes` ADD `rn_formule` TEXT NOT NULL;");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}

		// D�but de la 1.5.1? 20070904

		// ====================================
		// Ajouts concernant le dispositif EDT
		$result .= "&nbsp;->Cr�ation de la table 'salle_cours'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'salle_cours'"));
		if ($test1 == 0) {
			$query1 = mysql_query("CREATE TABLE salle_cours (`id_salle` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , `numero_salle` VARCHAR( 10 ) NOT NULL , `nom_salle` VARCHAR( 50 ) NOT NULL);");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe d�j�');
		}

		$result .= "&nbsp;->Cr�ation de la table 'edt_cours'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'edt_cours'"));
		if ($test1 == 0) {
			$query1 = mysql_query("CREATE TABLE `edt_cours` (`id_cours` int(3) NOT NULL auto_increment, `id_groupe` varchar(10) NOT NULL, `id_salle` varchar(3) NOT NULL, `jour_semaine` varchar(10) NOT NULL, `id_definie_periode` varchar(3) NOT NULL, `duree` varchar(10) NOT NULL default '2', `heuredeb_dec` varchar(3) NOT NULL default '0', `id_semaine` varchar(3) NOT NULL default '0', `id_calendrier` varchar(3) NOT NULL default '0', `modif_edt` varchar(3) NOT NULL default '0', PRIMARY KEY  (`id_cours`));");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe d�j�');
		}

		// Ajout d'un champ � cette table
		$result .= "&nbsp;->Ajout du champ 'login_prof' � la table 'edt_cours'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM edt_cours LIKE 'login_prof'"));
		if ($test1 == 0) {
			$sql="ALTER TABLE `edt_cours` ADD `login_prof` varchar(50) NOT NULL;";
			$query = mysql_query($sql);
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Cr�ation de la table 'edt_setting'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'edt_setting'"));
		if ($test1 == 0) {
			$query1 = mysql_query("CREATE TABLE `edt_setting` (`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`reglage` VARCHAR( 30 ) NOT NULL ,`valeur` VARCHAR( 30 ) NOT NULL);");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe d�j�');
		}

		$result .= "&nbsp;->Cr�ation de la table 'edt_calendrier'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'edt_calendrier'"));
		if ($test1 == 0) {
			$query1 = mysql_query("CREATE TABLE `edt_calendrier` (`id_calendrier` int(11) NOT NULL auto_increment,
`classe_concerne_calendrier` text NOT NULL,
`nom_calendrier` varchar(100) NOT NULL default '',
`debut_calendrier_ts` varchar(11) NOT NULL,
`fin_calendrier_ts` varchar(11) NOT NULL,
`jourdebut_calendrier` date NOT NULL default '0000-00-00',
`heuredebut_calendrier` time NOT NULL default '00:00:00',
`jourfin_calendrier` date NOT NULL default '0000-00-00',
`heurefin_calendrier` time NOT NULL default '00:00:00',
`numero_periode` tinyint(4) NOT NULL default '0',
`etabferme_calendrier` tinyint(4) NOT NULL,
`etabvacances_calendrier` tinyint(4) NOT NULL,
PRIMARY KEY (`id_calendrier`));");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe d�j�');
		}

		$result .= "&nbsp;->Ajout des champs 'debut_calendrier_ts' et 'fin_calendrier_ts' � la table 'edt_calendrier'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM edt_calendrier LIKE 'fin_calendrier_ts'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `edt_calendrier` ADD `debut_calendrier_ts` VARCHAR( 11 ) NOT NULL AFTER `nom_calendrier` ,ADD `fin_calendrier_ts` VARCHAR( 11 ) NOT NULL AFTER `debut_calendrier_ts` ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Les champs existent d�j�');
		}


		$result .= "&nbsp;->Cr�ation de la table 'edt_gr_nom'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'edt_gr_nom'"));
		if ($test1 == 0) {
			$query1 = mysql_query("CREATE TABLE `edt_gr_nom` (
					`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
					`nom` VARCHAR( 50 ) NOT NULL ,
					`nom_long` VARCHAR( 200 ) NOT NULL ,
					`subdivision_type` VARCHAR( 20 ) NOT NULL DEFAULT 'autre',
					`subdivision` VARCHAR( 50 ) NOT NULL ,
					PRIMARY KEY ( `id` ));");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe d�j�');
		}

		$result .= "&nbsp;->Cr�ation de la table 'edt_gr_eleves'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'edt_gr_eleves'"));
		if ($test1 == 0) {
			$query1 = mysql_query("CREATE TABLE `edt_gr_eleves` (
					`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
					`id_gr_nom` INT( 11 ) NOT NULL ,
					`id_eleve` INT( 11 ) NOT NULL ,
					PRIMARY KEY ( `id` ));");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe d�j�');
		}

		$result .= "&nbsp;->Cr�ation de la table 'edt_gr_profs'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'edt_gr_profs'"));
		if ($test1 == 0) {
			$query1 = mysql_query("CREATE TABLE `edt_gr_profs` (
					`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
					`id_gr_nom` INT( 11 ) NOT NULL ,
					`id_utilisateurs` VARCHAR( 50 ) NOT NULL ,
					PRIMARY KEY ( `id` ));");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe d�j�');
		}

		$result .= "&nbsp;->Cr�ation de la table 'edt_gr_classes'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'edt_gr_classes'"));
		if ($test1 == 0) {
			$query1 = mysql_query("CREATE TABLE `edt_gr_classes` (
					`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
					`id_gr_nom` INT( 11 ) NOT NULL ,
					`id_classe` INT( 11 ) NOT NULL ,
					PRIMARY KEY ( `id` ));");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe d�j�');
		}


		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'nom_creneaux_s' � la table 'edt_setting'<br/>";
		$req_test = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'nom_creneaux_s'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO edt_setting VALUES ('', 'nom_creneaux_s', '1');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'edt_aff_salle' � la table 'edt_setting'<br/>";
		$req_test = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_salle'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO edt_setting VALUES ('', 'edt_aff_salle', 'nom');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'edt_aff_matiere' � la table 'edt_setting'<br/>";
		$req_test = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_matiere'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO edt_setting VALUES ('', 'edt_aff_matiere', 'long');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'edt_aff_creneaux' � la table 'edt_setting'<br/>";
		$req_test = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_creneaux'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO edt_setting VALUES ('', 'edt_aff_creneaux', 'noms');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'edt_aff_init_infos' � la table 'edt_setting'<br/>";
		$req_test = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_init_infos'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO edt_setting VALUES ('', 'edt_aff_init_infos', 'oui');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'edt_aff_couleur' � la table 'edt_setting'<br/>";
		$req_test = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_couleur'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO edt_setting VALUES ('', 'edt_aff_couleur', 'nb');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'edt_aff_init_infos2' � la table 'edt_setting'<br/>";
		$req_test = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_init_infos2'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO edt_setting VALUES ('', 'edt_aff_init_infos2', 'oui');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'aff_cherche_salle' � la table 'edt_setting'<br/>";
		$req_test = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'aff_cherche_salle'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO edt_setting VALUES ('', 'aff_cherche_salle', 'tous');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'param_menu_edt' � la table 'edt_setting'<br/>";
		$req_test = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'param_menu_edt'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO edt_setting VALUES ('', 'param_menu_edt', 'mouseover');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'scolarite_modif_cours' � la table 'edt_setting'<br/>";
		$req_test = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'scolarite_modif_cours'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO edt_setting VALUES ('' , 'scolarite_modif_cours', 'y');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'edt_calendrier_ouvert' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name='edt_calendrier_ouvert'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('edt_calendrier_ouvert', 'y');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'scolarite_modif_cours' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name = 'scolarite_modif_cours'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('scolarite_modif_cours', 'y');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'autorise_edt_tous' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name = 'autorise_edt_tous'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('autorise_edt_tous', 'y');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'autorise_edt_admin' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name = 'autorise_edt_admin'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('autorise_edt_admin', 'y');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'autorise_edt_eleve' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name = 'autorise_edt_eleve'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('autorise_edt_eleve', 'no');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'mod_edt_gr' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name = 'mod_edt_gr'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('mod_edt_gr', 'n');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}


		// Fin des ajouts concernant le dispositif EDT
		// ====================================


		// Multisite
		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'multisite' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name='multisite'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('multisite', 'n');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		//D�buts du dispositif sur les rss dans le cdt
		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'rss_cdt_eleve' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name = 'rss_cdt_eleve'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('rss_cdt_eleve', 'n');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		// statuts dynamiques
		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'statuts_prives' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name = 'statuts_prives'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('statuts_prives', 'n');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		// Cr�ation des tables sur les statuts priv�s
		$result .= "&nbsp;->Cr�ation de la table 'droits_statut'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'droits_statut'"));
		if ($test1 == 0) {
			$query1 = mysql_query("CREATE TABLE `droits_statut` (`id` int(11) NOT NULL auto_increment, `nom_statut` varchar(30) NOT NULL, PRIMARY KEY  (`id`));");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe d�j�');
		}

		$result .= "&nbsp;->Cr�ation de la table 'droits_utilisateurs'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'droits_utilisateurs'"));
		if ($test1 == 0) {
			$query1 = mysql_query("CREATE TABLE `droits_utilisateurs` (`id` int(11) NOT NULL auto_increment, `id_statut` int(11) NOT NULL, `login_user` varchar(50) NOT NULL, PRIMARY KEY  (`id`));");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe d�j�');
		}

		$result .= "&nbsp;->Cr�ation de la table 'droits_speciaux'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'droits_speciaux'"));
		if ($test1 == 0) {
			$query1 = mysql_query("CREATE TABLE `droits_speciaux` (`id` int(11) NOT NULL auto_increment, `id_statut` int(11) NOT NULL, `nom_fichier` varchar(200) NOT NULL, `autorisation` char(1) NOT NULL, PRIMARY KEY  (`id`));");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe d�j�');
		}

		// ========================================


		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'active_notanet' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name='active_notanet'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('active_notanet', 'n');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}


		//+++++++Modif li� � longmax_login++++++++++++
		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'longmax_login' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name='longmax_login'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('longmax_login', '10');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}



		//===================================================
		// AJOUT DU CHAMP id A LA TABLE eleves
		$result .= "&nbsp;->Ajout du champ 'id' � la table 'eleves'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM eleves LIKE 'id'"));
		if ($test1 == 0) {
			$test2 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM eleves LIKE 'id_eleve'"));
			if ($test2 == 0) {
				$query2 = mysql_query("ALTER TABLE `eleves` ADD UNIQUE (`login`);");
				if ($query2) {
					$query3 = mysql_query("ALTER TABLE `eleves` DROP PRIMARY KEY;");
					if ($query3) {
						$query4 = mysql_query("ALTER TABLE `eleves` ADD `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ;");
						if ($query4) {
							$result .= msj_ok();
						} else {
							$result .= msj_erreur();
						}
					} else {
						$result .= msj_erreur();
					}
				} else {
					$result .= msj_erreur();
				}
			}
			else{
				$result .= msj_present('Le champ a d�j� �t� trait�');
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}
		//===================================================


		//===================================================
		$result .= "&nbsp;->Extension � 255 caract�res du champ 'name' de la table 'preferences'<br />";
		$query = mysql_query("ALTER TABLE `preferences` CHANGE `name` `name` VARCHAR( 255 ) NOT NULL;");
		if ($query) {
			$result .= msj_ok();
		} else {
			$result .= msj_erreur();
		}
		//===================================================

		//===================================================
		$result .= "&nbsp;->Modification du champ 'id' de la table 'eleves' en 'id_eleve'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM eleves LIKE 'id'"));
		$test2 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM eleves LIKE 'id_eleve'"));
		if ($test1 == 0) {
			if ($test2 == 0) {
				$result .= msj_erreur("Le champ n'existe pas !!!");
			}
			else{
				$result .= msj_present('Le champ a d�j� �t� trait� !');
			}
		}
		else{
			if ($test2 == 0) {
				$query = mysql_query("ALTER TABLE `eleves` CHANGE `id` `id_eleve` INT( 11 ) NOT NULL AUTO_INCREMENT;");
				if ($query) {
					$result .= msj_ok();
				} else {
					$result .= msj_erreur();
				}
			}
			else{
				$result .= msj_erreur(": Vous avez � la fois le champ 'id' et le champ 'id_eleve' !");
			}
		}
		//===================================================

		//===================================================
		$result .= "&nbsp;->Ajout d'un champ 'numind' � la table 'utilisateurs'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM utilisateurs LIKE 'numind'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `utilisateurs` ADD `numind` VARCHAR( 255 ) NOT NULL ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}
		//===================================================


		//===================================================
		$result .= "&nbsp;->Ajout d'un champ 'nom_etab_gras' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'nom_etab_gras'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `model_bulletin` ADD `nom_etab_gras` TINYINT NOT NULL ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout d'un champ 'taille_texte_date_edition' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'taille_texte_date_edition'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `model_bulletin` ADD `taille_texte_date_edition` FLOAT NOT NULL ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout d'un champ 'taille_texte_matiere' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'taille_texte_matiere'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `model_bulletin` ADD `taille_texte_matiere` FLOAT NOT NULL ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout d'un champ 'active_moyenne_general' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'active_moyenne_general'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `model_bulletin` ADD `active_moyenne_general` TINYINT NOT NULL ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout d'un champ 'titre_bloc_avis_conseil' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'titre_bloc_avis_conseil'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `model_bulletin` ADD `titre_bloc_avis_conseil` VARCHAR( 50 ) NOT NULL ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout d'un champ 'taille_titre_bloc_avis_conseil' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'taille_titre_bloc_avis_conseil'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `model_bulletin` ADD `taille_titre_bloc_avis_conseil` FLOAT NOT NULL ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout d'un champ 'taille_profprincipal_bloc_avis_conseil' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'taille_profprincipal_bloc_avis_conseil'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `model_bulletin` ADD `taille_profprincipal_bloc_avis_conseil` FLOAT NOT NULL ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout d'un champ 'affiche_fonction_chef' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'affiche_fonction_chef'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `model_bulletin` ADD `affiche_fonction_chef` TINYINT NOT NULL ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout d'un champ 'taille_texte_fonction_chef' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'taille_texte_fonction_chef'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `model_bulletin` ADD `taille_texte_fonction_chef` FLOAT NOT NULL ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout d'un champ 'taille_texte_identitee_chef' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'taille_texte_identitee_chef'"));
		if ($test1 == 0) {
			$query = mysql_query("ALTER TABLE `model_bulletin` ADD `taille_texte_identitee_chef` FLOAT NOT NULL ;");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}


		$result .= "&nbsp;->Ajout des champs 'tel_texte', 'fax_image',... � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'tel_texte'"));
		if ($test1 == 0) {
			$sql="ALTER TABLE `model_bulletin` ADD `tel_image` VARCHAR( 20 ) NOT NULL ,
ADD `tel_texte` VARCHAR( 20 ) NOT NULL ,
ADD `fax_image` VARCHAR( 20 ) NOT NULL ,
ADD `fax_texte` VARCHAR( 20 ) NOT NULL ,
ADD `courrier_image` VARCHAR( 20 ) NOT NULL ,
ADD `courrier_texte` VARCHAR( 20 ) NOT NULL ,
ADD `largeur_bloc_eleve` FLOAT NOT NULL ,
ADD `hauteur_bloc_eleve` FLOAT NOT NULL ,
ADD `largeur_bloc_adresse` FLOAT NOT NULL ,
ADD `hauteur_bloc_adresse` FLOAT NOT NULL ,
ADD `largeur_bloc_datation` FLOAT NOT NULL ,
ADD `hauteur_bloc_datation` FLOAT NOT NULL ,
ADD `taille_texte_classe` FLOAT NOT NULL ,
ADD `type_texte_classe` VARCHAR( 1 ) NOT NULL ,
ADD `taille_texte_annee` FLOAT NOT NULL ,
ADD `type_texte_annee` VARCHAR( 1 ) NOT NULL ,
ADD `taille_texte_periode` FLOAT NOT NULL ,
ADD `type_texte_periode` VARCHAR( 1 ) NOT NULL ,
ADD `taille_texte_categorie_cote` FLOAT NOT NULL ,
ADD `taille_texte_categorie` FLOAT NOT NULL ,
ADD `type_texte_date_datation` VARCHAR( 1 ) NOT NULL ,
ADD `cadre_adresse` TINYINT NOT NULL ;";
			//echo "<br />$sql<br />";
			$query = mysql_query($sql);
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout des champs 'centrage_logo', 'ajout_cadre_blanc_photo',... � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'centrage_logo'"));
		if ($test1 == 0) {
			$sql="ALTER TABLE `model_bulletin` ADD `centrage_logo` TINYINT NOT NULL DEFAULT '0',
ADD `Y_centre_logo` FLOAT NOT NULL DEFAULT '18',
ADD `ajout_cadre_blanc_photo` TINYINT NOT NULL DEFAULT '0';";
			//echo "<br />$sql<br />";
			$query = mysql_query($sql);
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}


		// 20071130
		$result .= "&nbsp;->Ajout des champs 'affiche_moyenne_mini_general' et 'affiche_moyenne_maxi_general' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'affiche_moyenne_mini_general'"));
		if ($test1 == 0) {
			$sql="ALTER TABLE `model_bulletin` ADD `affiche_moyenne_mini_general` TINYINT NOT NULL DEFAULT '1',
ADD `affiche_moyenne_maxi_general` TINYINT NOT NULL DEFAULT '1';";
			//echo "<br />$sql<br />";
			$query = mysql_query($sql);
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout du champ 'affiche_date_edition' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'affiche_date_edition'"));
		if ($test1 == 0) {
			$sql="ALTER TABLE `model_bulletin` ADD `affiche_date_edition` TINYINT NOT NULL DEFAULT '1';";
			//echo "<br />$sql<br />";
			$query = mysql_query($sql);
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		$result .= "&nbsp;->Ajout du champ 'active_moyenne_general' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'active_moyenne_general'"));
		if ($test1 == 0) {
			$sql="ALTER TABLE `model_bulletin` ADD `active_moyenne_general` TINYINT NOT NULL DEFAULT '1';";
			//echo "<br />$sql<br />";
			$query = mysql_query($sql);
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}
		//===================================================
		// Ajout d'un champ pour les AID et le bulletin simplifi�
		$result .= "&nbsp;->Ajout du champ 'bull_simplifie' � la table 'aid_config'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM aid_config LIKE 'bull_simplifie'"));
		if ($test1 == 0) {
			$sql="ALTER TABLE `aid_config` ADD `bull_simplifie` CHAR(1) NOT NULL DEFAULT 'y';";
			$query = mysql_query($sql);
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('Le champ existe d�j�');
		}

		// Cr�ation de la table absences_rb
		$result .= "&nbsp;->Ajout de la table absences_rb. <br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'absences_rb'"));
		if ($test1 == 0) {
			$sql = "CREATE TABLE `absences_rb` (`id` int(5) NOT NULL auto_increment,
`eleve_id` varchar(30) NOT NULL,
`retard_absence` varchar(1) NOT NULL default 'A',
`groupe_id` varchar(8) NOT NULL,
`edt_id` int(5) NOT NULL default '0',
`jour_semaine` varchar(10) NOT NULL,
`creneau_id` int(5) NOT NULL,
`debut_ts` int(11) NOT NULL,
`fin_ts` int(11) NOT NULL,
`date_saisie` int(20) NOT NULL,
`login_saisie` varchar(30) NOT NULL, PRIMARY KEY  (`id`));";
			$query = mysql_query($sql);
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('La table existe d�j�');
		}

		// Cr�ation de la table matieres_appreciations_tempo
		$result .= "&nbsp;->Ajout de la table matieres_appreciations_tempo. <br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'matieres_appreciations_tempo'"));
		if ($test1 == 0) {
			$sql = "CREATE TABLE `matieres_appreciations_tempo` ( `login` varchar(50) NOT NULL default '',
`id_groupe` int(11) NOT NULL default '0',
`periode` int(11) NOT NULL default '0',
`appreciation` text NOT NULL, PRIMARY KEY  (`login`,`id_groupe`,`periode`));";
			$query = mysql_query($sql);
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('La table existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'utiliserMenuBarre' � la table 'setting'<br />";
		$req_test = mysql_query("SELECT value FROM setting WHERE name='utiliserMenuBarre'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query = mysql_query("INSERT INTO setting VALUES ('utiliserMenuBarre', 'no');");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'active_absences_parents' � la table 'setting'<br />";
		$req_test = mysql_query("SELECT value FROM setting WHERE name='active_absences_parents'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query = mysql_query("INSERT INTO setting VALUES ('active_absences_parents', 'no');");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		$result .= "&nbsp;->Ajout (si besoin) du param�tre 'creneau_different' � la table 'setting'<br />";
		$req_test = mysql_query("SELECT value FROM setting WHERE name='creneau_different'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query = mysql_query("INSERT INTO setting VALUES ('creneau_different', 'n');");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		// Cr�ation de la table absences_creneaux_bis
		$result .= "&nbsp;->Ajout de la table absences_creneaux_bis. <br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'absences_creneaux_bis'"));
		if ($test1 == 0) {
			$sql = "CREATE TABLE `absences_creneaux_bis` (
`id_definie_periode` int(11) NOT NULL auto_increment,
`nom_definie_periode` varchar(10) NOT NULL default '',
`heuredebut_definie_periode` time NOT NULL default '00:00:00',
`heurefin_definie_periode` time NOT NULL default '00:00:00',
`suivi_definie_periode` tinyint(4) NOT NULL,
`type_creneaux` varchar(15) NOT NULL,
PRIMARY KEY  (`id_definie_periode`));";
			$query = mysql_query($sql);
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('La table existe d�j�');
		}

		$result .= "&nbsp;->Ajout de la table edt_init. <br />";
		$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'edt_init'"));
		if ($test1 == 0) {
			$sql = "CREATE TABLE `edt_init`
(`id_init` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ident_export` VARCHAR( 100 ) NOT NULL ,
`nom_export` VARCHAR( 200 ) NOT NULL ,
`nom_gepi` VARCHAR( 200 ) NOT NULL);";
			$query = mysql_query($sql);
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		}
		else{
			$result .= msj_present('La table existe d�j�');
		}


		//Initialisation des param�tres li�s au module inscription
		$result_inter = "";
		$result .= "&nbsp;-> Initialisation des param�tres li�s au module inscription<br />";

		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'inscription_items'"));
		if ($test == 0) {
			$result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS inscription_items (id int(11) NOT NULL auto_increment, date varchar(20) NOT NULL default '', heure varchar(10) NOT NULL default '', description varchar(200) NOT NULL default '', PRIMARY KEY  (id));");
			if ($result_inter == '')
			$result .= msj_ok("La table inscription_items a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table inscription_items existe d�j�");
		}

		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'inscription_j_login_items'"));
		if ($test == 0) {
			$result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS inscription_j_login_items (login varchar(20) NOT NULL default '', id int(11) NOT NULL default '0');");
			if ($result_inter == '')
			$result .= msj_ok("La table inscription_j_login_items a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_ok("La table inscription_j_login_items existe d�j�.");
		}

		$req = sql_query1("SELECT VALUE FROM setting WHERE NAME = 'active_inscription'");
		if ($req == -1)
		$result_inter .= traite_requete("INSERT INTO setting VALUES ('active_inscription', 'n');");
		else
		$result .= msj_present("Le param�tre active_inscription existe d�j�.");
		$req = sql_query1("SELECT VALUE FROM setting WHERE NAME = 'active_inscription_utilisateurs'");
		if ($req == -1)
		$result_inter .= traite_requete("INSERT INTO setting VALUES ('active_inscription_utilisateurs', 'n');");
		else
		$result .= msj_present("Le param�tre active_inscription_utilisateurs existe d�j�.");

		$req = sql_query1("SELECT VALUE FROM setting WHERE NAME = 'mod_inscription_explication'");
		if ($req == -1)
		$result_inter .= traite_requete("INSERT INTO setting (NAME, VALUE) VALUES('mod_inscription_explication', '<p> <strong>Pr&eacute;sentation des dispositifs du Lyc&eacute;e dans les coll&egrave;ges qui organisent des rencontres avec les parents.</strong> <br />\r\n<br />\r\nChacun d&rsquo;entre vous conna&icirc;t la situation dans laquelle sont plac&eacute;s les &eacute;tablissements : </p>\r\n<ul>\r\n    <li>baisse d&eacute;mographique</li>\r\n    <li>r&eacute;gulation des moyens</li>\r\n    <li>- ... </li>\r\n</ul>\r\nCette ann&eacute;e encore nous devons &ecirc;tre pr&eacute;sents dans les r&eacute;unions organis&eacute;es au sein des coll&egrave;ges afin de pr&eacute;senter nos sp&eacute;cificit&eacute;s, notre valeur ajout&eacute;e, les &eacute;volution du projet, le label international, ... <br />\r\nsur cette feuille, vous avez la possibilit&eacute; de vous inscrire afin d''intervenir dans un ou plusieurs coll&egrave;ges selon vos convenances.');");
		else
		$result .= msj_present("Le param�tre mod_inscription_explication existe d�j�.");
		$req = sql_query1("SELECT VALUE FROM setting WHERE NAME = 'mod_inscription_titre'");
		if ($req == -1)
		$result_inter .= traite_requete("INSERT INTO setting (NAME, VALUE) VALUES('mod_inscription_titre', 'Intervention dans les coll�ges');");
		else
		$result .= msj_present("Le param�tre mod_inscription_titre existe d�j�.");

		if ($result_inter == '') {
			$result .= msj_ok();
		} else {
			$result .= $result_inter."<br />";
		}
		//
		// Outils compl�menraires de gestion des AID
		//
		$result_inter = "";
		$result .= "<br />&nbsp;->Ajout des param�tres li�s aux outils compl�mentaires de gestion des AIDs<br />";
		// Cr�ation de la table j_aidcateg_utilisateurs
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'j_aidcateg_utilisateurs'"));
		if ($test == 0) {
			$result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS j_aidcateg_utilisateurs (indice_aid INT NOT NULL ,id_utilisateur VARCHAR( 50 ) NOT NULL);");
			if ($result_inter == '')
			$result .= msj_ok("La table j_aidcateg_utilisateurs a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table j_aidcateg_utilisateurs existe d�j�.");
		}
		$result_inter = "";
		// Modification de la table aid_config
		$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM aid_config LIKE 'feuille_presence'"));
		if ($test == 0) {
			$result_inter .= traite_requete("ALTER TABLE aid_config ADD outils_complementaires ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n';");
			$result_inter .= traite_requete("ALTER TABLE aid_config ADD feuille_presence ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n';");
			if ($result_inter == '')
			$result .= msj_ok("Les champs outils_complementaires et feuille_presence dans la table aid_config ont �t� cr��s !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("Les champs outils_complementaires et feuille_presence dans la table aid_config existent d�j�.");
		}
		// Modification de la table aid
		$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM aid LIKE 'en_construction'"));
		if ($test == 0) {
			$result_inter = traite_requete("ALTER TABLE aid ADD perso1 VARCHAR( 255 ) NOT NULL ,
ADD perso2 VARCHAR( 255 ) NOT NULL ,
ADD perso3 VARCHAR( 255 ) NOT NULL ,
ADD productions VARCHAR( 100 ) NOT NULL ,
ADD resume TEXT NOT NULL ,
ADD famille SMALLINT( 6 ) NOT NULL ,
ADD mots_cles VARCHAR( 255 ) NOT NULL ,
ADD adresse1 VARCHAR( 255 ) NOT NULL ,
ADD adresse2 VARCHAR( 255 ) NOT NULL ,
ADD public_destinataire VARCHAR( 50 ) NOT NULL ,
ADD contacts TEXT NOT NULL ,
ADD divers TEXT NOT NULL ,
ADD matiere1 VARCHAR( 100 ) NOT NULL ,
ADD matiere2 VARCHAR( 100 ) NOT NULL ,
ADD eleve_peut_modifier ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' ,
ADD prof_peut_modifier ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' ,
ADD cpe_peut_modifier ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' ,
ADD fiche_publique ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' ,
ADD affiche_adresse1 ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' ,
ADD en_construction ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n'
;");
			if ($result_inter == '')
			$result .= msj_ok("Les champ productions, resume, famille, mots_cles, adresse1, adress2, public_destinataire, contacts, divers, matiere1, matiere2, eleve_peut_modifier, prof_peut_modifier, cpe_peut_modifier, fiche_publique, affiche_adresse1, en_construction, perso1, perso2, perso2 dans la table aid ont �t� cr��s !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("Les champ productions, resume, famille, mots_cles, adresse1, adress2, public_destinataire, contacts, divers, matiere1, matiere2, eleve_peut_modifier, prof_peut_modifier, cpe_peut_modifier, fiche_publique, affiche_adresse1, en_construction, perso1, perso2, perso2  dans la table aid existent d�j�.");
		}
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'j_aid_eleves_resp'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `j_aid_eleves_resp` (`id_aid` varchar(100) NOT NULL default '',`login` varchar(60) NOT NULL default '',`indice_aid` int(11) NOT NULL default '0',PRIMARY KEY  (`id_aid`,`login`));");
			if ($result_inter == '')
			$result .= msj_ok("La table j_aid_eleves_resp a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table j_aid_eleves_resp existe d�j�.");
		}
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'aid_familles'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `aid_familles` (`ordre_affichage` smallint(6) NOT NULL default '0',`id` smallint(6) NOT NULL default '0',`type` varchar(250) NOT NULL default '');");
			if ($result_inter == '')
			$result .= msj_ok("La table aid_familles a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table aid_familles est d�j� cr��e.");
		}
		$test = mysql_num_rows(mysql_query("select ordre_affichage from aid_familles"));
		if ($test == 0) {
			$result_inter = traite_requete("INSERT INTO `aid_familles` (`ordre_affichage`, `id`, `type`) VALUES
(0, 10, 'Information, presse'),
(1, 11, 'Philosophie et psychologie, pens�e'),
(2, 12, 'Religions'),
(3, 13, 'Sciences sociales, soci�t�, humanitaire'),
(4, 14, 'Langues, langage'),
(5, 15, 'Sciences (sciences dures)'),
(6, 16, 'Techniques, sciences appliqu�es, m�decine, cuisine...'),
(7, 17, 'Arts, loisirs et sports'),
(8, 18, 'Litt�rature, th��tre, po�sie'),
(9, 19, 'G�ographie et Histoire, civilisations anciennes');");
			if ($result_inter == '')
			$result .= msj_ok("La table aid_familles a �t� mise � jour !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table aid_familles est d�j� remplie.");
		}
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'aid_public'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `aid_public` (`ordre_affichage` smallint(6) NOT NULL default '0',`id` smallint(6) NOT NULL default '0',`public` varchar(100) NOT NULL default '');");
			if ($result_inter == '')
			$result .= msj_ok("La table aid_public a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table aid_public existe d�j�.");
		}
		$test = mysql_num_rows(mysql_query("select * from aid_public"));
		if ($test == 0) {
			$result_inter = traite_requete("INSERT INTO `aid_public` (`ordre_affichage`, `id`, `public`) VALUES
(3, 1, 'Lyc�ens'),
(2, 2, 'Coll�giens'),
(1, 3, 'Ecoliers'),
(6, 4, 'Grand public'),
(5, 5, 'Experts (ou sp�cialistes)'),
(4, 6, 'Etudiants');");
			if ($result_inter == '')
			$result .= msj_ok("La table aid_public a �t� mise � jour !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table aid_public est d�j� remplie.");
		}
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'aid_productions'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `aid_productions` (`id` smallint(6) NOT NULL auto_increment, `nom` varchar(100) NOT NULL default '', PRIMARY KEY  (`id`) );");
			if ($result_inter == '')
			$result .= msj_ok("La table aid_productions a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table aid_productions existe d�j�.");
		}
		$test = mysql_num_rows(mysql_query("select * from aid_productions"));
		if ($test == 0) {
			$result_inter = traite_requete("INSERT INTO `aid_productions` (`id`, `nom`) VALUES
(1, 'Dossier papier'),
(2, 'Emission de radio'),
(3, 'Exposition'),
(4, 'Film'),
(5, 'Spectacle'),
(6, 'R�alisation plastique'),
(7, 'R�alisation technique ou scientifique'),
(8, 'Jeu vid�o'),
(9, 'Animation culturelle'),
(10, 'Maquette'),
(11, 'Site internet'),
(12, 'Diaporama'),
(13, 'Production musicale'),
(14, 'Production th��trale'),
(15, 'Animation en milieu scolaire'),
(16, 'Programmation logicielle'),
(17, 'Journal');");
			if ($result_inter == '')
			$result .= msj_ok("La table aid_productions a �t� mise � jour !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table aid_productions est d�j� remplie.");
		}
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'droits_aid'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `droits_aid` (`id` varchar(200) NOT NULL default '',`public` char(1) NOT NULL default '',`professeur` char(1) NOT NULL default '',`cpe` char(1) NOT NULL default '',`scolarite` char(1) NOT NULL default '',`eleve` char(1) NOT NULL default '',`responsable` char(1) NOT NULL default 'F',`secours` char(1) NOT NULL default '',`description` varchar(255) NOT NULL default '',`statut` char(1) NOT NULL default '',PRIMARY KEY  (`id`));");
			if ($result_inter == '')
			$result .= msj_ok("La table des droits_aid a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table droits_aid existe d�j�.");
		}
		$test = mysql_num_rows(mysql_query("select * from droits_aid"));
		if ($test == 0) {
			$result_inter = traite_requete("INSERT INTO `droits_aid` VALUES('nom', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A pr�ciser', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('numero', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A pr�ciser', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('perso1', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'A pr�ciser', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('perso2', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'A pr�ciser', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('productions', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Production', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('resume', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'R�sum�', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('famille', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Famille', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('mots_cles', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Mots cl�s', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('adresse1', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Adresse publique', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('adresse2', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Adresse priv�e', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('public_destinataire', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Public destinataire', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('contacts', 'F', 'V', 'F', 'F', 'V', 'F', 'F', 'Contacts, ressources', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('divers', 'F', 'V', 'F', 'F', 'V', 'F', 'F', 'Divers', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('matiere1', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Discipline principale', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('matiere2', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Discipline secondaire', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('eleve_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A pr�ciser', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('cpe_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A pr�ciser', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('prof_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A pr�ciser', '0');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('fiche_publique', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A pr�ciser', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('affiche_adresse1', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A pr�ciser', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('en_construction', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A pr�ciser', '1');");
			$result_inter .= traite_requete("INSERT INTO `droits_aid` VALUES('perso3', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'A pr�ciser', '0');");

			if ($result_inter == '')
			$result .= msj_ok("La table des droits_aid a �t� mise � jour !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table droits_aid est d�j� remplie.");
		}
		$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM matieres LIKE 'matiere_aid'"));
		if ($test == 0) {
			$result_inter = traite_requete("ALTER TABLE `matieres` ADD `matiere_aid` CHAR( 1 ) DEFAULT 'n' NOT NULL , ADD `matiere_atelier` CHAR( 1 ) DEFAULT 'n' NOT NULL;");
			if ($result_inter == '')
			$result .= msj_ok("Les champs matiere_aid et matiere_atelier ont �t� ajout�s � la table matieres !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("Les champs matiere_aid et matiere_atelier existent d�j� dans la table matieres !");
		}
		$result .= "<br />&nbsp;->Ajout de la table table matieres_appreciations_grp<br />";
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'matieres_appreciations_grp'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE `matieres_appreciations_grp` ( `id_groupe` int(11) NOT NULL default '0', `periode` int(11) NOT NULL default '0', `appreciation` text NOT NULL, PRIMARY KEY  (`id_groupe`,`periode`));");
			if ($result_inter == '')
			$result .= msj_ok("La table matieres_appreciations_grp a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table matieres_appreciations_grp existe d�j�.");
		}

		$result .= "<br />&nbsp;->Tentative d'ajout du champ display_parents_app dans la table cn_devoirs.<br />";
		$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM cn_devoirs LIKE 'display_parents_app'"));
		if ($test == 0) {
			$result_inter = traite_requete("ALTER TABLE `cn_devoirs` ADD `display_parents_app` CHAR( 1 ) NOT NULL DEFAULT '0'");
			if ($result_inter == '') {
				$result .= msj_ok();
			} else {
				$result .= $result_inter;
			}
		} else {
			$result .= msj_present("Le champs existe d�j�.");
		}
		//==========================================================
		// Module Ateliers
		$result .= "<br />&nbsp;->Mise en place du module Ateliers<br />";
		$test = sql_query1("SELECT VALUE FROM setting WHERE NAME = 'active_ateliers'");
		if ($test == -1) {
			$result_inter = traite_requete("INSERT INTO setting (NAME, VALUE) VALUES('active_ateliers', 'n');");
			if ($result_inter == '') {
				$result .= msj_ok("Le param�tre active_ateliers a �t� cr��.");
			} else {
				$result .= $result_inter;
			}
		} else {
			$result .= msj_present("Le param�tre active_ateliers existe d�j�.");
		}

		$test = sql_query1("SHOW TABLES LIKE 'ateliers_config'");
		if ($test == -1) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `ateliers_config` (`nom_champ` char(100) NOT NULL default '', `content` char(255) NOT NULL default '',`param` char(100) NOT NULL default '');");
			if ($result_inter == '') {
				$result .= msj_ok("La table ateliers_config a �t� cr��e.");
			} else {
				$result .= $result_inter;
			}
		} else {
			$result .= msj_present("La table ateliers_config existe d�j�.");
		}

		//==========================================================
		// Trombinoscope
		$result .= "<br />&nbsp;->Trombinoscope<br />";

		$req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'param_module_trombinoscopes'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0) {
			$result_inter = traite_requete("INSERT INTO setting VALUES ('param_module_trombinoscopes', 'no_gep');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre param_module_trombinoscopes � no_gep: Ok !");
			}
			else{
				$result.=msj_erreur("D�finition du param�tre param_module_trombinoscopes � no_gep: Erreur !");
			}
		}else {
			$result .= msj_present("Le param�tre param_module_trombinoscopes existe d�j� dans la table setting.");
		}

		$req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'active_module_trombinoscopes'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0) {
			$result .= traite_requete("INSERT INTO setting VALUES ('active_module_trombinoscopes', 'y');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre active_module_trombinoscopes � y: Ok !");
			}
			else{
				$result.=msj_erreur("D�finition du param�tre active_module_trombinoscopes � y: Erreur !");
			}
		}else {
			$result .= msj_present("Le param�tre active_module_trombinoscopes existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'l_max_aff_trombinoscopes'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('l_max_aff_trombinoscopes', '120');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre l_max_aff_trombinoscopes � 120: Ok !");
			}
			else{
				$result.=msj_erreur("D�finition du param�tre l_max_aff_trombinoscopes � 120: Erreur !");
			}
		}else {
			$result .= msj_present("Le param�tre l_max_aff_trombinoscopes existe d�j� dans la table setting.");
		}
		$req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'h_max_aff_trombinoscopes'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('h_max_aff_trombinoscopes', '160');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre h_max_aff_trombinoscopes � 160: Ok !");
			}
			else{
				$result.=msj_erreur("D�finition du param�tre h_max_aff_trombinoscopes � 160: Erreur !");
			}
		}else {
			$result .= msj_present("Le param�tre h_max_aff_trombinoscopes existe d�j� dans la table setting.");
		}
		$req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'l_max_imp_trombinoscopes'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('l_max_imp_trombinoscopes', '70');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre l_max_imp_trombinoscopes � 70: Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre l_max_imp_trombinoscopes � 70: Erreur !");
			}
		}else {
			$result .= msj_present("Le param�tre l_max_imp_trombinoscopes existe d�j� dans la table setting.");
		}
		$req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'h_max_imp_trombinoscopes'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('h_max_imp_trombinoscopes', '100');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre h_max_imp_trombinoscopes � 100: Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre h_max_imp_trombinoscopes � 100: Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre h_max_imp_trombinoscopes existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'h_resize_trombinoscopes'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('h_resize_trombinoscopes', '160');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre h_resize_trombinoscopes � 160: Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre h_resize_trombinoscopes � 160: Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre h_resize_trombinoscopes existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'l_resize_trombinoscopes'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('l_resize_trombinoscopes', '120');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre l_resize_trombinoscopes � 120: Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre l_resize_trombinoscopes � 120: Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre l_resize_trombinoscopes existe d�j� dans la table setting.");
		}

		//==========================================================
		// AJOUT: boireaus 20080218
		//        Dispositif de restriction des acc�s aux appr�ciations pour les comptes responsables/eleves
		$result .= "<br />&nbsp;->Dispositif de restriction des acc�s aux appr�ciations pour les comptes responsables/eleves<br />";
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'matieres_appreciations_acces'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `matieres_appreciations_acces` (`id_classe` INT( 11 ) NOT NULL , `statut` VARCHAR( 255 ) NOT NULL , `periode` INT( 11 ) NOT NULL , `date` DATE NOT NULL , `acces` ENUM( 'y', 'n', 'date' ) NOT NULL );");
			if ($result_inter == '')
			$result .= msj_ok("La table matieres_appreciations_acces a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table matieres_appreciations_acces existe d�j�.");
		}

		$req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'GepiAccesRestrAccesAppProfP'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0) {
			$result_inter = traite_requete("INSERT INTO setting VALUES ('GepiAccesRestrAccesAppProfP', 'no');");
			if ($result_inter == '')
			$result .= msj_ok("Le param�tre GepiAccesRestrAccesAppProfP a �t� ajout� � la table setting !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("Le param�tre GepiAccesRestrAccesAppProfP existe d�j� dans la table setting.");
		}
		// Module archivage
		$result .= "<br />&nbsp;->Module archivage : ajout (si besoin) du param�tre 'active_annees_anterieures' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name='active_annees_anterieures'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('active_annees_anterieures', 'n');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}
		$result .= "<br />&nbsp;->Module archivage : Cr�ation des tables d'archivage<br />";
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'archivage_aids'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `archivage_aids` (`id` int(11) NOT NULL auto_increment,`annee` varchar(200) NOT NULL default '',`nom` varchar(100) NOT NULL default '',`id_type_aid` int(11) NOT NULL default '0',`productions` varchar(100) NOT NULL default '',`resume` text NOT NULL,`famille` smallint(6) NOT NULL default '0',`mots_cles` text NOT NULL,`adresse1` varchar(255) NOT NULL default '',`adresse2` varchar(255) NOT NULL default '',`public_destinataire` varchar(50) NOT NULL default '',`contacts` text NOT NULL,`divers` text NOT NULL,`matiere1` varchar(100) NOT NULL default '',`matiere2` varchar(100) NOT NULL default '',`fiche_publique` enum('y','n') NOT NULL default 'n',`affiche_adresse1` enum('y','n') NOT NULL default 'n',`en_construction` enum('y','n') NOT NULL default 'n',`notes_moyenne` varchar(255) NOT NULL,`notes_min` varchar(255) NOT NULL,`notes_max` varchar(255) NOT NULL,`responsables` text NOT NULL,`eleves` text NOT NULL,`eleves_resp` text NOT NULL, PRIMARY KEY  (`id`));");
			if ($result_inter == '')
			$result .= msj_ok("La table archivage_aids a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table archivage_aids existe d�j�.");
		}
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'archivage_eleves2'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `archivage_eleves2` (`annee` varchar(50) NOT NULL default '',`ine` varchar(50) NOT NULL,`doublant` enum('-','R') NOT NULL default '-',`regime` varchar(255) NOT NULL, PRIMARY KEY  (`ine`,`annee`));");
			if ($result_inter == '')
			$result .= msj_ok("La table archivage_eleves2 a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table archivage_eleves2 existe d�j�.");
		}
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'archivage_eleves'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `archivage_eleves` (`ine` varchar(255) NOT NULL,`nom` varchar(255) NOT NULL default '',`prenom` varchar(255) NOT NULL default '',`sexe` char(1) NOT NULL,`naissance` date NOT NULL default '0000-00-00', PRIMARY KEY  (`ine`),  KEY `nom` (`nom`));");
			if ($result_inter == '')
			$result .= msj_ok("La table archivage_eleves a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table archivage_eleves existe d�j�.");
		}
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'archivage_disciplines'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `archivage_disciplines` (`id` int(11) NOT NULL auto_increment,`annee` varchar(200) NOT NULL,`INE` varchar(255) NOT NULL,`classe` varchar(255) NOT NULL,`num_periode` tinyint(4) NOT NULL,`nom_periode` varchar(255) NOT NULL,`special` varchar(255) NOT NULL,`matiere` varchar(255) NOT NULL,`prof` varchar(255) NOT NULL,`note` varchar(255) NOT NULL,`moymin` varchar(255) NOT NULL,`moymax` varchar(255) NOT NULL,`moyclasse` varchar(255) NOT NULL,`rang` tinyint(4) NOT NULL,`appreciation` text NOT NULL,`nb_absences` int(11) NOT NULL,`non_justifie` int(11) NOT NULL,`nb_retards` int(11) NOT NULL, PRIMARY KEY  (`id`));");
			if ($result_inter == '')
			$result .= msj_ok("La table archivage_disciplines a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table archivage_disciplines existe d�j�.");
		}
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'archivage_appreciations_aid'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `archivage_appreciations_aid` (`id_eleve` varchar(255) NOT NULL,`annee` varchar(200) NOT NULL,`classe` varchar(255) NOT NULL,`id_aid` int(11) NOT NULL,`periode` int(11) NOT NULL default '0',`appreciation` text NOT NULL,`note_eleve` varchar(50) NOT NULL,`note_moyenne_classe` varchar(255) NOT NULL,`note_min_classe` varchar(255) NOT NULL,`note_max_classe` varchar(255) NOT NULL,PRIMARY KEY  (`id_eleve`,`id_aid`,`periode`));");
			if ($result_inter == '')
			$result .= msj_ok("La table archivage_appreciations_aid a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table archivage_appreciations_aid existe d�j�.");
		}
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'archivage_aid_eleve'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `archivage_aid_eleve` (`id_aid` int(11) NOT NULL default '0',`id_eleve` varchar(255) NOT NULL,`eleve_resp` char(1) NOT NULL default 'n',PRIMARY KEY  (`id_aid`,`id_eleve`));");
			if ($result_inter == '')
			$result .= msj_ok("La table archivage_aid_eleve a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table archivage_aid_eleve existe d�j�.");
		}
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'archivage_types_aid'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `archivage_types_aid` (`id` int(11) NOT NULL auto_increment,`annee` varchar(200) NOT NULL default '',`nom` varchar(100) NOT NULL default '',`nom_complet` varchar(100) NOT NULL default '',`note_sur` int(11) NOT NULL default '0',`type_note` varchar(5) NOT NULL default '', `display_bulletin` char(1) NOT NULL default 'y', PRIMARY KEY  (`id`));");
			if ($result_inter == '')
			$result .= msj_ok("La table archivage_types_aid a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
			$result .= msj_present("La table archivage_types_aid existe d�j�.");
		}

		// ================ modif jjocal ===============
		$result .= "<br />&nbsp;->Ajout (si besoin) du param�tre 'use_ent' � la table 'setting'<br/>";
		$req_test = mysql_query("SELECT value FROM setting WHERE name = 'use_ent'");
		$res_test = mysql_num_rows($req_test);
		if ($res_test == 0){
			$query3 = mysql_query("INSERT INTO setting VALUES ('use_ent', 'n');");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le param�tre existe d�j�');
		}

		// Modification Delineau
		// lorsque le trunk sera officiellement en 1.5.1, on supprimera ces lignes
		$result .= "<br />&nbsp;->Mise � jour des tables d'archivage.<br />";
		$result_inter = traite_requete("ALTER TABLE `archivage_aids` CHANGE `annee` `annee` VARCHAR( 200 ) NOT NULL");
		if ($result_inter == '')
		$result .= msj_ok("Le champ annee de la table archivage_aids a �t� modifi� !");
		else
		$result .= $result_inter."<br />";
		$result_inter = traite_requete("ALTER TABLE `archivage_appreciations_aid` CHANGE `annee` `annee` VARCHAR( 200 ) NOT NULL");
		if ($result_inter == '')
		$result .= msj_ok("Le champ annee de la table archivage_appreciations_aid a �t� modifi� !");
		else
		$result .= $result_inter."<br />";
		$result_inter = traite_requete("ALTER TABLE `archivage_disciplines` CHANGE `annee` `annee` VARCHAR( 200 ) NOT NULL");
		if ($result_inter == '')
		$result .= msj_ok("Le champ annee de la table archivage_disciplines a �t� modifi� !");
		else
		$result .= $result_inter."<br />";
		$result_inter = traite_requete("ALTER TABLE `archivage_eleves2` CHANGE `annee` `annee` VARCHAR( 50 ) NOT NULL");
		if ($result_inter == '')
		$result .= msj_ok("Le champ annee de la table archivage_eleves2 a �t� modifi� !");
		else
		$result .= $result_inter."<br />";
		$result_inter = traite_requete("ALTER TABLE `archivage_types_aid` CHANGE `annee` `annee` VARCHAR( 200 ) NOT NULL");
		if ($result_inter == '')
		$result .= msj_ok("Le champ annee de la table archivage_types_aid a �t� modifi� !");
		else
		$result .= $result_inter."<br />";
		$result_inter = traite_requete("ALTER TABLE `archivage_aid_eleve` CHANGE `id_aid` `id_aid` INT( 11 ) NOT NULL DEFAULT '0'");
		if ($result_inter == '')
		$result .= msj_ok("Le champ id_aid de la table archivage_aid_eleve a �t� modifi� !");
		else
		$result .= $result_inter."<br />";
		// Fin des lignes � supprimer quand la version stable sera sortie


		//==========================================================
		// Modification Delineau
		$result .= "<br />&nbsp;->Tentative de cr�ation de la table j_aid_utilisateurs_gest.<br />";
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'j_aid_utilisateurs_gest'"));
		if ($test == 0) {
			$result_inter = traite_requete("CREATE TABLE j_aid_utilisateurs_gest (id_aid varchar(100) NOT NULL default '', id_utilisateur varchar(50) NOT NULL default '', indice_aid int(11) NOT NULL default '0', PRIMARY KEY  (id_aid,id_utilisateur))");
			if ($result_inter == '')
			$result .= msj_ok("La table j_aid_utilisateurs_gest a �t� cr��e !");
			else
			$result .= $result_inter."<br />";
		} else {
  		$result .= msj_present("La table j_aid_utilisateurs_gest existe d�j�.");
		}

		//==========================================================
		$result .= "<br />&nbsp;->Ajout du champ 'affiche_ine' � la table 'model_bulletin'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM model_bulletin LIKE 'affiche_ine'"));
		if ($test1 == 0) {
			$query3 = mysql_query("ALTER TABLE model_bulletin ADD affiche_ine TINYINT NOT NULL;");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}
		//==========================================================


		//==========================================================
		$result .= "<br />&nbsp;->Contr�le/Mise � jour du dispositif Notanet/Fiches Brevet<br />";
		$temoin_notanet_err=0;

		$sql="CREATE TABLE IF NOT EXISTS notanet (
login varchar(50) NOT NULL default '',
ine text NOT NULL,
id_mat tinyint(4) NOT NULL,
notanet_mat varchar(255) NOT NULL,
matiere varchar(50) NOT NULL,
note varchar(4) NOT NULL default '',
note_notanet varchar(4) NOT NULL,
id_classe smallint(6) NOT NULL default '0'
);";
		$result_inter = traite_requete($sql);
		if ($result_inter != '') {
			$result .= "Erreur sur la cr�ation de la table 'notanet': ".$result_inter."<br />";
			$temoin_notanet_err++;
		}

		$sql="SHOW COLUMNS FROM notanet LIKE 'note_notanet';";
		$test1 = mysql_num_rows(mysql_query($sql));

		$sql="SHOW COLUMNS FROM notanet LIKE 'id_mat';";
		$test2 = mysql_num_rows(mysql_query($sql));

		$sql="SHOW COLUMNS FROM notanet LIKE 'notanet_mat';";
		$test3 = mysql_num_rows(mysql_query($sql));

		if(($test1 == 0)||($test2 == 0)||($test3 == 0)) {
			$result .= "Suppression de l'ancienne table 'notanet': ";
			$query3 = mysql_query("DROP TABLE notanet;");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
				$temoin_notanet_err++;
			}
		}

		$sql="CREATE TABLE IF NOT EXISTS notanet (
login varchar(50) NOT NULL default '',
ine text NOT NULL,
id_mat tinyint(4) NOT NULL,
notanet_mat varchar(255) NOT NULL,
matiere varchar(50) NOT NULL,
note varchar(4) NOT NULL default '',
note_notanet varchar(4) NOT NULL,
id_classe smallint(6) NOT NULL default '0'
);";
		$result_inter = traite_requete($sql);
		if ($result_inter != '') {
			$result .= "Erreur sur la cr�ation de la table 'notanet': ".$result_inter."<br />";
			$temoin_notanet_err++;
		}

		$sql="CREATE TABLE IF NOT EXISTS notanet_app (
login varchar(50) NOT NULL,
id_mat tinyint(4) NOT NULL,
matiere varchar(50) NOT NULL,
appreciation text NOT NULL,
id int(11) NOT NULL auto_increment,
PRIMARY KEY  (id)
);";
		$result_inter = traite_requete($sql);
		if ($result_inter != '') {
			$result .= "Erreur sur la cr�ation de la table 'notanet_app': ".$result_inter."<br />";
			$temoin_notanet_err++;
		}

		$sql="CREATE TABLE IF NOT EXISTS notanet_corresp (
id int(11) NOT NULL auto_increment,
type_brevet tinyint(4) NOT NULL,
id_mat tinyint(4) NOT NULL,
notanet_mat varchar(255) NOT NULL default '',
matiere varchar(50) NOT NULL default '',
statut enum('imposee','optionnelle','non dispensee dans l etablissement') NOT NULL default 'imposee',
PRIMARY KEY  (id)
);";
		$result_inter = traite_requete($sql);
		if ($result_inter != '') {
			$result .= "Erreur sur la cr�ation de la table 'notanet_corresp': ".$result_inter."<br />";
			$temoin_notanet_err++;
		}

		$sql="SHOW COLUMNS FROM notanet_corresp LIKE 'type_brevet';";
		$test1 = mysql_num_rows(mysql_query($sql));
		if($test1 == 0) {
			$result .= "<br />Ajout du champ 'type_brevet' � la table 'notanet_corresp': ";
			$query3 = mysql_query("ALTER TABLE notanet_corresp ADD type_brevet TINYINT NOT NULL AFTER id;");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
				$temoin_notanet_err++;
			}
		}

		$sql="SHOW COLUMNS FROM notanet_corresp LIKE 'id_mat';";
		$test1 = mysql_num_rows(mysql_query($sql));
		if($test1 == 0) {
			$result .= "<br />Ajout du champ 'id_mat' � la table 'notanet_corresp': ";
			$query3 = mysql_query("ALTER TABLE `notanet_corresp` ADD `id_mat` TINYINT NOT NULL AFTER `type_brevet` ;");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
				$temoin_notanet_err++;
			}
		}

		$sql="CREATE TABLE IF NOT EXISTS notanet_ele_type (
login varchar(50) NOT NULL,
type_brevet tinyint(4) NOT NULL,
PRIMARY KEY  (login)
);";
		$result_inter = traite_requete($sql);
		if ($result_inter != '') {
			$result .= "Erreur sur la cr�ation de la table 'notanet_ele_type': ".$result_inter."<br />";
			$temoin_notanet_err++;
		}

		$sql="CREATE TABLE IF NOT EXISTS notanet_verrou (
id_classe TINYINT NOT NULL ,
type_brevet TINYINT NOT NULL ,
verrouillage CHAR( 1 ) NOT NULL
);";
		$result_inter = traite_requete($sql);
		if ($result_inter != '') {
			$result .= "Erreur sur la cr�ation de la table 'notanet_verrou': ".$result_inter."<br />";
			$temoin_notanet_err++;
		}

		$sql="CREATE TABLE IF NOT EXISTS notanet_avis (
login VARCHAR( 50 ) NOT NULL ,
favorable ENUM( 'O', 'N', '' ) NOT NULL ,
avis TEXT NOT NULL ,
PRIMARY KEY ( login )
);";
		$result_inter = traite_requete($sql);
		if ($result_inter != '') {
			$result .= "Erreur sur la cr�ation de la table 'notanet_avis': ".$result_inter."<br />";
			$temoin_notanet_err++;
		}

		$sql="CREATE TABLE IF NOT EXISTS notanet_socles (
login VARCHAR( 50 ) NOT NULL ,
b2i ENUM( 'MS', 'ME', 'MN', 'AB', '' ) NOT NULL ,
a2 ENUM( 'MS', 'ME', 'MN', 'AB', '' ) NOT NULL ,
lv VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( login )
);";
		$result_inter = traite_requete($sql);
		if ($result_inter != '') {
			$result .= "Erreur sur la cr�ation de la table 'notanet_socles': ".$result_inter."<br />";
			$temoin_notanet_err++;
		}


		$result .= "<br />Contr�le des valeurs du champ 'b2i' de 'notanet_socles': ";
		$query3 = mysql_query("ALTER TABLE notanet_socles CHANGE b2i b2i ENUM( 'MS', 'ME', 'MN', 'AB', '' ) NOT NULL;");
		if ($query3) {
			$result .= msj_ok();
		} else {
			$result .= msj_erreur();
			$temoin_notanet_err++;
		}

		$result .= "<br />Contr�le des valeurs du champ 'a2' de 'notanet_socles': ";
		$query3 = mysql_query("ALTER TABLE notanet_socles CHANGE a2 a2 ENUM( 'MS', 'ME', 'MN', 'AB', '' ) NOT NULL;");
		if ($query3) {
			$result .= msj_ok();
		} else {
			$result .= msj_erreur();
			$temoin_notanet_err++;
		}

		$result .= "<br />Contr�le des valeurs du champ 'favorable' de 'notanet_avis': ";
		$query3 = mysql_query("ALTER TABLE notanet_avis CHANGE favorable favorable ENUM( 'O', 'N', '' ) NOT NULL;");
		if ($query3) {
			$result .= msj_ok();
		} else {
			$result .= msj_erreur();
			$temoin_notanet_err++;
		}



		if($temoin_notanet_err==0) {$result .= msj_ok();}


		//==========================================================

		$result .= "<br />Contr�le de la conversion de la table 'j_eleves_etablissements': ";

		$sql="SELECT 1=1 FROM setting WHERE name='conversion_j_eleves_etablissements';";
		$test_conv=mysql_query($sql);
		if(mysql_num_rows($test_conv)>0) {
			$result .= msj_present("D�j� effectu�e !");
			//echo "<p>La conversion a d�j� �t� effectu�e.</p>\n";
		}
		else {
			$cpt_correction_ok=0;
			$cpt_correction_err=0;
			$cpt_nettoyage_ok=0;
			$cpt_nettoyage_err=0;

			$result .= "<br />Remplacement du LOGIN par l'ELENOET dans la table 'j_eleves_etablissements': ";

			$sql="SELECT id_eleve FROM j_eleves_etablissements;";
			$res_ele_etab=mysql_query($sql);
			if(mysql_num_rows($res_ele_etab)>0) {
				while($lig_ee=mysql_fetch_object($res_ele_etab)) {
					$sql="SELECT elenoet FROM eleves WHERE login='$lig_ee->id_eleve';";
					$test_ele=mysql_query($sql);
					if(mysql_num_rows($test_ele)>0) {
						$lig_ele=mysql_fetch_object($test_ele);
						if($lig_ele->elenoet!="") {
							$sql="UPDATE j_eleves_etablissements SET id_eleve='$lig_ele->elenoet' WHERE id_eleve='$lig_ee->id_eleve';";
							$correction=mysql_query($sql);
							if($correction) {
								$cpt_correction_ok++;
							}
							else {
								$cpt_correction_err++;
							}
						}
					}
					else {
						// On a une scorie: �l�ve qui n'est plus dans la table 'eleves'
						$sql="DELETE FROM j_eleves_etablissements WHERE id_eleve='$lig_ee->id_eleve';";
						$nettoyage=mysql_query($sql);
						if($nettoyage) {
							$cpt_nettoyage_ok++;
						}
						else {
							$cpt_nettoyage_err++;
						}
					}
				}
			}

			$result .= "<p>R�sultat des conversions:</p>\n";
			$result .= "<table class='boireaus' border='1' summary='Compte-rendu'>\n";
			$result .= "<tr><th>&nbsp;</th><th>Succ�s</th><th>Echec</th></tr>\n";
			$result .= "<tr><th>Conversion</th><td>$cpt_correction_ok</td><td>$cpt_correction_err</td></tr>\n";
			$result .= "<tr><th>Suppression de scories</th><td>$cpt_nettoyage_ok</td><td>$cpt_nettoyage_err</td></tr>\n";
			$result .= "</table>\n";

			$sql="INSERT INTO setting SET name='conversion_j_eleves_etablissements', value='effectuee';";
			$res_temoin=mysql_query($sql);
			if($res_temoin) {
				$result .= "<p>Mise en place d'un t�moin indiquant que la conversion est effectu�e.</p>\n";
			}
			else {
				$result .= "<p>ECHEC de la mise en place d'un t�moin indiquant que la conversion est effectu�e.</p>\n";
			}


		}



		$result .= "<br />&nbsp;->Ajout du champ 'lieu_naissance' � la table 'eleves'<br />";
		$test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM eleves LIKE 'lieu_naissance'"));
		if ($test1 == 0) {
			$query3 = mysql_query("ALTER TABLE eleves ADD lieu_naissance VARCHAR( 50 ) NOT NULL AFTER naissance ;");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe d�j�');
		}

		$test = sql_query1("SHOW TABLES LIKE 'communes'");
		if ($test == -1) {
			$result .= "<br />Cr�ation de la table 'communes'. ";
			$sql="CREATE TABLE IF NOT EXISTS communes (
code_commune_insee VARCHAR( 50 ) NOT NULL ,
departement VARCHAR( 50 ) NOT NULL ,
commune VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( code_commune_insee )
);";
			$result_inter = traite_requete($sql);
			if ($result_inter != '') {
				$result .= "<br />Erreur sur la cr�ation de la table 'communes': ".$result_inter."<br />";
				$temoin_notanet_err++;
			}
		}

		$test = sql_query1("SHOW TABLES LIKE 'commentaires_types'");
		if ($test == -1) {
			$result .= "<br />Cr�ation de la table 'commentaires_types'. ";
			$sql="CREATE TABLE IF NOT EXISTS commentaires_types (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
commentaire TEXT NOT NULL ,
num_periode INT NOT NULL ,
id_classe INT NOT NULL
);";
			$result_inter = traite_requete($sql);
			if ($result_inter != '') {
				$result .= "<br />Erreur sur la cr�ation de la table 'commentaires_types': ".$result_inter."<br />";
				$temoin_notanet_err++;
			}
		}


		$test = sql_query1("SHOW TABLES LIKE 'modele_bulletin'");
		if ($test == -1) {
			$sql="SELECT * FROM model_bulletin;";
			$res_model=mysql_query($sql);
			if(mysql_num_rows($res_model)>0) {
				$cpt=0;
				while($tab_model[$cpt]=mysql_fetch_assoc($res_model)) {
					$id_model[$cpt]=$tab_model[$cpt]['id_model_bulletin'];
					//echo "\$id_model[$cpt]=\$tab_model[$cpt]['id_model_bulletin']=".$tab_model[$cpt]['id_model_bulletin']."<br />";
					$cpt++;
				}
/*
for($i=0;$i<count($tab_model);$i++) {
if(!empty($tab_model[$i])) {
	//echo "<p>\$tab_model[$i]</p>";
	echo "<p>Enregistrement \$tab_model[$i] de l'ancienne table 'model_bulletin'.</p>\n";
	echo "<table border='1'>\n";
	foreach($tab_model[$i] as $key => $value) {
		echo "<tr>\n";
		echo "<th>$key</th>\n";
		echo "<td>$value</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
}
}
*/
				//$sql="DROP TABLE modele_bulletin;";
				//$nettoyage=mysql_query($sql);

				$result .= "<br />Cr�ation de latable 'modele_bulletin'<br />";
				$sql="CREATE TABLE IF NOT EXISTS modele_bulletin (
id_model_bulletin INT( 11 ) NOT NULL ,
nom VARCHAR( 255 ) NOT NULL ,
valeur VARCHAR( 255 ) NOT NULL
);";
				$res_model=mysql_query($sql);
				if(!$res_model) {
					$result .= msj_erreur();
					//echo "<p>ERREUR sur $sql</p>\n";
				}
				else {
					for($i=0;$i<count($tab_model);$i++) {
						$cpt=0;
						//if(isset($tab_model[$i])) {
						if(!empty($tab_model[$i])) {
							//echo "<p>\$tab_model[$i]: ";
							$result .= "Enregistrements d'apr�s \$tab_model[$i] dans la nouvelle table 'modele_bulletin': ";
							foreach($tab_model[$i] as $key => $value) {
								if($cpt>0) {$result .= ", ";}

								$sql="INSERT INTO modele_bulletin SET id_model_bulletin='".$id_model[$i]."', nom='".$key."', valeur='".$value."';";
								$insert=mysql_query($sql);
								if($insert) {
									$result .= "<span style='color:green;'>$key:$value</span> ";
								}
								else {
									$result .= "<span style='color:red;'>$key:$value</span> ";
								}
								$cpt++;
							}
							$result .= "<br />\n";
						}
					}
				}
			}
			else {
				$result .= "<br /><span style='color:red;'>Erreur:</span> L'ancienne table 'model_bulletin' semble absente???<br />";
			}
		}
		else {
			$result .= "<br />La table 'modele_bulletin' existe d�j�.<br />";
		}

		//==========================================================
		// ALTER TABLE `ct_devoirs_entry` ADD `vise` CHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `contenu` ;
		// ALTER TABLE `ct_entry` ADD `vise` VARCHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `contenu` ;
		// ALTER TABLE `ct_entry` ADD `visa` VARCHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `vise` ;

		// Modification de la base suite au dispositif de visa des cahiers de textes
		$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM ct_devoirs_entry LIKE 'vise'"));
		if ($test == 0) {
			$result_inter .= traite_requete("ALTER TABLE `ct_devoirs_entry` ADD `vise` CHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `contenu` ;");
			if ($result_inter == '') {
				$result .= msj_ok("Le champ vise a bien �t� cr�� dans la table ct_devoirs_entry !");
			} else {
				$result .= $result_inter."<br />";
			}
		} else {
			$result .= msj_present("Le champ vise dans la table ct_devoirs_entry existe d�j�.");
		}

		$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM ct_entry LIKE 'vise'"));
		if ($test == 0) {
			$result_inter .= traite_requete("ALTER TABLE `ct_entry` ADD `vise` VARCHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `contenu` ;");
			if ($result_inter == '') {
				$result .= msj_ok("Le champ vise a bien �t� cr�� dans la table ct_entry !");
			} else {
				$result .= $result_inter."<br />";
			}
		} else {
			$result .= msj_present("Le champ vise dans la table ct_entry existe d�j�.");
		}

		$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM ct_entry LIKE 'visa'"));
		if ($test == 0) {
			$result_inter .= traite_requete("ALTER TABLE `ct_entry` ADD `visa` VARCHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `vise` ;");
			if ($result_inter == '') {
				$result .= msj_ok("Le champ visa a bien �t� cr�� dans la table ct_entry !");
			} else {
				$result .= $result_inter."<br />";
			}
		} else {
			$result .= msj_present("Le champ visa dans la table ct_entry existe d�j�.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'visa_cdt_inter_modif_notices_visees'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('visa_cdt_inter_modif_notices_visees', 'yes');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre visa_cdt_inter_modif_notices_visees � 'yes': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre visa_cdt_inter_modif_notices_visees � 'yes': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre visa_cdt_inter_modif_notices_visees existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'texte_visa_cdt'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('texte_visa_cdt', 'Cahier de textes vis� ce jour <br />Le Principal <br /> M. XXXXX<br />');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre texte_visa_cdt : Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre texte_visa_cdt : Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre texte_visa_cdt existe d�j� dans la table setting.");
		}



		// Modification de la table utilisateurs (ajout de auth_mode)
		$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM utilisateurs LIKE 'auth_mode'"));
		if ($test == 0) {
			if($is_lcs_plugin=='yes') {
				$result_inter .= traite_requete("ALTER TABLE utilisateurs ADD auth_mode ENUM( 'gepi', 'ldap', 'sso' ) NOT NULL DEFAULT 'sso';");
			}
			else {
				$result_inter .= traite_requete("ALTER TABLE utilisateurs ADD auth_mode ENUM( 'gepi', 'ldap', 'sso' ) NOT NULL DEFAULT 'gepi';");
			}
			if ($result_inter == '') {
				$result .= msj_ok("Le champ auth_mode a bien �t� cr�� dans la table utilisateurs !");
				if (getSettingValue("use_sso") == 'yes') {
					$update = mysql_query("UPDATE utilisateurs SET auth_mode = 'sso'");
				}
			} else {
				$result .= $result_inter."<br />";
			}
		} else {
			$result .= msj_present("Le champ auth_mode dans la table utilisateurs existe d�j�.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'auth_locale'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			if($is_lcs_plugin=='yes') {
				$valeur_auth_locale='no';
			}
			else {
				$valeur_auth_locale='yes';
			}
			$result_inter = traite_requete("INSERT INTO setting VALUES ('auth_locale', '$valeur_auth_locale');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre auth_locale � '$valeur_auth_locale': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre auth_locale � '$valeur_auth_locale': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre auth_locale existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'auth_ldap'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('auth_ldap', 'no');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre auth_ldap � 'no': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre auth_ldap � 'no': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre auth_ldap existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'auth_sso'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			if($is_lcs_plugin=='yes') {
				$valeur_auth_sso='lcs';
			}
			else {
				$valeur_auth_sso='no';
			}
			$result_inter = traite_requete("INSERT INTO setting VALUES ('auth_sso', '$valeur_auth_sso');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre auth_sso � '$valeur_auth_sso': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre auth_sso � '$valeur_auth_sso': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre auth_sso existe d�j� dans la table setting.");
		}

		if (getSettingValue('use_sso') == 'yes') {
			saveSetting('auth_sso','yes');
			saveSetting('auth_locale','no');
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'use_sso'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==1){
			$result_inter = traite_requete("DELETE FROM setting WHERE (name = 'use_sso');");
			if ($result_inter == '') {
				$result.=msj_ok("Suppression du param�tre use_sso: Ok !");
			} else {
				$result.=msj_erreur("Suppression du param�tre use_sso: Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre use_sso est d�j� supprim� de la table setting.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'may_import_user_profile'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('may_import_user_profile', 'no');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre may_import_user_profile � 'no': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre may_import_user_profile � 'no': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre may_import_user_profile existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'ldap_write_access'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('ldap_write_access', 'no');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre ldap_write_access � 'no': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre ldap_write_access � 'no': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre ldap_write_access existe d�j� dans la table setting.");
		}


		$test = sql_query1("SHOW TABLES LIKE 'commentaires_types_profs'");
		if ($test == -1) {
			$result .= "<br />Cr�ation de la table 'commentaires_types_profs'. ";
			$sql="CREATE TABLE IF NOT EXISTS commentaires_types_profs (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
login VARCHAR( 255 ) NOT NULL ,
app TEXT NOT NULL
);";
			$result_inter = traite_requete($sql);
			if ($result_inter != '') {
				$result .= "<br />Erreur sur la cr�ation de la table 'commentaires_types_profs': ".$result_inter."<br />";
			}
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'denomination_professeur'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('denomination_professeur', 'professeur');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre denomination_professeur � 'professeur': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre denomination_professeur � 'professeur': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre denomination_professeur existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'denomination_professeurs'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('denomination_professeurs', 'professeurs');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre denomination_professeurs � 'professeurs': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre denomination_professeurs � 'professeurs': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre denomination_professeurs existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'denomination_responsable'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('denomination_responsable', 'responsable l�gal');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre denomination_responsable � 'responsable l�gal': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre denomination_responsable � 'responsable l�gal': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre denomination_responsable existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'denomination_responsables'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('denomination_responsables', 'responsables l�gaux');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre denomination_responsables � 'responsables l�gaux': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre denomination_responsables � 'responsables l�gaux': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre denomination_responsables existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'denomination_eleve'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('denomination_eleve', '�l�ve');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre denomination_eleve � '�l�ve': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre denomination_eleve � '�l�ve': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre denomination_eleve existe d�j� dans la table setting.");
		}

		$req_test=mysql_query("SELECT value FROM setting WHERE name = 'denomination_eleves'");
		$res_test=mysql_num_rows($req_test);
		if ($res_test==0){
			$result_inter = traite_requete("INSERT INTO setting VALUES ('denomination_eleves', '�l�ves');");
			if ($result_inter == '') {
				$result.=msj_ok("D�finition du param�tre denomination_eleves � '�l�ves': Ok !");
			} else {
				$result.=msj_erreur("D�finition du param�tre denomination_eleves � '�l�ves': Erreur !");
			}
		} else {
			$result .= msj_present("Le param�tre denomination_eleves existe d�j� dans la table setting.");
		}

		// Ajouts d'index
		$result .= "&nbsp;->Ajout de l'index 'statut' � la table utilisateurs<br />";
		//$req_test = mysql_query("SHOW INDEX FROM utilisateurs WHERE Key_name = 'statut'");
		//$req_res = mysql_num_rows($req_test);
		$req_res=0;
		$req_test = mysql_query("SHOW INDEX FROM utilisateurs ");
		if (mysql_num_rows($req_test)!=0) {
			while ($enrg = mysql_fetch_object($req_test)) {
				if ($enrg-> Key_name == 'statut') {$req_res++;}
			}
		}
		if ($req_res == 0) {
			$query = mysql_query("ALTER TABLE `utilisateurs` ADD INDEX statut ( `statut` )");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("L'index existe d�j�.");
		}

		$result .= "&nbsp;->Ajout de l'index 'etat' � la table utilisateurs<br />";
		//$req_test = mysql_query("SHOW INDEX FROM utilisateurs WHERE Key_name = 'etat'");
		//$req_res = mysql_num_rows($req_test);
		$req_res=0;
		$req_test = mysql_query("SHOW INDEX FROM utilisateurs ");
		if (mysql_num_rows($req_test)!=0) {
			while ($enrg = mysql_fetch_object($req_test)) {
				if ($enrg-> Key_name == 'etat') {$req_res++;}
			}
		}
		if ($req_res == 0) {
			$query = mysql_query("ALTER TABLE `utilisateurs` ADD INDEX etat ( `etat` )");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("L'index existe d�j�.");
		}


		$result .= "&nbsp;->Ajout de l'index 'login' � la table resp_pers<br />";
		//$req_test = mysql_query("SHOW INDEX FROM resp_pers WHERE Key_name = 'login'");
		//$req_res = mysql_num_rows($req_test);
		$req_res=0;
		$req_test = mysql_query("SHOW INDEX FROM resp_pers ");
		if (mysql_num_rows($req_test)!=0) {
			while ($enrg = mysql_fetch_object($req_test)) {
				if ($enrg-> Key_name == 'login') {$req_res++;}
			}
		}
		if ($req_res == 0) {
			$query = mysql_query("ALTER TABLE `resp_pers` ADD INDEX login ( `login` )");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("L'index existe d�j�.");
		}

		$result .= "&nbsp;->Ajout de l'index 'adr_id' � la table resp_pers<br />";
		//$req_test = mysql_query("SHOW INDEX FROM resp_pers WHERE Key_name = 'adr_id'");
		//$req_res = mysql_num_rows($req_test);
		$req_res=0;
		$req_test = mysql_query("SHOW INDEX FROM resp_pers ");
		if (mysql_num_rows($req_test)!=0) {
			while ($enrg = mysql_fetch_object($req_test)) {
				if ($enrg-> Key_name == 'adr_id') {$req_res++;}
			}
		}
		if ($req_res == 0) {
			$query = mysql_query("ALTER TABLE `resp_pers` ADD INDEX adr_id ( `adr_id` )");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("L'index existe d�j�.");
		}


		$result .= "&nbsp;->Ajout de l'index 'pers_id' � la table responsables2<br />";
		//$req_test = mysql_query("SHOW INDEX FROM responsables2 WHERE Key_name = 'pers_id'");
		//$req_res = mysql_num_rows($req_test);
		$req_res=0;
		$req_test = mysql_query("SHOW INDEX FROM responsables2 ");
		if (mysql_num_rows($req_test)!=0) {
			while ($enrg = mysql_fetch_object($req_test)) {
				if ($enrg-> Key_name == 'pers_id') {$req_res++;}
			}
		}
		if ($req_res == 0) {
			$query = mysql_query("ALTER TABLE `responsables2` ADD INDEX pers_id ( `pers_id` )");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("L'index existe d�j�.");
		}

		$result .= "&nbsp;->Ajout de l'index 'ele_id' � la table responsables2<br />";
		//$req_test = mysql_query("SHOW INDEX FROM responsables2 WHERE Key_name = 'ele_id'");
		//$req_res = mysql_num_rows($req_test);
		$req_res=0;
		$req_test = mysql_query("SHOW INDEX FROM responsables2 ");
		if (mysql_num_rows($req_test)!=0) {
			while ($enrg = mysql_fetch_object($req_test)) {
				if ($enrg-> Key_name == 'ele_id') {$req_res++;}
			}
		}
		if ($req_res == 0) {
			$query = mysql_query("ALTER TABLE `responsables2` ADD INDEX ele_id ( `ele_id` )");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("L'index existe d�j�.");
		}

		$result .= "&nbsp;->Ajout de l'index 'resp_legal' � la table responsables2<br />";
		//$req_test = mysql_query("SHOW INDEX FROM responsables2 WHERE Key_name = 'resp_legal'");
		//$req_res = mysql_num_rows($req_test);
		$req_res=0;
		$req_test = mysql_query("SHOW INDEX FROM responsables2 ");
		if (mysql_num_rows($req_test)!=0) {
			while ($enrg = mysql_fetch_object($req_test)) {
				if ($enrg-> Key_name == 'resp_legal') {$req_res++;}
			}
		}
		if ($req_res == 0) {
			$query = mysql_query("ALTER TABLE `responsables2` ADD INDEX resp_legal ( `resp_legal` )");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("L'index existe d�j�.");
		}

		$result .= "&nbsp;->Ajout de l'index 'ele_id' � la table eleves<br />";
		//$req_test = mysql_query("SHOW INDEX FROM eleves WHERE Key_name = 'ele_id'");
		//$req_res = mysql_num_rows($req_test);
		$req_res=0;
		$req_test = mysql_query("SHOW INDEX FROM eleves ");
		if (mysql_num_rows($req_test)!=0) {
			while ($enrg = mysql_fetch_object($req_test)) {
				if ($enrg-> Key_name == 'ele_id') {$req_res++;}
			}
		}
		if ($req_res == 0) {
			$query = mysql_query("ALTER TABLE `eleves` ADD INDEX ele_id ( `ele_id` )");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("L'index existe d�j�.");
		}

		$result .= "&nbsp;->Ajout de l'index 'id_classe' � la table j_eleves_classes<br />";
		//$req_test = mysql_query("SHOW INDEX FROM j_eleves_classes WHERE Key_name = 'id_classe'");
		//$req_res = mysql_num_rows($req_test);
		$req_res=0;
		$req_test = mysql_query("SHOW INDEX FROM j_eleves_classes ");
		if (mysql_num_rows($req_test)!=0) {
			while ($enrg = mysql_fetch_object($req_test)) {
				if ($enrg-> Key_name == 'id_classe') {$req_res++;}
			}
		}
		if ($req_res == 0) {
			$query = mysql_query("ALTER TABLE `j_eleves_classes` ADD INDEX id_classe ( `id_classe` )");
			if ($query) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("L'index existe d�j�.");
		}

?>
