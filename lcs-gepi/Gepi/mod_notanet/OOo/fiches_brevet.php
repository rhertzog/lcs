<?php
/* $Id: fiches_brevet.php 3323 2009-08-05 10:06:18Z crob $ */
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

// Construction du tableau de notes des fiches brevets





// Biblioth�que pour Notanet et Fiches brevet
include("../lib_brevets.php");


//===================================
//=== Enregistrement des param�tres d'impression ===
//===================================

if (isset($_POST['enregistrer_param'])) {
	$msg="";

	if (isset($_POST['fb_academie'])) {
		if (!saveSetting("fb_academie", $_POST['fb_academie'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_academie !";
		}
	}

	if (isset($_POST['fb_departement'])) {
		if (!saveSetting("fb_departement", $_POST['fb_departement'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_departement !";
		}
	}

	if (isset($_POST['fb_session'])) {
		if (!saveSetting("fb_session", $_POST['fb_session'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_session !";
		}
	}


	if (isset($_POST['fb_mode_moyenne'])) {
		if (!saveSetting("fb_mode_moyenne", $_POST['fb_mode_moyenne'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_mode_moyenne !";
		}
	}

	if($msg==""){$msg="Enregistrement effectu�.";}
}



//=======================
//=== Initialisation des variables ===
//=======================

$titre_page = "Fiches Brevet";

// R�cup�ration des variables:
// Tableau des classes:
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$type_brevet = isset($_POST['type_brevet']) ? $_POST['type_brevet'] : (isset($_GET['type_brevet']) ? $_GET['type_brevet'] : NULL);
if(isset($type_brevet)) {
	if((!ereg("[0-9]",$type_brevet))||(strlen(my_ereg_replace("[0-9]","",$type_brevet))!=0)) {
		$type_brevet=NULL;
	}
}

$avec_app=isset($_POST['avec_app']) ? $_POST['avec_app'] : "n";



//=====================================
//=== PARAM�TRAGE GENERAL DES FICHES BREVETS ===
//=====================================

if (isset($_GET['parametrer'])) {

	//**************** EN-TETE *****************
	require_once("../../lib/header.inc");
	//**************** FIN EN-TETE *****************


	// Param�trage des tailles de police, dimensions, nom d'acad�mie, de d�partement,...
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a>";
	echo " | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des classes</a>";
	echo "</p>\n";
	echo "</div>\n";

	echo "<h2>Param�tres d'affichage des Fiches Brevet</h2>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."' id='form_param' method='post'>\n";
	echo "<table border='0'>\n";

	$alt=1;
	$fb_academie=getSettingValue("fb_academie");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Acad�mie de: </td>\n";
	echo "<td><input type='text' name='fb_academie' value='$fb_academie' /></td>\n";
	echo "</tr>\n";

	$fb_departement=getSettingValue("fb_departement");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>D�partement de: </td>\n";
	echo "<td><input type='text' name='fb_departement' value='$fb_departement' /></td>\n";
	echo "</tr>\n";

	$fb_session=getSettingValue("fb_session");
	if($fb_session==""){
		$tmp_date=getdate();
		$tmp_mois=$tmp_date['mon'];
		if($tmp_mois>9){
			$fb_session=$tmp_date['year']+1;
		}
		else{
			$fb_session=$tmp_date['year'];
		}
	}
	
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Session: </td>\n";
	echo "<td><input type='text' name='fb_session' value='$fb_session' /></td>\n";
	echo "</tr>\n";

	// ****************************************************************************
	// MODE DE CALCUL POUR LES MOYENNES DES REGROUPEMENTS DE MATIERES:
	// - LV1: on fait la moyenne de toutes les LV1 (AGL1, ALL1)
	// ou
	// - LV1: on pr�sente pour chaque �l�ve, la moyenne qui correspond � sa LV1: ALL1 s'il fait ALL1,...
	// ****************************************************************************
	$fb_mode_moyenne=getSettingValue("fb_mode_moyenne");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td valign='top'>Mode de calcul des moyennes pour les options Notanet associ�es � plusieurs mati�res (<i>ex.: LV1 associ�e � AGL1 et ALL1</i>): </td>\n";
	echo "<td>";
		echo "<table border='0'>\n";
		echo "<tr>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='fb_mode_moyenne' value='1' ";
		if($fb_mode_moyenne!="2"){
			echo "checked />";
		}
		else{
			echo "/>";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo "Calculer la moyenne de toutes mati�res d'une m�me option Notanet confondues<br />\n";
		echo "(<i>on compte ensemble les AGL1 et ALL1; c'est la moyenne de toute la LV1 qui est effectu�e</i>)\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='fb_mode_moyenne' value='2' ";
		if($fb_mode_moyenne=="2"){
			echo "checked />";
		}
		else{
			echo "/>";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo "Calculer les moyennes par mati�res<br />\n";
		echo "(<i>on ne m�lange pas AGL1 et ALL1 dans le calcul de la moyenne de classe pour un �l�ve</i>)\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";
	
	echo "<p align='center'><input type='submit' name='enregistrer_param' value='Enregistrer' /></p>\n";
	echo "</form>\n";

	require("../../lib/footer.inc.php");
	die();

}

//============================
//=== FIN DU PARAMETRAGE  GENERAL ===
//============================




//===================================================
//=== V�RIFICATION QUE DES �L�VES SONT BIEN AFFECT�S � UN BREVET ===
//===================================================

$sql="SELECT DISTINCT type_brevet FROM notanet_ele_type ORDER BY type_brevet;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {

	//**************** EN-TETE *****************
	require_once("../../lib/header.inc");
	//**************** FIN EN-TETE *****************

	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association �l�ve/type de brevet n'a encore �t� r�alis�e.<br />Commencez par <a href='../select_eleves.php'>s�lectionner les �l�ves</a></p>\n";

	require("../../lib/footer.inc.php");
	die();
}

$sql="SELECT DISTINCT type_brevet FROM notanet_corresp ORDER BY type_brevet;";
$res=mysql_query($sql);
$nb_type_brevet=mysql_num_rows($res);

if($nb_type_brevet==0) {

	//**************** EN-TETE *****************
	require_once("../../lib/header.inc");
	//**************** FIN EN-TETE *****************

	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association mati�res/type de brevet n'a encore �t� r�alis�e.<br />Commencez par <a href='../select_matieres.php'>s�lectionner les mati�res</a></p>\n";

	require("../../lib/footer.inc.php");
	die();
}

//=======================
//=== FIN DE LA V�RIFICATION ===
//=======================




//==========================
//=== CHOIX DU BREVET � TRAITER ===
//==========================

// Biblioth�que pour Notanet et Fiches brevet
// include("../lib_brevets.php");

if(!isset($type_brevet)) {

	//**************** EN-TETE *****************
	require_once("../../lib/header.inc");
	//**************** FIN EN-TETE *****************

	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a> | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Param�trer</a>";
	echo "</p>\n";
	echo "</div>\n";

	echo "<ul>\n";
		while($lig=mysql_fetch_object($res)) {
			switch ($lig->type_brevet ) {
				case 0 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>G�n�rer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
				case 1 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>G�n�rer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
				case 2 :
					echo "<li>G�n�rer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</li>\n";
				break;
				case 3 :
					echo "<li>G�n�rer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</li>\n";
				break;
				case 4 :
					echo "<li>G�n�rer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</li>\n";
				break;
				case 5 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>G�n�rer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
				case 6 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>G�n�rer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
				case 7 :
					echo "<li>G�n�rer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</li>\n";
				break;
			}
		}
	echo "</ul>\n";

	require("../../lib/footer.inc.php");
	die();
}

//========================
//===  FIN DU CHOIX DU BREVET ===
//========================


//===================
//=== Donn�es communes ===
//===================

// Adresse �tablissement:
$gepiSchoolName=getSettingValue("gepiSchoolName");
$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1");
$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2");
$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode");
$gepiSchoolCity=getSettingValue("gepiSchoolCity");


$tabmatieres=array();
for($j=101;$j<=122;$j++){
	$tabmatieres[$j]=array();
}

//========================
//=== Fin des donn�es communes ===
//========================



//====================
//=== CHOIX DE LA CLASSE ===
//====================

if (!isset($id_classe)) {

	//**************** EN-TETE *****************
	require_once("../../lib/header.inc");
	//**************** FIN EN-TETE *****************

	// Choix de la classe:
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a>";
	echo " | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Type de brevet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Param�trer</a>";
	echo "</p>\n";
	echo "</div>\n";
	
	// Les tables notanet ne sont pas renseign�es, on s'arr�te
	$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, notanet n,notanet_ele_type net WHERE p.id_classe = c.id AND c.id=n.id_classe AND n.login=net.login AND net.type_brevet='$type_brevet' ORDER BY classe");
	if(!$call_data){
		echo "<p><font color='red'>Attention:</font> Il semble que vous n'ayez pas men� la proc�dure notanet � son terme.<br />Cette proc�dure renseigne des tables requises pour g�n�rer les fiches brevet.<br />Effectuez la <a href='../index.php'>proc�dure notanet</a>.</p>\n";

		require("../../lib/footer.inc.php");
		die();
	}
	$nombre_lignes = mysql_num_rows($call_data);

	echo "<div>\n";

	echo "<p>Choisissez les classes pour lesquelles vous souhaitez g�n�rer les fiches brevet:</p>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."' id='form_choix_classe' method='post'>\n";
	echo "<p><input type='hidden' name='type_brevet' value='$type_brevet' /></p>\n";
	echo "<p>S�lectionnez les classes : </p>\n";
	echo "<blockquote>\n";

	$size=min(10,$nombre_lignes);
	echo "<p><select name='id_classe[]' multiple='multiple' size='$size'>\n";
	$i = 0;
	while ($i < $nombre_lignes){
		$classe = mysql_result($call_data, $i, "classe");
		$ide_classe = mysql_result($call_data, $i, "id");
		echo "<option value='$ide_classe'";
		if($nombre_lignes==1) {echo " selected";}
		echo ">$classe</option>\n";
		$i++;
	}
	echo "</select></p>\n";
	echo "<p>\n<label id='avec_app_label' style='cursor: pointer;'><input type='checkbox' name='avec_app' id='avec_app' value='y' checked='checked' /> Avec les appr�ciations</label>\n";
	echo "<input type='submit' name='choix_classe' value='Envoyer' />\n</p>\n";
	
	echo "</blockquote>\n";
	echo "</form>\n";
	
	// Fermeture du DIV container initialis� dans le header.inc
	echo "</div>\n";
	require("../../lib/footer.inc.php");
	die();
	
}

//====================================
//=== FIN DU FORMULAIRE DE CHOIX DES CLASSES ===
//====================================


//==============================
// === Construction du tableau fiche brevet===
//==============================
	
// On r�cup�re le tableau des param�tres associ�s � ce type de brevet:
$tabmatieres=tabmatieres($type_brevet);

// tableau de correspondance Champs de $tabmatieres -> champs de publipostage OOo
$tab_champs_OOo=array();
for($j=101;$j<=122;$j++){
	if($tabmatieres[$j][0]!=''){
		$tab_champs_OOo[$j]=array();
		$tab_champs_OOo[$j][0]=$j;												// code de la mati�re
		$tab_champs_OOo[$j][1]=$tabmatieres[$j][0];			// Nom long
		$tab_champs_OOo[$j][2]="fb_note_".$j;						// Nom variable OOo
		switch($tabmatieres[$j][-1]){											// Coefficient
			case "NOTNONCA":																// Note non comptabilis�e
				$tab_champs_OOo[$j][3]="-1";
			break;
			case "PTSUP":																		// Seuls les points au dessus de la moyenne sont comptabilis�s
				$tab_champs_OOo[$j][3]="0";
			break;
			case "POINTS":																		// On r�cup�re le coef
				if ($tabmatieres[$j]['socle']=='n'){
					$tab_champs_OOo[$j][3]=$tabmatieres[$j][-2];	// On r�cup�re le coef
				} else{
					$tab_champs_OOo[$j][3]="-2";									// cas du B2I et A2 langue
				}
			break;
		}
	}
}




/***** Donn�es communes *****/
$fb_academie=getSettingValue("fb_academie");
$fb_departement=getSettingValue("fb_departement");
$fb_session=getSettingValue("fb_session");
// Si la session n'est pas renseign�e, on la calcule
if($fb_session==""){
	$tmp_date=getdate();
	$tmp_mois=$tmp_date['mon'];
	if($tmp_mois>9){
		$fb_session=$tmp_date['year']+1;
	}
	else{
		$fb_session=$tmp_date['year'];
	}
}
// Mode de calcul des moyennes
$fb_mode_moyenne=getSettingValue("fb_mode_moyenne");
if($fb_mode_moyenne!=2){$fb_mode_moyenne=1;}			


/***** Fin des donn�es communes *****/



/***** Faut-il afficher le lieu de naissance *****/
$ele_lieu_naissance=getSettingValue("ele_lieu_naissance") ? getSettingValue("ele_lieu_naissance") : "n";

// cr�ation d'un tableau pour stocker les notes d'�l�ves
$tab_eleves_OOo=array();
$nb_eleve=0;

// BOUCLE SUR LA LISTE DES CLASSES

for($i=0;$i<count($id_classe);$i++){

	// Calcul des moyennes de classes... pb avec le statut...
	$moy_classe=array();
	for($j=101;$j<=122;$j++){
		if($tabmatieres[$j][0]!=''){
			$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet WHERE note!='DI' AND note!='AB' AND note!='NN' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
			$res_moy=mysql_query($sql);
			if(mysql_num_rows($res_moy)>0){
				$lig_moy=mysql_fetch_object($res_moy);
				$moy_classe[$j]=$lig_moy->moyenne;
			}
			else{
				$moy_classe[$j]="";
			}
		}
	}


	// R�cup�ration du statut des mati�res : ceux valid�s lors du traitement NOTANET
	// pour rep�rer les mati�res non dispens�es.
	for($j=101;$j<=122;$j++){
		if($tabmatieres[$j][0]!=''){
			$sql="SELECT * FROM notanet_corresp WHERE notanet_mat='".$tabmatieres[$j][0]."' AND type_brevet='$type_brevet' LIMIT 1";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0){
				$lig=mysql_fetch_object($res);
				$tabmatieres[$j][-4]=$lig->statut;
				$tabmatieres[$j][-5]=$lig->matiere;
			}
			else{
				$tabmatieres[$j][-4]="";
				$tabmatieres[$j][-5]="";
			}
		}
	}


	//echo "<div class='noprint'>\n";

	$sql="SELECT DISTINCT e.* FROM eleves e,
									notanet n,
									notanet_ele_type net
							WHERE n.id_classe='$id_classe[$i]' AND
									n.login=e.login AND
									net.login=n.login AND
									net.type_brevet='$type_brevet'
							ORDER BY e.login;";
	$res1=mysql_query($sql);
	if(mysql_num_rows($res1)>0){
		// Boucle sur la liste des �l�ves
		while($lig1=mysql_fetch_object($res1)){

			$tab_eleves_OOo[$nb_eleve]=array();
			$tab_eleves_OOo[$nb_eleve]['nom']=$lig1->nom;
			$tab_eleves_OOo[$nb_eleve]['prenom']=$lig1->prenom;
			$tab_eleves_OOo[$nb_eleve]['fille']="";										// on initialise les champs pour ne pas avoir d'erreurs
			if($lig1->sexe=='F') {$tab_eleves_OOo[$nb_eleve]['fille']="e";} // ajouter un e � n�e si l'�l�ve est une fille
			$tab_eleves_OOo[$nb_eleve]['date_nais']=formate_date($lig1->naissance);
			$tab_eleves_OOo[$nb_eleve]['lieu_nais']="";										// on initialise les champs pour ne pas avoir d'erreurs
			if($ele_lieu_naissance=="y") {$tab_eleves_OOo[$nb_eleve]['lieu_nais']=preg_replace ( '@<[\/\!]*?[^<>]*?>@si'  , ''  , get_commune($lig1->lieu_naissance,1)) ;} // r�cup�rer la commune
			$tab_eleves_OOo[$nb_eleve]['ecole']=$gepiSchoolName;
			$tab_eleves_OOo[$nb_eleve]['adresse1']=$gepiSchoolAdress1;
			$tab_eleves_OOo[$nb_eleve]['adresse2']=$gepiSchoolAdress2;
			$tab_eleves_OOo[$nb_eleve]['codeposte']=$gepiSchoolZipCode;
			$tab_eleves_OOo[$nb_eleve]['commune']=$gepiSchoolCity;
			$tab_eleves_OOo[$nb_eleve]['acad']=$fb_academie;
			$tab_eleves_OOo[$nb_eleve]['departe']=$fb_departement;
			$tab_eleves_OOo[$nb_eleve]['session']=$fb_session;
			
			$sql="SELECT doublant FROM j_eleves_regime WHERE login='".$lig1->login."';";
			$res_reg=mysql_query($sql);
			$doublant='n';
			if(mysql_num_rows($res_reg)>0) {
				$lig_reg=mysql_fetch_object($res_reg);
				if($lig_reg->doublant=='R') {
					$doublant='y';
				}
			}
			$tab_eleves_OOo[$nb_eleve]['redoublant']=$doublant;

			//=======================================
							
			if(($type_brevet==0)||($type_brevet==1)||($type_brevet==5)||($type_brevet==6)){

				$TOTAL=0;
				$TOTAL_COEF=0;
				$TOTAL_POINTS=0;
				for($j=101;$j<=122;$j++){
				
					//if ($tab_champs_OOo[$j][0]!='') {
					if ((isset($tab_champs_OOo[$j][0]))&&($tab_champs_OOo[$j][0]!='')) {
					
						$tab_eleves_OOo[$nb_eleve][$j]=array();
						for($l=0;$l<=4;$l++){																				
							$tab_eleves_OOo[$nb_eleve][$j][$l]=""; 										// on initialise les champs pour ne pas avoir d'erreurs
						}
						if($tab_champs_OOo[$j][3]>-2) {$tab_eleves_OOo[$nb_eleve][$j][3] = $moy_classe[$j];}
					
						$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
						$res_note=mysql_query($sql);
						if(mysql_num_rows($res_note)>0){
							$lig_note=mysql_fetch_object($res_note);
							$tab_eleves_OOo[$nb_eleve][$j][0]=$lig_note->note;			// On r�cup�re la note
							
							switch($tab_champs_OOo[$j][3]){
								case '-2':																							// Socle B2I et A2
									// rien � faire ?
								break;
								case '-1':																							// Note non prise en compte dans le calcul
									// on calcule la moyenne de la mati�re
									include("fb_moyenne.inc.php");	
									// on va chercher les appr�ciations si besoin
									include("fb_appreciation.inc.php");				
								break;			
								case '0':																							// Seuls les points au dessus de la moyenne comptent
									// on cherche le nom de l'option
									$sql_mat_fac="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_mat_fac=mysql_query($sql_mat_fac);
									if(mysql_num_rows($res_mat_fac)>0){
										$lig_mat_fac=mysql_fetch_object($res_mat_fac);
										$tab_eleves_OOo[$nb_eleve][$j][2]=ucfirst(accent_min(strtolower($lig_mat_fac->matiere)));
									}
									// on calcule la moyenne de la mati�re
									include("fb_moyenne.inc.php");
									
									// on va chercher les appr�ciations si besoin
									include("fb_appreciation.inc.php");
									
									// on extrait les points � ajouter
									if($tab_eleves_OOo[$nb_eleve][$j][0]>10) {
										$tab_eleves_OOo[$nb_eleve][$j][1]=($lig_note->note)-10;
										$TOTAL_POINTS= $TOTAL_POINTS+$tab_eleves_OOo[$nb_eleve][$j][1];
									}
								break;				
								default:									
									// on cherche le nom de la mati�re
									$sql_mat_fac="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_mat_fac=mysql_query($sql_mat_fac);
									if(mysql_num_rows($res_mat_fac)>0){
										$lig_mat_fac=mysql_fetch_object($res_mat_fac);
										$tab_eleves_OOo[$nb_eleve][$j][2]=ucfirst(accent_min(strtolower($lig_mat_fac->matiere)));
									}							
									
									// On calcul la note coefficient�e
									if($tab_eleves_OOo[$nb_eleve][$j][0]!="DI" && $tab_eleves_OOo[$nb_eleve][$j][0]!="NN" && $tab_eleves_OOo[$nb_eleve][$j][0]!="ABS") {
										$tab_eleves_OOo[$nb_eleve][$j][1]=($lig_note->note)*$tab_champs_OOo[$j][3];
										$TOTAL_POINTS =$TOTAL_POINTS+$tab_eleves_OOo[$nb_eleve][$j][1];
										$TOTAL_COEF= $TOTAL_COEF+$tab_champs_OOo[$j][3];
									}else {
										$tab_eleves_OOo[$nb_eleve][$j][1]="";
									}
									// on calcule la moyenne de la mati�re
									include("fb_moyenne.inc.php");
									
									// on va chercher les appr�ciations si besoin
									include("fb_appreciation.inc.php");
									
								//break;																	
							}
						}else{				// l'�leve n'a pas de note
							// on va chercher les appr�ciations si besoin
							include("fb_appreciation.inc.php");
						}
					}
				}					
			}
			// ************************************************************************************************
			// ************************************************************************************************
			// ************************************************************************************************	
			
			// AUTRE TYPE DE BREVET
			elseif(($type_brevet==2)||($type_brevet==3)){
				// � faire
			}
			// ************************************************************************************************
			// ************************************************************************************************
			// ************************************************************************************************
			// AUTRE TYPE DE BREVET
			elseif(($type_brevet==4)||($type_brevet==7)){
				// � faire
			}
			else{
				// echo "<p>BIZARRE! Ce type de brevet n'est pas pr�vu</p>";
			}
			
			// ************************************************************************************************
			//	 On r�cup�re l'avis du chef d'�tablissement
			$tab_eleves_OOo[$nb_eleve]['decision']="";						// on initialise le champ pour ne pas avoir d'erreur
			$tab_eleves_OOo[$nb_eleve]['appreciation']= "";			// on initialise le champ pour ne pas avoir d'erreur
			
			$sql="SELECT * FROM notanet_avis WHERE login='$lig1->login';";
			$res_avis=mysql_query($sql);
			if(mysql_num_rows($res_avis)>0) {
				$lig_avis=mysql_fetch_object($res_avis);
				if($lig_avis->favorable=="O") {$tab_eleves_OOo[$nb_eleve]['decision']="Avis favorable.";}
				elseif($lig_avis->favorable=="N") {$tab_eleves_OOo[$nb_eleve]['decision']="Avis d�favorable.";}
				$tab_eleves_OOo[$nb_eleve]['appreciation']= htmlentities($lig_avis->avis);
			}
			
			$tab_eleves_OOo[$nb_eleve]['totalpoints']=$TOTAL_POINTS;
			$tab_eleves_OOo[$nb_eleve]['totalcoef']=$TOTAL_COEF*20;
			$tab_eleves_OOo[$nb_eleve]['classe']=get_classe_from_id($id_classe[$i]);
			
				
			
			$nb_eleve=$nb_eleve+1;
		}
		// Fin de la boucle sur la liste des �l�ves
	}

	// FIN DE LA BOUCLE SUR LA LISTE DES CLASSES
}

//================================
// === Fin construction du tableau fiche brevet===
//================================




?>