<?php
/*
 * $Id: visu_toutes_notes2.php 8788 2012-04-09 19:09:23Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
if (getSettingValue("active_module_absence")=='2'){
    require_once("../lib/initialisationsPropel.inc.php");
}

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}



// INSERT INTO `droits` VALUES ('/cahier_notes/visu_toutes_notes2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//Initialisation

// Modifi� pour pouvoir r�cup�rer ces variables en GET pour les CSV
//$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] :  NULL;
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
//$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] :  NULL;
//$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);
$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : "1");


if ($num_periode=="annee") {
    $referent="annee";
} else {
    $referent="une_periode";
}

// On filtre au niveau s�curit� pour s'assurer qu'un prof n'est pas en train de chercher
// � visualiser des donn�es pour lesquelles il n'est pas autoris�

if (isset($id_classe)) {
	// On regarde si le type est correct :
	if (!is_numeric($id_classe)) {
		tentative_intrusion("3", "Changement de la valeur de id_classe pour un type non num�rique, en changeant la valeur d'un champ 'hidden' d'un formulaire.");
		echo "Erreur.";
		require ("../lib/footer.inc.php");
		die();
	}
	// On teste si le professeur a le droit d'acc�der � cette classe
	if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
		$test = mysql_num_rows(mysql_query("SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
		if ($test == "0") {
			tentative_intrusion("3", "Tentative d'acc�s par un prof � une classe dans laquelle il n'enseigne pas, sans en avoir l'autorisation. Tentative avanc�e : changement des valeurs de champs de type 'hidden' du formulaire.");
			echo "Vous ne pouvez pas acc�der � cette classe car vous n'y �tes pas professeur !";
			require ("../lib/footer.inc.php");
			die();
		}
	}
}

if((!isset($id_classe))||(!isset($num_periode))) {
	header("Location: index2.php?msg=".rawurlencode('Choisissez une classe'));
	die();
}

//debug_var();

check_token();

function my_echo($texte) {
	$debug=0;
	if($debug!=0) {
		echo $texte;
	}
}


$larg_tab = isset($_POST['larg_tab']) ? $_POST['larg_tab'] :  NULL;
$bord = isset($_POST['bord']) ? $_POST['bord'] :  NULL;

//============================
//$aff_abs = isset($_POST['aff_abs']) ? $_POST['aff_abs'] :  NULL;
//$aff_reg = isset($_POST['aff_reg']) ? $_POST['aff_reg'] :  NULL;
//$aff_doub = isset($_POST['aff_doub']) ? $_POST['aff_doub'] :  NULL;
//$aff_rang = isset($_POST['aff_rang']) ? $_POST['aff_rang'] :  NULL;
//$aff_date_naiss = isset($_POST['aff_date_naiss']) ? $_POST['aff_date_naiss'] :  NULL;

// Modifi� pour pouvoir r�cup�rer ces variables en GET pour les CSV
$aff_abs = isset($_POST['aff_abs']) ? $_POST['aff_abs'] : (isset($_GET['aff_abs']) ? $_GET['aff_abs'] : NULL);
$aff_reg = isset($_POST['aff_reg']) ? $_POST['aff_reg'] : (isset($_GET['aff_reg']) ? $_GET['aff_reg'] : NULL);
$aff_doub = isset($_POST['aff_doub']) ? $_POST['aff_doub'] : (isset($_GET['aff_doub']) ? $_GET['aff_doub'] : NULL);
$aff_rang = isset($_POST['aff_rang']) ? $_POST['aff_rang'] : (isset($_GET['aff_rang']) ? $_GET['aff_rang'] : NULL);
$aff_date_naiss = isset($_POST['aff_date_naiss']) ? $_POST['aff_date_naiss'] : (isset($_GET['aff_date_naiss']) ? $_GET['aff_date_naiss'] : NULL);
//============================


$couleur_alterne = isset($_POST['couleur_alterne']) ? $_POST['couleur_alterne'] :  NULL;

//================================
if(file_exists("../visualisation/draw_graphe.php")){
	$temoin_graphe="oui";
}
else{
	$temoin_graphe="non";
}
//================================

//============================
// Colorisation des r�sultats
$vtn_couleur_texte=isset($_POST['vtn_couleur_texte']) ? $_POST['vtn_couleur_texte'] : array();
$vtn_couleur_cellule=isset($_POST['vtn_couleur_cellule']) ? $_POST['vtn_couleur_cellule'] : array();
$vtn_borne_couleur=isset($_POST['vtn_borne_couleur']) ? $_POST['vtn_borne_couleur'] : array();
$vtn_coloriser_resultats=isset($_POST['vtn_coloriser_resultats']) ? $_POST['vtn_coloriser_resultats'] : "n";
//============================

include "../lib/periodes.inc.php";

// On appelle les �l�ves
if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
	// On ne s�lectionne que les �l�ves que le professeur a en cours
	if ($referent=="une_periode")
		// Calcul sur une seule p�riode
		$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* " .
				"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
				"WHERE (" .
				"jec.id_classe='$id_classe' AND " .
				"e.login = jeg.login AND " .
				"jeg.login = jec.login AND " .
				"jeg.id_groupe = jgp.id_groupe AND " .
				"jgp.login = '".$_SESSION['login']."' AND " .
				"jec.periode = '$num_periode' AND " .
				"jeg.periode = '$num_periode') " .
				"ORDER BY e.nom,e.prenom");
	else {
		// Calcul sur l'ann�e
		$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* " .
				"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
				"WHERE (" .
				"jec.id_classe='$id_classe' AND " .
				"e.login = jeg.login AND " .
				"jeg.login = jec.login AND " .
				"jeg.id_groupe = jgp.id_groupe AND " .
				"jgp.login = '".$_SESSION['login']."') " .
				"ORDER BY e.nom,e.prenom");
	}
} else {
	if ($referent=="une_periode")
		// Calcul sur une seule p�riode
		$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login AND j.periode='$num_periode') ORDER BY nom,prenom");
	else {
		// Calcul sur l'ann�e
		$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login) ORDER BY nom,prenom");
	}
}

$nb_lignes_eleves = mysql_num_rows($appel_donnees_eleves);
$nb_lignes_tableau = $nb_lignes_eleves;

//Initialisation
$moy_classe_point = 0;
$moy_classe_effectif = 0;
$moy_classe_min = 20;
$moy_classe_max = 0;
$moy_cat_classe_point = array();
$moy_cat_classe_effectif = array();
$moy_cat_classe_min = array();
$moy_cat_classe_max = array();

// =====================================
// AJOUT: boireaus
$largeur_graphe=700;
$hauteur_graphe=600;
$taille_police=3;
$epaisseur_traits=2;
$titre="Graphe";
$graph_title=$titre;
//$v_legend2="moyclasse";
$compteur=0;
$nb_series=2;

if(getSettingValue('graphe_largeur_graphe')){
	$largeur_graphe=getSettingValue('graphe_largeur_graphe');
}
else{
	$largeur_graphe=600;
}

if(getSettingValue('graphe_hauteur_graphe')){
	$hauteur_graphe=getSettingValue('graphe_hauteur_graphe');
}
else{
	$hauteur_graphe=400;
}

if(getSettingValue('graphe_taille_police')){
	$taille_police=getSettingValue('graphe_taille_police');
}
else{
	$taille_police=2;
}

if(getSettingValue('graphe_epaisseur_traits')){
	$epaisseur_traits=getSettingValue('graphe_epaisseur_traits');
}
else{
	$epaisseur_traits=2;
}

if(getSettingValue('graphe_temoin_image_escalier')){
	$temoin_image_escalier=getSettingValue('graphe_temoin_image_escalier');
}
else{
	$temoin_image_escalier="non";
}

if(getSettingValue('graphe_tronquer_nom_court')){
	$tronquer_nom_court=getSettingValue('graphe_tronquer_nom_court');
}
else{
	$tronquer_nom_court=0;
}

// =====================================


// On teste la pr�sence d'au moins un coeff pour afficher la colonne des coef
$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
$ligne_supl = 0;
if ($test_coef != 0) $ligne_supl = 1;


// On regarde si on doit afficher les moyennes des cat�gories de mati�res
$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
if ($affiche_categories == "y") {
	$affiche_categories = true;
} else {
	$affiche_categories = false;
}


// Si le rang des �l�ves est demand�, on met � jour le champ rang de la table matieres_notes
/*
if (($aff_rang) and ($referent=="une_periode")) {
	$periode_num=$num_periode;
	include "../lib/calcul_rang.inc.php";
	// Oui, mais pour la visualisation des moyennes des carnets de notes, 
	// ce rang ne correspond pas n�cessairement tant que la recopie des moyennes n'est pas faite.
}
*/

/*
// On regarde si on doit afficher les moyennes des cat�gories de mati�res
$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
if ($affiche_categories == "y") {
	$affiche_categories = true;
} else {
	$affiche_categories = false;
}
*/

if ($affiche_categories) {
	$get_cat = mysql_query("SELECT id FROM matieres_categories");
	$categories = array();
	while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
		$categories[] = $row["id"];
		$moy_cat_classe_point[$row["id"]] = 0;
		$moy_cat_classe_effectif[$row["id"]] = 0;
		$moy_cat_classe_min[$row["id"]] = 20;
		$moy_cat_classe_max[$row["id"]] = 0;
	}

	$cat_names = array();
	foreach ($categories as $cat_id) {
		$cat_names[$cat_id] = html_entity_decode_all_version(mysql_result(mysql_query("SELECT nom_court FROM matieres_categories WHERE id = '" . $cat_id . "'"), 0));
	}
}
// Calcul du nombre de mati�res � afficher

if ($affiche_categories) {
	// On utilise les valeurs sp�cifi�es pour la classe en question
	$groupeinfo = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id ".
	"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
	"WHERE ( " .
	"jgc.categorie_id = jmcc.categorie_id AND " .
	"jgc.id_classe='".$id_classe."' AND " .
	"jgm.id_groupe=jgc.id_groupe AND " .
	"m.matiere = jgm.id_matiere" .
	") " .
	"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet");
} else {
	$groupeinfo = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef
	FROM j_groupes_classes jgc, j_groupes_matieres jgm
	WHERE (
	jgc.id_classe='".$id_classe."' AND
	jgm.id_groupe=jgc.id_groupe
	)
	ORDER BY jgc.priorite,jgm.id_matiere");
}

$lignes_groupes = mysql_num_rows($groupeinfo);

//
// d�finition des premi�res colonnes nom, r�gime, doublant, ...
//
$displayed_categories = array();
$j = 0;
while($j < $nb_lignes_tableau) {
	// colonne nom+pr�nom
	$current_eleve_login[$j] = mysql_result($appel_donnees_eleves, $j, "login");
	$col[1][$j+$ligne_supl] = @mysql_result($appel_donnees_eleves, $j, "nom")." ".@mysql_result($appel_donnees_eleves, $j, "prenom");
	$ind = 2;

	//=======================================
	// colonne date de naissance
    if ($aff_date_naiss){
		$tmpdate=mysql_result($appel_donnees_eleves, $j, "naissance");
		$tmptab=explode("-",$tmpdate);
		if(strlen($tmptab[0])==4){$tmptab[0]=substr($tmptab[0],2,2);}
        $col[$ind][$j+$ligne_supl]=$tmptab[2]."/".$tmptab[1]."/".$tmptab[0];
        $ind++;
	}
	//=======================================

	// colonne r�gime
	if (($aff_reg) or ($aff_doub))
		$regime_doublant_eleve = mysql_query("SELECT * FROM j_eleves_regime WHERE login = '$current_eleve_login[$j]'");
	if ($aff_reg) {
		$col[$ind][$j+$ligne_supl] = @mysql_result($regime_doublant_eleve, 0, "regime");
		$ind++;
	}
	// colonne doublant
	if ($aff_doub) {
		$col[$ind][$j+$ligne_supl] = @mysql_result($regime_doublant_eleve, 0, "doublant");
		$ind++;
	}
	// Colonne absence
	if ($aff_abs) {
        if (getSettingValue("active_module_absence") != '2' || getSettingValue("abs2_import_manuel_bulletin") == 'y') {
		$abs_eleve = "NR";
		if ($referent=="une_periode")
			$abs_eleve = sql_query1("SELECT nb_absences FROM absences WHERE
			login = '$current_eleve_login[$j]' and
			periode = '".$num_periode."'
			");
		else {
			$abs_eleve = sql_query1("SELECT sum(nb_absences) FROM absences WHERE
			login = '$current_eleve_login[$j]'");
		}

		if ($abs_eleve == '-1') $abs_eleve = "NR";
		$col[$ind][$j+$ligne_supl] = $abs_eleve;
		$ind++;
        }else {
            $eleve = EleveQuery::create()->findOneByLogin($current_eleve_login[$j]);
            if ($eleve != null) {
                if ($referent == "une_periode") {
                    $abs_eleve = strval($eleve->getDemiJourneesAbsenceParPeriode($num_periode)->count());
                } else {
                    $date_jour = new DateTime('now');
                    $month = $date_jour->format('m');
                    if ($month > 7) {
                        $date_debut = new DateTime($date_jour->format('y') . '-09-01');
                        $date_fin = new DateTime($date_jour->format('y')+1 . '-08-31');
                    } else {
                        $date_debut = new DateTime($date_jour->format('y')-1 . '-09-01');
                        $date_fin = new DateTime($date_jour->format('y') . '-08-31');
	}
                    $abs_eleve = strval($eleve->getDemiJourneesAbsence($date_debut,$date_fin)->count());
                }
            } else {
                $abs_eleve = "NR";
            }
            $col[$ind][$j + $ligne_supl] = $abs_eleve;
            $ind++;
        }
    }

	// Colonne rang
	if (($aff_rang) and ($referent=="une_periode")) {
		$ind_colonne_rang=$ind;
		// La table j_eleves_classes ne contient les bonnes infos de rang qu'une fois la recopie des moyennes effectu�e.
		// Elle s'appuye sur la table matieres_notes qui n'est remplie qu'une fois la recopie faite, c'est-�-dire en fin de p�riode... pas au moment o� on utilise la Visualisation des moyennes des carnets de notes!
		/*
		$rang = sql_query1("select rang from j_eleves_classes where (
		periode = '".$num_periode."' and
		id_classe = '".$id_classe."' and
		login = '".$current_eleve_login[$j]."' )
		");
		if (($rang == 0) or ($rang == -1)) $rang = "-";
		$col[$ind][$j+$ligne_supl] = $rang;
		*/
		$col[$ind][$j+$ligne_supl]="-";

		$ind++;
	}

	$j++;
}

$num_debut_colonnes_matieres=$ind;

/*
// Colonne rang
if (($aff_rang) and ($referent!="une_periode")) {
	// Calculer le rang dans le cas ann�e enti�re
}
*/


// Etiquettes des premi�res colonnes
//$ligne1[1] = "Nom ";
$ligne1[1] = "<a href='#' onclick=\"document.getElementById('col_tri').value='1';".
			"document.forms['formulaire_tri'].submit();\"".
			" style='text-decoration:none; color:black;'>".
			"Nom ".
			"</a>";
$ligne1_csv[1] = "Nom ";

//if ($aff_reg) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=R�gime&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"r�gime\">";
//if ($aff_doub) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Redoublant&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"doublant\">";
//if ($aff_abs) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=1/2 journ�es d'absence&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"1/2 journ�es d'absence\">";
//if (($aff_rang) and ($referent=="une_periode")) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Rang de l'�l�ve&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"Rang de l'�l�ve\">";
//if ($aff_reg) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".htmlentities("R�gime")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"r�gime\" />";
//=========================
if ($aff_date_naiss){
	$ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Date de naissance")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"r�gime\" />";
	$ligne1_csv[] = "Date de naissance";
}
//=========================
if ($aff_reg) {
	$ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("R�gime")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"r�gime\" />";
	$ligne1_csv[]="R�gime";
}
if ($aff_doub) {
	$ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Redoublant&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"doublant\" />";
	$ligne1_csv[]="Redoublant";
}
if ($aff_abs) {
	//$ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("1/2 journ�es d'absence")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"1/2 journ�es d'absence\" />";
	$ligne1[] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".(count($ligne1)+1)."';".
				"document.forms['formulaire_tri'].submit();\">".
				"<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("1/2 journ�es d'absence")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"1/2 journ�es d'absence\" />".
				"</a>";
	$ligne1_csv[]="1/2 journ�es d'absence";
}
if (($aff_rang) and ($referent=="une_periode")) {
	//$ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'�l�ve")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"Rang de l'�l�ve\" />";
	$ligne1[] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".(count($ligne1)+1)."';".
				"document.getElementById('sens_tri').value='inverse';".
				"document.forms['formulaire_tri'].submit();\">".
				"<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'�l�ve")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"Rang de l'�l�ve\" />".
				"</a>";
	$ligne1_csv[]="Rang de l'�l�ve";
}

// Etiquettes des quatre derni�res lignes
if ($test_coef != 0) $col[1][0] = "Coefficient";
$col[1][$nb_lignes_tableau+$ligne_supl] = "Moyenne";
$col[1][$nb_lignes_tableau+1+$ligne_supl] = "Min";
$col[1][$nb_lignes_tableau+2+$ligne_supl] = "Max";
$ind = 2;
$nb_col = 1;
$k= 1;

//=========================
if ($aff_date_naiss){
	if ($test_coef != 0) $col[$ind][0] = "-";
    $col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
    $nb_col++;
    $k++;
    $ind++;
}
//=========================

if ($aff_reg) {
	if ($test_coef != 0) $col[$ind][0] = "-";
	$col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
	$nb_col++;
	$k++;
	$ind++;
}
if ($aff_doub) {
	if ($test_coef != 0) $col[$ind][0] = "-";
	$col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
	$nb_col++;
	$k++;
	$ind++;
}
if ($aff_abs) {
	if ($test_coef != 0) $col[$ind][0] = "-";
	$col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
	$nb_col++;
	$k++;
	$ind++;
}
if (($aff_rang) and ($referent=="une_periode")) {
	if ($test_coef != 0) $col[$ind][0] = "-";
	$col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
	$nb_col++;
	$k++;
	$ind++;
}


$j = '0';
while($j < $nb_lignes_tableau) {
	//$total_coef[$j+$ligne_supl] = 0;
	//$total_points[$j+$ligne_supl] = 0;
	$total_coef_classe[$j+$ligne_supl] = 0;
	$total_coef_eleve[$j+$ligne_supl] = 0;

	$total_points_classe[$j+$ligne_supl] = 0;
	$total_points_eleve[$j+$ligne_supl] = 0;

	// =================================
	// MODIF: boireaus
	if ($affiche_categories) {
		//$total_cat_coef[$j+$ligne_supl] = array();
		//$total_cat_points[$j+$ligne_supl] = array();
		$total_cat_coef_classe[$j+$ligne_supl] = array();
		$total_cat_coef_eleve[$j+$ligne_supl] = array();

		$total_cat_points_classe[$j+$ligne_supl] = array();
		$total_cat_points_eleve[$j+$ligne_supl] = array();

		foreach ($categories as $cat_id) {
			//$total_cat_coef[$j+$ligne_supl][$cat_id] = 0;
			//$total_cat_points[$j+$ligne_supl][$cat_id] = 0;
			$total_cat_coef_classe[$j+$ligne_supl][$cat_id] = 0;
			$total_cat_coef_eleve[$j+$ligne_supl][$cat_id] = 0;

			$total_cat_points_classe[$j+$ligne_supl][$cat_id] = 0;
			$total_cat_points_eleve[$j+$ligne_supl][$cat_id] = 0;
		}
	}
	// =================================
	$j++;
}


//=============================
// AJOUT: boireaus
$chaine_matieres=array();
$chaine_moy_eleve1=array();
$chaine_moy_classe=array();
//$chaine_moy_classe="";
//=============================

//
// d�finition des colonnes mati�res
//
$i= '0';

//pour calculer la moyenne annee de chaque matiere
$moyenne_annee_matiere=array();
$prev_cat_id = null;
while($i < $lignes_groupes){
	$moy_max = -1;
	$moy_min = 21;

	foreach ($moyenne_annee_matiere as $tableau => $value)  unset($moyenne_annee_matiere[$tableau]);

	$var_group_id = mysql_result($groupeinfo, $i, "id_groupe");
	$current_group = get_group($var_group_id);
	// Coeff pour la classe
	$current_coef = mysql_result($groupeinfo, $i, "coef");

	if((!isset($current_group['visibilite']['cahier_notes']))||($current_group['visibilite']['cahier_notes']=='y')) {
		$nb_col++;
		$k++;
	
		//==============================
		// AJOUT: boireaus
		if ($referent == "une_periode") {
			$sql="SELECT round(avg(cnc.note),1) moyenne FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			ccn.periode='$num_periode' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_moyenne = mysql_query($sql);
		}
		else{
			$sql="SELECT round(avg(cnc.note),1) moyenne FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_moyenne = mysql_query($sql);
		}
	
		$moy_classe_tmp = @mysql_result($call_moyenne, 0, "moyenne");
		//==============================
	
	
	
		if ($affiche_categories) {
		// On regarde si on change de cat�gorie de mati�re
			if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
				$prev_cat_id = $current_group["classes"]["classes"][$id_classe]["categorie_id"];
			}
		}
		$j = '0';
		while($j < $nb_lignes_tableau) {
	
			// Coefficient personnalis� pour l'�l�ve?
			$sql="SELECT value FROM eleves_groupes_settings WHERE (" .
					"login = '".$current_eleve_login[$j]."' AND " .
					"id_groupe = '".$current_group["id"]."' AND " .
					"name = 'coef')";
			$test_coef_personnalise = mysql_query($sql);
			if (mysql_num_rows($test_coef_personnalise) > 0) {
				$coef_eleve = mysql_result($test_coef_personnalise, 0);
			} else {
				// Coefficient du groupe:
				$coef_eleve = $current_coef;
			}
			$coef_eleve=number_format($coef_eleve,1, ',', ' ');
	
	
			if ($referent == "une_periode") {
				if (!in_array($current_eleve_login[$j], $current_group["eleves"][$num_periode]["list"])) {
					$col[$k][$j+$ligne_supl] = "/";
				} else {
					//================================
					// MODIF: boireaus
					//$current_eleve_note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$current_eleve_login[$j]' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
	
					$sql="SELECT cnc.note, cnc.statut FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
					ccn.id_groupe='".$current_group["id"]."' AND
					ccn.periode='$num_periode' AND
					cc.id_racine = ccn.id_cahier_notes AND
					cnc.id_conteneur=cc.id AND
					cc.id=cc.id_racine AND
					cnc.login='".$current_eleve_login[$j]."'";
					//echo "$sql";
					$res_moy=mysql_query($sql);
	
					if(mysql_num_rows($res_moy)>0) {
						$lig_moy=mysql_fetch_object($res_moy);
						if($lig_moy->statut=='y') {
							if($lig_moy->note!="") {
								$col[$k][$j+$ligne_supl] = number_format($lig_moy->note,1, ',', ' ');
								$temp=$lig_moy->note;
								if ($current_coef > 0) {
									if ($affiche_categories) {
										if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
										//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
										//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$temp;
	
										$total_cat_coef_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve;
										$total_cat_points_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve*$temp;
	
										$total_cat_coef_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef;
										$total_cat_points_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef*$temp;
									}
									//$total_coef[$j+$ligne_supl] += $current_coef;
									//$total_points[$j+$ligne_supl] += $current_coef*$temp;
	
									$total_coef_eleve[$j+$ligne_supl] += $coef_eleve;
									$total_points_eleve[$j+$ligne_supl] += $coef_eleve*$temp;
	
									$total_coef_classe[$j+$ligne_supl] += $current_coef;
									$total_points_classe[$j+$ligne_supl] += $current_coef*$temp;
	
									//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
									//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$temp;
								}
								/*
								if($chaine_matieres[$j+$ligne_supl]==""){
									$chaine_matieres[$j+$ligne_supl]=$current_group["matiere"]["matiere"];
									$chaine_moy_eleve1[$j+$ligne_supl]=$lig_moy->note;
								}
								else{
									$chaine_matieres[$j+$ligne_supl].="|".$current_group["matiere"]["matiere"];
									$chaine_moy_eleve1[$j+$ligne_supl].="|".$lig_moy->note;
								}
								*/
							}
							else{
								$col[$k][$j+$ligne_supl] = '-';
							}
						}
						else{
							$col[$k][$j+$ligne_supl] = '-';
						}
					}
					else {
						$col[$k][$j+$ligne_supl] = '-';
					}
	
					$sql="SELECT * FROM j_eleves_groupes WHERE id_groupe='".$current_group["id"]."' AND periode='$num_periode'";
					$test_eleve_grp=mysql_query($sql);
					if(mysql_num_rows($test_eleve_grp)>0){
						if(!isset($chaine_matieres[$j+$ligne_supl])){
						//if($chaine_matieres[$j+$ligne_supl]==""){
							$chaine_matieres[$j+$ligne_supl]=$current_group["matiere"]["matiere"];
							//$chaine_moy_eleve1[$j+$ligne_supl]=$lig_moy->note;
							$chaine_moy_eleve1[$j+$ligne_supl]=$col[$k][$j+$ligne_supl];
							$chaine_moy_classe[$j+$ligne_supl]=$moy_classe_tmp;
						}
						else{
							$chaine_matieres[$j+$ligne_supl].="|".$current_group["matiere"]["matiere"];
							//$chaine_moy_eleve1[$j+$ligne_supl].="|".$lig_moy->note;
							$chaine_moy_eleve1[$j+$ligne_supl].="|".$col[$k][$j+$ligne_supl];
							$chaine_moy_classe[$j+$ligne_supl].="|".$moy_classe_tmp;
						}
					}
	
			/*
				//$current_eleve_statut = @mysql_result($current_eleve_note_query, 0, "statut");
				if ($current_eleve_statut != "") {
					$col[$k][$j+$ligne_supl] = $current_eleve_statut;
				} else {
					$temp = @mysql_result($current_eleve_note_query, 0, "note");
					if  ($temp != '')  {
					$col[$k][$j+$ligne_supl] = number_format($temp,1, ',', ' ');
					if ($current_coef > 0) {
						if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
						$total_coef[$j+$ligne_supl] += $current_coef;
						$total_points[$j+$ligne_supl] += $current_coef*$temp;
						$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
						$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$temp;
					}
					} else {
					$col[$k][$j+$ligne_supl] = '-';
					}
				}
			*/
					//================================
				}
			}
			else {
				// Ann�e enti�re
				$p = "1";
				$moy = 0;
				$non_suivi = 2;
				$coef_moy = 0;
				while ($p < $nb_periode) {
					if (!in_array($current_eleve_login[$j], $current_group["eleves"][$p]["list"])) {
						$non_suivi = $non_suivi*2;
					} else {
						//================================
						// MODIF: boireaus
						//$current_eleve_note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$current_eleve_login[$j]' AND id_groupe='" . $current_group["id"] . "' AND periode='$p')");
	
						$sql="SELECT cnc.note, cnc.statut FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
						ccn.id_groupe='".$current_group["id"]."' AND
						ccn.periode='$p' AND
						cc.id_racine = ccn.id_cahier_notes AND
						cnc.id_conteneur=cc.id AND
						cc.id=cc.id_racine AND
						cnc.login='".$current_eleve_login[$j]."'";
						//echo "$sql";
						$res_moy=mysql_query($sql);
	
						if(mysql_num_rows($res_moy)>0){
							$lig_moy=mysql_fetch_object($res_moy);
							if($lig_moy->statut=='y'){
								if($lig_moy->note!=""){
									$moy += $lig_moy->note;
									$coef_moy++;
								}
							}
						}
	
				/*
						$current_eleve_statut = @mysql_result($current_eleve_note_query, 0, "statut");
						if ($current_eleve_statut == "") {
						$temp = @mysql_result($current_eleve_note_query, 0, "note");
						if  ($temp != '')  {
							$moy += $temp;
							$coef_moy++;
						}
						}
				*/
						//================================
					}
					$p++;
				}
	
	
				$moy_eleve_grp_courant_annee="-";
	
				if ($non_suivi == (pow(2,$nb_periode))) {
					// L'�l�ve n'a suivi la mati�re sur aucune p�riode
					$col[$k][$j+$ligne_supl] = "/";
				} else if ($coef_moy != 0) {
					// L'�l�ve a au moins une note sur au moins une p�riode
					$moy = $moy/$coef_moy;
					$moy_min = min($moy_min,$moy);
					$moy_max = max($moy_max,$moy);
					$col[$k][$j+$ligne_supl] = number_format($moy,1, ',', ' ');
	
					$moy_eleve_grp_courant_annee=$col[$k][$j+$ligne_supl];
	
					if ($current_coef > 0) {
						if($affiche_categories){
							if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
							//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
							//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;
	
							$total_cat_coef_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve;
							$total_cat_points_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve*$moy;
	
							$total_cat_coef_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef;
							$total_cat_points_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;
						}
						//$total_coef[$j+$ligne_supl] += $current_coef;
						//$total_points[$j+$ligne_supl] += $current_coef*$moy;
	
						$total_coef_eleve[$j+$ligne_supl] += $coef_eleve;
						$total_points_eleve[$j+$ligne_supl] += $coef_eleve*$moy;
	
						$total_coef_classe[$j+$ligne_supl] += $current_coef;
						$total_points_classe[$j+$ligne_supl] += $current_coef*$moy;
						//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
						//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;
					}
				} else {
					// Bien que suivant la mati�re, l'�l�ve n'a aucune note � toutes les p�riode (absent, pas de note, disp ...)
					$col[$k][$j+$ligne_supl] = "-";
				}
	
	
	
	
	
	
	
				$sql="SELECT * FROM j_eleves_groupes WHERE id_groupe='".$current_group["id"]."'";
				$test_eleve_grp=mysql_query($sql);
				if(mysql_num_rows($test_eleve_grp)>0) {
					//if($chaine_matieres[$j+$ligne_supl]==""){
					if(!isset($chaine_matieres[$j+$ligne_supl])){
						$chaine_matieres[$j+$ligne_supl]=$current_group["matiere"]["matiere"];
						//$chaine_moy_eleve1[$j+$ligne_supl]=$lig_moy->note;
						$chaine_moy_eleve1[$j+$ligne_supl]=$moy_eleve_grp_courant_annee;
						$chaine_moy_classe[$j+$ligne_supl]=$moy_classe_tmp;
					}
					else{
						if($chaine_matieres[$j+$ligne_supl]==""){
							$chaine_matieres[$j+$ligne_supl]=$current_group["matiere"]["matiere"];
							//$chaine_moy_eleve1[$j+$ligne_supl]=$lig_moy->note;
							$chaine_moy_eleve1[$j+$ligne_supl]=$moy_eleve_grp_courant_annee;
							$chaine_moy_classe[$j+$ligne_supl]=$moy_classe_tmp;
						}
						else{
							$chaine_matieres[$j+$ligne_supl].="|".$current_group["matiere"]["matiere"];
							//$chaine_moy_eleve1[$j+$ligne_supl].="|".$lig_moy->note;
							$chaine_moy_eleve1[$j+$ligne_supl].="|".$moy_eleve_grp_courant_annee;
							$chaine_moy_classe[$j+$ligne_supl].="|".$moy_classe_tmp;
						}
					}
				}
	
	
			}
			/*
			echo "\$current_eleve_login[$j]=".$current_eleve_login[$j]."<br />\n";
			echo "\$chaine_matieres[$j+$ligne_supl]=".$chaine_matieres[$j+$ligne_supl]."<br />\n";
			echo "\$chaine_moy_eleve1[$j+$ligne_supl]=".$chaine_moy_eleve1[$j+$ligne_supl]."<br /><br />\n";
			*/
			$j++;
		}
	
		//================================
		// MODIF: boireaus
		if ($referent == "une_periode") {
			//$call_moyenne = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
			$sql="SELECT round(avg(cnc.note),1) moyenne FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			ccn.periode='$num_periode' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_moyenne = mysql_query($sql);
	
			//$call_max = mysql_query("SELECT max(note) note_max FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
			$sql="SELECT max(cnc.note) note_max FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			ccn.periode='$num_periode' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_max = mysql_query($sql);
	
			//$call_min = mysql_query("SELECT min(note) note_min FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
			$sql="SELECT min(cnc.note) note_min FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			ccn.periode='$num_periode' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_min = mysql_query($sql);
		}
		else {
			//$call_moyenne = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "')");
			$sql="SELECT round(avg(cnc.note),1) moyenne FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_moyenne = mysql_query($sql);
		}
		//================================
	
		$temp = @mysql_result($call_moyenne, 0, "moyenne");
	
	/*
		//========================================
		// AJOUT: boireaus
		if($chaine_moy_classe==""){
			$chaine_moy_classe=$temp;
		}
		else{
			$chaine_moy_classe.="|".$temp;
		}
	*/
		//================================
		// AJOUT: boireaus
		if($temoin_graphe=="oui"){
			if($i==$lignes_groupes-1){
				for($loop=0;$loop<$nb_lignes_tableau;$loop++){
					//$col[1][$loop+$ligne_supl]="<a href='../visualisation/draw_graphe?".rawurlencode($chaine_matieres[$loop+$ligne_supl])."'>".$col[1][$loop+$ligne_supl]."</a>";
	
					//$col[1][$loop+$ligne_supl]="<a href='../visualisation/draw_graphe?".$chaine_matieres[$loop+$ligne_supl]."'>".$col[1][$loop+$ligne_supl]."</a>";
					//$col[1][$loop+$ligne_supl]="<a href='draw_graphe.php?".
	
					if(isset($chaine_moy_eleve1[$loop+$ligne_supl])) {
	
						$col_csv[1][$loop+$ligne_supl]=$col[1][$loop+$ligne_supl];
	
						$tmp_col=$col[1][$loop+$ligne_supl];
						//echo "\$current_eleve_login[$loop]=$current_eleve_login[$loop]<br />";
						$col[1][$loop+$ligne_supl]="<a href='../visualisation/draw_graphe.php?".
						"temp1=".strtr($chaine_moy_eleve1[$loop+$ligne_supl],',','.').
						"&amp;temp2=".strtr($chaine_moy_classe[$loop+$ligne_supl],',','.').
						"&amp;etiquette=".$chaine_matieres[$loop+$ligne_supl].
						"&amp;titre=$graph_title".
						"&amp;v_legend1=".$current_eleve_login[$loop].
						"&amp;v_legend2=moyclasse".
						"&amp;compteur=$compteur".
						"&amp;nb_series=$nb_series".
						"&amp;id_classe=$id_classe".
						"&amp;mgen1=".
						"&amp;mgen2=";
						//"&amp;periode=$periode".
						$col[1][$loop+$ligne_supl].="&amp;tronquer_nom_court=$tronquer_nom_court";
						if($referent == "une_periode"){
							$col[1][$loop+$ligne_supl].="&amp;periode=".rawurlencode("P�riode ".$num_periode);
						}
						else{
							$col[1][$loop+$ligne_supl].="&amp;periode=".rawurlencode("Ann�e");
						}
						$col[1][$loop+$ligne_supl].="&amp;largeur_graphe=$largeur_graphe".
						"&amp;hauteur_graphe=$hauteur_graphe".
						"&amp;taille_police=$taille_police".
						"&amp;epaisseur_traits=$epaisseur_traits".
						"&amp;temoin_image_escalier=$temoin_image_escalier".
						"' target='_blank'>".$tmp_col.
						"</a>";
	
					}
				}
				//echo "\$chaine_moy_classe=".$chaine_moy_classe."<br /><br />\n";
			}
		}
		// ===============================
		//========================================
	
		if ($test_coef != 0) {
			if ($current_coef > 0) {
				$col[$k][0] = number_format($current_coef,1, ',', ' ');
			}
			else {
				$col[$k][0] = "-";
			}
		}
		if ($temp != '') {
			//$col[$k][$nb_lignes_tableau+$ligne_supl] = $temp;
			$col[$k][$nb_lignes_tableau+$ligne_supl] = number_format($temp,1, ',', ' ');
		}
		else {
			$col[$k][$nb_lignes_tableau+$ligne_supl] = '-';
		}
		if ($referent == "une_periode") {
			$temp = @mysql_result($call_min, 0, "note_min");
			if($temp != ''){
				//$col[$k][$nb_lignes_tableau+1+$ligne_supl] = $temp;
				$col[$k][$nb_lignes_tableau+1+$ligne_supl] = number_format($temp,1, ',', ' ');
			}
			else{
				$col[$k][$nb_lignes_tableau+1+$ligne_supl] = '-';
			}
			$temp=@mysql_result($call_max, 0, "note_max");
			if ($temp != '') {
				//$col[$k][$nb_lignes_tableau+2+$ligne_supl] = $temp;
				$col[$k][$nb_lignes_tableau+2+$ligne_supl] = number_format($temp,1, ',', ' ');
			}
			else {
				$col[$k][$nb_lignes_tableau+2+$ligne_supl] = '-';
			}
		}
		else{
			if ($moy_min <=20)
				$col[$k][$nb_lignes_tableau+1+$ligne_supl] = number_format($moy_min,1, ',', ' ');
			else
				$col[$k][$nb_lignes_tableau+1+$ligne_supl] = '-';
	
			if ($moy_max >= 0)
				$col[$k][$nb_lignes_tableau+2+$ligne_supl] = number_format($moy_max,1, ',', ' ');
			else
				$col[$k][$nb_lignes_tableau+2+$ligne_supl] = '-';
		}
	
		$nom_complet_matiere = $current_group["description"];
		$nom_complet_coupe = (strlen($nom_complet_matiere) > 20)? urlencode(substr($nom_complet_matiere,0,20)."...") : urlencode($nom_complet_matiere);
	
		$nom_complet_coupe_csv=(strlen($nom_complet_matiere) > 20) ? substr($nom_complet_matiere,0,20) : $nom_complet_matiere;
		$nom_complet_coupe_csv=preg_replace("/;/","",$nom_complet_coupe_csv);
	
		//$ligne1[$k] = "<IMG SRC=\"../lib/create_im_mat.php?texte=$nom_complet_coupe&width=22\" WIDTH=\"22\" BORDER=\"0\" />";
		//$ligne1[$k] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("$nom_complet_coupe")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"$nom_complet_coupe\" />";
		$ligne1[$k]="<a href='#' onclick=\"document.getElementById('col_tri').value='$k';";
		$ligne1[$k].="document.forms['formulaire_tri'].submit();\">";
		$ligne1[$k] .= "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("$nom_complet_coupe")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"$nom_complet_coupe\" />";
		$ligne1[$k].="</a>";
	
		$ligne1_csv[$k] = "$nom_complet_coupe_csv";
	}

	$i++;
}
// Derni�re colonne des moyennes g�n�rales
if ($ligne_supl == 1) {
	// Les moyennes pour chaque cat�gorie
	//echo "\$displayed_categories=$displayed_categories<br />";
	foreach($displayed_categories as $cat_id) {
		$nb_col++;
		//$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne : " . $cat_names[$cat_id])."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"".$cat_names[$cat_id]."\" />";

		$ligne1[$nb_col] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".$nb_col."';".
				"document.forms['formulaire_tri'].submit();\">".
				"<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne : " . $cat_names[$cat_id])."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"".$cat_names[$cat_id]."\" />".
				"</a>";

		$ligne1_csv[$nb_col] = "Moyenne : " . $cat_names[$cat_id];

		$j = '0';
		while($j < $nb_lignes_tableau) {
			//if ($total_cat_coef[$j+$ligne_supl][$cat_id] > 0) {
			if ($total_cat_coef_eleve[$j+$ligne_supl][$cat_id] > 0) {
				/*
				$col[$nb_col][$j+$ligne_supl] = number_format($total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id],1, ',', ' ');
				$moy_cat_classe_point[$cat_id] +=$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id];
				$moy_cat_classe_effectif[$cat_id]++;
				$moy_cat_classe_min[$cat_id] = min($moy_cat_classe_min[$cat_id],$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id]);
				*/

				//echo "\$moy_cat_classe_min[$cat_id]=$moy_cat_classe_min[$cat_id]<br />\n";
				//echo "\$moy_cat_classe_min[$cat_id]=\$total_cat_points[$j+$ligne_supl][$cat_id]/\$total_cat_coef[$j+$ligne_supl][$cat_id]=".$total_cat_points[$j+$ligne_supl][$cat_id]."/".$total_cat_coef[$j+$ligne_supl][$cat_id]."=".$moy_cat_classe_min[$cat_id]."<br /><br />\n";

				//$moy_cat_classe_max[$cat_id] = max($moy_cat_classe_max[$cat_id],$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id]);

				$col[$nb_col][$j+$ligne_supl] = number_format($total_cat_points_eleve[$j+$ligne_supl][$cat_id]/$total_cat_coef_eleve[$j+$ligne_supl][$cat_id],1, ',', ' ');
				$moy_cat_classe_point[$cat_id] +=$total_cat_points_classe[$j+$ligne_supl][$cat_id]/$total_cat_coef_classe[$j+$ligne_supl][$cat_id];
				$moy_cat_classe_effectif[$cat_id]++;
				$moy_cat_classe_min[$cat_id] = min($moy_cat_classe_min[$cat_id],$total_cat_points_eleve[$j+$ligne_supl][$cat_id]/$total_cat_coef_eleve[$j+$ligne_supl][$cat_id]);
				$moy_cat_classe_max[$cat_id] = max($moy_cat_classe_max[$cat_id],$total_cat_points_eleve[$j+$ligne_supl][$cat_id]/$total_cat_coef_eleve[$j+$ligne_supl][$cat_id]);

			} else {
				$col[$nb_col][$j+$ligne_supl] = '/';
			}
			$j++;
		}
		$col[$nb_col][0] = "-";
		if ($moy_cat_classe_point[$cat_id] == 0) {
			$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = "-";
			$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = "-";
			$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = "-";
		} else {
			$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = number_format($moy_cat_classe_point[$cat_id]/$moy_cat_classe_effectif[$cat_id],1, ',', ' ');
			$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = number_format($moy_cat_classe_min[$cat_id],1, ',', ' ');
			$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = number_format($moy_cat_classe_max[$cat_id],1, ',', ' ');
		}
	}

	// La moyenne g�n�rale
	$nb_col++;
	//$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Moyenne g�n�rale&width=22\" WIDTH=\"22\" BORDER=\"0\" />";
	//$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne g�n�rale")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"Moyenne g�n�rale\" />";

	$ligne1[$nb_col]="<a href='#' onclick=\"document.getElementById('col_tri').value='$nb_col';";
	if(preg_match("/^Rang/i",$ligne1[$nb_col])) {$ligne1[$nb_col].="document.getElementById('sens_tri').value='inverse';";}
	$ligne1[$nb_col].="document.forms['formulaire_tri'].submit();\">";
    $ligne1[$nb_col].="<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne g�n�rale")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"Moyenne g�n�rale\" />";
	$ligne1[$nb_col].="</a>";

	$ligne1_csv[$nb_col] = "Moyenne g�n�rale";

	$j = '0';
	while($j < $nb_lignes_tableau) {
		//if ($total_coef[$j+$ligne_supl] > 0) {
		if ($total_coef_eleve[$j+$ligne_supl] > 0) {
			/*
			$col[$nb_col][$j+$ligne_supl] = number_format($total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl],1, ',', ' ');
			$moy_classe_point +=$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl];
			$moy_classe_effectif++;
			$moy_classe_min = min($moy_classe_min,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
			$moy_classe_max = max($moy_classe_max,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
			*/
            $col[$nb_col][$j+$ligne_supl] = number_format($total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl],1, ',', ' ');
            $moy_classe_point +=$total_points_classe[$j+$ligne_supl]/$total_coef_classe[$j+$ligne_supl];
            $moy_classe_effectif++;
            $moy_classe_min = min($moy_classe_min,$total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl]);
            $moy_classe_max = max($moy_classe_max,$total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl]);
		} else {
			$col[$nb_col][$j+$ligne_supl] = '/';
		}
		$j++;
	}
	$col[$nb_col][0] = "-";
	if ($moy_classe_point == 0) {
		$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = "-";
		$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = "-";
		$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = "-";
	} else {
		$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = number_format($moy_classe_point/$moy_classe_effectif,1, ',', ' ');
		$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = number_format($moy_classe_min,1, ',', ' ');
		$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = number_format($moy_classe_max,1, ',', ' ');
	}


	// Colonne Rang: Cas du choix d'une p�riode: on calcule le rang d'apr�s la moyenne g�n�rale de l'�l�ve
	if (($aff_rang)&&(isset($ind_colonne_rang))) {
		// On va calculer les rangs
		//$ind_colonne_rang
		// Initialisation:
		$k=0;
		unset($tmp_tab);
		unset($rg);
		//while($k <= $nb_lignes_tableau) {
		while($k < $nb_lignes_tableau) {
			$rg[$k]=$k;

			//echo $col[1][$k+1].": ".$col[$nb_col][$k+1]." et total_coef:".$total_coef_eleve[$k+1]."<br />";
			//$tmp_tab[$k]=$col[$nb_col][$k+$ligne_supl];
			$tmp_tab[$k]=preg_replace("/,/",".",$col[$nb_col][$k+$ligne_supl]);

			// Initialisation:
			$col[$ind_colonne_rang][$k]="-";
			$k++;
		}

		array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);

		$k=0;
		while($k < $nb_lignes_tableau) {
			if(isset($rg[$k])) {
				$col[$ind_colonne_rang][$rg[$k]+1]=$k+1;
			}
			$k++;
		}

	}

	// Colonne rang (en fin de tableau (derni�re colonne) dans le cas Ann�e enti�re)
	if (($aff_rang) and ($referent!="une_periode")) {
		// Calculer le rang dans le cas ann�e enti�re
		//$nb_col++;

		/*
		function my_echo($texte) {
			$debug=0;
			if($debug!=0) {
				echo $texte;
			}
		}
		*/

		// Pr�paratifs

		// Initialisation d'un tableau pour les rangs et affectation des valeurs r�index�es dans un tableau temporaire
		my_echo("<table>");
		my_echo("<tr>");
		my_echo("<td>");
		my_echo("<table>");
		unset($rg);
		unset($tmp_tab);
		$k=0;
		unset($rg);
		while($k < $nb_lignes_tableau) {
			$rg[$k]=$k;

			if ($total_coef_eleve[$k+$ligne_supl] > 0) {
				$tmp_tab[$k]=preg_replace("/,/",".",$col[$nb_col][$k+1]);
				my_echo("<tr>");
				my_echo("<td>".($k+1)."</td><td>".$col[1][$k+1]."</td><td>".$col[$nb_col][$k+1]."</td><td>$tmp_tab[$k]</td>");
				my_echo("</tr>");
			}
			else {
				$tmp_tab[$k]="?";
				my_echo("<tr>");
				my_echo("<td>".($k+1)."</td><td>".$col[1][$k+1]."</td><td>".$col[$nb_col][$k+1]."</td><td>$tmp_tab[$k] --</td>");
				my_echo("</tr>");
			}

			$k++;
		}
			my_echo("</table>");
		my_echo("</td>");

		if(isset($tmp_tab)) {
			array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);
		}

		my_echo("<td>");
			my_echo("<table>");
		$k=0;
		while($k < $nb_lignes_tableau) {
			if(isset($rg[$k])) {
				my_echo("<tr><td>\$rg[$k]+1=".($rg[$k]+1)."</td><td>".$col[1][$rg[$k]+1]."</td></tr>");

			}
			$k++;
		}
			my_echo("</table>");
		my_echo("</td>");
		my_echo("</tr>");
		my_echo("</table>");

		// On ajoute une colonne
		$nb_col++;

		// Initialisation de la colonne ajout�e
		$j=1;
		while($j <= $nb_lignes_tableau) {
			$col[$nb_col][$j]="-";
			$j++;
		}

		// Affectation des rangs dans la colonne ajout�e
		$k=0;
		while($k < $nb_lignes_tableau) {
			if(isset($rg[$k])) {
				$col[$nb_col][$rg[$k]+1]=$k+1;
			}
			$k++;
		}

		// Remplissage de la ligne de titre
        //$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'�l�ve")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"Rang de l'�l�ve\" />";

		$ligne1[$nb_col] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".$nb_col."';".
				"document.getElementById('sens_tri').value='inverse';".
				"document.forms['formulaire_tri'].submit();\">".
				"<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'�l�ve")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"Rang de l'�l�ve\" />".
				"</a>";

        $ligne1_csv[$nb_col] = "Rang de l'�l�ve";

		// Remplissage de la ligne coefficients
        $col[$nb_col][0] = "-";

		// Remplissage des lignes Moyenne g�n�rale, minimale et maximale
        $col[$nb_col][$nb_lignes_tableau+$ligne_supl] = "-";
        $col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = "-";
        $col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = "-";
	}

}

//===============================
// A FAIRE: 20080424
// INTERCALER ICI un dispositif analogue � celui de index1.php pour trier autrement

if((isset($_POST['col_tri']))&&($_POST['col_tri']!='')) {
	// Pour activer my_echo � des fins de debug, passer $debug � 1 dans la d�claration de la fonction plus haut dans la page
	my_echo("\$_POST['col_tri']=".$_POST['col_tri']."<br />");
	$col_tri=$_POST['col_tri'];

	$nb_colonnes=$nb_col;

	// if ($test_coef != 0) $col[1][0] = "Coefficient";

	// Ajout d'une ligne de d�calage si il y a une ligne de coeff
	if($col[1][0]=="Coefficient") {
		//$b_inf=1;
		//$b_sup=$nb_lignes_tableau+1;
		$corr=1;
	}
	else {
		//$b_inf=0;
		//$b_sup=$nb_lignes_tableau;
		$corr=0;
	}

	// V�rifier si $col_tri est bien un entier compris entre 0 et $nb_col ou $nb_col+1
	if((strlen(preg_replace("/[0-9]/","",$col_tri))==0)&&($col_tri>0)&&($col_tri<=$nb_colonnes)) {
		my_echo("<table>");
		my_echo("<tr><td valign='top'>");
		unset($tmp_tab);
		unset($rg);
		for($loop=0;$loop<$nb_lignes_tableau;$loop++) {
		//for($loop=$b_inf;$loop<$b_sup;$loop++) {
			// Il faut le POINT au lieu de la VIRGULE pour obtenir un tri correct sur les notes
			//$tmp_tab[$loop]=my_ereg_replace(",",".",$col_csv[$col_tri][$loop]);
			//$tmp_tab[$loop]=my_ereg_replace(",",".",$col[$col_tri][$loop]);
			$tmp_tab[$loop]=preg_replace("/,/",".",$col[$col_tri][$loop+$corr]);
			//$tmp_tab[$loop]=my_ereg_replace(",",".",$col[$col_tri][$loop]);
			my_echo("\$tmp_tab[$loop]=".$tmp_tab[$loop]."<br />");
		}

		my_echo("</td>");
		my_echo("<td valign='top'>");

		$i=0;
		while($i < $nb_lignes_tableau) {
		//$i=$b_inf;
		//while($i < $b_sup) {
			//my_echo($col_csv[1][$i]."<br />");
			my_echo($col[1][$i+$corr]."<br />");
			$i++;
		}
		my_echo("</td>");
		my_echo("<td valign='top'>");


		//$i=0;
		//while($i < $nb_lignes_tableau) {
		$i=0;
		while($i < $nb_lignes_tableau) {
			$rg[$i]=$i;
			$i++;
		}

		// Tri du tableau avec stockage de l'ordre dans $rg d'apr�s $tmp_tab
		array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);


		$i=0;
		while($i < $nb_lignes_tableau) {
			my_echo("\$rg[$i]=".$rg[$i]."<br />");
			$i++;
		}
		my_echo("</td>");
		my_echo("<td valign='top'>");


		// On utilise des tableaux temporaires le temps de la r�affectation dans l'ordre
		$tmp_col=array();
		//$tmp_col_csv=array();

		$i=0;
		$rang_prec = 1;
		$note_prec='';
		while ($i < $nb_lignes_tableau) {
			$ind = $rg[$i];
			if ($tmp_tab[$i] == "-") {
				//$rang_gen = '0';
				$rang_gen = '-';
			}
			else {
				if ($tmp_tab[$i] == $note_prec) {
					$rang_gen = $rang_prec;
				}
				else {
					$rang_gen = $i+1;
				}
				$note_prec = $tmp_tab[$i];
				$rang_prec = $rang_gen;
			}

			//$col[$nb_col+1][$ind]="ind=$ind, i=$i et rang_gen=$rang_gen";
			for($m=1;$m<=$nb_colonnes;$m++) {
				my_echo("\$tmp_col[$m][$i]=\$col[$m][$ind+$corr]=".$col[$m][$ind+$corr]."<br />");
				$tmp_col[$m][$i]=$col[$m][$ind+$corr];
				//$tmp_col_csv[$m][$ind]=$col_csv[$m][$ind];

			}
			$i++;
		}
		my_echo("</td></tr>");
		my_echo("</table>");

		// On r�affecte les valeurs dans le tableau initial � l'aide du tableau temporaire
		if((isset($_POST['sens_tri']))&&($_POST['sens_tri']=="inverse")) {
			for($m=1;$m<=$nb_colonnes;$m++) {
				for($i=0;$i<$nb_lignes_tableau;$i++) {
					$col[$m][$i+$corr]=$tmp_col[$m][$nb_lignes_tableau-1-$i];
					//$col_csv[$m][$i]=$tmp_col_csv[$m][$nombre_eleves-1-$i];
				}
			}
		}
		else {
			for($m=1;$m<=$nb_colonnes;$m++) {
				//$col[$m]=$tmp_col[$m];
				//$col_csv[$m]=$tmp_col_csv[$m];
				// Pour ne pas perdre les lignes de moyennes de classe
				for($i=0;$i<$nb_lignes_tableau;$i++) {
					$col[$m][$i+$corr]=$tmp_col[$m][$i];
					//$col_csv[$m][$i]=$tmp_col_csv[$m][$i];
				}
			}
		}
	}
}
//=========================

//===============================

$nb_lignes_tableau = $nb_lignes_tableau + 3 + $ligne_supl;

function affiche_tableau_csv2($nombre_lignes, $nb_col, $ligne1, $col, $col_csv) {
	$chaine="";
	$j = 1;
	while($j < $nb_col+1) {
		if($j>1){
			//echo ";";
			$chaine.=";";
		}
		//echo $ligne1[$j];
		$chaine.=$ligne1[$j];
		$j++;
	}
	//echo "<br />";
	//echo "\n";
	$chaine.="\n";

	$i = "0";
	while($i < $nombre_lignes) {
		$j = 1;
		while($j < $nb_col+1) {
			if($j>1){
				//echo ";";
				$chaine.=";";
			}
			//echo $col[$j][$i];
			if(isset($col_csv[$j][$i])) {
				$chaine.=$col_csv[$j][$i];
			}
			else {
				$chaine.=$col[$j][$i];
			}
			$j++;
		}
		//echo "<br />";
		//echo "\n";
		$chaine.="\n";
		$i++;
	}
	return $chaine;
}

if(isset($_GET['mode'])) {
	if($_GET['mode']=="csv") {
		$classe = sql_query1("SELECT classe FROM classes WHERE id = '$id_classe'");

		if ($referent == "une_periode") {
			$chaine_titre="Classe_".$classe."_Resultats_".$nom_periode[$num_periode]."_Annee_scolaire_".getSettingValue("gepiYear");
		} else {
			$chaine_titre="Classe_".$classe."_Resultats_Moyennes_annuelles_Annee_scolaire_".getSettingValue("gepiYear");
		}

		$now = gmdate('D, d M Y H:i:s') . ' GMT';

		$nom_fic=$chaine_titre."_".$now.".csv";

		// Filtrer les caract�res dans le nom de fichier:
		$nom_fic=preg_replace("/[^a-zA-Z0-9_.-]/","",remplace_accents($nom_fic,'all'));

		/*
		header('Content-Type: text/x-csv');
		header('Expires: ' . $now);
		// lem9 & loic1: IE need specific headers
		if (my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
			header('Content-Disposition: inline; filename="' . $nom_fic . '"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
			header('Pragma: no-cache');
		}
		*/
		send_file_download_headers('text/x-csv',$nom_fic);

		$fd="";
		$fd.=affiche_tableau_csv2($nb_lignes_tableau, $nb_col, $ligne1_csv, $col, $col_csv);
		echo $fd;
		die();
	}
}

//**************** EN-TETE *****************
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//=================================================
if($vtn_coloriser_resultats=='y') {
	//check_token();
	$sql="DELETE FROM preferences WHERE login='".$_SESSION['login']."' AND name LIKE 'vtn_%';";
	$del=mysql_query($sql);

	foreach($vtn_couleur_texte as $key => $value) {
		$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='vtn_couleur_texte$key', value='$value';";
		$insert=mysql_query($sql);
	}
	foreach($vtn_couleur_cellule as $key => $value) {
		$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='vtn_couleur_cellule$key', value='$value';";
		$insert=mysql_query($sql);
	}
	foreach($vtn_borne_couleur as $key => $value) {
		$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='vtn_borne_couleur$key', value='$value';";
		$insert=mysql_query($sql);
	}
}
//=================================================
$sql="DELETE FROM preferences WHERE name LIKE 'vtn_pref_%' AND login='".$_SESSION['login']."';";
$del=mysql_query($sql);

//$tab_pref=array('num_periode', 'larg_tab', 'bord', 'couleur_alterne', 'aff_abs', 'aff_reg', 'aff_doub', 'aff_date_naiss', 'aff_rang');
$tab_pref=array('num_periode', 'larg_tab', 'bord', 'couleur_alterne', 'aff_abs', 'aff_reg', 'aff_doub', 'aff_rang');
for($loop=0;$loop<count($tab_pref);$loop++) {
	$tmp_var=$tab_pref[$loop];
	if($$tmp_var=='') {$$tmp_var="n";}
	$sql="INSERT INTO preferences SET name='vtn_pref_".$tmp_var."', value='".$$tmp_var."', login='".$_SESSION['login']."';";
	//echo "$sql<br />";
	$insert=mysql_query($sql);
	$_SESSION['vtn_pref_'.$tmp_var]=$$tmp_var;
}
$sql="INSERT INTO preferences SET name='vtn_pref_coloriser_resultats', value='$vtn_coloriser_resultats', login='".$_SESSION['login']."';";
$insert=mysql_query($sql);
$_SESSION['vtn_pref_coloriser_resultats']=$vtn_coloriser_resultats;
//=================================================

$classe = sql_query1("SELECT classe FROM classes WHERE id = '$id_classe'");

// Lien pour g�n�rer un CSV
echo "<div class='noprint' style='float: right; border: 1px solid black; background-color: white; width: 7em; height: 1em; text-align: center; padding-bottom:3px;'>
<a href='".$_SERVER['PHP_SELF']."?mode=csv&amp;id_classe=$id_classe&amp;num_periode=$num_periode";

if($aff_abs){
	echo "&amp;aff_abs=$aff_abs";
}
if($aff_reg){
	echo "&amp;aff_reg=$aff_reg";
}
if($aff_doub){
	echo "&amp;aff_doub=$aff_doub";
}
if($aff_rang){
	echo "&amp;aff_rang=$aff_rang";
}
if($aff_date_naiss){
	echo "&amp;aff_date_naiss=$aff_date_naiss";
}
echo add_token_in_url();
echo "'>Export CSV</a>
</div>\n";

$la_date=date("d/m/Y H:i");

if($vtn_coloriser_resultats=='y') {
	echo "<div class='noprint' style='float: right; width: 10em; text-align: center; padding-bottom:3px;'>\n";

	echo "<p class='bold' style='text-align:center;'>L�gende de la colorisation</p>\n";
	$legende_colorisation="<table class='boireaus' summary='L�gende de la colorisation'>\n";
	$legende_colorisation.="<thead>\n";
		$legende_colorisation.="<tr>\n";
		$legende_colorisation.="<th>Borne<br />sup�rieure</th>\n";
		$legende_colorisation.="<th>Couleur texte</th>\n";
		$legende_colorisation.="<th>Couleur cellule</th>\n";
		$legende_colorisation.="</tr>\n";
	$legende_colorisation.="</thead>\n";
	$legende_colorisation.="<tbody>\n";
	$alt=1;
	foreach($vtn_borne_couleur as $key => $value) {
		$alt=$alt*(-1);
		$legende_colorisation.="<tr class='lig$alt'>\n";
		$legende_colorisation.="<td>$vtn_borne_couleur[$key]</td>\n";
		$legende_colorisation.="<td style='color:$vtn_couleur_texte[$key]'>$vtn_couleur_texte[$key]</td>\n";
		$legende_colorisation.="<td style='color:$vtn_couleur_cellule[$key]'>$vtn_couleur_cellule[$key]</td>\n";
		$legende_colorisation.="</tr>\n";
	}
	$legende_colorisation.="</tbody>\n";
	$legende_colorisation.="</table>\n";

	echo $legende_colorisation;
echo "</div>\n";
}

echo "<div>\n";

if ($referent == "une_periode") {
	//echo "<p class=bold>Classe : $classe - R�sultats : $nom_periode[$num_periode] - Ann�e scolaire : ".getSettingValue("gepiYear")."</p>";
	echo "<p class='bold'>Classe : $classe - Moyennes du carnet de notes du $nom_periode[$num_periode] - (<i>".$la_date."</i>) - ".getSettingValue("gepiYear")."</p>\n";
}
else {
	//echo "<p class=bold>Classe : $classe - R�sultats : Moyennes annuelles - Ann�e scolaire : ".getSettingValue("gepiYear")."</p>";
	echo "<p class='bold'>Classe : $classe - Moyennes annuelles du carnet de notes - (<i>".$la_date."</i>) - ".getSettingValue("gepiYear")."</p>\n";
}

//affiche_tableau($nb_lignes_tableau, $nb_col, $ligne1, $col, $larg_tab, $bord,0,1,$couleur_alterne);

function affiche_tableau2($nombre_lignes, $nb_col, $ligne1, $col, $larg_tab, $bord, $col1_centre, $col_centre, $couleur_alterne) {
	// $col1_centre = 1 --> la premi�re colonne est centr�e
	// $col1_centre = 0 --> la premi�re colonne est align�e � gauche
	// $col_centre = 1 --> toutes les autres colonnes sont centr�es.
	// $col_centre = 0 --> toutes les autres colonnes sont align�es.
	// $couleur_alterne --> les couleurs de fond des lignes sont altern�s
	global $num_debut_colonnes_matieres, $num_debut_lignes_eleves, $vtn_coloriser_resultats, $vtn_borne_couleur, $vtn_couleur_texte, $vtn_couleur_cellule;

	echo "<table summary=\"Moyennes des carnets de notes\" class='boireaus' border=\"$bord\" cellspacing=\"0\" width=\"$larg_tab\" cellpadding=\"1\">\n";
	echo "<tr>\n";
	$j = 1;
	while($j < $nb_col+1) {
		//echo "<th class='small'>$ligne1[$j]</th>\n";
		echo "<th class='small' id='td_ligne1_$j'>$ligne1[$j]</th>\n";
		$j++;
	}
	echo "</tr>\n";
	$i = "0";
	$bg_color = "";
	$flag = "1";
	$alt=1;
	while($i < $nombre_lignes) {
        if((isset($couleur_alterne))&&($couleur_alterne=='y')) {
			if ($flag==1) $bg_color = "bgcolor=\"#C0C0C0\""; else $bg_color = "     " ;
		}

	    $alt=$alt*(-1);
        echo "<tr class='";
        if((isset($couleur_alterne))&&($couleur_alterne=='y')) {echo "lig$alt ";}
		echo "white_hover'>\n";
		$j = 1;
		while($j < $nb_col+1) {
			if ((($j == 1) and ($col1_centre == 0)) or (($j != 1) and ($col_centre == 0))){
				echo "<td class='small' ";
				//echo $bg_color;
				if(($vtn_coloriser_resultats=='y')&&($j>=$num_debut_colonnes_matieres)&&($i>=$num_debut_lignes_eleves)) {
					if(strlen(preg_replace('/[0-9.,]/','',$col[$j][$i]))==0) {
						for($loop=0;$loop<count($vtn_borne_couleur);$loop++) {
							if(preg_replace('/,/','.',$col[$j][$i])<=preg_replace('/,/','.',$vtn_borne_couleur[$loop])) {
								echo " style='";
								if($vtn_couleur_texte[$loop]!='') {echo "color:$vtn_couleur_texte[$loop]; ";}
								if($vtn_couleur_cellule[$loop]!='') {echo "background-color:$vtn_couleur_cellule[$loop]; ";}
								echo "'";
								break;
							}
						}
					}
				}
				echo ">{$col[$j][$i]}</td>\n";
			} else {
				echo "<td align=\"center\" class='small' ";
				//echo $bg_color;
				if(($vtn_coloriser_resultats=='y')&&($j>=$num_debut_colonnes_matieres)&&($i>=$num_debut_lignes_eleves)) {
					if(strlen(preg_replace('/[0-9.,]/','',$col[$j][$i]))==0) {
						for($loop=0;$loop<count($vtn_borne_couleur);$loop++) {
							if(preg_replace('/,/','.',$col[$j][$i])<=preg_replace('/,/','.',$vtn_borne_couleur[$loop])) {
								echo " style='";
								if($vtn_couleur_texte[$loop]!='') {echo "color:$vtn_couleur_texte[$loop]; ";}
								if($vtn_couleur_cellule[$loop]!='') {echo "background-color:$vtn_couleur_cellule[$loop]; ";}
								echo "'";
								break;
							}
						}
					}
				}
				echo ">{$col[$j][$i]}</td>\n";
			}
			$j++;
		}
		echo "</tr>\n";
		if ($flag == "1") $flag = "0"; else $flag = "1";
		$i++;
	}
	echo "</table>\n";
}

affiche_tableau2($nb_lignes_tableau, $nb_col, $ligne1, $col, $larg_tab, $bord,0,1,$couleur_alterne);

if($vtn_coloriser_resultats=='y') {
	echo "<p class='bold'>L�gende de la colorisation&nbsp;:</p>\n";
	echo $legende_colorisation;
}

echo "<div class='noprint'>\n";
echo "<p><b>Attention:</b> Les moyennes visualis�es ici sont des photos � un instant t de ce qui a �t� saisi par les professeurs.<br />\n";
echo "Cela ne correspond pas n�cessairement � ce qui apparaitra sur le bulletin apr�s saisie d'autres r�sultats et ajustements �ventuels des coefficients.</p>\n";
echo "</div>\n";

//=======================================================
// MODIF: boireaus 20080424
// Pour permettre de trier autrement...
echo "\n<!-- Formulaire pour l'affichage avec tri sur la colonne cliqu�e -->\n";
echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" name=\"formulaire_tri\">\n";
echo add_token_field();
echo "<input type='hidden' name='col_tri' id='col_tri' value='' />\n";
echo "<input type='hidden' name='sens_tri' id='sens_tri' value='' />\n";

echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";

if(isset($_POST['aff_abs'])) {
	echo "<input type='hidden' name='aff_abs' value='".$_POST['aff_abs']."' />\n";
}
if(isset($_POST['aff_reg'])) {
	echo "<input type='hidden' name='aff_reg' value='".$_POST['aff_reg']."' />\n";
}
if(isset($_POST['aff_doub'])) {
	echo "<input type='hidden' name='aff_doub' value='".$_POST['aff_doub']."' />\n";
}
if(isset($_POST['aff_rang'])) {
	echo "<input type='hidden' name='aff_rang' value='".$_POST['aff_rang']."' />\n";
}
if(isset($_POST['aff_date_naiss'])) {
	echo "<input type='hidden' name='aff_date_naiss' value='".$_POST['aff_date_naiss']."' />\n";
}

if($vtn_coloriser_resultats=='y') {
	echo "<input type='hidden' name='vtn_coloriser_resultats' value='$vtn_coloriser_resultats' />\n";
	foreach($vtn_couleur_texte as $key => $value) {
		echo "<input type='hidden' name='vtn_couleur_texte[$key]' value='$value' />\n";
	}
	foreach($vtn_couleur_cellule as $key => $value) {
		echo "<input type='hidden' name='vtn_couleur_cellule[$key]' value='$value' />\n";
	}
	foreach($vtn_borne_couleur as $key => $value) {
		echo "<input type='hidden' name='vtn_borne_couleur[$key]' value='$value' />\n";
	}
}

echo "<input type='hidden' name='larg_tab' value='$larg_tab' />\n";
echo "<input type='hidden' name='bord' value='$bord' />\n";
echo "<input type='hidden' name='couleur_alterne' value='$couleur_alterne' />\n";

echo "</form>\n";

if(isset($col_tri)) {
	echo "<script type='text/javascript'>
	if(document.getElementById('td_ligne1_$col_tri')) {
		document.getElementById('td_ligne1_$col_tri').style.backgroundColor='white';
	}
</script>\n";
}
//=======================================================

require("../lib/footer.inc.php");
?>
