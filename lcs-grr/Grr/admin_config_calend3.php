<?php
#########################################################################
#                    admin_config_calend3.php                           #
#                                                                       #
#            interface permettant la la réservation en bloc             #
#                  de journées entières                                 #
#               Dernière modification : 09/12/2007                      #
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
    $s .= "<table class=\"calendar2\" border=\"1\" cellspacing=\"2\">\n";
    $s .= "<tr>\n";
    $s .= "<td width=\"200\" align=center valign=top class=\"calendarHeader2\" colspan=7>$monthName&nbsp;$year</td>\n";
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


            if ($d > 0 && $d <= $daysInMonth)
            {
                $temp = mktime(0,0,0,$month,$d,$year);
                $day = grr_sql_query1("SELECT day FROM grr_calendrier_jours_cycle WHERE day='$temp'");
                $jour = grr_sql_query1("SELECT Jours FROM grr_calendrier_jours_cycle WHERE DAY='$temp'");
				if (intval($jour)>0)
					{
					$alt=get_vocab('jour_cycle')." ".$jour;
					$jour=ucfirst(substr(get_vocab("rep_type_6"),0,1)).$jour;
					}
				else
					{
					$alt=get_vocab('jour_cycle').' '.$jour;
					if (strlen($jour)>5) {
					    $jour = substr($jour,0,3)."..";
					}
					$jour="<font size=-1>".$jour."</font>";
					}
                if (!isset($_GET["pview"]))
                    if (($day < 0))
                        $s .= "<td class=\"calendar2\" align=\"center\" valign=\"top\" bgcolor=\"#FF8585\">";
                    else
                        $s .= "<td class=\"calendar2\" align=\"center\" valign=\"top\" bgcolor=\"#C0FF82\">";
                else
                    $s .= "<td align=center valign=top>";
                $s .= "<b><font color=black>".$d."</font></b>";
                // Pour aller checher la date ainsi que son Jour cycle
                $s .= "<br />";
                if (isset($_GET["pview"])) {
                    if (($day < 0))
                        $s .= "<font color=red><img src=\"img_grr/stop.png\" border=\"0\"  width=\"16\" height=\"16\" /></font>";
                    else
                        $s .= "<font color=blue size=\"+1\"><i>".$jour."</i> </font>";
                } else {
                    if (($day < 0))
                        $s .= "<font color=blue><a href=admin_calend_jour_cycle.php?page_calend=3&amp;date=".$temp."><img src=\"img_grr/stop.png\" border=\"0\" alt=\"(aucun)\"  width=\"16\" height=\"16\" /></a></font>";
                    else
                        $s .= "<font color=\"blue\"  size=\"+1\"><a href=admin_calend_jour_cycle.php?page_calend=3&amp;date=".$temp." title=\"".$alt."\" >".$jour."</a></font>";
                }

            } else {
                if (!isset($_GET["pview"]))
                    $s .= "<td class=\"calendar2\" align=\"center\" valign=\"top\">";
                else
                    $s .= "<td align=center valign=top>";

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
if (!        isset($_GET['pview']))
    include "admin_col_gauche.php";
// Affichage du tableau de choix des sous-configurations des jours/cycles (créer et voir le calendrier des jours/cycles)
if (!isset($_GET['pview']))
    include "include/admin_calend_jour_cycle.inc.php";
?>
<script src="functions.js" type="text/javascript" language="javascript"></script>
<?php

echo "<h3>".get_vocab('calendrier_jours/cycles');
if (!isset($_GET['pview']))
    echo grr_help("aide_grr_jours_cycle");
echo "</h3>";
if (!isset($_GET['pview'])) {
    echo get_vocab("explication_Jours_Cycles3");
    echo "<br />".get_vocab("explication_Jours_Cycles4")."<br />";
}


// Modification d'un jour cycle
// intval($jour)=-1 : pas de jour cycle
// intval($jour)=0 : Titre
// intval($jour)>0 : Jour cycle
if(!isset($_GET['pview']) and isset($_GET['date'])) {
    $jour_cycle = grr_sql_query1("select Jours from grr_calendrier_jours_cycle  WHERE DAY = ".$_GET['date']."");
    echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px; width: 80%; margin-left: auto; margin-right: auto;\">";
    echo "<legend>".get_vocab('Journee du')." ".affiche_date($_GET['date'])."</legend> ";
    echo "<form name=\"main\" method=\"get\" action=\"admin_calend_jour_cycle.php\">\n";
	echo "<input type='radio' name='selection' value='0'";
	if (intval($jour_cycle)==-1) echo " checked";
	echo " />".get_vocab('Cette journee ne correspond pas a un jour cycle')."<br />";
	echo "<input type='radio' name='selection' value='1'";
	if (intval($jour_cycle)>0) echo " checked";
    echo " />".get_vocab("nouveau_jour_cycle");
    echo "<SELECT name=\"newDay\" size=\"1\" onClick=\"check(1)\">";
    for($i=1;$i<(getSettingValue("nombre_jours_Jours/Cycles")+1);$i++){
        echo "<OPTION value=\"".$i."\" ";
        if ($jour_cycle == $i) echo " selected";
        echo " >j".$i."</OPTION>";
    }
    echo "</SELECT>\n";
    echo "<input name=\"newdate\" type=\"hidden\" value=\"".$_GET['date']."\" />";
    echo "<input type=\"hidden\" value=\"3\" name=\"page_calend\" /><br />";
	echo "<input type='radio' name='selection' value='2'";
	if (intval($jour_cycle)==0) echo " checked";
	echo " />".get_vocab('Nommer_journee_par_le_titre_suivant').get_vocab('deux_points');
    echo "<input type=\"text\" name=\"titre\" onfocus=\"check(2)\"";
	if (!intval($jour_cycle)>0) echo " value=\"".$jour_cycle."\"";
    echo "/><br /><br /><center><input type=\"submit\" value=\"Enregistrer\" /></center>\n";
    echo "</form>\n";
    echo "</fieldset>\n";
}
// Enregistrement du nouveau jour cycle
if (isset($_GET['selection']))	{
	if ($_GET['selection']==0) {
		grr_sql_query("delete from grr_calendrier_jours_cycle WHERE DAY = ".$_GET['newdate']."");
	}
	elseif ($_GET['selection']==1) {
    grr_sql_query("delete from grr_calendrier_jours_cycle WHERE DAY = ".$_GET['newdate']."");
    grr_sql_query("insert into grr_calendrier_jours_cycle set Jours =".$_GET['newDay'].", DAY = ".$_GET['newdate']."");
}
	elseif ($_GET['selection']==2) {
		grr_sql_query("delete from grr_calendrier_jours_cycle WHERE DAY = ".$_GET['newdate']."");
		grr_sql_query("insert into grr_calendrier_jours_cycle set Jours ='".protect_data_sql($_GET['titre'])."', DAY = ".$_GET['newdate']."");
	}
}


    $basetime = mktime(12,0,0,6,11+$weekstarts,2000);
    echo "<table cellspacing=\"20\" border=\"0\">\n";

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
if (!isset($_GET['pview'])) {
    echo "\n<a href=admin_calend_jour_cycle.php?page_calend=3&amp;pview=1 target='_blank'>Format Imprimable</a>\n";
    echo "</td>\n</tr>";
    echo "</table>";
}
// fin de l'affichage de la colonne de droite


?>
<SCRIPT type="text/javascript" LANGUAGE="JavaScript">
function check (select)
{
   document.forms['main'].selection[select].checked=true;
}</SCRIPT>
</body>
</html>