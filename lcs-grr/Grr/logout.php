<?php
/**
 * logout.php
 * script de deconnexion
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2009-06-04 15:30:17 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: logout.php,v 1.6 2009-06-04 15:30:17 grr Exp $
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
 * $Log: logout.php,v $
 * Revision 1.6  2009-06-04 15:30:17  grr
 * *** empty log message ***
 *
 * Revision 1.5  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.4  2009-01-20 07:19:17  grr
 * *** empty log message ***
 *
 * Revision 1.3  2008-11-11 22:01:14  grr
 * *** empty log message ***
 *
 *
 */

require_once("include/connect.inc.php");
require_once("include/config.inc.php");
include "include/misc.inc.php";
include "include/functions.inc.php";
require_once("include/$dbsys.inc.php");
// Settings
require_once("./include/settings.inc.php");
//Chargement des valeurs de la table settingS
if (!loadSettings())
    die("Erreur chargement settings");

// Param�tres langage
include "include/language.inc.php";

require_once("./include/session.inc.php");


if ((getSettingValue('sso_statut') == 'lasso_visiteur') or (getSettingValue('sso_statut') == 'lasso_utilisateur')) {
  require_once(SPKITLASSO.'/lassospkit_public_api.inc.php');
  session_name(SESSION_NAME);
  @session_start();
  if (@$_SESSION['lasso_nameid'] != NULL)
    {
      // Nous sommes authentifi�s: on se d�connecte, puis on revient
      lassospkit_set_userid(getUserName()); // work-around
      lassospkit_set_nameid($_SESSION['lasso_nameid']);
      lassospkit_soap_logout();
      lassospkit_clean();
    }
}



grr_closeSession($_GET['auto']);

if (isset($_GET['url'])) {
  $url = rawurlencode($_GET['url']);
  header("Location: login.php?url=".$url);
  exit;
}

//redirection vers l'url de d�connexion
$url = getSettingValue("url_disconnect");
if ($url != '') {
  header("Location: $url");
  exit;
}


if (isset($_GET['redirect_page_accueil']) and ($_GET['redirect_page_accueil'] == 'yes')) {
   header("Location: ./".htmlspecialchars_decode(page_accueil())."");
   exit;
}
echo begin_page(get_vocab("mrbs"),"no_session");
?>
<div class="center">
<h1>
<?php
 if (!$_GET['auto']) {
     echo (get_vocab("msg_logout1")."<br/>");
 } else {
     echo (get_vocab("msg_logout2")."<br/>");
 }
?>
</h1><a href="login.php"><?php echo (get_vocab("msg_logout3")."<br/>"); ?></a>
</p>
</div>
</body>
</html>