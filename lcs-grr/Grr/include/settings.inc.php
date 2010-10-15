<?php
#########################################################################
#                        setting.inc.php                                #
#                                                                       #
#    Biblioth�que de fonction pour la gestion de la table grr_setting   #
#                                                                       #
#            Derni�re modification : 10/07/2006                         #
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

/**
 * Load settings from the database
 *
 * Query all the settings
 * Fetch the result in the $grrSettings associative array
 *
 * Returns true if all went good, false otherwise
 *
 *
 * @return bool The settings are loaded
 */
function loadSettings()
{
    global $grrSettings;
    // Pour tenir compte du changement de nom de la table setting � partir de la version 1.8
    $test = grr_sql_query1("select NAME  from grr_setting where NAME='version'");
    if ($test != -1)
       $sql = "select `NAME`, `VALUE` from grr_setting";
    else
        $sql = "select `NAME`, `VALUE` from setting";

    $res = grr_sql_query($sql);
    if (! $res) return (false);
    if (grr_sql_count($res) == 0) {
        return (false);
    } else {
        for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
            $grrSettings[$row[0]] = $row[1];
        }
        return (true);
    }
}

/**
 * Get the value of a grr_setting by its name
 *
 * Use this function within other functions so you don'y have to declare
 * $grrSettings global
 *
 * Returns the value if the name exists
 *
 * @_name               string                  The name of the setting you want
 *
 * @return              mixed                   The value matching _name
 */

function getSettingValue($_name)
{
    global $grrSettings;
    if (isset($grrSettings[$_name])) return ($grrSettings[$_name]);
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
    global $grrSettings;
    if (isset($grrSettings[$_name])) {
    $sql = "update grr_setting set VALUE = '" . protect_data_sql($_value) . "' where NAME = '" . protect_data_sql($_name) . "'";
    $res = grr_sql_query($sql);
         if ( ! $res) return (false);
    } else {
        $sql = "insert into grr_setting set NAME = '" . protect_data_sql($_name) . "', VALUE = '" . protect_data_sql($_value) . "'";
    $res = grr_sql_query($sql);
        if ( ! $res) return (false);
    }
    $grrSettings[$_name] = $_value;
    return (true);
}