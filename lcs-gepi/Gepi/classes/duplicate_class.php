<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);
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


$msg = '';

if (isset($new_name_defined)) {

    if (!my_ereg("^[[:print:]]{1,10}$",trim($nom_court))) {
    $msg .= "Le nom court doit être composé de caractères alphanumériques (de 1 à 10 caractères).<br />";
    unset($new_name_defined);
    }

    if (!my_ereg("^([[:print:]]|[âäàéèêëüûöôîï]){1,50}$",trim($nom_complet))) {
        $msg .= "Le nom complet doit être composé de caractères alphanumériques (de 1 à 50 caractères).<br />";
    unset($new_name_defined);
    }
}

if (isset($eleves_selected)) {
	check_token();

    // On fait l'enregistrement de la nouvelle classe
    $get_settings = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes WHERE id='$id_classe'");
    $suivi_par = traitement_magic_quotes(corriger_caracteres(@old_mysql_result($get_settings, "0", "suivi_par")));
    $formule = traitement_magic_quotes(corriger_caracteres(@old_mysql_result($get_settings, "0", "formule")));
    $nom_court = traitement_magic_quotes(corriger_caracteres(urldecode($nom_court)));
    $nom_complet = traitement_magic_quotes(corriger_caracteres(urldecode($nom_complet)));



    $register_newclass = mysqli_query($GLOBALS["mysqli"], "INSERT INTO classes SET
    classe='$nom_court',
    nom_complet='$nom_complet',
    formule='$formule',
    suivi_par='$suivi_par',
    format_nom='np'");
    if (!$register_newclass) $msg .= "Erreur lors de l'enregistrement de la nouvelle classe.<br />";

    $newclass_id = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT max(id) FROM classes"), 0);

    // Maintenant on duplique les entrées de la table j_classes_matieres_professeurs
    $get_data1 = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM j_classes_matieres_professeurs WHERE id_classe='$id_classe'");
    $nb1 = mysqli_num_rows($get_data1);
    for ($i1=0;$i1<$nb1;$i1++) {
        $id_matiere = old_mysql_result($get_data1, $i1, "id_matiere");
        $id_professeur = old_mysql_result($get_data1, $i1, "id_professeur");
        $priorite = old_mysql_result($get_data1, $i1, "priorite");
        $ordre_prof = old_mysql_result($get_data1, $i1, "ordre_prof");

        $register1 = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_classes_matieres_professeurs SET id_classe='$newclass_id',id_matiere='$id_matiere',id_professeur='$id_professeur',priorite='$priorite',ordre_prof='$ordre_prof',coef='0', recalcul_rang='y'");
        if (!$register1) $msg .= "Erreur lors de l'enregistrement dans j_classes_matieres_professeurs pour la matière $id_matiere.<br />";
    }

    // table périodes
    $get_data2 = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe='$id_classe'");
    $nb2 = mysqli_num_rows($get_data2);
    for ($i2=0;$i2<$nb2;$i2++) {
        $nom_periode = traitement_magic_quotes(corriger_caracteres(old_mysql_result($get_data2, $i2, "nom_periode")));
        $num_periode = old_mysql_result($get_data2, $i2, "num_periode");
        $verouiller = old_mysql_result($get_data2, $i2, "verouiller");
        $register2 = mysqli_query($GLOBALS["mysqli"], "INSERT INTO periodes SET nom_periode='$nom_periode',num_periode='$num_periode',verouiller='$verouiller',id_classe='$newclass_id'");
        if (!$register2) $msg .= "Erreur lors de l'enregistrement de la période $nom_periode.<br />";
    }

    // On appelle la liste pour faire un traitement élève par élève.

    $query = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' AND e.login = c.login)");
    $number = mysqli_num_rows($query);

    for ($l=0;$l<$number;$l++) {
        $eleve_login = old_mysql_result($query, $l, "login");

        if (isset($$eleve_login)) {

            $update1 = mysqli_query($GLOBALS["mysqli"], "UPDATE j_eleves_classes SET id_classe='$newclass_id' WHERE (login='$eleve_login' AND id_classe='$id_classe')");
            if (!$update1) $msg .= "Erreur lors de la mise à jour de la liaison élève / classe pour l'élève $eleve_login.<br />";
            $update2 = mysqli_query($GLOBALS["mysqli"], "UPDATE j_eleves_professeurs SET id_classe='$newclass_id' WHERE (login='$eleve_login' AND id_classe='$id_classe')");
            if (!$update2) $msg .= "Erreur lors de la mise à jour de la liaison élève / professeur pour l'élève $eleve_login.<br />";
        }
    }

}

//**************** EN-TETE *****************
$titre_page = "Duplication ou scission d'une classe";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à la gestion des classes</a>
</p>
<?php


if (!isset($new_name_defined)) {
    echo "<p>Vous vous apprêtez à scinder une classe.<br />Selon le cas de figure, vous serez amené à recommencer cette opération plusieurs fois.<br /><br />Notez que les paramètres de période de la nouvelle classe seront identiques à ceux de la classe d'origine. Il en est de même des professeurs et matières déjà assignés. De même les élèves gardent le même professeur principal. Ces informations peuvent être modifiées par la suite, une fois l'opération terminée.</p>";

    echo "<form action='duplicate_class.php' method=post>";

	echo add_token_field();

    echo "<p>Veuillez sélectionner la classe que vous souhaitez scinder :<br />";
    $call_classes = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
    $nombreligne = mysqli_num_rows($call_classes);
    echo "<select name='id_classe' size=1>\n";
    $i = "0" ;
    while ($i < $nombreligne) {
        $id_classe = old_mysql_result($call_classes, $i, "id");
        $l_classe = old_mysql_result($call_classes, $i, "classe");
        //echo "<option value='$id_classe' size=1>$l_classe</option>";
        echo "<option value='$id_classe'>$l_classe</option>\n";
    $i++;
    }
    echo "</select>\n";
    echo "<input type=hidden name=new_name_defined value='1' />";
    echo "<p>Entrez le nom court de la nouvelle classe (ex: 2NDE3) :</p>";
    echo "<p><input type=text size=20 name='nom_court' value='' />";
    echo "<br /><p>Entrez le nouveau nom complet de la nouvelle classe (ex: Seconde 3) :</p>";
    echo "<p><input type=text size=20 name='nom_complet' value='' />";
    echo "<br /><p><input type=submit value='Etape suivante' />";
    echo "</form>";

}

if (isset($new_name_defined) && !isset($eleves_selected)) {
    echo "<p>Vous devez désormais sélectionner les élèves qui vont être retirés de la classe d'origine pour être affectés à la nouvelle classe. Cette affectation est effective pour toutes les périodes de la classe d'origine.</p>";

    echo "<p>Elèves à affecter à la nouvelle classe :</p>";

    $call_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
    WHERE (c.id_classe = '$id_classe' AND e.login = c.login)");
    $nb = mysqli_num_rows($call_eleves);

    echo "<form action='duplicate_class.php' method=post>";

	echo add_token_field();

    echo "<table cellpadding=5>";

    for ($k=0;$k<$nb;$k++) {

        $eleve_login = old_mysql_result($call_eleves, $k, "login");
        $eleve_nom = old_mysql_result($call_eleves, $k, "nom");
        $eleve_prenom = old_mysql_result($call_eleves, $k, "prenom");

        echo "<tr><td>";
        echo "<input type=checkbox name='$eleve_login' value='new' />";
        echo "</td><td>";
        echo "$eleve_nom $eleve_prenom";
        echo "</td></tr>";

    }

    echo "</table>";
    echo "<input type=hidden name='eleves_selected' value='1' />";
    echo "<input type=hidden name='id_classe' value='$id_classe' />";
    echo "<input type=hidden name='new_name_defined' value='1' />";
    echo "<input type=hidden name='nom_court' value='".urlencode($nom_court)."' />";
    echo "<input type=hidden name='nom_complet' value='".urlencode($nom_complet)."' />";
    echo "<p><input type=submit value='Enregistrer la duplication' />";
    echo "<p>Attention !!! Procédure irrémédiable !</p>";
    echo "</form>";


}

if (isset($eleves_selected) && isset($new_name_defined)) {
    echo "<p class=alert>La nouvelle classe a bien été crée. Veuillez vérifier que tout s'est bien passé en allant visualiser les paramètres de cette nouvelle classe.</p>";
}
require("../lib/footer.inc.php");
?>