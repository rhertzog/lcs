<?php
#########################################################################
#                    admin_config_calend2.php                           #
#                                                                       #
#            interface permettant la la réservation en bloc             #
#                  de journées entières                                 #
#               Dernière modification : 10/12/2007                      #
#                                                                       #
#########################################################################
/*
 * Copyright 2003-2005 Laurent Delineau
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

$grr_script_name = "admin_calend_jour_cycle.php";
include "include/mrbs_sql.inc.php";

$back = '';
if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);

$day   = date("d");
$month = date("m");
$year  = date("Y");

function cal($month, $year)
{
    global $weekstarts;
    if (!isset($weekstarts)) $weekstarts = 0;
    $s = "";
    $daysInMonth = getDaysInMonth($month, $year);
    $date = mktime(12, 0, 0, $month, 1, $year);
    $first = (strftime("%w",$date) + 7 - $weekstarts) % 7;
    $monthName = utf8_strftime("%B",$date);
    $s .= "<table class=\"calendar2\" border=1 cellspacing=3>\n";
    $s .= "<tr>\n";
    $s .= "<td align=center valign=top class=\"calendarHeader2\" colspan=7>$monthName&nbsp;$year</td>\n";
    $s .= "</tr>\n";
    $s .= "<tr>\n";
    $s .= getFirstDays();
    $s .= "</tr>\n";
    $d = 1 - $first;
    while ($d <= $daysInMonth)
    {
        $s .= "<tr>\n";
        for ($i = 0; $i < 7; $i++)
        {
            $basetime = mktime(12,0,0,6,11+$weekstarts,2000);
            $show = $basetime + ($i * 24 * 60 * 60);
            $nameday = utf8_strftime('%A',$show);

            $s .= "<td class=\"calendar2\" align=center valign=top>";
            if ($d > 0 && $d <= $daysInMonth)
            {
                $temp = mktime(0,0,0,$month,$d,$year);
                $s .= $d;
                $day = grr_sql_query1("SELECT day FROM grr_calendrier_jours_cycle WHERE day='$temp'");
                $s .= "<br /><INPUT TYPE=\"checkbox\" NAME=\"$temp\" VALUE=\"$nameday\" ";
                if (!($day < 0)) $s .= "checked ";
                $s .= " />";
            } else {
                $s .= "&nbsp;";
            }
            $s .= "</td>\n";
            $d++;
        }
        $s .= "</tr>\n";
    }
    $s .= "</table>\n";
    return $s;
}

if(authGetUserLevel(getUserName(),-1) < 5)
{
    showAccessDenied($day, $month, $year, $area,$back);
    exit();
}

# print the page header
print_header("","","","",$type="with_session", $page="admin");
// Affichage de la colonne de gauche
include "admin_col_gauche.php";
// Affichage du tableau de choix des sous-configuration pour les Jours/Cycles (Créer et voir calendrier Jours/Cycle)
include "include/admin_calend_jour_cycle.inc.php";
?>
<script src="functions.js" type="text/javascript" language="javascript"></script>
<?php
echo "<h3>".get_vocab('calendrier_jours/cycles').grr_help("aide_grr_jours_cycle")."</h3>";
if (isset($_POST['record']) and  ($_POST['record'] == 'yes')) {
    // On vide la table grr_calendar
    $sql = "truncate table grr_calendrier_jours_cycle";
    if (grr_sql_command($sql) < 0) fatal_error(1, "<p>" . grr_sql_error());
    $result = 0;
    $end_bookings = getSettingValue("end_bookings");
    $n = getSettingValue("begin_bookings");
    $month = strftime("%m", getSettingValue("begin_bookings"));
    $year = strftime("%Y", getSettingValue("begin_bookings"));
    $day = 1;
    // Pour aller chercher le Jour cycle qui débutera le premier cycle de jours
    $m = getSettingValue("jour_debut_Jours/Cycles");
    while ($n <= $end_bookings) {
         $daysInMonth = getDaysInMonth($month, $year);
         $day = 1;
         while ($day <= $daysInMonth) {
             $n = mktime(0,0,0,$month,$day,$year);
             if (isset($_POST[$n])) {
                 // Le jour a été selectionné dans le calendrier
                 $starttime = mktime($morningstarts, 0, 0, $month, $day  , $year);
                 $endtime   = mktime($eveningends, 0, $resolution, $month, $day, $year);
                 // On efface toutes les résa en conflit
                 $sql = "select id from grr_room";
                 $res = grr_sql_query($sql);
                 if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
                     $result += grrDelEntryInConflict($row[0], $starttime, $endtime, 0, 0, 1);
                 // On enregistre la valeur dans grr_calendar
                 $m = cree_calendrier_date_valide($n,$m);
             }
             $day++;
         }
         $month++;
         if ($month == 13) {
             $year++;
             $month = 1;
         }
    }
}


    echo "<p>".get_vocab("les_journees_cochees_sont_valides").get_vocab("deux_points");
    echo "<br />* ".get_vocab("nombre_jours_Jours/Cycles").get_vocab("deux_points").getSettingValue("nombre_jours_Jours/Cycles");
    echo "<br />* ".get_vocab("debut_Jours/Cycles").get_vocab("deux_points").getSettingValue("jour_debut_Jours/Cycles");


    echo "<br /><br />".get_vocab("explication_Jours_Cycles2")."</p>";
    echo "<table cellpadding=\"3\">\n";
    $basetime = mktime(12,0,0,6,11+$weekstarts,2000);
    for ($i = 0; $i < 7; $i++)
    {
        $show = $basetime + ($i * 24 * 60 * 60);
        $lday = utf8_strftime('%A',$show);
        echo "<tr>\n";
        echo "<td><span class='small'><a href='admin_calend_jour_cycle.php' onclick=\"setCheckboxesGrr('formulaire', true, '$lday' ); return false;\">".get_vocab("check_all_the").$lday."s</a></span></td>\n";
        echo "<td><span class='small'><a href='admin_calend_jour_cycle.php' onclick=\"setCheckboxesGrr('formulaire', false, '$lday' ); return false;\">".get_vocab("uncheck_all_the").$lday."s</a></span></td>\n";
        echo "</tr>\n";
    }
    echo "<tr>\n<td><span class='small'><a href='admin_calend_jour_cycle.php' onclick=\"setCheckboxesGrr('formulaire', false, 'all'); return false;\">".get_vocab("uncheck_all_")."</a></span></td>\n";
    echo "<td></td></tr>\n";
    echo "</table>\n";
    echo "<form action=\"admin_calend_jour_cycle.php?page_calend=2\" method=\"post\" name=\"formulaire\">\n";
    echo "<table cellspacing=20>\n";

    $n = getSettingValue("begin_bookings");
    $end_bookings = getSettingValue("end_bookings");

    $debligne = 1;
    $month = strftime("%m", getSettingValue("begin_bookings"));
    $year = strftime("%Y", getSettingValue("begin_bookings"));

    while ($n <= $end_bookings) {
        if ($debligne == 1) {
            echo "<tr>\n";
            $inc = 0;
            $debligne = 0;
        }
        $inc++;
        echo "<td>\n";
        echo cal($month, $year);
        echo "</td>";
        if ($inc == 3) {
            echo "</tr>";
            $debligne = 1;
        }
        $month++;
        if ($month == 13) {
            $year++;
            $month = 1;
        }
        $n = mktime(0,0,0,$month,1,$year);
    }
    echo "</table>";
    echo "<center><div id=\"fixe\"><input type=\"submit\" onclick=\"return confirmlink(this, '".AddSlashes(get_vocab("avertissement_effacement"))."', '".get_vocab("admin_config_calend1.php")."')\" name=\"ok\" value=\"".get_vocab("save")."\" /></div></center>\n";
    echo "<input type=\"hidden\" name=\"record\" value=\"yes\" />\n";
    echo "</form>";


// fin de l'affichage de la colonne de droite
echo "</td></tr></table>";

?>


</body>
</html>