<?php
/**
 * month_all.php
 * Interface d'accueil avec affichage par mois des r�servation de toutes les ressources d'un domaine
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2009-12-02 20:11:07 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: month_all.php,v 1.17 2009-12-02 20:11:07 grr Exp $
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
 * $Log: month_all.php,v $
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
$grr_script_name = "month_all.php";
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
$back = "";
if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);

if ($type_session == "with_session") $_SESSION['type_month_all'] = "month_all";
$type_month_all='month_all';

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
        echo make_site_select_html('month_all.php',$id_site,$year,$month,$day,getUserName());
        echo make_area_select_html('month_all.php',$id_site, $area, $year, $month, $day, getUserName());
        echo make_room_select_html('month',$area, $room, $year, $month, $day);
        echo "</td>\n";
    } else {
        # S�lection sous la forme de listes
        echo "<td>\n";
        echo make_site_list_html('month_all.php',$id_site,$year,$month,$day,getUserName());
        echo "</td><td>";
        echo make_area_list_html('month_all.php',$id_site, $area, $year, $month, $day, getUserName());
        echo "</td>\n<td>\n";
        make_room_list_html('month.php', $area, $room, $year, $month, $day);
        echo "</td>\n\n";
    }


    #Draw the three month calendars
    minicals($year, $month, $day, $area, '', 'month_all');
    echo "</tr></table>\n";
}

$this_area_name = grr_sql_query1("select area_name from ".TABLE_PREFIX."_area where id=$area");


 echo "<div class=\"titre_planning\">" . ucfirst(utf8_strftime("%B %Y", $month_start))
  . "<br />".ucfirst($this_area_name)." - ".get_vocab("all_areas");
 if ($_GET['pview'] != 1) echo " <a href=\"month_all2.php?year=$year&amp;month=$month&amp;area=$area\"><img src=\"img_grr/change_view.png\" alt=\"".get_vocab("change_view")."\" title=\"".get_vocab("change_view")."\" class=\"image\" /></a>";
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
    echo "<table width=\"100%\"><tr><td>
      <a href=\"month_all.php?year=$yy&amp;month=$ym&amp;area=$area\">
      &lt;&lt; ".get_vocab("monthbefore")."</a></td>
      <td>&nbsp;</td>
      <td align=\"right\"><a href=\"month_all.php?year=$ty&amp;month=$tm&amp;area=$area\">
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
# row[6] = Description compl�te
# row[7] = type
# row[8] = Mod�ration
# row[9] = beneficiaire ext�rieur
# row[10] = id de la ressource
$sql = "SELECT start_time, end_time, ".TABLE_PREFIX."_entry.id, name, beneficiaire, room_name, ".TABLE_PREFIX."_entry.description, type, ".TABLE_PREFIX."_entry.moderate, beneficiaire_ext, ".TABLE_PREFIX."_room.id
   FROM ".TABLE_PREFIX."_entry inner join ".TABLE_PREFIX."_room on ".TABLE_PREFIX."_entry.room_id=".TABLE_PREFIX."_room.id
   WHERE (start_time <= $month_end AND end_time > $month_start and area_id='".$area."')
   ORDER by start_time, end_time, ".TABLE_PREFIX."_room.room_name";

# Build an array of information about each day in the month.
# The information is stored as:
#  d[monthday]["id"][] = ID of each entry, for linking.
#  d[monthday]["data"][] = "start-stop" times of each entry.
$res = grr_sql_query($sql);
if (! $res) echo grr_sql_error();
else for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
{
   // calcul de l'acc�s � la ressource en fonction du niveau de l'utilisateur
   $verif_acces_ressource[$row[10]] = verif_acces_ressource(getUserName(), $row[10]);


    // Calcul du niveau d'acc�s aux fiche de r�servation d�taill�es des ressources
    $acces_fiche_reservation[$row[10]] = verif_acces_fiche_reservation(getUserName(), $row[10]);

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
        $d[$day_num]["id_room"][] = $row[10];
        // Info-bulle
        if (getSettingValue("display_info_bulle") == 1)
            $d[$day_num]["who"][] = get_vocab("reservee au nom de").affiche_nom_prenom_email($row[4],$row[9],"nomail");
        else if (getSettingValue("display_info_bulle") == 2)
            $d[$day_num]["who"][] = $row[6];
        else
            $d[$day_num]["who"][] = "";

        $d[$day_num]["who1"][] = affichage_lien_resa_planning($row[3],$row[2]);
        $d[$day_num]["room"][]=$row[5] ;
        $d[$day_num]["color"][] = $row[7];
        $d[$day_num]["description"][] = affichage_resa_planning($row[6],$row[2]);
        $d[$day_num]["moderation"][] = $row[8];

        $midnight_tonight = $midnight + 86400;

        # Describe the start and end time, accounting for "all day"
        # and for entries starting before/ending after today.
        # There are 9 cases, for start time < = or > midnight this morning,
        # and end time < = or > midnight tonight.
        # Use ~ (not -) to separate the start and stop times, because MSIE
        # will incorrectly line break after a -.
        if ($enable_periods == 'y') {
            $start_str = preg_replace("/ /", "&nbsp;", period_time_string($row[0]));
            $end_str   = preg_replace("/ /", "&nbsp;", period_time_string($row[1], -1));
            switch (cmp3($row[0], $midnight) . cmp3($row[1], $midnight_tonight))
            {
            case "> < ":         # Starts after midnight, ends before midnight
            case "= < ":         # Starts at midnight, ends before midnight
                    if ($start_str == $end_str)
                        $d[$day_num]["data"][] = $start_str;
                    else
                        $d[$day_num]["data"][] = $start_str . "~" . $end_str;
                    break;
            case "> = ":         # Starts after midnight, ends at midnight
                    $d[$day_num]["data"][] = $start_str . "~24:00";
                    break;
            case "> > ":         # Starts after midnight, continues tomorrow
                    $d[$day_num]["data"][] = $start_str . "~====&gt;";
                    break;
            case "= = ":         # Starts at midnight, ends at midnight
                    $d[$day_num]["data"][] = $all_day;
                    break;
            case "= > ":         # Starts at midnight, continues tomorrow
                    $d[$day_num]["data"][] = $all_day . "====&gt;";
                    break;
            case "< < ":         # Starts before today, ends before midnight
                    $d[$day_num]["data"][] = "&lt;====~" . $end_str;
                    break;
            case "< = ":         # Starts before today, ends at midnight
                    $d[$day_num]["data"][] = "&lt;====" . $all_day;
                    break;
            case "< > ":         # Starts before today, continues tomorrow
                    $d[$day_num]["data"][] = "&lt;====" . $all_day . "====&gt;";
                    break;
            }
        } else {
          switch (cmp3($row[0], $midnight) . cmp3($row[1], $midnight_tonight))
          {
            case "> < ":         # Starts after midnight, ends before midnight
            case "= < ":         # Starts at midnight, ends before midnight
                $d[$day_num]["data"][] = date(hour_min_format(), $row[0]) . "~" . date(hour_min_format(), $row[1]);
                break;
            case "> = ":         # Starts after midnight, ends at midnight
                $d[$day_num]["data"][] = date(hour_min_format(), $row[0]) . "~24:00";
                break;
            case "> > ":         # Starts after midnight, continues tomorrow
                $d[$day_num]["data"][] = date(hour_min_format(), $row[0]) . "~====&gt;";
                break;
            case "= = ":         # Starts at midnight, ends at midnight
                $d[$day_num]["data"][] = $all_day;
                break;
            case "= > ":         # Starts at midnight, continues tomorrow
                $d[$day_num]["data"][] = $all_day . "====&gt;";
                break;
            case "< < ":         # Starts before today, ends before midnight
                $d[$day_num]["data"][] = "&lt;====~" . date(hour_min_format(), $row[1]);
                break;
            case "< = ":         # Starts before today, ends at midnight
                $d[$day_num]["data"][] = "&lt;====" . $all_day;
                break;
            case "< > ":         # Starts before today, continues tomorrow
                $d[$day_num]["data"][] = "&lt;====" . $all_day . "====&gt;";
                break;
         }
        }

        # Only if end time > midnight does the loop continue for the next day.
        if ($row[1] <= $midnight_tonight) break;
        $day_num++;
        $t = $midnight = $midnight_tonight;

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

// D�but du tableau affichant le planning
echo "<table border=\"2\" width=\"100%\">\n";

// D�but affichage premi�re ligne (intitul� des jours)
echo "<tr>";
for ($weekcol = 0; $weekcol < 7; $weekcol++)
{
    $num_week_day = ($weekcol + $weekstarts)%7;
    if ($display_day[$num_week_day] == 1)  // on n'affiche pas tous les jours de la semaine
        echo "<th style=\"width:14%;\">" . day_name($num_week_day) . "</th>\n";
}
echo "</tr>\n";
// Fin affichage premi�re ligne (intitul� des jours)


// D�but affichage des lignes affichant les r�servations

// On grise les cellules appartenant au mois pr�c�dent
$weekcol = 0;
if ($weekcol != $weekday_start) {
	echo "<tr>";
	for ($weekcol = 0; $weekcol < $weekday_start; $weekcol++)
	{
		$num_week_day = ($weekcol + $weekstarts)%7;
		if ($display_day[$num_week_day] == 1)  // on n'affiche pas tous les jours de la semaine
			echo "<td class=\"cell_month_o\" >&nbsp;</td>\n";
	}
}

// D�but Premi�re boucle sur les jours du mois
for ($cday = 1; $cday <= $days_in_month; $cday++)
{
    $num_week_day = ($weekcol + $weekstarts)%7;
    $t=mktime(0,0,0,$month,$cday,$year);
    $name_day = ucfirst(utf8_strftime("%d", $t));
	$jour_cycle = grr_sql_query1("SELECT Jours FROM ".TABLE_PREFIX."_calendrier_jours_cycle WHERE DAY='$t'");
    if ($weekcol == 0) echo "<tr>\n";
    if ($display_day[$num_week_day] == 1) {// d�but condition "on n'affiche pas tous les jours de la semaine"
    echo "<td valign=\"top\" class=\"cell_month\">";
    // On affiche les jours du mois dans le coin sup�rieur gauche de chaque cellule
    echo "<div class=\"monthday\"><a title=\"".htmlspecialchars(get_vocab("see_all_the_rooms_for_the_day"))."\"   href=\"day.php?year=$year&amp;month=$month&amp;day=$cday&amp;area=$area\">".$name_day;
	    if (getSettingValue("jours_cycles_actif") == "Oui" and intval($jour_cycle)>-1)
        if (intval($jour_cycle)>0)
            echo " - ".get_vocab("rep_type_6")." ".$jour_cycle;
        else
            echo " - ".$jour_cycle;

	echo "</a></div>\n";
    if (est_hors_reservation(mktime(0,0,0,$month,$cday,$year),$area)) {
        echo "<div class=\"empty_cell\">";
        echo "<img src=\"img_grr/stop.png\" alt=\"".get_vocab("reservation_impossible")."\" title=\"".get_vocab("reservation_impossible")."\" width=\"16\" height=\"16\" class=\"".$class_image."\"  /></div>\n";
    } else {

     // Des r�servation � afficher pour ce jour ?
     if (isset($d[$cday]["id"][0]))
     {
        $n = count($d[$cday]["id"]);
        # Show the start/stop times, 2 per line, linked to view_entry.
        # If there are 12 or fewer, show them, else show 11 and "...".
        for ($i = 0; $i < $n; $i++)
        {
          if ($verif_acces_ressource[$d[$cday]["id_room"][$i]]) { // On n'affiche pas les r�servation des ressources non visibles pour l'utilisateur.
            if ($i == 11 && $n > 12)
            {
                echo " ...\n";
                break;
            }
            echo "\n<table width='100%' border='0'><tr>\n";
            tdcell($d[$cday]["color"][$i]);
            echo "<span class=\"small_planning\">";
            echo $d[$cday]["data"][$i]
                . "<br /><i><b>"
                . htmlspecialchars($d[$cday]["room"][$i])
                . "</b></i><br />";
           // si la r�servation est � mod�rer, on le signale
           if ((isset($d[$cday]["moderation"][$i])) and ($d[$cday]["moderation"][$i]==1))
               echo "&nbsp;<img src=\"img_grr/flag_moderation.png\" alt=\"".get_vocab("en_attente_moderation")."\" title=\"".get_vocab("en_attente_moderation")."\" class=\"image\" />&nbsp;\n";

          if ($acces_fiche_reservation[$d[$cday]["id_room"][$i]])
              echo "<a title=\"".htmlspecialchars($d[$cday]["who"][$i])."\" href=\"view_entry.php?id=" . $d[$cday]["id"][$i]
              . "&amp;day=$cday&amp;month=$month&amp;year=$year&amp;page=month_all\">"
              . $d[$cday]["who1"][$i]
              . "</a>";
          else
                echo $d[$cday]["who1"][$i];

          if ($d[$cday]["description"][$i]!= "")
              echo "<br /><i>(".$d[$cday]["description"][$i].")</i>";
          echo "</span></td></tr></table>\n";
        }
       }
     }
    }
    echo "</td>\n";
    } // fin condition "on n'affiche pas tous les jours de la semaine"
    if (++$weekcol == 7) {
		$weekcol = 0;
		echo "</tr>";
	}
}
// Fin Premi�re boucle sur les jours du mois

// On grise les cellules appartenant au mois suivant
if ($weekcol > 0) for (; $weekcol < 7; $weekcol++)
{
    $num_week_day = ($weekcol + $weekstarts)%7;
    if ($display_day[$num_week_day] == 1)  // on n'affiche pas tous les jours de la semaine
        echo "<td class=\"cell_month_o\" >&nbsp;</td>\n";
}
echo "</tr></table>\n";
show_colour_key($area);
// Affichage d'un message pop-up
affiche_pop_up(get_vocab("message_records"),"user");
include "include/trailer.inc.php";
?>