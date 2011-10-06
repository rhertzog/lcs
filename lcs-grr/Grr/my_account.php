<?php
/**
 * my_account.php
 * Interface permettant � l'utilisateur de g�rer son compte dans l'application GRR
 * Derni�re modification : $Date: 2009-06-04 15:30:17 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @author    Marc-Henri PAMISEUX <marcori@users.sourceforge.net>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @copyright Copyright 2008 Marc-Henri PAMISEUX
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: my_account.php,v 1.11 2009-06-04 15:30:17 grr Exp $
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
 * $Log: my_account.php,v $
 * Revision 1.11  2009-06-04 15:30:17  grr
 * *** empty log message ***
 *
 * Revision 1.10  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.9  2009-04-09 14:52:31  grr
 * *** empty log message ***
 *
 * Revision 1.8  2009-02-27 22:05:03  grr
 * *** empty log message ***
 *
 * Revision 1.7  2009-01-20 07:19:17  grr
 * *** empty log message ***
 *
 * Revision 1.6  2008-11-16 22:00:59  grr
 * *** empty log message ***
 *
 * Revision 1.5  2008-11-11 22:01:14  grr
 * *** empty log message ***
 *
 * Revision 1.4  2008-11-10 08:17:34  grr
 * *** empty log message ***
 *
 * Revision 1.3  2008-11-07 21:39:40  grr
 * *** empty log message ***
 *
 *
 */

/**
 * Chargement des fichiers de configuration et de d�finition des variables
 */
 include_once('include/connect.inc.php');
 include_once('include/config.inc.php');
 include_once('include/misc.inc.php');
/**
 * Chargement du fichier de d�finitions des fonctions standards
 */
 include_once('include/functions.inc.php');
/**
 * Chargement du fichier de d�finitions des fonctions de base de donn�es
 */
 require_once('include/'.$dbsys.'.inc.php');
/**
 * Chargement du fichier de d�finitions des fonctions de Session
 */
 require_once('include/session.inc.php');
/**
 * Chargement du fichier de param�trage du langage
 */
 include_once('include/language.inc.php');
/**
 * Chargement de la biblioth�ques des fonctions Settings
 */
 include_once('include/settings.inc.php');

 $grr_script_name = 'my_account.php';

 // Chargement des valeurs de la table settings
 if (!loadSettings())
   die('Erreur chargement settings');

 // On d�sactive la fonction VerifNomPrenomUser
 $desactive_VerifNomPrenomUser='y';

 if (!grr_resumeSession())
 {
   header('Location: logout.php?auto=1&url=$url');
   die();
 };
 // Construction des identifiants de la ressource $room, du domaine $area, du site $id_site
 Definition_ressource_domaine_site();
 // Si nous ne savons pas la date, nous devons la cr�er
 $day = isset($_POST['day']) ? $_POST['day'] : (isset($_GET['day']) ? $_GET['day'] : date('d'));
 $month = isset($_POST['month']) ? $_POST['month'] : (isset($_GET['month']) ? $_GET['month'] : date('m'));
 $year = isset($_POST['year']) ? $_POST['year'] : (isset($_GET['year']) ? $_GET['year'] : date('Y'));

 // Param�tres langage
 include_once('include/language.inc.php');

 $back = '';
 if (isset($_SERVER['HTTP_REFERER']))
   $back = htmlspecialchars($_SERVER['HTTP_REFERER']);
/*
 if (!(IsAllowedToModifyMdp()) and !(IsAllowedToModifyProfil()) and !(IsAllowedToModifyEmail()))
 {
   if (authGetUserLevel(getUserName(),-1,'room') < min(getSettingValue('allow_users_modify_mdp'),getSettingValue('allow_users_modify_profil'),getSettingValue('allow_users_modify_email')))
   {
     showAccessDenied($day, $month, $year, $area,$back);
     exit();
   }
 }
*/
 $user_login = isset($_POST['user_login']) ? $_POST['user_login'] : ($user_login = isset($_GET['user_login']) ? $_GET['user_login'] : NULL);
 $valid = isset($_POST['valid']) ? $_POST['valid'] : NULL;
 $msg='';

 // Si valid='yes', la page a �t� invoqu�e pour validation de son formulaire
 if ($valid == 'yes')
 {
  if (IsAllowedToModifyMdp()) {
   $reg_password_a = isset($_POST['reg_password_a']) ? $_POST['reg_password_a'] : NULL;
   $reg_password1 = isset($_POST['reg_password1']) ? $_POST['reg_password1'] : NULL;
   $reg_password2 = isset($_POST['reg_password2']) ? $_POST['reg_password2'] : NULL;
   if ($reg_password_a != '')
   {
     $reg_password_a_c = md5($reg_password_a);
     if ($_SESSION['password'] == $reg_password_a_c)
     {
       if ($reg_password1 != $reg_password2)
         $msg = get_vocab('wrong_pwd2');
       else
       {
         // Restriction dans le cas d'une d�mo
         VerifyModeDemo();

         $reg_password1 = md5($reg_password1);
         $sql = "UPDATE ".TABLE_PREFIX."_utilisateurs
                 SET password='".protect_data_sql($reg_password1)."'
                 WHERE login='".getUserName()."'";
         if (grr_sql_command($sql) < 0)
           fatal_error(0, get_vocab('update_pwd_failed') . grr_sql_error());
         else
         {
           $msg = get_vocab('update_pwd_succeed');
           $_SESSION['password'] = $reg_password1;
         }
       }
     }
     else
       $msg = get_vocab('wrong_old_pwd');
   }
  }
   $sql = "SELECT email,source,nom,prenom
           FROM ".TABLE_PREFIX."_utilisateurs
           WHERE login='".getUserName()."'";
   $res = grr_sql_query($sql);
   if ($res)
   {
     for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
     {
       $user_email = $row[0];
       $user_source = $row[1];
       $user_nom = $row[2];
       $user_prenom = $row[3];
     }
   }
   $reg_email = isset($_POST['reg_email']) ? $_POST['reg_email'] : $user_email;
   $reg_nom = isset($_POST['reg_nom']) ? $_POST['reg_nom'] : $user_nom;
   $reg_prenom = isset($_POST['reg_prenom']) ? $_POST['reg_prenom'] : $user_prenom;
   $champ_manquant= 'n';

   if (trim($reg_nom) == '')
     $champ_manquant= 'y';
   if (trim($reg_prenom) == '')
     $champ_manquant= 'y';

   if (($user_email != $reg_email) or ($user_nom != $reg_nom) or ($user_prenom != $reg_prenom))
   {

    $sql = "UPDATE ".TABLE_PREFIX."_utilisateurs SET ";
    $flag_virgule = 'n';

     if (IsAllowedToModifyProfil()) {
      if (trim($reg_nom) != '')
      {
       $sql.="nom = '" . protect_data_sql($reg_nom)."'";
       $flag_virgule = 'y';
       $_SESSION['nom'] = htmlspecialchars($reg_nom);
      }
      if (trim($reg_prenom) != '')
      {
       if ($flag_virgule == 'y') $sql .=",";
       $sql .= "prenom = '" . protect_data_sql($reg_prenom)."'";
       $flag_virgule = 'y';
       $_SESSION['prenom'] = htmlspecialchars($reg_prenom);
      }
     }


     if (IsAllowedToModifyEmail()) {
      if ($flag_virgule == 'y') $sql .=",";
      $sql.="email = '" . protect_data_sql($reg_email)."'";
     }
     $sql.="WHERE login='".getUserName()."'";

    if ((IsAllowedToModifyProfil()) or (IsAllowedToModifyEmail()))
     if (grr_sql_command($sql) < 0)
       fatal_error(0, get_vocab('message_records_error') . grr_sql_error());
     else
       $msg.="\\n".get_vocab('message_records');
   }
   if (IsAllowedToModifyProfil() and ($champ_manquant=='y'))
     $msg .= "\\n".get_vocab('required');
 }

 // Si valid='yes', la page a �t� invoqu�e pour validation de son formulaire
 // Si valid='reset' la page a �t� invoqu� pour remettre � blanc les champs du formulaire
 // Mais dans les 2 cas, il faut valider les donn�es
 if (($valid == 'yes') or ($valid=='reset'))
 {
   $default_site = isset($_POST['id_site']) ? $_POST['id_site'] : NULL;
   $default_area = isset($_POST['id_area']) ? $_POST['id_area'] : NULL;
   $default_room = isset($_POST['id_room']) ? $_POST['id_room'] : NULL;
   $default_style = isset($_POST['default_css']) ? $_POST['default_css'] : NULL;
   $default_list_type = isset($_POST['area_list_format']) ? $_POST['area_list_format'] : NULL;
   $default_language = isset($_POST['default_language']) ? $_POST['default_language'] : NULL;
   $sql = "UPDATE ".TABLE_PREFIX."_utilisateurs
           SET default_site = '".protect_data_sql($default_site)."',
               default_area = '".protect_data_sql($default_area)."',
               default_room = '".protect_data_sql($default_room)."',
               default_style = '". protect_data_sql($default_style)."',
               default_list_type = '".protect_data_sql($default_list_type)."',
               default_language = '".protect_data_sql($default_language)."'
           WHERE login='".getUserName()."'";

   if (grr_sql_command($sql) < 0)
     fatal_error(0, get_vocab('message_records_error').grr_sql_error());
   else
   {
     if (($default_site !='') and ($default_site !='0'))
       $_SESSION['default_site'] = $default_site;
     else
       $_SESSION['default_site'] = getSettingValue('default_site');

     if (($default_area !='') and ($default_area !='0'))
       $_SESSION['default_area'] = $default_area;
     else
       $_SESSION['default_area'] = getSettingValue('default_area');

     if (($default_room !='') and ($default_room !='0'))
       $_SESSION['default_room'] = $default_room;
     else
       $_SESSION['default_room'] = getSettingValue('default_room');

     if ($default_style !='')
       $_SESSION['default_style'] = $default_style;
     else
       $_SESSION['default_style'] = getSettingValue('default_css');

     if ($default_list_type !='')
       $_SESSION['default_list_type'] = $default_list_type;
     else
       $_SESSION['default_list_type'] = getSettingValue('area_list_format');

     if ($default_language !='')
       $_SESSION['default_language'] = $default_language;
     else
       $_SESSION['default_language'] = getSettingValue('default_language');
   }
 }
 // Utilisation de la biblioth�qye prototype dans ce script
 $use_prototype = 'y';

 print_header($day, $month, $year, isset($area) ? $area : '');

 echo("\n    <!-- Repere ".$grr_script_name." -->\n");

 if (getSettingValue("module_multisite") == "Oui")
   $use_site='y';
 else
   $use_site='n';

 // On appelle les informations de l'utilisateur pour les afficher
 $sql="SELECT nom,prenom,statut,email,default_site,default_area,default_room,default_style,default_list_type,default_language,source
       FROM ".TABLE_PREFIX."_utilisateurs
       WHERE login='".getUserName()."'";
 $res = grr_sql_query($sql);
 if ($res)
 {
   for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
   {
     $user_nom = $row[0];
     $user_prenom = $row[1];
     $user_statut = $row[2];
     $user_email = $row[3];

     if (($row[4] !='') and ($row[4] !='0'))
       $default_site = $row[4];
     else
       $default_site = getSettingValue('default_site');

     if (($row[5] !='') and ($row[5] !='0'))
       $default_area = $row[5];
     else
       $default_area = getSettingValue('default_area');

     if (($row[6] !='') and ($row[6] !='0'))
       $default_room = $row[6];
     else
       $default_room = getSettingValue('default_room');

     if ($row[7] !='')
       $default_css = $row[7];
     else
       $default_css = getSettingValue('default_css');

     if ($row[8] !='')
       $default_list_type = $row[8];
     else
       $default_list_type = getSettingValue('area_list_format');

     if ($row[9] !='')
       $default_language = $row[9];
     else
       $default_language = getSettingValue('default_language');

     $user_source = $row[10];
   }
 }
 ?>
 <script type="text/javascript" >
 function modifier_liste_domaines(){
    new Ajax.Updater($('div_liste_domaines'),"my_account_modif_listes.php",{method: 'get', parameters: $('id_site').serialize(true)+'&'+'default_area=<?php echo $default_area; ?>'+'&'+'session_login=<?php echo getUserName(); ?>'+'&'+'use_site=<?php echo $use_site; ?>'+'&'+'type=domaine'});
 }
 function modifier_liste_ressources(action){
     new Ajax.Updater($('div_liste_ressources'),"my_account_modif_listes.php",{method: 'get', parameters: $('id_area').serialize(true)+'&'+'default_room=<?php echo $default_room; ?>'+'&'+'type=ressource'+'&'+'action='+action});
 }
 </script>
 <?php


 // Affichage d'un pop-up
 affiche_pop_up($msg,'admin');

 echo('
    <form id="param_account" action="my_account.php" method="post">
      <table>');

 // Pas de modif possible
 if (!(IsAllowedToModifyProfil()))
 {
   echo('
        <tr>
          <td><b>'.get_vocab('login').get_vocab('deux_points').'</b></td>
          <td>'.getUserName().'</td>
        </tr>');
   echo('
        <tr>
          <td><b>'.get_vocab('last_name').get_vocab('deux_points').'</b></td>
          <td>'.$user_nom.'</td>
        </tr>');
   echo('
        <tr>
          <td><b>'.get_vocab('first_name').get_vocab('deux_points').'</b></td>
          <td>'.$user_prenom.'</td>
        </tr>');
 } else {
  /**
   * Modifs possibles
   */

// Modifs possibles
   // login
   echo('
        <tr>
          <td><b>'.get_vocab('login').get_vocab('deux_points').'</b></td>');
   echo('
          <td>'.getUserName().'</td>
        </tr>');

   // Nom
   echo('
        <tr>
          <td><b>'.get_vocab('last_name').get_vocab('deux_points').'</b>*</td>');
   echo('
          <td><input type="text" name="reg_nom" value="');
   if ($user_nom)
     echo htmlspecialchars($user_nom);
   echo('" size="30" /></td>
        </tr>');

   // Pr�nom
   echo('
        <tr>
          <td><b>'.get_vocab('first_name').get_vocab('deux_points').'</b>*</td>
          <td><input type="text" name="reg_prenom" value="');
   if ($user_prenom)
     echo htmlspecialchars($user_prenom);
   echo('" size="30" /></td>
        </tr>');
 }

// Pas de modif possible
 if (!(IsAllowedToModifyEmail()))
 {
   echo('
        <tr>
          <td><b>'.get_vocab('mail_user').get_vocab('deux_points').'</b></td>
          <td>'.$user_email.'</td>
        </tr>');
 } else {
   // Email
   echo('
        <tr>
          <td><b>'.get_vocab('mail_user').get_vocab('deux_points').'</b></td>
          <td><input type="text" name="reg_email" value="');
   if ($user_email)
     echo htmlspecialchars($user_email);
   echo('" size="30" /></td>
        </tr>');
 }

 if ($user_statut == "utilisateur")
   $text_user_statut = get_vocab("statut_user");
 else if ($user_statut == "visiteur")
   $text_user_statut = get_vocab("statut_visitor");
 else if ($user_statut == "gestionnaire_utilisateur")
   $text_user_statut = get_vocab("statut_user_administrator");
 else if ($user_statut == "administrateur")
   $text_user_statut = get_vocab("statut_administrator");
 else
   $text_user_statut = $user_statut;

 echo('
        <tr>
          <td><b>'.get_vocab('statut').get_vocab('deux_points').'</b></td>
          <td>'.$text_user_statut.'</td>
        </tr>
      </table>');

 if (IsAllowedToModifyProfil())
 {
   echo('<p>('.get_vocab('required').')</p>');
   if ((trim($user_nom) == "") or (trim($user_prenom) == ''))
     echo("\n".'      <h2 class="avertissement">'.get_vocab('nom_prenom_valides').'</h2>');
 }

 if (IsAllowedToModifyMdp())
 {
   echo('
      <div><br />
      <br />
      <table border="0" width="100%">
        <tr>
          <td onclick="clicMenu(\'1\')" class="fontcolor4" style="cursor: inherit" align="center">
            <span class="bground">
              <b><a href="#">'.get_vocab('click_here_to_modify_pwd').'</a></b>
            </span>
          </td>
        </tr>
        <tr style="display:none" id="menu1">
          <td>
            <br />
            <p>'.get_vocab('pwd_msg_warning').'</p>'.get_vocab('old_pwd').get_vocab('deux_points').'
            <input type="password" name="reg_password_a" size="20" />
            <br />'.get_vocab('new_pwd1').get_vocab('deux_points').'
            <input type="password" name="reg_password1" size="20" />
            <br />'.get_vocab('new_pwd1').get_vocab('deux_points').'
            <input type="password" name="reg_password2" size="20" />
          </td>
        </tr>
      </table>
      <br /></div>
      <hr />');
 }
 // Configuration de l'affichage par defaut
 echo("\n".'      <h3>'.get_vocab('default_parameter_values_title').'</h3>');

/**
 * Choix du type d'affichage
 */
 echo("\n".'      <h4>'.get_vocab('explain_area_list_format').'</h4>');
 echo('
      <table>
        <tr>
          <td>'.get_vocab('liste_area_list_format').'</td>
          <td>
            <input type="radio" name="area_list_format" value="list" ');
 if ($default_list_type =='list')
   echo('checked="checked"');
 echo(' />');
 echo('
          </td>
        </tr>
        <tr>
          <td>'.get_vocab('select_area_list_format').'</td>
          <td>
            <input type="radio" name="area_list_format" value="select" ');
 if ($default_list_type =='select')
   echo('checked="checked" ');
 echo(' />');
 echo('
          </td>
        </tr>
      </table>');

if (getSettingValue("module_multisite") == "Oui")
  echo ('
      <h4>'.get_vocab('explain_default_area_and_room_and_site').'</h4>');
else
  echo ('
      <h4>'.get_vocab('explain_default_area_and_room').'</h4>');


/**
 * Liste des sites
 */
 if (getSettingValue("module_multisite") == "Oui")
 {
   $sql = "SELECT id,sitecode,sitename
           FROM ".TABLE_PREFIX."_site
           ORDER BY id ASC";
   $resultat = grr_sql_query($sql);
   echo('
      <table>
        <tr>
          <td>'.get_vocab('default_site').get_vocab('deux_points').'</td>
          <td>
            <select id="id_site" name="id_site" onchange="modifier_liste_domaines();modifier_liste_ressources(2)">
              <option value="-1">'.get_vocab('choose_a_site').'</option>'."\n");
  for ($enr = 0; ($row = grr_sql_row($resultat, $enr)); $enr++)
  {
      echo '              <option value="'.$row[0].'"';
      if ($default_site == $row[0])
        echo ' selected="selected" ';
      echo '>'.htmlspecialchars($row[2]);
      echo '</option>'."\n";

  }
  echo('            </select>
          </td>
        </tr>');
} else {
 echo '<input type="hidden" id="id_site" name="id_site" value="-1" />
       <table>';
}

/**
  * Liste des domaines
 */
echo '<tr><td colspan="2">';
echo '<div id="div_liste_domaines">';
// Ici, on ins�re la liste des domaines avec de l'ajax !
echo '</div></td></tr>';

/**
 * Liste des ressources
 */
echo '<tr><td colspan="2">';
echo '<div id="div_liste_ressources">';
echo '<input type="hidden" id="id_area" name="id_area" value="'.$default_area.'" />';
// Ici, on ins�re la liste des ressouces avec de l'ajax !
echo '</div></td></tr></table>';

// Au chargement de la page, on remplit les listes de domaine et de ressources
echo '<script type="text/javascript">modifier_liste_domaines();</script>'."\n";
echo '<script type="text/javascript">modifier_liste_ressources(1);</script>'."\n";


/**
 * Choix de la feuille de style part d�faut
 */
echo '      <h4>'.get_vocab('explain_css').'</h4>';
echo '
      <table>
        <tr>
          <td>'.get_vocab('choose_css').'</td>
          <td>
            <select name="default_css">'."\n";
$i=0;
while ($i < count($liste_themes))
{
  echo '              <option value="'.$liste_themes[$i].'"';
  if ($default_css == $liste_themes[$i])
    echo ' selected="selected"';
  echo ' >'.encode_message_utf8($liste_name_themes[$i]).'</option>'."\n";
  $i++;
}
echo '            </select>
          </td>
        </tr>
      </table>'."\n";

/**
 * Choix de la langue
 */
echo '      <h4>'.get_vocab('choose_language').'</h4>';
echo '
      <table>
        <tr>
          <td>'.get_vocab('choose_css').'</td>
          <td>
            <select name="default_language">'."\n";
$i=0;
while ($i < count($liste_language))
{
   echo '              <option value="'.$liste_language[$i].'"';
   if ($default_language == $liste_language[$i])
     echo ' selected="selected"';
   echo ' >'.encode_message_utf8($liste_name_language[$i]).'</option>'."\n";
   $i++;
}
echo('            </select>
          </td>
        </tr>
      </table>
      <div>
      <input type="hidden" name="valid" value="yes" />
      <input type="hidden" name="day" value="'.$day.'" />
      <input type="hidden" name="month" value="'.$month.'" />
      <input type="hidden" name="year" value="'.$year.'" />
      <br />
      <input type="submit" value="'.get_vocab('save').'" />
      </div>
    </form>
    <!-- Formulaire de Reset des donn�es -->
    <form id="reset" action="my_account.php" method="post">
      <div>
      <input type="hidden" name="valid" value="reset" />
      <input type="hidden" name="day" value="'.$day.'" />
      <input type="hidden" name="month" value="'.$month.'" />
      <input type="hidden" name="year" value="'.$year.'" />
      <input type="hidden" name="id_site" value="-1" />
      <input type="hidden" name="id_area" value="-1" />
      <input type="hidden" name="id_room" value="-1" />
      <input type="hidden" name="default_css" value="" />
      <input type="hidden" name="area_list_format" value="" />
      <input type="hidden" name="default_language" value="" />
      <input type="submit" value="'.get_vocab('reset').'" />
      </div>
    </form>
  </body>
</html>');