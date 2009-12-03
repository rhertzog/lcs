<?php
/*
 * $Id: visu_fiches.php 2147 2008-07-23 09:01:04Z tbelliard $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
// Initialisations files
require_once("../lib/initialisations.inc.php");

// Initialisation des variables
$indice_aid = $_GET["indice_aid"];
// V�rification de la validit� de $indice_aid et $aid_id
if (!VerifAidIsAcive($indice_aid,"")) {
    echo "<p>Vous tentez d'acc�der � des outils qui ne sont pas activ�s. veuillez contacter l'administrateur.</p></body></html>";
    die();
}
$nom_projet = sql_query1("select nom from aid_config where indice_aid='".$indice_aid."'");


$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE *********************
$titre_page = "Visualisation des fiches ".$nom_projet;
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class=bold>";
echo "<a href=\"index_fiches.php?indice_aid=".$indice_aid."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";
echo "<center><H3>Liste des projets ".$nom_projet."</H3></center>";

$_login = $_SESSION["login"];
$message_avertissement = "";
$non_defini = "<font color='red'>Non d�fini</font>";

include "./fiches.inc.php";
?>