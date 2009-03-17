<?php
#########################################################################
#                            admin_maj.php                              #
#                                                                       #
#            interface permettant la mise � jour de la base de donn�es  #
#               Derni�re modification : 20/03/2008                      #
#                                                                       #
#                                                                       #
#########################################################################
/*
 * Copyright 2003-2005 Laurent Delineau
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

include "include/connect.inc.php";
include "include/config.inc.php";
include "include/misc.inc.php";
include "include/functions.inc.php";
include "include/$dbsys.inc.php";
$grr_script_name = "admin_maj.php";

// Settings
require_once("./include/settings.inc.php");
//Chargement des valeurs de la table settingS
if (!loadSettings())
    die("Erreur chargement settings");

// Session related functions
require_once("./include/session.inc.php");

// Param�tres langage
include "include/language.inc.php";

function traite_requete($requete="") {
    $retour="";
    $res = mysql_query($requete);
    $erreur_no = mysql_errno();
    if (!$erreur_no) {
        $retour = "";
    } else {
        switch ($erreur_no) {
        case "1060":
        // le champ existe d�j� : pas de probl�me
        $retour = "";
        break;
        case "1061":
        // La cl�f existe d�j� : pas de probl�me
        $retour = "";
        break;
        case "1062":
        // Pr�sence d'un doublon : cr�ation de la cl�f impossible
        $retour = "<font color=\"#FF0000\">Erreur (<b>non critique</b>) sur la requ�te : <i>".$requete."</i> (".mysql_errno()." : ".mysql_error().")</font><br />\n";
        break;
        case "1068":
        // Des cl�fs existent d�j� : pas de probl�me
        $retour = "";
        break;
        case "1091":
        // D�j� supprim� : pas de probl�me
        $retour = "";
        break;
        default:
        $retour = "<font color=\"#FF0000\">Erreur sur la requ�te : <i>".$requete."</i> (".mysql_errno()." : ".mysql_error().")</font><br />\n";
        break;
        }
    }
    return $retour;
}


$valid = isset($_POST["valid"]) ? $_POST["valid"] : 'no';
$version_old = isset($_POST["version_old"]) ? $_POST["version_old"] : '';
if (isset($_GET["force_maj"])) $version_old = $_GET["force_maj"];

if (isset($_POST['submit'])) {
    if (isset($_POST['login']) && isset($_POST['password'])) {
        // Test pour tenir compte du changement de nom de la table grr_utilisateurs lors du passage � la version 1.8
        $num_version = grr_sql_query1("select NAME from grr_setting WHERE NAME='version'");
        if ($num_version != -1)
            $sql = "select upper(login) login, password, prenom, nom, statut from grr_utilisateurs where login = '" . $_POST['login'] . "' and password = md5('" . $_POST['password'] . "') and etat != 'inactif' and statut='administrateur' ";
        else
            $sql = "select upper(login) login, password, prenom, nom, statut from utilisateurs where login = '" . $_POST['login'] . "' and password = md5('" . $_POST['password'] . "') and etat != 'inactif' and statut='administrateur' ";
        $res_user = grr_sql_query($sql);
        $num_row = grr_sql_count($res_user);
        if ($num_row == 1) {
            $valid='yes';
        } else {
            $message = get_vocab("wrong_pwd");
        }
    }
}

if (getSettingValue('sso_statut') == 'lcs')
{
  include LCS_PAGE_AUTH_INC_PHP;
  include LCS_PAGE_LDAP_INC_PHP;
  list ($idpers,$login) = isauth();
  if ($idpers) {
      list($user, $groups)=people_get_variables($login, true);
      $lcs_tab_login["nom"] = $user["nom"];
      $lcs_tab_login["email"] = $user["email"];
      $long = strlen($user["fullname"]) - strlen($user["nom"]);
      $lcs_tab_login["fullname"] = substr($user["fullname"], 0, $long) ;
      foreach($groups as $value) {
          $lcs_groups[] = $value["cn"];
      }
      // A ce stade, l'utilisateur est authentifi� par LCS
      // Etablir � nouveau la connexion � la base
      if (empty($db_nopersist))
          $db_c = mysql_pconnect($dbHost, $dbUser, $dbPass);
      else
          $db_c = mysql_connect($dbHost, $dbUser, $dbPass);
      if (!$db_c || !mysql_select_db ($dbDb)) {
          echo "\n<p>\n" . get_vocab('failed_connect_db') . "\n";
          exit;
      }

      if (!(is_eleve($login)))
         $user_ext_authentifie = 'lcs_eleve';
      else
         $user_ext_authentifie = 'lcs_non_eleve';
      $password = '';
      $result = grr_opensession($login,$password,$user_ext_authentifie,$lcs_tab_login,$lcs_groups) ;
   }
}

if ((!@grr_resumeSession()) and $valid!='yes') {
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
    <HTML>
    <HEAD>
    <META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php
    if ($unicode_encoding)
        echo "utf-8";
    else
        echo $charset_html;
    ?>">
    <link REL="stylesheet" href="themes/default/css/style.css" type="text/css">
    <TITLE> GRR </TITLE>
    <LINK REL="SHORTCUT ICON" href="./favicon.ico">
    <script type="text/javascript" src="./functions.js" language="javascript"></script>
    </HEAD>
    <BODY>
    <form action="admin_maj.php" method='post' style="width: 100%; margin-top: 24px; margin-bottom: 48px;">
    <div class="center">
    <H2><?php echo get_vocab("maj_bdd").grr_help("aide_grr_maj"); ?></H2>

    <?php
    if (isset($message)) {
        echo("<p><font color=red>" . encode_message_utf8($message) . "</font></p>");
    }
    ?>
    <fieldset style="padding-top: 8px; padding-bottom: 8px; width: 40%; margin-left: auto; margin-right: auto;">
    <legend style="font-variant: small-caps;"><?php echo get_vocab("identification"); ?></legend>
    <table style="width: 100%; border: 0;" cellpadding="5" cellspacing="0">
    <tr>
    <td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="login"><?php echo get_vocab("login"); ?></label></td>
    <td style="text-align: center; width: 60%;"><input type="text" name="login" size="16" /></td>
    </tr>
    <tr>
    <td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="password"><?php echo get_vocab("pwd"); ?></label></td>
    <td style="text-align: center; width: 60%;"><input type="password" name="password" size="16" /></td>
    </tr>
    </table>
    <input type="submit" name="submit" value="<?php echo get_vocab("submit"); ?>" style="font-variant: small-caps;" />
    </fieldset>
    </div>
    </form>
    </body>
    </html>
    <?php
    die();
};

$back = '';
if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);
if ((authGetUserLevel(getUserName(),-1) < 5) and ($valid != 'yes'))
{
    $day   = date("d");
    $month = date("m");
    $year  = date("Y");
    showAccessDenied($day, $month, $year, $area,$back);
    exit();
}
if ($valid == 'no') {
    # print the page header
    print_header("","","","",$type="with_session", $page="admin");
    // Affichage de la colonne de gauche
    include "admin_col_gauche.php";

} else {
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
    <HTML>
    <HEAD>
    <META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php
    if ($unicode_encoding)
        echo "utf-8";
    else
        echo $charset_html;
    ?>">

    <link REL="stylesheet" href="style.css" type="text/css">
    <LINK REL="SHORTCUT ICON" href="favicon.ico">
    <TITLE> GRR </TITLE>
    </HEAD>
    <BODY>
    <?php
}

    ?>
    <script type="text/javascript" src="./functions.js" language="javascript"></script>
    <?php
$result = '';
$result_inter = '';
if (isset($_POST['maj']) or isset($_GET['force_maj'])) {
    // On commence la mise � jour

    if  ($version_old < "1.4.9") {
        $result .= "<b>Mise � jour jusqu'� la version 1.4 :</b><br />";
        $result_inter .= traite_requete("ALTER TABLE mrbs_area ADD order_display TINYINT NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE mrbs_room ADD max_booking SMALLINT DEFAULT '-1' NOT NULL ;");
        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='sessionMaxLength'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('sessionMaxLength', '30');");

        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='automatic_mail'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('automatic_mail', 'yes');");

        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='begin_bookings'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('begin_bookings', '1062367200');");

        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='end_bookings'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('end_bookings', '1088546400');");

        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='company'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('company', 'Nom de l\'�tablissement');");

        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='webmaster_name'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('webmaster_name', 'Webmestre de GRR');");

        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='webmaster_email'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('webmaster_email', 'admin@mon.site.fr');");

        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='technical_support_email'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('technical_support_email', 'support.technique@mon.site.fr');");

        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='grr_url'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('grr_url', 'http://mon.site.fr/grr/');");

        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='disable_login'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('disable_login', 'no');");

        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';
    }
    if ($version_old < "1.5.9") {
        $result .= "<b>Mise � jour jusqu'� la version 1.5 :</b><br />";
        // GRR1.5
        $result_inter .= traite_requete("ALTER TABLE utilisateurs ADD default_area SMALLINT NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE utilisateurs ADD default_room SMALLINT NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE utilisateurs ADD default_style VARCHAR( 50 ) NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE utilisateurs ADD default_list_type VARCHAR( 50 ) NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE utilisateurs ADD default_language VARCHAR( 3 ) NOT NULL ;");
        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='title_home_page'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('title_home_page', 'Gestion et R�servation de Ressources');");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('message_home_page', 'En raison du caract�re personnel du contenu, ce site est soumis � des restrictions utilisateurs. Pour acc�der aux outils de r�servation, identifiez-vous :');");
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';
    }
    if ($version_old < "1.6.9") {
        $result .= "<b>Mise � jour jusqu'� la version 1.6 :</b><br />";
        // GRR1.6
        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='default_language'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('default_language', 'fr');");
        $result_inter .= traite_requete("ALTER TABLE mrbs_entry ADD statut_entry CHAR( 1 ) DEFAULT '-' NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE mrbs_room ADD statut_room CHAR( 1 ) DEFAULT '1' NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE mrbs_room ADD show_fic_room CHAR( 1 ) DEFAULT 'n' NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE mrbs_room ADD picture_room VARCHAR( 50 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE mrbs_room ADD comment_room TEXT NOT NULL;");
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';
    }
    if ($version_old < "1.7.9") {
        $result .= "<b>Mise � jour jusqu'� la version 1.7 :</b><br />";
        // GRR1.7
        $result_inter .= traite_requete("ALTER TABLE utilisateurs ADD source VARCHAR( 10 ) NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE j_mailuser_room CHANGE login login VARCHAR( 20 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE j_user_area CHANGE login login VARCHAR( 20 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE j_user_room CHANGE login login VARCHAR( 20 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE j_mailuser_room ADD PRIMARY KEY ( login , id_room ) ;");
        $result_inter .= traite_requete("ALTER TABLE j_user_area ADD PRIMARY KEY ( login , id_area ) ;");
        $result_inter .= traite_requete("ALTER TABLE j_user_room ADD PRIMARY KEY ( login , id_room ) ;");
        $result_inter .= traite_requete("ALTER TABLE log CHANGE LOGIN LOGIN VARCHAR( 20 ) NOT NULL;");
        $req = grr_sql_query1("SELECT VALUE FROM setting WHERE NAME='url_disconnect'");
        if ($req == -1) $result_inter .= traite_requete("INSERT INTO setting VALUES ('url_disconnect', '');");

        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';
    }
    if ($version_old < "1.8.0.9") {
        $result .= "<b>Mise � jour jusqu'� la version 1.8 :</b><br />";
        // GRR1.8
        $result_inter .= traite_requete("ALTER TABLE utilisateurs CHANGE login login VARCHAR( 20 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE utilisateurs CHANGE nom nom VARCHAR( 30 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE utilisateurs CHANGE prenom prenom VARCHAR( 30 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE utilisateurs CHANGE password password VARCHAR( 32 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE utilisateurs CHANGE email email VARCHAR( 100 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE utilisateurs CHANGE statut statut VARCHAR( 30 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE utilisateurs ADD PRIMARY KEY ( login );");
        $result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS grr_j_useradmin_area (login varchar(20) NOT NULL default '', id_area int(11) NOT NULL default '0', PRIMARY KEY  (login,id_area) );");
        $result_inter .= traite_requete("ALTER TABLE j_mailuser_room RENAME grr_j_mailuser_room;");
        $result_inter .= traite_requete("ALTER TABLE j_user_area RENAME grr_j_user_area;");
        $result_inter .= traite_requete("ALTER TABLE j_user_room RENAME grr_j_user_room;");
        $result_inter .= traite_requete("ALTER TABLE log RENAME grr_log;");
        $result_inter .= traite_requete("ALTER TABLE mrbs_area RENAME grr_area;");
        $result_inter .= traite_requete("ALTER TABLE mrbs_entry RENAME grr_entry;");
        $result_inter .= traite_requete("ALTER TABLE mrbs_repeat RENAME grr_repeat;");
        $result_inter .= traite_requete("ALTER TABLE mrbs_room RENAME grr_room;");
        $result_inter .= traite_requete("ALTER TABLE setting RENAME grr_setting;");
        $result_inter .= traite_requete("ALTER TABLE utilisateurs RENAME grr_utilisateurs;");
        $result_inter .= traite_requete("ALTER TABLE grr_area ADD ip_adr VARCHAR(15) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE grr_area CHANGE area_name area_name VARCHAR( 30 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE grr_room CHANGE description description VARCHAR( 60 ) NOT NULL;");
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';

    }
    if ($version_old < "1.9.0.9") {
        $result .= "<b>Mise � jour jusqu'� la version 1.9 :</b><br />";
        // GRR1.9
        $result_inter .= traite_requete("ALTER TABLE grr_area ADD morningstarts_area SMALLINT NOT NULL ,ADD eveningends_area SMALLINT NOT NULL , ADD resolution_area SMALLINT NOT NULL ,ADD eveningends_minutes_area SMALLINT NOT NULL ,ADD weekstarts_area SMALLINT NOT NULL ,ADD twentyfourhour_format_area SMALLINT NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE grr_area ADD calendar_default_values VARCHAR( 1 ) DEFAULT 'y' NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE grr_room ADD delais_max_resa_room SMALLINT DEFAULT '-1' NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE grr_room ADD delais_min_resa_room SMALLINT DEFAULT '0' NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE grr_room ADD order_display SMALLINT DEFAULT '0' NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE grr_room ADD allow_action_in_past VARCHAR( 1 ) DEFAULT 'n' NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE grr_j_mailuser_room CHANGE login login VARCHAR( 40 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE grr_j_user_area CHANGE login login VARCHAR( 40 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE grr_j_user_room CHANGE login login VARCHAR( 40 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE grr_j_useradmin_area CHANGE login login VARCHAR( 40 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE grr_log CHANGE LOGIN LOGIN VARCHAR( 40 ) NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE grr_utilisateurs CHANGE login login VARCHAR( 40 ) NOT NULL;");
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';

    }
    if ($version_old < "1.9.1.9") {
        $result .= "<b>Mise � jour jusqu'� la version 1.9.1 :</b><br />";
        // GRR1.9.1
        $result_inter .= traite_requete("ALTER TABLE grr_log CHANGE USER_AGENT USER_AGENT VARCHAR( 100 ) NOT NULL;");
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';
    }
    if ($version_old < "1.9.2.9") {
        $result .= "<b>Mise � jour jusqu'� la version 1.9.2 :</b><br />";
        // GRR1.9.2
        $result_inter .= traite_requete("ALTER TABLE grr_area ADD enable_periods VARCHAR( 1 ) DEFAULT 'n' NOT NULL ;");
        $result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS grr_area_periodes (id_area INT NOT NULL , num_periode SMALLINT NOT NULL , nom_periode VARCHAR( 100 ) NOT NULL );");
        $result_inter .= traite_requete("ALTER TABLE grr_room ADD delais_option_reservation SMALLINT DEFAULT '0' NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE grr_entry ADD option_reservation INT DEFAULT '0' NOT NULL;");
        $result_inter .= traite_requete("ALTER TABLE grr_room ADD dont_allow_modify VARCHAR( 1 ) DEFAULT 'n' NOT NULL ;");
        $result_inter .= traite_requete("ALTER TABLE grr_room ADD type_affichage_reser SMALLINT DEFAULT '0' NOT NULL;");
        $result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS grr_type_area (id int(11) NOT NULL auto_increment, type_name varchar(30) NOT NULL default '',order_display smallint(6) NOT NULL default '0',couleur smallint(6) NOT NULL default '0',type_letter char(2) NOT NULL default '',  PRIMARY KEY  (id));");
        $result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS grr_j_type_area (id_type int(11) NOT NULL default '0', id_area int(11) NOT NULL default '0');");
        $result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS grr_calendar (DAY int(11) NOT NULL default '0');");
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';

    }

    if ($version_old < "1.9.3.9") {
      $result .= "<b>Mise � jour jusqu'� la version 1.9.3 :</b><br />";
      // GRR1.9.3
      $result_inter .= traite_requete("ALTER TABLE grr_entry ADD overload_desc text;");
      $result_inter .= traite_requete("ALTER TABLE grr_repeat ADD overload_desc text;");
      $result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS grr_overload (id int(11) NOT NULL auto_increment, id_area INT NOT NULL, fieldname VARCHAR(25) NOT NULL default '', fieldtype VARCHAR(25) NOT NULL default '', obligatoire CHAR( 1 ) DEFAULT 'n' NOT NULL, PRIMARY KEY  (id));");
      $result_inter .= traite_requete("ALTER TABLE grr_area ADD display_days VARCHAR( 7 ) DEFAULT 'yyyyyyy' NOT NULL;");
      $result_inter .= traite_requete("UPDATE grr_utilisateurs SET default_style='';");

      // Suppression du param�tre url_disconnect_lemon
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='url_disconnect_lemon'");
      if (($req != -1) and (($req != ""))) {
          $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('url_disconnect', '".$req."');");
          $del = traite_requete("DELETE from grr_setting where NAME='url_disconnect_lemon'");
      }
      // Mise � jour de cas_statut
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='cas_statut'");
      if ($req == "visiteur") {
          $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('sso_statut', 'cas_visiteur');");
          $del = traite_requete("DELETE from grr_setting where NAME='cas_statut'");
      }
      if ($req == "utilisateur") {
          $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('sso_statut', 'cas_utilisateur');");
          $del = traite_requete("DELETE from grr_setting where NAME='cas_statut'");
      }
      // Mise � jour de lemon_statut
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='lemon_statut'");
      if ($req == "visiteur") {
          $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('sso_statut', 'lemon_visiteur');");
          $del = traite_requete("DELETE from grr_setting where NAME='lemon_statut'");
      }
      if ($req == "utilisateur") {
          $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('sso_statut', 'lemon_utilisateur');");
          $del = traite_requete("DELETE from grr_setting where NAME='lemon_statut'");
      }



      if ($result_inter == '')
    {
      $result .= "<font color=\"green\">Ok !</font><br />";
    }
      else
    {
      $result .= $result_inter;
    }
      $result_inter = '';

    }

    if ($version_old < "1.9.4.9") {
      $result .= "<b>Mise � jour jusqu'� la version 1.9.4 :</b><br />";
      // GRR1.9.4
      $result_inter .= traite_requete("ALTER TABLE grr_overload ADD fieldlist TEXT NOT NULL AFTER fieldtype;");
      $result_inter .= traite_requete("ALTER TABLE grr_entry CHANGE type type CHAR(2);");
      $result_inter .= traite_requete("ALTER TABLE grr_repeat CHANGE type type CHAR(2);");
      $result_inter .= traite_requete("ALTER TABLE  grr_room ADD  moderate TINYINT( 1 ) NULL DEFAULT  '0';");
      $result_inter .= traite_requete("ALTER TABLE  grr_entry ADD  moderate TINYINT( 1 ) NULL DEFAULT  '0';");
      $result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS grr_entry_moderate (id int(11) NOT NULL auto_increment, login_moderateur varchar(40) NOT NULL default '',motivation_moderation text NOT NULL,start_time int(11) NOT NULL default '0',end_time int(11) NOT NULL default '0',entry_type int(11) NOT NULL default '0', repeat_id int(11) NOT NULL default '0',room_id int(11) NOT NULL default '1',timestamp timestamp(14) NOT NULL,create_by varchar(25) NOT NULL default '',name varchar(80) NOT NULL default '',type char(2) default NULL,description text,statut_entry char(1) NOT NULL default '-',option_reservation int(11) NOT NULL default '0',overload_desc text,moderate tinyint(1) default '0', PRIMARY KEY  (id), KEY idxStartTime (start_time), KEY idxEndTime (end_time) );");
      $result_inter .= traite_requete("ALTER TABLE grr_area ADD id_type_par_defaut INT(11) DEFAULT '-1' NOT NULL;");
      $result_inter .= traite_requete("ALTER TABLE grr_overload ADD obligatoire CHAR( 1 ) DEFAULT 'n' NOT NULL;");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='allow_users_modify_profil'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('allow_users_modify_profil', '2');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='allow_users_modify_mdp'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('allow_users_modify_mdp', '2');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='display_info_bulle'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('display_info_bulle', '1');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='display_full_description'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('display_full_description', '1');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='pview_new_windows'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('pview_new_windows', '1');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='default_report_days'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('default_report_days', '30');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='use_fckeditor'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('use_fckeditor', '1');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='authentification_obli'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('authentification_obli', '0');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='visu_fiche_description'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('visu_fiche_description', '0');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='allow_search_level'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('allow_search_level', '1');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='allow_user_delete_after_begin'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('allow_user_delete_after_begin', '0');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='allow_gestionnaire_modify_del'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('allow_gestionnaire_modify_del', '1');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='javascript_info_disabled'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('javascript_info_disabled', '1');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='javascript_info_admin_disabled'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('javascript_info_admin_disabled', '1');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='pass_leng'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('pass_leng', '6');");
      if ($result_inter == '')
      {
      $result .= "<font color=\"green\">Ok !</font><br />";
      }
      else
      {
      $result .= $result_inter;
      }
      $result_inter = '';

    }

    if ($version_old < "1.9.5.1") {
      $result .= "<b>Mise � jour jusqu'� la version 1.9.5 RC1 :</b><br />";
      // GRR1.9.5
      $result_inter .= traite_requete("ALTER TABLE grr_area ADD duree_max_resa_area INT DEFAULT '-1' NOT NULL ;");
      $result_inter .= traite_requete("ALTER TABLE grr_area ADD duree_par_defaut_reservation_area SMALLINT DEFAULT '0' NOT NULL ;");
      $result_inter .= traite_requete("ALTER TABLE grr_log CHANGE USER_AGENT USER_AGENT VARCHAR( 255 );");
      $result_inter .= traite_requete("ALTER TABLE grr_log CHANGE REFERER REFERER VARCHAR( 255 );");
      $result_inter .= traite_requete("ALTER TABLE grr_entry ADD jours TINYINT( 2 ) NOT NULL DEFAULT '0';");
      $result_inter .= traite_requete("ALTER TABLE grr_repeat ADD jours TINYINT( 2 ) NOT NULL DEFAULT '0';");
      $result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS grr_calendrier_jours_cycle (DAY int(11) NOT NULL default '0', Jours tinyint(2) NOT NULL default '0');");
      $result_inter .= traite_requete("ALTER TABLE grr_overload ADD affichage CHAR( 1 ) DEFAULT 'n' NOT NULL;");
      $result_inter .= traite_requete("ALTER TABLE grr_overload ADD overload_mail CHAR( 1 ) DEFAULT 'n' NOT NULL;");
      $result_inter .= traite_requete("ALTER TABLE grr_area CHANGE resolution_area resolution_area INT DEFAULT '0' NOT NULL");
      $result_inter .= traite_requete("ALTER TABLE grr_area CHANGE duree_par_defaut_reservation_area duree_par_defaut_reservation_area INT DEFAULT '0' NOT NULL");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='allow_users_modify_email'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('allow_users_modify_email', '2');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='jour_debut_Jours/Cycles'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('jour_debut_Jours/Cycles', '1');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='nombre_jours_Jours/Cycles'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('nombre_jours_Jours/Cycles', '1');");
      $req = grr_sql_query1("SELECT NAME FROM grr_setting WHERE NAME='UserAllRoomsMaxBooking'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('UserAllRoomsMaxBooking', '-1');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='jours_cycles_actif'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('jours_cycles_actif', 'Non');");
      $req = grr_sql_query1("SELECT count(VALUE) FROM grr_setting WHERE NAME='grr_mail_Password'");
      if ($req == 0) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('grr_mail_Password', '');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='grr_mail_method'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('grr_mail_method', 'mail');");
      $req = grr_sql_query1("SELECT count(VALUE) FROM grr_setting WHERE NAME='grr_mail_smtp'");
      if ($req == 0) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('grr_mail_smtp', '');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='grr_mail_Bcc'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('grr_mail_Bcc', 'n');");
      $req = grr_sql_query1("SELECT count(VALUE) FROM grr_setting WHERE NAME='grr_mail_Username'");
      if ($req == 0) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('grr_mail_Username', '');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='verif_reservation_auto'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('verif_reservation_auto', '0');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='ConvertLdapUtf8toIso'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('ConvertLdapUtf8toIso', 'y');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='ActiveModeDiagnostic'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('ActiveModeDiagnostic', 'n');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='ldap_champ_nom'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('ldap_champ_nom', 'sn');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='ldap_champ_prenom'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('ldap_champ_prenom', 'givenname');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='ldap_champ_email'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('ldap_champ_email', 'mail');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='gestion_lien_aide'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('gestion_lien_aide', 'ext');");
      $req = grr_sql_query1("SELECT count(VALUE) FROM grr_setting WHERE NAME='lien_aide'");
      if ($req == 0) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('lien_aide', '');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='display_short_description'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('display_short_description', '1');");
      $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='remplissage_description_breve'");
      if ($req == -1) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('remplissage_description_breve', '1');");
      $req1 = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='ldap_statut'");
      $req2 = grr_sql_query1("SELECT count(VALUE) FROM grr_setting WHERE NAME='ldap_champ_recherche'");
      if ((($req1=="utilisateur") or ($req1=="visiteur")) and ($req2 == 0)) {
          $result_inter .= "<br /><font color='red'><b>AVERTISSEMENT</b> : suite � cette mise � jour, vous devez configurer l'<b>attribut utilis� pour la recherche dans l'annuaire ldap</b>. Pour cela, rendez-vous dans la page de configuration LDAP.</font><br />";

      }
      if ($req2 == 0) $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('ldap_champ_recherche', 'uid');");

      $req = grr_sql_count(grr_sql_query("SHOW COLUMNS FROM grr_entry LIKE 'beneficiaire'"));
      if ($req == 0) {
          $result_inter .= traite_requete("ALTER TABLE `grr_entry` ADD beneficiaire VARCHAR( 100 ) NOT NULL AFTER `create_by`");
          $result_inter .= traite_requete("update `grr_entry` set `beneficiaire` = `create_by`");
          $result_inter .= traite_requete("ALTER TABLE `grr_entry_moderate` ADD beneficiaire VARCHAR( 100 ) NOT NULL AFTER `create_by`");
          $result_inter .= traite_requete("update `grr_entry_moderate` set `beneficiaire` = `create_by`");
          $result_inter .= traite_requete("ALTER TABLE `grr_repeat` ADD beneficiaire VARCHAR( 100 ) NOT NULL AFTER `create_by`");
          $result_inter .= traite_requete("update `grr_repeat` set `beneficiaire` = `create_by`");
          $result_inter .= traite_requete("ALTER TABLE `grr_entry` ADD beneficiaire_ext VARCHAR( 200 ) NOT NULL AFTER `create_by`");
          $result_inter .= traite_requete("ALTER TABLE `grr_entry_moderate` ADD beneficiaire_ext VARCHAR( 200 ) NOT NULL AFTER `create_by`");
          $result_inter .= traite_requete("ALTER TABLE `grr_repeat` ADD beneficiaire_ext VARCHAR( 200 ) NOT NULL AFTER `create_by`");

      };
      $result_inter .= traite_requete("ALTER TABLE grr_room ADD qui_peut_reserver_pour VARCHAR( 1 ) DEFAULT '5' NOT NULL");
      if ($result_inter == '')
      {
      $result .= "<font color=\"green\">Ok !</font><br />";
      }
      else
      {
      $result .= $result_inter;
      }
      $result_inter = '';

    }

    if ($version_old < "1.9.5.9") {
      $result .= "<b>Mise � jour jusqu'� la version 1.9.5 :</b><br />";
      // GRR1.9.5
      $result_inter .= traite_requete("ALTER TABLE grr_calendrier_jours_cycle CHANGE Jours Jours VARCHAR(20);");
      if (getSettingValue("maj195_champ_rep_type_grr_repeat") != 1) {
          // Avant la version 195, la valeur 6 �tait utilis�e pour le type "une semaine sur n"
          // et la valeur 7 pour la p�riodicit� jour cycle
          $result_inter .= traite_requete("update grr_repeat set rep_type = 2 where rep_type = 6");
          $result_inter .= traite_requete("update grr_repeat set rep_type = 6 where rep_type = 7");
          $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('maj195_champ_rep_type_grr_repeat', '1')");
      }
      $result_inter .= traite_requete("ALTER TABLE grr_room ADD active_ressource_empruntee CHAR( 1 ) NOT NULL DEFAULT 'y'");
      $result_inter .= traite_requete("ALTER TABLE grr_overload ADD confidentiel CHAR( 1 ) NOT NULL DEFAULT 'n'");
      if ($result_inter == '')
      {
      $result .= "<font color=\"green\">Ok !</font><br />";
      }
      else
      {
      $result .= $result_inter;
      }
      $result_inter = '';
    }

    // Mise � jour du num�ro de version
    $req = grr_sql_query1("SELECT VALUE FROM grr_setting WHERE NAME='version'");
    if ($req == -1) {
        $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('version', '".$version_grr."');");
    } else {
        $result_inter .= traite_requete("UPDATE grr_setting SET VALUE='".$version_grr."' WHERE NAME='version';");
    }

    // Mise � jour du num�ro de RC
    $req = grr_sql_command("DELETE FROM grr_setting WHERE NAME='versionRC'");
    $result_inter .= traite_requete("INSERT INTO grr_setting VALUES ('versionRC', '".$version_grr_RC."');");


    //Re-Chargement des valeurs de la table settingS
    if (!loadSettings()) {
        die("Erreur chargement settings");
    }
    affiche_pop_up(get_vocab("maj_good"),"force");
}
// Num�ro de version effective
$version_old = getSettingValue("version");
if ($version_old == "") $version_old = "1.3";
// Num�ro de RC
$version_old_RC = getSettingValue("versionRC");

// Calcul du num�ro de version actuel de la base qui sert aux test de comparaison et de la chaine � afficher
if ($version_old_RC == "") {
    $version_old_RC = 9;
    $display_version_old = $version_old;
} else {
    $display_version_old = $version_old."_RC".$version_old_RC;
}
$version_old .= ".".$version_old_RC;

// Calcul de la chaine � afficher
if ($version_grr_RC == "") {
    $display_version_grr = $version_grr.$sous_version_grr;
} else {
    $display_version_grr = $version_grr."_RC".$version_grr_RC;
}

echo "<h2>".get_vocab('admin_maj.php').grr_help("aide_grr_maj")."</h2>";

echo "<hr />";
// Num�ro de version
echo "<h3>".get_vocab("num_version_title")."</h3>\n";
echo "<p>".get_vocab("num_version").$display_version_grr;
echo "</p>\n";

echo get_vocab('database') . grr_sql_version() . "\n";
echo "<br />" . get_vocab('system') . php_uname() . "\n";
echo "<br />Version PHP : " . phpversion() . "\n";


echo "<p>".get_vocab("maj_go_www")."<a href=\"".$grr_devel_url."\">".get_vocab("mrbs")."</a></p>\n";
echo "<hr />\n";
// Mise � jour de la base de donn�e
echo "<h3>".get_vocab("maj_bdd")."</h3>";
// V�rification du num�ro de version
if (verif_version()) {
    echo "<form action=\"admin_maj.php\" method=\"post\">";
    echo "<p><font color=red><b>".get_vocab("maj_bdd_not_update");
    echo " ".get_vocab("maj_version_bdd").$display_version_old;
    echo "</b></font><br />";
    echo get_vocab("maj_do_update")."<b>".$display_version_grr."</b></p>";
    echo "<input type=submit value='".get_vocab("maj_submit_update")."' />";
    echo "<input type=hidden name='maj' value='yes' />";
    echo "<input type=hidden name='version_old' value='$version_old' />";
    echo "<input type=hidden name='valid' value='$valid' />";
    echo "</form>";
} else {
    echo "<p>".get_vocab("maj_no_update_to_do")."</p>";
    echo "<center><p><a href=\"./\">".get_vocab("welcome")."</a></p></center>";
}

// V�rification du format des champs additionnels
if ($version_grr > "1.9.4") {
    // par s�curit�, ii version sup�rieure � 194 on force maj194_champs_additionnels � 1 (donc on ne v�rifie par les champs add
    grr_sql_query("DELETE from grr_setting where NAME = 'maj194_champs_additionnels'");
    grr_sql_query("INSERT INTO grr_setting VALUES ('maj194_champs_additionnels', '1');");

}
// Avant version 1.9.4, les champs add �taient stock�s sous la forme <id_champ>champ_encode_en_base_64</id_champ>
// A partir de la version 1.9.4, les champs add. sont stock�s sous la forme @id_champ@url_encode(champ)@/id_champ@
if (($version_grr > "1.9.3") and (getSettingValue("maj194_champs_additionnels") != 1) and (isset($_POST['maj']) or isset($_GET['force_maj']))) {


    // On constuite un tableau des id des grr_overload:
    $sql_overload = grr_sql_query("select id from grr_overload");
    for ($i = 0; ($row = grr_sql_row($sql_overload, $i)); $i++) {
        $tab_id_overload[] = $row[0];
    }
    // On selectionne les entr�es
    $sql_entry = grr_sql_query("select overload_desc, id from grr_entry where overload_desc != ''");
    for ($i = 0; ($row = grr_sql_row($sql_entry, $i)); $i++) {
        $nouvelle_chaine = "";
        foreach ($tab_id_overload as $value) {
            $begin_string = "<".$value.">";
            $end_string = "</".$value.">";
            $begin_pos = strpos($row[0],$begin_string);
            $end_pos = strpos($row[0],$end_string);
            if ( $begin_pos !== false && $end_pos !== false ) {
                $first = $begin_pos + strlen($begin_string);
                $data = substr($row[0],$first,$end_pos-$first);
                $data  = urlencode(base64_decode($data));
                $nouvelle_chaine .= "@".$value."@".$data."@/".$value."@";
            }

        }
        // On met � jour le champ
        if ($nouvelle_chaine != '') $up = grr_sql_query("update grr_entry set overload_desc = '".$nouvelle_chaine."' where id='".$row[1]."'");
    }
    // on inscrit le r�sultat dans la table grr_settings
    grr_sql_query("DELETE from grr_setting where NAME = 'maj194_champs_additionnels'");
    grr_sql_query("INSERT INTO grr_setting VALUES ('maj194_champs_additionnels', '1');");

    $result .= "<b>Mise � jour des champs additionnels : </b><font color=\"green\">Ok !</font><br /><br />";
}

echo "<hr />";
if (isset($result) and ($result != '')) {
    echo "<center><table width=\"80%\" border=\"1\" cellpadding=\"5\" cellspacing=\"1\"><tr><td><h2 align=\"center\">".encode_message_utf8("R�sultat de la mise � jour")."</H2>";
    echo encode_message_utf8($result);
    echo $result_inter;
    echo "</td></tr></table></center>";
}

// Test de coh�rence des types de r�servation
if ($version_grr > "1.9.1") {
    $res = grr_sql_query("select distinct type from grr_entry order by type");
    if ($res) {
        $liste = "";
        for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
        {
            $test = grr_sql_query1("select type_letter from grr_type_area where type_letter='".$row[0]."'");
            if ($test == -1) $liste .= $row[0]." ";
        }
        if ($liste != "") {
            echo encode_message_utf8("<table border=\"1\" cellpadding=\"5\"><tr><td><p><font color=red><b>ATTENTION : votre table des types de r�servation n'est pas � jour :</b></font></p>");
            echo encode_message_utf8("<p>Depuis la version 1.9.2, les types de r�servation ne sont plus d�finis dans le fichier config.inc.php
            mais directement en ligne. Un ou plusieurs types sont actuellement utilis�s dans les r�servations
            mais ne figurent pas dans la tables des types. Cela risque d'engendrer des messages d'erreur. <b>Il s'agit du ou des types suivants : ".$liste."</b>");
            echo encode_message_utf8("<br /><br />Vous devez donc d�finir dans <a href= './admin_type.php'>l'interface de gestion des types</a>, le ou les types manquants, en vous aidant �ventuellement des informations figurant dans votre ancien fichier config.inc.php.</p></td></tr></table>");
        }
    }
}
// fin de l'affichage de la colonne de droite
if ($valid == 'no') echo "</td></tr></table>";
?>
</body>
</html>