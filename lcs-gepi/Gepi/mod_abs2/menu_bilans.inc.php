<?php
/**
 *
 * @version $Id: menu_bilans.inc.php 5114 2010-08-26 15:29:50Z crob $
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

//echo "<ul class='css-tabs' id='menutabs'>\n";

// $onglet_abs = reset(explode("?", basename($_SERVER["REQUEST_URI"])));
$basename_serveur=explode("?", basename($_SERVER["REQUEST_URI"]));
$onglet_abs = reset($basename_serveur);

$_SESSION['abs2_onglet'] = $onglet_abs;
// Tests � remplacer par des tests sur les droits attribu�s aux statuts
if(($_SESSION['statut']=='cpe')||
    ($_SESSION['statut']=='scolarite')) {

    echo "<ul class='css-tabs' id='menutabs' style='font-size:85%'>\n";

    echo "<li><a href='tableau_des_appels.php' ";
    if($onglet_abs=='tableau_des_appels.php') {echo "class='current' ";}
    echo "title='Tableau des appels'>Tableau des appels</a></li>\n";

    echo "<li><a href='absences_du_jour.php' ";
    if($onglet_abs=='absences_du_jour.php') {echo "class='current' ";}
    echo "title='Absences du jour'>Absences du jour</a></li>\n";

    echo "<li><a href='bilan_du_jour.php' ";
    if($onglet_abs=='bilan_du_jour.php') {echo "class='current' ";}
    echo "title='Bilan du jour'>Bilan du jour</a></li>\n";

    echo "<li><a href='extraction_saisies.php' ";
    if($onglet_abs=='extraction_saisies.php') {echo "class='current' ";}
    echo "title='Extraction des saisies'>Extraction des saisies</a></li>\n";

    echo "<li><a href='extraction_demi-journees.php' ";
    if($onglet_abs=='extraction_demi-journees.php') {echo "class='current' ";}
    echo "title='Extraction des saisies'>Extraction des demi-journ�es</a></li>\n";

    echo "</ul>\n";

}

?>
