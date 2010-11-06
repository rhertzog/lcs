<?php
/*
*
* $Id: visu_releve_notes_bis.php 5689 2010-10-15 19:23:18Z crob $
*
* Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, St�phane Boireau, Christian Chapel
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
}


$sql="SELECT 1=1 FROM droits WHERE id='/cahier_notes/visu_releve_notes_bis.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/cahier_notes/visu_releve_notes_bis.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V','F', 'Relev� de notes', '1');";
	$res_insert=mysql_query($sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//================================

//+++++++++++++++++++++++++
// A FAIRE:
// Ajouter un t�moin pour ne pas g�n�rer d'affichage pouvant emp�cher la g�n�ration de relev� PDF...
//+++++++++++++++++++++++++

$contexte_document_produit="releve_notes";
/*
$deux_releves_par_page=isset($_POST('deux_releves_par_page']) ? $_POST('deux_releves_par_page'] : "non";

if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
	$deux_releves_par_page="non";
	//$un_seul_bull_par_famille="oui";
}
*/

//====================================================
//=============== ENTETE STANDARD ====================
//if (!isset($_POST['valide_select_eleves'])) {
if(!isset($_POST['choix_parametres'])) {
	//**************** EN-TETE *********************
	$titre_page = "Visualisation relev� de notes";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************
}
//============== FIN ENTETE STANDARD =================
//====================================================
//============== ENTETE BULLETIN HTML ================
elseif ((isset($_POST['mode_bulletin']))&&($_POST['mode_bulletin']=='html')) {
	include("header_releve_html.php");
	//debug_var();
}
//============ FIN ENTETE BULLETIN HTML ==============
//====================================================
//============== ENTETE BULLETIN PDF ================
elseif ((isset($_POST['mode_bulletin']))&&($_POST['mode_bulletin']=='pdf')) {
	$mode_utf8_pdf=getSettingValue("mode_utf8_bulletins_pdf");
	if($mode_utf8_pdf=="") {$mode_utf8_pdf="n";}
	include("../bulletin/header_bulletin_pdf.php");
	include("../bulletin/header_releve_pdf.php");
}
//============ FIN ENTETE BULLETIN HTML ==============
//====================================================

//echo "microtime()=".microtime()."<br />";
//echo "time()=".time()."<br />";

$debug="n";
$tab_instant=array();
include("visu_releve_notes_func.lib.php");

//=========================
// Classes s�lectionn�es:
$tab_id_classe=isset($_POST['tab_id_classe']) ? $_POST['tab_id_classe'] : NULL;

// P�riode:
// $choix_periode='periode' ou 'intervalle'
$choix_periode=isset($_POST['choix_periode']) ? $_POST['choix_periode'] : NULL;
// Si $choix_periode='periode'
//$periode=isset($_POST['periode']) ? $_POST['periode'] : NULL;
$tab_periode_num=isset($_POST['tab_periode_num']) ? $_POST['tab_periode_num'] : NULL;
// Si $choix_periode='intervalle'
$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : NULL;
$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : NULL;

$choix_parametres=isset($_POST['choix_parametres']) ? $_POST['choix_parametres'] : NULL;

// Un prof peut choisir un groupe plut�t qu'une liste de classes
$id_groupe=($_SESSION['statut']=='professeur') ? (isset($_POST['id_groupe']) ? $_POST['id_groupe'] : NULL) : NULL;
if(isset($id_groupe)) {
	if(($id_groupe=='')||(strlen(my_ereg_replace("[0-9]","",$id_groupe))!=0)) {
		tentative_intrusion(2, "Tentative d'un professeur de manipuler l'identifiant id_groupe en y mettant des caract�res non num�riques ou un identifiant de groupe vide.");
		echo "<p>L'identifiant de groupe est erron�.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	// On v�rifie si le prof est bien associ� au groupe
	$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' AND login='".$_SESSION['login']."';";
	$test_grp_prof=mysql_query($sql);
	if(mysql_num_rows($test_grp_prof)==0) {
		//intrusion
		tentative_intrusion(2, "Tentative d'un professeur d'acc�der aux relev�s de notes d'un groupe auquel il n'est pas associ�.");
		echo "<p>Vous n'�tes pas associ� au groupe choisi.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}
	//echo "id_groupe=$id_groupe<br />\n";
}
//=========================


//=========================
// A FAIRE

// CAS D'UN ELEVE
// - Pr�remplir la classe pour sauter l'�tape? sauf s'il a chang� de classe?
// - Permettre de choisir la p�riode
// - Pr�remplir la s�lection d'�l�ves pour sauter l'�tape (en renseignant un champ cach�) et en cas de manipulation (vider le champ) interdire l'acc�s �l�ve dans la section de choix des �l�ves
// Contr�ler avant et apr�s validation des param�tres si les champs propos�s/re�us sont corrects (conforme � ce qu'on autorise � l'�l�ve)

// CAS D'UN RESPONSABLE
// Si un seul enfant, cf CAS D'UN ELEVE
// Si plusieurs, permettre de choisir l'enfant et remplir la classe en cons�quence...
// ...


if($_SESSION['statut']=='eleve') {
	if(getSettingValue("GepiAccesReleveEleve") != "yes") {
		echo "<p>Vous n'�tes pas autoris� � acc�der aux relev�s de notes.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (jec.id_classe=c.id AND jec.login='".$_SESSION['login']."');";
	$test_ele_clas=mysql_query($sql);
	if(mysql_num_rows($test_ele_clas)==0) {
		echo "<p>Vous n'�tes pas affect� dans une classe et donc pas autoris� � acc�der aux relev�s de notes.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	elseif(mysql_num_rows($test_ele_clas)==1) {
		// On pr�remplit la classe
		$lig_clas=mysql_fetch_object($test_ele_clas);
		$tab_id_classe=array();
		$tab_id_classe[]=$lig_clas->id;
		//echo "\$lig_clas->id=$lig_clas->id<br />";
	}
	else {
		// C'est un �l�ve qui a chang� de classe en cours d'ann�e.
		// Il faut laisser faire le choix de la classe ou des classes
	}
}
elseif($_SESSION['statut']=='responsable') {
	if(getSettingValue("GepiAccesReleveParent") != "yes") {
		echo "<p>Vous n'�tes pas autoris� � acc�der aux relev�s de notes.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT DISTINCT c.*  FROM eleves e, j_eleves_classes jec, classes c, responsables2 r, resp_pers rp
			WHERE (e.ele_id=r.ele_id AND
					r.pers_id=rp.pers_id AND
					rp.login='".$_SESSION['login']."' AND
					c.id=jec.id_classe AND
					jec.login=e.login);";
	$test_ele_clas=mysql_query($sql);
	if(mysql_num_rows($test_ele_clas)==0) {
		echo "<p>Aucun des �l�ves dont vous �tes responsable ne semble inscrit dans une classe; vous n'�tes donc pas autoris� � acc�der aux relev�s de notes.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	elseif(mysql_num_rows($test_ele_clas)==1) {
		// On pr�remplit la classe
		$lig_clas=mysql_fetch_object($test_ele_clas);
		$tab_id_classe=array();
		$tab_id_classe[]=$lig_clas->id;
		//echo "\$lig_clas->id=$lig_clas->id<br />";
	}
	else {
		// Le ou les enfants du responsable sont dans plusieurs classes ou l'�l�ve a chang� de classe
		// Il faut laisser faire le choix de la classe ou des classes
	}
}
//=========================

//======================================================
//==================CHOIX DES CLASSES===================
//if(!isset($tab_id_classe)) {
// On contr�le plus haut que $id_groupe=NULL si on n'est pas prof
if ((!isset($tab_id_classe))&&(!isset($id_groupe))) {
	echo "<p class='bold'>";
	if($_SESSION['statut']=='professeur') {
		echo "<a href='index.php'>Retour</a>";
	}
	elseif(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='administrateur')) {
		echo "<a href='../accueil.php'>Retour � l'accueil</a>";
		echo " | ";
		echo "<a href='param_releve_html.php' target='_blank'>Param�tres du relev� HTML</a>";
		echo " | ";
		echo "<a href='visu_releve_notes.php'>Ancien dispositif</a>";
	}
	else {
		echo "<a href='../accueil.php'>Retour � l'accueil</a>";
	}
	echo "</p>\n";

	echo "<p class='bold'>Choix des classes";
	if ($_SESSION['statut']=='professeur') {echo " ou d'un groupe";}
	echo ":</p>\n";

	//==============================
	// A REVOIR:
	// Laiss� pour le moment, parce que le cas PROF n'a pas �t� trait�:
	//$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE c.id=jec.id_classe ORDER BY c.classe;";
	// Pas plus que le cas Eleve ou Responsable

	// Et un prof doit pouvoir lister les relev�s de notes associ�s � ses groupes...
	//==============================

	if (($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") == "yes")) {
		$sql="SELECT DISTINCT c.* FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif(($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpe") == "yes")) {
		$sql="SELECT DISTINCT c.* FROM classes c,
										j_eleves_classes jecl,
										j_eleves_cpe jec
								WHERE (
										c.id=jecl.id_classe AND
										jecl.login=jec.e_login AND
										jec.cpe_login='".$_SESSION['login']."'
									)
								ORDER BY classe";
		// A REVOIR:
		// Les droits ne sont pas corrects:
		// - on restreint scolarit� � ses classes alors que GepiAccesReleveScol correspond dans Droits d'acc�s � Toutes les classes
		// - on ne restreint pas le CPE de semblable fa�on aux �l�ves dont il est responsable -> CORRIG�
	}
	elseif($_SESSION['statut']=='professeur') {
		// GepiAccesReleveProf               -> que les �l�ves de ses groupes
		// GepiAccesReleveProfTousEleves     -> tous les �l�ves de ses classes
		// GepiAccesReleveProfToutesClasses  -> tous les �l�ves de toutes les classes


		// GepiAccesReleveProfToutesClasses  -> tous les �l�ves de toutes les classes
		if(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes") {
			$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE c.id=jec.id_classe ORDER BY c.classe;";
		}
		elseif ((getSettingValue("GepiAccesReleveProf") == "yes")||(getSettingValue("GepiAccesReleveProfTousEleves") == "yes")) {
			// A ce stade on ne r�cup�re pas les �l�ves, mais seulement les classes:
			// GepiAccesReleveProf               -> que les �l�ves de ses groupes
			// GepiAccesReleveProfTousEleves     -> tous les �l�ves de ses classes
			$sql="SELECT DISTINCT c.* FROM j_groupes_classes jgc,
											j_groupes_professeurs jgp,
											classes c
									WHERE (
										c.id=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='".$_SESSION['login']."'
										)
									ORDER BY c.classe;";
		}
		elseif(getSettingValue("GepiAccesReleveProfP")=="yes") {
			$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."' LIMIT 1;";
			$test_acces=mysql_query($sql);
			if(mysql_num_rows($test_acces)>0) {
				$sql="SELECT DISTINCT c.* FROM j_eleves_professeurs jep,
											classes c
									WHERE (
										c.id=jep.id_classe AND
										jep.professeur='".$_SESSION['login']."'
										)
									ORDER BY c.classe;";
			}
			else {
				echo "<p>Vous n'�tes pas ".getSettingValue("gepi_prof_suivi").", donc pas autoris� � acc�der aux relev�s de notes des �l�ves.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
		else {
			echo "<p>Vous n'�tes pas autoris� � acc�der aux relev�s de notes des �l�ves.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
	elseif(($_SESSION['statut']=='eleve')&&(getSettingValue("GepiAccesReleveEleve") == "yes")) {
		// Un �l�ve qui change de classe peut avoir plusieurs classes o� retrouver ses notes
		$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe AND jec.login='".$_SESSION['login']."') ORDER BY c.classe;";
	}
	elseif(($_SESSION['statut']=='responsable')&&(getSettingValue("GepiAccesReleveParent") == "yes")) {
		$sql="SELECT DISTINCT c.*  FROM eleves e, j_eleves_classes jec, classes c, responsables2 r, resp_pers rp
				WHERE (e.ele_id=r.ele_id AND
						r.pers_id=rp.pers_id AND
						rp.login='".$_SESSION['login']."' AND
						c.id=jec.id_classe AND
						jec.login=e.login);";
	}
	else {
		echo "<p>Vous n'�tes pas autoris� � acc�der aux relev�s de notes des �l�ves.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	//echo "$sql<br />";
	$call_classes=mysql_query($sql);

	$nb_classes=mysql_num_rows($call_classes);
	if($nb_classes==0){
		echo "<p>Aucune classe avec �l�ve affect� n'a �t� trouv�e.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	// Affichage sur 3 colonnes
	$nb_classes_par_colonne=round($nb_classes/3);

	echo "<table width='100%' summary='Tableau de choix des classes'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	while($lig_clas=mysql_fetch_object($call_classes)) {

		//affichage 2 colonnes
		if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='tab_id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange='unCheckRadio();change_style_classe($cpt)' /> $lig_clas->classe</label>";
		echo "<br />\n";
		$cpt++;
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

echo "<script type='text/javascript'>
	function change_style_classe(num) {
		if(document.getElementById('tab_id_classe_'+num)) {
			if(document.getElementById('tab_id_classe_'+num).checked) {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}
</script>\n";

	echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout d�cocher</a></p>\n";


	$nb_grp_prof=0;
	if(($_SESSION['statut']=='professeur')&&((getSettingValue("GepiAccesReleveProfToutesClasses") == "yes")||(getSettingValue("GepiAccesReleveProf") == "yes")||(getSettingValue("GepiAccesReleveProfTousEleves") == "yes"))) {
		echo "<p><b>Alternativement</b>, vous pouvez choisir un groupe:</p>\n";

		$groupes_prof=get_groups_for_prof($_SESSION['login']);
		$nb_grp_prof=count($groupes_prof);

		echo "<p>\n";
		for($i=0;$i<$nb_grp_prof;$i++) {
			echo "<input type='radio' name='id_groupe' id='id_groupe_".$i."' value='".$groupes_prof[$i]['id']."' /> ";
			echo "<label for='id_groupe_".$i."' style='cursor: pointer;'>\n";
			echo $groupes_prof[$i]['name'];
			echo "(<i>";
			if($groupes_prof[$i]['name']!=$groupes_prof[$i]["matiere"]['nom_complet']) {echo $groupes_prof[$i]["matiere"]['nom_complet']." en ";}
			echo $groupes_prof[$i]['classlist_string'];
			echo "</i>)";
			echo "</label>\n";
			echo "<br />\n";
		}
		echo "</p>\n";
	}

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}

		if(mode==true) {unCheckRadio();}
	}

	function unCheckRadio() {
		for (var k=0;k<$nb_grp_prof;k++) {
			if(document.getElementById('id_groupe_'+k)){
				document.getElementById('id_groupe_'+k).checked = false;
				change_style_classe(k);
			}
		}
	}
</script>\n";


}
//======================================================
//=================CHOIX DE LA PERIODE==================
elseif(!isset($choix_periode)) {

	echo "<p class='bold'>";
	if($_SESSION['statut']=='professeur') {
		echo "<a href='index.php'>Retour</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
	}
	elseif(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='administrateur')) {
		echo "<a href='../accueil.php'>Retour � l'accueil</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
		echo " | ";
		echo "<a href='param_releve_html.php' target='_blank'>Param�tres du relev� HTML</a>";
	}
	else {
		echo "<a href='../accueil.php'>Retour � l'accueil</a>";
	}
	echo "</p>\n";

	// Choisir les p�riodes permettant l'�dition des relev�s de notes

	if($_SESSION['statut']=="professeur") {
		if(isset($id_groupe)) {
			// On a fait un choix de groupe... on refait la liste des classes d'apr�s l'id_groupe choisi
			unset($tab_id_classe);
			$sql="SELECT DISTINCT id_classe FROM j_groupes_classes WHERE id_groupe='$id_groupe';";
			$res_clas=mysql_query($sql);
			if(mysql_num_rows($res_clas)==0) {
				echo "<p>ERREUR: Le groupe choisi ne semble associ� � aucune classe???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			while($lig_clas=mysql_fetch_object($res_clas)) {
				$tab_id_classe[]=$lig_clas->id_classe;
			}
		}
	}

	echo "<p class='bold'>Choisissez la p�riode d'affichage pour ";
	if(isset($id_groupe)) {
		$current_group=get_group($id_groupe);
		echo $current_group['name'];
		echo "(<i>";
		if($current_group['name']!=$current_group['matiere']['nom_complet']) {echo $current_group['matiere']['nom_complet']." en ";}
		echo $current_group['classlist_string'];
		echo "</i>)";

	}
	else {
		//echo "la ou les classes ";
		if(count($tab_id_classe)==1) {
			echo "la classe ";
		}
		else {
			echo "les classes ";
		}
		for($i=0;$i<count($tab_id_classe);$i++) {
			if($i>0) {echo ", ";}
			echo get_class_from_id($tab_id_classe[$i]);
		}
	}
	echo ":</p>\n";
	//debug_var();
	//=======================
	//Configuration du calendrier
	include("../lib/calendrier/calendrier.class.php");
	//$cal1 = new Calendrier("form_choix_edit", "display_date_debut");
	//$cal2 = new Calendrier("form_choix_edit", "display_date_fin");
	$cal1 = new Calendrier("formulaire", "display_date_debut");
	$cal2 = new Calendrier("formulaire", "display_date_fin");
	//=======================

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	echo "<table border='0' summary='Tableau de choix des p�riodes'>\n";
	echo "<tr valign='top'>\n";
	echo "<td>\n";
    echo "<a name=\"calend\"></a>";
	//echo "<input type=\"radio\" name=\"choix_periode\" id='choix_periode_dates' value=\"intervalle\" checked />";
	echo "<input type=\"radio\" name=\"choix_periode\" id='choix_periode_dates' value=\"intervalle\" ";
	// Dans le cas d'un retour en arri�re, le champ peut avoir �t� pr�alablement coch�
	//if((!isset($choix_periode))||($choix_periode=="intervalle")) {
	if(!isset($tab_periode_num)) {
		echo "checked ";
	}
	echo "/>";
	echo "</td>\n";


	//=======================
	// Pour �viter de refaire le choix des dates en changeant de classe, on utilise la SESSION...
	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");

	if($mois>7) {$date_debut_tmp="01/09/$annee";} else {$date_debut_tmp="01/09/".($annee-1);}

	//$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $jour."/".$mois."/".$annee);
	$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $date_debut_tmp);

	$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : (isset($_SESSION['display_date_fin']) ? $_SESSION['display_date_fin'] : $jour."/".$mois."/".$annee);
	//=======================


	echo "<td>\n";
	echo "<label for='choix_periode_dates' style='cursor: pointer;'> \nDe la date : </label>";

    echo "<input type='text' name = 'display_date_debut' size='10' value = \"".$display_date_debut."\" onfocus=\"document.getElementById('choix_periode_dates').checked=true;\" />";
    echo "<label for='choix_periode_dates' style='cursor: pointer;'><a href=\"#calend\" onClick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";

    echo "&nbsp;� la date : </label>";
    echo "<input type='text' name = 'display_date_fin' size='10' value = \"".$display_date_fin."\" onfocus=\"document.getElementById('choix_periode_dates').checked=true;\" />";
    echo "<label for='choix_periode_dates' style='cursor: pointer;'><a href=\"#calend\" onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	echo "<br />\n";
    echo " (<i>Veillez � respecter le format jj/mm/aaaa</i>)</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr valign='top'>\n";
	echo "<td colspan='2'>\n";
	echo "Ou";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr valign='top'>\n";
	echo "<td>\n";

	echo "<input type=\"radio\" name=\"choix_periode\" id='choix_periode' value='periode' ";
	// Dans le cas d'un retour en arri�re, le champ peut avoir �t� pr�alablement coch�
	//if((isset($choix_periode))&&($choix_periode=="periode")) {
	if(isset($tab_periode_num)) {
		echo "checked ";
	}
	echo "/><label for='choix_periode' style='cursor: pointer;'> <b>P�riode</b></label>\n";

	echo "</td>\n";
	echo "<td>\n";

		$sql="SELECT MAX(num_periode) max_per FROM periodes;";
		$res_max_per=mysql_query($sql);
		$lig_max_per=mysql_fetch_object($res_max_per);
		$max_per=$lig_max_per->max_per;

		$tab_periode_exclue=array();

		$tab_nom_periode=array();

		echo "<table class='boireaus' border='1' summary='Tableau de choix des p�riodes'>\n";
		echo "<tr>\n";
		echo "<th>Classe</th>\n";
		for($j=1;$j<=$max_per;$j++) {
			echo "<th>P�riode $j</th>\n";
		}
		echo "</tr>\n";
		$alt=1;
		for($i=0;$i<count($tab_id_classe);$i++) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>".get_class_from_id($tab_id_classe[$i])."\n";
			echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";
			echo "</td>\n";
			for($j=1;$j<=$max_per;$j++) {
				if(!isset($tab_nom_periode[$j])) {$tab_nom_periode[$j]=array();}

				$sql="SELECT * FROM periodes WHERE num_periode='$j' AND id_classe='".$tab_id_classe[$i]."';";
				$res_per=mysql_query($sql);
				if(mysql_num_rows($res_per)==0) {
					if(!in_array($j,$tab_periode_exclue)) {$tab_periode_exclue[]=$j;}
					echo "<td style='background-color:red;'>X</td>\n";
				}
				else {
					$lig_per=mysql_fetch_object($res_per);
					if(!in_array($lig_per->nom_periode,$tab_nom_periode[$j])) {$tab_nom_periode[$j][]=$lig_per->nom_periode;}
					echo "<td>";
					if($lig_per->verouiller=="O") {
						echo "Close";
					}
					elseif($lig_per->verouiller=="N") {
						echo "Non close";
					}
					else {
						echo "Partiellement close";
					}
					echo "</td>\n";
				}
			}
			echo "</tr>\n";
		}

		echo "<tr>\n";
		echo "<th>Choix</th>\n";
		for($j=1;$j<=$max_per;$j++) {
			if(!in_array($j,$tab_periode_exclue)) {
				echo "<td style='background-color:lightgreen;'>";
				//echo "<label for='choix_periode' style='cursor: pointer;'><input type=\"radio\" name=\"periode\" value='$j' /></label>\n";
				echo "<label for='choix_periode' style='cursor: pointer;'><input type=\"checkbox\" name=\"tab_periode_num[]\" value='$j' ";
				// Dans le cas d'un retour en arri�re, le champ peut avoir �t� pr�alablement coch�
				if((isset($tab_periode_num))&&(in_array($j,$tab_periode_num))) {
					echo "checked ";
				}
				echo "onchange=\"document.getElementById('choix_periode').checked=true\" ";
				echo "/></label>\n";
			}
			else {
				echo "<td style='background-color:red;'>&nbsp;";
			}
			echo "</td>\n";
		}
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<th>Classe</th>\n";
		for($j=1;$j<=$max_per;$j++) {
			echo "<th>";
			for($k=0;$k<count($tab_nom_periode[$j]);$k++) {
				if($k>0) {echo "<br />\n";}
				echo $tab_nom_periode[$j][$k];
			}
			echo "</th>\n";
		}
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	if(isset($id_groupe)) {
		// Cas d'un prof (on a forc� plus haut � NULL $id_groupe si on n'a pas affaire � un prof)
		echo "<input type='hidden' name='id_groupe' value='".$id_groupe."' />\n";
	}

	/*
	if($_SESSION['statut']=='eleve') {
		// Il faudrait remplir les champs tab_selection_ele_".$i."_".$j."[] en tenant compte de $choix_periode qui n'est pas encore POST�
		echo "<input type='hidden' name='valide_select_eleves' value='y' />\n";
	}
	*/

	echo "<p><input type='submit' name='valide_choix_periode' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";

	echo "<p><i>Remarque&nbsp;:</i></p>\n";
	echo "<blockquote>\n";
	echo "<p>Les relev�s d'une date � une autre ne font appara�tre que les mati�res dans lesquelles il y a des notes.<br />\n";
	echo "Les relev�s pour une p�riode compl�te en revanche font appara�tre toutes les mati�res, m�me si aucune note n'est saisie.</p>\n";
	echo "On choisit en g�n�ral la p�riode compl�te lorsqu'on veut imprimer un relev� en m�me temps que le bulletin (<i>au verso par exemple</i>) et en fin de p�riode, il est bon d'avoir toutes les mati�res.</p>\n";
	echo "</blockquote>\n";

}
//======================================================
//==============CHOIX DE LA SELECTION D'ELEVES==========
elseif(!isset($_POST['valide_select_eleves'])) {

	if(isset($display_date_debut)) {$_SESSION['display_date_debut']=$display_date_debut;}
	if(isset($display_date_fin)) {$_SESSION['display_date_fin']=$display_date_fin;}

	echo "<p class='bold'>";
	if($_SESSION['statut']=='professeur') {
		echo "<a href='index.php'>Retour</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."' onClick=\"document.forms['form_retour'].submit();return false;\">Choisir d'autres p�riodes</a>\n";
	}
	elseif(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='administrateur')) {
		echo "<a href='../accueil.php'>Retour � l'accueil</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."' onClick=\"document.forms['form_retour'].submit();return false;\">Choisir d'autres p�riodes</a>\n";
		echo " | ";
		echo "<a href='param_releve_html.php' target='_blank'>Param�tres du relev� HTML</a>";
	}
	else {
		echo "<a href='../accueil.php'>Retour � l'accueil</a>";
	}
	echo "</p>\n";

	//===========================
	// FORMULAIRE POUR LE RETOUR AU CHOIX DES PERIODES
	echo "\n<!-- Formulaire de retour au choix des p�riodes -->\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='form_retour'>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";
	}
	//echo "<input type='hidden' name='choix_periode' value='$choix_periode' />\n";
	if($choix_periode=='periode') {
		//echo "<input type='hidden' name='periode' value='$periode' />\n";
		for($j=0;$j<count($tab_periode_num);$j++) {
			echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";
		}
	}
	else {
		$periode="intervalle";
		echo "<input type='hidden' name='display_date_debut' value='$display_date_debut' />\n";
		echo "<input type='hidden' name='display_date_fin' value='$display_date_fin' />\n";
	}

	if(isset($id_groupe)) {
		// Cas d'un prof (on a forc� plus haut � NULL $id_groupe si on n'a pas affaire � un prof)
		echo "<input type='hidden' name='id_groupe' value='".$id_groupe."' />\n";
	}
	echo "</form>\n";
	//===========================

	echo "<p class='bold'>S�lection des �l�ves parmi les �l�ves de ";
	for($i=0;$i<count($tab_id_classe);$i++) {
		if($i>0) {echo ", ";}
		echo get_class_from_id($tab_id_classe[$i]);
	}
	if($choix_periode=='periode') {
		if(count($tab_periode_num)==1) {
			echo " pour la p�riode ".$tab_periode_num[0];
		}
		else {
			echo " pour les p�riodes ";
			for($j=0;$j<count($tab_periode_num);$j++) {
				if($j>0) {echo ", ";}
				echo $tab_periode_num[$j];
			}
		}
	}
	else {
		echo "<br />pour une extraction des notes entre le $display_date_debut et le $display_date_fin";
	}
	echo ":</p>\n";

	echo "\n<!-- Formulaire de choix des �l�ves et des param�tres -->\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire' target='_blank'>\n";
	//echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire' target='_blank' onsubmit='if(test_check_ele()) {document.forms['formulaire'].submit();}'>\n";

	//echo "<p><input type='submit' name='bouton_valide_select_eleves2' value='Valider' /></p>\n";
	echo "<p><input type='button' name='bouton_valide_select_eleves2' value='Valider' onclick='test_check_ele()' /></p>\n";

	// P�riode d'affichage:
	echo "<input type='hidden' name='choix_periode' value='$choix_periode' />\n";
	if($choix_periode=='periode') {
		//echo "<input type='hidden' name='periode' value='$periode' />\n";
		for($j=0;$j<count($tab_periode_num);$j++) {
			echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";
		}
	}
	else {
		$periode="intervalle";
		echo "<input type='hidden' name='display_date_debut' value='$display_date_debut' />\n";
		echo "<input type='hidden' name='display_date_fin' value='$display_date_fin' />\n";
	}






	//===========================================================
	//===========================================================
	//===========================================================

	//=======================================
	// A remplacer par la suite par un choix:
	//echo "<input type='hidden' name='mode_bulletin' value='html' />\n";
	//echo "<input type='hidden' name='un_seul_bull_par_famille' value='non' />\n";

	echo "<p><input type='radio' id='releve_html' name='mode_bulletin' value='html' checked /><label for='releve_html'> Relev� HTML</label><br />\n";
	echo "<input type='radio' id='releve_pdf' name='mode_bulletin' value='pdf' onchange='display_div_param_pdf();' /><label for='releve_pdf'> Relev� PDF</label></p>\n";

	echo "<div id='div_param_pdf'>\n";
		//echo "<br />\n";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for='use_cell_ajustee' style='cursor: pointer;'><input type='checkbox' name='use_cell_ajustee' id='use_cell_ajustee' value='n' /> Ne pas utiliser la nouvelle fonction use_cell_ajustee() pour l'�criture des appr�ciations.</label>";

		$titre_infobulle="Fonction cell_ajustee()\n";
		$texte_infobulle="Pour les appr�ciations sur les bulletins, relev�s,... on utilisait auparavant la fonction DraxTextBox() de FPDF.<br />Cette fonction avait parfois un comportement curieux avec des textes tronqu�s ou beaucoup plus petits dans la cellule que ce qui semblait pouvoir tenir dans la case.<br />La fonction cell_ajustee() est une fonction que mise au point pour tenter de faire mieux que DraxTextBox().<br />Comme elle n'a pas �t� exp�riment�e par suffisamment de monde sur trunk, nous avons mis une case � cocher qui permet d'utiliser l'ancienne fonction DrawTextBox() si cell_ajustee() ne se r�v�lait pas aussi bien fichue que nous l'esp�rons;o).<br />\n";
		//$texte_infobulle.="\n";
		$tabdiv_infobulle[]=creer_div_infobulle('a_propos_cell_ajustee',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_cell_ajustee','y',100,100);\"  onmouseout=\"cacher_div('a_propos_cell_ajustee');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";

		echo "<br />\n";
	echo "</div>\n";

	echo "<script type='text/javascript'>
	function display_div_param_pdf() {
		if(document.getElementById('div_param_pdf')) {
			if(document.getElementById('releve_pdf').checked==true) {
				document.getElementById('div_param_pdf').style.display='';
			}
			else {
				document.getElementById('div_param_pdf').style.display='none';
			}
		}
	}

	display_div_param_pdf();
</script>\n";

	//=======================================

	if ((($_SESSION['statut']=='eleve') AND (getSettingValue("GepiAccesOptionsReleveEleve") != "yes"))||
		(($_SESSION['statut']=='responsable') AND (getSettingValue("GepiAccesOptionsReleveParent") != "yes"))) {
		// T�moin destin� � sauter l'�tape des param�tres
		echo "<input type='hidden' name='choix_parametres' value='y' />\n";
		//echo "<input type='hidden' name='mode_bulletin' value='html' />\n";
		echo "<input type='hidden' name='un_seul_bull_par_famille' value='oui' />\n";

		echo "<input type='hidden' name='deux_releves_par_page' value='non' />\n";
	}
	else {
		echo "<p>\n";
		echo "<b>Param�tres:</b> \n";
		echo "<span id='pliage_param_releve'>\n";
		echo "(<i>";
		echo "<a href='#' onclick=\"document.getElementById('div_param_releve').style.display='';return false;\">Afficher</a>";
		echo " / \n";
		echo "<a href='#' onclick=\"document.getElementById('div_param_releve').style.display='none';return false;\">Masquer</a>";
		echo " les param�tres du relev� de notes</i>).";
		echo "</span>\n";
		echo "</p>\n";

		echo "<div id='div_param_releve'>\n";

		if (($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
			echo "<table border='0' summary='Tableau de param�tres'>\n";
			echo "<tr><td valign='top'><input type='checkbox' name='un_seul_bull_par_famille' id='un_seul_bull_par_famille' value='oui' /></td><td><label for='un_seul_bull_par_famille' style='cursor: pointer;'>Ne pas imprimer de relev� de notes pour le deuxi�me parent<br />(<i>m�me dans le cas de parents s�par�s</i>).</label></td></tr>\n";

			echo "<tr><td valign='top'><input type='checkbox' name='deux_releves_par_page' id='deux_releves_par_page' value='oui' /></td><td><label for='deux_releves_par_page' style='cursor: pointer;'>Produire deux relev�s par page (<i>PDF</i>).</label></td></tr>\n";

			echo "<tr><td valign='top'><input type='checkbox' name='tri_par_etab_orig' id='tri_par_etab_orig' value='y' /></td><td><label for='tri_par_etab_orig' style='cursor: pointer;'>Trier les relev�s par �tablissement d'origine.</label></td></tr>\n";

			echo "</table>\n";
		}
		else {
			echo "<input type='hidden' name='un_seul_bull_par_famille' value='oui' />\n";
			echo "<input type='hidden' name='deux_releves_par_page' value='non' />\n";
		}

		// AJOUTER LES PARAMETRES...
		//echo "<hr width='100' />\n";

		//=======================================
		// Tableau des param�tres mis dans un fichier externe pour permettre la m�me exploitation dans le cas d'insertion des relev�s de notes entre les bulletins
		include("tableau_choix_parametres_releves_notes.php");
		//=======================================

		echo "<input type='hidden' name='valide_select_eleves' value='y' />\n";
		//echo "<p><input type='submit' name='choix_parametres' value='Valider' /></p>\n";
		echo "<input type='hidden' name='choix_parametres' value='effectue' />\n";

		echo "</div>\n";

		echo "<script type='text/javascript'>
function CocheLigne(item) {
	for (var i=0;i<".count($tab_id_classe).";i++) {
		if(document.getElementById(item+'_'+i)){
			document.getElementById(item+'_'+i).checked = true;
		}
	}
}

function DecocheLigne(item) {
	for (var i=0;i<".count($tab_id_classe).";i++) {
		if(document.getElementById(item+'_'+i)){
			document.getElementById(item+'_'+i).checked = false;
		}
	}
}

function ToutCocher() {
";
		for($k=0;$k<count($tab_item);$k++) {
			echo "	CocheLigne('".$tab_item[$k]."');\n";
		}
		echo "	CocheLigne('rn_app');
	CocheLigne('rn_adr_resp');
}

function ToutDeCocher() {
";
		for($k=0;$k<count($tab_item);$k++) {
			echo "	DecocheLigne('".$tab_item[$k]."');\n";
		}
		echo "	DecocheLigne('rn_app');
	DecocheLigne('rn_adr_resp');
}

document.getElementById('div_param_releve').style.display='none';

</script>\n";

	}
	//===========================================================
	//===========================================================
	//===========================================================





	if(count($tab_id_classe)>1) {
		echo "<p>Pour toutes les classes";
		/*
		if(count($tab_periode_num)>1) {
			echo " et toutes les p�riodes";
		}
		*/
		echo " <a href='#' onClick='cocher_tous_eleves();return false;'>Cocher tous les �l�ves</a> / <a href='#' onClick='decocher_tous_eleves();return false;'>D�cocher tous les �l�ves</a></p>\n";
	}

	$max_eff_classe=0;
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";

		echo "<p class='bold'>Classe de ".get_class_from_id($tab_id_classe[$i])."</p>\n";

		echo "<table class='boireaus' summary='Tableau de choix des �l�ves'>\n";
		echo "<tr>\n";
		echo "<th>El�ves</th>\n";

		if($choix_periode=='periode') {
			for($j=0;$j<count($tab_periode_num);$j++) {
				//echo "<th>P�riode $j</th>\n";
				$sql="SELECT nom_periode FROM periodes WHERE id_classe='".$tab_id_classe[$i]."' AND num_periode='".$tab_periode_num[$j]."';";
				$res_per=mysql_query($sql);
				$lig_per=mysql_fetch_object($res_per);
				echo "<th>\n";

				//echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";

				echo $lig_per->nom_periode;

				echo "<br />\n";

				//echo "<a href=\"javascript:CocheColonneSelectEleves(".$i.",".$j.");changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Cocher tous les �l�ves' /></a> / <a href=\"javascript:DecocheColonneSelectEleves(".$i.",".$j.");changement();\"><img src='../images/disabled.png' width='15' height='15' alt='D�cocher tous les �l�ves' /></a>\n";
				echo "<a href=\"javascript:CocheColonneSelectEleves(".$i.",".$j.");\"><img src='../images/enabled.png' width='15' height='15' alt='Cocher tous les �l�ves' /></a> / <a href=\"javascript:DecocheColonneSelectEleves(".$i.",".$j.");\"><img src='../images/disabled.png' width='15' height='15' alt='D�cocher tous les �l�ves' /></a>\n";

				echo "</th>\n";
			}
		/*
		if($choix_periode=='periode') {
			$sql="SELECT nom_periode FROM periodes WHERE id_classe='".$tab_id_classe[$i]."' AND num_periode='".$periode."';";
			$res_per=mysql_query($sql);
			$lig_per=mysql_fetch_object($res_per);
			echo "<th>\n";
			echo $lig_per->nom_periode;
			echo "<br />\n";

			echo "<a href=\"javascript:CocheColonne(".$i.",".$periode.");changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne(".$i.",".$periode.");changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout d�cocher' /></a>\n";

			echo "</th>\n";
		*/
		}
		else {
			echo "<th>\n";
			echo "Du $display_date_debut au $display_date_fin<br />\n";

			//echo "<a href=\"javascript:CocheColonneSelectEleves(".$i.",'".$periode."');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Cocher tous les �l�ves' /></a> / <a href=\"javascript:DecocheColonneSelectEleves(".$i.",'".$periode."');changement();\"><img src='../images/disabled.png' width='15' height='15' alt='D�cocher tous les �l�ves' /></a>\n";
			echo "<a href=\"javascript:CocheColonneSelectEleves(".$i.",'".$periode."');\"><img src='../images/enabled.png' width='15' height='15' alt='Cocher tous les �l�ves' /></a> / <a href=\"javascript:DecocheColonneSelectEleves(".$i.",'".$periode."');\"><img src='../images/disabled.png' width='15' height='15' alt='D�cocher tous les �l�ves' /></a>\n";

			echo "</th>\n";
		}

		echo "</tr>\n";




		if (($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") == "yes")) {
			$sql="SELECT DISTINCT e.* FROM eleves e,
							j_eleves_classes jec
				WHERE jec.login=e.login AND
							jec.id_classe='".$tab_id_classe[$i]."'
				ORDER BY e.nom,e.prenom;";
		}
		elseif (($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpe") == "yes")) {
			$sql="SELECT DISTINCT e.* FROM eleves e,
							j_eleves_classes jec,
							j_eleves_cpe jecpe
				WHERE jec.login=e.login AND
						jecpe.e_login=e.login AND
						jecpe.cpe_login='".$_SESSION['login']."' AND
						jec.id_classe='".$tab_id_classe[$i]."'
				ORDER BY e.nom,e.prenom;";
		}
		elseif (($_SESSION['statut'] == 'professeur') AND
				(
					(getSettingValue("GepiAccesReleveProf") == "yes") ||
					(getSettingValue("GepiAccesReleveProfTousEleves") == "yes") ||
					(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes")
				)
			) {
			$sql="SELECT DISTINCT e.* FROM eleves e,
							j_eleves_classes jec
				WHERE jec.login=e.login AND
							jec.id_classe='".$tab_id_classe[$i]."'
				ORDER BY e.nom,e.prenom;";
			// On fait le filtrage des �l�ves plus bas dans le cas du prof
		}
		elseif(($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiAccesReleveProfP")=="yes")) {
			$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."' AND id_classe='".$tab_id_classe[$i]."' LIMIT 1;";
			$test_acces=mysql_query($sql);
			if(mysql_num_rows($test_acces)>0) {
				$sql="SELECT DISTINCT e.* FROM eleves e,
								j_eleves_classes jec
					WHERE jec.login=e.login AND
								jec.id_classe='".$tab_id_classe[$i]."'
					ORDER BY e.nom,e.prenom;";
			}
			else {
				// On pourrait mettre un tentative_intrusion()
				echo "<p>Vous n'�tes pas ".getSettingValue("gepi_prof_suivi")." de cette classe, donc pas autoris� � acc�der aux relev�s de notes de ces �l�ves.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
		elseif(($_SESSION['statut']=='eleve')&&(getSettingValue("GepiAccesReleveEleve") == "yes")) {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE (jec.id_classe='".$tab_id_classe[$i]."' AND jec.login='".$_SESSION['login']."' AND jec.login=e.login);";
		}
		elseif(($_SESSION['statut']=='responsable')&&(getSettingValue("GepiAccesReleveParent") == "yes")) {
			$sql="SELECT DISTINCT e.*  FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp
					WHERE (e.ele_id=r.ele_id AND
							r.pers_id=rp.pers_id AND
							rp.login='".$_SESSION['login']."' AND
							jec.id_classe='".$tab_id_classe[$i]."' AND
							(r.resp_legal='1' OR r.resp_legal='2') AND
							jec.login=e.login);";
		}
		//echo "$sql<br />";

		$res_ele=mysql_query($sql);
		$alt=1;
		$cpt=0;
		while($lig_ele=mysql_fetch_object($res_ele)) {

			//$acces_prof_a_cet_eleve="y";
			//if() {
			//============================================
			// A FAIRE -> FAIT PLUS BAS
			// Dans le cas du choix d'un groupe, on contr�le si l'�l�ve fait partie du groupe
			//$sql="SELECT 1=1 FROM j_eleves_groupes WHERE login='".$lig_ele->login."' AND id_groupe='';";
			//$sql="SELECT 1=1 FROM j_eleves_groupes WHERE login='".$lig_ele->login."' AND id_groupe='' AND (periode='' OR periode='');";
			//============================================

			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='text-align:left;'>".$lig_ele->nom." ".$lig_ele->prenom."</td>\n";

			if($choix_periode=='periode') {
				for($j=0;$j<count($tab_periode_num);$j++) {

					$sql="SELECT 1=1 FROM j_eleves_classes jec
						WHERE jec.id_classe='".$tab_id_classe[$i]."' AND
								jec.periode='".$tab_periode_num[$j]."';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0) {
						if (isset($id_groupe)) {
							// Cas d'un prof
							// Dans le cas du choix d'un groupe, on contr�le si l'�l�ve fait partie du groupe
							$sql="SELECT 1=1 FROM j_eleves_groupes WHERE (login='".$lig_ele->login."' AND id_groupe='$id_groupe' AND periode='".$tab_periode_num[$j]."');";
							$test_ele_grp=mysql_query($sql);
							if(mysql_num_rows($test_ele_grp)>0) {
								echo "<td><input type='checkbox' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$lig_ele->login."\" ";
								// Dans le cas d'un retour en arri�re, des cases peuvent avoir �t� coch�es
								$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$j]) ? $_POST['tab_selection_ele_'.$i.'_'.$j] : array();
								if(in_array($lig_ele->login,$tab_selection_eleves)) {
									echo "checked ";
								}
								echo "/></td>\n";
							}
							else {
								echo "<td>-</td>\n";
							}
						}
						else {
							// Si c'est un prof avec seulement le droit de voir les �l�ves de ses groupes, il faut limiter les cases � cocher
							if(($_SESSION['statut']=='professeur') &&
							(getSettingValue("GepiAccesReleveProf") == "yes") &&
							(getSettingValue("GepiAccesReleveProfTousEleves") != "yes") &&
							(getSettingValue("GepiAccesReleveProfToutesClasses") != "yes")) {
								$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_professeurs jgp
												WHERE (
														jeg.id_groupe=jgp.id_groupe AND
														jeg.login='".$lig_ele->login."' AND
														jgp.login='".$_SESSION['login']."' AND
														jeg.periode='".$tab_periode_num[$j]."'
														);";
								$test_ele_grp=mysql_query($sql);
								if(mysql_num_rows($test_ele_grp)==0) {
									echo "<td>-</td>\n";
								}
								else {
									echo "<td><input type='checkbox' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$lig_ele->login."\" ";
									// Dans le cas d'un retour en arri�re, des cases peuvent avoir �t� coch�es
									$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$j]) ? $_POST['tab_selection_ele_'.$i.'_'.$j] : array();
									if(in_array($lig_ele->login,$tab_selection_eleves)) {
										echo "checked ";
									}
									echo "/></td>\n";
								}
							}
							elseif($_SESSION['statut']=='eleve') {
								// Un �l�ve ne doit voir que lui-m�me
								/*
								echo "<td><input type='checkbox' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$lig_ele->login."\" ";
								echo "checked ";
								echo "/></td>\n";
								*/
								echo "<td>";
								echo "<input type='hidden' name='tab_selection_ele_".$i."_".$j."[]' value=\"".$_SESSION['login']."\" />";
								echo "<img src='../images/enabled.png' width='15' height='15' alt='Coch�' />";
								echo "</td>\n";
							}
							elseif($_SESSION['statut']=='responsable') {
								// Un responsable ne voit que ses enfants
								echo "<td><input type='checkbox' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$lig_ele->login."\" ";
								echo "checked ";
								echo "/></td>\n";
							}
							else {
								$sql="SELECT 1=1 FROM j_eleves_classes jec
												WHERE (jec.id_classe='".$tab_id_classe[$i]."' AND
														jec.login='".$lig_ele->login."' AND
														jec.periode='".$tab_periode_num[$j]."');";
								$test_ele_grp=mysql_query($sql);
								if(mysql_num_rows($test_ele_grp)==0) {
									echo "<td>-</td>\n";
								}
								else {
									echo "<td><input type='checkbox' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$lig_ele->login."\" ";
									// Dans le cas d'un retour en arri�re, des cases peuvent avoir �t� coch�es
									$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$j]) ? $_POST['tab_selection_ele_'.$i.'_'.$j] : array();
									if(in_array($lig_ele->login,$tab_selection_eleves)) {
										echo "checked ";
									}
									echo "/></td>\n";
								}
							}
						}
					}
					else {
						echo "<td>-</td>\n";
					}
				}
			/*
			if($choix_periode=='periode') {
				$sql="SELECT 1=1 FROM j_eleves_classes jec
					WHERE jec.id_classe='".$tab_id_classe[$i]."' AND
							jec.periode='".$periode."';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0) {
					echo "<td>\n";
					echo "<input type='checkbox' name='tab_selection_ele_".$i."_".$periode."[]' id='tab_selection_ele_".$i."_".$periode."_".$cpt."' value=\"".$lig_ele->login."\" checked />\n";
					echo "</td>\n";
				}
				else {
					echo "<td>-</td>\n";
				}
			*/
			}
			else {
				echo "<td>\n";

				if (isset($id_groupe)) {
					// Dans le cas du choix d'un groupe, on contr�le si l'�l�ve fait partie du groupe
					$sql="SELECT 1=1 FROM j_eleves_groupes WHERE (login='".$lig_ele->login."' AND id_groupe='$id_groupe');";
					$test_ele_grp=mysql_query($sql);
					if(mysql_num_rows($test_ele_grp)>0) {
						echo "<input type='checkbox' name='tab_selection_ele_".$i."_".$periode."[]' id='tab_selection_ele_".$i."_".$periode."_".$cpt."' value=\"".$lig_ele->login."\" ";

						// Dans le cas d'un retour en arri�re, des cases peuvent avoir �t� coch�es
						$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$periode]) ? $_POST['tab_selection_ele_'.$i.'_'.$periode] : array();
						if(in_array($lig_ele->login,$tab_selection_eleves)) {
							echo "checked ";
						}
						echo "/></td>\n";
					}
					else {
						echo "<td>-</td>\n";
					}
				}
				else {
					if(($_SESSION['statut']=='professeur') &&
					(getSettingValue("GepiAccesReleveProf") == "yes") &&
					(getSettingValue("GepiAccesReleveProfTousEleves") != "yes") &&
					(getSettingValue("GepiAccesReleveProfToutesClasses") != "yes")) {
						$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_professeurs jgp
										WHERE (
												jeg.id_groupe=jgp.id_groupe AND
												jeg.login='".$lig_ele->login."' AND
												jgp.login='".$_SESSION['login']."'
												);";
						$test_ele_grp=mysql_query($sql);
						if(mysql_num_rows($test_ele_grp)==0) {
							echo "<td>-</td>\n";
						}
						else {
							echo "<input type='checkbox' name='tab_selection_ele_".$i."_".$periode."[]' id='tab_selection_ele_".$i."_".$periode."_".$cpt."' value=\"".$lig_ele->login."\" ";

							// Dans le cas d'un retour en arri�re, des cases peuvent avoir �t� coch�es
							$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$periode]) ? $_POST['tab_selection_ele_'.$i.'_'.$periode] : array();
							if(in_array($lig_ele->login,$tab_selection_eleves)) {
								echo "checked ";
							}
							echo "/></td>\n";
						}
					}
					else {
						echo "<input type='checkbox' name='tab_selection_ele_".$i."_".$periode."[]' id='tab_selection_ele_".$i."_".$periode."_".$cpt."' value=\"".$lig_ele->login."\" ";

						// Dans le cas d'un retour en arri�re, des cases peuvent avoir �t� coch�es
						$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$periode]) ? $_POST['tab_selection_ele_'.$i.'_'.$periode] : array();
						if(in_array($lig_ele->login,$tab_selection_eleves)) {
							echo "checked ";
						}
						echo "/></td>\n";
					}
				}

			}

			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";

		if($max_eff_classe<$cpt) {$max_eff_classe=$cpt;}

	}

//count($tab_periode_num)

	echo "<script type='text/javascript'>

function CocheColonneSelectEleves(i,j) {
	for (var k=0;k<$max_eff_classe;k++) {
		if(document.getElementById('tab_selection_ele_'+i+'_'+j+'_'+k)){
			document.getElementById('tab_selection_ele_'+i+'_'+j+'_'+k).checked = true;
		}
	}
}

function DecocheColonneSelectEleves(i,j) {
	for (var k=0;k<$max_eff_classe;k++) {
		if(document.getElementById('tab_selection_ele_'+i+'_'+j+'_'+k)){
			document.getElementById('tab_selection_ele_'+i+'_'+j+'_'+k).checked = false;
		}
	}
}

function cocher_tous_eleves() {
";

	for($i=0;$i<count($tab_id_classe);$i++) {
		if($choix_periode=='periode') {
			for($j=0;$j<count($tab_periode_num);$j++) {
				echo "CocheColonneSelectEleves($i,$j);\n";
			}
		}
		else {
			echo "CocheColonneSelectEleves($i,'$periode');\n";
		}
	}

	echo "}
function decocher_tous_eleves() {
";

	for($i=0;$i<count($tab_id_classe);$i++) {
		if($choix_periode=='periode') {
			for($j=0;$j<count($tab_periode_num);$j++) {
				echo "DecocheColonneSelectEleves($i,$j);\n";
			}
		}
		else {
			echo "DecocheColonneSelectEleves($i,'$periode');\n";
		}
	}

	echo "}\n";

	if($_SESSION['statut']!='eleve') {
		echo "function test_check_ele() {
	eff_eleve_checked=0;
	
	tabinput=document.getElementsByTagName('input');
	
	for(i=0;i<tabinput.length;i++) {
		//nom_element=tabinput[i].getAttribute('name');
		// On ne peut pas tester si le champ est coch� en utilisant l'attribut 'name' parce que cet attribut est de la forme 'tab_selection_ele_...[]' et les crochets posent pb.
		
		if(tabinput[i].getAttribute('id')) {
			nom_element=tabinput[i].getAttribute('id');

			t=nom_element.substring(0,18);

			if(t=='tab_selection_ele_') {
				if(document.getElementById(nom_element).checked==true) {
					eff_eleve_checked++;
				}
			}
		}
	}
	
	if(eff_eleve_checked==0) {
		//alert('Aucun �l�ve n est s�lectionn�.');
		//return confirm('Aucun �l�ve n est s�lectionn�. Voulez-vous quand m�me g�n�rer le relev�?');
		if(confirm('Aucun �l�ve n est s�lectionn�. Voulez-vous quand m�me g�n�rer le relev�?')) {document.forms['formulaire'].submit();}
	}
	else {
	//	alert('Au moins un �l�ve est s�lectionn�: '+eff_eleve_checked);
	document.forms['formulaire'].submit();
	}
}\n";
	}
	else {
		echo "function test_check_ele() {
	// On ne fait pas de test dans le cas d'un login eleve
	// L'�l�ve ne peut consulter que ses notes et est donc n�cessairement coch�
	document.forms['formulaire'].submit();
}\n";
	}
	echo "// On coche tous les �l�ves par d�faut:
cocher_tous_eleves();
</script>\n";

	//echo "<p><a href='javascript:test_check_ele();return false;'>Test �l�ve</a></p>\n";
	//echo "<p><a href='#' onclick='test_check_ele();return false;'>Test �l�ve</a></p>\n";

	if(isset($id_groupe)) {
		// Cas d'un prof (on a forc� plus haut � NULL $id_groupe si on n'a pas affaire � un prof)
		echo "<input type='hidden' name='id_groupe' value='".$id_groupe."' />\n";
	}

	/*
	if ((($_SESSION['statut']=='eleve') AND (getSettingValue("GepiAccesOptionsReleveEleve") != "yes"))||
		(($_SESSION['statut']=='responsable') AND (getSettingValue("GepiAccesOptionsReleveParent") != "yes"))) {
		// T�moin destin� � sauter l'�tape des param�tres
		echo "<input type='hidden' name='choix_parametres' value='y' />\n";
		echo "<input type='hidden' name='mode_bulletin' value='html' />\n";
		echo "<input type='hidden' name='un_seul_bull_par_famille' value='oui' />\n";
	}
	*/
	echo "<input type='hidden' name='valide_select_eleves' value='y' />\n";
	//echo "<p><input type='submit' name='bouton_valide_select_eleves2' value='Valider' /></p>\n";
	//echo "<p><input type='submit' name='bouton_valide_select_eleves2' value='Valider' onclick='test_check_ele()' /></p>\n";
	echo "<p><input type='button' name='bouton_valide_select_eleves2' value='Valider' onclick='test_check_ele()' /></p>\n";
	echo "</form>\n";
}
//=======================================================
//================CHOIX DES PARAMETRES===================
/*
elseif(!isset($choix_parametres)) {

	echo "<p class='bold'><a href='index.php'>Retour � l'index</a>";
	echo "| <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."' onClick=\"document.forms['form_retour'].submit();return false;\">Choisir d'autres p�riodes</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."' onClick=\"document.forms['form_retour_eleves'].submit();return false;\">Choisir d'autres �l�ves</a>\n";
	if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='administrateur')) {
		echo " | <a href='param_releve_html.php' target='_blank'>Param�tres du relev� HTML</a>";
	}
	echo "</p>\n";


	//===========================
	// FORMULAIRE POUR LE RETOUR AU CHOIX DES PERIODES
	echo "\n<!-- Formulaire de retour au choix des p�riodes -->\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='form_retour_periode'>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";
	}
	//echo "<input type='hidden' name='choix_periode' value='$choix_periode' />\n";
	if($choix_periode=='periode') {
		//echo "<input type='hidden' name='periode' value='$periode' />\n";
		for($j=0;$j<count($tab_periode_num);$j++) {
			echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";
		}
	}
	else {
		$periode="intervalle";
		echo "<input type='hidden' name='display_date_debut' value='$display_date_debut' />\n";
		echo "<input type='hidden' name='display_date_fin' value='$display_date_fin' />\n";
	}
	if(isset($id_groupe)) {
		// Cas d'un prof (on a forc� plus haut � NULL $id_groupe si on n'a pas affaire � un prof)
		echo "<input type='hidden' name='id_groupe' value='".$id_groupe."' />\n";
	}
	echo "</form>\n";
	//===========================
	// FORMULAIRE POUR LE RETOUR AU CHOIX DES ELEVES
	echo "\n<!-- Formulaire de retour au choix des �l�ves -->\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='form_retour_eleves'>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";
	}

	echo "<input type='hidden' name='choix_periode' value='$choix_periode' />\n";
	if($choix_periode=='periode') {
		//echo "<input type='hidden' name='periode' value='$periode' />\n";
		for($j=0;$j<count($tab_periode_num);$j++) {
			echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";
		}
	}
	else {
		$periode="intervalle";
		echo "<input type='hidden' name='display_date_debut' value='$display_date_debut' />\n";
		echo "<input type='hidden' name='display_date_fin' value='$display_date_fin' />\n";
	}

	for($i=0;$i<count($tab_id_classe);$i++) {
		if($choix_periode=='periode') {
			for($j=0;$j<count($tab_periode_num);$j++) {
				unset($tab_selection_eleves);
				$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$j]) ? $_POST['tab_selection_ele_'.$i.'_'.$j] : array();
				for($k=0;$k<count($tab_selection_eleves);$k++) {
					echo "<input type='hidden' name='tab_selection_ele_".$i."_".$j."[]' value=\"".$tab_selection_eleves[$k]."\" />\n";
				}
			}
		}
		else {
			unset($tab_selection_eleves);
			$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_intervalle']) ? $_POST['tab_selection_ele_'.$i.'_intervalle'] : array();
			for($k=0;$k<count($tab_selection_eleves);$k++) {
				echo "<input type='hidden' name='tab_selection_ele_".$i."_intervalle[]' value=\"".$tab_selection_eleves[$k]."\" />\n";
			}
		}
	}
	if(isset($id_groupe)) {
		// Cas d'un prof (on a forc� plus haut � NULL $id_groupe si on n'a pas affaire � un prof)
		echo "<input type='hidden' name='id_groupe' value='".$id_groupe."' />\n";
	}
	echo "</form>\n";
	//===========================



	//+++++++++++++++++++++++++++++++++++
	// A FAIRE
	// Contr�ler les param�tres propos�s en fonction de
	// GepiAccesOptionsReleveParent
	// GepiAccesOptionsReleveEleve
	//+++++++++++++++++++++++++++++++++++

	echo "\n<!-- Formulaire de choix des param�tres -->\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire' target='_blank'>\n";

	// Classes:

	echo "<p class='bold'>Choix des param�tres pour la s�lection d'�l�ves parmi les �l�ves de ";


	for($i=0;$i<count($tab_id_classe);$i++) {
		if($i>0) {echo ", ";}
		echo get_class_from_id($tab_id_classe[$i]);
		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";
	}
	if($choix_periode=='periode') {
		if(count($tab_periode_num)==1) {
			echo " pour la p�riode ".$tab_periode_num[0];
		}
		else {
			echo " pour les p�riodes ";
			for($j=0;$j<count($tab_periode_num);$j++) {
				if($j>0) {echo ", ";}
				echo $tab_periode_num[$j];
			}
		}
	}
	else {
		echo "<br />pour une extraction des notes entre le $display_date_debut et le $display_date_fin";
	}
	echo ":</p>\n";


	// P�riode d'affichage:
	echo "<input type='hidden' name='choix_periode' value='$choix_periode' />\n";
	if($choix_periode=='periode') {
		//echo "<input type='hidden' name='periode' value='$periode' />\n";
		//echo "count(\$tab_periode_num)=".count($tab_periode_num)."<br />";
		for($j=0;$j<count($tab_periode_num);$j++) {
			echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";
		}
	}
	else {
		$periode="intervalle";
		echo "<input type='hidden' name='display_date_debut' value='$display_date_debut' />\n";
		echo "<input type='hidden' name='display_date_fin' value='$display_date_fin' />\n";
	}

	// S�lection d'�l�ves:
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<!-- Eleves de ".get_class_from_id($tab_id_classe[$i])." -->\n";
		if($choix_periode=='periode') {
			//echo "count(\$tab_periode_num)=".count($tab_periode_num);
			for($j=0;$j<count($tab_periode_num);$j++) {
				unset($tab_selection_eleves);
				echo "<!-- Eleves de ".get_class_from_id($tab_id_classe[$i])." sur la p�riode ".$tab_periode_num[$j]." -->\n";
				$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$j]) ? $_POST['tab_selection_ele_'.$i.'_'.$j] : array();
				for($k=0;$k<count($tab_selection_eleves);$k++) {
					echo "<input type='hidden' name='tab_selection_ele_".$i."_".$j."[]' value=\"".$tab_selection_eleves[$k]."\" />\n";
				}
			}
		}
		else {
			unset($tab_selection_eleves);
			echo "<!-- Eleves de ".get_class_from_id($tab_id_classe[$i])." sur la p�riode ".$display_date_debut." � ".$display_date_fin." -->\n";
			$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_intervalle']) ? $_POST['tab_selection_ele_'.$i.'_intervalle'] : array();
			for($k=0;$k<count($tab_selection_eleves);$k++) {
				echo "<input type='hidden' name='tab_selection_ele_".$i."_intervalle[]' value=\"".$tab_selection_eleves[$k]."\" />\n";
			}
		}
	}


	//=======================================
	// A remplacer par la suite par un choix:
	echo "<input type='hidden' name='mode_bulletin' value='html' />\n";
	//echo "<input type='hidden' name='un_seul_bull_par_famille' value='non' />\n";
	//=======================================

	if (($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
		echo "<table border='0'>\n";
		echo "<tr><td valign='top'><input type='checkbox' name='un_seul_bull_par_famille' id='un_seul_bull_par_famille' value='oui' /></td><td><label for='un_seul_bull_par_famille' style='cursor: pointer;'>Ne pas imprimer de relev� de notes pour le deuxi�me parent<br />(<i>m�me dans le cas de parents s�par�s</i>).</label></td></tr>\n";
		echo "</table>\n";
	}
	else {
		echo "<input type='hidden' name='un_seul_bull_par_famille' value='oui' />\n";
	}

	// AJOUTER LES PARAMETRES...
	//echo "<hr width='100' />\n";

	//=======================================
	// Tableau des param�tres mis dans un fichier externe pour permettre la m�me exploitation dans le cas d'insertion des relev�s de notes entre les bulletins
	include("tableau_choix_parametres_releves_notes.php");
	//=======================================

	echo "<input type='hidden' name='valide_select_eleves' value='y' />\n";
	echo "<p><input type='submit' name='choix_parametres' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
function CocheLigne(item) {
	for (var i=0;i<".count($tab_id_classe).";i++) {
		if(document.getElementById(item+'_'+i)){
			document.getElementById(item+'_'+i).checked = true;
		}
	}
}

function DecocheLigne(item) {
	for (var i=0;i<".count($tab_id_classe).";i++) {
		if(document.getElementById(item+'_'+i)){
			document.getElementById(item+'_'+i).checked = false;
		}
	}
}

function ToutCocher() {
";
	for($k=0;$k<count($tab_item);$k++) {
		echo "	CocheLigne('".$tab_item[$k]."');\n";
	}
	echo "	CocheLigne('rn_app');
	CocheLigne('rn_adr_resp');
}

function ToutDeCocher() {
";
	for($k=0;$k<count($tab_item);$k++) {
		echo "	DecocheLigne('".$tab_item[$k]."');\n";
	}
	echo "	DecocheLigne('rn_app');
	DecocheLigne('rn_adr_resp');
}

</script>\n";

}
*/
//=======================================================
//===EXTRACTION DES DONNEES PUIS AFFICHAGE DES RELEVES===
else {
	$mode_bulletin=isset($_POST['mode_bulletin']) ? $_POST['mode_bulletin'] : "html";
	$un_seul_bull_par_famille=isset($_POST['un_seul_bull_par_famille']) ? $_POST['un_seul_bull_par_famille'] : "non";
	$deux_releves_par_page=isset($_POST['deux_releves_par_page']) ? $_POST['deux_releves_par_page'] : "non";

	$tri_par_etab_orig=isset($_POST['tri_par_etab_orig']) ? $_POST['tri_par_etab_orig'] : "n";

	if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
		$deux_releves_par_page="non";
		$un_seul_bull_par_famille="oui";
	}

	$nb_releve_par_page=1;
	if($deux_releves_par_page=="oui") {
		$nb_releve_par_page=2;
	}

	// Prof principal
	$gepi_prof_suivi=getSettingValue("gepi_prof_suivi");

	if($mode_bulletin!="pdf") {
		echo "<div id='infodiv'>
<p id='titre_infodiv' style='font-weight:bold; text-align:center; border:1px solid black;'></p>
<table class='boireaus' width='100%' summary=\"Tableau de d�roulement de l'extraction/g�n�ration\">
<tr>
<th colspan='3' id='td_info'></th>
</tr>
<tr>
<th width='33%'>Classe</th>
<th width='33%'>P�riode</th>
<th>El�ve</th>
</tr>
<tr>
<td id='td_classe'></td>
<td id='td_periode'></td>
<td id='td_ele'></td>
</tr>
</table>
</div>\n";
	}

	//========================================
	// Extraction des donn�es externalis�e pour permettre un appel depuis la g�n�ration de bulletins de fa�on � intercaler les relev�s de notes entre les bulletins
	include("extraction_donnees_releves_notes.php");
	//========================================

	// DEBUG:
	/*
	echo "\$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['id_cn'][2367]['conteneurs'][0]['moy']=".$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['id_cn'][2367]['conteneurs'][0]['moy']."<br />\n";
	echo "\$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['devoir'][1]['note']=".$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['devoir'][1]['note']."<br />\n";
	echo "\$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['devoir'][1]['statut']=".$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['devoir'][1]['statut']."<br />\n";
	*/

	//========================================================================
	// A CE STADE LE TABLEAU $tab_releve EST RENSEIGN�
	// PLUS AUCUNE REQUETE NE DEVRAIT ETRE NECESSAIRE
	// OU ALORS IL FAUDRAIT LES EFFECTUER AU-DESSUS ET COMPLETER $tab_releve
	//
	// IL Y AURA A RENSEIGNER $tab_releve[$id_classe][$periode_num]['modele_pdf']
	// SI ON FAIT UNE IMPRESSION DE RELEVE PDF, POUR NE PAS REFAIRE LES REQUETES
	// POUR CHAQUE ELEVE.
	//========================================================================

	if($mode_bulletin!="pdf") {
		echo "<script type='text/javascript'>
	document.getElementById('td_info').innerHTML='Affichage';
</script>\n";
	}
	else {
		// d�finition d'une variable
		$hauteur_pris = 0;

		/*****************************************
		* d�but de la g�n�ration du fichier PDF  *
		* ****************************************/
		header('Content-type: application/pdf');
		//cr�ation du PDF en mode Portrait, unit�e de mesure en mm, de taille A4
		$pdf=new bul_PDF('p', 'mm', 'A4');
		$nb_eleve_aff = 1;
		$categorie_passe = '';
		$categorie_passe_count = 0;
		$pdf->SetCreator($gepiSchoolName);
		$pdf->SetAuthor($gepiSchoolName);
		$pdf->SetKeywords('');
		$pdf->SetSubject('Releve_de_notes');
		$pdf->SetTitle('Releve_de_notes');
		$pdf->SetDisplayMode('fullwidth', 'single');
		$pdf->SetCompression(TRUE);
		$pdf->SetAutoPageBreak(TRUE, 5);

		$responsable_place = 0;
	}

	function regime($id_reg) {
		switch($id_reg) {
			case "d/p":
				$regime="demi-pensionnaire";
				break;
			case "ext.":
				$regime="externe";
				break;
			case "int.":
				$regime="interne";
				break;
			case "i-e":
				$regime="interne-extern�";
				break;
			default:
				$regime="R�gime inconnu???";
				break;
		}
	
		return $regime;
	}

	// Compteur pour g�rer les 2 relev�s par page en PDF
	$compteur_releve=0;
	// Compteur pour les insertions de saut de page en HTML
	$compteur_releve_bis=0;
	// Initialisation pour r�cup global dans releve_html() et signalement ensuite s'il s'agit de deux relev�s pour des parents s�par�s
	$nb_releves=1;
	for($loop_classe=0;$loop_classe<count($tab_id_classe);$loop_classe++) {
		$id_classe=$tab_id_classe[$loop_classe];
		$classe=get_class_from_id($id_classe);

		if($mode_bulletin!="pdf") {
			echo "<script type='text/javascript'>
	document.getElementById('td_classe').innerHTML='".$classe."';
</script>\n";
		}

		for($loop_periode_num=0;$loop_periode_num<count($tab_periode_num);$loop_periode_num++) {

			$periode_num=$tab_periode_num[$loop_periode_num];

			//==============================
			if($mode_bulletin!="pdf") {
				echo "<script type='text/javascript'>
	document.getElementById('td_periode').innerHTML='".$periode_num."';
</script>\n";
				flush();
			}
			//==============================

			if($mode_bulletin!="pdf") {
				echo "<div class='noprint' style='background-color:white; border: 1px solid red;'>\n";
				echo "<h2>Classe de ".$classe."</h2>\n";
				if($periode_num=="intervalle") {
					echo "<p><b>Du $display_date_debut au $display_date_fin</b></p>\n";
				}
				else {
					echo "<p><b>P�riode $periode_num</b></p>\n";
				}

				echo "<p>Effectif de la classe: ".$tab_releve[$id_classe][$periode_num]['eff_classe']."</p>\n";
				//echo "</div>\n";
			}

			//if(!isset($tab_releve[$id_classe][$periode_num]['eleve'])) {
			if((!isset($tab_releve[$id_classe][$periode_num]['eleve']))&&($mode_bulletin!="pdf")) {
				echo "<p>Aucun �l�ve s�lectionn�/coch� dans cette classe pour cette p�riode.</p>\n";
				echo "</div>\n";
			}
			else {

				if($mode_bulletin!="pdf") {
					//+++++++++++++++++++++++++++++++++++
					// A FAIRE: Il faudrait afficher l'effectif des �l�ves choisis faisant partie de la classe/p�riode...
					echo "<p>".count($tab_releve[$id_classe][$periode_num]['eleve'])." �l�ve(s) s�lectionn�(s) dans cette classe (<i>pour cette p�riode</i>).</p>\n";
					//+++++++++++++++++++++++++++++++++++
	
					echo "</div>\n";
				}

				//$compteur_releve=0;
				if(isset($tab_releve[$id_classe][$periode_num]['eleve'])) {

					unset($tmp_tab);
					unset($rg);
					//$tri_par_etab_orig="y";
					if($tri_par_etab_orig=='y') {
						for($k=0;$k<count($tab_releve[$id_classe][$periode_num]['eleve']);$k++) {
							$rg[$k]=$k;
							$tmp_tab[$k]=$tab_releve[$id_classe][$periode_num]['eleve'][$k]['etab_id'];
						}
						array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);
					}

					for($i=0;$i<count($tab_releve[$id_classe][$periode_num]['eleve']);$i++) {
						if($tri_par_etab_orig=='n') {$rg[$i]=$i;}

						if(isset($tab_releve[$id_classe][$periode_num]['selection_eleves'])) {
							//if (in_array($tab_releve[$id_classe][$periode_num]['eleve'][$i]['login'],$tab_releve[$id_classe][$periode_num]['selection_eleves'])) {
	
								//+++++++++++++++++++++++++++++++++++
								//===============================================
								/*
	
								// A FAIRE -> A FAIRE PLUS HAUT POUR NE PLUS FAIRE DE REQUETES DANS CETTE PARTIE L�
	
								// Contr�ler qu'il n'y a pas d'usurpation d'acc�s
								$autorisation_acces='n';
								// Si c'est un prof
								if($_SESSION['statut']=='professeur') {
									// GepiAccesReleveProf               -> que les �l�ves de ses groupes
									// GepiAccesReleveProfTousEleves     -> tous les �l�ves de ses classes
									// GepiAccesReleveProfToutesClasses  -> tous les �l�ves de toutes les classes
	
									// GepiAccesReleveProfToutesClasses  -> tous les �l�ves de toutes les classes
									if(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes") {
										// On v�rifie seulement que c'est bien le login d'un �l�ve d'une classe
										$sql="SELECT 1=1 FROM j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND login='".$tab_releve[$id_classe][$periode_num]['eleve'][$i]['login']."';";
										$verif=mysql_query($sql);
										if(mysql_num_rows($verif)>0) {$autorisation_acces='y';}
									}
									elseif (getSettingValue("GepiAccesReleveProf") == "yes") {
	
									}
									elseif (getSettingValue("GepiAccesReleveProfTousEleves") == "yes") {
	
									}
									else {
	
									}
								}
								// Si c'est un CPE
								// Si c'est un compte scolarit�
								// Si c'est un �l�ve
								// Si c'est un responsable
								*/
								$autorisation_acces='y';
								//===============================================
								//+++++++++++++++++++++++++++++++++++
	
								if($autorisation_acces=='y') {
									if($mode_bulletin!="pdf") {
										echo "<script type='text/javascript'>
	document.getElementById('td_ele').innerHTML='".$tab_releve[$id_classe][$periode_num]['eleve'][$rg[$i]]['login']."';
</script>\n";
										flush();
	
										// Saut de page si jamais ce n'est pas le premier bulletin
										//if($compteur_releve>0) {echo "<p class='saut'>&nbsp;</p>\n";}
										if($compteur_releve_bis>0) {echo "<p class='saut'>&nbsp;</p>\n";}
	
										// G�n�ration du bulletin de l'�l�ve
										releve_html($tab_releve[$id_classe][$periode_num],$rg[$i],-1);

										$chaine_info_deux_releves="";
										if(($un_seul_bull_par_famille=="non")&&($nb_releves>1)) {$chaine_info_deux_releves=".<br /><span style='color:red'>Plusieurs relev�s pour une m�me famille&nbsp: les adresses des deux responsables diff�rent.</span><br /><span style='color:red'>Si vous ne souhaitez pas de deuxi�me relev�, pensez � cocher la case 'Un seul relev� par famille'.</span>";}

										echo "<div class='espacement_bulletins'><div align='center'>Espacement (<i>non imprim�</i>) entre les relev�s".$chaine_info_deux_releves."</div></div>\n";
	
										flush();
									}
									else {
										// Relev� PDF
	
										// G�n�ration du relev� PDF de l'�l�ve
										releve_pdf($tab_releve[$id_classe][$periode_num],$rg[$i]);
	
									}
	
									//$compteur_releve++;
									$compteur_releve_bis++;
	
								}
							//}
						}
					}
				}
			}
		}
	}

	/*
	echo "<style type='text/css'>
	@media screen{
		.espacement_bulletins {
			width: 100%;
			height: 50px;
			border:1px solid red;
			background-color: white;
		}
	}
	@media print{
		.espacement_bulletins {
			display:none;
		}

		#remarques_bas_de_page {
			display:none;
		}

		.alerte_erreur {
			display:none;
		}
	}
</style>\n";
*/

	if($mode_bulletin!="pdf") {
		echo "<script type='text/javascript'>
	document.getElementById('infodiv').style.display='none';
</script>\n";
	}
	else {
		// Envoyer le PDF et quitter
		$nom_releve = date("Ymd_Hi");
		$nom_fichier = 'releve_notes_'.$nom_releve.'.pdf';
		$pdf->Output($nom_fichier,'I');

		die();
	}

}

require("../lib/footer.inc.php");
?>
