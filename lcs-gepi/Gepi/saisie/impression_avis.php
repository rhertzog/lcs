<?php
/*
 *  $Id: impression_avis.php 4692 2010-07-06 14:13:45Z crob $
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

//INSERT INTO droits VALUES ('/saisie/impression_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F','Impression des avis trimestrielles des conseils de classe.', '');

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
$titre_page = "Impression des avis du conseil de classe";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

$id_choix_periode=isset($_POST['id_choix_periode']) ? $_POST["id_choix_periode"] : 0;

// On teste si un professeur peut saisir les avis
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiRubConseilProf")!='yes') {
   die("Droits insuffisants pour effectuer cette op�ration");
}

// On teste si le service scolarit� peut saisir les avis
if (($_SESSION['statut'] == 'scolarite') and getSettingValue("GepiRubConseilScol")!='yes') {
   die("Droits insuffisants pour effectuer cette op�ration");
}

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo " | <a href='../impression/parametres_impression_pdf_avis.php'>R�gler les param�tres du PDF</a>";
echo "</p>\n";

if ($_SESSION['statut'] == 'scolarite') { // Scolarite

	if ($id_choix_periode != 0) {
		$periode = "P�riode N�".$id_choix_periode;
		echo "<h3>".$periode;
		echo "</h3>\n";
	} else {
		$periode="";
		echo "<h3>Choix de la p�riode : ";
		echo "</h3>\n";
	}

	// Impression multiple
	echo "<div style=\"text-align: center;\">\n";
	echo "<fieldset>\n";

	if ($id_choix_periode == 0) {
		echo "<legend>S�lectionnez la p�riode pour laquelle vous souhaitez imprimer les avis en s�rie.</legend>\n";
		echo "<form method=\"post\" action=\"impression_avis.php\" name=\"imprime_serie\">\n";
		$requete_periode = "SELECT DISTINCT `num_periode` FROM `periodes`";
		$resultat_periode = mysql_query($requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysql_error());
		echo "<br />\n";
		While ( $data_periode = mysql_fetch_array ($resultat_periode)) {
			echo "P�riode ".$data_periode['num_periode']." : <input type='radio' name='id_choix_periode' value='".$data_periode['num_periode']."' /> <br />\n";
		}
		echo "<br /><br /> <input value=\"Valider la p�riode\" name=\"Valider\" type=\"submit\" />\n
				<br />\n";
	
		echo "</form>\n";
	} else {
		echo "<legend>S�l�ctionnez la (ou les) classe(s) pour lesquels vous souhaitez imprimer les avis.</legend>\n";
		echo "<form method=\"post\" action=\"../impression/avis_pdf.php\" target='_blank' name=\"avis_pdf\">\n";
		if ($id_choix_periode != 0) {
			echo "<br />\n";

			echo "<select id='liste_classes' name='id_liste_classes[]' multiple='yes' size='5'>\n";
				if($_SESSION['statut']=='scolarite'){ //n'affiche que les classes du profil scolarit�
					$login_scolarite = $_SESSION['login'];
					$requete_classe = "SELECT `periodes`.`id_classe`, `classes`.`classe`, `classes`.`nom_complet` , jsc.login, jsc.id_classe
									FROM `periodes`, `classes` , `j_scol_classes` jsc
									WHERE (jsc.login='$login_scolarite'
									AND jsc.id_classe=classes.id
									AND `periodes`.`num_periode` = ".$id_choix_periode."
									AND `classes`.`id` = `periodes`.`id_classe`)
									ORDER BY `nom_complet` ASC";
				} else {
					$requete_classe = "SELECT `periodes`.`id_classe`, `classes`.`classe`, `classes`.`nom_complet` FROM `periodes`, `classes` WHERE `periodes`.`num_periode` = ".$id_choix_periode." AND `classes`.`id` = `periodes`.`id_classe` ORDER BY `nom_complet` ASC";
				}
				$resultat_classe = mysql_query($requete_classe) or die('Erreur SQL !'.$requete_classe.'<br />'.mysql_error());
				echo "		<optgroup label=\"-- Les classes --\">\n";
				While ( $data_classe = mysql_fetch_array ($resultat_classe)) {
					echo "		<option value=\"";
					echo $data_classe['id_classe'];
					echo "\">";
					echo $data_classe['nom_complet']." (".$data_classe['classe'].")";
					echo "</option>\n";
				}
				echo "		</optgroup>\n";
			echo "	</select>\n";
			echo "<input value=\"".$id_choix_periode."\" name=\"id_periode\" type=\"hidden\" />\n";
			echo "<br /><br /> <input value=\"Valider les classes\" name=\"Valider\" type=\"submit\" />\n";
			echo "<br />\n";
		}
		echo "</form>\n";
	}
	echo "</fieldset>\n";
	echo "</div>";

	echo "<br />\n";
	
	echo "<h3>Liste des classes : </h3>\n"; //modele imprime PDF
	
	// Pourle compte scolarite, possibilit� d'imprimer les avis en synth�se (pour ses classes)
	echo "<p>S�l�ctionnez la classe et la p�riode pour lesquels vous souhaitez imprimer les avis :</p>\n";

	$sql = "SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	$result_classes=mysql_query($sql);
	$nb_classes = mysql_num_rows($result_classes);

	if(mysql_num_rows($result_classes)==0){
		echo "<p>Il semble qu'aucune classe n'ait encore �t� cr��e.</p>\n";
	}
	else {
		$nb_classes=mysql_num_rows($result_classes);
		$nb_class_par_colonne=round($nb_classes/3);
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='left'>\n";
		$cpt=0;
		//echo "<td style='padding: 0 10px 0 10px'>\n";
		echo "<td>\n";
		echo "<table border='1' class='boireaus'>\n";
		while($lig_class=mysql_fetch_object($result_classes)){
			if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)){
				echo "</table>\n";
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td>\n";
				echo "<table border='1' class='boireaus'>\n";
			}

			$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$lig_class->id' ORDER BY num_periode";
			$res_per=mysql_query($sql);

			if(mysql_num_rows($res_per)==0){
				echo "<p>ERREUR: Aucune p�riode n'est d�finie pour la classe $lig_class->classe</p>\n";
				echo "</body></html>\n";
				die();
			}
			else{
				echo "<tr>\n";
				echo "<th>$lig_class->classe</th>\n";
				$alt=1;
				while($lig_per=mysql_fetch_object($res_per)){
					$alt=$alt*(-1);
					echo "<td class='lig$alt'> - <a href='../impression/avis_pdf.php?id_classe=$lig_class->id&amp;periode_num=$lig_per->num_periode' target='_blank'>".$lig_per->nom_periode."</a></td>\n";
				}
				echo "</tr>\n";
			}
			$cpt++;
		}
		echo "</table>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}



// Module toutes les classes scolarit�

} else { // appel pour un prof
    echo "<br />";
    $call_prof_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
    $nombre_classe = mysql_num_rows($call_prof_classe);
    if ($nombre_classe == "0") {
        echo "Vous n'�tes pas ".getSettingValue("gepi_prof_suivi")." ! Il ne vous revient donc pas d'imprimer les avis de conseil de classe.";
    } else {
        $j = "0";
        echo "<p>Vous �tes ".getSettingValue("gepi_prof_suivi")." dans la classe de :</p>";
        while ($j < $nombre_classe) {
            $id_classe = mysql_result($call_prof_classe, $j, "id");
            $classe_suivi = mysql_result($call_prof_classe, $j, "classe");

            include "../lib/periodes.inc.php";
            $k="1";
            while ($k < $nb_periode) {

                   echo "<br /><b>$classe_suivi </b>- ";
                   echo "<a href='../impression/avis_pdf.php?id_classe=$id_classe&amp;periode_num=$k'>".ucfirst($nom_periode[$k])."</a>";

               $k++;
            }
            echo "<br />";
            $j++;
        }
    }
}
require("../lib/footer.inc.php");
?>