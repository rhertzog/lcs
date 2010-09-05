<?php

/**
 * Fichiers qui permet de param�trer les couleurs de chaque mati�re des emplois du temps
 *
 * @version $Id: edt_param_couleurs.php 4053 2010-01-29 20:41:31Z adminpaulbert $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
require_once("./choix_langue.php");

$titre_page = TITLE_EDT_PARAM_COLORS;
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");
//require_once("./fonctions_edt_2.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

/*/ S�curit�
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}*/
// CSS et js particulier � l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";
$utilisation_jsdivdrag = "";
//==============PROTOTYPE===============
$utilisation_prototype = "ok";
//============fin PROTOTYPE=============
// On ins�re l'ent�te de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php");
?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">
    <?php require_once("./menu.inc.new.php"); ?>
<h3 class="gepi"><?php echo CLICK_ON_COLOR ?></h3>
<p><?php echo TEXT1_EDT_PARAM_COLORS ?></p>

<table id="edt_table_couleurs">
	<thead>
	<tr><th><?php echo FIELD ?></th><th><?php echo SHORT_NAME ?></th><th><?php echo COLOR ?></th></tr>
	</thead>

	<tbody>

<?php
// On affiche la liste des mati�res
$req_sql = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY nom_complet");
$nbre_matieres = mysql_num_rows($req_sql);

	for($i=0; $i < $nbre_matieres; $i++){
	$aff_matiere[$i]["court"] = mysql_result($req_sql, $i, "matiere");
	$aff_matiere[$i]["long"] = mysql_result($req_sql, $i, "nom_complet");
	// On d�termine la couleur choisie
	$recher_couleur = "M_".$aff_matiere[$i]["court"];
	$color = GetSettingEdt($recher_couleur);
		if ($color == "") {
			$color = "none";
		}
		// On construit le tableau
		echo '
		<tr id="M_'.$aff_matiere[$i]["court"].'">
			<td>'.$aff_matiere[$i]["long"].'</td>
			<td>'.$aff_matiere[$i]["court"].'</td>
			<td class="cadreCouleur'.$color.'">
				<p onclick="couleursEdtAjax(\'M_'.$aff_matiere[$i]["court"].'\', \'non\');">'.MODIFY_COLOR.'</p>
			</td>
		</tr>
		';

	}
?>

	</tbody>

</table>
<br /><br />
	</div>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>