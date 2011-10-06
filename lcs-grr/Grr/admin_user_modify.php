<?php
/**
 * admin_user_modify.php
 * Interface de modification/cr�ation d'un utilisateur de l'application GRR
 * Derni�re modification : $Date: 2010-04-07 15:38:14 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   admin
 * @version   $Id: admin_user_modify.php,v 1.16 2010-04-07 15:38:14 grr Exp $
 * @filesource
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
 * $Log: admin_user_modify.php,v $
 * Revision 1.16  2010-04-07 15:38:14  grr
 * *** empty log message ***
 *
 * Revision 1.15  2009-12-02 20:11:07  grr
 * *** empty log message ***
 *
 * Revision 1.14  2009-09-29 18:02:56  grr
 * *** empty log message ***
 *
 * Revision 1.13  2009-06-04 15:30:17  grr
 * *** empty log message ***
 *
 * Revision 1.12  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.11  2009-03-24 13:30:07  grr
 * *** empty log message ***
 *
 * Revision 1.10  2009-02-27 13:28:19  grr
 * *** empty log message ***
 *
 * Revision 1.9  2009-01-20 07:19:17  grr
 * *** empty log message ***
 *
 * Revision 1.8  2008-11-16 22:00:58  grr
 * *** empty log message ***
 *
 * Revision 1.7  2008-11-11 22:01:14  grr
 * *** empty log message ***
 *
 * Revision 1.6  2008-11-10 06:49:45  grr
 * les options "administrateur g�n�ral"  et "gestionnaire d'utilisateurs" n'apparaissent plus dans la liste de choix des gestionnaires d'utilisateurs.
 *
 *
 *
 */


include "include/admin.inc.php";
$grr_script_name = "admin_user_modify.php";

$back = '';
if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);
$day   = date("d");
$month = date("m");
$year  = date("Y");

if ((authGetUserLevel(getUserName(),-1) < 6) and (authGetUserLevel(getUserName(),-1,'user') !=  1))
{
    showAccessDenied($day, $month, $year, '',$back);
    exit();
}
// un gestionnaire d'utilisateurs ne peut pas modifier un administrateur g�n�ral ou un gestionnaire d'utilisateurs
if (isset($_GET["user_login"]) and (authGetUserLevel(getUserName(),-1,'user') ==  1)) {
    $test_statut = grr_sql_query1("select statut from ".TABLE_PREFIX."_utilisateurs where login='".$_GET["user_login"]."'");
    if (($test_statut == "administrateur") or ($test_statut == "gestionnaire_utilisateur")) {
        showAccessDenied($day, $month, $year, '',$back);
        exit();
    }
}


#If we dont know the right date then make it up
unset($user_login);
$user_login = isset($_GET["user_login"]) ? $_GET["user_login"] : NULL;
$valid = isset($_GET["valid"]) ? $_GET["valid"] : NULL;
$msg='';
$user_nom='';
$user_prenom='';
$user_mail='';
$user_statut='';
$user_source='local';
$user_etat='';
$display="";
$retry='';



if ($valid == "yes") {
    // Restriction dans le cas d'une d�mo
    VerifyModeDemo();
    $reg_nom = isset($_GET["reg_nom"]) ? $_GET["reg_nom"] : NULL;
    $reg_prenom = isset($_GET["reg_prenom"]) ? $_GET["reg_prenom"] : NULL;
    $new_login = isset($_GET["new_login"]) ? $_GET["new_login"] : NULL;
    $reg_password = isset($_GET["reg_password"]) ? unslashes($_GET["reg_password"]) : NULL;
    $reg_password2 = isset($_GET["reg_password2"]) ? unslashes($_GET["reg_password2"]) : NULL;
    $reg_statut = isset($_GET["reg_statut"]) ? $_GET["reg_statut"] : NULL;
    $reg_email = isset($_GET["reg_email"]) ? $_GET["reg_email"] : NULL;
    $reg_etat = isset($_GET["reg_etat"]) ? $_GET["reg_etat"] : NULL;
    $reg_source = isset($_GET["reg_source"]) ? $_GET["reg_source"] : NULL;
    $reg_type_authentification = isset($_GET["type_authentification"]) ? $_GET["type_authentification"] : "locale";
    if ($reg_type_authentification!="locale") $reg_password="";

    if (($reg_nom == '') or ($reg_prenom == '')) {
        $msg = get_vocab("please_enter_name");
        $retry = 'yes';
    } else {
        //
        // actions si un nouvel utilisateur a �t� d�fini
        //
        $test_login = preg_replace("/([A-Za-z0-9_@. -])/","",$new_login);
        if ((isset($new_login)) and ($new_login!='') and ($test_login=="") ) {
            // un gestionnaire d'utilisateurs ne peut pas cr�er un administrateur g�n�ral ou un gestionnaire d'utilisateurs
            $test_statut = TRUE;
            if  (authGetUserLevel(getUserName(),-1) < 6) {
                if (($reg_statut == "administrateur") or ($reg_statut == "gestionnaire_utilisateur"))
                    $test_statut = FALSE;
            }
            $new_login = strtoupper($new_login);
            if ($reg_password !='')
              $reg_password_c = md5($reg_password);
            else
              if ($reg_type_authentification!="locale")
                 $reg_password_c='';
              else {
                 $msg = get_vocab("passwd_error");
                 $retry = 'yes';
              }
            if (!($test_statut)) {
                $msg = get_vocab("erreur_choix_statut");
                $retry = 'yes';
            } else if ((($reg_password != $reg_password2) or (strlen($reg_password) < $pass_leng)) and ($reg_type_authentification=="locale")) {
                $msg = get_vocab("passwd_error");
                $retry = 'yes';
            } else {
                $sql = "SELECT * FROM ".TABLE_PREFIX."_utilisateurs WHERE login = '".$new_login."'";
                $res = grr_sql_query($sql);
                $nombreligne = grr_sql_count ($res);
                if ($nombreligne != 0) {
                    $msg = get_vocab("error_exist_login");
                    $retry = 'yes';
                } else {
                    $sql = "INSERT INTO ".TABLE_PREFIX."_utilisateurs SET
                    nom='".protect_data_sql($reg_nom)."',
                    prenom='".protect_data_sql($reg_prenom)."',
                    login='".protect_data_sql($new_login)."',
                    password='".protect_data_sql($reg_password_c)."',
                    statut='".protect_data_sql($reg_statut)."',
                    email='".protect_data_sql($reg_email)."',
                    etat='".protect_data_sql($reg_etat)."',
                    default_site = '-1',
                    default_area = '-1',
                    default_room = '-1',
                    default_style = '',
                    default_list_type = '',
                    default_language = 'fr',";
                    if ($reg_type_authentification=="locale")
                        $sql .= "source='local'";
                    else
                        $sql .= "source='ext'";
                    if (grr_sql_command($sql) < 0)
                        {fatal_error(0, get_vocab("msg_login_created_error") . grr_sql_error());
                    } else {
                        $msg = get_vocab("msg_login_created");
                    }
                    $user_login = $new_login;
                }
            }
//
//action s'il s'agit d'une modification
//
        } else if ((isset($user_login)) and ($user_login!='')) {
            // un gestionnaire d'utilisateurs ne peut pas modifier un administrateur g�n�ral ou un gestionnaire d'utilisateurs
            $test_statut = TRUE;
            if  (authGetUserLevel(getUserName(),-1) < 6) {
                $old_statut = grr_sql_query1("select statut from ".TABLE_PREFIX."_utilisateurs where login='".protect_data_sql($user_login)."'");
                if (((($old_statut == "administrateur") or ($old_statut == "gestionnaire_utilisateur")) and ($old_statut !=$reg_statut))
                or ((($old_statut == "utilisateur") or ($old_statut == "visiteur")) and (($reg_statut == "administrateur") or ($reg_statut == "gestionnaire_utilisateur"))))
                    $test_statut = FALSE;
            }
            if (!($test_statut)) {
                $msg = get_vocab("erreur_choix_statut");
                $retry = 'yes';
            } else if ($reg_type_authentification=="locale") {
                // On demande un changement de la source ext->local
                if (($reg_password=='') and ($reg_password2=='')) {
                    $old_mdp = grr_sql_query1("select password from ".TABLE_PREFIX."_utilisateurs where login='".protect_data_sql($user_login)."'");
                    if (($old_mdp=='') or ($old_mdp==-1)) {
                      $msg = get_vocab("passwd_error");
                      $retry = 'yes';
                    } else
                      $reg_password_c='';
                } else {
                  $reg_password_c = md5($reg_password);
                  if (($reg_password != $reg_password2) or (strlen($reg_password) < $pass_leng)) {
                    $msg = get_vocab("passwd_error");
                    $retry = 'yes';
                  }
                }

            }
        if ($retry != 'yes') {
            $sql = "UPDATE ".TABLE_PREFIX."_utilisateurs SET nom='".protect_data_sql($reg_nom)."',
            prenom='".protect_data_sql($reg_prenom)."',
            statut='".protect_data_sql($reg_statut)."',
            email='".protect_data_sql($reg_email)."',";

            if ($reg_type_authentification=="locale") {
              $sql .= "source='local',";
              if ($reg_password_c!='')
                $sql .= "password='".protect_data_sql($reg_password_c)."',";
              } else
              $sql .= "source='ext',password='',";




            $sql .= "etat='".protect_data_sql($reg_etat)."'
            WHERE login='".protect_data_sql($user_login)."'";
            if (grr_sql_command($sql) < 0)
                {fatal_error(0, get_vocab("message_records_error") . grr_sql_error());
            } else {
                $msg = get_vocab("message_records");
            }

            // Cas o� on a d�clar� un utilisateur inactif, on le supprime dans les tables ".TABLE_PREFIX."_j_user_area,  ".TABLE_PREFIX."_j_mailuser_room
            if ($reg_etat != 'actif') {
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_user_area WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0) fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_mailuser_room WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0) fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_useradmin_area WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0)
                    fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_useradmin_site WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0)
                    fatal_error(0, get_vocab('message_records_error') . grr_sql_error());

            }

            // Cas o� on a d�clar� un utilisateur visiteur, on le supprime dans les tables ".TABLE_PREFIX."_j_user_area, ".TABLE_PREFIX."_j_mailuser_room et ".TABLE_PREFIX."_j_user_room

            if ($reg_statut=='visiteur') {
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_user_room WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0)
                    fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_mailuser_room WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0)
                    fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_user_area WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0)
                    fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_useradmin_area WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0)
                    fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_useradmin_site WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0)
                    fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
            }
            if ($reg_statut=='administrateur') {
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_user_room WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0)
                    fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_user_area WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0)
                    fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_useradmin_area WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0)
                    fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
                $sql = "DELETE FROM ".TABLE_PREFIX."_j_useradmin_site WHERE login='$user_login'";
                if (grr_sql_command($sql) < 0)
                    fatal_error(0, get_vocab('message_records_error') . grr_sql_error());

            }
        }

        } else {
            $msg = get_vocab("only_letters_and_numbers");
            $retry = 'yes';
        }
    }
    if ($retry == 'yes') {
        $user_nom = $reg_nom;
        $user_prenom = $reg_prenom;
        $user_statut = $reg_statut;
        $user_mail = $reg_email;
        $user_etat = $reg_etat;
    }
}

// On appelle les informations de l'utilisateur pour les afficher :
if (isset($user_login) and ($user_login!='')) {
    $sql = "SELECT nom, prenom, statut, etat, email, source FROM ".TABLE_PREFIX."_utilisateurs WHERE login='$user_login'";
    $res = grr_sql_query($sql);
    if ($res) {
        for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
        {
        $user_nom = htmlspecialchars($row[0]);
        $user_prenom = htmlspecialchars($row[1]);
        $user_statut = $row[2];
        $user_etat = $row[3];
        $user_mail = htmlspecialchars($row[4]);
        $user_source = $row[5];
        if ($user_source=="local")
          $flag_is_local="y";
        else
          $flag_is_local="n";
        }
    }
}

if ($user_source=="local")
    $statut_div = "visible";
else
    $statut_div = "hidden";

if((authGetUserLevel(getUserName(),-1) < 1) and (getSettingValue("authentification_obli")==1))
{
    showAccessDenied($day, $month, $year, '',$back);
    exit();
}

// Utilisation de la biblioth�qye prototype dans ce script
$use_prototype = 'y';

# print the page header
print_header("","","","",$type="with_session", $page="admin");
?>
<script type='text/javascript'>
	function display_password_fields(id){
		if ($(id).value=='locale') {
			$('password_fields').style.display='block';
			$('password_fields').style.visibility='visible';
		} else {
			$('password_fields').style.visibility='hidden';
			$('password_fields').style.display='none';
		}
	}

</script>
<?php

// Affichage d'un pop-up
affiche_pop_up($msg,"admin");

if (isset($user_login) and ($user_login!='')) {
    echo "<h2>".get_vocab('admin_user_modify_modify.php')."</h2>";
} else {
    echo "<h2>".get_vocab('admin_user_modify_create.php')."</h2>";
}


?>
<p class="bold">
| <a href="admin_user.php?display=<?php echo $display; ?>"><?php echo get_vocab("back"); ?></a> |
<?php
if (isset($user_login) and ($user_login!='')) {
    echo "<a href=\"admin_user_modify.php?display=$display\">".get_vocab("display_add_user")."</a> | ";

} ?>
<br /><?php echo get_vocab("required"); ?>
</p>
<form action="admin_user_modify.php?display=<?php echo $display; ?>" method='get'><div>

<?php
if ((getSettingValue("sso_statut") != "") or  (getSettingValue("ldap_statut") != '') or  (getSettingValue("imap_statut") != '')) {
    echo get_vocab("authentification").get_vocab("deux_points");
    echo "<select id=\"select_auth_mode\" name='type_authentification' onchange=\"display_password_fields(this.id);\">\n";
    echo "<option value='locale'";
    if ($user_source=='local') echo "selected=\"selected\" ";
    echo ">".get_vocab("authentification_base_locale")."</option>\n";
    echo "<option value='externe'";
    if ($user_source=='ext') echo "selected=\"selected\" ";
    echo ">".get_vocab("authentification_base_externe")."</option>\n";
    echo "</select><br /><br />\n";
}
echo get_vocab("login")." *".get_vocab("deux_points");
if (isset($user_login) and ($user_login!='')) {
    echo $user_login;
    echo "<input type=\"hidden\" name=\"reg_login\" value=\"$user_login\" />\n";
} else {
    echo "<input type=\"text\" name=\"new_login\" size=\"40\" value=\"".htmlentities($user_login)."\" />\n";
}

echo "<table border=\"0\" cellpadding=\"5\"><tr>\n";
echo "<td>".get_vocab("last_name")." *".get_vocab("deux_points")."</td>\n<td><input type=\"text\" name=\"reg_nom\" size=\"40\" value=\"";
if ($user_nom) echo htmlspecialchars($user_nom);
echo "\" /></td>\n";
echo "<td>".get_vocab("first_name")." *".get_vocab("deux_points")."</td>\n<td><input type=\"text\" name=\"reg_prenom\" size=\"20\" value=\"";
if ($user_nom) echo htmlspecialchars($user_prenom);
echo "\" /></td>\n";
echo "<td></td><td></td>";
echo "</tr>\n";

echo "<tr><td>".get_vocab("mail_user").get_vocab("deux_points")."</td><td><input type=\"text\" name=\"reg_email\" size=\"30\" value=\"";
if ($user_mail)  echo htmlspecialchars($user_mail);
echo "\" /></td>\n";

echo "<td>".get_vocab("statut").get_vocab("deux_points")."</td>\n";
echo "<td><select name=\"reg_statut\" size=\"1\">\n";
echo "<option value=\"visiteur\" "; if ($user_statut == "visiteur") { echo "selected=\"selected\"";}; echo ">".get_vocab("statut_visitor")."</option>\n";
echo "<option value=\"utilisateur\" "; if ($user_statut == "utilisateur") { echo "selected=\"selected\"";}; echo ">".get_vocab("statut_user")."</option>\n";
// un gestionnaire d'utilisateurs ne peut pas cr�er un administrateur g�n�ral ou un gestionnaire d'utilisateurs
if  (authGetUserLevel(getUserName(),-1) >= 6) {
  echo "<option value=\"gestionnaire_utilisateur\" "; if ($user_statut == "gestionnaire_utilisateur") { echo "selected=\"selected\"";}; echo ">".get_vocab("statut_user_administrator")."</option>\n";
  echo "<option value=\"administrateur\" "; if ($user_statut == "administrateur") { echo "selected=\"selected\"";}; echo ">".get_vocab("statut_administrator")."</option>\n";
}
echo "</select></td>\n";

if (strtolower(getUserName()) != strtolower($user_login)) {
  echo "<td>".get_vocab("activ_no_activ").get_vocab("deux_points")."</td>";
  echo "<td><select name=\"reg_etat\" size=\"1\">\n";
  echo "<option value=\"actif\" ";
  if ($user_etat == "actif")  echo "selected=\"selected\"";
  echo ">".get_vocab("activ_user")."</option>\n";
  echo "<option value=\"inactif\" ";
  if ($user_etat == "inactif")  echo "selected=\"selected\"";
  echo ">".get_vocab("no_activ_user")."</option>\n";
  echo "</select></td>";
} else {
    echo "<td></td><td><input type=\"hidden\" name=\"reg_etat\" value=\"$user_etat\" /></td>\n";
}
echo "</tr>\n";
echo "</table>";

echo "<div id='password_fields' style='visibility: ".$statut_div.";'>";
    if ((isset($user_login)) and ($user_login!='') and ($flag_is_local=="y"))
        echo "<b>".get_vocab("champ_vide_mot_de_passe_inchange")."</b>";
    echo "<br />".get_vocab("pwd_toot_short")." *".get_vocab("deux_points")."<input type=\"password\" name=\"reg_password\" size=\"20\" />\n";
    echo "<br />".get_vocab("confirm_pwd")." *".get_vocab("deux_points")."<input type=\"password\" name=\"reg_password2\" size=\"20\" />\n";
echo "</div>";

echo "<br />";
echo "<input type=\"hidden\" name=\"valid\" value=\"yes\" />\n";
if (isset($user_login)) echo "<input type=\"hidden\" name=\"user_login\" value=\"".$user_login."\" />\n";
echo "<br /><div style=\"text-align:center;\"><input type=\"submit\" value=\"".get_vocab("save")."\" /></div>\n";
echo "</div></form>\n";

// On affiche la liste des privil�ges de cet utilisateurs
if ((isset($user_login)) and ($user_login!='')) {
  echo "<h2>".get_vocab('liste_privileges').$user_prenom." ".$user_nom." :</h2>";
  $a_privileges = 'n';
  if (getSettingValue("module_multisite") == "Oui") {
      $req_site = "select id, sitename from ".TABLE_PREFIX."_site order by sitename";
      $res_site = grr_sql_query($req_site);
      if ($res_site) {
        for ($i = 0; ($row_site = grr_sql_row($res_site, $i)); $i++) {
           // On teste si l'utilisateur administre un site
           $test_admin_site = grr_sql_query1("select count(id_site) from ".TABLE_PREFIX."_j_useradmin_site j where j.login = '".$user_login."' and j.id_site='".$row_site[0]."'");
           if ($test_admin_site >= 1) {
             $a_privileges = 'y';
             echo "<h3>".get_vocab("site").get_vocab("deux_points").$row_site[1];
             echo "</h3>";
             echo "<ul>";
             echo "<li><b>".get_vocab("administrateur du site")."</b></li>";
             echo "</ul>";
           }
        }
      }
  }

  $req_area = "select id, area_name, access from ".TABLE_PREFIX."_area order by order_display";
  $res_area = grr_sql_query($req_area);
  if ($res_area) {
    for ($i = 0; ($row_area = grr_sql_row($res_area, $i)); $i++) {
        // On teste si l'utilisateur administre le domaine
        $test_admin = grr_sql_query1("select count(id_area) from ".TABLE_PREFIX."_j_useradmin_area j where j.login = '".$user_login."' and j.id_area='".$row_area[0]."'");
        if ($test_admin >= 1) $is_admin = 'y'; else $is_admin = 'n';
        // On teste si l'utilisateur g�re des ressources dans ce domaine
        $nb_room = grr_sql_query1("select count(r.room_name) from ".TABLE_PREFIX."_room r
        left join ".TABLE_PREFIX."_area a on r.area_id=a.id
        where a.id='".$row_area[0]."'");

        $req_room = "select r.room_name from ".TABLE_PREFIX."_room r
        left join ".TABLE_PREFIX."_j_user_room j on r.id=j.id_room
        left join ".TABLE_PREFIX."_area a on r.area_id=a.id
        where j.login = '".$user_login."' and a.id='".$row_area[0]."'";
        $res_room = grr_sql_query($req_room);
        $is_gestionnaire = '';
        if ($res_room) {
            if ((grr_sql_count($res_room) == $nb_room) and ($nb_room!=0))
                $is_gestionnaire = $vocab["all_rooms"];
            else
            for ($j = 0; ($row_room = grr_sql_row($res_room, $j)); $j++) {
                $is_gestionnaire .= $row_room[0]."<br />";
            }
        }
        // On teste si l'utilisateur re�oit des mails automatiques
        $req_mail = "select r.room_name from ".TABLE_PREFIX."_room r
        left join ".TABLE_PREFIX."_j_mailuser_room j on r.id=j.id_room
        left join ".TABLE_PREFIX."_area a on r.area_id=a.id
        where j.login = '".$user_login."' and a.id='".$row_area[0]."'";
        $res_mail = grr_sql_query($req_mail);
        $is_mail = '';
        if ($res_mail) {
            for ($j = 0; ($row_mail = grr_sql_row($res_mail, $j)); $j++) {
                $is_mail .= $row_mail[0]."<br />";
            }
        }
        // Si le domaine est restreint, on teste si l'utilateur a acc�s
        if ($row_area[2] == 'r') {
            $test_restreint = grr_sql_query1("select count(id_area) from ".TABLE_PREFIX."_j_user_area j where j.login = '".$user_login."' and j.id_area='".$row_area[0]."'");
            if ($test_restreint >= 1) $is_restreint = 'y'; else $is_restreint = 'n';
        } else $is_restreint = 'n';

        if (($is_admin == 'y') or ($is_restreint == 'y') or ($is_gestionnaire != '') or ($is_mail != '')) {
            $a_privileges = 'y';
            echo "<h3>".get_vocab("match_area").get_vocab("deux_points").$row_area[1];
            if ($row_area[2] == 'r') echo " (".$vocab["restricted"].")";
            echo "</h3>";
            echo "<ul>";
            if ($is_admin == 'y') echo "<li><b>".get_vocab("administrateur du domaine")."</b></li>";
            if ($is_restreint == 'y') echo "<li><b>".get_vocab("a acces au domaine")."</b></li>";
            if ($is_gestionnaire != '') {
                echo "<li><b>".get_vocab("gestionnaire des resources suivantes")."</b><br />";
                echo $is_gestionnaire;
                echo "</li>";
            }
            if ($is_mail != '') {
                echo "<li><b>".get_vocab("est prevenu par mail")."</b><br />";
                echo $is_mail;
                echo "</li>";
            }
            echo "</ul>";
        }
    }
  }
  if ($a_privileges == 'n') {
      if ($user_statut == 'administrateur')
          echo  "<div>".get_vocab("administrateur general").".</div>";
      else
          echo "<div>".get_vocab("pas de privileges").".</div>";
  }
}

echo "</body></html>";
?>