<?php
/**
 * admin_room_del
 * Interface de confirmation de suppression d'un domaine ou d'une ressource
 * de l'application GRR
 * Derni�re modification : $Date: 2009-09-29 18:02:56 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   admin
 * @version   $Id: admin_room_del.php,v 1.11 2009-09-29 18:02:56 grr Exp $
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
 * $Log: admin_room_del.php,v $
 * Revision 1.11  2009-09-29 18:02:56  grr
 * *** empty log message ***
 *
 * Revision 1.10  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.9  2009-02-27 13:28:19  grr
 * *** empty log message ***
 *
 * Revision 1.8  2008-11-16 22:00:58  grr
 * *** empty log message ***
 *
 * Revision 1.7  2008-11-13 21:32:51  grr
 * *** empty log message ***
 *
 * Revision 1.6  2008-11-11 22:01:14  grr
 * *** empty log message ***
 *
 * Revision 1.5  2008-11-10 07:06:39  grr
 * *** empty log message ***
 *
 * Revision 1.4  2008-11-07 21:39:40  grr
 * *** empty log message ***
 *
 * Revision 1.3  2008-11-06 21:57:34  grr
 * *** empty log message ***
 *
 *
 */

include "include/admin.inc.php";
$grr_script_name = "admin_room_del.php";

$type = isset($_GET["type"]) ? $_GET["type"] : NULL;
$confirm = isset($_GET["confirm"]) ? $_GET["confirm"] : NULL;
$room = isset($_GET["room"]) ? $_GET["room"] : NULL;
$id_area = isset($_POST["id_area"]) ? $_POST["id_area"] : (isset($_GET["id_area"]) ? $_GET["id_area"] : NULL);
$id_site = isset($_POST['id_site']) ? $_POST['id_site'] : (isset($_GET['id_site']) ? $_GET['id_site'] : -1);
if (isset($room)) settype($room,"integer");
if (isset($id_area)) settype($id_area,"integer");
if (isset($id_site)) settype($id_site,"integer");

if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);


#If we dont know the right date then make it up

$day   = date("d");
$month = date("m");
$year  = date("Y");


# This is gonna blast away something. We want them to be really
# really sure that this is what they want to do.

if($type == "room")
{
    // Seuls les admin de la ressources peuvent supprimer la ressource
    if ((authGetUserLevel(getUserName(),$room) < 4) or (!verif_acces_ressource(getUserName(), $room)))
    {
        showAccessDenied($day, $month, $year, '',$back);
        exit();
    }

    # We are supposed to delete a room
    if(isset($confirm))
    {
        # They have confirmed it already, so go blast!
        grr_sql_begin();
        # First take out all appointments for this room
        grr_sql_command("delete from ".TABLE_PREFIX."_entry where room_id=$room");
        grr_sql_command("delete from ".TABLE_PREFIX."_entry_moderate where room_id=$room");
        #
        grr_sql_command("DELETE FROM ".TABLE_PREFIX."_j_mailuser_room  WHERE id_room=$room");
        grr_sql_command("DELETE FROM ".TABLE_PREFIX."_j_user_room WHERE id_room=$room");

        # Now take out the room itself
        grr_sql_command("delete from ".TABLE_PREFIX."_room where id=$room");
        grr_sql_commit();

        # Go back to the admin page
        Header("Location: admin_room.php?id_area=$id_area&id_site=$id_site");
    }
    else
    {
        # print the page header
        print_header("","","","",$type="with_session", $page="admin");
        echo "<div class=\"page_sans_col_gauche\">";


        # We tell them how bad what theyre about to do is
        # Find out how many appointments would be deleted

        $sql = "select name, start_time, end_time from ".TABLE_PREFIX."_entry where room_id=$room";
        $res = grr_sql_query($sql);
        if (! $res) echo grr_sql_error();
        elseif (grr_sql_count($res) > 0)
        {
            echo get_vocab("deletefollowing") . ":<ul>";

            for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
            {
                echo "<li>$row[0] (";
                echo time_date_string($row[1],$dformat) . " -> ";
                echo time_date_string($row[2],$dformat) . ")";
            }

            echo "</ul>";
        }

        echo "<h1 style=\"text-align:center;\">" .  get_vocab("sure") . "</h1>";
        echo "<h1 style=\"text-align:center;\"><a href=\"admin_room_del.php?type=room&amp;room=$room&amp;confirm=Y&amp;id_area=$id_area\">" . get_vocab("YES") . "!</a> &nbsp;&nbsp;&nbsp; <a href=\"admin_room.php?id_area=$id_area\">" . get_vocab("NO") . "!</a></h1>";
        echo "</div>";
    }
}

if($type == "area")
{
    // Seul l'admin peut supprimer un domaine
    if(authGetUserLevel(getUserName(),$id_area,'area') < 5)
    {
        showAccessDenied($day, $month, $year, '',$back);
        exit();
    }

    # We are only going to let them delete an area if there are
    # no rooms. its easier
    $n = grr_sql_query1("select count(*) from ".TABLE_PREFIX."_room where area_id=$id_area");
    if ($n == 0)
    {
        // Suppression des champ additionnels
        $sqlstring = "select id from ".TABLE_PREFIX."_overload where id_area='".$id_area."'";
        $result = grr_sql_query($sqlstring);
        for ($i = 0; ($field_row = grr_sql_row($result, $i)); $i++) {
            $id_overload = $field_row[0];
            // Suppression des donn�es dans les r�servations d�j� effectu�es
            grrDelOverloadFromEntries($id_overload);
            $sql = "delete from ".TABLE_PREFIX."_overload where id=$id_overload;";
            grr_sql_command($sql);
        }
        # OK, nothing there, lets blast it away
        grr_sql_command("delete from ".TABLE_PREFIX."_area where id=$id_area");
        grr_sql_command("update ".TABLE_PREFIX."_utilisateurs set default_area = '-1', default_room = '-1' where default_area='".$id_area."'");
        grr_sql_command("DELETE FROM ".TABLE_PREFIX."_area_periodes WHERE id_area=$id_area");
        grr_sql_command("DELETE FROM ".TABLE_PREFIX."_j_useradmin_area WHERE id_area=$id_area");
        grr_sql_command("DELETE FROM ".TABLE_PREFIX."_j_type_area WHERE id_area=$id_area");
        grr_sql_command("DELETE FROM ".TABLE_PREFIX."_j_user_area WHERE id_area=$id_area");
        grr_sql_command("DELETE FROM ".TABLE_PREFIX."_j_site_area WHERE id_area=$id_area");
        $test = grr_sql_query1("select VALUE from ".TABLE_PREFIX."_setting where NAME='default_area'");
        if ($test==$id_area) {
            grr_sql_command("delete from ".TABLE_PREFIX."_setting where NAME='default_area'");
            grr_sql_command("delete from ".TABLE_PREFIX."_setting where NAME='default_room'");
            // Settings
            require_once("./include/settings.inc.php");
            //Chargement des valeurs de la table settingS
            if (!loadSettings())
                die("Erreur chargement settings");

        }
        # Redirect back to the admin page
        header("Location: admin_room.php?id_site=$id_site");
    }
    else
    {
        # There are rooms left in the area
        # print the page header
        print_header("","","","",$type="with_session", $page="admin");

        echo "<div class=\"page_sans_col_gauche\">";
        echo "<p>".get_vocab('delarea');
        echo "<br /><a href=\"admin_room.php?id_area=$id_area&amp;id_site=$id_site\">" . get_vocab('back') . "</a></p></div>";
    }
}
?>
</body>
</html>