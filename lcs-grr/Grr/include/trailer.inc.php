<?php
/**
 * trailer.inc.php
 * script de bas de page html
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2009-06-04 15:30:18 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: trailer.inc.php,v 1.4 2009-06-04 15:30:18 grr Exp $
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
 * $Log: trailer.inc.php,v $
 * Revision 1.4  2009-06-04 15:30:18  grr
 * *** empty log message ***
 *
 * Revision 1.3  2009-01-20 07:19:17  grr
 * *** empty log message ***
 *
 * Revision 1.2  2008-11-16 22:00:59  grr
 * *** empty log message ***
 *
 *
 */

// Affichage d'un lien pour format imprimable
if ( ( !isset($_GET['pview'])  or ($_GET['pview'] != 1)) and (isset($affiche_pview))) {
    echo "<div class=\"format_imprimable\"><a href=\"". traite_grr_url($grr_script_name) ."?";
    if (isset($_SERVER['QUERY_STRING']) and ($_SERVER['QUERY_STRING'] != ''))
        echo htmlspecialchars($_SERVER['QUERY_STRING']) . "&amp;";
    echo "pview=1\" ";
    if (getSettingValue("pview_new_windows")==1) echo " target=\"_blank\"";
    echo ">" . get_vocab("ppreview") . "</a></div>";
}
    // Affichage du message d'erreur en cas d'�chec de l'envoi de mails automatiques
    if (!(getSettingValue("javascript_info_disabled"))) {
      if ((isset($_SESSION['session_message_error'])) and ($_SESSION['session_message_error']!=''))  {
        echo "<script type=\"text/javascript\">";
        echo "<!--\n";
        echo " alert(\"".get_vocab("title_automatic_mail")."\\n".$_SESSION['session_message_error']."\\n".get_vocab("technical_contact")."\")";
        echo "//-->";
        echo "</script>";
        $_SESSION['session_message_error'] = "";
      }
    }

?>
</body>
</html>