<?php

/**
 * Fichier d'initialisation de l'EdT
 * Pour effacer la table avant une nouvelle initialisation il faut faire un TRUNCATE TABLE nom_table;
 * @version $Id: edt_initialiser.php 2147 2008-07-23 09:01:04Z tbelliard $
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
$titre_page = "Emploi du temps - Initialisation";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");
require_once("./fonctions_cours.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// S�curit�
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// S�curit� suppl�mentaire par rapport aux param�tres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die('Vous devez demander � votre administrateur l\'autorisation de voir cette page.');
}
// CSS et js particulier � l'EdT
$utilisation_win = 'oui';
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "edt_organisation/style_edt";

// On ins�re l'ent�te de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php"); ?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">
<?php
	// Initialisation des variables de la page
$initialiser = isset($_POST["initialiser"]) ? $_POST["initialiser"] : NULL;
$choix_prof = isset($_POST["prof"]) ? $_POST["prof"] : NULL;
$enseignement = isset($_POST["enseignement"]) ? $_POST["enseignement"] : NULL;
$ch_heure = isset($_POST["ch_heure"]) ? $_POST["ch_heure"] : NULL;
$ch_jour_semaine = isset($_POST["ch_jour_semaine"]) ? $_POST["ch_jour_semaine"] : NULL;
$duree = isset($_POST["duree"]) ? $_POST["duree"] : NULL;
$heure_debut = isset($_POST["heure_debut"]) ? $_POST["heure_debut"] : NULL;
$choix_semaine = isset($_POST["choix_semaine"]) ? $_POST["choix_semaine"] : NULL;
$login_salle = isset($_POST["login_salle"]) ? $_POST["login_salle"] : NULL;
$init = isset($_GET["init"]) ? $_GET["init"] : NULL;

	// On affiche ou non les infos de base
$aff_reglages = GetSettingEdt("edt_aff_init_infos");

if ($aff_reglages == "oui") {
	echo "
	<p style=\"font-weight: bold;\">Avant de commencer la cr�ation des cours dans l'emploi du temps, il faut pr�parer le logiciel.</p>

	<p>Une partie de l'initialisation est commune avec le module absences :
	<br />&nbsp;-&nbsp;<a href=\"../mod_absences/admin/admin_periodes_absences.php?action=visualiser\">les diff&eacute;rents cr&eacute;neaux</a> de la journ&eacute;e.
	<br />&nbsp;-&nbsp;<a href=\"../mod_absences/admin/admin_config_semaines.php?action=visualiser\">le type de semaine</a> (paire/impaire, A/B/C, 1/2,...).
	<br />&nbsp;-&nbsp;<a href=\"../mod_absences/admin/admin_horaire_ouverture.php?action=visualiser\">les horaires de l'&eacute;tablissement</a>.</p>

<hr />

	<p>Il faut renseigner le calendrier en cliquant sur le menu &agrave; gauche. Toutes les p&eacute;riodes
	qui apparaissent dans l'emploi du temps doivent &ecirc;tre d&eacute;finies : trimestres, vacances, ... Si tous vos
	cours durent le temps de l'ann&eacute;e scolaire, vous pouvez vous passer de cette &eacute;tape.</p>

<hr />
	<p>
	Pour entrer des informations dans l'emploi du temps de Gepi, il y a plusieurs possibilit&eacute;s.
	<br /><br />
	<p onclick=\"ouvrirWin2('id_manuel'); return false;\" style=\"float: left; border: 1px solid grey; background: #FFFFFF; width: 200px; text-align: center; cursor: pointer;\">
	M�thode manuelle</p>

	<p onclick=\"changerDisplayDiv('edt_init_import'); return false;\" style=\"position: relative; margin-left: 350px; border: 1px solid grey; background: #FFFFFF; width: 200px; text-align: center; cursor: pointer;\">
	Importation</p>
	";
}
// ============================= c'est l� que j'ai sorti la partie manuelle ==================

	// une fois initialis�, la partie suivante peut �tre verrouill�e
			// Pour d�verrouiller, le traitement se fait ici l�

echo '<div id="edt_init_import" style="display: none;">';

if (isset($init) AND $init == "ok") {
	$req_reprendre_init = mysql_query("UPDATE edt_setting SET valeur = 'oui' WHERE reglage = 'edt_aff_init_infos2'");
}
else if (isset($init) AND $init == "ko") {
	$req_reprendre_init = mysql_query("UPDATE edt_setting SET valeur = 'non' WHERE reglage = 'edt_aff_init_infos2'");
}

$aff_reglages2 = GetSettingEdt("edt_aff_init_infos2");

if ($aff_reglages2 == "oui") {
	echo '
	<span class="refus">Le module EdT n\'est pas initialis&eacute;.
	<a href="./edt_initialiser.php?init=ko">Cliquer ici quand vous avez termin&eacute; l\'initialisation</a></span>

	<h5>Pour l\'initialisation du d&eacute;but d\'ann&eacute;e, les diff&eacute;rents logiciels de conception des emplois
 	du temps ne permettent pas d\'avoir une seule proc&eacute;dure. Il convient donc de bien d&eacute;terminer ce
	 qui est possible. Avant de vous lancer dans cette initialisation, vous devez vous assurer d\'avoir param&eacute;tr&eacute;
 	l\'ensemble des informations relatives aux horaires de l\'&eacute;tablissement.</h5>

 	<h4 class="red">Pour les proc�dures suivantes, vous pouvez demander � Gepi d\'effacer les cours existants ou d�cider de les conserver.</h4>

 	<div id="lien" style="background: #fefefe; margin-left: 200px; width: 400px;">
 		<br />
		<p class="edt_lien"><a href="./edt_init_csv.php">Fichiers csv construit manuellement.</a></p>
		<p class="edt_lien"><a href="./edt_init_csv2.php">Export depuis UnDeuxTemps.</a></p>
		<p class="edt_lien"><a href="./edt_init_texte.php">Export type Charlemagne, IndexEducation.</a></p>
		<br />
	</div>
 		';

 //<div id=\"lien\"><a href=\"./index_edt.php?initialiser=ok&xml=ok\">Cliquer ici pour une initialisation par fichiers xml (type export STSWeb)</a></div>
}
else if ($aff_reglages2 == "non") {
	echo '
	<span class="accept">Le module EdT est initialis&eacute;.
	<a href="./edt_initialiser.php?init=ok">Cliquer ici pour reprendre cette initialisation</a></span>
		';
}
else {
	echo '
	Il y a un probl&egrave;me de r&eacute;glage dans votre base de donn&eacute;es, il faut peut-&ecirc;tre la mettre &agrave; jour.
		';

}
echo '</div>';
?>

	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>