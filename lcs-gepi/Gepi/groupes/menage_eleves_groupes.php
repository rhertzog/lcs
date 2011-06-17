<?php
/*
* $Id: menage_eleves_groupes.php 5920 2010-11-20 21:04:58Z crob $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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


$sql="SELECT 1=1 FROM droits WHERE id='/groupes/menage_eleves_groupes.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/groupes/menage_eleves_groupes.php',
	administrateur='V',
	professeur='F',
	cpe='F',
	scolarite='F',
	eleve='F',
	responsable='F',
	secours='F',
	autre='F',
	description='Groupes: Desinscription des eleves sans notes ni appreciations',
	statut='';";
	$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Initialisation des variables
$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
$num_periode = isset($_GET['num_periode']) ? $_GET['num_periode'] : (isset($_POST['num_periode']) ? $_POST["num_periode"] : NULL);

if((isset($id_classe))&&(isset($num_periode))&&(isset($_GET['confirmation_menage']))&&($_GET['confirmation_menage']=='y')) {
	check_token();

	$nb_desinscriptions=0;
	$nb_erreurs_desinscriptions=0;
	if((preg_match("/^[0-9]*$/",$id_classe))&&(preg_match("/^[0-9]*$/",$num_periode))) {
		$sql="SELECT 1=1 FROM periodes WHERE id_classe='$id_classe' AND num_periode='$num_periode';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==1) {
			//$groups=get_groups_for_class($id_classe);
			//foreach($groups as $current_group) {
			$sql="select g.id from groupes g, j_groupes_classes j where (g.id = j.id_groupe and j.id_classe='".$id_classe."') ORDER BY j.priorite, g.name";
			$query=mysql_query($sql);
			while($lig_group=mysql_fetch_object($query)) {
				$current_group=get_group($lig_group->id);
				foreach($current_group["eleves"][$num_periode]["users"] as $tab_ele) {
					// Pour ne traiter que les �l�ves de la classe courante:
					$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='".$tab_ele['login']."' AND periode='$num_periode' AND id_classe='$id_classe';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0) {
						if (test_before_eleve_removal($tab_ele['login'], $current_group['id'], $num_periode)) {
							$sql="DELETE FROM j_eleves_groupes WHERE id_groupe='".$current_group['id']."' AND login='".$tab_ele['login']."' AND periode='".$num_periode."';";
							//echo "$sql<br />\n";
							$resultat_nettoyage=mysql_query($sql);
							if($resultat_nettoyage) {
								$nb_desinscriptions++;
							}
							else {
								$nb_erreurs_desinscriptions++;
							}
						}
					}
				}
			}
		}
	}

	if($nb_desinscriptions==0) {
		$msg="Aucune d�sinscription n'a �t� effectu�e.";
	}
	elseif($nb_desinscriptions==0) {
		$msg="Une d�sinscription a �t� effectu�e.";
	}
	else {
		$msg="$nb_desinscriptions d�sinscriptions ont �t� effectu�es.";
	}

	if($nb_erreurs_desinscriptions>0) {
		$msg.="<br />$nb_erreurs_desinscriptions erreur(s) a(ont) eu lieu lors de la d�sinscription d'�l�ves.";
	}
}

// =================================
$chaine_options_classes="";
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysql_query($sql);
if(mysql_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;

	$cpt_classe=0;
	$num_classe=-1;

	if(!isset($id_classe)) {
		// On choisit la premi�re classe de la liste
		$lig_class_tmp=mysql_fetch_object($res_class_tmp);
		$id_classe=$lig_class_tmp->id;

		// On relance la requ�te pour r�cup�rer le suivant et la chaine des classes
		$sql="SELECT id, classe FROM classes ORDER BY classe";
		$res_class_tmp=mysql_query($sql);
	}

	while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
			$temoin_tmp=1;
			if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
		}

		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}

		$cpt_classe++;
	}
}
// =================================

$themessage  = 'Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
$titre_page = "Gestion des groupes: M�nage";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

//debug_var();

echo "<script type='text/javascript' language='javascript'>
// Initialisation
change='no';

function CocheCase(boul) {
	nbelements = document.formulaire.elements.length;
	for (i = 0 ; i < nbelements ; i++) {
	if (document.formulaire.elements[i].type =='checkbox')
		document.formulaire.elements[i].checked = boul ;
	}
}
";

echo "function CocheLigne(ki) {
	for (var i=1;i<$nb_periode;i++) {
		if(document.getElementById('case_'+i+'_'+ki)){
			document.getElementById('case_'+i+'_'+ki).checked = true;
		}
	}
}
";

echo "function DecocheLigne(ki) {
	for (var i=1;i<$nb_periode;i++) {
		if(document.getElementById('case_'+i+'_'+ki)){
			document.getElementById('case_'+i+'_'+ki).checked = false;
		}
	}
}
</script>\n";

/*
if(!isset($id_classe)) {
	// Tableau de choix de la classe
	require("../lib/footer.inc.php");
	die();
}
*/

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
echo "<p class='bold'>\n";
echo "<a href='edit_class.php?id_classe=$id_classe'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo "><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

$chaine_optionnelle="";
if(isset($num_periode)) {
	$chaine_optionnelle="&amp;num_periode=$num_periode";
	echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
}

// Choisir une autre classe
//echo " | <a href='".$_SERVER['PHP_SELF']."'>Faire le m�nage pour une autre classe</a>";
if($id_class_prec!=0){
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=".$id_class_prec.$chaine_optionnelle."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe pr�c�dente</a>";
}

if($chaine_options_classes!="") {
	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";

	echo " | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}

if($id_class_suiv!=0){
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=".$id_class_suiv.$chaine_optionnelle."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>";
}
echo "</p>\n";
echo "</form>\n";

$classe=get_class_from_id($id_classe);
echo "<p>Cette page est destin�e � d�sinscrire des groupes/enseignements de la classe de <b>".$classe."</b> pour une p�riode donn�e, tous les �l�ves qui n'ont ni note ni appr�ciation sur le bulletin.</p>\n";

if(!isset($num_periode)) {
	echo "<p>Pour quelle p�riode souhaitez-vous effectuer le m�nage dans la classe de <b>".$classe."</b>?</p>\n";
	$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode;";
	$res_per=mysql_query($sql);
	while($lig_per=mysql_fetch_object($res_per)) {
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;num_periode=$lig_per->num_periode'>$lig_per->nom_periode</a><br />\n";
	}

	require("../lib/footer.inc.php");
	die();
}

if((isset($_GET['confirmation_menage']))&&($_GET['confirmation_menage']=='y')) {
	echo "<p>Voici les groupes apr�s validation des d�sinscriptions.</p>\n";
}
else {
	echo "<p>Contr�le des d�sinscriptions � effectuer en <b>$classe</b> en <b>p�riode $num_periode</b>.";
	echo "<br />\n";
	echo "En <span style='color: green'>vert</span> la liste des �l�ves � <span style='color: green'>conserver</span> dans l'enseignement et en <span style='color: red'>rouge</span> ceux qui seront <span style='color: red'>d�sinscrits</span> de l'enseignement si vous validez.</p>\n";
}
//$groups=get_groups_for_class($id_classe);
//foreach($groups as $current_group) {

$sql="select g.id from groupes g, j_groupes_classes j where (g.id = j.id_groupe and j.id_classe='".$id_classe."') ORDER BY j.priorite, g.name";
$query=mysql_query($sql);
while($lig_group=mysql_fetch_object($query)) {
	$current_group=get_group($lig_group->id);

	echo "<p>Liste des �l�ves en ".htmlentities($current_group["name"])." - ".htmlentities($current_group["description"])." (<i>".$current_group["classlist_string"]."</i>)<br />\n";
	/*
	echo "<pre>";
	print_r($current_group);
	echo "</pre>";
	*/
	$cpt=0;
	foreach($current_group["eleves"][$num_periode]["users"] as $tab_ele) {
		// Pour ne traiter que les �l�ves de la classe courante:
		$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='".$tab_ele['login']."' AND periode='$num_periode' AND id_classe='$id_classe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			if($cpt>0) {echo ", ";}
			if (test_before_eleve_removal($tab_ele['login'], $current_group['id'], $num_periode)) {
				echo "<span style='color: red'>";
			}
			else {
				echo "<span style='color: green'>";
			}
			echo $tab_ele['nom']." ".$tab_ele['prenom'];
			echo "</span>\n";
			$cpt++;
		}
	}
	echo "</p>\n";
}

if((!isset($_GET['confirmation_menage']))||($_GET['confirmation_menage']=='n')) {
	echo "<p><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;num_periode=$num_periode&amp;confirmation_menage=y".add_token_in_url()."'>Confirmer les d�sinscriptions</a>.</p>\n";
}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");

?>