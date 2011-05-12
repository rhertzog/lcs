<?php
/* $Id: verrouillage_saisie_app.php 6730 2011-03-30 09:43:45Z crob $ */
/*
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


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}





//======================================================================================
// Section checkAccess() � d�commenter en prenant soin d'ajouter le droit correspondant:
//INSERT INTO droits VALUES('/mod_notanet/verrouillage_saisie_app.php','V','F','F','F','F','F','F','F','Notanet: (D�)Verrouillage des saisies','');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================


if (isset($_POST['is_posted'])) {
	check_token();

	$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
	//if((strlen(my_ereg_replace("[0-9a-zA-Z_ ]","",$id_classe))!=0)||($id_classe=="")){$id_classe=NULL;}

	// Type de brevet:
	$type_brevet=isset($_POST['type_brevet']) ? $_POST['type_brevet'] : (isset($_GET['type_brevet']) ? $_GET['type_brevet'] : NULL);

	$msg="";
	$nb_enr=0;

	for($i=0;$i<count($id_classe);$i++) {
		if((strlen(preg_replace("/[0-9]/","",$id_classe[$i]))==0)&&($id_classe[$i]!="")){
			//$sql="SELECT 1=1 FROM classes c, notanet n WHERE c.id='".$id_classe[$i]."' AND n.id_classe=c.id;";
			$sql="SELECT 1=1 FROM classes c WHERE c.id='".$id_classe[$i]."';";
			$res_test=mysql_query($sql);
			if(mysql_num_rows($res_test)!=0) {
				for($j=0;$j<count($type_brevet);$j++) {
					$verrou=isset($_POST['verrouiller_'.$id_classe[$i].'_'.$type_brevet[$j]]) ? $_POST['verrouiller_'.$id_classe[$i].'_'.$type_brevet[$j]] : NULL;
					if(isset($verrou)) {
						if($verrou!='N') {
							$verrou="O";
						}

						$sql="DELETE FROM notanet_verrou WHERE id_classe='".$id_classe[$i]."' AND type_brevet='".$type_brevet[$j]."';";
						$nettoyage=mysql_query($sql);

						$sql="INSERT INTO notanet_verrou SET id_classe='".$id_classe[$i]."', type_brevet='".$type_brevet[$j]."', verrouillage='$verrou';";
						$insert=mysql_query($sql);
						if(!$insert) {
							$msg.="Erreur lors de<br />$sql<br />";
						}
						else {
							$nb_enr++;
						}
					}
				}
			}
		}
	}

	if($msg=="") {
		$msg="$nb_enr enregistrement(s) effectu�(s).";
	}
}




//**************** EN-TETE *****************
$titre_page = "Notanet: Verrouillage des saisies";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

// Biblioth�que pour Notanet et Fiches brevet
include("lib_brevets.php");

echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='../accueil.php'>Accueil</a>";
echo " | <a href='index.php'>Accueil Notanet</a>";
echo "</p>\n";
echo "</div>\n";

echo "<p>Cette page permet de (d�)verrouiller la saisie des appr�ciations pour les fiches brevet.</p>\n";

$sql="CREATE TABLE notanet_verrou (
id_classe TINYINT NOT NULL ,
type_brevet TINYINT NOT NULL ,
verrouillage CHAR( 1 ) NOT NULL
);";
$create_table=mysql_query($sql);




$sql="SELECT DISTINCT type_brevet FROM notanet_ele_type ORDER BY type_brevet;";
$res1=mysql_query($sql);
$nb_type_brevet1=mysql_num_rows($res1);
if($nb_type_brevet1==0) {

	echo "<p>Aucune association �l�ve/type de brevet n'a encore �t� r�alis�e.<br />Commencez par <a href='select_eleves.php'>s�lectionner les �l�ves</a></p>\n";

	require("../lib/footer.inc.php");
	die();
}

$sql="SELECT DISTINCT type_brevet FROM notanet_corresp ORDER BY type_brevet;";
$res2=mysql_query($sql);
$nb_type_brevet2=mysql_num_rows($res2);
//if(mysql_num_rows($res)==0) {
if($nb_type_brevet2==0) {

	echo "<p>Aucune association mati�res/type de brevet n'a encore �t� r�alis�e.<br />Commencez par <a href='select_matieres.php'>s�lectionner les mati�res</a></p>\n";

	require("../lib/footer.inc.php");
	die();
}




$sql="SELECT DISTINCT net.type_brevet FROM notanet_ele_type net, notanet_corresp nc WHERE nc.type_brevet=net.type_brevet ORDER BY net.type_brevet;";
$res3=mysql_query($sql);
$nb_type_brevet=mysql_num_rows($res3);
if($nb_type_brevet==0) {
	echo "<p>Aucun type_brevet n'est encore param�tr� avec association mati�res/type de brevet et associations �l�ves/type de brevet.<br />Commencez par <a href='index.php'>r�aliser ces op�rations</a></p>\n";

	require("../lib/footer.inc.php");
	die();
}

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
echo add_token_field();

echo "<p class='bold'>Tableau des verrouillages:</p>\n";

echo "<table class='boireaus'>\n";
echo "<tr>\n";
echo "<th rowspan='2'>Classe</th>\n";
echo "<th colspan='$nb_type_brevet'>Type de brevet</th>\n";
echo "</tr>\n";

unset($type_brevet);
$cpt=0;
echo "<tr>\n";
while($lig3=mysql_fetch_object($res3)) {
	echo "<th>".$tab_type_brevet[$lig3->type_brevet];
	$type_brevet[$cpt]=$lig3->type_brevet;
	echo "<input type='hidden' name='type_brevet[]' value='".$type_brevet[$cpt]."' />\n";
	echo "</th>\n";
	$cpt++;
}
echo "</tr>\n";

$sql="SELECT DISTINCT c.id,c.classe FROM classes c, notanet n WHERE c.id=n.id_classe ORDER BY id_classe;";
$res4=mysql_query($sql);
if(mysql_num_rows($res4)==0) {
	echo "</table>\n";

	echo "<p>Aucune classe n'a �t� trouv�e???</p>\n";

	require("../lib/footer.inc.php");
	die();
}

$alt=1;
while($lig4=mysql_fetch_object($res4)) {
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td>".$lig4->classe;
	echo "<input type='hidden' name='id_classe[]' value='".$lig4->id."' />\n";
	echo "</td>\n";

	for($i=0;$i<count($type_brevet);$i++) {
		$sql="SELECT 1=1 FROM notanet n, notanet_ele_type net WHERE n.login=net.login AND net.type_brevet='".$type_brevet[$i]."';";
		$res_test=mysql_query($sql);
		echo "<td>\n";
		if(mysql_num_rows($res_test)==0) {
			echo "&nbsp;";
		}
		else {
			$sql="SELECT * FROM notanet_verrou WHERE id_classe='$lig4->id' AND type_brevet='".$type_brevet[$i]."';";
			$res_test=mysql_query($sql);
			if(mysql_num_rows($res_test)==0) {
				$verrou="O";
			}
			else {
				$lig_verrou=mysql_fetch_object($res_test);
				$verrou=$lig_verrou->verrouillage;
			}
			//echo "O <input type='radio' name='verrouiller_".$lig4->id."_".$i."' value='O' ";
			echo "O <input type='radio' name='verrouiller_".$lig4->id."_".$type_brevet[$i]."' value='O' ";
			if($verrou=="O") {
				echo "checked ";
			}
			echo "/>\n";
			//echo "<input type='radio' name='verrouiller_".$lig4->id."_".$i."' value='N' ";
			echo "<input type='radio' name='verrouiller_".$lig4->id."_".$type_brevet[$i]."' value='N' ";
			if($verrou=="N") {
				echo "checked ";
			}
			echo "/> N\n";
		}
		echo "</td>\n";
	}
}
echo "</tr>\n";
echo "</table>\n";

echo "<input type='hidden' name='is_posted' value='yes' />\n";
echo "<p><input type='submit' value='Enregistrer' /></p>\n";

echo "</form>\n";

echo "<p><i>NOTE:</i> 'O' correspond � un verrouillage/interdiction des saisies, tandis que le 'N' permet la saisie.</p>\n";

require("../lib/footer.inc.php");
?>
