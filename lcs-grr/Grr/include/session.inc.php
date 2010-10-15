<?php
#########################################################################
#                            session.inc.php                            #
#                                                                       #
#        Biblioth�que de fonctions g�rant les sessions                  #
#                Derni�re modification : 19/11/2006                     #
#                                                                       #
#########################################################################
/*
 * Copyright 2003-2005 Laurent Delineau
 * D'apr�s http://mrbs.sourceforge.net/
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GRR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GRR; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * Open a new session
 *
 * Check the provided login and password
 * Register data from the database to the session cookie
 * Log the session
 *
 * Returns 1 if login succeeded, >= 1 otherwise
 *
 * @_login              string                  Login of the user
 * @_password           string                  Password
 *
 * @return              bool                    The session is open
 */

function grr_opensession($_login, $_password, $_user_ext_authentifie = '', $tab_login=array(), $tab_groups=array())
{
    // Initialisation de $auth_ldap
    $auth_ldap = 'no';
    // On traite le cas o� l'utilisateur a �t� authentifi� par SSO
    if ($_user_ext_authentifie != '') {
        // Statut par d�faut
        $_statut = "";
        $sso = getSettingValue("sso_statut");
        if ($sso == "cas_visiteur") $_statut = "visiteur";
        else if ($sso == "cas_utilisateur") $_statut = "utilisateur";
        else if ($sso == "lemon_visiteur") $_statut = "visiteur";
        else if ($sso == "lemon_utilisateur") $_statut = "utilisateur";
        else if ($sso == "http_visiteur") $_statut = "visiteur";
        else if ($sso == "http_utilisateur") $_statut = "utilisateur";
        else if ($sso == "lasso_visiteur") $_statut = "visiteur";
        else if ($sso == "lasso_utilisateur") $_statut = "utilisateur";
        else if ($sso == "lcs") {
            if ($_user_ext_authentifie == "lcs_eleve") $_statut = getSettingValue("lcs_statut_eleve");
            if ($_user_ext_authentifie == "lcs_non_eleve") $_statut = getSettingValue("lcs_statut_prof");
            $temoin_grp_ok="non";
            if (trim(getSettingValue("lcs_liste_groupes_autorises")) == "") {
                $temoin_grp_ok="oui";
            } else {
                $tab_grp_autorise=explode(";",getSettingValue("lcs_liste_groupes_autorises"));
                for($i=0;$i<count($tab_grp_autorise);$i++) {
                    if (in_array($tab_grp_autorise[$i],$tab_groups)) {
                        $temoin_grp_ok="oui";
                    }
                }
            }
            // Si l'utilisateur n'appartient pas aux groupes LCS autoris�s
            if ($temoin_grp_ok != 'oui') {
                 return "5";
                 die();
            }

        }
        $sql = "select upper(login) login, password, prenom, nom, statut, now() start, default_area, default_room, default_style, default_list_type, default_language, source
        from grr_utilisateurs
        where login = '" . protect_data_sql($_login) . "' and ";
        if ($_user_ext_authentifie != 'lasso') $sql .= " password = '' and ";
        $sql .= " etat != 'inactif'";
        $res_user = grr_sql_query($sql);
        $num_row = grr_sql_count($res_user);
        if ($num_row == 1) {  // L'utilisateur est pr�sent dans la base locale
            if ($sso == "lcs") { // Mise � jour des donn�es
                $nom_user = $tab_login["nom"];
                $email_user = $tab_login["email"];
                $prenom_user = $tab_login["fullname"];
                // On met � jour
                $sql = "UPDATE grr_utilisateurs SET
                nom='".protect_data_sql($nom_user)."',
                prenom='".protect_data_sql($prenom_user)."',
                email='".protect_data_sql($email_user)."'
                where login='".protect_data_sql($_login)."'";
            }
            if (grr_sql_command($sql) < 0)
                {fatal_error(0, get_vocab("msg_login_created_error") . grr_sql_error());
                return "2";
                die();
            }
            // on r�cup�re les donn�es de l'utilisateur dans $row
            $row = grr_sql_row($res_user,0);
        } else {
            // L'utilisateur n'est pas pr�sent dans la base locale ou est inactif
            //  ou poss�de un mot de passe (utilisateur local GRR)
            // On teste si un utilisateur porte d�j� le m�me login
            $test = grr_sql_query1("select login from grr_utilisateurs where login = '".protect_data_sql($_login)."'");
            if ($test != '-1') {
                // le login existe d�j� : impossible d'importer le profil.
                return "3";
                die();
            } else {
                if ($sso == "lcs") {
                    if ($_statut == 'aucun') {
                        // L'utilisateur n'est pas autoris� � se connecter � GRR
                        return "5";
                        die();
                    } else {
                        $nom_user = $tab_login["nom"];
                        $email_user = $tab_login["email"];
                        $prenom_user = $tab_login["fullname"];
                     }
                } else if ($sso == "lasso_visiteur" or $sso == "lasso_utilisateur") {
                    if (!empty($tab_login)) {
                        $nom_user = $tab_login["nom"];
                        $email_user = $tab_login["email"];
                        $prenom_user = $tab_login["fullname"];
                     }
                } else {
                    $nom_user = $_login;
                    $email_user = "";
                    $prenom_user = "";
                }

                // On ins�re le nouvel utilisateur
                $sql = "INSERT INTO grr_utilisateurs SET
                nom='".protect_data_sql($nom_user)."',
                prenom='".protect_data_sql($prenom_user)."',
                login='".protect_data_sql($_login)."',
                password='',
                statut='".$_statut."',
                email='".protect_data_sql($email_user)."',
                etat='actif',
                source='ext'";
                if (grr_sql_command($sql) < 0)
                    {fatal_error(0, get_vocab("msg_login_created_error") . grr_sql_error());
                    return "2";
                    die();
                }
                // on r�cup�re les donn�es de l'utilisateur
                $sql = "select upper(login) login, password, prenom, nom, statut, now() start, default_area, default_room, default_style, default_list_type, default_language, source
                from grr_utilisateurs
                where login = '" . protect_data_sql($_login) . "' and
                source = 'ext' and
                etat != 'inactif'";
                $res_user = grr_sql_query($sql);
                $num_row = grr_sql_count($res_user);
                if ($num_row == 1) {
                    // on r�cup�re les donn�es de l'utilisateur dans $row
                    $row = grr_sql_row($res_user,0);
               } else {
                   return "2";
                   die();
               }
            }
        }
    } else {   // On traite le cas usuel (non SSO)
        $passwd_md5 = md5($_password);
        $sql = "select upper(login) login, password, prenom, nom, statut, now() start, default_area, default_room, default_style, default_list_type, default_language, source, etat
        from grr_utilisateurs
        where login = '" . protect_data_sql($_login) . "' and
        password = '".$passwd_md5."'";
        $res_user = grr_sql_query($sql);
        $num_row = grr_sql_count($res_user);
        if ($num_row != 1) {  // L'utilisateur n'est pas pr�sent dans la base locale
            // Cas o� Ldap a �t� configur� :
            // On tente une authentification ldap
            if ((getSettingValue("ldap_statut") != '') and (@function_exists("ldap_connect")) and (@file_exists("include/config_ldap.inc.php"))) {
                $login_search = ereg_replace("[^-@._[:space:][:alnum:]]", "", $_login);
                if ($login_search != $_login) {
                    return "6"; //L'identifiant comporte des carat�res non autoris�s
                    exit();
                }
	          // Convertir depuis UTF-8 (jeu de caracteres par defaut)
				    if ((function_exists("utf8_decode")) and (getSettingValue("ConvertLdapUtf8toIso")=="y")) {
					    $_password=utf8_encode($_password);
				    }

                $user_dn = grr_verif_ldap($_login, $_password);
                if ($user_dn=="error_1") {
                    //  chemin invalide ou filtre add mauvais
                    return "7";
                    exit();
                } else if ($user_dn=="error_2") {
                    // aucune entr�e ne correspond au filtre
                    return "8";
                    exit();
                } else if ($user_dn=="error_3") {
                    // plus de deux r�sultats dans la recherche -> Echec de l'authentification ldap
                    return "9";
                    exit();
                } else if ($user_dn) {
                    $auth_ldap = 'yes'; // Voir suite plus bas
                } else {
                    // Echec de l'authentification ldap
                    return "4";
                    exit();
                }
            } else {
                return "2";
                exit();
            }
        } else {
            // on r�cup�re les donn�es de l'utilisateur dans $row
            $row = grr_sql_row($res_user,0);
            // S'il s'agit d'un utilisateur inactif, on s'arr�te l�
            if ($row[12] == 'inactif') {
                return "5";
                exit();
            }

        }

    }
    // Cette partie ne concerne que les utilisateurs pour lesquels l'authentification ldap ci-dessus a r�ussi
    // On tente d'interroger la base ldap pour obtenir des infos sur l'utilisateur
    if ($auth_ldap == 'yes') {
        // Cas particulier des serveur SE3
        // se3_liste_groupes_autorises est vide -> pas de restriction
        if (trim(getSettingValue("se3_liste_groupes_autorises")) == "") {
            $temoin_grp_ok="oui";
        } else {
            // se3_liste_groupes_autorises n'est pas vide -> on teste si le $_login appartient � un des groupes
            $temoin_grp_ok="non";
            $tab_grp_autorise=explode(";",getSettingValue("se3_liste_groupes_autorises"));
            for($i=0;$i<count($tab_grp_autorise);$i++) {
                if(se3_grp_members($tab_grp_autorise[$i],$_login)=="oui"){
                    $temoin_grp_ok="oui";
                }
            }
        }
        if ($temoin_grp_ok!="oui") {
            // Connexion � GRR non autoris�e.
            return "5";
            die();
        }
        // Fin cas particulier des serveur SE3


        // on regarde si un utilisateur ldap ayant le m�me login existe d�j�
        $sql = "select upper(login) login, password, prenom, nom, statut, now() start, default_area, default_room, default_style, default_list_type, default_language, source
        from grr_utilisateurs
        where login = '" . protect_data_sql($_login) . "' and
        source = 'ext' and
        etat != 'inactif'";
        $res_user = grr_sql_query($sql);
        $num_row = grr_sql_count($res_user);
        if ($num_row == 1) {
            // un utilisateur ldap ayant le m�me login existe d�j�
            // on r�cup�re les donn�es de l'utilisateur dans $row
            $row = grr_sql_row($res_user,0);
        } else {
             // pas d'utilisateur ldap ayant le m�me login dans la base GRR
             // Lire les infos sur l'utilisateur depuis LDAP
             include "config_ldap.inc.php";
             // Connexion � l'annuaire
             $ds = grr_connect_ldap($ldap_adresse,$ldap_port,$ldap_login,$ldap_pwd,$use_tls);
           	 // Test with login and password of the user
         		 if (!$ds) {
			           $ds = grr_connect_ldap($ldap_adresse,$ldap_port,$_login,$_password,$use_tls);
		         }

             if ($ds) {
                 $result = @ldap_read($ds, $user_dn, "objectClass=*", array(getSettingValue("ldap_champ_nom"),getSettingValue("ldap_champ_prenom"),getSettingValue("ldap_champ_email")));
             }
             if (!$result) {
                 return "2";
                 die();
             }
             // Recuperer les donnees de l'utilisateur
             $info = @ldap_get_entries($ds, $result);
             if (!is_array($info)) {
                 return "2";
                 die();
             }
             for ($i = 0; $i < $info["count"]; $i++) {
                 $val = $info[$i];
                 if (is_array($val)) {
               	     if (isset($val[getSettingValue("ldap_champ_nom")][0]))
					   $l_nom = ucfirst($val[getSettingValue("ldap_champ_nom")][0]);
					   else $l_nom=iconv("ISO-8859-1","utf-8","Nom � pr�ciser");
                     if (isset($val[getSettingValue("ldap_champ_prenom")][0]))
					   $l_prenom = ucfirst($val[getSettingValue("ldap_champ_prenom")][0]);
					   else $l_prenom=iconv("ISO-8859-1","utf-8","Pr�nom � pr�ciser");
                     if (isset($val[getSettingValue("ldap_champ_email")][0])) $l_email = $val[getSettingValue("ldap_champ_email")][0]; else $l_email='';
                 }
             }
            // Convertir depuis UTF-8 (jeu de caracteres par defaut)
            if ((function_exists("utf8_decode")) and (getSettingValue("ConvertLdapUtf8toIso")=="y")) {
                $l_email = utf8_decode($l_email);
                $l_nom = utf8_decode($l_nom);
                $l_prenom = utf8_decode($l_prenom);
            }
            // On teste si un utilisateur porte d�j� le m�me login
            $test = grr_sql_query1("select login from grr_utilisateurs where login = '".protect_data_sql($_login)."'");
            if ($test != '-1') {
                // authentification bonne mais le login existe d�j� : impossible d'importer le profil.
                return "3";
                die();
            } else {
                // On ins�re le nouvel utilisateur
                $sql = "INSERT INTO grr_utilisateurs SET
                nom='".protect_data_sql($l_nom)."',
                prenom='".protect_data_sql($l_prenom)."',
                login='".protect_data_sql($_login)."',
                password='',
                statut='".getSettingValue("ldap_statut")."',
                email='".protect_data_sql($l_email)."',
                etat='actif',
                source='ext'";
                if (grr_sql_command($sql) < 0)
                    {fatal_error(0, get_vocab("msg_login_created_error") . grr_sql_error());
                    return "2";
                    die();
                }

                $sql = "select upper(login) login, password, prenom, nom, statut, now() start, default_area, default_room, default_style, default_list_type, default_language, source
                from grr_utilisateurs
                where login = '" . protect_data_sql($_login) . "' and
                source = 'ext' and
                etat != 'inactif'";
                $res_user = grr_sql_query($sql);
                $num_row = grr_sql_count($res_user);
                if ($num_row == 1) {
                    // on r�cup�re les donn�es de l'utilisateur dans $row
                    $row = grr_sql_row($res_user,0);
               } else {
                   return "2";
                   die();
               }
            }
        }
    }

    // On teste si la connexion est active ou non
    if ((getSettingValue("disable_login")=='yes') and ($row[4] != "administrateur")) {
        return "2";
        die();
    }

    //
    // A ce stade, on dispose dans tous les cas d'un tableau $row contenant les informations n�cessaires � l'�tablissment d'une session
    //

    // Session starts now
    session_name(SESSION_NAME);
    @session_start();
    // Is this user already connected ?
    $sql = "select SESSION_ID from grr_log where SESSION_ID = '" . session_id() . "' and LOGIN = '" . protect_data_sql($_login) . "' and now() between START and END";
    $res = grr_sql_query($sql);
    $num_row = grr_sql_count($res);
    if (($num_row > 0) and isset($_SESSION['start'])) {
        $sql = "update grr_log set END = now() + interval " . getSettingValue("sessionMaxLength") . " minute where SESSION_ID = '" . session_id() . "' and START = '" . $_SESSION['start'] . "'";
    //  $sql = "update grr_log set END = now() + interval " . getSettingValue("sessionMaxLength") . " minute where SESSION_ID = '" . session_id() . "'";

        $res = grr_sql_query($sql);
        return "1";
    } else {
        session_unset();
//      session_destroy();
    }
    // reset $_SESSION
    $_SESSION = array();
    $_SESSION['login'] = $row[0];
    $_SESSION['password'] = $row[1];
    $_SESSION['prenom'] = $row[2];
    $_SESSION['nom'] = $row[3];
    $_SESSION['statut'] = $row[4];
    $_SESSION['start'] = $row[5];
    $_SESSION['maxLength'] = getSettingValue("sessionMaxLength");
    if (($row[6] !='') and ($row[6] !='0')) $_SESSION['default_area'] = $row[6]; else $_SESSION['default_area'] = getSettingValue("default_area");
    if (($row[7] !='') and ($row[7] !='0')) $_SESSION['default_room'] = $row[7]; else $_SESSION['default_room'] = getSettingValue("default_room");
    if ($row[8] !='') $_SESSION['default_style'] = $row[8]; else $_SESSION['default_style'] = getSettingValue("default_css");
    if ($row[9] !='') $_SESSION['default_list_type'] = $row[9]; else $_SESSION['default_list_type'] = getSettingValue("area_list_format");
    if ($row[10] !='') $_SESSION['default_language'] = $row[10]; else $_SESSION['default_language'] = getSettingValue("default_language");
    $_SESSION['source_login'] = $row[11];

    // It's a new connection, insert into log
     if (isset($_SERVER["HTTP_REFERER"])) $httpreferer = substr($_SERVER["HTTP_REFERER"],0,254); else $httpreferer = '';
    $sql = "insert into grr_log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
                '" . $_SESSION['login'] . "',
                '" . $_SESSION['start'] . "',
                '" . session_id() . "',
                '" . $_SERVER['REMOTE_ADDR'] . "',
                '" . substr($_SERVER['HTTP_USER_AGENT'],0,254) . "',
                '" . $httpreferer . "',
                '1',
                '" . $_SESSION['start'] . "' + interval " . getSettingValue("sessionMaxLength") . " minute
            )
        ;";
    $res = grr_sql_query($sql);

/* Fonctionnalit� SE3 (Palissy - Saintes - philippe.duval@ac-poitiers.fr) :
Utilisation du LDAP pour inscrire automatiquement les utilisateurs dans les groupes administration, acc�s et gestion
Ce code est associ� � une nouvelle table :
CREATE TABLE grr_j_groupe_se3 (groupe varchar(40) NOT NULL default '',id_area_room int(11) NOT NULL default '0', statut varchar(20) NOT NULL default '',  PRIMARY KEY  (`groupe`,`id_area_room`));
Par ailleurs, pour que cette fonctionnalit� soit compl�te et dans l'esprit de GRR, il faudra d�velopper une "petite" interface dans GRR pour g�rer les entr�es dans cette table.
*/
	// D�but de la fonctionnalit� SE3
	$grp=@grr_sql_query("SELECT groupe, id_area_room, statut FROM grr_j_groupe_se3");
	if ($grp) { // si la table grr_j_groupe_se3 est implant�e et non vide
		while ($resgrp=@mysql_fetch_array($grp)) {	// balaye tous les groupes pr�sents dans la table grr_j_groupadmin_area
	    $statut_se3 = $resgrp['statut'];		$area_se3 = $resgrp['id_area_room'];
		if ($statut_se3 == 'administrateur')	{ $table_user_se3 = 'grr_j_useradmin_area'; $type_res = 'id_area'; }
		if ($statut_se3 == 'acces_restreint')	{ $table_user_se3 = 'grr_j_user_area'; $type_res = 'id_area'; }
		if ($statut_se3 == 'gestionnaire')		{ $table_user_se3 = 'grr_j_user_room'; $type_res = 'id_room'; }
      if(se3_grp_members($resgrp['groupe'],$_login)=="oui")
		  $add_user_se3 = @grr_sql_query("INSERT INTO `".$table_user_se3."` (login, ".$type_res.") values('".$_login."',".$area_se3.")");
		else	// Cette fonctionnalit� enl�ve les droits, donc elle enl�ve TOUS LES DROITS m�me mis � la main ! On peut donc l'enlever si le mode de fonctionnement est mixte (manuel et ldap)
		  $del_user_se3 = @grr_sql_query("DELETE FROM `".$table_user_se3."` WHERE `login`='".$_login."' AND `".$type_res."`=".$area_se3);
	  }
	}
	// Note : Il reste � g�rer finement l'interface graphique et � d�duire l'incompatibilit� �ventuelle entre le domaine par d�faut et les domaines autoris�s pour chaque utilisateur
	// Fin de la fonctionnalit� SE3

/* Application du patch en production depuis la rentr�e � Palissy : Z�ro probl�me (ci-dessous, l'extraction de la table via phpmyadmin)
CREATE TABLE `grr_j_groupe_se3` (
  `groupe` varchar(40) NOT NULL default '',
  `id_area_room` int(11) NOT NULL default '0',
  `statut` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`groupe`,`id_area_room`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `grr_j_groupe_se3` (`groupe`, `id_area_room`, `statut`) VALUES
('GRR_ADMIN_SALLES_REUNIONS', 1, 'administrateur'),
('GRR_ADMIN_SALLES_PEDAGOGIQUES', 2, 'administrateur'),
('GRR_ADMIN_LABOS_LANGUES', 3, 'administrateur'),
('GRR_SALLES_REUNIONS', 1, 'acces_restreint'),
('GRR_SALLES_PEDAGOGIQUES', 2, 'acces_restreint'),
('GRR_LABOS_LANGUES', 3, 'acces_restreint'),
('GRR_GESTION_SALLE_A01', 1, 'gestionnaire'),
('GRR_GESTION_SALLE_A03', 2, 'gestionnaire'),
('GRR_GESTION_SALLE_A314', 3, 'gestionnaire'),
('GRR_GESTION_SALLE_A409', 4, 'gestionnaire'),
('GRR_GESTION_SALLE_D05', 5, 'gestionnaire'),
('GRR_GESTION_SALLE_A301E', 6, 'gestionnaire');
*/

    return "1";
}

/**
 * Resume a session
 *
 * Check that all the expected data is present
 * Check login / password against database
 * Update the timeout in the grr_log table
 *
 * Returns true if session resumes, false otherwise
 *
 *
 * @return              bool                    The session resumed
 */
function grr_resumeSession()
{
    global $is_authentified_lcs;
    // Resuming session
    session_name(SESSION_NAME);
    @session_start();
    if ((getSettingValue('sso_statut') == 'lcs') and ($is_authentified_lcs == 'no') and ($_SESSION['source_login'] == "ext")) {
        return (false);
        die();
    }
    // La session est-elle expir�e
    if (isset($_SESSION['login'])) {
        $test_session = grr_sql_query1("select count(LOGIN) from grr_log where END > now() and LOGIN = '".$_SESSION['login']."'");
        if ($test_session==0)
            // D�truit toutes les variables de session
            $_SESSION = array();
    }

    if ((!isset($_SESSION)) or (!isset($_SESSION['login']))){
        return (false);
        die();
    }
    if ((getSettingValue("disable_login")=='yes') and ($_SESSION['statut'] != "administrateur")) {
        return (false);
        die();
    }
    // To be removed
    // Validating session data
    $sql = "select password = '" . $_SESSION['password'] . "' PASSWORD, login = '" . $_SESSION['login'] . "' LOGIN, statut = '" . $_SESSION['statut'] . "' STATUT
        from grr_utilisateurs where login = '" . $_SESSION['login'] . "'";

    $res = grr_sql_query($sql);
    $row = grr_sql_row($res, 0);
    // Checking for a timeout
    $sql2 = "select now() > END TIMEOUT from grr_log where SESSION_ID = '" . session_id() . "' and START = '" . $_SESSION['start'] . "'";
    if ($row[0] != "1" || $row[1] != "1" || $row[2] != "1") {
        return (false);
    } else if (grr_sql_query1($sql2)) { // Le temps d'inactivit� est sup�rieur � la limite fix�e.
        // cas d'une authentification LCS
        if (getSettingValue('sso_statut') == 'lcs') {
            if ($is_authentified_lcs == 'yes') // l'utilisateur est authentifi� par LCS, on renouvelle la session
                {
                $sql = "update grr_log set END = now() + interval " . $_SESSION['maxLength'] . " minute where SESSION_ID = '" . session_id() . "' and START = '" . $_SESSION['start'] . "'";
                $res = grr_sql_query($sql);
                return (true);
            } else // L'utilisateur n'est plus authentifi�
               return (false);
         } else  // cas g�n�ral
               return (false);
    } else {
        $sql = "update grr_log set END = now() + interval " . $_SESSION['maxLength'] . " minute where SESSION_ID = '" . session_id() . "' and START = '" . $_SESSION['start'] . "'";
        $res = grr_sql_query($sql);
        return (true);
    }
}

/**
 * Close a session
 *
 * Set the closing time in the logs
 * Destroy all session data
 * @_auto               string                  Session auto-close flag
 * @return              nothing
 */
function grr_closeSession(&$_auto)
{
    settype($_auto,"integer");
    session_name(SESSION_NAME);
    @session_start();
    // Sometimes 'start' may not exist, because the session was previously closed by another window
    // It's not necessary to grr_log this, then
    if (isset($_SESSION['start'])) {
            $sql = "update grr_log set AUTOCLOSE = '" . $_auto . "', END = now() where SESSION_ID = '" . session_id() . "' and START = '" . $_SESSION['start'] . "'";
        $res = grr_sql_query($sql);
    }
    // D�truit toutes les variables de session
    $_SESSION = array();

    // D�truit le cookie sur le navigateur
    $CookieInfo = session_get_cookie_params();
    @setcookie(session_name(), '', time()-3600, $CookieInfo['path']);
    // On d�truit la session
    session_destroy();
}



function grr_verif_ldap($_login, $_password) {
    global $ldap_filter;
    if ($_password == '') {
        return false;
        exit();
    }
    include "config_ldap.inc.php";

    $ds = grr_connect_ldap($ldap_adresse,$ldap_port,$ldap_login,$ldap_pwd,$use_tls);
		// Test with login and password of the user
		if (!$ds) {
			$ds = grr_connect_ldap($ldap_adresse,$ldap_port,$_login,$_password,$use_tls);
		}

    if ($ds) {
        // Attributs test�s pour egalite avec le login
        $atts = explode("|",getSettingValue("ldap_champ_recherche"));
        //$atts = array('uid', 'login', 'userid', 'cn', 'sn', 'samaccountname', 'userprincipalname');
        $login_search = ereg_replace("[^-@._[:space:][:alnum:]]", "", $_login); // securite
        // Tenter une recherche pour essayer de retrouver le DN
        reset($atts);
        while (list(, $att) = each($atts)) {
            $dn = grr_ldap_search_user($ds, $ldap_base, $att, $login_search, $ldap_filter);
            if (($dn=="error_1") or ($dn=="error_2") or ($dn=="error_3")) {
              return $dn; // On renvoie le code d'erreur
            } else if ($dn) {
                // on a le dn
                if (@ldap_bind($ds, $dn, $_password)) {
                    @ldap_unbind($ds);
                     return $dn;
                }
            }
        }
        // Si echec, essayer de deviner le DN, dans le cas o� il n'y a pas de filtre suppl�mentaires
        reset($atts);
        if (!isset($ldap_filter) or ($ldap_filter="")) {
          while (list(, $att) = each($atts)) {
            $dn = $att."=".$login_search.",".$ldap_base;
            if (@ldap_bind($ds, $dn, $_password)) {
                @ldap_unbind($ds);
                return $dn;
            }
          }
        }
        return false;
    } else {
        return false;
    }
}

function grr_connect_ldap($l_adresse,$l_port,$l_login,$l_pwd, $use_tls, $msg_error = "no") {
    $ds = @ldap_connect($l_adresse, $l_port);
    if($ds) {
       // On dit qu'on utilise LDAP V3, sinon la V2 par d�faut est utilis� et le bind ne passe pas.
       if (!(ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3))) {
         if ($msg_error != "no") return "error_1";
         die();
       }
       // Option LDAP_OPT_REFERRALS � d�sactiver dans le cas d'active directory
       @ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
   		 if ($use_tls) {
   		 		if (!@ldap_start_tls($ds)) {
   		 		    if ($msg_error != "no") return "error_2";
		   		    return false;
			    }
		   }
       // Acc�s non anonyme
       if ($l_login != '') {
          // On tente un bind
          $b = ldap_bind($ds, $l_login, $l_pwd);
       } else {
          // Acc�s anonyme
          $b = ldap_bind($ds);
       }
       if ($b) {
           return $ds;
       } else {
           if ($msg_error != "no") return "error_3";
           return false;
       }
    } else {
       if ($msg_error != "no") return "error_4";
       return false;
    }
}
/*
Effectue une recherche dans l'annuaire
$ds : identifiant de serveur ldap
$basedn : chemin de recherche
$login_attr : attribut de recherche
$login : valeur prise par l'attribut
$filtre_sup : filtre suppl�mentaire de recherche
$diagnostic : "no" ou "yes"
$diagnostic="yes" :
-> mode utilis� dans les tests de connexion � l'annuaire (admin_config_ldap.php) et quand le mode diagnostic est activ�.
-> On renvoie diff�rents codes d'erreur pour aider � l'analyse des r�sultats
$diagnostic="no" :
-> mode "normal" utilis� lors des connexions � l'annuaire pour se connecter � GRR.
*/

function grr_ldap_search_user($ds, $basedn, $login_attr, $login, $filtre_sup="", $diagnostic="no") {
    if (getSettingValue("ActiveModeDiagnostic")=="y")
        $diagnostic="yes";
/*
  // une alternative au filtre suivant :
  $filter = "(|(".$login_attr."=".$login.")(".$login_attr."=".$login."@*))";
	if (!empty ($filtre_sup)){
	    $filter = "(&".$filter.$filtre_sup.")";
*/
  // Construction du filtre
  $filter = "(".$login_attr."=".$login.")";
	if (!empty ($filtre_sup)){
		$filter = "(& ".$filter.$filtre_sup.")";
	}
	$res = @ldap_search($ds, $basedn, $filter, array ("dn", $login_attr),0,0);
  if ($res) {
      $info = @ldap_get_entries($ds, $res);
      if  ((!is_array($info)) or ($info['count'] == 0)) {
	        // Mode diagnostic
  	      if ($diagnostic!="no")
             return "error_2"; // aucune entr�e ne correspond au filtre
			    else
             // Mode normal
	  		     return false;
          die();
      } else if ($info['count'] > 1) {
		      // Si plusieurs entr�es, on accepte uniquement en mode diagnostic
		      if ($diagnostic!="no")
            return "error_3";
          else
    			  // Mode normal
            return false;
          die();
		    } else {
 			    return $info[0]['dn']; // Succ�s total -> on retourne le dn
 			    die();
        }
	  } else {
	      // Mode diagnostic
        if ($diagnostic!="no")
            return "error_1"; // chemin invalide ou filtre add mauvais
			  else
           // Mode normal
           return false;
	  }
}
?>