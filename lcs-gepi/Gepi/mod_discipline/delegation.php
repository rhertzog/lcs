<?php

/*
 * $Id: delegation.php 6727 2011-03-29 15:14:30Z crob $
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

$variables_non_protegees = 'yes';
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
/*
$sql = "SELECT 1=1 FROM `droits` WHERE id='/mod_discipline/delegation.php';";
$test = mysql_query($sql);
if (mysql_num_rows($test) == 0) {
    $sql = "INSERT INTO droits VALUES ( '/mod_discipline/delegation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: D�finir les d�l�gations pour exclusion temporaire', '')";
    $test = mysql_query($sql);
}
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/delegation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: D�finir les d�l�gations pour exclusion temporaire', '');";
*/
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

$msg = "";

$cpt = isset($_POST['cpt']) ? $_POST['cpt'] : 0;
$suppr_delegation = isset($_POST['suppr_delegation']) ? $_POST['suppr_delegation'] : NULL;
$fct_delegation=isset($_POST['fct_delegation']) ? $_POST['fct_delegation'] : NULL;
$fct_autorite=isset($_POST['fct_autorite']) ? $_POST['fct_autorite'] : NULL;
$nom_autorite=isset($_POST['nom_autorite']) ? $_POST['nom_autorite'] : NULL;

if (isset($NON_PROTECT["fct_delegation"])){
			$fct_delegation=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["fct_delegation"]));
			// Contr�le des saisies pour supprimer les sauts de lignes surnum�raires.
			//$fct_delegation=my_ereg_replace('(\\\r\\\n)+',"\r\n",$fct_delegation);
			$fct_delegation=preg_replace('/(\\\r\\\n)+/',"\r\n",$fct_delegation);
			$fct_delegation=preg_replace('/(\\\r)+/',"\r",$fct_delegation);
			$fct_delegation=preg_replace('/(\\\n)+/',"\n",$fct_delegation);
		}
		else {
			$fct_delegation="";
		}

if (isset($suppr_delegation)) {
	check_token();
    for ($i = 0; $i < $cpt; $i++) {
        if (isset($suppr_delegation[$i])) {
            $sql = "DELETE FROM s_delegation WHERE id_delegation='$suppr_delegation[$i]';";
			//echo $sql;
            $suppr = mysql_query($sql);
            if (!$suppr) {
                $msg.="ERREUR lors de la suppression de la d�l�gation n�" . $suppr_delegation[$i] . ".<br />\n";
            } else {
                  $msg.="Suppression de la delegation n�" . $suppr_delegation[$i] . ".<br />\n";
                 $sql = "UPDATE s_exclusions SET id_signataire=0 WHERE id_signataire=" . $suppr_delegation[$i] . ";";
                 $res = mysql_query($sql);
                if (!$res) {
                    $msg.="ERREUR lors de la mise � jour la delegation aux exclusions prononc�es ! <br />\n";
                } else {
                    $msg.="Mise � jour de la delegation aux exclusions prononc�es effectu�e.<br />\n";
                }
            }
        }
    }
}

if ((isset($fct_autorite)) && ($fct_autorite != '')) {
	check_token();

    $a_enregistrer = 'y';

    $sql = "SELECT fct_autorite FROM s_delegation ORDER BY fct_autorite;";
	//echo $sql;
    $res = mysql_query($sql);
    if (mysql_num_rows($res) > 0) {
        $tab_delegation = array();
        while ($lig = mysql_fetch_object($res)) {
            $tab_delegation[] = $lig->fct_autorite;
        }

        if (in_array($delegation, $tab_delegation)) {
            $a_enregistrer = 'n';
        }
    }
	
    if ($a_enregistrer == 'y') {
        //$lieu=addslashes(my_ereg_replace('(\\\r\\\n)+',"\r\n",my_ereg_replace("&#039;","'",html_entity_decode($lieu))));
       // $categorie = my_ereg_replace('(\\\r\\\n)+', "\r\n", $categorie);
        $sql = "INSERT INTO s_delegation SET fct_delegation='" . $fct_delegation . "', fct_autorite='" . $fct_autorite . "', nom_autorite='" . $nom_autorite. "';";
		
        $res = mysql_query($sql);
        if (!$res) {
            $msg.="ERREUR lors de l'enregistrement de " . $fct_autorite . "<br />\n";
        } else {
            $msg.="Enregistrement de " . $fct_autorite . "<br />\n";
        }
    }
}

$themessage = 'Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
//$titre_page = "Sanctions: D�finition des qualit�s";
$titre_page = "Discipline: Gestion des d�l�gations d'exclusion temporaire";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='" . $_SERVER['PHP_SELF'] . "' method='post' name='formulaire'>\n";
echo add_token_field();

//echo "<p class='bold'>Saisie des qualit�s dans un incident&nbsp;:</p>\n";
echo "<p class='bold'>Saisie des d�l�gations&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt = 0;
$sql = "SELECT * FROM s_delegation ORDER BY id_delegation;";
$res = mysql_query($sql);
if (mysql_num_rows($res) == 0) {
    //echo "<p>Aucune qualit� n'est encore d�finie.</p>\n";
    echo "<p>Aucune d�l�gation n'est encore d�finie.</p>\n";
} else {
    //echo "<p>Qualit�s existantes&nbsp;:</p>\n";
    echo "<p>D�l�gations existantes&nbsp;:</p>\n";
    echo "<table class='boireaus' border='1' summary='Tableau des d�l�gations existantes'>\n";
    echo "<tr>\n";
    echo "<th>Texte de d�l�gation du chef d'�tablissement</th>\n";
    echo "<th>Fonction de l'autorit� signataire</th>\n";
	echo "<th>Nom de l'autorit� signataire</th>\n";
    echo "<th>Supprimer</th>\n";
    echo "</tr>\n";
    $alt = 1;
	
    while ($lig = mysql_fetch_object($res)) {
        $alt = $alt * (-1);
        echo "<tr class='lig$alt'>\n";

        echo "<td>\n";
        echo "<label for='suppr_delegation_$cpt' style='cursor:pointer;'>";
        echo $lig->fct_delegation;
        echo "</label>";
        echo "</td>\n";

        echo "<td>\n";
        echo "<label for='suppr_delegation_$cpt' style='cursor:pointer;'>";
        echo $lig->fct_autorite;
        echo "</label>";
        echo "</td>\n";
		
		echo "<td>\n";
        echo "<label for='suppr_delegation_$cpt' style='cursor:pointer;'>";
        echo $lig->nom_autorite;
        echo "</label>";
        echo "</td>\n";


        echo "<td><input type='checkbox' name='suppr_delegation[]' id='suppr_delegation_$cpt' value=\"$lig->id_delegation\" onchange='changement();' /></td>\n";
        echo "</tr>\n";

        $cpt++;
    }

    echo "</table>\n";
}
	echo "</blockquote>\n";
	echo "<table class='boireaus' border='1' summary='Saisie des informations de d�l�gation'>\n";
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Texte de la d�l�gation du chef d'�tablissement&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<textarea name='no_anti_inject_fct_delegation' cols='50' onchange='changement();'></textarea>\n";
	echo "<i>(facultatif) Ex : Pour le Chef d'�tablissement,</BR></BR>et par d�l�gation,</i></td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Fonction de l'autorit� signataire&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='fct_autorite' id='fct_autorite' value='' onchange='changement();' />\n";
	echo "<i>Fonction du personnel de direction ou du d�l�gataire</i></td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nom de l'autorit� signataire&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='nom_autorite' id='nom_autorite' value='' onchange='changement();' />\n";
	echo "<i>Nom du personnel de direction ou du d�l�gataire</i></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>