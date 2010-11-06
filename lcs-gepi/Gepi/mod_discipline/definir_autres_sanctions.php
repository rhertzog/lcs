<?php

/*
 * $Id: definir_autres_sanctions.php 5400 2010-09-23 10:01:22Z crob $
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

// On indique qu'il faut creer des variables non prot�g�es (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/definir_autres_sanctions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: D�finir types sanctions', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_autres_sanctions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: D�finir types sanctions', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(strtolower(substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d acc�der au module Discipline qui est d�sactiv� !");
	tentative_intrusion(1, "Tentative d'acc�s au module Discipline qui est d�sactiv�.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$msg="";

$suppr_nature=isset($_POST['suppr_nature']) ? $_POST['suppr_nature'] : NULL;
$nature=isset($_POST['nature']) ? $_POST['nature'] : NULL;
$cpt=isset($_POST['cpt']) ? $_POST['cpt'] : 0;

if(isset($suppr_nature)) {
	for($i=0;$i<$cpt;$i++) {
		if(isset($suppr_nature[$i])) {
			$sql="SELECT 1=1 FROM s_autres_sanctions WHERE id_nature='$suppr_nature[$i]';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				$msg.="Il n'est pas possible de supprimer le type de sanction n�".$suppr_nature[$i]." parce qu'il est associ� � des sanctions d�j� saisies pour un ou des �l�ves.<br />\n";
			}
			else {
				$sql="DELETE FROM s_types_sanctions WHERE id_nature='$suppr_nature[$i]';";
				$suppr=mysql_query($sql);
				if(!$suppr) {
					$msg.="ERREUR lors de la suppression de la nature n�".$suppr_nature[$i].".<br />\n";
				}
				else {
					$msg.="Suppression de la nature n�".$suppr_nature[$i].".<br />\n";
				}
			}
		}
	}
}

//if((isset($nature))&&($nature!='')&&(isset($type))&&(($type=='prise')||($type=='demandee'))) {
if(isset($nature)) {
	$a_enregistrer='y';

	$sql="SELECT * FROM s_types_sanctions ORDER BY nature;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$tab_nature=array();
		while($lig=mysql_fetch_object($res)) {
			$tab_nature[]=$lig->nature;
		}

		if(in_array($nature,$tab_nature)) {$a_enregistrer='n';}
	}


	if((isset($nature))&&($nature!='')) {
		if($a_enregistrer=='y') {
			//$nature=addslashes(my_ereg_replace('(\\\r\\\n)+',"\r\n",my_ereg_replace("&#039;","'",html_entity_decode($nature))));
			//$nature=addslashes(my_ereg_replace('(\\\r\\\n)+',"\r\n",$nature));
			//$nature=addslashes(my_ereg_replace('(\\\r\\\n)+',"\r\n",html_entity_decode($nature)));
			//$nature=addslashes(my_ereg_replace('(\\\r\\\n)+',"\r\n",my_ereg_replace("'","&#039;",$nature)));
			$nature=my_ereg_replace('(\\\r\\\n)+',"\r\n",$nature);

			$sql="INSERT INTO s_types_sanctions SET nature='".$nature."';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);
			if(!$res) {
				$msg.="ERREUR lors de l'enregistrement de ".$nature."<br />\n";
			}
			else {
				$msg.="Enregistrement de ".$nature."<br />\n";
			}
		}
	}
}

$themessage  = 'Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: D�finition des types de sanctions";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";

echo "<p>Les types de sanctions pr�d�finis sont: Retenue, Exclusion, Travail.<br />
La pr�sente page est destin�e � ajouter d'autres types de sanctions (<i>'mise au pilori', 'flagellation avec des orties', 'regarder Questions pour un champion',... selon les go�ts de l'�tablissement en mati�re de supplices divers;o</i>).</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

echo "<p class='bold'>Saisie de types de sanctions&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt=0;
$sql="SELECT * FROM s_types_sanctions ORDER BY nature;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "<p>Aucune sanction suppl�mentaire n'est encore d�finie.</p>\n";
}
else {
	echo "<p>Sanctions existantes&nbsp;:</p>\n";
	echo "<table class='boireaus' border='1' summary='Tableau des sanctions existantes'>\n";
	echo "<tr>\n";
	echo "<th>Nature</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";

		echo "<td>\n";
		echo "<label for='suppr_nature_$cpt' style='cursor:pointer;'>";
		echo $lig->nature;
		echo "</label>";
		echo "</td>\n";

		echo "<td><input type='checkbox' name='suppr_nature[]' id='suppr_nature_$cpt' value=\"$lig->id_nature\" onchange='changement();' /></td>\n";
		echo "</tr>\n";

		$cpt++;
	}

	echo "</table>\n";
}

echo "<p>Nouvelle nature&nbsp;: <input type='text' name='nature' value='' onchange='changement();' />\n";
echo "</p>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";

echo "</blockquote>\n";
echo "</form>\n";
echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>