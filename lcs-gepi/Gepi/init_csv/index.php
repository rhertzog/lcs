<?php
/*
 * $Id: index.php 3387 2009-09-07 11:27:40Z crob $
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
	else {
		echo "</p>\n";
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
<!--/p-->
<ul>
<li><p>Au cours de la proc�dure, le cas �ch�ant, certaines donn�es de l'ann�e pass�e seront d�finitivement effac�es de la base GEPI (�l�ves, notes, appr�ciations, ...) . Seules seront conserv�es les donn�es suivantes, qui seront seulement mises � jour si n�cessaire :<br /><br />
- les donn�es relatives aux �tablissements,<br />
- les donn�es relatives aux classes : intitul�s courts, intitul�s longs, nombre de p�riodes et noms des p�riodes,<br />
- les donn�es relatives aux mati�res : identifiants et intitul�s complets,<br />
- les donn�es relatives aux utilisateurs (professeurs, administrateurs, ...). Concernant les professeurs, les mati�res enseign�es par les professeurs sont conserv�es,<br />
- Les donn�es relatives aux diff�rents types d'AID.<br />&nbsp;</p></li>

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

	<p>L'initialisation s'effectue en plusieurs phases successives, chacune n�cessitant un fichier CSV sp�cifique, que vous devrez fournir au bon format :</p>
    <ul>
    <li><p><a href='eleves.php'>Proc�der � la premi�re phase</a> d'importation des �l�ves. <b>g_eleves.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre les champs suivants :
    	<br/>Nom ; Pr�nom ; Date de naissance ; n� identifiant interne (�tab) ; n� identifiant national ; Code �tablissement pr�c�dent ; Doublement (OUI | NON) ; R�gime (INTERN | EXTERN | IN.EX. | DP DAN) ; Sexe (F ou M)<br />&nbsp;</p></li>

    <li><p><a href='responsables.php'>Proc�der � la deuxi�me phase</a> d'importation des responsables des �l�ves : le fichier <b>g_responsables.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>n� d'identifiant �l�ve interne � l'�tablissement ; Nom du responsable ; Pr�nom du responsable ; Civilit� ;  Ligne 1 Adresse ; Ligne 2 Adresse ; Code postal ; Commune<br />&nbsp;</p></li>

    <li><p><a href='disciplines.php'>Proc�der � la troisi�me phase</a> d'importation des mati�res : le fichier <b>g_disciplines.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>Nom court mati�re ; Nom long mati�re<br />&nbsp;</p></li>

    <li><p><a href='professeurs.php'>Proc�der � la quatri�me phase</a> d'importation des professeurs : le fichier <b>g_professeurs.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>Nom ; Pr�nom ; Civilit� ; Adresse e-mail<br />&nbsp;</p></li>

    <li><p><a href='eleves_classes.php'>Proc�der � la cinqui�me phase</a> d'affectation des �l�ves aux classes  : le fichier <b>g_eleves_classes.csv</b> requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>n� d'identifiant �l�ve interne � l'�tablissement ; Identifiant court de la classe
    	<br/>Remarque : cette op�ration cr�� automatiquement les classes dans Gepi, mais ne leur attribue qu'un nom court (identifiant). Vous devrez ajouter le nom long par l'interface de gestion des classes.<br />&nbsp;</p></li>


    <li><p><a href='prof_disc_classes.php'>Proc�der � la sixi�me phase</a> d'affectation des mati�res � chaque professeur et d'affectation des professeurs dans chaque classe : le fichier <b>g_prof_disc_classes.csv</b> requis. Cette importation va d�finir les comp�tences des professeurs et cr�er les groupes d'enseignement dans chaque classe.
    	<br />Il doit contenir, dans l'ordre, les champs suivants :
    	<br />Login du professeur ; Nom court de la mati�re ; Le ou les identifiants de classe (s�par�s par des !) ; Le type de cours (CG (= cours g�n�ral) | OPT (= option))
    	<br />Remarques :
    	<br />Si le dernier champ est vide et qu'une seule classe est pr�sente dans le troisi�me champ, le type sera d�fini comme "g�n�ral". S'il est vide et que plusieurs classes ont �t� d�finies, alors le type sera d�fini comme "option".
    	<br />Lorsque l'enseignement est g�n�ral, tous les �l�ves de la classe sont automatiquement associ�s � cet enseignement.
    	<br />Lorsque l'enseignement est une option, aucun �l�ve n'y est associ�, l'association se faisant � la septi�me �tape.
    	<br />Attention ! Ne mettez plusieurs classes pour une m�me mati�re que s'il s'agit d'un seul enseignement ! Si un professeur enseigne la m�me mati�re dans deux classes diff�rentes, il faut alors deux lignes distinctes dans le fichier CSV, avec une seule classe d�finie pour chaque ligne.<br />&nbsp;</p></li>

    <li><p><a href='eleves_options.php'>Proc�der � la septi�me phase</a> d'affectation des �l�ves � chaque groupe d'option : le fichier <b>g_eleves_options.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>n� d'identifiant �l�ve interne � l'�tablissement ; Identifiants des mati�res suivies en option, s�par�s par des !
    	<br/>Remarque : si plusieurs groupes avec la m�me mati�re sont trouv�s dans la classe de l'�l�ve, alors l'�l�ve sera associ� � tous ces diff�rents groupes.<br />&nbsp;</p></li>
    </ul>
	<br />
</li>
<li><p>Une fois toute la proc�dure d'initialisation des donn�es termin�e, il vous sera possible d'effectuer toutes les modifications n�cessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.</p></li>
</ul>
<p><br /></p>

<p><b>ATTENTION:</b> Le <i>n� d'identifiant �l�ve interne � l'�tablissement</i> ne doit �tre constitu� que de chiffres.</p>
<p><br /></p>

<?php require("../lib/footer.inc.php");?>
