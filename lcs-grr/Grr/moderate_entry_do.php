<?php
#########################################################################
#                         moderate_entry_do                             #
#                                                                       #
#                  Dernière modification : 05/09/2006                   #
#                                                                       #
#########################################################################
/*
 * Copyright 2003-2007 Laurent Delineau et Clever Age (http://www.clever-age.com)
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

include "include/connect.inc.php";
include "include/config.inc.php";
include "include/functions.inc.php";
include "include/$dbsys.inc.php";
include "include/misc.inc.php";
include "include/mrbs_sql.inc.php";

// Settings
require_once("./include/settings.inc.php");

//Chargement des valeurs de la table settingS
if (!loadSettings())
    die("Erreur chargement settings");

// Session related functions
require_once("./include/session.inc.php");

// Paramètres langage
include "include/language.inc.php";

// Resume session
$fin_session = 'n';
if (!grr_resumeSession())
    $fin_session = 'y';

if (($fin_session == 'y') and (getSettingValue("authentification_obli")==1)) {
    header("Location: ./logout.php?auto=1");
    die();
};

if ((getSettingValue("authentification_obli")==0) and (!isset($_SESSION['login']))) {
    $session_login = '';
    $type_session = "no_session";
}
else
{
  $session_login = $_SESSION['login'];
  $type_session = "with_session";
}

// On vérifie que l'utilisateur a bien le droit d'être ici
$room_id = grr_sql_query1("select room_id from grr_entry where id='".$_POST['id']."'");
$back = '';
if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);
if (authGetUserLevel(getUserName(),$room_id) < 3)
{
    showAccessDenied($day, $month, $year, $area,$back);
    exit();
}

// j'ai besoin de $repeat_id '
$sql = "select repeat_id from grr_entry where id =".$_POST['id'];
$res = grr_sql_query($sql);
if (! $res) fatal_error(0, grr_sql_error());
$row = grr_sql_row($res, 0);
$repeat_id = $row['0'];

// Initialisation
$series = 0;
if ($_POST['moderate'] == "S1") {
     $_POST['moderate'] = "1";
     $series = 1;
}
if ($_POST['moderate'] == "S0") {
     $_POST['moderate'] = "0";
     $series = 1;
}

if ($series==0) {
    //moderation de la ressource
    if ($_POST['moderate'] == 1) {
        $sql = "update grr_entry set moderate = 2 where id = ".$_POST['id'];
    } else {
        $sql = "update grr_entry set moderate = 3 where id = ".$_POST['id'];
    }
    $res = grr_sql_query($sql);
    if (! $res) fatal_error(0, grr_sql_error());

    if (!(grr_backup($_POST['id'],$_SESSION['login'],$_POST['description']))) fatal_error(0, grr_sql_error());
    $tab_id_moderes = array();
} else { // cas d'une série
    // on constitue le tableau des id de la périodicité
    $sql = "select id from grr_entry where repeat_id=".$repeat_id;
    $res = grr_sql_query($sql);
    if (! $res) fatal_error(0, grr_sql_error());
    $tab_entry = array();
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
        $tab_entry[] = $row['0'];
    }
    $tab_id_moderes = array();
    // Boucle sur les résas
    foreach ($tab_entry as $entry_tom) {
        $test = grr_sql_query1("select count(id) from grr_entry_moderate where id = '".$entry_tom."'");
        // Si il existe déjà une entrée dans grr_entry_moderate, cela signifie que la réservation a déjà été modérée.
        // Sinon :
        if ($test == 0) {
            //moderation de la ressource
            if ($_POST['moderate'] == 1) {
                $sql = "update grr_entry set moderate = 2 where id = '".$entry_tom."'";
            } else {
                $sql = "update grr_entry set moderate = 3 where id = '".$entry_tom."'";
           }
           $res = grr_sql_query($sql);
           if (! $res) fatal_error(0, grr_sql_error());

           if (!(grr_backup($entry_tom,$_SESSION['login'],$_POST['description']))) fatal_error(0, grr_sql_error());           // Backup : on enregistre les infos dans grr_entry_moderate
           // On constitue un tableau des réservations modérées
           $tab_id_moderes[] = $entry_tom;
        }
    }
}

// Avant d'effacer la réservation, on procède à la notification par mail
send_mail($_POST['id'],6,$dformat,$tab_id_moderes);

//moderation de la ressource
if ($_POST['moderate'] != 1) {
    // on efface l'entrée de la base
    if ($series==0) {
        $sql = "delete from grr_entry where id = ".$_POST['id'];
        $res = grr_sql_query($sql);
        if (! $res) fatal_error(0, grr_sql_error());
    } else {
        // On sélectionne toutes les réservation de la périodicité
        $res = grr_sql_query("select id from grr_entry where repeat_id='".$repeat_id."'");
        if (! $res) fatal_error(0, grr_sql_error());
        for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
            $entry_tom = $row['0'];
            // Pour chaque réservation, on teste si celle-ci a été refusée
            $test = grr_sql_query1("select count(id) from grr_entry_moderate where id = '".$entry_tom."' and moderate='3'");
            // Si oui, on supprime la réservation
            if ($test > 0)
                $del = grr_sql_query("delete from grr_entry where id = '".$entry_tom."'");
        }
        // On supprime l'info de périodicité
        $del_repeat = grr_sql_query("delete from grr_repeat where id='".$repeat_id."'");
        $dupdate_repeat = grr_sql_query("update grr_entry set repead_id = '0' where repead_id='".$repeat_id."'");
    }
}

$back = 'view_entry.php?id='.$_POST['id'].'&page='.$_POST['page'];

// recuperation
header ('Location: '.$back);
exit();
?>