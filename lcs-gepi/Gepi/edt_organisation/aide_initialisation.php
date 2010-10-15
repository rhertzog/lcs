<?php

/**
 *
 *
 * @version $Id: aide_initialisation.php $
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

$titre_page = "Emploi du temps - Aide � l'initialisation";
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
// INSERT INTO droits VALUES ('/edt_organisation/aide_initialisation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F','aide � l\'initialisation', '');
$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/aide_initialisation.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/edt_organisation/aide_initialisation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F','aide � l\'initialisation', '');";
	$res_insert=mysql_query($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
if ($_SESSION["statut"] != "administrateur") {
	Die('Vous devez demander � votre administrateur l\'autorisation de voir cette page.');
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
<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
    require_once("./menu.inc.new.php");
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
?>

<h2><strong>Aide � l'initialisation</strong></h2>
<p>Le module d'emplois du temps de GEPI a trois vocations : </p>
<p><strong>Objectif 1 </strong>: proposer les emplois du temps des classes, des salles, des profs et de chaque �l�ve � l'ann�e ou sur des p�riodes d�finies.</p>
<p><strong>Objectif 2 </strong>: proposer des outils de recherche de salles libres.</p>
<p><strong>Objectif 3 </strong>: permettre aux enseignants d'utiliser le module d'absences.</p>
<p>Avant de pouvoir utiliser tout ceci, vous devez remplir les emplois du temps des enseignants manuellement ou automatiquement. Pour se faire, nous vous proposons plusieurs scenarii possibles.</p>

<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
?>

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
?>
<p><h3><strong>Scenario 1 : Initialisation manuelle des emplois du temps � l'ann�e</strong></h3></p>
<p>C'est le scenario le plus simple et le plus intuitif � mettre en oeuvre. La proc�dure ci-dessous n'est pas forc�ment � prendre strictement dans l'ordre propos� mais chaque �tape doit �tre r�alis�e avec soin.</p>
<p><strong>1. Semaines : </strong>Vous devez d�finir les semaines altern�es s'il y en a dans votre �tablissement (cas typique Semaine A - Semaine B). Dans la version actuelle, le module d'emploi du temps ne g�re pas simultan�ment une alternance sur plus de deux semaines. Dans le menu, cliquez sur [Cr�ation] [Cr�er/�diter les semaines] </p>
<p><strong>2. Horaires : </strong>Vous devez �galement d�finir les jours et les horaires d'ouverture de votre �tablissement. Dans le menu, cliquez sur [Cr�ation] [D�finir les horaires d'ouverture] </p>
<p><strong>3. Cr�neaux : </strong>Vous devez d�finir les cr�neaux qui d�coupent la journ�e de cours. Pour le module d'emploi du temps, tous les cr�neaux d�finis sont par d�faut d'une heure (et ce quelque soit l'heure de d�but et de fin d'un cr�neau). Par la suite, le module d'emploi du temps vous laisse la possibilit� pour la saisie des cours de diviser chaque cr�neau en deux sous cr�neaux de 30 minutes. Rien ne vous emp�che de d�finir un cr�neau de 1h30 mais pour les emplois du temps, ce sera un cr�neau basique d'une heure. C'est souvent le cas lorsque l'on d�finit la pause d�jeuner qui, dans la plupart des cas, dure 1h30 (12h00 - 13h30). Pour ce cas pr�cis, vous pouvez d�finir ce cr�neau Repas de 12h00 � 13h30. Cependant, il faut garder � l'esprit que ce sera un cr�neau d'une heure qui sera retenu. Dans le menu, cliquez sur [Cr�ation] [D�finir les cr�neaux] </p>
<p><strong>4. AID : </strong>Vous devez cr�er les cours qui ne font pas partie des enseignements d�finis dans les classes. Ceci est le cas lorsque des groupes sont d�finis dans certaines disciplines (exemple : SVT Gr1, Techno GrA etc...). C'est aussi le cas lorsque certains enseignants ont des responsabilit�s tel que les heures de Labo (SVT, Histoire/G�o), les cr�neaux UNSS et de Coordonnateur pour l'EPS, les ateliers en g�n�ral qui n'entrent pas dans les enseignements "traditionnels". Pour tous ces cas, il faut d�finir ce que l'on appelle des AID. A partir de la page d'accueil GEPI, cliquez sur [Gestion des bases] [Gestion des AID] </p>
<p><strong>5. Salles : </strong>Vous devez d�finir les salles dans lesquelles se d�roulent les cours. Dans le menu, cliquez sur [Cr�ation] [Cr�er/�diter les salles] </p>
<p><strong>6. Emplois du temps profs : </strong>Le remplissage des emplois du temps se fait � partir de ceux des enseignants. Le module d'emploi du temps se charge alors de construire les emplois du temps des classes, de chaque �l�ve et des salles. Dans ce scenario, la saisie se fait manuellement � partir de l'emploi du temps de chaque enseignant. Vous pouvez choisir de remplir vous m�me ces emplois du temps ou d�l�guer cette tache � chaque enseignant. Cette d�l�gation se fait � partir de [Gestion des Modules] [Emplois du temps]. Pour cr�er les emplois du temps profs, cliquez sur [Cr�ation][Initialisation manuelle].</p>
<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
?>


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
?>
<p><h3><strong>Scenario 2 : Initialisation manuelle des emplois du temps par p�riode</strong></h3></p>
<p>Ce scenario est un peu plus complexe � mettre en place que le pr�c�dent puisqu'il n�cessite la gestion de p�riodes sur l'ann�e. L'id�e ici est de cr�er des p�riodes (ce peut �tre des trimestres, des demi-trimestres, des mois, des semaines ou un m�lange de tout cela) dans lesquels on d�finit les emplois du temps (en fait, on d�finit les edt dans le premi�re p�riode et on les duplique dans les suivantes). L'avantage du d�coupage en p�riode est que les emplois du temps peuvent varier d'une p�riode � l'autre. Ceci est tr�s utile si certains enseignements n'existent pas � l'ann�e (enseignements semestriels). On peut �galement se servir de ce d�coupage en pr�vision de semaines sp�ciales pour lesquelles les emplois du temps sont diff�rents (semaines d'examens blancs, semaines de stages en 3�me). La phase pr�liminaire est identique � celle du scenario 1 :</p>
<p>1. Vous r�alisez les 5 premi�res �tapes vues pr�c�demment (Semaines, Horaires, Cr�neaux, AID, Salles)</p>
<p><strong>2. P�riodes : </strong>Vous allez d�finir la ou les p�riodes qui vont d�couper l'ann�e scolaire. Evitez le chevauchement des p�riodes qui n'est pas une fonctionnalit� pr�vue dans le module d'emploi du temps. A partir du menu, cliquez sur [Cr�ation] [Cr�er/�diter les p�riodes].</p>
<p><strong>3. Emplois du temps profs : </strong>Le remplissage des emplois du temps se fait � partir de ceux des enseignants. Le module d'emploi du temps se charge alors de construire les emplois du temps des classes, de chaque �l�ve et des salles. Dans ce scenario, la saisie se fait manuellement � partir de l'emploi du temps de chaque enseignant. Vous pouvez choisir de remplir vous m�me ces emplois du temps ou d�l�guer cette tache � chaque enseignant. Cette d�l�gation se fait � partir de [Gestion des Modules] [Emplois du temps]. Pour cr�er les emplois du temps profs, cliquez sur [Cr�ation][Initialisation manuelle]. <strong>Pour pouvoir utiliser le syst�me des p�riodes et ainsi pouvoir modifier les emplois du temps dans chacune de ces p�riodes, il faut commencer par saisir les emplois du temps de la premi�re p�riode et les dupliquer via l'interface des p�riodes en faisant de simples copier/coller.</strong></p>
<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
?>

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
?>
<p><h3><strong>Scenario 3 : Initialisation automatique par importation</strong></h3></p>
<p>Ce scenario est diff�rent des deux pr�c�dents au moment du remplissage des emplois du temps. Au lieu de faire une saisie manuelle, on importe un fichier contenant tous les emplois du temps. Ce fichier doit �tre format� de fa�on � �tre reconnu par GEPI. Actuellement, le module d'emploi du temps vous propose trois formats d'importation possible : un simple fichier .csv, un fichier d'export du logiciel UnDeuxTemps, un export de type Charlemagne. La difficult� de cette importation est de taille : il faut faire coincider les enseignements contenus dans GEPI avec ceux contenus dans le fichier import�. D'ailleurs, il n'est pas rare de devoir finir ce travail d'importation manuellement pour quelques enseignements. Pour que cette importation se passe au mieux, le travail pr�liminaire suivant est indispensable.</p>
<p>1. Vous r�alisez dans un premier temps les �tapes 1 et 2 du scenario pr�c�dent.</p>
<p><strong>2. Importation du fichier : </strong>Cliquez sur [Cr�ation] [Initialisation automatique] et suivez les �tapes qui correspondent au  type d'import r�alis�.</strong></p>
<?php
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