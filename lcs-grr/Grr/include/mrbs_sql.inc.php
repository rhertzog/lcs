<?php
/**
 * mrbs_sql.inc.php
 * Biblioth�que de fonctions propres � l'application GRR
 *
 * Derni�re modification : $Date: 2010-01-06 10:21:20 $
 *
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @author    Marc-Henri PAMISEUX <marcori@users.sourceforge.net>
 * @copyright Copyright 2003-2005 Laurent Delineau
 * @copyright Copyright 2008 Marc-Henri PAMISEUX
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   include
 * @version   $Id: mrbs_sql.inc.php,v 1.16 2010-01-06 10:21:20 grr Exp $
 * @filesource
 *
 * This file is part of GRR.
 * D'apr�s http://mrbs.sourceforge.net/
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
 *
 */

/** mrbsCheckFree()
 *
 * Check to see if the time period specified is free
 *
 * $room_id   - Which room are we checking
 * $starttime - The start of period
 * $endtime   - The end of the period
 * $ignore    - An entry ID to ignore, 0 to ignore no entries
 * $repignore - A repeat ID to ignore everything in the series, 0 to ignore no series
 *
 * Returns:
 *   nothing   - The area is free
 *   something - An error occured, the return value is human readable
 */
function mrbsCheckFree($room_id, $starttime, $endtime, $ignore, $repignore)
{
    global $vocab;
    # Select any meetings which overlap ($starttime,$endtime) for this room:
    $sql = "SELECT id, name, start_time FROM ".TABLE_PREFIX."_entry WHERE
        start_time < '".$endtime."' AND end_time > '".$starttime."'
        AND room_id = '".$room_id."'";

    if ($ignore > 0)
        $sql .= " AND id <> $ignore";
    if ($repignore > 0)
        $sql .= " AND repeat_id <> $repignore";
    $sql .= " ORDER BY start_time";

    $res = grr_sql_query($sql);
    if(! $res)
        return grr_sql_error();
    if (grr_sql_count($res) == 0)
    {
        grr_sql_free($res);
        return "";
    }
    // Get the room's area ID for linking to day, week, and month views:
    $area = mrbsGetRoomArea($room_id);

    // Build a string listing all the conflicts:
    $err = "";
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
        $starts = getdate($row[2]);
        $param_ym = "area=$area&amp;year=$starts[year]&amp;month=$starts[mon]";
        $param_ymd = $param_ym . "&amp;day=$starts[mday]";

        $err .= "<li><a href=\"view_entry.php?id=$row[0]\">$row[1]</a>"
        . " ( " . utf8_strftime('%A %d %B %Y %T', $row[2]) . ") "
        . "(<a href=\"day.php?$param_ymd\">".get_vocab("viewday")."</a>"
        . " | <a href=\"week.php?room=$room_id&amp;$param_ymd\">".get_vocab("viewweek")."</a>"
        . " | <a href=\"month.php?room=$room_id&amp;$param_ym\">".get_vocab("viewmonth")."</a>)\n";
    }
    return $err;
}

/** grrCheckOverlap()
 *
 * Dans le cas d'une r�servation avec p�riodicit�,
 * V�rifie que les diff�rents cr�neaux ne se chevaussent pas.
 *
 * $reps : tableau des d�buts de r�servation
 * $diff : dur�e d'une r�servation
 */
function grrCheckOverlap($reps, $diff)
{
    $err = "";
    for($i = 1; $i < count($reps); $i++) {
        if ($reps[$i] < ($reps[0] + $diff)) {
            $err = "yes";
        }
    }
    if ($err=="")
        return TRUE;
    else
        return FALSE;
}


/** grrDelEntryInConflict()
 *
 *  Efface les r�servation qui sont en partie ou totalement dans le cr�neau $starttime<->$endtime
 *
 * $room_id   - Which room are we checking
 * $starttime - The start of period
 * $endtime   - The end of the period
 * $ignore    - An entry ID to ignore, 0 to ignore no entries
 * $repignore - A repeat ID to ignore everything in the series, 0 to ignore no series
 *
 * Returns:
 *   nothing   - The area is free
 *   something - An error occured, the return value is human readable
 *   if $flag = 1, return the number of erased entries.
 */
function grrDelEntryInConflict($room_id, $starttime, $endtime, $ignore, $repignore, $flag)
{
    global $vocab, $dformat;
    # Select any meetings which overlap ($starttime,$endtime) for this room:
    $sql = "SELECT id FROM ".TABLE_PREFIX."_entry WHERE
        start_time < '".$endtime."' AND end_time > '".$starttime."'
        AND room_id = '".$room_id."'";
    if ($ignore > 0)
        $sql .= " AND id <> $ignore";
    if ($repignore > 0)
        $sql .= " AND repeat_id <> $repignore";
    $sql .= " ORDER BY start_time";

    $res = grr_sql_query($sql);
    if(! $res)
        return grr_sql_error();
    if (grr_sql_count($res) == 0)
    {
        grr_sql_free($res);
        return "";
    }
    # Efface les r�sas concern�es
    $err = "";
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
        if (getSettingValue("automatic_mail") == 'yes') $_SESSION['session_message_error'] = send_mail($row[0],3,$dformat);
        $result = mrbsDelEntry(getUserName(), $row[0], NULL , 1);
    }
    if ($flag == 1) return $result;
}


/** mrbsDelEntry()
 *
 * Delete an entry, or optionally all entrys.
 *
 * $user   - Who's making the request
 * $id     - The entry to delete
 * $series - If set, delete the series, except user modified entrys
 * $all    - If set, include user modified entrys in the series delete
 *
 * Returns:
 *   0        - An error occured
 *   non-zero - The entry was deleted
 */
function mrbsDelEntry($user, $id, $series, $all)
{
    global $correct_diff_time_local_serveur, $enable_periods;
    $date_now = mktime();
    $id_room = grr_sql_query1("select room_id FROM ".TABLE_PREFIX."_entry WHERE id='".$id."'");
    $repeat_id = grr_sql_query1("SELECT repeat_id FROM ".TABLE_PREFIX."_entry WHERE id='".$id."'");
    if ($repeat_id < 0)
        return 0;

    $sql = "SELECT beneficiaire, id, entry_type FROM ".TABLE_PREFIX."_entry WHERE ";

    if(($series) and ($repeat_id > 0))
        $sql .= "repeat_id='".protect_data_sql($repeat_id)."'";
    else
        $sql .= "id='".$id."'";

    $res = grr_sql_query($sql);

    $removed = 0;

    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
        if(!getWritable($row[0], $user, $id))
            continue;

        if (!verif_booking_date($user, $row[1], $id_room, "", $date_now, $enable_periods, ""))
            continue;

        if($series && $row[2] == 2 && !$all)
            continue;

        if (grr_sql_command("DELETE FROM ".TABLE_PREFIX."_entry WHERE id=" . $row[1]) > 0)
            $removed++;
        grr_sql_command("DELETE FROM ".TABLE_PREFIX."_entry_moderate WHERE id=" . $row[1]);
    }

    if ($repeat_id > 0 &&
            grr_sql_query1("SELECT count(*) FROM ".TABLE_PREFIX."_entry WHERE repeat_id='".protect_data_sql($repeat_id)."'") == 0)
        grr_sql_command("DELETE FROM ".TABLE_PREFIX."_repeat WHERE id='".$repeat_id."'");

    return $removed > 0;
}


/*
  mrbsGetAreaIdFromRoomId($room_id)
*/

function mrbsGetAreaIdFromRoomId($room_id)
{
  // Avec la room_id on r�cup�re l'area_id
  $sqlstring = "select area_id from ".TABLE_PREFIX."_room where id=$room_id";
  $result = grr_sql_query($sqlstring);

  if (! $result) fatal_error(1, grr_sql_error());
  if (grr_sql_count($result) != 1) fatal_error(1, get_vocab('roomid') . $id_entry . get_vocab('not_found'));

  $area_id_row = grr_sql_row($result, 0);
  grr_sql_free($result);

  return $area_id_row[0];

}



/** mrbsOverloadGetFieldslist()
 *
 * Return an array with all fields name
 * $id_area - Id of the id_area
 *
 */
function mrbsOverloadGetFieldslist($id_area,$room_id=0)
{
  if ($room_id > 0 ) {
      // il faut rechercher le id_area en fonction du room_id
      $id_area = grr_sql_query1("select area_id from ".TABLE_PREFIX."_room where id='".$room_id."'");
      if ($id_area == -1) {
          fatal_error(1, get_vocab('error_room') . $room_id . get_vocab('not_found'));
          $id_area = "";
      }
  }
  // si l'id de l'area n'est pas pr�cis�, on cherche tous les champs additionnels
  if ($id_area == "")
      $sqlstring = "select fieldname ,fieldtype, ".TABLE_PREFIX."_overload.id, fieldlist, ".TABLE_PREFIX."_area.area_name, affichage, overload_mail, ".TABLE_PREFIX."_overload.obligatoire, ".TABLE_PREFIX."_overload.confidentiel from ".TABLE_PREFIX."_overload, ".TABLE_PREFIX."_area
      where(".TABLE_PREFIX."_overload.id_area = ".TABLE_PREFIX."_area.id) order by fieldname,fieldtype ";
  else
      $sqlstring = "select fieldname,fieldtype, id, fieldlist, affichage, overload_mail, obligatoire, confidentiel from ".TABLE_PREFIX."_overload where id_area='".$id_area."' order by fieldname,fieldtype";
  $result = grr_sql_query($sqlstring);
  $fieldslist = array();
  if (! $result) fatal_error(1, grr_sql_error());

  if (grr_sql_count($result) <0) fatal_error(1, get_vocab('error_area') . $id_area . get_vocab('not_found'));
  for ($i = 0; ($field_row = grr_sql_row($result, $i)); $i++)
    {
    if ($id_area == "") {
      $fieldslist[$field_row[0]." (".$field_row[4].")"]["type"] = $field_row[1];
      $fieldslist[$field_row[0]." (".$field_row[4].")"]["id"] = $field_row[2];
      if (trim($field_row[3]) != "") {
          $tab_list = explode("|", $field_row[3]);
          foreach ($tab_list as $value) {
              if (trim($value) != "")
                  $fieldslist[$field_row[0]." (".$field_row[4].")"]["list"][] = trim($value);
          }
      }
      $fieldslist[$field_row[0]." (".$field_row[4].")"]["affichage"] = $field_row[5];
      $fieldslist[$field_row[0]." (".$field_row[4].")"]["overload_mail"] = $field_row[6];
      $fieldslist[$field_row[0]." (".$field_row[4].")"]["obligatoire"] = $field_row[7];
      $fieldslist[$field_row[0]." (".$field_row[4].")"]["confidentiel"] = $field_row[8];
     } else {
      $fieldslist[$field_row[0]]["name"] = $field_row[0];
      $fieldslist[$field_row[0]]["type"] = $field_row[1];
      $fieldslist[$field_row[0]]["id"] = $field_row[2];
      $fieldslist[$field_row[0]]["affichage"] = $field_row[4];
      $fieldslist[$field_row[0]]["overload_mail"] = $field_row[5];
      $fieldslist[$field_row[0]]["obligatoire"] = $field_row[6];
      $fieldslist[$field_row[0]]["confidentiel"] = $field_row[7];
      if (trim($field_row[3]) != "") {
          $tab_list = explode("|", $field_row[3]);
          foreach ($tab_list as $value) {
              if (trim($value) != "")
                  $fieldslist[$field_row[0]]["list"][] = trim($value);
          }
      }
     }
    }
  return $fieldslist;
}

/** mrbsEntryGetOverloadDesc()
 *
 * Return an array with all additionnal fields
 * $id - Id of the entry
 *
 */
function mrbsEntryGetOverloadDesc($id_entry)
{
  $room_id = 0;
  $overload_array = array();
  $overload_desc = "";
  // On r�cup�re les donn�es overload desc dans ".TABLE_PREFIX."_entry.
  if ($id_entry != NULL) {
      $overload_array = array();
      $sqlstring = "select overload_desc,room_id from ".TABLE_PREFIX."_entry where id=".$id_entry.";";
      $result = grr_sql_query($sqlstring);

      if (! $result) fatal_error(1, grr_sql_error());
      if (grr_sql_count($result) != 1) fatal_error(1, get_vocab('entryid') . $id_entry . get_vocab('not_found'));

      $overload_desc_row = grr_sql_row($result, 0);
      grr_sql_free($result);

      $overload_desc = $overload_desc_row[0];
      $room_id = $overload_desc_row[1];
    }
  if ( $room_id >0 ) {
      $area_id = mrbsGetAreaIdFromRoomId($room_id);


      // Avec l'id_area on r�cup�re la liste des champs additionnels dans ".TABLE_PREFIX."_overload.
      $fieldslist = mrbsOverloadGetFieldslist($area_id);

      foreach ( $fieldslist as $field=>$fieldtype)  {
//      $begin_string = "<".$fieldslist[$field]["id"].">";   //tructruc
//      $end_string = "</".$fieldslist[$field]["id"].">";    //tructruc
        $begin_string = "@".$fieldslist[$field]["id"]."@";
        $end_string = "@/".$fieldslist[$field]["id"]."@";
        $l1 = strlen($begin_string);
        $l2 = strlen($end_string);

        $chaine = $overload_desc;
        $balise_fermante='n'; // initialisation
        $balise_ouvrante='n'; // initialisation
        $traitement1=true; // initialisation
        $traitement2=true; // initialisation
        while(($traitement1!==false) or ($traitement2!==false)) {
           // le premier traitement cherche la prochaine occurence de $begin_string et retourne la portion de chaine apr�s cette occurence
           if ($traitement1!=false) {
             $chaine1 = strstr ( $chaine  , $begin_string  ); // retourne la sous-cha�ne de $chaine, allant de la premi�re occurrence de $begin_string jusqu'� la fin de la cha�ne.
             if ($chaine1!==false) { // on a trouv� une occurence de $begin_string
               $balise_ouvrante='y'; // on sait qu'il y a au moins une balise ouvrante
               $chaine = substr($chaine1,$l1,strlen($chaine1)-$l1); // on retourne la chaine en ayant �limin� le d�but de chaine correspondant � $begin_string
               $result = $chaine; // On m�morise la valeur pr�c�dente
             } else {
               $traitement1=false; // on n'effectuera pas ce traitement au prochain passage dans la boucle
             }
           }
           // le 2�me traitement cherche la derni�re occurence de $end_string en partant de la fin et retourne la portion de chaine avant cette occurence
           if ($traitement2!=false) {
             // La boucle suivante a pour effet de d�terminer la derni�re occurence de $end_string
             $ind=0;
             $end_pos=true;
             while ($end_pos!==false) {
                $end_pos = strpos($chaine,$end_string,$ind); // On rep�re
                if ($end_pos !==false) {
                   $balise_fermante='y';
                   $ind_old = $end_pos;
                   $ind = $end_pos+$l2;
                } else
                   break; // on sort de la boucle
             }
             // a ce niveau, $ind_old est la derni�re occurence de $end_string trouv�e dans $chaine
             if ($ind != 0 ) {
                 $chaine = substr($chaine,0,$ind_old);
                 $result = $chaine; // On m�morise la valeur pr�c�dente
             } else
                 $traitement2=false;
           }
        } // while
        if (($balise_fermante=='n' ) or ($balise_ouvrante=='n'))
            $overload_array[$field]["valeur"]='';
        else
            $overload_array[$field]["valeur"]=urldecode($result);

        $overload_array[$field]["id"] = $fieldslist[$field]["id"];
        $overload_array[$field]["affichage"] = grr_sql_query1("select affichage from ".TABLE_PREFIX."_overload where id = '".$fieldslist[$field]["id"]."'");
        $overload_array[$field]["overload_mail"] = grr_sql_query1("select overload_mail from ".TABLE_PREFIX."_overload where id = '".$fieldslist[$field]["id"]."'");
        $overload_array[$field]["obligatoire"] = grr_sql_query1("select obligatoire from ".TABLE_PREFIX."_overload where id = '".$fieldslist[$field]["id"]."'");
        $overload_array[$field]["confidentiel"] = grr_sql_query1("select confidentiel from ".TABLE_PREFIX."_overload where id = '".$fieldslist[$field]["id"]."'");
      }
      return $overload_array;
  }
  return $overload_array;
}

/** grrExtractValueFromOverloadDesc()
 *
 * Extrait la chaine correspondante au champ id de la chaine $chaine
 *
 */
function grrExtractValueFromOverloadDesc($chaine,$id)
{
//    $begin_string = "<".$id.">"; //tructruc
//    $end_string = "</".$id.">";  //tructruc
    $begin_string = "@".$id."@";
    $end_string = "@/".$id."@";
    $data = "";
    $begin_pos = strpos($chaine,$begin_string);
    $end_pos = strpos($chaine,$end_string);
    if ( $begin_pos !== false && $end_pos !== false ) {
        $first = $begin_pos + strlen($begin_string);
        $data = substr($chaine,$first,$end_pos-$first);
//        $data = base64_decode($data); //tructruc
        $data = urldecode($data);
    } else $data = "";
    return $data;
}


/** mrbsCreateSingleEntry()
 *
 * Create a single (non-repeating) entry in the database
 *
 * $starttime   - Start time of entry
 * $endtime     - End time of entry
 * $entry_type  - Entry type
 * $repeat_id   - Repeat ID
 * $room_id     - Room ID
 * $beneficiaire       - beneficiaire
 * $beneficiaire_ext - b�n�ficiaire ext�rieur
 * $name        - Name
 * $type        - Type (Internal/External)
 * $description - Description
 *$rep_jour_c - Le jour cycle d'une r�servation, si aucun 0
 *
 * Returns:
 *   0        - An error occured while inserting the entry
 *   non-zero - The entry's ID
 */
function mrbsCreateSingleEntry($starttime, $endtime, $entry_type, $repeat_id, $room_id,
                               $creator, $beneficiaire, $beneficiaire_ext, $name, $type, $description, $option_reservation,$overload_data, $moderate, $rep_jour_c, $statut_entry)
{
  $overload_data_string = "";
  $overload_fields_list = mrbsOverloadGetFieldslist(0,$room_id);

  foreach ($overload_fields_list as $field=>$fieldtype)
    {
      $id_field = $overload_fields_list[$field]["id"];
      if (array_key_exists($id_field,$overload_data))
      {
//      $begin_string = "<".$id_field.">"; //tructruc
//      $end_string = "</".$id_field.">";  //tructruc
      $begin_string = "@".$id_field."@";
      $end_string = "@/".$id_field."@";

//    $overload_data_string .= $begin_string.base64_encode($overload_data[$id_field]).$end_string; // tructruc
    $overload_data_string .= $begin_string.urlencode($overload_data[$id_field]).$end_string; // tructruc
      }
    }

   $sql = "INSERT INTO ".TABLE_PREFIX."_entry (  start_time,   end_time,   entry_type,    repeat_id,   room_id,
                                      create_by, beneficiaire, beneficiaire_ext, name, type, description, statut_entry, option_reservation,overload_desc, moderate, jours)
                            VALUES ($starttime, $endtime, '".protect_data_sql($entry_type)."', $repeat_id, $room_id,
                                    '".protect_data_sql($creator)."', '".protect_data_sql($beneficiaire)."', '".protect_data_sql($beneficiaire_ext)."', '".protect_data_sql($name)."', '".protect_data_sql($type)."', '".protect_data_sql($description)."', '".protect_data_sql($statut_entry)."', '".$option_reservation."','".protect_data_sql($overload_data_string)."', ".$moderate.",".$rep_jour_c.")";

    if (grr_sql_command($sql) < 0) return 0;
    // s'il s'agit d'une modification d'une ressource d�j� mod�r�e et accept�e : on met � jour les infos dans la table ".TABLE_PREFIX."_entry_moderate
    $new_id = grr_sql_insert_id("".TABLE_PREFIX."_entry", "id");
    if ($moderate==2) moderate_entry_do($new_id,1,"","no");

}

/** mrbsCreateRepeatEntry()
 *
 * Creates a repeat entry in the data base
 *
 * $starttime   - Start time of entry
 * $endtime     - End time of entry
 * $rep_type    - The repeat type
 * $rep_enddate - When the repeating ends
 * $rep_opt     - Any options associated with the entry
 * $room_id     - Room ID
 * $beneficiaire       - beneficiaire
 * $beneficiaire_ext   - beneficiaire ext�rieur
 * $creator     - celui aui a cr�� ou modifi� la r�servation.
 * $name        - Name
 * $type        - Type (Internal/External)
 * $description - Description
  *$rep_jour_c - Le jour cycle d'une r�servation, si aucun 0
 *
 * Returns:
 *   0        - An error occured while inserting the entry
 *   non-zero - The entry's ID
 */
function mrbsCreateRepeatEntry($starttime, $endtime, $rep_type, $rep_enddate, $rep_opt,
                               $room_id, $creator, $beneficiaire, $beneficiaire_ext, $name, $type, $description, $rep_num_weeks,$overload_data, $rep_jour_c)
{
  $overload_data_string = "";
  $area_id = mrbsGetAreaIdFromRoomId($room_id);

  $overload_fields_list = mrbsOverloadGetFieldslist($area_id);

  foreach ($overload_fields_list as $field=>$fieldtype)
    {
      $id_field = $overload_fields_list[$field]["id"];
      if (array_key_exists($id_field,$overload_data))
      {
//      $begin_string = "<".$id_field.">"; //tructruc
//      $end_string = "</".$id_field.">";  //tructruc
      $begin_string = "@".$id_field."@";
      $end_string = "@/".$id_field."@";
//    $overload_data_string .= $begin_string.base64_encode($overload_data[$id_field]).$end_string; // tructruc
    $overload_data_string .= $begin_string.urlencode($overload_data[$id_field]).$end_string; // tructruc

      }
    }
  $sql = "INSERT INTO ".TABLE_PREFIX."_repeat (
  start_time, end_time, rep_type, end_date, rep_opt, room_id, create_by, beneficiaire, beneficiaire_ext, type, name, description, rep_num_weeks, overload_desc, jours)
  VALUES ($starttime, $endtime,  $rep_type, $rep_enddate, '$rep_opt', $room_id,   '".protect_data_sql($creator)."','".protect_data_sql($beneficiaire)."','".protect_data_sql($beneficiaire_ext)."', '".protect_data_sql($type)."', '".protect_data_sql($name)."', '".protect_data_sql($description)."', '$rep_num_weeks','".protect_data_sql($overload_data_string)."',".$rep_jour_c.")";


  if (grr_sql_command($sql) < 0)
    {
      return 0;

    }
  return grr_sql_insert_id("".TABLE_PREFIX."_repeat", "id");
}


/** same_day_next_month
 *  Return the number of days to step forward for a "monthly repeat,
 *  corresponding day" series - same week number and day of week next month.
 *  This function always returns either 28 or 35.
 *  For dates after the 28th day of a month, the results are undefined.
 */
function same_day_next_month($time)
{
    $days_in_month = date("t", $time);
    $day = date("d", $time);
    $weeknumber = (int)(($day - 1) / 7) + 1;
    if ($day + 7 * (5 - $weeknumber) <= $days_in_month) return 35;
    else return 28;
}

/** mrbsGetRepeatEntryList
 *
 * Returns a list of the repeating entrys
 *
 * $time     - The start time
 * $enddate  - When the repeat ends
 * $rep_type - What type of repeat is it
 * $rep_opt  - The repeat entrys
 * $max_ittr - After going through this many entrys assume an error has occured
 * *$rep_jour_c - Le jour cycle d'une r�servation, si aucun 0
 *
 * Returns:
 *   empty     - The entry does not repeat
 *   an array  - This is a list of start times of each of the repeat entrys
 */
function mrbsGetRepeatEntryList($time, $enddate, $rep_type, $rep_opt, $max_ittr, $rep_num_weeks, $rep_jour_c,$area)
{
    $sec   = date("s", $time);
    $min   = date("i", $time);
    $hour  = date("G", $time);
    $day   = date("d", $time);
    $month = date("m", $time);
    $year  = date("Y", $time);

    $entrys = "";
    $entrys_return = "";
    $k=0;
    for($i = 0; $i < $max_ittr; $i++)
    {
        $time = mktime($hour, $min, $sec, $month, $day, $year);
        if ($time > $enddate)
            break;
        $time2 = mktime(0, 0, 0, $month, $day, $year);

        if (!(est_hors_reservation($time2,$area))) {
            $entrys_return[$k] = $time;
            $k++;
        }
        $entrys[$i] = $time;
        switch($rep_type)
        {
            // Daily repeat
            case 1:
                $day += 1;
                break;

            // Weekly repeat
            case 2:
                $j = $cur_day = date("w", $entrys[$i]);
                // Skip over days of the week which are not enabled:
                while ((($j = ($j + 1) % (7*$rep_num_weeks)) != $cur_day && $j<7 &&!$rep_opt[$j]) or ($j>=7))
                {
                    $day += 1;
                }

                $day += 1;
                break;

            // Monthly repeat
            case 3:
                $month += 1;
                break;

            // Yearly repeat
            case 4:
                $year += 1;
                break;

            // Monthly repeat on same week number and day of week
            case 5:
                $day += same_day_next_month($time);
                break;

            // Si la p�riodicit� est par Jours/Cycle
            case 6:
                $sql = "SELECT * FROM ".TABLE_PREFIX."_calendrier_jours_cycle WHERE DAY >= '".$time2."' AND DAY <= '".$enddate."' AND Jours = '".$rep_jour_c."'";
                $result = mysql_query($sql);
                $kk = 0;
                $tableFinale = array();
                while($table = mysql_fetch_array($result)){
                    $day   = date("d", $table['DAY']);
                    $month = date("m", $table['DAY']);
                    $year  = date("Y", $table['DAY']);
                    $tableFinale[$kk] = mktime($hour, $min, $sec, $month, $day, $year);
                    $kk++;
                }
                return $tableFinale;
                break;

            // Unknown repeat option
            default:
                return;
        }
    }

    return $entrys_return;
}

/** mrbsCreateRepeatingEntrys()
 *
 * Creates a repeat entry in the data base + all the repeating entrys
 *
 * $starttime   - Start time of entry
 * $endtime     - End time of entry
 * $rep_type    - The repeat type
 * $rep_enddate - When the repeating ends
 * $rep_opt     - Any options associated with the entry
 * $room_id     - Room ID
 * $beneficiaire       - beneficiaire
 * $beneficiaire_ext - b�n�ficiaire ext�rieur
 * $name        - Name
 * $type        - Type (Internal/External)
 * $description - Description
  *$rep_jour_c - Le jour cycle d'une r�servation, si aucun 0
 *
 * Returns:
 *   0        - An error occured while inserting the entry
 *   non-zero - The entry's ID
 */
function mrbsCreateRepeatingEntrys($starttime, $endtime, $rep_type, $rep_enddate, $rep_opt,
                                   $room_id, $creator, $beneficiaire, $beneficiaire_ext, $name, $type, $description, $rep_num_weeks, $option_reservation,$overload_data, $moderate, $rep_jour_c)
{
    global $max_rep_entrys, $id_first_resa;
    $area = mrbsGetRoomArea($room_id);
    $reps = mrbsGetRepeatEntryList($starttime, $rep_enddate, $rep_type, $rep_opt, $max_rep_entrys, $rep_num_weeks, $rep_jour_c, $area);
    if(count($reps) > $max_rep_entrys)
        return 0;

    if(empty($reps))
    {
        mrbsCreateSingleEntry($starttime, $endtime, 0, 0, $room_id, $creator, $beneficiaire, $beneficiaire_ext, $name, $type, $description, $option_reservation,$overload_data,$moderate, $rep_jour_c,"-");
        $id_first_resa = grr_sql_insert_id("".TABLE_PREFIX."_entry", "id");
        return;
    }

    $ent = mrbsCreateRepeatEntry($starttime, $endtime, $rep_type, $rep_enddate, $rep_opt, $room_id, $creator, $beneficiaire, $beneficiaire_ext, $name, $type, $description, $rep_num_weeks,$overload_data, $rep_jour_c);
    if($ent)
    {
        $diff = $endtime - $starttime;

        for($i = 0; $i < count($reps); $i++) {
            mrbsCreateSingleEntry($reps[$i], $reps[$i] + $diff, 1, $ent,
                 $room_id, $creator, $beneficiaire, $beneficiaire_ext, $name, $type, $description, $option_reservation,$overload_data, $moderate, $rep_jour_c,"-");
            $id_new_resa = grr_sql_insert_id("".TABLE_PREFIX."_entry", "id");
            // s'il s'agit d'une modification d'une ressource d�j� mod�r�e et accept�e : on met � jour les infos dans la table ".TABLE_PREFIX."_entry_moderate
            if ($moderate==2) moderate_entry_do($id_new_resa,1,"","no");
            // On r�cup�re l'id de la premi�re r�servation de la s�rie et qui sera utilis� pour l'enoi d'un mail
            if ($i == 0) $id_first_resa = $id_new_resa;
            }
    }

    return $ent;
}

/* mrbsGetEntryInfo()
 *
 * Get the booking's entrys
 *
 * @param integer $id : The ID for which to get the info for.
 * @return variant    : nothing = The ID does not exist
 *    array   = The bookings info
 */
function mrbsGetEntryInfo($id)
{
    $sql = "SELECT start_time, end_time, entry_type, repeat_id, room_id,
                   timestamp, beneficiaire, name, type, description
           FROM ".TABLE_PREFIX."_entry
           WHERE id = '".$id."'";
    $res = grr_sql_query($sql);
   if (! $res)
     return;

   $ret = '';
    if(grr_sql_count($res) > 0)
    {
        $row = grr_sql_row($res, 0);

        $ret["start_time"]  = $row[0];
        $ret["end_time"]    = $row[1];
        $ret["entry_type"]  = $row[2];
        $ret["repeat_id"]   = $row[3];
        $ret["room_id"]     = $row[4];
        $ret["timestamp"]   = $row[5];
        $ret["beneficiaire"]   = $row[6];
        $ret["name"]        = $row[7];
        $ret["type"]        = $row[8];
        $ret["description"] = $row[9];

    }
    grr_sql_free($res);

    return $ret;
}

function mrbsGetRoomArea($id)
{
    $id = grr_sql_query1("SELECT area_id FROM ".TABLE_PREFIX."_room WHERE (id = '".$id."')");
    if ($id <= 0) return 0;
    return $id;
}
function mrbsGetAreaSite($id)
{
    if (getSettingValue("module_multisite") == "Oui") {
      $id = grr_sql_query1("SELECT id_site FROM ".TABLE_PREFIX."_j_site_area WHERE (id_area = '".$id."')");
      return $id;
    } else {
      return -1;
    }
}


 function moderate_entry_do($_id,$_moderate,$_description,$send_mail="yes")
 {
global $dformat;

// On v�rifie que l'utilisateur a bien le droit d'�tre ici
$room_id = grr_sql_query1("select room_id from ".TABLE_PREFIX."_entry where id='".$_id."'");
if (authGetUserLevel(getUserName(),$room_id) < 3)
{
    fatal_error(0,"Op�ration interdite");
    exit();
}


// j'ai besoin de $repeat_id '
$sql = "select repeat_id from ".TABLE_PREFIX."_entry where id =".$_id;
$res = grr_sql_query($sql);
if (! $res) fatal_error(0, grr_sql_error());
$row = grr_sql_row($res, 0);
$repeat_id = $row['0'];

// Initialisation
$series = 0;
if ($_moderate == "S1") {
     $_moderate = "1";
     $series = 1;
}
if ($_moderate == "S0") {
     $_moderate = "0";
     $series = 1;
}

if ($series==0) {
    //moderation de la ressource
    if ($_moderate == 1) {
        $sql = "update ".TABLE_PREFIX."_entry set moderate = 2 where id = ".$_id;
    } else {
        $sql = "update ".TABLE_PREFIX."_entry set moderate = 3 where id = ".$_id;
    }
    $res = grr_sql_query($sql);
    if (! $res) fatal_error(0, grr_sql_error());

    if (!(grr_backup($_id,$_SESSION['login'],$_description))) fatal_error(0, grr_sql_error());
    $tab_id_moderes = array();
} else { // cas d'une s�rie
    // on constitue le tableau des id de la p�riodicit�
    $sql = "select id from ".TABLE_PREFIX."_entry where repeat_id=".$repeat_id;
    $res = grr_sql_query($sql);
    if (! $res) fatal_error(0, grr_sql_error());
    $tab_entry = array();
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
        $tab_entry[] = $row['0'];
    }
    $tab_id_moderes = array();
    // Boucle sur les r�sas
    foreach ($tab_entry as $entry_tom) {
        $test = grr_sql_query1("select count(id) from ".TABLE_PREFIX."_entry_moderate where id = '".$entry_tom."'");
        // Si il existe d�j� une entr�e dans ".TABLE_PREFIX."_entry_moderate, cela signifie que la r�servation a d�j� �t� mod�r�e.
        // Sinon :
        if ($test == 0) {
            //moderation de la ressource
            if ($_moderate == 1) {
                $sql = "update ".TABLE_PREFIX."_entry set moderate = 2 where id = '".$entry_tom."'";
            } else {
                $sql = "update ".TABLE_PREFIX."_entry set moderate = 3 where id = '".$entry_tom."'";
           }
           $res = grr_sql_query($sql);
           if (! $res) fatal_error(0, grr_sql_error());

           if (!(grr_backup($entry_tom,$_SESSION['login'],$_description))) fatal_error(0, grr_sql_error());           // Backup : on enregistre les infos dans ".TABLE_PREFIX."_entry_moderate
           // On constitue un tableau des r�servations mod�r�es
           $tab_id_moderes[] = $entry_tom;
        }
    }
}

// Avant d'effacer la r�servation, on proc�de � la notification par mail, uniquement si la salle n'a pas d�j� �t� mod�r�e.
if ($send_mail=="yes")
   send_mail($_id,6,$dformat,$tab_id_moderes);

//moderation de la ressource
if ($_moderate != 1) {
    // on efface l'entr�e de la base
    if ($series==0) {
        $sql = "delete from ".TABLE_PREFIX."_entry where id = ".$_id;
        $res = grr_sql_query($sql);
        if (! $res) fatal_error(0, grr_sql_error());
    } else {
        // On s�lectionne toutes les r�servation de la p�riodicit�
        $res = grr_sql_query("select id from ".TABLE_PREFIX."_entry where repeat_id='".$repeat_id."'");
        if (! $res) fatal_error(0, grr_sql_error());
        for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
            $entry_tom = $row['0'];
            // Pour chaque r�servation, on teste si celle-ci a �t� refus�e
            $test = grr_sql_query1("select count(id) from ".TABLE_PREFIX."_entry_moderate where id = '".$entry_tom."' and moderate='3'");
            // Si oui, on supprime la r�servation
            if ($test > 0)
                $del = grr_sql_query("delete from ".TABLE_PREFIX."_entry where id = '".$entry_tom."'");
        }
        // On supprime l'info de p�riodicit�
        $del_repeat = grr_sql_query("delete from ".TABLE_PREFIX."_repeat where id='".$repeat_id."'");
        $dupdate_repeat = grr_sql_query("update ".TABLE_PREFIX."_entry set repead_id = '0' where repead_id='".$repeat_id."'");
    }
}
}

?>