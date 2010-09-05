<?php
/**
 *
 * @version $Id: index.php 5018 2010-08-04 21:23:31Z jjacquard $
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

// Initialisation des feuilles de style apr�s modification pour am�liorer l'accessibilit�
$accessibilite="y";

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On v�rifie si le module est activ�
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activ�.");
}

if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
    die("Le module n'est pas activ�.");
}

//on va redirig� vers le bonee onglet
if (isset($_SESSION['abs2_onglet']) && $_SESSION['abs2_onglet'] != 'index.php') {
    header("Location: ./".$_SESSION['abs2_onglet']);
    die();
}

if ($utilisateur->getStatut()=="cpe" || $utilisateur->getStatut()=="scolarite") {
    header("Location: ./tableau_des_appels.php");
    die();
} else if ($utilisateur->getStatut()=="professeur") {
    header("Location: ./saisir_groupe.php");
    die();
} else if ($utilisateur->getStatut()=="autre") {
    header("Location: ./saisir_eleve.php");
    die();
}

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$titre_page = "Les absences";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

include('menu_abs2.inc.php');

echo "<div class='css-panes' id='containDiv'>\n";
    echo "<div style='display:block'>\n";
    //echo "<p>Petit texte de pr�sentation du module...</p>\n";
    echo "</div>\n";
echo "</div>\n";

require_once("../lib/footer.inc.php");
?>