<?php
#########################################################################
#                        trailer.inc.php                                #
#                                                                       #
#                 script de bas de page html                            #
#                                                                       #
#            Dernière modification : 21/04/2005                         #
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

// Affichage d'un lien pour format imprimable
if ( ( !isset($_GET['pview'])  or ($_GET['pview'] != 1)) and (isset($affiche_pview))) {
    echo '<p align="center"><a href="'. traite_grr_url($grr_script_name) .'?';
    if (isset($_SERVER['QUERY_STRING']) and ($_SERVER['QUERY_STRING'] != ''))
        echo htmlspecialchars($_SERVER['QUERY_STRING']) . '&amp;';
    echo 'pview=1" ';
    if (getSettingValue("pview_new_windows")==1) echo ' target="_blank"';
    echo '>' . get_vocab("ppreview") . '</a></p>';
}

?>
</BODY>
</HTML>