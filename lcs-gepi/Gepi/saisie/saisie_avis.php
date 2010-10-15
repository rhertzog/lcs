<?php
/*
 * $Id: saisie_avis.php 2147 2008-07-23 09:01:04Z tbelliard $
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
$titre_page = "Saisie des avis du conseil de classe";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On teste si un professeur peut saisir les avis
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiRubConseilProf")!='yes') {
   die("Droits insuffisants pour effectuer cette op�ration");
}

// On teste si le service scolarit� peut saisir les avis
if (($_SESSION['statut'] == 'scolarite') and getSettingValue("GepiRubConseilScol")!='yes') {
   die("Droits insuffisants pour effectuer cette op�ration");
}

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
    //$call_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");

	if($_SESSION['statut']=='scolarite'){
		$call_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	else{
		$call_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	}

    $nombre_classe = mysql_num_rows($call_classe);
	if($nombre_classe==0){
		echo "<p>Aucune classe ne vous est attribu�e.<br />Contactez l'administrateur pour qu'il effectue le param�trage appropri� dans la Gestion des classes.</p>\n";
	}
	else{

		$sql="SELECT MAX(num_periode) nb_max_periode FROM periodes;";
		$res_per=mysql_query($sql);
		$tmp_per=mysql_fetch_object($res_per);
		$nb_max_periode=$tmp_per->nb_max_periode;

		echo "<table class='boireaus'>\n";

		echo "<tr>\n";
		echo "<th rowspan='2'>Classe</th>\n";
		echo "<th rowspan='2' onMouseover=\"afficher_div('info_classe','y',10,10)\" onmouseout=\"cacher_div('info_classe')\">Avis seul</th>\n";
		echo "<th rowspan='2'>Individuel avec<br />appr�ciations</th>\n";
		echo "<th colspan='$nb_max_periode'>Import CSV</th>\n";
		echo "</tr>\n";

		$tabdiv_infobulle[]=creer_div_infobulle("info_classe","","","<center>Saisir les avis, pour toute la classe, avec rappel des avis des autres p�riodes.</center>","",10,0,"n","n","y","n");


		echo "<tr>\n";
		for($i=1;$i<=$nb_max_periode;$i++){
			echo "<th>P�riode $i</th>\n";
		}
		echo "</tr>\n";


		$tabdiv_infobulle[]=creer_div_infobulle("saisie_avis1","","","<center>Saisir les avis, pour toute la classe, avec rappel des avis des autres p�riodes.</center>","",15,0,"n","n","y","n");
		$tabdiv_infobulle[]=creer_div_infobulle("saisie_avis2","","","<center>Saisir les avis, �l�ve par �l�ve, avec visualisation des r�sultats de l'�l�ve.</center>","",15,0,"n","n","y","n");
		$tabdiv_infobulle[]=creer_div_infobulle("import_avis","","","<center>Importer un fichier d'appr�ciations (format csv)</center>","",15,0,"n","n","y","n");		$tabdiv_infobulle[]=creer_div_infobulle("periode_close","","","<center>".$gepiClosedPeriodLabel."</center>","",8,0,"n","n","y","n");

		$j = "0";
		$alt=1;
		while ($j < $nombre_classe) {
			$id_classe = mysql_result($call_classe, $j, "id");
			$classe_suivi = mysql_result($call_classe, $j, "classe");

			/*
			echo "<br /><b>$classe_suivi</b> --- <a href='saisie_avis1.php?id_classe=$id_classe'>Saisir les avis, pour toute la classe, avec rappel des avis des autres p�riodes.</a>";
			echo "<br /><b>$classe_suivi</b> --- <a href='saisie_avis2.php?id_classe=$id_classe'>Saisir les avis, �l�ve par �l�ve, avec visualisation des r�sultats de l'�l�ve.</a><br />";
			*/

			/*
			include "../lib/periodes.inc.php";
			$k="1";
			while ($k < $nb_periode) {
				if ($ver_periode[$k] != "O") {
					echo "<b>$classe_suivi</b> --- ".ucfirst($nom_periode[$k]);
					echo " --- <a href='import_app_cons.php?id_classe=$id_classe&amp;periode_num=$k'>Importer un fichier d'appr�ciations (format csv)</a><br />";
				}
				$k++;
			}
			*/

			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>$classe_suivi</td>\n";
			//echo "<td><a href='saisie_avis1.php?id_classe=$id_classe'><img src='../images/import_notes_app.png' width='30' height='30' /></a></td>\n";
			echo "<td><a href='saisie_avis1.php?id_classe=$id_classe'><img src='../images/saisie_avis1.png' width='34' height='34'";
			echo " onmouseover=\"afficher_div('saisie_avis1','y',10,10)\" onmouseout=\"cacher_div('saisie_avis1')\" />\n";
			echo "</a></td>\n";
			echo "<td><a href='saisie_avis2.php?id_classe=$id_classe'><img src='../images/saisie_avis2.png' width='34' height='34'";
			echo " onmouseover=\"afficher_div('saisie_avis2','y',10,10)\" onmouseout=\"cacher_div('saisie_avis2')\" />\n";
			echo "</a></td>\n";


			include "../lib/periodes.inc.php";
			$k="1";
			while ($k < $nb_periode) {
				if ($ver_periode[$k] != "O") {
					echo "<td><a href='import_app_cons.php?id_classe=$id_classe&amp;periode_num=$k'><img src='../images/import_notes_app.png' width='30' height='30'";
					echo " onmouseover=\"afficher_div('import_avis','y',10,10)\" onmouseout=\"cacher_div('import_avis')\" />\n";
					echo "</a></td>\n";
				}
				else{
					echo "<td>\n";
					echo "<img src='../images/disabled.png' width='20' height='20'";
					echo " onmouseover=\"afficher_div('periode_close','y',10,10)\" onmouseout=\"cacher_div('periode_close')\" />\n";
					echo "</td>\n";
				}
				$k++;
			}
			echo "</tr>\n";

			$j++;
		}
		echo "</table>\n";

	}
} else {
    $call_prof_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
    $nombre_classe = mysql_num_rows($call_prof_classe);
    if ($nombre_classe == "0") {
        echo "Vous n'�tes pas ".getSettingValue("gepi_prof_suivi")." ! Il ne vous revient donc pas de saisir les avis de conseil de classe.";
    } else {
        $j = "0";
        echo "<p>Vous �tes ".getSettingValue("gepi_prof_suivi")." dans la classe de :</p>";
        while ($j < $nombre_classe) {
            $id_classe = mysql_result($call_prof_classe, $j, "id");
            $classe_suivi = mysql_result($call_prof_classe, $j, "classe");
            echo "<br />$classe_suivi --- <a href='saisie_avis1.php?id_classe=$id_classe'>Saisir les avis pour mon groupe.</a>";
            echo "<br />$classe_suivi --- <a href='saisie_avis2.php?id_classe=$id_classe'>Saisir les avis pour mon groupe, avec visualisation des r�sultats.</a>";
            include "../lib/periodes.inc.php";
            $k="1";
            while ($k < $nb_periode) {
               if ($ver_periode[$k] != "O") {
                   echo "<br />$classe_suivi --- ".ucfirst($nom_periode[$k]);
                   echo " --- <a href='import_app_cons.php?id_classe=$id_classe&amp;periode_num=$k'>Importer un fichier d'appr�ciations (format csv)</a>";
               }
               $k++;
            }
            echo "<br />";
            $j++;
        }
    }
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>