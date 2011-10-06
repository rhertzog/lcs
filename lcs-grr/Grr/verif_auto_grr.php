<?php
/**
 * verif_auto_grr.php
 * Ex�cution de taches automatiques
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2009-10-09 07:55:48 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: verif_auto_grr.php,v 1.5 2009-10-09 07:55:48 grr Exp $
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


/*
MOT DE PASSE
------------
  L'ex�cution de ce script requiert un mot de passe d�fini dans l'interface en ligne de GRR (configuration g�n�rale -> Interactivit�)

*/

$titre = "GRR - Ex&eacute;cution de t&acirc;ches automatiques";
include "include/connect.inc.php";
include "include/config.inc.php";
include "include/misc.inc.php";
include "include/functions.inc.php";
include "include/$dbsys.inc.php";
include "include/mrbs_sql.inc.php";

$grr_script_name = "verif_auto_grr.php";
include("include/settings.inc.php");
if (!loadSettings())
    die("Erreur chargement settings");

if ((!isset($_GET['mdp'])) and (!isset($argv[1]))) {
    echo "Il manque des arguments pour executer ce script. Reportez-vous a la documentation.";
    die();
}

// D�but du script
if (isset($argv[1])) {
  DEFINE("CHEMIN_COMPLET_GRR",getSettingValue("chemin_complet_grr"));
  chdir(CHEMIN_COMPLET_GRR);
}
include "include/language.inc.php";

if (!isset($_GET['mdp']))
    $_GET['mdp']=$argv[1];

if ((!isset($_GET['mdp'])) or ($_GET['mdp'] != getSettingValue("motdepasse_verif_auto_grr")) or (getSettingValue("motdepasse_verif_auto_grr")=='')) {
    if (!isset($argv[1]))
        echo begin_page($titre,$page="no_session")."<p>";
    echo "Le mot de passe fourni est invalide.";
    if (!isset($argv[1])) {
        echo "</p>";
        include "include/trailer.inc.php";
    }
    die();
}

if (!isset($argv[1]))
    echo begin_page($titre,$page="no_session");
// On v�rifie une fois par jour si le d�lai de confirmation des r�servations est d�pass�
// Si oui, les r�servations concern�es sont supprim�es et un mail automatique est envoy�.
verify_confirm_reservation();

// On v�rifie une fois par jour que les ressources ont �t� rendue en fin de r�servation
// Si non, une notification email est envoy�e
verify_retard_reservation();
if (!isset($argv[1])) {
    echo "<p>Le script a �t� ex�cut�.</p>";
    include "include/trailer.inc.php";
} else {
    echo "Le script a ete execute.";
}
?>