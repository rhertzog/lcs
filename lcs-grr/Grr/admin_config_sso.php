<?php
/**
 * admin_config_sso.php
 * Interface permettant l'activation de la prise en compte d'un environnement SSO
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2010-04-07 15:38:14 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: admin_config_sso.php,v 1.12 2010-04-07 15:38:14 grr Exp $
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
 * $Log: admin_config_sso.php,v $
 * Revision 1.12  2010-04-07 15:38:14  grr
 * *** empty log message ***
 *
 * Revision 1.11  2009-12-16 14:52:31  grr
 * *** empty log message ***
 *
 * Revision 1.10  2009-12-02 21:37:20  grr
 * *** empty log message ***
 *
 * Revision 1.9  2009-12-02 20:11:07  grr
 * *** empty log message ***
 *
 * Revision 1.8  2009-06-04 15:30:17  grr
 * *** empty log message ***
 *
 * Revision 1.7  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.6  2009-04-09 14:52:31  grr
 * *** empty log message ***
 *
 * Revision 1.5  2009-02-27 22:05:03  grr
 * *** empty log message ***
 *
 * Revision 1.4  2008-11-16 22:00:58  grr
 * *** empty log message ***
 *
 *
 */

include "include/admin.inc.php";
$grr_script_name = "admin_config_sso.php";

$back = '';
if (isset($_SERVER['HTTP_REFERER'])) $back = grr_htmlSpecialChars($_SERVER['HTTP_REFERER']);
if ((isset($sso_restrictions)) and ($sso_restrictions==true)) {
    $day   = date("d");
    $month = date("m");
    $year  = date("Y");
    showAccessDenied($day, $month, $year, '',$back);
    exit();
}

if(authGetUserLevel(getUserName(),-1) < 6)
{
    $day   = date("d");
    $month = date("m");
    $year  = date("Y");
    showAccessDenied($day, $month, $year, '',$back);
    exit();
}

if (isset($_POST['valid'])) {
    VerifyModeDemo();

    if (!isset($_POST['cacher_lien_deconnecter'])) $cacher_lien_deconnecter = "n"; else $cacher_lien_deconnecter = "y";
      if (!saveSetting("cacher_lien_deconnecter", $cacher_lien_deconnecter))
          echo "Erreur lors de l'enregistrement de cacher_lien_deconnecter !<br />";

    if (isset($_POST['Url_portail_sso'])) {
        if (!saveSetting("Url_portail_sso", $_POST['Url_portail_sso'])) {
            echo "Erreur lors de l'enregistrement de Url_portail_sso ! <br />";
        }
    }


    if ($_POST['sso_statut'] == "no_sso") {
        $req = grr_sql_query("delete from ".TABLE_PREFIX."_setting where NAME = 'sso_statut'");
        $grrSettings['sso_statut'] = '';
    } else {
        if (!saveSetting("sso_statut", $_POST['sso_statut'])) {
            echo "Erreur lors de l'enregistrement de sso_statut !<br />";
        }
        $grrSettings['sso_statut'] = $_POST['sso_statut'];

    }
    if (!saveSetting("lcs_statut_prof", $_POST['lcs_statut_prof'])) {
        echo "Erreur lors de l'enregistrement de lcs_statut_prof !<br />";
    }
    $grrSettings['lcs_statut_prof'] = $_POST['lcs_statut_prof'];

    if (!saveSetting("lcs_statut_eleve", $_POST['lcs_statut_eleve'])) {
        echo "Erreur lors de l'enregistrement de lcs_statut_eleve !<br />";
    }
    $grrSettings['lcs_statut_eleve'] = $_POST['lcs_statut_eleve'];
    if (!saveSetting("lcs_liste_groupes_autorises", $_POST['lcs_liste_groupes_autorises'])) {
        echo "Erreur lors de l'enregistrement de lcs_liste_groupes_autorises !<br />";
    }
    $grrSettings['lcs_liste_groupes_autorises'] = $_POST['lcs_liste_groupes_autorises'];

    if (!saveSetting("http_champ_email", $_POST['http_champ_email'])) {
    echo "Erreur lors de l'enregistrement de http_champ_email !<br />";
    }
    $grrSettings['http_champ_email'] = $_POST['http_champ_email'];

    if (!saveSetting("http_champ_nom", $_POST['http_champ_nom'])) {
    echo "Erreur lors de l'enregistrement de http_champ_nom !<br />";
    }
    $grrSettings['http_champ_nom'] = $_POST['http_champ_nom'];

    if (!saveSetting("http_champ_prenom", $_POST['http_champ_prenom'])) {
    echo "Erreur lors de l'enregistrement de http_champ_prenom !<br />";
    }
    $grrSettings['http_champ_prenom'] = $_POST['http_champ_prenom'];

    if ($_POST['http_sso_domain'] == "") {
        $_POST['http_sso_statut_domain']="";
    } else {
        if ((!isset($_POST['http_sso_statut_domain'])) or ($_POST['http_sso_statut_domain'] == ""))
         $_POST['http_sso_statut_domain'] = "visiteur";
    }
    if (!saveSetting("http_sso_domain", $_POST['http_sso_domain'])) {
        echo "Erreur lors de l'enregistrement de http_sso_domain !<br />";
    }
    $grrSettings['http_sso_domain'] = $_POST['http_sso_domain'];

    if (isset($_POST['http_sso_domain'])) {
      if (!saveSetting("http_sso_statut_domain", $_POST['http_sso_statut_domain'])) {
        echo "Erreur lors de l'enregistrement de http_sso_statut_domain !<br />";
      }
      $grrSettings['http_sso_statut_domain'] = $_POST['http_sso_statut_domain'];
    }
    if (isset($_POST['Url_cacher_page_login'])) {
        if (!saveSetting("Url_cacher_page_login", $_POST['Url_cacher_page_login'])) {
            echo "Erreur lors de l'enregistrement de Url_cacher_page_login ! <br />";
        }
    }

    if (!isset($_POST['sso_IsNotAllowedModify'])) $sso_IsNotAllowedModify = "n"; else $sso_IsNotAllowedModify = "y";
      if (!saveSetting("sso_IsNotAllowedModify", $sso_IsNotAllowedModify)) {
          echo "Erreur lors de l'enregistrement de sso_IsNotAllowedModify !<br />";
      }

    if (($_POST['sso_statut'] != "cas_visiteur") and ($_POST['sso_statut'] != "cas_utilisateur"))
        $sso_active_correspondance_profil_statut = "n";
    else
      if (!isset($_POST['sso_active_correspondance_profil_statut'])) $sso_active_correspondance_profil_statut = "n"; else $sso_active_correspondance_profil_statut = "y";
        if (!saveSetting("sso_ac_corr_profil_statut", $sso_active_correspondance_profil_statut)) {
            echo "Erreur lors de l'enregistrement de sso_active_correspondance_profil_statut !<br />";
        }

    if (($_POST['sso_statut'] != "cas_visiteur") and ($_POST['sso_statut'] != "cas_utilisateur"))
        $sso_redirection_accueil_grr = "n";
    else
      if (!isset($_POST['sso_redirection_accueil_grr'])) $sso_redirection_accueil_grr = "n"; else $sso_redirection_accueil_grr = "y";
        if (!saveSetting("sso_redirection_accueil_grr", $sso_redirection_accueil_grr)) {
            echo "Erreur lors de l'enregistrement de sso_redirection_accueil_grr !<br />";
        }

}

if (isset($_SERVER['HTTP_REFERER'])) $back = grr_htmlSpecialChars($_SERVER['HTTP_REFERER']);
if ((authGetUserLevel(getUserName(),-1) < 6) and ($valid != 'yes'))
{
    showAccessDenied($day, $month, $year, '',$back);
    exit();
}

# print the page header
print_header("","","","",$type="with_session", $page="admin");
// Affichage de la colonne de gauche
include "admin_col_gauche.php";

echo "<form action=\"admin_config_sso.php\" method=\"post\">\n";

echo "<h2>".get_vocab("admin_config_sso.php")."</h2>\n";
echo "<div>\n<input type=\"radio\" name=\"sso_statut\" value=\"no_sso\" ";
if (getSettingValue("sso_statut")=='') echo " checked=\"checked\" ";
echo "/>".get_vocab("Ne_pas_activer_Service_sso")."<br />\n";

if (getSettingValue("sso_statut")!='') {
    echo "<h2>".get_vocab("autres parametres sso")."</h2>\n";
    echo "<input type=\"checkbox\" name=\"cacher_lien_deconnecter\" value=\"y\" ";
    if (getSettingValue("cacher_lien_deconnecter")=="y") echo " checked=\"checked\"";
    echo " />";
    echo get_vocab("sso_actif_cacher_lien_deconnecter");

    // Ajout Check Box empecher les utilisateurs externes de modifier leurs nom prenom et email
    echo "<br /><br /><input type=\"checkbox\" name=\"sso_IsNotAllowedModify\" value=\"y\" ";
    if (getSettingValue("sso_IsNotAllowedModify")=="y") echo " checked=\"checked\"";
    echo " />";
    echo get_vocab("sso_IsNotAllowedModify")."\n";;


    // URL pour empecher l'acces a la page login.php
    echo "<br /><br />".get_vocab("cacher_page_login")."\n";
    $value_url=getSettingValue("Url_cacher_page_login");
    echo "<br /><input type=\"text\" name=\"Url_cacher_page_login\" size=\"40\" value =\"$value_url\"/>\n";

    /*
     Url "Portail
    */
    echo "<br /><br />".get_vocab("Url_portail_sso_explain")."\n";
    $value_url=getSettingValue("Url_portail_sso");
    echo "<br /><input type=\"text\" name=\"Url_portail_sso\" size=\"40\" value =\"$value_url\"/>\n";

}

echo "</div><hr />\n";
// Configuration cas
echo "<h2>".get_vocab("config_cas_title").grr_help("aide_grr_cas_sso")."</h2>\n";
echo "<div>\n<input type=\"hidden\" name=\"valid\" value=\"1\" /></div>\n";
echo "<p>".get_vocab("CAS_SSO_explain")."</p>\n";
echo "<h3>".get_vocab("Statut_par_defaut_utilisateurs_importes")."</h3>\n";
echo "<div>".get_vocab("choix_statut_CAS_SSO")."<br />\n";
echo "<input type=\"radio\" name=\"sso_statut\" value=\"cas_visiteur\" ";
if (getSettingValue("sso_statut")=='cas_visiteur') echo " checked=\"checked\" ";
echo "/>".get_vocab("statut_visitor")."<br />\n";
echo "<input type=\"radio\" name=\"sso_statut\" value=\"cas_utilisateur\" ";
if (getSettingValue("sso_statut")=='cas_utilisateur') echo " checked=\"checked\" ";
echo "/>".get_vocab("statut_user")."<br />\n";

// Forcer authentification ou rediriger vers la page d'accuil de GRR
echo "<br />".get_vocab("sso_redirection_accueil_grr_text1");
echo "<br /><input type=\"checkbox\" name=\"sso_redirection_accueil_grr\" value=\"y\" ";
if (getSettingValue("sso_redirection_accueil_grr")=="y") echo " checked=\"checked\"";
echo " />";
echo get_vocab("sso_redirection_accueil_grr_text2");

// Afficher l'interface de mise en correspondance ldap <--> statut dans GRR.
echo "<br /><br />".get_vocab("sso_active_correspondance_profil_statut_text");
echo "<br /><input type=\"checkbox\" name=\"sso_active_correspondance_profil_statut\" value=\"y\" ";
if (getSettingValue("sso_ac_corr_profil_statut")=="y") echo " checked=\"checked\"";
echo " />";
echo get_vocab("sso_active_correspondance_profil_statut");
echo "</div>";





echo "<hr />\n";
// Configuration lemonldap
echo "<h2>".get_vocab("config_lemon_title").grr_help("aide_grr_lemon")."</h2>\n";
echo "<div><input type=\"hidden\" name=\"valid\" value=\"1\" /></div>\n";
echo "<p>".get_vocab("lemon_SSO_explain")."</p>\n";
echo "<h3>".get_vocab("Statut_par_defaut_utilisateurs_importes")."</h3>\n";
echo "<div>".get_vocab("choix_statut_lemon_SSO")."<br />\n";
echo "<input type=\"radio\" name=\"sso_statut\" value=\"lemon_visiteur\" ";
if (getSettingValue("sso_statut")=='lemon_visiteur') echo " checked=\"checked\" ";
echo "/>".get_vocab("statut_visitor")."<br />\n";
echo "<input type=\"radio\" name=\"sso_statut\" value=\"lemon_utilisateur\" ";
if (getSettingValue("sso_statut")=='lemon_utilisateur') echo " checked=\"checked\" ";
echo "/>".get_vocab("statut_user")."<br /></div>\n";

echo "<hr />\n";
// Configuration lcs
echo "<h2>".get_vocab("config_lcs_title").grr_help("aide_grr_lcs")."</h2>\n";
echo "<div><input type=\"hidden\" name=\"valid\" value=\"1\" /></div>\n";
echo "<p>".get_vocab("lcs_SSO_explain")."</p>\n";
echo "<h3>".get_vocab("Statut_par_defaut_utilisateurs_importes")."</h3>\n";
echo "<div>".get_vocab("choix_statut_lcs_SSO")."<br />\n";
echo "<input type=\"radio\" name=\"sso_statut\" value=\"lcs\" ";
if (getSettingValue("sso_statut")=='lcs') echo " checked=\"checked\" ";
echo "/>".get_vocab("active_lcs")."<br /></div>\n";

echo "<table>\n";
echo "<tr><td>".get_vocab("statut_eleve").get_vocab("deux_points")."</td>\n";
echo "<td><select name=\"lcs_statut_eleve\" size=\"1\">\n";
echo "<option value=\"aucun\"";
if (getSettingValue("lcs_statut_eleve")=='aucun') echo " selected=\"selected\" ";
echo ">(ne pas importer)</option>\n";
echo "<option value=\"utilisateur\"";
if (getSettingValue("lcs_statut_eleve")=='utilisateur') echo " selected=\"selected\" ";
echo ">usager</option>\n";
echo "<option value=\"visiteur\"";
if (getSettingValue("lcs_statut_eleve")=='visiteur') echo " selected=\"selected\" ";
echo ">visiteur</option>\n";
echo "</select></td></tr>\n";

echo "<tr><td>".get_vocab("statut_non_eleve").get_vocab("deux_points")."</td>\n";
echo "<td><select name=\"lcs_statut_prof\" size=\"1\">\n";
echo "<option value=\"aucun\"";
if (getSettingValue("lcs_statut_prof")=='aucun') echo " selected=\"selected\" ";
echo ">(ne pas importer)</option>\n";
echo "<option value=\"utilisateur\"";
if (getSettingValue("lcs_statut_prof")=='utilisateur') echo " selected=\"selected\" ";
echo ">usager</option>\n";
echo "<option value=\"visiteur\"";
if (getSettingValue("lcs_statut_prof")=='visiteur') echo " selected=\"selected\" ";
echo ">visiteur</option>\n";
echo "</select></td></tr>\n";
echo "</table>";
echo "<div>".get_vocab("lcs_SSO_explain_2");
echo "<br /><br />";
echo get_vocab("lcs_SSO_explain_3");
echo "<br /><input type=\"text\" name=\"lcs_liste_groupes_autorises\" value=\"".htmlentities( getSettingValue("lcs_liste_groupes_autorises"))."\" size=\"50\" /></div>\n";

echo "<hr />\n";
// Configuration Lasso
echo "<h2>".get_vocab("config_lasso_title").grr_help("aide_grr_lasso_sso")."</h2>\n";
echo "<div><input type=\"hidden\" name=\"valid\" value=\"1\" /></div>\n";
echo "<p>".get_vocab("lasso_SSO_explain")."</p>\n";
echo "<h3>".get_vocab("Statut_par_defaut_utilisateurs_importes")."</h3>\n";
echo "<div>".get_vocab("choix_statut_lasso_SSO")."<br />\n";
echo "<input type=\"radio\" name=\"sso_statut\" value=\"lasso_visiteur\" ";
if (getSettingValue("sso_statut")=='lasso_visiteur') echo " checked=\"checked\" ";
echo "/>".get_vocab("statut_visitor")."<br />\n";
echo "<input type=\"radio\" name=\"sso_statut\" value=\"lasso_utilisateur\" ";
if (getSettingValue("sso_statut")=='lasso_utilisateur') echo " checked=\"checked\" ";
echo "/>".get_vocab("statut_user")."<br /></div>\n";

echo "<hr />\n";
// Configuration apache
echo "<h2>".get_vocab("config_http_title").grr_help("aide_grr_authentification_http")."</h2>\n";
echo "<div><input type=\"hidden\" name=\"valid\" value=\"1\" /></div>\n";
echo "<p>".get_vocab("http_SSO_explain")."</p>\n";
echo "<h3>".get_vocab("Statut_par_defaut_utilisateurs_importes")."</h3>\n";
echo "<div>".get_vocab("choix_statut_http_SSO")."<br />\n";
echo "<input type=\"radio\" name=\"sso_statut\" value=\"http_visiteur\" ";
if (getSettingValue("sso_statut")=='http_visiteur') echo " checked=\"checked\" ";
echo "/>".get_vocab("statut_visitor")."<br />\n";
echo "<input type=\"radio\" name=\"sso_statut\" value=\"http_utilisateur\" ";
if (getSettingValue("sso_statut")=='http_utilisateur') echo " checked=\"checked\" ";
echo "/>".get_vocab("statut_user")."<br /></div>\n";

//ajout des champs de recherche perso :
echo "<div><br />".get_vocab("explain_champs recherche")."<br />\n";
echo get_vocab("name").get_vocab("deux_points")."<input type=\"text\" name=\"http_champ_nom\"";
if (getSettingValue("http_champ_nom")) echo "value=\"".getSettingValue("http_champ_nom")."\"";
echo "/><br />";
echo get_vocab("first_name").get_vocab("deux_points")."<input type=\"text\" name=\"http_champ_prenom\"";
if (getSettingValue("http_champ_prenom")) echo "value=\"".getSettingValue("http_champ_prenom")."\" ";
echo "/><br />";

echo get_vocab("mail_user").get_vocab("deux_points")."<input type=\"text\" name=\"http_champ_email\"";
if (getSettingValue("http_champ_email")) echo "value=\"".getSettingValue("http_champ_email")."\" ";
echo "/></div>";


// Configuration apache par nom de domaine
echo "<h3>".get_vocab("Statut_pour_domaine_particulier")."</h3>\n";
echo "<div>".get_vocab("http_explain_statut_domaine")."<br />\n";

echo get_vocab("http_domaine_particulier").get_vocab("deux_points")."<input type=\"text\" name=\"http_sso_domain\"";
if (getSettingValue("http_sso_domain")) echo "value=\"".getSettingValue("http_sso_domain")."\" ";
echo "/>";

echo "<br />".get_vocab("statut").get_vocab("deux_points")."<br />";
echo "<input type=\"radio\" name=\"http_sso_statut_domain\" value=\"visiteur\" ";
if (getSettingValue("http_sso_statut_domain")=='visiteur') echo " checked=\"checked\" ";
echo "/>".get_vocab("statut_visitor")."<br />\n";
echo "<input type=\"radio\" name=\"http_sso_statut_domain\" value=\"utilisateur\" ";
if (getSettingValue("http_sso_statut_domain")=='utilisateur') echo " checked=\"checked\" ";
echo "/>".get_vocab("statut_user")."<br /></div>\n";


echo "<hr />\n";
echo "<div style=\"text-align:centrer;\"><input type=\"submit\" name=\"Valider\"value=\"".get_vocab("save")."\" />\n</div>\n";
echo "</form>\n";

// fin de l'affichage de la colonne de droite
echo "</td></tr></table>\n";

?>
</body>
</html>



