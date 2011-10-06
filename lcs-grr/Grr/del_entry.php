<?php
/**
 * del_entry.php
 * Interface de suppresssion d'une r�servation
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2009-06-04 15:30:17 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: del_entry.php,v 1.7 2009-06-04 15:30:17 grr Exp $
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
 * $Log: del_entry.php,v $
 * Revision 1.7  2009-06-04 15:30:17  grr
 * *** empty log message ***
 *
 * Revision 1.6  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.5  2009-01-20 07:19:17  grr
 * *** empty log message ***
 *
 * Revision 1.4  2008-11-16 22:00:58  grr
 * *** empty log message ***
 *
 *
 */

include "include/connect.inc.php";
include "include/config.inc.php";
include "include/functions.inc.php";
include "include/$dbsys.inc.php";
include_once('include/misc.inc.php');
include "include/mrbs_sql.inc.php";
$grr_script_name = "del_entry.php";
// Settings
require_once("./include/settings.inc.php");
//Chargement des valeurs de la table settingS
if (!loadSettings())
    die("Erreur chargement settings");

// Session related functions
require_once("./include/session.inc.php");

// Resume session
if (!grr_resumeSession()) {
    header("Location: ./logout.php?auto=1&url=$url");
    die();
};

// Param�tres langage
include "include/language.inc.php";

$series = isset($_GET["series"]) ? $_GET["series"] : NULL;
if (isset($series)) settype($series,"integer");
$page = verif_page();
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    settype($id,"integer");
} else {
    die();
}

if($info = mrbsGetEntryInfo($id))
{
    $day   = strftime("%d", $info["start_time"]);
    $month = strftime("%m", $info["start_time"]);
    $year  = strftime("%Y", $info["start_time"]);

    $area  = mrbsGetRoomArea($info["room_id"]);
    $back = "";
    if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);
    if(authGetUserLevel(getUserName(),-1) < 1)
    {
        showAccessDenied($day, $month, $year, $area,$back);
        exit();
    }
    if(!getWritable($info["beneficiaire"], getUserName(),$id))
    {
        showAccessDenied($day, $month, $year, $area,$back);
        exit;
    }
    if(authUserAccesArea(getUserName(), $area)==0)
    {
        showAccessDenied($day, $month, $year, $area,$back);
        exit();
    }

    grr_sql_begin();
    if (getSettingValue("automatic_mail") == 'yes') {
        $_SESSION['session_message_error'] = send_mail($id,3,$dformat);
    }
    // On v�rifie les dates
    $room_id = grr_sql_query1("SELECT ".TABLE_PREFIX."_entry.room_id FROM ".TABLE_PREFIX."_entry, ".TABLE_PREFIX."_room WHERE ".TABLE_PREFIX."_entry.room_id = ".TABLE_PREFIX."_room.id AND ".TABLE_PREFIX."_entry.id='".$id."'");
    $date_now = mktime();
    get_planning_area_values($area); // R�cup�ration des donn�es concernant l'affichage du planning du domaine
    if ((!(verif_booking_date(getUserName(), $id, $room_id, -1, $date_now, $enable_periods))) or
    ((verif_booking_date(getUserName(), $id, $room_id, -1, $date_now, $enable_periods)) and ($can_delete_or_create!="y"))
    )
    {
          showAccessDenied($day, $month, $year, $area,$back);
          exit();
    }

    $result = mrbsDelEntry(getUserName(), $id, $series, 1);
    grr_sql_commit();
    if ($result)
    {
        $_SESSION['displ_msg'] = 'yes';
        Header("Location: ".$page.".php?day=$day&month=$month&year=$year&area=$area&room=".$info["room_id"]);
        exit();
    }
}

// If you got this far then we got an access denied.
$day   = date("d");
$month = date("m");
$year  = date("Y");
showAccessDenied($day, $month, $year, $area,$back);
?>