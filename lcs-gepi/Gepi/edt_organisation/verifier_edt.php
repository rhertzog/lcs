<?php

/**
 *
 *
 * @version $Id: verifier_edt.php $
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Emploi du temps - Maintenance";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

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
// ajout de la ligne suivante dans 'sql/data_gepi.sql' et 'utilitaires/updates/access_rights.inc.php'
// INSERT INTO droits VALUES ('/edt_organisation/verifier_edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'v�rifier la table edt_cours', '');

$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/verifier_edt.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/edt_organisation/verifier_edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F','v�rifier la table edt_cours', '');";
	$res_insert=mysql_query($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
if ($_SESSION["statut"] != "administrateur") {
	Die('Vous devez demander � votre administrateur l\'autorisation de voir cette page.');
}

// ===== Initialisation des variables =====
$supprimer = isset($_GET["supprimer"]) ? $_GET["supprimer"] : (isset($_POST["supprimer"]) ? $_POST["supprimer"] : NULL);
$message = "";

// ============================================ Suppression d'un emploi du temps

if (isset($supprimer)) {
    if ($supprimer == "suppression_profs") {
	    $req_profs = mysql_query("SELECT DISTINCT login_prof FROM edt_cours WHERE
					    login_prof NOT IN (SELECT login FROM utilisateurs)
                        ");
        if (mysql_num_rows($req_profs) != 0) {
            $req_suppression_prof = mysql_query("DELETE FROM edt_cours WHERE
                        login_prof NOT IN (SELECT login FROM utilisateurs)
                        ");
        }
    }
    else if ($supprimer== "suppression_groupes") {
	    $req_groupes = mysql_query("SELECT DISTINCT id_groupe FROM edt_cours WHERE
					id_groupe NOT IN (SELECT id FROM groupes) AND
                    id_groupe != ''
                    ");
        if (mysql_num_rows($req_groupes) != 0) {
	        while ($rep_groupes = mysql_fetch_array($req_groupes)) {
                $req_suppression_groupe = mysql_query("DELETE FROM edt_cours WHERE
                            id_groupe = '".$rep_groupes['id_groupe']."'
                            ");
	        }
        }
	    $req_groupes = mysql_query("SELECT DISTINCT id_aid FROM edt_cours WHERE
					id_aid NOT IN (SELECT id FROM aid) AND
                    id_aid != ''
                    ");
        if (mysql_num_rows($req_groupes) != 0) {
	        while ($rep_groupes = mysql_fetch_array($req_groupes)) {
                $req_suppression_groupe = mysql_query("DELETE FROM edt_cours WHERE
                            id_aid = '".$rep_groupes['id_aid']."'
                            ");
	        }
        }

    }

}


// CSS et js particulier � l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";

//++++++++++ l'ent�te de Gepi +++++
require_once("../lib/header.inc");
//++++++++++ fin ent�te +++++++++++
//++++++++++ le menu EdT ++++++++++
require_once("./menu.inc.php");
//++++++++++ fin du menu ++++++++++

?>


<br/>
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

	<?php 
        if ($message != "") {
            echo $message;
        }
        require_once("./menu.inc.new.php"); ?>


<h2><strong>Maintenance</strong></h2>
<?php

$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        

	$req_profs = mysql_query("SELECT DISTINCT login_prof FROM edt_cours WHERE
					login_prof NOT IN (SELECT login FROM utilisateurs)
                    ");
    if (mysql_num_rows($req_profs) != 0) {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 1</strong></p>';
        echo "<p style=\"text-align:center;\">".mysql_num_rows($req_profs)." enseignant(s) inscrit(s) dans les emplois du temps n'existe(nt) plus dans GEPI</p>";
        echo '<p style="text-align:center;padding:8px;">
               <a style="background-color:#FFFABD;border:2px solid black;padding:2px;" href="./verifier_edt.php?supprimer=suppression_profs">Lancer la proc�dure de nettoyage</a>
            </p>';
    }
    else {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 1</strong></p>';
        echo '<p style="text-align:center;">Il y a concordance parfaite entre enseignants enregistr�s sur GEPI et ceux enregistr�s dans les emplois du temps</p>';

    }
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}


$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        

	$req_groupes = mysql_query("SELECT DISTINCT id_groupe FROM edt_cours WHERE
					id_groupe NOT IN (SELECT id FROM groupes) AND
                    id_groupe != ''
                    ");
    if (mysql_num_rows($req_groupes) != 0) {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 2</strong></p>';
	    echo "<p style=\"text-align:center;\">".mysql_num_rows($req_groupes)." enseignement(s) inscrit(s) dans les emplois du temps n'existe(nt) plus dans GEPI</p>";
        //while ($rep = mysql_fetch_array($req_groupes)) {
        //    echo "<p style=\"text-align:center;\">".$rep['id_groupe']."<p>";
        //}
        echo '<p style="text-align:center;padding:8px;">
	           <a style="background-color:#FFFABD;border:2px solid black;padding:2px;" href="./verifier_edt.php?supprimer=suppression_groupes">Lancer la proc�dure de nettoyage</a>
            </p>';
    }
    else {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 2</strong></p>';
        echo '<p style="text-align:center;">Il y a concordance parfaite entre enseignements enregistr�s sur GEPI et ceux enregistr�s dans les emplois du temps</p>';

    }
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}

$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        

	$req_groupes = mysql_query("SELECT DISTINCT id_aid FROM edt_cours WHERE
					id_aid NOT IN (SELECT id FROM aid) AND
                    id_aid != '' 
                    ");
    if (mysql_num_rows($req_groupes) != 0) {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 3</strong></p>';
	    echo "<p style=\"text-align:center;\">".mysql_num_rows($req_groupes)." aid(s) inscrit(s) dans les emplois du temps n'existe(nt) plus dans GEPI</p>";
        echo '<p style="text-align:center;padding:8px;">
	           <a style="background-color:#FFFABD;border:2px solid black;padding:2px;" href="./verifier_edt.php?supprimer=suppression_groupes">Lancer la proc�dure de nettoyage</a>
            </p>';
    }
    else {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 3</strong></p>';
        echo '<p style="text-align:center;">Il y a concordance parfaite entre aids enregistr�s sur GEPI et ceux enregistr�s dans les emplois du temps</p>';

    }
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}


?>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>