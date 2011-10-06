<?php
/**
 * admin_col_gauche.php
 * colonne de gauche des �crans d'administration
 * des sites, des domaines et des ressources de l'application GRR
 * Derni�re modification : $Date: 2010-04-07 15:38:13 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @author    Marc-Henri PAMISEUX <marcori@users.sourceforge.net>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @copyright Copyright 2008 Marc-Henri PAMISEUX
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   admin
 * @version   $Id: admin_col_gauche.php,v 1.13 2010-04-07 15:38:13 grr Exp $
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
 * $Log: admin_col_gauche.php,v $
 * Revision 1.13  2010-04-07 15:38:13  grr
 * *** empty log message ***
 *
 * Revision 1.12  2009-12-02 20:11:07  grr
 * *** empty log message ***
 *
 * Revision 1.11  2009-06-04 15:30:17  grr
 * *** empty log message ***
 *
 * Revision 1.10  2009-04-10 05:33:10  grr
 * *** empty log message ***
 *
 * Revision 1.9  2009-02-27 13:28:19  grr
 * *** empty log message ***
 *
 * Revision 1.8  2009-01-20 07:19:16  grr
 * *** empty log message ***
 *
 * Revision 1.7  2008-11-16 22:00:58  grr
 * *** empty log message ***
 *
 * Revision 1.6  2008-11-11 22:01:14  grr
 * *** empty log message ***
 *
 * Revision 1.5  2008-11-06 21:57:34  grr
 * *** empty log message ***
 *
 * Revision 1.4  2008-11-05 21:47:53  grr
 * *** empty log message ***
 *
 * Revision 1.3  2008-11-05 09:02:42  grr
 * Premier volet de modifications relatives � la gestion multi-sites.
 *
 */

function affichetableau($liste,$titre='')
 {
  global $chaine, $vocab;
  if (count($liste) > 0)
    {
      echo "<fieldset>\n";
      echo "<legend>$titre</legend><ul>\n";
      $k = 0;
      foreach($liste as $key)
    {
      if ($chaine == $key)
        echo "<li><span class=\"bground\"><b>".get_vocab($key)."</b></span></li>\n";
      else
        echo "<li><a href='".$key."'>".get_vocab($key)."</a></li>\n";
      $k++;
    }
      echo "</ul></fieldset>\n";
    }
}

echo "<table border=\"0\" cellspacing=\"4\" cellpadding=\"4\">";
// Affichage de la colonne de gauche

?>
<tr><td class="colgauche_admin">

<?php
if(get_request_uri()!='')
{
  $url_ = parse_url(get_request_uri());
  $pos = strrpos($url_['path'], "/")+1;
  $chaine = substr($url_['path'],$pos);
}
else
{
  $chaine = '';
}

echo "<div id=\"colgauche\">\n";
$liste = array();
if(authGetUserLevel(getUserName(),-1,'area') >= 6)
$liste[] = 'admin_config.php';
if(authGetUserLevel(getUserName(),-1,'area') >= 6)
$liste[] = 'admin_type.php';
if(authGetUserLevel(getUserName(),-1,'area') >= 6)
$liste[] = 'admin_calend_ignore.php';
if (getSettingValue("jours_cycles_actif") == "Oui") {
	if(authGetUserLevel(getUserName(),-1,'area') >= 6)
	$liste[] = 'admin_calend_jour_cycle.php';
}
affichetableau($liste,get_vocab("admin_menu_general"));

$liste = array();
if (getSettingValue("module_multisite") == "Oui") {
	if(authGetUserLevel(getUserName(),-1,'area') >= 6)
	$liste[] = 'admin_site.php';
}
if(authGetUserLevel(getUserName(),-1,'area') >= 4)
$liste[] = 'admin_room.php';
if(authGetUserLevel(getUserName(),-1,'area') >= 4)
$liste[] = 'admin_overload.php';
if (getSettingValue("module_multisite") == "Oui")
    affichetableau($liste,get_vocab("admin_menu_site_area_room"));
else
    affichetableau($liste,get_vocab("admin_menu_arearoom"));

$liste = array();
if ((authGetUserLevel(getUserName(),-1,'area') >= 6) or (authGetUserLevel(getUserName(),-1,'user') == 1))
$liste[] = 'admin_user.php';
if (getSettingValue("module_multisite") == "Oui")
  if(authGetUserLevel(getUserName(),-1,'area') >= 6)
  $liste[] = 'admin_admin_site.php';
if(authGetUserLevel(getUserName(),-1,'area') >= 6)
$liste[] = 'admin_right_admin.php';
if(authGetUserLevel(getUserName(),-1,'area') >= 4)
$liste[] = 'admin_access_area.php';
if(authGetUserLevel(getUserName(),-1,'area') >= 4)
$liste[] = 'admin_right.php' ;
if ((getSettingValue("ldap_statut")!="") or (getSettingValue("sso_statut")!="") or (getSettingValue("imap_statut")!="")) {
    if(authGetUserLevel(getUserName(),-1,'area') >= 6)
    $liste[] = 'admin_purge_accounts.php';
}
affichetableau($liste,get_vocab("admin_menu_user"));

$liste = array();
if(authGetUserLevel(getUserName(),-1,'area') >= 4)
$liste[] = 'admin_email_manager.php';
if(authGetUserLevel(getUserName(),-1,'area') >= 6)
$liste[] = 'admin_view_connexions.php';
if(authGetUserLevel(getUserName(),-1,'area') >= 5)
$liste[] = 'admin_calend.php';
if(authGetUserLevel(getUserName(),-1,'area') >= 6)
$liste[] = 'admin_maj.php';
if (getSettingValue("sso_ac_corr_profil_statut")=='y') {
    if(authGetUserLevel(getUserName(),-1,'area') >= 5)
    $liste[] = 'admin_corresp_statut.php';
}
affichetableau($liste,get_vocab("admin_menu_various"));
// Possibilit� de bloquer l'affichage de la rubrique "Authentification et ldap"
if ((!isset($sso_restrictions)) or ($sso_restrictions==false)) {
    $liste = array();
    if(authGetUserLevel(getUserName(),-1,'area') >= 6)
    $liste[] = 'admin_config_ldap.php';
    if(authGetUserLevel(getUserName(),-1,'area') >= 6)
    $liste[] = 'admin_config_sso.php';
   //ajout page admin_config_imap.php
    if(authGetUserLevel(getUserName(),-1,'area') >= 6)
    $liste[] = 'admin_config_imap.php';
    affichetableau($liste,get_vocab("admin_menu_auth"));

}
// d�but affichage de la colonne de gauche
      echo "</div>\n";
?>
</td><td>

