<?php
/*
 * $Id: maj_import.php 8812 2012-05-30 20:01:41Z crob $
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if(strstr($_SERVER['HTTP_REFERER'],"eleves/index.php")) {$_SESSION['retour_apres_maj_sconet']="../eleves/index.php";}

//**************** EN-TETE *****************
$titre_page = "Mise � jour eleves/responsables";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
echo "<p class=bold>";
echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";

/*
echo "<p>Vous pouvez effectuer les mises � jour de deux fa�ons:</p>\n";
echo "<ul>\n";
echo "<li><a href='maj_import2.php'>Nouvelle m�thode (<i>plus compl�te</i>)</a>: Nouvelle m�thode, en fournissant directement les fichiers XML de Sconet/STS.</li>\n";
echo "<li><a href='maj_import1.php'>Ancienne m�thode</a>: En g�n�rant des fichiers CSV � partir des fichiers XML de Sconet/STS.</li>\n";
echo "</ul>\n";
echo "<p><br /></p>\n";
*/

echo "<p><a href='maj_import2.php'>Mise � jour des donn�es �l�ves/responsables � l'aide des fichiers XML de Sconet/STS</a>.</p>\n";
echo "<p><br /></p>\n";

//==================================
// RNE de l'�tablissement pour comparer avec le RNE de l'�tablissement de l'ann�e pr�c�dente
$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
//==================================
if($gepiSchoolRne=="") {
	echo "<p><b style='color:red;'>Attention</b>: Le RNE de l'�tablissement n'est pas renseign� dans 'Gestion g�n�rale/<a href='../gestion/param_gen.php' target='_blank'>Configuration g�n�rale</a>'<br />Cela peut perturber l'import de l'�tablissement d'origine des �l�ves.<br />Vous devriez corriger avant de poursuivre.</p>\n";
	echo "<p><br /></p>\n";
}

$sql="SELECT 1=1 FROM eleves;";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0){
	echo "<p>Aucun �l�ve ne semble encore pr�sent dans la base.</p>\n";
}
else{
	$sql="SELECT * FROM eleves WHERE ele_id LIKE 'e%' OR ele_id LIKE '';";
	$res_ele=mysql_query($sql);

	if(mysql_num_rows($res_ele)==0){
		echo "<p>Tous vos �l�ves ont un identifiant 'ele_id' format� comme ceux provenant de Sconet.<br />C'est ce qu'il faut pour la mise � jour d'apr�s Sconet.</p>\n";
	}
	else{
		echo "<p>Un ou des �l�ves ont un identifiant 'ele_id' correspondant � une initialisation sans Sconet ou � une cr�ation individuelle manuelle.<br />Ces �l�ves ne pourront pas �tre mis � jour automatiquement d'apr�s Sconet.</p>";

		echo "<p>Voir en <a href='#notes_correction'>sous le tableau</a> les possibilit�s de correction.</p>\n";

		echo "<blockquote>\n";
		echo "<table class='boireaus' summary='El�ves � corriger'>\n";
		echo "<tr>\n";
		echo "<th>Identifiant<br />'ele_id'</th>\n";
		echo "<th>Identifiant<br />'elenoet'</th>\n";
		echo "<th>Login</th>\n";
		echo "<th>Nom</th>\n";
		echo "<th>Pr�nom</th>\n";
		echo "<th>Classe</th>\n";
		echo "</tr>\n";
		$alt=1;
		while($lig=mysql_fetch_object($res_ele)){
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>".$lig->ele_id."</td>\n";
			echo "<td>".$lig->elenoet."</td>\n";
			echo "<td><a href='../eleves/modify_eleve.php?eleve_login=$lig->login'>".$lig->login."</a></td>\n";
			echo "<td>".strtoupper($lig->nom)."</td>\n";
			echo "<td>".ucfirst(strtolower($lig->prenom))."</td>\n";
			echo "<td>\n";

			$sql="SELECT DISTINCT c.classe FROM classes c, j_eleves_classes jec WHERE jec.id_classe=c.id AND jec.login='$lig->login';";
			$res_clas=mysql_query($sql);
			if(mysql_num_rows($res_clas)==0){
				echo "(<i><span style='color:red;'>aucune classe</span></i>)\n";
			}
			else{
				$cpt_clas=0;
				echo "(<i>";
				while($lig3=mysql_fetch_object($res_clas)){
					if($cpt_clas>0){echo ", \n";}
					echo $lig3->classe;
					$cpt_clas++;
				}
				echo "</i>)\n";
			}

			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";

		echo "<a name='notes_correction'></a>\n";
		echo "<p>Si les ELE_ID ne sont pas corrects, mais que les ELENOET de la table 'eleves' correspondent bien � ceux du fichier 'ElevesSansAdresses.xml', vous pouvez corriger les 'ELE_ID' automatiquement dans la page suivante: <a href='corrige_ele_id.php'>Correction des ELE_ID</a></p>\n";

		echo "</blockquote>\n";
	}
}


$sql="SELECT 1=1 FROM resp_pers;";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0){
	echo "<p>Aucun responsables ne semble encore d�fini.</p>\n";
}
else{
	$sql="SELECT * FROM resp_pers WHERE pers_id LIKE 'p%';";
	$res_pers=mysql_query($sql);
	if(mysql_num_rows($res_pers)==0){
		echo "<p>Tous vos responsables ont un identifiant 'pers_id' format� comme ceux provenant de Sconet.<br />C'est ce qu'il faut pour la mise � jour d'apr�s Sconet.</p>\n";
	}
	else{
		echo "<p>Un ou des responsables ont un identifiant 'pers_id' correspondant � une initialisation sans Sconet ou � une cr�ation individuelle manuelle.<br />Ces responsables ne pourront pas �tre mis � jour automatiquement d'apr�s Sconet.</p>\n";

		echo "<blockquote>\n";
		echo "<table class='boireaus'>\n";
		echo "<tr>\n";
		echo "<th>Identifiant<br />'pers_id'</th>\n";
		echo "<th>Nom</th>\n";
		echo "<th>Pr�nom</th>\n";
		echo "<th>Responsable de</th>\n";
		echo "</tr>\n";
		$alt=1;
		while($lig=mysql_fetch_object($res_pers)){
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>".$lig->pers_id."</td>\n";
			echo "<td>".strtoupper($lig->nom)."</td>\n";
			echo "<td>".ucfirst(strtolower($lig->prenom))."</td>\n";
			echo "<td>\n";

			$sql="SELECT e.login,e.nom,e.prenom FROM eleves e, responsables2 r WHERE e.ele_id=r.ele_id AND r.pers_id='$lig->pers_id';";
			$res_resp=mysql_query($sql);
			if(mysql_num_rows($res_resp)==0){
				echo "<span style='color:red;'>Aucun �l�ve associ�</span>\n";
			}
			else{
				$cpt_ele=0;
				while($lig2=mysql_fetch_object($res_resp)){
					if($cpt_ele>0){echo "<br />\n";}
					echo ucfirst(strtolower($lig2->prenom))." ".strtoupper($lig2->nom);
					$sql="SELECT DISTINCT c.classe FROM classes c, j_eleves_classes jec WHERE jec.id_classe=c.id AND jec.login='$lig2->login';";
					$res_clas=mysql_query($sql);
					if(mysql_num_rows($res_clas)==0){
						echo "(<i><span style='color:red;'>aucune classe</span></i>)\n";
					}
					else{
						$cpt_clas=0;
						echo "(<i>";
						while($lig3=mysql_fetch_object($res_clas)){
							if($cpt_clas>0){echo ", \n";}
							echo $lig3->classe;
							$cpt_clas++;
						}
						echo "</i>)\n";
					}
					$cpt_ele++;
				}
			}

			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "</blockquote>\n";
	}
}


echo "<p><br /></p>\n";
echo "<p><i>NOTE&nbsp;:</i> Cette page ne permet pas d'initialiser une ann�e, mais seulement de mettre � jour en cours d'ann�e les informations �l�ves (<i>nom, pr�nom, naissance, INE, r�gime,...</i>) et responsables (<i>nom, pr�nom, changement d'adresse, tel,...</i>), et d'importer les �l�ves/responsables ajout�s en cours d'ann�e.</p>\n";

// Il faudrait permettre de corriger l'ELE_ID et le PERS_ID
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
