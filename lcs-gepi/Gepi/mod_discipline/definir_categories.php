<?php

/*
 * $Id: definir_categories.php 5400 2010-09-23 10:01:22Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Didier Blanqui
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

//$variables_non_protegees = 'yes';
// Initialisations files
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
$sql = "SELECT 1=1 FROM `droits` WHERE id='/mod_discipline/definir_categories.php';";
$test = mysql_query($sql);
if (mysql_num_rows($test) == 0) {
    $sql = "INSERT INTO droits VALUES ( '/mod_discipline/definir_categories.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: D�finir les cat�gories', '')";
    $test = mysql_query($sql);
}
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_lieux.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: D�finir les lieux', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if(strtolower(substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d acc�der au module Discipline qui est d�sactiv� !");
	tentative_intrusion(1, "Tentative d'acc�s au module Discipline qui est d�sactiv�.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$msg = "";

$suppr_categorie = isset($_POST['suppr_categorie']) ? $_POST['suppr_categorie'] : NULL;
$categorie = isset($_POST['categorie']) ? $_POST['categorie'] : NULL;
$sigle = isset($_POST['sigle']) ? $_POST['sigle'] : NULL;
$cpt = isset($_POST['cpt']) ? $_POST['cpt'] : 0;

if (isset($suppr_categorie)) {
    for ($i = 0; $i < $cpt; $i++) {
        if (isset($suppr_categorie[$i])) {
            $sql = "DELETE FROM s_categories WHERE id='$suppr_categorie[$i]';";
            $suppr = mysql_query($sql);
            if (!$suppr) {
                //$msg.="ERREUR lors de la suppression de la qualit� n�".$suppr_lieu[$i].".<br />\n";
                $msg.="ERREUR lors de la suppression de la cat�gorie n�" . $suppr_categorie[$i] . ".<br />\n";
            } else {
                $msg.="Suppression de la cat�gorie n�" . $suppr_categorie[$i] . ".<br />\n";
                $sql = "UPDATE s_incidents SET id_categorie=0 WHERE id_categorie=" . $suppr_categorie[$i] . ";";
                $res = mysql_query($sql);
                if (!$res) {
                    $msg.="ERREUR lors de la mise � jour des incidents de la cat�gorie supprim�e !! <br />\n";
                } else {
                    $msg.="Mise � jour des incidents de la cat�gorie supprim�e effectu�e.<br />\n";
                }
            }
        }
    }
}

if ((isset($categorie)) && ($categorie != '')) {
    $a_enregistrer = 'y';

    $sql = "SELECT categorie FROM s_categories ORDER BY categorie;";
    $res = mysql_query($sql);
    if (mysql_num_rows($res) > 0) {
        $tab_categorie = array();
        while ($lig = mysql_fetch_object($res)) {
            $tab_categorie[] = $lig->categorie;
        }

        if (in_array($categorie, $tab_categorie)) {
            $a_enregistrer = 'n';
        }
    }

    if ($a_enregistrer == 'y') {
        //$lieu=addslashes(my_ereg_replace('(\\\r\\\n)+',"\r\n",my_ereg_replace("&#039;","'",html_entity_decode($lieu))));
        $categorie = my_ereg_replace('(\\\r\\\n)+', "\r\n", $categorie);

        $sql = "INSERT INTO s_categories SET categorie='" . $categorie . "', sigle='" . $sigle . "';";
        $res = mysql_query($sql);
        if (!$res) {
            $msg.="ERREUR lors de l'enregistrement de " . $categorie . "<br />\n";
        } else {
            $msg.="Enregistrement de " . $categorie . "<br />\n";
        }
    }
}

$themessage = 'Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
//$titre_page = "Sanctions: D�finition des qualit�s";
$titre_page = "Discipline: D�finition des cat�gories";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='" . $_SERVER['PHP_SELF'] . "' method='post' name='formulaire'>\n";

//echo "<p class='bold'>Saisie des qualit�s dans un incident&nbsp;:</p>\n";
echo "<p class='bold'>Saisie des cat�gories des incidents&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt = 0;
$sql = "SELECT * FROM s_categories ORDER BY categorie;";
$res = mysql_query($sql);
if (mysql_num_rows($res) == 0) {
    //echo "<p>Aucune qualit� n'est encore d�finie.</p>\n";
    echo "<p>Aucune cat�gorie n'est encore d�finie.</p>\n";
} else {
    //echo "<p>Qualit�s existantes&nbsp;:</p>\n";
    //echo "<table class='boireaus' border='1' summary='Tableau des qualit�s existantes'>\n";
    echo "<p>Cat�gories existantes&nbsp;:</p>\n";
    echo "<table class='boireaus' border='1' summary='Tableau des cat�gories existantes'>\n";
    echo "<tr>\n";
    //echo "<th>Qualit�</th>\n";
    echo "<th>Cat�gorie</th>\n";
    echo "<th>Sigle</th>\n";
    echo "<th>Supprimer</th>\n";
    echo "</tr>\n";
    $alt = 1;
    while ($lig = mysql_fetch_object($res)) {
        $alt = $alt * (-1);
        echo "<tr class='lig$alt'>\n";

        echo "<td>\n";
        echo "<label for='suppr_categorie_$cpt' style='cursor:pointer;'>";
        echo $lig->categorie;
        echo "</label>";
        echo "</td>\n";

        echo "<td>\n";
        echo "<label for='suppr_categorie_$cpt' style='cursor:pointer;'>";
        echo $lig->sigle;
        echo "</label>";
        echo "</td>\n";

        echo "<td><input type='checkbox' name='suppr_categorie[]' id='suppr_categorie_$cpt' value=\"$lig->id\" onchange='changement();' /></td>\n";
        echo "</tr>\n";

        $cpt++;
    }

    echo "</table>\n";
}
echo "</blockquote>\n";

echo "<p>Nouvelle cat�gorie&nbsp;: <input type='text' name='categorie' value='' onchange='changement();' /></p>\n";
echo "<p>Sigle&nbsp;: <input type='text' name='sigle' value='' onchange='changement();' /></p>\n";
echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>