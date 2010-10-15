<?php
#########################################################################
#                         day.php                                       #
#                                                                       #
#    Permet l'affichage de la page d'accueil lorsque l'on est en mode   #
#    d'affichage "jour".                                                #
#                                                                       #
#                  Derni�re modification : 20/03/2008                   #
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
include "include/misc.inc.php";
include "include/mrbs_sql.inc.php";
include "include/functions.inc.php";
include "include/$dbsys.inc.php";
include "include/mincals.inc.php";
$grr_script_name = "day.php";
#Param�tres de connection
require_once("./include/settings.inc.php");

#Chargement des valeurs de la table settings
if (!loadSettings())
    die("Erreur chargement settings");

#Fonction relative � la session
require_once("./include/session.inc.php");
   #Si nous ne savons pas la date, nous devons la cr�er
$date_now = mktime();
if (!isset($day) or !isset($month) or !isset($year))
{
    if ($date_now < getSettingValue("begin_bookings"))
        $date_ = getSettingValue("begin_bookings");
    else if ($date_now > getSettingValue("end_bookings"))
        $date_ = getSettingValue("end_bookings");
    else
        $date_ = $date_now;
    $day   = date("d",$date_);
    $month = date("m",$date_);
    $year  = date("Y",$date_);
} else
{
    // V�rification des dates
    settype($month,"integer");
    settype($day,"integer");
    settype($year,"integer");
    $minyear = strftime("%Y", getSettingValue("begin_bookings"));
    $maxyear = strftime("%Y", getSettingValue("end_bookings"));
    if ($day < 1) $day = 1;
    if ($day > 31) $day = 31;
    if ($month < 1) $month = 1;
    if ($month > 12) $month = 12;
    if ($year < $minyear) $year = $minyear;
    if ($year > $maxyear) $year = $maxyear;

    #Si la date n'est pas valide, ils faut la modifier (Si le nombre de jours est supp�rieur au nombre de jours dans un mois)
    while (!checkdate($month, $day, $year))
        $day--;
}


// Resume session
if ((!grr_resumeSession())and (getSettingValue("authentification_obli")==1)) {
    header("Location: ./logout.php?auto=1");
    die();
};
if (empty($area)) $area = get_default_area();

// Param�tres langage
include "include/language.inc.php";

// On affiche le lien "format imprimable" en bas de la page
$affiche_pview = '1';
if (!isset($_GET['pview'])) $_GET['pview'] = 0; else $_GET['pview'] = 1;

$back = '';
if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);

if ((getSettingValue("authentification_obli")==0) and (!isset($_SESSION['login']))) {
    $session_login = '';
    $session_statut = '';
    $type_session = "no_session";
} else {
    $session_login = $_SESSION['login'];
    $session_statut = $_SESSION['statut'];
    $type_session = "with_session";
}

// R�cup�ration des donn�es concernant l'affichage du planning du domaine
get_planning_area_values($area);

// Si aucun domaine n'est d�fini
if ($area == 0) {
   print_header($day, $month, $year, $area,$type_session);
   echo "<H1>".get_vocab("noareas")."</H1>";
   echo "<A HREF='admin_accueil.php'>".get_vocab("admin")."</A>\n
   </BODY>
   </HTML>";
   exit();
}

if((authGetUserLevel(getUserName(),-1) < 1) and (getSettingValue("authentification_obli")==1))
{
    showAccessDenied($day, $month, $year, $area,$back);
    exit();
}
if(authUserAccesArea($session_login, $area)==0)
{
    showAccessDenied($day, $month, $year, $area,$back);
    exit();
}

if (check_begin_end_bookings($day, $month, $year))
{
    showNoBookings($day, $month, $year, $area,$back,$type_session);
    exit();
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
?>
<script type="text/javascript" src="functions.js" language="javascript"></script>
<?php

// Si format imprimable ($_GET['pview'] = 1), on n'affiche pas cette partie
if ($_GET['pview'] != 1) {

// Affichage d'un message pop-up
affiche_pop_up(get_vocab("message_records"),"user");

echo "<table width=\"100%\" cellspacing=15><tr>\n<td>";

if (isset($_SESSION['default_list_type']) or (getSettingValue("authentification_obli")==1)) {
    $area_list_format = $_SESSION['default_list_type'];
} else {
    $area_list_format = getSettingValue("area_list_format");
}

#Show all avaliable areas
# need to show either a select box or a normal html list,
if ($area_list_format != "list") {
  echo make_area_select_html('day.php', $area, $year, $month, $day, $session_login); # from functions.inc.php
	echo make_room_select_html('week', $area, "", $year, $month, $day);
} else {
	echo "\n<table cellspacing=15><tr><td>\n";
  echo make_area_list_html('day.php', $area, $year, $month, $day, $session_login); # from functions.inc.php
	echo "</td><td>";
	make_room_list_html('week.php', $area, "", $year, $month, $day);
	echo "</td></tr></table>";
}
echo "</td>\n";
#Draw the three month calendars
minicals($year, $month, $day, $area, -1, 'day');
echo "</tr></table>";

// fin de la condition "Si format imprimable"
}

#y? are year, month and day of yesterday
#t? are year, month and day of tomorrow
$ind = 1;
$test = 0;
while (($test == 0) and ($ind < 7)) {
    $i= mktime(0,0,0,$month,$day-$ind,$year);
    $test =$display_day[date("w",$i)];
    $ind++;
}
$yy = date("Y",$i);
$ym = date("m",$i);
$yd = date("d",$i);

$i= mktime(0,0,0,$month,$day,$year);
$jour_cycle = grr_sql_query1("SELECT Jours FROM grr_calendrier_jours_cycle WHERE DAY='$i'");

$ind = 1;
$test = 0;
while (($test == 0) and ($ind < 7)) {
    $i= mktime(0,0,0,$month,$day+$ind,$year);
    $test =$display_day[date("w",$i)];
    $ind++;
}
$ty = date("Y",$i);
$tm = date("m",$i);
$td = date("d",$i);

# Define the start and end of the day.
$am7=mktime($morningstarts,0,0,$month,$day,$year);
$pm7=mktime($eveningends,$eveningends_minutes,0,$month,$day,$year);

#Show current date
$this_area_name = grr_sql_query1("select area_name from grr_area where id='".protect_data_sql($area)."'");

echo "<h2 align=center>" . ucfirst(utf8_strftime($dformat, $am7)) . " - ";
if (getSettingValue("jours_cycles_actif") == "Oui" and intval($jour_cycle)>-1)
    if (intval($jour_cycle)>0)
	      echo  get_vocab("rep_type_6")." ".$jour_cycle."<br />";
	  else
	      echo  $jour_cycle."<br />";
echo ucfirst($this_area_name)." - ".get_vocab("all_areas")."</h2>\n";


// Si format imprimable ($_GET['pview'] = 1), on n'affiche pas cette partie
if ($_GET['pview'] != 1) {
    #Show Go to day before and after links
    echo "<table width=\"100%\"><tr>\n<td>\n<a href=\"day.php?year=$yy&amp;month=$ym&amp;day=$yd&amp;area=$area\">&lt;&lt; ".get_vocab('daybefore')."</a></td>\n<td align=right><a href=\"day.php?year=$ty&amp;month=$tm&amp;day=$td&amp;area=$area\">".get_vocab('dayafter')." &gt;&gt;</a></td>\n</tr></table>\n";
}

#We want to build an array containing all the data we want to show
#and then spit it out.

#Get all appointments for today in the area that we care about
#Note: The predicate clause 'start_time <= ...' is an equivalent but simpler
#form of the original which had 3 BETWEEN parts. It selects all entries which
#occur on or cross the current day.
$sql = "SELECT grr_room.id, start_time, end_time, name, grr_entry.id, type, beneficiaire, statut_entry, grr_entry.description, grr_entry.option_reservation, grr_entry.moderate, beneficiaire_ext
   FROM grr_entry, grr_room
   WHERE grr_entry.room_id = grr_room.id
   AND area_id = '".protect_data_sql($area)."'
   AND start_time < ".($pm7+$resolution)." AND end_time > $am7 ORDER BY start_time";

$res = grr_sql_query($sql);
if (! $res) {
//    fatal_error(0, grr_sql_error());
    include "include/trailer.inc.php";
    exit;
}

for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
    # Each row weve got here is an appointment.
    #Row[0] = Room ID
    #row[1] = start time
    #row[2] = end time
    #row[3] = short description
    #row[4] = id of this booking
    #row[5] = type (internal/external)
    #row[6] = identifiant du r�servant
    #row[7] = satut of the booking
    #row[8] = Full description
    #row[9] = option_reservation
    #row[10] = �tat de mod�ration de la r�servation
    #row[11] = b�n�ficiaire ext�rieur

    # $today is a map of the screen that will be displayed
    # It looks like:
    #     $today[Room ID][Time][id]
    #                          [color]
    #                          [data]

    # Fill in the map for this meeting. Start at the meeting start time,
    # or the day start time, whichever is later. End one slot before the
    # meeting end time (since the next slot is for meetings which start then),
    # or at the last slot in the day, whichever is earlier.
    # Note: int casts on database rows for max may be needed for PHP3.
    # Adjust the starting and ending times so that bookings which don't
    # start or end at a recognized time still appear.

    $start_t = max(round_t_down($row[1], $resolution, $am7), $am7);
    $end_t = min(round_t_up($row[2], $resolution, $am7) - $resolution, $pm7);

    // Calcul du nombre de cr�neaux qu'occupe la r�servation
    $cellules[$row[4]]=($end_t-$start_t)/$resolution+1;
    // Initialisation du compteur
    $compteur[$row[4]]=0;

    for ($t = $start_t; $t <= $end_t; $t += $resolution)
    {
        $today[$row[0]][$t]["id"]    = $row[4];
        $today[$row[0]][$t]["color"] = $row[5];
        $today[$row[0]][$t]["data"]  = "";
        $today[$row[0]][$t]["who"] = "";
        $today[$row[0]][$t]["statut"] = $row[7];
        $today[$row[0]][$t]["moderation"] = $row[10];
        $today[$row[0]][$t]["option_reser"] = $row[9];
        // Construction des infos � afficher sur le planning
        $today[$row[0]][$t]["description"] = affichage_resa_planning($row[8],$row[4]);
    }

    # Show the name of the booker in the first segment that the booking
    # happens in, or at the start of the day if it started before today.
    if ($row[1] < $am7) {
        $today[$row[0]][$am7]["data"] = affichage_lien_resa_planning($row[3],$row[4]);
        // Info-bulle
        if (getSettingValue("display_info_bulle") == 1)
            $today[$row[0]][$am7]["who"] = get_vocab("reservation au nom de").affiche_nom_prenom_email($row[6],$row[11],"nomail");
        else if (getSettingValue("display_info_bulle") == 2)
            $today[$row[0]][$am7]["who"] = $row[8];
        else
            $today[$row[0]][$am7]["who"] = "";
    } else {
        $today[$row[0]][$start_t]["data"] = affichage_lien_resa_planning($row[3],$row[4]);
        // Info-bulle
        if (getSettingValue("display_info_bulle") == 1)
            $today[$row[0]][$start_t]["who"] = get_vocab("reservation au nom de").affiche_nom_prenom_email($row[6],$row[11]);
        else if (getSettingValue("display_info_bulle") == 2)
            $today[$row[0]][$start_t]["who"] = $row[8];
        else
            $today[$row[0]][$start_t]["who"] = "";
    }
}
# We need to know what all the rooms area called, so we can show them all
# pull the data from the db and store it. Convienently we can print the room
# headings and capacities at the same time

$sql = "select room_name, capacity, id, description, statut_room, show_fic_room, delais_option_reservation, moderate from grr_room where area_id='".protect_data_sql($area)."' order by order_display, room_name";
$res = grr_sql_query($sql);

# It might be that there are no rooms defined for this area.
# If there are none then show an error and dont bother doing anything
# else
if (! $res) fatal_error(0, grr_sql_error());
if (grr_sql_count($res) == 0)
{
    echo "<h1>".get_vocab('no_rooms_for_area')."</h1>";
    grr_sql_free($res);
}
else
{
    #This is where we start displaying stuff
    echo "<table cellspacing=0 border=1 width=\"100%\">";

    // Premi�re ligne du tableau
    echo "<tr>\n<th width=\"5%\">&nbsp;</th>";
    $tab[1][] = "&nbsp;";
    $room_column_width = (int)(90 / grr_sql_count($res));
    $nbcol = 0;
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
        $room_name[$i] = $row[0];
        $id_room[$i] =  $row[2];
        $statut_room[$id_room[$i]] =  $row[4];
        $statut_moderate[$id_room[$i]] =  $row[7];
        $nbcol++;
        if ($row[1]) {
            $temp = "<br />($row[1] ".($row[1] >1 ? get_vocab("number_max2") : get_vocab("number_max")).")";
        } else {
            $temp="";
        }
        if ($statut_room[$id_room[$i]] == "0") $temp .= "<br /><font color=\"#BA2828\"><b>".get_vocab("ressource_temporairement_indisponible")."</b></font>"; // Ressource temporairement indisponible
        if ($statut_moderate[$id_room[$i]] == "1") $temp .= "<br /><font color=\"#BA2828\"><b>".get_vocab("reservations_moderees")."</b></font>"; // Ressource temporairement indisponible
        echo "<th width=\"$room_column_width%\"";
        // Si la ressource est temporairement indisponible, on le signale
        if ($statut_room[$id_room[$i]] == "0") echo " class='avertissement' ";
        echo ">" . htmlspecialchars($row[0])."\n";
        if (htmlspecialchars($row[3]. $temp != '')) {
            if (htmlspecialchars($row[3] != '')) $saut = "<br />"; else $saut = "";
            echo $saut."<i><span class =\"small\">". htmlspecialchars($row[3]) . $temp."\n</span></i>";
        }

        echo "<br />";
        if (verif_display_fiche_ressource(getUserName(), $id_room[$i]) and $_GET['pview'] != 1)
            echo "<A href='javascript:centrerpopup(\"view_room.php?id_room=$id_room[$i]\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")' title=\"".get_vocab("fiche_ressource")."\">
           <img src=\"img_grr/details.png\" alt=\"d&eacute;tails\" border=\"0\" class=\"print_image\"  /></a>";
        if (authGetUserLevel(getUserName(),$id_room[$i]) > 2 and $_GET['pview'] != 1)
            echo "<a href='admin_edit_room.php?room=$id_room[$i]'><img src=\"img_grr/editor.png\" alt=\"configuration\" border=\"0\" title=\"".get_vocab("Configurer la ressource")."\" width=\"30\" height=\"30\" class=\"print_image\"  /></a>";
        echo "</th>";
        // stockage de la premi�re ligne :
        $tab[1][$i+1] = htmlspecialchars($row[0]);
        if (htmlspecialchars($row[3]. $temp != '')) {
            if (htmlspecialchars($row[3] != '')) $saut = "<br />"; else $saut = "";
            $tab[1][$i+1] .="<br />-".$saut."<i><span class =\"small\">". htmlspecialchars($row[3]) . $temp."\n</span></i>";
        }
        $tab[1][$i+1] .= "<br />";
        if (verif_display_fiche_ressource(getUserName(), $id_room[$i]))
            $tab[1][$i+1] .= "<A href='javascript:centrerpopup(\"view_room.php?id_room=$id_room[$i]\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")' title=\"".get_vocab("fiche_ressource")."\">
           <img src=\"img_grr/details.png\" alt=\"d�tails\" border=\"0\" class=\"print_image\"  /></a>";
        if (authGetUserLevel(getUserName(),$id_room[$i]) > 2 and $_GET['pview'] != 1)
            $tab[1][$i+1] .= "<a href='admin_edit_room.php?room=$id_room[$i]'><img src=\"img_grr/editor.png\" alt=\"configuration\" border=\"0\" title=\"".get_vocab("Configurer la ressource")."\" width=\"30\" height=\"30\" class=\"print_image\"  /></a>";
        // fin stockage de la premi�re ligne :


        $rooms[] = $row[2];
        $delais_option_reservation[$row[2]] = $row[6];
    }
    echo "<th width=\"5%\">&nbsp;</th></tr>\n";
    $tab[1][] = "&nbsp;";

    // Deuxi�me ligne et lignes suivantes du tableau
    echo "<tr>\n";
    tdcell("cell_hours");
    if ($enable_periods == 'y')
        echo get_vocab('period');
    else
        echo get_vocab('time');
    echo "</td>\n";

    if ($enable_periods == 'y')
        $tab[2][] = get_vocab('period');
    else
        $tab[2][] = get_vocab('time');

    for ($i = 0; $i < $nbcol; $i++)
    {
        // Si la ressource est temporairement indisponible, on le signale, sinon, couleur normale
        if ($statut_room[$id_room[$i]] == "0") tdcell("avertissement"); else tdcell("cell_hours");
        if ($_GET['pview'] != 1)
           echo "<a title=\"".htmlspecialchars(get_vocab("see_week_for_this_room"))."\"  href=\"week.php?year=$year&amp;month=$month&amp;day=$day&amp;area=$area&amp;room=$id_room[$i]\">".get_vocab("week")."</a><br /><a title=\"".htmlspecialchars(get_vocab("see_month_for_this_room"))."\" href=\"month.php?year=$year&amp;month=$month&amp;day=$day&amp;area=$area&amp;room=$id_room[$i]\">".get_vocab("month")."</a>";
        if ($_GET['pview'] != 1)
           $tab[2][] = "<a title=\"".htmlspecialchars(get_vocab("see_week_for_this_room"))."\"  href=\"week.php?year=$year&amp;month=$month&amp;day=$day&amp;area=$area&amp;room=$id_room[$i]\">".get_vocab("week")."</a><br /><a title=\"".htmlspecialchars(get_vocab("see_month_for_this_room"))."\" href=\"month.php?year=$year&amp;month=$month&amp;day=$day&amp;area=$area&amp;room=$id_room[$i]\">".get_vocab("month")."</a>";
        else
           $tab[2][] = "";

        echo "</td>\n";
    }
    tdcell("cell_hours");
    if ($enable_periods == 'y')
        echo get_vocab('period');
    else
        echo get_vocab('time');

    if ($enable_periods == 'y')
        $tab[2][] = get_vocab('period');
    else
        $tab[2][] = get_vocab('time');


    echo "</td>\n</tr>\n";


    $tab_ligne = 3;
    // D�but premi�re boucle sur le temps
    for ($t = $am7; $t <= $pm7; $t += $resolution)
    {
        # Show the time linked to the URL for highlighting that time
        echo "<tr>\n";


        tdcell("cell_hours");
        if( $enable_periods == 'y' ){
            $time_t = date("i", $t);
            $time_t_stripped = preg_replace( "/^0/", "", $time_t );
            echo $periods_name[$time_t_stripped] . "</td>\n";
            $tab[$tab_ligne][] = $periods_name[$time_t_stripped];
        } else {
            echo date(hour_min_format(),$t) . "</td>\n";
            $tab[$tab_ligne][] = date(hour_min_format(),$t);
        }


        // D�but Deuxi�me boucle sur la liste des ressources du domaine
        while (list($key, $room) = each($rooms))
        {
            if(isset($today[$room][$t]["id"])) // il y a une r�servation sur le cr�neau
            {
                $id    = $today[$room][$t]["id"];
                $color = $today[$room][$t]["color"];
                $descr = htmlspecialchars($today[$room][$t]["data"]);
            }
            else
                unset($id);  // $id non d�fini signifie donc qu'il n'y a pas de r�sa sur le cr�neau

            // D�finition des couleurs de fond de cellule
            if  ((isset($id)) and (!est_hors_reservation(mktime(0,0,0,$month,$day,$year))))   // 1er cas : il y a une r�servation sur le cr�neau
            {
                $c = $color;
            } else if ($statut_room[$room] == "0") // 2�me cas : ou bien la ressource est temporairement indisponible
                $c = "avertissement"; // on le signale par une couleur sp�cifique
            else  // 3�me cas : sinon, il s'agit d'un cr�neau libre
                $c = "empty_cell";

            // S'il s'agit d'un cr�neau avec une resa :
            // s'il s'agit du premier passage ($compteur[$id]=0), on fait un tdcell_rowspan
            // Sinon, pas de <td>
            if  ((isset($id)) and (!est_hors_reservation(mktime(0,0,0,$month,$day,$year)))) {
                if( $compteur[$id] == 0 ) {
                    // Y-a-il chevauchement de deux blocs dans le cas o� la hauteur du bloc est sup�rieure � 1 ?
                    if ($cellules[$id] != 1) {
                       // Dans ce cas, on s'int�resse � la derni�re ligne du bloc
                       if(isset($today[$room][$t+($cellules[$id]-1)*$resolution]["id"])) {
                         // Il y a chevaussement seulement si l'id correspondant est diff�rent de l'id actuel
                         $id_derniere_ligne_du_bloc = $today[$room][$t+($cellules[$id]-1)*$resolution]["id"];
                         // Dan ce cas, on r�duit la taille du bloc pour �viter le chevaussement
                         if ($id_derniere_ligne_du_bloc != $id) $cellules[$id] = $cellules[$id]-1;
                       }
                    }
                    tdcell_rowspan ($c, $cellules[$id]);
                }
                $compteur[$id] = 1; // on incr�mente le compteur initialement � z�ro
            } else
                tdcell ($c); // il s'agit d'un cr�neau libre  -> <td> normal
            // Si $compteur[$id] a atteint == $cellules[$id]+1

            if ((!isset($id)) or (est_hors_reservation(mktime(0,0,0,$month,$day,$year)))) // Le cr�neau est libre
            {
                $hour = date("H",$t);
                $minute  = date("i",$t);
                $date_booking = mktime($hour, $minute, 0, $month, $day, $year);
                echo "<center>";
                if (est_hors_reservation(mktime(0,0,0,$month,$day,$year))) {
                    echo "<center><img src=\"img_grr/stop.png\" border=\"0\" alt=\"".get_vocab("reservation_impossible")."\"  title=\"".get_vocab("reservation_impossible")."\" width=\"16\" height=\"16\" class=\"print_image\"  /></center>";
                    $tab[$tab_ligne][] = "<center><img src=\"img_grr/stop.png\" border=\"0\" alt=\"".get_vocab("reservation_impossible")."\"  title=\"".get_vocab("reservation_impossible")."\" width=\"16\" height=\"16\" class=\"print_image\"  /></center>";
                } else

                if (((authGetUserLevel(getUserName(),-1) > 1) or (auth_visiteur(getUserName(),$room) == 1))
                 and (UserRoomMaxBooking(getUserName(), $room, 1) != 0)
                 and verif_booking_date(getUserName(), -1, $room, $date_booking, $date_now, $enable_periods)
                 and verif_delais_max_resa_room(getUserName(), $room, $date_booking)
                 and verif_delais_min_resa_room(getUserName(), $room, $date_booking)
                 and (($statut_room[$room] == "1") or
                  (($statut_room[$room] == "0") and (authGetUserLevel(getUserName(),$room) > 2) ))
                  and $_GET['pview'] != 1) {
                    if ($enable_periods == 'y') {
                        echo "<a href=\"edit_entry.php?area=$area&amp;room=$room&amp;period=$time_t_stripped&amp;year=$year&amp;month=$month&amp;day=$day&amp;page=day\" title=\"".get_vocab("cliquez_pour_effectuer_une_reservation")."\" ><img src=\"img_grr/new.png\" border=\"0\" alt=\"".get_vocab("add")."\" width=\"16\" height=\"16\" class=\"print_image\" /></a>";
                        $tab[$tab_ligne][] = "<a href=\"edit_entry.php?area=$area&amp;room=$room&amp;period=$time_t_stripped&amp;year=$year&amp;month=$month&amp;day=$day&amp;page=day\" title=\"".get_vocab("cliquez_pour_effectuer_une_reservation")."\" ><img src=\"img_grr/new.png\" border=\"0\" alt=\"".get_vocab("add")."\" width=\"16\" height=\"16\" class=\"print_image\" /></a>";
                    } else {
                        echo "<a href=\"edit_entry.php?area=$area&amp;room=$room&amp;hour=$hour&amp;minute=$minute&amp;year=$year&amp;month=$month&amp;day=$day&amp;page=day\" title=\"".get_vocab("cliquez_pour_effectuer_une_reservation")."\" ><img src=img_grr/new.png border=0 alt=\"".get_vocab("add")."\" class=\"print_image\" /></a>";
                        $tab[$tab_ligne][] =  "<a href=\"edit_entry.php?area=$area&amp;room=$room&amp;hour=$hour&amp;minute=$minute&amp;year=$year&amp;month=$month&amp;day=$day&amp;page=day\" title=\"".get_vocab("cliquez_pour_effectuer_une_reservation")."\" ><img src=img_grr/new.png border=0 alt=\"".get_vocab("add")."\" class=\"print_image\" /></a>";
                    }
                } else {
                    echo "&nbsp;";
                    $tab[$tab_ligne][] = "&nbsp;";
                }
                echo "</center>";
                echo "</td>\n";
            }
            elseif ($descr != "")
            {
                // si la r�servation est "en cours", on le signale
                if ((isset($today[$room][$t]["statut"])) and ($today[$room][$t]["statut"]!='-')) {
                    echo "&nbsp;<img src=\"img_grr/buzy.png\" alt=\"".get_vocab("reservation_en_cours")."\" title=\"".get_vocab("reservation_en_cours")."\" width=\"20\" height=\"20\" border=\"0\" />&nbsp;\n";
                    $tab[$tab_ligne][] = "&nbsp;<img src=\"img_grr/buzy.png\" alt=\"".get_vocab("reservation_en_cours")."\" title=\"".get_vocab("reservation_en_cours")."\" width=\"20\" height=\"20\" border=\"0\" />&nbsp;\n";
                }
                // si la r�servation est � confirmer, on le signale
                if (($delais_option_reservation[$room] > 0) and (isset($today[$room][$t]["option_reser"])) and ($today[$room][$t]["option_reser"]!=-1)) {
                    echo "&nbsp;<img src=\"img_grr/small_flag.png\" alt=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."\" title=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."&nbsp;".time_date_string_jma($today[$room][$t]["option_reser"],$dformat)."\" width=\"20\" height=\"20\" border=\"0\" />&nbsp;\n";
                    $tab[$tab_ligne][] = "&nbsp;<img src=\"img_grr/small_flag.png\" alt=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."\" title=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."&nbsp;".time_date_string_jma($today[$room][$t]["option_reser"],$dformat)."\" width=\"20\" height=\"20\" border=\"0\" />&nbsp;\n";
                }
                // si la r�servation est � mod�rer, on le signale
                if ((isset($today[$room][$t]["moderation"])) and ($today[$room][$t]["moderation"]=='1')) {
                    echo "&nbsp;<img src=\"img_grr/flag_moderation.png\" alt=\"".get_vocab("en_attente_moderation")."\" title=\"".get_vocab("en_attente_moderation")."\" border=\"0\" />&nbsp;\n";
                    $tab[$tab_ligne][] = "&nbsp;<img src=\"img_grr/flag_moderation.png\" alt=\"".get_vocab("en_attente_moderation")."\" title=\"".get_vocab("en_attente_moderation")."\" border=\"0\" />&nbsp;\n";
                }

                #if it is booked then show
                if (($statut_room[$room] == "1") or
                (($statut_room[$room] == "0") and (authGetUserLevel(getUserName(),$room) > 2) )) {
                    echo " <a title=\"".htmlspecialchars($today[$room][$t]["who"])."\" href=\"view_entry.php?id=$id&amp;area=$area&amp;day=$day&amp;month=$month&amp;year=$year&amp;page=day\">$descr</a>";
                    $tab[$tab_ligne][] = " <a title=\"".htmlspecialchars($today[$room][$t]["who"])."\" href=\"view_entry.php?id=$id&amp;area=$area&amp;day=$day&amp;month=$month&amp;year=$year&amp;page=day\">$descr</a>";
                    if ($today[$room][$t]["description"]!= "") {
                        echo "<br /><i>".$today[$room][$t]["description"]."</i>";
                        $tab[$tab_ligne][] = "<br /><i>".$today[$room][$t]["description"]."</i>";
                    }
                } else {
                    echo " $descr";
                    $tab[$tab_ligne][] = " $descr";
                }
                echo "</td>\n";
            }
        } // Fin Deuxi�me boucle sur la liste des ressources du domaine

        // R�p�tition de la premi�re colonne
        // Si la ressource est temporairement indisponible, on le signale, sinon, couleur normale
        tdcell("cell_hours");
        if( $enable_periods == 'y' ){
            $time_t = date("i", $t);
            $time_t_stripped = preg_replace( "/^0/", "", $time_t );
            echo $periods_name[$time_t_stripped] . "</td>\n";
            $tab[$tab_ligne][] =  $periods_name[$time_t_stripped];

        } else {
            echo date(hour_min_format(),$t) . "</td>\n";
            $tab[$tab_ligne][] = date(hour_min_format(),$t);
        }

        echo "</tr>\n";

        reset($rooms);
        $tab_ligne++;
    }
    // r�p�tition de la ligne d'en-t�te
    echo "<tr>\n<th>&nbsp;</th>";
    for ($i = 0; $i < $nbcol; $i++)
    {
        echo "<th";
        if ($statut_room[$id_room[$i]] == "0") echo " class='avertissement' ";
        echo ">" . htmlspecialchars($room_name[$i])."</th>";
    }
    echo "<th>&nbsp;</th></tr>\n";

    echo "</table>";
    show_colour_key($area);
}
/*
echo "<table border=\"1\">";
foreach ($tab as $cle => $value) {
    echo "<tr>";
    foreach ($value as $value2) {
        echo "<td>";
        echo $value2;
        echo "</td>";
    }
    echo "</tr>";
}
echo "<table border=\"1\">";

for ($j=0; $j<=($nbcol+1); $j++) {
    echo "<tr>";
    for ($i=1; $i<=count($tab); $i++) {
        echo "<td>";
        echo $tab[$i][$j];
        echo "</td>";
    }
    echo "</tr>";
}
*/


include "include/trailer.inc.php";
?>