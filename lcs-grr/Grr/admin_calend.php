<?php
/**
 * admin_calend.php
 * interface permettant la la r�servation en bloc de journ�es enti�res
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2009-09-29 18:02:56 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @author    Marc-Henri PAMISEUX <marcori@users.sourceforge.net>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @copyright Copyright 2008 Marc-Henri PAMISEUX
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   admin
 * @version   $Id: admin_calend.php,v 1.12 2009-09-29 18:02:56 grr Exp $
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
 * $Log: admin_calend.php,v $
 * Revision 1.12  2009-09-29 18:02:56  grr
 * *** empty log message ***
 *
 * Revision 1.11  2009-06-04 15:30:17  grr
 * *** empty log message ***
 *
 * Revision 1.10  2009-04-14 12:59:16  grr
 * *** empty log message ***
 *
 * Revision 1.9  2009-04-09 14:52:31  grr
 * *** empty log message ***
 *
 * Revision 1.8  2009-02-27 13:28:19  grr
 * *** empty log message ***
 *
 * Revision 1.7  2008-11-16 22:00:58  grr
 * *** empty log message ***
 *
 * Revision 1.6  2008-11-11 22:01:14  grr
 * *** empty log message ***
 *
 *
 */

include "include/admin.inc.php";
$grr_script_name = "admin_calend.php";

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
    $s .= "<table class=\"calendar2\" border=\"1\" cellspacing=\"3\">\n";
    $s .= "<tr>\n";
    $s .= "<td class=\"calendarHeader2\" colspan=\"7\">$monthName&nbsp;$year</td>\n";
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

            $s .= "<td class=\"calendar2\" valign=\"top\">";
            if ($d > 0 && $d <= $daysInMonth)
            {
                $s .= $d;
                $temp = mktime(0,0,0,$month,$d,$year);
                // On teste si le jour est f�ri� :
                $test = grr_sql_query1("select DAY from ".TABLE_PREFIX."_calendar where DAY = '".$temp."'");
                if ($test == '-1')
                    $s .= "<br /><input type=\"checkbox\" name=\"$temp\" value=\"$nameday\" />";
                else
                    $s .= "<br /><input type=\"checkbox\" name=\"$temp\" value=\"$nameday\"  disabled />";
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


if(authGetUserLevel(getUserName(),-1,'area') < 5)
{
    showAccessDenied($day, $month, $year, '',$back);
    exit();
}

// Initialisation
$etape = isset($_POST["etape"]) ? $_POST["etape"] : NULL;
$areas = isset($_POST["areas"]) ? $_POST["areas"] : NULL;
$rooms = isset($_POST["rooms"]) ? $_POST["rooms"] : NULL;
$name = isset($_POST["name"]) ? $_POST["name"] : NULL;
$beneficiaire = isset($_POST["beneficiaire"]) ? $_POST["beneficiaire"] : NULL;
$description = isset($_POST["description"]) ? $_POST["description"] : NULL;
$type_ = isset($_POST["type_"]) ? $_POST["type_"] : NULL;
$type_resa = isset($_POST["type_resa"]) ? $_POST["type_resa"] : NULL;
$hour = isset($_POST["hour"]) ? $_POST["hour"] : NULL;
settype($hour,"integer");
$end_hour = isset($_POST["end_hour"]) ? $_POST["end_hour"] : NULL;
settype($end_hour,"integer");
$minute = isset($_POST["minute"]) ? $_POST["minute"] : NULL;
settype($minute,"integer");
$end_minute = isset($_POST["end_minute"]) ? $_POST["end_minute"] : NULL;
settype($end_minute,"integer");
$period = isset($_POST["period"]) ? $_POST["period"] : NULL;
$end_period = isset($_POST["end_period"]) ? $_POST["end_period"] : NULL;
$all_day = isset($_POST["all_day"]) ? $_POST["all_day"] : NULL;

# print the page header
print_header("","","","",$type="with_session", $page="admin");
// Affichage de la colonne de gauche
include "admin_col_gauche.php";

echo "<h2>".get_vocab('admin_calendar_title.php')."</h2>\n";


if (isset($_POST['record']) and  ($_POST['record'] == 'yes')) {
    $etape = 4;
    $result = 0;
    $end_bookings = getSettingValue("end_bookings");
    // On reconstitue le tableau des ressources
    $sql = "select id from ".TABLE_PREFIX."_room";
    $res = grr_sql_query($sql);
    if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
        $temp = "id_room_".$row[0];
        if ((isset($_POST[$temp])) and verif_acces_ressource(getUserName(),$row[0])) {
            // La ressource est selectionn�e
//            $rooms[] = $id;
            // On r�cup�re les donn�es du domaine
            $area_id = grr_sql_query1("SELECT area_id FROM ".TABLE_PREFIX."_room WHERE id = '".$row[0]."'");
            $id_site = grr_sql_query1("SELECT id_site FROM ".TABLE_PREFIX."_j_site_area WHERE id_area = '".$area_id."'");
            if(authGetUserLevel(getUserName(),$id_site,'site') >= 5) {
             get_planning_area_values($area_id);
             $n = getSettingValue("begin_bookings");
             $month = strftime("%m", getSettingValue("begin_bookings"));
             $year = strftime("%Y", getSettingValue("begin_bookings"));
             $day = 1;
             while ($n <= $end_bookings) {
                $daysInMonth = getDaysInMonth($month, $year);
                $day = 1;
                while ($day <= $daysInMonth) {
                    $n = mktime(0,0,0,$month,$day,$year);
                    if (isset($_POST[$n])) {
                    $erreur = 'n';
                    // Le jour a �t� selectionn� dans le calendrier
                        if (!isset($all_day)) {
                        // Cas des r�servation par cr�neaux pr�-d�finis
                          if($enable_periods=='y') {
                              $resolution = 60;
                              $hour = 12;
                              $end_hour = 12;
                              if (isset($period))
                                  $minute = $period;
                              else
                                  $minute = 0;
                              if (isset($end_period))
                                  $end_minute = $end_period+1;
                              else
                                  $end_minute = $eveningends_minutes+1;
                            }
                            $starttime = mktime($hour, $minute, 0, $month, $day, $year);
                            $endtime   = mktime($end_hour, $end_minute, 0, $month, $day, $year);
                            if ($endtime <= $starttime) $erreur = 'y';
                        } else {
                            $starttime = mktime($morningstarts, 0, 0, $month, $day  , $year);
                            $endtime   = mktime($eveningends, $eveningends_minutes , $resolution, $month, $day, $year);
                        }
                        if ($erreur != 'y') {
                          // On efface toutes les r�sa en conflit
                          $result += grrDelEntryInConflict($row[0], $starttime, $endtime, 0, 0, 1);
                          // S'il s'agit d'une action de r�servation, on r�serve !
                          if ($type_resa == "resa") {
                              // Par s�curit�, on teste quand m�me s'il reste des conflits
                              $err = mrbsCheckFree($row[0], $starttime, $endtime, 0,0);
                              if (!$err) {
                                  mrbsCreateSingleEntry($starttime, $endtime, 0, 0, $row[0], getUserName(), $beneficiaire, "", $name, $type_, $description, -1,array(),0,0,'-');
                              }
                          }
                        }
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
        }
    }
}

if ($etape==4) {
    if ($result == '') $result = 0;
    if ($type_resa == "resa") {
        echo "<h3 style=\"text-align:center;\">".get_vocab("reservation_en_bloc")."</h3>\n";
        echo "<h3>".get_vocab("reservation_en_bloc_result")."</h3>\n";
        if ($result != 0) echo "<p>".get_vocab("reservation_en_bloc_result2")."<b>".$result."</b></p>\n";
    } else {
        echo "<h3 style=\"text-align:center;\" class=\"avertissement\">".get_vocab("suppression_en_bloc")."</h3>\n";
        echo "<h3>".get_vocab("suppression_en_bloc_result")."<b>".$result."</b></h3>\n";
    }

}
if ($etape==3) {
    // Etape N� 3
    echo "<h3 style=\"text-align:center;\">".get_vocab("etape_n")."3/3</h3>\n";
    if ($type_resa == "resa")
        echo "<h3 style=\"text-align:center;\">".get_vocab("reservation_en_bloc")."</h3>\n";
    else
        echo "<h3 style=\"text-align:center;\"  class=\"avertissement\">".get_vocab("suppression_en_bloc")."</h3>\n";

    if (!isset($rooms)) {
        echo "<h3>".get_vocab("noarea")."</h3>\n";
        // fin de l'affichage de la colonne de droite
        echo "</td></tr></table>\n";
        echo "</body></html>\n";
        die();
    }

    echo "<form action=\"admin_calend.php\" method=\"post\" id=\"formulaire\" >\n";

    $test_enable_periods_y = 0;
    $test_enable_periods_n = 0;

    foreach ( $rooms as $room_id ) {
        $temp = "id_room_".$room_id;
        echo "<div><input type=\"hidden\" name=\"".$temp."\" value=\"yes\" /></div>\n";
        $area_id = grr_sql_query1("SELECT area_id FROM ".TABLE_PREFIX."_room WHERE id = '".$room_id."'");
        $test_enable_periods_y += grr_sql_query1("select count(enable_periods) FROM ".TABLE_PREFIX."_area WHERE (id = '".$area_id."' and enable_periods='y')");
        $test_enable_periods_n += grr_sql_query1("select count(enable_periods) FROM ".TABLE_PREFIX."_area WHERE (id = '".$area_id."' and enable_periods='n')");

    }
    // On teste si tous les domaines selectionn�s sont du m�me type d'affichage � savoir :
    // soit des cr�neaux de r�servation bas�s sur le temps,
    // soit des cr�neaux de r�servation bas�s sur des intitul�s pr�-d�finis.
    if ($test_enable_periods_y == 0)
        $all_enable_periods = 'n';
    else if ($test_enable_periods_n == 0)
        $all_enable_periods = 'y';
    else
        $all_enable_periods = 'incompatible';

    if ($all_enable_periods != "incompatible") {
        // On propose une heure de d�but et une heure de fin de r�servation
        $texte_debut_fin_reservation = "";
        // On prend comme domaine de r�f�rence le dernier domaine de la boucle  foreach ( $rooms as $room_id ) {
        // C'est pas parfait mais bon !
        get_planning_area_values($area_id);

        if ($all_enable_periods=='y') {
            // Cr�neaux bas�s sur les intitul�s pr�-d�finis
            // Heure ou cr�neau de d�but de r�servation
            $texte_debut_fin_reservation .= "<b>".get_vocab("date").get_vocab("deux_points")."</b>";
            $texte_debut_fin_reservation .= "<br />".get_vocab("period")."\n";
            $texte_debut_fin_reservation .= "<select name=\"period\">";
            foreach ($periods_name as $p_num => $p_val)
                {
                $texte_debut_fin_reservation .= "<option value=\"$p_num\">$p_val</option>";
                }
            $texte_debut_fin_reservation .= "</select>\n";
            $texte_debut_fin_reservation .= "<br /><br /><b>".get_vocab("fin_reservation").get_vocab("deux_points")."</b>";
            $texte_debut_fin_reservation .= "<br />".get_vocab("period")."\n";
            $texte_debut_fin_reservation .= "<select name=\"end_period\">";
            foreach ($periods_name as $p_num => $p_val)
                {
                $texte_debut_fin_reservation .= "<option value=\"$p_num\">$p_val</option>";
                }
            $texte_debut_fin_reservation .= "</select>\n";

        } else {
            // Cr�neaux bas�s sur le temps
            // Heure ou cr�neau de d�but de r�servation
            $texte_debut_fin_reservation .= "<b>".get_vocab("date").get_vocab("deux_points")."</b>";
            $texte_debut_fin_reservation .= "<br />".get_vocab("time")."
            <input name=\"hour\" size=\"2\" value=\"".$morningstarts."\" MAXLENGTH=2 />
            <input name=\"minute\" size=\"2\" value=\"0\" MAXLENGTH=2 />";
            $texte_debut_fin_reservation .= "<br /><br /><b>".get_vocab("fin_reservation").get_vocab("deux_points")."</b>";
            $texte_debut_fin_reservation .= "<br />".get_vocab("time")."
            <input name=\"end_hour\" size=\"2\" value=\"".$morningstarts."\" MAXLENGTH=2 />
            <input name=\"end_minute\" size=\"2\" value=\"0\" MAXLENGTH=2 />";

        }

      }  else {
          $texte_debut_fin_reservation = get_vocab("domaines_de_type_incompatibles");
          echo "<input type=\"hidden\" name=\"all_day\" value=\"y\" />";
      }

    echo "<table cellpadding=\"3\" width=\"100%\" border=\"0\">\n";
    $basetime = mktime(12,0,0,6,11+$weekstarts,2000);
    for ($i = 0; $i < 7; $i++)
    {
        $show = $basetime + ($i * 24 * 60 * 60);
        $lday = utf8_strftime('%A',$show);
        echo "<tr>\n";
        echo "<td><span class='small'><a href='admin_calend.php' onclick=\"setCheckboxesGrr(document.getElementById('formulaire'), true, '$lday' ); return false;\">".get_vocab("check_all_the").$lday."s</a></span></td>\n";
        echo "<td><span class='small'><a href='admin_calend.php' onclick=\"setCheckboxesGrr(document.getElementById('formulaire'), false, '$lday' ); return false;\">".get_vocab("uncheck_all_the").$lday."s</a></span></td>\n";
        if ($i == 0) echo "<td rowspan=\"8\">&nbsp;&nbsp;</td><td rowspan=\"8\">$texte_debut_fin_reservation</td>\n";
        echo "</tr>\n";
    }
    echo "<tr>\n<td><span class='small'><a href='admin_calend.php' onclick=\"setCheckboxesGrr(document.getElementById('formulaire'), false, 'all'); return false;\">".get_vocab("uncheck_all_")."</a></span></td>\n";
    echo "<td>&nbsp;</td></tr>\n";
    echo "</table>\n";
    echo "<table cellspacing=\"20\">\n";

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
    if ($inc < 3) {
      $k=$inc;
      while($k < 3) {
        echo "<td>&nbsp;</td>\n";
        $k++;
      } // while
      echo "</tr>";
    }

    echo "</table>";
    echo "<div id=\"fixe\"><input type=\"submit\" name=\"".get_vocab('save')."\" /></div>\n";
    echo "<div>\n<input type=\"hidden\" name=\"record\" value=\"yes\" />\n";
    echo "<input type=\"hidden\" name=\"etape\" value=\"4\" />\n";
    echo "<input type=\"hidden\" name=\"name\" value=\"".$name."\" />\n";
    echo "<input type=\"hidden\" name=\"description\" value=\"".$description."\" />\n";
    echo "<input type=\"hidden\" name=\"beneficiaire\" value=\"".$beneficiaire."\" />\n";
    echo "<input type=\"hidden\" name=\"type_\" value=\"".$type_."\" />\n";
    echo "<input type=\"hidden\" name=\"type_resa\" value=\"".$type_resa."\" />\n";
    echo "</div>\n</form>";
} else if ($etape==2) {
    // Etape 2
    ?>
    <script  type="text/javascript"  >
    <?php
    if ($type_resa == "resa") {
    ?>
    function validate_and_submit ()
    {
      if(document.getElementById("main").name.value == "") {
        alert ( "<?php echo get_vocab('you_have_not_entered') . '\n' . get_vocab('brief_description') ?>");
        return false;
      } else if (document.getElementById("main").elements[3].value =='') {
        alert("<?php echo get_vocab("choose_a_room"); ?>");
        return false;
      } else if  (document.getElementById("main").type_.value=='0') {
        alert("<?php echo get_vocab("choose_a_type"); ?>");
        return false;
      } else
        return true;
    }
    <?php
    } else {
    ?>

    function validate_and_submit ()
    {
      if (document.getElementById("main").elements[0].value =='') {
        alert("<?php echo get_vocab("choose_a_room"); ?>");
        return false;
      } else {
        return true;
      }
    }
    <?php
    }
    ?>
    </script>
    <?php

    echo "<h3 style=\"text-align:center;\">".get_vocab("etape_n")."2/3</h3>\n";
    if ($type_resa == "resa")
        echo "<h3 style=\"text-align:center;\">".get_vocab("reservation_en_bloc")."</h3>\n";
    else
        echo "<h3 style=\"text-align:center;\"  class=\"avertissement\">".get_vocab("suppression_en_bloc")."</h3>\n";

    if (!isset($areas)) {
        echo "<h3>".get_vocab("noarea")."</h3>\n";
        // fin de l'affichage de la colonne de droite
        echo "</td></tr></table>\n";
        echo "</body></html>\n";
        die();
    }

    // Choix des ressources
    echo "<form action=\"admin_calend.php\" method=\"post\" id=\"main\" onsubmit=\"return validate_and_submit();\">\n";
    echo "<table border=\"0\">\n";
    if ($type_resa == "resa") {
      echo "<tr><td class=\"CR\"><b>".ucfirst(trim(get_vocab("reservation au nom de"))).get_vocab("deux_points")."</b></td>\n\n";
      echo "<td class=\"CL\"><select size=\"1\" name=\"beneficiaire\">\n";
      $sql = "SELECT DISTINCT login, nom, prenom FROM ".TABLE_PREFIX."_utilisateurs WHERE  (etat!='inactif' and statut!='visiteur' ) order by nom, prenom";
      $res = grr_sql_query($sql);
      if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
        echo "<option value='$row[0]' ";
        if (getUserName() == $row[0])  echo " selected=\"selected\"";
        echo ">$row[1]  $row[2] </option>";
     }
     echo "</select></td>\n</tr>\n";

      echo "<tr><td class=\"CR\"><b>".get_vocab("namebooker").get_vocab("deux_points")."</b></td>\n";
      echo "<td class=\"CL\"><input name=\"name\" size=\"40\" value=\"\" /></td></tr>";
      echo "<tr><td class=\"TR\"><b>".get_vocab("fulldescription")."</b></td>\n";
      echo "<td class=\"TL\"><textarea name=\"description\" rows=\"8\" cols=\"40\" ></textarea></td></tr>";
    }
    echo "<tr><td class=\"CR\"><b>".get_vocab("rooms").get_vocab("deux_points")."</b></td>\n";
    echo "<td class=\"CL\" valign=\"top\"><table border=\"0\"><tr><td>";
    echo "<select name=\"rooms[]\" multiple=\"multiple\">";
    foreach ( $areas as $area_id ) {
        # then select the rooms in that area
        $sql = "select id, room_name from ".TABLE_PREFIX."_room where area_id=$area_id ";
        // tableau des ressources auxquelles l'utilisateur n'a pas acc�s
        $tab_rooms_noaccess = verif_acces_ressource(getUserName(), 'all');
        // on ne cherche pas parmi les ressources invisibles pour l'utilisateur
        foreach($tab_rooms_noaccess as $key){
          $sql .= " and id != $key ";
        }

        $sql .= "order by order_display,room_name";
        $res = grr_sql_query($sql);
        if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
        {
            echo "<option value=\"".$row[0]."\">".$row[1]."</option>";
        }
    }
    echo "</select></td><td>".get_vocab("ctrl_click")."</td></tr></table>\n";
    echo "</td></tr>\n";
    if ($type_resa == "resa") {
      echo "<tr><td class=\"CR\"><b>".get_vocab("type").get_vocab("deux_points")."</b></td>\n";
      echo "<td class=\"CL\"><select name=\"type_\">\n";
      echo "<option value='0'>".get_vocab("choose")."</option>\n";
      $sql = "SELECT t.type_name, t.type_letter FROM ".TABLE_PREFIX."_type_area t
      LEFT JOIN ".TABLE_PREFIX."_j_type_area j on j.id_type=t.id
      WHERE (j.id_area  IS NULL or (";
      $ind = 0;
      foreach ( $areas as $area_id ) {
          if ($ind != 0) $sql .= " and ";
          $sql .= "j.id_area != '".$area_id."'";
          $ind = 1;
      }
      $sql .= "))
      ORDER BY order_display";
      $res = grr_sql_query($sql);
      if ($res) {
      for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
        echo "<option value=\"".$row[1]."\" ";
        if ($type_ == $row[1]) echo " selected=\"selected\"";
        echo " >".$row[0]."</option>\n";
        }
      }
      echo "</select></td></tr>";
    }
    echo "</table>\n";
    echo "<div><input type=\"hidden\" name=\"etape\" value=\"3\" />\n";
    echo "<input type=\"hidden\" name=\"type_resa\" value=\"".$type_resa."\" />\n";
    echo "<input type=\"submit\" value=\"".get_vocab("next")."\" />";
    echo "</div></form>";

} else if (!$etape) {
    // Etape 1 :
    echo get_vocab("admin_calendar_explain_1.php");
    echo "<h3 style=\"text-align:center;\">".get_vocab("etape_n")."1/3</h3>\n";
    // Choix des domaines
    echo "<form action=\"admin_calend.php\" method=\"post\">\n";
    echo "<table border=\"1\"><tr><td>\n";
    echo "<p><b>".get_vocab("choix_domaines")."</b></p>";
    echo "<select name=\"areas[]\" multiple=\"multiple\">\n";
    if(authGetUserLevel(getUserName(),-1) >= 6)
      $sql = "select id, area_name from ".TABLE_PREFIX."_area
      order by order_display, area_name";
    else
      $sql = "select a.id, a.area_name from ".TABLE_PREFIX."_area a, ".TABLE_PREFIX."_j_site_area j, ".TABLE_PREFIX."_site s, ".TABLE_PREFIX."_j_useradmin_site u
      WHERE a.id=j.id_area and j.id_site = s.id and s.id=u.id_site and u.login='".getUserName()."'
      order by a.order_display, a.area_name";

    $res = grr_sql_query($sql);
    if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
        echo "<option value=\"".$row[0]."\">".$row[1]."</option>\n";
    }
    echo "</select><br />".get_vocab("ctrl_click");
    echo "</td><td>";
    echo "<p><b>".get_vocab("choix_action")."</b></p>";
    echo "<table><tr>";
    echo "<td><input type=\"radio\" name=\"type_resa\" value=\"resa\" checked=\"checked\" /></td>\n";
    echo "<td>".get_vocab("reservation_en_bloc")."</td>\n";
    echo "</tr><tr>\n";
    echo "<td><input type=\"radio\" name=\"type_resa\" value=\"suppression\" /></td>\n";
    echo "<td>".get_vocab("suppression_en_bloc")."</td>\n";
    echo "</tr></table>\n";
    echo "</td></tr></table>\n";
    echo "<div><input type=\"hidden\" name=\"etape\" value=\"2\" />\n";
    echo "<br /><input type=\"submit\" name=\"Continuer\" value=\"".get_vocab("next")."\" />\n";
    echo "</div></form>\n";
}

// fin de l'affichage de la colonne de droite
echo "</td></tr></table>";

?>


</body>
</html>