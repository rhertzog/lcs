<?php
/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e pour l'annuaire LDAP Scribe NG";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="../gestion/index.php#init_scribe_ng"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<p>Vous allez effectuer l'initialisation de l'ann�e scolaire qui vient de d�buter, directement depuis l'annuaire LDAP de Scribe NG. Celui-ci doit donc avoir �t� pr�alablement aliment�. Cette proc�dure ne s'effectue qu'une seule fois.</p>

<?php
	echo "<p>Avez-vous pens� � effectuer les diff�rentes op�rations de fin d'ann�e et pr�paration de nouvelle ann�e � la page <a href='../gestion/changement_d_annee.php' style='font-weight:bold;'>Changement d'ann�e</a>&nbsp?</p>\n";
?>

<ul>
<li>Au cours de la proc�dure, le cas �ch�ant, certaines donn�es de l'ann�e pass�e seront d�finitivement effac�es de la base GEPI (�l�ves, notes, appr�ciations, ...) . Seules seront conserv�es les donn�es suivantes :<br /><br />
- les donn�es relatives aux �tablissements,<br />
- les donn�es relatives aux classes : intitul�s courts, intitul�s longs, nombre de p�riodes et noms des p�riodes,<br />
- les donn�es relatives aux mati�res : identifiants et intitul�s complets,<br />
- les donn�es relatives aux utilisateurs personnels dans l'�tablissement (professeurs, administrateurs, ...). Concernant les professeurs, les mati�res enseign�es par les professeurs sont conserv�es (et �ventuellement compl�t�es en phase 5),<br />
- Les donn�es relatives aux diff�rents types d'AID.</li><br />

<li>L'initialisation s'effectue en diff�rentes phases :<br />
    <ul>
    <br />
    <li><a href='etape1.php'>Proc�der � la premi�re phase</a> d'importation des �l�ves et de cr&eacute;ation des classes.</li>
    <br />
    <li><a href='etape2.php'>Proc�der � la deuxi�me phase</a> de cr&eacute;ation des p&eacute;riodes et d'affectation des &eacute;l&egrave;ves dans les classes.</li>
    <br />
    <li><a href='etape3.php'>Proc�der � la troisi�me phase</a> d'importation des responsables l&eacute;gaux des &eacute;l&egrave;ves.</li>
    <br />
    <li><a href='etape4.php'>Proc�der � la quatri�me phase</a> d'importation des professeurs.</li>
    <br />
    <li><a href='etape5.php'>Proc�der � la cinqui�me phase</a> d'importation des mati�res.</li>
    <br />
    <li><a href='etape6.php'>Proc�der � la sixi�me phase</a> d'importation des enseignements.</li>
    <br />
    <li><a href='etape7.php'>Proc�der � la septi�me phase</a> d'importation des personnels non-enseignants.</li>
    <br />
    <br />
</li>
<li>Une fois toute la proc�dure d'initialisation des donn�es termin�e, il vous sera possible d'effectuer toutes les modifications n�cessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.</li>
</ul>
<?php require("../lib/footer.inc.php");?>
