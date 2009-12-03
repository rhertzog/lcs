<?php
/*
 * $Id: index.php 2813 2008-12-28 17:13:09Z crob $
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

// Initialisation
$saisie_matiere = isset($_POST['saisie_matiere']) ? $_POST['saisie_matiere'] : (isset($_GET['saisie_matiere']) ? $_GET['saisie_matiere'] : NULL);

$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
    $current_group = get_group($id_groupe);
} else {
    $current_group = false;
}

include "../lib/periodes.inc.php";
//**************** EN-TETE *****************
$titre_page = "Saisie des moyennes et appr�ciations";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>

<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>
<?php

if($_SESSION['statut']=='secours') {
	echo " | <a href='saisie_secours_eleve.php'>Choix d'un �l�ve</a>";
}

if ($current_group) {

    $matiere_nom = $current_group["matiere"]["nom_complet"];
    $classes = $current_group["classlist_string"];

    echo " | <a href='index.php'>Mes enseignements</a></p>\n";
    //echo "<p class='grand'> Groupe : " . $current_group["description"] . " ($classes) | Mati�re : $matiere_nom</p>";
    echo "<p class='grand'> Groupe : " . htmlentities($current_group["description"]) . " ($classes) | Mati�re : ".htmlentities($matiere_nom)."</p>\n";
    //echo "<p class='bold'>Saisie manuelle (tous trimestres) :</p>\n<ul>\n";

	/*
    echo "<p class='bold'>Saisie manuelle (tous trimestres) :</p>\n";
	echo "<ul>\n";

    //echo "<li><a href='saisie_notes.php?id_groupe=$id_groupe'>Saisir les moyennes manuellement</a></li>\n";
    //echo "<li><a href='saisie_notes.php?id_groupe=$id_groupe'>Saisir les moyennes</a> (<i>avec importation �ventuelle � partir du carnet de notes</i>)</li>\n";
    echo "<li><a href='saisie_notes.php?id_groupe=$id_groupe'>Saisir les moyennes</a></li>\n";

    //echo "<li><a href='saisie_appreciations.php?id_groupe=$id_groupe'>Saisir les appr�ciations manuellement</a></li>\n</ul>\n";
    echo "<li><a href='saisie_appreciations.php?id_groupe=$id_groupe'>Saisir les appr�ciations</a></li>\n</ul>\n";
	*/

    echo "<p class='bold'>Saisie manuelle :</p>\n";
	echo "<blockquote>\n";
	//echo "<ul>\n";
    //echo "<li>\n";
		$i=1;
		echo "<table class='boireaus' border='1' summary='Saisie'>\n";
		echo "<tr>\n";
		echo "<th></th>\n";
		while ($i < $nb_periode) {
			//echo "<th>P�riode $i</th>\n";
			echo "<th>".$current_group["periodes"][$i]["nom_periode"]."</th>\n";
			$i++;
		}
		echo "<th>Impression</th>\n";
		echo "</tr>\n";

		$i=1;
		echo "<tr class='lig-1'>\n";
		echo "<th>Moyennes</th>\n";
		$liste_periodes_ouvertes="";
		while ($i < $nb_periode) {
			echo "<td>\n";
			//if ($current_group["classe"]["ver_periode"]["all"][$i] >= 2) {
			if (($current_group["classe"]["ver_periode"]["all"][$i] >= 2)||
				((($current_group["classe"]["ver_periode"]["all"][$i]!=0))&&($_SESSION['statut']=='secours'))) {
				if($liste_periodes_ouvertes!=""){$liste_periodes_ouvertes.=", ";}
				$liste_periodes_ouvertes.=$current_group["periodes"][$i]["nom_periode"];

				$tabdiv_infobulle[]=creer_div_infobulle("info_periode_$i","","","<center>Saisir les moyennes</center>","",10,0,"n","n","y","n");

				echo "<a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$i' onmouseover=\"afficher_div('info_periode_$i','y',10,10)\" onmouseout=\"cacher_div('info_periode_$i')\">";
				//echo "<img src='../images/edit16.png' width='32' height='32' ";
				echo "<img src='../images/icons/bulletin_edit.png' width='34' height='34' alt='Saisie de notes' ";
				//echo "alt='Saisir les moyennes' title='Saisir les moyennes' ";
				echo "/></a>\n";
			}
			else{
				$tabdiv_infobulle[]=creer_div_infobulle("info_periode_$i","","","<center>Consulter les moyennes</center>","",12,0,"n","n","y","n");

				echo "<a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$i' onmouseover=\"afficher_div('info_periode_$i','y',10,10)\" onmouseout=\"cacher_div('info_periode_$i')\"><img src='../images/icons/chercher.png' width='32' height='32' alt='Consultation des moyennes' ";
				//echo "alt='Saisir les moyennes' title='Consulter les moyennes' ";
				echo "/></a>\n";
			}
			echo "</td>\n";
			$i++;
		}

		$tabdiv_infobulle[]=creer_div_infobulle("info_visu","","","<center>Visualisation des moyennes et appr�ciations</center>","",12,0,"n","n","y","n");

		echo "<td rowspan='2'><a href='../prepa_conseil/index1.php?id_groupe=$id_groupe' onmouseover=\"afficher_div('info_visu','y',10,10)\" onmouseout=\"cacher_div('info_visu')\"><img src='../images/icons/print.png' width='32' height='32' alt='Visualisation des moyennes et appr�ciations' /></a></td>\n";
		echo "</tr>\n";


		/*
		$i=1;
		echo "<tr class='lig1'>\n";
		echo "<th>Appr�ciations</th>\n";
		echo "<td colspan='$nb_periode'>\n";
		if($liste_periodes_ouvertes!=""){

			echo "<a href='saisie_appreciations.php?id_groupe=$id_groupe'>Saisir les appr�ciations</a>";
			echo "<br />(<i>$liste_periodes_ouvertes</i>)\n";
		}
		else{
			echo "<a href='saisie_appreciations.php?id_groupe=$id_groupe'>Consulter les appr�ciations</a>";
		}
		echo "</td>\n";
		echo "</tr>\n";
		*/

		$i=1;
		echo "<tr class='lig1'>\n";
		echo "<th>Appr�ciations</th>\n";
		while ($i < $nb_periode) {
			echo "<td>\n";
			//if ($current_group["classe"]["ver_periode"]["all"][$i] >= 2) {
			if (($current_group["classe"]["ver_periode"]["all"][$i] >= 2)||
				((($current_group["classe"]["ver_periode"]["all"][$i]!=0))&&($_SESSION['statut']=='secours'))) {

				$tabdiv_infobulle[]=creer_div_infobulle("info_app_periode_$i","","","<center>Saisir les appr�ciations</center>","",12,0,"n","n","y","n");

				echo "<a href='saisie_appreciations.php?id_groupe=$id_groupe' onmouseover=\"afficher_div('info_app_periode_$i','y',10,10)\" onmouseout=\"cacher_div('info_app_periode_$i')\"><img src='../images/icons/bulletin_edit.png' width='34' height='34' alt='Saisie appr�ciations' /></a>";

			}
			else{
				$tabdiv_infobulle[]=creer_div_infobulle("info_app_periode_$i","","","<center>".$gepiClosedPeriodLabel."</center>","",8,0,"n","n","y","n");

				echo "<img src='../images/disabled.png' width='20' height='20' alt='P�riode close' ";
				//echo " alt='".$gepiClosedPeriodLabel."' title='".$gepiClosedPeriodLabel."'";
				echo " onmouseover=\"afficher_div('info_app_periode_$i','y',10,10)\" onmouseout=\"cacher_div('info_app_periode_$i')\" />\n";
			}
			echo "</td>\n";
			$i++;
		}
		echo "</tr>\n";



		echo "<tr class='lig-1'>\n";
		echo "<th>Importation d'un fichier CSV</th>\n";
		$i=1;
		while ($i < $nb_periode) {
			echo "<td>\n";
			//if ($current_group["classe"]["ver_periode"]["all"][$i] >= 2) {
			if (($current_group["classe"]["ver_periode"]["all"][$i] >= 2)||
				((($current_group["classe"]["ver_periode"]["all"][$i]!=0))&&($_SESSION['statut']=='secours'))) {
				$tabdiv_infobulle[]=creer_div_infobulle("info_import_csv_periode_$i","","","<center>Import CSV<br />(<i>les champs vides ne sont pas import�s</i>)</center>","",15,0,"n","n","y","n");

				//echo "<a href='import_note_app.php?id_groupe=$id_groupe&amp;periode_num=$i' onmouseover=\"afficher_div('info_import_csv_periode_$i','y',10,10)\" onmouseout=\"cacher_div('info_import_csv_periode_$i')\"><img src='../images/import4.png' width='32' height='32' ";
				echo "<a href='import_note_app.php?id_groupe=$id_groupe&amp;periode_num=$i' onmouseover=\"afficher_div('info_import_csv_periode_$i','y',10,10)\" onmouseout=\"cacher_div('info_import_csv_periode_$i')\"><img src='../images/import_notes_app.png' width='30' height='30' alt='Import' ";
				echo "/></a>\n";
			}
			else{
				$tabdiv_infobulle[]=creer_div_infobulle("info_import_csv_periode_$i","","","<center>".$gepiClosedPeriodLabel."</center>","",8,0,"n","n","y","n");

				echo "<img src='../images/disabled.png' width='20' height='20' alt='P�riode close'";
				//echo " alt='".$gepiClosedPeriodLabel."' title='".$gepiClosedPeriodLabel."'";
				echo " onmouseover=\"afficher_div('info_import_csv_periode_$i','y',10,10)\" onmouseout=\"cacher_div('info_import_csv_periode_$i')\" />\n";
			}
			echo "</td>\n";
			$i++;
		}
		echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";

		echo "</table>\n";
	//echo "</li>\n";

	echo "<p><br /></p>\n";
	//echo "<li>\n";
	/*
	echo "<p>";
	if($liste_periodes_ouvertes!=""){
		echo "<a href='saisie_appreciations.php?id_groupe=$id_groupe'>Saisir les appr�ciations</a>";
		echo " (<i>$liste_periodes_ouvertes</i>)\n";
	}
	else{
		echo "<a href='saisie_appreciations.php?id_groupe=$id_groupe'>Consulter les appr�ciations</a>";
	}
	echo "</p>\n";
	*/

	//echo "</li>\n";
	//echo "</ul>\n";
	echo "</blockquote>\n";


	/*
	// Ce n'est plus utile... c'est � la premi�re p�riode ouverte que l'on acc�de avec la saisie manuelle.
    $i="1";
    // Avec affichage de la colonne "carnet de note"
    while ($i < $nb_periode) {
        if ($current_group["classe"]["ver_periode"]["all"][$i] >= 2) {
            echo "<p class='bold'>".ucfirst($nom_periode[$i])." - Importation � partir du carnet de notes :</p>\n";
            echo "<ul><li><a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$i'>Importer les notes � partir du carnet de notes</a></li></ul>\n";
        }
        $i++;
    }
	*/



	echo "<p class='bold'>Pr�paration de l'importation d'un fichier de moyennes/appr�ciations :</p>\n";
	echo "<blockquote>\n";

	echo "<table class='boireaus' border='1' summary='Export'>\n";
	echo "<tr>\n";
	echo "<th>Export / P�riode</th>\n";
	$i=1;
	while ($i < $nb_periode) {
		//echo "<th>P�riode $i</th>\n";
		echo "<th>".$current_group["periodes"][$i]["nom_periode"]."</th>\n";
		$i++;
	}
	echo "</tr>\n";

	$i=1;
	echo "<tr class='lig-1'>\n";
	echo "<th>CSV</th>\n";
	while ($i < $nb_periode) {
		echo "<td>\n";
		//if ($current_group["classe"]["ver_periode"]["all"][$i] >= 2) {
		if (($current_group["classe"]["ver_periode"]["all"][$i] >= 2)||
			((($current_group["classe"]["ver_periode"]["all"][$i]!=0))&&($_SESSION['statut']=='secours'))) {
			$tabdiv_infobulle[]=creer_div_infobulle("info_export_csv_periode_$i","","","<center>Export CSV des identifiants GEPI, avec les colonnes Moyennes et Appr�ciations de cette classe, avec ligne d'ent�te.</center>","",15,0,"n","n","y","n");

			echo "<a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=$i&amp;champs=3&amp;ligne_entete=y&amp;mode=Id_Note_App' onmouseover=\"afficher_div('info_export_csv_periode_$i','y',10,10)\" onmouseout=\"cacher_div('info_export_csv_periode_$i')\"><img src='../images/notes_app_csv.png' width='30' height='30' alt='Export' ";
			echo "/></a>\n";
		}
		else{
			$tabdiv_infobulle[]=creer_div_infobulle("info_export_csv_periode_$i","","","<center>".$gepiClosedPeriodLabel."</center>","",8,0,"n","n","y","n");

			echo "<img src='../images/disabled.png' width='20' height='20' alt='P�riode close'";
			//echo " alt='".$gepiClosedPeriodLabel."' title='".$gepiClosedPeriodLabel."'";
			echo " onmouseover=\"afficher_div('info_export_csv_periode_$i','y',10,10)\" onmouseout=\"cacher_div('info_export_csv_periode_$i')\" />\n";
		}
		echo "</td>\n";
		$i++;
	}
	echo "</tr>\n";



	if(getSettingValue("export_cn_ods")=='y') {
		echo "<tr class='lig1'>\n";
		echo "<th>ODS</th>\n";

		$temoin_ods="y";
		// V�rification si les tests file_exists() sont accept�s.
		if(getSettingValue("gepi_pmv")!="n"){
			if(!file_exists("../lib/ss_zip.class.php")){
				$temoin_ods="n";
			}
		}

		if($_SESSION['user_temp_directory']!='y'){
			$temoin_ods="n";
		}

		if($temoin_ods=="y"){
			$i="1";
			// importation par csv
			while ($i < $nb_periode) {
				echo "<td>\n";
				//if ($current_group["classe"]["ver_periode"]["all"][$i] >= 2) {
				if (($current_group["classe"]["ver_periode"]["all"][$i] >= 2)||
					((($current_group["classe"]["ver_periode"]["all"][$i]!=0))&&($_SESSION['statut']=='secours'))) {

					//echo "-> <a href='export_class_ods.php?id_groupe=$id_groupe&amp;periode_num=$i'>T�l�charger un fichier tableur OpenOffice (<i>ODS</i>) avec les identifiants GEPI, les colonnes Moyennes et Appr�ciations de cette classe.</a>\n";

					$tabdiv_infobulle[]=creer_div_infobulle("info_export_ods_periode_$i","","","<center>Export tableur OpenOffice.org (<i>ODS</i>) des identifiants GEPI, avec les colonnes Moyennes et Appr�ciations de cette classe, avec ligne d'ent�te.</center>","",15,0,"n","n","y","n");

					echo "<a href='export_class_ods.php?id_groupe=$id_groupe&amp;periode_num=$i' onmouseover=\"afficher_div('info_export_ods_periode_$i','y',10,10)\" onmouseout=\"cacher_div('info_export_ods_periode_$i')\"><img src='../images/notes_app_ods.png' width='30' height='30' alt='Export ODS' ";
					echo "/></a>\n";

				}
				else{
					$tabdiv_infobulle[]=creer_div_infobulle("info_export_ods_periode_$i","","","<center>".$gepiClosedPeriodLabel."</center>","",8,0,"n","n","y","n");

					echo "<img src='../images/disabled.png' width='20' height='20' alt='P�riode close' ";
					//echo " alt='".$gepiClosedPeriodLabel."' title='".$gepiClosedPeriodLabel."'";
					echo " onmouseover=\"afficher_div('info_export_ods_periode_$i','y',10,10)\" onmouseout=\"cacher_div('info_export_ods_periode_$i')\" />\n";
				}
				echo "</td>\n";
				$i++;
			}
		}
		else{
			echo "<td colspan='$nb_periode'><font color='red'>L'export tableur ODS n'est pas possible.</font></td>\n";
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "</blockquote>\n";




/*
    $i="1";
    // importation par csv
    while ($i < $nb_periode) {
        if ($current_group["classe"]["ver_periode"]["all"][$i] >= 2) {
            //echo "<p class='bold'>".ucfirst($nom_periode[$i])." - Importation d'un fichier de moyennes/appr�ciations (format csv) :</p>\n";
            echo "<p class='bold'>".ucfirst($nom_periode[$i])." - Pr�paration de l'importation d'un fichier de moyennes/appr�ciations (format csv) :</p>\n";
            //echo "<ul>\n<li><a href='import_note_app.php?id_groupe=$id_groupe&amp;periode_num=$i'>Proc�der � l'importation</a> et consulter l'aide.</li>\n";

            echo "<ul>\n";
            echo "<li>Pr�paration du fichier d'importation :<br />-> <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=".$i."&amp;champs=3'>T�l�charger le fichier des noms, pr�noms et identifiants GEPI de cette classe</a>, ou bien,";

            echo "<li>Pr�paration du fichier d'importation :";
            //echo "<br />-> <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=".$i."&amp;champs=1'>T�l�charger le fichier des identifiants GEPI seuls de cette classe.</a></ul>";
            //echo "<br />-> <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=".$i."&amp;champs=1'>T�l�charger le fichier des identifiants GEPI seuls de cette classe</a>, ou encore";
            echo "<br />-> <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=".$i."&amp;champs=3&amp;ligne_entete=y&amp;mode=Id_Note_App'>T�l�charger le fichier des identifiants GEPI avec les colonnes Moyennes et Appr�ciations de cette classe, avec ligne d'ent�te.</a>";
            echo "<br />\n";
			if(getSettingValue("export_cn_ods")=='y') {
				if($_SESSION['user_temp_directory']=='y'){
					echo "-> <a href='export_class_ods.php?id_groupe=$id_groupe&amp;periode_num=$i'>T�l�charger un fichier tableur OpenOffice (<i>ODS</i>) avec les identifiants GEPI, les colonnes Moyennes et Appr�ciations de cette classe.</a>\n";
				}
				else{
					echo "-> <font color='red'>L'export tableur ODS n'est pas possible.</font>";
				}
				echo "<br />\n";
			}
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<i>les champs vides ne sont pas import�s</i>)\n";
            echo "</li>\n";
            echo "</ul>\n";
        }
        $i++;
    }
	*/

} else {

    // On commence par g�rer simplement la liste des groupes pour les professeurs

    if ($_SESSION["statut"] == "professeur") {
        echo "<p>Saisir les moyennes ou appr�ciations par classe :</p>\n";

        //$groups = get_groups_for_prof($_SESSION["login"]);
		$groups = get_groups_for_prof($_SESSION["login"],"classe puis mati�re");
        foreach ($groups as $group) {
		//echo "<p><a href='index.php?id_groupe=" . $group["id"] . "'>" . $group["description"] . "</a> (" . $group["classlist_string"] . ")</p>\n";
		//echo "<p><a href='index.php?id_groupe=" . $group["id"] . "'>" . htmlentities($group["description"]) . "</a> (" . $group["classlist_string"] . ")</p>\n";
		//echo "<p><a href='index.php?id_groupe=" . $group["id"] . "'>" . htmlentities($group["description"]) . "</a> (" . $group["classlist_string"] . ")</p>\n";
		echo "<p><span class='norme'><b>" . $group["classlist_string"] . "</b> : ";
		echo "<a href='index.php?id_groupe=" . $group["id"] ."'>" . htmlentities($group["description"]) . "</a>";
		echo "</span></p>\n";
        }
    } elseif ($_SESSION["statut"] == "secours") {
        echo "<p>Saisir les moyennes ou appr�ciations par classe :</p>\n";
        $appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
        $lignes = mysql_num_rows($appel_donnees);
        $i = 0;
        while($i < $lignes){
            $id_classe = mysql_result($appel_donnees, $i, "id");
            $nom_classe = mysql_result($appel_donnees, $i, "classe");
            echo "<p><span class='norme'><b>$nom_classe</b> : ";
			echo "<a href='recopie_moyennes.php?id_classe=$id_classe&amp;retour=saisie_index'><b>Recopie des moyennes</b></a> - ";
            $groups = get_groups_for_class($id_classe);
            foreach ($groups as $group) {

		$sql="SELECT u.nom,u.prenom FROM j_groupes_professeurs jgp, utilisateurs u WHERE
				jgp.login=u.login AND
				jgp.id_groupe='".$group["id"]."'
				ORDER BY u.nom,u.prenom";
		$res_prof=mysql_query($sql);
		$texte_alternatif="Pas de prof???";
		if(mysql_num_rows($res_prof)>0){
			$texte_alternatif="";
			while($ligne=mysql_fetch_object($res_prof)){
				$texte_alternatif.=", ".ucfirst(strtolower($ligne->prenom))." ".strtoupper($ligne->nom);
			}
			$texte_alternatif=substr($texte_alternatif,2);
		}

		//echo "<a href='index.php?id_groupe=" . $group["id"] . "'>" . $group["description"] . "</a> - \n";
		echo "<a href='index.php?id_groupe=" . $group["id"] . "' title='$texte_alternatif'>" . htmlentities($group["description"]) . "</a> - \n";
            }
            $i++;
            echo "</span>\n";
            echo "</p>\n";
        }
    }
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>