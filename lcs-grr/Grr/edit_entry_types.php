<?php
/**
 * edit_entry_types.php
 * Page "Ajax" utilis�e pour g�n�rer les types
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2010-04-07 17:49:56 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: edit_entry_types.php,v 1.10 2010-04-07 17:49:56 grr Exp $
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
 * $Log: edit_entry_types.php,v $
 * Revision 1.10  2010-04-07 17:49:56  grr
 * *** empty log message ***
 *
 * Revision 1.9  2010-03-03 14:41:34  grr
 * *** empty log message ***
 *
 * Revision 1.8  2009-06-04 15:30:17  grr
 * *** empty log message ***
 *
 * Revision 1.7  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.6  2009-03-24 13:30:07  grr
 * *** empty log message ***
 *
 * Revision 1.5  2008-11-16 22:00:58  grr
 * *** empty log message ***
 *
 * Revision 1.4  2008-11-11 22:01:14  grr
 * *** empty log message ***
 *
 *
 */

include "include/admin.inc.php";

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

if(authUserAccesArea(getUserName(), $areas)==0)
{
    showAccessDenied("","","","","");
    exit();
}

// Type de r�servation
$qui_peut_reserver_pour  = grr_sql_query1("select qui_peut_reserver_pour from grr_room where id='".$room."'");
$aff_default=((authGetUserLevel(getUserName(),-1,"room") >= $qui_peut_reserver_pour) or (authGetUserLevel(getUserName(),$areas,"area") >= $qui_peut_reserver_pour));
$aff_type=max(authGetUserLevel(getUserName(),-1,"room"),authGetUserLevel(getUserName(),$areas,"area"));
// Avant d'afficher la liste d�roulante des types, on stocke dans $display_type et on teste le nombre de types � afficher
// Si ne nombre est �gal � 1, on ne laisse pas le choix
$nb_type = 0;
$type_nom_unique = "??";
$type_id_unique = "??";
$display_type = "<table width=100%><tr><td class=\"E\"><B>".get_vocab("type")." *".get_vocab("deux_points")."</B></td></tr>\n";
$affiche_mess_asterisque=true;
$display_type .= "<tr><td class=\"CL\">";
$display_type .= "<select id=\"type\" name=\"type\" size=\"1\" onclick=\"setdefault('type_default','')\">\n";
$display_type .= "<option value='0'>".get_vocab("choose")."\n";
$sql = "SELECT DISTINCT t.type_name, t.type_letter, t.id FROM ".TABLE_PREFIX."_type_area t
LEFT JOIN ".TABLE_PREFIX."_j_type_area j on j.id_type=t.id
WHERE (j.id_area  IS NULL or j.id_area != '".$areas."') and (t.disponible<='".$aff_type."')
ORDER BY t.order_display";
$res = grr_sql_query($sql);

if ($res)
  for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
      // La requ�te sql pr�c�dente laisse passer les cas o� un type est non valide
      // dans le domaine concern� ET au moins dans un autre domaine, d'o� le test suivant
      $test = grr_sql_query1("select id_type from ".TABLE_PREFIX."_j_type_area where id_type = '".$row[2]."' and id_area='".$areas."'");
      if ($test == -1)
    {
      $nb_type ++;
      $type_nom_unique = $row[0];
      $type_id_unique = $row[1];
      $display_type .= "<option value=\"".$row[1]."\" ";
      // Modification d'une r�servation
      if ($type != "") {
        if ($type == $row[1])  {
          $display_type .=  " selected=\"selected\"";
        }
      } else {
      // Nouvelle r�servation
          $id_type_par_defaut = grr_sql_query1("select id_type_par_defaut from ".TABLE_PREFIX."_area where id = '".$areas."'");
		  //R�cup�re le cookie par defaut
		  if($aff_default and isset($_COOKIE['type_default'])) $cookie = $_COOKIE['type_default']; else $cookie="";
          if ((!$cookie and ($id_type_par_defaut == $row[2])) or ($cookie and $cookie==$row[0])) $display_type .=  " selected=\"selected\"";
      }
      $display_type .=  " >".htmlentities(removeMailUnicode($row[0]))."</option>\n";
    }
    }

$display_type .=  "</select>";
if($aff_default)
	$display_type .= "&nbsp;<input type=\"button\" value=\"".get_vocab("definir par defaut")."\" onclick=\"setdefault('type_default',document.getElementById('main').type.options[document.getElementById('main').type.options.selectedIndex].text)\" />";
$display_type .= "</td></tr></table>\n";

if ($unicode_encoding)
 header("Content-Type: text/html;charset=utf-8");
else
 header("Content-Type: text/html;charset=".$charset_html);

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

if ($nb_type > 1) {
    echo $display_type;
} else {
	echo "<table width=100%><tr><td class=\"E\"><b>".get_vocab("type").get_vocab("deux_points").htmlentities(removeMailUnicode($type_nom_unique))."</b>"."<input name=\"type\" type=\"hidden\" value=\"".$type_id_unique."\" /></td></tr></table>\n";
}
?>