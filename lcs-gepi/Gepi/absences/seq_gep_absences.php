<?php
/*
 * Last modification  : 13/12/2005
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

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
//**************** EN-TETE *****************
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
echo "<h2>Intitulé des séquences</h2>";
echo "<p>Le tableau suivant indique quelles séquences correspondent à la matinée ou à l'après-midi.
Ces données sont issues du fichier GEP : F_NOMA.DBF";

echo "<table cellpadding=2 cellspacing=0 border=1><tr><td><b>Intitulé de la séquence</b></td><td><b>Type</b></td></tr>";
$sql = "select id_seq, type  from absences_gep order by id_seq";
$res = sql_query($sql);
for ($i = 0; ($row = sql_row($res, $i)); $i++) {
    if ($row[1] == 'M') $temp ="Matinée"; else $temp = "Après-midi";
    echo "<tr><td>".$row[0]."</td><td>".$temp."</td></tr>";
}
echo "</table>";

?>

</body>
</html>
