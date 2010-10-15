<?php
/* $Id: select_arriv_red.php 3323 2009-08-05 10:06:18Z crob $ */
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/select_arriv_red.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/select_arriv_red.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='G�n�se des classes: S�lection des arrivants/redoublants',
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
$choix=isset($_POST['choix']) ? $_POST['choix'] : (isset($_GET['choix']) ? $_GET['choix'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : NULL;

if((isset($projet))&&(isset($choix))&&(($choix=='Red')||($choix=='Arriv'))&&(isset($ele_login))) {
	$nb_err=0;
	$nb_reg=0;
	for($i=0;$i<count($ele_login);$i++) {
		if(($ele_login[$i]!='')&&(my_ereg_replace("[A-Za-z0-9_.-]","",$ele_login[$i])=='')) {
			$sql="DELETE FROM gc_ele_arriv_red WHERE projet='$projet' AND login='$ele_login[$i]';";
			//echo "$sql<br />";
			$del=mysql_query($sql);

			$sql="INSERT INTO gc_ele_arriv_red SET projet='$projet', login='$ele_login[$i]', statut='$choix';";
			//echo "$sql<br />";
			if($insert=mysql_query($sql)) {
				$nb_reg++;
			}
			else {
				$nb_err++;
			}
		}
	}

	if($nb_err==0) {
		$msg="$nb_reg $choix enregistr�s.";
	}
	else {
		$msg="ERREUR: $nb_err lors de l'enregistrement des $choix.";
	}
}

//**************** EN-TETE *****************
$titre_page = "G�n�se classe: S�lection redoublants et arrivants";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

if((!isset($projet))||($projet=="")) {
	echo "<p style='color:red'>ERREUR: Le projet n'est pas choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='index.php?projet=$projet'>Retour</a>";
//echo "</p>\n";
//echo "</div>\n";

if(isset($_POST['suppr'])) {
	$suppr=$_POST['suppr'];

	for($i=0;$i<count($suppr);$i++) {
		$sql="DELETE FROM gc_ele_arriv_red WHERE projet='$projet' AND login='$suppr[$i]';";
		$del=mysql_query($sql);
	}
}

if(!isset($choix)) {
	echo "</p>\n";

	echo "<h2>Projet $projet</h2>\n";

	echo "<p>Les �l�ves, redoublants ou arrivants, doivent �tre inscrits dans la table 'eleves' pour pouvoir �tre pris en compte dans un projet.</p>\n";
	echo "<ul>\n";
	echo "<li>\n";
	echo "<p><a href='../eleves/add_eleve.php?projet=$projet&amp;mode=multiple' target='_blank'>Saisir des arrivants</a>.</p>\n";
	echo "</li>\n";

	echo "<li>\n";
	echo "<p><a href='".$_SERVER['PHP_SELF']."?projet=$projet&amp;choix=Arriv'>S�lectionner des arrivants</a> parmi ceux que vous venez de saisir.</p>\n";
	echo "</li>\n";

	echo "<li>\n";
	echo "<p><a href='".$_SERVER['PHP_SELF']."?projet=$projet&amp;choix=Red'>S�lectionner des redoublants</a> parmi les �l�ves inscrits dans des classes cette ann�e.</p>\n";
	echo "</li>\n";

	echo "</ul>\n";

	//echo "<p style='color:red'>Mettre la liste des red/arriv d�j� saisi et pouvoir en supprimer.</p>\n";

	$sql="SELECT * FROM gc_ele_arriv_red WHERE projet='$projet' ORDER BY statut, login;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		echo "<p>Liste des red/arriv d�j� saisis&nbsp;:</p>\n";
		echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
		echo "<table class='boireaus' border='1' summary='Redoublants et arrivants'>\n";
		echo "<tr>\n";
		echo "<th>El�ve</th>\n";
		echo "<th>Statut</th>\n";
		echo "<th>\n";
		//echo "Supprimer\n";
		echo "<input type='submit' name='supprimer' value='Supprimer' />\n";
		echo "</th>\n";
		echo "</tr>\n";
		$alt=1;
		while($lig=mysql_fetch_object($res)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			$sql="SELECT nom,prenom FROM eleves WHERE login='$lig->login';";
			$res2=mysql_query($sql);
			$lig2=mysql_fetch_object($res2);
			echo strtoupper($lig2->nom)." ".ucfirst(strtolower($lig2->prenom));
			echo "</td>\n";
			echo "<td>\n";
			echo $lig->statut;
			echo "</td>\n";
			echo "<td>\n";
			echo "<input type='checkbox' name='suppr[]' value='$lig->login' />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "<input type='hidden' name='projet' value='$projet' />\n";
		echo "</form>\n";
	}
}
elseif($choix=='Red') {

	echo " | <a href='".$_SERVER['PHP_SELF']."?projet=$projet'>Redoublants et arrivants</a>";
	echo "</p>\n";

	echo "<h2>Projet $projet</h2>\n";

	echo "<h3>S�lection des redoublants</h3>\n";

	if(!isset($id_classe)) {
		echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

		$sql="SELECT id,classe FROM classes ORDER BY classe;";
		$res_classes=mysql_query($sql);
		$nb_classes=mysql_num_rows($res_classes);
		// Ajouter des classes
		echo "<p>Dans quelles classes sont les redoublants � inscrire dans le projet '$projet'&nbsp;:\n";
		echo "</p>\n";
		
		// Affichage sur 4/5 colonnes
		$nb_classes_par_colonne=round($nb_classes/4);
		
		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";
		
		$cpt_i = 0;
		
		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";
		
		while($lig_clas=mysql_fetch_object($res_classes)) {
		
			//affichage 2 colonnes
			if(($cpt_i>0)&&(round($cpt_i/$nb_classes_par_colonne)==$cpt_i/$nb_classes_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}
		
			echo "<input type='checkbox' name='id_classe[]' id='id_classe_$cpt_i' value='$lig_clas->id' ";
			echo "/><label for='id_classe_$cpt_i'>$lig_clas->classe</label>";
			//echo "<input type='hidden' name='classe[$lig_clas->id]' value='$lig_clas->classe' />";
			echo "<br />\n";
			$cpt_i++;
		}
		
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		
		echo "<input type='hidden' name='projet' value='$projet' />\n";
		echo "<input type='hidden' name='choix' value='$choix' />\n";
		echo "<p><input type='submit' name='choix_classes' value='Valider' /></p>\n";
		echo "</form>\n";
	}
	else {
		echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

		$cpt=0;
		for($i=0;$i<count($id_classe);$i++) {
			echo "<p class='bold'>Classe de ".get_class_from_id($id_classe[$i])."</p>\n";
	
			echo "<table class='boireaus' summary='Choix des �l�ves'>\n";
			echo "<tr>\n";
			echo "<th>El�ves</th>\n";
			echo "<th>\n";
			echo "<a href=\"javascript:CocheClasse($i);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheClasse($i);changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout d�cocher' /></a>\n";
			echo "</th>\n";
			echo "</tr>\n";
	
			$sql="SELECT DISTINCT e.* FROM eleves e,
							j_eleves_classes jec
				WHERE jec.login=e.login AND
							jec.id_classe='".$id_classe[$i]."'
				ORDER BY e.nom,e.prenom;";
			$res_ele=mysql_query($sql);
			$alt=1;
			while($lig_ele=mysql_fetch_object($res_ele)) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				echo "<td style='text-align:left;'>".$lig_ele->nom." ".$lig_ele->prenom."</td>\n";

				echo "<td><input type='checkbox' name='ele_login[]' id='tab_selection_ele_".$i."_".$cpt."' value=\"".$lig_ele->login."\" ";

				$sql="SELECT 1=1 FROM gc_ele_arriv_red WHERE projet='$projet' AND login='$lig_ele->login' AND statut='$choix';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0) { echo "checked ";}
		
				echo "/></td>\n";

				echo "</tr>\n";
				$cpt++;
			}
			echo "</table>\n";
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
		}

		echo "<input type='hidden' name='projet' value='$projet' />\n";
		echo "<input type='hidden' name='choix' value='$choix' />\n";
		echo "<p><input type='submit' name='choix_classes' value='Valider' /></p>\n";
		echo "</form>\n";

		echo "<script type='text/javascript'>

function CocheClasse(i) {
	for (var k=0;k<$cpt;k++) {
		if(document.getElementById('tab_selection_ele_'+i+'_'+k)){
			document.getElementById('tab_selection_ele_'+i+'_'+k).checked = true;
		}
	}
}

function DecocheClasse(i) {
	for (var k=0;k<$cpt;k++) {
		if(document.getElementById('tab_selection_ele_'+i+'_'+k)){
			document.getElementById('tab_selection_ele_'+i+'_'+k).checked = false;
		}
	}
}

</script>\n";

	}
}
elseif($choix=='Arriv') {
	echo " | <a href='".$_SERVER['PHP_SELF']."?projet=$projet'>Redoublants et arrivants</a>";
	echo "</p>\n";

	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

	$cpt=0;

	echo "<h2>Projet $projet</h2>\n";

	echo "<h3>S�lection des nouveaux arrivants</h3>\n";

	echo "<p class='bold'>El�ves non affect�s dans des classes:</p>\n";

	echo "<table class='boireaus' summary='Choix des �l�ves'>\n";
	echo "<tr>\n";
	echo "<th>El�ves</th>\n";
	echo "<th>\n";
	echo "<a href=\"javascript:CocheEleves();changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheEleves();changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout d�cocher' /></a>\n";
	echo "</th>\n";
	echo "</tr>\n";

	$sql="SELECT e.* FROM eleves e
		LEFT JOIN j_eleves_classes jec ON jec.login=e.login
		where jec.login is NULL;";
	$res_ele=mysql_query($sql);
	$alt=1;
	while($lig_ele=mysql_fetch_object($res_ele)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='text-align:left;'>".$lig_ele->nom." ".$lig_ele->prenom."</td>\n";

		echo "<td><input type='checkbox' name='ele_login[]' id='tab_selection_ele_".$cpt."' value=\"".$lig_ele->login."\" ";

		$sql="SELECT 1=1 FROM gc_ele_arriv_red WHERE projet='$projet' AND login='$lig_ele->login' AND statut='$choix';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) { echo "checked ";}

		echo "/></td>\n";
		echo "</tr>\n";
		$cpt++;
	}
	echo "</table>\n";

	echo "<input type='hidden' name='projet' value='$projet' />\n";
	echo "<input type='hidden' name='choix' value='$choix' />\n";
	echo "<p><input type='submit' name='choix_classes' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>

function CocheEleves() {
	for (var k=0;k<$cpt;k++) {
		if(document.getElementById('tab_selection_ele_'+k)){
			document.getElementById('tab_selection_ele_'+k).checked = true;
		}
	}
}

function DecocheEleves() {
	for (var k=0;k<$cpt;k++) {
		if(document.getElementById('tab_selection_ele_'+k)){
			document.getElementById('tab_selection_ele_'+k).checked = false;
		}
	}
}

</script>\n";






}

require("../lib/footer.inc.php");
die();

/*
$tab_id_div=array();
$tab_classe_fut=array();
$classes_futures="";
$sql="SELECT * FROM gc_divisions WHERE projet='$projet';";
$res_div=mysql_query($sql);
if(mysql_num_rows($res_div)>0) {
	while($lig_div=mysql_fetch_object($res_div)) {
		if($lig_div->statut=='actuelle') {
			$tab_id_div[]=$lig_div->id_classe;
		}
		else {
			$tab_classe_fut[]=$lig_div->classe;
			if($classes_futures!="") {$classes_futures.=",";}
			$classes_futures.=$lig_div->classe;
		}
	}
}

echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

$sql="SELECT id,classe FROM classes ORDER BY classe;";
$res_classes=mysql_query($sql);
$nb_classes=mysql_num_rows($res_classes);
// Ajouter des classes
echo "<p>Liste des classes actuelles&nbsp;:\n";
echo "</p>\n";

// Affichage sur 4/5 colonnes
$nb_classes_par_colonne=round($nb_classes/4);

echo "<table width='100%' summary='Choix des classes'>\n";
echo "<tr valign='top' align='center'>\n";

$cpt_i = 0;

echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
echo "<td align='left'>\n";

while($lig_clas=mysql_fetch_object($res_classes)) {

	//affichage 2 colonnes
	if(($cpt_i>0)&&(round($cpt_i/$nb_classes_par_colonne)==$cpt_i/$nb_classes_par_colonne)){
		echo "</td>\n";
		echo "<td align='left'>\n";
	}

	echo "<input type='checkbox' name='id_classe[]' id='id_classe_$cpt_i' value='$lig_clas->id' ";
	if(in_array($lig_clas->id,$tab_id_div)) {echo "checked ";}
	echo "/><label for='id_classe_$cpt_i'>$lig_clas->classe</label>";
	echo "<input type='hidden' name='classe[$lig_clas->id]' value='$lig_clas->classe' />";
	echo "<br />\n";
	$cpt_i++;
}

echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";


echo "<p>Ajouter une ou des classes futures&nbsp;:\n";
echo " <input type='text' name='classes_futures' value='$classes_futures' /><br />\n";
echo "(<i>pour saisir plusieurs classes, mettre une virgule entre les classes</i>)</p>\n";

echo "<input type='hidden' name='projet' value='$projet' />\n";
echo "<p><input type='submit' name='choix_classes' value='Valider' /></p>\n";
echo "</form>\n";


echo "</blockquote>\n";
*/

require("../lib/footer.inc.php");
?>
