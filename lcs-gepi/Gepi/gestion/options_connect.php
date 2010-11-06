<?php
/*
 * $Id: options_connect.php 5743 2010-10-24 16:23:13Z regis $
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Begin standart header

$titre_page = "Options de connexion";



// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session

$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


// Enregistrement de la dur�e de conservation des donn�es

if (isset($_POST['duree'])) {
    if (!saveSetting(("duree_conservation_logs"), $_POST['duree'])) {
        $msg = "Erreur lors de l'enregistrement de la dur�e de conservation des connexions !";
    } else {
        $msg = "La dur�e de conservation des connexions a �t� enregistr�e.<br />Le changement sera pris en compte apr�s la prochaine connexion � GEPI.";
    }
}


if (isset($_POST['auth_options_posted']) && $_POST['auth_options_posted'] == "1") {

	if (isset($_POST['auth_sso'])) {
	    if (!in_array($_POST['auth_sso'], array("none","lemon","cas","lcs"))) {
	    	$_POST['auth_sso'] = "none";
	    }
		saveSetting("auth_sso", $_POST['auth_sso']);
	}


	if (isset($_POST['auth_locale'])) {
	    if ($_POST['auth_locale'] != "yes") {
	    	$_POST['auth_locale'] = "no";
	    }
	} else {
		$_POST['auth_locale'] = "no";
	}
	saveSetting("auth_locale", $_POST['auth_locale']);

	if (isset($_POST['auth_ldap'])) {
	    if ($_POST['auth_ldap'] != "yes") {
	    	$_POST['auth_ldap'] = "no";
	    }
	} else {
		$_POST['auth_ldap'] = "no";
	}
	saveSetting("auth_ldap", $_POST['auth_ldap']);

	if (isset($_POST['ldap_write_access'])) {
	    if ($_POST['ldap_write_access'] != "yes") {
	    	$_POST['ldap_write_access'] = "no";
	    }
	} else {
		$_POST['ldap_write_access'] = "no";
	}
	saveSetting("ldap_write_access", $_POST['ldap_write_access']);

    	if (isset($_POST['sso_display_portail'])) {
	    if ($_POST['sso_display_portail'] != "yes") {
	    	$_POST['sso_display_portail'] = "no";
	    }
	} else {
		$_POST['sso_display_portail'] = "no";
	}
	saveSetting("sso_display_portail", $_POST['sso_display_portail']);
	
        if (isset($_POST['sso_hide_logout'])) {
	    if ($_POST['sso_hide_logout'] != "yes") {
	    	$_POST['sso_hide_logout'] = "no";
	    }
	} else {
		$_POST['sso_hide_logout'] = "no";
	}
	saveSetting("sso_hide_logout", $_POST['sso_hide_logout']);
    
    
    	if (isset($_POST['sso_url_portail'])) {
	    saveSetting("sso_url_portail", $_POST['sso_url_portail']);
	}
    
    
	if (isset($_POST['may_import_user_profile'])) {
	    if ($_POST['may_import_user_profile'] != "yes") {
	    	$_POST['may_import_user_profile'] = "no";
	    }
	} else {
		$_POST['may_import_user_profile'] = "no";
	}
	saveSetting("may_import_user_profile", $_POST['may_import_user_profile']);

	if (isset($_POST['sso_scribe'])) {
	    if ($_POST['sso_scribe'] != "yes") {
	    	$_POST['sso_scribe'] = "no";
	    }
	} else {
		$_POST['sso_scribe'] = "no";
	}
	saveSetting("sso_scribe", $_POST['sso_scribe']);


	if (isset($_POST['statut_utilisateur_defaut'])) {
	    if (!in_array($_POST['statut_utilisateur_defaut'], array("professeur","responsable","eleve"))) {
	    	$_POST['statut_utilisateur_defaut'] = "professeur";
	    }
		saveSetting("statut_utilisateur_defaut", $_POST['statut_utilisateur_defaut']);
	}
	
	if (isset($_POST['login_sso_url'])) {
		saveSetting("login_sso_url", $_POST['login_sso_url']);
	}

}



// Load settings

if (!loadSettings()) {
    die("Erreur chargement settings");
}



// Suppression du journal de connexion

if (isset($_POST['valid_sup_logs']) ) {
    $sql = "delete from log where END < now()";
    $res = sql_query($sql);
    if ($res) {
       $msg = "La suppression des entr�es dans le journal de connexion a �t� effectu�e.";
    } else {
       $msg = "Il y a eu un probl�me lors de la suppression des entr�es dans le journal de connexion.";
    }
}

// Changement de mot de passe obligatoire
if (isset($_POST['valid_chgt_mdp'])) {
	if ((!$session_gepi->auth_ldap && !$session_gepi->auth_sso) || getSettingValue("ldap_write_access")) {
    	$sql = "UPDATE utilisateurs SET change_mdp='y' where login != '".$_SESSION['login']."'";
	} else {
		$sql = "UPDATE utilisateurs SET change_mdp='y' WHERE (login != '".$_SESSION['login']."' AND auth_mode != 'ldap' AND auth_mode != 'sso')";
	}

    $res = sql_query($sql);
    if ($res) {
       $msg = "La demande de changement obligatoire de mot de passe a �t� enregistr�e.";
    } else {
       $msg = "Il y a eu un probl�me lors de l'enregistrement de la demande de changement obligatoire de mot de passe.";
    }
}


//Activation / d�sactivation de la proc�dure de r�initialisation du mot de passe par email
if (isset($_POST['enable_password_recovery'])) {
    if (!saveSetting("enable_password_recovery", $_POST['enable_password_recovery'])) {
        $msg = "Il y a eu un probl�me lors de l'enregistrement du param�tre d'activation/d�sactivation de la proc�dure de r�cup�ration automatis�e des mots de passe.";
    } else {
        $msg = "L'enregistrement du param�tre d'activation/d�sactivation de la proc�dure de r�cup�ration automatis�e des mots de passe a �t� effectu� avec succ�s.";
    }
}

// End standart header
require_once("../lib/header.inc");
isset($mode_navig);
$mode_navig = isset($_POST["mode_navig"]) ? $_POST["mode_navig"] : (isset($_GET["mode_navig"]) ? $_GET["mode_navig"] : NULL);
if ($mode_navig == 'accueil') {
    $retour = "../accueil.php";
} else {
    $retour = "index.php#options_connect";
}

echo "<p class=bold><a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";


//
// Activation/d�sactivation de la proc�dure de r�cup�ration du mot de passe
//
echo "<h3 class='gepi'>Mots de passe perdus</h3>\n";
echo "<form action=\"options_connect.php\" method=\"post\">\n";
echo "<input type='radio' name='enable_password_recovery' value='no' id='label_1b'";
if (getSettingValue("enable_password_recovery")=='no') echo " checked ";
echo " /> <label for='label_1b' style='cursor: pointer;'>D�sactiver la proc�dure automatis�e de r�cup�ration de mot de passe</label>\n";

echo "<br /><input type='radio' name='enable_password_recovery' value='yes' id='label_2b'";
if (getSettingValue("enable_password_recovery")=='yes') echo " checked ";
echo " /> <label for='label_2b' style='cursor: pointer;'>Activer la proc�dure automatis�e de r�cup�ration de mot de passe</label>\n";

echo "<center><input type=\"submit\" value=\"Valider\" /></center>\n";
echo "</form>\n";

echo"<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>\n";

//
// Changement du mot de passe obligatoire
//
// Cette option n'est propos�e que si les mots de passe sont �ditables dans Gepi
//
if ($session_gepi->auth_locale ||
		(($session_gepi->auth_ldap || $session_gepi->auth_sso)
				&& getSettingValue("ldap_write_access") == "yes")) {
echo "<h3 class='gepi'>Changement du mot de passe obligatoire lors de la prochaine connexion</h3>\n";
echo "<p><b>ATTENTION : </b>En validant le bouton ci-dessous, <b>tous les utilisateurs</b> dont le mot de passe est �ditable par Gepi (les utilisateurs locaux, ou bien tous les utilisateurs si un acc�s LDAP en �criture a �t� configur�) seront amen�s � changer leur mot de passe lors de leur prochaine connexion.</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_chgt_mdp\" method=\"post\">\n";
echo "<center><input type=\"submit\" name=\"valid_chgt_mdp\" value=\"Valider\" onclick=\"return confirmlink(this, '�tes-vous s�r de vouloir forcer le changement de mot de passe de tous les utilisateurs ?', 'Confirmation')\" /></center>\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";
echo "</form><hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>\n";
}

//
// Param�trage du Single Sign-On
//

echo "<h3 class='gepi'>Mode d'authentification</h3>\n";
echo "<p><span style='color: red'><strong>Attention !</strong></span> Ne modifiez ces param�tres que si vous savez vraiment ce que vous faites ! Si vous activez l'authentification SSO et que vous ne pouvez plus vous connecter � Gepi en administrateur, vous pouvez utiliser la variable \$block_sso dans le fichier /lib/global.inc pour d�sactiver le SSO et rebasculer en authentification locale. Il est donc vivement recommand� de cr�er un compte administrateur local (dont le login n'interf�rera pas avec un login SSO) avant d'activer le SSO.</p>\n";
echo "<p>Gepi permet d'utiliser plusieurs modes d'authentification en parall�le. Les combinaisons les plus courantes seront une authentification locale avec une authentifcation LDAP, ou bien une authentification locale et une authentification unique (utilisant un serveur d'authentification distinct).</p>\n";
echo "<p>Le mode d'authentification est explicitement sp�cifi� pour chaque utilisateur dans la base de donn�es de Gepi. Assurez-vous que le mode d�fini correspond effectivement au mode utilis� par l'utilisateur.</p>\n";
echo "<p>Dans le cas d'une authentification externe (LDAP ou SSO), aucun mot de passe n'est stock� dans la base de donn�es de Gepi.</p>\n";
echo "<p>Si vous param�trez un acc�s LDAP en �criture, les mots de passe des utilisateurs pourront �tre modifi�s directement � travers Gepi, m�me pour les modes LDAP et SSO. L'administrateur pourra �galement �diter les donn�es de base de l'utilisateur (nom, pr�nom, email). Lorsque vous activez l'acc�s LDAP en �criture, assurez-vous que le param�trage sur le serveur LDAP permet � l'utilisateur de connexion LDAP de modifier les champs login, mot de passe, nom, pr�nom et email.</p>\n";
echo "<p>Si vous utilisez CAS, vous devez entrer les informations de configuration du serveur CAS dans le fichier /secure/config_cas.inc.php (un mod�le de configuration se trouve dans le fichier /secure/config_cas.cfg).</p>\n";
echo "<p>Si vous utilisez l'authentification sur serveur LDAP, ou bien que vous activez l'acc�s LDAP en �criture, vous devez renseigner le fichier /secure/config_ldap.inc.php avec les informations n�cessaires pour se connecter au serveur (un mod�le se trouve dans /secure/config_ldap.cfg).</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_auth\" method=\"post\">\n";

echo "<p><strong>Modes d'authentification :</strong></p>\n";
echo "<p><input type='checkbox' name='auth_locale' value='yes' id='label_auth_locale'";
if (getSettingValue("auth_locale")=='yes') echo " checked ";
echo " /> <label for='label_auth_locale' style='cursor: pointer;'>Authentification autonome (sur la base de donn�es de Gepi)</label>\n";

$ldap_setup_valid = LDAPServer::is_setup();
echo "<br/><input type='checkbox' name='auth_ldap' value='yes' id='label_auth_ldap'";
if (getSettingValue("auth_ldap")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_auth_ldap' style='cursor: pointer;'>Authentification LDAP";
if (!$ldap_setup_valid) echo " <em>(s�lection impossible : le fichier /secure/config_ldap.inc.php n'est pas pr�sent)</em>\n";
echo "</label>\n";
echo "</p>\n";

echo "<p>Service d'authentification unique : ";

echo "<br/><input type='radio' name='auth_sso' value='none' id='no_sso'";
if (getSettingValue("auth_sso")=='none') echo " checked ";
echo " /> <label for='no_sso' style='cursor: pointer;'>Non utilis�</label>\n";

$lcs_setup_valid = file_exists("../secure/config_lcs.inc.php") ? true : false;
echo "<br/><input type='radio' name='auth_sso' value='lcs' id='lcs'";
if (getSettingValue("auth_sso")=='lcs' && $lcs_setup_valid) echo " checked ";
if (!$lcs_setup_valid) echo " disabled";
echo " /> <label for='lcs' style='cursor: pointer;'>LCS";
if (!$lcs_setup_valid) echo " <em>(s�lection impossible : le fichier /secure/config_lcs.inc.php n'est pas pr�sent)</em>\n";
echo "</label>\n";

$cas_setup_valid = file_exists("../secure/config_cas.inc.php") ? true : false;
echo "<br /><input type='radio' name='auth_sso' value='cas' id='label_2'";
if (getSettingValue("auth_sso")=='cas' && $cas_setup_valid) echo " checked ";
if (!$cas_setup_valid) echo " disabled";
echo " /> <label for='label_2' style='cursor: pointer;'>CAS";
if (!$cas_setup_valid) echo " <em>(s�lection impossible : le fichier /secure/config_cas.inc.php n'est pas pr�sent)</em>\n";
echo "</label>\n";


echo "<br /><input type='radio' name='auth_sso' value='lemon' id='label_3'";
if (getSettingValue("auth_sso")=='lemon') echo " checked ";
echo " /> <label for='label_3' style='cursor: pointer;'>LemonLDAP</label>\n";
echo "</p>\n";
echo "<p>Remarque : les changements n'affectent pas les sessions en cours.";

echo "<p><strong>Options suppl�mentaires :</strong></p>\n";

echo "<p><input type='checkbox' name='may_import_user_profile' value='yes' id='label_import_user_profile'";
if (getSettingValue("may_import_user_profile")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_import_user_profile' style='cursor: pointer;'>Import � la vol�e des comptes utilisateurs authentifi�s correctement (en LDAP ou SSO).";
if (!$ldap_setup_valid) echo " <em>(s�lection impossible : le fichier /secure/config_ldap.inc.php n'est pas pr�sent)</em>\n";
echo "</label>\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='sso_scribe' value='yes' id='label_sso_scribe'";
if (getSettingValue("sso_scribe")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_sso_scribe' style='cursor: pointer;'>Utilisation avec l'annuaire LDAP de Scribe NG, versions 2.2 et sup�rieures (permet l'import � la vol�e de donn�es plus compl�tes lorsque cet ENT est utilis� et que l'option 'Import � la vol�e', ci-dessus, est coch�e).";
if (!$ldap_setup_valid) echo " <em>(s�lection impossible : le fichier /secure/config_ldap.inc.php n'est pas pr�sent)</em>\n";
echo "</label>\n";
echo "</p>\n";

echo "<p>Statut par d�faut appliqu� en cas d'impossibilit� de d�terminer le statut lors de l'import :";
echo "<br/>\n<select name=\"statut_utilisateur_defaut\" size=\"1\">\n";
echo "<option ";
if(isset($gepiSettings['statut_utilisateur_defaut'])) {$statut_defaut = $gepiSettings['statut_utilisateur_defaut'];}else {$statut_defaut="professeur";}
if ($statut_defaut == "professeur") echo "selected";
echo " value='professeur'>Professeur</option>\n";
echo "<option ";
if ($statut_defaut == "eleve") echo "selected";
echo " value='eleve'>�l�ve</option>\n";
echo "<option ";
if ($statut_defaut == "responsable") echo "selected";
echo " value='responsable'>Responsable l�gal</option>\n";
echo "</select>\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='ldap_write_access' value='yes' id='label_ldap_write_access'";
if (getSettingValue("ldap_write_access")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_ldap_write_access' style='cursor: pointer;'>Acc�s LDAP en �criture.";
if (!$ldap_setup_valid) echo " <em>(s�lection impossible : le fichier /secure/config_ldap.inc.php n'est pas pr�sent)</em>\n";
echo "</label>\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='sso_display_portail' value='yes' id='label_sso_display_portail'";
if ($gepiSettings['sso_display_portail'] == 'yes') echo " checked ";
echo " /> <label for='label_sso_display_portail' style='cursor: pointer;'>Sessions SSO uniquement : afficher un lien vers un portail (vous devez renseigner le champ ci-dessous).";
echo "</label>\n";
echo "</p>\n";

echo "<p>\n";
echo "<label for='label_sso_url_portail' style='cursor: pointer;'>Adresse compl�te du portail : </label>\n";
echo "<input type='text' size='60' name='sso_url_portail' value='".$gepiSettings['sso_url_portail']."' id='label_sso_url_portail' />\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='sso_hide_logout' value='yes' id='label_sso_hide_logout'";
if ($gepiSettings['sso_hide_logout'] == 'yes') echo " checked='checked' ";
echo " /> <label for='label_sso_hide_logout' style='cursor: pointer;'>Sessions SSO uniquement : masquer le lien de d�connexion (soyez s�r que l'utilisateur dispose alors d'un moyen alternatif de se d�connecter).";
echo "</label>\n";
echo "</p>\n";

echo "<br/>\n";
echo "<p>\n";
echo "<label for='login_sso_url' style='cursor: pointer;'>Fichier d'identification SSO alternatif (� utiliser � la place de login_sso.php) : </label>\n";
echo "<input type='text' size='60' name='login_sso_url' value='".getSettingValue('login_sso_url')."' id='login_sso_url' />\n";

echo "</p>\n";

echo "<br/>\n";
echo "<center><input type=\"submit\" name=\"auth_mode_submit\" value=\"Valider\" onclick=\"return confirmlink(this, '�tes-vous s�r de vouloir changer le mode d\' authentification ?', 'Confirmation')\" /></center>\n";

echo "<input type='hidden' name='auth_options_posted' value='1' />\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";

echo "</form>



<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\" />\n";



//
// Dur�e de conservation des logs
//
echo "<h3 class='gepi'>Dur�e de conservation des connexions</h3>\n";
echo "<p>Conform�ment � la loi loi informatique et libert� 78-17 du 6 janvier 1978, la dur�e de conservation de ces donn�es doit �tre d�termin�e et proportionn�e aux finalit�s de leur traitement.
Cependant par s�curit�, il est conseill� de conserver une trace des connexions sur un laps de temps suffisamment long.
</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_chgt_duree\" method=\"post\">\n";
echo "Dur�e de conservation des informations sur les connexions : <select name=\"duree\" size=\"1\">\n";
echo "<option ";
$duree = getSettingValue("duree_conservation_logs");
if ($duree == 30) echo "selected";
echo " value=30>Un mois</option>\n";
echo "<option ";
if ($duree == 60) echo "selected";
echo " value=60>Deux mois</option>\n";
echo "<option ";
if ($duree == 183) echo "selected";
echo " value=183>Six mois</option>\n";
echo "<option ";
if ($duree == 365) echo "selected";
echo " value=365>Un an</option>\n";
echo "</select>\n";
echo "<input type=\"submit\" name=\"Valider\" value=\"Enregistrer\" />\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";
echo "</form>\n";
//
// Nettoyage du journal
//
?>
<hr class="header" style="margin-top: 32px; margin-bottom: 24px;"/>
<h3 class='gepi'>Suppression de toutes les entr�es du journal de connexion</h3>
<?php
$sql = "select START from log order by END";
$res = sql_query($sql);
$logs_number = sql_count($res);
$row = sql_row($res, 0);
$annee = substr($row[0],0,4);
$mois =  substr($row[0],5,2);
$jour =  substr($row[0],8,2);
echo "<p>Nombre d'entr�es actuellement pr�sentes dans le journal de connexion : <b>".$logs_number."</b><br />\n";
echo "Actuellement, le journal contient l'historique des connexions depuis le <b>".$jour."/".$mois."/".$annee."</b></p>\n";
echo "<p><b>ATTENTION : </b>En validant le bouton ci-dessous, <b>toutes les entr�es du journal de connexion (hormis les connexions en cours) seront supprim�es</b>.</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_sup_logs\" method=\"post\">\n";
echo "<center><input type=\"submit\" name=\"valid_sup_logs\" value=\"Valider\" onclick=\"return confirmlink(this, '�tes-vous s�r de vouloir supprimer tout l\'historique du journal de connexion ?', 'Confirmation')\" /></center>\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";
echo "</form><br/>\n";

require("../lib/footer.inc.php");
?>
