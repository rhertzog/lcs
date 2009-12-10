<?php
/* $Id: affiche_listes.php 3323 2009-08-05 10:06:18Z crob $ */
/*
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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/affiche_listes.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/affiche_listes.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='G�n�se des classes: Affichage de listes',
statut='';";
$insert=mysql_query($sql);
}

//======================================================================================
// Section checkAccess() � d�commenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : NULL);

$user_temp_directory=get_user_temp_directory();

//**************** EN-TETE *****************
$titre_page = "G�n�se classe: affichage de listes";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();


if((!isset($projet))||($projet=="")) {
	echo "<p style='color:red'>ERREUR: Le projet n'est pas choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='index.php?projet=$projet'>Retour</a>";
//echo "</div>\n";

$afficher_listes=isset($_POST['afficher_listes']) ? $_POST['afficher_listes'] : (isset($_GET['afficher_listes']) ? $_GET['afficher_listes'] : NULL);

// Choix des �l�ves � afficher:
if(!isset($afficher_listes)) {
	echo "</p>\n";

	echo "<h2>Projet $projet</h2>\n";

	// Affichage d�j� choisi ou non
	$id_aff=isset($_POST['id_aff']) ? $_POST['id_aff'] : (isset($_GET['id_aff']) ? $_GET['id_aff'] : NULL);
	if((my_ereg_replace("[0-9]","",$id_aff)!="")||($id_aff=="")) {unset($id_aff);}



	if(isset($id_aff)) {
		$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : NULL;
		if(isset($suppr)) {
			for($i=0;$i<count($suppr);$i++) {
				$sql="DELETE FROM gc_affichages WHERE projet='$projet' AND id_aff='$id_aff' AND id_req='$suppr[$i]';";
				//echo "$sql<br />";
				$del=mysql_query($sql);
			}

			// Si plus aucune requ�te ne reste, il faudrait peut-�tre supprimer le $id_aff... non... il sera r�utilis� si on rajoute une requ�te et il sera perdu et absent de la base si on quitte la page sans ajouter de requ�te
		}
	}



	// Ajout d'une requ�te pour l'affichage en cours
	if(isset($_POST['ajouter'])) {
		$id_clas_act=isset($_POST['id_clas_act']) ? $_POST['id_clas_act'] : array();
		$clas_fut=isset($_POST['clas_fut']) ? $_POST['clas_fut'] : array();
		$avec_lv1=isset($_POST['avec_lv1']) ? $_POST['avec_lv1'] : array();
		$sans_lv1=isset($_POST['sans_lv1']) ? $_POST['sans_lv1'] : array();
		$avec_lv2=isset($_POST['avec_lv2']) ? $_POST['avec_lv2'] : array();
		$sans_lv2=isset($_POST['sans_lv2']) ? $_POST['sans_lv2'] : array();
		$avec_lv3=isset($_POST['avec_lv3']) ? $_POST['avec_lv3'] : array();
		$sans_lv3=isset($_POST['sans_lv3']) ? $_POST['sans_lv3'] : array();
		$avec_autre=isset($_POST['avec_autre']) ? $_POST['avec_autre'] : array();
		$sans_autre=isset($_POST['sans_autre']) ? $_POST['sans_autre'] : array();

		//$id_aff=isset($_POST['id_aff']) ? $_POST['id_aff'] : (isset($_GET['id_aff']) ? $_GET['id_aff'] : NULL);
		//if((my_ereg_replace("[0-9]","",$id_aff)!="")||($id_aff=="")) {unset($id_aff);}
		if(!isset($id_aff)) {
			$sql="SELECT MAX(id_aff) AS max_id_aff FROM gc_affichages;";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				$id_aff=1;
			}
			else {
				$lig_tmp=mysql_fetch_object($res);
				$id_aff=$lig_tmp->max_id_aff+1;
			}
		}

		$sql="SELECT MAX(id_req) AS max_id_req FROM gc_affichages WHERE projet='$projet' AND id_aff='$id_aff';";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			$id_req=1;
		}
		else {
			$lig_tmp=mysql_fetch_object($res);
			$id_req=$lig_tmp->max_id_req+1;
		}
		//echo "id_req=$id_req<br />";

		if(count($id_clas_act)>0) {
			for($i=0;$i<count($id_clas_act);$i++) {
				$sql="INSERT INTO gc_affichages SET projet='$projet', id_aff='$id_aff', id_req='$id_req', type='id_clas_act', valeur='$id_clas_act[$i]';";
				$insert=mysql_query($sql);
			}
		}
		if(count($clas_fut)>0) {
			for($i=0;$i<count($clas_fut);$i++) {
				$sql="INSERT INTO gc_affichages SET projet='$projet', id_aff='$id_aff', id_req='$id_req', type='clas_fut', valeur='$clas_fut[$i]';";
				$insert=mysql_query($sql);
			}
		}

		if(count($avec_lv1)>0) {
			for($i=0;$i<count($avec_lv1);$i++) {
				$sql="INSERT INTO gc_affichages SET projet='$projet', id_aff='$id_aff', id_req='$id_req', type='avec_lv1', valeur='$avec_lv1[$i]';";
				$insert=mysql_query($sql);
			}
		}
		if(count($avec_lv2)>0) {
			for($i=0;$i<count($avec_lv2);$i++) {
				$sql="INSERT INTO gc_affichages SET projet='$projet', id_aff='$id_aff', id_req='$id_req', type='avec_lv2', valeur='$avec_lv2[$i]';";
				$insert=mysql_query($sql);
			}
		}
		if(count($avec_lv3)>0) {
			for($i=0;$i<count($avec_lv3);$i++) {
				$sql="INSERT INTO gc_affichages SET projet='$projet', id_aff='$id_aff', id_req='$id_req', type='avec_lv3', valeur='$avec_lv3[$i]';";
				$insert=mysql_query($sql);
			}
		}
		if(count($avec_autre)>0) {
			for($i=0;$i<count($avec_autre);$i++) {
				$sql="INSERT INTO gc_affichages SET projet='$projet', id_aff='$id_aff', id_req='$id_req', type='avec_autre', valeur='$avec_autre[$i]';";
				$insert=mysql_query($sql);
			}
		}
		if(count($sans_lv1)>0) {
			for($i=0;$i<count($sans_lv1);$i++) {
				$sql="INSERT INTO gc_affichages SET projet='$projet', id_aff='$id_aff', id_req='$id_req', type='sans_lv1', valeur='$sans_lv1[$i]';";
				$insert=mysql_query($sql);
			}
		}
		if(count($sans_lv2)>0) {
			for($i=0;$i<count($sans_lv2);$i++) {
				$sql="INSERT INTO gc_affichages SET projet='$projet', id_aff='$id_aff', id_req='$id_req', type='sans_lv2', valeur='$sans_lv2[$i]';";
				$insert=mysql_query($sql);
			}
		}
		if(count($sans_lv3)>0) {
			for($i=0;$i<count($sans_lv3);$i++) {
				$sql="INSERT INTO gc_affichages SET projet='$projet', id_aff='$id_aff', id_req='$id_req', type='sans_lv3', valeur='$sans_lv3[$i]';";
				$insert=mysql_query($sql);
			}
		}
		if(count($sans_autre)>0) {
			for($i=0;$i<count($sans_autre);$i++) {
				$sql="INSERT INTO gc_affichages SET projet='$projet', id_aff='$id_aff', id_req='$id_req', type='sans_autre', valeur='$sans_autre[$i]';";
				$insert=mysql_query($sql);
			}
		}

	}


	//========================================================================
	// R�cup�ration de la liste des classes et options pour le projet en cours
	$sql="SELECT DISTINCT id_classe, classe FROM gc_divisions WHERE projet='$projet' AND statut='actuelle' ORDER BY classe;";
	$res_clas_act=mysql_query($sql);
	$nb_clas_act=mysql_num_rows($res_clas_act);
	if($nb_clas_act==0) {
		echo "<p>Aucune classe actuelle n'est encore choisie pour ce projet.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	
	$sql="SELECT DISTINCT classe FROM gc_divisions WHERE projet='$projet' AND statut='future' ORDER BY classe;";
	$res_clas_fut=mysql_query($sql);
	$nb_clas_fut=mysql_num_rows($res_clas_fut);
	if($nb_clas_fut==0) {
		echo "<p>Aucune classe future n'est encore d�finie pour ce projet.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='lv1' ORDER BY opt;";
	$res_lv1=mysql_query($sql);
	$nb_lv1=mysql_num_rows($res_lv1);
	
	$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='lv2' ORDER BY opt;";
	$res_lv2=mysql_query($sql);
	$nb_lv2=mysql_num_rows($res_lv2);
	
	$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='lv3' ORDER BY opt;";
	$res_lv3=mysql_query($sql);
	$nb_lv3=mysql_num_rows($res_lv3);
	
	$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='autre' ORDER BY opt;";
	$res_autre=mysql_query($sql);
	$nb_autre=mysql_num_rows($res_autre);
	//========================================================================


	//=========================================================
	// Liste des affichages pr�c�demment programm�s pour ce projet:
	$sql="SELECT DISTINCT id_aff,projet FROM gc_affichages WHERE projet='$projet' ORDER BY id_aff;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		echo "<div id='div_autres_aff' style='width:10em; float: right; text-align:center;'>\n";
		//echo "Autres affichages&nbsp;: ";
		echo "<p>Liste des affichages d�finis&nbsp;: ";
		$cpt=0;
		while($lig_tmp=mysql_fetch_object($res)) {
			if($cpt>0) {echo ", ";}
			echo "<a href='?projet=$lig_tmp->projet&amp;id_aff=$lig_tmp->id_aff'>$lig_tmp->id_aff</a>\n";
			$cpt++;
		}
		echo "</p>\n";

		if(isset($id_aff)) {
			echo "<hr />\n";
			echo "<div id='div_affich_listes' style='text-align:center;'>\n";
			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
			echo "<input type='hidden' name='projet' value='$projet' />\n";
			echo "<input type='hidden' name='id_aff' value='$id_aff' />\n";
			echo "<p align='center'><b>Affichage n�$id_aff&nbsp;:</b> <input type='submit' name='afficher_listes' value='Afficher les listes' /></p>\n";
			echo "</form>\n";
			echo "</div>\n";
		}

		echo "</div>\n";
	}
	//=========================================================

	if(isset($id_aff)) {
		echo "<p class='bold'>Liste de requ�tes pour l'affichage n�$id_aff</p>";
	}

	//================================
	// Formulaire d'ajout de requ�tes:
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

	if(isset($id_aff)) {
		echo "<input type='hidden' name='id_aff' value='$id_aff' />\n";
	}
	if(isset($id_req)) {
		echo "<input type='hidden' name='id_req' value='$id_req' />\n";
	}

	echo "<table class='boireaus' border='1' summary='Choix des param�tres'>\n";
	echo "<tr>\n";
	echo "<th>Classe actuelle</th>\n";
	echo "<th>Classe future</th>\n";
	if($nb_lv1>0) {echo "<th>LV1</th>\n";}
	if($nb_lv2>0) {echo "<th>LV2</th>\n";}
	if($nb_lv3>0) {echo "<th>LV3</th>\n";}
	if($nb_autre>0) {echo "<th>Autre option</th>\n";}
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td style='vertical-align:top; padding:2px;' class='lig-1'>\n";
	$cpt=0;
	while($lig=mysql_fetch_object($res_clas_act)) {
		echo "<input type='checkbox' name='id_clas_act[]' id='id_clas_act_$cpt' value='$lig->id_classe' /><label for='id_clas_act_$cpt'>$lig->classe</label><br />\n";
		$cpt++;
	}
	echo "<input type='checkbox' name='id_clas_act[]' id='id_clas_act_$cpt' value='Red' /><label for='id_clas_act_$cpt'>Redoublants</label><br />\n";
	$cpt++;
	echo "<input type='checkbox' name='id_clas_act[]' id='id_clas_act_$cpt' value='Arr' /><label for='id_clas_act_$cpt'>Arrivants</label><br />\n";
	$cpt++;
	echo "</td>\n";

	echo "<td style='vertical-align:top; padding:2px;' class='lig-1'>\n";
	$cpt=0;
	while($lig=mysql_fetch_object($res_clas_fut)) {
		//echo "<input type='checkbox' name='clas_fut[]' id='clas_fut_$cpt' value='$lig->classe' /><label for='clas_fut_$cpt'>$lig->classe</label><br />\n";

		//$sql="SELECT 1=1 FROM gc_eleve_fut_classe WHERE projet='$projet' AND classe='$lig->classe';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND classe_future='$lig->classe';";
		$res_test=mysql_query($sql);
		if(mysql_num_rows($res_test)>0) {
			echo "<input type='checkbox' name='clas_fut[]' id='clas_fut_$cpt' value='$lig->classe' /><label for='clas_fut_$cpt'>$lig->classe</label><br />\n";
		}
		else {
			echo "_ $lig->classe<br />\n";
		}

		$cpt++;
	}
	echo "<input type='checkbox' name='clas_fut[]' id='clas_fut_$cpt' value='' /><label for='clas_fut_$cpt'>Non encore affect�</label><br />\n";
	$cpt++;
	/*
	echo "<input type='checkbox' name='clas_fut[]' id='clas_fut_$cpt' value='Red' /><label for='clas_fut_$cpt'>Red</label><br />\n";
	$cpt++;
	echo "<input type='checkbox' name='clas_fut[]' id='clas_fut_$cpt' value='Dep' /><label for='clas_fut_$cpt'>Dep</label><br />\n";
	$cpt++;
	*/
	echo "</td>\n";

	if($nb_lv1>0) {
		echo "<td style='vertical-align:top; padding:2px;' class='lig1'>\n";
			echo "<table class='boireaus' border='1' summary='LV1'>\n";
			echo "<tr>\n";
			echo "<th>Avec</th>\n";
			echo "<th>Sans</th>\n";
			echo "<th>LV</th>\n";
			echo "</tr>\n";
			while($lig=mysql_fetch_object($res_lv1)) {
				echo "<tr>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='avec_lv1[]' value='$lig->opt' />\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='sans_lv1[]' value='$lig->opt' />\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "$lig->opt\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		echo "</td>\n";
	}

	if($nb_lv2>0) {
		echo "<td style='vertical-align:top; padding:2px;' class='lig1'>\n";
			echo "<table class='boireaus' border='2' summary='LV2'>\n";
			echo "<tr>\n";
			echo "<th>Avec</th>\n";
			echo "<th>Sans</th>\n";
			echo "<th>LV</th>\n";
			echo "</tr>\n";
			while($lig=mysql_fetch_object($res_lv2)) {
				echo "<tr>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='avec_lv2[]' value='$lig->opt' />\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='sans_lv2[]' value='$lig->opt' />\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "$lig->opt\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		echo "</td>\n";
	}

	if($nb_lv3>0) {
		echo "<td style='vertical-align:top; padding:2px;' class='lig1'>\n";
			echo "<table class='boireaus' border='3' summary='LV3'>\n";
			echo "<tr>\n";
			echo "<th>Avec</th>\n";
			echo "<th>Sans</th>\n";
			echo "<th>LV</th>\n";
			echo "</tr>\n";
			while($lig=mysql_fetch_object($res_lv3)) {
				echo "<tr>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='avec_lv3[]' value='$lig->opt' />\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='sans_lv3[]' value='$lig->opt' />\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "$lig->opt\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		echo "</td>\n";
	}

	if($nb_autre>0) {
		echo "<td style='vertical-align:top; padding:2px;' class='lig1'>\n";
			echo "<table class='boireaus' border='1' summary='Option'>\n";
			echo "<tr>\n";
			echo "<th>Avec</th>\n";
			echo "<th>Sans</th>\n";
			echo "<th>Option</th>\n";
			echo "</tr>\n";
			while($lig=mysql_fetch_object($res_autre)) {
				echo "<tr>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='avec_autre[]' value='$lig->opt' />\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='sans_autre[]' value='$lig->opt' />\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "$lig->opt\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		echo "</td>\n";
	}

	// Pouvoir faire une recherche par niveau aussi?


	echo "</tr>\n";
	echo "</table>\n";

	echo "<input type='hidden' name='projet' value='$projet' />\n";
	//echo "<input type='hidden' name='is_posted' value='y' />\n";
	echo "<p align='center'><input type='submit' name='ajouter' value='Ajouter' /></p>\n";
	//================================

	//echo "<input type='checkbox' name='afficher_listes' value='y' /> Finaliser et afficher les listes\n";


	//================================
	// Suite du formulaire avec la liste des requ�tes d�j� effectu�es:
	if(isset($id_aff)) {

		$sql="SELECT DISTINCT id_req FROM gc_affichages WHERE projet='$projet'AND id_aff='$id_aff' ORDER BY id_req;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while($lig=mysql_fetch_object($res)) {
				//$txt_requete="<div style='display:inline; width:20em; margin: 2px; border: 1px solid black;'>\n";
				$txt_requete="";
				$txt_requete.="<table summary='Requ�te n�$lig->id_req'>\n";
				$txt_requete.="<tr>\n";
				$txt_requete.="<td valign='top'>\n";
				$txt_requete.="<input type='checkbox' name='suppr[]' id='suppr_$lig->id_req' value='$lig->id_req' /> ";
				$txt_requete.="</td>\n";
				$txt_requete.="<td>\n";
				$txt_requete.="<b><label for='suppr_$lig->id_req'>Requ�te n�$lig->id_req</label></b>";

				//===========================================
				$id_req=$lig->id_req;

				$sql_ele="SELECT DISTINCT login FROM gc_eleves_options WHERE projet='$projet' AND classe_future!='Dep' AND classe_future!='Red'";
				$sql_ele_id_classe_act="";
				$sql_ele_classe_fut="";

				$sql="SELECT * FROM gc_affichages WHERE projet='$projet' AND id_aff='$id_aff' AND id_req='$id_req' ORDER BY type;";
				$res_tmp=mysql_query($sql);
				while($lig_tmp=mysql_fetch_object($res_tmp)) {
					switch($lig_tmp->type) {
						case 'id_clas_act':
							if($sql_ele_id_classe_act!='') {$sql_ele_id_classe_act.=" OR ";}
							$sql_ele_id_classe_act.="id_classe_actuelle='$lig_tmp->valeur'";
							break;
		
						case 'clas_fut':
							if($sql_ele_classe_fut!='') {$sql_ele_classe_fut.=" OR ";}
							$sql_ele_classe_fut.="classe_future='$lig_tmp->valeur'";
							break;
		
						case 'avec_lv1':
							$sql_ele.=" AND liste_opt LIKE '%|$lig_tmp->valeur|%'";
							break;
						case 'avec_lv2':
							$sql_ele.=" AND liste_opt LIKE '%|$lig_tmp->valeur|%'";
							break;
						case 'avec_lv3':
							$sql_ele.=" AND liste_opt LIKE '%|$lig_tmp->valeur|%'";
							break;
		
						case 'avec_autre':
							$sql_ele.=" AND liste_opt LIKE '%|$lig_tmp->valeur|%'";
							break;
		
						case 'sans_lv1':
							$sql_ele.=" AND liste_opt NOT LIKE '%|$lig_tmp->valeur|%'";
							break;
						case 'sans_lv2':
							$sql_ele.=" AND liste_opt NOT LIKE '%|$lig_tmp->valeur|%'";
							break;
						case 'sans_lv3':
							$sql_ele.=" AND liste_opt NOT LIKE '%|$lig_tmp->valeur|%'";
							break;
						case 'sans_autre':
							$sql_ele.=" AND liste_opt NOT LIKE '%|$lig_tmp->valeur|%'";
							break;
					}
				}
		
				//$tab_ele=array();
		
				if($sql_ele_id_classe_act!='') {$sql_ele.=" AND ($sql_ele_id_classe_act)";}
				if($sql_ele_classe_fut!='') {$sql_ele.=" AND ($sql_ele_classe_fut)";}
		
				$sql_ele.=";";
				//echo "$sql_ele<br />\n";
				$res_ele=mysql_query($sql_ele);

				$txt_requete.=" <span style='font-size:small;font-style:italic;'>(".mysql_num_rows($res_ele).")</span><br />";

				//===========================================


				$sql="SELECT * FROM gc_affichages WHERE projet='$projet'AND id_aff='$id_aff' AND type='id_clas_act' AND id_req='$lig->id_req';";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					$txt_requete.="Classe actuelle (";
					$cpt=0;
					while($lig2=mysql_fetch_object($res2)) {
						if($cpt>0) {$txt_requete.=", ";}
						if(($lig2->valeur=='Red')||($lig2->valeur=='Arriv')) {
							$txt_requete.=$lig2->valeur;
						}
						else {
							$txt_requete.=get_class_from_id($lig2->valeur);
						}
						$cpt++;
					}
					$txt_requete.=")<br />";
				}

				$sql="SELECT * FROM gc_affichages WHERE projet='$projet'AND id_aff='$id_aff' AND type='clas_fut' AND id_req='$lig->id_req';";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					$txt_requete.="Classe future (";
					$cpt=0;
					while($lig2=mysql_fetch_object($res2)) {
						if($cpt>0) {$txt_requete.=", ";}
						$txt_requete.=$lig2->valeur;
						$cpt++;
					}
					$txt_requete.=")<br />";
				}

				$sql="SELECT * FROM gc_affichages WHERE projet='$projet'AND id_aff='$id_aff' AND type LIKE 'avec_%' AND id_req='$lig->id_req';";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					$txt_requete.="Avec les options (<span style='color:green;'>";
					$cpt=0;
					while($lig2=mysql_fetch_object($res2)) {
						if($cpt>0) {$txt_requete.=", ";}
						$txt_requete.=$lig2->valeur;
						$cpt++;
					}
					$txt_requete.="</span>)<br />";
				}

				$sql="SELECT * FROM gc_affichages WHERE projet='$projet'AND id_aff='$id_aff' AND type LIKE 'sans_%' AND id_req='$lig->id_req';";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					$txt_requete.="Sans les options (<span style='color:red;'>";
					$cpt=0;
					while($lig2=mysql_fetch_object($res2)) {
						if($cpt>0) {$txt_requete.=", ";}
						$txt_requete.=$lig2->valeur;
						$cpt++;
					}
					$txt_requete.="</span>)<br />";
				}

				$txt_requete.="</td>\n";
				$txt_requete.="</tr>\n";
				$txt_requete.="</table>\n";
				//$txt_requete.="</div>\n";

				echo $txt_requete;
			}

			// Ajouter un bouton pour supprimer
			echo "<p>Cochez les requ�tes � supprimer.</p>\n";

		}
	}

	echo "</form>\n";
	//================================

	if(!isset($txt_requete)) {
		echo "<script type='text/javascript'>
	if(document.getElementById('div_affich_listes')) {document.getElementById('div_affich_listes').style.display='none';}
</script>\n";
	}

	echo "<p><i>NOTES&nbsp;:</i></p>\n";
	echo "<ul>\n";
	echo "<li>Les colonnes Classe actuelle et Classe future sont trait�es suivant le mode OU<br />
	Si vous cochez deux classes, les �l�ves pris en compte seront '<i>membre de Classe 1 OU membre de Classe 2</i>'</li>\n";
	echo "<li>Les colonnes d'options sont trait�es suivant le mode ET.<br />
	Ce sera par exemple '<i>Avec AGL1 ET Avec ESP2 ET Avec LATIN ET Sans DECP3</i>'</li>\n";
	//echo "<li></li>\n";
	echo "</ul>\n";

}
else {
	//=============================================================================================
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//=============================================================================================
	// Les requ�tes sont choisies, on va proc�der � l'affichage des �l�ves correspondants
	// Affichage des listes pour $projet et $id_aff

	echo " | <a href='".$_SERVER['PHP_SELF']."?projet=$projet'>Autre s�lection</a>";

	$id_aff=isset($_POST['id_aff']) ? $_POST['id_aff'] : (isset($_GET['id_aff']) ? $_GET['id_aff'] : NULL);
	if((my_ereg_replace("[0-9]","",$id_aff)!="")||($id_aff=="")) {unset($id_aff);}
	if(!isset($id_aff)) {
		echo "<p>ERREUR: La variable 'id_aff' n'est pas affect�e.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo " | <a href='".$_SERVER['PHP_SELF']."?projet=$projet&amp;id_aff=$id_aff'>Modifier la liste de requ�tes pour l'affichage n�$id_aff</a>";
	echo "</p>\n";

	echo "<h2>Projet $projet</h2>\n";


	//=========================================================
	// Liste des affichages pr�c�demment programm�s pour ce projet:
	$sql="SELECT DISTINCT id_aff,projet FROM gc_affichages WHERE projet='$projet' ORDER BY id_aff;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		echo "<div id='div_autres_aff' style='width:10em; float: right; text-align:center;'>\n";
		//echo "Autres affichages&nbsp;: ";
		echo "Liste des affichages&nbsp;: ";
		$cpt=0;
		while($lig_tmp=mysql_fetch_object($res)) {
			if($cpt>0) {echo ", ";}
			echo "<a href='?projet=$lig_tmp->projet&amp;id_aff=$lig_tmp->id_aff&amp;afficher_listes=y'>$lig_tmp->id_aff</a>\n";
			$cpt++;
		}
		echo "</p>\n";

		echo "<hr />\n";

		echo "<div id='div_ods' style='text-align:center; border:1px solid black;'>\n";
		echo "</div>\n";
	
		echo "<hr />\n";

		echo "<div id='div_divers' style='text-align:center;'>\n";
		echo "<a href='#' onclick=\"afficher_div('recap_eff','y',-100,20);return false;\">Effectifs des requ�tes</a>";
		echo "</div>\n";

		echo "</div>\n";
	}
	//=========================================================


	// Affichage...
	//Construire la requ�te SQL et l'afficher

	$eff_lv1=-1;
	$eff_lv2=-1;
	$eff_lv3=-1;
	$eff_autre=-1;

	/*
	function echo_debug($texte) {
		$debug=0;
		if($debug==1) {
			echo $texte;
		}
	}
	*/

	//$id_aff=isset($_POST['id_aff']) ? $_POST['id_aff'] : NULL;
	$id_aff=isset($_POST['id_aff']) ? $_POST['id_aff'] : (isset($_GET['id_aff']) ? $_GET['id_aff'] : NULL);
	if((my_ereg_replace("[0-9]","",$id_aff)!="")||($id_aff=="")) {unset($id_aff);}
	if(!isset($id_aff)) {
		echo "<p>ERREUR: La variable 'id_aff' n'est pas affect�e.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		unset($tab_id_req);
		$tab_id_req=array();
		$sql="SELECT DISTINCT id_req FROM gc_affichages WHERE projet='$projet' AND id_aff='$id_aff' ORDER BY id_req;";
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			$tab_id_req[]=$lig->id_req;
		}
	}









//============================================================
//============================================================
//============================================================
	//On n'effectue qu'une fois ces requ�tes communes hors de la boucle sur la liste des requ�tes associ�es � l'affichage choisi

	$classe_fut=array();
	$sql="SELECT DISTINCT classe FROM gc_divisions WHERE projet='$projet' AND statut='future' ORDER BY classe;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Aucune classe future n'est encore d�finie pour ce projet.</p>\n";
		// Est-ce que cela doit vraiment bloquer la saisie des options?
		require("../lib/footer.inc.php");
		die();
	}
	else {
		$tab_opt_exclue=array();

		$chaine_classes_fut="tab_classes_fut=new Array(";
		$cpt_tmp=0;
		while($lig=mysql_fetch_object($res)) {
			$classe_fut[]=$lig->classe;
			if($cpt_tmp>0) {$chaine_classes_fut.=",";}
			$chaine_classes_fut.="'".$lig->classe."'";

			$tab_opt_exclue["$lig->classe"]=array();
			//=========================
			// Options exlues pour la classe
			$sql="SELECT opt_exclue FROM gc_options_classes WHERE projet='$projet' AND classe_future='$lig->classe';";
			$res_opt_exclues=mysql_query($sql);
			while($lig_opt_exclue=mysql_fetch_object($res_opt_exclues)) {
				$tab_opt_exclue["$lig->classe"][]=$lig_opt_exclue->opt_exclue;
			}
			//=========================

			$cpt_tmp++;
		}
		$classe_fut[]="Red";
		$classe_fut[]="Dep";
		$classe_fut[]=""; // Vide pour les Non Affect�s

		$chaine_classes_fut.=",'Red','Dep','')";
	}
	
	$id_classe_actuelle=array();
	$classe_actuelle=array();
	$sql="SELECT DISTINCT id_classe,classe FROM gc_divisions WHERE projet='$projet' AND statut='actuelle' ORDER BY classe;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Aucune classe actuelle n'est encore s�lectionn�e pour ce projet.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		while($lig=mysql_fetch_object($res)) {
			$id_classe_actuelle[]=$lig->id_classe;
			$classe_actuelle[]=$lig->classe;
		}

		// On ajoute redoublants et arrivants
		$id_classe_actuelle[]='Red';
		$classe_actuelle[]='Red';
	
		$id_classe_actuelle[]='Arriv';
		$classe_actuelle[]='Arriv';
	}

	$chaine_lv1="tab_lv1=new Array(";
	$lv1=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv1' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$cpt_tmp=0;
		while($lig=mysql_fetch_object($res)) {
			$lv1[]=$lig->opt;
			if($cpt_tmp>0) {$chaine_lv1.=",";}
			$chaine_lv1.="'".$lig->opt."'";
			$cpt_tmp++;
		}
	}
	$chaine_lv1.=")";


	$chaine_lv2="tab_lv2=new Array(";
	$lv2=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv2' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$cpt_tmp=0;
		while($lig=mysql_fetch_object($res)) {
			$lv2[]=$lig->opt;
			if($cpt_tmp>0) {$chaine_lv2.=",";}
			$chaine_lv2.="'".$lig->opt."'";
			$cpt_tmp++;
		}
	}
	$chaine_lv2.=")";
	
	$chaine_lv3="tab_lv3=new Array(";
	$lv3=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv3' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$cpt_tmp=0;
		while($lig=mysql_fetch_object($res)) {
			$lv3[]=$lig->opt;
			if($cpt_tmp>0) {$chaine_lv3.=",";}
			$chaine_lv3.="'".$lig->opt."'";
			$cpt_tmp++;
		}
	}
	$chaine_lv3.=")";
	
	$autre_opt=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='autre' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$autre_opt[]=$lig->opt;
		}
	}

	echo "<script type='text/javascript'>
	// Tableau de listes pour assurer la colorisation des lignes
	var $chaine_classes_fut;
	var $chaine_lv1;
	var $chaine_lv2;
	var $chaine_lv3;
</script>\n";

	//=============================
	include("lib_gc.php");
	// On y initialise les couleurs
	// Il faut que le tableaux $classe_fut soit initialis�.
	//=============================
//============================================================
//============================================================
//============================================================

	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

	// Colorisation
	echo "<p>Colorisation&nbsp;: ";
	echo "<select name='colorisation' onchange='lance_colorisation()'>
	<option value='classe_fut' selected>Classe future</option>
	<option value='lv1'>LV1</option>
	<option value='lv2'>LV2</option>
	<option value='profil'>Profil</option>
	</select>\n";
	echo "</p>\n";

//============================================================
//============================================================
//============================================================

	$tab_ele_toutes_requetes=array();

	$fich_csv="";

	$cpt=0;
	for($loop=0;$loop<count($tab_id_req);$loop++) {

		$id_req=$tab_id_req[$loop];

		$id_clas_act=array();
		$clas_fut=array();
		$avec_lv1=array();
		$avec_lv2=array();
		$avec_lv3=array();
		$avec_autre=array();
		$sans_lv1=array();
		$sans_lv2=array();
		$sans_lv3=array();
		$sans_autre=array();

		//echo "<p><b>Requ�te n�$id_req</b><br />";
		$fich_csv.="Requ�te n�$id_req\n";

		$lien_affect="<p><a name='requete_$id_req'></a><a href='affect_eleves_classes.php?projet=$projet&amp;choix_affich=1";
		$fin_lien_affect="' target='_blank'><b>Requ�te n�$id_req</b></a><br />";

		$tab_requete=array();
		$tab_requete_csv=array();

		//=========================
		// D�but de la requ�te � forger pour ne retenir que les �l�ves souhait�s
		$sql_ele="SELECT DISTINCT login FROM gc_eleves_options WHERE projet='$projet' AND classe_future!='Dep' AND classe_future!='Red'";

		$sql_ele_id_classe_act="";
		$sql_ele_classe_fut="";
		//=========================

		$sql="SELECT * FROM gc_affichages WHERE projet='$projet' AND id_aff='$id_aff' AND id_req='$id_req' ORDER BY type;";
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			switch($lig->type) {
				case 'id_clas_act':
					$id_clas_act[]=$lig->valeur;
					if(!isset($tab_requete[0])) {$tab_requete[0]="Classe actuelle (<span style='color:black;'>";$tab_requete_csv[0]="Classe actuelle (";} else {$tab_requete[0].=", ";$tab_requete_csv[0].=", ";}
					if(($lig->valeur=='Red')||($lig->valeur=='Arriv')) {
						$tab_requete[0].=$lig->valeur;$tab_requete_csv[0].=$lig->valeur;
					}
					else {
						$tab_requete[0].=get_class_from_id($lig->valeur);$tab_requete_csv[0].=get_class_from_id($lig->valeur);
					}

					$lien_affect.="&amp;id_clas_act[]=$lig->valeur";

					//$sql_ele.=" AND id_classe_actuelle='$lig->valeur'";

					if($sql_ele_id_classe_act!='') {$sql_ele_id_classe_act.=" OR ";}
					$sql_ele_id_classe_act.="id_classe_actuelle='$lig->valeur'";
					break;

				case 'clas_fut':
					$clas_fut[]=$lig->valeur;
					if(!isset($tab_requete[1])) {$tab_requete[1]="Classe future (<span style='color:black;'>";$tab_requete_csv[1]="Classe future (";} else {$tab_requete[1].=", ";$tab_requete_csv[1].=", ";}
					if(($lig->valeur=='Red')||($lig->valeur=='Dep')) {
						$tab_requete[1].=$lig->valeur;$tab_requete_csv[1].=$lig->valeur;
					}
					else {
						//$tab_requete[1].=get_class_from_id($lig->valeur);$tab_requete_csv[1].=get_class_from_id($lig->valeur);
						$tab_requete[1].=$lig->valeur;$tab_requete_csv[1].=$lig->valeur;
					}

					$lien_affect.="&amp;clas_fut[]=$lig->valeur";

					//$sql_ele.=" AND classe_future='$lig->valeur'";

					if($sql_ele_classe_fut!='') {$sql_ele_classe_fut.=" OR ";}
					$sql_ele_classe_fut.="classe_future='$lig->valeur'";
					break;

				case 'avec_lv1':
					$avec_lv1[]=$lig->valeur;
					if(!isset($tab_requete[2])) {$tab_requete[2]="Avec les options (<span style='color:green;'>";$tab_requete_csv[2]="Avec les options (";} else {$tab_requete[2].=", ";$tab_requete_csv[2].=", ";}
					$tab_requete[2].=$lig->valeur;$tab_requete_csv[2].=$lig->valeur;

					$lien_affect.="&amp;avec_lv1[]=$lig->valeur";

					$sql_ele.=" AND liste_opt LIKE '%|$lig->valeur|%'";
					break;
				case 'avec_lv2':
					$avec_lv2[]=$lig->valeur;
					if(!isset($tab_requete[2])) {$tab_requete[2]="Avec les options (<span style='color:green;'>";$tab_requete_csv[2]="Avec les options (";} else {$tab_requete[2].=", ";$tab_requete_csv[2].=", ";}
					$tab_requete[2].=$lig->valeur;$tab_requete_csv[2].=$lig->valeur;

					$lien_affect.="&amp;avec_lv2[]=$lig->valeur";

					$sql_ele.=" AND liste_opt LIKE '%|$lig->valeur|%'";
					break;
				case 'avec_lv3':
					$avec_lv3[]=$lig->valeur;
					if(!isset($tab_requete[2])) {$tab_requete[2]="Avec les options (<span style='color:green;'>";$tab_requete_csv[2]="Avec les options (";} else {$tab_requete[2].=", ";$tab_requete_csv[2].=", ";}
					$tab_requete[2].=$lig->valeur;$tab_requete_csv[2].=$lig->valeur;

					$lien_affect.="&amp;avec_lv3[]=$lig->valeur";

					$sql_ele.=" AND liste_opt LIKE '%|$lig->valeur|%'";
					break;

				case 'avec_autre':
					$avec_autre[]=$lig->valeur;
					if(!isset($tab_requete[2])) {$tab_requete[2]="Avec les options (<span style='color:green;'>";$tab_requete_csv[2]="Avec les options (";} else {$tab_requete[2].=", ";$tab_requete_csv[2].=", ";}
					$tab_requete[2].=$lig->valeur;$tab_requete_csv[2].=$lig->valeur;

					$lien_affect.="&amp;avec_autre[]=$lig->valeur";

					$sql_ele.=" AND liste_opt LIKE '%|$lig->valeur|%'";
					break;

				case 'sans_lv1':
					$sans_lv1[]=$lig->valeur;
					if(!isset($tab_requete[3])) {$tab_requete[3]="Sans les options (<span style='color:red;'>";$tab_requete_csv[3]="Sans les options (";} else {$tab_requete[3].=", ";$tab_requete_csv[3].=", ";}
					$tab_requete[3].=$lig->valeur;$tab_requete_csv[3].=$lig->valeur;

					$lien_affect.="&amp;sans_lv1[]=$lig->valeur";

					$sql_ele.=" AND liste_opt NOT LIKE '%|$lig->valeur|%'";
					break;
				case 'sans_lv2':
					$sans_lv2[]=$lig->valeur;
					if(!isset($tab_requete[3])) {$tab_requete[3]="Sans les options (<span style='color:red;'>";$tab_requete_csv[3]="Sans les options (";} else {$tab_requete[3].=", ";$tab_requete_csv[3].=", ";}
					$tab_requete[3].=$lig->valeur;$tab_requete_csv[3].=$lig->valeur;

					$lien_affect.="&amp;sans_lv2[]=$lig->valeur";

					$sql_ele.=" AND liste_opt NOT LIKE '%|$lig->valeur|%'";
					break;
				case 'sans_lv3':
					$sans_lv3[]=$lig->valeur;
					if(!isset($tab_requete[3])) {$tab_requete[3]="Sans les options (<span style='color:red;'>";$tab_requete_csv[3]="Sans les options (";} else {$tab_requete[3].=", ";$tab_requete_csv[3].=", ";}
					$tab_requete[3].=$lig->valeur;$tab_requete_csv[3].=$lig->valeur;

					$lien_affect.="&amp;sans_lv3[]=$lig->valeur";

					$sql_ele.=" AND liste_opt NOT LIKE '%|$lig->valeur|%'";
					break;
				case 'sans_autre':
					$sans_autre[]=$lig->valeur;
					if(!isset($tab_requete[3])) {$tab_requete[3]="Sans les options (<span style='color:red;'>";$tab_requete_csv[3]="Sans les options (";} else {$tab_requete[3].=", ";$tab_requete_csv[3].=", ";}
					$tab_requete[3].=$lig->valeur;$tab_requete_csv[3].=$lig->valeur;

					$lien_affect.="&amp;sans_autre[]=$lig->valeur";

					$sql_ele.=" AND liste_opt NOT LIKE '%|$lig->valeur|%'";
					break;
			}
		}

		$lien_affect.=$fin_lien_affect;
		echo $lien_affect;
		//"<br />\n";

		if(!isset($lignes_requetes)) {
			$lignes_requetes="<p>";
		}
		else {
			$lignes_requetes.="<p>";
		}
		$lignes_requetes.="<a href='#requete_$id_req'>Requ�te n�$id_req</a> (<i>\n";

		for($m=0;$m<4;$m++) {
			if(isset($tab_requete[$m])) {
				echo $tab_requete[$m]."</span>)<br />\n";
				$fich_csv.=$tab_requete_csv[$m].")\n";

				$lignes_requetes.=$tab_requete[$m]."</span>) ";
			}
		}
		$lignes_requetes.="</i>)&nbsp;: \n";

		// On r�initialise le tableau pour faire table rase des logins de la requ�te pr�c�dente
		$tab_ele=array();

		if($sql_ele_id_classe_act!='') {$sql_ele.=" AND ($sql_ele_id_classe_act)";}
		if($sql_ele_classe_fut!='') {$sql_ele.=" AND ($sql_ele_classe_fut)";}

		$sql_ele.=";";
		//echo "$sql_ele<br />\n";
		$res_ele=mysql_query($sql_ele);
		while ($lig_ele=mysql_fetch_object($res_ele)) {
			$tab_ele[]=$lig_ele->login;
		}
		$lignes_requetes.=count($tab_ele)."</p>\n";

		//=========================================================================
		// $tab_ele est rempli
		// On va parcourir toutes les classes pour pr�senter les �l�ves retenus ($tab_ele) dans l'ordre des classes d'origine

		// D�but du tableau des �l�ves pour la requ�te courante

		echo "<table class='boireaus' border='1' summary='Tableau des options'>\n";
	
		//==========================================
		echo "<tr>\n";

		echo "<th>El�ve</th>\n";

		echo "<th>Profil</th>\n";
		echo "<th>Niveau</th>\n";
		echo "<th>Absences</th>\n";
		echo "<th>Classe<br />actuelle</th>\n";
		//$fich_csv.="El�ve;Classe actuelle;";
		$fich_csv.="El�ve;Clas.act;";

		if(count($lv1)>0) {echo "<th>LV1</th>\n";$fich_csv.="LV1;";}
		if(count($lv2)>0) {echo "<th>LV2</th>\n";$fich_csv.="LV2;";}
		if(count($lv3)>0) {echo "<th>LV3</th>\n";$fich_csv.="LV3;";}
		if(count($autre_opt)>0) {echo "<th>Options</th>\n";$fich_csv.="Options;";}

		echo "<th rowspan='2'>Observations</th>\n";

		echo "</tr>\n";
		$fich_csv.="\n";

		//==========================================

		echo "<tr>\n";
		//echo "<th>Effectifs&nbsp;: <span id='eff_tot'>&nbsp;</span></th>\n";
		echo "<th>Eff.select&nbsp;: <span id='eff_select$loop'>...</span></th>\n";
		echo "<th id='eff_select_sexe$loop'>...</th>\n";

		echo "<th>&nbsp;</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "<th>&nbsp;</th>\n";

		if(count($lv1)>0) {echo "<th>&nbsp;</th>\n";}
		if(count($lv2)>0) {echo "<th>&nbsp;</th>\n";}
		if(count($lv3)>0) {echo "<th>&nbsp;</th>\n";}
		if(count($autre_opt)>0) {echo "<th>&nbsp;</th>\n";}

		echo "</tr>\n";

		//==========================================
		$lignes_tab="";
		//==========================================

		$eff_tot_select=0;
		$eff_tot_select_M=0;
		$eff_tot_select_F=0;


		$chaine_id_classe="";
		//$cpt=0;
		// Boucle sur toutes les classes actuelles
		for($j=0;$j<count($id_classe_actuelle);$j++) {
			//$eff_tot_classe_M=0;
			//$eff_tot_classe_F=0;

			if($chaine_id_classe!="") {$chaine_id_classe.=",";}
			$chaine_id_classe.="'$id_classe_actuelle[$j]'";
	
			//==========================================
			//$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe_actuelle[$j]' ORDER BY e.nom,e.prenom;";
			if(($id_classe_actuelle[$j]!='Red')&&($id_classe_actuelle[$j]!='Arriv')) {
				$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe_actuelle[$j]' ORDER BY e.nom,e.prenom;";
			}
			else {
				$sql="SELECT DISTINCT e.* FROM eleves e, gc_ele_arriv_red gc WHERE gc.login=e.login AND gc.statut='$id_classe_actuelle[$j]' AND gc.projet='$projet' ORDER BY e.nom,e.prenom;";
			}
			//echo "<tr><td colspan='5'>$sql</tr></tr>\n";
			$res=mysql_query($sql);
			//$eff_tot_classe=mysql_num_rows($res);
			//$eff_tot+=$eff_tot_classe;
			//==========================================
		
			if(mysql_num_rows($res)>0) {
				while($lig=mysql_fetch_object($res)) {

					if(in_array($lig->login,$tab_ele)) {
						$tab_ele_toutes_requetes[]=$lig->login;

						$eff_tot_select++;
						if(strtoupper($lig->sexe)=='F') {$eff_tot_select_F++;} else {$eff_tot_select_M++;}

						//$num_eleve2_id_classe_actuelle[$j]=$cpt;

						echo "<tr id='tr_eleve_$cpt' class='white_hover'>\n";
						echo "<td>\n";
						echo "<a name='eleve$cpt'></a>\n";
						if(file_exists("../photos/eleves/".$lig->elenoet.".jpg")) {
							echo "<a href='#eleve$cpt' onmouseover=\"affiche_photo('".$lig->elenoet.".jpg','".addslashes(strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom)))."');afficher_div('div_photo','y',100,100);\" onmouseout=\"cacher_div('div_photo')\" onclick=\"return false;\">";

							echo strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom));
							echo "</a>\n";
						}
						else {
							echo strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom));
						}
						echo "<input type='hidden' name='eleve[$cpt]' value='$lig->login' />\n";
						echo "</td>\n";
						$lignes_tab.=strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom)).";";

						//===================================
						// Initialisations
						$profil='RAS';
						$moy="-";
						$nb_absences="-";
						$non_justifie="-";
						$nb_retards="-";

						// On r�cup�re les classe future, lv1, lv2, lv3 et autres options de l'�l�ve $lig->login
						$fut_classe="";

						$tab_ele_opt=array();
						$sql="SELECT * FROM gc_eleves_options WHERE projet='$projet' AND login='$lig->login';";
						$res_opt=mysql_query($sql);
						if(mysql_num_rows($res_opt)>0) {
							$lig_opt=mysql_fetch_object($res_opt);
	
							$fut_classe=$lig_opt->classe_future;
	
							$profil=$lig_opt->profil;
							$moy=$lig_opt->moy;
							$nb_absences=$lig_opt->nb_absences;
							$non_justifie=$lig_opt->non_justifie;
							$nb_retards=$lig_opt->nb_retards;
			
							$tmp_tab=explode("|",$lig_opt->liste_opt);
							for($n=0;$n<count($tmp_tab);$n++) {
								if($tmp_tab[$n]!="") {
									$tab_ele_opt[]=strtoupper($tmp_tab[$n]);
								}
							}
						}
						else {
							// On r�cup�re les options de l'ann�e �coul�e (ann�e qui se termine)
							$sql="SELECT * FROM j_eleves_groupes jeg, j_groupes_matieres jgm WHERE jeg.id_groupe=jgm.id_groupe AND jeg.login='$lig->login';";
							$res_opt=mysql_query($sql);
							if(mysql_num_rows($res_opt)>0) {
								while($lig_opt=mysql_fetch_object($res_opt)) {
									$tab_ele_opt[]=strtoupper($lig_opt->id_matiere);
								}
							}
						}
						//===================================
						// Profil...
						echo "<td>\n";
						for($m=0;$m<count($tab_profil);$m++) {if($profil==$tab_profil[$m]) {echo "<span style='color:".$tab_couleur_profil[$m].";'>";break;}}
						echo $profil;
						echo "</span>\n";
						echo "<input type='hidden' name='profil[$cpt]' id='profil_$cpt' value='$profil' />\n";
						echo "</td>\n";

						// Niveau...
						echo "<td>\n";
						if(($moy!="")&&(strlen(my_ereg_replace("[0-9.,]","",$moy))==0)) {
							if($moy<7) {
								echo "<span style='color:red;'>";
							}
							elseif($moy<9) {
								echo "<span style='color:orange;'>";
							}
							elseif($moy<12) {
								echo "<span style='color:gray;'>";
							}
							elseif($moy<15) {
								echo "<span style='color:green;'>";
							}
							else {
								echo "<span style='color:blue;'>";
							}
							echo "$moy\n";
							echo "</span>";
							echo "<input type='hidden' name='moy[$cpt]' id='moy_$cpt' value='$moy' />\n";
						}
						else {
							echo "-\n";
							echo "<input type='hidden' name='moy[$cpt]' id='moy_$cpt' value='-' />\n";
						}
						echo "</td>\n";

						//===================================
						echo "<td>\n";
						echo colorise_abs($nb_absences,$non_justifie,$nb_retards);
						echo "<input type='hidden' name='nb_absences[$cpt]' id='nb_absences_$cpt' value='$nb_absences' />\n";
						echo "<input type='hidden' name='non_justifie[$cpt]' id='non_justifie_$cpt' value='$non_justifie' />\n";
						echo "<input type='hidden' name='nb_retards[$cpt]' id='nb_retards_$cpt' value='$nb_retards' />\n";
						echo "</td>\n";

						//===================================
						echo "<td>\n";
						//echo "<input type='hidden' name='classe_fut[$cpt]' id='classe_fut_".$i."_".$cpt."' value='$fut_classe' />\n";
						echo "<input type='hidden' name='ele_classe_fut[$cpt]' id='classe_fut_".$cpt."' value='$fut_classe' />\n";

						echo $classe_actuelle[$j];
						echo "</td>\n";
						$lignes_tab.=$classe_actuelle[$j].";";

						if(count($lv1)>0) {
							echo "<td>\n";
							for($i=0;$i<count($lv1);$i++) {
								if(in_array(strtoupper($lv1[$i]),$tab_ele_opt)) {
									echo $lv1[$i];

									echo "<input type='hidden' name='ele_lv1[$cpt]' id='lv1_".$cpt."' value='$lv1[$i]' />\n";

									$lignes_tab.=$lv1[$i].";";

								}
							}
							echo "</td>\n";
						}
			

						if(count($lv2)>0) {
							echo "<td>\n";
							for($i=0;$i<count($lv2);$i++) {
								if(in_array(strtoupper($lv2[$i]),$tab_ele_opt)) {
									echo $lv2[$i];
									echo "<input type='hidden' name='ele_lv2[$cpt]' id='lv2_".$cpt."' value='$lv2[$i]' />\n";

									$lignes_tab.=$lv2[$i].";";
								}
							}
							echo "</td>\n";
						}

						if(count($lv3)>0) {
							echo "<td>\n";
							for($i=0;$i<count($lv3);$i++) {
								if(in_array(strtoupper($lv3[$i]),$tab_ele_opt)) {
									echo $lv3[$i];
									echo "<input type='hidden' name='ele_lv3[$cpt]' id='lv3_".$cpt."' value='$lv3[$i]' />\n";

									$lignes_tab.=$lv3[$i].";";
								}
							}
							echo "</td>\n";
						}


						if(count($autre_opt)>0) {
							echo "<td>\n";
							for($i=0;$i<count($autre_opt);$i++) {
								if(in_array(strtoupper($autre_opt[$i]),$tab_ele_opt)) {
									echo $autre_opt[$i];

									$lignes_tab.=$autre_opt[$i].";";
								}
							}
						}

						echo "<td>\n";
						if(($fut_classe!='Red')&&($fut_classe!='Dep')&&($fut_classe!='')) {
							for($i=0;$i<count($tab_ele_opt);$i++) {
								if(in_array($tab_ele_opt[$i],$tab_opt_exclue["$fut_classe"])) {
									echo "<span style='color:red;'>ERREUR: L'option $tab_ele_opt[$i] est exclue en $fut_classe</span>";
								}
							}
						}
						echo "</td>\n";

						echo "</tr>\n";
						$lignes_tab.="\n";
						$cpt++;
					}
				}
			}
		}

		echo "</table>\n";
		$lignes_tab.="\n";

		echo "<input type='hidden' name='projet' value='$projet' />\n";

		echo "<script type='text/javascript'>
	document.getElementById('eff_select'+$loop).innerHTML=$eff_tot_select;
	document.getElementById('eff_select_sexe'+$loop).innerHTML=$eff_tot_select_M+'/'+$eff_tot_select_F;
</script>\n";

		//$fich_csv.="Eff.select :$eff_tot_select;$eff_tot_select_M / $eff_tot_select_F";
		$fich_csv.="Eff.select :$eff_tot_select;$eff_tot_select_M-/-$eff_tot_select_F";
		if(count($lv1)>0) {
			//echo "<th>&nbsp;</th>\n";
			$fich_csv.=";";
		}
		if(count($lv2)>0) {
			//echo "<th>&nbsp;</th>\n";
			$fich_csv.=";";
		}
		if(count($lv3)>0) {
			//echo "<th>&nbsp;</th>\n";
			$fich_csv.=";";
		}
		if(count($autre_opt)>0) {
			//echo "<th>&nbsp;</th>\n";
			$fich_csv.=";";
		}
		//if(count($lv2)>0) {echo "<th>&nbsp;</th>\n";$fich_csv.=";";}
		//if(count($lv3)>0) {echo "<th>&nbsp;</th>\n";$fich_csv.=";";}
		//if(count($autre_opt)>0) {echo "<th>&nbsp;</th>\n";$fich_csv.=";";}
		$fich_csv.="\n";

		$fich_csv.=$lignes_tab;
	}

	echo "</form>\n";

	sort($tab_ele_toutes_requetes);
	$tmp_array=$tab_ele_toutes_requetes;
	$tab_ele_toutes_requetes=array_unique($tmp_array);

	// Liste des �l�ves non retenus dans les listes
	$tab_exclus=array();
	//$sql="SELECT DISTINCT geo.login FROM gc_eleves_options geo WHERE geo.projet='$projet' ORDER BY geo.login;";
	$sql="SELECT DISTINCT geo.login FROM gc_eleves_options geo WHERE geo.projet='$projet' ORDER BY geo.login;";
	$res=mysql_query($sql);
	while($lig=mysql_fetch_object($res)) {
		if(!in_array($lig->login,$tab_ele_toutes_requetes)) {
			$tab_exclus[]=$lig->login;
		}
	}

	echo "<p>Effectif de ces s�lections&nbsp;: ".count($tab_ele_toutes_requetes)."</p>\n";

	if(count($tab_exclus)>0) {
		echo "<p><a href='#' onclick=\"modif_div_exclus();return false;\">Liste des �l�ves hors s�lection</a></p>\n";
		echo "<div id='div_exclus'>\n";
		echo "<p>Effectif hors des s�lections&nbsp;: ".count($tab_exclus)."</p>\n";
		echo "<table class='boireaus' border='1' summary='Liste des �l�ves hors s�lection'>\n";
		echo "<tr><th>El�ve</th><th>Options</th></tr>\n";
		$alt=1;
		for($loop=0;$loop<count($tab_exclus);$loop++) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			$sql="SELECT nom,prenom FROM eleves WHERE login='$tab_exclus[$loop]';";
			$res=mysql_query($sql);
			$lig=mysql_fetch_object($res);
			echo strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom));
			echo "</td>\n";

			echo "<td>\n";
			//$sql="SELECT opt FROM gc_eleves_options WHERE projet='$projet' AND login='$tab_exclus[$loop]';";
			$sql="SELECT liste_opt FROM gc_eleves_options WHERE projet='$projet' AND login='$tab_exclus[$loop]';";
			$res=mysql_query($sql);
			//$compteur=0;
			while($lig=mysql_fetch_object($res)) {
				//if($compteur>0) {echo ", ";}
				//echo $lig->opt;

				//echo my_ereg_replace("^|","",my_ereg_replace("|$","",$lig->liste_opt));

				$compteur=0;
				$tmp_tab=explode("|",$lig->liste_opt);
				for($n=0;$n<count($tmp_tab);$n++) {
					if($tmp_tab[$n]!="") {
						if($compteur>0) {echo ", ";}

						echo strtoupper($tmp_tab[$n]);

						$compteur++;
					}
				}

				//$compteur++;
			}
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "</div>\n";

		echo "<script type='text/javascript'>
	//if(document.getElementById('div_exclus')) {
	//	document.getElementById('div_exclus').style.display='none';
	//}

	var etat_affiche_exclus=1;

	function modif_div_exclus() {
		etat_affiche_exclus=etat_affiche_exclus*(-1);

		if(document.getElementById('div_exclus')) {
			if(etat_affiche_exclus==1) {
				document.getElementById('div_exclus').style.display='';
			}
			else {
				document.getElementById('div_exclus').style.display='none';
			}
		}

		return false;
	}

	modif_div_exclus();

</script>\n";

	}

	//echo "<pre>";
	//echo $fich_csv;
	//echo "</pre>";

	if($user_temp_directory) {
		//=================================
		$date_fichier=date("Ymd_Hi");
		//$fich=fopen("csv/listes_".$projet."_".$date_fichier.".csv","w+");
		$fich=fopen("../temp/".$user_temp_directory."/listes_".$projet."_".$date_fichier.".csv","w+");
		fwrite($fich,$fich_csv);
		fclose($fich);
		//=================================

		echo "Fichiers <a href='../temp/".$user_temp_directory."/listes_".$projet."_".$date_fichier.".csv'>CSV</a>\n";
		echo " - ";
		echo "<a href='genere_ods.php?fichier_csv=listes_".$projet."_".$date_fichier.".csv&amp;projet=$projet&amp;detail=oui' target='_blank'>ODS_detaill�</a>\n";
		echo " - ";
		echo "<a href='genere_ods.php?fichier_csv=listes_".$projet."_".$date_fichier.".csv&amp;projet=$projet&amp;detail=non' target='_blank'>ODS</a>\n";
	
		echo "<script type='text/javascript'>
	document.getElementById('div_ods').innerHTML=\"Fichiers <a href='../temp/".$user_temp_directory."/listes_".$projet."_".$date_fichier.".csv'>CSV</a> <a href='genere_ods.php?fichier_csv=listes_".$projet."_".$date_fichier.".csv&amp;projet=$projet&amp;detail=non' target='_blank'>ODS</a> <a href='genere_ods.php?fichier_csv=listes_".$projet."_".$date_fichier.".csv&amp;projet=$projet&amp;detail=oui' target='_blank'>ODS_detaill�</a>\";
</script>\n";
	}
	else {
		echo "<p style='color:red'>Le dossier temporaire de l'utilisateur n'existe pas.<br />On ne g�n�re pas de fichier CSV.</p>\n";

		echo "<script type='text/javascript'>
	document.getElementById('div_ods').innerHTML=\"<p style='color:red'>Le dossier temporaire de l'utilisateur n'existe pas.<br />On ne g�n�re pas de fichier CSV.</p>\";
</script>\n";
	}
	//=================================

	//=================================
	$titre="<span id='entete_div_photo_eleve'>El�ve</span>";
	$texte="<div id='corps_div_photo_eleve' align='center'>\n";
	$texte.="<br />\n";
	$texte.="</div>\n";
	
	$tabdiv_infobulle[]=creer_div_infobulle('div_photo',$titre,"",$texte,"",14,0,'y','y','n','n');
	
	echo "<script type='text/javascript'>
	function affiche_photo(photo,nom_prenom) {
		document.getElementById('entete_div_photo_eleve').innerHTML=nom_prenom;
		document.getElementById('corps_div_photo_eleve').innerHTML='<img src=\"../photos/eleves/'+photo+'\" width=\"150\" alt=\"Photo\" /><br />';
	}
";
	//=================================

echo "
	var couleur_classe_fut=new Array($chaine_couleur_classe_fut);
	var couleur_lv1=new Array($chaine_couleur_lv1);
	var couleur_lv2=new Array($chaine_couleur_lv2);
	var couleur_lv3=new Array($chaine_couleur_lv3);

	var couleur_profil=new Array($chaine_couleur_profil);
	var tab_profil=new Array($chaine_profil);

	function colorise(mode,n) {
		var k;
		var i;
		var m;

		for(i=0;i<$cpt;i++) {
			if(mode!='profil') {
				if(document.getElementById(mode+'_'+i)) {
					for(k=0;k<n;k++) {
						if(mode=='classe_fut') {
							if(document.getElementById(mode+'_'+i).value==tab_classes_fut[k]) {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_classe_fut[k];
							}
						}
	
						if(mode=='lv1') {
							if(document.getElementById(mode+'_'+i).value==tab_lv1[k]) {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_lv1[k];
							}
						}
	
						if(mode=='lv2') {
							if(document.getElementById(mode+'_'+i).value==tab_lv2[k]) {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_lv2[k];
							}
						}
	
						if(mode=='lv3') {
							if(document.getElementById(mode+'_'+i).value==tab_lv3[k]) {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_lv3[k];
							}
						}
					}
				}
			}
			else {
				for(m=0;m<couleur_profil.length;m++) {
					if(document.getElementById('profil_'+i).value==tab_profil[m]) {
						document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_profil[m];
					}
				}
			}
		}
	}

	colorise('classe_fut',".count($classe_fut).");

	function lance_colorisation() {
		cat=document.forms[0].elements['colorisation'].options[document.forms[0].elements['colorisation'].selectedIndex].value;
		//alert(cat);
		if(cat=='classe_fut') {
			colorise(cat,".count($classe_fut).");
		}
		if(cat=='lv1') {
			colorise(cat,".count($lv1).");
		}
		if(cat=='lv2') {
			colorise(cat,".count($lv2).");
		}
		if(cat=='lv3') {
			colorise(cat,".count($lv3).");
		}
		if(cat=='profil') {
			colorise(cat,".count($tab_profil).");
		}
	}

	</script>\n";

	// A METTRE DANS UNE INFOBULLE?
	echo "<p style='bold'>R�capitulatif des effectifs&nbsp;:</p>\n";
	echo $lignes_requetes;

	$titre="R�capitulatif des effectifs";
	$texte=$lignes_requetes;
	$tabdiv_infobulle[]=creer_div_infobulle('recap_eff',$titre,"",$texte,"",30,0,'y','y','n','n');

}


require("../lib/footer.inc.php");
?>