<?php
/*
 * $Id: index.php 2745 2008-12-08 14:20:55Z delineau $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//**************** EN-TETE **************************************
$titre_page = "Gestion des classes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
?>
<p class=bold>
<a href="../accueil_admin.php"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour </a>
 | <a href='classes_param.php'>Param�trage de plusieurs classes par lots</a>
 | <a href='cpe_resp.php'>Param�trage rapide CPE Responsable</a>
 | <a href='scol_resp.php'>Param�trage scolarit�</a>
 | <a href='acces_appreciations.php'>Param�trage de l'acc�s aux appr�ciations</a>
</p>
<p style='margin-top: 10px;'>
<img src='../images/icons/add.png' alt='' class='back_link' /> <a href="modify_nom_class.php">Ajouter une classe</a>
</p>
<?php
// On va chercher les classes d�j� existantes, et on les affiche.
$call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
$nombre_lignes = mysql_num_rows($call_data);
if ($nombre_lignes != 0) {
    $flag = 1;
    echo "<table cellpadding=3 cellspacing=0 style='border: none; border-collapse: collapse;'>\n";
    $i = 0;
    while ($i < $nombre_lignes){
        $id_classe = mysql_result($call_data, $i, "id");
        $classe = mysql_result($call_data, $i, "classe");
        echo "<tr";
        if ($flag==1) { echo " class='fond_sombre'"; $flag = 0;} else {$flag=1;};
        echo "><td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB; border-right: 1px solid #BBBBBB;'>";
		echo "<b>$classe</b> ";
        echo "</td>\n";
		echo "<td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB; border-right: 1px solid #BBBBBB;'>";
		echo "<a href='periodes.php?id_classe=$id_classe'><img src='../images/icons/date.png' alt='' /> P�riodes</a></td>\n";
		//echo "<td>|<a href='modify_class.php?id_classe=$id_classe'>G�rer les mati�res</a></td>";
		echo "<td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB; border-right: 1px solid #BBBBBB;'>";
        $nb_per = mysql_num_rows(mysql_query("select id_classe from periodes where id_classe = '$id_classe'"));
        if ($nb_per != 0) {
            echo "<a href='classes_const.php?id_classe=$id_classe'><img src='../images/icons/edit_user.png' alt='' /> �l�ves</a></td><td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB;'>";
        } else {
            echo "&nbsp;</td>\n<td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB;'>";
        }
      echo "<a href='../groupes/edit_class.php?id_classe=$id_classe'> <img src='../images/icons/document.png' alt='' /> Enseignements</a></td>\n";
			echo "<td style='padding: 5px; padding-right: 10px; border-right: 1px solid #BBBBBB;'>";

	//=======================================
	// MODIF: boireaus
	// Ajout d'un choix pour d�finir des blocs scientifiques, litt�raires,...
/*
        echo "<a href='modify_nom_class.php?id_classe=$id_classe'>Modifier les param�tres</a></td><td>|
        <a href='../lib/confirm_query.php?liste_cible=$id_classe&amp;action=del_classe'>Supprimer la classe</a> |
        </td><td>";
*/
        if ($nb_per != 0) {
			echo "[<a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe'>config. simplifi�e</a>]</td>\n";
		}
		else {
            echo "&nbsp;</td>\n";
        }

		echo "<td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB; border-right: 1px solid #BBBBBB;'>
	<a href='modify_nom_class.php?id_classe=$id_classe'><img src='../images/icons/configure.png' alt='' /> Param�tres</a></td>\n";

		echo "<td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB;'>
        <a href='../lib/confirm_query.php?liste_cible=$id_classe&amp;action=del_classe'><img src='../images/icons/delete.png' alt='' /> Supprimer</a></td>\n";

		echo "<td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB; border-right: 1px solid #BBBBBB;'>";
	//=======================================


        if ($nb_per == 0) echo " <b>(Classe virtuelle)</b> "; else echo "&nbsp;";

        echo "</td>\n</tr>\n";
    	$i++;
    }

    echo "</table>";
} else {
    echo "<p class='grand'>Attention : aucune classe n'a �t� d�finie dans la base GEPI !</p>";
    echo "<p>Vous pouvez ajouter des classes � la base en cliquant sur le lien ci-dessus, ou bien directement <br /><a href='../initialisation/index.php'>importer les �l�ves et les classes � partir de fichiers GEP.</a></p>";
}
require("../lib/footer.inc.php");
?>