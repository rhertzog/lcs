<?php
/*
 * $Id$
 *
 * Copyright 2009 Josselin Jacquard
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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

// On d�samorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) || isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
//echo("Debug Locale : ".setLocale(LC_TIME,0));

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//On v�rifie si le module est activ�
if (getSettingValue("active_cahiers_texte")!='y') {
    die("Le module n'est pas activ�.");
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

// On met le header en petit par d�faut
$_SESSION['cacher_header'] = "y";
//**************** EN-TETE *****************
$titre_page = "Cahier de textes";

$style_specifique = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique = "cahier_texte_2/init_cahier_texte_2";
$utilisation_win = 'oui';
$utilisation_jsdivdrag = "non";
$windows_effects = "non";
$message_deconnexion = "non";

//on regarde si les preferences pour le cdt sont precisees dans la requete
$cdt_version_pref = isset($_POST["cdt_version_pref"]) ? $_POST["cdt_version_pref"] :(isset($_GET["cdt_version_pref"]) ? $_GET["cdt_version_pref"] :NULL);
if ($cdt_version_pref != null) {
    $utilisateur->setPreferenceValeur("cdt_version", $cdt_version_pref);
}

//on regarde les preference de l'utilisateur
if ($utilisateur->getPreferenceValeur("cdt_version") == "1") {
    header("Location: ../cahier_texte/index.php?cdt_version_pref=1");
    die();
}


//on reste sur le cdt1, le navigateur n'etant pas compatible avec le cdt2
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6' ) !== FALSE) {
    header("Location: ../cahier_texte/index.php");
    die();
}

require_once("../lib/header.inc");
//**************** FIN EN-TETE *************
//-----------------------------------------------------------------------------------

// si l'id d'un groupe est sp�cifi�, on l'enregistre dans un champ hidden, il sera utilis� par le javascript d'initialisation pour basculer vers le groupe concern�
echo "<input type='hidden' name='id_groupe_init' id='id_groupe_init' value='";
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
if ($id_groupe != NULL) {
	echo $_GET["id_groupe"];
} else if (isset($_SESSION['id_groupe_session'])) {
	echo $_SESSION['id_groupe_session'];
}
echo "' />";

echo "<table width=\"98%\" cellspacing=0 align=\"center\" summary=\"Tableau d'ent�te\">\n";
echo "<tr>\n";
echo "<td valign='center'>\n";
echo "<button style='width: 200px;' onclick=\"javascript:
						getWinDernieresNotices().show();
						getWinDernieresNotices().toFront();
						return false;
				\">Voir les derni�res notices</button>\n";
echo "<br />";
echo "<button style='width: 200px;' onclick=\"javascript:
						getWinDernieresNotices().setLocation(155, 40);
						getWinDernieresNotices().hide();
						getWinCalendar().setLocation(0, GetWidth() - 245);
						getWinEditionNotice().setLocation(160, 334);
						getWinEditionNotice().setSize(GetWidth()-360, GetHeight() - 160);
						getWinListeNotices().setLocation(160, 0);
						getWinListeNotices().setSize(330, GetHeight() - 160)
						return false;
				\">Repositionner les fenetres</button>\n";
echo "</td>";

echo "<td width='20 px'>";
echo "<a href=\"./index.php?cdt_version_pref=1\">\n";
echo "<img src='../images/icons/cdt2_1.png' alt='Utiliser la version 1 du cahier de textes' class='link' title='Utiliser la version 1 du cahier de textes'/> </a>";
//echo "<button style='width: 200px;' onclick=\"javascript:window.location.replace('./index.php?cdt_version_pref=1')
//				\">Utiliser la version 1 du cahier de textes</button>\n";
echo "</td>";
// **********************************************
// Affichage des diff�rents groupes du professeur
// R�cup�ration de toutes les infos sur le groupe
echo "<td valign='center'>";
$groups = $utilisateur->getGroupes();
if ($groups->isEmpty()) {
    echo "<br /><br />";
    echo "<b>Aucun cahier de textes n'est disponible.</b>";
    echo "<br /><br />";
}

$a = 1;
	foreach($groups as $group) {
	echo "<a href=\"#\" style=\"font-size: 11pt;\"  onclick=\"javascript:
			id_groupe = '".$group->getId()."';
			getWinDernieresNotices().hide();
			getWinListeNotices();
			new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$group->getId()."', {encoding: 'ISO-8859-1'});
			getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=".$group->getId()."&today='+getCalendarUnixDate(), { 
	            		encoding: 'ISO-8859-1',
	            		onComplete : 
	            		function() {
	            			initWysiwyg();
						}
					}
			);
			return false;
    	\">";

	echo $group->getNameAvecClasses();
	echo "</a>&nbsp;\n";

    if ($a == 3) {
    	$a = 1;
    } else {
		$a = $a + 1;
	}
}
echo "<a href='creer_sequence.php'>Pr&eacute;parer une s&eacute;quence enti&egrave;re</a></td>";
// Fin Affichage des diff�rents groupes du professeur
// **********************************************
echo "<td width='250 px'></td>";
echo "</tr>\n";
echo "</table>\n<hr />";
require("../lib/footer.inc.php");
?>
