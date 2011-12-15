<?php

/**
 * Fichier qui enregistre les concordances et les cours du fichier edt_init_csv2.php
 *
 * @version $Id: edt_init_concordance2.php 8606 2011-11-07 14:52:35Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, St�phane Boireau, Julien Jocal
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

// fonctions edt
require_once("./fonctions_edt.php");
require_once("./edt_init_fonctions.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}
// S�curit�
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// Initialisation des variables
$etape = isset($_POST["etape"]) ? $_POST["etape"] : NULL;
$concord_csv2 = isset($_POST["concord_csv2"]) ? $_POST["concord_csv2"] : NULL;
$nbre_lignes = isset($_POST["nbre_lignes"]) ? $_POST["nbre_lignes"] : NULL;
$aff_infos = isset($_POST["aff_infos"]) ? $_POST["aff_infos"] : NULL;
$debug = NULL; $aff_create = NULL;
//$ = isset($_POST[""]) ? $_POST[""] : NULL;
$msg_enreg = '';

//=================================
//$titre_page="Enregistrer les concordances(2) pour l'import de l'EdT";
require_once("../lib/header.inc");
//=================================
echo "<div id='header'>
	<br />
	<p style='text-align: center;'>Concordances</p>
	<br />

</div>
";
// traitement des donn�es qui arrivent

if ($etape != NULL) {
	// Alors on peut commencer le traitement

	echo '<p>Etape num�ro : '.$etape.'</p>';
	echo '<p>Nbre lignes : '.$nbre_lignes.'</p>';

	if ($etape != 12 AND $etape != 5) {
			$values = NULL;
		// C'est le cas g�n�ral pour enregistrer les concordances entre le fichier csv et Gepi
		// On r�ceptionne les donn�es et on les rentre dans la base
		for($a = 0; $a < $nbre_lignes; $a++){

			$nom_gepi[$a] = isset($_POST["nom_gepi_".$a]) ? $_POST["nom_gepi_".$a] : NULL;
			$nom_export[$a] = isset($_POST["nom_export_".$a]) ? $_POST["nom_export_".$a] : NULL;

			// On pr�pare la requ�te en v�rifiant qu'elle doit bien �tre construite
			if ($nom_gepi[$a] != '' AND $nom_gepi[$a] != 'none') {
				$values .= "('', '".$etape."', '".$nom_export[$a]."', '".$nom_gepi[$a]."'), ";

			}
		}
		// On envoie toutes les requ�tes d'un coup
		echo $values;
		$envoie = mysql_query("INSERT INTO edt_init (id_init, ident_export, nom_export, nom_gepi)
					VALUE ".$values." ('', ".$etape.", 'fin', 'fin')")
					OR error_reporting('Erreur dans la requ�te $envoie de l\'�tape '.$etape.' : '.mysql_error().'<br />'.$envoie);
		// On r�cup�re le nombre de valeurs enregistr�es et on affiche
		if ($etape == 6 OR $etape == 8 OR $etape == 9 OR $etape == 11) {
			echo '<p>Aucun enregistrement, passez � l\'�tape suivante.</p>';
		}else{
			echo '<p>'.$nbre_lignes.' lignes ont �t� enregistr�es dans la base.</p>';
		}

	}elseif($etape == 5){
			$enre = $deja = 0;
		// Ce sont les salles. On va enregistrer celles qui ne sont pas encore dans Gepi
		for($a = 0; $a < $nbre_lignes; $a++){
			$nom_export[$a] = isset($_POST["nom_export_".$a]) ? $_POST["nom_export_".$a] : NULL;
			$test = testerSalleCsv2($nom_export[$a]);
			if ($test == "enregistree") {
				$enre++;
			}elseif($test == "ok"){
				$deja++;
			}
		}
		// Les genres et nombres
		if ($enre >= 2) {
			$s = 's';
			$ont = 'ont';
		}else{
			$s = '';
			$ont = 'a';
		}
		if ($deja >= 2) {
			$en = 'en';
		}else{
			$en = '';
		}
		echo '
		<p>'.$enre.' nouvelle'.$s.' salle'.$s.' '.$ont.' �t� enregistr�e'.$s.' et '.$deja.' existai'.$en.'t d�j�.</p>';

	}elseif($etape == 12){
		// Ce sont les cours qui arrivent, car on a termin� les concordances
		//for($i = 0; $i < $nbre_lignes; $i++){
		$sql="SELECT * FROM tempo2;";
		$res_tempo=mysql_query($sql);
		while($lig_tempo=mysql_fetch_object($res_tempo)) {
			// On initialise toutes les variables et on affiche la valeur de chaque cours
			//$ligne = isset($_POST["ligne_".$i]) ? $_POST["ligne_".$i] : NULL;
			$ligne=$lig_tempo->col1;

			//echo $ligne.'<br />';
			// On explose la variable pour r�cup�rer toutes les donn�es
			$tab = explode("|", $ligne);
			/*/ Toutes les infos sont envoy�es en brut
			for($v = 0; $v < 12; $v++){
				if (!isset($tab[$v])) {

					$tab[$v] = '';
				}else{

					$tab[$v] = my_ereg_replace("wkzx", "'", my_ereg_replace("zxwk", '"', $tab[$v]));

				}
			}*/

			$enregistre = enregistreCoursCsv2($tab[0], $tab[1], $tab[2], $tab[3], $tab[4], $tab[5], $tab[6], $tab[7], $tab[8], $tab[9], $tab[10], $tab[11]);

				$debug = 'ok';

			if ($enregistre["reponse"] == 'ok') {
				// On affiche les infos si c'est demand�

				if ($aff_infos == 'oui' OR $debug == 'ok') {
					$msg_enreg .= 'La ligne '.$i.' a bien �t� enregistr�e.<br />';
				}
			} elseif($enregistre["reponse"] == 'non') {

				if ($aff_infos == 'oui' OR getSettingValue("mod_edt_gr") == 'y' OR $debug == 'ok') {

					// On teste le message d'erreur
					$test_e = strpos($enregistre["msg_erreur"], "Ce");

					if (getSettingValue("mod_edt_gr") == 'y' AND $test_e === FALSE) {

						$essai = gestion_edt_gr($tab);

						if ($essai == 'non') {
							// On a termin�
							$aff_create = 'Pas d\'enregistrement.';
						}else{

							$tab[7] = 'EDT|'.$essai;
							$enregistre2 = enregistreCoursCsv2($tab[0], $tab[1], $tab[2], $tab[3], $tab[4], $tab[5], $tab[6], $tab[7], $tab[8], $tab[9], $tab[10], $tab[11]);
							if ($enregistre2["reponse"] == 'ok') {

								$aff_create = 'Le cours est enregistr� avec un edt_gr.';

							}

						}

					} // if (getSettingValue("mod_edt_gr") == 'y'

					$msg_enreg .= '<p title="'.$tab[0].'|'.$tab[1].'|'.$tab[2].'|'.$tab[3].'|'.$tab[4].'|'.$tab[5].'|'.$tab[6].'|'.$tab[7].'|'.$tab[8].'|'.$tab[9].'|'.$tab[10].'|'.$tab[11].'">La ligne '.$i.' n\'a pas �t� reconnue.'.$enregistre["msg_erreur"].''.$aff_create.'</p>'."\n";
				}
			}else{
				echo '(ligne '.$i.')&nbsp;->&nbsp;Il y a eu un souci car ce n\'est pas ok ou non qui arrive mais '.$enregistre["msg_erreur"].'.<br />';
			}
		}
		echo $msg_enreg; // permet d'afficher le message de bilan
	}

	// On incr�mente le num�ro de l'�tape
	if ($etape != 12) {
		$prochaine_etape = $etape + 1;
		$vers_etape2 = mysql_query("UPDATE edt_init SET nom_export = '".$prochaine_etape."' WHERE ident_export = 'fichierTexte2'");
		// et on affiche un lien qui permet de continuer
		echo '
		<a href="edt_init_csv2.php">Pour continuer les concordances, veuillez recommencer la m�me proc�dure pour l\'�tape n� '.$prochaine_etape.'</a>';

	}else{
		$tempdir = get_user_temp_directory();
		if (file_exists("../temp/".$tempdir."/g_edt_2.csv")) {
			// On efface le fichier csv
			//unlink("../temp/".$tempdir."/g_edt_2.csv");
			echo '
			<p style="color: green;">Le fichier est conserv� dans le r�pertoire de l\'administrateur.</p>';
		}

		// On affiche un lien pour revenir � la page de d�part
		echo '
		<a href="edt_init_csv2.php" style="border: 1px solid black;">Retour</a>';
	}
} // on a termin� le travail de concordances

require("../lib/footer.inc.php");
?>
