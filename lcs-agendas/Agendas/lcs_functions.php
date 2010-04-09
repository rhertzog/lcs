<?php
function lcs_import_data ( $data, $overwrite, $type ) {
  global $login, $count_con, $count_suc, $error_num, $ImportType;
  global $single_user, $single_user_login, $numDeleted, $errormsg;
  global $ALLOW_CONFLICTS, $ALLOW_CONFLICT_OVERRIDE, $H2COLOR;
  global $calUser, $sqlLog,$message;

  $oldUIDs = array ();
  $oldIds = array ();
  $firstEventId = $count_suc = 0;
  $ImportType = 'ICAL';
  // $importId = -1;
  $importId = 1;
  $subType = '';
  if ( $type == 'icalclient' ) {
    $ImportType = 'ICAL';
    $type = 'ical';
    $subType = 'icalclient';
  } else if ( $type == 'remoteics' || $type == 'hcal' ) {
    $ImportType = 'RMTICS';
    $type = 'rmtics';
    $subType = 'remoteics';
  }
if ($overwrite) {
//on efface tous les evenement edt du user
//recherche du cat_id
$res = dbi_execute ( 'SELECT cat_id FROM webcal_categories WHERE cat_owner = ? AND cat_name = ?', array ($login,'EDT') );
  if ( $res ) {
    if ( $row = dbi_fetch_row ( $res ) ) {
      $id_cat = $row[0];
    }
    dbi_free_result ( $res );
}
//recherche des cal_id
$res = dbi_execute ( 'SELECT cal_id FROM webcal_entry_categories WHERE cat_id = '.$id_cat );
  if ( $res ) {
  $i=0;
    while ( $row = dbi_fetch_row ( $res ) ) {
            $id_cals[$i] = $row[0];
            $i++;
    }
    dbi_free_result ( $res );
    }
  $message.= 'Ev&#233;nements supprim&#233;s : '. count($id_cals). '<br />';
if (isset ($id_cals)) {
	foreach ( $id_cals as $id_cals ) {  
// Delete event  for this user
      dbi_execute ( 'DELETE FROM webcal_entry WHERE cal_id = ?',
        array ( $id_cals ) );
  	dbi_execute ( 'DELETE FROM webcal_entry_user WHERE cal_id = ?',
        array ( $id_cals ) );
   dbi_execute ( 'DELETE FROM webcal_import_data WHERE cal_id = ?',
        array ( $id_cals ) );
   dbi_execute ( 'DELETE FROM webcal_entry_log WHERE cal_entry_id = ?',
        array ( $id_cals ) );
        }
       }
   dbi_execute ( 'DELETE FROM webcal_entry_categories WHERE cat_id = '.$id_cat);          
 }       
  // Generate a unique import id
  $res = dbi_execute ( 'SELECT MAX(cal_import_id) FROM webcal_import' );
  if ( $res ) {
    if ( $row = dbi_fetch_row ( $res ) ) {
      $importId = $row[0] + 1;
    }
    dbi_free_result ( $res );
  }
  $sql = 'INSERT INTO webcal_import ( cal_import_id, cal_name,
    cal_date, cal_type, cal_login ) VALUES ( ?, NULL, ?, ?, ? )';
  if ( ! dbi_execute ( $sql, array ( $importId, date ( 'Ymd' ),
    $type, $login ) ) ) {
	$errormsg = db_error ();
	$my_errormsg.=db_error ();
    return;
  }
  if ( ! is_array ( $data ) )
    return false;
  foreach ( $data as $Entry ) {
    // do_debug ( "Entry Array " . print_r ( $Entry, true ) );
    $participants[0] = $calUser;
    // $participants[0] = $login;
    $Entry['start_date'] = gmdate ( 'Ymd', $Entry['StartTime'] );
    $Entry['start_time'] = gmdate ( 'His', $Entry['StartTime'] );
    $Entry['end_date'] = gmdate ( 'Ymd', $Entry['EndTime'] );
    $Entry['end_time'] = gmdate ( 'His', $Entry['EndTime'] );
    // not in icalclient
    if ( $overwrite && ! empty ( $Entry['UID'] ) ) {
      if ( empty ( $oldUIDs[$Entry['UID']] ) ) {
        $oldUIDs[$Entry['UID']] = 1;
      } else {
        $oldUIDs[$Entry['UID']]++;
      }
    }
    // Check for untimed
    if ( ! empty ( $Entry['Untimed'] ) && $Entry['Untimed'] == 1 ) {
      $Entry['start_time'] = 0;
    }
    // Check for all day
    if ( ! empty ( $Entry['AllDay'] ) && $Entry['AllDay'] == 1 ) {
      $Entry['start_time'] = 0;
      $Entry['end_time'] = 0;
      $Entry['Duration'] = '1440';
    }

    $priority = ( ! empty (  $Entry['Priority'] ) ?
      $Entry['Priority'] : 5 );

    if ( ! empty ( $Entry['Completed'] ) ) {
      $cal_completed = substr ( $Entry['Completed'], 0, 8 );
    } else {
      $cal_completed = '';
    }
    if ( strlen ( $cal_completed < 8 ) ) $cal_completed = '';

    $months = ( ! empty ( $Entry['Repeat']['ByMonth'] ) ) ?
    $Entry['Repeat']['ByMonth'] : '';

    $updateMode = false;
    // See if event already is there from prior import.
    // The same UID is used for all events imported at once with iCal.
    // So, we still don't have enough info to find the exact
    // event we want to replace. We could just delete all
    // existing events that correspond to the UID.
    // NOTE:(cek) commented out 'publish'. Will not work if event
    // was originally created from importing.
    if ( ! empty ( $Entry['UID'] ) ) {
      $res = dbi_execute ( 'SELECT wid.cal_id '
         . 'FROM webcal_import_data wid, webcal_entry_user weu WHERE '
        // "cal_import_type = 'publish' AND " .
        . 'wid.cal_id = weu.cal_id AND '
         . 'weu.cal_login = ? AND '
         . 'cal_external_id = ?', array ( $login, $Entry['UID'] ) );
      if ( $res ) {
        if ( $row = dbi_fetch_row ( $res ) ) {
          if ( ! empty ( $row[0] ) ) {
            $id = $row[0];
            $updateMode = true;
            // update rather than add a new event
          }
        }
      }
    }

    if ( ! $updateMode && $subType != 'icalclient' && $subType != 'remoteics' ) {
      // first check for any schedule conflicts
      if ( ( $ALLOW_CONFLICT_OVERRIDE == 'N' && $ALLOW_CONFLICTS == 'N' ) &&
          ( $Entry['Duration'] != 0 ) ) {
        $ex_days = array ();
        if ( ! empty ( $Entry['Repeat']['Exceptions'] ) ) {
          foreach ( $Entry['Repeat']['Exceptions'] as $ex_date ) {
            $ex_days[] = gmdate ( 'Ymd', $ex_date );
          }
        }
        $inc_days = array ();
        if ( ! empty ( $Entry['Repeat']['Inclusions'] ) ) {
          foreach ( $Entry['Repeat']['Inclusions'] as $inc_date ) {
            $inc_days[] = gmdate ( 'Ymd', $inc_date );
          }
        }
        // test if all Repeat Elements exist
        $rep_interval = ( ! empty ( $Entry['Repeat']['Interval'] ) ?
          $Entry['Repeat']['Interval'] : '' );
        $rep_bymonth = ( ! empty ( $Entry['Repeat']['ByMonth'] ) ?
          $Entry['Repeat']['ByMonth'] : '' );
        $rep_byweekno = ( ! empty ( $Entry['Repeat']['ByWeekNo'] ) ?
          $Entry['Repeat']['ByWeekNo'] : '' );
        $rep_byyearday = ( ! empty ( $Entry['Repeat']['ByYearDay'] ) ?
          $Entry['Repeat']['ByYearDay'] : '' );
        $rep_byweekno = ( ! empty ( $Entry['Repeat']['ByWeekNo'] ) ?
          $Entry['Repeat']['ByWeekNo'] : '' );
        $rep_byweekno = ( ! empty ( $Entry['Repeat']['ByWeekNo'] ) ?
          $Entry['Repeat']['ByWeekNo'] : '' );
        $rep_byweekno = ( ! empty ( $Entry['Repeat']['ByWeekNo'] ) ?
          $Entry['Repeat']['ByWeekNo'] : '' );
        $rep_bymonthday = ( ! empty ( $Entry['Repeat']['ByMonthDay'] ) ?
          $Entry['Repeat']['ByMonthDay'] : '' );
        $rep_byday = ( ! empty ( $Entry['Repeat']['ByDay'] ) ?
          $Entry['Repeat']['ByDay'] : '' );
        $rep_bysetpos = ( ! empty ( $Entry['Repeat']['BySetPos'] ) ?
          $Entry['Repeat']['BySetPos'] : '' );
        $rep_count = ( ! empty ( $Entry['Repeat']['Count'] ) ?
          $Entry['Repeat']['Count'] : '' );
        $rep_until = ( ! empty ( $Entry['Repeat']['Until'] ) ?
          $Entry['Repeat']['Until'] : '' );
        $rep_wkst = ( ! empty ( $Entry['Repeat']['Wkst'] ) ?
          $Entry['Repeat']['Wkst'] : '' );

        $dates = get_all_dates( $Entry['StartTime'],
          RepeatType( $Entry['Repeat']['Frequency'] ), $rep_interval,
          array ( $rep_bymonth, $rep_byweekno, $rep_byyearday, $rep_bymonthday,
          $rep_byday, $rep_bysetpos ), $rep_count, $rep_until, $rep_wkst,
          $ex_days, $inc_days );

        $overlap = check_for_conflicts ( $dates, $Entry['Duration'],
          $Entry['StartTime'], $participants, $login, 0 );
      }
    } //end  $subType != 'icalclient' && != 'remoteics'
    if ( empty ( $error ) ) {
      if ( ! $updateMode ) {
        // Add the Event
        $res = dbi_execute ( 'SELECT MAX(cal_id) FROM webcal_entry' );
        if ( $res ) {
          $row = dbi_fetch_row ( $res );
          $id = $row[0] + 1;
          dbi_free_result ( $res );
        } else {
          $id = 1;
        }
      }
      // not in icalclient
      if ( $firstEventId == 0 )
        $firstEventId = $id;

      $names = array ();
      $values = array ();
      $names[] = 'cal_id';
      $values[] = $id;
      if ( ! $updateMode ) {
        $names[] = 'cal_create_by';
        $values[] = ( $ImportType == 'RMTICS' ? $calUser : $login );
      }
      $names[] = 'cal_date';
      $values[] = $Entry['start_date'];
      $names[] = 'cal_time';
      $values[] = ( ! empty ( $Entry['Untimed'] ) && $Entry['Untimed'] == 1 )
      ? '-1' : $Entry['start_time'];
      $names[] = 'cal_mod_date';
      $values[] = gmdate ( 'Ymd' );
      $names[] = 'cal_mod_time';
      $values[] = gmdate ( 'Gis' );
      $names[] = 'cal_duration';
      $values[] = sprintf ( "%d", $Entry['Duration'] );
      $names[] = 'cal_priority';
      $values[] = $priority;

      if ( ! empty ( $Entry['Class'] ) ) {
        $names[] = 'cal_access';
        $entryclass = $Entry['Class'];
        $values[] = $entryclass;
      }

      if ( ! empty ( $Entry['Location'] ) ) {
        $names[] = 'cal_location';
        $entryclass = $Entry['Location'];
        $values[] = $entryclass;
      }

      if ( ! empty ( $Entry['URL'] ) ) {
        $names[] = 'cal_url';
        $entryclass = $Entry['URL'];
        $values[] = $entryclass;
      }

      if ( ! empty ( $cal_completed ) ) {
        $names[] = 'cal_completed';
        $values[] = $cal_completed;
      }
      if ( ! empty ( $Entry['Due'] ) ) {
        $names[] = 'cal_due_date';
        $values[] = sprintf ( "%d", substr ( $Entry['Due'], 0, 8 ) );
        $names[] = 'cal_due_time';
        $values[] = sprintf ( "%d", substr ( $Entry['Due'], 9, 6 ) );
      }
      if ( ! empty ( $Entry['CalendarType'] ) ) {
        $names[] = 'cal_type';
        if ( $Entry['CalendarType'] == 'VEVENT' || $Entry['CalendarType'] == 'VFREEBUSY' ) {
          $values[] = ( ! empty ( $Entry['Repeat'] ) )? 'M': 'E';
        } else if ( $Entry['CalendarType'] == 'VTODO' ) {
          $values[] = ( ! empty ( $Entry['Repeat'] ) )? 'N': 'T';
        }
      }
      if ( strlen ( $Entry['Summary'] ) == 0 )
        $Entry['Summary'] = translate ( 'Unnamed Event' );
      if ( empty ( $Entry['Description'] ) )
        $Entry['Description'] = $Entry['Summary'];
      $Entry['Summary'] = str_replace ( "\\n", "\n", $Entry['Summary'] );
      $Entry['Summary'] = str_replace ( "\\'", "'", $Entry['Summary'] );
      $Entry['Summary'] = str_replace ( "\\\"", "\"", $Entry['Summary'] );
      $Entry['Summary'] = str_replace ( "'", "\\'", $Entry['Summary'] );
      $names[] = 'cal_name';
      $values[] = $Entry['Summary'];
      $Entry['Description'] = str_replace ( "\\n", "\n", $Entry['Description'] );
      $Entry['Description'] = str_replace ( "\\'", "'", $Entry['Description'] );
      $Entry['Description'] = str_replace ( "\\\"", "\"", $Entry['Description'] );
      $Entry['Description'] = str_replace ( "'", "\\'", $Entry['Description'] );
      // added these to try and compensate for Sunbird escaping html
      $Entry['Description'] = str_replace ( "\;", ";", $Entry['Description'] );
      $Entry['Description'] = str_replace ( "\,", ",", $Entry['Description'] );
      // Mozilla will send this goofy string, so replace it with real html
      $Entry['Description'] = str_replace ( '=0D=0A=', '<br />',
        $Entry['Description'] );
      $Entry['Description'] = str_replace ( '=0D=0A', '',
        $Entry['Description'] );
      // Allow option to not limit description size
      // This will only be practical for mysql and MSSQL/Postgres as
      // these do not have limits on the table definition
      // TODO Add this option to preferences
      if ( empty ( $LIMIT_DESCRIPTION_SIZE ) || $LIMIT_DESCRIPTION_SIZE == 'Y' ) {
        // limit length to 1024 chars since we setup tables that way
        if ( strlen ( $Entry['Description'] ) >= 1024 ) {
          $Entry['Description'] = substr ( $Entry['Description'], 0, 1019 )
           . '...';
        }
      }
      $names[] = 'cal_description';
      $values[] = $Entry['Description'];
      // do_debug ( "descr='" . $Entry['Description'] . "'" );
      $sql_params = array ();
      $namecnt = count ( $names );
      if ( $updateMode ) {
        $sql = 'UPDATE webcal_entry SET ';
        for ( $f = 0; $f < $namecnt; $f++ ) {
          if ( $f > 0 )
            $sql .= ', ';
          $sql .= $names[$f] . ' = ?';
          $sql_params[] = $values[$f];
        }
        $sql .= ' WHERE cal_id = ?';
        $sql_params[] = $id;
      } else {
        $string_names = '';
        $string_values = '';
        for ( $f = 0; $f < $namecnt; $f++ ) {
          if ( $f > 0 ) {
            $string_names .= ', ';
            $string_values .= ', ';
          }
          $string_names .= $names[$f];
          $string_values .= '?';
          $sql_params[] = $values[$f];
        }
        $sql = 'INSERT INTO webcal_entry ( ' . $string_names . ' ) VALUES ( '
         . $string_values . ' )';
      }
       //do_debug ( date("H:i:s")." entry SQL> $sql" );
      if ( empty ( $error ) ) {
        if ( ! dbi_execute ( $sql, $sql_params ) ) {
          $error .= db_error ();
          // do_debug ( $error );
          break;
        } else if ( $ImportType == 'RMTICS' ) {
          $count_suc++;
        }
      }
      // log add/update
      if ( $Entry['CalendarType'] == 'VTODO' ) {
        activity_log ( $id, $login, $calUser,
          $updateMode ? LOG_UPDATE_T : LOG_CREATE_T, 'Import from '
           . $ImportType );
      } else {
        activity_log ( $id, $login, $calUser,
          $updateMode ? LOG_UPDATE : LOG_CREATE, 'Import from ' . $ImportType );
      }
      // not in icalclient
      if ( $single_user == 'Y' ) {
        $participants[0] = $single_user_login;
      }
      // Now add to webcal_import_data
      if ( ! $updateMode ) {
        // only in icalclient
        // add entry to webcal_import and webcal_import_data
        $uid = generate_uid ( $id );
        $uid = empty ( $Entry['UID'] ) ? $uid : $Entry['UID'];
        if ( $importId < 0 ) {
          $importId = create_import_instance ();
        }

        if ( $ImportType == 'PALMDESKTOP' ) {
          $sql = 'INSERT INTO webcal_import_data ( cal_import_id, cal_id,
            cal_login, cal_import_type, cal_external_id )
            VALUES ( ?, ?, ?, ?, ? )';
          $sqlLog .= $sql . "<br />\n";
          if ( ! dbi_execute ( $sql, array ( $importId, $id,
                $calUser, 'palm', $Entry['RecordID'] ) ) ) {
            $error = db_error ();
            break;
          }
        } else if ( $ImportType == 'VCAL' ) {
          $uid = empty ( $Entry['UID'] ) ? null : $Entry['UID'];
          if ( strlen ( $uid ) > 200 )
            $uid = null;
          $sql = 'INSERT INTO webcal_import_data ( cal_import_id, cal_id,
            cal_login, cal_import_type, cal_external_id )
            VALUES ( ?, ?, ?, ?, ? )';
          $sqlLog .= $sql . "<br />\n";
          if ( ! dbi_execute ( $sql, array ( $importId, $id, $calUser, 'vcal', $uid ) ) ) {
            $error = db_error ();
            break;
          }
        } else if ( $ImportType == 'ICAL' ) {
          $uid = empty ( $Entry['UID'] ) ? null : $Entry['UID'];
          // This may cause problems
          if ( strlen ( $uid ) > 200 )
            $uid = substr ( $uid, 0, 200 );
          $sql = 'INSERT INTO webcal_import_data ( cal_import_id, cal_id,
            cal_login, cal_import_type, cal_external_id )
            VALUES ( ?, ?, ?, ?, ? )';
          $sqlLog .= $sql . "<br />\n";
          if ( ! dbi_execute ( $sql, array ( $importId, $id, $calUser, 'ical', $uid ) ) ) {
            $error = db_error ();
            break;
          }
        }
      }
      // Now add participants
      $status = ( ! empty ( $Entry['Status'] ) ? $Entry['Status'] : 'A' );
      $percent = ( ! empty ( $Entry['Percent'] ) ? $Entry['Percent'] : '0' );
      if ( ! $updateMode ) {
        $sql = 'INSERT INTO webcal_entry_user
          ( cal_id, cal_login, cal_status, cal_percent )
          VALUES ( ?, ?, ?, ? )';
          //( date("H:i:s")."add part SQL> $sql" );
        if ( ! dbi_execute ( $sql, array ( $id, $participants[0], $status,
          $percent ) ) ) {
          $error = db_error ();
          // do_debug ( "Error: " . $error );
          break;
        }
      } else {
        // ( date("H:i:s")." up part SQL> $sql" );
        $sql = 'UPDATE webcal_entry_user SET cal_status = ?
          WHERE cal_id = ?';
        if ( ! dbi_execute ( $sql, array ( $status, $id ) ) ) {
          $error = db_error ();
          // do_debug ( "Error: " . $error );
          break;
        }
        // update percentage only if set
        if ( $percent != '' ) {
          $sql = 'UPDATE webcal_entry_user SET cal_percent = ?
            WHERE cal_id = ?';
          if ( ! dbi_execute ( $sql, array ( $percent, $id ) ) ) {
            $error = db_error ();
            // do_debug ( "Error: " . $error );
            break;
          }
        }
        dbi_execute ( 'DELETE FROM webcal_entry_categories WHERE cal_id = ?',
          array ( $id ) );
      }
      // update Categories
      if ( ! empty ( $Entry['Categories'] ) ) {
        $cat_ids = $Entry['Categories'];
        $cat_order = 1;
        foreach ( $cat_ids as $cat_id ) {
          $sql = 'INSERT INTO webcal_entry_categories
            ( cal_id, cat_id, cat_order, cat_owner ) VALUES ( ?, ?, ?, ? )';

          if ( ! dbi_execute ( $sql, array ( $id, $cat_id, $cat_order++, $login ) ) ) {
            $error = db_error ();
            // do_debug ( "Error: " . $error );
            break;
          }
        }
      }
      // Add repeating info
      if ( $updateMode ) {
        // remove old repeating info
        dbi_execute ( 'DELETE FROM webcal_entry_repeats WHERE cal_id = ?',
          array ( $id ) );
        dbi_execute ( 'DELETE FROM webcal_entry_repeats_not WHERE cal_id = ?',
          array ( $id ) );
      }
      $names = array ();
      $values = array ();
      if ( ! empty ( $Entry['Repeat']['Frequency'] ) ) {
        $names[] = 'cal_id';
        $values[] = $id;

        $names[] = 'cal_type';
        $values[] = RepeatType( $Entry['Repeat']['Frequency'] );

        $names[] = 'cal_frequency';
        $values[] = ( ! empty ( $Entry['Repeat']['Interval'] ) ?
          $Entry['Repeat']['Interval'] : 1 );

        if ( ! empty ( $Entry['Repeat']['ByMonth'] ) ) {
          $names[] = 'cal_bymonth';
          $values[] = $Entry['Repeat']['ByMonth'];
        }

        if ( ! empty ( $Entry['Repeat']['ByMonthDay'] ) ) {
          $names[] = 'cal_bymonthday';
          $values[] = $Entry['Repeat']['ByMonthDay'];
        }
        if ( ! empty ( $Entry['Repeat']['ByDay'] ) ) {
          $names[] = 'cal_byday';
          $values[] = $Entry['Repeat']['ByDay'];
        }
        if ( ! empty ( $Entry['Repeat']['BySetPos'] ) ) {
          $names[] = 'cal_bysetpos';
          $values[] = $Entry['Repeat']['BySetPos'];
        }
        if ( ! empty ( $Entry['Repeat']['ByWeekNo'] ) ) {
          $names[] = 'cal_byweekno';
          $values[] = $Entry['Repeat']['ByWeekNo'];
        }
        if ( ! empty ( $Entry['Repeat']['ByYearDay'] ) ) {
          $names[] = 'cal_byyearday';
          $values[] = $Entry['Repeat']['ByYearDay'];
        }
        if ( ! empty ( $Entry['Repeat']['Wkst'] ) ) {
          $names[] = 'cal_wkst';
          $values[] = $Entry['Repeat']['Wkst'];
        }

        if ( ! empty ( $Entry['Repeat']['Count'] ) ) {
          $names[] = 'cal_count';
          $values[] = $Entry['Repeat']['Count'];
        }

        if ( ! empty ( $Entry['Repeat']['Until'] ) ) {
          $REND = localtime ( $Entry['Repeat']['Until'] );
          if ( ! empty ( $Entry['Repeat']['Count'] ) ) {
            // Get end time from DTSTART
            $RENDTIME = $Entry['start_time'];
          } else {
            $RENDTIME = gmdate ( 'His', $Entry['Repeat']['Until'] );
          }
          $names[] = 'cal_end';
          $values[] = gmdate ( 'Ymd', $Entry['Repeat']['Until'] );
          // if ( $RENDTIME != '000000' ) {
          $names[] = 'cal_endtime';
          $values[] = $RENDTIME;
          // }
        }

        $string_names = '';
        $string_values = '';
        $sql_params = array ();
        $namecnt = count ( $names );
        for ( $f = 0; $f < $namecnt; $f++ ) {
          if ( $f > 0 ) {
            $string_names .= ', ';
            $string_values .= ', ';
          }
          $string_names .= $names[$f];
          $string_values .= '?';
          $sql_params[] = $values[$f];
        }
        $sql = 'INSERT INTO webcal_entry_repeats ( ' . $string_names
         . ' ) VALUES ( ' . $string_values . ' )';

        if ( ! dbi_execute ( $sql, $sql_params ) ) {
          $error = 'Unable to add to webcal_entry_repeats: '
           . dbi_error () . "<br /><br />\n<b>SQL:</b> $sql";
          break;
        }
        // Repeating Exceptions...
        if ( ! empty ( $Entry['Repeat']['Exceptions'] ) ) {
          foreach ( $Entry['Repeat']['Exceptions'] as $ex_date ) {
            $ex_date = gmdate ( 'Ymd', $ex_date );
            $sql = 'INSERT INTO webcal_entry_repeats_not
              ( cal_id, cal_date, cal_exdate ) VALUES ( ?,?,? )';

            if ( ! dbi_execute ( $sql, array ( $id, $ex_date, 1 ) ) ) {
              $error = 'Unable to add to webcal_entry_repeats_not: ' .
              dbi_error () . "<br /><br />\n<b>SQL:</b> $sql";
              break;
            }
          }
        }
        // Repeating Inclusions...
        if ( ! empty ( $Entry['Repeat']['Inclusions'] ) ) {
          foreach ( $Entry['Repeat']['Inclusions'] as $inc_date ) {
            $inc_date = gmdate ( 'Ymd', $inc_date );
            $sql = 'INSERT INTO webcal_entry_repeats_not
              ( cal_id, cal_date, cal_exdate ) VALUES ( ?,?,? )';

            if ( ! dbi_execute ( $sql, array ( $id, $inc_date, 0 ) ) ) {
              $error = 'Unable to add to webcal_entry_repeats_not: ' .
              dbi_error () . "<br /><br />\n<b>SQL:</b> $sql";
              break;
            }
          }
        }
      } // End Repeat
      // Add Alarm info
      if ( $updateMode )
        dbi_execute ( 'DELETE FROM webcal_reminders WHERE  cal_id = ?',
         array ( $id ) );

      if ( ! empty ( $Entry['AlarmSet'] ) && $Entry['AlarmSet'] == 1 ) {
        $names = array ();
        $values = array ();

        $names[] = 'cal_id';
        $values[] = $id;
        if ( ! empty ( $Entry['ADate'] ) ) {
          $names[] = 'cal_date';
          $values[] = $Entry['ADate'];
        }
        if ( ! empty ( $Entry['AOffset'] ) ) {
          $names[] = 'cal_offset';
          $values[] = $Entry['AOffset'];
        }
        if ( ! empty ( $Entry['ADuration'] ) ) {
          $names[] = 'cal_duration';
          $values[] = $Entry['ADuration'];
        }
        if ( ! empty ( $Entry['ARepeat'] ) ) {
          $names[] = 'cal_repeats';
          $values[] = $Entry['ARepeat'];
        }
        if ( ! empty ( $Entry['ABefore'] ) ) {
          $names[] = 'cal_before';
          $values[] = $Entry['ABefore'];
        }
        if ( ! empty ( $Entry['ARelated'] ) ) {
          $names[] = 'cal_related';
          $values[] = $Entry['ARelated'];
        }
        if ( ! empty ( $Entry['AAction'] ) ) {
          $names[] = 'cal_action';
          $values[] = $Entry['AAction'];
        }
        $string_names = '';
        $string_values = '';
        $sql_params = array ();
        $namecnt = count ( $names );
        for ( $f = 0; $f < $namecnt; $f++ ) {
          if ( $f > 0 ) {
            $string_names .= ', ';
            $string_values .= ', ';
          }
          $string_names .= $names[$f];
          $string_values .= '?';
          $sql_params[] = $values[$f];
        }
        $sql = 'INSERT INTO webcal_reminders (' . $string_names . ' ) '
         . ' VALUES ( ' . $string_values . ' )';
        if ( ! dbi_execute ( $sql, $sql_params ) )
          $error = db_error ();
      }
    }
    // here to end not in icalclient
    if ( $subType != 'icalclient' && $subType != 'remoteics' ) {
      if ( ! empty ( $error ) && empty ( $overlap ) ) {
        $error_num++;
        echo print_error ( $error ) . "\n<br />\n";
      }
      if ( $Entry['Duration'] > 0 ) {
        $time = trim( display_time ( '', 0, $Entry['StartTime'] )
           . '-' . display_time ( '', 2, $Entry['EndTime'] ) );
      }
      // Conflicting
      if ( ! empty ( $overlap ) ) {
        $message.= '<b><h2>' .
        translate ( 'Scheduling Conflict' ) . ': ';
        $count_con++;
        $message.= '</h2></b>';

        $dd = date ( 'm-d-Y', $Entry['StartTime'] );
        $Entry['Summary'] = str_replace ( "''", "'", $Entry['Summary'] );
        $Entry['Summary'] = str_replace ( "'", "\\'", $Entry['Summary'] );
        $message.= htmlspecialchars ( $Entry['Summary'] );
        $message.= ' (' . $dd;
        if ( ! empty ( $time ) )
          $message.= '&nbsp; ' . $time;
        $message.= ")<br />\n";
        etranslate ( 'conflicts with the following existing calendar entries' );
        $message.= ":<ul>\n" . $overlap . "</ul>\n";
      } else {
        // No Conflict
        if ( $count_suc == 0 ) {
          //echo '<b><h2>' .
          //translate ( 'Event Imported' ) . ":</h2></b><br />\n";
        }
        $count_suc++;

        $dd = $Entry['start_date'];
       
      }
      // Reset Variables
      $overlap = $error = $dd = $time = '';
    }
    
  }
}
?>