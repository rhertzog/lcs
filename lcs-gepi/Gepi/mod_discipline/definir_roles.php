<?php

/*
 * $Id: definir_roles.php 5401 2010-09-23 10:01:32Z crob $
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

//$variables_non_protegees = 'yes';

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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/saisie_qualites.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Saisie des qualit�s', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/saisie_qualites.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Saisie des qualit�s', '');;";
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

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// REMARQUE: Le terme de 'qualit�' a �t� remplac� par 'r�le'
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require('sanctions_func_lib.php');

$msg="";

$suppr_qualite=isset($_POST['suppr_qualite']) ? $_POST['suppr_qualite'] : NULL;
$qualite=isset($_POST['qualite']) ? $_POST['qualite'] : NULL;
$cpt=isset($_POST['cpt']) ? $_POST['cpt'] : 0;

if(isset($suppr_qualite)) {
	for($i=0;$i<$cpt;$i++) {
		if(isset($suppr_qualite[$i])) {
			$sql="DELETE FROM s_qualites WHERE id='$suppr_qualite[$i]';";
			$suppr=mysql_query($sql);
			if(!$suppr) {
				//$msg.="ERREUR lors de la suppression de la qualit� n�".$suppr_qualite[$i].".<br />\n";
				$msg.="ERREUR lors de la suppression du r�le n�".$suppr_qualite[$i].".<br />\n";
			}
			else {
				$msg.="Suppression du r�le n�".$suppr_qualite[$i].".<br />\n";
			}
		}
	}
}

if((isset($qualite))&&($qualite!='')) {
	$a_enregistrer='y';

	$sql="SELECT qualite FROM s_qualites ORDER BY qualite;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$tab_qualite=array();
		while($lig=mysql_fetch_object($res)) {
			$tab_qualite[]=$lig->qualite;
		}

		if(in_array($qualite,$tab_qualite)) {$a_enregistrer='n';}
	}

	if($a_enregistrer=='y') {
		//$qualite=addslashes(my_ereg_replace('(\\\r\\\n)+',"\r\n",my_ereg_replace("&#039;","'",html_entity_decode($qualite))));
		$qualite=my_ereg_replace('(\\\r\\\n)+',"\r\n",$qualite);

		$sql="INSERT INTO s_qualites SET qualite='".$qualite."';";
		$res=mysql_query($sql);
		if(!$res) {
			$msg.="ERREUR lors de l'enregistrement de ".$qualite."<br />\n";
		}
		else {
			$msg.="Enregistrement de ".$qualite."<br />\n";
		}
	}
}

$themessage  = 'Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
//$titre_page = "Sanctions: D�finition des qualit�s";
$titre_page = "Discipline: D�finition des r�les";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

//echo "<p class='bold'>Saisie des qualit�s dans un incident&nbsp;:</p>\n";
echo "<p class='bold'>Saisie des r�les dans un incident&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt=0;
$sql="SELECT * FROM s_qualites ORDER BY qualite;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	//echo "<p>Aucune qualit� n'est encore d�finie.</p>\n";
	echo "<p>Aucun r�le n'est encore d�fini.</p>\n";
}
else {
	//echo "<p>Qualit�s existantes&nbsp;:</p>\n";
	//echo "<table class='boireaus' border='1' summary='Tableau des qualit�s existantes'>\n";
	echo "<p>R�les existants&nbsp;:</p>\n";
	echo "<table class='boireaus' border='1' summary='Tableau des r�les existants'>\n";
	echo "<tr>\n";
	//echo "<th>Qualit�</th>\n";
	echo "<th>R�le</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";

		echo "<td>\n";
		echo "<label for='suppr_qualite_$cpt' style='cursor:pointer;'>";
		echo $lig->qualite;
		echo "</label>";
		echo "</td>\n";

		echo "<td><input type='checkbox' name='suppr_qualite[]' id='suppr_qualite_$cpt' value=\"$lig->id\" onchange='changement();' /></td>\n";
		echo "</tr>\n";

		$cpt++;
	}

	echo "</table>\n";
}
echo "</blockquote>\n";

echo "<p>Nouvelle qualit�&nbsp;: <input type='text' name='qualite' value='' onchange='changement();' /></p>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>