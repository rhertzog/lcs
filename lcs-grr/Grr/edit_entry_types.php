<?php
#########################################################################
#                        edit_entry_types.php                           #
#                                                                       #
#            Page "Ajax" utilis�e pour g�n�rer les types                #
#                                                                       #
#            Derni�re modification : 09/04/2008                         #
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
if (isset($_GET["type"])) {
  $type = $_GET["type"];
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

// Type de r�servation

// Avant d'afficher la liste d�roulante des types, on stocke dans $display_type et on teste le nombre de types � afficher
// Si ne nombre est �gal � 1, on ne laisse pas le choix
$nb_type = 0;
$type_nom_unique = "??";
$type_id_unique = "??";
$display_type = "<B>".get_vocab("type")." *".get_vocab("deux_points")."</B></TD></TR>\n";
$display_type .= "<TR><TD class=\"CL\">";
$display_type .= "<SELECT name=\"type\" size=\"1\">\n";
$display_type .= "<OPTION VALUE='0'>".get_vocab("choose")."\n";
$sql = "SELECT DISTINCT t.type_name, t.type_letter, t.id FROM grr_type_area t
LEFT JOIN grr_j_type_area j on j.id_type=t.id
WHERE (j.id_area  IS NULL or j.id_area != '".$areas."')
ORDER BY t.order_display";
$res = grr_sql_query($sql);

if ($res)
  for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
      // La requ�te sql pr�c�dente laisse passer les cas o� un type est non valide
      // dans le domaine concern� ET au moins dans un autre domaine, d'o� le test suivant
      $test = grr_sql_query1("select id_type from grr_j_type_area where id_type = '".$row[2]."' and id_area='".$areas."'");
      if ($test == -1)
    {
      $nb_type ++;
      $type_nom_unique = $row[0];
      $type_id_unique = $row[1];
      $display_type .= "<OPTION VALUE=\"".$row[1]."\" ";
      // Modification d'une r�servation
      if ($type != "") {
        if ($type == $row[1])  {
          $display_type .=  " SELECTED";
        }
      } else {
      // Nouvelle r�servation
          $id_type_par_defaut = grr_sql_query1("select id_type_par_defaut from grr_area where id = '".$areas."'");
          if ($id_type_par_defaut == $row[2])  $display_type .=  " SELECTED";
      }
      $display_type .=  " >".htmlentities(removeMailUnicode($row[0]))."</option>\n";
    }
    }

$display_type .=  "</SELECT>\n";
header("Content-Type: text/html;charset=".$charset_html);
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

if ($nb_type > 1) {
    echo $display_type;
} else {
    echo "<b>".get_vocab("type").get_vocab("deux_points").htmlentities(removeMailUnicode($type_nom_unique))."</b><input type=\"hidden\" name=\"type\" value=\"".$type_id_unique."\" />\n";
}
?>