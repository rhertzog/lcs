<?php
/**
 * day.php
 * Permet l'affichage de la page d'accueil lorsque l'on est en mode d'affichage "jour".
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2009-12-02 20:11:07 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: day.php,v 1.20 2009-12-02 20:11:07 grr Exp $
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
 * $Log: day.php,v $
 * Revision 1.20  2009-12-02 20:11:07  grr
 * *** empty log message ***
 *
 * Revision 1.19  2009-10-09 07:55:48  grr
 * *** empty log message ***
 *
 * Revision 1.18  2009-09-29 18:02:56  grr
 * *** empty log message ***
 *
 * Revision 1.17  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.16  2009-04-09 14:52:31  grr
 * *** empty log message ***
 *
 * Revision 1.15  2009-03-24 13:30:07  grr
 * *** empty log message ***
 *
 * Revision 1.14  2009-02-27 22:05:03  grr
 * *** empty log message ***
 *
 * Revision 1.13  2009-01-28 16:01:31  grr
 * *** empty log message ***
 *
 * Revision 1.12  2009-01-20 07:19:17  grr
 * *** empty log message ***
 *
 * Revision 1.11  2008-11-16 22:00:58  grr
 * *** empty log message ***
 *
 * Revision 1.10  2008-11-14 07:29:09  grr
 * *** empty log message ***
 *
 * Revision 1.9  2008-11-13 21:32:51  grr
 * *** empty log message ***
 *
 * Revision 1.8  2008-11-11 22:01:14  grr
 * *** empty log message ***
 *
 * Revision 1.7  2008-11-10 08:17:34  grr
 * *** empty log message ***
 *
 * Revision 1.6  2008-11-10 07:06:39  grr
 * *** empty log message ***
 *
 *
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
if (!grr_resumeSession()) {
    if ((getSettingValue("authentification_obli")==1) or ((getSettingValue("authentification_obli")==0) and (isset($_SESSION['login'])))) {
       header("Location: ./logout.php?auto=1&url=$url");
       die();
    }
};
// Construction des identifiants de la ressource $room, du domaine $area, du site $id_site
Definition_ressource_domaine_site();

// Param�tres langage
include "include/language.inc.php";

// On affiche le lien "format imprimable" en bas de la page
$affiche_pview = '1';
if (!isset($_GET['pview'])) $_GET['pview'] = 0; else $_GET['pview'] = 1;
if ($_GET['pview'] == 1)
    $class_image = "print_image";
else
    $class_image = "image";


$back = '';
if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);

if ((getSettingValue("authentification_obli")==0) and (getUserName()=='')) {
    $type_session = "no_session";
} else {
    $type_session = "with_session";
}

// R�cup�ration des donn�es concernant l'affichage du planning du domaine
get_planning_area_values($area);

// Si aucun domaine n'est d�fini
if ($area <= 0) {
   print_header($day, $month, $year, $area,$type_session);
   echo "<h1>".get_vocab("noareas")."</h1>";
   echo "<a href='admin_accueil.php'>".get_vocab("admin")."</a>\n
   </body>
   </html>";
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

// Si format imprimable ($_GET['pview'] = 1), on n'affiche pas cette partie
if ($_GET['pview'] != 1) {


echo "<table width=\"100%\" cellspacing=\"15\"><tr>\n";

if (isset($_SESSION['default_list_type']) or (getSettingValue("authentification_obli")==1)) {
    $area_list_format = $_SESSION['default_list_type'];
} else {
    $area_list_format = getSettingValue("area_list_format");
}

# S�lection des sites, domaines et ressources
if ($area_list_format != "list") {
  # S�lection sous la forme de listes d�roulantes
  echo "<td>\n";
  echo make_site_select_html('day.php',$id_site,$year,$month,$day,getUserName());
  echo make_area_select_html('day.php',$id_site,$area,$year,$month,$day,getUserName());
	echo make_room_select_html('week',$area,"",$year,$month,$day);
  echo "</td>\n";
} else {
  # S�lection sous la forme de listes
	echo "<td>\n";
  echo make_site_list_html('day.php',$id_site,$year,$month,$day,getUserName());
	echo "</td><td>";
  echo make_area_list_html('day.php',$id_site,$area,$year,$month,$day,getUserName());
	echo "</td><td>";
	make_room_list_html('week.php',$area,"",$year,$month,$day);
	echo "</td>";
}

#Affichage des calendriers
minicals($year, $month, $day, $area, -1, 'day');

echo "</tr></table>";

// fin de la condition "Si format imprimable"
}

#y? are year, month and day of yesterday
#t? are year, month and day of tomorrow
$ind = 1;
$test = 0;
while (($test == 0) and ($ind <= 7)) {
    $i= mktime(0,0,0,$month,$day-$ind,$year);
    $test =$display_day[date("w",$i)];
    $ind++;
}
$yy = date("Y",$i);
$ym = date("m",$i);
$yd = date("d",$i);

$i= mktime(0,0,0,$month,$day,$year);
$jour_cycle = grr_sql_query1("SELECT Jours FROM ".TABLE_PREFIX."_calendrier_jours_cycle WHERE DAY='$i'");

$ind = 1;
$test = 0;
while (($test == 0) and ($ind <= 7)) {
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
$this_area_name = grr_sql_query1("select area_name from ".TABLE_PREFIX."_area where id='".protect_data_sql($area)."'");

echo "<div class=\"titre_planning\">" . ucfirst(utf8_strftime($dformat, $am7));
if (getSettingValue("jours_cycles_actif") == "Oui" and intval($jour_cycle)>-1)
    if (intval($jour_cycle)>0)
	      echo " - ".get_vocab("rep_type_6")." ".$jour_cycle;
	  else
	      echo " - ".$jour_cycle;
echo "<br />".ucfirst($this_area_name)." - ".get_vocab("all_areas")."</div>\n";


// Si format imprimable ($_GET['pview'] = 1), on n'affiche pas cette partie
if ($_GET['pview'] != 1) {
    #Show Go to day before and after links
    echo "<table width=\"100%\"><tr>\n<td>\n<a href=\"day.php?year=$yy&amp;month=$ym&amp;day=$yd&amp;area=$area\">&lt;&lt; ".get_vocab('daybefore')."</a></td>\n<td align=\"right\"><a href=\"day.php?year=$ty&amp;month=$tm&amp;day=$td&amp;area=$area\">".get_vocab('dayafter')." &gt;&gt;</a></td>\n</tr></table>\n";
}

#We want to build an array containing all the data we want to show
#and then spit it out.

#Get all appointments for today in the area that we care about
#Note: The predicate clause 'start_time <= ...' is an equivalent but simpler
#form of the original which had 3 BETWEEN parts. It selects all entries which
#occur on or cross the current day.
$sql = "SELECT ".TABLE_PREFIX."_room.id, start_time, end_time, name, ".TABLE_PREFIX."_entry.id, type, beneficiaire, statut_entry, ".TABLE_PREFIX."_entry.description, ".TABLE_PREFIX."_entry.option_reservation, ".TABLE_PREFIX."_entry.moderate, beneficiaire_ext
   FROM ".TABLE_PREFIX."_entry, ".TABLE_PREFIX."_room
   WHERE ".TABLE_PREFIX."_entry.room_id = ".TABLE_PREFIX."_room.id
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
grr_sql_free($res);
# We need to know what all the rooms area called, so we can show them all
# pull the data from the db and store it. Convienently we can print the room
# headings and capacities at the same time

$sql = "select room_name, capacity, id, description, statut_room, show_fic_room, delais_option_reservation, moderate from ".TABLE_PREFIX."_room where area_id='".protect_data_sql($area)."' order by order_display, room_name";
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
    echo "<table cellspacing=\"0\" border=\"1\" width=\"100%\">";

    // Premi�re ligne du tableau
      echo "<tr>\n<th style=\"width:5%;\">&nbsp;</th>";
    $tab[1][] = "&nbsp;";
    $room_column_width = (int)(90 / grr_sql_count($res));
    $nbcol = 0;
    $rooms = array();
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
      $id_room[$i] =  $row[2];
      $nbcol++;
        // On affiche pas toutes les ressources
        if (verif_acces_ressource(getUserName(), $id_room[$i])) {
        $room_name[$i] = $row[0];

        $statut_room[$id_room[$i]] =  $row[4];
        $statut_moderate[$id_room[$i]] =  $row[7];
        // Calcul du niveau d'acc�s aux fiche de r�servation d�taill�es des ressources
        $acces_fiche_reservation = verif_acces_fiche_reservation(getUserName(), $id_room[$i]);
        if ($row[1]) {
            $temp = "<br /><span class=\"small\">($row[1] ".($row[1] >1 ? get_vocab("number_max2") : get_vocab("number_max")).")</span>";
        } else {
            $temp="";
        }
        if ($statut_room[$id_room[$i]] == "0") $temp .= "<br /><span class=\"texte_ress_tempo_indispo\">".get_vocab("ressource_temporairement_indisponible")."</span>"; // Ressource temporairement indisponible
        if ($statut_moderate[$id_room[$i]] == "1") $temp .= "<br /><span class=\"texte_ress_moderee\">".get_vocab("reservations_moderees")."</span>"; // Ressource mod�r�e
        echo "<th style=\"width:$room_column_width%;\" ";
        // Si la ressource est temporairement indisponible, on le signale
        if ($statut_room[$id_room[$i]] == "0") echo " class='avertissement' ";
        echo ">" . htmlspecialchars($row[0])."\n";
        if (htmlspecialchars($row[3]. $temp != '')) {
            if (htmlspecialchars($row[3] != '')) $saut = "<br />"; else $saut = "";
            echo $saut.htmlspecialchars($row[3]) . $temp."\n";
        }

        echo "<br />";
        if (verif_display_fiche_ressource(getUserName(), $id_room[$i]) and $_GET['pview'] != 1)
            echo "<a href='javascript:centrerpopup(\"view_room.php?id_room=$id_room[$i]\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")' title=\"".get_vocab("fiche_ressource")."\">
           <img src=\"img_grr/details.png\" alt=\"d&eacute;tails\" class=\"".$class_image."\"  /></a>";
        if (authGetUserLevel(getUserName(),$id_room[$i]) > 2 and $_GET['pview'] != 1)
            echo "<a href='admin_edit_room.php?room=$id_room[$i]'><img src=\"img_grr/editor.png\" alt=\"configuration\" title=\"".get_vocab("Configurer la ressource")."\" width=\"30\" height=\"30\" class=\"".$class_image."\"  /></a>";
        // La ressource est-elle emprunt�e ?
        affiche_ressource_empruntee($id_room[$i]);

        echo "</th>";
        // stockage de la premi�re ligne :
        $tab[1][$i+1] = htmlspecialchars($row[0]);
        if (htmlspecialchars($row[3]. $temp != '')) {
            if (htmlspecialchars($row[3] != '')) $saut = "<br />"; else $saut = "";
            $tab[1][$i+1] .="<br />-".$saut."<i><span class =\"small\">". htmlspecialchars($row[3]) . $temp."\n</span></i>";
        }
        $tab[1][$i+1] .= "<br />";
        if (verif_display_fiche_ressource(getUserName(), $id_room[$i]))
            $tab[1][$i+1] .= "<a href='javascript:centrerpopup(\"view_room.php?id_room=$id_room[$i]\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")' title=\"".get_vocab("fiche_ressource")."\">
           <img src=\"img_grr/details.png\" alt=\"d�tails\" class=\"".$class_image."\"  /></a>";
        if (authGetUserLevel(getUserName(),$id_room[$i]) > 2 and $_GET['pview'] != 1)
            $tab[1][$i+1] .= "<a href='admin_edit_room.php?room=$id_room[$i]'><img src=\"img_grr/editor.png\" alt=\"configuration\" title=\"".get_vocab("Configurer la ressource")."\" width=\"30\" height=\"30\" class=\"".$class_image."\"  /></a>";
        // fin stockage de la premi�re ligne :


        $rooms[] = $row[2];
        $delais_option_reservation[$row[2]] = $row[6];
        }
    }
    if (count($rooms)==0) {
        echo "<br /><h1>".get_vocab("droits_insuffisants_pour_voir_ressources")."</h1><br />";
        include "include/trailer.inc.php";
        exit;
    }
    echo "<th  style=\"width:5%;\">&nbsp;</th></tr>\n";
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
      // On affiche pas toutes les ressources
      if (verif_acces_ressource(getUserName(), $id_room[$i])) {
        // Si la ressource est temporairement indisponible, on le signale, sinon, couleur normale
        if ($statut_room[$id_room[$i]] == "0") tdcell("avertissement"); else tdcell("cell_hours");
        if ($_GET['pview'] != 1)
           echo "<a title=\"".htmlspecialchars(get_vocab("see_week_for_this_room"))."\"  href=\"week.php?year=$year&amp;month=$month&amp;day=$day&amp;room=$id_room[$i]\">".get_vocab("week")."</a><br /><a title=\"".htmlspecialchars(get_vocab("see_month_for_this_room"))."\" href=\"month.php?year=$year&amp;month=$month&amp;day=$day&amp;room=$id_room[$i]\">".get_vocab("month")."</a>";
        if ($_GET['pview'] != 1)
           $tab[2][] = "<a title=\"".htmlspecialchars(get_vocab("see_week_for_this_room"))."\"  href=\"week.php?year=$year&amp;month=$month&amp;day=$day&amp;room=$id_room[$i]\">".get_vocab("week")."</a><br /><a title=\"".htmlspecialchars(get_vocab("see_month_for_this_room"))."\" href=\"month.php?year=$year&amp;month=$month&amp;day=$day&amp;room=$id_room[$i]\">".get_vocab("month")."</a>";
        else
           $tab[2][] = "";

        echo "</td>\n";
      }
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
            echo affiche_heure_creneau($t,$resolution)."</td>\n";
            $tab[$tab_ligne][] = affiche_heure_creneau($t,$resolution);
        }

        // D�but Deuxi�me boucle sur la liste des ressources du domaine
        while (list($key, $room) = each($rooms))
        {
          // On affiche pas toutes les ressources
          if (verif_acces_ressource(getUserName(), $room)) {
            if(isset($today[$room][$t]["id"])) // il y a une r�servation sur le cr�neau
            {
                $id    = $today[$room][$t]["id"];
                $color = $today[$room][$t]["color"];
                $descr = $today[$room][$t]["data"];
            }
            else
                unset($id);  // $id non d�fini signifie donc qu'il n'y a pas de r�sa sur le cr�neau

            // D�finition des couleurs de fond de cellule
            if  ((isset($id)) and (!est_hors_reservation(mktime(0,0,0,$month,$day,$year),$area)))   // 1er cas : il y a une r�servation sur le cr�neau
            {
                $c = $color;
            } else if ($statut_room[$room] == "0") // 2�me cas : ou bien la ressource est temporairement indisponible
                $c = "avertissement"; // on le signale par une couleur sp�cifique
            else  // 3�me cas : sinon, il s'agit d'un cr�neau libre
                $c = "empty_cell";

            // S'il s'agit d'un cr�neau avec une resa :
            // s'il s'agit du premier passage ($compteur[$id]=0), on fait un tdcell_rowspan
            // Sinon, pas de <td>
            if  ((isset($id)) and (!est_hors_reservation(mktime(0,0,0,$month,$day,$year),$area))) {
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

            if ((!isset($id)) or (est_hors_reservation(mktime(0,0,0,$month,$day,$year),$area))) // Le cr�neau est libre
            {
                $hour = date("H",$t);
                $minute  = date("i",$t);
                $date_booking = mktime($hour, $minute, 0, $month, $day, $year);
                if (est_hors_reservation(mktime(0,0,0,$month,$day,$year),$area)) {
                    echo "<img src=\"img_grr/stop.png\" alt=\"".get_vocab("reservation_impossible")."\"  title=\"".get_vocab("reservation_impossible")."\" width=\"16\" height=\"16\" class=\"".$class_image."\"  />";
                    $tab[$tab_ligne][] = "<img src=\"img_grr/stop.png\" alt=\"".get_vocab("reservation_impossible")."\"  title=\"".get_vocab("reservation_impossible")."\" width=\"16\" height=\"16\" class=\"".$class_image."\"  />";
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
                        echo "<a href=\"edit_entry.php?room=$room&amp;period=$time_t_stripped&amp;year=$year&amp;month=$month&amp;day=$day&amp;page=day\" title=\"".get_vocab("cliquez_pour_effectuer_une_reservation")."\" ><img src=\"img_grr/new.png\" alt=\"".get_vocab("add")."\" width=\"16\" height=\"16\" class=\"".$class_image."\" /></a>";
                        $tab[$tab_ligne][] = "<a href=\"edit_entry.php?room=$room&amp;period=$time_t_stripped&amp;year=$year&amp;month=$month&amp;day=$day&amp;page=day\" title=\"".get_vocab("cliquez_pour_effectuer_une_reservation")."\" ><img src=\"img_grr/new.png\" class=\"".$class_image."\" alt=\"".get_vocab("add")."\" width=\"16\" height=\"16\" /></a>";
                    } else {
                        echo "<a href=\"edit_entry.php?room=$room&amp;hour=$hour&amp;minute=$minute&amp;year=$year&amp;month=$month&amp;day=$day&amp;page=day\" title=\"".get_vocab("cliquez_pour_effectuer_une_reservation")."\" ><img src=\"img_grr/new.png\" class=\"".$class_image."\" alt=\"".get_vocab("add")."\" /></a>";
                        $tab[$tab_ligne][] =  "<a href=\"edit_entry.php?room=$room&amp;hour=$hour&amp;minute=$minute&amp;year=$year&amp;month=$month&amp;day=$day&amp;page=day\" title=\"".get_vocab("cliquez_pour_effectuer_une_reservation")."\" ><img src=\"img_grr/new.png\" class=\"".$class_image."\" alt=\"".get_vocab("add")."\" /></a>";
                    }
                } else {
                    echo "&nbsp;";
                    $tab[$tab_ligne][] = "&nbsp;";
                }
                echo "</td>\n";
            }
            elseif ($descr != "")
            {
                // si la r�servation est "en cours", on le signale
                if ((isset($today[$room][$t]["statut"])) and ($today[$room][$t]["statut"]!='-')) {
                    echo "&nbsp;<img src=\"img_grr/buzy.png\" alt=\"".get_vocab("ressource actuellement empruntee")."\" title=\"".get_vocab("ressource actuellement empruntee")."\" width=\"20\" height=\"20\" class=\"image\" />&nbsp;\n";
                    $tab[$tab_ligne][] = "&nbsp;<img src=\"img_grr/buzy.png\" alt=\"".get_vocab("ressource actuellement empruntee")."\" title=\"".get_vocab("ressource actuellement empruntee")."\" width=\"20\" height=\"20\" class=\"image\" />&nbsp;\n";
                }
                // si la r�servation est � confirmer, on le signale
                if (($delais_option_reservation[$room] > 0) and (isset($today[$room][$t]["option_reser"])) and ($today[$room][$t]["option_reser"]!=-1)) {
                    echo "&nbsp;<img src=\"img_grr/small_flag.png\" alt=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."\" title=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."&nbsp;".time_date_string_jma($today[$room][$t]["option_reser"],$dformat)."\" width=\"20\" height=\"20\" class=\"image\" />&nbsp;\n";
                    $tab[$tab_ligne][] = "&nbsp;<img src=\"img_grr/small_flag.png\" alt=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."\" title=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."&nbsp;".time_date_string_jma($today[$room][$t]["option_reser"],$dformat)."\" width=\"20\" height=\"20\" class=\"image\" />&nbsp;\n";
                }
                // si la r�servation est � mod�rer, on le signale
                if ((isset($today[$room][$t]["moderation"])) and ($today[$room][$t]["moderation"]=='1')) {
                    echo "&nbsp;<img src=\"img_grr/flag_moderation.png\" alt=\"".get_vocab("en_attente_moderation")."\" title=\"".get_vocab("en_attente_moderation")."\" class=\"image\" />&nbsp;\n";
                    $tab[$tab_ligne][] = "&nbsp;<img src=\"img_grr/flag_moderation.png\" alt=\"".get_vocab("en_attente_moderation")."\" title=\"".get_vocab("en_attente_moderation")."\" class=\"image\" />&nbsp;\n";
                }

                #if it is booked then show
                if (($statut_room[$room] == "1") or
                (($statut_room[$room] == "0") and (authGetUserLevel(getUserName(),$room) > 2) )) {
                    if ($acces_fiche_reservation) {
                        echo " <a title=\"".htmlspecialchars($today[$room][$t]["who"])."\" href=\"view_entry.php?id=$id&amp;day=$day&amp;month=$month&amp;year=$year&amp;page=day\">$descr</a>";
                       $tab[$tab_ligne][] = " <a title=\"".htmlspecialchars($today[$room][$t]["who"])."\" href=\"view_entry.php?id=$id&amp;day=$day&amp;month=$month&amp;year=$year&amp;page=day\">$descr</a>";
                    } else {
                        echo " $descr";
                        $tab[$tab_ligne][] = " $descr";
                    }
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
            echo affiche_heure_creneau($t,$resolution)."</td>\n";
            $tab[$tab_ligne][] = affiche_heure_creneau($t,$resolution);
        }

        echo "</tr>\n";

        reset($rooms);
        $tab_ligne++;
    }
    // r�p�tition de la ligne d'en-t�te
    echo "<tr>\n<th>&nbsp;</th>";
    for ($i = 0; $i < $nbcol; $i++)
    {
        // On affiche pas toutes les ressources
        if (verif_acces_ressource(getUserName(), $id_room[$i])) {
          echo "<th";
          if ($statut_room[$id_room[$i]] == "0") echo " class='avertissement' ";
          echo ">" . htmlspecialchars($room_name[$i])."</th>";
        }
    }
    echo "<th>&nbsp;</th></tr>\n";

    echo "</table>";
    show_colour_key($area);
}
grr_sql_free($res);
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

// Affichage d'un message pop-up
affiche_pop_up(get_vocab("message_records"),"user");
include "include/trailer.inc.php";
?>