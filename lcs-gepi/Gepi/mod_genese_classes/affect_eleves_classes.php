<?php
/* $Id: affect_eleves_classes.php 3323 2009-08-05 10:06:18Z crob $ */
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/affect_eleves_classes.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/affect_eleves_classes.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='G�n�se des classes: Affectation des �l�ves',
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


//if((isset($_POST['is_posted']))&&(isset($_POST['valide_aff_classe_fut']))) {
if(isset($_POST['is_posted'])) {
	//echo "GRRRRRRR";
	$eleve=isset($_POST['eleve']) ? $_POST['eleve'] : array();
	$classe_fut=isset($_POST['classe_fut']) ? $_POST['classe_fut'] : array();

	//echo "count(\$eleve)=".count($eleve)."<br />";

	$nb_reg=0;
	$nb_err=0;

	$complement_msg="";
	if(count($eleve)>0) {
		//$sql="DELETE FROM gc_eleve_fut_classe WHERE projet='$projet';";
		//$del=mysql_query($sql);

		$profil=isset($_POST['profil']) ? $_POST['profil'] : array();

		for($i=0;$i<count($eleve);$i++) {
			/*
			$sql="DELETE FROM gc_eleve_fut_classe WHERE projet='$projet' AND login='$eleve[$i]';";
			//echo "$sql<br />";
			$del=mysql_query($sql);

			$sql="INSERT INTO gc_eleve_fut_classe SET login='$eleve[$i]', classe='$classe_fut[$i]', projet='$projet';";
			//echo "$sql<br />";
			if($insert=mysql_query($sql)) {$nb_reg++;} else {$nb_err++;}
			*/

			if(!isset($classe_fut[$i])) {
				$complement_msg.="<br />Erreur sur la classe de $eleve[$i]";
			}
			else {
				$sql="UPDATE gc_eleves_options SET classe_future='$classe_fut[$i]', profil='$profil[$i]' WHERE login='$eleve[$i]' AND projet='$projet';";
				if($update=mysql_query($sql)) {$nb_reg++;} else {$nb_err++;}
			}
		}
	}

	if($nb_err==0) {
		$msg="$nb_reg enregistrements effectu�s.";
	}
	else {
		$msg="ERREUR: $nb_err erreurs lors de l'enregistrement des classes futures,...";
	}
	$msg.=$complement_msg;
}

$themessage  = 'Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "G�n�se classe: affectation des �l�ves";
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
//echo "<p class='bold'><a href='index.php?projet=$projet'>Retour</a>";
//echo "</div>\n";

$choix_affich=isset($_POST['choix_affich']) ? $_POST['choix_affich'] : (isset($_GET['choix_affich']) ? $_GET['choix_affich'] : NULL);

// Choix des �l�ves � afficher:
//if(!isset($_POST['choix_affich'])) {
if(!isset($choix_affich)) {
	echo "<p class='bold'><a href='index.php?projet=$projet'>Retour</a>";
	echo "</p>\n";

	echo "<h2>Projet $projet</h2>\n";

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
	
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

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
	echo "<p align='center'><input type='submit' name='choix_affich' value='Valider' /></p>\n";

	echo "</form>\n";

	echo "<p><i>NOTES&nbsp;:</i></p>\n";
	echo "<ul>\n";
	echo "<li>En s�lectionnant toutes les classes futures et les Non affect�s, on obtient une liste avec les effectifs utiles dans les options.<br />
	En haut de tableau, on a les effectifs totaux et en bas de tableau, on a les effectifs de la s�lection.</li>";
	echo "<li>Les colonnes Classe actuelle et Classe future sont trait�es suivant le mode OU<br />
	Si vous cochez deux classes, les �l�ves pris en compte seront '<i>membre de Classe 1 OU membre de Classe 2</i>'</li>\n";
	echo "<li>Les colonnes d'options sont trait�es suivant le mode ET.<br />
	Ce sera par exemple '<i>Avec AGL1 ET Avec ESP2 ET Avec LATIN ET Sans DECP3</i>'</li>\n";
	echo "</ul>\n";
}
else {
	echo "<p class='bold'><a href='index.php?projet=$projet'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Retour</a>";

	echo " | <a href='".$_SERVER['PHP_SELF']."?projet=$projet'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Autre s�lection</a>";
	echo "</p>\n";

	echo "<h2>Projet $projet</h2>\n";

	//echo "<p style='color:red'>A FAIRE ENCORE: rappeler la requete.</p>\n";

	// Affichage...
	//Construire la requ�te SQL et l'afficher

	$id_clas_act=isset($_POST['id_clas_act']) ? $_POST['id_clas_act'] : (isset($_GET['id_clas_act']) ? $_GET['id_clas_act'] : array());
	$clas_fut=isset($_POST['clas_fut']) ? $_POST['clas_fut'] : (isset($_GET['clas_fut']) ? $_GET['clas_fut'] : array());
	$avec_lv1=isset($_POST['avec_lv1']) ? $_POST['avec_lv1'] : (isset($_GET['avec_lv1']) ? $_GET['avec_lv1'] : array());
	$sans_lv1=isset($_POST['sans_lv1']) ? $_POST['sans_lv1'] : (isset($_GET['sans_lv1']) ? $_GET['sans_lv1'] : array());
	$avec_lv2=isset($_POST['avec_lv2']) ? $_POST['avec_lv2'] : (isset($_GET['avec_lv2']) ? $_GET['avec_lv2'] : array());
	$sans_lv2=isset($_POST['sans_lv2']) ? $_POST['sans_lv2'] : (isset($_GET['sans_lv2']) ? $_GET['sans_lv2'] : array());
	$avec_lv3=isset($_POST['avec_lv3']) ? $_POST['avec_lv3'] : (isset($_GET['avec_lv3']) ? $_GET['avec_lv3'] : array());
	$sans_lv3=isset($_POST['sans_lv3']) ? $_POST['sans_lv3'] : (isset($_GET['sans_lv3']) ? $_GET['sans_lv3'] : array());
	$avec_autre=isset($_POST['avec_autre']) ? $_POST['avec_autre'] : (isset($_GET['avec_autre']) ? $_GET['avec_autre'] : array());
	$sans_autre=isset($_POST['sans_autre']) ? $_POST['sans_autre'] : (isset($_GET['sans_autre']) ? $_GET['sans_autre'] : array());

	//=========================
	// D�but de la requ�te � forger pour ne retenir que les �l�ves souhait�s
	$sql_ele="SELECT DISTINCT login FROM gc_eleves_options WHERE projet='$projet' AND classe_future!='Dep' AND classe_future!='Red'";

	$sql_ele_id_classe_act="";
	$sql_ele_classe_fut="";
	//=========================

	$chaine_classes_actuelles="";
	if(count($id_clas_act)>0) {
		for($i=0;$i<count($id_clas_act);$i++) {
			if($i>0) {$sql_ele_id_classe_act.=" OR ";}
			$sql_ele_id_classe_act.="id_classe_actuelle='$id_clas_act[$i]'";

			if($i>0) {$chaine_classes_actuelles.=", ";}
			$chaine_classes_actuelles.=get_class_from_id($id_clas_act[$i]);
		}
		$sql_ele.=" AND ($sql_ele_id_classe_act)";
	}

	$chaine_classes_futures="";
	if(count($clas_fut)>0) {
		for($i=0;$i<count($clas_fut);$i++) {
			if($i>0) {$sql_ele_classe_fut.=" OR ";}
			$sql_ele_classe_fut.="classe_future='$clas_fut[$i]'";

			if($i>0) {$chaine_classes_futures.=", ";}
			if($clas_fut[$i]=='') {$chaine_classes_futures.='Non.aff';} else {$chaine_classes_futures.=$clas_fut[$i];}
		}
		$sql_ele.=" AND ($sql_ele_classe_fut)";
	}

	$chaine_avec_opt="";
	for($i=0;$i<count($avec_lv1);$i++) {
		$sql_ele.=" AND liste_opt LIKE '%|$avec_lv1[$i]|%'";

		if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
		$chaine_avec_opt.="<span style='color:green;'>".$avec_lv1[$i]."</span>";
	}

	for($i=0;$i<count($avec_lv2);$i++) {
		$sql_ele.=" AND liste_opt LIKE '%|$avec_lv2[$i]|%'";

		if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
		$chaine_avec_opt.="<span style='color:green;'>".$avec_lv2[$i]."</span>";
	}

	for($i=0;$i<count($avec_lv3);$i++) {
		$sql_ele.=" AND liste_opt LIKE '%|$avec_lv3[$i]|%'";

		if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
		$chaine_avec_opt.="<span style='color:green;'>".$avec_lv3[$i]."</span>";
	}

	for($i=0;$i<count($avec_autre);$i++) {
		$sql_ele.=" AND liste_opt LIKE '%|$avec_autre[$i]|%'";

		if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
		$chaine_avec_opt.="<span style='color:green;'>".$avec_autre[$i]."</span>";
	}

	$chaine_sans_opt="";
	for($i=0;$i<count($sans_lv1);$i++) {
		$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_lv1[$i]|%'";

		if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
		$chaine_sans_opt.="<span style='color:red;'>".$sans_lv1[$i]."</span>";
	}

	for($i=0;$i<count($sans_lv2);$i++) {
		$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_lv2[$i]|%'";

		if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
		$chaine_sans_opt.="<span style='color:red;'>".$sans_lv2[$i]."</span>";
	}

	for($i=0;$i<count($sans_lv3);$i++) {
		$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_lv3[$i]|%'";

		if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
		$chaine_sans_opt.="<span style='color:red;'>".$sans_lv3[$i]."</span>";
	}

	for($i=0;$i<count($sans_autre);$i++) {
		$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_autre[$i]|%'";

		if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
		$chaine_sans_opt.="<span style='color:red;'>".$sans_autre[$i]."</span>";
	}

	$tab_ele=array();
	$sql_ele.=";";
	//echo "$sql_ele<br />\n";
	$res_ele=mysql_query($sql_ele);
	while ($lig_ele=mysql_fetch_object($res_ele)) {
		$tab_ele[]=$lig_ele->login;
	}

	// Rappel de la requ�te:
	echo "<p>";
	if($chaine_classes_actuelles!="") {echo "Classes actuelles $chaine_classes_actuelles<br />\n";}
	if($chaine_classes_futures!="") {echo "Classes futures $chaine_classes_futures<br />\n";}
	if($chaine_avec_opt!="") {echo "Avec $chaine_avec_opt<br />\n";}
	if($chaine_sans_opt!="") {echo "Sans $chaine_sans_opt<br />\n";}
	echo "&nbsp;</p>\n";

/*
$_POST['id_clas_act']=	Array (*)
$_POST['id_clas_act'][0]=	23
$_POST['id_clas_act'][1]=	24
$_POST['avec_lv1']=	Array (*)
$_POST['avec_lv1'][0]=	AGL1
$_POST['avec_lv2']=	Array (*)
$_POST['avec_lv2'][0]=	ESP2
$_POST['avec_autre']=	Array (*)
$_POST['avec_autre'][0]=	LATIN
$_POST['projet']=	4eme_vers_3eme
*/


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
		while($lig=mysql_fetch_object($res)) {
			$classe_fut[]=$lig->classe;

			$tab_opt_exclue["$lig->classe"]=array();
			//=========================
			// Options exlues pour la classe
			$sql="SELECT opt_exclue FROM gc_options_classes WHERE projet='$projet' AND classe_future='$lig->classe';";
			$res_opt_exclues=mysql_query($sql);
			while($lig_opt_exclue=mysql_fetch_object($res_opt_exclues)) {
				$tab_opt_exclue["$lig->classe"][]=$lig_opt_exclue->opt_exclue;
			}
			//=========================

		}
		$classe_fut[]="Red";
		$classe_fut[]="Dep";
		$classe_fut[]=""; // Vide pour les Non Affect�s
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
	
	$lv1=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv1' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$lv1[]=$lig->opt;
		}
	}
	
	
	$lv2=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv2' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$lv2[]=$lig->opt;
		}
	}
	
	$lv3=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv3' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$lv3[]=$lig->opt;
		}
	}
	
	$autre_opt=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='autre' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$autre_opt[]=$lig->opt;
		}
	}
	
	//=============================
	include("lib_gc.php");
	// On y initialise les couleurs
	// Il faut que le tableaux $classe_fut soit initialis�.
	//=============================
	
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

	for($i=0;$i<count($avec_lv1);$i++) {
		echo "<input type='hidden' name='avec_lv1[$i]' value='$avec_lv1[$i]' />\n";
	}
	for($i=0;$i<count($avec_lv2);$i++) {
		echo "<input type='hidden' name='avec_lv2[$i]' value='$avec_lv2[$i]' />\n";
	}
	for($i=0;$i<count($avec_lv3);$i++) {
		echo "<input type='hidden' name='avec_lv3[$i]' value='$avec_lv3[$i]' />\n";
	}
	for($i=0;$i<count($avec_autre);$i++) {
		echo "<input type='hidden' name='avec_autre[$i]' value='$avec_autre[$i]' />\n";
	}

	for($i=0;$i<count($sans_lv1);$i++) {
		echo "<input type='hidden' name='sans_lv1[$i]' value='$sans_lv1[$i]' />\n";
	}
	for($i=0;$i<count($sans_lv2);$i++) {
		echo "<input type='hidden' name='sans_lv2[$i]' value='$sans_lv2[$i]' />\n";
	}
	for($i=0;$i<count($sans_lv3);$i++) {
		echo "<input type='hidden' name='sans_lv3[$i]' value='$sans_lv3[$i]' />\n";
	}
	for($i=0;$i<count($sans_autre);$i++) {
		echo "<input type='hidden' name='sans_autre[$i]' value='$sans_autre[$i]' />\n";
	}

	for($i=0;$i<count($id_clas_act);$i++) {
		echo "<input type='hidden' name='id_clas_act[$i]' value='$id_clas_act[$i]' />\n";
	}

	for($i=0;$i<count($clas_fut);$i++) {
		echo "<input type='hidden' name='clas_fut[$i]' value='$clas_fut[$i]' />\n";
	}


	// Colorisation
	echo "<p>Colorisation&nbsp;: ";
	echo "<select name='colorisation' onchange='lance_colorisation()'>
	<option value='classe_fut' selected>Classe future</option>
	<option value='lv1'>LV1</option>
	<option value='lv2'>LV2</option>
	<option value='profil'>Profil</option>
	</select>\n";
	
	echo "</p>\n";



	$eff_fut_classe_hors_selection=array();
	$eff_fut_classe_hors_selection_F=array();
	$eff_fut_classe_hors_selection_M=array();

	echo "<p align='center'><input type='submit' name='valide_aff_classe_fut0' value='Valider' /></p>\n";

	echo "<table class='boireaus' border='1' summary='Tableau des options'>\n";

	//==========================================
	echo "<tr>\n";
	echo "<th rowspan='2'>El�ve</th>\n";
	echo "<th rowspan='2'>Sexe</th>\n";
	echo "<th rowspan='2'>Classe<br />actuelle</th>\n";
	echo "<th rowspan='2'>Profil</th>\n";
	echo "<th rowspan='2'>Niveau</th>\n";
	echo "<th rowspan='2'>Absences Non.Just Retards</th>\n";

	//if(count($classe_fut)>0) {echo "<th colspan='".(count($classe_fut)+2)."'>Classes futures</th>\n";}
	if(count($classe_fut)>0) {echo "<th colspan='".count($classe_fut)."'>Classes futures</th>\n";}
	if(count($lv1)>0) {echo "<th colspan='".count($lv1)."'>LV1</th>\n";}
	if(count($lv2)>0) {echo "<th colspan='".count($lv2)."'>LV2</th>\n";}
	if(count($lv3)>0) {echo "<th colspan='".count($lv3)."'>LV3</th>\n";}
	if(count($autre_opt)>0) {echo "<th colspan='".count($autre_opt)."'>Autres options</th>\n";}
	echo "</tr>\n";
	//==========================================
	echo "<tr>\n";
	for($i=0;$i<count($classe_fut);$i++) {
		echo "<th>$classe_fut[$i]</th>\n";

		// Initialisation
		$eff_fut_classe_hors_selection[$i]=0;
		$eff_fut_classe_hors_selection_F[$i]=0;
		$eff_fut_classe_hors_selection_M[$i]=0;
	}
	for($i=0;$i<count($lv1);$i++) {
		echo "<th>$lv1[$i]</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		echo "<th>$lv2[$i]</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		echo "<th>$lv3[$i]</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		echo "<th>$autre_opt[$i]</th>\n";
	}
	echo "</tr>\n";
	//==========================================



	$eff_tot=0;
	$eff_tot_M=0;
	$eff_tot_F=0;

	$eff_tot_classe_M=0;
	$eff_tot_classe_F=0;
	$eff_tot_classe=0;

	$j=-1;
	//$id_classe_actuelle[$j]=-1;
	$i=-1;
	echo "<tr>\n";
	//echo "<th>Effectifs&nbsp;: <span id='eff_tot'>&nbsp;</span></th>\n";
	echo "<th>Eff.tot&nbsp;:</th>\n";
	echo "<th id='eff_tot'>...</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	for($i=0;$i<count($classe_fut);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleve_fut_classe WHERE projet='$projet' AND classe='$classe_fut[$i]';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND classe_future='$classe_fut[$i]';";
		$res=mysql_query($sql);
		$effectif_classe_fut[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_classe_fut_".$i."'>".$effectif_classe_fut[$i]."</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND opt='$lv1[$i]';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND liste_opt LIKE '%|$lv1[$i]|%';";
		$res=mysql_query($sql);
		$effectif_lv1[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_lv1_".$i."'>".$effectif_lv1[$i]."</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND opt='$lv2[$i]';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND liste_opt LIKE '%|$lv2[$i]|%';";
		$res=mysql_query($sql);
		$effectif_lv2[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_lv2_".$i."'>".$effectif_lv2[$i]."</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND opt='$lv3[$i]';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND liste_opt LIKE '%|$lv3[$i]|%';";
		$res=mysql_query($sql);
		$effectif_lv3[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_lv3_".$i."'>".$effectif_lv3[$i]."</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND opt='$autre_opt[$i]';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND liste_opt LIKE '%|$autre_opt[$i]|%';";
		$res=mysql_query($sql);
		$effectif_autre_opt[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_autre_opt_".$i."'>".$effectif_autre_opt[$i]."</th>\n";
	}
	echo "</tr>\n";
	//==========================================
	echo "<tr>\n";
	//echo "<th>Effectifs&nbsp;: <span id='eff_tot_selection'>&nbsp;</span></th>\n";
	echo "<th>Eff.tot.sexe&nbsp;:</th>\n";
	echo "<th id='eff_tot_sexe'>...</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	for($i=0;$i<count($classe_fut);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleve_fut_classe g, eleves e WHERE g.projet='$projet' AND g.classe='$classe_fut[$i]' AND e.login=g.login AND e.sexe='M';";
		$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.classe_future='$classe_fut[$i]' AND e.login=g.login AND e.sexe='M';";
		$res=mysql_query($sql);
		$effectif_classe_fut_M[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_sexe_classe_fut_".$i."'>".$effectif_classe_fut_M[$i]."/".($effectif_classe_fut[$i]-$effectif_classe_fut_M[$i])."</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.opt='$lv1[$i]' AND e.login=g.login AND e.sexe='M';";
		$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.liste_opt LIKE '%|$lv1[$i]|%' AND e.login=g.login AND e.sexe='M';";
		$res=mysql_query($sql);
		$effectif_lv1_M[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_sexe_lv1_".$i."'>".$effectif_lv1_M[$i]."/".($effectif_lv1[$i]-$effectif_lv1_M[$i])."</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.opt='$lv2[$i]' AND e.login=g.login AND e.sexe='M';";
		$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.liste_opt LIKE '%|$lv2[$i]|%' AND e.login=g.login AND e.sexe='M';";
		$res=mysql_query($sql);
		$effectif_lv2_M[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_sexe_lv2_".$i."'>".$effectif_lv2_M[$i]."/".($effectif_lv2[$i]-$effectif_lv2_M[$i])."</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.opt='$lv3[$i]' AND e.login=g.login AND e.sexe='M';";
		$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.liste_opt LIKE '%|$lv3[$i]|%' AND e.login=g.login AND e.sexe='M';";
		$res=mysql_query($sql);
		$effectif_lv3_M[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_sexe_lv3_".$i."'>".$effectif_lv3_M[$i]."/".($effectif_lv3[$i]-$effectif_lv3_M[$i])."</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.opt='$autre_opt[$i]' AND e.login=g.login AND e.sexe='M';";
		$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.liste_opt LIKE '%|$autre_opt[$i]|%' AND e.login=g.login AND e.sexe='M';";
		$res=mysql_query($sql);
		$effectif_autre_opt_M[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_sexe_autre_opt_".$i."'>".$effectif_autre_opt_M[$i]."/".($effectif_autre_opt[$i]-$effectif_autre_opt_M[$i])."</th>\n";
	}
	echo "</tr>\n";
	//==========================================
	echo "<tr>\n";
	echo "<th>Coches</th>\n";

	// Mettre l� les effectifs de la s�lection
	echo "<th id='eff_selection'>&nbsp;</th>\n";

	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	for($i=0;$i<count($classe_fut);$i++) {
		echo "<th>\n";
		echo "<a href=\"javascript:modif_colonne('classe_fut_$i',true);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
		$eff_selection_classe_fut[$i]=0;
		$eff_selection_classe_fut_M[$i]=0;
		$eff_selection_classe_fut_F[$i]=0;
		echo "</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		echo "<th id='eff_select_lv1_$i'>\n";
		//echo "<a href=\"javascript:modif_colonne('lv1_$i',true)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
		$eff_selection_lv1[$i]=0;
		$eff_selection_lv1_M[$i]=0;
		$eff_selection_lv1_F[$i]=0;
		echo "&nbsp;";
		echo "</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		echo "<th id='eff_select_lv2_$i'>\n";
		//echo "<a href=\"javascript:modif_colonne('lv2_$i',true)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
		$eff_selection_lv2[$i]=0;
		$eff_selection_lv2_M[$i]=0;
		$eff_selection_lv2_F[$i]=0;
		echo "&nbsp;";
		echo "</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		echo "<th id='eff_select_lv3_$i'>\n";
		//echo "<a href=\"javascript:modif_colonne('lv3_$i',true)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
		$eff_selection_lv3[$i]=0;
		$eff_selection_lv3_M[$i]=0;
		$eff_selection_lv3_F[$i]=0;
		echo "&nbsp;";
		echo "</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		echo "<th id='eff_select_autre_opt_$i'>\n";
		//echo "<a href=\"javascript:modif_colonne('autre_opt_$i',true)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
		//echo " / <a href=\"javascript:modif_colonne('autre_opt_$i',false)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout d�cocher' /></a>";
		echo "&nbsp;";
		$eff_selection_autre_opt[$i]=0;
		$eff_selection_autre_opt_M[$i]=0;
		$eff_selection_autre_opt_F[$i]=0;
		echo "</th>\n";
	}
	echo "</tr>\n";
	//==========================================



	$chaine_id_classe="";
	$cpt=0;
	// Boucle sur toutes les classes actuelles
	for($j=0;$j<count($id_classe_actuelle);$j++) {
		//$num_eleve1_id_classe_actuelle[$j]=$cpt;
		$eff_tot_classe_M=0;
		$eff_tot_classe_F=0;
	
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
		$res=mysql_query($sql);
		$eff_tot_classe=mysql_num_rows($res);
		$eff_tot+=$eff_tot_classe;
		//==========================================

		if(mysql_num_rows($res)>0) {
			while($lig=mysql_fetch_object($res)) {

				if(strtoupper($lig->sexe)=='F') {$eff_tot_classe_F++;$eff_tot_F++;} else {$eff_tot_classe_M++;$eff_tot_M++;}

				if(!in_array($lig->login,$tab_ele)) {

					$fut_classe="";
					//$sql="SELECT * FROM gc_eleve_fut_classe WHERE projet='$projet' AND login='$lig->login';";
					$sql="SELECT classe_future FROM gc_eleves_options WHERE projet='$projet' AND login='$lig->login';";
					$res_clas=mysql_query($sql);
					if(mysql_num_rows($res_clas)>0) {
						// On r�cup�re la classe future s'il y a d�j� un enregistrement
						while($lig_clas=mysql_fetch_object($res_clas)) {
							// On ne devrait faire qu'un tour dans la boucle
							//$fut_classe=strtoupper($lig_clas->classe);
							$fut_classe=strtoupper($lig_clas->classe_future);
						}
					}

					for($i=0;$i<count($classe_fut);$i++) {
						if($fut_classe==strtoupper($classe_fut[$i])) {
							$eff_fut_classe_hors_selection[$i]++;
							if($lig->sexe=='F') {
								$eff_fut_classe_hors_selection_F[$i]++;
							}
							else {
								$eff_fut_classe_hors_selection_M[$i]++;
							}
							break;
						}
					}

				}
				else {
					//$num_eleve2_id_classe_actuelle[$j]=$cpt;
		
					echo "<tr id='tr_eleve_$cpt' class='white_hover'>\n";
					echo "<td>\n";
					echo "<a name='eleve$cpt'></a>\n";
					if(file_exists("../photos/eleves/".$lig->elenoet.".jpg")) {
						echo "<a href='#eleve$cpt' onclick=\"affiche_photo('".$lig->elenoet.".jpg','".addslashes(strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom)))."');afficher_div('div_photo','y',100,100);return false;\">";
						echo strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom));
						echo "</a>\n";
					}
					else {
						echo strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom));
					}
					echo "<input type='hidden' name='eleve[$cpt]' value='$lig->login' />\n";
					echo "</td>\n";
					echo "<td id='eleve_sexe_$cpt'>$lig->sexe</td>\n";
					echo "<td>$classe_actuelle[$j]</td>\n";


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
						for($loop=0;$loop<count($tmp_tab);$loop++) {
							if($tmp_tab[$loop]!="") {
								$tab_ele_opt[]=strtoupper($tmp_tab[$loop]);
							}
						}
					}
					else {
						// On r�cup�re les options de l'ann�e �coul�e (ann�e qui se termine)
						// ON NE DEVRAIT PAS VENIR SUR CETTE PAGE SANS ETRE PASSE D'ABORD PAR select_eleves_options.php
						$sql="SELECT * FROM j_eleves_groupes jeg, j_groupes_matieres jgm WHERE jeg.id_groupe=jgm.id_groupe AND jeg.login='$lig->login';";
						$res_opt=mysql_query($sql);
						if(mysql_num_rows($res_opt)>0) {
							while($lig_opt=mysql_fetch_object($res_opt)) {
								$tab_ele_opt[]=strtoupper($lig_opt->id_matiere);
							}
						}
					}
					//===================================


					//echo "<td>Profil</td>\n";
					echo "<td>\n";
					echo "<input type='hidden' name='profil[$cpt]' id='profil_$cpt' value='$profil' />\n";
					echo "<div id='div_profil_$cpt' onclick=\"affiche_set_profil($cpt);changement();return false;\">$profil</div>\n";
					echo "</td>\n";



					//===================================
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
					}
					else {
						echo "-\n";
					}
					echo "</td>\n";
					//===================================

					//===================================
					echo "<td>\n";
					echo colorise_abs($nb_absences,$non_justifie,$nb_retards);
					echo "</td>\n";
					//===================================

					//===================================
					for($i=0;$i<count($classe_fut);$i++) {
						echo "<td>\n";

						$coche_possible='y';
						if(($classe_fut[$i]!='Red')&&($classe_fut[$i]!='Dep')&&($classe_fut[$i]!='')) {
							for($loop=0;$loop<count($tab_ele_opt);$loop++) {
								if(in_array($tab_ele_opt[$loop],$tab_opt_exclue["$classe_fut[$i]"])) {
									$coche_possible='n';
									break;
								}
							}
						}

						if($coche_possible=='y') {
							echo "<input type='radio' name='classe_fut[$cpt]' id='classe_fut_".$i."_".$cpt."' value='$classe_fut[$i]' ";
							if($fut_classe==strtoupper($classe_fut[$i])) {
								echo "checked ";
	
								$eff_selection_classe_fut[$i]++;
								if($lig->sexe=='F') {
									$eff_selection_classe_fut_F[$i]++;
								}
								else {
									$eff_selection_classe_fut_M[$i]++;
								}
							}
							//alert('bip');
							echo "onmouseover=\"test_aff_classe3('".$lig->login."','".$classe_fut[$i]."');\" onmouseout=\"cacher_div('div_test_aff_classe2');\" ";
							echo "onchange=\"calcule_effectif('classe_fut',".count($classe_fut).");colorise_ligne('classe_fut',$cpt,$i);changement();\" ";
							//echo "title=\"$lig->login/$classe_fut[$i]\" ";
							echo "/>\n";
						}
						else {
							echo "_";
						}

						echo "</td>\n";
					}
		
					for($i=0;$i<count($lv1);$i++) {
						echo "<td>\n";
						if(in_array(strtoupper($lv1[$i]),$tab_ele_opt)) {
							echo "<div style='display:none;'><input type='checkbox' name='lv1[$cpt]' id='lv1_".$i."_".$cpt."' value='$lv1[$i]' checked /></div>\n";
							echo "<span title='$lv1[$i]'>X</span>";
							$eff_selection_lv1[$i]++;
							if($lig->sexe=='F') {
								$eff_selection_lv1_F[$i]++;
							}
							else {
								$eff_selection_lv1_M[$i]++;
							}
						}
						else {
							echo "&nbsp;";
						}
						// Compter les effectifs...
						echo "</td>\n";
					}
		
		
					for($i=0;$i<count($lv2);$i++) {
						echo "<td>\n";
						if(in_array(strtoupper($lv2[$i]),$tab_ele_opt)) {
							echo "<div style='display:none;'><input type='checkbox' name='lv2[$cpt]' id='lv2_".$i."_".$cpt."' value='$lv2[$i]' checked /></div>\n";
							echo "<span title='$lv2[$i]'>X</span>";
							$eff_selection_lv2[$i]++;
							if($lig->sexe=='F') {
								$eff_selection_lv2_F[$i]++;
							}
							else {
								$eff_selection_lv2_M[$i]++;
							}
						}
						else {
							echo "&nbsp;";
						}
						echo "</td>\n";
					}
		
		
					for($i=0;$i<count($lv3);$i++) {
						echo "<td>\n";
						if(in_array(strtoupper($lv3[$i]),$tab_ele_opt)) {
							echo "<div style='display:none;'><input type='checkbox' name='lv3[$cpt]' id='lv3_".$i."_".$cpt."' value='$lv3[$i]' checked /></div>\n";
							echo "<span title='$lv3[$i]'>X</span>";
							$eff_selection_lv3[$i]++;
							if($lig->sexe=='F') {
								$eff_selection_lv3_F[$i]++;
							}
							else {
								$eff_selection_lv3_M[$i]++;
							}
						}
						else {
							echo "&nbsp;";
						}
						echo "</td>\n";
					}
		
					for($i=0;$i<count($autre_opt);$i++) {
						echo "<td>\n";
						if(in_array(strtoupper($autre_opt[$i]),$tab_ele_opt)) {
							echo "<div style='display:none;'><input type='checkbox' name='autre_opt[$cpt]' id='autre_opt_".$i."_".$cpt."' value='$autre_opt[$i]' checked /></div>\n";
							echo "<span title='$autre_opt[$i]'>X</span>";
							$eff_selection_autre_opt[$i]++;
							if($lig->sexe=='F') {
								$eff_selection_autre_opt_F[$i]++;
							}
							else {
								$eff_selection_autre_opt_M[$i]++;
							}
						}
						else {
							echo "&nbsp;";
						}
						echo "</td>\n";
					}
					echo "</tr>\n";
					$cpt++;
				}
			}
		}
	}

	//==========================================
	echo "<tr>\n";
	//echo "<th>Effectifs&nbsp;: <span id='eff_tot'>&nbsp;</span></th>\n";
	echo "<th rowspan='2'>Eff.select&nbsp;:</th>\n";
	echo "<th rowspan='2' id='eff_select'>$cpt</th>\n";
	echo "<th rowspan='2' id='eff_select_sexe'>&nbsp;</th>\n";
	echo "<th rowspan='2'>&nbsp;</th>\n";
	echo "<th rowspan='2'>&nbsp;</th>\n";
	echo "<th rowspan='2'>&nbsp;</th>\n";

	for($i=0;$i<count($classe_fut);$i++) {
		echo "<th id='eff_col_classe_fut_select_".$i."'>".$eff_selection_classe_fut[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		echo "<th id='eff_col_lv1_select_".$i."'>".$eff_selection_lv1[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		echo "<th id='eff_col_lv2_select_".$i."'>".$eff_selection_lv2[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		echo "<th id='eff_col_lv3_select_".$i."'>".$eff_selection_lv3[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		echo "<th id='eff_col_autre_opt_select_".$i."'>".$eff_selection_autre_opt[$i];
		echo "</th>\n";
	}
	echo "</tr>\n";


	echo "<tr>\n";
	for($i=0;$i<count($classe_fut);$i++) {
		echo "<th id='eff_col_sexe_classe_fut_select_".$i."'>\n";
		echo $eff_selection_classe_fut_M[$i]."/".$eff_selection_classe_fut_F[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		echo "<th id='eff_col_sexe_lv1_select_".$i."'>\n";
		echo $eff_selection_lv1_M[$i]."/".$eff_selection_lv1_F[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		echo "<th id='eff_col_sexe_lv2_select_".$i."'>\n";
		echo $eff_selection_lv2_M[$i]."/".$eff_selection_lv2_F[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		echo "<th id='eff_col_sexe_lv3_select_".$i."'>\n";
		echo $eff_selection_lv3_M[$i]."/".$eff_selection_lv3_F[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		echo "<th id='eff_col_sexe_autre_opt_select_".$i."'>\n";
		echo $eff_selection_autre_opt_M[$i]."/".$eff_selection_autre_opt_F[$i];
		echo "</th>\n";
	}
	echo "</tr>\n";

	//==========================================
	echo "<tr>\n";
	echo "<th>El�ve</th>\n";
	echo "<th>Sexe</th>\n";
	echo "<th>Classe<br />actuelle</th>\n";
	echo "<th>Profil</th>\n";
	echo "<th>Niveau</th>\n";
	echo "<th>Absences Non.Just Retards</th>\n";

	for($i=0;$i<count($classe_fut);$i++) {
		echo "<th>$classe_fut[$i]</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		echo "<th>$lv1[$i]</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		echo "<th>$lv2[$i]</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		echo "<th>$lv3[$i]</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		echo "<th>$autre_opt[$i]</th>\n";
	}
	echo "</tr>\n";
	//==========================================

	echo "</table>\n";
	
	echo "<input type='hidden' name='projet' value='$projet' />\n";
	echo "<input type='hidden' name='is_posted' value='y' />\n";
	echo "<input type='hidden' name='choix_affich' value='done' />\n";
	echo "<p align='center'><input type='submit' name='valide_aff_classe_fut' value='Valider' /></p>\n";
	echo "<hr width='200'/>\n";

	echo "</form>\n";


	$titre="<span id='entete_div_photo_eleve'>El�ve</span>";
	$texte="<div id='corps_div_photo_eleve' align='center'>\n";
	$texte.="<br />\n";
	$texte.="</div>\n";
	
	$tabdiv_infobulle[]=creer_div_infobulle('div_photo',$titre,"",$texte,"",14,0,'y','y','n','n');
	

	//===============================================
	// Param�tres concernant le d�lais avant affichage d'une infobulle via delais_afficher_div()
	// Hauteur de la bande test�e pour la position de la souris:
	$hauteur_survol_infobulle=20;
	// Largeur de la bande test�e pour la position de la souris:
	$largeur_survol_infobulle=100;
	// D�lais en ms avant affichage:
	$delais_affichage_infobulle=500;

	echo "<script type='text/javascript'>
	/*
	function test_aff_classe(classe_fut) {
		//new Ajax.Updater($('div_test_aff_classe'),'liste_classe_fut.php?projet='+$projet+'&amp;classe_fut='+classe_fut,{method: 'get'});
		//new Ajax.Updater($('div_test_aff_classe'),'liste_classe_fut.php?classe_fut='+classe_fut,{method: 'get'});
		new Ajax.Updater($('div_test_aff_classe'),'liste_classe_fut.php?classe_fut='+classe_fut+'&projet=$projet',{method: 'get'});
	}
	*/

	function test_aff_classe2(classe_fut) {
		new Ajax.Updater($('div_test_aff_classe2'),'liste_classe_fut.php?classe_fut='+classe_fut+'&projet=$projet',{method: 'get'});
		delais_afficher_div('div_test_aff_classe2','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);
	}

	function test_aff_classe3(login,classe_fut) {
		new Ajax.Updater($('div_test_aff_classe2'),'liste_classe_fut.php?ele_login='+login+'&classe_fut='+classe_fut+'&projet=$projet',{method: 'get'});
		delais_afficher_div('div_test_aff_classe2','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);
	}
</script>\n";

	echo "<div id='div_test_aff_classe2' class='infobulle_corps' style='position:absolute; border:1px solid black;'>Classes futures</div>\n";

	//===============================================

	$titre="S�lection du profil";
	$texte="<p style='text-align:center;'>";
	for($loop=0;$loop<count($tab_profil);$loop++) {
		if($loop>0) {$texte.=" - ";}
		$texte.="<a href='#' onclick=\"set_profil('".$tab_profil[$loop]."');return false;\">$tab_profil[$loop]</a>";
	}
	$texte.="</p>\n";
	$tabdiv_infobulle[]=creer_div_infobulle('div_set_profil',$titre,"",$texte,"",14,0,'y','y','n','n');

	echo "<input type='hidden' name='profil_courant' id='profil_courant' value='-1' />\n";

	echo "<script type='text/javascript'>

	var couleur_profil=new Array($chaine_couleur_profil);
	var tab_profil=new Array($chaine_profil);

	function set_profil(profil) {
		var cpt=document.getElementById('profil_courant').value;
		document.getElementById('profil_'+cpt).value=profil;

		for(m=0;m<couleur_profil.length;m++) {
			if(document.getElementById('profil_'+cpt).value==tab_profil[m]) {
				document.getElementById('div_profil_'+cpt).style.color=couleur_profil[m];
			}
		}

		document.getElementById('div_profil_'+cpt).innerHTML=profil;
		cacher_div('div_set_profil');
	}
	
	function affiche_set_profil(cpt) {
		document.getElementById('profil_courant').value=cpt;
		afficher_div('div_set_profil','y',100,100);
	}

	for(i=0;i<$cpt;i++) {
		profil=document.getElementById('profil_'+i).value;

		for(m=0;m<couleur_profil.length;m++) {
			if(document.getElementById('profil_'+cpt).value==tab_profil[m]) {
				document.getElementById('div_profil_'+cpt).style.color=couleur_profil[m];
			}
		}
	}

</script>
\n";

	//===============================================

	echo "<script type='text/javascript'>
	document.getElementById('div_test_aff_classe2').style.display='none';

	function affiche_photo(photo,nom_prenom) {
		document.getElementById('entete_div_photo_eleve').innerHTML=nom_prenom;
		document.getElementById('corps_div_photo_eleve').innerHTML='<img src=\"../photos/eleves/'+photo+'\" width=\"150\" alt=\"Photo\" /><br />';
	}
";


echo "var eff_fut_classe_hors_selection=new Array(";
for($i=0;$i<count($classe_fut);$i++) {
	if($i>0) {echo ",";}
	echo "$eff_fut_classe_hors_selection[$i]";
}
echo ");\n";

echo "var eff_fut_classe_hors_selection_F=new Array(";
for($i=0;$i<count($classe_fut);$i++) {
	if($i>0) {echo ",";}
	echo "$eff_fut_classe_hors_selection_F[$i]";
}
echo ");\n";

echo "var eff_fut_classe_hors_selection_M=new Array(";
for($i=0;$i<count($classe_fut);$i++) {
	if($i>0) {echo ",";}
	echo "$eff_fut_classe_hors_selection_M[$i]";
}
echo ");\n";


echo "
	function calcule_effectif(champ,n) {
		for(k=0;k<n;k++) {
			eff=0;
			eff_M=0;
			eff_F=0;
			for(i=0;i<$cpt;i++) {
				//alert('document.getElementById('+champ+'_'+i+')')
				if(document.getElementById(champ+'_'+k+'_'+i)) {
					if(document.getElementById(champ+'_'+k+'_'+i).checked) {
						eff++;
						if(document.getElementById('eleve_sexe_'+i).innerHTML=='M') {eff_M++;} else {eff_F++;}
					}
				}
			}

			document.getElementById('eff_col_'+champ+'_select_'+k).innerHTML=eff;
			document.getElementById('eff_col_sexe_'+champ+'_select_'+k).innerHTML=eff_M+'/'+eff_F;

			eff=eff+eff_fut_classe_hors_selection[k];
			eff_M=eff_M+eff_fut_classe_hors_selection_M[k];
			eff_F=eff_F+eff_fut_classe_hors_selection_F[k];

			document.getElementById('eff_col_'+champ+'_'+k).innerHTML=eff;
			document.getElementById('eff_col_sexe_'+champ+'_'+k).innerHTML=eff_M+'/'+eff_F;

			//alert('eff='+eff);
		}
	}
	
	calcule_effectif('classe_fut',".count($classe_fut).");

	var couleur_classe_fut=new Array($chaine_couleur_classe_fut);
	var couleur_lv1=new Array($chaine_couleur_lv1);
	var couleur_lv2=new Array($chaine_couleur_lv2);
	var couleur_lv3=new Array($chaine_couleur_lv3);
	
	function colorise(mode,n) {
		var k;
		var i;

		for(k=0;k<n;k++) {
			for(i=0;i<$cpt;i++) {
				if(mode!='profil') {
					if(document.getElementById(mode+'_'+k+'_'+i)) {
						if(document.getElementById(mode+'_'+k+'_'+i).checked) {
							if(mode=='classe_fut') {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_classe_fut[k];
							}
							if(mode=='lv1') {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_lv1[k];
							}
							if(mode=='lv2') {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_lv2[k];
							}
							if(mode=='lv3') {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_lv3[k];
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
	}
	
	colorise('classe_fut',".count($classe_fut).");
	
	function colorise_ligne(cat,cpt,i) {
		// On ne traite qu'une ligne contrairement � colorise()
		//alert('couleur_classe_fut[0]='+couleur_classe_fut[0]);
		//alert(document.forms[0].elements['colorisation'].options[document.forms[0].elements['colorisation'].selectedIndex].value);
		if(document.forms[0].elements['colorisation'].options[document.forms[0].elements['colorisation'].selectedIndex].value==cat) {
			if(cat=='classe_fut') {
				//alert(cat);
				//alert(i);
				//alert(couleur_classe_fut[i]);
				document.getElementById('tr_eleve_'+cpt).style.backgroundColor=couleur_classe_fut[i];
			}
			if(cat=='lv1') {
				document.getElementById('tr_eleve_'+cpt).style.backgroundColor=couleur_lv1[i];
			}
			if(cat=='lv2') {
				document.getElementById('tr_eleve_'+cpt).style.backgroundColor=couleur_lv2[i];
			}
			if(cat=='lv3') {
				document.getElementById('tr_eleve_'+cpt).style.backgroundColor=couleur_lv3[i];
			}
			if(cat=='profil') {
				document.getElementById('tr_eleve_'+cpt).style.backgroundColor=couleur_profil[i];
			}
		}
	}
	
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

	function modif_colonne(col,mode) {
		for(i=0;i<=$cpt;i++) {
			if(document.getElementById(col+'_'+i)) {
				document.getElementById(col+'_'+i).checked=mode;
			}
		}
	
		cat=document.forms[0].elements['colorisation'].options[document.forms[0].elements['colorisation'].selectedIndex].value;
		if(col.substr(0,cat.length)==cat) {lance_colorisation();}

		// Lancer un recalcul des effectifs
		calcule_effectif('classe_fut',".count($classe_fut).");
	}

</script>\n";

	echo "<p><i>NOTES&nbsp;:</i></p>\n";
	echo "<ul>\n";
	//echo "<li></li>\n";
	echo "<li>Les Redoublants (<i>Red</i>) et Partants (<i>Dep</i>) sont exclus de la s�lection puisque d�j� affect�s.</li>\n";
	echo "</ul>\n";
}

require("../lib/footer.inc.php");
?>
