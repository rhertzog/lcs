<?php
/**
 * index.php
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2010-04-07 15:38:14 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @author    Marc-Henri PAMISEUX <marcori@users.sourceforge.net>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @copyright Copyright 2008 Marc-Henri PAMISEUX
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   admin
 * @version   $Id: index.php,v 1.10 2010-04-07 15:38:14 grr Exp $
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
 * $Log: index.php,v $
 * Revision 1.10  2010-04-07 15:38:14  grr
 * *** empty log message ***
 *
 * Revision 1.9  2009-12-16 14:52:31  grr
 * *** empty log message ***
 *
 * Revision 1.8  2009-12-02 20:11:07  grr
 * *** empty log message ***
 *
 * Revision 1.7  2009-10-09 07:55:48  grr
 * *** empty log message ***
 *
 * Revision 1.5  2009-06-04 15:30:17  grr
 * *** empty log message ***
 *
 * Revision 1.4  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.3  2008-11-11 22:01:14  grr
 * *** empty log message ***
 *
 *
 */


/* Pour ce script, on cherche � afficher toutes les erreurs PHP
(sauf dans le cas d'un serveur LCS car sinon, des erreurs dues � des scripts LCS apparaissent.
*/
if (!@file_exists("/var/www/lcs/includes/headerauth.inc.php"))
    error_reporting (E_ALL);
require_once("include/config.inc.php");
if (file_exists("include/connect.inc.php"))
   include "include/connect.inc.php";
require_once("include/misc.inc.php");
require_once("include/functions.inc.php");
require_once("include/settings.inc.php");
// Param�tres langage
include "include/language.inc.php";
// Dans le cas d'une base mysql, on teste la bonne installation de la base et on propose une installation automatis�e.
if ($dbsys == "mysql")
{
  $flag='';
  $correct_install = '';
  $msg='';
  if (@file_exists("include/connect.inc.php"))
    {
      require_once("include/connect.inc.php");
      if (@mysql_connect("$dbHost", "$dbUser", "$dbPass"))
    {
      if (@mysql_select_db("$dbDb"))
        {
          // Premier test
          $j = '0';
          while ($j < count($liste_tables)) {
            $test = mysql_query("select count(*) from ".$table_prefix.$liste_tables[$j]);
            if (!$test) {
                $flag = 'yes';
            }
            $j++;
          }
          if ($flag == 'yes') {
             $msg = "<p>La connection au serveur $dbsys est �tablie mais certaines tables sont absentes de la base $dbDb.</p>";
             $correct_install = 'no';
          }
          /*
          // Premier test (test remplac� par le pr�c�dent car il semblerait que sur certaines installation, l'utilisateur de la base n'avait pas le droit d'�x�cuter "show tables" !)
          $liste2 = array();
          $tableNames = mysql_query("SHOW TABLES FROM ".$dbDb);
          if ($tableNames) {
             $j = '0';
             while ($j < mysql_num_rows($tableNames)) {
                $liste2[$j] = mysql_tablename($tableNames, $j);
                $j++;
             }
             $j = '0';
             while ($j < count($liste_tables)) {
                $temp = $table_prefix.$liste_tables[$j];
                if (!(in_array($temp, $liste2))) {
                   $correct_install='no';
                   $flag = 'yes';
                }
                $j++;
             }
             if ($flag == 'yes') {
                $msg = "<p>La connection au serveur $dbsys est �tablie mais certaines tables sont absentes de la base $dbDb.</p>";
                $correct_install = 'no';
             }
          }
          */
        }
      else
        {
          $msg = "La connection au serveur $dbsys est �tablie mais impossible de s�lectionner la base contenant les tables GRR.";
          $correct_install = 'no';
            }
        }
      else
    {
      $msg = "Erreur de connection au serveur $dbsys. Le fichier \"connect.inc.php\" ne contient peut-�tre pas les bonnes informations de connection.";
      $correct_install = 'no';
        }
    }
  else
    {
      $msg = "Le fichier \"connect.inc.php\" contenant les informations de connection est introuvable.";
      $correct_install = 'no';
    }
  if ($correct_install=='no')
    {
      echo begin_page("GRR (Gestion et R�servation de Ressources) ");
      echo "<h1 class=\"center\">Gestion et R�servation de Ressources</h1>\n";
      echo "<div style=\"text-align:center;\"><span style=\"color:red;font-weight:bold\">".$msg."</span>\n";
      echo "<ul><li>Soit vous proc�dez � une mise � jour vers une nouvelle version de GRR. Dans ce cas, vous devez proc�der � une mise � jour de la base de donn�es MySql.<br />";
      echo "<b><a href='./admin_maj.php'>Mettre � jour la base Mysql</a></b><br /></li>";
      echo "<li>Soit l'installation de GRR n'est peut-�tre pas termin�e. Vous pouvez proc�der � une installation/r�installation de la base.<br />";
        echo "<a href='install_mysql.php'>Installer la base $dbsys</a></li></ul></div>";
        ?>
        </body>
    </html>
        <?php
        die();
    }
}
require_once("include/$dbsys.inc.php");
require_once("./include/session.inc.php");
// Settings
require_once("./include/settings.inc.php");
//Chargement des valeurs de la table settingS

if (!loadSettings())
{
  die("Erreur chargement settings");
}
$cook = session_get_cookie_params();

// Cas d'une authentification CAS
if ((getSettingValue('sso_statut') == 'cas_visiteur') or (getSettingValue('sso_statut') == 'cas_utilisateur'))
{
  require_once("./include/cas.inc.php");
  // A ce stade, l'utilisateur est authentifi� par CAS
  $password = '';
  $user_ext_authentifie = 'cas';
  if (!isset($user_nom)) $user_nom='';
  $cas_tab_login["user_nom"] = $user_nom;
  if (!isset($user_prenom)) $user_prenom='';
  $cas_tab_login["user_prenom"] = $user_prenom;
  if (!isset($user_mail)) $user_mail='';
  $cas_tab_login["user_email"] = $user_mail;
  if (!isset($user_code_fonction)) $user_code_fonction='';
  $cas_tab_login["user_code_fonction"] = $user_code_fonction;
  if (!isset($user_libelle_fonction)) $user_libelle_fonction='';
  $cas_tab_login["user_libelle_fonction"] = $user_libelle_fonction;
  if (!isset($user_language)) $user_language='';
  $cas_tab_login["user_language"] = $user_language;
  if (!isset($user_default_style)) $user_default_style='';
  $cas_tab_login["user_default_style"] = $user_default_style;

  $result = grr_opensession($login,$password,$user_ext_authentifie,$cas_tab_login) ;
  // On �crit les donn�es de session et ferme la session
  session_write_close();
  $message = '';
  if ($result=="2")
    {
      $message = get_vocab("echec_connexion_GRR");
      $message .= " ".get_vocab("wrong_pwd");
    }
  else if ($result == "3")
    {
      $message = get_vocab("echec_connexion_GRR");
      $message .= "<br />". get_vocab("importation_impossible");
    }
  else if ($result == "4")
    {
      $message = get_vocab("echec_connexion_GRR");
      $message .= " ".get_vocab("causes_possibles");
      $message .= "<br />- ".get_vocab("wrong_pwd");
      $message .= "<br />- ". get_vocab("echec_authentification_ldap");
  }
  else if ($result != "1")
  {
      $message = get_vocab("echec_connexion_GRR");
      $message .= "<br />Cause inconnue.";
  }

  if ($message != '')
    {
      echo $message;
      die();
    }


  if (grr_resumeSession() )
  {
   header("Location: ".htmlspecialchars_decode(page_accueil())."");
  }

// Cas d'une authentification Lemonldap
}
else if ((getSettingValue('sso_statut') == 'lemon_visiteur') or (getSettingValue('sso_statut') == 'lemon_utilisateur'))
{
  if (isset($_GET['login'])) $login = $_GET['login']; else $login = "";
  if (isset($_COOKIE['user'])) $cookie_user=$_COOKIE['user']; else $cookie_user="";
  if(empty($cookie_user) or $cookie_user != $login)
    {
      if ((getSettingValue("Url_cacher_page_login")!="") and ((!isset($sso_super_admin)) or ($sso_super_admin==false))) {
          header("Location: ".getSettingValue("Url_cacher_page_login"));
      } else
          header("Location: ".htmlspecialchars_decode(page_accueil())."");
          //header("Location: ./login.php");
      // Echec de l'authentification lemonldap
      die();
      echo "</body></html>";
    }
  // A ce stade, l'utilisateur est authentifi� par Lemonldap
  $user_ext_authentifie = 'lemon';
  $password = '';
  $result = grr_opensession($login,$password,$user_ext_authentifie) ;
  // On �crit les donn�es de session et ferme la session
  session_write_close();
  $message = '';
  if ($result=="2")
    {
      $message = get_vocab("echec_connexion_GRR");
      $message .= " ".get_vocab("wrong_pwd");
    }
  else if ($result == "3")
    {
      $message = get_vocab("echec_connexion_GRR");
      $message .= "<br />". get_vocab("importation_impossible");
    }
  else if ($result != "1")
    {
      $message = get_vocab("echec_connexion_GRR");
      $message .= "<br />Cause inconnue.";
    }

  if ($message != '')
    {
      echo $message;
      die();
    }


  if (grr_resumeSession() )
  {
   header("Location: ".htmlspecialchars_decode(page_accueil())."");
  }
// Cas d'une authentification LCS
}
else if (getSettingValue('sso_statut') == 'lcs')
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

      if (is_eleve($login))
         $user_ext_authentifie = 'lcs_eleve';
      else
         $user_ext_authentifie = 'lcs_non_eleve';
      $password = '';
      $result = grr_opensession($login,$password,$user_ext_authentifie,$lcs_tab_login,$lcs_groups) ;
      // On �crit les donn�es de session et ferme la session
      session_write_close();
      $message = '';
      if ($result=="2") {
          $message = get_vocab("echec_connexion_GRR");
          $message .= " ".get_vocab("wrong_pwd");
      } else if ($result == "3") {
           $message = get_vocab("echec_connexion_GRR");
           $message .= "<br />". get_vocab("importation_impossible");
      } else if ($result == "4") {
          $message = get_vocab("echec_connexion_GRR");
          $message .= " ".get_vocab("causes_possibles");
          $message .= "<br />- ".get_vocab("wrong_pwd");
         $message .= "<br />- ". get_vocab("echec_authentification_ldap");
      }
        else if ($result == "5")
      {
        $message = get_vocab("echec_connexion_GRR");
        $message .= "<br />". get_vocab("connexion_a_grr_non_autorisee");
      }

      if ($message != '') {
          fatal_error(1, $message);
          die();
      }
      if (grr_resumeSession() ) {
           header("Location: ".htmlspecialchars_decode(page_accueil())."");
      }
  } else {
    // L'utilisateur n'a pas �t� identifi�'
      if (getSettingValue("authentification_obli")==1) { // authentification obligatoire, l'utilisateur est renvoy� vers une page de connexion
         require_once("include/session.inc.php");
         grr_closeSession($_GET['auto']);
         header("Location:".LCS_PAGE_AUTHENTIF);
      } else {
         header("Location: ".htmlspecialchars_decode(page_accueil()).""); // authentification non obligatoire, l'utilisateur est simple visiteur
      }
   }
}
// Cas d'une authentification Lasso
if ((getSettingValue('sso_statut') == 'lasso_visiteur') or (getSettingValue('sso_statut') == 'lasso_utilisateur'))
{
  require_once(SPKITLASSO.'/lassospkit_public_api.inc.php');
  if (lassospkit_nameid() == NULL)
    {
      // S'il y a eu une erreur et que l'on revient, afficher
      // l'erreur. Cela annule la redirection de header(), mais
      // l'utilisateur pourra quand m�me cliquer manuellement sur un
      // lien.
      $error = lassospkit_error();
      if (!empty($error))
	{
	  echo "SSO error:<br /><pre>$error</pre><br />";
	}

      // Pas encore authentifi� - on se connecte:
      $return_url = get_request_uri();
      lassospkit_redirect_federate($return_url);
      exit();
    }

  // A ce stade, l'utilisateur est authentifi� par Lasso
  $password = '';
  $login = lassospkit_userid(); // vide si pas encore f�d�r�

  if (empty($login))
    {
      // Construit un identifiant unique
      $sql = "SELECT login FROM ".TABLE_PREFIX."_utilisateurs
			WHERE login LIKE 'lasso_%'";
      $res = grr_sql_query($sql);
      $existing_users = array();
      for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
	$existing_users[] = $row[0];
      $max = 0;
      foreach ($existing_users as $user)
	{
	  if (preg_match('/lasso_(\d+)/', $user, $matches))
	    if ($matches[1] > $max)
	      $max = $matches[1];
	}
      $login = 'lasso_'.($max+1);

      // Stockage de la d�f�ration
      lassospkit_set_userid($login);
    }

  $user_ext_authentifie = 'lasso';
  $tab_login["fullname"] = "Anne";
  $tab_login["nom"] = "Nonyme";
  $tab_login["email"] = "";

  // S'il y a des attributs suppl�mentaires, on les utilise
  $attributes = lassospkit_get_assertion_attributes();
  if ($attributes) {
    // Get infos from the Identity Provider
    $user_infos = array();
    // Nom Pr�nom
    list($tab_login['nom'], $tab_login['fullname']) = split(' ', $attributes['cn'][0]);
    $tab_login['email'] = $attributes['mail'][0];
    // Pour l'instant on ne red�finit pas le login
    //$tab_login['???'] = $attributes['username'][0];
  }

  $result = grr_opensession($login,$password,$user_ext_authentifie, $tab_login);

  // Stocker le nameid dans la session pour se souvenir que c'est
  // un login lasso
  $_SESSION['lasso_nameid'] = lassospkit_nameid();
  // Ne plus r�utiliser la session spkitlasso courante, pour
  // �viter les probl�mes de nettoyage au logout distant
  lassospkit_set_nameid(null);
  lassospkit_clean();

  // On �crit les donn�es de session et ferme la session
  session_write_close();
  $message = '';
  if ($result=="2")
    {
      $message = get_vocab("echec_connexion_GRR");
      $message .= " ".get_vocab("wrong_pwd");
    }
  else if ($result == "3")
    {
      // L'utilisateur existe d�j�
      $message = get_vocab("echec_connexion_GRR");
      $message .= "<br />". get_vocab("importation_impossible");
    }
  else if ($result == "4")
    {
      $message = get_vocab("echec_connexion_GRR");
      $message .= " ".get_vocab("causes_possibles");
      $message .= "<br />- ".get_vocab("wrong_pwd");
      $message .= "<br />- ". get_vocab("echec_authentification_ldap");
  }
  else if ($result != "1")
  {
      $message = get_vocab("echec_connexion_GRR");
      $message .= "<br />Cause inconnue.";
  }

  if ($message != '')
    {
      echo $message;
      die();
    }


  if (grr_resumeSession() )
  {
   header("Location: ".htmlspecialchars_decode(page_accueil())."");
  }
// Cas d'une authentification apache
}
else if ((getSettingValue('sso_statut') == 'http_visiteur') or (getSettingValue('sso_statut') == 'http_utilisateur'))
{
    // Nous utilisons les fonction d'authentification par PHP (plut�t que par Apache) � l'aide des lignes :
    // header('WWW-Authenticate: Basic realm="..."'); et header('HTTP/1.0 401 Unauthorized');
    // Mais ces fonctions ne sont disponibles que si PHP est ex�cut� comme module Apache,
    // et non pas sous la forme d'un CGI.
    // Si PHP est en mode cgi il faut utiliser une r�ecriture de l'url vie le module rewrite de apache :
    // Vous devez cr�er un fichier .htaccess ayant comme contenu
    //  <IfModule mod_rewrite.c>
    //  RewriteEngine on
    //  RewriteRule .* - [E=REMOTE_USER:%,L]
    //  </IfModule>
    // Cela permet de r�cup�rer dans $_SERVER['REMOTE_USER'] le login et le mot de passe
    // mais crypt� :
    // on obtient le login et le password sous la forme : user:password


    // Cas le plus courant :
    if (isset($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_USER'])) {
        $login = $_SERVER['PHP_AUTH_USER'];
    // Pour les versions plus anciennes de PHP < 4.1.0 (en fait inutile ici car GRR exige PHP > 4.3.1
    } else if (isset($HTTP_SERVER_VARS['PHP_AUTH_USER']) && !empty($HTTP_SERVER_VARS['PHP_AUTH_USER'])) {
        $login = $HTTP_SERVER_VARS['PHP_AUTH_USER'];
    // L'utilisateur est authentifi� mais $_SERVER['PHP_AUTH_USER'] est vide, on tente de r�cup�rer le login dans $_SERVER['REMOTE_USER']
    } else if (isset($_SERVER['REMOTE_USER']) && !empty($_SERVER['REMOTE_USER'])) {
        // Cas ou PHP est en mode cgi
        if (preg_match('/Basic+(.*)$/i', $_SERVER['REMOTE_USER'], $matches) ) {
           // Si PHP est en mode cgi il faut utiliser une r�ecriture de l'url vie le module rewrite de apache :
           // Vous devez cr�er un fichier .htaccess ayant comme contenu
           //  <IfModule mod_rewrite.c>
           //  RewriteEngine on
           //  RewriteRule .* - [E=REMOTE_USER:%,L]
           //  </IfModule>
           // Cela permet de r�cup�rer dans $_SERVER['REMOTE_USER'] le login et le mot de passe
           // mais crypt� :
           // on obtient le login et le password sous la forme : user:password
           $identifiers = base64_decode($matches[1]);
           // on l'exporte dans un tableau
           $identifiers_tab = explode(':', $identifiers);
           // on r�cup�re le tout dans des variables
           $login = strip_tags($identifiers_tab[0]);
           // le mot de passe peut �tre r�cup�r� dans strip_tags($identifiers_tab[1]) mais on n'en a pas besoin ici
        } else {
        // Cas normal
            $login = $_SERVER['REMOTE_USER'];
        }
    // Cas de PHP4 en mode CGI sur IIS
    } else if (isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
        list($login, $pw) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    } else {
        // on demande de s'identifier
        // A ce stade :
        // - soit l'utilisateur ne s'est pas encore identifi�
        // - soit l'utilisateur s'est identifi� mais de fa�on incorrecte
        // - soit l'utilisateur s'est identifi� de fa�on correcte mais l'identifiant n'a pas pu �tre r�cup�r�.
        $my_message = "Module d'authentification de GRR";
        header('WWW-Authenticate: Basic realm="' . $my_message . '"');
        header('HTTP/1.0 401 Unauthorized');
        // en cas d'annulation
        echo begin_page(get_vocab("mrbs"),"no_session");
        echo "<h3>".get_vocab("wrong_pwd")."</h3>";
        echo "<h3>".get_vocab("connexion_a_grr_non_autorisee")."</h3>";
        echo "</body></html>";
        exit();
    }
    // A ce stade, l'utilisateur est authentifi� et $login n'est pas vide via le serveur apache
    $user_ext_authentifie = 'apache';
    $password = '';
    $result = grr_opensession($login,$password,$user_ext_authentifie);
    // On �crit les donn�es de session et ferme la session
    session_write_close();
    $message = '';
    if ($result=="2")
    {
      $message = get_vocab("echec_connexion_GRR");
      $message .= " ".get_vocab("wrong_pwd");
    }
    else if ($result == "3")
    {
      $message = get_vocab("echec_connexion_GRR");
      $message .= "<br />". get_vocab("importation_impossible");
    }
     else if ($result != "1")
    {
      $message = get_vocab("echec_connexion_GRR");
      $message .= "<br />Cause inconnue.";
    }

  if ($message != '')
    {
      echo $message;
      die();
    }

  if (grr_resumeSession() )
  {
   header("Location: ".htmlspecialchars_decode(page_accueil())."");
  }
}
else
{
  if (getSettingValue("authentification_obli")==1)
    {
      if ($cook["path"] != '')
    {
      if (grr_resumeSession())
        {

          header("Location: ".htmlspecialchars_decode(page_accueil())."");
        }
      else
        {
         if ((getSettingValue("Url_cacher_page_login")!="") and ((!isset($sso_super_admin)) or ($sso_super_admin==false))) {
           header("Location: ".getSettingValue("Url_cacher_page_login"));
         } else
           header("Location: ./login.php");
        }
    }
      else
    {
      if ((getSettingValue("Url_cacher_page_login")!="") and ((!isset($sso_super_admin)) or ($sso_super_admin==false))) {
          header("Location: ".getSettingValue("Url_cacher_page_login"));
      } else
          header("Location: ./login.php");

    }
    }
  else
    {
      header("Location: ".htmlspecialchars_decode(page_accueil())."");
    }
}
?>