<?php
/*
 * $Id: settings.inc 3465 2009-09-25 15:12:30Z crob $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * Load settings from the database
 *
 * Query all the settings
 * Fetch the result in the $gepiSettings associative array
 *
 * Returns true if all went good, false otherwise
 *
 *
 * @return bool The settings are loaded
 */
function loadSettings()
{
    global $gepiSettings;
    $sql = "select NAME, VALUE from setting";
    $res = sql_query($sql);
    if (! $res) return (false);
    if (sql_count($res) == 0) {
        return (false);
    } else {
        for ($i = 0; ($row = sql_row($res, $i)); $i++) {
            $gepiSettings[$row[0]] = $row[1];
        }
        return (true);
    }
}

/**
 * Get the value of a setting by its name
 *
 * Use this function within other functions so you don'y have to declare
 * $gepiSettings global
 *
 * Returns the value if the name exists
 *
 * @_name               string                  The name of the setting you want
 *
 * @return              mixed                   The value matching _name
 */

function getSettingValue($_name)
{
    global $gepiSettings;
    if (isset($gepiSettings[$_name])) return ($gepiSettings[$_name]);
}

/**
 * Save a name, value pair to the database
 *
 * Use this function ponctually. If you need to save several settings,
 * you'd better write your own code
 *
 * Returns the result of the operation
 *
 * @_name               string                  The name of the setting to save
 * @_value              string                  Its value
 *
 * @return              bool                    The result of the operation
 */
function saveSetting($_name, $_value)
{
    global $gepiSettings;
   if (isset($gepiSettings[$_name])) {
    $sql = "update setting set VALUE = \"" . $_value . "\" where NAME = \"" . $_name . "\"";
    $res = sql_query($sql);
         if ( ! $res) return (false);
    } else {
        $sql = "insert into setting set NAME = \"" . $_name . "\", VALUE = \"" . $_value . "\"";
    $res = sql_query($sql);
        if ( ! $res) return (false);
    }
    $gepiSettings[$_name] = $_value;
    return (true);
}
?>