<?php
#########################################################################
#                                  year.php                             #
#                                                                       #
#   Interface d'accueil avec affichage par mois sur plusieurs mois      #
#             des r�servation de toutes les ressources d'un domaine     #
#            Derni�re modification : 15/11/2007                         #
#                                                                       #
#########################################################################
/*
 * Copyright 2003-2005 Laurent Delineau
 * D'apr�s http://mrbs.sourceforge.net/
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
include "include/mincals.inc.php";
$grr_script_name = "year.php";
// Settings
require_once("./include/settings.inc.php");
//Chargement des valeurs de la table settingS
if (!loadSettings())
    die("Erreur chargement settings");

// Session related functions
require_once("./include/session.inc.php");
// Resume session
if ((!grr_resumeSession())and (getSettingValue("authentification_obli")==1)) {
    header("Location: ./logout.php?auto=1");
    die();
};

// Param�tres langage
include "include/language.inc.php";

// On affiche le lien "format imprimable" en bas de la page
$affiche_pview = '1';
if (!isset($_GET['pview'])) $_GET['pview'] = 0; else $_GET['pview'] = 1;

$from_month = isset($_GET["from_month"]) ? $_GET["from_month"] : NULL;
$from_year = isset($_GET["from_year"]) ? $_GET["from_year"] : NULL;
$to_month = isset($_GET["to_month"]) ? $_GET["to_month"] : NULL;
$to_year = isset($_GET["to_year"]) ? $_GET["to_year"] : NULL;

$day = 1;
$date_now = mktime();
# Default parameters:
if (empty($debug_flag)) $debug_flag = 0;
if (empty($from_month) || empty($from_year) || !checkdate($from_month, 1, $from_year))
{
    if ($date_now < getSettingValue('begin_bookings'))
        $date_ = getSettingValue('begin_bookings');
    else if ($date_now > getSettingValue('end_bookings'))
        $date_ = getSettingValue('end_bookings');
    else
        $date_ = $date_now;
    $day   = date('d',$date_);
    $from_month = date('m',$date_);
    $from_year  = date('Y',$date_);
} else {
    $date_ = mktime(0, 0, 0, $from_month, $day, $from_year);
    if ($date_ < getSettingValue('begin_bookings'))
        $date_ = getSettingValue('begin_bookings');
    else if ($date_ > getSettingValue('end_bookings'))
        $date_ = getSettingValue('end_bookings');
    $day   = date('d',$date_);
    $from_month = date('m',$date_);
    $from_year  = date('Y',$date_);
}
if (empty($to_month) || empty($to_year) || !checkdate($to_month, 1, $to_year))
{
    $to_month = $from_month;
    $to_year  = $from_year;
} else {
    $date_ = mktime(0, 0, 0, $to_month, 1, $to_year);
    if ($date_ < getSettingValue('begin_bookings'))
        $date_ = getSettingValue('begin_bookings');
    else if ($date_ > getSettingValue('end_bookings'))
        $date_ = getSettingValue('end_bookings');
    $to_month = date('m',$date_);
    $to_year  = date('Y',$date_);
}


if ((getSettingValue("authentification_obli")==0) and (!isset($_SESSION['login']))) {
    $session_login = '';
    $session_statut = '';
    $type_session = "no_session";
} else {
    $session_login = $_SESSION['login'];
    $session_statut = $_SESSION['statut'];
    $type_session = "with_session";
}

$back = '';
if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);

if (check_begin_end_bookings($day, $from_month, $from_year))
{
    showNoBookings($day, $from_month, $from_year, $area,$back,$type_session);
    exit();
}


if((authGetUserLevel(getUserName(),-1) < 1) and (getSettingValue("authentification_obli")==1))
{
    showAccessDenied($day, $from_month, $from_year, $area,$back);
    exit();
}
if(authUserAccesArea($session_login, $area)==0)
{
    showAccessDenied($day, $from_month, $from_year, $area,$back);
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
print_header($day, $from_month, $from_year, $area, $type_session);

// Affichage d'un message pop-up
affiche_pop_up(get_vocab("message_records"),"user");

if (empty($area))
    $area = get_default_area();
if (empty($room))
    $room = grr_sql_query1("select min(id) from grr_room where area_id=$area");
# Note $room will be -1 if there are no rooms; this is checked for below.

// R�cup�ration des donn�es concernant l'affichage du planning du domaine
get_planning_area_values($area);

# Month view start time. This ignores morningstarts/eveningends because it
# doesn't make sense to not show all entries for the day, and it messes
# things up when entries cross midnight.
$month_start = mktime(0, 0, 0, $from_month, 1, $from_year);

# What column the month starts in: 0 means $weekstarts weekday.
$weekday_start = (date("w", $month_start) - $weekstarts + 7) % 7;

$days_in_to_month = date("t", $to_month);

$month_end = mktime(23, 59, 59, $to_month, $days_in_to_month, $to_year);
if ($enable_periods=='y') {
    $resolution = 60;
    $morningstarts = 12;
    $eveningends = 12;
    $eveningends_minutes = count($periods_name)-1;
}

// Si format imprimable ($_GET['pview'] = 1), on n'affiche pas cette partie
if ($_GET['pview'] != 1) {
    echo "<table width=\"100%\" cellspacing=\"15\" border=\"0\"><tr><td>";

    if (isset($_SESSION['default_list_type']) or (getSettingValue("authentification_obli")==1)) {
        $area_list_format = $_SESSION['default_list_type'];
    } else {
        $area_list_format = getSettingValue("area_list_format");
    }
    # show either a select box or the normal html list
    if ($area_list_format != "list") {
        echo make_area_select_html('year.php', $area, $from_year, $from_month, $day, $session_login); # from functions.inc.php
    } else {
        echo "<table cellspacing=15><tr><td>";
        echo make_area_list_html('year.php', $area, $from_year, $from_month, $day, $session_login); # from functions.inc.php
        #Montre toutes les rooms du domaine affich�
        echo "</td></tr></table>";
    }
    echo "</td>\n";
    echo "<td>\n";
    echo "\n<form method='get' action=year.php>";
    echo "<table border=\"0\">\n";

    echo "<tr><td>".get_vocab("report_start").get_vocab("deux_points")."</td>";
    echo "<td>";
    echo genDateSelector("from_", "", $from_month, $from_year,"");
    echo "</td></tr>";
    echo "<tr><td>".get_vocab("report_end").get_vocab("deux_points");
    echo "</td><td>\n";
    echo genDateSelector("to_", "", $to_month, $to_year,"");
    echo "</td></tr>\n";
    echo "<tr><td>\n";
    echo "<input type=\"hidden\" name=\"area\" value=\"$area\" />\n";

    echo "<input type=\"submit\" name=\"valider\" value=\"".$vocab["goto"]."\" /></td><td>&nbsp;</td></tr>\n";
    echo "</table>\n";

    echo "</form></td>\n";

	echo '<td><a title="'.htmlspecialchars(get_vocab('back')).'" href="'.page_accueil('no').'">'.$vocab['back'].'</a></td>';

    echo "</tr></table>\n";
}
$this_area_name = grr_sql_query1("select area_name from grr_area where id=$area");

# Don't continue if this area has no rooms:
if ($room <= 0)
{
    echo "<h1>".get_vocab("no_rooms_for_area")."</h1>";
    include "include/trailer.inc.php";
    exit;
}
echo "<h2 align=center>".ucfirst($this_area_name)." - ".get_vocab("all_areas")." </h2>\n";

# Used below: localized "all day" text but with non-breaking spaces:
$all_day = ereg_replace(" ", "&nbsp;", get_vocab("all_day"));

#Get all meetings for this month in the room that we care about
# row[0] = Start time
# row[1] = End time
# row[2] = Entry ID
# row[3] = Entry name (brief description)
# row[4] = beneficiaire of the booking
# row[5] = Nom de la ressource
# row[6] = statut
# row[7] = Description compl�te
$sql = "SELECT start_time, end_time,grr_entry.id, name, beneficiaire, room_name, statut_entry, grr_entry.description, grr_entry.option_reservation, grr_room.delais_option_reservation, type, grr_entry.moderate, beneficiaire_ext
   FROM grr_entry inner join grr_room on grr_entry.room_id=grr_room.id
   WHERE (start_time <= $month_end AND end_time > $month_start and area_id='".$area."')
   ORDER by start_time, end_time, grr_room.room_name";

# Build an array of information about each day in the month.
# The information is stored as:
#  d[monthday]["id"][] = ID of each entry, for linking.
#  d[monthday]["data"][] = "start-stop" times of each entry.

$res = grr_sql_query($sql);
if (! $res) echo grr_sql_error();
else {
if (grr_sql_count($res) == 0) {
    echo "<center><h2>".get_vocab("nothing_found")."</h2></center></body></html>";
    die();
}

for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
{
    # Fill in data for each day during the month that this meeting covers.
    # Note: int casts on database rows for min and max is needed for PHP3.
    $t = max((int)$row[0], $month_start);
    $end_t = min((int)$row[1], $month_end);
    $day_num = date("j", $t);
    $month_num = date("m", $t);
    $year_num  = date("Y", $t);

    if ($enable_periods == 'y')
        $midnight = mktime(12,0,0,$month_num,$day_num,$year_num);
    else
        $midnight = mktime(0, 0, 0, $month_num, $day_num, $year_num);
    while ($t < $end_t)
    {
        $d[$day_num][$month_num][$year_num]["id"][] = $row[2];
        // Info-bulle
        $temp = "";
        if (getSettingValue("display_info_bulle") == 1)
            $temp = get_vocab("reservee au nom de").affiche_nom_prenom_email($row[4],$row[12],"nomail");
        else if (getSettingValue("display_info_bulle") == 2)
            $temp = $row[7];

        if ($temp != "") $temp = " - ".$temp;
        $d[$day_num][$month_num][$year_num]["who1"][] = affichage_lien_resa_planning($row[3],$row[2]);
        $d[$day_num][$month_num][$year_num]["room"][]=$row[5] ;
        $d[$day_num][$month_num][$year_num]["res"][] = $row[6];
        $d[$day_num][$month_num][$year_num]["color"][] = $row[10];
        if ($row[9] > 0)
            $d[$day_num][$month_num][$year_num]["option_reser"][] = $row[8];
        else
            $d[$day_num][$month_num][$year_num]["option_reser"][] = -1;
        $d[$day_num][$month_num][$year_num]["moderation"][] = $row[11];

        $midnight_tonight = $midnight + 86400;

        # Describe the start and end time, accounting for "all day"
        # and for entries starting before/ending after today.
        # There are 9 cases, for start time < = or > midnight this morning,
        # and end time < = or > midnight tonight.
        # Use ~ (not -) to separate the start and stop times, because MSIE
        # will incorrectly line break after a -.
        $all_day2 = ereg_replace("&nbsp;", " ", $all_day);
        if ($enable_periods == 'y') {
            $start_str = ereg_replace("&nbsp;", " ", period_time_string($row[0]));
            $end_str   = ereg_replace("&nbsp;", " ", period_time_string($row[1], -1));
            switch (cmp3($row[0], $midnight) . cmp3($row[1], $midnight_tonight))
            {
            case "> < ":         # Starts after midnight, ends before midnight
            case "= < ":         # Starts at midnight, ends before midnight
                    if ($start_str == $end_str)
                        $d[$day_num][$month_num][$year_num]["data"][] = $start_str." - ".$row[3].$temp;
                    else
                        $d[$day_num][$month_num][$year_num]["data"][] = $start_str . "~" . $end_str." - ".$row[3].$temp;
                    break;
            case "> = ":         # Starts after midnight, ends at midnight
                    $d[$day_num][$month_num][$year_num]["data"][] = $start_str . "~24:00"." - ".$row[3].$temp;
                    break;
            case "> > ":         # Starts after midnight, continues tomorrow
                    $d[$day_num][$month_num][$year_num]["data"][] = $start_str . "~====>"." - ".$row[3].$temp;
                    break;
            case "= = ":         # Starts at midnight, ends at midnight
                    $d[$day_num][$month_num][$year_num]["data"][] = $all_day2.$temp;
                    break;
            case "= > ":         # Starts at midnight, continues tomorrow
                    $d[$day_num][$month_num][$year_num]["data"][] = $all_day2 . "====>"." - ".$row[3].$temp;
                    break;
            case "< < ":         # Starts before today, ends before midnight
                    $d[$day_num][$month_num][$year_num]["data"][] = "<====~" . $end_str." - ".$row[3].$temp;
                    break;
            case "< = ":         # Starts before today, ends at midnight
                    $d[$day_num][$month_num][$year_num]["data"][] = "<====" . $all_day2." - ".$row[3].$temp;
                    break;
            case "< > ":         # Starts before today, continues tomorrow
                    $d[$day_num][$month_num][$year_num]["data"][] = "<====" . $all_day2 . "====>"." - ".$row[3].$temp;
                    break;
            }
        } else {
          switch (cmp3($row[0], $midnight) . cmp3($row[1], $midnight_tonight))
          {
            case "> < ":         # Starts after midnight, ends before midnight
            case "= < ":         # Starts at midnight, ends before midnight
                $d[$day_num][$month_num][$year_num]["data"][] = date(hour_min_format(), $row[0]) . "~" . date(hour_min_format(), $row[1])." - ".$row[3].$temp;
                break;
            case "> = ":         # Starts after midnight, ends at midnight
                $d[$day_num][$month_num][$year_num]["data"][] = date(hour_min_format(), $row[0]) . "~24:00"." - ".$row[3].$temp;
                break;
            case "> > ":         # Starts after midnight, continues tomorrow
                $d[$day_num][$month_num][$year_num]["data"][] = date(hour_min_format(), $row[0]) . "~====>"." - ".$row[3].$temp;
                break;
            case "= = ":         # Starts at midnight, ends at midnight
                $d[$day_num][$month_num][$year_num]["data"][] = $all_day2.$temp;
                break;
            case "= > ":         # Starts at midnight, continues tomorrow
                $d[$day_num][$month_num][$year_num]["data"][] = $all_day2 . "====>"." - ".$row[3].$temp;
                break;
            case "< < ":         # Starts before today, ends before midnight
                $d[$day_num][$month_num][$year_num]["data"][] = "<====~" . date(hour_min_format(), $row[1])." - ".$row[3].$temp;
                break;
            case "< = ":         # Starts before today, ends at midnight
                $d[$day_num][$month_num][$year_num]["data"][] = "<====" . $all_day2." - ".$row[3].$temp;
                break;
            case "< > ":         # Starts before today, continues tomorrow
                $d[$day_num][$month_num][$year_num]["data"][] = "<====" . $all_day2 . "====>"." - ".$row[3].$temp;
                break;
          }
        }

        # Only if end time > midnight does the loop continue for the next day.
        if ($row[1] <= $midnight_tonight) break;
        //$day_num++;

        $t = $midnight = $midnight_tonight;
        $day_num = date("j", $t);
        $month_num = date("m", $t);
        $year_num  = date("Y", $t);
    }
}
}

// Boucle sur les mois
$month_indice =  $month_start;

while ($month_indice < $month_end) {
$month_num = date("m", $month_indice);
$year_num  = date("Y", $month_indice);
$days_in_month = date("t", $month_indice);

$weekcol=0;

echo "<h2 align=center>" . ucfirst(utf8_strftime("%B %Y", $month_indice)). "</h2>\n";

echo "<table border=2>\n";
$sql = "select room_name, capacity, id, description from grr_room where area_id=$area order by order_display,room_name";
$res = grr_sql_query($sql);

// D�but affichage de la premi�re ligne
echo "<tr><th></th>\n";
//Corrige un bug avec certains fuseaux horaires (par exemple GMT-05:00 celui du Qu�bec) :
//plusieurs mois d�butent par le dernier jours du mois pr�c�dent.
//En changeant "gmmktime" par "mktime" le bug est corrig�
//$t2=gmmktime(0,0,0,$month_num,1,$year_num);
$t2=mktime(0,0,0,$month_num,1,$year_num);
for ($k = 0; $k<$days_in_month; $k++) {
    $cday = date("j", $t2);
    $cmonth =date("m", $t2);
    $cweek = date("w", $t2);
    $cyear = date("Y", $t2);
    $name_day = ucfirst(utf8_strftime("%a<br />%d", $t2));
    $temp = mktime(0,0,0,$cmonth,$cday,$cyear);
	$jour_cycle = grr_sql_query1("SELECT Jours FROM grr_calendrier_jours_cycle WHERE DAY='$temp'");
    $t2 += 86400;
    // On inscrit le num�ro du mois dans la deuxi�me ligne
    if ($display_day[$cweek]==1) {
        echo "<td valign=top height=50 class=\"cell_month\"><center><div class=\"monthday\"><a title=\"".htmlspecialchars(get_vocab("see_all_the_rooms_for_the_day"))."\"   href=\"day.php?year=$year_num&amp;month=$month_num&amp;day=$cday&amp;area=$area\">$name_day</a>";
        if (getSettingValue("jours_cycles_actif") == "Oui" and intval($jour_cycle)>-1)
            if (intval($jour_cycle)>0)
                echo "<br /><b><i>".ucfirst(substr(get_vocab("rep_type_6"),0,1)).$jour_cycle."</i></b>";
            else {
			    if (strlen($jour_cycle)>5)
				    $jour_cycle = substr($jour_cycle,0,3)."..";
                echo "<br /><b><i>".$jour_cycle."</i></b>";
            }
        echo "</div></center>\n";
    }
}
echo "</tr>";
// Fin affichage de la premi�re ligne

$li=0;
for ($ir = 0; ($row = grr_sql_row($res, $ir)); $ir++)
{
    echo "<tr><th>" . htmlspecialchars($row[0]) ."</th>\n";
    $li++;
    //Corrige un bug avec certains fuseaux horaires (par exemple GMT-05:00 celui du Qu�bec) :
    //plusieurs mois d�butent par le dernier jours du mois pr�c�dent.
    //En changeant "gmmktime" par "mktime" le bug est corrig�
    //$t2=gmmktime(0,0,0,$month_num,1,$year_num);
    $t2=mktime(0,0,0,$month_num,1,$year_num);
    for ($k = 0; $k<$days_in_month; $k++)
      {
        $cday = date("j", $t2);
        $cweek = date("w", $t2);
        $t2 += 86400;
       if ($display_day[$cweek]==1) { // D�but condition "on n'affiche pas tous les jours de la semaine"
        echo "<td height=50 valign=top class=\"cell_month\">&nbsp;";
    if (est_hors_reservation(mktime(0,0,0,$month_num,$cday,$year_num)))
            echo "<center><img src=\"img_grr/stop.png\" border=\"0\" alt=\"".get_vocab("reservation_impossible")."\"  title=\"".get_vocab("reservation_impossible")."\" width=\"16\" height=\"16\" class=\"print_image\"  /></center>";


        # Anything to display for this day?
        if (isset($d[$cday][$cmonth][$cyear]["id"][0])) {


        $n = count($d[$cday][$cmonth][$cyear]["id"]);
        # Show the start/stop times, 2 per line, linked to view_entry.
        # If there are 12 or fewer, show them, else show 11 and "...".
        for ($i = 0; $i < $n; $i++)
        {
            if ($i == 11 && $n > 12)
            {
                echo " ...\n";
                break;
            }
        for ($i = 0; $i < $n; $i++) {

        if ($d[$cday][$cmonth][$cyear]["room"][$i]==$row[0]) {
                    #if ($i > 0 && $i % 2 == 0) echo "<br />"; else echo " ";
           echo "\n<br /><table width='100%'><tr>";
           tdcell($d[$cday][$cmonth][$cyear]["color"][$i]);


           if ($d[$cday][$cmonth][$cyear]["res"][$i]!='-') echo "&nbsp;<img src=\"img_grr/buzy.png\" alt=\"".get_vocab("reservation_en_cours")."\" title=\"".get_vocab("reservation_en_cours")."\" width=\"20\" height=\"20\" border=\"0\" />&nbsp;\n";
           // si la r�servation est � confirmer, on le signale
           if ((isset($d[$cday][$cmonth][$cyear]["option_reser"][$i])) and ($d[$cday][$cmonth][$cyear]["option_reser"][$i]!=-1)) echo "&nbsp;<img src=\"img_grr/small_flag.png\" alt=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."\" title=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."&nbsp;".time_date_string_jma($d[$cday][$cmonth][$cyear]["option_reser"][$i],$dformat)."\" width=\"20\" height=\"20\" border=\"0\" />&nbsp;\n";
           // si la r�servation est � mod�rer, on le signale
           if ((isset($d[$cday][$cmonth][$cyear]["moderation"][$i])) and ($d[$cday][$cmonth][$cyear]["moderation"][$i]==1))
               echo "&nbsp;<img src=\"img_grr/flag_moderation.png\" alt=\"".get_vocab("en_attente_moderation")."\" title=\"".get_vocab("en_attente_moderation")."\" border=\"0\" align=\"middle\"/>&nbsp;\n";

            echo "<a title=\"".htmlspecialchars($d[$cday][$cmonth][$cyear]["data"][$i])."\" href=\"view_entry.php?id=" . $d[$cday][$cmonth][$cyear]["id"][$i]."&amp;page=month\">"
                    .htmlspecialchars($d[$cday][$cmonth][$cyear]["who1"][$i]{0})
                    . "</a>"
                    . "</td></tr></table>";
                }
    }


        }


    }
    echo "</td>\n";
    } // fin condition "on n'affiche pas tous les jours de la semaine"
//    if (++$weekcol == 7) $weekcol = 0;

    }
}

echo "</tr></table>\n";

$month_indice = mktime(0, 0, 0, $month_num+1, 2, $year_num);
// Fin de la boucle sur les mois
}

show_colour_key($area);
include "include/trailer.inc.php";
?>