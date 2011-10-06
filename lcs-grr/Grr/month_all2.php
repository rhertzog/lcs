<?php
/**
 * month_all2.php
 * Interface d'accueil avec affichage par mois des r�servation de toutes les ressources d'un domaine
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2009-12-02 20:11:07 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: month_all2.php,v 1.17 2009-12-02 20:11:07 grr Exp $
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
 * $Log: month_all2.php,v $
 * Revision 1.17  2009-12-02 20:11:07  grr
 * *** empty log message ***
 *
 * Revision 1.16  2009-09-29 18:02:57  grr
 * *** empty log message ***
 *
 * Revision 1.15  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.14  2009-02-27 22:05:03  grr
 * *** empty log message ***
 *
 * Revision 1.13  2009-01-20 07:19:17  grr
 * *** empty log message ***
 *
 * Revision 1.12  2008-11-16 22:00:59  grr
 * *** empty log message ***
 *
 * Revision 1.11  2008-11-14 07:29:09  grr
 * *** empty log message ***
 *
 * Revision 1.10  2008-11-13 21:32:51  grr
 * *** empty log message ***
 *
 * Revision 1.9  2008-11-11 22:01:14  grr
 * *** empty log message ***
 *
 * Revision 1.8  2008-11-10 08:17:34  grr
 * *** empty log message ***
 *
 * Revision 1.7  2008-11-10 07:06:39  grr
 * *** empty log message ***
 *
 *
 */

include "include/connect.inc.php";
include "include/config.inc.php";
include "include/misc.inc.php";
include "include/functions.inc.php";
include "include/$dbsys.inc.php";
include "include/mincals.inc.php";
include "include/mrbs_sql.inc.php";
$grr_script_name = "month_all2.php";
// Settings
require_once("./include/settings.inc.php");
//Chargement des valeurs de la table settingS
if (!loadSettings())
    die("Erreur chargement settings");

// Session related functions
require_once("./include/session.inc.php");
// Resume session
if (!grr_resumeSession()) {
    if ((getSettingValue("authentification_obli")==1) or ((getSettingValue("authentification_obli")==0) and (isset($_SESSION['login'])))) {
       header("Location: ./logout.php?auto=1&url=$url");
       die();
    }
};

// Construction des identifiants de la ressource $room, du domaine $area, du site $id_site
Definition_ressource_domaine_site();

// R�cup�ration des donn�es concernant l'affichage du planning du domaine
get_planning_area_values($area);

// Param�tres langage
include "include/language.inc.php";

// On affiche le lien "format imprimable" en bas de la page
$affiche_pview = '1';
if (!isset($_GET['pview'])) $_GET['pview'] = 0; else $_GET['pview'] = 1;
if ($_GET['pview'] == 1)
    $class_image = "print_image";
else
    $class_image = "image";

# Default parameters:
if (empty($debug_flag)) $debug_flag = 0;
if (empty($month) || empty($year) || !checkdate($month, 1, $year))
{
    $month = date("m");
    $year  = date("Y");
}
if (!isset($day)) $day = 1;

if ((getSettingValue("authentification_obli")==0) and (getUserName()=='')) {
    $type_session = "no_session";
} else {
    $type_session = "with_session";
}
if ($type_session == "with_session") $_SESSION['type_month_all'] = "month_all2";
$type_month_all="month_all2";

$back = '';
if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);


if (check_begin_end_bookings($day, $month, $year))
{
    showNoBookings($day, $month, $year, $area,$back,$type_session);
    exit();
}

if((authGetUserLevel(getUserName(),-1) < 1) and (getSettingValue("authentification_obli")==1))
{
    showAccessDenied($day, $month, $year, $area,$back);
    exit();
}
if(authUserAccesArea(getUserName(), $area)==0)
{
    showAccessDenied($day, $month, $year, $area,$back);
    exit();
}

# 3-value compare: Returns result of compare as "< " "= " or "> ".
function cmp3($a, $b)
{
    if ($a < $b) return "< ";
    if ($a == $b) return "= ";
    return "> ";
}

// On v�rifie une fois par jour si le d�lai de confirmation des r�servations est d�pass�
// Si oui, les r�servations concern�es sont supprim�es et un mail automatique est envoy�.
// On v�rifie une fois par jour que les ressources ont �t� rendue en fin de r�servation
// Si non, une notification email est envoy�e
if (getSettingValue("verif_reservation_auto")==0) {
    verify_confirm_reservation();
    verify_retard_reservation();
}

# print the page header
print_header($day, $month, $year, $area, $type_session);
# Month view start time. This ignores morningstarts/eveningends because it
# doesn't make sense to not show all entries for the day, and it messes
# things up when entries cross midnight.
$month_start = mktime(0, 0, 0, $month, 1, $year);

# What column the month starts in: 0 means $weekstarts weekday.
$weekday_start = (date("w", $month_start) - $weekstarts + 7) % 7;

$days_in_month = date("t", $month_start);

$month_end = mktime(23, 59, 59, $month, $days_in_month, $year);
if ($enable_periods=='y') {
    $resolution = 60;
    $morningstarts = 12;
    $eveningends = 12;
    $eveningends_minutes = count($periods_name)-1;
}

$this_area_name = "";
$this_room_name = "";

// Si format imprimable ($_GET['pview'] = 1), on n'affiche pas cette partie
if ($_GET['pview'] != 1) {
    #Table avec areas, rooms, minicals.
    echo "<table width=\"100%\" cellspacing=\"15\" border=\"0\"><tr>";

    if (isset($_SESSION['default_list_type']) or (getSettingValue("authentification_obli")==1)) {
        $area_list_format = $_SESSION['default_list_type'];
    } else {
        $area_list_format = getSettingValue("area_list_format");
    }

    # S�lection des sites, domaines et ressources
    if ($area_list_format != "list") {
        # S�lection sous la forme de listes d�roulantes
        echo "<td>\n";
        echo make_site_select_html('month_all2.php',$id_site,$year,$month,$day,getUserName());
        echo make_area_select_html('month_all2.php',$id_site, $area, $year, $month, $day, getUserName());
        echo make_room_select_html('month',$area, $room, $year, $month, $day);
        echo "</td>\n";
    } else {
        # S�lection sous la forme de listes
        echo "<td>\n";
        echo make_site_list_html('month_all2.php',$id_site,$year,$month,$day,getUserName());
        echo "</td><td>";
        echo make_area_list_html('month_all2.php',$id_site, $area, $year, $month, $day, getUserName());
        echo "</td>\n<td>\n";
        make_room_list_html('month.php', $area, $room, $year, $month, $day);
        echo "</td>\n\n";
    }

    #Draw the three month calendars
    minicals($year, $month, $day, $area, '', 'month_all2');
    echo "</tr></table>\n";
}

$this_area_name = grr_sql_query1("select area_name from ".TABLE_PREFIX."_area where id=$area");


 echo "<div class=\"titre_planning\">" . ucfirst(utf8_strftime("%B %Y", $month_start))
  . "<br />".ucfirst($this_area_name)." - ".get_vocab("all_areas");
  if ($_GET['pview'] != 1) echo " <a href=\"month_all.php?year=$year&amp;month=$month&amp;area=$area\"><img src=\"img_grr/change_view.png\" alt=\"".get_vocab("change_view")."\" title=\"".get_vocab("change_view")."\" class=\"image\" /></a>";
 echo "</div>\n";


# Show Go to month before and after links
#y? are year and month of the previous month.
#t? are year and month of the next month.

$i= mktime(0,0,0,$month-1,1,$year);
$yy = date("Y",$i);
$ym = date("n",$i);

$i= mktime(0,0,0,$month+1,1,$year);
$ty = date("Y",$i);
$tm = date("n",$i);
// Si format imprimable ($_GET['pview'] = 1), on n'affiche pas cette partie
if ($_GET['pview'] != 1) {
    echo "<table width=\"980\"><tr><td>
      <a href=\"month_all2.php?year=$yy&amp;month=$ym&amp;area=$area\">
      &lt;&lt; ".get_vocab("monthbefore")."</a></td>
      <td>&nbsp;</td>
      <td align=\"right\"><a href=\"month_all2.php?year=$ty&amp;month=$tm&amp;area=$area\">
      ".get_vocab("monthafter")." &gt;&gt;</a></td></tr></table>";
}

if ($debug_flag)
    echo "<p>DEBUG: month=$month year=$year start=$weekday_start range=$month_start:$month_end\n";

# Used below: localized "all day" text but with non-breaking spaces:
$all_day = preg_replace("/ /", "&nbsp;", get_vocab("all_day"));

#Get all meetings for this month in the room that we care about
# row[0] = Start time
# row[1] = End time
# row[2] = Entry ID
# row[3] = Entry name (brief description)
# row[4] = beneficiaire of the booking
# row[5] = Nom de la ressource
# row[6] = statut
# row[7] = Description compl�te



$sql = "SELECT start_time, end_time,".TABLE_PREFIX."_entry.id, name, beneficiaire, room_name, statut_entry, ".TABLE_PREFIX."_entry.description, ".TABLE_PREFIX."_entry.option_reservation, ".TABLE_PREFIX."_room.delais_option_reservation, type, ".TABLE_PREFIX."_entry.moderate
   FROM ".TABLE_PREFIX."_entry inner join ".TABLE_PREFIX."_room on ".TABLE_PREFIX."_entry.room_id=".TABLE_PREFIX."_room.id
   WHERE (start_time <= $month_end AND end_time > $month_start and area_id='".$area."')
   ORDER by start_time, end_time, ".TABLE_PREFIX."_room.room_name";

# Build an array of information about each day in the month.
# The information is stored as:
#  d[monthday]["id"][] = ID of each entry, for linking.
#  d[monthday]["data"][] = "start-stop" times of each entry.

$res = grr_sql_query($sql);
if (! $res) echo grr_sql_error();
else {
if (grr_sql_count($res) == 0) {
    echo "<div class=\"titre_planning\">".get_vocab("nothing_found")."</div></body></html>";
    die();
}

for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
{
    $sql_beneficiaire = "SELECT prenom, nom FROM ".TABLE_PREFIX."_utilisateurs WHERE login = '$row[4]'";
    $res_beneficiaire = grr_sql_query($sql_beneficiaire);
    if ($res_beneficiaire) $row_user = grr_sql_row($res_beneficiaire, 0);

    if ($debug_flag)
        echo "<br />DEBUG: result $i, id $row[2], starts $row[0], ends $row[1]\n";

    # Fill in data for each day during the month that this meeting covers.
    # Note: int casts on database rows for min and max is needed for PHP3.
    $t = max((int)$row[0], $month_start);
    $end_t = min((int)$row[1], $month_end);
    $day_num = date("j", $t);
    if ($enable_periods == 'y')
        $midnight = mktime(12,0,0,$month,$day_num,$year);
    else
        $midnight = mktime(0, 0, 0, $month, $day_num, $year);
    while ($t < $end_t)
    {
        if ($debug_flag) echo "<br />DEBUG: Entry $row[2] day $day_num\n";
        $d[$day_num]["id"][] = $row[2];
        // Info-bulle
        $temp = "";
        if (getSettingValue("display_info_bulle") == 1)
            $temp = get_vocab("reservee au nom de").$row_user[0]." ".$row_user[1];
        else if (getSettingValue("display_info_bulle") == 2)
            $temp = $row[7];

        if ($temp != "") $temp = " - ".$temp;
        $d[$day_num]["who1"][] = affichage_lien_resa_planning($row[3],$row[2]);
        $d[$day_num]["room"][]=$row[5] ;
        $d[$day_num]["res"][] = $row[6];
        $d[$day_num]["color"][] = $row[10];
        if ($row[9] > 0)
            $d[$day_num]["option_reser"][] = $row[8];
        else
            $d[$day_num]["option_reser"][] = -1;
        $d[$day_num]["moderation"][] = $row[11];

        $midnight_tonight = $midnight + 86400;

        # Describe the start and end time, accounting for "all day"
        # and for entries starting before/ending after today.
        # There are 9 cases, for start time < = or > midnight this morning,
        # and end time < = or > midnight tonight.
        # Use ~ (not -) to separate the start and stop times, because MSIE
        # will incorrectly line break after a -.
        $all_day2 = preg_replace("/&nbsp;/", " ", $all_day);
        if ($enable_periods == 'y') {
            $start_str = preg_replace("/&nbsp;/", " ", period_time_string($row[0]));
            $end_str   = preg_replace("/&nbsp;/", " ", period_time_string($row[1], -1));
            switch (cmp3($row[0], $midnight) . cmp3($row[1], $midnight_tonight))
            {
            case "> < ":         # Starts after midnight, ends before midnight
            case "= < ":         # Starts at midnight, ends before midnight
                    if ($start_str == $end_str)
                        $d[$day_num]["data"][] = $start_str." - ".$row[3].$temp;
                    else
                        $d[$day_num]["data"][] = $start_str . "~" . $end_str." - ".$row[3].$temp;
                    break;
            case "> = ":         # Starts after midnight, ends at midnight
                    $d[$day_num]["data"][] = $start_str . "~24:00"." - ".$row[3].$temp;
                    break;
            case "> > ":         # Starts after midnight, continues tomorrow
                    $d[$day_num]["data"][] = $start_str . "~====>"." - ".$row[3].$temp;
                    break;
            case "= = ":         # Starts at midnight, ends at midnight
                    $d[$day_num]["data"][] = $all_day2.$temp;
                    break;
            case "= > ":         # Starts at midnight, continues tomorrow
                    $d[$day_num]["data"][] = $all_day2 . "====>"." - ".$row[3].$temp;
                    break;
            case "< < ":         # Starts before today, ends before midnight
                    $d[$day_num]["data"][] = "<====~" . $end_str." - ".$row[3].$temp;
                    break;
            case "< = ":         # Starts before today, ends at midnight
                    $d[$day_num]["data"][] = "<====" . $all_day2." - ".$row[3].$temp;
                    break;
            case "< > ":         # Starts before today, continues tomorrow
                    $d[$day_num]["data"][] = "<====" . $all_day2 . "====>"." - ".$row[3].$temp;
                    break;
            }
        } else {
          switch (cmp3($row[0], $midnight) . cmp3($row[1], $midnight_tonight))
          {
            case "> < ":         # Starts after midnight, ends before midnight
            case "= < ":         # Starts at midnight, ends before midnight
                $d[$day_num]["data"][] = date(hour_min_format(), $row[0]) . "~" . date(hour_min_format(), $row[1])." - ".$row[3].$temp;
                break;
            case "> = ":         # Starts after midnight, ends at midnight
                $d[$day_num]["data"][] = date(hour_min_format(), $row[0]) . "~24:00"." - ".$row[3].$temp;
                break;
            case "> > ":         # Starts after midnight, continues tomorrow
                $d[$day_num]["data"][] = date(hour_min_format(), $row[0]) . "~====>"." - ".$row[3].$temp;
                break;
            case "= = ":         # Starts at midnight, ends at midnight
                $d[$day_num]["data"][] = $all_day2.$temp;
                break;
            case "= > ":         # Starts at midnight, continues tomorrow
                $d[$day_num]["data"][] = $all_day2 . "====>"." - ".$row[3].$temp;
                break;
            case "< < ":         # Starts before today, ends before midnight
                $d[$day_num]["data"][] = "<====~" . date(hour_min_format(), $row[1])." - ".$row[3].$temp;
                break;
            case "< = ":         # Starts before today, ends at midnight
                $d[$day_num]["data"][] = "<====" . $all_day2." - ".$row[3].$temp;
                break;
            case "< > ":         # Starts before today, continues tomorrow
                $d[$day_num]["data"][] = "<====" . $all_day2 . "====>"." - ".$row[3].$temp;
                break;
          }
        }

        # Only if end time > midnight does the loop continue for the next day.
        if ($row[1] <= $midnight_tonight) break;
        $day_num++;
        $t = $midnight = $midnight_tonight;
    }
}
}
if ($debug_flag)
{
    echo "<p>DEBUG: Array of month day data:<p><pre>\n";
    for ($i = 1; $i <= $days_in_month; $i++)
    {
        if (isset($d[$i]["id"]))
        {
            $n = count($d[$i]["id"]);
            echo "Day $i has $n entries:\n";
            for ($j = 0; $j < $n; $j++)
                echo "  ID: " . $d[$i]["id"][$j] .
                    " Data: " . $d[$i]["data"][$j] . "\n";
        }
    }
    echo "</pre>\n";
}
$weekcol=0;
echo "<table border=\"2\" width=\"980\">\n";
# Weekday name header row:
/*echo "<tr><th width=10px>&nbsp;</th>";
for ($weekcol = 0; $weekcol < 5; $weekcol++)
{
    echo "<th colspan=7 width=\"170px\">sem ".date("W",mktime(0,0,0,$month,$day,$year))."</th>";
    //echo "<th width=\"14%\">" . day_name(($weekcol + $weekstarts)%7) . "</th>";
    $day = $day+7;
}
echo "</tr>\n";*/


$sql = "select room_name, capacity, id, description from ".TABLE_PREFIX."_room where area_id=$area order by order_display,room_name";
$res = grr_sql_query($sql);

// D�but affichage de la premi�re ligne
echo "<tr><th></th>\n";
//Corrige un bug avec certains fuseaux horaires (par exemple GMT-05:00 celui du Qu�bec) :
//plusieurs mois d�butent par le dernier jours du mois pr�c�dent.
//En changeant "gmmktime" par "mktime" le bug est corrig�
//$t2=gmmktime(0,0,0,$month,1,$year);
$t2=mktime(0,0,0,$month,1,$year);
for ($k = 0; $k<$days_in_month; $k++) {
    $cday = date("j", $t2);
    $cweek = date("w", $t2);
    $name_day = ucfirst(utf8_strftime("%a %d", $t2));
    $temp = mktime(0,0,0,$month,$cday,$year);
  	$jour_cycle = grr_sql_query1("SELECT Jours FROM ".TABLE_PREFIX."_calendrier_jours_cycle WHERE DAY='$temp'");
    $t2 += 86400;
    // On inscrit le num�ro du mois dans la deuxi�me ligne
    if ($display_day[$cweek]==1)
		{
        echo "<th valign=\"top\" >$name_day";
    if (getSettingValue("jours_cycles_actif") == "Oui" and intval($jour_cycle)>-1)
        if (intval($jour_cycle)>0)
            echo "<br /></><i> ".ucfirst(substr(get_vocab("rep_type_6"),0,1)).$jour_cycle."</i>";
        else {
	  			if (strlen($jour_cycle)>5)
					    $jour_cycle = substr($jour_cycle,0,3)."..";
          echo "<br /></><i>".$jour_cycle."</i>";
        }
		echo "</th>\n";
	}
}
echo "</tr>";
// Fin affichage de la premi�re ligne

$li=0;
for ($ir = 0; ($row = grr_sql_row($res, $ir)); $ir++)
{
   // calcul de l'acc�s � la ressource en fonction du niveau de l'utilisateur
   $verif_acces_ressource = verif_acces_ressource(getUserName(), $row[2]);
   if ($verif_acces_ressource) {  // on n'affiche pas toutes les ressources

    // Calcul du niveau d'acc�s aux fiche de r�servation d�taill�es des ressources
    $acces_fiche_reservation = verif_acces_fiche_reservation(getUserName(), $row[2]);

    echo "<tr><th>" . htmlspecialchars($row[0]) ."</th>\n";
    $li++;
    //Corrige un bug avec certains fuseaux horaires (par exemple GMT-05:00 celui du Qu�bec) :
    //plusieurs mois d�butent par le dernier jours du mois pr�c�dent.
    //En changeant "gmmktime" par "mktime" le bug est corrig�
    //$t2=gmmktime(0,0,0,$month,1,$year);
    $t2=mktime(0,0,0,$month,1,$year);
    for ($k = 0; $k<$days_in_month; $k++)
      {
        $cday = date("j", $t2);
        $cweek = date("w", $t2);
        $t2 += 86400;
       if ($display_day[$cweek]==1) { // D�but condition "on n'affiche pas tous les jours de la semaine"
        echo "<td valign=\"top\" class=\"cell_month\">&nbsp;";
        if (est_hors_reservation(mktime(0,0,0,$month,$cday,$year),$area)) {
            echo "<div class=\"empty_cell\">";
            echo "<img src=\"img_grr/stop.png\" alt=\"".get_vocab("reservation_impossible")."\"  title=\"".get_vocab("reservation_impossible")."\" width=\"16\" height=\"16\" class=\"".$class_image."\"  /></div>\n";
        } else {
            # Anything to display for this day?
            if (isset($d[$cday]["id"][0])) {
                $n = count($d[$cday]["id"]);
                # Show the start/stop times, 2 per line, linked to view_entry.
                # If there are 12 or fewer, show them, else show 11 and "...".
                for ($i = 0; $i < $n; $i++) {
                    if ($i == 11 && $n > 12) {
                        echo " ...\n";
                        break;
                    }
                    for ($i = 0; $i < $n; $i++) {
                      if ($d[$cday]["room"][$i]==$row[0]) {
                        #if ($i > 0 && $i % 2 == 0) echo "<br />"; else echo " ";
                        echo "\n<br /><table width='100%'><tr>";
                        tdcell($d[$cday]["color"][$i]);
                        if ($d[$cday]["res"][$i]!='-') echo "&nbsp;<img src=\"img_grr/buzy.png\" alt=\"".get_vocab("ressource actuellement empruntee")."\" title=\"".get_vocab("ressource actuellement empruntee")."\" width=\"20\" height=\"20\" class=\"image\" />&nbsp;\n";
                        // si la r�servation est � confirmer, on le signale
                        if ((isset($d[$cday]["option_reser"][$i])) and ($d[$cday]["option_reser"][$i]!=-1)) echo "&nbsp;<img src=\"img_grr/small_flag.png\" alt=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."\" title=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."&nbsp;".time_date_string_jma($d[$cday]["option_reser"][$i],$dformat)."\" width=\"20\" height=\"20\" class=\"image\" />&nbsp;\n";
                        // si la r�servation est � mod�rer, on le signale
                        if ((isset($d[$cday]["moderation"][$i])) and ($d[$cday]["moderation"][$i]==1))
                            echo "&nbsp;<img src=\"img_grr/flag_moderation.png\" alt=\"".get_vocab("en_attente_moderation")."\" title=\"".get_vocab("en_attente_moderation")."\" class=\"image\" />&nbsp;\n";
                        echo "<span class=\"small_planning\">";
                        if ($acces_fiche_reservation)
                            echo "<a title=\"".htmlspecialchars($d[$cday]["data"][$i])."\" href=\"view_entry.php?id=" . $d[$cday]["id"][$i]."&amp;page=month\">"
                            .$d[$cday]["who1"][$i]{0}
                            . "</a>";
                        else
                            echo $d[$cday]["who1"][$i]{0};
                        echo "</span></td></tr></table>";
                      }
                    }

               }

            }
        }
        echo "</td>\n";
    } // fin condition "on n'affiche pas tous les jours de la semaine"
//    if (++$weekcol == 7) $weekcol = 0;

    }
    echo "</tr>";
  }
}

/*# Skip from end of month to end of week:
if ($weekcol > 0) for (; $weekcol < 7; $weekcol++)
{
    echo "<td class=\"cell_month_o\" >&nbsp;</td>\n";
}*/
echo "</table>\n";
show_colour_key($area);
// Affichage d'un message pop-up
affiche_pop_up(get_vocab("message_records"),"user");
include "include/trailer.inc.php";
?>