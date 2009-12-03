<?php

/*
 * $Id: index.php 3386 2009-09-07 11:22:25Z crob $
 *
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}

/*
if (!function_exists("dbase_open"))  {
    $msg = "ATTENTION : PHP n'est pas configur� pour g�rer les fichiers GEP (dbf). L'extension  d_base n'est pas active. Adressez-vous � l'administrateur du serveur pour corriger le probl�me.";
}
*/

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="../gestion/index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<p>Vous allez effectuer l'initialisation de l'ann�e scolaire qui vient de d�buter.<br />
<?php
	$sql="SELECT 1=1 FROM matieres_notes LIMIT 1;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo "Avez-vous pens� � <a href='#' onmouseover=\"afficher_div('archivage','y',20,20);\" onclick=\"return false;\">archiver</a> l'ann�e qui se termine ?</p>\n";
		$texte="<p>L'archivage de l'ann�e en cours vous permettra, une fois pass� � l'ann�e suivante, de consulter les bulletins ant�rieurs de chacun de vos �l�ves, pour peu qu'ils aient �t� scolaris�s dans votre �tablissement.</p><p>Cela n�cessite l'activation du <a href='../mod_annees_anterieures/admin.php'>module 'Ann�es ant�rieures'</a>.</p>";
		$tabdiv_infobulle[]=creer_div_infobulle('archivage',"Archivage d'une ann�e","",$texte,"",30,0,'y','y','n','n');
	}

	// CDT
	$sql="SELECT 1=1 FROM ct_entry LIMIT 1;";
	$test1=mysql_query($sql);
	$sql="SELECT 1=1 FROM ct_devoirs_entry LIMIT 1;";
	$test2=mysql_query($sql);
	if((mysql_num_rows($test1)>0)||(mysql_num_rows($test2)>0)) {
		echo "<p>Les cahiers de textes ne sont pas vides.<br />Vous devriez <a href='cahier_texte_admin/admin_ct.php'>vider les cahiers de textes de l'an dernier</a> avant de proc�der � l'initialisation.</p>\n";
	}
?>
</p>
<ul>
<li>
	<p>Au cours de la proc�dure, le cas �ch�ant, certaines donn�es de l'ann�e pass�e seront d�finitivement effac�es de la base GEPI (�l�ves, notes, appr�ciations, ...).<br />
	Seules seront conserv�es les donn�es suivantes :<br /></p>
	<ul>
		<li><p>les donn�es relatives aux �tablissements,</p></li>
		<li><p>les donn�es relatives aux classes : intitul�s courts, intitul�s longs, nombre de p�riodes et noms des p�riodes,</p></li>
		<li><p>les donn�es relatives aux mati�res : identifiants et intitul�s complets,</p></li>
		<li><p>les donn�es relatives aux utilisateurs (professeurs, administrateurs, ...). Concernant les professeurs, les mati�res enseign�es par les professeurs sont conserv�es,</p></li>
		<li><p>Les donn�es relatives aux diff�rents types d'AID.</p></li>
	</ul>
</li>
<li>
	<!--p>L'initialisation s'effectue en quatre phases, chacune n�cessitant un fichier GEP particulier <b>ou des CSV</b>:</p>
	<ul>
		<li><p>Vous devez disposer des fichiers F_ELE.DBF et F_ERE.DBF g�n�r�s par l'AutoSco.<br />
		G�n�rer le F_ELE.CSV correspondant au F_ELE.DBF depuis Sconet est assez facile sauf pour l'ERENO qui n'est pas r�cup�r� et du coup g�n�rer un F_ERE.CSV n'est pas commode.<br />Il faut en effet fixer arbitrairement un ERENO pour faire le lien entre parents et enfants et ne r�cup�rer que les entr�es souhait�es de Sconet pour les parents (<i>on r�cup�re l� plus de deux lignes par �l�ve...</i>)...<br />Bref, j'ai laiss� en plan.</p></li>
		<li><p>Vous pouvez g�n�rer les fichiers F_TMT.CSV, F_MEN.CSV et F_GPD.CSV � l'aide de l'export XML de STS une fois l'emploi du temps remont�.</p>
		<p>Vous pouvez �galement compl�ter partiellement le F_WIND.CSV de cette fa�on.<br />
		Partiellement parce que certains champs ne sont pas r�cup�r�s:</p>
		<ul>
			<li>le NUMEN (INDNNI) utilis� comme mot de passe par d�faut par GEPI n'est pas r�cup�r�.<br />
			Il est alors propos� de d�finir un mot de passe al�atoire ou d'utiliser la date de naissance � la place.</li>
			<li>La civilit� n'est pas r�cup�r�e non plus (<i>mais il est assez facile de la compl�ter</i>).</li>
			<li>Enfin, le champ FONCCO n'est pas rempli non plus (<i>mais c'est en principe 'ENS' pour tous les enseignants</i>).</li>
		</ul>
		<p><a href='lecture_xml_sts_emp.php'>G�n�rer les fichiers CSV � partir de l'export XML de STS</a>.</p>
		<p><b>AJOUT:</b> <a href='lecture_xml_sconet.php'>G�n�rer les fichiers CSV � partir des exports XML de Sconet</a>.</p></li>
	</ul-->
	<p>Professeurs, mati�res,...: <a href='lecture_xml_sts_emp.php'>G�n�rer les fichiers CSV � partir de l'export XML de STS</a>.</p>
	<p>El�ves: <a href='lecture_xml_sconet.php'>G�n�rer les fichiers CSV � partir des exports XML de Sconet</a>.</p>
</li>
<li>

	<?php
	//==================================
	// RNE de l'�tablissement pour comparer avec le RNE de l'�tablissement de l'ann�e pr�c�dente
	$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
	//==================================
	if($gepiSchoolRne=="") {
		echo "<p><b style='color:red;'>Attention</b>: Le RNE de l'�tablissement n'est pas renseign� dans 'Gestion g�n�rale/<a href='../gestion/param_gen.php' target='_blank'>Configuration g�n�rale</a>'<br />Cela peut perturber l'import de l'�tablissement d'origine des �l�ves.<br />Vous devriez corriger avant de poursuivre.</p>\n";
	}
	?>

	<p>Pour proc�der aux importations:</p>
	<ul>
		<li><p><a href='step1.php'>Proc�der � la premi�re phase</a> d'importation des �l�ves,  de constitution des classes et d'affectation des �l�ves dans les classes : le fichier <b>ELEVES.CSV</b> est requis.</p></li>
		<li><p><a href='responsables.php'>Proc�der � la deuxi�me phase</a> d'importation des responsables des �l�ves : les fichiers <b>PERSONNES.CSV</b>, <b>RESPONSABLES.CSV</b> et <b>ADRESSES.CSV</b> sont requis.</p></li>
		<li><p><a href='disciplines_csv.php'>Proc�der � la troisi�me phase</a> d'importation des mati�res : le fichier <b>F_tmt.csv</b> est requis.</p></li>
		<li><p><a href='prof_csv.php'>Proc�der � la quatri�me phase</a> d'importation des professeurs : le fichier <b>F_wind.csv</b> est requis.</p></li>
		<li><p><a href='prof_disc_classe_csv.php'>Proc�der � la cinqui�me phase</a> d'affectation des mati�res � chaque professeur, d'affectation des professeurs dans chaque classe  et de d�finition des options suivies par les �l�ves : les fichiers <b>F_men.csv</b> et <b>F_gpd.csv</b> sont requis.</p></li>

		<li><p><a href='init_pp.php'>Proc�der � la sixi�me phase</a>: Initialisation des professeurs principaux.</p></li>

		<li><p><a href='clean_tables.php'>Proc�der � la septi�me phase</a> de nettoyage des donn�es : les donn�es inutiles import�es � partir des fichiers GEP lors des diff�rentes phases d'initialisation seront effac�es !</p></li>

	</ul>
</li>
<li>
	<p>Une fois toute la proc�dure d'initialisation des donn�es termin�e, il vous sera possible d'effectuer toutes les modifications n�cessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.</p>
</li>
</ul>
<?php require("../lib/footer.inc.php");?>