<?php
#########################################################################
#                        edit_entry_champs_add.php                      #
#                                                                       #
#            Page "Ajax" utilis�e pour g�n�rer les champs               #
#                additionnels dans la page de r�servation               #
#                                                                       #
#            Derni�re modification : 09/04/2008                           #
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

include "include/admin.inc.php";
include "include/mrbs_sql.inc.php";

/* Ce script a besoin de trois arguments pass�s par la m�thode GET :
$id : l'identifiant de la r�servation (0 si nouvelle r�servation)
$areas : l'identifiant du domaine
$room : l'identifiant de la ressource
*/

// Initialisation
if (isset($_GET["id"])) {
  $id = $_GET["id"];
  settype($id,"integer");
} else die();

if (isset($_GET['areas'])) {
  $areas = $_GET['areas'];
  settype($areas,"integer");
}
else die();
if (isset($_GET['room'])) {
  $room = $_GET['room'];
  if ($room != "") settype($room,"integer");
}
else die();


if ((authGetUserLevel(getUserName(),-1) < 2) and (auth_visiteur(getUserName(),$room) == 0))
{
    showAccessDenied("","","","","");
    exit();
}

if(authUserAccesArea($_SESSION['login'], $areas)==0)
{
    showAccessDenied("","","","","");
    exit();
}

// Champs additionneles : on r�cup�re les donn�es de la r�servation si il y en a
if ($id !=0)
    $overload_data = mrbsEntryGetOverloadDesc($id);

header("Content-Type: text/html;charset=".$charset_html);
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

echo "</td></tr>";
// Boucle sur les areas
$overload_fields = mrbsOverloadGetFieldslist($areas);
foreach ($overload_fields as $fieldname=>$fieldtype) {
        if ($overload_fields[$fieldname]["obligatoire"] == "y") $flag_obli = " *" ; else $flag_obli = "";
        echo "<tr><td><table width=\"100%\" id=\"id_".$areas."_".$overload_fields[$fieldname]["id"]."\">";
        echo "<TR><TD class=E><b>".removeMailUnicode($fieldname).$flag_obli."</b></TD></TR>\n";
        if (isset($overload_data[$fieldname]["valeur"]))
            $data = $overload_data[$fieldname]["valeur"];
        else
            $data = "";
        if ($overload_fields[$fieldname]["type"] == "textarea" )
            echo "<TR><TD><TEXTAREA COLS=\"80\" ROWS=\"2\" name=\"addon_".$overload_fields[$fieldname]["id"]."\">".htmlentities(removeMailUnicode($data))."</TEXTAREA></TD></TR>\n";
        else if ($overload_fields[$fieldname]["type"] == "text" )
            echo "<TR><TD><INPUT size=\"80\" type=\"text\" name=\"addon_".$overload_fields[$fieldname]["id"]."\" value=\"".htmlentities(removeMailUnicode($data))."\" /></TD></TR>\n";
        else {
            echo "<TR><TD><select name=\"addon_".$overload_fields[$fieldname]["id"]."\" size=\"1\">\n";
            if ($overload_fields[$fieldname]["obligatoire"] == 'y') echo '<option value="">'.get_vocab('choose').'</option>';
            foreach ($overload_fields[$fieldname]["list"] as $value) {
                echo "<option ";
                if ($data == trim($value,"&") or ($data=="" and $value[0]=="&")) echo " selected";
                echo ">".trim($value,"&")."</option>\n";
            }
            echo "</select>\n</TD></TR>\n";
        }
        echo "</table>\n";
}
?>