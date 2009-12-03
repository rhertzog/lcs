<?php
/*
 * Last modification  : 22/08/2006
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
};


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


$msg = '';

if (isset($new_name_defined)) {

    if (!ereg("^[[:print:]]{1,10}$",trim($nom_court))) {
    $msg .= "Le nom court doit �tre compos� de caract�res alphanum�riques (de 1 � 10 caract�res).<br />";
    unset($new_name_defined);
    }

    if (!ereg("^([[:print:]]|[�������������]){1,50}$",trim($nom_complet))) {
        $msg .= "Le nom complet doit �tre compos� de caract�res alphanum�riques (de 1 � 50 caract�res).<br />";
    unset($new_name_defined);
    }
}

if (isset($eleves_selected)) {

    // On fait l'enregistrement de la nouvelle classe
    $get_settings = mysql_query("SELECT * FROM classes WHERE id='$id_classe'");
    $suivi_par = traitement_magic_quotes(corriger_caracteres(@mysql_result($get_settings, "0", "suivi_par")));
    $formule = traitement_magic_quotes(corriger_caracteres(@mysql_result($get_settings, "0", "formule")));
    $nom_court = traitement_magic_quotes(corriger_caracteres(urldecode($nom_court)));
    $nom_complet = traitement_magic_quotes(corriger_caracteres(urldecode($nom_complet)));



    $register_newclass = mysql_query("INSERT INTO classes SET
    classe='$nom_court',
    nom_complet='$nom_complet',
    formule='$formule',
    suivi_par='$suivi_par',
    format_nom='np'");
    if (!$register_newclass) $msg .= "Erreur lors de l'enregistrement de la nouvelle classe.<br />";

    $newclass_id = mysql_result(mysql_query("SELECT max(id) FROM classes"), 0);

    // Maintenant on duplique les entr�es de la table j_classes_matieres_professeurs
    $get_data1 = mysql_query("SELECT * FROM j_classes_matieres_professeurs WHERE id_classe='$id_classe'");
    $nb1 = mysql_num_rows($get_data1);
    for ($i1=0;$i1<$nb1;$i1++) {
        $id_matiere = mysql_result($get_data1, $i1, "id_matiere");
        $id_professeur = mysql_result($get_data1, $i1, "id_professeur");
        $priorite = mysql_result($get_data1, $i1, "priorite");
        $ordre_prof = mysql_result($get_data1, $i1, "ordre_prof");

        $register1 = mysql_query("INSERT INTO j_classes_matieres_professeurs SET id_classe='$newclass_id',id_matiere='$id_matiere',id_professeur='$id_professeur',priorite='$priorite',ordre_prof='$ordre_prof',coef='0', recalcul_rang='y'");
        if (!$register1) $msg .= "Erreur lors de l'enregistrement dans j_classes_matieres_professeurs pour la mati�re $id_matiere.<br />";
    }

    // table p�riodes
    $get_data2 = mysql_query("SELECT * FROM periodes WHERE id_classe='$id_classe'");
    $nb2 = mysql_num_rows($get_data2);
    for ($i2=0;$i2<$nb2;$i2++) {
        $nom_periode = traitement_magic_quotes(corriger_caracteres(mysql_result($get_data2, $i2, "nom_periode")));
        $num_periode = mysql_result($get_data2, $i2, "num_periode");
        $verouiller = mysql_result($get_data2, $i2, "verouiller");
        $register2 = mysql_query("INSERT INTO periodes SET nom_periode='$nom_periode',num_periode='$num_periode',verouiller='$verouiller',id_classe='$newclass_id'");
        if (!$register2) $msg .= "Erreur lors de l'enregistrement de la p�riode $nom_periode.<br />";
    }

    // table p�riodes observatoire
    $get_data3 = mysql_query("SELECT * FROM periodes_observatoire WHERE id_classe='$id_classe'");
    $nb3 = mysql_num_rows($get_data3);
    for ($i3=0;$i3<$nb3;$i3++) {
        $nom_periode = traitement_magic_quotes(corriger_caracteres(mysql_result($get_data3, $i3, "nom_periode")));
        $num_periode = mysql_result($get_data3, $i3, "num_periode");
        $verouiller = mysql_result($get_data3, $i3, "verouiller");
        $date_limite = traitement_magic_quotes(corriger_caracteres(mysql_result($get_data3, $i3, "date_limite")));
        $periode_affiliee = mysql_result($get_data3, $i3, "periode_affiliee");

        $register3 = mysql_query("INSERT INTO periodes_observatoire SET nom_periode='$nom_periode',num_periode='$num_periode',verouiller='$verouiller',id_classe='$newclass_id',date_limite='$date_limite',periode_affiliee='$periode_affiliee'");
        if (!$register3) $msg .= "Erreur lors de l'enregistrement de la p�riode observatoire $nom_periode.<br />";
    }

    // On appelle la liste pour faire un traitement �l�ve par �l�ve.

    $query = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' AND e.login = c.login)");
    $number = mysql_num_rows($query);

    for ($l=0;$l<$number;$l++) {
        $eleve_login = mysql_result($query, $l, "login");

        if (isset($$eleve_login)) {

            $update1 = mysql_query("UPDATE j_eleves_classes SET id_classe='$newclass_id' WHERE (login='$eleve_login' AND id_classe='$id_classe')");
            if (!$update1) $msg .= "Erreur lors de la mise � jour de la liaison �l�ve / classe pour l'�l�ve $eleve_login.<br />";
            $update2 = mysql_query("UPDATE j_eleves_professeurs SET id_classe='$newclass_id' WHERE (login='$eleve_login' AND id_classe='$id_classe')");
            if (!$update2) $msg .= "Erreur lors de la mise � jour de la liaison �l�ve / professeur pour l'�l�ve $eleve_login.<br />";
        }
    }

}

//**************** EN-TETE *****************
$titre_page = "Duplication ou scission d'une classe";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour � la gestion des classes</a>
</p>
<?php


if (!isset($new_name_defined)) {
    echo "<p>Vous vous appr�tez � scinder une classe.<br />Selon le cas de figure, vous serez amen� � recommencer cette op�ration plusieurs fois.<br /><br />Notez que les param�tres de p�riode de la nouvelle classe seront identiques � ceux de la classe d'origine. Il en est de m�me des professeurs et mati�res d�j� assign�s. De m�me les �l�ves gardent le m�me professeur principal. Ces informations peuvent �tre modifi�es par la suite, une fois l'op�ration termin�e.</p>";

    echo "<form action='duplicate_class.php' method=post>";
    echo "<p>Veuillez s�lectionner la classe que vous souhaitez scinder :<br />";
    $call_classes = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
    $nombreligne = mysql_num_rows($call_classes);
    echo "<select name='id_classe' size=1>\n";
    $i = "0" ;
    while ($i < $nombreligne) {
        $id_classe = mysql_result($call_classes, $i, "id");
        $l_classe = mysql_result($call_classes, $i, "classe");
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
    echo "<p>Vous devez d�sormais s�lectionner les �l�ves qui vont �tre retir�s de la classe d'origine pour �tre affect�s � la nouvelle classe. Cette affectation est effective pour toutes les p�riodes de la classe d'origine.</p>";

    echo "<p>El�ves � affecter � la nouvelle classe :</p>";

    $call_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
    WHERE (c.id_classe = '$id_classe' AND e.login = c.login)");
    $nb = mysql_num_rows($call_eleves);

    echo "<form action='duplicate_class.php' method=post>";
    echo "<table cellpadding=5>";

    for ($k=0;$k<$nb;$k++) {

        $eleve_login = mysql_result($call_eleves, $k, "login");
        $eleve_nom = mysql_result($call_eleves, $k, "nom");
        $eleve_prenom = mysql_result($call_eleves, $k, "prenom");

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
    echo "<p>Attention !!! Proc�dure irr�m�diable !</p>";
    echo "</form>";


}

if (isset($eleves_selected) && isset($new_name_defined)) {
    echo "<p class=alert>La nouvelle classe a bien �t� cr�e. Veuillez v�rifier que tout s'est bien pass� en allant visualiser les param�tres de cette nouvelle classe.</p>";
}
require("../lib/footer.inc.php");
?>