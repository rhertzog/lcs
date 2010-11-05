<?php
/*
 * $Id: disciplines.php 5608 2010-10-08 19:48:51Z crob $
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

$liste_tables_del = array(
//"absences",
//"aid",
//"aid_appreciations",
//"aid_config",
//"avis_conseil_classe",
//"classes",
//"droits",
//"eleves",
//"responsables",
//"etablissements",
"groupes",
//"j_aid_eleves",
//"j_aid_utilisateurs",
//"j_eleves_classes",
//"j_eleves_etablissements",
"j_eleves_groupes",
//"j_eleves_professeurs",
//"j_eleves_regime",
//"j_professeurs_matieres",
"j_groupes_classes",
"j_groupes_professeurs",
"j_groupes_matieres",
"j_signalement",
//"log",
//"matieres",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
//"periodes",
//"tempo2",
//"temp_gep_import",
//"tempo",
//"utilisateurs",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
//"setting"
);


//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e : Importation des mati�res";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href='../init_scribe/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if (isset($_POST['is_posted'])) {
    // L'admin a valid� la proc�dure, on proc�de donc...
    include "../lib/eole_sync_functions.inc.php";
    // On commence par r�cup�rer toutes les mati�res depuis le LDAP
    $ldap_server = new LDAPServer;
    $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(description=Matiere*)");
    $info = ldap_get_entries($ldap_server->ds,$sr);


    if ($_POST['record'] == "yes") {
        // Suppression des donn�es pr�sentes dans les tables en lien avec les mati�res
        /* NON! On ne fait qu'une mise � jour de la liste, le cas �ch�ant...
        $j=0;
        while ($j < count($liste_tables_del)) {
            if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
                $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
            }
            $j++;
        }
        */

        $new_matieres = array();
        for ($i=0;$i<$info["count"];$i++) {

            $matiere = $info[$i]["cn"][0];
            $matiere = traitement_magic_quotes(corriger_caracteres(trim($matiere)));
            $matiere = preg_replace("/[^A-Za-z0-9.\-]/","",strtoupper($matiere));
            $get_matieres = mysql_query("SELECT matiere FROM matieres");
            $nbmat = mysql_num_rows($get_matieres);
            $matieres = array();
            for($j=0;$j<$nbmat;$j++) {
                $matieres[] = mysql_result($get_matieres, $j, "matiere");
            }

            if (!in_array($matiere, $matieres)) {
                $reg_matiere = mysql_query("INSERT INTO matieres SET matiere='".$matiere."',nom_complet='".htmlentities($_POST['reg_nom_complet'][$matiere])."', priority='11',matiere_aid='n',matiere_atelier='n'");
            } else {
                $reg_matiere = mysql_query("UPDATE matieres SET nom_complet='".htmlentities($_POST['reg_nom_complet'][$matiere])."' WHERE matiere = '" . $matiere . "'");
            }
            if (!$reg_matiere) echo "<p>Erreur lors de l'enregistrement de la mati�re $matiere.";
            $new_matieres[] = $matiere;

            // On regarde maintenant les affectations professeur/mati�re
            for($k=0;$k<$info[$i]["memberuid"]["count"];$k++) {
                $member = $info[$i]["memberuid"][$k];
                $test = mysql_result(mysql_query("SELECT count(*) FROM j_professeurs_matieres WHERE (id_professeur = '" . $member . "' and id_matiere = '" . $matiere . "')"), 0);
                if ($test == 0) {
                    $res = mysql_query("INSERT into j_professeurs_matieres SET id_professeur = '" . $member . "', id_matiere = '" . $matiere . "'");
                }
            }

        }

        // On efface les mati�res qui ne sont plus utilis�es

        $to_remove = array_diff($matieres, $new_matieres);

        foreach($to_remove as $delete) {
            $res = mysql_query("DELETE from matieres WHERE matiere = '" . $delete . "'");
            $res2 = mysql_query("DELETE from j_professeurs_matieres WHERE id_matiere = '" . $delete . "'");
        }

        echo "<p>Op�ration effectu�e.</p>";
        echo "<p>Vous pouvez v�rifier l'importation en allant sur la page de <a href='../matieres/index.php'>gestion des mati�res</a>.</p>";

    } elseif ($_POST['record'] == "no") {

            echo "<form enctype='multipart/form-data' action='disciplines.php' method=post name='formulaire'>";
            echo "<input type=hidden name='record' value='yes'>";
            echo "<input type=hidden name='is_posted' value='yes'>";

            echo "<p>Les mati�res en vert indiquent des mati�res d�j� existantes dans la base GEPI.<br />Les mati�res en rouge indiquent des mati�res nouvelles et qui vont �tre ajout�es � la base GEPI.<br /></p>";
            echo "<p>Attention !!! Il n'y a pas de tests sur les champs entr�s. Soyez vigilant � ne pas mettre des caract�res sp�ciaux dans les champs ...</p>";
            echo "<p>Essayez de remplir tous les champs, cela �vitera d'avoir � le faire ult�rieurement.</p>";
            echo "<p>N'oubliez pas <b>d'enregistrer les donn�es</b> en cliquant sur le bouton en bas de la page<br /><br />";
            echo "<br/>";
            echo "<center>";
            echo "<table border=1 cellpadding=2 cellspacing=2>";
            echo "<tr><td><p class=\"small\">Identifiant de la mati�re</p></td><td><p class=\"small\">Nom complet</p></td></tr>";
            for ($i=0;$i<$info["count"];$i++) {
                $matiere = $info[$i]["cn"][0];
                $matiere = traitement_magic_quotes(corriger_caracteres(trim($matiere)));
                $nom_court = preg_replace("/[^A-Za-z0-9.\-]/","",strtoupper($matiere));
                $nom_long = htmlentities($matiere);
                $test_exist = mysql_query("SELECT * FROM matieres WHERE matiere='$nom_court'");
                $nb_test_matiere_exist = mysql_num_rows($test_exist);

                if ($nb_test_matiere_exist==0) {
                    $disp_nom_court = "<font color=red>".$nom_court."</font>";
                } else {
                    $disp_nom_court = "<font color=green>".$nom_court."</font>";
                }
                echo "<tr>";
                echo "<td>";
                echo "<p><b><center>$disp_nom_court</center></b></p>";
                echo "";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_nom_complet[$nom_court]' value=\"".$nom_long."\">\n";
                echo "</td></tr>";
            }
            echo "</table>\n";
            echo "</center>";
            echo "<center><input type='submit' value='Enregistrer les donn�es'></center>\n";
            echo "</form>\n";
    }

} else {

    echo "<p><b>ATTENTION ...</b><br />";
    echo "<p>Si vous poursuivez la proc�dure les donn�es telles que notes, appr�ciations, ... seront effac�es.</p>";
    echo "<p>Seules la table contenant les mati�res et la table mettant en relation les mati�res et les professeurs seront conserv�es.</p>";
    echo "<p>L'op�ration d'importation des mati�res depuis le LDAP de Scribe va effectuer les op�rations suivantes :</p>";
    echo "<ul>";
    echo "<li>Ajout ou mise � jour de chaque mati�res pr�sente dans le LDAP</li>";
    echo "<li>Association professeurs <-> mati�res</li>";
    echo "</ul>";
    echo "<form enctype='multipart/form-data' action='disciplines.php' method=post>";
    echo "<input type=hidden name='is_posted' value='yes'>";
    echo "<input type=hidden name='record' value='no'>";

    echo "<p>Etes-vous s�r de vouloir continuer ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='Je suis s�r'>";
    echo "</form>";
}
require("../lib/footer.inc.php");
?>